<!-- Call Buttons Component -->
<div class="flex space-x-2">
    <!-- Audio Call Button -->
    <button 
        onclick="initiateCall('{{ $type }}', {{ $targetId }}, 'audio')"
        class="bg-green-600 hover:bg-green-700 text-white p-2 rounded-full transition-colors duration-200"
        title="Audio Call"
    >
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
        </svg>
    </button>

    <!-- Video Call Button -->
    <button 
        onclick="initiateCall('{{ $type }}', {{ $targetId }}, 'video')"
        class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full transition-colors duration-200"
        title="Video Call"
    >
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M2 6a2 2 0 012-2h6l2 2h6a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14 8a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
    </button>
</div>

<script>
async function initiateCall(type, targetId, callType) {
    if (window.videoCallManager) {
        try {
            await window.videoCallManager.initiateCall(type, targetId, callType);
        } catch (error) {
            console.error('Failed to initiate call:', error);
            alert('Failed to start call. Please check your permissions and try again.');
        }
    } else {
        console.error('Video call manager not initialized');
        alert('Video calling is not available. Please refresh the page.');
    }
}
</script>