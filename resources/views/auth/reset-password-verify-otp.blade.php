<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Verify OTP - Seller Sathi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap 5.3.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #edf2f7, #dbeafe);
      font-family: 'Inter', sans-serif;
    }

    .otp-card {
      max-width: 420px;
      margin: 100px auto;
      background: #fff;
      border-radius: 12px;
      padding: 35px 30px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>

<body>
  <div class="otp-card">
    <h4 class="text-center mb-4 fw-bold">🔑 Verify OTP</h4>

    @if (session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.otp.verify') }}">
      @csrf

      <input type="hidden" name="email" value="{{ session('password_reset_email') }}">

      <!-- OTP Input -->
      <div class="mb-3">
        <label for="otp" class="form-label">Enter OTP</label>
        <input type="text" name="otp" id="otp"
               class="form-control @error('otp') is-invalid @enderror"
               required autofocus>
        @error('otp')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>

      <!-- Submit Button -->
      <div class="d-grid mt-4">
        <button type="submit" class="btn btn-success">
          ✅ Verify OTP
        </button>
      </div>
    </form>
  </div>

  <!-- Bootstrap Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
