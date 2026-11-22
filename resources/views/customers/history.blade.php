
@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h2 class="fw-bold mb-1">{{ $customer->name }}</h2>
            <p class="text-muted mb-0">Customer Profile & History</p>
        </div>

        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
            ← Back to Customers
        </a>
    </div>

    {{-- Customer Details --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Customer Information</h5>

            <div class="row g-3">
                <div class="col-md-4">
                    <small class="text-muted">Customer Name</small>
                    <div class="fw-semibold">{{ $customer->name }}</div>
                </div>

                <div class="col-md-4">
                    <small class="text-muted">Mobile Number</small>
                    <div class="fw-semibold">{{ $customer->mobile ?: 'Not provided' }}</div>
                </div>

                <div class="col-md-4">
                    <small class="text-muted">Address</small>
                    <div class="fw-semibold">{{ $customer->address ?: 'Not provided' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Financial Summary --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <small class="text-muted">Total Milk Supplied</small>
                    <h4 class="fw-bold text-primary mt-2">
                        {{ number_format((float) $totalMilk, 2) }} L
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <small class="text-muted">Total Billed</small>
                    <h4 class="fw-bold mt-2">
                        ₹{{ number_format((float) $totalBilled, 2) }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <small class="text-muted">Total Paid</small>
                    <h4 class="fw-bold text-success mt-2">
                        ₹{{ number_format((float) $totalPaid, 2) }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <small class="text-muted">Outstanding Balance</small>
                    <h4 class="fw-bold text-danger mt-2">
                        ₹{{ number_format((float) $totalOutstanding, 2) }}
                    </h4>
                </div>
            </div>
        </div>

    </div>

    {{-- Milk Delivery History --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">
            <h5 class="fw-bold mb-3">🥛 Milk Delivery History</h5>

            @if($milkEntries->isEmpty())
                <p class="text-muted text-center py-3">No milk entries found.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th class="text-end">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($milkEntries as $entry)
                                <tr>
                                    <td>{{ $entry->entry_date->format('d M Y') }}</td>
                                    <td class="text-end fw-semibold">
                                        {{ number_format((float) $entry->quantity, 2) }} L
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $milkEntries->links() }}
            @endif
        </div>
    </div>

    {{-- Bill History --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">
            <h5 class="fw-bold mb-3">🧾 Monthly Bill History</h5>

            @if($bills->isEmpty())
                <p class="text-muted text-center py-3">No bills generated yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Bill Number</th>
                                <th>Period</th>
                                <th class="text-end">Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bills as $bill)
                                <tr>
                                    <td>{{ $bill->bill_number }}</td>
                                    <td>
                                        {{ $bill->period_start->format('M Y') }}
                                    </td>
                                    <td class="text-end fw-semibold">
                                        ₹{{ number_format((float) $bill->total_amount, 2) }}
                                    </td>
                                    <td>
                                        @if($bill->payment_status === 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($bill->payment_status === 'partial')
                                            <span class="badge bg-warning text-dark">Partial</span>
                                        @else
                                            <span class="badge bg-danger">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('bills.show', $bill->id) }}"
                                           class="btn btn-sm btn-outline-success">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $bills->links() }}
            @endif
        </div>
    </div>

    {{-- Payment History --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">
            <h5 class="fw-bold mb-3">💰 Payment History</h5>

            @if($payments->isEmpty())
                <p class="text-muted text-center py-3">No payments recorded yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Bill Number</th>
                                <th>Method</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                    <td>{{ $payment->bill->bill_number }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                    <td class="text-end fw-semibold text-success">
                                        ₹{{ number_format((float) $payment->amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $payments->links() }}
            @endif
        </div>
    </div>

</div>
@endsection
