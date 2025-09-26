<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset PIN - Office Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Reset Your PIN</h4>
                    </div>
                    <div class="card-body">
                        <form id="resetPinForm">
                            <div class="mb-3">
                                <label class="form-label">New 4-Digit PIN</label>
                                <input type="password" class="form-control text-center" id="pin" maxlength="4" pattern="[0-9]{4}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm PIN</label>
                                <input type="password" class="form-control text-center" id="confirmPin" maxlength="4" pattern="[0-9]{4}" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Reset PIN</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('resetPinForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const pin = document.getElementById('pin').value;
            const confirmPin = document.getElementById('confirmPin').value;
            
            if (pin !== confirmPin) {
                alert('PINs do not match');
                return;
            }
            
            fetch('/reset-pin/{{ $token }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ pin, confirm_pin: confirmPin })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('PIN reset successfully!');
                    window.location.href = '/chat';
                }
            });
        });
    </script>
</body>
</html>