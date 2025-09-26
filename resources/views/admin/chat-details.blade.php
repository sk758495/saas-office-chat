<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Admin Chat Monitor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .chat-container { height: 100vh; background: white; }
        .chat-header { background: #1976d2; color: white; padding: 15px 20px; }
        .messages-container { 
            height: calc(100vh - 80px); 
            overflow-y: auto; 
            padding: 20px;
            background: linear-gradient(to bottom, #e3f2fd 0%, #f8f9fa 100%);
        }
        .message { 
            margin-bottom: 15px;
            display: flex;
            align-items: flex-end;
        }
        .message-bubble { 
            max-width: 70%; 
            padding: 10px 15px;
            border-radius: 18px;
            word-wrap: break-word;
            position: relative;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .message.sent { justify-content: flex-end; }
        .message.sent .message-bubble { 
            background: #dcf8c6;
            color: #303030;
            border-bottom-right-radius: 4px;
        }
        .message.received { justify-content: flex-start; }
        .message.received .message-bubble { 
            background: white;
            color: #303030;
            border-bottom-left-radius: 4px;
        }
        .sender-name { 
            font-size: 12px;
            color: #667eea;
            font-weight: 600;
            margin-bottom: 3px;
        }
        .message-time { 
            font-size: 11px;
            color: #8696a0;
            margin-top: 5px;
        }
        .read-status {
            font-size: 12px;
            color: #4fc3f7;
            margin-left: 5px;
        }
        .file-message {
            padding: 8px 12px;
            background: rgba(0,0,0,0.05);
            border-radius: 8px;
            border: 1px solid rgba(0,0,0,0.1);
            margin-bottom: 5px;
        }
        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 12px;
            margin: 0 8px;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.chat-monitor') }}" class="btn btn-light btn-sm me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h5 class="mb-0">{{ $title }}</h5>
                    <small>{{ $messages->count() }} messages</small>
                </div>
            </div>
            <div class="text-end">
                <small>Admin Monitor</small>
            </div>
        </div>
        
        <div class="messages-container">
            @php $lastSender = null; @endphp
            @foreach($messages as $message)
                @php 
                    $showAvatar = $lastSender !== $message->sender_id;
                    $lastSender = $message->sender_id;
                @endphp
                
                <div class="message received">
                    @if($showAvatar)
                        <div class="avatar">
                            {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                        </div>
                    @else
                        <div style="width: 48px;"></div>
                    @endif
                    
                    <div class="message-bubble">
                        @if($showAvatar)
                            <div class="sender-name">{{ $message->sender->name }}</div>
                        @endif
                        
                        @if($message->file_path)
                            @if($message->type === 'image')
                                <img src="/storage/{{ $message->file_path }}" class="img-fluid" style="max-width: 200px; border-radius: 8px; margin-bottom: 5px;">
                            @else
                                <div class="file-message">
                                    <i class="fas fa-file me-2"></i>
                                    <a href="/storage/{{ $message->file_path }}" target="_blank" class="text-decoration-none">
                                        {{ $message->file_name ?? 'Download File' }}
                                    </a>
                                </div>
                            @endif
                        @endif
                        
                        @if($message->message)
                            <div>{{ $message->message }}</div>
                        @endif
                        
                        <div class="d-flex justify-content-between align-items-end mt-1">
                            <div class="message-time">{{ $message->created_at->format('H:i') }}</div>
                            <span class="read-status">
                                <i class="fas fa-check{{ $message->is_read ? '-double' : '' }}"></i>
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
            
            @if($messages->isEmpty())
                <div class="text-center text-muted mt-5">
                    <i class="fas fa-comments fa-4x mb-3" style="opacity: 0.3;"></i>
                    <h5>No messages yet</h5>
                    <p>This conversation hasn't started.</p>
                </div>
            @endif
        </div>
    </div>
    
    <script>
        // Auto scroll to bottom
        document.querySelector('.messages-container').scrollTop = document.querySelector('.messages-container').scrollHeight;
    </script>
</body>
</html>