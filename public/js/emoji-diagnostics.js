// Emoji Features Diagnostic and Fix Script
class EmojiDiagnostics {
    constructor() {
        this.issues = [];
        this.fixes = [];
        this.init();
    }
    
    init() {
        console.log('🔍 Starting Emoji Features Diagnostics...');
        this.checkEmojiPicker();
        this.checkEmojiShortcuts();
        this.checkCSS();
        this.checkIntegration();
        this.applyFixes();
        this.showResults();
    }
    
    checkEmojiPicker() {
        console.log('📋 Checking Emoji Picker...');
        
        // Check if EmojiPicker class exists
        if (typeof EmojiPicker === 'undefined') {
            this.issues.push('❌ EmojiPicker class not found');
            this.fixes.push('Reload emoji-picker.js script');
        } else {
            console.log('✅ EmojiPicker class found');
        }
        
        // Check if instance exists
        if (typeof window.emojiPicker === 'undefined') {
            this.issues.push('❌ EmojiPicker instance not created');
            this.fixes.push('Create EmojiPicker instance');
        } else {
            console.log('✅ EmojiPicker instance exists');
        }
        
        // Check if DOM element exists
        const pickerElement = document.getElementById('emojiPicker');
        if (!pickerElement) {
            this.issues.push('❌ Emoji picker DOM element not found');
            this.fixes.push('Create emoji picker DOM element');
        } else {
            console.log('✅ Emoji picker DOM element found');
        }
        
        // Check if toggle function exists
        if (typeof toggleEmojiPicker === 'undefined') {
            this.issues.push('❌ toggleEmojiPicker function not found');
            this.fixes.push('Define toggleEmojiPicker function');
        } else {
            console.log('✅ toggleEmojiPicker function found');
        }
    }
    
    checkEmojiShortcuts() {
        console.log('⌨️ Checking Emoji Shortcuts...');
        
        // Check if EmojiShortcuts class exists
        if (typeof EmojiShortcuts === 'undefined') {
            this.issues.push('❌ EmojiShortcuts class not found');
            this.fixes.push('Reload emoji-shortcuts.js script');
        } else {
            console.log('✅ EmojiShortcuts class found');
        }
        
        // Check if instance exists
        if (typeof window.emojiShortcuts === 'undefined') {
            this.issues.push('❌ EmojiShortcuts instance not created');
            this.fixes.push('Create EmojiShortcuts instance');
        } else {
            console.log('✅ EmojiShortcuts instance exists');
            
            // Check shortcuts data
            if (window.emojiShortcuts.shortcuts) {
                const shortcutCount = Object.keys(window.emojiShortcuts.shortcuts).length;\n                console.log(`✅ ${shortcutCount} shortcuts loaded`);\n            } else {\n                this.issues.push('❌ Shortcuts data not accessible');\n            }\n        }\n    }\n    \n    checkCSS() {\n        console.log('🎨 Checking CSS...');\n        \n        const cssLink = document.querySelector('link[href*=\"emoji-picker.css\"]');\n        if (!cssLink) {\n            this.issues.push('❌ Emoji picker CSS not loaded');\n            this.fixes.push('Load emoji-picker.css');\n        } else {\n            console.log('✅ Emoji picker CSS found');\n        }\n    }\n    \n    checkIntegration() {\n        console.log('🔗 Checking Integration...');\n        \n        // Check if message input exists\n        const messageInput = document.getElementById('messageText');\n        if (!messageInput) {\n            this.issues.push('❌ Message input field not found');\n        } else {\n            console.log('✅ Message input field found');\n        }\n        \n        // Check if emoji button exists\n        const emojiBtn = document.getElementById('emojiBtn');\n        if (!emojiBtn) {\n            this.issues.push('❌ Emoji button not found');\n        } else {\n            console.log('✅ Emoji button found');\n        }\n    }\n    \n    applyFixes() {\n        console.log('🔧 Applying fixes...');\n        \n        // Fix 1: Create EmojiPicker instance if missing\n        if (typeof EmojiPicker !== 'undefined' && typeof window.emojiPicker === 'undefined') {\n            try {\n                window.emojiPicker = new EmojiPicker();\n                console.log('✅ Created EmojiPicker instance');\n            } catch (error) {\n                console.error('❌ Failed to create EmojiPicker:', error);\n            }\n        }\n        \n        // Fix 2: Create EmojiShortcuts instance if missing\n        if (typeof EmojiShortcuts !== 'undefined' && typeof window.emojiShortcuts === 'undefined') {\n            try {\n                window.emojiShortcuts = new EmojiShortcuts();\n                console.log('✅ Created EmojiShortcuts instance');\n            } catch (error) {\n                console.error('❌ Failed to create EmojiShortcuts:', error);\n            }\n        }\n        \n        // Fix 3: Define toggleEmojiPicker function if missing\n        if (typeof toggleEmojiPicker === 'undefined') {\n            window.toggleEmojiPicker = function() {\n                if (window.emojiPicker) {\n                    window.emojiPicker.toggle();\n                    \n                    // Toggle button state\n                    const emojiBtn = document.getElementById('emojiBtn');\n                    if (emojiBtn) {\n                        emojiBtn.classList.toggle('active', window.emojiPicker.isVisible);\n                    }\n                } else {\n                    console.error('EmojiPicker instance not found');\n                }\n            };\n            console.log('✅ Created toggleEmojiPicker function');\n        }\n        \n        // Fix 4: Load CSS if missing\n        const cssLink = document.querySelector('link[href*=\"emoji-picker.css\"]');\n        if (!cssLink) {\n            const link = document.createElement('link');\n            link.rel = 'stylesheet';\n            link.href = '/css/emoji-picker.css';\n            document.head.appendChild(link);\n            console.log('✅ Loaded emoji-picker.css');\n        }\n        \n        // Fix 5: Add keyboard shortcuts if missing\n        if (!document.emojiKeyboardListenerAdded) {\n            document.addEventListener('keydown', function(e) {\n                // Ctrl/Cmd + ; to toggle emoji picker\n                if ((e.ctrlKey || e.metaKey) && e.key === ';') {\n                    e.preventDefault();\n                    if (typeof toggleEmojiPicker === 'function') {\n                        toggleEmojiPicker();\n                    }\n                }\n                \n                // Escape to close emoji picker\n                if (e.key === 'Escape' && window.emojiPicker && window.emojiPicker.isVisible) {\n                    window.emojiPicker.hide();\n                    const emojiBtn = document.getElementById('emojiBtn');\n                    if (emojiBtn) {\n                        emojiBtn.classList.remove('active');\n                    }\n                }\n            });\n            document.emojiKeyboardListenerAdded = true;\n            console.log('✅ Added keyboard shortcuts');\n        }\n    }\n    \n    showResults() {\n        console.log('📊 Diagnostic Results:');\n        \n        if (this.issues.length === 0) {\n            console.log('🎉 All emoji features are working correctly!');\n        } else {\n            console.log(`⚠️ Found ${this.issues.length} issues:`);\n            this.issues.forEach(issue => console.log(issue));\n            \n            console.log('🔧 Applied fixes:');\n            this.fixes.forEach(fix => console.log(`✅ ${fix}`));\n        }\n        \n        // Test basic functionality\n        this.runTests();\n    }\n    \n    runTests() {\n        console.log('🧪 Running functionality tests...');\n        \n        // Test 1: Emoji picker toggle\n        if (typeof toggleEmojiPicker === 'function') {\n            console.log('✅ Emoji picker toggle function works');\n        } else {\n            console.log('❌ Emoji picker toggle function not working');\n        }\n        \n        // Test 2: Shortcut conversion\n        if (window.emojiShortcuts && window.emojiShortcuts.shortcuts) {\n            const testShortcuts = [':)', ':heart:', ':fire:'];\n            const workingShortcuts = testShortcuts.filter(shortcut => \n                window.emojiShortcuts.shortcuts[shortcut]\n            );\n            console.log(`✅ ${workingShortcuts.length}/${testShortcuts.length} test shortcuts working`);\n        }\n        \n        // Test 3: DOM elements\n        const messageInput = document.getElementById('messageText');\n        const emojiBtn = document.getElementById('emojiBtn');\n        const pickerElement = document.getElementById('emojiPicker');\n        \n        console.log(`✅ DOM elements: Input(${!!messageInput}) Button(${!!emojiBtn}) Picker(${!!pickerElement})`);\n    }\n    \n    // Manual test functions\n    testEmojiPicker() {\n        if (typeof toggleEmojiPicker === 'function') {\n            toggleEmojiPicker();\n            return 'Emoji picker toggled';\n        }\n        return 'Emoji picker not available';\n    }\n    \n    testShortcut(shortcut) {\n        if (window.emojiShortcuts && window.emojiShortcuts.shortcuts) {\n            const emoji = window.emojiShortcuts.shortcuts[shortcut];\n            return emoji ? `${shortcut} → ${emoji}` : `${shortcut} not found`;\n        }\n        return 'Shortcuts not available';\n    }\n    \n    insertEmoji(emoji) {\n        const messageInput = document.getElementById('messageText');\n        if (messageInput) {\n            const cursorPos = messageInput.selectionStart || messageInput.value.length;\n            const textBefore = messageInput.value.substring(0, cursorPos);\n            const textAfter = messageInput.value.substring(messageInput.selectionEnd || cursorPos);\n            \n            messageInput.value = textBefore + emoji + textAfter;\n            messageInput.focus();\n            \n            const newPos = cursorPos + emoji.length;\n            messageInput.setSelectionRange(newPos, newPos);\n            \n            return `Inserted ${emoji}`;\n        }\n        return 'Message input not found';\n    }\n}\n\n// Auto-run diagnostics when loaded\ndocument.addEventListener('DOMContentLoaded', function() {\n    // Wait a bit for other scripts to load\n    setTimeout(() => {\n        window.emojiDiagnostics = new EmojiDiagnostics();\n        \n        // Add global test functions\n        window.testEmojiPicker = () => window.emojiDiagnostics.testEmojiPicker();\n        window.testShortcut = (shortcut) => window.emojiDiagnostics.testShortcut(shortcut);\n        window.insertEmoji = (emoji) => window.emojiDiagnostics.insertEmoji(emoji);\n        \n        console.log('🎯 Test functions available:');\n        console.log('- testEmojiPicker() - Toggle emoji picker');\n        console.log('- testShortcut(\":)\") - Test shortcut conversion');\n        console.log('- insertEmoji(\"😊\") - Insert emoji into input');\n    }, 1000);\n});