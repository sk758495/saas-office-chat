@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Company Settings</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Company Information -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Company Information</h2>
                    
                    <form action="{{ route('company.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                                <input type="text" name="name" value="{{ old('name', $company->name) }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" name="email" value="{{ old('email', $company->email) }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Company Logo</label>
                                <input type="file" name="logo" accept="image/*" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                @error('logo')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea name="address" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('address', $company->address) }}</textarea>
                            @error('address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                Update Company Information
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Subscription & Plan Info -->
            <div class="space-y-6">
                <!-- Current Logo -->
                @if($company->logo)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Logo</h3>
                    <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" 
                         class="w-32 h-32 object-cover rounded-lg mx-auto">
                </div>
                @endif

                <!-- Plan Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Subscription Plan</h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Current Plan:</span>
                            <span class="font-semibold {{ $company->isPaid() ? 'text-green-600' : 'text-orange-600' }}">
                                {{ ucfirst($company->plan) }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-semibold {{ $company->is_active ? 'text-green-600' : 'text-red-600' }}">
                                {{ $company->is_active ? 'Active' : 'Suspended' }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Max Users:</span>
                            <span class="font-semibold">
                                {{ $company->isPaid() ? 'Unlimited' : $company->max_users }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Storage Limit:</span>
                            <span class="font-semibold">
                                {{ $company->isPaid() ? 'Unlimited' : $company->max_storage_mb . 'MB' }}
                            </span>
                        </div>
                        
                        @if($company->subscription_expires_at)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Expires:</span>
                            <span class="font-semibold">
                                {{ $company->subscription_expires_at->format('M d, Y') }}
                            </span>
                        </div>
                        @endif
                        
                        @if($company->subscription_amount)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Amount:</span>
                            <span class="font-semibold">
                                ${{ number_format($company->subscription_amount, 2) }}/year
                            </span>
                        </div>
                        @endif
                    </div>
                    
                    @if($company->plan === 'free')
                    <form action="{{ route('company.upgrade') }}" method="POST" class="mt-6">
                        @csrf
                        <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            Upgrade to Paid Plan
                        </button>
                    </form>
                    @endif
                </div>

                <!-- Usage Statistics -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Usage Statistics</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Users</span>
                                <span class="font-medium">{{ $company->users()->count() }} / {{ $company->isPaid() ? '∞' : $company->max_users }}</span>
                            </div>
                            @if(!$company->isPaid())
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ ($company->users()->count() / $company->max_users) * 100 }}%"></div>
                            </div>
                            @endif
                        </div>
                        
                        <div class="pt-2 border-t">
                            <p class="text-sm text-gray-600">
                                @if($company->isPaid())
                                    Enjoying unlimited access with paid plan
                                @else
                                    {{ $company->getRemainingUsers() }} users remaining
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection