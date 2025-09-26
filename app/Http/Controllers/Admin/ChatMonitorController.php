<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Group;
use App\Models\Message;
use App\Models\User;

class ChatMonitorController extends Controller
{
    public function index()
    {
        $companyId = auth('admin')->user()->company_id;
        
        $individualChats = Chat::with(['user1', 'user2', 'messages' => function($q) {
            $q->latest()->limit(1);
        }])->whereHas('user1', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->get()->map(function($chat) {
            return [
                'type' => 'individual',
                'id' => $chat->id,
                'participants' => $chat->user1->name . ' ↔ ' . $chat->user2->name,
                'last_message' => $chat->messages->first()?->message ?? 'No messages',
                'last_activity' => $chat->last_message_at ?? $chat->created_at,
                'message_count' => $chat->messages()->count()
            ];
        });

        $groupChats = Group::where('company_id', $companyId)->with(['members', 'messages' => function($q) {
            $q->latest()->limit(1);
        }])->get()->map(function($group) {
            return [
                'type' => 'group',
                'id' => $group->id,
                'participants' => $group->name . ' (' . $group->members->count() . ' members)',
                'last_message' => $group->messages->first()?->message ?? 'No messages',
                'last_activity' => $group->messages->first()?->created_at ?? $group->created_at,
                'message_count' => $group->messages()->count()
            ];
        });

        $allChats = $individualChats->concat($groupChats)->sortByDesc('last_activity');

        return view('admin.chat-monitor', compact('allChats'));
    }

    public function show($type, $id)
    {
        if ($type === 'individual') {
            $chat = Chat::with(['user1', 'user2'])->findOrFail($id);
            $messages = Message::where('chat_id', $id)->with('sender')->orderBy('created_at')->get();
            $title = $chat->user1->name . ' ↔ ' . $chat->user2->name;
        } else {
            $group = Group::with('members')->findOrFail($id);
            $messages = Message::where('group_id', $id)->with('sender')->orderBy('created_at')->get();
            $title = $group->name . ' (Group)';
        }

        return view('admin.chat-details', compact('messages', 'title', 'type', 'id'));
    }
}