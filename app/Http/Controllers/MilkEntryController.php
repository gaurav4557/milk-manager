<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MilkEntry;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MilkEntryController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        $date = $validated['date'] ?? now()->toDateString();

        $customers = auth()->user()
            ->customers()
            ->orderBy('name')
            ->get();

        $entries = MilkEntry::where('user_id', auth()->id())
            ->where('entry_date', $date)
            ->whereIn('customer_id', $customers->pluck('id'))
            ->get()
            ->keyBy('customer_id');

        return view('milk-entries.index', compact(
            'customers',
            'entries',
            'date'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entry_date' => ['required', 'date', 'before_or_equal:today'],
            'quantities' => ['required', 'array'],
            'quantities.*' => ['required', 'numeric', 'min:0', 'max:1000'],
        ]);

        $quantities = $validated['quantities'];

        DB::transaction(function () use ($validated, $quantities) {

            $customers = Customer::where('user_id', auth()->id())
                ->whereIn('id', array_keys($quantities))
                ->get();

            if ($customers->count() !== count($quantities)) {
                throw ValidationException::withMessages([
                    'quantities' => 'Invalid customer selection.',
                ]);
            }

            foreach ($customers as $customer) {
                MilkEntry::updateOrCreate(
                    [
                        'customer_id' => $customer->id,
                        'entry_date' => $validated['entry_date'],
                    ],
                    [
                        'user_id' => auth()->id(),
                        'quantity' => $quantities[$customer->id],
                    ]
                );
            }
        });

        return redirect()
            ->route('milk-entries.index', [
                'date' => $validated['entry_date'],
            ])
            ->with('success', 'Daily milk entries saved successfully!');
    }

    public function destroy(string $id)
    {
        $entry = MilkEntry::where('user_id', auth()->id())
            ->findOrFail($id);

        $date = $entry->entry_date->toDateString();

        // Prevent deleting an entry already covered by a generated bill.
        $billExists = Bill::where('user_id', auth()->id())
            ->where('customer_id', $entry->customer_id)
            ->whereDate('period_start', '<=', $date)
            ->whereDate('period_end', '>=', $date)
            ->exists();

        if ($billExists) {
            return redirect()
                ->route('milk-entries.index', ['date' => $date])
                ->with(
                    'error',
                    'This milk entry is included in a generated bill. Change its quantity to 0, save the entry, and regenerate the monthly bill before deleting it.'
                );
        }

        $entry->delete();

        return redirect()
            ->route('milk-entries.index', ['date' => $date])
            ->with('success', 'Milk entry deleted successfully!');
    }
}
