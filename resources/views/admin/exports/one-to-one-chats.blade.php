<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>One-to-One Chats Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .chat { margin-bottom: 20px; border: 1px solid #ddd; padding: 15px; }
        .chat-header { background: #f5f5f5; padding: 10px; margin: -15px -15px 10px -15px; }
        .message { margin: 5px 0; padding: 8px; border-radius: 5px; }
        .message.sent { background: #e3f2fd; text-align: right; }
        .message.received { background: #f5f5f5; }
        .message-info { font-size: 11px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>One-to-One Chats Report</h1>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>

    @foreach($chats as $chat)
    <div class="chat">
        <div class="chat-header">
            <strong>{{ $chat->user1->name }} ↔ {{ $chat->user2->name }}</strong>
            <span style="float: right;">Started: {{ $chat->created_at->format('Y-m-d H:i') }}</span>
        </div>
        
        @foreach($chat->messages->take(20) as $message)
        <div class="message {{ $message->sender_id == $chat->user1_id ? 'sent' : 'received' }}">
            <div>{{ $message->message }}</div>
            <div class="message-info">
                {{ $message->sender->name }} - {{ $message->created_at->format('M d, H:i') }}
            </div>
        </div>
        @endforeach
        
        @if($chat->messages->count() > 20)
        <p><em>... and {{ $chat->messages->count() - 20 }} more messages</em></p>
        @endif
    </div>
    @endforeach
</body>
</html>