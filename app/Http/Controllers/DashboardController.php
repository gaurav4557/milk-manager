<?php

namespace App\Http\Controllers;

use App\Models\MilkEntry;
use App\Models\Payment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $farmer = auth()->user();

        $today = today();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        // Total customers
        $totalCustomers = $farmer->customers()->count();

        // Today's milk supplied
        $todayMilk = MilkEntry::where('user_id', $farmer->id)
            ->whereDate('entry_date', $today)
            ->sum('quantity');

        // Today's milk value using the applicable rate
        $todayRate = $farmer->milkRates()
            ->whereDate('effective_from', '<=', $today)
            ->orderByDesc('effective_from')
            ->value('rate');

        $todayMilkValue = $todayRate
            ? round((float) $todayMilk * (float) $todayRate, 2)
            : 0;

        // Current month's billed amount
        $monthlyBilling = $farmer->bills()
            ->whereBetween('period_start', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->sum('total_amount');

        // Payments received this month
        $monthlyCollection = Payment::whereHas('bill', function ($query) use ($farmer) {
            $query->where('user_id', $farmer->id);
        })
            ->whereBetween('payment_date', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->sum('amount');

        // Total outstanding balance
        $outstandingAmount = $farmer->bills()
            ->sum('balance_amount');

        // Latest milk entries
        $recentEntries = MilkEntry::where('user_id', $farmer->id)
            ->with('customer')
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalCustomers',
            'todayMilk',
            'todayMilkValue',
            'monthlyBilling',
            'monthlyCollection',
            'outstandingAmount',
            'recentEntries'
        ));
    }
}
