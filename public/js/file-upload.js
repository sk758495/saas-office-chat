// File and Folder Upload Handler
class FileUploadHandler {
    constructor() {
        this.setupEventListeners();
    }

    setupEventListeners() {
        // File input change handler
        document.addEventListener('change', (e) => {
            if (e.target.matches('input[type="file"][data-upload-type]')) {
                this.handleFileSelection(e.target);
            }
        });

        // Folder input change handler  
        document.addEventListener('change', (e) => {
            if (e.target.matches('input[type="file"][webkitdirectory]')) {
                this.handleFolderSelection(e.target);
            }
        });
    }

    handleFileSelection(input) {
        const file = input.files[0];
        if (!file) return;

        const uploadType = input.dataset.uploadType;
        const preview = this.createFilePreview(file, uploadType);
        
        // Show preview
        this.showUploadPreview(preview, uploadType);
    }

    handleFolderSelection(input) {
        const files = Array.from(input.files);
        if (files.length === 0) return;

        const folderName = this.extractFolderName(files[0].webkitRelativePath);
        const preview = this.createFolderPreview(files, folderName);
        
        // Show preview
        this.showUploadPreview(preview, 'folder');
    }

    createFilePreview(file, uploadType) {
        const isZip = file.name.toLowerCase().endsWith('.zip');
        const isImage = file.type.startsWith('image/');
        
        return {
            type: isZip ? 'zip' : (isImage ? 'image' : 'file'),
            name: file.name,
            size: this.formatFileSize(file.size),
            file: file,
            uploadType: uploadType
        };
    }

    createFolderPreview(files, folderName) {
        return {
            type: 'folder',
            name: folderName,
            fileCount: files.length,
            totalSize: this.formatFileSize(files.reduce((sum, f) => sum + f.size, 0)),
            files: files
        };
    }

    showUploadPreview(preview, uploadType) {
        const previewContainer = document.getElementById('upload-preview');
        if (!previewContainer) return;

        let html = '';
        
        if (preview.type === 'folder') {
            html = `
                <div class="upload-preview-item folder-preview">
                    <div class="preview-icon">📁</div>
                    <div class="preview-info">
                        <div class="preview-name">${preview.name}</div>
                        <div class="preview-details">${preview.fileCount} files • ${preview.totalSize}</div>
                    </div>
                    <button type="button" class="remove-preview" onclick="this.parentElement.remove()">×</button>
                </div>
            `;
        } else if (preview.type === 'zip') {
            html = `
                <div class="upload-preview-item zip-preview">
                    <div class="preview-icon">🗜️</div>
                    <div class="preview-info">
                        <div class="preview-name">${preview.name}</div>
                        <div class="preview-details">ZIP Archive • ${preview.size}</div>
                    </div>
                    <button type="button" class="remove-preview" onclick="this.parentElement.remove()">×</button>
                </div>
            `;
        } else if (preview.type === 'image') {
            const imageUrl = URL.createObjectURL(preview.file);
            html = `
                <div class="upload-preview-item image-preview">
                    <img src="${imageUrl}" alt="${preview.name}" class="preview-image">
                    <div class="preview-info">
                        <div class="preview-name">${preview.name}</div>
                        <div class="preview-details">${preview.size}</div>
                    </div>
                    <button type="button" class="remove-preview" onclick="this.parentElement.remove()">×</button>
                </div>
            `;
        } else {
            html = `
                <div class="upload-preview-item file-preview">
                    <div class="preview-icon">📄</div>
                    <div class="preview-info">
                        <div class="preview-name">${preview.name}</div>
                        <div class="preview-details">${preview.size}</div>
                    </div>
                    <button type="button" class="remove-preview" onclick="this.parentElement.remove()">×</button>
                </div>
            `;
        }

        previewContainer.innerHTML = html;
        previewContainer.style.display = 'block';
    }

    extractFolderName(relativePath) {
        return relativePath.split('/')[0];
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Method to send file/folder with message
    sendWithAttachment(messageData, attachmentType) {
        const formData = new FormData();
        
        // Add message data
        Object.keys(messageData).forEach(key => {
            if (messageData[key] !== null && messageData[key] !== undefined) {
                formData.append(key, messageData[key]);
            }
        });

        // Add attachment based on type
        if (attachmentType === 'folder') {
            const folderInput = document.querySelector('input[webkitdirectory]');
            if (folderInput && folderInput.files.length > 0) {
                Array.from(folderInput.files).forEach((file, index) => {
                    formData.append(`folder_files[${index}]`, file);
                });
                formData.append('folder_name', this.extractFolderName(folderInput.files[0].webkitRelativePath));
            }
        } else {
            const fileInput = document.querySelector('input[type="file"]:not([webkitdirectory])');
            if (fileInput && fileInput.files[0]) {
                formData.append('file', fileInput.files[0]);
            }
        }

        return formData;
    }
}

// Initialize the file upload handler
document.addEventListener('DOMContentLoaded', () => {
    new FileUploadHandler();
});

// Export for use in other scripts
window.FileUploadHandler = FileUploadHandler;