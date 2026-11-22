
@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Monthly Bills</h2>
            <p class="text-muted mb-0">Generate and manage customer milk bills.</p>
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

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Generate Monthly Bill</h5>

            <form action="{{ route('bills.generate') }}" method="POST">
                @csrf

                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Select Customer</label>
                        <select name="customer_id" class="form-select" required>
                            <option value="">Choose customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                    @if($customer->mobile)
                                        - {{ $customer->mobile }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Select Month</label>
                        <input type="month"
                               name="month"
                               class="form-control"
                               value="{{ old('month', $month) }}"
                               required>
                    </div>

                    <div class="col-md-3">
                        <button type="submit" class="btn btn-success w-100">
                            Generate Bill
                        </button>
                    </div>
                </div>
            </form>

            <small class="text-muted d-block mt-3">
                The bill includes every date in the selected month. Dates without a milk entry are counted as 0 litres.
            </small>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Generated Bills</h5>
                <span class="badge bg-success">{{ $bills->count() }} Bills</span>
            </div>

            @if($bills->isEmpty())
                <div class="text-center py-5">
                    <h6 class="fw-bold">No bills generated yet</h6>
                    <p class="text-muted mb-0">
                        Select a customer and generate their first monthly bill.
                    </p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Customer</th>
                                <th>Bill Number</th>
                                <th>Total Litres</th>
                                <th>Total Amount</th>
                                <th>Payment Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bills as $bill)
                                <tr>
                                    <td class="fw-semibold">{{ $bill->customer_name }}</td>
                                    <td>{{ $bill->bill_number }}</td>
                                    <td>{{ number_format((float) $bill->total_litres, 2) }} L</td>
                                    <td class="fw-bold">
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
                                            View Bill
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="text-center text-muted mt-4 small">
        Designed &amp; Developed by Gaurav Abhale
    </div>

</div>
@endsection
