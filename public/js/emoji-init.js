// Emoji Features Initialization Script
(function() {
    'use strict';
    
    console.log('🚀 Initializing Emoji Features...');
    
    // Ensure DOM is ready
    function initializeEmojis() {
        // Initialize Emoji Picker
        if (typeof EmojiPicker !== 'undefined' && !window.emojiPicker) {
            try {
                window.emojiPicker = new EmojiPicker();
                console.log('✅ Emoji Picker initialized');
            } catch (error) {
                console.error('❌ Failed to initialize Emoji Picker:', error);
            }
        }
        
        // Initialize Emoji Shortcuts
        if (typeof EmojiShortcuts !== 'undefined' && !window.emojiShortcuts) {
            try {
                window.emojiShortcuts = new EmojiShortcuts();
                console.log('✅ Emoji Shortcuts initialized');
            } catch (error) {
                console.error('❌ Failed to initialize Emoji Shortcuts:', error);
            }
        }
        
        // Ensure toggle function exists
        if (typeof window.toggleEmojiPicker === 'undefined') {
            window.toggleEmojiPicker = function() {
                if (window.emojiPicker) {
                    window.emojiPicker.toggle();
                    
                    // Toggle button state
                    const emojiBtn = document.getElementById('emojiBtn');
                    if (emojiBtn) {
                        emojiBtn.classList.toggle('active', window.emojiPicker.isVisible);
                    }
                } else {
                    console.error('❌ EmojiPicker instance not found');
                    // Try to create it
                    if (typeof EmojiPicker !== 'undefined') {
                        window.emojiPicker = new EmojiPicker();
                        window.emojiPicker.toggle();
                    }
                }
            };
            console.log('✅ Toggle function created');
        }
        
        // Add enhanced shortcut processing
        enhanceShortcutProcessing();
        
        // Add visual feedback for emoji features
        addVisualFeedback();
        
        console.log('🎉 Emoji features initialization complete!');
    }
    
    function enhanceShortcutProcessing() {
        // Enhanced shortcut processing for better user experience
        const messageInput = document.getElementById('messageText');
        if (messageInput) {
            // Add placeholder hint
            const originalPlaceholder = messageInput.placeholder;
            messageInput.placeholder = originalPlaceholder + ' (Try :) or :heart: or Ctrl+; for emojis)';
            
            // Add real-time shortcut conversion
            let shortcutTimeout;
            messageInput.addEventListener('input', function(e) {
                clearTimeout(shortcutTimeout);
                shortcutTimeout = setTimeout(() => {
                    if (window.emojiShortcuts) {
                        window.emojiShortcuts.processShortcuts(this);
                    }
                }, 300); // Debounce for better performance
            });
            
            // Add space/enter trigger for immediate conversion
            messageInput.addEventListener('keydown', function(e) {
                if (e.key === ' ' || e.key === 'Enter') {
                    setTimeout(() => {
                        if (window.emojiShortcuts) {
                            window.emojiShortcuts.processShortcuts(this);
                        }
                    }, 10);
                }
            });
            
            console.log('✅ Enhanced shortcut processing added');
        }
    }
    
    function addVisualFeedback() {
        // Add visual feedback for emoji button
        const emojiBtn = document.getElementById('emojiBtn');
        if (emojiBtn) {
            // Add hover effect
            emojiBtn.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.1)';
                this.style.transition = 'transform 0.2s ease';
            });
            
            emojiBtn.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
            
            // Add click feedback
            emojiBtn.addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 100);
            });
            
            console.log('✅ Visual feedback added');
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeEmojis);
    } else {
        // DOM is already ready
        initializeEmojis();
    }
    
    // Fallback initialization after a delay
    setTimeout(initializeEmojis, 1000);
    
    // Export for debugging
    window.emojiInit = {
        reinitialize: initializeEmojis,
        checkStatus: function() {
            console.log('📊 Emoji Features Status:');
            console.log('- EmojiPicker class:', typeof EmojiPicker !== 'undefined' ? '✅' : '❌');
            console.log('- EmojiShortcuts class:', typeof EmojiShortcuts !== 'undefined' ? '✅' : '❌');
            console.log('- EmojiPicker instance:', typeof window.emojiPicker !== 'undefined' ? '✅' : '❌');
            console.log('- EmojiShortcuts instance:', typeof window.emojiShortcuts !== 'undefined' ? '✅' : '❌');
            console.log('- Toggle function:', typeof window.toggleEmojiPicker !== 'undefined' ? '✅' : '❌');
            console.log('- Message input:', document.getElementById('messageText') ? '✅' : '❌');
            console.log('- Emoji button:', document.getElementById('emojiBtn') ? '✅' : '❌');
            console.log('- Emoji picker element:', document.getElementById('emojiPicker') ? '✅' : '❌');
        }
    };
    
})();