@extends('layouts.admin')

@section('title', 'Departments')
@section('page-title', 'Departments')
@section('page-subtitle', 'Manage organization departments')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-building me-2"></i>All Departments</h5>
        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Department
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th><i class="fas fa-hashtag me-1"></i>ID</th>
                        <th><i class="fas fa-building me-1"></i>Department Name</th>
                        <th><i class="fas fa-toggle-on me-1"></i>Status</th>
                        <th><i class="fas fa-calendar me-1"></i>Created</th>
                        <th><i class="fas fa-cog me-1"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($departments as $department)
                    <tr>
                        <td class="fw-bold">#{{ $department->id }}</td>
                        <td>{{ $department->name }}</td>
                        <td>
                            <span class="badge {{ $department->status ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                                <i class="fas fa-{{ $department->status ? 'check' : 'times' }} me-1"></i>
                                {{ $department->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-muted">{{ $department->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                <form action="{{ route('admin.departments.destroy', $department) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this department?')">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($departments->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-building fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No departments found</h5>
                <p class="text-muted">Create your first department to get started.</p>
                <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add First Department
                </a>
            </div>
        @endif
    </div>
</div>
@endsection