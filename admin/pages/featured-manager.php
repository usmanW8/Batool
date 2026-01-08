<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../config/database.php';

require_login();

$page_title = 'Featured Portfolio Manager';
$current_page = 'featured';

// Handle UPDATE request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_featured'])) {
    $id = intval($_POST['id']);
    $title = $_POST['title'] ?? '';
    
    // Get current image path
    $current = db_fetch_single("SELECT image_path FROM featured_portfolio WHERE id = ?", [$id], 'i');
    $image_path = $current['image_path'];
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_result = upload_image($_FILES['image'], 'img/portfolio/');
        if ($upload_result['success']) {
            $image_path = $upload_result['path'];
        }
    }
    
    $sql = "UPDATE featured_portfolio SET title = ?, image_path = ? WHERE id = ?";
    $stmt = db_query($sql, [$title, $image_path, $id], 'ssi');
    
    if ($stmt) {
        redirect_with_message('/Batool/admin/pages/featured-manager.php', 'Featured item updated successfully!', 'success');
    }
}

// Get all featured items
$featured_items = db_fetch_all("SELECT * FROM featured_portfolio ORDER BY display_order");

// Get item being edited
$edit_item = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_item = db_fetch_single("SELECT * FROM featured_portfolio WHERE id = ?", [intval($_GET['edit'])], 'i');
}

include '../includes/header.php';
?>

<div class="content-box">
    <h2>Featured Portfolio Images</h2>
    <p style="color: #666; margin-bottom: 30px;">
        Manage the 6 featured portfolio images displayed on the homepage.
    </p>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
        <?php foreach ($featured_items as $item): ?>
        <div class="glass-card" style="padding: 15px; border-radius: 12px; border: 2px solid <?php echo ($edit_item && $edit_item['id'] == $item['id']) ? '#A67B5B' : 'transparent'; ?>;">
            <div style="aspect-ratio: 4/3; border-radius: 8px; overflow: hidden; margin-bottom: 12px;">
                <img src="<?php echo get_image_url($item['image_path']); ?>" 
                     alt="<?php echo h($item['title']); ?>"
                     style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <h3 style="margin: 0 0 10px; font-size: 1rem; color: #3E3228;">
                <?php echo h($item['title']); ?>
            </h3>
            <p style="margin: 0 0 12px; color: #666; font-size: 0.85rem;">
                Position: <?php echo $item['display_order']; ?>
            </p>
            <a href="?edit=<?php echo $item['id']; ?>" 
               class="btn btn-primary btn-sm" 
               style="text-decoration: none; width: 100%; text-align: center; display: inline-block; padding: 8px 16px; font-size: 0.9rem;">
                <i data-lucide="edit-2" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i>
                Edit
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($edit_item): ?>
<div class="content-box" style="margin-top: 30px;">
    <h2>Edit: <?php echo h($edit_item['title']); ?></h2>
    
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
        
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" 
                   id="title" 
                   name="title" 
                   class="form-control" 
                   value="<?php echo h($edit_item['title']); ?>" 
                   required>
        </div>
        
        <div class="form-group">
            <label for="image">Image (Leave empty to keep current)</label>
            <input type="file" 
                   id="image" 
                   name="image" 
                   class="form-control" 
                   accept="image/*"
                   onchange="previewImage(this, 'image_preview')">
            
            <div class="current-image">
                <p style="font-weight: 600; margin-bottom: 10px; margin-top: 15px; font-size: 0.9rem;">Current Image:</p>
                <img src="<?php echo get_image_url($edit_item['image_path']); ?>" 
                     alt="<?php echo h($edit_item['title']); ?>">
            </div>
            
            <div class="image-preview-container" style="display: none;">
                <p style="font-weight: 600; margin-bottom: 10px; margin-top: 15px; font-size: 0.9rem;">New Image Preview:</p>
                <img id="image_preview" class="image-preview" style="display: none;">
            </div>
        </div>
        
        <div style="margin-top: 20px;">
            <button type="submit" name="update_featured" class="btn btn-primary">
                <i data-lucide="save" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;"></i>
                Update Featured Item
            </button>
            <a href="/Batool/admin/pages/featured-manager.php" class="btn btn-secondary" style="margin-left: 10px; text-decoration: none;">
                Cancel
            </a>
        </div>
    </form>
</div>
<?php endif; ?>

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
