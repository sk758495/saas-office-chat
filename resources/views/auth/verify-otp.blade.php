<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OTP Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5.3.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #e9ecef, #f8f9fa);
            font-family: 'Inter', sans-serif;
        }

        .otp-container {
            max-width: 420px;
            margin: 80px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .otp-input {
            font-size: 1.2rem;
            text-align: center;
            letter-spacing: 8px;
        }

        .btn-primary, .btn-outline-secondary {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="otp-container">
        <h4 class="text-center mb-4">🔐 OTP Verification</h4>

        <!-- OTP Form -->
        <form method="POST" action="{{ route('auth.otp.verify') }}">
            @csrf
            <div class="mb-3">
                <label for="otp" class="form-label">Enter OTP</label>
                <input type="text" id="otp" name="otp" maxlength="6" required
                       class="form-control otp-input @error('otp') is-invalid @enderror"
                       placeholder="______" autofocus>

                @error('otp')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary mt-3">✅ Verify OTP</button>
        </form>

        <!-- Resend OTP -->
        <form method="POST" action="{{ route('auth.otp.resend') }}" class="mt-3">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">🔁 Resend OTP</button>
        </form>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
            @csrf
            <x-responsive-nav-link :href="route('logout')"
                onclick="event.preventDefault(); this.closest('form').submit();">
                🚪 {{ __('Log Out') }}
            </x-responsive-nav-link>
        </form>
    </div>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
