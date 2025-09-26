<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Group Chats Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .group { margin-bottom: 25px; border: 1px solid #ddd; padding: 15px; }
        .group-header { background: #f0f8ff; padding: 10px; margin: -15px -15px 10px -15px; }
        .members { margin: 10px 0; font-size: 12px; color: #666; }
        .message { margin: 5px 0; padding: 8px; border-radius: 5px; background: #f9f9f9; }
        .message-info { font-size: 11px; color: #666; margin-top: 3px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Group Chats Report</h1>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>

    @foreach($groups as $group)
    <div class="group">
        <div class="group-header">
            <strong>{{ $group->name }}</strong>
            <span style="float: right;">Created: {{ $group->created_at->format('Y-m-d H:i') }}</span>
        </div>
        
        <div class="members">
            <strong>Members:</strong> 
            @foreach($group->members as $member)
                {{ $member->name }}@if(!$loop->last), @endif
            @endforeach
        </div>
        
        @foreach($group->messages->take(15) as $message)
        <div class="message">
            <div>{{ $message->message }}</div>
            <div class="message-info">
                {{ $message->sender->name }} - {{ $message->created_at->format('M d, H:i') }}
            </div>
        </div>
        @endforeach
        
        @if($group->messages->count() > 15)
        <p><em>... and {{ $group->messages->count() - 15 }} more messages</em></p>
        @endif
    </div>
    @endforeach
</body>
</html>