<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../config/database.php';

require_login();

$page_title = 'Portfolio Category';
$current_page = 'portfolio';

// Get category ID
$category_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

if (!$category_id) {
    redirect_with_message('/Batool/admin/pages/portfolio-manager.php', 'Invalid category', 'error');
}

// Get category details
$category = db_fetch_single("SELECT * FROM portfolio_categories WHERE id = ?", [$category_id], 'i');

if (!$category) {
    redirect_with_message('/Batool/admin/pages/portfolio-manager.php', 'Category not found', 'error');
}

// Get all items in this category
$items = db_fetch_all(
    "SELECT * FROM portfolio_items WHERE category_id = ? ORDER BY display_order, created_at DESC",
    [$category_id],
    'i'
);

include '../includes/header.php';
?>

<div class="content-box">
    <div style="margin-bottom: 20px;">
        <a href="/Batool/admin/pages/portfolio-manager.php" style="color: #A67B5B; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
            Back to Categories
        </a>
    </div>
    
    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
        <div style="background: #A67B5B20; padding: 15px; border-radius: 50%; color: #A67B5B;">
            <i data-lucide="<?php echo h($category['icon_name']); ?>" style="width: 32px; height: 32px;"></i>
        </div>
        <div>
            <h2 style="margin: 0;"><?php echo h($category['category_name']); ?></h2>
            <p style="margin: 5px 0 0; color: #666;">
                <?php echo count($items); ?> item<?php echo count($items) != 1 ? 's' : ''; ?> in this category
            </p>
        </div>
    </div>
    
    <?php if (empty($items)): ?>
        <p style="color: #666; text-align: center; padding: 40px 0;">
            No portfolio items in this category yet.
        </p>
    <?php else: ?>
    <div style="display: grid; gap: 20px;">
        <?php foreach ($items as $item): ?>
        <div class="glass-card" style="padding: 20px; border-radius: 12px; display: flex; gap: 20px; align-items: center;">
            <!-- Thumbnail -->
            <div style="width: 120px; height: 120px; border-radius: 8px; overflow: hidden; flex-shrink: 0;">
                <img src="<?php echo get_image_url($item['image_path']); ?>" 
                     alt="<?php echo h($item['title']); ?>"
                     style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            
            <!-- Info -->
            <div style="flex: 1;">
                <h3 style="margin: 0 0 10px; font-size: 1.25rem; color: #3E3228;">
                    <?php echo h($item['title']); ?>
                </h3>
                <div style="display: flex; gap: 15px; margin-bottom: 10px; color: #666; font-size: 0.9rem;">
                    <span><strong>Year:</strong> <?php echo h($item['year']); ?></span>
                    <span><strong>Medium:</strong> <?php echo h($item['medium']); ?></span>
                    <?php if ($item['status']): ?>
                    <span>
                        <strong>Status:</strong> 
                        <span style="color: <?php echo $item['status'] === 'available' ? '#059669' : ($item['status'] === 'sold' ? '#DC2626' : '#6B7280'); ?>;">
                            <?php echo ucfirst(h($item['status'])); ?>
                        </span>
                    </span>
                    <?php endif; ?>
                </div>
                <?php if ($item['description']): ?>
                <p style="margin: 0; color: #666; font-size: 0.9rem; line-height: 1.5;">
                    <?php echo h(substr($item['description'], 0, 150)) . (strlen($item['description']) > 150 ? '...' : ''); ?>
                </p>
                <?php endif; ?>
            </div>
            
            <!-- Actions -->
            <div>
                <a href="/Batool/admin/pages/portfolio-edit-item.php?id=<?php echo $item['id']; ?>" 
                   class="btn btn-primary"
                   style="text-decoration: none; white-space: nowrap;">
                    <i data-lucide="edit-2" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;"></i>
                    Edit Item
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
