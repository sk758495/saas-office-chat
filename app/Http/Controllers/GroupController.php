<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FileUploadHelper;

class GroupController extends Controller
{
    public function create(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'members' => 'required|array|min:1',
                'members.*' => 'exists:users,id'
            ]);

            $group = Group::create([
                'name' => $request->name,
                'description' => $request->description,
                'created_by' => Auth::id(),
                'company_id' => Auth::user()->company_id
            ]);

            // Add creator as admin
            $group->members()->attach(Auth::id(), ['role' => 'admin']);
            
            // Add selected members
            foreach ($request->members as $memberId) {
                if ($memberId != Auth::id()) {
                    $group->members()->attach($memberId, ['role' => 'member']);
                }
            }

            return response()->json(['success' => true, 'group' => $group->load('members')]);
        } catch (\Exception $e) {
            \Log::error('Group creation error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create group: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $group = Group::with(['members', 'messages.sender'])->findOrFail($id);
        
        // Check if user is member
        if (!$group->members->contains(Auth::id())) {
            return response()->json(['error' => 'Not authorized'], 403);
        }

        // Mark messages as read for current user
        $group->messages()
             ->where('sender_id', '!=', Auth::id())
             ->where('is_read', false)
             ->update(['is_read' => true, 'read_at' => now()]);

        $messages = $group->messages()->with('sender')->orderBy('created_at')->get();
        
        // Add read status for each message
        foreach ($messages as $message) {
            if ($message->sender_id == Auth::id()) {
                // Check if all group members have read this message
                $totalMembers = $group->members->count() - 1; // Exclude sender
                $readCount = Message::where('id', $message->id)
                    ->where('is_read', true)
                    ->count();
                $message->all_read = $readCount >= $totalMembers;
            }
        }

        return response()->json([
            'group' => $group,
            'messages' => $messages
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'message' => 'nullable|string',
            'file' => 'nullable|file|max:51200', // 50MB max for ZIP files
            'folder_files.*' => 'nullable|file|max:10240',
            'folder_name' => 'nullable|string|max:255',
        ]);

        if (!$request->message && !$request->hasFile('file') && !$request->hasFile('folder_files')) {
            return response()->json(['error' => 'Message, file, or folder is required'], 400);
        }

        $group = Group::findOrFail($request->group_id);
        
        // Check if user is member
        if (!$group->members->contains(Auth::id())) {
            return response()->json(['error' => 'Not authorized'], 403);
        }

        $messageData = [
            'group_id' => $request->group_id,
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
            $messageData['file_name'] = $folderData['original_name'] . '.zip';
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

        return response()->json([
            'success' => true,
            'message' => $message->load('sender')
        ]);
    }

    public function addMember(Request $request, $groupId)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        
        $group = Group::findOrFail($groupId);
        $currentUser = Auth::user();
        
        // Check if current user is admin
        $membership = $group->members()->where('user_id', $currentUser->id)->first();
        if (!$membership || $membership->pivot->role !== 'admin') {
            return response()->json(['error' => 'Only admins can add members'], 403);
        }

        $group->members()->attach($request->user_id);
        return response()->json(['success' => true]);
    }

    public function removeMember(Request $request, $groupId)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        
        $group = Group::findOrFail($groupId);
        $currentUser = Auth::user();
        
        // Check if current user is admin
        $membership = $group->members()->where('user_id', $currentUser->id)->first();
        if (!$membership || $membership->pivot->role !== 'admin') {
            return response()->json(['error' => 'Only admins can remove members'], 403);
        }

        // Prevent removing yourself
        if ($request->user_id == $currentUser->id) {
            return response()->json(['error' => 'Cannot remove yourself'], 400);
        }

        $group->members()->detach($request->user_id);
        return response()->json(['success' => true]);
    }

    public function makeAdmin(Request $request, $groupId)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        
        $group = Group::findOrFail($groupId);
        $currentUser = Auth::user();
        
        // Check if current user is admin
        $membership = $group->members()->where('user_id', $currentUser->id)->first();
        if (!$membership || $membership->pivot->role !== 'admin') {
            return response()->json(['error' => 'Only admins can make other users admin'], 403);
        }

        // Check if target user is a member
        $targetMembership = $group->members()->where('user_id', $request->user_id)->first();
        if (!$targetMembership) {
            return response()->json(['error' => 'User is not a member of this group'], 400);
        }

        // Update user to admin
        $group->members()->updateExistingPivot($request->user_id, ['role' => 'admin']);
        
        return response()->json(['success' => true]);
    }

    public function updateGroupPhoto(Request $request, $groupId)
    {
        $request->validate([
            'profile_picture' => 'required|image|max:2048'
        ]);

        $group = Group::findOrFail($groupId);
        $currentUser = Auth::user();
        
        // Check if current user is admin
        $membership = $group->members()->where('user_id', $currentUser->id)->first();
        if (!$membership || $membership->pivot->role !== 'admin') {
            return response()->json(['error' => 'Only admins can update group photo'], 403);
        }
        
        if ($request->hasFile('profile_picture')) {
            $fileName = time() . '_' . $request->file('profile_picture')->getClientOriginalName();
            $filePath = $request->file('profile_picture')->storeAs('group-photos', $fileName, 'public');
            $group->update(['profile_picture' => $filePath]);
        }

        return response()->json(['success' => true, 'photo_url' => '/storage/' . $filePath]);
    }

    public function exitGroup($groupId)
    {
        $group = Group::findOrFail($groupId);
        $currentUser = Auth::user();
        
        // Check if user is a member
        $membership = $group->members()->where('user_id', $currentUser->id)->first();
        if (!$membership) {
            return response()->json(['error' => 'You are not a member of this group'], 400);
        }

        // Remove user from group
        $group->members()->detach($currentUser->id);
        
        return response()->json(['success' => true]);
    }


}