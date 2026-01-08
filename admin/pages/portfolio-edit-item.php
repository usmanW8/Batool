<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../config/database.php';

require_login();

$page_title = 'Edit Portfolio Item';
$current_page = 'portfolio';

// Get item ID
$item_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

if (!$item_id) {
    redirect_with_message('/Batool/admin/pages/portfolio-manager.php', 'Invalid item', 'error');
}

// Get item details
$item = db_fetch_single("SELECT * FROM portfolio_items WHERE id = ?", [$item_id], 'i');

if (!$item) {
    redirect_with_message('/Batool/admin/pages/portfolio-manager.php', 'Item not found', 'error');
}

// Get category for breadcrumb
$category = db_fetch_single("SELECT * FROM portfolio_categories WHERE id = ?", [$item['category_id']], 'i');

// Handle UPDATE request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $long_description = $_POST['long_description'] ?? '';
    $year = $_POST['year'] ?? '';
    $medium = $_POST['medium'] ?? '';
    $dimensions = $_POST['dimensions'] ?? '';
    $materials = $_POST['materials'] ?? '';
    $youtube_url = $_POST['youtube_url'] ?? '';
    $price = $_POST['price'] ?? 0;
    $status = $_POST['status'] ?? 'available';
    $display_order = $_POST['display_order'] ?? 0;
    
    // Auto-generate slug from title if not provided
    $slug = $_POST['slug'] ?? strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($title)));
    
    // Get current image path
    $image_path = $item['image_path'];
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_result = upload_image($_FILES['image'], 'img/portfolio/');
        if ($upload_result['success']) {
            $image_path = $upload_result['path'];
        }
    }
    
    $sql = "UPDATE portfolio_items SET 
            title = ?, 
            description = ?, 
            long_description = ?,
            slug = ?,
            image_path = ?, 
            year = ?, 
            medium = ?,
            dimensions = ?,
            materials = ?,
            youtube_url = ?,
            price = ?,
            status = ?,
            display_order = ?
            WHERE id = ?";
    
    $stmt = db_query($sql, [
        $title, $description, $long_description, $slug, $image_path, 
        $year, $medium, $dimensions, $materials, $youtube_url, $price, $status, 
        $display_order, $item_id
    ], 'ssssssssssdsii');
    
    if ($stmt) {
        redirect_with_message(
            '/Batool/admin/pages/portfolio-category.php?id=' . $item['category_id'], 
            'Portfolio item updated successfully!', 
            'success'
        );
    }
}

include '../includes/header.php';
?>

<div class="content-box">
    <!-- Breadcrumb -->
    <div style="margin-bottom: 20px; font-size: 0.9rem;">
        <a href="/Batool/admin/pages/portfolio-manager.php" style="color: #A67B5B; text-decoration: none;">Categories</a>
        <span style="margin: 0 8px; color: #999;">›</span>
        <a href="/Batool/admin/pages/portfolio-category.php?id=<?php echo $item['category_id']; ?>" style="color: #A67B5B; text-decoration: none;">
            <?php echo h($category['category_name']); ?>
        </a>
        <span style="margin: 0 8px; color: #999;">›</span>
        <span style="color: #666;"><?php echo h($item['title']); ?></span>
    </div>
    
    <h2>Edit: <?php echo h($item['title']); ?></h2>
    <p style="color: #666; margin-bottom: 30px;">Update the details for this portfolio item.</p>
    
    <form method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" 
                   id="title" 
                   name="title" 
                   class="form-control" 
                   value="<?php echo h($item['title']); ?>" 
                   required>
        </div>
        
        <div class="form-group">
            <label for="description">Short Description (for grid view)</label>
            <textarea id="description" 
                      name="description" 
                      class="form-control" 
                      rows="2"
                      placeholder="Brief 1-2 sentence description"><?php echo h($item['description']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="long_description">Long Description (for detail page)</label>
            <textarea id="long_description" 
                      name="long_description" 
                      class="form-control" 
                      rows="5"
                      placeholder="Full description with story, inspiration, techniques used..."><?php echo h($item['long_description']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="slug">URL Slug (auto-generated if empty)</label>
            <input type="text" 
                   id="slug" 
                   name="slug" 
                   class="form-control" 
                   value="<?php echo h($item['slug']); ?>" 
                   placeholder="e.g., ethereal-sketch-2023">
            <small style="color: #666; display: block; margin-top: 5px;">SEO-friendly URL. Leave empty to auto-generate from title.</small>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="year">Year</label>
                <input type="text" 
                       id="year" 
                       name="year" 
                       class="form-control" 
                       value="<?php echo h($item['year']); ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="medium">Medium</label>
                <input type="text" 
                       id="medium" 
                       name="medium" 
                       class="form-control" 
                       value="<?php echo h($item['medium']); ?>" 
                       required 
                       placeholder="e.g., Acrylic on Canvas">
            </div>
            
            <div class="form-group">
                <label for="dimensions">Dimensions</label>
                <input type="text" 
                       id="dimensions" 
                       name="dimensions" 
                       class="form-control" 
                       value="<?php echo h($item['dimensions']); ?>" 
                       placeholder="e.g., 24x36 inches or 60x90 cm">
            </div>
        </div>
        
        <div class="form-group">
            <label for="materials">Materials / Techniques</label>
            <input type="text" 
                   id="materials" 
                   name="materials" 
                   class="form-control" 
                   value="<?php echo h($item['materials']); ?>" 
                   placeholder="e.g., Acrylic on canvas, gold leaf, mixed media">
        </div>
        
        <div class="form-group">
            <label for="youtube_url">YouTube Video URL (optional)</label>
            <input type="url" 
                   id="youtube_url" 
                   name="youtube_url" 
                   class="form-control" 
                   value="<?php echo h($item['youtube_url'] ?? ''); ?>" 
                   placeholder="https://www.youtube.com/watch?v=... or https://youtu.be/...">
            <small style="color: #666; display: block; margin-top: 5px;">
                🎬 <strong>Product Demo Video:</strong> Add a YouTube link to showcase this product. 
                The video will be embedded on the product detail page below the "Buy Now" button.
            </small>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="price">Price (optional)</label>
                <input type="number" 
                       id="price" 
                       name="price" 
                       class="form-control" 
                       value="<?php echo h($item['price']); ?>" 
                       step="0.01"
                       placeholder="0.00">
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="available" <?php echo $item['status'] === 'available' ? 'selected' : ''; ?>>Available</option>
                    <option value="sold" <?php echo $item['status'] === 'sold' ? 'selected' : ''; ?>>Sold</option>
                    <option value="private" <?php echo $item['status'] === 'private' ? 'selected' : ''; ?>>Private Collection</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="display_order">Display Order</label>
            <input type="number" 
                   id="display_order" 
                   name="display_order" 
                   class="form-control" 
                   value="<?php echo h($item['display_order']); ?>">
            <small style="color: #666; display: block; margin-top: 5px;">Lower numbers appear first</small>
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
                <img src="<?php echo get_image_url($item['image_path']); ?>" 
                     alt="<?php echo h($item['title']); ?>">
            </div>
            
            <div class="image-preview-container" style="display: none;">
                <p style="font-weight: 600; margin-bottom: 10px; margin-top: 15px; font-size: 0.9rem;">New Image Preview:</p>
                <img id="image_preview" class="image-preview" style="display: none;">
            </div>
        </div>
        
        <div style="margin-top: 30px; display: flex; gap: 15px;">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;"></i>
                Update Item
            </button>
            <a href="/Batool/admin/pages/portfolio-category.php?id=<?php echo $item['category_id']; ?>" 
               class="btn btn-secondary" 
               style="text-decoration: none;">
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
