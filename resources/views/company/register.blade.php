<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Registration - Office Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #ff6b35 0%, #f7931e 50%, #ff4757 100%); }
        .orange-gradient { background: linear-gradient(135deg, #ff9a56 0%, #ff6b35 100%); }
        .red-gradient { background: linear-gradient(135deg, #ff6b6b 0%, #ff4757 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .input-focus { transition: all 0.3s ease; }
        .input-focus:focus { transform: scale(1.02); }
        .animate-float { animation: float 6s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-20px); } }
        .error-input { border-color: #ef4444 !important; background-color: #fef2f2; }
        .success-input { border-color: #10b981 !important; background-color: #f0fdf4; }
        .tooltip { position: relative; }
        .tooltip:hover .tooltip-text { visibility: visible; opacity: 1; }
        .tooltip-text { visibility: hidden; opacity: 0; position: absolute; z-index: 1; bottom: 125%; left: 50%; margin-left: -60px; background-color: #374151; color: white; text-align: center; border-radius: 6px; padding: 5px 10px; font-size: 12px; transition: opacity 0.3s; }
    </style>
</head>
<body class="gradient-bg min-h-screen">
    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-white opacity-10 rounded-full animate-float"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-white opacity-5 rounded-full animate-float" style="animation-delay: -3s;"></div>
    </div>

    <div class="relative min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-20 w-20 bg-white rounded-full flex items-center justify-center mb-6 shadow-lg">
                    <i class="fas fa-building text-3xl text-indigo-600"></i>
                </div>
                <h2 class="text-4xl font-bold text-white mb-2">Join Office Chat</h2>
                <p class="text-indigo-100 text-lg">Transform your team communication today</p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg shadow-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-red-400 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('company.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- Company Information -->
                <div class="bg-white rounded-2xl shadow-xl p-8 card-hover">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-building text-2xl text-indigo-600 mr-3"></i>
                        <h3 class="text-2xl font-bold text-gray-800">Company Details</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-building mr-2"></i>Company Name <span class="text-red-500">*</span>
                                <span class="tooltip">
                                    <i class="fas fa-info-circle text-gray-400 ml-1 cursor-help"></i>
                                    <span class="tooltip-text">Enter your official company name</span>
                                </span>
                            </label>
                            <input type="text" name="company_name" required value="{{ old('company_name') }}"
                                   class="w-full px-4 py-3 border-2 rounded-xl focus:border-indigo-500 focus:outline-none input-focus {{ $errors->has('company_name') ? 'error-input border-red-500' : 'border-gray-200' }}"
                                   placeholder="Enter your company name">
                            @error('company_name')
                                <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-envelope mr-2"></i>Company Email <span class="text-red-500">*</span>
                                <span class="tooltip">
                                    <i class="fas fa-info-circle text-gray-400 ml-1 cursor-help"></i>
                                    <span class="tooltip-text">Official company email address</span>
                                </span>
                            </label>
                            <input type="email" name="company_email" required value="{{ old('company_email') }}"
                                   class="w-full px-4 py-3 border-2 rounded-xl focus:border-indigo-500 focus:outline-none input-focus {{ $errors->has('company_email') ? 'error-input border-red-500' : 'border-gray-200' }}"
                                   placeholder="company@domain.com">
                            @error('company_email')
                                <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-phone mr-2"></i>Phone Number
                                <span class="tooltip">
                                    <i class="fas fa-info-circle text-gray-400 ml-1 cursor-help"></i>
                                    <span class="tooltip-text">Company contact number (optional)</span>
                                </span>
                            </label>
                            <input type="text" name="company_phone" value="{{ old('company_phone') }}"
                                   class="w-full px-4 py-3 border-2 rounded-xl focus:border-indigo-500 focus:outline-none input-focus {{ $errors->has('company_phone') ? 'error-input border-red-500' : 'border-gray-200' }}"
                                   placeholder="+1 (555) 123-4567">
                            @error('company_phone')
                                <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-image mr-2"></i>Company Logo
                                <span class="tooltip">
                                    <i class="fas fa-info-circle text-gray-400 ml-1 cursor-help"></i>
                                    <span class="tooltip-text">Upload PNG, JPG (max 2MB)</span>
                                </span>
                            </label>
                            <input type="file" name="logo" accept="image/png,image/jpeg,image/jpg" 
                                   class="w-full px-4 py-3 border-2 rounded-xl focus:border-indigo-500 focus:outline-none {{ $errors->has('logo') ? 'error-input border-red-500' : 'border-gray-200' }}">
                            <p class="text-xs text-gray-500 mt-1">Recommended: 200x200px, PNG or JPG, max 2MB</p>
                            @error('logo')
                                <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-map-marker-alt mr-2"></i>Address
                                <span class="tooltip">
                                    <i class="fas fa-info-circle text-gray-400 ml-1 cursor-help"></i>
                                    <span class="tooltip-text">Company physical address (optional)</span>
                                </span>
                            </label>
                            <textarea name="company_address" rows="3" 
                                      class="w-full px-4 py-3 border-2 rounded-xl focus:border-indigo-500 focus:outline-none input-focus {{ $errors->has('company_address') ? 'error-input border-red-500' : 'border-gray-200' }}"
                                      placeholder="Enter company address">{{ old('company_address') }}</textarea>
                            @error('company_address')
                                <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Admin Account -->
                <div class="bg-white rounded-2xl shadow-xl p-8 card-hover">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-user-shield text-2xl text-indigo-600 mr-3"></i>
                        <h3 class="text-2xl font-bold text-gray-800">Admin Account</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user mr-2"></i>Admin Name <span class="text-red-500">*</span>
                                <span class="tooltip">
                                    <i class="fas fa-info-circle text-gray-400 ml-1 cursor-help"></i>
                                    <span class="tooltip-text">Full name of the admin user</span>
                                </span>
                            </label>
                            <input type="text" name="admin_name" required value="{{ old('admin_name') }}"
                                   class="w-full px-4 py-3 border-2 rounded-xl focus:border-indigo-500 focus:outline-none input-focus {{ $errors->has('admin_name') ? 'error-input border-red-500' : 'border-gray-200' }}"
                                   placeholder="Enter admin name">
                            @error('admin_name')
                                <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-envelope mr-2"></i>Admin Email <span class="text-red-500">*</span>
                                <span class="tooltip">
                                    <i class="fas fa-info-circle text-gray-400 ml-1 cursor-help"></i>
                                    <span class="tooltip-text">Admin login email address</span>
                                </span>
                            </label>
                            <input type="email" name="admin_email" required value="{{ old('admin_email') }}"
                                   class="w-full px-4 py-3 border-2 rounded-xl focus:border-indigo-500 focus:outline-none input-focus {{ $errors->has('admin_email') ? 'error-input border-red-500' : 'border-gray-200' }}"
                                   placeholder="admin@company.com">
                            @error('admin_email')
                                <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock mr-2"></i>Password <span class="text-red-500">*</span>
                                <span class="tooltip">
                                    <i class="fas fa-info-circle text-gray-400 ml-1 cursor-help"></i>
                                    <span class="tooltip-text">Min 8 chars, include uppercase, lowercase, number</span>
                                </span>
                            </label>
                            <div class="relative">
                                <input type="password" name="admin_password" required id="password"
                                       class="w-full px-4 py-3 border-2 rounded-xl focus:border-indigo-500 focus:outline-none input-focus {{ $errors->has('admin_password') ? 'error-input border-red-500' : 'border-gray-200' }}"
                                       placeholder="Create strong password">
                                <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye" id="password-eye"></i>
                                </button>
                            </div>
                            <div class="mt-1">
                                <div class="text-xs text-gray-500">
                                    <div id="length-check" class="flex items-center"><i class="fas fa-times text-red-400 mr-1"></i>At least 8 characters</div>
                                    <div id="uppercase-check" class="flex items-center"><i class="fas fa-times text-red-400 mr-1"></i>One uppercase letter</div>
                                    <div id="lowercase-check" class="flex items-center"><i class="fas fa-times text-red-400 mr-1"></i>One lowercase letter</div>
                                    <div id="number-check" class="flex items-center"><i class="fas fa-times text-red-400 mr-1"></i>One number</div>
                                </div>
                            </div>
                            @error('admin_password')
                                <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock mr-2"></i>Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" name="admin_password_confirmation" required id="confirm-password"
                                       class="w-full px-4 py-3 border-2 rounded-xl focus:border-indigo-500 focus:outline-none input-focus {{ $errors->has('admin_password_confirmation') ? 'error-input border-red-500' : 'border-gray-200' }}"
                                       placeholder="Confirm password">
                                <button type="button" onclick="togglePassword('confirm-password')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye" id="confirm-password-eye"></i>
                                </button>
                            </div>
                            <div id="password-match" class="text-xs mt-1 hidden">
                                <div class="flex items-center text-red-500"><i class="fas fa-times mr-1"></i>Passwords do not match</div>
                            </div>
                            @error('admin_password_confirmation')
                                <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Subscription Plans -->
                <div class="bg-white rounded-2xl shadow-xl p-8 card-hover">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-crown text-2xl text-indigo-600 mr-3"></i>
                        <h3 class="text-2xl font-bold text-gray-800">Choose Your Plan</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Free Plan -->
                        <div class="relative">
                            <input type="radio" name="plan" value="free" id="free" checked class="sr-only">
                            <label for="free" class="block cursor-pointer">
                                <div class="border-2 border-gray-200 rounded-xl p-6 hover:border-indigo-500 transition-all duration-300">
                                    <div class="text-center">
                                        <i class="fas fa-gift text-3xl text-green-500 mb-3"></i>
                                        <h4 class="text-xl font-bold text-gray-800 mb-2">Free Plan</h4>
                                        <p class="text-3xl font-bold text-green-600 mb-4">$0<span class="text-sm text-gray-500">/year</span></p>
                                        <ul class="text-sm text-gray-600 space-y-2">
                                            <li><i class="fas fa-check text-green-500 mr-2"></i>5 Users</li>
                                            <li><i class="fas fa-check text-green-500 mr-2"></i>100MB Storage</li>
                                            <li><i class="fas fa-check text-green-500 mr-2"></i>Basic Chat</li>
                                        </ul>
                                    </div>
                                </div>
                            </label>
                        </div>
                        
                        <!-- Paid Plan -->
                        <div class="relative">
                            <input type="radio" name="plan" value="paid" id="paid" class="sr-only">
                            <label for="paid" class="block cursor-pointer">
                                <div class="border-2 border-indigo-500 rounded-xl p-6 bg-gradient-to-br from-indigo-50 to-purple-50 relative overflow-hidden">
                                    <div class="absolute top-0 right-0 bg-indigo-500 text-white px-3 py-1 text-xs font-bold rounded-bl-lg">
                                        POPULAR
                                    </div>
                                    <div class="text-center">
                                        <i class="fas fa-star text-3xl text-indigo-500 mb-3"></i>
                                        <h4 class="text-xl font-bold text-gray-800 mb-2">Premium Plan</h4>
                                        <p class="text-3xl font-bold text-indigo-600 mb-4">$999<span class="text-sm text-gray-500">/year</span></p>
                                        <ul class="text-sm text-gray-600 space-y-2">
                                            <li><i class="fas fa-check text-indigo-500 mr-2"></i>Unlimited Users</li>
                                            <li><i class="fas fa-check text-indigo-500 mr-2"></i>Unlimited Storage</li>
                                            <li><i class="fas fa-check text-indigo-500 mr-2"></i>Advanced Features</li>
                                            <li><i class="fas fa-check text-indigo-500 mr-2"></i>Priority Support</li>
                                        </ul>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" id="submit-btn" class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-bold py-4 px-12 rounded-2xl text-lg shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                        <i class="fas fa-rocket mr-2"></i><span id="btn-text">Start Your Journey</span>
                        <i class="fas fa-spinner fa-spin ml-2 hidden" id="loading-spinner"></i>
                    </button>
                    <p class="text-orange-100 text-sm mt-4">Already have an account? <a href="{{ route('admin.login') }}" class="text-white font-semibold hover:underline">Sign In</a></p>
                    <p class="text-orange-100 text-xs mt-2"><i class="fas fa-shield-alt mr-1"></i>Your data is secure and encrypted</p>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Plan selection animation
        document.querySelectorAll('input[name="plan"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('label[for="free"], label[for="paid"]').forEach(label => {
                    label.querySelector('div').classList.remove('ring-4', 'ring-indigo-300');
                });
                if(this.checked) {
                    document.querySelector(`label[for="${this.id}"] div`).classList.add('ring-4', 'ring-indigo-300');
                }
            });
        });
        
        // Initialize free plan selection
        document.querySelector('label[for="free"] div').classList.add('ring-4', 'ring-green-300');

        // Password visibility toggle
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const eye = document.getElementById(fieldId + '-eye');
            
            if (field.type === 'password') {
                field.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }

        // Password strength validation
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const checks = {
                'length-check': password.length >= 8,
                'uppercase-check': /[A-Z]/.test(password),
                'lowercase-check': /[a-z]/.test(password),
                'number-check': /\d/.test(password)
            };
            
            Object.keys(checks).forEach(checkId => {
                const element = document.getElementById(checkId);
                const icon = element.querySelector('i');
                
                if (checks[checkId]) {
                    icon.classList.remove('fa-times', 'text-red-400');
                    icon.classList.add('fa-check', 'text-green-500');
                    element.classList.remove('text-red-500');
                    element.classList.add('text-green-600');
                } else {
                    icon.classList.remove('fa-check', 'text-green-500');
                    icon.classList.add('fa-times', 'text-red-400');
                    element.classList.remove('text-green-600');
                    element.classList.add('text-red-500');
                }
            });
        });

        // Password confirmation validation
        document.getElementById('confirm-password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const matchDiv = document.getElementById('password-match');
            
            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    matchDiv.innerHTML = '<div class="flex items-center text-green-600"><i class="fas fa-check mr-1"></i>Passwords match</div>';
                    matchDiv.classList.remove('hidden');
                } else {
                    matchDiv.innerHTML = '<div class="flex items-center text-red-500"><i class="fas fa-times mr-1"></i>Passwords do not match</div>';
                    matchDiv.classList.remove('hidden');
                }
            } else {
                matchDiv.classList.add('hidden');
            }
        });

        // Form submission with loading state
        document.querySelector('form').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const spinner = document.getElementById('loading-spinner');
            
            submitBtn.disabled = true;
            btnText.textContent = 'Creating Account...';
            spinner.classList.remove('hidden');
        });

        // Real-time validation feedback
        document.querySelectorAll('input[required], textarea[required]').forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.classList.add('error-input', 'border-red-500');
                    this.classList.remove('success-input', 'border-green-500');
                } else {
                    this.classList.remove('error-input', 'border-red-500');
                    this.classList.add('success-input', 'border-green-500');
                }
            });
        });
    </script>
</body>
</html>