@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Designations</h3>
                    <a href="{{ route('admin.designations.create') }}" class="btn btn-primary">Add Designation</a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($designations as $designation)
                                <tr>
                                    <td>{{ $designation->id }}</td>
                                    <td>{{ $designation->name }}</td>
                                    <td>{{ $designation->department->name }}</td>
                                    <td>
                                        <span class="badge {{ $designation->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $designation->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $designation->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.designations.edit', $designation) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('admin.designations.destroy', $designation) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection