
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Customers | Milk Manager</title>

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
            max-width: 1100px;
            margin: auto;
            padding: 30px 16px;
        }

        .content-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,.04);
        }

        .btn-green {
            background: #287a42;
            color: white;
            border: none;
        }

        .btn-green:hover {
            background: #1e6033;
            color: white;
        }

        .customer-name {
            font-weight: 700;
            color: #245c35;
        }

        .empty-state {
            padding: 45px 15px;
            text-align: center;
            color: #718075;
        }

        .table th {
            color: #526357;
            font-size: 13px;
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
        }

        .action-buttons {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        @media(max-width: 576px) {
            .content-card {
                padding: 15px;
            }
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

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">My Customers</h2>
            <p class="text-secondary mb-0">
                Manage your milk customers.
            </p>
        </div>

        <a href="{{ route('customers.create') }}" class="btn btn-green px-4 py-2">
            + Add Customer
        </a>
    </div>

    <div class="content-card">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Customer List</h5>

            <span class="badge text-bg-success">
                {{ $customers->total() }} Customers
            </span>
        </div>

        @if($customers->count() > 0)

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer Name</th>
                            <th>Mobile Number</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($customers as $customer)
                            <tr>
                                <td>
                                    {{ $customers->firstItem() + $loop->index }}
                                </td>

                                <td class="customer-name">
                                    {{ $customer->name }}
                                </td>

                                <td>
                                    {{ $customer->mobile ?: 'Not provided' }}
                                </td>

                                <td>
                                    {{ $customer->address ?: 'Not provided' }}
                                </td>

                                <td>
                                    <div class="action-buttons">

                                        <a href="{{ route('customers.history', $customer->id) }}"
                                           class="btn btn-sm btn-success">
                                            View Profile
                                        </a>

                                        <a href="{{ route('customers.edit', $customer->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            Edit
                                        </a>

                                        <form action="{{ route('customers.destroy', $customer->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Are you sure you want to remove this customer?');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Delete
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            <div class="mt-3">
                {{ $customers->links('pagination::bootstrap-5') }}
            </div>

        @else

            <div class="empty-state">
                <div style="font-size: 45px;">👥</div>

                <h5 class="fw-bold mt-3">No customers added yet</h5>

                <p>Add your first customer to start managing milk records.</p>

                <a href="{{ route('customers.create') }}" class="btn btn-green mt-2">
                    Add Your First Customer
                </a>
            </div>

        @endif

    </div>

    <footer class="text-center text-secondary small mt-4">
        © {{ date('Y') }} Milk Manager
        <br>
        Designed &amp; Developed by Gaurav Abhale
    </footer>

</main>

</body>
</html>
