
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Milk Entry | Milk Manager</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7f4;
            font-family: Arial, sans-serif;
            color: #26352a;
        }

        .navbar { background: #245c35; }

        .brand {
            color: white;
            font-size: 21px;
            font-weight: 800;
            text-decoration: none;
        }

        .page-container {
            max-width: 1100px;
            margin: auto;
            padding: 30px 16px;
        }

        .content-card {
            background: white;
            border-radius: 17px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,.04);
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 11px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #398653;
            box-shadow: 0 0 0 0.2rem rgba(57,134,83,.12);
        }

        .btn-green {
            background: #287a42;
            color: white;
            border: none;
            border-radius: 9px;
            padding: 11px 20px;
            font-weight: 600;
        }

        .btn-green:hover {
            background: #1e6033;
            color: white;
        }

        .quantity-input {
            max-width: 170px;
            min-width: 115px;
        }

        .table th {
            color: #526357;
            font-size: 13px;
            white-space: nowrap;
        }

        .table td { vertical-align: middle; }

        .customer-name {
            font-weight: 700;
            color: #245c35;
        }

        .quantity-label {
            font-size: 12px;
            color: #718075;
        }

        .empty-state {
            text-align: center;
            padding: 45px 15px;
            color: #718075;
        }

        .btn-delete {
            border-radius: 8px;
            font-size: 13px;
            padding: 7px 12px;
        }

        @media(max-width: 576px) {
            .content-card { padding: 16px; }
            .quantity-input { min-width: 110px; }
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
        <h2 class="fw-bold mb-1">Daily Milk Entry</h2>
        <p class="text-secondary mb-0">
            Record the daily milk quantity for each customer.
        </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-warning">
            {{ session('error') }}
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

    <!-- Date Selection -->
    <div class="content-card mb-4">

        <form action="{{ route('milk-entries.index') }}" method="GET">

            <div class="row align-items-end g-3">

                <div class="col-md-5">
                    <label for="date" class="form-label fw-semibold">
                        Select Entry Date
                    </label>

                    <input
                        type="date"
                        name="date"
                        id="date"
                        class="form-control"
                        value="{{ $date }}"
                        max="{{ now()->toDateString() }}"
                        required
                    >
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-green w-100">
                        Load Date
                    </button>
                </div>

                <div class="col-md-4">
                    <div class="text-secondary small">Selected date</div>
                    <div class="fw-bold">
                        {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                    </div>
                </div>

            </div>

        </form>

    </div>

    <!-- Customer Records -->
    <div class="content-card">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

            <div>
                <h5 class="fw-bold mb-1">Customer Milk Records</h5>
                <p class="text-secondary small mb-0">
                    Enter quantities in litres. Edit or delete saved entries.
                </p>
            </div>

            <span class="badge text-bg-success">
                {{ $customers->count() }} Customers
            </span>

        </div>

        @if($customers->count() > 0)

            <!-- Main Save Form -->
            <form action="{{ route('milk-entries.store') }}" method="POST">
                @csrf

                <input type="hidden" name="entry_date" value="{{ $date }}">

                <div class="table-responsive">

                    <table class="table table-hover">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer Name</th>
                                <th>Mobile</th>
                                <th>Milk Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($customers as $customer)

                                @php
                                    $savedQuantity = old(
                                        'quantities.' . $customer->id,
                                        optional($entries->get($customer->id))->quantity ?? 0
                                    );

                                    $standardQuantities = [
                                        '0', '0.5', '1', '2',
                                        '0.000', '0.500', '1.000', '2.000'
                                    ];

                                    $isStandard = in_array(
                                        (string)$savedQuantity,
                                        $standardQuantities,
                                        true
                                    );

                                    $existingEntry = $entries->get($customer->id);
                                @endphp

                                <tr>

                                    <td>{{ $loop->iteration }}</td>

                                    <td class="customer-name">
                                        {{ $customer->name }}
                                    </td>

                                    <td>
                                        {{ $customer->mobile ?: '—' }}
                                    </td>

                                    <td>

                                        <div class="d-flex flex-column gap-1">

                                            <select
                                                class="form-select quantity-input quantity-select"
                                                data-customer="{{ $customer->id }}"
                                                aria-label="Milk quantity for {{ $customer->name }}"
                                            >
                                                <option value="0" {{ (float)$savedQuantity == 0.0 ? 'selected' : '' }}>
                                                    0 L
                                                </option>

                                                <option value="0.5" {{ (float)$savedQuantity == 0.5 ? 'selected' : '' }}>
                                                    0.5 L
                                                </option>

                                                <option value="1" {{ (float)$savedQuantity == 1.0 ? 'selected' : '' }}>
                                                    1 L
                                                </option>

                                                <option value="2" {{ (float)$savedQuantity == 2.0 ? 'selected' : '' }}>
                                                    2 L
                                                </option>

                                                <option value="custom" {{ !$isStandard ? 'selected' : '' }}>
                                                    Other
                                                </option>
                                            </select>

                                            <input
                                                type="number"
                                                step="0.001"
                                                min="0"
                                                max="1000"
                                                class="form-control quantity-input custom-quantity"
                                                data-customer="{{ $customer->id }}"
                                                placeholder="Enter litres"
                                                value="{{ !$isStandard ? $savedQuantity : '' }}"
                                                style="display: none;"
                                            >

                                            <input
                                                type="hidden"
                                                name="quantities[{{ $customer->id }}]"
                                                id="quantity-{{ $customer->id }}"
                                                value="{{ $savedQuantity }}"
                                            >

                                            <span class="quantity-label">
                                                Litres per day
                                            </span>

                                        </div>

                                    </td>

                                    <td>
                                        @if($existingEntry)
                                            <button
                                                type="submit"
                                                class="btn btn-outline-danger btn-delete"
                                                form="delete-entry-{{ $existingEntry->id }}"
                                                onclick="return confirm('Are you sure you want to delete this milk entry?')"
                                            >
                                                Delete
                                            </button>
                                        @else
                                            <span class="text-secondary small">Not saved</span>
                                        @endif
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4">

                    <div class="text-secondary small">
                        Check every customer's quantity before saving.
                    </div>

                    <button type="submit" class="btn btn-green">
                        Save Daily Entries
                    </button>

                </div>

            </form>

            <!-- Separate Delete Forms -->
            @foreach($customers as $customer)

                @php
                    $existingEntry = $entries->get($customer->id);
                @endphp

                @if($existingEntry)
                    <form
                        id="delete-entry-{{ $existingEntry->id }}"
                        action="{{ route('milk-entries.destroy', $existingEntry->id) }}"
                        method="POST"
                        class="d-none"
                    >
                        @csrf
                        @method('DELETE')
                    </form>
                @endif

            @endforeach

        @else

            <div class="empty-state">

                <div style="font-size:45px;">👥</div>

                <h5 class="fw-bold mt-3">
                    No customers found
                </h5>

                <p>
                    Add customers before entering daily milk quantities.
                </p>

                <a href="{{ route('customers.create') }}" class="btn btn-green mt-2">
                    Add Customer
                </a>

            </div>

        @endif

    </div>

    <footer class="text-center text-secondary small mt-4">
        © {{ date('Y') }} Milk Manager
        <br>
        Designed & Developed by Gaurav Abhale
    </footer>

</main>

<script>
    document.querySelectorAll('.quantity-select').forEach(function(select) {

        const customerId = select.dataset.customer;

        const customInput = document.querySelector(
            '.custom-quantity[data-customer="' + customerId + '"]'
        );

        const hiddenInput = document.getElementById(
            'quantity-' + customerId
        );

        function updateQuantity() {

            if (select.value === 'custom') {

                customInput.style.display = 'block';
                hiddenInput.value = customInput.value;

            } else {

                customInput.style.display = 'none';
                customInput.value = '';
                hiddenInput.value = select.value;

            }
        }

        select.addEventListener('change', updateQuantity);

        customInput.addEventListener('input', function() {
            hiddenInput.value = customInput.value;
        });

        updateQuantity();

    });
</script>

</body>
</html>
