@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Company Settings</h1>
        <p class="text-gray-600 mt-2">Manage your company information and preferences</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Company Information -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Basic Info -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="orange-gradient px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-building mr-3"></i>Company Information
                    </h2>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('company.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-building text-orange-500 mr-2"></i>Company Name
                                </label>
                                <input type="text" name="name" value="{{ old('name', $company->name) }}" 
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:outline-none transition-colors" required>
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-envelope text-orange-500 mr-2"></i>Email
                                </label>
                                <input type="email" name="email" value="{{ old('email', $company->email) }}" 
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:outline-none transition-colors" required>
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-phone text-orange-500 mr-2"></i>Phone
                                </label>
                                <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" 
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:outline-none transition-colors">
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-image text-orange-500 mr-2"></i>Company Logo
                                </label>
                                <input type="file" name="logo" accept="image/*" 
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:outline-none transition-colors">
                                @error('logo')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>Address
                            </label>
                            <textarea name="address" rows="3" 
                                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:outline-none transition-colors">{{ old('address', $company->address) }}</textarea>
                            @error('address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <button type="submit" class="orange-gradient text-white px-8 py-3 rounded-xl font-medium hover:shadow-lg transition-all">
                                <i class="fas fa-save mr-2"></i>Update Information
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Chat Theme Customization -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="red-gradient px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-palette mr-3"></i>Chat Theme Customization
                    </h2>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('company.chat-theme.update') }}" method="POST" id="themeForm">
                        @csrf
                        
                        <!-- Predefined Themes -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Choose a Theme</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <!-- Orange Sunset -->
                                <div class="theme-option cursor-pointer border-2 rounded-xl p-4 transition-all hover:shadow-lg" 
                                     data-primary="#ff6b35" data-secondary="#f7931e" data-name="Orange Sunset">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <div class="w-6 h-6 rounded-full" style="background: linear-gradient(135deg, #ff6b35, #f7931e)"></div>
                                        <span class="font-medium text-sm">Orange Sunset</span>
                                    </div>
                                    <div class="bg-gray-100 rounded-lg p-3">
                                        <div class="w-full h-2 rounded-full mb-2" style="background: linear-gradient(135deg, #ff6b35, #f7931e)"></div>
                                        <div class="flex space-x-1">
                                            <div class="w-8 h-4 bg-orange-200 rounded"></div>
                                            <div class="w-12 h-4 bg-orange-300 rounded"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Blue Ocean -->
                                <div class="theme-option cursor-pointer border-2 rounded-xl p-4 transition-all hover:shadow-lg" 
                                     data-primary="#3b82f6" data-secondary="#1d4ed8" data-name="Blue Ocean">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <div class="w-6 h-6 rounded-full" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8)"></div>
                                        <span class="font-medium text-sm">Blue Ocean</span>
                                    </div>
                                    <div class="bg-gray-100 rounded-lg p-3">
                                        <div class="w-full h-2 rounded-full mb-2" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8)"></div>
                                        <div class="flex space-x-1">
                                            <div class="w-8 h-4 bg-blue-200 rounded"></div>
                                            <div class="w-12 h-4 bg-blue-300 rounded"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Purple Galaxy -->
                                <div class="theme-option cursor-pointer border-2 rounded-xl p-4 transition-all hover:shadow-lg" 
                                     data-primary="#8b5cf6" data-secondary="#7c3aed" data-name="Purple Galaxy">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <div class="w-6 h-6 rounded-full" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed)"></div>
                                        <span class="font-medium text-sm">Purple Galaxy</span>
                                    </div>
                                    <div class="bg-gray-100 rounded-lg p-3">
                                        <div class="w-full h-2 rounded-full mb-2" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed)"></div>
                                        <div class="flex space-x-1">
                                            <div class="w-8 h-4 bg-purple-200 rounded"></div>
                                            <div class="w-12 h-4 bg-purple-300 rounded"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Green Forest -->
                                <div class="theme-option cursor-pointer border-2 rounded-xl p-4 transition-all hover:shadow-lg" 
                                     data-primary="#10b981" data-secondary="#059669" data-name="Green Forest">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <div class="w-6 h-6 rounded-full" style="background: linear-gradient(135deg, #10b981, #059669)"></div>
                                        <span class="font-medium text-sm">Green Forest</span>
                                    </div>
                                    <div class="bg-gray-100 rounded-lg p-3">
                                        <div class="w-full h-2 rounded-full mb-2" style="background: linear-gradient(135deg, #10b981, #059669)"></div>
                                        <div class="flex space-x-1">
                                            <div class="w-8 h-4 bg-green-200 rounded"></div>
                                            <div class="w-12 h-4 bg-green-300 rounded"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pink Rose -->
                                <div class="theme-option cursor-pointer border-2 rounded-xl p-4 transition-all hover:shadow-lg" 
                                     data-primary="#ec4899" data-secondary="#be185d" data-name="Pink Rose">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <div class="w-6 h-6 rounded-full" style="background: linear-gradient(135deg, #ec4899, #be185d)"></div>
                                        <span class="font-medium text-sm">Pink Rose</span>
                                    </div>
                                    <div class="bg-gray-100 rounded-lg p-3">
                                        <div class="w-full h-2 rounded-full mb-2" style="background: linear-gradient(135deg, #ec4899, #be185d)"></div>
                                        <div class="flex space-x-1">
                                            <div class="w-8 h-4 bg-pink-200 rounded"></div>
                                            <div class="w-12 h-4 bg-pink-300 rounded"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dark Mode -->
                                <div class="theme-option cursor-pointer border-2 rounded-xl p-4 transition-all hover:shadow-lg" 
                                     data-primary="#374151" data-secondary="#1f2937" data-name="Dark Mode">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <div class="w-6 h-6 rounded-full" style="background: linear-gradient(135deg, #374151, #1f2937)"></div>
                                        <span class="font-medium text-sm">Dark Mode</span>
                                    </div>
                                    <div class="bg-gray-100 rounded-lg p-3">
                                        <div class="w-full h-2 rounded-full mb-2" style="background: linear-gradient(135deg, #374151, #1f2937)"></div>
                                        <div class="flex space-x-1">
                                            <div class="w-8 h-4 bg-gray-400 rounded"></div>
                                            <div class="w-12 h-4 bg-gray-500 rounded"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Custom Colors -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Primary Color</label>
                                <input type="color" name="chat_primary_color" id="primaryColor" 
                                       value="{{ old('chat_primary_color', $company->chat_primary_color) }}"
                                       class="w-full h-12 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:outline-none">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Secondary Color</label>
                                <input type="color" name="chat_secondary_color" id="secondaryColor" 
                                       value="{{ old('chat_secondary_color', $company->chat_secondary_color) }}"
                                       class="w-full h-12 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:outline-none">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Theme Name</label>
                                <input type="text" name="chat_theme_name" id="themeName" 
                                       value="{{ old('chat_theme_name', $company->chat_theme_name) }}"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:outline-none transition-colors"
                                       placeholder="Custom Theme">
                            </div>
                        </div>

                        <!-- Preview -->
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Preview</h3>
                            <div class="bg-gray-100 rounded-xl p-6">
                                <div id="chatPreview" class="max-w-md mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
                                    <div id="previewHeader" class="px-4 py-3 text-white font-medium">
                                        Chat Header
                                    </div>
                                    <div class="p-4 space-y-3">
                                        <div class="flex justify-end">
                                            <div id="previewMessage1" class="px-4 py-2 rounded-lg text-white max-w-xs">
                                                Your message
                                            </div>
                                        </div>
                                        <div class="flex justify-start">
                                            <div class="px-4 py-2 bg-gray-200 rounded-lg max-w-xs">
                                                Received message
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <button type="submit" class="red-gradient text-white px-8 py-3 rounded-xl font-medium hover:shadow-lg transition-all">
                                <i class="fas fa-palette mr-2"></i>Apply Theme
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Current Logo -->
            @if($company->logo)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Logo</h3>
                <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" 
                     class="w-32 h-32 object-cover rounded-lg mx-auto">
            </div>
            @endif

            <!-- Plan Information -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="orange-gradient px-6 py-4">
                    <h3 class="text-lg font-bold text-white">Subscription Plan</h3>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Current Plan:</span>
                        <span class="font-semibold {{ $company->isPaid() ? 'text-green-600' : 'text-orange-600' }}">
                            {{ ucfirst($company->plan) }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Max Users:</span>
                        <span class="font-semibold">
                            {{ $company->isPaid() ? 'Unlimited' : $company->max_users }}
                        </span>
                    </div>
                    
                    @if($company->plan === 'free')
                    <form action="{{ route('company.upgrade') }}" method="POST" class="mt-6">
                        @csrf
                        <button type="submit" class="w-full red-gradient text-white px-4 py-3 rounded-xl font-medium hover:shadow-lg transition-all">
                            <i class="fas fa-arrow-up mr-2"></i>Upgrade Plan
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Theme selection
    document.querySelectorAll('.theme-option').forEach(option => {
        option.addEventListener('click', function() {
            // Remove active class from all options
            document.querySelectorAll('.theme-option').forEach(opt => {
                opt.classList.remove('border-orange-500', 'bg-orange-50');
                opt.classList.add('border-gray-200');
            });
            
            // Add active class to selected option
            this.classList.remove('border-gray-200');
            this.classList.add('border-orange-500', 'bg-orange-50');
            
            // Update form values
            document.getElementById('primaryColor').value = this.dataset.primary;
            document.getElementById('secondaryColor').value = this.dataset.secondary;
            document.getElementById('themeName').value = this.dataset.name;
            
            updatePreview();
        });
    });

    // Color input changes
    document.getElementById('primaryColor').addEventListener('input', updatePreview);
    document.getElementById('secondaryColor').addEventListener('input', updatePreview);

    function updatePreview() {
        const primary = document.getElementById('primaryColor').value;
        const secondary = document.getElementById('secondaryColor').value;
        
        const header = document.getElementById('previewHeader');
        const message = document.getElementById('previewMessage1');
        
        header.style.background = `linear-gradient(135deg, ${primary}, ${secondary})`;
        message.style.background = `linear-gradient(135deg, ${primary}, ${secondary})`;
    }

    // Initialize preview
    updatePreview();

    // Set current theme as active
    const currentPrimary = '{{ $company->chat_primary_color }}';
    const currentSecondary = '{{ $company->chat_secondary_color }}';
    
    document.querySelectorAll('.theme-option').forEach(option => {
        if (option.dataset.primary === currentPrimary && option.dataset.secondary === currentSecondary) {
            option.classList.remove('border-gray-200');
            option.classList.add('border-orange-500', 'bg-orange-50');
        }
    });
</script>
@endsection