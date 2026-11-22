<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Registration | Milk Manager</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7f2, #e8f0e5);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            padding: 25px 15px;
        }

        .register-card {
            width: 100%;
            max-width: 480px;
            background: #ffffff;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
        }

        .brand-icon {
            width: 60px;
            height: 60px;
            background: #e8f5e9;
            color: #287a42;
            border-radius: 17px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin: 0 auto 12px;
        }

        .brand-title {
            color: #245c35;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .form-control {
            padding: 12px 14px;
            border-radius: 10px;
        }

        .form-control:focus {
            border-color: #398653;
            box-shadow: 0 0 0 0.2rem rgba(57, 134, 83, 0.12);
        }

        .btn-register {
            background: #287a42;
            color: white;
            border: none;
            padding: 13px;
            border-radius: 10px;
            font-weight: 600;
        }

        .btn-register:hover {
            background: #1e6033;
            color: white;
        }

        .login-link {
            color: #287a42;
            font-weight: 600;
            text-decoration: none;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        .form-text {
            font-size: 12px;
        }
    </style>
</head>

<body>

<div class="register-card">

    <div class="text-center mb-4">
        <div class="brand-icon">🥛</div>

        <h2 class="brand-title">Milk Manager</h2>

        <p class="text-secondary mb-0">
            Create your farmer account
        </p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h5 class="fw-bold mb-1">Farmer Registration</h5>

    <p class="text-secondary small mb-4">
        Enter your details to get started.
    </p>

    <form action="{{ route('register.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">
                Farmer Name
            </label>

            <input
                type="text"
                class="form-control @error('name') is-invalid @enderror"
                id="name"
                name="name"
                placeholder="Enter your full name"
                value="{{ old('name') }}"
                maxlength="100"
                autocomplete="name"
                required
            >
        </div>

        <div class="mb-3">
            <label for="mobile" class="form-label fw-semibold">
                Mobile Number
            </label>

            <input
                type="tel"
                class="form-control @error('mobile') is-invalid @enderror"
                id="mobile"
                name="mobile"
                placeholder="Enter your mobile number"
                value="{{ old('mobile') }}"
                maxlength="16"
                autocomplete="tel"
                required
            >

            <div class="form-text">
                Use a mobile number that is not already registered.
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">
                Password
            </label>

            <input
                type="password"
                class="form-control @error('password') is-invalid @enderror"
                id="password"
                name="password"
                placeholder="Create a password"
                autocomplete="new-password"
                required
            >

            <div class="form-text">
                Password must contain at least 8 characters.
            </div>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label fw-semibold">
                Confirm Password
            </label>

            <input
                type="password"
                class="form-control"
                id="password_confirmation"
                name="password_confirmation"
                placeholder="Enter password again"
                autocomplete="new-password"
                required
            >
        </div>

        <div class="mb-4">
            <label for="registration_code" class="form-label fw-semibold">
                Secret Registration Code
            </label>

            <input
                type="password"
                class="form-control @error('registration_code') is-invalid @enderror"
                id="registration_code"
                name="registration_code"
                placeholder="Enter registration code"
                autocomplete="off"
                required
            >

            <div class="form-text">
                Ask the Milk Manager administrator for this code.
            </div>
        </div>

        <button type="submit" class="btn btn-register w-100">
            Create Farmer Account
        </button>
    </form>

    <div class="text-center mt-4">
        <span class="text-secondary small">
            Already have an account?
        </span>

        <a href="{{ route('login') }}" class="login-link">
            Login
        </a>
    </div>

    <p class="text-center text-secondary small mt-4 mb-0">
        © {{ date('Y') }} Milk Manager
    </p>

</div>

</body>
</html>
