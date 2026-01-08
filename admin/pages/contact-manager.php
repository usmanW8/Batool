<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../config/database.php';

require_login();

$page_title = 'Contact Information Manager';
$current_page = 'contact';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $location = $_POST['location'] ?? '';
    $social_instagram = $_POST['social_instagram'] ?? '';
    $social_youtube = $_POST['social_youtube'] ?? '';
    $social_linkedin = $_POST['social_linkedin'] ?? '';
    
    $sql = "UPDATE contact_info SET 
            email = ?, 
            location = ?, 
            social_instagram = ?, 
            social_youtube = ?, 
            social_linkedin = ?
            WHERE id = 1";
    
    $stmt = db_query($sql, [$email, $location, $social_instagram, $social_youtube, $social_linkedin], 'sssss');
    
    if ($stmt) {
        redirect_with_message('/Batool/admin/pages/contact-manager.php', 'Contact information updated successfully!', 'success');
    }
}

// Get current contact info
$contact = db_fetch_single("SELECT * FROM contact_info LIMIT 1");

include '../includes/header.php';
?>

<div class="content-box">
    <h2>Contact Information</h2>
    <p style="color: #666; margin-bottom: 30px;">
        Update your contact details and social media links that appear on your website.
    </p>
    
    <form method="POST">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   class="form-control" 
                   value="<?php echo h($contact['email'] ?? ''); ?>" 
                   required>
        </div>
        
        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" 
                   id="location" 
                   name="location" 
                   class="form-control" 
                   value="<?php echo h($contact['location'] ?? ''); ?>" 
                   required>
        </div>
        
        <hr style="margin: 40px 0; border: none; border-top: 2px solid var(--border-color);">
        
        <h3 style="font-family: 'Playfair Display', serif; margin-bottom: 25px; font-size: 1.4rem;">Social Media Links</h3>
        
        <div class="form-group">
            <label for="social_instagram">
                <i data-lucide="instagram" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;"></i>
                Instagram URL
            </label>
            <input type="url" 
                   id="social_instagram" 
                   name="social_instagram" 
                   class="form-control" 
                   value="<?php echo h($contact['social_instagram'] ?? ''); ?>" 
                   placeholder="https://instagram.com/yourprofile">
        </div>
        
        <div class="form-group">
            <label for="social_youtube">
                <i data-lucide="youtube" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;"></i>
                YouTube URL
            </label>
            <input type="url" 
                   id="social_youtube" 
                   name="social_youtube" 
                   class="form-control" 
                   value="<?php echo h($contact['social_youtube'] ?? ''); ?>" 
                   placeholder="https://youtube.com/yourchannel">
        </div>
        
        <div class="form-group">
            <label for="social_linkedin">
                <i data-lucide="linkedin" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;"></i>
                LinkedIn URL
            </label>
            <input type="url" 
                   id="social_linkedin" 
                   name="social_linkedin" 
                   class="form-control" 
                   value="<?php echo h($contact['social_linkedin'] ?? ''); ?>" 
                   placeholder="https://linkedin.com/in/yourprofile">
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

<?php include '../includes/footer.php'; ?>
