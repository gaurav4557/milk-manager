
@extends('layouts.app')

@section('content')
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 no-print">
        <a href="{{ route('bills.index', ['month' => \Carbon\Carbon::parse($bill->period_start)->format('Y-m')]) }}"
           class="btn btn-outline-secondary">
            ← Back to Bills
        </a>

        <button onclick="window.print()" class="btn btn-success">
            Print / Save PDF
        </button>
    </div>

    <div class="card border-0 shadow-sm invoice-card">
        <div class="card-body p-4 p-md-5">

            <div class="row border-bottom pb-4 mb-4">
                <div class="col-md-7">
                    <h2 class="fw-bold text-success mb-1">MILK MANAGER</h2>
                    <p class="text-muted mb-3">Monthly Milk Invoice</p>

                    <h4 class="fw-bold mb-1">{{ $bill->farmer_name }}</h4>
                    <p class="text-muted mb-0">
                        Farmer / Milk Supplier
                    </p>
                    <p class="mb-0">Mobile: {{ $bill->farmer_mobile }}</p>
                </div>

                <div class="col-md-5 text-md-end mt-4 mt-md-0">
                    <h5 class="fw-bold">INVOICE</h5>
                    <p class="mb-1">
                        <strong>Bill No:</strong> {{ $bill->bill_number }}
                    </p>
                    <p class="mb-1">
                        <strong>Bill Date:</strong>
                        {{ $bill->generated_at->format('d M Y') }}
                    </p>
                    <p class="mb-0">
                        <strong>Billing Period:</strong><br>
                        {{ $bill->period_start->format('d M Y') }}
                        to
                        {{ $bill->period_end->format('d M Y') }}
                    </p>
                </div>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold text-uppercase text-muted">Bill To</h6>
                <h5 class="fw-bold mb-1">{{ $bill->customer_name }}</h5>
                <p class="mb-0">
                    Mobile: {{ $bill->customer_mobile ?: 'Not provided' }}
                </p>
            </div>

            <h6 class="fw-bold mb-3">Date-wise Milk Details</h6>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th class="text-end">Quantity (Litres)</th>
                            <th class="text-end">Rate / Litre</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($bill->items as $item)
                            <tr>
                                <td>{{ $item->entry_date->format('d M Y') }}</td>
                                <td class="text-end">
                                    {{ number_format((float) $item->quantity, 2) }}
                                </td>
                                <td class="text-end">
                                    ₹{{ number_format((float) $item->rate_per_litre, 2) }}
                                </td>
                                <td class="text-end fw-semibold">
                                    ₹{{ number_format((float) $item->amount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row justify-content-end mt-4">
                <div class="col-md-6 col-lg-5">
                    <table class="table table-sm">
                        <tr>
                            <th>Total Milk</th>
                            <td class="text-end fw-bold">
                                {{ number_format((float) $bill->total_litres, 2) }} L
                            </td>
                        </tr>
                        <tr>
                            <th>Total Bill Amount</th>
                            <td class="text-end fw-bold">
                                ₹{{ number_format((float) $bill->total_amount, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <th>Amount Paid</th>
                            <td class="text-end text-success fw-bold">
                                ₹{{ number_format((float) $bill->paid_amount, 2) }}
                            </td>
                        </tr>
                        <tr class="border-top">
                            <th>Balance Due</th>
                            <td class="text-end text-danger fw-bold fs-5">
                                ₹{{ number_format((float) $bill->balance_amount, 2) }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-4 p-3 rounded bg-light">
                <strong>Payment Status:</strong>

                @if($bill->payment_status === 'paid')
                    <span class="badge bg-success ms-2">PAID</span>
                @elseif($bill->payment_status === 'partial')
                    <span class="badge bg-warning text-dark ms-2">PARTIALLY PAID</span>
                @else
                    <span class="badge bg-danger ms-2">PAYMENT PENDING</span>
                @endif
            </div>

            <div class="text-center border-top mt-5 pt-4">
                <p class="fw-semibold mb-1">Thank you for your business!</p>
                <p class="text-muted small mb-0">
                    This is a computer-generated milk bill.
                </p>
                <p class="text-muted small mt-3 mb-0">
                    Designed &amp; Developed by Gaurav Abhale
                </p>
            </div>

        </div>
    </div>
</div>

<style>
    @media print {
        @page {
            size: A4;
            margin: 12mm;
        }

        body {
            background: #fff !important;
        }

        .no-print,
        nav,
        footer {
            display: none !important;
        }

        .container {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .invoice-card {
            box-shadow: none !important;
            border: none !important;
        }

        .card-body {
            padding: 0 !important;
        }

        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
        }
    }
</style>
@endsection
