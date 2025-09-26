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
            
            // Broadcast to all connected clients except sender
            wss.clients.forEach((client) => {
                if (client !== ws && client.readyState === WebSocket.OPEN) {
                    client.send(JSON.stringify(data));
                }
            });
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

server.listen(6001, '0.0.0.0', () => {
    console.log('WebSocket server running on ws://office-chat.jashmainfosoft.com:6001/ws');
    console.log('HTTP broadcast endpoint: http://office-chat.jashmainfosoft.com:6001/broadcast');
});