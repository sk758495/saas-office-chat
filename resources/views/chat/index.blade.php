<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office Chat</title>
    <link rel="icon" type="image/png" href="/user/images/Office-Chat-fevicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/css/emoji-picker.css" rel="stylesheet">
    <link href="/css/video-call.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    <style>
        :root {
            --chat-primary: {{ $chatTheme['primary_color'] }};
            --chat-secondary: {{ $chatTheme['secondary_color'] }};
            --chat-gradient: linear-gradient(135deg, {{ $chatTheme['primary_color'] }}, {{ $chatTheme['secondary_color'] }});
        }
        body { background: #f0f2f5; }
        .chat-container { height: 100vh; background: white; }
        .user-list { 
            height: 100vh; 
            overflow-y: auto; 
            background: white;
            border-right: 1px solid #e4e6ea;
        }
        .chat-area { 
            height: 100vh; 
            display: flex; 
            flex-direction: column;
            background: #f0f2f5;
        }
        .messages-container { 
            flex: 1; 
            overflow-y: auto; 
            padding: 20px;
            background: linear-gradient(to bottom, #e3f2fd 0%, #f8f9fa 100%);
        }
        .message { 
            margin-bottom: 12px;
            display: flex;
            align-items: flex-end;
        }
        .message.sent { 
            justify-content: flex-end;
        }
        .message.received { 
            justify-content: flex-start;
        }
        .message-bubble { 
            max-width: 65%; 
            padding: 8px 12px;
            border-radius: 18px;
            word-wrap: break-word;
            position: relative;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .message.sent .message-bubble { 
            background: var(--chat-gradient);
            color: white;
            border-bottom-right-radius: 4px;
        }
        .message.received .message-bubble { 
            background: white;
            color: #303030;
            border-bottom-left-radius: 4px;
        }
        .user-item { 
            padding: 12px 16px;
            border-bottom: 1px solid #f2f3f5;
            cursor: pointer;
            transition: all 0.2s ease;
            background: white;
        }
        .user-item:hover { 
            background: #f5f6f7;
        }
        .user-item.active { 
            background: color-mix(in srgb, var(--chat-primary) 10%, white);
            border-left: 4px solid var(--chat-primary);
        }
        .user-avatar { 
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--chat-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .online-indicator { 
            width: 12px;
            height: 12px;
            background: #4caf50;
            border-radius: 50%;
            position: absolute;
            bottom: 0;
            right: 0;
            border: 2px solid white;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.1);
        }
        .message-input { 
            border-top: 1px solid #e4e6ea;
            padding: 16px 20px;
            background: #f8f9fa;
        }
        .message-input .input-group {
            background: white;
            border-radius: 25px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .message-input .form-control {
            border: none;
            background: transparent;
            padding: 12px 16px;
            border-radius: 0;
        }
        .message-input .form-control:focus {
            box-shadow: none;
            border: none;
        }
        .message-input .btn {
            border-radius: 0;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            margin: 0;
        }
        .message-input .btn:first-child {
            border-top-left-radius: 25px;
            border-bottom-left-radius: 25px;
        }
        .message-input .btn:last-child {
            border-top-right-radius: 25px;
            border-bottom-right-radius: 25px;
        }
        
        /* Emoji button styling */
        #emojiBtn {
            color: #666;
            transition: all 0.2s ease;
        }
        
        #emojiBtn:hover {
            color: var(--chat-primary);
            background: color-mix(in srgb, var(--chat-primary) 10%, white);
        }
        
        #emojiBtn.active {
            color: var(--chat-primary);
            background: color-mix(in srgb, var(--chat-primary) 20%, white);
        }
        
        /* Emoji rendering in messages */
        .message-bubble {
            font-family: 'Segoe UI Emoji', 'Apple Color Emoji', 'Noto Color Emoji', sans-serif;
            line-height: 1.4;
        }
        
        .message-bubble .emoji {
            font-size: 1.2em;
            vertical-align: middle;
        }
        
        /* Emoji-only messages */
        .message-bubble.emoji-only {
            background: transparent;
            box-shadow: none;
            padding: 4px 8px;
        }
        
        .message-bubble.emoji-only .emoji {
            font-size: 2.5em;
        }
        .empty-chat {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #8696a0;
        }
        .empty-chat i {
            opacity: 0.3;
            margin-bottom: 16px;
        }
        .message-time { 
            font-size: 10px;
            color: #8696a0;
            opacity: 0.7;
            flex-grow: 1;
        }
        .read-status {
            font-size: 12px;
            color: #8696a0;
            margin-left: 5px;
        }
        .read-status.read {
            color: var(--chat-primary);
        }
        .file-message {
            padding: 8px 12px;
            background: rgba(0,0,0,0.05);
            border-radius: 8px;
            border: 1px solid rgba(0,0,0,0.1);
            margin-bottom: 5px;
        }
        .file-message a {
            color: var(--chat-primary);
            font-weight: 500;
        }
        .file-message a:hover {
            color: var(--chat-secondary);
        }

        /* Responsive Design */
        /* Emoji picker mobile positioning */
        @media (max-width: 768px) {
            .emoji-picker {
                position: fixed;
                bottom: 60px;
                left: 10px;
                right: 10px;
                width: auto;
            }
        }
        
        @media (max-width: 768px) {
            .chat-container { height: 100vh; }
            .user-list { 
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                z-index: 1000;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .user-list.show {
                transform: translateX(0);
            }
            .chat-area {
                width: 100%;
                margin-left: 0;
            }
            .user-avatar {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
            .user-item {
                padding: 10px 12px;
            }
            .message-bubble {
                max-width: 80%;
                padding: 6px 10px;
                font-size: 14px;
            }
            .messages-container {
                padding: 15px;
            }
            .message-input {
                padding: 12px 15px;
            }
            .message-input .btn {
                width: 40px;
                height: 40px;
            }
            .message-input .form-control {
                padding: 10px 12px;
                font-size: 14px;
            }
            .empty-chat h5 {
                font-size: 18px;
            }
            .empty-chat i {
                font-size: 2.5rem;
            }
            #header-desktop{
                margin-top: 45px;
            }
            .header-desktop{
                margin-top: 45px;
            }
        }

        @media (max-width: 576px) {
            .user-avatar {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }
            .user-item {
                padding: 8px 10px;
            }
            .message-bubble {
                max-width: 85%;
                padding: 5px 8px;
                font-size: 13px;
            }
            .messages-container {
                padding: 10px;
            }
            .message-input {
                padding: 10px 12px;
            }
            .message-input .btn {
                width: 36px;
                height: 36px;
            }
            .message-input .form-control {
                padding: 8px 10px;
                font-size: 13px;
            }
            .online-indicator {
                width: 10px;
                height: 10px;
            }
            .message-time {
                font-size: 9px;
            }
            .read-status {
                font-size: 10px;
            }
        }

        /* Loader Styles */
        .btn-loading {
            position: relative;
            pointer-events: none;
        }
        .btn-loading .btn-text {
            opacity: 0;
        }
        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .chat-loading {
            opacity: 0.6;
        }
        .form-loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Privacy Feature Styles */
        .message-bubble.privacy-mode {
            filter: blur(4px);
            transition: filter 0.3s ease;
            cursor: pointer;
        }
        .message-bubble.privacy-mode:hover {
            filter: blur(0px);
        }
        
        /* Drag and Drop Styles */
        .drag-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(102, 126, 234, 0.1);
            border: 3px dashed var(--chat-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            backdrop-filter: blur(2px);
        }
        .drag-overlay-content {
            text-align: center;
            color: var(--chat-primary);
            font-size: 1.2rem;
            font-weight: 600;
        }
        .drag-overlay-content i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.8;
        }
        .messages-container.drag-over {
            background: rgba(102, 126, 234, 0.05);
        }
        .privacy-toggle {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            transition: all 0.3s ease;
        }
        .privacy-toggle:hover {
            background: rgba(255,255,255,0.3);
            color: white;
        }
        .privacy-toggle.active {
            background: #ff5722;
            border-color: #ff5722;
        }

        /* Mobile Navigation */
        .mobile-nav {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: var(--chat-gradient);
            color: white;
            padding: 10px 15px;
            z-index: 1001;
            align-items: center;
            justify-content: space-between;
        }

        @media (max-width: 768px) {
            .mobile-nav {
                display: flex;
            }
            .chat-area {
                padding-top: 50px;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Navigation -->
    <div class="mobile-nav">
        <button class="btn btn-light btn-sm" onclick="toggleUserList()">
            <i class="fas fa-bars"></i>
        </button>
        <span id="mobileTitle">Office Chat</span>
        <button class="btn btn-light btn-sm" onclick="showCreateGroupModal()">
            <i class="fas fa-users"></i>
        </button>
    </div>

    <div class="container-fluid chat-container">
        <div class="row h-100">
            <!-- Users List -->
            <div class="col-md-4 col-lg-3 p-0 user-list">
                <div class="p-3 text-white" style="background: var(--chat-gradient);">
                    <div class="d-flex justify-content-between align-items-center" id="header-desktop">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                @if(auth()->user()->profile_photo || auth()->user()->avatar)
                                    <img src="{{ auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : asset('storage/' . auth()->user()->avatar) }}" 
                                         class="rounded-circle" width="40" height="40" style="object-fit: cover; border: 2px solid rgba(255,255,255,0.3);">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px; background: rgba(255,255,255,0.2); border: 2px solid rgba(255,255,255,0.3); color: white; font-weight: 600;">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="d-flex align-items-center mb-1">
                                    @if(auth()->user()->company && auth()->user()->company->logo)
                                        <img src="{{ asset('storage/' . auth()->user()->company->logo) }}" 
                                             class="me-2" width="20" height="20" style="object-fit: cover; border-radius: 4px;">
                                    @endif
                                    <h5 class="mb-0">
                                        {{ auth()->user()->company ? auth()->user()->company->name : 'Office Chat' }}
                                    </h5>
                                </div>
                                <small>{{ auth()->user()->name }} • {{ auth()->user()->company && auth()->user()->company->isPaid() ? 'Premium' : 'Free Plan' }}</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-light btn-sm" onclick="showCreateGroupModal()">
                                <i class="fas fa-users me-1"></i>
                            </button>
                            <button class="btn privacy-toggle btn-sm" id="privacyToggle" onclick="togglePrivacyMode()" title="Toggle Privacy Mode">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                            <button class="btn btn-light btn-sm" id="soundToggle" onclick="toggleSound()" title="Disable Sounds">
                                <i class="fas fa-volume-up"></i>
                            </button>
                            <button class="btn btn-light btn-sm" onclick="testNotificationPermission()" title="Test Notifications">
                                <i class="fas fa-bell"></i>
                            </button>
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown">
                                    <i class="fas fa-user"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user me-2"></i>Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-3">
                    <input type="text" class="form-control" id="searchUsers" placeholder="Search users...">
                </div>
                
                <div id="usersList">
                    <!-- Groups Section -->
                    @if($groups->count() > 0)
                    <div class="px-3 py-2 bg-light border-bottom">
                        <small class="text-muted fw-bold">GROUPS</small>
                    </div>
                    @foreach($groups as $group)
                    <div class="user-item" data-group-id="{{ $group->id }}" onclick="openGroupChat({{ $group->id }})">
                        <div class="d-flex align-items-center">
                            <div class="position-relative me-3">
                                @if($group->profile_picture)
                                    <div class="user-avatar">
                                        <img src="/storage/{{ $group->profile_picture }}" class="w-100 h-100 rounded-circle" style="object-fit: cover;">
                                    </div>
                                @else
                                    <div class="user-avatar" style="background: linear-gradient(135deg, #4caf50 0%, #2e7d32 100%);">
                                        <i class="fas fa-users"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="fw-bold">{{ $group->name }}</div>
                                    @if($group->unread_count > 0)
                                        <span class="badge bg-danger rounded-pill">{{ $group->unread_count }}</span>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    {{ $group->members->count() }} members
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    <!-- Users Section -->
                    <div class="px-3 py-2 bg-light border-bottom">
                        <small class="text-muted fw-bold">USERS</small>
                    </div>
                    @endif
                    
                    @foreach($users as $user)
                    <div class="user-item" data-user-id="{{ $user->id }}" onclick="openChat({{ $user->id }})">
                        <div class="d-flex align-items-center">
                            <div class="position-relative me-3">
                                @if($user->profile_photo)
                                    <img src="/storage/{{ $user->profile_photo }}" class="user-avatar" style="object-fit: cover;">
                                @else
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                @if($user->is_online)
                                    <div class="online-indicator"></div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="fw-bold">{{ $user->name }}</div>
                                    @if($user->unread_count > 0)
                                        <span class="badge bg-danger rounded-pill">{{ $user->unread_count }}</span>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    {{ $user->department->name ?? 'No Department' }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Chat Area -->
            <div class="col-md-8 col-lg-9 p-0 chat-area">
                <div id="chatHeader" class="header-desktop p-3 bg-white border-bottom d-none">
                    <div class="d-flex align-items-center">
                        <div class="user-avatar me-3" onclick="showUserProfile()" style="cursor: pointer;">
                            <span id="chatUserInitial"></span>
                        </div>
                        <div class="flex-grow-1" onclick="showUserProfile()" style="cursor: pointer;">
                            <div class="fw-bold" id="chatUserName"></div>
                            <small class="text-muted" id="chatUserInfo"></small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <!-- Video Call Buttons -->
                            <div id="callButtons" class="d-flex gap-1" style="display: none;">
                                <button class="btn btn-success btn-sm" onclick="startCall('audio')" title="Audio Call">
                                    <i class="fas fa-phone"></i>
                                </button>
                                <button class="btn btn-primary btn-sm" onclick="startCall('video')" title="Video Call">
                                    <i class="fas fa-video"></i>
                                </button>
                            </div>
                            <button class="btn btn-outline-primary btn-sm" id="addMemberBtn" onclick="showAddMemberModal()" style="display: none;">
                                <i class="fas fa-user-plus"></i>
                            </button>
                            <i class="fas fa-info-circle text-muted" onclick="showUserProfile()" style="cursor: pointer;"></i>
                        </div>
                    </div>
                </div>
                
                <div id="messagesContainer" class="messages-container">
                    <div class="empty-chat">
                        <i class="fas fa-comments fa-4x"></i>
                        <h5>Office Chat</h5>
                        <p>Select a user to start chatting</p>
                    </div>
                </div>
                
                <div id="messageInput" class="message-input d-none">
                    <div id="filePreview" class="d-none mb-3">
                        <div class="position-relative d-inline-block">
                            <div id="imagePreview" class="d-none">
                                <img id="previewImg" class="img-fluid" style="max-width: 200px; border-radius: 8px;">
                            </div>
                            <div id="documentPreview" class="d-none file-message" style="min-width: 200px;">
                                <i id="fileIcon" class="fas fa-file me-2"></i>
                                <span id="fileName"></span>
                            </div>
                            <div id="folderPreview" class="d-none file-message" style="min-width: 200px;">
                                <i class="fas fa-folder me-2 text-warning"></i>
                                <span id="folderName"></span>
                                <small class="d-block text-muted" id="folderDetails"></small>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" onclick="removePreview()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <form id="messageForm" enctype="multipart/form-data">
                        <div class="input-group">
                            <input type="file" id="fileInput" class="d-none" accept="*/*">
                            <input type="file" id="folderInput" class="d-none" webkitdirectory multiple>
                            <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('fileInput').click()" title="Upload File">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('folderInput').click()" title="Upload Folder">
                                <i class="fas fa-folder"></i>
                            </button>
                            <input type="text" class="form-control" id="messageText" placeholder="Type a message... (Try :) or :heart: or Ctrl+; for emojis)" autocomplete="off">
                            <button type="button" class="btn btn-outline-secondary" id="emojiBtn" onclick="toggleEmojiPicker()" title="Add Emoji">
                                <i class="fas fa-smile"></i>
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Group Modal -->
    <div class="modal fade" id="createGroupModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-users me-2"></i>Create New Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createGroupForm">
                        <div class="mb-3">
                            <label class="form-label">Group Name</label>
                            <input type="text" class="form-control" id="groupName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description (Optional)</label>
                            <textarea class="form-control" id="groupDescription" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Add Members</label>
                            <div id="membersList" class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                @foreach($users as $user)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="{{ $user->id }}" id="member{{ $user->id }}">
                                    <label class="form-check-label" for="member{{ $user->id }}">
                                        {{ $user->name }} - {{ $user->department->name ?? 'No Dept' }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Create Group</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- User Profile Modal -->
    <div class="modal fade" id="userProfileModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user me-2"></i>User Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="user-avatar mx-auto mb-3" id="modalUserAvatar">
                        <span id="modalUserInitial"></span>
                    </div>
                    <h6 id="modalUserName"></h6>
                    <div class="text-start mt-3">
                        <div class="mb-2">
                            <small class="text-muted">Email:</small><br>
                            <span id="modalUserEmail"></span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Mobile:</small><br>
                            <span id="modalUserMobile"></span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Department:</small><br>
                            <span id="modalUserDepartment"></span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Designation:</small><br>
                            <span id="modalUserDesignation"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Group Members Modal -->
    <div class="modal fade" id="groupMembersModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-users me-2"></i><span id="groupModalTitle"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <div class="position-relative d-inline-block">
                            <img id="groupProfilePhoto" class="rounded-circle" width="100" height="100" style="object-fit: cover;">
                            <button class="btn btn-primary btn-sm position-absolute bottom-0 end-0 rounded-circle" 
                                    id="groupPhotoBtn" onclick="document.getElementById('groupPhotoInput').click()" style="display: none;">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                        <input type="file" id="groupPhotoInput" class="d-none" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Description:</small><br>
                        <span id="groupModalDescription"></span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted fw-bold">Members (<span id="memberCount"></span>):</small>
                        <div id="groupMembersList" class="mt-2"></div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-warning btn-sm" onclick="exitGroup()">
                            <i class="fas fa-sign-out-alt me-1"></i>Exit Group
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div class="modal fade" id="addMemberModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addMemberForm">
                        <div class="mb-3">
                            <label class="form-label">Select User to Add</label>
                            <select class="form-select" id="newMemberSelect" required>
                                <option value="">Choose a user...</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Add Member</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Viewer Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img id="modalImage" class="img-fluid" style="max-height: 80vh; max-width: 100%;">
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button class="btn btn-primary" id="downloadModalBtn">
                        <i class="fas fa-download me-1"></i>Download
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Call Permission Modal -->
    <div class="modal fade" id="callPermissionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-video me-2"></i>Start Call</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                        <h6 id="permissionTitle">Camera & Microphone Access Required</h6>
                        <p class="text-muted" id="permissionMessage">This will request access to your camera and microphone for video calling.</p>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Please allow permissions when prompted by your browser.</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="startCallBtn">
                        <i class="fas fa-phone me-1"></i>Start Call
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/notification-sound.js"></script>
    <script src="/js/notification-permission.js"></script>
    <script src="/js/emoji-simple.js"></script>
    <script src="/js/video-call.js"></script>
    <script>
        let currentChatUserId = null;
        let currentChat = null;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Notification sound setup - using base64 WAV for compatibility
        const notificationSound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUarm7blmGgU7k9n1unEiBC13yO/eizEIHWq+8+OWT');
        notificationSound.preload = 'auto';
        let soundEnabled = localStorage.getItem('soundEnabled') !== 'false';
        let privacyMode = localStorage.getItem('privacyMode') === 'true';
        let lastMessageCount = 0;
        let lastGroupMessageCounts = {};
        
        // Notification permission is now handled by NotificationPermissionManager
        // which will show reminders every minute if permission is not granted

        // Show browser notification - works everywhere
        function showBrowserNotification(message, senderName, chatId, isGroup = false) {
            if ('Notification' in window && Notification.permission === 'granted') {
                const notification = new Notification(senderName, {
                    body: message || 'New message',
                    icon: '/favicon.ico'
                });
                
                notification.onclick = function() {
                    window.focus();
                    if (isGroup) {
                        openGroupChat(chatId);
                    } else {
                        openChat(chatId);
                    }
                    notification.close();
                };
                
                setTimeout(() => notification.close(), 5000);
            }
        }

        // Play notification sound
        function playNotificationSound() {
            if (soundEnabled) {
                notificationSound.currentTime = 0;
                notificationSound.play().catch(e => {
                    console.log('Audio failed, using fallback beep:', e);
                    if (typeof fallbackBeep === 'function') {
                        fallbackBeep();
                    }
                });
            }
        }
        
        // Toggle sound function
        function toggleSound() {
            soundEnabled = !soundEnabled;
            localStorage.setItem('soundEnabled', soundEnabled);
            const soundBtn = document.getElementById('soundToggle');
            soundBtn.innerHTML = soundEnabled ? '<i class="fas fa-volume-up"></i>' : '<i class="fas fa-volume-mute"></i>';
            soundBtn.title = soundEnabled ? 'Disable Sounds' : 'Enable Sounds';
        }

        // Toggle privacy mode function
        function togglePrivacyMode() {
            privacyMode = !privacyMode;
            localStorage.setItem('privacyMode', privacyMode);
            const privacyBtn = document.getElementById('privacyToggle');
            privacyBtn.classList.toggle('active', privacyMode);
            privacyBtn.innerHTML = privacyMode ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            privacyBtn.title = privacyMode ? 'Disable Privacy Mode' : 'Enable Privacy Mode';
            
            // Apply privacy mode to all current messages
            const messageBubbles = document.querySelectorAll('.message-bubble');
            messageBubbles.forEach(bubble => {
                if (privacyMode) {
                    bubble.classList.add('privacy-mode');
                } else {
                    bubble.classList.remove('privacy-mode');
                }
            });
        }

        // Loader utility functions
        function showButtonLoader(button) {
            button.classList.add('btn-loading');
            button.disabled = true;
            if (!button.querySelector('.btn-text')) {
                button.innerHTML = `<span class="btn-text">${button.innerHTML}</span>`;
            }
        }

        function hideButtonLoader(button) {
            button.classList.remove('btn-loading');
            button.disabled = false;
        }

        function showChatLoader() {
            document.getElementById('messagesContainer').classList.add('chat-loading');
        }

        function hideChatLoader() {
            document.getElementById('messagesContainer').classList.remove('chat-loading');
        }

        function openChat(userId) {
            const activeUserItem = document.querySelector(`[data-user-id="${userId}"]`);
            
            // Toggle chat if same user clicked
            if (currentChatUserId === userId && activeUserItem.classList.contains('active')) {
                closeChat();
                return;
            }
            
            currentChatUserId = userId;
            
            // Update active user
            document.querySelectorAll('.user-item').forEach(item => {
                item.classList.remove('active');
            });
            if (activeUserItem) {
                activeUserItem.classList.add('active');
            }
            
            // Show chat interface
            document.getElementById('chatHeader').classList.remove('d-none');
            document.getElementById('messageInput').classList.remove('d-none');
            
            // Show loading
            showChatLoader();
            
            // Load chat
            fetch(`/chat/${userId}`)
                .then(response => response.json())
                .then(data => {
                    currentChat = data.chat;
                    displayMessages(data.messages);
                    updateChatHeader(data.otherUser);
                })
                .catch(error => {
                    console.error('Error:', error);
                })
                .finally(() => {
                    hideChatLoader();
                });
        }

        function closeChat() {
            currentChatUserId = null;
            currentGroupId = null;
            currentChatUser = null;
            currentGroup = null;
            
            document.querySelectorAll('.user-item').forEach(item => {
                item.classList.remove('active');
            });
            document.getElementById('chatHeader').classList.add('d-none');
            document.getElementById('messageInput').classList.add('d-none');
            document.getElementById('messagesContainer').innerHTML = `
                <div class="empty-chat">
                    <i class="fas fa-comments fa-4x"></i>
                    <h5>Office Chat</h5>
                    <p>Select a user or group to start chatting</p>
                </div>
            `;
        }

        let currentChatUser = null;

        function updateChatHeader(user) {
            currentChatUser = user;
            currentGroup = null;
            
            const chatUserAvatar = document.querySelector('#chatHeader .user-avatar');
            chatUserAvatar.style.background = 'var(--chat-gradient)';
            
            if (user.profile_photo) {
                chatUserAvatar.innerHTML = `<img src="/storage/${user.profile_photo}" class="w-100 h-100 rounded-circle" style="object-fit: cover;">`;
            } else {
                chatUserAvatar.innerHTML = `<span>${user.name.charAt(0).toUpperCase()}</span>`;
            }
            document.getElementById('chatUserName').textContent = user.name;
            document.getElementById('chatUserInfo').textContent = user.department?.name || 'No Department';
            
            // Show call buttons for individual chats
            document.getElementById('callButtons').style.display = 'flex';
            // Hide add member button for individual chats
            document.getElementById('addMemberBtn').style.display = 'none';
        }

        let currentGroup = null;

        function showUserProfile() {
            if (currentGroup) {
                showGroupMembers();
            } else if (currentChatUser) {
                showIndividualProfile();
            }
        }

        function showIndividualProfile() {
            // Update modal content
            const modalAvatar = document.getElementById('modalUserAvatar');
            if (currentChatUser.profile_photo) {
                modalAvatar.innerHTML = `<img src="/storage/${currentChatUser.profile_photo}" class="w-100 h-100 rounded-circle" style="object-fit: cover;">`;
            } else {
                modalAvatar.innerHTML = `<span id="modalUserInitial">${currentChatUser.name.charAt(0).toUpperCase()}</span>`;
            }
            
            document.getElementById('modalUserName').textContent = currentChatUser.name;
            document.getElementById('modalUserEmail').textContent = currentChatUser.email || 'Not provided';
            document.getElementById('modalUserMobile').textContent = currentChatUser.mobile || 'Not provided';
            document.getElementById('modalUserDepartment').textContent = currentChatUser.department?.name || 'No Department';
            document.getElementById('modalUserDesignation').textContent = currentChatUser.designation?.name || 'No Designation';
            
            new bootstrap.Modal(document.getElementById('userProfileModal')).show();
        }

        function showGroupMembers() {
            if (!currentGroup) return;
            
            document.getElementById('groupModalTitle').textContent = currentGroup.name;
            document.getElementById('groupModalDescription').textContent = currentGroup.description || 'No description';
            document.getElementById('memberCount').textContent = currentGroup.members.length;
            
            // Set group profile photo
            const groupPhoto = document.getElementById('groupProfilePhoto');
            if (currentGroup.profile_picture) {
                groupPhoto.src = `/storage/${currentGroup.profile_picture}`;
            } else {
                groupPhoto.src = 'https://via.placeholder.com/80/4caf50/ffffff?text=' + currentGroup.name.charAt(0).toUpperCase();
            }
            
            const membersList = document.getElementById('groupMembersList');
            membersList.innerHTML = '';
            
            // Check if current user is admin
            const currentUserMember = currentGroup.members.find(m => m.id == {{ auth()->id() }});
            const isCurrentUserAdmin = currentUserMember && currentUserMember.pivot && currentUserMember.pivot.is_admin;
            
            // Show photo upload button for admins
            document.getElementById('groupPhotoBtn').style.display = isCurrentUserAdmin ? 'block' : 'none';
            
            currentGroup.members.forEach(member => {
                const memberDiv = document.createElement('div');
                memberDiv.className = 'd-flex align-items-center mb-2 p-2 border rounded';
                
                const isAdmin = member.pivot && member.pivot.is_admin;
                const isCurrentUser = member.id == {{ auth()->id() }};
                const adminBadge = isAdmin ? '<span class="badge bg-primary ms-2">Admin</span>' : '';
                
                let actionButtons = '';
                if (isCurrentUserAdmin && !isCurrentUser) {
                    actionButtons = `
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                ${!isAdmin ? `<li><a class="dropdown-item" href="#" onclick="transferOwnership(${member.id})"><i class="fas fa-crown me-2"></i>Make Admin</a></li>` : ''}
                                <li><a class="dropdown-item text-danger" href="#" onclick="removeMember(${member.id})"><i class="fas fa-user-minus me-2"></i>Remove</a></li>
                            </ul>
                        </div>
                    `;
                }
                
                memberDiv.innerHTML = `
                    <div class="user-avatar me-3" style="width: 32px; height: 32px; font-size: 12px;">
                        ${member.profile_photo ? 
                            `<img src="/storage/${member.profile_photo}" class="w-100 h-100 rounded-circle" style="object-fit: cover;">` :
                            `<span>${member.name.charAt(0).toUpperCase()}</span>`
                        }
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold">${member.name}${adminBadge}</div>
                        <small class="text-muted">${member.department?.name || 'No Department'}</small>
                    </div>
                    ${actionButtons}
                `;
                
                membersList.appendChild(memberDiv);
            });
            
            new bootstrap.Modal(document.getElementById('groupMembersModal')).show();
        }

        function displayMessages(messages) {
            const container = document.getElementById('messagesContainer');
            container.innerHTML = '';
            
            messages.forEach(message => {
                const messageDiv = document.createElement('div');
                const isSent = message.sender_id == {{ auth()->id() }};
                messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
                
                const messageDate = new Date(message.created_at);
                const timeString = messageDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                const dateString = messageDate.toLocaleDateString();
                const isToday = messageDate.toDateString() === new Date().toDateString();
                const displayTime = isToday ? timeString : `${dateString} ${timeString}`;
                
                // Show sender info for group messages (received messages only)
                let senderInfo = '';
                if (currentGroupId && !isSent && message.sender) {
                    const senderAvatar = message.sender.profile_photo ? 
                        `<img src="/storage/${message.sender.profile_photo}" class="rounded-circle" style="width: 24px; height: 24px; object-fit: cover;">` :
                        `<div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; background: var(--chat-gradient); color: white; font-size: 10px; font-weight: 600;">${message.sender.name.charAt(0).toUpperCase()}</div>`;
                    
                    senderInfo = `<div class="d-flex align-items-center mb-2">
                        ${senderAvatar}
                        <span class="sender-name ms-2" style="font-size: 0.75rem; color: #667eea; font-weight: 600;">${message.sender.name}</span>
                    </div>`;
                }
                
                let content = '';
                if (message.file_path) {
                    const fileName = message.file_name || message.file_path.split('/').pop();
                    const isImage = /\.(jpg|jpeg|png|gif|webp)$/i.test(fileName);
                    const isZip = message.file_type === 'zip' || fileName.toLowerCase().endsWith('.zip');
                    
                    if (isImage && !isZip) {
                        content = `<img src="/storage/${message.file_path}" class="img-fluid" style="max-width: 200px; border-radius: 8px; margin-bottom: 5px;">`;
                    } else if (message.is_folder || isZip) {
                        const folderName = message.original_folder_name || fileName.replace('.zip', '');
                        const fileCount = message.folder_contents ? message.folder_contents.length : 'Multiple';
                        
                        content = `<div class="file-message">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-${message.is_folder ? 'folder' : 'file-archive'} me-2 text-warning"></i>
                                <div>
                                    <div class="fw-bold">${folderName}</div>
                                    <small class="text-muted">${message.is_folder ? 'Folder' : 'ZIP Archive'} • ${fileCount} files</small>
                                </div>
                            </div>
                            ${message.folder_contents ? `
                                <div class="folder-contents" style="max-height: 100px; overflow-y: auto; font-size: 11px; border-top: 1px solid #ddd; padding-top: 5px;">
                                    ${message.folder_contents.slice(0, 5).map(file => `
                                        <div class="d-flex justify-content-between">
                                            <span>${file.name}</span>
                                            <span class="text-muted">${file.size ? formatFileSize(file.size) : ''}</span>
                                        </div>
                                    `).join('')}
                                    ${message.folder_contents.length > 5 ? `<div class="text-muted">... and ${message.folder_contents.length - 5} more files</div>` : ''}
                                </div>
                            ` : ''}
                            <button class="btn btn-sm btn-outline-primary mt-2" onclick="downloadFile(${message.id})">
                                <i class="fas fa-download me-1"></i>Download
                            </button>
                        </div>`;
                    } else {
                        let fileIcon = 'fa-file';
                        if (fileName.toLowerCase().endsWith('.pdf')) fileIcon = 'fa-file-pdf text-danger';
                        else if (fileName.toLowerCase().match(/\.(doc|docx)$/)) fileIcon = 'fa-file-word text-primary';
                        else if (fileName.toLowerCase().match(/\.(xls|xlsx)$/)) fileIcon = 'fa-file-excel text-success';
                        
                        content = `<div class="file-message">
                            <i class="fas ${fileIcon} me-2"></i>
                            <a href="/storage/${message.file_path}" target="_blank" class="text-decoration-none">${fileName}</a>
                        </div>`;
                    }
                    if (message.message) {
                        content += `<div class="mt-2">${message.message}</div>`;
                    }
                } else {
                    content = message.message || 'Message';
                }
                
                // Read status for sent messages
                let readStatus = '';
                if (isSent) {
                    if (currentGroupId) {
                        // Group message read status
                        const allRead = message.all_read || false;
                        readStatus = `<span class="read-status ${allRead ? 'read' : ''}">
                            <i class="fas fa-check${allRead ? '-double' : ''}"></i>
                        </span>`;
                    } else {
                        // Individual message read status
                        readStatus = `<span class="read-status ${message.is_read ? 'read' : ''}">
                            <i class="fas fa-check${message.is_read ? '-double' : ''}"></i>
                        </span>`;
                    }
                }
                
                const privacyClass = privacyMode ? ' privacy-mode' : '';
                messageDiv.innerHTML = `
                    <div class="message-bubble${privacyClass}">
                        ${senderInfo}
                        ${content}
                        <div class="d-flex justify-content-between align-items-end mt-1">
                            <div class="message-time">${displayTime}</div>
                            ${readStatus}
                        </div>
                    </div>
                `;
                
                container.appendChild(messageDiv);
            });
            
            container.scrollTop = container.scrollHeight;
        }

        let currentGroupId = null;

        // Send message
        document.getElementById('messageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const messageText = document.getElementById('messageText').value;
            const fileInput = document.getElementById('fileInput');
            const folderInput = document.getElementById('folderInput');
            const sendBtn = document.querySelector('#messageForm .btn-primary');
            
            if (!messageText.trim() && !fileInput.files[0] && !folderInput.files.length) return;
            
            // Show loader
            const originalContent = sendBtn.innerHTML;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            sendBtn.disabled = true;
            
            const formData = new FormData();
            
            if (currentGroupId) {
                formData.append('group_id', currentGroupId);
                var sendUrl = '/groups/send-message';
            } else {
                formData.append('receiver_id', currentChatUserId);
                var sendUrl = '/chat/send';
            }
            
            if (messageText.trim()) formData.append('message', messageText.trim());
            
            // Handle folder upload
            if (folderInput.files.length > 0) {
                Array.from(folderInput.files).forEach((file, index) => {
                    formData.append(`folder_files[${index}]`, file);
                });
                formData.append('folder_name', folderInput.files[0].webkitRelativePath.split('/')[0]);
            }
            // Handle single file upload (including ZIP)
            if (fileInput.files[0]) {
                formData.append('file', fileInput.files[0]);
            }
            
            fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Server response:', text);
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    });
                }
                return response.text().then(text => {
                    console.log('Server response:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Invalid JSON response:', text);
                        throw new Error('Server returned invalid response');
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    document.getElementById('messageText').value = '';
                    fileInput.value = '';
                    folderInput.value = '';
                    removePreview();
                    
                    // Add new message to current chat without refreshing
                    if (currentChatUserId) {
                        addMessageToChat(data.message);
                        moveChatToTop(currentChatUserId, false);
                    } else if (currentGroupId) {
                        addMessageToChat(data.message);
                        moveChatToTop(currentGroupId, true);
                    }
                    
                    // Update unread counts for other users
                    updateUnreadCounts();
                    updateGroupUnreadCounts();
                } else {
                    console.error('Server error:', data);
                    alert('Error: ' + (data.error || data.message || 'Upload failed'));
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                alert('Upload failed: ' + error.message);
            })
            .finally(() => {
                // Reset button
                sendBtn.innerHTML = originalContent;
                sendBtn.disabled = false;
            });
        });

        function showCreateGroupModal() {
            new bootstrap.Modal(document.getElementById('createGroupModal')).show();
        }

        function openGroupChat(groupId) {
            const activeGroupItem = document.querySelector(`[data-group-id="${groupId}"]`);
            
            // Toggle group chat if same group clicked
            if (currentGroupId === groupId && activeGroupItem.classList.contains('active')) {
                closeChat();
                return;
            }
            
            currentChatUserId = null;
            currentGroupId = groupId;
            
            // Update active item
            document.querySelectorAll('.user-item').forEach(item => {
                item.classList.remove('active');
            });
            if (activeGroupItem) {
                activeGroupItem.classList.add('active');
                
                // Remove unread badge when opening group
                const badge = activeGroupItem.querySelector('.badge');
                if (badge) {
                    badge.remove();
                }
            }
            
            // Show chat interface
            document.getElementById('chatHeader').classList.remove('d-none');
            document.getElementById('messageInput').classList.remove('d-none');
            
            // Show loading
            showChatLoader();
            
            // Load group chat
            fetch(`/groups/${groupId}`)
                .then(response => response.json())
                .then(data => {
                    displayMessages(data.messages);
                    updateGroupChatHeader(data.group);
                })
                .catch(error => {
                    console.error('Error loading group:', error);
                })
                .finally(() => {
                    hideChatLoader();
                });
        }

        function updateGroupChatHeader(group) {
            currentGroup = group;
            currentChatUser = null;
            
            const chatUserAvatar = document.querySelector('#chatHeader .user-avatar');
            
            if (group.profile_picture) {
                chatUserAvatar.innerHTML = `<img src="/storage/${group.profile_picture}" class="w-100 h-100 rounded-circle" style="object-fit: cover;">`;
                chatUserAvatar.style.background = 'transparent';
            } else {
                chatUserAvatar.innerHTML = '<i class="fas fa-users"></i>';
                chatUserAvatar.style.background = 'var(--chat-gradient)';
            }
            
            document.getElementById('chatUserName').textContent = group.name;
            document.getElementById('chatUserInfo').textContent = `${group.members.length} members`;
            
            // Show call buttons for group chats
            document.getElementById('callButtons').style.display = 'flex';
            // Show add member button for groups
            document.getElementById('addMemberBtn').style.display = 'block';
        }

        // Update unread counts for groups
        function updateGroupUnreadCounts() {
            fetch('/api/group-unread-counts')
                .then(response => response.json())
                .then(data => {
                    // Remove all existing group badges
                    document.querySelectorAll('[data-group-id] .badge').forEach(badge => {
                        const groupItem = badge.closest('[data-group-id]');
                        if (!groupItem.classList.contains('active')) {
                            badge.remove();
                        }
                    });
                    
                    // Add new badges
                    Object.keys(data).forEach(groupId => {
                        const groupItem = document.querySelector(`[data-group-id="${groupId}"]`);
                        if (groupItem && !groupItem.classList.contains('active')) {
                            const nameDiv = groupItem.querySelector('.fw-bold');
                            const parentDiv = nameDiv.parentElement;
                            
                            if (!parentDiv.querySelector('.badge')) {
                                const badge = document.createElement('span');
                                badge.className = 'badge bg-danger rounded-pill';
                                badge.textContent = data[groupId];
                                parentDiv.appendChild(badge);
                            }
                        }
                    });
                })
                .catch(error => console.error('Group unread count error:', error));
        }

        // Auto-refresh messages every 3 seconds for new messages from others
        setInterval(() => {
            if (currentChatUserId) {
                fetch(`/chat/${currentChatUserId}`)
                    .then(response => response.json())
                    .then(data => {
                        const currentMessageCount = document.querySelectorAll('.message').length;
                        if (data.messages.length > currentMessageCount) {
                            // Check if new message is from other user (not sent by current user)
                            const newMessages = data.messages.slice(currentMessageCount);
                            const hasNewFromOthers = newMessages.some(msg => msg.sender_id != {{ auth()->id() }});
                            
                            if (hasNewFromOthers) {
                                playNotificationSound();
                                const lastMessage = newMessages[newMessages.length - 1];
                                showBrowserNotification(
                                    lastMessage.message || 'File attachment',
                                    data.otherUser.name,
                                    currentChatUserId
                                );
                            }
                            
                            currentChat = data.chat;
                            displayMessages(data.messages);
                            moveChatToTop(currentChatUserId, false);
                        }
                    })
                    .catch(error => console.error('Auto-refresh error:', error));
            } else if (currentGroupId) {
                fetch(`/groups/${currentGroupId}`)
                    .then(response => response.json())
                    .then(data => {
                        const currentMessageCount = document.querySelectorAll('.message').length;
                        if (data.messages.length > currentMessageCount) {
                            // Check if new message is from other user (not sent by current user)
                            const newMessages = data.messages.slice(currentMessageCount);
                            const hasNewFromOthers = newMessages.some(msg => msg.sender_id != {{ auth()->id() }});
                            
                            if (hasNewFromOthers) {
                                playNotificationSound();
                                const lastMessage = newMessages[newMessages.length - 1];
                                if (lastMessage.sender) {
                                    showBrowserNotification(
                                        lastMessage.message || 'File attachment',
                                        `${lastMessage.sender.name} (${data.group.name})`,
                                        currentGroupId,
                                        true
                                    );
                                }
                            }
                            
                            displayMessages(data.messages);
                            updateGroupChatHeader(data.group);
                            moveChatToTop(currentGroupId, true);
                        }
                    })
                    .catch(error => console.error('Auto-refresh error:', error));
            }
        }, 3000);

        // Update unread counts function
        function updateUnreadCounts() {
            fetch('/api/unread-counts')
                .then(response => response.json())
                .then(data => {
                    // Remove all existing badges first
                    document.querySelectorAll('[data-user-id] .badge').forEach(badge => {
                        const userItem = badge.closest('[data-user-id]');
                        if (!userItem.classList.contains('active')) {
                            badge.remove();
                        }
                    });
                    
                    // Add new badges
                    Object.keys(data).forEach(userId => {
                        const userItem = document.querySelector(`[data-user-id="${userId}"]`);
                        if (userItem && !userItem.classList.contains('active')) {
                            const nameDiv = userItem.querySelector('.fw-bold');
                            const parentDiv = nameDiv.parentElement;
                            
                            if (!parentDiv.querySelector('.badge')) {
                                const badge = document.createElement('span');
                                badge.className = 'badge bg-danger rounded-pill';
                                badge.textContent = data[userId];
                                parentDiv.appendChild(badge);
                            }
                        }
                    });
                })
                .catch(error => console.error('Unread count error:', error));
        }

        // Global notification checker - works everywhere
        setInterval(() => {
            // Check individual chats
            fetch('/api/unread-counts')
                .then(response => response.json())
                .then(data => {
                    const totalUnread = Object.values(data).reduce((sum, count) => sum + count, 0);
                    if (totalUnread > lastMessageCount) {
                        playNotificationSound();
                        
                        // Show notification for new messages
                        Object.keys(data).forEach(userId => {
                            if (data[userId] > 0 && userId != currentChatUserId) {
                                const userItem = document.querySelector(`[data-user-id="${userId}"]`);
                                if (userItem) {
                                    const userName = userItem.querySelector('.fw-bold').textContent;
                                    showBrowserNotification('New message', userName, userId);
                                }
                            }
                        });
                    }
                    lastMessageCount = totalUnread;
                    
                    // Update badges
                    document.querySelectorAll('[data-user-id] .badge').forEach(badge => {
                        const userItem = badge.closest('[data-user-id]');
                        if (!userItem.classList.contains('active')) {
                            badge.remove();
                        }
                    });
                    
                    Object.keys(data).forEach(userId => {
                        const userItem = document.querySelector(`[data-user-id="${userId}"]`);
                        if (userItem && !userItem.classList.contains('active')) {
                            const nameDiv = userItem.querySelector('.fw-bold');
                            const parentDiv = nameDiv.parentElement;
                            
                            if (!parentDiv.querySelector('.badge')) {
                                const badge = document.createElement('span');
                                badge.className = 'badge bg-danger rounded-pill';
                                badge.textContent = data[userId];
                                parentDiv.appendChild(badge);
                            }
                        }
                    });
                })
                .catch(error => console.error('Unread count error:', error));
            
            // Check group chats
            fetch('/api/group-unread-counts')
                .then(response => response.json())
                .then(data => {
                    const totalGroupUnread = Object.values(data).reduce((sum, count) => sum + count, 0);
                    const lastGroupTotal = Object.values(lastGroupMessageCounts).reduce((sum, count) => sum + count, 0);
                    
                    if (totalGroupUnread > lastGroupTotal) {
                        playNotificationSound();
                        
                        // Show notification for new group messages
                        Object.keys(data).forEach(groupId => {
                            if (data[groupId] > 0 && groupId != currentGroupId) {
                                const groupItem = document.querySelector(`[data-group-id="${groupId}"]`);
                                if (groupItem) {
                                    const groupName = groupItem.querySelector('.fw-bold').textContent;
                                    showBrowserNotification('New message', groupName, groupId, true);
                                }
                            }
                        });
                    }
                    lastGroupMessageCounts = data;
                    
                    // Update group badges
                    document.querySelectorAll('[data-group-id] .badge').forEach(badge => {
                        const groupItem = badge.closest('[data-group-id]');
                        if (!groupItem.classList.contains('active')) {
                            badge.remove();
                        }
                    });
                    
                    Object.keys(data).forEach(groupId => {
                        const groupItem = document.querySelector(`[data-group-id="${groupId}"]`);
                        if (groupItem && !groupItem.classList.contains('active')) {
                            const nameDiv = groupItem.querySelector('.fw-bold');
                            const parentDiv = nameDiv.parentElement;
                            
                            if (!parentDiv.querySelector('.badge')) {
                                const badge = document.createElement('span');
                                badge.className = 'badge bg-danger rounded-pill';
                                badge.textContent = data[groupId];
                                parentDiv.appendChild(badge);
                            }
                        }
                    });
                })
                .catch(error => console.error('Group unread count error:', error));
        }, 2000);

        // Create group form submission
        document.getElementById('createGroupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const groupName = document.getElementById('groupName').value;
            const groupDescription = document.getElementById('groupDescription').value;
            const selectedMembers = Array.from(document.querySelectorAll('#membersList input:checked')).map(cb => cb.value);
            const submitBtn = this.querySelector('button[type="submit"]');
            
            if (selectedMembers.length === 0) {
                alert('Please select at least one member');
                return;
            }
            
            showButtonLoader(submitBtn);
            this.classList.add('form-loading');
            
            fetch('/groups', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name: groupName,
                    description: groupDescription,
                    members: selectedMembers
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Group created successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('createGroupModal')).hide();
                    document.getElementById('createGroupForm').reset();
                    location.reload();
                } else {
                    alert(data.error || 'Error creating group');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Network error: ' + error.message);
            })
            .finally(() => {
                hideButtonLoader(submitBtn);
                this.classList.remove('form-loading');
            });
        });

        // File input change handler for preview
        document.getElementById('fileInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const isImage = file.type.startsWith('image/');
                const isZip = file.name.toLowerCase().endsWith('.zip');
                
                if (isImage) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('previewImg').src = e.target.result;
                        document.getElementById('imagePreview').classList.remove('d-none');
                        document.getElementById('documentPreview').classList.add('d-none');
                        document.getElementById('folderPreview').classList.add('d-none');
                        document.getElementById('filePreview').classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                } else {
                    // Show document preview
                    document.getElementById('fileName').textContent = file.name;
                    
                    // Set appropriate icon based on file type
                    const fileIcon = document.getElementById('fileIcon');
                    if (isZip) {
                        fileIcon.className = 'fas fa-file-archive me-2 text-warning';
                    } else if (file.type.includes('pdf')) {
                        fileIcon.className = 'fas fa-file-pdf me-2 text-danger';
                    } else if (file.type.includes('word') || file.name.endsWith('.doc') || file.name.endsWith('.docx')) {
                        fileIcon.className = 'fas fa-file-word me-2 text-primary';
                    } else {
                        fileIcon.className = 'fas fa-file me-2 text-secondary';
                    }
                    
                    document.getElementById('documentPreview').classList.remove('d-none');
                    document.getElementById('imagePreview').classList.add('d-none');
                    document.getElementById('folderPreview').classList.add('d-none');
                    document.getElementById('filePreview').classList.remove('d-none');
                }
            }
        });

        // Folder input change handler
        document.getElementById('folderInput').addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            if (files.length > 0) {
                const folderName = files[0].webkitRelativePath.split('/')[0];
                const totalSize = files.reduce((sum, file) => sum + file.size, 0);
                const sizeText = formatFileSize(totalSize);
                
                document.getElementById('folderName').textContent = folderName;
                document.getElementById('folderDetails').textContent = `${files.length} files • ${sizeText}`;
                
                document.getElementById('folderPreview').classList.remove('d-none');
                document.getElementById('imagePreview').classList.add('d-none');
                document.getElementById('documentPreview').classList.add('d-none');
                document.getElementById('filePreview').classList.remove('d-none');
            }
        });

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Add single message to chat without full refresh
        function addMessageToChat(message) {
            const container = document.getElementById('messagesContainer');
            const messageDiv = document.createElement('div');
            const isSent = message.sender_id == {{ auth()->id() }};
            messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
            
            const messageDate = new Date(message.created_at);
            const timeString = messageDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            const dateString = messageDate.toLocaleDateString();
            const isToday = messageDate.toDateString() === new Date().toDateString();
            const displayTime = isToday ? timeString : `${dateString} ${timeString}`;
            
            // Show sender info for group messages (received messages only)
            let senderInfo = '';
            if (currentGroupId && !isSent && message.sender) {
                const senderAvatar = message.sender.profile_photo ? 
                    `<img src="/storage/${message.sender.profile_photo}" class="rounded-circle" style="width: 24px; height: 24px; object-fit: cover;">` :
                    `<div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; background: var(--chat-gradient); color: white; font-size: 10px; font-weight: 600;">${message.sender.name.charAt(0).toUpperCase()}</div>`;
                
                senderInfo = `<div class="d-flex align-items-center mb-2">
                    ${senderAvatar}
                    <span class="sender-name ms-2" style="font-size: 0.75rem; color: var(--chat-primary); font-weight: 600;">${message.sender.name}</span>
                </div>`;
            }
            
            let content = '';
            if (message.file_path) {
                const fileName = message.file_name || message.file_path.split('/').pop();
                const isImage = /\.(jpg|jpeg|png|gif|webp)$/i.test(fileName);
                const isZip = message.file_type === 'zip' || fileName.toLowerCase().endsWith('.zip');
                
                if (isImage && !isZip) {
                    content = `
                        <div class="image-message-container position-relative">
                            <img src="/storage/${message.file_path}" class="img-fluid image-preview" 
                                 style="max-width: 200px; border-radius: 8px; margin-bottom: 5px; cursor: pointer;" 
                                 onclick="viewImage(${message.id}, '/storage/${message.file_path}')">
                            <div class="image-actions mt-1">
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="viewImage(${message.id}, '/storage/${message.file_path}')">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="btn btn-sm btn-outline-success" onclick="downloadFile(${message.id})">
                                    <i class="fas fa-download"></i> Download
                                </button>
                            </div>
                        </div>
                    `;
                } else if (message.is_folder || isZip) {
                    const folderName = message.original_folder_name || fileName.replace('.zip', '');
                    const fileCount = message.folder_contents ? message.folder_contents.length : 'Multiple';
                    
                    content = `<div class="file-message">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-${message.is_folder ? 'folder' : 'file-archive'} me-2 text-warning"></i>
                            <div>
                                <div class="fw-bold">${folderName}</div>
                                <small class="text-muted">${message.is_folder ? 'Folder' : 'ZIP Archive'} • ${fileCount} files</small>
                            </div>
                        </div>
                        <a href="/storage/${message.file_path}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-download me-1"></i>Download
                        </a>
                    </div>`;
                } else {
                    let fileIcon = 'fa-file';
                    if (fileName.toLowerCase().endsWith('.pdf')) fileIcon = 'fa-file-pdf text-danger';
                    else if (fileName.toLowerCase().match(/\.(doc|docx)$/)) fileIcon = 'fa-file-word text-primary';
                    else if (fileName.toLowerCase().match(/\.(xls|xlsx)$/)) fileIcon = 'fa-file-excel text-success';
                    
                    content = `<div class="file-message">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="fas ${fileIcon} me-2"></i>
                                <span class="text-decoration-none">${fileName}</span>
                            </div>
                            <button class="btn btn-sm btn-outline-success" onclick="downloadFile(${message.id})">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>`;
                }
                if (message.message) {
                    content += `<div class="mt-2">${message.message}</div>`;
                }
            } else {
                content = message.message || 'Message';
            }
            
            const privacyClass = privacyMode ? ' privacy-mode' : '';
            messageDiv.innerHTML = `
                <div class="message-bubble${privacyClass}">
                    ${senderInfo}
                    ${content}
                    <div class="d-flex justify-content-between align-items-end mt-1">
                        <div class="message-time">${displayTime}</div>
                        ${isSent ? '<span class="read-status"><i class="fas fa-check"></i></span>' : ''}
                    </div>
                </div>
            `;
            
            container.appendChild(messageDiv);
            container.scrollTop = container.scrollHeight;
        }

        // Move chat to top of list (WhatsApp style)
        function moveChatToTop(userId, isGroup = false) {
            const selector = isGroup ? `[data-group-id="${userId}"]` : `[data-user-id="${userId}"]`;
            const chatItem = document.querySelector(selector);
            
            if (chatItem) {
                const usersList = document.getElementById('usersList');
                const usersSection = usersList.querySelector('.bg-light');
                
                if (isGroup) {
                    // Move to top of groups section
                    const groupsSection = usersList.querySelector('.bg-light');
                    if (groupsSection && groupsSection.nextElementSibling) {
                        groupsSection.parentNode.insertBefore(chatItem, groupsSection.nextElementSibling);
                    }
                } else {
                    // Move to top of users section or after groups
                    if (usersSection) {
                        const usersHeader = Array.from(usersList.querySelectorAll('.bg-light')).find(el => 
                            el.textContent.includes('USERS'));
                        if (usersHeader && usersHeader.nextElementSibling) {
                            usersHeader.parentNode.insertBefore(chatItem, usersHeader.nextElementSibling);
                        }
                    } else {
                        usersList.insertBefore(chatItem, usersList.firstChild);
                    }
                }
            }
        }

        // Remove preview function
        function removePreview() {
            document.getElementById('filePreview').classList.add('d-none');
            document.getElementById('imagePreview').classList.add('d-none');
            document.getElementById('documentPreview').classList.add('d-none');
            document.getElementById('folderPreview').classList.add('d-none');
            document.getElementById('fileInput').value = '';
            document.getElementById('folderInput').value = '';
            window.droppedFiles = null;
        }

        // Mobile responsive functions
        function toggleUserList() {
            const userList = document.querySelector('.user-list');
            userList.classList.toggle('show');
        }

        // Close user list when chat is opened on mobile
        function openChatMobile(userId) {
            openChat(userId);
            if (window.innerWidth <= 768) {
                document.querySelector('.user-list').classList.remove('show');
                const userName = document.querySelector(`[data-user-id="${userId}"] .fw-bold`).textContent;
                document.getElementById('mobileTitle').textContent = userName;
            }
        }

        // Update openChat function for mobile
        const originalOpenChat = openChat;
        openChat = function(userId) {
            originalOpenChat(userId);
            if (window.innerWidth <= 768) {
                document.querySelector('.user-list').classList.remove('show');
                const userName = document.querySelector(`[data-user-id="${userId}"] .fw-bold`).textContent;
                document.getElementById('mobileTitle').textContent = userName;
            }
        };

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                document.querySelector('.user-list').classList.remove('show');
                document.getElementById('mobileTitle').textContent = 'Office Chat';
            }
        });
        
        // Test notification permission function
        function testNotificationPermission() {
            console.log('Test notification button clicked');
            if (window.notificationManager) {
                window.notificationManager.showReminderModal();
            } else {
                console.log('Notification manager not found, requesting permission directly');
                if ('Notification' in window) {
                    Notification.requestPermission().then(permission => {
                        console.log('Permission result:', permission);
                        if (permission === 'granted') {
                            new Notification('Test', { body: 'Notifications are working!' });
                        }
                    });
                }
            }
        }
        


        // Drag and Drop functionality
        let dragCounter = 0;
        
        function initializeDragAndDrop() {
            const messagesContainer = document.getElementById('messagesContainer');
            const chatArea = document.querySelector('.chat-area');
            
            // Prevent default drag behaviors
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                messagesContainer.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            // Highlight drop area when item is dragged over it
            ['dragenter', 'dragover'].forEach(eventName => {
                messagesContainer.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                messagesContainer.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight(e) {
                if (!currentChatUserId && !currentGroupId) return;
                
                dragCounter++;
                messagesContainer.classList.add('drag-over');
                
                if (!document.querySelector('.drag-overlay')) {
                    const overlay = document.createElement('div');
                    overlay.className = 'drag-overlay';
                    overlay.innerHTML = `
                        <div class="drag-overlay-content">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <div>Drop files here to send</div>
                            <small>Images, documents, and folders supported</small>
                        </div>
                    `;
                    chatArea.style.position = 'relative';
                    chatArea.appendChild(overlay);
                }
            }
            
            function unhighlight(e) {
                if (!currentChatUserId && !currentGroupId) return;
                
                dragCounter--;
                if (dragCounter === 0) {
                    messagesContainer.classList.remove('drag-over');
                    const overlay = document.querySelector('.drag-overlay');
                    if (overlay) {
                        overlay.remove();
                    }
                }
            }
            
            // Handle dropped files
            messagesContainer.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                if (!currentChatUserId && !currentGroupId) return;
                
                dragCounter = 0;
                messagesContainer.classList.remove('drag-over');
                const overlay = document.querySelector('.drag-overlay');
                if (overlay) overlay.remove();
                
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files.length > 0) {
                    handleDroppedFiles(files);
                }
            }
        }
        
        function handleDroppedFiles(files) {
            const fileArray = Array.from(files);
            
            if (fileArray.length === 1) {
                // Single file - use existing file input
                const file = fileArray[0];
                const fileInput = document.getElementById('fileInput');
                
                // Create a new FileList-like object
                const dt = new DataTransfer();
                dt.items.add(file);
                fileInput.files = dt.files;
                
                // Trigger change event to show preview
                fileInput.dispatchEvent(new Event('change', { bubbles: true }));
            } else {
                // Multiple files - treat as folder
                handleMultipleFiles(fileArray);
            }
        }
        
        function handleMultipleFiles(files) {
            // Show folder preview for multiple files
            const totalSize = files.reduce((sum, file) => sum + file.size, 0);
            const sizeText = formatFileSize(totalSize);
            
            document.getElementById('folderName').textContent = `Dropped Files (${files.length})`;
            document.getElementById('folderDetails').textContent = `${files.length} files • ${sizeText}`;
            
            document.getElementById('folderPreview').classList.remove('d-none');
            document.getElementById('imagePreview').classList.add('d-none');
            document.getElementById('documentPreview').classList.add('d-none');
            document.getElementById('filePreview').classList.remove('d-none');
            
            // Store files for sending
            window.droppedFiles = files;
        }
        
        // Update message form submission to handle dropped files
        const originalFormSubmit = document.getElementById('messageForm').onsubmit;
        document.getElementById('messageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const messageText = document.getElementById('messageText').value;
            const fileInput = document.getElementById('fileInput');
            const folderInput = document.getElementById('folderInput');
            const sendBtn = document.querySelector('#messageForm .btn-primary');
            
            if (!messageText.trim() && !fileInput.files[0] && !folderInput.files.length && !window.droppedFiles) return;
            
            // Show loader
            const originalContent = sendBtn.innerHTML;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            sendBtn.disabled = true;
            
            const formData = new FormData();
            
            if (currentGroupId) {
                formData.append('group_id', currentGroupId);
                var sendUrl = '/groups/send-message';
            } else {
                formData.append('receiver_id', currentChatUserId);
                var sendUrl = '/chat/send';
            }
            
            if (messageText.trim()) formData.append('message', messageText.trim());
            
            // Handle dropped files
            if (window.droppedFiles && window.droppedFiles.length > 0) {
                window.droppedFiles.forEach((file, index) => {
                    formData.append(`folder_files[${index}]`, file);
                });
                formData.append('folder_name', `Dropped Files (${window.droppedFiles.length})`);
            }
            // Handle folder upload
            else if (folderInput.files.length > 0) {
                Array.from(folderInput.files).forEach((file, index) => {
                    formData.append(`folder_files[${index}]`, file);
                });
                formData.append('folder_name', folderInput.files[0].webkitRelativePath.split('/')[0]);
            }
            // Handle single file upload
            else if (fileInput.files[0]) {
                formData.append('file', fileInput.files[0]);
            }
            
            fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Server response:', text);
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    });
                }
                return response.text().then(text => {
                    console.log('Server response:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Invalid JSON response:', text);
                        throw new Error('Server returned invalid response');
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    document.getElementById('messageText').value = '';
                    fileInput.value = '';
                    folderInput.value = '';
                    window.droppedFiles = null;
                    removePreview();
                    
                    // Add new message to current chat without refreshing
                    if (currentChatUserId) {
                        addMessageToChat(data.message);
                        moveChatToTop(currentChatUserId, false);
                    } else if (currentGroupId) {
                        addMessageToChat(data.message);
                        moveChatToTop(currentGroupId, true);
                    }
                    
                    // Update unread counts for other users
                    updateUnreadCounts();
                    updateGroupUnreadCounts();
                } else {
                    console.error('Server error:', data);
                    alert('Error: ' + (data.error || data.message || 'Upload failed'));
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                alert('Upload failed: ' + error.message);
            })
            .finally(() => {
                // Reset button
                sendBtn.innerHTML = originalContent;
                sendBtn.disabled = false;
            });
        });

        // Initialize button states
        document.addEventListener('DOMContentLoaded', function() {
            const soundBtn = document.getElementById('soundToggle');
            if (soundBtn) {
                soundBtn.innerHTML = soundEnabled ? '<i class="fas fa-volume-up"></i>' : '<i class="fas fa-volume-mute"></i>';
                soundBtn.title = soundEnabled ? 'Disable Sounds' : 'Enable Sounds';
            }
            
            const privacyBtn = document.getElementById('privacyToggle');
            if (privacyBtn) {
                privacyBtn.classList.toggle('active', privacyMode);
                privacyBtn.innerHTML = privacyMode ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
                privacyBtn.title = privacyMode ? 'Disable Privacy Mode' : 'Enable Privacy Mode';
            }
            
            // Initialize drag and drop
            initializeDragAndDrop();
        });

        // Group photo upload
        document.getElementById('groupPhotoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && currentGroup) {
                const photoImg = document.getElementById('groupProfilePhoto');
                
                photoImg.style.opacity = '0.5';
                photoImg.style.filter = 'blur(2px)';
                
                const formData = new FormData();
                formData.append('profile_picture', file);
                
                fetch(`/groups/${currentGroup.id}/photo`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        photoImg.src = data.photo_url;
                        currentGroup.profile_picture = data.photo_url.replace('/storage/', '');
                        updateGroupChatHeader(currentGroup);
                        
                        const groupItem = document.querySelector(`[data-group-id="${currentGroup.id}"] .user-avatar`);
                        if (groupItem) {
                            groupItem.innerHTML = `<img src="${data.photo_url}" class="w-100 h-100 rounded-circle" style="object-fit: cover;">`;
                        }
                    } else {
                        alert('Failed to upload photo');
                    }
                })
                .finally(() => {
                    photoImg.style.opacity = '1';
                    photoImg.style.filter = 'none';
                });
            }
        });

        // Exit group function
        function exitGroup() {
            if (!currentGroup || !confirm('Are you sure you want to exit this group? You will no longer receive messages from this group.')) return;
            
            fetch(`/groups/${currentGroup.id}/exit`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('You have left the group');
                    bootstrap.Modal.getInstance(document.getElementById('groupMembersModal')).hide();
                    closeChat();
                    location.reload();
                } else {
                    alert(data.error || 'Failed to exit group');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }



        // Show add member modal
        function showAddMemberModal() {
            if (!currentGroup) return;
            
            // Populate available users (exclude current members)
            const select = document.getElementById('newMemberSelect');
            select.innerHTML = '<option value="">Choose a user...</option>';
            
            fetch('/api/users')
                .then(response => response.json())
                .then(users => {
                    const currentMemberIds = currentGroup.members.map(m => m.id);
                    users.forEach(user => {
                        if (!currentMemberIds.includes(user.id)) {
                            const option = document.createElement('option');
                            option.value = user.id;
                            option.textContent = `${user.name} - ${user.department?.name || 'No Dept'}`;
                            select.appendChild(option);
                        }
                    });
                })
                .catch(error => console.error('Error loading users:', error));
            
            new bootstrap.Modal(document.getElementById('addMemberModal')).show();
        }

        // Add member form submission
        document.getElementById('addMemberForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const userId = document.getElementById('newMemberSelect').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            
            if (!userId || !currentGroup) return;
            
            showButtonLoader(submitBtn);
            
            fetch(`/groups/${currentGroup.id}/add-member`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ user_id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Member added successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('addMemberModal')).hide();
                    // Refresh group data
                    openGroupChat(currentGroup.id);
                } else {
                    alert(data.error || 'Failed to add member');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            })
            .finally(() => {
                hideButtonLoader(submitBtn);
            });
        });

        // Remove member function (admin only)
        function removeMember(userId) {
            if (!currentGroup || !confirm('Are you sure you want to remove this member?')) return;
            
            fetch(`/groups/${currentGroup.id}/remove-member`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ user_id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Member removed successfully!');
                    // Refresh group data
                    openGroupChat(currentGroup.id);
                    // Close and reopen members modal to refresh
                    bootstrap.Modal.getInstance(document.getElementById('groupMembersModal')).hide();
                } else {
                    alert(data.error || 'Failed to remove member');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        // Transfer ownership function (admin only)
        function transferOwnership(userId) {
            if (!currentGroup || !confirm('Are you sure you want to make this user an admin?')) return;
            
            fetch(`/groups/${currentGroup.id}/make-admin`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ user_id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User is now an admin!');
                    // Refresh group data
                    openGroupChat(currentGroup.id);
                    // Close and reopen members modal to refresh
                    bootstrap.Modal.getInstance(document.getElementById('groupMembersModal')).hide();
                } else {
                    alert(data.error || 'Failed to make admin');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        // Image viewer functions
        let currentImageMessageId = null;
        
        function viewImage(messageId, imageSrc) {
            currentImageMessageId = messageId;
            // Use the view file route for better image loading
            document.getElementById('modalImage').src = `/files/view/${messageId}`;
            new bootstrap.Modal(document.getElementById('imageModal')).show();
        }
        
        // Download file function
        function downloadFile(messageId) {
            const downloadUrl = `/files/download/${messageId}`;
            
            // Create a temporary link and click it to trigger download
            const a = document.createElement('a');
            a.href = downloadUrl;
            a.style.display = 'none';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
        
        // Download from modal
        document.getElementById('downloadModalBtn').addEventListener('click', function() {
            if (currentImageMessageId) {
                downloadFile(currentImageMessageId);
            }
        });
        
        // Video Call Functions
        let videoCallManager = null;
        
        // Initialize video call manager
        function initializeVideoCallManager() {
            if (typeof VideoCallManager !== 'undefined') {
                videoCallManager = new VideoCallManager();
                window.videoCallManager = videoCallManager;
            } else {
                console.warn('VideoCallManager not loaded');
            }
        }
        
        // Global variable to store call type
        let pendingCallType = null;
        
        // Start call function
        async function startCall(callType) {
            console.log('Starting call:', { callType, currentChatUserId, currentGroupId, currentChat });
            
            if (!window.videoCallManager) {
                console.error('Video call manager not available');
                alert('Video calling is not available. Please refresh the page.');
                return;
            }
            
            try {
                if (currentGroupId) {
                    console.log('Starting group call for group:', currentGroupId);
                    await window.videoCallManager.initiateCall('group', currentGroupId, callType);
                } else if (currentChatUserId && currentChat) {
                    console.log('Starting one-to-one call for chat:', currentChat.id);
                    await window.videoCallManager.initiateCall('one_to_one', currentChat.id, callType);
                } else {
                    throw new Error('Please select a user or group to call.');
                }
            } catch (error) {
                console.error('Failed to start call:', error);
                alert(error.message || 'Failed to start call. Please check your permissions and try again.');
            }
        }
        

        
        // Initialize video call manager when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Check if HTTPS is required
            if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                console.warn('Video calling requires HTTPS in production. Some features may not work.');
            }
            
            // Delay initialization to ensure all scripts are loaded
            setTimeout(initializeVideoCallManager, 1000);
        });
    </script>
</body>
</html>