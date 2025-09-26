<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Office Chat</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="/user/images/Office-Chat-fevicon.png">

  <!-- Bootstrap 5.3.3 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #e3f2fd, #ffffff);
      font-family: 'Inter', sans-serif;
    }

    .login-card {
      max-width: 420px;
      margin: 80px auto;
      background: #fff;
      border-radius: 16px;
      padding: 40px 30px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
      animation: slideFade 0.5s ease-in-out;
    }

    @keyframes slideFade {
      0% {
        transform: translateY(20px);
        opacity: 0;
      }
      100% {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .toggle-password {
      position: absolute;
      right: 12px;
      top: 38px;
      cursor: pointer;
      color: #6c757d;
    }
  </style>
</head>

<body>

  <div class="login-card">
    <div class="text-center mb-4">
      <img src="/user/images/office-chat-logo.png" alt="Office Chat" style="height: 50px; width: auto; margin-bottom: 15px;">
      <h4 class="fw-bold">Login to Your Account</h4>
    </div>

    <!-- Session Status -->
    @if (session('status'))
      <div class="alert alert-success mb-3">{{ session('status') }}</div>
    @endif

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}">
      @csrf

      <!-- Email -->
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" name="email"
               value="{{ old('email') }}"
               class="form-control @error('email') is-invalid @enderror"
               required autofocus>
        @error('email')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>

      <!-- Password -->
      <div class="mb-3 position-relative">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" name="password"
               class="form-control @error('password') is-invalid @enderror" required>
        <span class="toggle-password" onclick="togglePassword()">
          <i class="bi bi-eye-slash" id="eyeIcon"></i>
        </span>
        @error('password')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>

      <!-- Remember Me -->
      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
        <label class="form-check-label" for="remember_me">
          Remember me
        </label>
      </div>

      <!-- Login Button -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        @if (Route::has('password.request'))
          <a href="{{ route('password.request') }}" class="text-decoration-none small text-muted">
            Forgot your password?
          </a>
        @endif
        <button type="submit" class="btn btn-primary">
          Log In
        </button>
      </div>
    </form>

   

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function togglePassword() {
      const input = document.getElementById("password");
      const icon = document.getElementById("eyeIcon");
      if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
      } else {
        input.type = "password";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
      }
    }
  </script>
</body>
</html>
