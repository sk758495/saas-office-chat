{{-- resources/views/admin/auth/verify-otp.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Admin OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header text-center">
                    <h4>Email OTP Verification</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.otp.verify') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="otp" class="form-label">Enter OTP</label>
                            <input id="otp" type="text" class="form-control" name="otp" required>
                            @error('otp')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning">Verify OTP</button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('admin.otp.resend') }}" class="mt-3 text-center">
                        @csrf
                        <button type="submit" class="btn btn-link">Resend OTP</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>