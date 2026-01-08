<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../config/database.php';

require_login();

$page_title = 'Digital Experience Manager';
$current_page = 'digital';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content_types = ['laptop_mockup', 'parcels', 'reviews', 'content_reel', 'visuals'];
    
    foreach ($content_types as $type) {
        if (isset($_FILES[$type]) && $_FILES[$type]['error'] === 0) {
            $upload_result = upload_image($_FILES[$type], 'img/digital/');
            
            if ($upload_result['success']) {
                $sql = "UPDATE digital_content SET image_path = ? WHERE content_type = ?";
                db_query($sql, [$upload_result['path'], $type], 'ss');
            }
        }
    }
    
    redirect_with_message('/Batool/admin/pages/digital-manager.php', 'Digital section images updated successfully!', 'success');
}

// Get current images
$images = db_fetch_all("SELECT * FROM digital_content ORDER BY display_order");

include '../includes/header.php';
?>

<div class="content-box">
    <h2>Digital Experience Images</h2>
    <p style="color: #666; margin-bottom: 30px;">
        Manage images for your digital experience section, including your platform mockups and content creation showcase.
    </p>
    
    <form method="POST" enctype="multipart/form-data">
        <?php foreach ($images as $image): ?>
        <div class="form-group">
            <label for="<?php echo h($image['content_type']); ?>">
                <?php echo h($image['title']); ?>
            </label>
            <input type="file" 
                   id="<?php echo h($image['content_type']); ?>" 
                   name="<?php echo h($image['content_type']); ?>" 
                   class="form-control" 
                   accept="image/*"
                   onchange="previewImage(this, '<?php echo h($image['content_type']); ?>_preview')">
            
            <div class="current-image">
                <p style="font-weight: 600; margin-bottom: 10px; font-size: 0.9rem;">Current Image:</p>
                <img src="<?php echo get_image_url($image['image_path']); ?>" 
                     alt="<?php echo h($image['title']); ?>">
            </div>
            
            <div class="image-preview-container" style="display: none;">
                <p style="font-weight: 600; margin-bottom: 10px; font-size: 0.9rem;">New Image Preview:</p>
                <img id="<?php echo h($image['content_type']); ?>_preview" class="image-preview" style="display: none;">
            </div>
        </div>
        
        <?php if ($image !== end($images)): ?>
        <hr style="margin: 30px 0; border: none; border-top: 1px solid var(--border-color);">
        <?php endif; ?>
        <?php endforeach; ?>
        
        <div style="margin-top: 40px; padding-top: 30px; border-top: 2px solid var(--border-color);">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;"></i>
                Save All Changes
            </button>
            <a href="/Batool/admin/index.php" class="btn btn-secondary" style="margin-left: 15px;">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    function previewImage(input, previewId) {
        const container = input.parentElement.querySelector('.image-preview-container');
        const preview = document.getElementById(previewId);
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                container.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php include '../includes/footer.php'; ?>
