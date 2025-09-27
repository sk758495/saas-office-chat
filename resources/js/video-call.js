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

    initializeSocket() {
        // Initialize WebSocket connection for signaling
        this.socket = new WebSocket(`ws://localhost:8080`);
        
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
            // Get user media
            const constraints = {
                video: callType === 'video',
                audio: true
            };
            
            this.localStream = await navigator.mediaDevices.getUserMedia(constraints);
            
            // Create call via API
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            const response = await fetch('/api/calls/initiate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
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
            alert('Failed to start call. Please check your camera/microphone permissions.');
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
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            const recordingsResponse = await fetch(`/api/calls/${this.currentCall.id}/recordings`, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });
            
            const recordings = await recordingsResponse.json();
            const latestRecording = recordings[recordings.length - 1];

            if (latestRecording) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                await fetch(`/api/recordings/${latestRecording.id}/upload`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
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
        
        callInterface.innerHTML = `
            <div class="flex-1 relative">
                <div id="remote-videos" class="grid grid-cols-2 gap-2 h-full p-4">
                    <!-- Remote videos will be added here -->
                </div>
                <div id="local-video-container" class="absolute bottom-4 right-4 w-48 h-36 bg-black rounded-lg overflow-hidden">
                    <video id="local-video" autoplay muted class="w-full h-full object-cover"></video>
                </div>
            </div>
            
            <div class="bg-gray-800 p-4 flex justify-center space-x-4">
                <button id="toggle-video" class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h6l2 2h6a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14 8a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </button>
                
                <button id="toggle-audio" class="bg-green-600 hover:bg-green-700 text-white p-3 rounded-full">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7 4a3 3 0 016 0v4a3 3 0 11-6 0V4zm4 10.93A7.001 7.001 0 0017 8a1 1 0 10-2 0A5 5 0 015 8a1 1 0 00-2 0 7.001 7.001 0 006 6.93V17H6a1 1 0 100 2h8a1 1 0 100-2h-3v-2.07z" clip-rule="evenodd"/>
                    </svg>
                </button>
                
                <button id="toggle-recording" class="bg-yellow-600 hover:bg-yellow-700 text-white p-3 rounded-full">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                    </svg>
                </button>
                
                <button id="end-call" class="bg-red-600 hover:bg-red-700 text-white p-3 rounded-full">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 6.707 6.293a1 1 0 00-1.414 1.414L8.586 11l-3.293 3.293a1 1 0 001.414 1.414L10 12.414l3.293 3.293a1 1 0 001.414-1.414L11.414 11l3.293-3.293z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
            
            <div id="recording-indicator" class="absolute top-4 left-4 bg-red-600 text-white px-3 py-1 rounded-full text-sm hidden">
                <span class="animate-pulse">●</span> Recording
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
            button.className = enabled 
                ? 'bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full'
                : 'bg-gray-600 hover:bg-gray-700 text-white p-3 rounded-full';
        }
    }

    updateAudioButton(enabled) {
        const button = document.getElementById('toggle-audio');
        if (button) {
            button.className = enabled 
                ? 'bg-green-600 hover:bg-green-700 text-white p-3 rounded-full'
                : 'bg-gray-600 hover:bg-gray-700 text-white p-3 rounded-full';
        }
    }

    updateRecordingUI() {
        const indicator = document.getElementById('recording-indicator');
        const button = document.getElementById('toggle-recording');
        
        if (indicator) {
            indicator.classList.toggle('hidden', !this.isRecording);
        }
        
        if (button) {
            button.className = this.isRecording
                ? 'bg-red-600 hover:bg-red-700 text-white p-3 rounded-full animate-pulse'
                : 'bg-yellow-600 hover:bg-yellow-700 text-white p-3 rounded-full';
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

// Initialize video call manager
window.videoCallManager = new VideoCallManager();

// Export for use in other files
export default VideoCallManager;