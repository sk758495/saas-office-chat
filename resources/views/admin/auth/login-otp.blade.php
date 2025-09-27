<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login with OTP - Office Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-600 to-purple-700 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <i class="fas fa-mobile-alt text-4xl text-blue-600 mb-4"></i>
                <h2 class="text-3xl font-bold text-gray-800">Login with OTP</h2>
                <p class="text-gray-600 mt-2">Secure login using email verification</p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    @foreach($errors->all() as $error)
                        <p class="text-red-600 text-sm"><i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-green-600 text-sm"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</p>
                </div>
            @endif

            @if(!session('otp_sent'))
                <form method="POST" action="{{ route('admin.login-otp.send') }}">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-envelope mr-2"></i>Email Address
                        </label>
                        <input type="email" name="email" required value="{{ old('email') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                               placeholder="Enter your email">
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-paper-plane mr-2"></i>Send OTP
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.login-otp.verify') }}">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-key mr-2"></i>Enter OTP
                        </label>
                        <input type="text" name="otp" required maxlength="6"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 text-center text-2xl tracking-widest"
                               placeholder="000000">
                        <p class="text-sm text-gray-500 mt-2">OTP sent to your email address</p>
                    </div>

                    <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition duration-200">
                        <i class="fas fa-sign-in-alt mr-2"></i>Verify & Login
                    </button>
                </form>

                <div class="text-center mt-4">
                    <a href="{{ route('admin.login-otp') }}" class="text-blue-600 hover:underline text-sm">
                        <i class="fas fa-redo mr-1"></i>Send New OTP
                    </a>
                </div>
            @endif

            <div class="text-center mt-6">
                <a href="{{ route('admin.login') }}" class="text-blue-600 hover:underline">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>