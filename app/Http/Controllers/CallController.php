<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\CallRecording;
use App\Models\Chat;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CallController extends Controller
{
    public function initiateCall(Request $request)
    {
        // Debug logging
        \Log::info('Call initiate request data:', $request->all());
        \Log::info('Current user:', ['id' => auth()->id(), 'name' => auth()->user()?->name]);
        
        // Simplified validation for debugging
        if (!$request->has('type') || !$request->has('call_type')) {
            return response()->json([
                'success' => false,
                'message' => 'Missing required fields: type and call_type',
                'received_data' => $request->all()
            ], 422);
        }
        
        if (!in_array($request->type, ['one_to_one', 'group'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid type. Must be one_to_one or group',
                'received_type' => $request->type
            ], 422);
        }
        
        if (!in_array($request->call_type, ['audio', 'video'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid call_type. Must be audio or video',
                'received_call_type' => $request->call_type
            ], 422);
        }
        
        if ($request->type === 'one_to_one' && !$request->chat_id) {
            return response()->json([
                'success' => false,
                'message' => 'chat_id is required for one_to_one calls',
                'received_chat_id' => $request->chat_id
            ], 422);
        }
        
        if ($request->type === 'group' && !$request->group_id) {
            return response()->json([
                'success' => false,
                'message' => 'group_id is required for group calls',
                'received_group_id' => $request->group_id
            ], 422);
        }
        
        // Additional validation for user access
        if ($request->type === 'one_to_one') {
            $chat = Chat::find($request->chat_id);
            if (!$chat || ($chat->user1_id !== auth()->id() && $chat->user2_id !== auth()->id())) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have access to this chat'
                ], 403);
            }
        } else {
            $group = Group::find($request->group_id);
            if (!$group || !$group->members()->where('user_id', auth()->id())->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this group'
                ], 403);
            }
        }

        try {
            $call = Call::create([
                'call_id' => Str::uuid(),
                'type' => $request->type,
                'call_type' => $request->call_type,
                'status' => 'initiated',
                'caller_id' => auth()->id(),
                'chat_id' => $request->chat_id,
                'group_id' => $request->group_id,
                'started_at' => now(),
            ]);
            
            \Log::info('Call created successfully:', ['call_id' => $call->id]);
        } catch (\Exception $e) {
            \Log::error('Failed to create call:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create call: ' . $e->getMessage()
            ], 500);
        }

        // Add participants
        try {
            if ($request->type === 'one_to_one') {
                $chat = Chat::find($request->chat_id);
                if (!$chat) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Chat not found'
                    ], 404);
                }
                $otherUser = $chat->getOtherUser(auth()->id());
                if (!$otherUser) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Other user not found in chat'
                    ], 404);
                }
                $call->participants()->attach($otherUser->id, ['status' => 'invited']);
                \Log::info('Added participant to one-to-one call:', ['user_id' => $otherUser->id]);
            } else {
                $group = Group::find($request->group_id);
                if (!$group) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Group not found'
                    ], 404);
                }
                $memberIds = $group->members()->where('user_id', '!=', auth()->id())->pluck('user_id');
                foreach ($memberIds as $memberId) {
                    $call->participants()->attach($memberId, ['status' => 'invited']);
                }
                \Log::info('Added participants to group call:', ['member_count' => $memberIds->count()]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to add participants:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to add participants: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'call' => $call->load(['caller', 'participants', 'chat', 'group'])
        ]);
    }

    public function joinCall(Request $request, Call $call)
    {
        $user = auth()->user();
        
        $call->participants()->updateExistingPivot($user->id, [
            'status' => 'joined',
            'joined_at' => now()
        ]);

        if ($call->status === 'initiated') {
            $call->update(['status' => 'active']);
        }

        return response()->json([
            'success' => true,
            'call' => $call->load(['caller', 'participants', 'recordings'])
        ]);
    }

    public function leaveCall(Request $request, Call $call)
    {
        $user = auth()->user();
        
        $call->participants()->updateExistingPivot($user->id, [
            'status' => 'left',
            'left_at' => now()
        ]);

        // Check if all participants have left
        $activeParticipants = $call->participants()
            ->wherePivot('status', 'joined')
            ->count();

        if ($activeParticipants === 0 || $user->id === $call->caller_id) {
            $call->update([
                'status' => 'ended',
                'ended_at' => now(),
                'duration' => now()->diffInSeconds($call->started_at)
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function declineCall(Request $request, Call $call)
    {
        $user = auth()->user();
        
        $call->participants()->updateExistingPivot($user->id, [
            'status' => 'declined'
        ]);

        // If one-to-one call and declined, end the call
        if ($call->type === 'one_to_one') {
            $call->update(['status' => 'declined']);
        }

        return response()->json(['success' => true]);
    }

    public function startRecording(Request $request, Call $call)
    {
        $recording = CallRecording::create([
            'call_id' => $call->id,
            'file_path' => 'recordings/' . $call->call_id . '_' . time() . '.webm',
            'file_name' => 'Call_Recording_' . $call->call_id . '.webm',
            'status' => 'recording',
            'started_by' => auth()->id(),
            'started_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'recording' => $recording
        ]);
    }

    public function stopRecording(Request $request, CallRecording $recording)
    {
        $recording->update([
            'status' => 'completed',
            'completed_at' => now(),
            'duration' => now()->diffInSeconds($recording->started_at)
        ]);

        return response()->json(['success' => true]);
    }

    public function uploadRecording(Request $request, CallRecording $recording)
    {
        $request->validate([
            'recording' => 'required|file|mimes:webm,mp4,avi|max:102400' // 100MB max
        ]);

        $file = $request->file('recording');
        $path = $file->storeAs('recordings', $recording->file_name, 'public');

        $recording->update([
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'status' => 'completed'
        ]);

        return response()->json(['success' => true]);
    }

    public function getCallHistory(Request $request)
    {
        $user = auth()->user();
        
        $calls = Call::where('caller_id', $user->id)
            ->orWhereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['caller', 'participants', 'recordings', 'chat', 'group'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($calls);
    }

    public function getCallRecordings(Request $request, Call $call)
    {
        $recordings = $call->recordings()
            ->with('startedBy')
            ->where('status', 'completed')
            ->get();

        return response()->json($recordings);
    }

    public function downloadRecording(CallRecording $recording)
    {
        if (!Storage::disk('public')->exists($recording->file_path)) {
            return response()->json(['error' => 'Recording not found'], 404);
        }

        return Storage::disk('public')->download($recording->file_path, $recording->file_name);
    }
}