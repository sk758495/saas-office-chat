@extends('layouts.admin')

@section('title', 'Chat Monitor')
@section('page-title', 'Chat Monitor')
@section('page-subtitle', 'Monitor all conversations and group chats')

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-comment fa-2x text-primary mb-3"></i>
                <h5>Individual Chats</h5>
                <div class="h3 text-primary">{{ $allChats->where('type', 'individual')->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-users fa-2x text-success mb-3"></i>
                <h5>Group Chats</h5>
                <div class="h3 text-success">{{ $allChats->where('type', 'group')->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-envelope fa-2x text-info mb-3"></i>
                <h5>Total Messages</h5>
                <div class="h3 text-info">{{ $allChats->sum('message_count') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-comments me-2"></i>All Conversations</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th><i class="fas fa-tag me-1"></i>Type</th>
                        <th><i class="fas fa-users me-1"></i>Participants</th>
                        <th><i class="fas fa-comment-dots me-1"></i>Last Message</th>
                        <th><i class="fas fa-hashtag me-1"></i>Messages</th>
                        <th><i class="fas fa-clock me-1"></i>Last Activity</th>
                        <th><i class="fas fa-cog me-1"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allChats as $chat)
                    <tr>
                        <td>
                            <span class="badge bg-{{ $chat['type'] === 'individual' ? 'primary' : 'success' }} px-3 py-2">
                                <i class="fas fa-{{ $chat['type'] === 'individual' ? 'user' : 'users' }} me-1"></i>
                                {{ ucfirst($chat['type']) }}
                            </span>
                        </td>
                        <td class="fw-bold">{{ $chat['participants'] }}</td>
                        <td class="text-muted">{{ Str::limit($chat['last_message'], 40) }}</td>
                        <td>
                            <span class="badge bg-light text-dark">{{ $chat['message_count'] }}</span>
                        </td>
                        <td class="text-muted">{{ $chat['last_activity']->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('admin.chat-monitor.show', [$chat['type'], $chat['id']]) }}" 
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-eye me-1"></i>View Chat
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($allChats->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No conversations found</h5>
                <p class="text-muted">Users haven't started chatting yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection