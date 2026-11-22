
@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="mb-4">
        <h2 class="fw-bold">Welcome, {{ auth()->user()->name }}! 👋</h2>
        <p class="text-muted">Here's your milk business overview.</p>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="fs-3">👥</div>
                    <p class="text-muted small mb-1">Total Customers</p>
                    <h3 class="fw-bold mb-0">{{ $totalCustomers }}</h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="fs-3">🥛</div>
                    <p class="text-muted small mb-1">Today's Milk</p>
                    <h3 class="fw-bold mb-0">
                        {{ number_format((float) $todayMilk, 2) }} L
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="fs-3">💵</div>
                    <p class="text-muted small mb-1">Today's Milk Value</p>
                    <h3 class="fw-bold text-success mb-0">
                        ₹{{ number_format((float) $todayMilkValue, 2) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="fs-3">🧾</div>
                    <p class="text-muted small mb-1">Monthly Billing</p>
                    <h3 class="fw-bold mb-0">
                        ₹{{ number_format((float) $monthlyBilling, 2) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="fs-3">💰</div>
                    <p class="text-muted small mb-1">Monthly Collection</p>
                    <h3 class="fw-bold text-success mb-0">
                        ₹{{ number_format((float) $monthlyCollection, 2) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="fs-3">⚠️</div>
                    <p class="text-muted small mb-1">Outstanding Balance</p>
                    <h3 class="fw-bold text-danger mb-0">
                        ₹{{ number_format((float) $outstandingAmount, 2) }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    {{-- Quick Actions --}}
    <h5 class="fw-bold mb-3">Manage Your Business</h5>

    <div class="row g-3 mb-4">

        @php
            $modules = [
                ['icon' => '👥', 'name' => 'Customers', 'description' => 'Manage customers', 'route' => 'customers.index'],
                ['icon' => '🥛', 'name' => 'Daily Milk Entry', 'description' => 'Record daily supply', 'route' => 'milk-entries.index'],
                ['icon' => '⚙️', 'name' => 'Milk Rates', 'description' => 'Manage milk prices', 'route' => 'milk-rates.index'],
                ['icon' => '🧾', 'name' => 'Monthly Bills', 'description' => 'Generate invoices', 'route' => 'bills.index'],
                ['icon' => '💰', 'name' => 'Payments', 'description' => 'Record customer payments', 'route' => 'payments.index'],
                ['icon' => '📊', 'name' => 'Revenue Reports', 'description' => 'View business reports', 'route' => 'reports.index'],
            ];
        @endphp

        @foreach($modules as $module)
            <div class="col-6 col-lg-4">
                <a href="{{ route($module['route']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 module-card">
                        <div class="card-body p-3">
                            <div class="fs-2 mb-2">{{ $module['icon'] }}</div>
                            <h6 class="fw-bold text-dark mb-1">{{ $module['name'] }}</h6>
                            <p class="text-muted small mb-0">{{ $module['description'] }}</p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach

    </div>

    {{-- Recent Milk Entries --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Recent Milk Entries</h5>
                <a href="{{ route('milk-entries.index') }}"
                   class="btn btn-sm btn-outline-success">
                    View All
                </a>
            </div>

            @if($recentEntries->isEmpty())
                <p class="text-muted text-center py-4 mb-0">
                    No milk entries recorded yet.
                </p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Customer</th>
                                <th class="text-end">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentEntries as $entry)
                                <tr>
                                    <td>{{ $entry->entry_date->format('d M Y') }}</td>
                                    <td>{{ $entry->customer->name }}</td>
                                    <td class="text-end fw-semibold">
                                        {{ number_format((float) $entry->quantity, 2) }} L
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>

<style>
    .module-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .module-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
    }
</style>
@endsection
