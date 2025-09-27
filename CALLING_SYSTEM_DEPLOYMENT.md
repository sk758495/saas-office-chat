# Voice/Video Calling System - Production Deployment Guide

## Quick Start (For Immediate Testing)

### 1. Start WebSocket Server
```bash
# Navigate to your project directory
cd "c:\Users\sunnyverma\Desktop\SAAS Websites\office-chat"

# Run the production startup script
start-websocket-production.bat
```

### 2. Test the System
1. Open: `https://emplora.jashmainfosoft.com/call-test.html`
2. Click "Test WebSocket" to verify connection
3. Click "Test Voice Call" or "Test Video Call"
4. Check the call log for detailed information

## System Architecture

### Components
1. **Laravel Backend** - Call management and API endpoints
2. **WebSocket Server** - Real-time signaling (Node.js)
3. **Frontend JavaScript** - Video call interface and WebRTC handling
4. **Polling Fallback** - Backup when WebSocket fails

### Flow Diagram
```
User A                    Laravel API                 WebSocket Server              User B
  |                          |                            |                         |
  |-- Initiate Call -------->|                            |                         |
  |                          |-- Store Call in DB        |                         |
  |                          |-- Broadcast Invitation -->|                         |
  |                          |                            |-- Send Invitation ---->|
  |<-- Call Interface -------|                            |                         |
  |                          |                            |<-- Accept/Decline ------|
  |<-- WebRTC Signaling -----|<-- Relay Signaling -------|-- Relay Signaling ---->|
```

## Production Configuration

### 1. WebSocket Server Setup

**File: `websocket-server.js`**
- Runs on port 6001
- Handles call signaling and invitations
- Supports CORS for your domain
- Automatic client management

**Start Command:**
```bash
node websocket-server.js
```

**Production URL:**
- WebSocket: `ws://emplora.jashmainfosoft.com:6001/ws`
- HTTP Broadcast: `http://emplora.jashmainfosoft.com:6001/broadcast`

### 2. Laravel API Endpoints

**Call Management:**
- `POST /api/calls/initiate` - Start a new call
- `POST /api/calls/{call}/join` - Join an existing call
- `POST /api/calls/{call}/leave` - Leave a call
- `GET /api/calls/pending-invitations` - Get pending invitations (polling fallback)

**Authentication:**
- Uses Laravel Sanctum tokens
- CSRF protection enabled
- Session-based authentication supported

### 3. Frontend Integration

**File: `public/js/video-call.js`**
- WebSocket connection with automatic fallback
- WebRTC peer-to-peer communication
- Media device management
- Call interface UI

**Required HTML Meta Tags:**
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="user-id" content="{{ auth()->id() }}">
<meta name="api-token" content="{{ $user->createToken('api')->plainTextToken }}">
```

## Testing Instructions

### 1. Basic System Test
```
1. Open https://emplora.jashmainfosoft.com/call-test.html
2. Check system information panel (green = good, red = problem)
3. Click "Test WebSocket" - should show connection success
4. Click "Test API" - should show authentication success
```

### 2. Voice Call Test
```
1. Click "Test Voice Call"
2. Allow microphone access when prompted
3. Should see call interface appear
4. Check call log for success messages
```

### 3. Video Call Test
```
1. Click "Test Video Call"
2. Allow camera and microphone access when prompted
3. Should see call interface with local video
4. Check call log for success messages
```

### 4. Incoming Call Test
```
1. Click "Simulate Incoming Call"
2. Should see call invitation modal
3. Test Accept/Decline buttons
4. Check call log for invitation details
```

## Troubleshooting

### Common Issues

#### 1. WebSocket Connection Failed
**Symptoms:** "WebSocket connection failed" in logs
**Solutions:**
- Ensure WebSocket server is running: `node websocket-server.js`
- Check if port 6001 is open in firewall
- Verify domain configuration
- System will automatically use polling fallback

#### 2. Camera/Microphone Access Denied
**Symptoms:** "NotAllowedError" or permission denied
**Solutions:**
- Click camera icon in browser address bar
- Select "Allow" for camera and microphone
- Refresh page and try again
- Check if other apps are using the devices

#### 3. API Authentication Failed
**Symptoms:** 401 errors or "Authentication failed"
**Solutions:**
- Ensure user is logged in to Laravel
- Check CSRF token is present
- Verify Sanctum is configured correctly
- Clear browser cache and cookies

#### 4. Call Invitation Not Received
**Symptoms:** Other user doesn't get call invitation
**Solutions:**
- Check WebSocket server logs for broadcast messages
- Verify both users are connected to WebSocket
- Polling fallback should work if WebSocket fails
- Check browser console for errors

### Debug Commands

**Check WebSocket Status:**
```javascript
console.log('WebSocket state:', videoCallManager.socket?.readyState);
console.log('Polling active:', !!videoCallManager.pollingInterval);
```

**Test Manual Invitation:**
```javascript
videoCallManager.handleSignalingMessage({
    type: 'call-invitation',
    callId: 'test-123',
    callerName: 'Test User',
    callType: 'video'
});
```

**Check Authentication:**
```javascript
fetch('/api/test-auth', {
    headers: { 'Accept': 'application/json' },
    credentials: 'same-origin'
}).then(r => r.json()).then(console.log);
```

## Production Deployment Steps

### 1. Server Setup
```bash
# Install Node.js dependencies
npm install ws express cors

# Start WebSocket server (keep running)
node websocket-server.js

# Or use PM2 for production
npm install -g pm2
pm2 start websocket-server.js --name "websocket-server"
```

### 2. Laravel Configuration
```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Ensure storage permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### 3. Database Setup
```bash
# Run migrations if not already done
php artisan migrate

# Ensure call-related tables exist
# - calls
# - call_participants
# - call_recordings
```

### 4. Web Server Configuration

**Apache (.htaccess):**
```apache
# Already configured in public/.htaccess
# Ensure mod_rewrite is enabled
```

**Nginx (if using):**
```nginx
# Add WebSocket proxy
location /ws {
    proxy_pass http://localhost:6001;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header Host $host;
}
```

### 5. SSL/HTTPS Configuration
- Video calling works better with HTTPS
- Ensure SSL certificate is valid
- WebSocket can use HTTP for now, but WSS is recommended

## Monitoring and Maintenance

### 1. Log Files
- **Laravel:** `storage/logs/laravel.log`
- **WebSocket:** Console output (consider logging to file)
- **Browser:** Developer Console

### 2. Performance Monitoring
- Monitor WebSocket connections: `clients.size`
- Track active calls: `activeCalls.size`
- Monitor API response times

### 3. Regular Maintenance
- Restart WebSocket server weekly
- Clear Laravel caches regularly
- Monitor disk space for call recordings
- Update dependencies monthly

## Security Considerations

### 1. Authentication
- All API endpoints require authentication
- WebSocket connections are user-specific
- CSRF protection enabled

### 2. Data Privacy
- Call data is stored temporarily
- Recordings are optional and user-controlled
- No call content is logged

### 3. Network Security
- Use HTTPS in production
- Consider WSS for WebSocket
- Implement rate limiting for API calls

## Support and Maintenance

### Contact Information
- **Domain:** https://emplora.jashmainfosoft.com
- **Test Page:** https://emplora.jashmainfosoft.com/call-test.html
- **WebSocket:** ws://emplora.jashmainfosoft.com:6001/ws

### System Status Check
1. WebSocket server running: Check port 6001
2. Laravel application: Check main site
3. Database connectivity: Check API responses
4. Media permissions: Check browser settings

The calling system is now production-ready with comprehensive error handling, fallback mechanisms, and detailed logging for troubleshooting.