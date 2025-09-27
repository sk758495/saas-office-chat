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
    
    // Check and request permissions
    async checkPermissions(callType = 'video') {
        try {
            const constraints = {
                video: callType === 'video',
                audio: true
            };
            
            // Test permissions by requesting media
            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            
            // Stop the test stream immediately
            stream.getTracks().forEach(track => track.stop());
            
            return true;
        } catch (error) {
            console.error('Permission check failed:', error);
            return false;
        }
    }

    initializeSocket() {
        // Initialize WebSocket connection for signaling
        this.socket = new WebSocket(`ws://localhost:6001/ws`);
        
        this.socket.onopen = () => {
            console.log('WebSocket connected for video calls');
        };

        this.socket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            this.handleSignalingMessage(data);
        };
    }

    async initiateCall(type, targetId, callType = 'video') {
        try {
            // Check if getUserMedia is supported
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('Your browser does not support video calling. Please use a modern browser like Chrome, Firefox, or Safari.');
            }

            // Request permissions explicitly
            const constraints = {
                video: callType === 'video',
                audio: true
            };
            
            console.log('Requesting media permissions:', constraints);
            this.localStream = await navigator.mediaDevices.getUserMedia(constraints);
            console.log('Media permissions granted:', this.localStream);
            
            if (!this.localStream) {
                throw new Error('Failed to get media stream');
            }
            
            // Create call via API
            const response = await fetch('/api/calls/initiate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                },
                body: JSON.stringify({
                    type: type,
                    call_type: callType,
                    chat_id: type === 'one_to_one' ? targetId : null,
                    group_id: type === 'group' ? targetId : null
                })
            });

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
            
            if (error.name === 'NotAllowedError') {
                errorMessage += 'Please allow camera and microphone access when prompted by your browser.';
            } else if (error.name === 'NotFoundError') {
                errorMessage += 'No camera or microphone found. Please connect your devices.';
            } else if (error.name === 'NotReadableError') {
                errorMessage += 'Camera or microphone is already in use by another application.';
            } else if (error.name === 'OverconstrainedError') {
                errorMessage += 'Camera or microphone constraints cannot be satisfied.';
            } else {
                errorMessage += error.message || 'Please check your camera/microphone permissions.';
            }
            
            alert(errorMessage);
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
            const response = await fetch(`/api/calls/${this.currentCall.id}/join`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
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
                await fetch(`/api/calls/${this.currentCall.id}/leave`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
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
            const response = await fetch(`/api/calls/${this.currentCall.id}/start-recording`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
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
        if (confirm(`Incoming ${data.callType} call. Accept?`)) {
            this.currentCall = { id: data.callId, call_type: data.callType };
            this.joinCall(data.callId);
        }
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
        // Get current user ID from your auth system
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