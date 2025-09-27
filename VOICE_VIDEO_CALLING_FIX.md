# Voice/Video Calling Fix Guide

## Issues Fixed

### 1. Call Invitations Not Reaching Recipients
**Problem**: When initiating a call, the other party doesn't receive the invitation.

**Root Cause**: 
- WebSocket server not properly broadcasting invitations
- Mismatch in participant data structure between frontend and backend
- Missing fallback mechanism when WebSocket fails

**Solution**:
- Enhanced `CallController::initiateCall()` to broadcast invitations via WebSocket
- Added polling fallback mechanism when WebSocket is unavailable
- Fixed participant data structure consistency

### 2. WebSocket Connection Issues
**Problem**: WebSocket connection fails on production domain.

**Solution**:
- Added automatic fallback to polling when WebSocket fails
- Enhanced error handling and connection retry logic
- Created production WebSocket startup script

### 3. Missing API Endpoints
**Problem**: No endpoint for retrieving pending call invitations.

**Solution**:
- Added `/api/calls/pending-invitations` endpoint
- Implemented cache-based invitation storage as fallback

## Files Modified

### Backend (Laravel)
1. **app/Http/Controllers/CallController.php**
   - Added `broadcastCallInvitation()` method
   - Added `storeCallInvitation()` fallback method
   - Added `getPendingInvitations()` endpoint
   - Enhanced error handling and logging

2. **routes/api.php**
   - Added `/api/calls/pending-invitations` route

### Frontend (JavaScript)
3. **public/js/video-call.js**
   - Added polling fallback mechanism
   - Enhanced WebSocket error handling
   - Improved call invitation flow
   - Added `startPollingFallback()` and `stopPollingFallback()` methods

### Test Files
4. **public/call-test.html** (New)
   - Comprehensive testing interface
   - Real-time logging and status updates
   - Voice and video call testing

5. **start-websocket-production.bat** (New)
   - Production WebSocket server startup script

## How It Works Now

### Call Initiation Flow
1. User clicks call button
2. Frontend calls `/api/calls/initiate` endpoint
3. Backend creates call record in database
4. Backend attempts to broadcast invitation via WebSocket
5. If WebSocket fails, invitation is stored in cache
6. Recipients receive invitation via WebSocket OR polling

### Invitation Delivery
- **Primary**: WebSocket real-time delivery
- **Fallback**: HTTP polling every 2 seconds
- **Cache**: 2-minute expiration for pending invitations

### Error Handling
- Comprehensive error messages with troubleshooting steps
- Automatic fallback mechanisms
- Detailed logging for debugging

## Testing Instructions

### 1. Basic Functionality Test
```
1. Open https://emplora.jashmainfosoft.com/call-test.html
2. Click "Test Voice Call" or "Test Video Call"
3. Check browser console and call log for detailed information
4. Verify permissions are requested properly
```

### 2. WebSocket Test
```
1. Start WebSocket server: run start-websocket-production.bat
2. Open browser developer tools → Network tab
3. Look for WebSocket connection attempts
4. Check console for connection status messages
```

### 3. Polling Fallback Test
```
1. Ensure WebSocket server is NOT running
2. Initiate a call from one browser tab
3. Open another tab and check if invitation appears
4. Should see "Starting polling fallback" in console
```

## Production Deployment

### 1. WebSocket Server
```bash
# Install dependencies
npm install ws express cors

# Start server (keep running)
node websocket-server.js
```

### 2. Laravel Configuration
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear

# Ensure storage permissions
chmod -R 755 storage/
```

### 3. HTTPS Requirements
- Ensure SSL certificate is properly configured
- Video calling requires HTTPS for getUserMedia API
- WebSocket should use WSS (secure) protocol

## Troubleshooting

### Common Issues

1. **"Camera/microphone access denied"**
   - Click camera icon in browser address bar
   - Select "Allow" for camera and microphone
   - Refresh page and try again

2. **"WebSocket connection failed"**
   - Check if WebSocket server is running on port 6001
   - Verify firewall allows port 6001
   - System will automatically use polling fallback

3. **"Call invitation not received"**
   - Check browser console for errors
   - Verify both users are authenticated
   - Check if polling fallback is working

4. **"HTTPS required"**
   - Ensure using https:// not http://
   - Check SSL certificate validity
   - Local testing: use localhost (exempt from HTTPS requirement)

### Debug Commands

```javascript
// Check WebSocket status
console.log(videoCallManager.socket?.readyState);

// Check polling status
console.log(videoCallManager.pollingInterval);

// Manual invitation test
videoCallManager.handleSignalingMessage({
    type: 'call-invitation',
    callId: 'test-123',
    callerName: 'Test User',
    callType: 'video'
});
```

## Next Steps

1. **Test the fixes**:
   - Use the test page: `/call-test.html`
   - Test with multiple browser tabs/users
   - Verify both voice and video calls work

2. **Monitor logs**:
   - Check Laravel logs: `storage/logs/laravel.log`
   - Check browser console for JavaScript errors
   - Monitor WebSocket server console output

3. **Production deployment**:
   - Start WebSocket server on production
   - Test with real users
   - Monitor performance and error rates

The calling system now has robust fallback mechanisms and should work reliably even when WebSocket connections fail.