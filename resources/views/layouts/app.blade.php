
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Milk Manager')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f7f6;
            font-family: Arial, sans-serif;
            color: #26332b;
        }

        .navbar {
            background: #14532d;
        }

        .navbar-brand {
            color: white !important;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .navbar .nav-link {
            color: #e5f5e9 !important;
            padding: 10px 12px !important;
            border-radius: 6px;
            transition: 0.2s;
        }

        .navbar .nav-link:hover,
        .navbar .nav-link.active {
            color: white !important;
            background: rgba(255, 255, 255, 0.15);
        }

        .main-content {
            min-height: 80vh;
        }

        .card {
            border-radius: 12px;
        }

        .btn-success {
            background-color: #198754;
        }

        .navbar-toggler {
            border-color: rgba(255,255,255,0.4);
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        footer {
            color: #6c757d;
            font-size: 13px;
            text-align: center;
            padding: 20px 0;
        }

        @media (max-width: 991px) {
            .navbar-nav {
                padding-top: 12px;
            }

            .navbar .nav-link {
                padding: 10px !important;
            }

            .navbar-nav .btn {
                margin: 8px 0;
            }
        }
    </style>

    @yield('styles')
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container">

        <a class="navbar-brand" href="{{ route('dashboard') }}">
            🥛 Milk Manager
        </a>

        @auth

            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#mainNavbar"
                    aria-controls="mainNavbar"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">

                <ul class="navbar-nav ms-auto align-items-lg-center">

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                           href="{{ route('dashboard') }}">
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}"
                           href="{{ route('customers.index') }}">
                            Customers
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('milk-entries.*') ? 'active' : '' }}"
                           href="{{ route('milk-entries.index') }}">
                            Milk Entries
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('milk-rates.*') ? 'active' : '' }}"
                           href="{{ route('milk-rates.index') }}">
                            Milk Rates
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('bills.*') ? 'active' : '' }}"
                           href="{{ route('bills.index') }}">
                            Bills
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}"
                           href="{{ route('payments.index') }}">
                            Payments
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                           href="{{ route('reports.index') }}">
                            Reports
                        </a>
                    </li>

                    <li class="nav-item ms-lg-2">
                        <span class="nav-link">
                            👤 {{ auth()->user()->name }}
                        </span>
                    </li>

                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf

                            <button type="submit"
                                    class="btn btn-outline-light btn-sm ms-lg-2">
                                Logout
                            </button>
                        </form>
                    </li>

                </ul>

            </div>

        @endauth

    </div>
</nav>

<main class="main-content">
    @yield('content')
</main>

<footer>
    Designed &amp; Developed by Gaurav Abhale
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@yield('scripts')

</body>
</html>
