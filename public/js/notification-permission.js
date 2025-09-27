// Notification Permission Manager
class NotificationPermissionManager {
    constructor() {
        this.reminderInterval = null;
        this.reminderCount = 0;
        this.maxReminders = 5; // Stop after 5 reminders
        this.reminderIntervalMs = 60000; // 1 minute
        this.init();
    }

    init() {
        console.log('NotificationPermissionManager init called');
        
        // Check permission status on load
        const hasPermission = this.checkPermissionStatus();
        console.log('Has notification permission:', hasPermission);
        
        // Start reminder if needed
        const shouldRemind = this.shouldShowReminder();
        console.log('Should show reminder:', shouldRemind);
        
        if (shouldRemind) {
            console.log('Starting reminder system...');
            // Show first reminder after 2 seconds
            setTimeout(() => {
                this.showReminderModal();
            }, 2000);
            this.startReminder();
        }
    }

    checkPermissionStatus() {
        if (!('Notification' in window)) {
            console.log('This browser does not support notifications');
            return false;
        }

        const permission = Notification.permission;
        console.log('Notification permission:', permission);
        
        return permission === 'granted';
    }

    shouldShowReminder() {
        if (!('Notification' in window)) return false;
        
        const permission = Notification.permission;
        const dismissed = localStorage.getItem('notificationReminderDismissed');
        const reminderCount = parseInt(localStorage.getItem('notificationReminderCount') || '0');
        
        return permission === 'default' && 
               !dismissed && 
               reminderCount < this.maxReminders;
    }

    startReminder() {
        // Set interval for recurring reminders (every minute)
        this.reminderInterval = setInterval(() => {
            console.log('Checking if should show reminder...');
            if (this.shouldShowReminder()) {
                console.log('Showing reminder modal');
                this.showReminderModal();
            } else {
                console.log('Stopping reminder');
                this.stopReminder();
            }
        }, this.reminderIntervalMs);
    }

    stopReminder() {
        if (this.reminderInterval) {
            clearInterval(this.reminderInterval);
            this.reminderInterval = null;
        }
    }

    showReminderModal() {
        // Increment reminder count
        this.reminderCount = parseInt(localStorage.getItem('notificationReminderCount') || '0') + 1;
        localStorage.setItem('notificationReminderCount', this.reminderCount.toString());

        // Create modal if it doesn't exist
        if (!document.getElementById('notificationPermissionModal')) {
            this.createReminderModal();
        }

        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('notificationPermissionModal'));
        modal.show();
    }

    createReminderModal() {
        const modalHtml = `
            <div class="modal fade" id="notificationPermissionModal" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-bell me-2"></i>Enable Notifications
                            </h5>
                        </div>
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-bell fa-3x text-primary mb-3"></i>
                                <h6>Don't miss important messages!</h6>
                                <p class="text-muted">
                                    Enable notifications to receive alerts when you get new messages, 
                                    even when the chat is not active.
                                </p>
                            </div>
                            <div class="alert alert-info">
                                <small>
                                    <i class="fas fa-info-circle me-1"></i>
                                    You can change this setting anytime in your browser settings.
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-primary" onclick="notificationManager.requestPermission()">
                                <i class="fas fa-check me-1"></i>Enable Notifications
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="notificationManager.dismissReminder()">
                                <i class="fas fa-times me-1"></i>Not Now
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="notificationManager.dismissPermanently()">
                                Don't Ask Again
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    async requestPermission() {
        try {
            const permission = await Notification.requestPermission();
            
            if (permission === 'granted') {
                this.showSuccessMessage();
                this.stopReminder();
                
                // Test notification
                setTimeout(() => {
                    new Notification('Office Chat', {
                        body: 'Notifications are now enabled! 🎉',
                        icon: '/favicon.ico'
                    });
                }, 500);
            } else {
                this.showErrorMessage();
            }
        } catch (error) {
            console.error('Error requesting notification permission:', error);
            this.showErrorMessage();
        }

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('notificationPermissionModal'));
        if (modal) modal.hide();
    }

    dismissReminder() {
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('notificationPermissionModal'));
        if (modal) modal.hide();

        // If we've shown too many reminders, stop
        if (this.reminderCount >= this.maxReminders) {
            this.dismissPermanently();
        }
    }

    dismissPermanently() {
        localStorage.setItem('notificationReminderDismissed', 'true');
        this.stopReminder();
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('notificationPermissionModal'));
        if (modal) modal.hide();

        // Show info message
        this.showInfoMessage('You can enable notifications anytime from your browser settings.');
    }

    showSuccessMessage() {
        this.showToast('Notifications enabled successfully!', 'success');
    }

    showErrorMessage() {
        this.showToast('Failed to enable notifications. Please check your browser settings.', 'danger');
    }

    showInfoMessage(message) {
        this.showToast(message, 'info');
    }

    showToast(message, type = 'info') {
        // Create toast container if it doesn't exist
        if (!document.getElementById('toastContainer')) {
            const toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }

        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        document.getElementById('toastContainer').insertAdjacentHTML('beforeend', toastHtml);
        
        const toast = new bootstrap.Toast(document.getElementById(toastId));
        toast.show();

        // Remove toast element after it's hidden
        document.getElementById(toastId).addEventListener('hidden.bs.toast', function() {
            this.remove();
        });
    }

    // Reset reminder settings (for testing)
    resetSettings() {
        localStorage.removeItem('notificationReminderDismissed');
        localStorage.removeItem('notificationReminderCount');
        this.reminderCount = 0;
        console.log('Notification reminder settings reset');
    }
}

// Initialize the notification manager when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing notification manager...');
    window.notificationManager = new NotificationPermissionManager();
    console.log('Notification manager initialized');
});