class EmojiPicker {
    constructor() {
        this.isVisible = false;
        this.currentCategory = 'recent';
        this.recentEmojis = JSON.parse(localStorage.getItem('recentEmojis') || '[]');
        this.frequentEmojis = JSON.parse(localStorage.getItem('frequentEmojis') || '{}');
        
        this.emojis = {
            recent: [],
            smileys: [
                '😀', '😃', '😄', '😁', '😆', '😅', '🤣', '😂', '🙂', '🙃', '😉', '😊', '😇', '🥰', '😍', '🤩',
                '😘', '😗', '☺️', '😚', '😙', '🥲', '😋', '😛', '😜', '🤪', '😝', '🤑', '🤗', '🤭', '🤫', '🤔',
                '🤐', '🤨', '😐', '😑', '😶', '😏', '😒', '🙄', '😬', '🤥', '😔', '😪', '🤤', '😴', '😷', '🤒',
                '🤕', '🤢', '🤮', '🤧', '🥵', '🥶', '🥴', '😵', '🤯', '🤠', '🥳', '🥸', '😎', '🤓', '🧐', '😕',
                '😟', '🙁', '☹️', '😮', '😯', '😲', '😳', '🥺', '😦', '😧', '😨', '😰', '😥', '😢', '😭', '😱',
                '😖', '😣', '😞', '😓', '😩', '😫', '🥱', '😤', '😡', '😠', '🤬', '😈', '👿', '💀', '☠️', '💩',
                '🤡', '👻', '👽', '🤖', '😺', '😸', '😹', '😻', '😼', '😽', '🙀', '😿', '😾'
            ],
            people: [
                '👶', '🧒', '👦', '👧', '🧑', '👱', '👨', '🧔', '👩', '🧓', '👴', '👵', '🙍', '🙎', '🙅', '🙆',
                '💁', '🙋', '🧏', '🙇', '🤦', '🤷', '👮', '🕵️', '💂', '🥷', '👷', '🤴', '👸', '👳', '👲', '🧕',
                '🤵', '👰', '🤰', '🤱', '👼', '🎅', '🤶', '🦸', '🦹', '🧙', '🧚', '🧛', '🧜', '🧝', '🧞', '🧟',
                '💆', '💇', '🚶', '🧍', '🧎', '🏃', '💃', '🕺', '🕴️', '👯', '🧖', '🧗', '🤺', '🏇', '⛷️', '🏂',
                '🏌️', '🏄', '🚣', '🏊', '⛹️', '🏋️', '🚴', '🚵', '🤸', '🤼', '🤽', '🤾', '🤹', '🧘', '🛀', '🛌'
            ],
            animals: [
                '🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨', '🐯', '🦁', '🐮', '🐷', '🐽', '🐸', '🐵',
                '🙈', '🙉', '🙊', '🐒', '🐔', '🐧', '🐦', '🐤', '🐣', '🐥', '🦆', '🦅', '🦉', '🦇', '🐺', '🐗',
                '🐴', '🦄', '🐝', '🐛', '🦋', '🐌', '🐞', '🐜', '🦟', '🦗', '🕷️', '🕸️', '🦂', '🐢', '🐍', '🦎',
                '🦖', '🦕', '🐙', '🦑', '🦐', '🦞', '🦀', '🐡', '🐠', '🐟', '🐬', '🐳', '🐋', '🦈', '🐊', '🐅',
                '🐆', '🦓', '🦍', '🦧', '🐘', '🦛', '🦏', '🐪', '🐫', '🦒', '🦘', '🐃', '🐂', '🐄', '🐎', '🐖'
            ],
            food: [
                '🍎', '🍐', '🍊', '🍋', '🍌', '🍉', '🍇', '🍓', '🫐', '🍈', '🍒', '🍑', '🥭', '🍍', '🥥', '🥝',
                '🍅', '🍆', '🥑', '🥦', '🥬', '🥒', '🌶️', '🫑', '🌽', '🥕', '🫒', '🧄', '🧅', '🥔', '🍠', '🥐',
                '🥯', '🍞', '🥖', '🥨', '🧀', '🥚', '🍳', '🧈', '🥞', '🧇', '🥓', '🥩', '🍗', '🍖', '🦴', '🌭',
                '🍔', '🍟', '🍕', '🫓', '🥪', '🥙', '🧆', '🌮', '🌯', '🫔', '🥗', '🥘', '🫕', '🍝', '🍜', '🍲',
                '🍛', '🍣', '🍱', '🥟', '🦪', '🍤', '🍙', '🍚', '🍘', '🍥', '🥠', '🥮', '🍢', '🍡', '🍧', '🍨'
            ],
            activities: [
                '⚽', '🏀', '🏈', '⚾', '🥎', '🎾', '🏐', '🏉', '🥏', '🎱', '🪀', '🏓', '🏸', '🏒', '🏑', '🥍',
                '🏏', '🪃', '🥅', '⛳', '🪁', '🏹', '🎣', '🤿', '🥊', '🥋', '🎽', '🛹', '🛷', '⛸️', '🥌', '🎿',
                '⛷️', '🏂', '🪂', '🏋️', '🤼', '🤸', '⛹️', '🤺', '🤾', '🏌️', '🏇', '🧘', '🏄', '🏊', '🤽', '🚣',
                '🧗', '🚵', '🚴', '🏆', '🥇', '🥈', '🥉', '🏅', '🎖️', '🏵️', '🎗️', '🎫', '🎟️', '🎪', '🤹', '🎭',
                '🩰', '🎨', '🎬', '🎤', '🎧', '🎼', '🎵', '🎶', '🥁', '🪘', '🎹', '🎷', '🎺', '🪗', '🎸', '🪕'
            ],
            travel: [
                '🚗', '🚕', '🚙', '🚌', '🚎', '🏎️', '🚓', '🚑', '🚒', '🚐', '🛻', '🚚', '🚛', '🚜', '🏍️', '🛵',
                '🚲', '🛴', '🛹', '🛼', '🚁', '🛸', '✈️', '🛩️', '🪂', '💺', '🚀', '🛰️', '🚉', '🚊', '🚝', '🚞',
                '🚋', '🚃', '🚂', '🚄', '🚅', '🚆', '🚇', '🚈', '🚉', '🚐', '🚍', '🚘', '🚖', '🚡', '🚠', '🚟',
                '⛴️', '🛥️', '🚤', '⛵', '🛶', '🚣', '🛳️', '⚓', '🪝', '⛽', '🚧', '🚨', '🚥', '🚦', '🛑', '🚏',
                '🗺️', '🗿', '🗽', '🗼', '🏰', '🏯', '🏟️', '🎡', '🎢', '🎠', '⛲', '⛱️', '🏖️', '🏝️', '🏜️', '🌋'
            ],
            objects: [
                '⌚', '📱', '📲', '💻', '⌨️', '🖥️', '🖨️', '🖱️', '🖲️', '🕹️', '🗜️', '💽', '💾', '💿', '📀', '📼',
                '📷', '📸', '📹', '🎥', '📽️', '🎞️', '📞', '☎️', '📟', '📠', '📺', '📻', '🎙️', '🎚️', '🎛️', '🧭',
                '⏱️', '⏲️', '⏰', '🕰️', '⌛', '⏳', '📡', '🔋', '🔌', '💡', '🔦', '🕯️', '🪔', '🧯', '🛢️', '💸',
                '💵', '💴', '💶', '💷', '🪙', '💰', '💳', '💎', '⚖️', '🪜', '🧰', '🔧', '🔨', '⚒️', '🛠️', '⛏️',
                '🪓', '🪚', '🔩', '⚙️', '🪤', '🧱', '⛓️', '🧲', '🔫', '💣', '🧨', '🪓', '🔪', '🗡️', '⚔️', '🛡️'
            ],
            symbols: [
                '❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔', '❣️', '💕', '💞', '💓', '💗', '💖',
                '💘', '💝', '💟', '☮️', '✝️', '☪️', '🕉️', '☸️', '✡️', '🔯', '🕎', '☯️', '☦️', '🛐', '⛎', '♈',
                '♉', '♊', '♋', '♌', '♍', '♎', '♏', '♐', '♑', '♒', '♓', '🆔', '⚛️', '🉑', '☢️', '☣️', '📴', '📳',
                '🈶', '🈚', '🈸', '🈺', '🈷️', '✴️', '🆚', '💮', '🉐', '㊙️', '㊗️', '🈴', '🈵', '🈹', '🈲', '🅰️',
                '🅱️', '🆎', '🆑', '🅾️', '🆘', '❌', '⭕', '🛑', '⛔', '📛', '🚫', '💯', '💢', '♨️', '🚷', '🚯'
            ],
            flags: [
                '🏁', '🚩', '🎌', '🏴', '🏳️', '🏳️‍🌈', '🏳️‍⚧️', '🏴‍☠️', '🇦🇫', '🇦🇽', '🇦🇱', '🇩🇿', '🇦🇸', '🇦🇩', '🇦🇴', '🇦🇮',
                '🇦🇶', '🇦🇬', '🇦🇷', '🇦🇲', '🇦🇼', '🇦🇺', '🇦🇹', '🇦🇿', '🇧🇸', '🇧🇭', '🇧🇩', '🇧🇧', '🇧🇾', '🇧🇪', '🇧🇿', '🇧🇯',
                '🇧🇲', '🇧🇹', '🇧🇴', '🇧🇦', '🇧🇼', '🇧🇷', '🇮🇴', '🇻🇬', '🇧🇳', '🇧🇬', '🇧🇫', '🇧🇮', '🇰🇭', '🇨🇲', '🇨🇦', '🇮🇨',
                '🇨🇻', '🇧🇶', '🇰🇾', '🇨🇫', '🇹🇩', '🇨🇱', '🇨🇳', '🇨🇽', '🇨🇨', '🇨🇴', '🇰🇲', '🇨🇬', '🇨🇩', '🇨🇰', '🇨🇷', '🇨🇮'
            ]
        };
        
        this.categoryIcons = {
            recent: '🕒',
            smileys: '😀',
            people: '👤',
            animals: '🐶',
            food: '🍎',
            activities: '⚽',
            travel: '🚗',
            objects: '💡',
            symbols: '❤️',
            flags: '🏁'
        };
        
        this.init();
    }
    
    init() {
        this.createEmojiPicker();
        this.bindEvents();
        this.updateRecentEmojis();
    }
    
    createEmojiPicker() {
        const picker = document.createElement('div');
        picker.className = 'emoji-picker';
        picker.id = 'emojiPicker';
        
        picker.innerHTML = `
            <div class="emoji-picker-header">
                <input type="text" class="emoji-search" placeholder="Search emojis..." id="emojiSearch">
            </div>
            <div class="emoji-categories">
                ${Object.keys(this.categoryIcons).map(category => 
                    `<div class="emoji-category ${category === 'recent' ? 'active' : ''}" data-category="${category}">
                        ${this.categoryIcons[category]}
                    </div>`
                ).join('')}
            </div>
            <div class="emoji-content" id="emojiContent">
                ${this.renderEmojiContent()}
            </div>
        `;
        
        document.body.appendChild(picker);
    }
    
    renderEmojiContent() {
        let content = '';
        
        if (this.currentCategory === 'recent') {
            content += this.renderRecentEmojis();
        }
        
        const categoryEmojis = this.emojis[this.currentCategory] || [];
        if (categoryEmojis.length > 0) {
            const categoryName = this.currentCategory.charAt(0).toUpperCase() + this.currentCategory.slice(1);
            content += `
                <div class="emoji-section">
                    <div class="emoji-section-title">${categoryName}</div>
                    <div class="emoji-grid">
                        ${categoryEmojis.map(emoji => 
                            `<div class="emoji-item" data-emoji="${emoji}">${emoji}</div>`
                        ).join('')}
                    </div>
                </div>
            `;
        }
        
        return content;
    }
    
    renderRecentEmojis() {
        if (this.recentEmojis.length === 0) {
            return '<div class="emoji-section"><div class="emoji-section-title">No recent emojis</div></div>';
        }
        
        const frequentlyUsed = Object.entries(this.frequentEmojis)
            .sort(([,a], [,b]) => b - a)
            .slice(0, 16)
            .map(([emoji]) => emoji);
        
        let content = '';
        
        if (frequentlyUsed.length > 0) {
            content += `
                <div class="emoji-section emoji-recent">
                    <div class="emoji-section-title">Frequently Used</div>
                    <div class="emoji-frequent">
                        ${frequentlyUsed.map(emoji => 
                            `<div class="emoji-item" data-emoji="${emoji}">${emoji}</div>`
                        ).join('')}
                    </div>
                </div>
            `;
        }
        
        content += `
            <div class="emoji-section">
                <div class="emoji-section-title">Recently Used</div>
                <div class="emoji-grid">
                    ${this.recentEmojis.slice(0, 24).map(emoji => 
                        `<div class="emoji-item" data-emoji="${emoji}">${emoji}</div>`
                    ).join('')}
                </div>
            </div>
        `;
        
        return content;
    }
    
    bindEvents() {
        // Category switching
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('emoji-category')) {
                this.switchCategory(e.target.dataset.category);
            }
            
            if (e.target.classList.contains('emoji-item')) {
                this.selectEmoji(e.target.dataset.emoji);
            }
        });
        
        // Search functionality
        const searchInput = document.getElementById('emojiSearch');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.searchEmojis(e.target.value);
            });
        }
        
        // Close picker when clicking outside
        document.addEventListener('click', (e) => {
            const picker = document.getElementById('emojiPicker');
            const emojiBtn = document.getElementById('emojiBtn');
            
            if (picker && !picker.contains(e.target) && e.target !== emojiBtn) {
                this.hide();
            }
        });
    }
    
    switchCategory(category) {
        this.currentCategory = category;
        
        // Update active category
        document.querySelectorAll('.emoji-category').forEach(cat => {
            cat.classList.remove('active');
        });
        document.querySelector(`[data-category="${category}"]`).classList.add('active');
        
        // Update content
        document.getElementById('emojiContent').innerHTML = this.renderEmojiContent();
        
        // Clear search
        const searchInput = document.getElementById('emojiSearch');
        if (searchInput) {
            searchInput.value = '';
        }
    }
    
    searchEmojis(query) {
        if (!query.trim()) {
            document.getElementById('emojiContent').innerHTML = this.renderEmojiContent();
            return;
        }
        
        const allEmojis = Object.values(this.emojis).flat();
        const filteredEmojis = allEmojis.filter(emoji => {
            // Simple search - you can enhance this with emoji names/keywords
            return emoji.includes(query) || this.getEmojiKeywords(emoji).some(keyword => 
                keyword.toLowerCase().includes(query.toLowerCase())
            );
        });
        
        const content = `
            <div class="emoji-section">
                <div class="emoji-section-title">Search Results (${filteredEmojis.length})</div>
                <div class="emoji-grid">
                    ${filteredEmojis.map(emoji => 
                        `<div class="emoji-item" data-emoji="${emoji}">${emoji}</div>`
                    ).join('')}
                </div>
            </div>
        `;
        
        document.getElementById('emojiContent').innerHTML = content;
    }
    
    getEmojiKeywords(emoji) {
        // Enhanced emoji keywords for better search
        const keywords = {
            '😀': ['happy', 'smile', 'joy', 'grin'],
            '😂': ['laugh', 'lol', 'funny', 'tears'],
            '😢': ['sad', 'cry', 'tear', 'upset'],
            '😍': ['love', 'heart', 'eyes', 'crush'],
            '😘': ['kiss', 'love', 'blow'],
            '😊': ['happy', 'smile', 'blush'],
            '😎': ['cool', 'sunglasses', 'awesome'],
            '😴': ['sleep', 'tired', 'zzz'],
            '😷': ['sick', 'mask', 'ill'],
            '🤔': ['think', 'hmm', 'wonder'],
            '🙄': ['eye', 'roll', 'whatever'],
            '😤': ['angry', 'mad', 'huff'],
            '🥺': ['puppy', 'eyes', 'please'],
            '❤️': ['love', 'heart', 'red'],
            '💙': ['love', 'heart', 'blue'],
            '💚': ['love', 'heart', 'green'],
            '💛': ['love', 'heart', 'yellow'],
            '💜': ['love', 'heart', 'purple'],
            '👍': ['thumbs', 'up', 'good', 'like', 'yes'],
            '👎': ['thumbs', 'down', 'bad', 'dislike', 'no'],
            '👌': ['ok', 'okay', 'perfect', 'good'],
            '✌️': ['peace', 'victory', 'two'],
            '🤝': ['handshake', 'deal', 'agreement'],
            '👏': ['clap', 'applause', 'good', 'job'],
            '🙏': ['pray', 'please', 'thanks'],
            '🎉': ['party', 'celebration', 'confetti'],
            '🎊': ['party', 'celebration', 'confetti'],
            '🔥': ['fire', 'hot', 'flame', 'lit'],
            '💯': ['hundred', 'perfect', 'score', 'full'],
            '⚡': ['lightning', 'fast', 'energy'],
            '💪': ['strong', 'muscle', 'power'],
            '🚀': ['rocket', 'fast', 'launch'],
            '⭐': ['star', 'favorite', 'good'],
            '✨': ['sparkle', 'magic', 'shine'],
            '💎': ['diamond', 'precious', 'gem'],
            '🏆': ['trophy', 'winner', 'champion'],
            '🎯': ['target', 'goal', 'bullseye'],
            '📱': ['phone', 'mobile', 'cell'],
            '💻': ['computer', 'laptop', 'work'],
            '🎵': ['music', 'note', 'song'],
            '🍕': ['pizza', 'food', 'italian'],
            '🍔': ['burger', 'food', 'fast'],
            '☕': ['coffee', 'drink', 'morning'],
            '🍺': ['beer', 'drink', 'alcohol'],
            '🎂': ['cake', 'birthday', 'celebration'],
            '🌟': ['star', 'shine', 'bright'],
            '🌈': ['rainbow', 'colorful', 'pride'],
            '☀️': ['sun', 'sunny', 'bright'],
            '🌙': ['moon', 'night', 'sleep'],
            '⚽': ['soccer', 'football', 'sport'],
            '🏀': ['basketball', 'sport', 'ball'],
            '🎮': ['game', 'gaming', 'controller'],
            '📚': ['book', 'study', 'read'],
            '✈️': ['plane', 'travel', 'flight'],
            '🚗': ['car', 'drive', 'vehicle'],
            '🏠': ['home', 'house', 'building'],
            '🌍': ['world', 'earth', 'globe'],
            '🔔': ['bell', 'notification', 'ring'],
            '🔕': ['mute', 'silent', 'quiet'],
            '📢': ['megaphone', 'announcement', 'loud'],
            '💰': ['money', 'cash', 'rich'],
            '💸': ['money', 'flying', 'spend'],
            '🎁': ['gift', 'present', 'surprise'],
            '🎈': ['balloon', 'party', 'celebration'],
            '🌺': ['flower', 'beautiful', 'nature'],
            '🌸': ['cherry', 'blossom', 'spring'],
            '🌹': ['rose', 'love', 'romantic'],
            '🍀': ['clover', 'luck', 'irish'],
            '🌿': ['leaf', 'nature', 'green'],
            '🦄': ['unicorn', 'magic', 'fantasy'],
            '🐶': ['dog', 'puppy', 'pet'],
            '🐱': ['cat', 'kitten', 'pet'],
            '🦊': ['fox', 'clever', 'orange'],
            '🐻': ['bear', 'cute', 'teddy'],
            '🐼': ['panda', 'cute', 'china'],
            '🦁': ['lion', 'king', 'strong'],
            '🐯': ['tiger', 'strong', 'stripes'],
            '🦋': ['butterfly', 'beautiful', 'transform'],
            '🐝': ['bee', 'honey', 'busy'],
            '🌊': ['wave', 'ocean', 'water'],
            '🏔️': ['mountain', 'high', 'peak'],
            '🏖️': ['beach', 'sand', 'vacation'],
            '🏝️': ['island', 'tropical', 'paradise'],
            '🎪': ['circus', 'tent', 'fun'],
            '🎭': ['theater', 'drama', 'masks'],
            '🎨': ['art', 'paint', 'creative'],
            '🎬': ['movie', 'film', 'cinema'],
            '📷': ['camera', 'photo', 'picture'],
            '🎤': ['microphone', 'sing', 'karaoke'],
            '🎧': ['headphones', 'music', 'listen'],
            '🎸': ['guitar', 'music', 'rock'],
            '🥳': ['party', 'celebration', 'hat'],
            '🤩': ['star', 'struck', 'amazed'],
            '🥰': ['love', 'hearts', 'adore'],
            '😋': ['yummy', 'delicious', 'tongue'],
            '🤪': ['crazy', 'wild', 'fun'],
            '🤭': ['giggle', 'oops', 'hand'],
            '🤫': ['shh', 'quiet', 'secret'],
            '🤗': ['hug', 'embrace', 'warm'],
            '🤤': ['drool', 'hungry', 'want'],
            '🥱': ['yawn', 'tired', 'sleepy'],
            '🤯': ['mind', 'blown', 'explode'],
            '🥵': ['hot', 'sweat', 'heat'],
            '🥶': ['cold', 'freeze', 'shiver'],
            '🤠': ['cowboy', 'hat', 'western'],
            '🤡': ['clown', 'funny', 'joke'],
            '👻': ['ghost', 'boo', 'spooky'],
            '👽': ['alien', 'ufo', 'space'],
            '🤖': ['robot', 'ai', 'tech'],
            '💀': ['skull', 'death', 'spooky'],
            '☠️': ['skull', 'danger', 'pirate'],
            '💩': ['poop', 'shit', 'funny'],
            '🙈': ['monkey', 'see', 'no', 'evil'],
            '🙉': ['monkey', 'hear', 'no', 'evil'],
            '🙊': ['monkey', 'speak', 'no', 'evil']
        };
        
        return keywords[emoji] || [];
    }
    
    selectEmoji(emoji) {
        // Add to recent emojis
        this.addToRecent(emoji);
        
        // Add to frequent emojis
        this.addToFrequent(emoji);
        
        // Insert emoji into message input
        const messageInput = document.getElementById('messageText');
        if (messageInput) {
            const cursorPos = messageInput.selectionStart || messageInput.value.length;
            const textBefore = messageInput.value.substring(0, cursorPos);
            const textAfter = messageInput.value.substring(messageInput.selectionEnd || cursorPos);
            
            messageInput.value = textBefore + emoji + textAfter;
            messageInput.focus();
            
            // Set cursor position after emoji
            const newPos = cursorPos + emoji.length;
            messageInput.setSelectionRange(newPos, newPos);
            
            // Trigger input event for any listeners
            messageInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
        
        // Don't hide picker immediately - let user select multiple emojis
        // this.hide();
    }
    
    addToRecent(emoji) {
        // Remove if already exists
        this.recentEmojis = this.recentEmojis.filter(e => e !== emoji);
        
        // Add to beginning
        this.recentEmojis.unshift(emoji);
        
        // Keep only last 50
        this.recentEmojis = this.recentEmojis.slice(0, 50);
        
        // Save to localStorage
        localStorage.setItem('recentEmojis', JSON.stringify(this.recentEmojis));
    }
    
    addToFrequent(emoji) {
        this.frequentEmojis[emoji] = (this.frequentEmojis[emoji] || 0) + 1;
        localStorage.setItem('frequentEmojis', JSON.stringify(this.frequentEmojis));
    }
    
    updateRecentEmojis() {
        this.emojis.recent = this.recentEmojis;
    }
    
    show() {
        const picker = document.getElementById('emojiPicker');
        if (picker) {
            picker.classList.add('show');
            this.isVisible = true;
            
            // Update button state
            const emojiBtn = document.getElementById('emojiBtn');
            if (emojiBtn) {
                emojiBtn.classList.add('active');
            }
            
            // Focus search input
            const searchInput = document.getElementById('emojiSearch');
            if (searchInput) {
                setTimeout(() => searchInput.focus(), 100);
            }
        }
    }
    
    hide() {
        const picker = document.getElementById('emojiPicker');
        if (picker) {
            picker.classList.remove('show');
            this.isVisible = false;
            
            // Update button state
            const emojiBtn = document.getElementById('emojiBtn');
            if (emojiBtn) {
                emojiBtn.classList.remove('active');
            }
        }
    }
    
    toggle() {
        if (this.isVisible) {
            this.hide();
        } else {
            this.show();
        }
    }
}

// Initialize emoji picker when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.emojiPicker = new EmojiPicker();
});

// Function to toggle emoji picker
function toggleEmojiPicker() {
    if (window.emojiPicker) {
        window.emojiPicker.toggle();
        
        // Toggle button state
        const emojiBtn = document.getElementById('emojiBtn');
        if (emojiBtn) {
            emojiBtn.classList.toggle('active', window.emojiPicker.isVisible);
        }
    }
}

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + ; to toggle emoji picker
    if ((e.ctrlKey || e.metaKey) && e.key === ';') {
        e.preventDefault();
        toggleEmojiPicker();
    }
    
    // Escape to close emoji picker
    if (e.key === 'Escape' && window.emojiPicker && window.emojiPicker.isVisible) {
        window.emojiPicker.hide();
        const emojiBtn = document.getElementById('emojiBtn');
        if (emojiBtn) {
            emojiBtn.classList.remove('active');
        }
    }
});