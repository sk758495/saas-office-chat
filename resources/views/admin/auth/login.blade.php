<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Login - Office Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #ff6b35 0%, #f7931e 50%, #ff4757 100%); }
        .glass-effect { backdrop-filter: blur(20px); background: rgba(255, 255, 255, 0.1); }
        .input-glow:focus { box-shadow: 0 0 20px rgba(102, 126, 234, 0.4); }
        .animate-pulse-slow { animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        .animate-bounce-slow { animation: bounce 3s infinite; }
        .floating { animation: floating 3s ease-in-out infinite; }
        @keyframes floating { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-10px); } }
    </style>
</head>
<body class="gradient-bg min-h-screen relative overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0">
        <div class="absolute top-20 left-20 w-32 h-32 bg-white opacity-10 rounded-full animate-pulse-slow"></div>
        <div class="absolute top-40 right-32 w-20 h-20 bg-white opacity-20 rounded-full animate-bounce-slow"></div>
        <div class="absolute bottom-32 left-40 w-24 h-24 bg-white opacity-15 rounded-full floating"></div>
        <div class="absolute bottom-20 right-20 w-16 h-16 bg-white opacity-25 rounded-full animate-pulse-slow" style="animation-delay: 1s;"></div>
    </div>

    <div class="relative min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo and Header -->
            <div class="text-center">
                <div class="mx-auto h-24 w-24 glass-effect rounded-full flex items-center justify-center mb-6 border border-white border-opacity-20">
                    <i class="fas fa-comments text-4xl text-white"></i>
                </div>
                <h2 class="text-4xl font-bold text-white mb-2">Welcome Back</h2>
                <p class="text-indigo-100 text-lg">Sign in to your company dashboard</p>
            </div>

            <!-- Login Form -->
            <div class="glass-effect rounded-2xl shadow-2xl p-8 border border-white border-opacity-20">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        @foreach($errors->all() as $error)
                            <p class="text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.attempt') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold text-white mb-2">
                            <i class="fas fa-envelope mr-2"></i>Email Address
                        </label>
                        <div class="relative">
                            <input id="email" type="email" name="email" required autofocus
                                   class="w-full px-4 py-3 pl-12 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-xl text-white placeholder-indigo-200 focus:outline-none focus:border-white focus:bg-opacity-30 input-glow transition-all duration-300"
                                   placeholder="Enter your email">
                            <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-indigo-200"></i>
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-white mb-2">
                            <i class="fas fa-lock mr-2"></i>Password
                        </label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required
                                   class="w-full px-4 py-3 pl-12 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-xl text-white placeholder-indigo-200 focus:outline-none focus:border-white focus:bg-opacity-30 input-glow transition-all duration-300"
                                   placeholder="Enter your password">
                            <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-indigo-200"></i>
                            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-indigo-200 hover:text-white transition-colors">
                                <i id="toggleIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center text-sm text-indigo-100">
                            <input type="checkbox" class="mr-2 rounded border-white border-opacity-30 bg-white bg-opacity-20 text-indigo-600 focus:ring-indigo-500">
                            Remember me
                        </label>
                        <a href="{{ route('admin.forgot-password') }}" class="text-sm text-white hover:text-indigo-200 transition-colors">
                            Forgot password?
                        </a>
                    </div>

                    <button type="submit" class="w-full bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-bold py-3 px-6 rounded-xl border border-white border-opacity-30 hover:border-opacity-50 transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                    </button>
                </form>

                <!-- Alternative Login Options -->
                <div class="mt-6">
                    <div class="text-center">
                        <p class="text-indigo-100 text-sm mb-4">Or login with</p>
                        <a href="{{ route('admin.login-otp') }}" class="w-full inline-flex justify-center items-center bg-gradient-to-r from-green-500 to-teal-500 hover:from-green-600 hover:to-teal-600 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-mobile-alt mr-2"></i>Login with OTP
                        </a>
                    </div>
                </div>

                <!-- Divider -->
                <div class="mt-8 pt-6 border-t border-white border-opacity-20">
                    <div class="text-center">
                        <p class="text-indigo-100 text-sm mb-4">New to Office Chat?</p>
                        <a href="{{ route('company.register') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-plus mr-2"></i>Register Your Company
                        </a>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="grid grid-cols-3 gap-4 mt-8">
                <div class="text-center">
                    <div class="glass-effect rounded-lg p-4 border border-white border-opacity-20">
                        <i class="fas fa-shield-alt text-2xl text-white mb-2"></i>
                        <p class="text-xs text-indigo-100">Secure</p>
                    </div>
                </div>
                <div class="text-center">
                    <div class="glass-effect rounded-lg p-4 border border-white border-opacity-20">
                        <i class="fas fa-bolt text-2xl text-white mb-2"></i>
                        <p class="text-xs text-indigo-100">Fast</p>
                    </div>
                </div>
                <div class="text-center">
                    <div class="glass-effect rounded-lg p-4 border border-white border-opacity-20">
                        <i class="fas fa-users text-2xl text-white mb-2"></i>
                        <p class="text-xs text-indigo-100">Team-First</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Add floating animation to form on load
        window.addEventListener('load', function() {
            document.querySelector('.glass-effect').style.animation = 'floating 6s ease-in-out infinite';
        });
    </script>
</body>
</html>