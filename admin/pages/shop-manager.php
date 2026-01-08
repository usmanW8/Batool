<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../config/database.php';

require_login();

$page_title = 'Shop Manager';
$current_page = 'shop';
$valid_tabs = ['categories', 'snugglets', 'products'];
$active_tab = isset($_GET['tab']) && in_array($_GET['tab'], $valid_tabs) ? $_GET['tab'] : 'categories';

// ==========================================
// HANDLE FORM SUBMISSIONS
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // --- CATEGORIES ---
    if ($action === 'manage_category') {
        $id = $_POST['id'] ?? null;
        $name = trim($_POST['name']);
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $display_order = intval($_POST['display_order']);
        
        $image_path = $_POST['current_image'] ?? '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = '../../img/shop/';
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            $filename = 'cat_' . time() . '_' . uniqid() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                $image_path = 'img/shop/' . $filename;
            }
        }

        if ($id) {
            db_query("UPDATE shop_categories SET name=?, slug=?, image_path=?, display_order=? WHERE id=?", [$name, $slug, $image_path, $display_order, $id], 'sssii');
            redirect_with_message("?tab=categories", "Category updated!", "success");
        } else {
            db_query("INSERT INTO shop_categories (name, slug, image_path, display_order) VALUES (?, ?, ?, ?)", [$name, $slug, $image_path, $display_order], 'sssi');
            redirect_with_message("?tab=categories", "Category added!", "success");
        }
    }

    // --- SNUGGLETS ---
    if ($action === 'manage_snugglet') {
        $id = $_POST['id'] ?? null;
        $name = trim($_POST['name']);
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $bg_color = $_POST['bg_color'] ?? '#ffffff';
        $display_order = intval($_POST['display_order']);
        
        $image_path = $_POST['current_image'] ?? '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = '../../img/shop/';
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            $filename = 'snug_' . time() . '_' . uniqid() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                $image_path = 'img/shop/' . $filename;
            }
        }

        if ($id) {
            db_query("UPDATE shop_snugglets SET name=?, slug=?, image_path=?, bg_color=?, display_order=? WHERE id=?", [$name, $slug, $image_path, $bg_color, $display_order, $id], 'ssssii');
            redirect_with_message("?tab=snugglets", "Snugglet updated!", "success");
        } else {
            db_query("INSERT INTO shop_snugglets (name, slug, image_path, bg_color, display_order) VALUES (?, ?, ?, ?, ?)", [$name, $slug, $image_path, $bg_color, $display_order], 'ssssi');
            redirect_with_message("?tab=snugglets", "Snugglet added!", "success");
        }
    }

    // --- PRODUCTS ---
    if ($action === 'manage_product') {
        $id = $_POST['id'] ?? null;
        $name = trim($_POST['name']);
        // New Fields
        $subtitle = trim($_POST['subtitle'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $detailed_description = trim($_POST['detailed_description'] ?? '');
        $materials = trim($_POST['materials'] ?? '');
        $dimensions = trim($_POST['dimensions'] ?? '');
        $is_sold_out = intval($_POST['is_sold_out'] ?? 0);
        
        $price = floatval($_POST['price']);
        $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
        $snugglet_id = !empty($_POST['snugglet_id']) ? intval($_POST['snugglet_id']) : null;
        
        $image_path = $_POST['current_image'] ?? '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = '../../img/shop/';
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            $filename = 'prod_' . time() . '_' . uniqid() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                $image_path = 'img/shop/' . $filename;
            }
        }

        if ($id) {
            db_query(
                "UPDATE shop_products SET name=?, subtitle=?, description=?, detailed_description=?, materials=?, dimensions=?, price=?, is_sold_out=?, image_path=?, category_id=?, snugglet_id=? WHERE id=?", 
                [$name, $subtitle, $description, $detailed_description, $materials, $dimensions, $price, $is_sold_out, $image_path, $category_id, $snugglet_id, $id], 
                'ssssssdissii'
            );
            redirect_with_message("?tab=products", "Product updated!", "success");
        } else {
            db_query(
                "INSERT INTO shop_products (name, subtitle, description, detailed_description, materials, dimensions, price, is_sold_out, image_path, category_id, snugglet_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
                [$name, $subtitle, $description, $detailed_description, $materials, $dimensions, $price, $is_sold_out, $image_path, $category_id, $snugglet_id], 
                'ssssssdissi'
            );
            redirect_with_message("?tab=products", "Product added!", "success");
        }
    }
}

// ==========================================
// HANDLE DELETES
// ==========================================
if (isset($_GET['delete']) && isset($_GET['type'])) {
    $id = intval($_GET['delete']);
    $type = $_GET['type'];
    
    if ($type === 'categories') {
        db_query("DELETE FROM shop_categories WHERE id=?", [$id], 'i');
        redirect_with_message("?tab=categories", "Category deleted!", "success");
    } elseif ($type === 'snugglets') {
        db_query("DELETE FROM shop_snugglets WHERE id=?", [$id], 'i');
        redirect_with_message("?tab=snugglets", "Snugglet deleted!", "success");
    } elseif ($type === 'products') {
        db_query("DELETE FROM shop_products WHERE id=?", [$id], 'i');
        redirect_with_message("?tab=products", "Product deleted!", "success");
    }
}

// ==========================================
// FETCH DATA
// ==========================================
$edit_item = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $table = 'shop_' . $active_tab;
    $edit_item = db_fetch_single("SELECT * FROM $table WHERE id=?", [$edit_id], 'i');
}

$categories = db_fetch_all("SELECT * FROM shop_categories ORDER BY display_order ASC");
$snugglets = db_fetch_all("SELECT * FROM shop_snugglets ORDER BY display_order ASC");
$products = db_fetch_all("SELECT p.*, c.name as cat_name, s.name as snug_name FROM shop_products p LEFT JOIN shop_categories c ON p.category_id = c.id LEFT JOIN shop_snugglets s ON p.snugglet_id = s.id ORDER BY p.id DESC");

include '../includes/header.php';
?>

<!-- TABS NAVIGATION -->
<div class="content-box" style="padding: 15px 30px; margin-bottom: 20px;">
    <div style="display: flex; gap: 20px;">
        <a href="?tab=categories" class="btn <?php echo $active_tab === 'categories' ? 'btn-primary' : 'btn-secondary'; ?>">Categories</a>
        <a href="?tab=snugglets" class="btn <?php echo $active_tab === 'snugglets' ? 'btn-primary' : 'btn-secondary'; ?>">Snugglets</a>
        <a href="?tab=products" class="btn <?php echo $active_tab === 'products' ? 'btn-primary' : 'btn-secondary'; ?>">Products</a>
    </div>
</div>

<!-- ================= CATEGORIES TAB ================= -->
<?php if ($active_tab === 'categories'): ?>
<div class="content-box">
    <h2><?php echo $edit_item ? 'Edit' : 'Add'; ?> Category</h2>
    <form method="POST" class="form" enctype="multipart/form-data">
        <input type="hidden" name="action" value="manage_category">
        <?php if ($edit_item): ?>
            <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
            <input type="hidden" name="current_image" value="<?php echo $edit_item['image_path']; ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo h($edit_item['name'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label>Display Order</label>
            <input type="number" name="display_order" class="form-control" value="<?php echo h($edit_item['display_order'] ?? 0); ?>">
        </div>
        <div class="form-group">
            <label>Image (Square Recommended)</label>
            <?php if ($edit_item && $edit_item['image_path']): ?>
                <img src="../../<?php echo $edit_item['image_path']; ?>" style="height: 50px; border-radius: 4px; display:block; margin-bottom:5px;">
            <?php endif; ?>
            <input type="file" name="image" class="form-control" accept="image/*" <?php echo $edit_item ? '' : 'required'; ?>>
        </div>
        <button type="submit" class="btn btn-primary"><?php echo $edit_item ? 'Update' : 'Add'; ?></button>
        <?php if ($edit_item): ?>
            <a href="?tab=categories" class="btn btn-secondary">Cancel</a>
        <?php endif; ?>
    </form>
    
    <h3 style="margin-top: 40px;">Manage Categories</h3>
    <table class="table">
        <thead><tr><th>Image</th><th>Name</th><th>Order</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
            <tr>
                <td><img src="../../<?php echo h($cat['image_path']); ?>" style="width: 40px; height: 40px; border-radius: 4px; object-fit:cover;"></td>
                <td><?php echo h($cat['name']); ?></td>
                <td><?php echo h($cat['display_order']); ?></td>
                <td>
                    <a href="?tab=categories&edit=<?php echo $cat['id']; ?>" class="btn btn-small btn-secondary">Edit</a>
                    <a href="?tab=categories&delete=<?php echo $cat['id']; ?>&type=categories" class="btn btn-small btn-danger" onclick="return confirm('Delete?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ================= SNUGGLETS TAB ================= -->
<?php elseif ($active_tab === 'snugglets'): ?>
<div class="content-box">
    <h2><?php echo $edit_item ? 'Edit' : 'Add'; ?> Snugglet</h2>
    <form method="POST" class="form" enctype="multipart/form-data">
        <input type="hidden" name="action" value="manage_snugglet">
        <?php if ($edit_item): ?>
            <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
            <input type="hidden" name="current_image" value="<?php echo $edit_item['image_path']; ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo h($edit_item['name'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label>Card Background Color (Hex)</label>
            <input type="color" name="bg_color" class="form-control" style="height: 50px;" value="<?php echo h($edit_item['bg_color'] ?? '#ffffff'); ?>">
        </div>
        <div class="form-group">
            <label>Display Order</label>
            <input type="number" name="display_order" class="form-control" value="<?php echo h($edit_item['display_order'] ?? 0); ?>">
        </div>
        <div class="form-group">
            <label>Image (Character Art)</label>
            <?php if ($edit_item && $edit_item['image_path']): ?>
                <img src="../../<?php echo $edit_item['image_path']; ?>" style="height: 50px; border-radius: 4px; display:block; margin-bottom:5px;">
            <?php endif; ?>
            <input type="file" name="image" class="form-control" accept="image/*" <?php echo $edit_item ? '' : 'required'; ?>>
        </div>
        <button type="submit" class="btn btn-primary"><?php echo $edit_item ? 'Update' : 'Add'; ?></button>
        <?php if ($edit_item): ?>
            <a href="?tab=snugglets" class="btn btn-secondary">Cancel</a>
        <?php endif; ?>
    </form>
    
    <h3 style="margin-top: 40px;">Manage Snugglets</h3>
    <table class="table">
        <thead><tr><th>Image</th><th>Name</th><th>Bg Color</th><th>Order</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($snugglets as $snug): ?>
            <tr>
                <td><img src="../../<?php echo h($snug['image_path']); ?>" style="width: 40px; height: 40px; object-fit:contain;"></td>
                <td><?php echo h($snug['name']); ?></td>
                <td>
                    <div style="width: 20px; height: 20px; border-radius: 50%; background: <?php echo h($snug['bg_color']); ?>; border: 1px solid #ddd;"></div>
                </td>
                <td><?php echo h($snug['display_order']); ?></td>
                <td>
                    <a href="?tab=snugglets&edit=<?php echo $snug['id']; ?>" class="btn btn-small btn-secondary">Edit</a>
                    <a href="?tab=snugglets&delete=<?php echo $snug['id']; ?>&type=snugglets" class="btn btn-small btn-danger" onclick="return confirm('Delete?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ================= PRODUCTS TAB ================= -->
<?php elseif ($active_tab === 'products'): ?>
<div class="content-box">
    <h2><?php echo $edit_item ? 'Edit' : 'Add'; ?> Product</h2>
    <form method="POST" class="form" enctype="multipart/form-data">
        <input type="hidden" name="action" value="manage_product">
        <?php if ($edit_item): ?>
            <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
            <input type="hidden" name="current_image" value="<?php echo $edit_item['image_path']; ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo h($edit_item['name'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Subtitle (e.g. "Meet Your New Travel Buddy")</label>
            <input type="text" name="subtitle" class="form-control" value="<?php echo h($edit_item['subtitle'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Price</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?php echo h($edit_item['price'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Availability Status</label>
            <select name="is_sold_out" class="form-control">
                <option value="0" <?php echo ($edit_item && $edit_item['is_sold_out'] == 0) ? 'selected' : ''; ?>>In Stock</option>
                <option value="1" <?php echo ($edit_item && $edit_item['is_sold_out'] == 1) ? 'selected' : ''; ?>>Sold Out</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Short Description (Card View)</label>
            <textarea name="description" class="form-control" rows="2"><?php echo h($edit_item['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label>Detailed Description (Product Page)</label>
            <textarea name="detailed_description" class="form-control" rows="5"><?php echo h($edit_item['detailed_description'] ?? ''); ?></textarea>
        </div>

        <div style="display: flex; gap: 20px;">
            <div class="form-group" style="flex: 1;">
                <label>Materials</label>
                <textarea name="materials" class="form-control" rows="3" placeholder="e.g. Handcrafted from cold porcelain clay..."><?php echo h($edit_item['materials'] ?? ''); ?></textarea>
            </div>
            <div class="form-group" style="flex: 1;">
                <label>Dimensions / Size</label>
                <textarea name="dimensions" class="form-control" rows="3" placeholder="e.g. 1 inch by 1 inch..."><?php echo h($edit_item['dimensions'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="form-group">
            <label>Assign to Category</label>
            <select name="category_id" class="form-control">
                <option value="">-- None --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo ($edit_item && $edit_item['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                        <?php echo h($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Assign to Snugglet Collection</label>
            <select name="snugglet_id" class="form-control">
                <option value="">-- None --</option>
                <?php foreach ($snugglets as $snug): ?>
                    <option value="<?php echo $snug['id']; ?>" <?php echo ($edit_item && $edit_item['snugglet_id'] == $snug['id']) ? 'selected' : ''; ?>>
                        <?php echo h($snug['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Product Image</label>
            <?php if ($edit_item && $edit_item['image_path']): ?>
                <img src="../../<?php echo $edit_item['image_path']; ?>" style="height: 50px; border-radius: 4px; display:block; margin-bottom:5px;">
            <?php endif; ?>
            <input type="file" name="image" class="form-control" accept="image/*" <?php echo $edit_item ? '' : 'required'; ?>>
        </div>
        <button type="submit" class="btn btn-primary"><?php echo $edit_item ? 'Update' : 'Add'; ?></button>
        <?php if ($edit_item): ?>
            <a href="?tab=products" class="btn btn-secondary">Cancel</a>
        <?php endif; ?>
    </form>
    
    <h3 style="margin-top: 40px;">Manage Products</h3>
    <table class="table">
        <thead><tr><th>Image</th><th>Name</th><th>Price</th><th>Status</th><th>Category</th><th>Snugglet</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($products as $prod): ?>
            <tr>
                <td><img src="../../<?php echo h($prod['image_path']); ?>" style="width: 40px; height: 40px; border-radius: 4px; object-fit:cover;"></td>
                <td><?php echo h($prod['name']); ?></td>
                <td>$<?php echo h($prod['price']); ?></td>
                <td>
                    <?php if ($prod['is_sold_out']): ?>
                        <span style="background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">Sold Out</span>
                    <?php else: ?>
                        <span style="background: #d1fae5; color: #065f46; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">In Stock</span>
                    <?php endif; ?>
                </td>
                <td><?php echo h($prod['cat_name'] ?? '-'); ?></td>
                <td><?php echo h($prod['snug_name'] ?? '-'); ?></td>
                <td>
                    <a href="?tab=products&edit=<?php echo $prod['id']; ?>" class="btn btn-small btn-secondary">Edit</a>
                    <a href="?tab=products&delete=<?php echo $prod['id']; ?>&type=products" class="btn btn-small btn-danger" onclick="return confirm('Delete?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
