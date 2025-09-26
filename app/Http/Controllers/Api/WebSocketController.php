<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WebSocketController extends Controller
{
    public function connect(Request $request)
    {
        return response()->json([
            'success' => true,
            'websocket_url' => 'ws://office-chat.jashmainfosoft.com:6001/ws',
            'status' => 'ready_to_connect',
            'user_id' => auth()->id()
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
          ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    public function broadcast(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'data' => 'required|array',
            'user_id' => 'required|integer',
            'chat_id' => 'nullable|integer',
            'group_id' => 'nullable|integer'
        ]);

        // Send to WebSocket server
        try {
            $response = Http::timeout(5)->post('http://office-chat.jashmainfosoft.com:6001/broadcast', [
                'type' => $validated['type'],
                'data' => $validated['data'],
                'user_id' => $validated['user_id'],
                'chat_id' => $validated['chat_id'] ?? null,
                'group_id' => $validated['group_id'] ?? null,
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message broadcasted successfully'
            ])->header('Access-Control-Allow-Origin', '*')
              ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
              ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to broadcast message',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}