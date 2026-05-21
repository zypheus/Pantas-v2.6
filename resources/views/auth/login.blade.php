<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Important for mobile scaling -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <style>
        .rounded-custom {
            border-radius: 20px;
            padding: 14px;
            font-size: 18px;
        }

        .login-title {
            font-size: 26px;
        }

        .login-subtitle {
            font-size: 17px;
        }

        @media (max-width: 576px) {
            .login-title {
                font-size: 22px;
            }

            .login-subtitle {
                font-size: 15px;
            }

            .rounded-custom {
                font-size: 16px;
                padding: 12px;
            }

            .card {
                padding: 2rem 1rem !important;
            }
        }
    </style>
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="height: 100vh;">

    <div class="card shadow login-card w-100 mx-3" style="max-width: 450px; padding: 3rem;">
        <div class="text-center">
            <img src="{{ asset('images/d.png') }}" alt="Area 51 Logo" class="mb-3" style="max-width: 180px; width: 100%;">
        </div>

        <h5 class="text-center fw-bold login-title">Welcome! Let’s Begin</h5>
        <p class="text-center text-muted login-subtitle">Log in to Continue</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <input type="email" name="email" class="form-control rounded-custom" placeholder="Email" required autofocus>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control rounded-custom" placeholder="Password" required>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label text-lowercase" for="remember">
                        Remember me
                    </label>
                </div>
                <a href="#" class="text-primary">Forgot password?</a>
            </div>

            @error('email')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary btn-lg">Login</button>
            </div>
            
        
            
            <div class="d-grid mt-3">
                <a href="{{ route('patron.register') }}" class="btn btn-outline-primary btn-lg">
                    Register
                </a>
            </div>

        </form>
    </div>

</body>
</html>
