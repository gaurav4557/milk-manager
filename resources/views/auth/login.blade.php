
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Milk Manager</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7f2, #e8f0e5);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 540px;
            background: #ffffff;
            border-radius: 24px;
            padding: 38px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
        }

        .brand-icon {
            width: 65px;
            height: 65px;
            background: #e8f5e9;
            color: #287a42;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin: 0 auto 15px;
        }

        .brand-title {
            color: #245c35;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .form-control {
            padding: 13px 15px;
            border-radius: 10px;
        }

        .form-control:focus {
            border-color: #398653;
            box-shadow: 0 0 0 0.2rem rgba(57, 134, 83, 0.12);
        }

        .btn-login {
            background: #287a42;
            color: white;
            border: none;
            padding: 13px;
            border-radius: 10px;
            font-weight: 600;
        }

        .btn-login:hover {
            background: #1e6033;
            color: white;
        }

        .register-link {
            color: #287a42;
            font-weight: 600;
            text-decoration: none;
        }

        .register-link:hover {
            text-decoration: underline;
        }

        .developer-credit {
            border-top: 1px solid #edf0ed;
            padding-top: 15px;
            margin-top: 18px;
            color: #718075;
            font-size: 13px;
        }

        .developer-credit strong {
            color: #287a42;
            font-weight: 700;
        }
    </style>
</head>

<body>

<div class="login-card">

    <div class="text-center mb-4">
        <div class="brand-icon">🥛</div>

        <h2 class="brand-title">Milk Manager</h2>

        <p class="text-secondary mb-0">
            Manage your milk business easily
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

    <h5 class="fw-bold mb-1">Welcome Back!</h5>

    <p class="text-secondary small mb-4">
        Login to access your farmer dashboard.
    </p>

    <form action="{{ route('login.store') }}" method="POST">
        @csrf

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
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">
                Password
            </label>

            <input
                type="password"
                class="form-control"
                id="password"
                name="password"
                placeholder="Enter your password"
                autocomplete="current-password"
                required
            >
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input
                    class="form-check-input"
                    type="checkbox"
                    name="remember"
                    value="1"
                    id="remember"
                >

                <label class="form-check-label small" for="remember">
                    Remember me
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-login w-100">
            Login to Dashboard
        </button>
    </form>

    <div class="text-center mt-4">
        <span class="text-secondary small">
            Don't have an account?
        </span>

        <a href="{{ route('register') }}" class="register-link">
            Register Now
        </a>
    </div>

    <div class="text-center mt-4">
        <p class="text-secondary small mb-2">
            © {{ date('Y') }} Milk Manager. All rights reserved.
        </p>

        <div class="developer-credit">
            Designed & Developed by <strong>Gaurav Abhale</strong>
        </div>
    </div>

</div>

</body>
</html>
