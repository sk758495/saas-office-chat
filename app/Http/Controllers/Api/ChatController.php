<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FileUploadHelper;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ChatController extends Controller
{
    public function index()
    {
        $currentUserId = Auth::id();
        
        $users = User::where('id', '!=', $currentUserId)
                    ->with(['department', 'designation'])
                    ->get();
        
        // Add unread count and last message time for each user
        foreach ($users as $user) {
            $chat = Chat::where(function($q) use ($currentUserId, $user) {
                $q->where('user1_id', $currentUserId)->where('user2_id', $user->id)
                  ->orWhere('user1_id', $user->id)->where('user2_id', $currentUserId);
            })->first();
            
            $user->unread_count = $chat ? 
                Message::where('chat_id', $chat->id)
                       ->where('sender_id', $user->id)
                       ->where('is_read', false)
                       ->count() : 0;
            
            $user->last_message_at = $chat ? 
                Message::where('chat_id', $chat->id)
                       ->latest('created_at')
                       ->value('created_at') : null;
        }
        
        $users = $users->sortByDesc('last_message_at');
        
        // Get user's groups with unread counts
        $groups = Auth::user()->groups()->with(['members', 'lastMessage'])->get();
        
        foreach ($groups as $group) {
            $group->unread_count = Message::where('group_id', $group->id)
                                         ->where('sender_id', '!=', $currentUserId)
                                         ->where('is_read', false)
                                         ->count();
            
            $group->last_message_at = Message::where('group_id', $group->id)
                                            ->latest('created_at')
                                            ->value('created_at');
        }
        
        $groups = $groups->sortByDesc('last_message_at');

        return response()->json([
            'success' => true,
            'users' => $users->values(),
            'groups' => $groups->values()
        ]);
    }

    public function show($userId)
    {
        $chat = $this->getOrCreateChat(Auth::id(), $userId);
        $messages = $chat->messages()->with('sender')->orderBy('created_at')->get();
        
        // Mark messages as read
        $chat->messages()
             ->where('sender_id', '!=', Auth::id())
             ->where('is_read', false)
             ->update(['is_read' => true, 'read_at' => now()]);

        $otherUser = User::with(['department', 'designation'])->findOrFail($userId);
        
        return response()->json([
            'success' => true,
            'chat' => $chat,
            'messages' => $messages,
            'otherUser' => $otherUser
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'nullable|string',
            'file' => 'nullable|file|max:51200',
            'folder_files.*' => 'nullable|file|max:10240',
            'folder_name' => 'nullable|string|max:255',
        ]);
        
        if (!$request->message && !$request->hasFile('file') && !$request->hasFile('folder_files')) {
            return response()->json(['success' => false, 'message' => 'Message, file, or folder is required'], 400);
        }

        $chat = $this->getOrCreateChat(Auth::id(), $request->receiver_id);
        
        $messageData = [
            'chat_id' => $chat->id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
            'type' => 'text'
        ];

        // Handle folder upload
        if ($request->hasFile('folder_files')) {
            $folderData = FileUploadHelper::handleFolderUpload(
                $request->file('folder_files'),
                $request->folder_name ?: 'folder'
            );
            
            $messageData['file_path'] = $folderData['zip_path'];
            $messageData['file_type'] = 'zip';
            $messageData['folder_contents'] = $folderData['folder_contents'];
            $messageData['is_folder'] = true;
            $messageData['original_folder_name'] = $folderData['original_name'];
            $messageData['type'] = 'file';
        }
        // Handle single file upload
        elseif ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            
            if ($extension === 'zip') {
                $zipData = FileUploadHelper::handleZipUpload($file);
                $messageData['file_path'] = $zipData['file_path'];
                $messageData['file_type'] = 'zip';
                $messageData['folder_contents'] = $zipData['folder_contents'];
                $messageData['original_folder_name'] = $zipData['original_name'];
                $messageData['type'] = 'file';
            } else {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('chat-files', $fileName, 'public');
                
                $messageData['file_path'] = $filePath;
                $messageData['file_type'] = $extension;
                $messageData['type'] = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']) ? 'image' : 'file';
            }
            
            $messageData['file_name'] = $file->getClientOriginalName();
        }

        $message = Message::create($messageData);
        $chat->update(['last_message_at' => now()]);



        return response()->json([
            'success' => true,
            'message' => $message->load('sender')
        ]);
    }

    public function getUsers()
    {
        $currentUserId = Auth::id();
        
        $users = User::where('id', '!=', $currentUserId)
                    ->with(['department', 'designation'])
                    ->get();
        
        foreach ($users as $user) {
            $chat = Chat::where(function($q) use ($currentUserId, $user) {
                $q->where('user1_id', $currentUserId)->where('user2_id', $user->id)
                  ->orWhere('user1_id', $user->id)->where('user2_id', $currentUserId);
            })->first();
            
            $user->unread_count = $chat ? 
                Message::where('chat_id', $chat->id)
                       ->where('sender_id', $user->id)
                       ->where('is_read', false)
                       ->count() : 0;
        }
        
        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }

    public function getUnreadCounts()
    {
        $currentUserId = Auth::id();
        $unreadCounts = [];
        
        $users = User::where('id', '!=', $currentUserId)->get();
        
        foreach ($users as $user) {
            $chat = Chat::where(function($q) use ($currentUserId, $user) {
                $q->where('user1_id', $currentUserId)->where('user2_id', $user->id)
                  ->orWhere('user1_id', $user->id)->where('user2_id', $currentUserId);
            })->first();
            
            $count = $chat ? 
                Message::where('chat_id', $chat->id)
                       ->where('sender_id', $user->id)
                       ->where('is_read', false)
                       ->count() : 0;
                       
            if ($count > 0) {
                $unreadCounts[$user->id] = $count;
            }
        }
        
        return response()->json([
            'success' => true,
            'unread_counts' => $unreadCounts
        ]);
    }

    public function getGroupUnreadCounts()
    {
        $currentUserId = Auth::id();
        $unreadCounts = [];
        
        $groups = Auth::user()->groups;
        
        foreach ($groups as $group) {
            $count = Message::where('group_id', $group->id)
                           ->where('sender_id', '!=', $currentUserId)
                           ->where('is_read', false)
                           ->count();
                           
            if ($count > 0) {
                $unreadCounts[$group->id] = $count;
            }
        }
        
        return response()->json([
            'success' => true,
            'unread_counts' => $unreadCounts
        ]);
    }

    public function getAllMessages()
    {
        $currentUserId = Auth::id();
        
        $messages = Message::whereHas('chat', function($query) use ($currentUserId) {
            $query->where('user1_id', $currentUserId)
                  ->orWhere('user2_id', $currentUserId);
        })
        ->orWhere(function($query) use ($currentUserId) {
            $query->whereHas('group.members', function($q) use ($currentUserId) {
                $q->where('user_id', $currentUserId);
            });
        })
        ->with(['sender', 'chat', 'group'])
        ->orderBy('created_at', 'desc')
        ->paginate(50);
        
        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    public function getMessage($id)
    {
        $currentUserId = Auth::id();
        
        $message = Message::where('id', $id)
            ->where(function($query) use ($currentUserId) {
                $query->whereHas('chat', function($q) use ($currentUserId) {
                    $q->where('user1_id', $currentUserId)
                      ->orWhere('user2_id', $currentUserId);
                })
                ->orWhereHas('group.members', function($q) use ($currentUserId) {
                    $q->where('user_id', $currentUserId);
                });
            })
            ->with(['sender', 'chat', 'group'])
            ->first();
            
        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function deleteMessage($id)
    {
        $currentUserId = Auth::id();
        
        $message = Message::where('id', $id)
            ->where('sender_id', $currentUserId)
            ->first();
            
        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found or unauthorized'
            ], 404);
        }
        
        $message->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully'
        ]);
    }

    public function viewFile($id)
    {
        $currentUserId = Auth::id();
        
        $message = Message::where('id', $id)
            ->where(function($query) use ($currentUserId) {
                $query->whereHas('chat', function($q) use ($currentUserId) {
                    $q->where('user1_id', $currentUserId)
                      ->orWhere('user2_id', $currentUserId);
                })
                ->orWhereHas('group.members', function($q) use ($currentUserId) {
                    $q->where('user_id', $currentUserId);
                });
            })
            ->whereNotNull('file_path')
            ->first();
            
        if (!$message) {
            return response()->json(['error' => 'File not found'], 404);
        }
        
        $filePath = storage_path('app/public/' . $message->file_path);
        
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found on server'], 404);
        }
        
        return response()->file($filePath);
    }

    public function downloadFile($id)
    {
        $currentUserId = Auth::id();
        
        $message = Message::where('id', $id)
            ->where(function($query) use ($currentUserId) {
                $query->whereHas('chat', function($q) use ($currentUserId) {
                    $q->where('user1_id', $currentUserId)
                      ->orWhere('user2_id', $currentUserId);
                })
                ->orWhereHas('group.members', function($q) use ($currentUserId) {
                    $q->where('user_id', $currentUserId);
                });
            })
            ->whereNotNull('file_path')
            ->first();
            
        if (!$message) {
            return response()->json(['error' => 'File not found'], 404);
        }
        
        $filePath = storage_path('app/public/' . $message->file_path);
        
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found on server'], 404);
        }
        
        $fileName = $message->file_name ?: basename($message->file_path);
        
        return response()->download($filePath, $fileName);
    }

    private function getOrCreateChat($user1Id, $user2Id)
    {
        if ($user1Id > $user2Id) {
            [$user1Id, $user2Id] = [$user2Id, $user1Id];
        }

        return Chat::firstOrCreate([
            'user1_id' => $user1Id,
            'user2_id' => $user2Id
        ]);
    }
}