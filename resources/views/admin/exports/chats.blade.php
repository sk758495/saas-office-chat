<!DOCTYPE html>
<html>
<head>
    <title>Chats Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #333; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .section { margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Chats & Groups Report</h1>
        <p>Generated on {{ date('F d, Y') }}</p>
    </div>

    <div class="section">
        <h2>Individual Chats ({{ $chats->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Participants</th>
                    <th>Created</th>
                    <th>Last Activity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($chats as $chat)
                <tr>
                    <td>{{ $chat->id }}</td>
                    <td>{{ $chat->user1->name }} ↔ {{ $chat->user2->name }}</td>
                    <td>{{ $chat->created_at->format('M d, Y') }}</td>
                    <td>{{ $chat->last_message_at ? $chat->last_message_at->format('M d, Y H:i') : 'No messages' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Group Chats ({{ $groups->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Group Name</th>
                    <th>Members</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groups as $group)
                <tr>
                    <td>{{ $group->id }}</td>
                    <td>{{ $group->name }}</td>
                    <td>{{ $group->members->count() }}</td>
                    <td>{{ $group->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>