@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Add New Department</h1>
        <p class="text-gray-600 mt-2">Create a new department for your organization</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="orange-gradient px-8 py-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-building text-2xl text-white"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Department Information</h2>
                    <p class="text-orange-100">Fill in the details below</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="p-8">
            <form action="{{ route('admin.departments.store') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-building text-orange-500 mr-2"></i>Department Name
                        </label>
                        <input type="text" name="name" id="name" required
                               class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:outline-none transition-colors text-lg"
                               placeholder="e.g., Human Resources, Engineering, Marketing"
                               value="{{ old('name') }}">
                        @error('name')
                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-toggle-on text-orange-500 mr-2"></i>Status
                        </label>
                        <div class="flex items-center space-x-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="status" value="1" checked class="sr-only">
                                <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                                <span class="text-gray-700 font-medium">Active</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="status" value="0" class="sr-only">
                                <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-times text-white text-xs"></i>
                                </div>
                                <span class="text-gray-700 font-medium">Inactive</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between mt-10 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.departments.index') }}" class="flex items-center text-gray-600 hover:text-gray-800 font-medium transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Departments
                    </a>
                    <button type="submit" class="orange-gradient hover:shadow-lg text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i>Create Department
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Card -->
    <div class="mt-8 bg-gradient-to-r from-orange-50 to-red-50 border border-orange-200 rounded-xl p-6">
        <div class="flex items-start">
            <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                <i class="fas fa-info text-white"></i>
            </div>
            <div>
                <h3 class="text-orange-800 font-bold text-lg mb-2">Department Guidelines</h3>
                <ul class="text-orange-700 space-y-1 text-sm">
                    <li><i class="fas fa-check text-orange-500 mr-2"></i>Department names should be unique within your organization</li>
                    <li><i class="fas fa-check text-orange-500 mr-2"></i>You can add designations to departments after creation</li>
                    <li><i class="fas fa-check text-orange-500 mr-2"></i>Inactive departments won't appear in user registration</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    // Radio button styling
    document.querySelectorAll('input[name="status"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('input[name="status"]').forEach(r => {
                const div = r.nextElementSibling;
                if (r.checked) {
                    if (r.value === '1') {
                        div.className = 'w-6 h-6 bg-green-500 rounded-full flex items-center justify-center mr-3';
                        div.innerHTML = '<i class="fas fa-check text-white text-xs"></i>';
                    } else {
                        div.className = 'w-6 h-6 bg-red-500 rounded-full flex items-center justify-center mr-3';
                        div.innerHTML = '<i class="fas fa-times text-white text-xs"></i>';
                    }
                } else {
                    div.className = 'w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center mr-3';
                    div.innerHTML = '<i class="fas fa-times text-white text-xs"></i>';
                }
            });
        });
    });
</script>
@endsection