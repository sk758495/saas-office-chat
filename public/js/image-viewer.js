// Image Viewer and Download Functionality
class ImageViewer {
    constructor() {
        this.token = localStorage.getItem('auth_token');
        this.init();
    }

    init() {
        this.createModal();
        this.attachEventListeners();
    }

    createModal() {
        const modal = document.createElement('div');
        modal.id = 'imageModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center';
        modal.innerHTML = `
            <div class="relative max-w-4xl max-h-full p-4">
                <button id="closeModal" class="absolute top-2 right-2 text-white text-2xl z-10 bg-black bg-opacity-50 rounded-full w-8 h-8 flex items-center justify-center">×</button>
                <img id="modalImage" class="max-w-full max-h-full object-contain" src="" alt="">
                <div class="absolute bottom-4 right-4 flex gap-2">
                    <button id="downloadBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Download
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }

    attachEventListeners() {
        // Close modal
        document.getElementById('closeModal').addEventListener('click', () => {
            this.closeModal();
        });

        // Close on background click
        document.getElementById('imageModal').addEventListener('click', (e) => {
            if (e.target.id === 'imageModal') {
                this.closeModal();
            }
        });

        // Download button
        document.getElementById('downloadBtn').addEventListener('click', () => {
            const messageId = document.getElementById('modalImage').dataset.messageId;
            this.downloadFile(messageId);
        });
    }

    viewImage(messageId, imageSrc) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        
        modalImage.src = imageSrc;
        modalImage.dataset.messageId = messageId;
        modal.classList.remove('hidden');
    }

    closeModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.add('hidden');
    }

    async downloadFile(messageId) {
        try {
            const response = await fetch(`/api/files/download/${messageId}`, {
                headers: {
                    'Authorization': `Bearer ${this.token}`
                }
            });

            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = response.headers.get('Content-Disposition')?.split('filename=')[1] || 'download';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            } else {
                alert('Download failed');
            }
        } catch (error) {
            console.error('Download error:', error);
            alert('Download failed');
        }
    }
}

// Message rendering helper
function renderMessage(message) {
    const messageDiv = document.createElement('div');
    messageDiv.className = 'message mb-4';
    
    let content = '';
    
    if (message.type === 'image') {
        const imageUrl = `/api/files/view/${message.id}`;
        content = `
            <div class="image-message">
                <img src="${imageUrl}" 
                     alt="Image" 
                     class="max-w-xs rounded cursor-pointer hover:opacity-80"
                     onclick="imageViewer.viewImage(${message.id}, '${imageUrl}')"
                     onerror="this.src='/images/image-error.png'">
                <div class="mt-2 flex gap-2">
                    <button onclick="imageViewer.viewImage(${message.id}, '${imageUrl}')" 
                            class="text-blue-500 text-sm hover:underline">
                        View
                    </button>
                    <button onclick="imageViewer.downloadFile(${message.id})" 
                            class="text-green-500 text-sm hover:underline">
                        Download
                    </button>
                </div>
            </div>
        `;
    } else if (message.type === 'file') {
        content = `
            <div class="file-message">
                <div class="flex items-center gap-2 p-3 bg-gray-100 rounded">
                    <span class="file-icon">📎</span>
                    <span class="file-name">${message.file_name || 'File'}</span>
                    <button onclick="imageViewer.downloadFile(${message.id})" 
                            class="ml-auto text-blue-500 hover:underline">
                        Download
                    </button>
                </div>
            </div>
        `;
    } else {
        content = `<div class="text-message">${message.message}</div>`;
    }
    
    messageDiv.innerHTML = `
        <div class="message-header text-sm text-gray-500 mb-1">
            ${message.sender.name} - ${new Date(message.created_at).toLocaleString()}
        </div>
        ${content}
    `;
    
    return messageDiv;
}

// Initialize image viewer
const imageViewer = new ImageViewer();