<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password - Seller Sathi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #f0f4ff, #dceefb);
      font-family: 'Inter', sans-serif;
    }
    .reset-card {
      max-width: 480px;
      margin: 90px auto;
      background: #fff;
      padding: 35px 30px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .password-toggle {
      cursor: pointer;
      position: absolute;
      right: 15px;
      top: 38px;
    }
  </style>
</head>

<body>
  <div class="reset-card">
    <h4 class="text-center fw-bold mb-4">🔒 Reset Your Password</h4>

    <form method="POST" action="{{ route('password.store') }}">
      @csrf
      <input type="hidden" name="email" value="{{ session('password_reset_email') }}">

      <!-- New Password -->
      <div class="mb-3 position-relative">
        <label for="password" class="form-label">New Password</label>
        <input type="password" name="password" id="password"
               class="form-control @error('password') is-invalid @enderror"
               required>
        <i class="bi bi-eye-fill password-toggle" onclick="togglePassword('password')"></i>
        @error('password')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>

      <!-- Confirm Password -->
      <div class="mb-4 position-relative">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation"
               class="form-control @error('password_confirmation') is-invalid @enderror"
               required>
        <i class="bi bi-eye-fill password-toggle" onclick="togglePassword('password_confirmation')"></i>
        @error('password_confirmation')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>

      <!-- Submit -->
      <div class="d-grid">
        <button type="submit" class="btn btn-primary">
          🔐 Reset Password
        </button>
      </div>
    </form>
  </div>

  <!-- Bootstrap + Icons -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />

  <!-- Toggle Password Script -->
  <script>
    function togglePassword(id) {
      const input = document.getElementById(id);
      input.type = input.type === 'password' ? 'text' : 'password';
    }
  </script>
</body>
</html>
