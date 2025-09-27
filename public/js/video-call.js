class VideoCallManager {
    constructor() {
        this.localStream = null;
        this.remoteStreams = new Map();
        this.peers = new Map();
        this.isRecording = false;
        this.mediaRecorder = null;
        this.recordedChunks = [];
        this.currentCall = null;
        this.socket = null;
        this.isCallActive = false;
        
        this.initializeSocket();
    }
    
    // Check if permissions are already granted
    async checkPermissions(callType = 'video') {
        try {
            // Check if getUserMedia is supported
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                return false;
            }
            
            // Check current permission status without requesting
            const videoPermission = callType === 'video' ? await navigator.permissions.query({name: 'camera'}) : {state: 'granted'};
            const audioPermission = await navigator.permissions.query({name: 'microphone'});
            
            return videoPermission.state === 'granted' && audioPermission.state === 'granted';
        } catch (error) {
            // Fallback: permissions API not supported, assume we need to request
            console.log('Permissions API not supported, will request during call');
            return true; // Allow the call to proceed
        }
    }

    initializeSocket() {
        // Initialize WebSocket connection for signaling
        const wsProtocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        const wsHost = window.location.hostname;
        const wsPort = '6001';
        this.socket = new WebSocket(`${wsProtocol}//${wsHost}:${wsPort}/ws`);
        
        this.socket.onopen = () => {
            console.log('WebSocket connected for video calls');
            // Authenticate the WebSocket connection
            this.sendSignalingMessage({
                type: 'auth',
                user_id: this.getCurrentUserId()
            });
        };

        this.socket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            this.handleSignalingMessage(data);
        };
        
        this.socket.onclose = () => {
            console.log('WebSocket disconnected, attempting to reconnect...');
            setTimeout(() => this.initializeSocket(), 3000);
        };
        
        this.socket.onerror = (error) => {
            console.error('WebSocket error:', error);
        };
    }

    async initiateCall(type, targetId, callType = 'video') {
        try {
            // Check HTTPS requirement
            if (location.protocol !== 'https:' && location.hostname !== 'localhost') {
                throw new Error('Video calling requires HTTPS connection. Please use https:// instead of http://');
            }

            // Check if getUserMedia is supported
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('Your browser does not support video calling. Please use Chrome 60+, Firefox 55+, Safari 11+, or Edge 79+.');
            }

            // Check if we're in a secure context
            if (!window.isSecureContext && location.hostname !== 'localhost') {
                throw new Error('Video calling requires a secure connection (HTTPS).');
            }

            // Request permissions explicitly with better error handling
            const constraints = {
                video: callType === 'video' ? { width: 640, height: 480 } : false,
                audio: { echoCancellation: true, noiseSuppression: true }
            };
            
            console.log('Requesting media permissions:', constraints);
            
            // Try to get user media with timeout
            const mediaPromise = navigator.mediaDevices.getUserMedia(constraints);
            const timeoutPromise = new Promise((_, reject) => 
                setTimeout(() => reject(new Error('Permission request timed out')), 10000)
            );
            
            this.localStream = await Promise.race([mediaPromise, timeoutPromise]);
            console.log('Media permissions granted:', this.localStream);
            
            if (!this.localStream) {
                throw new Error('Failed to get media stream');
            }
            
            // Create call via API
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const token = localStorage.getItem('auth_token') || document.querySelector('meta[name="api-token"]')?.getAttribute('content');
            
            const headers = {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            };
            
            if (token) {
                headers['Authorization'] = `Bearer ${token}`;
            }
            
            const response = await fetch('/api/calls/initiate', {
                method: 'POST',
                headers: headers,
                credentials: 'same-origin',
                body: JSON.stringify({
                    type: type,
                    call_type: callType,
                    chat_id: type === 'one_to_one' ? targetId : null,
                    group_id: type === 'group' ? targetId : null
                })
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('API Error Response:', errorText);
                throw new Error(`API Error: ${response.status} ${response.statusText}`);
            }
            
            const result = await response.json();
            if (result.success) {
                this.currentCall = result.call;
                this.showCallInterface();
                this.setupLocalVideo();
                
                // Send call invitation via WebSocket
                this.sendSignalingMessage({
                    type: 'call-invitation',
                    callId: this.currentCall.call_id,
                    from: this.currentCall.caller.id,
                    callType: callType,
                    participants: result.call.participants
                });
            }
        } catch (error) {
            console.error('Error initiating call:', error);
            
            let errorMessage = 'Failed to start call. ';
            let troubleshooting = '';
            
            if (error.name === 'NotAllowedError') {
                errorMessage += 'Camera/microphone access was denied.';
                troubleshooting = '\n\nTroubleshooting:\n1. Click the camera/microphone icon in your browser\'s address bar\n2. Select "Allow" for camera and microphone\n3. Refresh the page and try again';
            } else if (error.name === 'NotFoundError') {
                errorMessage += 'No camera or microphone found.';
                troubleshooting = '\n\nTroubleshooting:\n1. Connect your camera/microphone\n2. Check if other apps are using them\n3. Try restarting your browser';
            } else if (error.name === 'NotReadableError') {
                errorMessage += 'Camera or microphone is already in use.';
                troubleshooting = '\n\nTroubleshooting:\n1. Close other video calling apps (Zoom, Teams, etc.)\n2. Close other browser tabs using camera/microphone\n3. Restart your browser';
            } else if (error.name === 'OverconstrainedError') {
                errorMessage += 'Camera or microphone settings are not supported.';
                troubleshooting = '\n\nTroubleshooting:\n1. Try using a different camera/microphone\n2. Update your browser\n3. Check device drivers';
            } else if (error.message.includes('HTTPS')) {
                errorMessage = error.message;
                troubleshooting = '\n\nTroubleshooting:\n1. Use https:// instead of http://\n2. Contact your administrator for SSL setup';
            } else if (error.message.includes('timed out')) {
                errorMessage += 'Permission request timed out.';
                troubleshooting = '\n\nTroubleshooting:\n1. Look for permission popup in your browser\n2. Check if popup blocker is enabled\n3. Try refreshing and clicking quickly';
            } else {
                errorMessage += error.message || 'Unknown error occurred.';
                troubleshooting = '\n\nTroubleshooting:\n1. Refresh the page\n2. Check browser console for errors\n3. Try a different browser';
            }
            
            alert(errorMessage + troubleshooting);
            
            // Also show in console for debugging
            console.group('Video Call Error Details');
            console.error('Error name:', error.name);
            console.error('Error message:', error.message);
            console.error('Browser:', navigator.userAgent);
            console.error('Protocol:', location.protocol);
            console.error('Secure context:', window.isSecureContext);
            console.groupEnd();
        }
    }

    async joinCall(callId) {
        try {
            const constraints = {
                video: this.currentCall.call_type === 'video',
                audio: true
            };
            
            this.localStream = await navigator.mediaDevices.getUserMedia(constraints);
            
            // Join call via API
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            const response = await fetch(`/api/calls/${this.currentCall.id}/join`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            if (response.ok) {
                this.showCallInterface();
                this.setupLocalVideo();
                this.isCallActive = true;
                
                // Send join confirmation via WebSocket
                this.sendSignalingMessage({
                    type: 'call-joined',
                    callId: callId,
                    userId: this.getCurrentUserId()
                });
            }
        } catch (error) {
            console.error('Error joining call:', error);
        }
    }

    async leaveCall() {
        try {
            if (this.currentCall) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                await fetch(`/api/calls/${this.currentCall.id}/leave`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
            }

            this.cleanup();
            this.hideCallInterface();
            
            // Send leave notification
            this.sendSignalingMessage({
                type: 'call-left',
                callId: this.currentCall?.call_id,
                userId: this.getCurrentUserId()
            });
            
        } catch (error) {
            console.error('Error leaving call:', error);
        }
    }

    async startRecording() {
        try {
            if (!this.localStream) return;

            const options = {
                mimeType: 'video/webm;codecs=vp9,opus'
            };

            this.mediaRecorder = new MediaRecorder(this.localStream, options);
            this.recordedChunks = [];

            this.mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    this.recordedChunks.push(event.data);
                }
            };

            this.mediaRecorder.onstop = () => {
                this.uploadRecording();
            };

            // Start recording via API
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            const response = await fetch(`/api/calls/${this.currentCall.id}/start-recording`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            if (response.ok) {
                this.mediaRecorder.start();
                this.isRecording = true;
                this.updateRecordingUI();
            }
        } catch (error) {
            console.error('Error starting recording:', error);
        }
    }

    async stopRecording() {
        if (this.mediaRecorder && this.isRecording) {
            this.mediaRecorder.stop();
            this.isRecording = false;
            this.updateRecordingUI();
        }
    }

    async uploadRecording() {
        try {
            const blob = new Blob(this.recordedChunks, { type: 'video/webm' });
            const formData = new FormData();
            formData.append('recording', blob, `call_${this.currentCall.call_id}.webm`);

            // Get the recording ID from the current call's recordings
            const recordingsResponse = await fetch(`/api/calls/${this.currentCall.id}/recordings`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            });
            
            const recordings = await recordingsResponse.json();
            const latestRecording = recordings[recordings.length - 1];

            if (latestRecording) {
                await fetch(`/api/recordings/${latestRecording.id}/upload`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    },
                    body: formData
                });
            }
        } catch (error) {
            console.error('Error uploading recording:', error);
        }
    }

    showCallInterface() {
        const callInterface = document.getElementById('video-call-interface');
        if (callInterface) {
            callInterface.classList.remove('hidden');
        } else {
            this.createCallInterface();
        }
    }

    hideCallInterface() {
        const callInterface = document.getElementById('video-call-interface');
        if (callInterface) {
            callInterface.classList.add('hidden');
        }
    }

    createCallInterface() {
        const callInterface = document.createElement('div');
        callInterface.id = 'video-call-interface';
        callInterface.className = 'fixed inset-0 bg-gray-900 z-50 flex flex-col';
        callInterface.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #1a1a1a;
            z-index: 9999;
            display: flex;
            flex-direction: column;
        `;
        
        callInterface.innerHTML = `
            <div style="flex: 1; position: relative;">
                <div id="remote-videos" class="remote-video-grid" style="display: grid; gap: 8px; padding: 16px; height: 100%;">
                    <!-- Remote videos will be added here -->
                </div>
                <div id="local-video-container" style="position: absolute; bottom: 80px; right: 16px; width: 200px; height: 150px; background: #000; border-radius: 12px; overflow: hidden; border: 2px solid #3b82f6;">
                    <video id="local-video" autoplay muted style="width: 100%; height: 100%; object-fit: cover;"></video>
                </div>
            </div>
            
            <div style="background: rgba(31, 41, 55, 0.95); padding: 16px; display: flex; justify-content: center; gap: 16px;">
                <button id="toggle-video" style="background: #3b82f6; color: white; padding: 12px; border-radius: 50%; border: none; width: 48px; height: 48px; cursor: pointer;">
                    <i class="fas fa-video"></i>
                </button>
                
                <button id="toggle-audio" style="background: #10b981; color: white; padding: 12px; border-radius: 50%; border: none; width: 48px; height: 48px; cursor: pointer;">
                    <i class="fas fa-microphone"></i>
                </button>
                
                <button id="toggle-recording" style="background: #f59e0b; color: white; padding: 12px; border-radius: 50%; border: none; width: 48px; height: 48px; cursor: pointer;">
                    <i class="fas fa-record-vinyl"></i>
                </button>
                
                <button id="end-call" style="background: #ef4444; color: white; padding: 12px; border-radius: 50%; border: none; width: 48px; height: 48px; cursor: pointer;">
                    <i class="fas fa-phone-slash"></i>
                </button>
            </div>
            
            <div id="recording-indicator" style="position: absolute; top: 16px; left: 16px; background: #ef4444; color: white; padding: 8px 16px; border-radius: 20px; font-size: 14px; display: none;">
                <span style="animation: blink 1s infinite;">●</span> Recording
            </div>
        `;
        
        document.body.appendChild(callInterface);
        this.setupCallControls();
    }

    setupCallControls() {
        document.getElementById('toggle-video')?.addEventListener('click', () => {
            this.toggleVideo();
        });
        
        document.getElementById('toggle-audio')?.addEventListener('click', () => {
            this.toggleAudio();
        });
        
        document.getElementById('toggle-recording')?.addEventListener('click', () => {
            if (this.isRecording) {
                this.stopRecording();
            } else {
                this.startRecording();
            }
        });
        
        document.getElementById('end-call')?.addEventListener('click', () => {
            this.leaveCall();
        });
    }

    setupLocalVideo() {
        const localVideo = document.getElementById('local-video');
        if (localVideo && this.localStream) {
            localVideo.srcObject = this.localStream;
        }
    }

    toggleVideo() {
        if (this.localStream) {
            const videoTrack = this.localStream.getVideoTracks()[0];
            if (videoTrack) {
                videoTrack.enabled = !videoTrack.enabled;
                this.updateVideoButton(videoTrack.enabled);
            }
        }
    }

    toggleAudio() {
        if (this.localStream) {
            const audioTrack = this.localStream.getAudioTracks()[0];
            if (audioTrack) {
                audioTrack.enabled = !audioTrack.enabled;
                this.updateAudioButton(audioTrack.enabled);
            }
        }
    }

    updateVideoButton(enabled) {
        const button = document.getElementById('toggle-video');
        if (button) {
            button.style.background = enabled ? '#3b82f6' : '#6b7280';
            button.innerHTML = enabled ? '<i class="fas fa-video"></i>' : '<i class="fas fa-video-slash"></i>';
        }
    }

    updateAudioButton(enabled) {
        const button = document.getElementById('toggle-audio');
        if (button) {
            button.style.background = enabled ? '#10b981' : '#6b7280';
            button.innerHTML = enabled ? '<i class="fas fa-microphone"></i>' : '<i class="fas fa-microphone-slash"></i>';
        }
    }

    updateRecordingUI() {
        const indicator = document.getElementById('recording-indicator');
        const button = document.getElementById('toggle-recording');
        
        if (indicator) {
            indicator.style.display = this.isRecording ? 'block' : 'none';
        }
        
        if (button) {
            button.style.background = this.isRecording ? '#ef4444' : '#f59e0b';
            button.innerHTML = this.isRecording ? '<i class="fas fa-stop"></i>' : '<i class="fas fa-record-vinyl"></i>';
        }
    }

    handleSignalingMessage(data) {
        switch (data.type) {
            case 'call-invitation':
                this.handleCallInvitation(data);
                break;
            case 'call-joined':
                this.handleCallJoined(data);
                break;
            case 'call-left':
                this.handleCallLeft(data);
                break;
        }
    }

    handleCallInvitation(data) {
        this.showCallInvitation(data);
    }
    
    showCallInvitation(data) {
        // Create call invitation modal
        const modal = document.createElement('div');
        modal.id = 'call-invitation-modal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        
        modal.innerHTML = `
            <div style="background: white; border-radius: 12px; padding: 24px; max-width: 320px; margin: 16px; text-align: center;">
                <div style="margin-bottom: 16px;">
                    <i class="fas fa-${data.callType === 'video' ? 'video' : 'phone'}" style="font-size: 48px; color: #3b82f6; margin-bottom: 8px;"></i>
                    <h3 style="font-size: 18px; font-weight: 600; margin: 8px 0;">Incoming ${data.callType} call</h3>
                    <p style="color: #6b7280;">From: ${data.callerName || 'Unknown'}</p>
                </div>
                <div style="display: flex; gap: 16px; justify-content: center;">
                    <button id="accept-call" style="background: #10b981; color: white; padding: 8px 24px; border-radius: 24px; border: none; cursor: pointer;">
                        <i class="fas fa-phone"></i> Accept
                    </button>
                    <button id="decline-call" style="background: #ef4444; color: white; padding: 8px 24px; border-radius: 24px; border: none; cursor: pointer;">
                        <i class="fas fa-phone-slash"></i> Decline
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Handle accept
        document.getElementById('accept-call').addEventListener('click', () => {
            this.currentCall = { id: data.callId, call_type: data.callType };
            this.joinCall(data.callId);
            modal.remove();
        });
        
        // Handle decline
        document.getElementById('decline-call').addEventListener('click', () => {
            this.sendSignalingMessage({
                type: 'call-response',
                callId: data.callId,
                response: 'decline',
                userId: this.getCurrentUserId()
            });
            modal.remove();
        });
        
        // Auto-decline after 30 seconds
        setTimeout(() => {
            if (document.getElementById('call-invitation-modal')) {
                modal.remove();
            }
        }, 30000);
    }

    handleCallJoined(data) {
        console.log('User joined call:', data.userId);
        // Handle peer connection setup here
    }

    handleCallLeft(data) {
        console.log('User left call:', data.userId);
        // Handle peer cleanup here
    }

    sendSignalingMessage(message) {
        if (this.socket && this.socket.readyState === WebSocket.OPEN) {
            this.socket.send(JSON.stringify(message));
        }
    }

    getCurrentUserId() {
        // Get current user ID from Laravel auth
        const userMeta = document.querySelector('meta[name="user-id"]');
        if (userMeta) {
            return parseInt(userMeta.getAttribute('content'));
        }
        // Fallback - try to get from global variable
        return window.currentUserId || 1;
    }

    cleanup() {
        if (this.localStream) {
            this.localStream.getTracks().forEach(track => track.stop());
            this.localStream = null;
        }
        
        this.peers.forEach(peer => peer.destroy());
        this.peers.clear();
        this.remoteStreams.clear();
        
        if (this.mediaRecorder && this.isRecording) {
            this.stopRecording();
        }
        
        this.currentCall = null;
        this.isCallActive = false;
    }
}

// Make VideoCallManager available globally
window.VideoCallManager = VideoCallManager;