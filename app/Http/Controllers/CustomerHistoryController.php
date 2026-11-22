<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;

class CustomerHistoryController extends Controller
{
    public function show(string $id)
    {
        $farmer = auth()->user();

        // Only allow access to this farmer's own customers
        $customer = $farmer->customers()->findOrFail($id);

        // Customer's milk delivery history
        $milkEntries = $customer->milkEntries()
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->paginate(15, ['*'], 'milk_page');

        // Customer's monthly bills
        $bills = $customer->bills()
            ->orderByDesc('period_start')
            ->paginate(10, ['*'], 'bills_page');

        // Customer's payment history
        $payments = Payment::whereHas('bill', function ($query) use ($farmer, $customer) {
            $query->where('user_id', $farmer->id)
                  ->where('customer_id', $customer->id);
        })
            ->with('bill')
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'payments_page');

        // Financial summary
        $totalBilled = $customer->bills()->sum('total_amount');

        $totalPaid = $customer->bills()->sum('paid_amount');

        $totalOutstanding = $customer->bills()->sum('balance_amount');

        // Total milk supplied
        $totalMilk = $customer->milkEntries()->sum('quantity');

        return view('customers.history', compact(
            'customer',
            'milkEntries',
            'bills',
            'payments',
            'totalBilled',
            'totalPaid',
            'totalOutstanding',
            'totalMilk'
        ));
    }
}

