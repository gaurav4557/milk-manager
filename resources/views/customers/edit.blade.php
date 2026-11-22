
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer | Milk Manager</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7f4;
            font-family: Arial, sans-serif;
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
            max-width: 650px;
            margin: auto;
            padding: 35px 16px;
        }

        .form-card {
            background: white;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,.05);
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
            padding: 12px;
            border-radius: 10px;
        }

        .btn-green:hover {
            background: #1e6033;
            color: white;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-dark">
    <div class="container">
        <a class="brand" href="{{ route('dashboard') }}">🥛 Milk Manager</a>

        <a href="{{ route('customers.index') }}" class="btn btn-outline-light btn-sm">
            Back to Customers
        </a>
    </div>
</nav>

<main class="page-container">

    <div class="form-card">

        <h3 class="fw-bold mb-1">Edit Customer</h3>

        <p class="text-secondary mb-4">
            Update {{ $customer->name }}'s information.
        </p>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ route('customers.update', $customer->id) }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">
                    Customer Name *
                </label>

                <input
                    type="text"
                    name="name"
                    id="name"
                    class="form-control"
                    value="{{ old('name', $customer->name) }}"
                    maxlength="100"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="mobile" class="form-label fw-semibold">
                    Mobile Number
                </label>

                <input
                    type="tel"
                    name="mobile"
                    id="mobile"
                    class="form-control"
                    value="{{ old('mobile', $customer->mobile) }}"
                    maxlength="16"
                >
            </div>

            <div class="mb-4">
                <label for="address" class="form-label fw-semibold">
                    Address
                </label>

                <textarea
                    name="address"
                    id="address"
                    class="form-control"
                    rows="3"
                    maxlength="1000"
                >{{ old('address', $customer->address) }}</textarea>
            </div>

            <button type="submit" class="btn btn-green w-100">
                Update Customer
            </button>

            <a href="{{ route('customers.index') }}"
               class="btn btn-light border w-100 mt-2">
                Cancel
            </a>

        </form>

    </div>

    <p class="text-center text-secondary small mt-4">
        Designed & Developed by Gaurav Abhale
    </p>

</main>

</body>
</html>
