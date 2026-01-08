<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../config/database.php';

require_login();

$page_title = 'About Section Manager';
$current_page = 'about';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sections = ['background', 'why_art', 'future'];
    
    foreach ($sections as $section) {
        $title = $_POST[$section . '_title'] ?? '';
        $content = $_POST[$section . '_content'] ?? '';
        
        $sql = "UPDATE about_content SET section_title = ?, content_text = ? WHERE section_name = ?";
        db_query($sql, [$title, $content, $section], 'sss');
    }
    
    redirect_with_message('/Batool/admin/pages/about-manager.php', 'About section updated successfully!', 'success');
}

// Get current about content
$about_sections = db_fetch_all("SELECT * FROM about_content ORDER BY display_order");

include '../includes/header.php';
?>

<div class="content-box">
    <h2>About Section Content</h2>
    <p style="color: #666; margin-bottom: 30px;">
        Edit the three main sections of your About page. Each section tells a part of your story.
    </p>
    
    <form method="POST">
        <?php foreach ($about_sections as $section): ?>
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

<?php include '../includes/footer.php'; ?>
