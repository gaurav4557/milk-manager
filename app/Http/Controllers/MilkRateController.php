<?php

namespace App\Http\Controllers;

use App\Models\MilkRate;
use Illuminate\Http\Request;

class MilkRateController extends Controller
{
    /**
     * Display milk rate settings and rate history.
     */
    public function index()
    {
        $rates = auth()->user()
            ->milkRates()
            ->orderByDesc('effective_from')
            ->orderByDesc('id')
            ->get();

        $currentRate = auth()->user()
            ->milkRates()
            ->whereDate('effective_from', '<=', today())
            ->orderByDesc('effective_from')
            ->orderByDesc('id')
            ->first();

        return view('milk-rates.index', compact(
            'rates',
            'currentRate'
        ));
    }

    /**
     * Save a new rate or update the rate for an existing effective date.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rate' => [
                'required',
                'numeric',
                'gt:0',
                'max:999999.99',
            ],

            'effective_from' => [
                'required',
                'date',
            ],
        ]);

        auth()->user()->milkRates()->updateOrCreate(
            [
                'effective_from' => $validated['effective_from'],
            ],
            [
                'rate' => $validated['rate'],
            ]
        );

        return redirect()
            ->route('milk-rates.index')
            ->with('success', 'Milk rate saved successfully!');
    }
}
