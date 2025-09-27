// Simple Emoji Implementation
(function() {
    'use strict';
    
    // Simple emoji shortcuts
    const shortcuts = {
        ':)': '😊', ':-)': '😊', ':(': '😞', ':-(': '😞',
        ':D': '😃', ':-D': '😃', ':P': '😛', ':-P': '😛',
        ';)': '😉', ';-)': '😉', ':o': '😮', ':-o': '😮',
        ':heart:': '❤️', ':fire:': '🔥', ':100:': '💯',
        ':thumbsup:': '👍', ':thumbsdown:': '👎', ':ok:': '👌',
        ':clap:': '👏', ':pray:': '🙏', ':star:': '⭐'
    };
    
    // Simple emoji picker data
    const emojis = {
        smileys: ['😀','😃','😄','😁','😆','😅','🤣','😂','🙂','🙃','😉','😊','😇','🥰','😍','🤩','😘','😗','☺️','😚','😙','🥲','😋','😛','😜','🤪','😝','🤑','🤗','🤭','🤫','🤔'],
        people: ['👶','🧒','👦','👧','🧑','👱','👨','🧔','👩','🧓','👴','👵','🙍','🙎','🙅','🙆','💁','🙋','🧏','🙇','🤦','🤷','👮','🕵️','💂','🥷','👷','🤴','👸','👳'],
        animals: ['🐶','🐱','🐭','🐹','🐰','🦊','🐻','🐼','🐨','🐯','🦁','🐮','🐷','🐽','🐸','🐵','🙈','🙉','🙊','🐒','🐔','🐧','🐦','🐤','🐣','🐥','🦆','🦅','🦉','🦇'],
        food: ['🍎','🍐','🍊','🍋','🍌','🍉','🍇','🍓','🫐','🍈','🍒','🍑','🥭','🍍','🥥','🥝','🍅','🍆','🥑','🥦','🥬','🥒','🌶️','🫑','🌽','🥕','🫒','🧄','🧅','🥔'],
        objects: ['⌚','📱','📲','💻','⌨️','🖥️','🖨️','🖱️','🖲️','🕹️','🗜️','💽','💾','💿','📀','📼','📷','📸','📹','🎥','📽️','🎞️','📞','☎️','📟','📠'],
        symbols: ['❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎','💔','❣️','💕','💞','💓','💗','💖','💘','💝','💟','☮️','✝️','☪️','🕉️','☸️','✡️','🔯','🕎','☯️','☦️','🛐']
    };
    
    let isPickerVisible = false;
    let recentEmojis = JSON.parse(localStorage.getItem('recentEmojis') || '[]');
    
    // Create emoji picker
    function createEmojiPicker() {
        if (document.getElementById('emojiPicker')) return;
        
        const picker = document.createElement('div');
        picker.id = 'emojiPicker';
        picker.className = 'emoji-picker';
        picker.style.cssText = `
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 320px;
            height: 350px;
            background: white;
            border: 1px solid #e4e6ea;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
            z-index: 1050;
            display: none;
            flex-direction: column;
        `;
        
        picker.innerHTML = `
            <div style="padding: 12px; border-bottom: 1px solid #e4e6ea; background: #f8f9fa; border-radius: 12px 12px 0 0;">
                <input type="text" id="emojiSearch" placeholder="Search emojis..." style="width: 100%; border: 1px solid #ddd; border-radius: 20px; padding: 8px 12px; font-size: 14px; outline: none;">
            </div>
            <div style="display: flex; padding: 8px; border-bottom: 1px solid #e4e6ea; background: #f8f9fa;">
                <div class="emoji-category active" data-category="recent" style="flex: 1; padding: 8px; text-align: center; cursor: pointer; border-radius: 6px; background: #1976d2; color: white;">🕒</div>
                <div class="emoji-category" data-category="smileys" style="flex: 1; padding: 8px; text-align: center; cursor: pointer; border-radius: 6px;">😀</div>
                <div class="emoji-category" data-category="people" style="flex: 1; padding: 8px; text-align: center; cursor: pointer; border-radius: 6px;">👤</div>
                <div class="emoji-category" data-category="animals" style="flex: 1; padding: 8px; text-align: center; cursor: pointer; border-radius: 6px;">🐶</div>
                <div class="emoji-category" data-category="food" style="flex: 1; padding: 8px; text-align: center; cursor: pointer; border-radius: 6px;">🍎</div>
                <div class="emoji-category" data-category="objects" style="flex: 1; padding: 8px; text-align: center; cursor: pointer; border-radius: 6px;">💡</div>
                <div class="emoji-category" data-category="symbols" style="flex: 1; padding: 8px; text-align: center; cursor: pointer; border-radius: 6px;">❤️</div>
            </div>
            <div id="emojiContent" style="flex: 1; overflow-y: auto; padding: 8px;"></div>
        `;
        
        document.body.appendChild(picker);
        
        // Add event listeners
        picker.addEventListener('click', function(e) {
            if (e.target.classList.contains('emoji-category')) {
                switchCategory(e.target.dataset.category);
            }
            if (e.target.classList.contains('emoji-item')) {
                selectEmoji(e.target.textContent);
            }
        });
        
        document.getElementById('emojiSearch').addEventListener('input', function(e) {
            searchEmojis(e.target.value);
        });
        
        // Close picker when clicking outside
        document.addEventListener('click', function(e) {
            if (!picker.contains(e.target) && e.target.id !== 'emojiBtn') {
                hideEmojiPicker();
            }
        });
        
        showCategory('recent');
    }
    
    function showCategory(category) {
        const content = document.getElementById('emojiContent');
        if (!content) return;
        
        let html = '';
        
        if (category === 'recent') {
            if (recentEmojis.length === 0) {
                html = '<div style="text-align: center; color: #666; padding: 20px;">No recent emojis</div>';
            } else {
                html = '<div style="display: grid; grid-template-columns: repeat(8, 1fr); gap: 4px;">';
                recentEmojis.slice(0, 32).forEach(emoji => {
                    html += `<div class="emoji-item" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; border-radius: 6px; font-size: 20px; transition: background 0.2s;" onmouseover="this.style.background='#f0f2f5'" onmouseout="this.style.background=''">${emoji}</div>`;
                });
                html += '</div>';
            }
        } else if (emojis[category]) {
            html = '<div style="display: grid; grid-template-columns: repeat(8, 1fr); gap: 4px;">';
            emojis[category].forEach(emoji => {
                html += `<div class="emoji-item" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; border-radius: 6px; font-size: 20px; transition: background 0.2s;" onmouseover="this.style.background='#f0f2f5'" onmouseout="this.style.background=''">${emoji}</div>`;
            });
            html += '</div>';
        }
        
        content.innerHTML = html;
    }
    
    function switchCategory(category) {
        // Update active category
        document.querySelectorAll('.emoji-category').forEach(cat => {
            cat.style.background = '';
            cat.style.color = '';
        });
        document.querySelector(`[data-category="${category}"]`).style.background = '#1976d2';
        document.querySelector(`[data-category="${category}"]`).style.color = 'white';
        
        showCategory(category);
        
        // Clear search
        const searchInput = document.getElementById('emojiSearch');
        if (searchInput) searchInput.value = '';
    }
    
    function searchEmojis(query) {
        if (!query.trim()) {
            showCategory('smileys');
            return;
        }
        
        const allEmojis = Object.values(emojis).flat();
        const filtered = allEmojis.filter(emoji => {
            // Simple search - you can enhance this
            return Object.keys(shortcuts).some(shortcut => 
                shortcut.includes(query.toLowerCase()) && shortcuts[shortcut] === emoji
            );
        });
        
        const content = document.getElementById('emojiContent');
        if (filtered.length === 0) {
            content.innerHTML = '<div style="text-align: center; color: #666; padding: 20px;">No emojis found</div>';
        } else {
            let html = '<div style="display: grid; grid-template-columns: repeat(8, 1fr); gap: 4px;">';
            filtered.forEach(emoji => {
                html += `<div class="emoji-item" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; border-radius: 6px; font-size: 20px; transition: background 0.2s;" onmouseover="this.style.background='#f0f2f5'" onmouseout="this.style.background=''">${emoji}</div>`;
            });
            html += '</div>';
            content.innerHTML = html;
        }
    }
    
    function selectEmoji(emoji) {
        // Add to recent
        recentEmojis = recentEmojis.filter(e => e !== emoji);
        recentEmojis.unshift(emoji);
        recentEmojis = recentEmojis.slice(0, 32);
        localStorage.setItem('recentEmojis', JSON.stringify(recentEmojis));
        
        // Insert into message input
        const messageInput = document.getElementById('messageText');
        if (messageInput) {
            const cursorPos = messageInput.selectionStart || messageInput.value.length;
            const textBefore = messageInput.value.substring(0, cursorPos);
            const textAfter = messageInput.value.substring(messageInput.selectionEnd || cursorPos);
            
            messageInput.value = textBefore + emoji + textAfter;
            messageInput.focus();
            
            const newPos = cursorPos + emoji.length;
            messageInput.setSelectionRange(newPos, newPos);
            
            messageInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }
    
    function showEmojiPicker() {
        const picker = document.getElementById('emojiPicker');
        if (picker) {
            picker.style.display = 'flex';
            isPickerVisible = true;
            
            const emojiBtn = document.getElementById('emojiBtn');
            if (emojiBtn) emojiBtn.classList.add('active');
        }
    }
    
    function hideEmojiPicker() {
        const picker = document.getElementById('emojiPicker');
        if (picker) {
            picker.style.display = 'none';
            isPickerVisible = false;
            
            const emojiBtn = document.getElementById('emojiBtn');
            if (emojiBtn) emojiBtn.classList.remove('active');
        }
    }
    
    // Process shortcuts
    function processShortcuts(input) {
        let text = input.value;
        let changed = false;
        
        Object.keys(shortcuts).forEach(shortcut => {
            if (text.includes(shortcut)) {
                text = text.replace(new RegExp(shortcut.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g'), shortcuts[shortcut]);
                changed = true;
            }
        });
        
        if (changed) {
            const cursorPos = input.selectionStart;
            input.value = text;
            input.setSelectionRange(cursorPos, cursorPos);
        }
    }
    
    // Global functions
    window.toggleEmojiPicker = function() {
        if (!document.getElementById('emojiPicker')) {
            createEmojiPicker();
        }
        
        if (isPickerVisible) {
            hideEmojiPicker();
        } else {
            showEmojiPicker();
        }
    };
    
    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        createEmojiPicker();
        
        // Add shortcut processing to message input
        const messageInput = document.getElementById('messageText');
        if (messageInput) {
            messageInput.addEventListener('input', function() {
                processShortcuts(this);
            });
            
            messageInput.addEventListener('keydown', function(e) {
                if (e.key === ' ' || e.key === 'Enter') {
                    setTimeout(() => processShortcuts(this), 10);
                }
            });
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === ';') {
                e.preventDefault();
                window.toggleEmojiPicker();
            }
            
            if (e.key === 'Escape' && isPickerVisible) {
                hideEmojiPicker();
            }
        });
        
        console.log('✅ Simple emoji features initialized');
    });
    
})();