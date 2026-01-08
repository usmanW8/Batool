<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../config/database.php';

require_login();

$page_title = 'Hero Section Manager';
$current_page = 'hero';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $main_title = $_POST['main_title'] ?? '';
    $subtitle = $_POST['subtitle'] ?? '';
    $typewriter_text = $_POST['typewriter_text'] ?? '';
    $cta_text = $_POST['cta_text'] ?? '';
    $quote_text = $_POST['quote_text'] ?? '';
    $identity_description = $_POST['identity_description'] ?? '';
    
    // Get current data
    $current = db_fetch_single("SELECT * FROM hero_section LIMIT 1");
    $profile_image = $current['profile_image'];
    $background_image = $current['background_image'];
    
    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $upload_result = upload_image($_FILES['profile_image'], 'img/hero/');
        if ($upload_result['success']) {
            $profile_image = $upload_result['path'];
        }
    }
    
    // Handle background image upload
    if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] === 0) {
        $upload_result = upload_image($_FILES['background_image'], 'img/hero/');
        if ($upload_result['success']) {
            $background_image = $upload_result['path'];
        }
    }
    
    // Update database
    $sql = "UPDATE hero_section SET 
            main_title = ?, 
            subtitle = ?, 
            typewriter_text = ?, 
            cta_text = ?, 
            quote_text = ?,
            identity_description = ?,
            profile_image = ?,
            background_image = ?
            WHERE id = 1";
    
    $stmt = db_query($sql, [
        $main_title, $subtitle, $typewriter_text, $cta_text, $quote_text, $identity_description, $profile_image, $background_image
    ], 'ssssssss');
    
    if ($stmt) {
        redirect_with_message('/Batool/admin/pages/hero-manager.php', 'Hero section updated successfully!', 'success');
    } else {
        redirect_with_message('/Batool/admin/pages/hero-manager.php', 'Failed to update hero section', 'error');
    }
}

// Get current hero data
$hero = db_fetch_single("SELECT * FROM hero_section LIMIT 1");

include '../includes/header.php';
?>

<div class="content-box">
    <h2>Edit Hero Section</h2>
    <p style="color: #666; margin-bottom: 30px;">
        Update the main hero section that appears on your homepage. This includes the title, subtitle, and background images.
    </p>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="subtitle">Subtitle (Name)</label>
            <input type="text" 
                   id="subtitle" 
                   name="subtitle" 
                   class="form-control" 
                   value="<?php echo h($hero['subtitle'] ?? ''); ?>" 
                   required>
        </div>
        
        <div class="form-group">
            <label for="main_title">Main Title</label>
            <textarea id="main_title" 
                      name="main_title" 
                      class="form-control" 
                      rows="3" 
                      required><?php echo h($hero['main_title'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="typewriter_text">Typewriter Text (Tagline)</label>
            <textarea id="typewriter_text" 
                      name="typewriter_text" 
                      class="form-control" 
                      rows="2" 
                      required><?php echo h($hero['typewriter_text'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="cta_text">Call-to-Action Button Text</label>
            <input type="text" 
                   id="cta_text" 
                   name="cta_text" 
                   class="form-control" 
                   value="<?php echo h($hero['cta_text'] ?? ''); ?>" 
                   required>
        </div>
        
        <div class="form-group">
            <label for="quote_text">Quote Text</label>
            <textarea id="quote_text" 
                      name="quote_text" 
                      class="form-control" 
                      rows="2" 
                      required><?php echo h($hero['quote_text'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="identity_description">Identity Description (My Identity Section)</label>
            <textarea id="identity_description" 
                      name="identity_description" 
                      class="form-control" 
                      rows="4" 
                      required><?php echo h($hero['identity_description'] ?? ''); ?></textarea>
            <small style="color: #666; display: block; margin-top: 5px;">
                This text appears in the "My Identity" section of the About page.
            </small>
        </div>
        
        <hr style="margin: 40px 0; border: none; border-top: 2px solid var(--border-color);">
        
        <div class="form-group">
            <label for="profile_image">Profile Image</label>
            <input type="file" 
                   id="profile_image" 
                   name="profile_image" 
                   class="form-control crop-enabled" 
                   data-crop-type="portfolio"
                   accept="image/*"
                   onchange="previewImage(this, 'profile_preview')">
            
            <div class="current-image">
                <p style="font-weight: 600; margin-bottom: 10px; font-size: 0.9rem;">Current Profile Image:</p>
                <img src="<?php echo get_image_url($hero['profile_image'] ?? ''); ?>" 
                     alt="Profile" 
                     style="max-width: 200px; border-radius: 50%;">
            </div>
            
            <div class="image-preview-container" style="display: none;">
                <p style="font-weight: 600; margin-bottom: 10px; font-size: 0.9rem;">New Image Preview:</p>
                <img id="profile_preview" class="image-preview" style="display: none; border-radius: 50%;">
            </div>
        </div>
        
        <div class="form-group">
            <label for="background_image">Background Image</label>
            <input type="file" 
                   id="background_image" 
                   name="background_image" 
                   class="form-control crop-enabled" 
                   data-crop-type="hero"
                   accept="image/*"
                   onchange="previewImage(this, 'background_preview')">
            
            <div class="current-image">
                <p style="font-weight: 600; margin-bottom: 10px; font-size: 0.9rem;">Current Background Image:</p>
                <img src="<?php echo get_image_url($hero['background_image'] ?? ''); ?>" alt="Background">
            </div>
            
            <div class="image-preview-container" style="display: none;">
                <p style="font-weight: 600; margin-bottom: 10px; font-size: 0.9rem;">New Image Preview:</p>
                <img id="background_preview" class="image-preview" style="display: none;">
            </div>
        </div>
        
        <div style="margin-top: 40px; padding-top: 30px; border-top: 2px solid var(--border-color);">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;"></i>
                Save Changes
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
