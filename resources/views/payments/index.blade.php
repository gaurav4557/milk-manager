
@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1">Payments Management</h2>
            <p class="text-muted mb-0">Record payments and track outstanding balances.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please check the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <p class="text-muted mb-1">Total Payments Received</p>
                    <h3 class="fw-bold text-success mb-0">
                        ₹{{ number_format((float) $totalReceived, 2) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <p class="text-muted mb-1">Total Outstanding Amount</p>
                    <h3 class="fw-bold text-danger mb-0">
                        ₹{{ number_format((float) $outstandingAmount, 2) }}
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Record Customer Payment</h5>

            @if($bills->isEmpty())
                <div class="alert alert-info mb-0">
                    No unpaid bills are currently available.
                </div>
            @else
                <form action="{{ route('payments.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Select Bill / Customer</label>
                            <select name="bill_id" class="form-select" required>
                                <option value="">Choose a bill</option>
                                @foreach($bills as $bill)
                                    <option value="{{ $bill->id }}"
                                        {{ old('bill_id') == $bill->id ? 'selected' : '' }}>
                                        {{ $bill->customer_name }}
                                        | {{ $bill->bill_number }}
                                        | Due ₹{{ number_format((float) $bill->balance_amount, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Amount (₹)</label>
                            <input type="number"
                                   name="amount"
                                   class="form-control"
                                   min="0.01"
                                   max="99999999"
                                   step="0.01"
                                   value="{{ old('amount') }}"
                                   placeholder="Enter amount"
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Payment Date</label>
                            <input type="date"
                                   name="payment_date"
                                   class="form-control"
                                   value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Payment Method</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cash">Cash</option>
                                <option value="upi">UPI</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Transaction Reference</label>
                            <input type="text"
                                   name="reference_number"
                                   class="form-control"
                                   value="{{ old('reference_number') }}"
                                   placeholder="Optional UPI / bank reference">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes"
                                      class="form-control"
                                      rows="2"
                                      placeholder="Optional payment notes">{{ old('notes') }}</textarea>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-success px-4">
                                Save Payment
                            </button>
                        </div>

                    </div>
                </form>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Payment History</h5>

            @if($payments->isEmpty())
                <div class="text-center py-4">
                    <h6 class="fw-bold">No payments recorded yet</h6>
                    <p class="text-muted mb-0">
                        Customer payments will appear here.
                    </p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Bill Number</th>
                                <th>Method</th>
                                <th>Reference</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                    <td class="fw-semibold">
                                        {{ $payment->bill->customer_name }}
                                    </td>
                                    <td>{{ $payment->bill->bill_number }}</td>
                                    <td>
                                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                    </td>
                                    <td>{{ $payment->reference_number ?: '—' }}</td>
                                    <td class="text-end fw-bold text-success">
                                        ₹{{ number_format((float) $payment->amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
