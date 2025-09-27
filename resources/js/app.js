import './bootstrap';
import VideoCallManager from './video-call';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Initialize video call manager
window.videoCallManager = new VideoCallManager();

Alpine.start();
