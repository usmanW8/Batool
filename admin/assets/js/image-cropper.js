// =========================================
// Image Cropper Module
// Handles image cropping for all admin uploads
// =========================================

let currentCropper = null;
let currentFileInput = null;
let currentCropType = null;
let croppedBlob = null;

// Initialize crop functionality when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    initializeCropInputs();
});

// Initialize all file inputs with crop-enabled class
function initializeCropInputs() {
    const cropInputs = document.querySelectorAll('input[type="file"].crop-enabled');

    cropInputs.forEach(input => {
        input.addEventListener('change', handleFileSelect);
    });
}

// Handle file selection
function handleFileSelect(e) {
    const file = e.target.files[0];
    if (!file) return;

    // Validate file type
    if (!file.type.match('image.*')) {
        alert('Please select an image file');
        e.target.value = '';
        return;
    }

    currentFileInput = e.target;
    currentCropType = e.target.getAttribute('data-crop-type') || 'section';

    // Read file and open crop modal
    const reader = new FileReader();
    reader.onload = function (event) {
        openCropModal(event.target.result);
    };
    reader.readAsDataURL(file);
}

// Open crop modal with image
function openCropModal(imageSrc) {
    const modal = document.getElementById('cropModal');
    const cropImage = document.getElementById('cropImage');
    const cropTitle = document.getElementById('cropTitle');
    const cropDimensions = document.getElementById('cropDimensions');
    const cropDescription = document.getElementById('cropDescription');

    // Get configuration for this crop type
    const config = getCropConfig(currentCropType);

    // Update modal content
    cropTitle.textContent = 'Crop ' + config.label;
    cropDimensions.textContent = config.dimensions;
    cropDescription.textContent = config.description;

    // Set image source
    cropImage.src = imageSrc;

    // Show modal
    modal.style.display = 'flex';

    // Initialize cropper
    initializeCropper(config);
}

// Initialize Cropper.js
function initializeCropper(config) {
    const cropImage = document.getElementById('cropImage');

    // Destroy existing cropper if any
    if (currentCropper) {
        currentCropper.destroy();
    }

    // Create new cropper
    currentCropper = new Cropper(cropImage, {
        aspectRatio: config.aspectRatio,
        viewMode: config.viewMode || 1,
        dragMode: 'move',
        autoCropArea: 0.8,
        restore: false,
        guides: true,
        center: true,
        highlight: false,
        cropBoxMovable: true,
        cropBoxResizable: true,
        toggleDragModeOnDblclick: false,
        minContainerWidth: 600,
        minContainerHeight: 400,
        ready: function () {
            // Add control button listeners
            addCropperControls();
        }
    });
}

// Add cropper control buttons
function addCropperControls() {
    const buttons = document.querySelectorAll('.crop-controls button[data-method]');

    buttons.forEach(button => {
        button.onclick = function () {
            const method = this.getAttribute('data-method');
            const option = this.getAttribute('data-option');

            if (method && currentCropper) {
                let result = currentCropper[method](option ? parseFloat(option) : undefined);
            }
        };
    });
}

// Close crop modal
function closeCropModal() {
    const modal = document.getElementById('cropModal');
    modal.style.display = 'none';

    // Destroy cropper
    if (currentCropper) {
        currentCropper.destroy();
        currentCropper = null;
    }

    // Reset file input only if no cropped image pending
    if (currentFileInput && !currentFileInput.dataset.hasCroppedImage) {
        currentFileInput.value = '';
    }
}

// Finalize crop and prepare for upload
function finalizeCrop() {
    if (!currentCropper) return;

    const config = getCropConfig(currentCropType);

    // Get cropped canvas
    const canvas = currentCropper.getCroppedCanvas({
        minWidth: config.minWidth,
        minHeight: config.minHeight,
        maxWidth: 4096,
        maxHeight: 4096,
        fillColor: '#fff',
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high'
    });

    if (!canvas) {
        alert('Failed to crop image. Please try again.');
        return;
    }

    // Convert canvas to blob
    canvas.toBlob(function (blob) {
        if (!blob) {
            alert('Failed to process image. Please try again.');
            return;
        }

        // Store blob globally for form submission
        croppedBlob = blob;

        // Get original filename
        const originalFile = currentFileInput.files[0];
        const fileName = originalFile.name;

        // Store necessary data for form submission
        currentFileInput.dataset.hasCroppedImage = 'true';
        currentFileInput.dataset.croppedFileName = fileName;
        currentFileInput.dataset.croppedFileType = blob.type;

        // Show preview
        showCroppedPreview(canvas.toDataURL());

        // Close modal
        closeCropModal();

        // Show success message
        showCropSuccessMessage();

        // Attach form submission handler
        attachFormSubmitHandler();

    }, currentFileInput.files[0].type, 0.95); // 95% quality
}

// Attach form submission handler to send cropped image
function attachFormSubmitHandler() {
    if (!currentFileInput) return;

    const form = currentFileInput.closest('form');
    if (!form) return;

    // Remove existing handler if any
    if (form.dataset.cropHandlerAttached === 'true') return;

    form.dataset.cropHandlerAttached = 'true';

    form.addEventListener('submit', function (e) {
        // Check if we have a cropped image for this form
        const croppedInput = form.querySelector('input[type="file"][data-has-cropped-image="true"]');

        if (croppedInput && croppedBlob) {
            e.preventDefault();

            // Create FormData from form
            const formData = new FormData(form);

            // Get the input name
            const inputName = croppedInput.getAttribute('name');
            const fileName = croppedInput.dataset.croppedFileName;
            const fileType = croppedInput.dataset.croppedFileType;

            // Remove the old file entry
            formData.delete(inputName);

            // Add the cropped blob with original filename
            formData.append(inputName, croppedBlob, fileName);

            // Submit via AJAX
            fetch(form.action || window.location.href, {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (response.ok) {
                        // Redirect or reload
                        window.location.href = response.url || window.location.href;
                    } else {
                        alert('Upload failed. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Upload error:', error);
                    alert('Upload failed. Please try again.');
                });
        }
    }, { once: false });
}

// Show cropped preview
function showCroppedPreview(dataUrl) {
    // Try to find preview image element
    const previewId = currentFileInput.getAttribute('data-preview');
    if (previewId) {
        const previewImg = document.getElementById(previewId);
        if (previewImg) {
            previewImg.src = dataUrl;
            previewImg.style.display = 'block';
        }
    }

    // Generic preview update
    const form = currentFileInput.closest('form');
    if (form) {
        const preview = form.querySelector('.image-preview, .logo-preview-section img');
        if (preview && preview.tagName === 'IMG') {
            preview.src = dataUrl;
        }
    }
}

// Show success message
function showCropSuccessMessage() {
    const config = getCropConfig(currentCropType);

    // Create temporary notification
    const notification = document.createElement('div');
    notification.className = 'crop-success-notification';
    notification.innerHTML = `
        <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
        <span><strong>Image cropped!</strong> Now click "Save Changes" or "Upload" button below to save.</span>
    `;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #10b981;
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 10px;
        font-family: 'Poppins', sans-serif;
        animation: slideInRight 0.3s ease;
        max-width: 400px;
    `;

    document.body.appendChild(notification);

    // Re-initialize lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Remove after 5 seconds (longer so user can read it)
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 5000);

    // Add visual indicator to file input
    if (currentFileInput) {
        const label = document.createElement('div');
        label.className = 'crop-ready-indicator';
        label.innerHTML = `
            <i data-lucide="check-circle" style="width: 14px; height: 14px;"></i>
            <span>Cropped image ready</span>
        `;
        label.style.cssText = `
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #10b981;
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 0.85rem;
            margin-top: 8px;
            font-weight: 500;
        `;

        // Remove any existing indicator
        const existing = currentFileInput.parentElement.querySelector('.crop-ready-indicator');
        if (existing) existing.remove();

        // Add new indicator
        currentFileInput.parentElement.appendChild(label);

        // Re-initialize lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
}

// Close modal when clicking outside
document.addEventListener('click', function (e) {
    const modal = document.getElementById('cropModal');
    if (e.target === modal) {
        closeCropModal();
    }
});

// ESC key to close modal
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('cropModal');
        if (modal && modal.style.display === 'flex') {
            closeCropModal();
        }
    }
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
