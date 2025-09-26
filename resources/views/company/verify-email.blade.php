<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Company Email</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Verify Company Email
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    We sent a verification code to<br>
                    <strong>{{ $company->email }}</strong>
                </p>
            </div>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            
            <form class="mt-8 space-y-6" action="{{ route('company.verify-email.submit') }}" method="POST">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Enter 6-digit verification code
                    </label>
                    <input type="text" name="otp" required maxlength="6" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-center text-2xl font-mono tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="000000" autocomplete="off">
                </div>
                
                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Verify Email
                    </button>
                </div>
                
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Didn't receive the code?
                        <a href="{{ route('company.resend-otp') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Resend OTP
                        </a>
                    </p>
                </div>
            </form>
            
            <div class="text-center">
                <a href="{{ route('company.register') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    ← Back to Registration
                </a>
            </div>
        </div>
    </div>
</body>
</html>