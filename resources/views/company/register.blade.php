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
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    @foreach($errors->all() as $error)
                        <p class="text-sm">{{ $error }}</p>
                    @endforeach
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
                                <i class="fas fa-building mr-2"></i>Company Name
                            </label>
                            <input type="text" name="company_name" required 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none input-focus"
                                   placeholder="Enter your company name">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-envelope mr-2"></i>Company Email
                            </label>
                            <input type="email" name="company_email" required 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none input-focus"
                                   placeholder="company@domain.com">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-phone mr-2"></i>Phone Number
                            </label>
                            <input type="text" name="company_phone" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none input-focus"
                                   placeholder="+1 (555) 123-4567">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-image mr-2"></i>Company Logo
                            </label>
                            <input type="file" name="logo" accept="image/*" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-map-marker-alt mr-2"></i>Address
                            </label>
                            <textarea name="company_address" rows="3" 
                                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none input-focus"
                                      placeholder="Enter company address"></textarea>
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
                                <i class="fas fa-user mr-2"></i>Admin Name
                            </label>
                            <input type="text" name="admin_name" required 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none input-focus"
                                   placeholder="Enter admin name">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-envelope mr-2"></i>Admin Email
                            </label>
                            <input type="email" name="admin_email" required 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none input-focus"
                                   placeholder="admin@company.com">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock mr-2"></i>Password
                            </label>
                            <input type="password" name="admin_password" required 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none input-focus"
                                   placeholder="Create strong password">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock mr-2"></i>Confirm Password
                            </label>
                            <input type="password" name="admin_password_confirmation" required 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none input-focus"
                                   placeholder="Confirm password">
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
                    <button type="submit" class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-bold py-4 px-12 rounded-2xl text-lg shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-rocket mr-2"></i>Start Your Journey
                    </button>
                    <p class="text-orange-100 text-sm mt-4">Already have an account? <a href="{{ route('admin.login') }}" class="text-white font-semibold hover:underline">Sign In</a></p>
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
    </script>
</body>
</html>