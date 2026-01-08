<!-- Image Crop Modal -->
<div id="cropModal" class="crop-modal" style="display: none;">
    <div class="crop-modal-content">
        <!-- Header -->
        <div class="crop-header">
            <h3 id="cropTitle">Crop Image</h3>
            <p id="cropDimensions" class="crop-dimensions">Adjust the crop area to your preference</p>
            <p id="cropDescription" class="crop-description"></p>
        </div>
        
        <!-- Crop Container -->
        <div class="crop-container">
            <img id="cropImage" src="" alt="Image to crop">
        </div>
        
        <!-- Controls -->
        <div class="crop-controls">
            <button type="button" data-method="zoom" data-option="0.1" title="Zoom In">
                <i data-lucide="zoom-in"></i>
                Zoom In
            </button>
            <button type="button" data-method="zoom" data-option="-0.1" title="Zoom Out">
                <i data-lucide="zoom-out"></i>
                Zoom Out
            </button>
            <button type="button" data-method="rotate" data-option="-90" title="Rotate Left">
                <i data-lucide="rotate-ccw"></i>
                Rotate Left
            </button>
            <button type="button" data-method="rotate" data-option="90" title="Rotate Right">
                <i data-lucide="rotate-cw"></i>
                Rotate Right
            </button>
            <button type="button" data-method="reset" data-option="" title="Reset">
                <i data-lucide="refresh-cw"></i>
                Reset
            </button>
        </div>
        
        <!-- Actions -->
        <div class="crop-actions">
            <button type="button" class="btn btn-secondary" onclick="closeCropModal()">
                <i data-lucide="x" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle; margin-right: 4px;"></i>
                Cancel
            </button>
            <button type="button" class="btn btn-primary" onclick="finalizeCrop()">
                <i data-lucide="check" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle; margin-right: 4px;"></i>
                Done
            </button>
        </div>
    </div>
</div>

<script>
    // Re-initialize Lucide icons when modal opens
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
