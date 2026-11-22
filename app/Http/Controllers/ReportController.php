<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\MilkEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
        ]);

        $farmer = auth()->user();

        $month = $validated['month'] ?? now()->format('Y-m');

        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Payments received today
        $dailyRevenue = Payment::whereHas('bill', function ($query) use ($farmer) {
            $query->where('user_id', $farmer->id);
        })
            ->whereDate('payment_date', today())
            ->sum('amount');

        // Payments received during selected month
        $monthlyRevenue = Payment::whereHas('bill', function ($query) use ($farmer) {
            $query->where('user_id', $farmer->id);
        })
            ->whereBetween('payment_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
            ->sum('amount');

        // Total milk supplied during selected month
        $totalMilk = MilkEntry::where('user_id', $farmer->id)
            ->whereBetween('entry_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
            ->sum('quantity');

        // Outstanding balance across all bills
        $outstandingAmount = $farmer->bills()
            ->sum('balance_amount');

        // Customer-wise billing report
        $customerReports = $farmer->customers()
            ->withSum('bills as total_billed', 'total_amount')
            ->withSum('bills as total_paid', 'paid_amount')
            ->withSum('bills as total_balance', 'balance_amount')
            ->orderBy('name')
            ->get();

        // Date-wise milk quantity and payments received
        $milkByDate = MilkEntry::where('user_id', $farmer->id)
            ->whereBetween('entry_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
            ->select('entry_date', DB::raw('SUM(quantity) as total_litres'))
            ->groupBy('entry_date')
            ->orderBy('entry_date')
            ->get()
            ->keyBy(fn ($item) => Carbon::parse($item->entry_date)->toDateString());

        $paymentsByDate = Payment::whereHas('bill', function ($query) use ($farmer) {
            $query->where('user_id', $farmer->id);
        })
            ->whereBetween('payment_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
            ->select('payment_date', DB::raw('SUM(amount) as total_received'))
            ->groupBy('payment_date')
            ->orderBy('payment_date')
            ->get()
            ->keyBy(fn ($item) => Carbon::parse($item->payment_date)->toDateString());

        $dailyReports = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $key = $date->toDateString();

            $dailyReports[] = [
                'date' => $date->copy(),
                'litres' => (float) ($milkByDate->get($key)->total_litres ?? 0),
                'revenue' => (float) ($paymentsByDate->get($key)->total_received ?? 0),
            ];
        }

        return view('reports.index', compact(
            'month',
            'dailyRevenue',
            'monthlyRevenue',
            'totalMilk',
            'outstandingAmount',
            'customerReports',
            'dailyReports'
        ));
    }
}

