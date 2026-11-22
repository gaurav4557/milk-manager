<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display only the logged-in farmer's customers.
     */
    public function index()
    {
        $customers = auth()->user()
            ->customers()
            ->latest()
            ->paginate(10);

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the add customer form.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Save a new customer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],

            'mobile' => [
                'nullable',
                'string',
                'regex:/^\+?[0-9]{10,15}$/',
            ],

            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        auth()->user()->customers()->create($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer added successfully!');
    }

    /**
     * Show the edit customer form.
     */
    public function edit(string $id)
    {
        $customer = auth()->user()
            ->customers()
            ->findOrFail($id);

        return view('customers.edit', compact('customer'));
    }

    /**
     * Update customer details.
     */
    public function update(Request $request, string $id)
    {
        $customer = auth()->user()
            ->customers()
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],

            'mobile' => [
                'nullable',
                'string',
                'regex:/^\+?[0-9]{10,15}$/',
            ],

            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        $customer->update($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated successfully!');
    }

    /**
     * Soft-delete a customer.
     */
    public function destroy(string $id)
    {
        $customer = auth()->user()
            ->customers()
            ->findOrFail($id);

        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer removed successfully!');
    }
}
