@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Add New Designation</h1>
        <p class="text-gray-600 mt-2">Create a new job position for your company</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('admin.designations.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-briefcase mr-2"></i>Designation Name
                    </label>
                    <input type="text" name="name" id="name" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none transition-colors"
                           placeholder="e.g., Software Engineer, Manager, etc."
                           value="{{ old('name') }}">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-building mr-2"></i>Department
                    </label>
                    <select name="department_id" id="department_id" required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none transition-colors">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.designations.index') }}" class="text-gray-600 hover:text-gray-800 font-medium transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Designations
                </a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>Create Designation
                </button>
            </div>
        </form>
    </div>

    @if($departments->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mt-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3"></i>
                <div>
                    <h3 class="text-yellow-800 font-semibold">No Departments Available</h3>
                    <p class="text-yellow-700 mt-1">You need to create departments first before adding designations.</p>
                    <a href="{{ route('admin.departments.create') }}" class="text-yellow-800 hover:text-yellow-900 font-medium underline mt-2 inline-block">
                        Create Department First
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection