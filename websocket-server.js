const WebSocket = require('ws');
const http = require('http');
const express = require('express');
const cors = require('cors');

const app = express();
app.use(cors());
app.use(express.json());

const server = http.createServer(app);
const wss = new WebSocket.Server({ 
    server,
    path: '/ws'
});

const clients = new Map();
const activeCalls = new Map(); // Store active call sessions

// HTTP endpoint for broadcasting
app.post('/broadcast', (req, res) => {
    const { type, data, user_id, chat_id, group_id, timestamp } = req.body;
    
    const message = {
        type,
        data,
        user_id,
        chat_id,
        group_id,
        timestamp
    };
    
    // Broadcast to all connected WebSocket clients
    wss.clients.forEach((client) => {
        if (client.readyState === WebSocket.OPEN) {
            client.send(JSON.stringify(message));
        }
    });
    
    res.json({ success: true, message: 'Broadcasted successfully' });
});

wss.on('connection', (ws, req) => {
    console.log('New WebSocket connection');
    
    ws.on('message', (message) => {
        try {
            const data = JSON.parse(message);
            console.log('Received:', data);
            
            // Store user connection
            if (data.type === 'auth' && data.user_id) {
                clients.set(data.user_id, ws);
                ws.user_id = data.user_id;
            }
            
            // Handle call signaling
            if (data.type === 'call-invitation') {
                handleCallInvitation(data, ws);
            } else if (data.type === 'call-response') {
                handleCallResponse(data, ws);
            } else if (data.type === 'webrtc-signal') {
                handleWebRTCSignal(data, ws);
            } else {
                // Broadcast to all connected clients except sender
                wss.clients.forEach((client) => {
                    if (client !== ws && client.readyState === WebSocket.OPEN) {
                        client.send(JSON.stringify(data));
                    }
                });
            }
        } catch (error) {
            console.error('Error parsing message:', error);
        }
    });

    ws.on('close', () => {
        console.log('WebSocket connection closed');
        if (ws.user_id) {
            clients.delete(ws.user_id);
        }
    });

    ws.on('error', (error) => {
        console.error('WebSocket error:', error);
    });
});

// Call signaling handlers
function handleCallInvitation(data, senderWs) {
    const { callId, participants, callType } = data;
    
    // Store call session
    activeCalls.set(callId, {
        participants: participants.map(p => p.id),
        callType,
        status: 'ringing'
    });
    
    // Send invitation to participants
    participants.forEach(participant => {
        const participantWs = clients.get(participant.id);
        if (participantWs && participantWs.readyState === WebSocket.OPEN) {
            participantWs.send(JSON.stringify({
                type: 'call-invitation',
                callId,
                caller: data.from,
                callType,
                timestamp: Date.now()
            }));
        }
    });
}

function handleCallResponse(data, senderWs) {
    const { callId, response, userId } = data; // response: 'accept' or 'decline'
    
    const call = activeCalls.get(callId);
    if (!call) return;
    
    // Notify all participants about the response
    call.participants.forEach(participantId => {
        const participantWs = clients.get(participantId);
        if (participantWs && participantWs.readyState === WebSocket.OPEN) {
            participantWs.send(JSON.stringify({
                type: 'call-response',
                callId,
                userId,
                response,
                timestamp: Date.now()
            }));
        }
    });
    
    if (response === 'accept') {
        call.status = 'active';
    } else if (response === 'decline') {
        // If declined, end the call
        activeCalls.delete(callId);
    }
}

function handleWebRTCSignal(data, senderWs) {
    const { callId, targetUserId, signal } = data;
    
    const targetWs = clients.get(targetUserId);
    if (targetWs && targetWs.readyState === WebSocket.OPEN) {
        targetWs.send(JSON.stringify({
            type: 'webrtc-signal',
            callId,
            fromUserId: senderWs.user_id,
            signal,
            timestamp: Date.now()
        }));
    }
}

server.listen(6001, '0.0.0.0', () => {
    console.log('WebSocket server running on ws://office-chat.jashmainfosoft.com:6001/ws');
    console.log('HTTP broadcast endpoint: http://office-chat.jashmainfosoft.com:6001/broadcast');
    console.log('Video calling signaling enabled');
});