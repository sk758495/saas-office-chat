# Video Calling & Recording System

## Overview
This system provides comprehensive video calling and recording functionality for your office chat application, including:

- **One-to-One Video/Audio Calls**
- **Group Video/Audio Calls**
- **Call Recording with Download**
- **Call History Management**
- **Real-time WebRTC Communication**

## Features

### 🎥 Video Calling
- HD video calling with WebRTC
- Audio-only calling option
- Screen sharing capabilities
- Multiple participants support
- Real-time connection status

### 📹 Recording System
- Record entire calls
- Download recordings in WebM format
- Automatic file management
- Recording status indicators
- File size and duration tracking

### 📊 Call Management
- Complete call history
- Call duration tracking
- Participant management
- Call status monitoring
- Missed call notifications

## Implementation

### 1. Database Structure
```sql
-- Calls table stores call sessions
calls: id, call_id, type, call_type, status, caller_id, chat_id, group_id, started_at, ended_at, duration

-- Call participants track who joined
call_participants: id, call_id, user_id, status, joined_at, left_at

-- Call recordings store recorded files
call_recordings: id, call_id, file_path, file_name, file_size, duration, status, started_by
```

### 2. API Endpoints
```php
POST /api/calls/initiate          // Start a new call
POST /api/calls/{call}/join       // Join an existing call
POST /api/calls/{call}/leave      // Leave a call
POST /api/calls/{call}/decline    // Decline a call invitation
POST /api/calls/{call}/start-recording  // Start recording
POST /api/recordings/{recording}/stop   // Stop recording
GET  /api/calls/history          // Get call history
GET  /api/recordings/{recording}/download // Download recording
```

### 3. Frontend Integration

#### Add Call Buttons to Chat Interface
```blade
<!-- In your chat header -->
<x-call-buttons type="one_to_one" :target-id="$chat->id" />

<!-- In group chat header -->
<x-call-buttons type="group" :target-id="$group->id" />
```

#### Include Call History Component
```blade
<!-- In your dashboard or chat page -->
<x-call-history />
```

#### Add Video Call CSS
```html
<link rel="stylesheet" href="{{ asset('css/video-call.css') }}">
```

### 4. JavaScript Usage

#### Initialize Video Call Manager
```javascript
// Already initialized in app.js
const callManager = window.videoCallManager;

// Start a video call
await callManager.initiateCall('one_to_one', chatId, 'video');

// Start an audio call
await callManager.initiateCall('group', groupId, 'audio');
```

#### Handle Incoming Calls
```javascript
// The system automatically handles incoming calls with browser notifications
// Users can accept/decline through the UI
```

## Usage Examples

### 1. One-to-One Video Call
```javascript
// Initiate call
await window.videoCallManager.initiateCall('one_to_one', 123, 'video');

// The other user receives a call invitation
// They can accept or decline
```

### 2. Group Video Call
```javascript
// Start group call
await window.videoCallManager.initiateCall('group', 456, 'video');

// All group members receive invitations
// Multiple participants can join
```

### 3. Recording Management
```javascript
// Start recording during a call
await window.videoCallManager.startRecording();

// Stop recording
await window.videoCallManager.stopRecording();

// Recordings are automatically uploaded and available for download
```

## Configuration

### 1. WebSocket Server
The WebSocket server handles real-time signaling for calls:
```bash
# Start WebSocket server
npm run websocket
```

### 2. Storage Configuration
Ensure your storage is configured for recordings:
```php
// config/filesystems.php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
],
```

### 3. Permissions
Users need camera/microphone permissions:
- The system automatically requests permissions
- Graceful fallback for denied permissions
- Audio-only mode if video is unavailable

## Security Features

### 1. Authentication
- All API endpoints require authentication
- WebSocket connections are user-specific
- Call participants are validated

### 2. Privacy
- Recordings are stored securely
- Only call participants can access recordings
- Automatic cleanup of old recordings (configurable)

### 3. Access Control
- Company-based isolation
- Department/group restrictions
- Admin monitoring capabilities

## Browser Compatibility

### Supported Browsers
- ✅ Chrome 60+
- ✅ Firefox 55+
- ✅ Safari 11+
- ✅ Edge 79+

### Required Features
- WebRTC support
- MediaRecorder API
- WebSocket support
- getUserMedia API

## Troubleshooting

### Common Issues

1. **Camera/Microphone Not Working**
   - Check browser permissions
   - Ensure HTTPS connection
   - Verify device availability

2. **Call Not Connecting**
   - Check WebSocket server status
   - Verify network connectivity
   - Check firewall settings

3. **Recording Failed**
   - Check storage permissions
   - Verify disk space
   - Check file upload limits

### Debug Mode
Enable debug logging:
```javascript
// In browser console
localStorage.setItem('debug_video_calls', 'true');
```

## Performance Optimization

### 1. Video Quality
- Automatic quality adjustment based on connection
- Configurable resolution settings
- Bandwidth optimization

### 2. Server Resources
- Efficient WebSocket handling
- Optimized recording storage
- Automatic cleanup processes

### 3. Client Performance
- Lazy loading of video components
- Memory management for streams
- Efficient DOM updates

## Future Enhancements

### Planned Features
- Screen sharing
- Virtual backgrounds
- Chat during calls
- Call scheduling
- Advanced recording options
- Mobile app integration

### Integration Options
- Calendar integration
- Email notifications
- Third-party video services
- AI-powered features

## Support

For technical support or feature requests:
1. Check the troubleshooting guide
2. Review browser console for errors
3. Verify WebSocket server status
4. Contact development team

---

**Note**: This system requires HTTPS for production use due to WebRTC security requirements.