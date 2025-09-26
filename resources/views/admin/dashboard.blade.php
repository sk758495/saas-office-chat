@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of your office chat system')

@section('content')
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-building fa-2x mb-3"></i>
                <h5>Departments</h5>
                <div class="display-6">{{ \App\Models\Department::count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-user-tag fa-2x mb-3"></i>
                <h5>Designations</h5>
                <div class="display-6">{{ \App\Models\Designation::count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-users fa-2x mb-3"></i>
                <h5>Users</h5>
                <div class="display-6">{{ \App\Models\User::count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-envelope fa-2x mb-3"></i>
                <h5>Messages</h5>
                <div class="display-6">{{ \App\Models\Message::count() }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-comment me-2"></i>Individual Chats</h5>
            </div>
            <div class="card-body text-center">
                <div class="display-4 text-primary mb-3">{{ \App\Models\Chat::count() }}</div>
                <p class="text-muted">Active one-on-one conversations</p>
                <a href="{{ route('admin.chat-monitor') }}" class="btn btn-primary">
                    <i class="fas fa-eye me-2"></i>View Chats
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Group Chats</h5>
            </div>
            <div class="card-body text-center">
                <div class="display-4 text-success mb-3">{{ \App\Models\Group::count() }}</div>
                <p class="text-muted">Active group conversations</p>
                <a href="{{ route('admin.chat-monitor') }}" class="btn btn-success">
                    <i class="fas fa-eye me-2"></i>View Groups
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.departments.create') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus me-2"></i>Add Department
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.designations.create') }}" class="btn btn-outline-success w-100">
                            <i class="fas fa-plus me-2"></i>Add Designation
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.chat-monitor') }}" class="btn btn-outline-info w-100">
                            <i class="fas fa-comments me-2"></i>Monitor Chats
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="dropdown">
                            <button class="btn btn-outline-warning w-100 dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-download me-2"></i>Export Data
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.export', 'users') }}"><i class="fas fa-users me-2"></i>Users</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.export', 'one-to-one-chats') }}"><i class="fas fa-user-friends me-2"></i>One-to-One Chats</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.export', 'group-chats') }}"><i class="fas fa-users me-2"></i>Group Chats</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.export', 'departments') }}"><i class="fas fa-building me-2"></i>Departments</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
