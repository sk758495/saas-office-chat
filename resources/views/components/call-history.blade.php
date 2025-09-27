<div x-data="callHistory()" x-init="loadCallHistory()" class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Call History</h2>
        <button @click="loadCallHistory()" class="text-blue-600 hover:text-blue-800">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
            </svg>
        </button>
    </div>

    <div x-show="loading" class="text-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
        <p class="text-gray-600 mt-2">Loading call history...</p>
    </div>

    <div x-show="!loading && calls.length === 0" class="text-center py-8">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
        </svg>
        <p class="text-gray-600">No call history found</p>
    </div>

    <div x-show="!loading && calls.length > 0" class="space-y-4">
        <template x-for="call in calls" :key="call.id">
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <!-- Call Type Icon -->
                        <div class="flex-shrink-0">
                            <div x-show="call.call_type === 'video'" class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 6a2 2 0 012-2h6l2 2h6a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14 8a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div x-show="call.call_type === 'audio'" class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                </svg>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center space-x-2">
                                <h3 class="font-medium text-gray-900" x-text="getCallTitle(call)"></h3>
                                <span class="px-2 py-1 text-xs rounded-full" 
                                      :class="getStatusClass(call.status)" 
                                      x-text="call.status"></span>
                            </div>
                            <div class="text-sm text-gray-600 mt-1">
                                <span x-text="formatDate(call.created_at)"></span>
                                <span x-show="call.duration > 0" class="ml-2">
                                    • Duration: <span x-text="formatDuration(call.duration)"></span>
                                </span>
                            </div>
                            <div x-show="call.participants && call.participants.length > 0" class="text-sm text-gray-500 mt-1">
                                Participants: <span x-text="call.participants.map(p => p.name).join(', ')"></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <!-- Recordings -->
                        <div x-show="call.recordings && call.recordings.length > 0" class="relative">
                            <button @click="showRecordings(call)" class="text-purple-600 hover:text-purple-800">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            <span class="absolute -top-2 -right-2 bg-purple-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center" 
                                  x-text="call.recordings.length"></span>
                        </div>

                        <!-- Call Again -->
                        <button @click="callAgain(call)" class="text-green-600 hover:text-green-800" title="Call Again">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Recordings Modal -->
    <div x-show="showRecordingsModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Call Recordings</h3>
                <button @click="showRecordingsModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-3">
                <template x-for="recording in selectedRecordings" :key="recording.id">
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded">
                        <div>
                            <p class="font-medium" x-text="recording.file_name"></p>
                            <p class="text-sm text-gray-600">
                                <span x-text="formatDuration(recording.duration)"></span> • 
                                <span x-text="recording.file_size_formatted"></span>
                            </p>
                        </div>
                        <button @click="downloadRecording(recording)" class="text-blue-600 hover:text-blue-800">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function callHistory() {
    return {
        calls: [],
        loading: false,
        showRecordingsModal: false,
        selectedRecordings: [],

        async loadCallHistory() {
            this.loading = true;
            try {
                const response = await fetch('/api/calls/history', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                });
                const data = await response.json();
                this.calls = data.data || [];
            } catch (error) {
                console.error('Error loading call history:', error);
            } finally {
                this.loading = false;
            }
        },

        getCallTitle(call) {
            if (call.type === 'group' && call.group) {
                return call.group.name;
            } else if (call.type === 'one_to_one' && call.chat) {
                const otherUser = call.chat.user1_id === window.currentUserId ? call.chat.user2 : call.chat.user1;
                return otherUser ? otherUser.name : 'Unknown User';
            }
            return 'Unknown';
        },

        getStatusClass(status) {
            const classes = {
                'completed': 'bg-green-100 text-green-800',
                'ended': 'bg-green-100 text-green-800',
                'missed': 'bg-red-100 text-red-800',
                'declined': 'bg-red-100 text-red-800',
                'active': 'bg-blue-100 text-blue-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffTime = Math.abs(now - date);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays === 1) {
                return 'Today ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            } else if (diffDays === 2) {
                return 'Yesterday ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            } else if (diffDays <= 7) {
                return date.toLocaleDateString([], {weekday: 'short'}) + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            } else {
                return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            }
        },

        formatDuration(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;

            if (hours > 0) {
                return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }
            return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        },

        showRecordings(call) {
            this.selectedRecordings = call.recordings || [];
            this.showRecordingsModal = true;
        },

        async downloadRecording(recording) {
            try {
                const response = await fetch(`/api/recordings/${recording.id}/download`, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                });
                
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = recording.file_name;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                }
            } catch (error) {
                console.error('Error downloading recording:', error);
            }
        },

        async callAgain(call) {
            if (window.videoCallManager) {
                await window.videoCallManager.initiateCall(
                    call.type,
                    call.type === 'one_to_one' ? call.chat_id : call.group_id,
                    call.call_type
                );
            }
        }
    }
}
</script>