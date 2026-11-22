<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Customer;
use App\Models\MilkEntry;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BillController extends Controller
{
    /**
     * Show customers and their monthly billing options.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
        ]);

        $month = $validated['month'] ?? now()->format('Y-m');

        $customers = auth()->user()
            ->customers()
            ->orderBy('name')
            ->get();

        $bills = auth()->user()
            ->bills()
            ->whereDate('period_start', Carbon::createFromFormat('Y-m', $month)->startOfMonth())
            ->orderByDesc('created_at')
            ->get()
            ->keyBy('customer_id');

        return view('bills.index', compact(
            'customers',
            'bills',
            'month'
        ));
    }

    /**
     * Generate or regenerate a customer's monthly bill.
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer'],
            'month' => ['required', 'date_format:Y-m'],
        ]);

        $farmer = auth()->user();

        // Ensure the customer belongs to the logged-in farmer.
        $customer = $farmer->customers()
            ->findOrFail($validated['customer_id']);

        $periodStart = Carbon::createFromFormat(
            'Y-m',
            $validated['month']
        )->startOfMonth();

        $periodEnd = $periodStart->copy()->endOfMonth();

        $rates = $farmer->milkRates()
            ->whereDate('effective_from', '<=', $periodEnd->toDateString())
            ->orderBy('effective_from')
            ->get();

        if ($rates->isEmpty()) {
            throw ValidationException::withMessages([
                'month' => 'Please set a milk rate before generating a bill.',
            ]);
        }

        $entries = MilkEntry::where('user_id', $farmer->id)
            ->where('customer_id', $customer->id)
            ->whereBetween('entry_date', [
                $periodStart->toDateString(),
                $periodEnd->toDateString(),
            ])
            ->get()
            ->keyBy(fn ($entry) => Carbon::parse($entry->entry_date)->toDateString());

        $bill = DB::transaction(function () use (
            $farmer,
            $customer,
            $periodStart,
            $periodEnd,
            $rates,
            $entries
        ) {
            $existingBill = Bill::where('user_id', $farmer->id)
                ->where('customer_id', $customer->id)
                ->whereDate('period_start', $periodStart->toDateString())
                ->whereDate('period_end', $periodEnd->toDateString())
                ->lockForUpdate()
                ->first();

            $bill = $existingBill ?? new Bill();

            if (!$existingBill) {
                $bill->bill_number = 'MM-' .
                    now()->format('Ymd') . '-' .
                    strtoupper(Str::random(8));
            }

            $bill->user_id = $farmer->id;
            $bill->customer_id = $customer->id;

            // Store snapshots so old bills retain the original details.
            $bill->farmer_name = $farmer->name;
            $bill->farmer_mobile = $farmer->mobile;
            $bill->customer_name = $customer->name;
            $bill->customer_mobile = $customer->mobile;

            $bill->period_start = $periodStart->toDateString();
            $bill->period_end = $periodEnd->toDateString();

            $bill->generated_at = now();

            $bill->save();

            // Rebuild daily bill items while preserving recorded payments.
            $bill->items()->delete();

            $totalLitres = 0;
            $totalAmount = 0;

            $days = CarbonPeriod::create(
                $periodStart->copy(),
                $periodEnd->copy()
            );

            foreach ($days as $day) {
                $date = $day->toDateString();

                $entry = $entries->get($date);

                $quantity = $entry
                    ? (float) $entry->quantity
                    : 0.0;

                // Find the latest rate effective on this date.
                $applicableRate = $rates
                    ->filter(function ($rate) use ($date) {
                        return $rate->effective_from->toDateString() <= $date;
                    })
                    ->last();

                if (!$applicableRate) {
                    throw ValidationException::withMessages([
                        'month' => "No milk rate is available for {$day->format('d M Y')}. Please add a rate effective on or before this date.",
                    ]);
                }

                $rate = (float) $applicableRate->rate;
                $amount = round($quantity * $rate, 2);

                BillItem::create([
                    'bill_id' => $bill->id,
                    'entry_date' => $date,
                    'quantity' => $quantity,
                    'rate_per_litre' => $rate,
                    'amount' => $amount,
                ]);

                $totalLitres += $quantity;
                $totalAmount += $amount;
            }

            $paidAmount = (float) $bill->payments()->sum('amount');

            $totalAmount = round($totalAmount, 2);
            $balanceAmount = round($totalAmount - $paidAmount, 2);

            $bill->total_litres = round($totalLitres, 3);
            $bill->total_amount = $totalAmount;
            $bill->paid_amount = $paidAmount;
            $bill->balance_amount = $balanceAmount;

            if ($balanceAmount <= 0) {
                $bill->payment_status = 'paid';
            } elseif ($paidAmount > 0) {
                $bill->payment_status = 'partial';
            } else {
                $bill->payment_status = 'pending';
            }

            $bill->save();

            return $bill;
        });

        return redirect()
            ->route('bills.show', $bill->id)
            ->with('success', 'Monthly bill generated successfully!');
    }

    /**
     * Display a bill belonging to the logged-in farmer.
     */
    public function show(string $id)
    {
        $bill = auth()->user()
            ->bills()
            ->with(['items', 'payments'])
            ->findOrFail($id);

        return view('bills.show', compact('bill'));
    }
}

