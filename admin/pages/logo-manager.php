<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../config/database.php';

require_login();

$page_title = 'Logo Manager';
$current_page = 'logo';

// Handle logo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['logo'])) {
    $file = $_FILES['logo'];
    
    // Validate file
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    if (!in_array($file['type'], $allowed_types)) {
        redirect_with_message('logo-manager.php', 'Invalid file type. Please upload JPG, PNG, GIF, or WebP.', 'error');
    }
    
    if ($file['size'] > $max_size) {
        redirect_with_message('logo-manager.php', 'File size must be less than 2MB.', 'error');
    }
    
    // Upload logo
    $upload_result = upload_image($file, 'img/Logo/', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    
    if ($upload_result['success']) {
        $logo_path = $upload_result['path'];
        
        // Update database
        $admin_id = $_SESSION['admin_id'] ?? null;
        $sql = "UPDATE site_logo SET logo_path = ?, updated_by = ? WHERE id = 1";
        db_query($sql, [$logo_path, $admin_id], 'si');
        
        redirect_with_message('logo-manager.php', 'Logo updated successfully!', 'success');
    } else {
        redirect_with_message('logo-manager.php', $upload_result['message'], 'error');
    }
}

// Fetch current logo
$logo = db_fetch_single("SELECT * FROM site_logo WHERE id = 1");

include '../includes/header.php';
?>

<div class="content-box">
    <h2>Logo Manager</h2>
    <p class="subtitle">Upload and manage your website logo. The logo will appear on both the frontend and admin dashboard.</p>
    
    <div class="logo-preview-section" style="background: #f5f5f5; padding: 30px; border-radius: 8px; margin: 20px 0; text-align: center;">
        <h3 style="margin-bottom: 20px; color: #4A3728;">Current Logo</h3>
        <div style="background: white; padding: 20px; border-radius: 8px; display: inline-block; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <img src="/Batool/<?php echo h($logo['logo_path'] ?? 'img/Logo/batoollogo.png'); ?>" 
                 alt="Current Logo" 
                 style="max-width: 300px; height: auto; display: block;">
        </div>
        <p style="margin-top: 15px; color: #666; font-size: 0.9rem;">
            <strong>Path:</strong> <?php echo h($logo['logo_path'] ?? 'img/Logo/batoollogo.png'); ?>
        </p>
        <?php if (!empty($logo['updated_at'])): ?>
        <p style="margin-top: 5px; color: #666; font-size: 0.9rem;">
            <strong>Last Updated:</strong> <?php echo format_datetime($logo['updated_at']); ?>
        </p>
        <?php endif; ?>
    </div>
    
    <form method="POST" enctype="multipart/form-data" style="max-width: 600px;">
        <div class="form-group">
            <label for="logo">Upload New Logo</label>
            <input type="file" 
                   id="logo" 
                   name="logo" 
                   accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" 
                   class="form-control crop-enabled" 
                   data-crop-type="logo"
                   required>
            <small class="form-text">
                Accepted formats: JPG, PNG, GIF, WebP • Max size: 2MB<br>
                <strong>Recommended:</strong> PNG with transparent background for best results
            </small>
        </div>
        
        <div class="alert alert-info" style="margin: 20px 0;">
            <strong>ⓘ Styling Information:</strong><br>
            • <strong>Frontend Navbar:</strong> Height: 96px, maintains aspect ratio, includes hover effects<br>
            • <strong>Admin Sidebar:</strong> Max width: 180px, height auto<br>
            • Your logo will automatically inherit these settings
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i data-lucide="upload" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;"></i>
            Upload Logo
        </button>
        <a href="../index.php" class="btn btn-secondary">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;"></i>
            Back to Dashboard
        </a>
    </form>
</div>

<script>
    // Preview file before upload
    document.getElementById('logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = document.querySelector('.logo-preview-section img');
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Initialize icons
    lucide.createIcons();
</script>

<?php include '../includes/footer.php'; ?>
