<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Office Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .btn-loading {
            position: relative;
            pointer-events: none;
        }
        .btn-loading .btn-text {
            opacity: 0;
        }
        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .form-loading {
            opacity: 0.6;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-user me-2"></i>Profile Settings</h4>
                        <a href="{{ route('chat.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Chat
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Profile Photo Section -->
                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <img id="profilePhoto" 
                                     src="{{ $user->profile_photo ? '/storage/' . $user->profile_photo : 'https://via.placeholder.com/150' }}" 
                                     class="rounded-circle" width="150" height="150" style="object-fit: cover;">
                                <button class="btn btn-primary btn-sm position-absolute bottom-0 end-0 rounded-circle" 
                                        onclick="document.getElementById('photoInput').click()">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                            <input type="file" id="photoInput" class="d-none" accept="image/*">
                            <h5 class="mt-2">{{ $user->name }}</h5>
                            <p class="text-muted">{{ $user->department->name ?? 'No Department' }} - {{ $user->designation->name ?? 'No Designation' }}</p>
                        </div>

                        <!-- Account Information Section -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6><i class="fas fa-user-edit me-2"></i>Account Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <div class="input-group">
                                            <input type="email" class="form-control" id="emailInput" value="{{ $user->email }}">
                                            <span class="input-group-text" id="emailVerifiedIcon" style="display: none;">
                                                <i class="fas fa-check-circle text-primary"></i>
                                            </span>
                                            <button class="btn btn-outline-primary" onclick="startEmailUpdate()">
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <button class="btn btn-outline-secondary w-100" onclick="startPasswordChange()">
                                            <i class="fas fa-key me-1"></i>Change Password
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Lock Section -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6><i class="fas fa-lock me-2"></i>Chat Security</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <strong>Chat Lock</strong>
                                        <br><small class="text-muted">Protect your chats with a 4-digit PIN</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="chatLockToggle" 
                                               {{ $user->chat_lock_enabled ? 'checked' : '' }}>
                                    </div>
                                </div>
                                
                                @if($user->chat_pin)
                                    <div class="mb-2">
                                        <small class="text-muted">Current PIN: </small>
                                        <span id="pinDisplay" class="font-monospace">••••</span>
                                        <button class="btn btn-link btn-sm p-0 ms-1" onclick="togglePinVisibility()">
                                            <i id="pinToggleIcon" class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm me-2" onclick="showChangePinModal()">
                                        <i class="fas fa-key me-1"></i>Change PIN
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" onclick="removePin()">
                                        <i class="fas fa-trash me-1"></i>Remove PIN
                                    </button>
                                @else
                                    <button class="btn btn-primary btn-sm" onclick="showSetPinModal()">
                                        <i class="fas fa-plus me-1"></i>Set PIN
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Logout Button -->
                        <div class="text-center">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PIN Modal -->
    <div class="modal fade" id="pinModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pinModalTitle">Set PIN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="pinForm">
                        <div class="mb-3">
                            <label class="form-label">4-Digit PIN</label>
                            <input type="password" class="form-control text-center" id="pin" maxlength="4" pattern="[0-9]{4}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm PIN</label>
                            <input type="password" class="form-control text-center" id="confirmPin" maxlength="4" pattern="[0-9]{4}">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save PIN</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Verification Modal -->
    <div class="modal fade" id="emailVerificationModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verify Current Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">We've sent a verification code to your current email address.</p>
                    <form id="emailVerificationForm">
                        <div class="mb-3">
                            <label class="form-label">Enter 6-digit OTP</label>
                            <input type="text" class="form-control text-center" id="emailOtp" maxlength="6" pattern="[0-9]{6}" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Verify</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- New Email Verification Modal -->
    <div class="modal fade" id="newEmailVerificationModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verify New Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">We've sent a verification code to your new email address.</p>
                    <form id="newEmailVerificationForm">
                        <div class="mb-3">
                            <label class="form-label">Enter 6-digit OTP</label>
                            <input type="text" class="form-control text-center" id="newEmailOtp" maxlength="6" pattern="[0-9]{6}" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Verify & Update Email</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- PIN Verification Modal -->
    <div class="modal fade" id="pinVerificationModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">🔒 Enter Your PIN</h5>
                </div>
                <div class="modal-body">
                    <p class="text-muted text-center">Please enter your 4-digit PIN to access chat</p>
                    <form id="pinVerificationForm">
                        <div class="mb-3">
                            <input type="password" class="form-control text-center" id="verifyPin" maxlength="4" pattern="[0-9]{4}" placeholder="••••" style="font-size: 24px; letter-spacing: 8px;">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Verify PIN</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Change Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="passwordForm">
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="currentPassword" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" id="newPassword" minlength="8" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirmPassword" minlength="8" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Check if PIN verification is required
        @if(session('pin_required'))
            new bootstrap.Modal(document.getElementById('pinVerificationModal')).show();
        @endif

        // Loader utility functions
        function showButtonLoader(button) {
            button.classList.add('btn-loading');
            button.disabled = true;
            if (!button.querySelector('.btn-text')) {
                button.innerHTML = `<span class="btn-text">${button.innerHTML}</span>`;
            }
        }

        function hideButtonLoader(button) {
            button.classList.remove('btn-loading');
            button.disabled = false;
        }

        function showFormLoader(form) {
            form.classList.add('form-loading');
        }

        function hideFormLoader(form) {
            form.classList.remove('form-loading');
        }

        // Profile photo upload
        document.getElementById('photoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const photoImg = document.getElementById('profilePhoto');
                const originalSrc = photoImg.src;
                
                // Show loading overlay
                photoImg.style.opacity = '0.5';
                photoImg.style.filter = 'blur(2px)';
                
                const formData = new FormData();
                formData.append('profile_photo', file);
                
                fetch('/profile/photo', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        photoImg.src = data.photo_url;
                    } else {
                        alert('Failed to upload photo');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Upload failed');
                })
                .finally(() => {
                    photoImg.style.opacity = '1';
                    photoImg.style.filter = 'none';
                });
            }
        });

        // Chat lock toggle
        document.getElementById('chatLockToggle').addEventListener('change', function() {
            const toggle = this;
            const originalState = !toggle.checked;
            
            toggle.disabled = true;
            
            fetch('/profile/toggle-lock', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    toggle.checked = originalState;
                } else {
                    toggle.checked = data.enabled;
                    if (data.message) alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toggle.checked = originalState;
                alert('An error occurred');
            })
            .finally(() => {
                toggle.disabled = false;
            });
        });

        // PIN verification form
        document.getElementById('pinVerificationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const pin = document.getElementById('verifyPin').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            
            if (pin.length !== 4) {
                alert('Please enter a 4-digit PIN');
                return;
            }
            
            showButtonLoader(submitBtn);
            showFormLoader(this);
            
            fetch('/profile/verify-pin', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ pin })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('pinVerificationModal')).hide();
                    window.location.href = '/chat';
                } else {
                    alert(data.message || 'Invalid PIN');
                    document.getElementById('verifyPin').value = '';
                    document.getElementById('verifyPin').focus();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            })
            .finally(() => {
                hideButtonLoader(submitBtn);
                hideFormLoader(this);
            });
        });

        // PIN modals
        function showSetPinModal() {
            document.getElementById('pinModalTitle').textContent = 'Set PIN';
            new bootstrap.Modal(document.getElementById('pinModal')).show();
        }

        function showChangePinModal() {
            document.getElementById('pinModalTitle').textContent = 'Change PIN';
            new bootstrap.Modal(document.getElementById('pinModal')).show();
        }

        // PIN form submission
        document.getElementById('pinForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const pin = document.getElementById('pin').value;
            const confirmPin = document.getElementById('confirmPin').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            
            if (pin !== confirmPin) {
                alert('PINs do not match');
                return;
            }
            
            showButtonLoader(submitBtn);
            showFormLoader(this);
            
            fetch('/profile/pin', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ pin, confirm_pin: confirmPin })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'PIN saved successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('pinModal')).hide();
                    location.reload();
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            })
            .finally(() => {
                hideButtonLoader(submitBtn);
                hideFormLoader(this);
            });
        });
        // PIN visibility toggle
        let pinVisible = false;
        function togglePinVisibility() {
            const pinDisplay = document.getElementById('pinDisplay');
            const toggleIcon = document.getElementById('pinToggleIcon');
            
            if (pinVisible) {
                pinDisplay.textContent = '••••';
                toggleIcon.className = 'fas fa-eye';
            } else {
                pinDisplay.textContent = '{{ $user->chat_pin ?? "" }}';
                toggleIcon.className = 'fas fa-eye-slash';
            }
            pinVisible = !pinVisible;
        }

        // Remove PIN
        function removePin() {
            if (confirm('Are you sure you want to remove your PIN? This will disable chat lock.')) {
                fetch('/profile/remove-pin', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    }
                });
            }
        }

        // Start email update process
        function startEmailUpdate() {
            const btn = document.querySelector('[onclick="startEmailUpdate()"]');
            showButtonLoader(btn);
            
            fetch('/profile/send-current-email-verification', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    new bootstrap.Modal(document.getElementById('emailVerificationModal')).show();
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            })
            .finally(() => {
                hideButtonLoader(btn);
            });
        }

        // Start password change process
        function startPasswordChange() {
            const btn = document.querySelector('[onclick="startPasswordChange()"]');
            showButtonLoader(btn);
            
            fetch('/profile/send-current-email-verification', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('emailVerificationModal').setAttribute('data-next-action', 'password');
                    new bootstrap.Modal(document.getElementById('emailVerificationModal')).show();
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            })
            .finally(() => {
                hideButtonLoader(btn);
            });
        }

        // Email verification form
        document.getElementById('emailVerificationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const otp = document.getElementById('emailOtp').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            
            showButtonLoader(submitBtn);
            showFormLoader(this);
            
            fetch('/profile/verify-current-email', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ otp })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show verified icon
                    document.getElementById('emailVerifiedIcon').style.display = 'flex';
                    
                    bootstrap.Modal.getInstance(document.getElementById('emailVerificationModal')).hide();
                    
                    const nextAction = document.getElementById('emailVerificationModal').getAttribute('data-next-action');
                    if (nextAction === 'password') {
                        new bootstrap.Modal(document.getElementById('passwordModal')).show();
                    } else {
                        updateEmail();
                    }
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            })
            .finally(() => {
                hideButtonLoader(submitBtn);
                hideFormLoader(this);
            });
        });

        // Update email after verification
        function updateEmail() {
            const email = document.getElementById('emailInput').value;
            
            fetch('/profile/email', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    new bootstrap.Modal(document.getElementById('newEmailVerificationModal')).show();
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating email');
            });
        }

        // New email verification form
        document.getElementById('newEmailVerificationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const otp = document.getElementById('newEmailOtp').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            
            showButtonLoader(submitBtn);
            showFormLoader(this);
            
            fetch('/profile/verify-new-email', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ otp })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    bootstrap.Modal.getInstance(document.getElementById('newEmailVerificationModal')).hide();
                    location.reload();
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            })
            .finally(() => {
                hideButtonLoader(submitBtn);
                hideFormLoader(this);
            });
        });

        // Password form submission
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            
            if (newPassword !== confirmPassword) {
                alert('New passwords do not match');
                return;
            }
            
            showButtonLoader(submitBtn);
            showFormLoader(this);
            
            fetch('/profile/password', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    current_password: currentPassword,
                    password: newPassword,
                    password_confirmation: confirmPassword
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    bootstrap.Modal.getInstance(document.getElementById('passwordModal')).hide();
                    document.getElementById('passwordForm').reset();
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating password');
            })
            .finally(() => {
                hideButtonLoader(submitBtn);
                hideFormLoader(this);
            });
        });
    </script>
</body>
</html>