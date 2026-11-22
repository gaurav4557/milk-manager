
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Milk Rate Settings | Milk Manager</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7f4;
            font-family: Arial, sans-serif;
            color: #26352a;
        }

        .navbar {
            background: #245c35;
        }

        .brand {
            color: white;
            font-size: 21px;
            font-weight: 800;
            text-decoration: none;
        }

        .page-container {
            max-width: 1000px;
            margin: auto;
            padding: 30px 16px;
        }

        .content-card {
            background: white;
            border-radius: 17px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,.04);
        }

        .rate-display {
            background: linear-gradient(120deg, #245c35, #398653);
            color: white;
            border-radius: 16px;
            padding: 25px;
        }

        .rate-display h2 {
            font-size: 36px;
            font-weight: 800;
        }

        .form-control {
            padding: 12px;
            border-radius: 10px;
        }

        .form-control:focus {
            border-color: #398653;
            box-shadow: 0 0 0 0.2rem rgba(57,134,83,.12);
        }

        .btn-green {
            background: #287a42;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
        }

        .btn-green:hover {
            background: #1e6033;
            color: white;
        }

        .table th {
            color: #526357;
            font-size: 13px;
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
        }

        .rate-value {
            color: #287a42;
            font-weight: 700;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-dark">
    <div class="container">
        <a class="brand" href="{{ route('dashboard') }}">
            🥛 Milk Manager
        </a>

        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
                Dashboard
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-light btn-sm">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<main class="page-container">

    <div class="mb-4">
        <h2 class="fw-bold mb-1">Milk Rate Settings</h2>
        <p class="text-secondary mb-0">
            Set your milk price and maintain your rate history.
        </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rate-display mb-4">

        <p class="mb-2">CURRENT MILK RATE</p>

        @if($currentRate)
            <h2>₹{{ number_format((float)$currentRate->rate, 2) }}</h2>

            <p class="mb-0">
                Per litre · Effective from
                {{ $currentRate->effective_from->format('d M Y') }}
            </p>
        @else
            <h2>Not Set</h2>

            <p class="mb-0">
                Please add your first milk rate below.
            </p>
        @endif

    </div>

    <div class="content-card mb-4">

        <h5 class="fw-bold mb-1">Set New Milk Rate</h5>

        <p class="text-secondary small mb-4">
            Enter the price and the date from which it should apply.
        </p>

        <form action="{{ route('milk-rates.store') }}" method="POST">
            @csrf

            <div class="row g-3">

                <div class="col-md-6">
                    <label for="rate" class="form-label fw-semibold">
                        Milk Rate (₹ per litre) *
                    </label>

                    <input
                        type="number"
                        name="rate"
                        id="rate"
                        class="form-control"
                        step="0.01"
                        min="0.01"
                        max="999999.99"
                        placeholder="Example: 45.00"
                        value="{{ old('rate') }}"
                        required
                    >
                </div>

                <div class="col-md-6">
                    <label for="effective_from" class="form-label fw-semibold">
                        Effective From *
                    </label>

                    <input
                        type="date"
                        name="effective_from"
                        id="effective_from"
                        class="form-control"
                        value="{{ old('effective_from', now()->toDateString()) }}"
                        required
                    >
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-green w-100">
                        Save Milk Rate
                    </button>
                </div>

            </div>
        </form>

        <p class="text-secondary small mt-3 mb-0">
            Saving a rate for an existing date updates that date's rate.
            Future rates will apply from their effective date.
        </p>

    </div>

    <div class="content-card">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">

            <div>
                <h5 class="fw-bold mb-1">Milk Rate History</h5>

                <p class="text-secondary small mb-0">
                    All rates saved by your account.
                </p>
            </div>

            <span class="badge text-bg-success">
                {{ $rates->count() }} Records
            </span>

        </div>

        @if($rates->count() > 0)

            <div class="table-responsive">

                <table class="table table-hover">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Effective Date</th>
                            <th>Rate Per Litre</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($rates as $rate)

                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    {{ $rate->effective_from->format('d M Y') }}
                                </td>

                                <td class="rate-value">
                                    ₹{{ number_format((float)$rate->rate, 2) }}
                                </td>

                                <td>
                                    @if($rate->effective_from->isToday())
                                        <span class="badge text-bg-success">
                                            Effective Today
                                        </span>
                                    @elseif($rate->effective_from->isFuture())
                                        <span class="badge text-bg-info">
                                            Upcoming
                                        </span>
                                    @else
                                        <span class="badge text-bg-secondary">
                                            Historical
                                        </span>
                                    @endif
                                </td>
                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="text-center py-5">
                <div style="font-size:45px;">💰</div>

                <h5 class="fw-bold mt-3">
                    No milk rates added yet
                </h5>

                <p class="text-secondary">
                    Add your first milk rate to get started.
                </p>
            </div>

        @endif

    </div>

    <footer class="text-center text-secondary small mt-4">
        © {{ date('Y') }} Milk Manager
        <br>
        Designed & Developed by <strong>Gaurav Abhale</strong>
    </footer>

</main>

</body>
</html>
