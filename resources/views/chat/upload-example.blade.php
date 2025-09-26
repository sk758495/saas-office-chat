<!DOCTYPE html>
<html>
<head>
    <title>File Upload Example</title>
    <link href="{{ asset('css/file-upload.css') }}" rel="stylesheet">
</head>
<body>
    <div class="chat-container">
        <!-- Upload Controls -->
        <div class="upload-controls">
            <label class="upload-btn">
                📎 File
                <input type="file" data-upload-type="file" accept="*/*">
            </label>
            
            <label class="upload-btn">
                🗜️ ZIP
                <input type="file" data-upload-type="zip" accept=".zip">
            </label>
            
            <label class="upload-btn">
                📁 Folder
                <input type="file" webkitdirectory multiple>
            </label>
        </div>

        <!-- Upload Preview -->
        <div id="upload-preview"></div>

        <!-- Message Input -->
        <div class="message-input">
            <input type="text" id="message-text" placeholder="Type a message...">
            <button onclick="sendMessage()">Send</button>
        </div>

        <!-- Messages Display -->
        <div id="messages-container">
            <!-- Example message with ZIP file -->
            <div class="message">
                <strong>John Doe:</strong>
                <p>Here's the project files</p>
                <div class="message-attachment">
                    <div class="attachment-header">
                        <span class="attachment-icon">🗜️</span>
                        <div class="attachment-info">
                            <h4>project-files.zip</h4>
                            <p>ZIP Archive • 2.5 MB</p>
                        </div>
                    </div>
                    <div class="folder-contents">
                        <div class="folder-file">
                            <span>index.html</span>
                            <span>15 KB</span>
                        </div>
                        <div class="folder-file">
                            <span>style.css</span>
                            <span>8 KB</span>
                        </div>
                        <div class="folder-file">
                            <span>script.js</span>
                            <span>12 KB</span>
                        </div>
                    </div>
                    <a href="#" class="download-btn">Download</a>
                </div>
            </div>

            <!-- Example message with folder -->
            <div class="message">
                <strong>Jane Smith:</strong>
                <p>Shared documents folder</p>
                <div class="message-attachment">
                    <div class="attachment-header">
                        <span class="attachment-icon">📁</span>
                        <div class="attachment-info">
                            <h4>Documents</h4>
                            <p>Folder • 5 files • 1.2 MB</p>
                        </div>
                    </div>
                    <div class="folder-contents">
                        <div class="folder-file">
                            <span>report.pdf</span>
                            <span>500 KB</span>
                        </div>
                        <div class="folder-file">
                            <span>data.xlsx</span>
                            <span>300 KB</span>
                        </div>
                        <div class="folder-file">
                            <span>notes.txt</span>
                            <span>5 KB</span>
                        </div>
                    </div>
                    <a href="#" class="download-btn">Download ZIP</a>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/file-upload.js') }}"></script>
    <script>
        function sendMessage() {
            const messageText = document.getElementById('message-text').value;
            const fileInput = document.querySelector('input[type="file"]:not([webkitdirectory])');
            const folderInput = document.querySelector('input[webkitdirectory]');
            
            if (!messageText && !fileInput.files[0] && !folderInput.files.length) {
                alert('Please enter a message or select a file/folder');
                return;
            }

            const formData = new FormData();
            formData.append('message', messageText);
            formData.append('receiver_id', 1); // Example receiver ID

            if (folderInput.files.length > 0) {
                Array.from(folderInput.files).forEach((file, index) => {
                    formData.append(`folder_files[${index}]`, file);
                });
                formData.append('folder_name', folderInput.files[0].webkitRelativePath.split('/')[0]);
            } else if (fileInput.files[0]) {
                formData.append('file', fileInput.files[0]);
            }

            // Send to server
            fetch('/chat/send-message', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear inputs
                    document.getElementById('message-text').value = '';
                    fileInput.value = '';
                    folderInput.value = '';
                    document.getElementById('upload-preview').style.display = 'none';
                    
                    // Add message to chat (implement your message display logic)
                    console.log('Message sent:', data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>