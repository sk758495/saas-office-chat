<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Send OTP - Seller Sathi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap 5.3.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #f8f9fa, #e3f2fd);
      font-family: 'Inter', sans-serif;
    }

    .otp-card {
      max-width: 420px;
      margin: 100px auto;
      background: #fff;
      border-radius: 12px;
      padding: 35px 30px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.08);
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
    <h4 class="text-center mb-4 fw-bold">🔐 Send OTP to Email</h4>

    @if (session('status'))
      <div class="alert alert-success mb-3">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.otp.send') }}">
      @csrf

      <!-- Email Input -->
      <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" name="email" id="email"
               class="form-control @error('email') is-invalid @enderror"
               required autofocus>
        @error('email')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>

      <!-- Submit Button -->
      <div class="d-grid mt-4">
        <button type="submit" class="btn btn-primary">
          📩 Send OTP
        </button>
      </div>
    </form>
  </div>

  <!-- Bootstrap Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
