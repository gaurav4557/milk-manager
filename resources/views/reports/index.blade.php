
@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="mb-4">
        <h2 class="fw-bold">Revenue Reports</h2>
        <p class="text-muted">Track milk supply, collections and customer balances.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('reports.index') }}" method="GET"
                  class="row g-3 align-items-end">

                <div class="col-md-5">
                    <label class="form-label fw-semibold">Select Report Month</label>
                    <input type="month" name="month" class="form-control"
                           value="{{ $month }}" required>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-success w-100">
                        View Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="text-muted small">Today's Collection</div>
                    <h4 class="fw-bold text-success mt-2 mb-0">
                        ₹{{ number_format((float) $dailyRevenue, 2) }}
                    </h4>
                    <small class="text-muted">Payments received today</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="text-muted small">Monthly Collection</div>
                    <h4 class="fw-bold text-success mt-2 mb-0">
                        ₹{{ number_format((float) $monthlyRevenue, 2) }}
                    </h4>
                    <small class="text-muted">Payments received this month</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="text-muted small">Total Milk Supplied</div>
                    <h4 class="fw-bold text-primary mt-2 mb-0">
                        {{ number_format((float) $totalMilk, 2) }} L
                    </h4>
                    <small class="text-muted">Selected month</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="text-muted small">Outstanding Amount</div>
                    <h4 class="fw-bold text-danger mt-2 mb-0">
                        ₹{{ number_format((float) $outstandingAmount, 2) }}
                    </h4>
                    <small class="text-muted">All unpaid bill balances</small>
                </div>
            </div>
        </div>

    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Customer-wise Report</h5>

            @if($customerReports->isEmpty())
                <p class="text-muted text-center py-3">No customers available.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Customer</th>
                                <th class="text-end">Total Billed</th>
                                <th class="text-end">Paid</th>
                                <th class="text-end">Balance</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($customerReports as $customer)
                                <tr>
                                    <td class="fw-semibold">{{ $customer->name }}</td>

                                    <td class="text-end">
                                        ₹{{ number_format((float) ($customer->total_billed ?? 0), 2) }}
                                    </td>

                                    <td class="text-end text-success">
                                        ₹{{ number_format((float) ($customer->total_paid ?? 0), 2) }}
                                    </td>

                                    <td class="text-end fw-bold text-danger">
                                        ₹{{ number_format((float) ($customer->total_balance ?? 0), 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Date-wise Report</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th class="text-end">Milk Supplied</th>
                            <th class="text-end">Payments Received</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($dailyReports as $report)
                            <tr>
                                <td>{{ $report['date']->format('d M Y') }}</td>

                                <td class="text-end">
                                    {{ number_format($report['litres'], 2) }} L
                                </td>

                                <td class="text-end fw-semibold text-success">
                                    ₹{{ number_format($report['revenue'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
