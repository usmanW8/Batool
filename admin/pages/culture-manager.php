<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../config/database.php';

require_login();

$page_title = 'Culture & Vision Manager';
$current_page = 'culture';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sections = ['heritage', 'responsibility', 'leadership'];
    
    foreach ($sections as $section) {
        $title = $_POST[$section . '_title'] ?? '';
        $content = $_POST[$section . '_content'] ?? '';
        
        $sql = "UPDATE culture_content SET section_title = ?, content_text = ? WHERE section_name = ?";
        db_query($sql, [$title, $content, $section], 'sss');
    }
    
    // Handle heritage image upload
    if (isset($_FILES['heritage_image']) && $_FILES['heritage_image']['error'] === 0) {
        $upload_result = upload_image($_FILES['heritage_image'], 'img/culture/');
        
        if ($upload_result['success']) {
            $sql = "UPDATE culture_content SET image_path = ? WHERE section_name = 'heritage'";
            db_query($sql, [$upload_result['path']], 's');
        }
    }
    
    redirect_with_message('/Batool/admin/pages/culture-manager.php', 'Culture section updated successfully!', 'success');
}

// Get current culture content
$culture_sections = db_fetch_all("SELECT * FROM culture_content ORDER BY id");

include '../includes/header.php';
?>

<div class="content-box">
    <h2>Culture & Vision Content</h2>
    <p style="color: #666; margin-bottom: 30px;">
        Edit the content for your Culture & Vision page, which describes your cultural values and future aspirations.
    </p>
    
    <form method="POST" enctype="multipart/form-data">
        <?php foreach ($culture_sections as $section): ?>
        <div style="margin-bottom: 40px; padding-bottom: 30px; border-bottom: 2px solid var(--border-color);">
            <div class="form-group">
                <label for="<?php echo h($section['section_name']); ?>_title">
                    <?php echo h($section['section_title']); ?> - Title
                </label>
                <input type="text" 
                       id="<?php echo h($section['section_name']); ?>_title" 
                       name="<?php echo h($section['section_name']); ?>_title" 
                       class="form-control" 
                       value="<?php echo h($section['section_title']); ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="<?php echo h($section['section_name']); ?>_content">
                    Content
                </label>
                <textarea id="<?php echo h($section['section_name']); ?>_content" 
                          name="<?php echo h($section['section_name']); ?>_content" 
                          class="form-control" 
                          rows="6" 
                          required><?php echo h($section['content_text']); ?></textarea>
            </div>
        </div>
        <?php endforeach; ?>
        
        <hr style="margin: 40px 0; border: none; border-top: 2px solid var(--border-color);">
        
        <div class="form-group">
            <label for="heritage_image">Heritage Image</label>
            <input type="file" 
                   id="heritage_image" 
                   name="heritage_image" 
                   class="form-control" 
                   accept="image/*"
                   onchange="previewImage(this, 'heritage_preview')">
            
            <?php
            $heritage = array_filter($culture_sections, function($s) { return $s['section_name'] === 'heritage'; });
            $heritage = reset($heritage);
            if ($heritage && $heritage['image_path']):
            ?>
            <div class="current-image">
                <p style="font-weight: 600; margin-bottom: 10px; font-size: 0.9rem;">Current Image:</p>
                <img src="<?php echo get_image_url($heritage['image_path']); ?>" alt="Heritage">
            </div>
            <?php endif; ?>
            
            <div class="image-preview-container" style="display: none;">
                <p style="font-weight: 600; margin-bottom: 10px; font-size: 0.9rem;">New Image Preview:</p>
                <img id="heritage_preview" class="image-preview" style="display: none;">
            </div>
        </div>
        
        <div style="margin-top: 40px;">
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
