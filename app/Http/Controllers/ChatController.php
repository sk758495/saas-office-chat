<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\FileUploadHelper;

class ChatController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Check if user has PIN enabled and not verified
        if ($user->chat_lock_enabled && $user->chat_pin && !session('chat_unlocked')) {
            return redirect()->route('profile.show')->with('pin_required', true);
        }
        
        $currentUserId = Auth::id();
        
        $users = User::where('id', '!=', $currentUserId)
                    ->where('company_id', auth()->user()->company_id)
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
            
            // Get last message time for sorting
            $user->last_message_at = $chat ? 
                Message::where('chat_id', $chat->id)
                       ->latest('created_at')
                       ->value('created_at') : null;
        }
        
        // Sort users by last message time (most recent first)
        $users = $users->sortByDesc('last_message_at');
        
        // Get user's groups with unread counts
        $groups = Auth::user()->groups()->with(['members', 'lastMessage'])->get();
        
        // Add unread count and last message time for each group
        foreach ($groups as $group) {
            $group->unread_count = Message::where('group_id', $group->id)
                                         ->where('sender_id', '!=', $currentUserId)
                                         ->where('is_read', false)
                                         ->count();
            
            // Get last message time for sorting
            $group->last_message_at = Message::where('group_id', $group->id)
                                            ->latest('created_at')
                                            ->value('created_at');
        }
        
        // Sort groups by last message time (most recent first)
        $groups = $groups->sortByDesc('last_message_at');
        
        $chats = Chat::where('user1_id', $currentUserId)
                    ->orWhere('user2_id', $currentUserId)
                    ->with(['user1', 'user2', 'lastMessage'])
                    ->orderBy('last_message_at', 'desc')
                    ->get();

        // Get company theme colors
        $company = auth()->user()->company;
        $chatTheme = [
            'primary_color' => $company->chat_primary_color ?? '#ff6b35',
            'secondary_color' => $company->chat_secondary_color ?? '#f7931e',
            'theme_name' => $company->chat_theme_name ?? 'Orange Sunset'
        ];
        
        return view('chat.index', compact('users', 'groups', 'chats', 'chatTheme'));
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
            'file' => 'nullable|file|max:51200', // 50MB max for ZIP files
            'folder_files.*' => 'nullable|file|max:10240',
            'folder_name' => 'nullable|string|max:255',
        ]);
        
        if (!$request->message && !$request->hasFile('file') && !$request->hasFile('folder_files')) {
            return response()->json(['error' => 'Message, file, or folder is required'], 400);
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
        // Handle single file upload (including ZIP)
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
            'message' => $message->load('sender'),
            'success' => true
        ]);
    }

    private function getOrCreateChat($user1Id, $user2Id)
    {
        // Ensure consistent ordering
        if ($user1Id > $user2Id) {
            [$user1Id, $user2Id] = [$user2Id, $user1Id];
        }

        return Chat::firstOrCreate([
            'user1_id' => $user1Id,
            'user2_id' => $user2Id
        ]);
    }

    public function getUsers()
    {
        $currentUserId = Auth::id();
        
        $users = User::where('id', '!=', $currentUserId)
                    ->where('company_id', auth()->user()->company_id)
                    ->with(['department', 'designation'])
                    ->get();
        
        // Add unread count for each user
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
        
        return response()->json($users);
    }

    public function getUnreadCounts()
    {
        $currentUserId = Auth::id();
        $unreadCounts = [];
        
        $users = User::where('id', '!=', $currentUserId)
                    ->where('company_id', auth()->user()->company_id)
                    ->get();
        
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
        
        return response()->json($unreadCounts);
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
        
        return response()->json($unreadCounts);
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
            abort(404, 'File not found');
        }
        
        $filePath = storage_path('app/public/' . $message->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found on server');
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
            abort(404, 'File not found');
        }
        
        $filePath = storage_path('app/public/' . $message->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found on server');
        }
        
        $fileName = $message->file_name ?: basename($message->file_path);
        
        return response()->download($filePath, $fileName);
    }
}