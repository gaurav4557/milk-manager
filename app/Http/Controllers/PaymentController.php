<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index()
    {
        $farmer = auth()->user();

        $bills = $farmer->bills()
            ->with('customer')
            ->where('balance_amount', '>', 0)
            ->orderByDesc('period_start')
            ->get();

        $payments = Payment::whereHas('bill', function ($query) use ($farmer) {
            $query->where('user_id', $farmer->id);
        })
            ->with('bill')
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->paginate(15);

        $totalReceived = Payment::whereHas('bill', function ($query) use ($farmer) {
            $query->where('user_id', $farmer->id);
        })->sum('amount');

        $outstandingAmount = $farmer->bills()
            ->sum('balance_amount');

        return view('payments.index', compact(
            'bills',
            'payments',
            'totalReceived',
            'outstandingAmount'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bill_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:99999999'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,upi,bank_transfer,other'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $farmer = auth()->user();

        $bill = $farmer->bills()
            ->where('id', $validated['bill_id'])
            ->firstOrFail();

        DB::transaction(function () use ($bill, $validated) {
            $lockedBill = Bill::whereKey($bill->id)
                ->lockForUpdate()
                ->firstOrFail();

            $paymentAmount = round((float) $validated['amount'], 2);
            $totalPaid = (float) $lockedBill->payments()->sum('amount');

            if ($paymentAmount > (float) $lockedBill->total_amount - $totalPaid + 0.001) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment cannot exceed the outstanding bill amount.',
                ]);
            }

            Payment::create([
                'bill_id' => $lockedBill->id,
                'amount' => $paymentAmount,
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $paidAmount = (float) $lockedBill->payments()->sum('amount');
            $balanceAmount = round(
                (float) $lockedBill->total_amount - $paidAmount,
                2
            );

            $lockedBill->paid_amount = round($paidAmount, 2);
            $lockedBill->balance_amount = max(0, $balanceAmount);

            if ($balanceAmount <= 0.001) {
                $lockedBill->payment_status = 'paid';
            } elseif ($paidAmount > 0) {
                $lockedBill->payment_status = 'partial';
            } else {
                $lockedBill->payment_status = 'pending';
            }

            $lockedBill->save();
        });

        return redirect()
            ->route('payments.index')
            ->with('success', 'Payment recorded successfully!');
    }
}

