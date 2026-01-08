<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../config/database.php';

require_login();

$page_title = 'Portfolio Manager';
$current_page = 'portfolio';

// Handle category update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $cat_id = intval($_POST['category_id']);
    $category_name = $_POST['category_name'] ?? '';
    $icon_name = $_POST['icon_name'] ?? '';
    
    $sql = "UPDATE portfolio_categories SET category_name = ?, icon_name = ? WHERE id = ?";
    $stmt = db_query($sql, [$category_name, $icon_name, $cat_id], 'ssi');
    
    if ($stmt) {
        redirect_with_message('/Batool/admin/pages/portfolio-manager.php', 'Category updated successfully!', 'success');
    }
}

// Get all categories with item counts
$categories_raw = db_fetch_all("SELECT * FROM portfolio_categories ORDER BY display_order");
$categories = [];

foreach ($categories_raw as $cat) {
    $count_result = db_fetch_single(
        "SELECT COUNT(*) as count FROM portfolio_items WHERE category_id = ?", 
        [$cat['id']], 
        'i'
    );
    $cat['item_count'] = $count_result['count'] ?? 0;
    $categories[] = $cat;
}

// Get category being edited
$edit_category = null;
if (isset($_GET['edit_category']) && is_numeric($_GET['edit_category'])) {
    $edit_category = db_fetch_single(
        "SELECT * FROM portfolio_categories WHERE id = ?", 
        [intval($_GET['edit_category'])], 
        'i'
    );
}

include '../includes/header.php';
?>

<div class="content-box">
    <h2>Portfolio Categories</h2>
    <p style="color: #666; margin-bottom: 30px;">
        Manage your portfolio by category. Click "View Items" to see and edit items in each category.
    </p>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        <?php foreach ($categories as $category): ?>
        <div class="glass-card" style="padding: 20px; border-radius: 12px; border: 2px solid <?php echo ($edit_category && $edit_category['id'] == $category['id']) ? '#A67B5B' : 'transparent'; ?>;">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                <div style="background: #A67B5B20; padding: 12px; border-radius: 50%; color: #A67B5B;">
                    <i data-lucide="<?php echo h($category['icon_name']); ?>" style="width: 24px; height: 24px;"></i>
                </div>
                <div style="flex: 1;">
                    <h3 style="margin: 0; font-size: 1.25rem; color: #3E3228;"><?php echo h($category['category_name']); ?></h3>
                    <p style="margin: 5px 0 0; color: #666; font-size: 0.9rem;">
                        <?php echo $category['item_count']; ?> item<?php echo $category['item_count'] != 1 ? 's' : ''; ?>
                    </p>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 15px;">
                <a href="/Batool/admin/pages/portfolio-category.php?id=<?php echo $category['id']; ?>" 
                   class="btn btn-primary" 
                   style="flex: 1; text-align: center; text-decoration: none;">
                    <i data-lucide="folder-open" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;"></i>
                    View Items
                </a>
                <a href="?edit_category=<?php echo $category['id']; ?>" 
                   class="btn btn-secondary"
                   style="text-decoration: none;">
                    <i data-lucide="edit-2" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;"></i>
                    Edit
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($edit_category): ?>
<div class="content-box" style="margin-top: 30px;">
    <h2>Edit Category: <?php echo h($edit_category['category_name']); ?></h2>
    
    <form method="POST">
        <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
        
        <div class="form-group">
            <label for="category_name">Category Name</label>
            <input type="text" 
                   id="category_name" 
                   name="category_name" 
                   class="form-control" 
                   value="<?php echo h($edit_category['category_name']); ?>" 
                   required>
        </div>
        
        <div class="form-group">
            <label for="icon_name">Icon Name (Lucide icon)</label>
            <input type="text" 
                   id="icon_name" 
                   name="icon_name" 
                   class="form-control" 
                   value="<?php echo h($edit_category['icon_name']); ?>" 
                   required
                   placeholder="e.g., pen-tool, gem, palette">
            <small style="color: #666; display: block; margin-top: 5px;">
                Browse icons at: <a href="https://lucide.dev/icons/" target="_blank">lucide.dev/icons</a>
            </small>
        </div>
        
        <div style="margin-top: 20px;">
            <button type="submit" name="update_category" class="btn btn-primary">
                <i data-lucide="save" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;"></i>
                Update Category
            </button>
            <a href="/Batool/admin/pages/portfolio-manager.php" class="btn btn-secondary" style="margin-left: 10px; text-decoration: none;">
                Cancel
            </a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
