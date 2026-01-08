<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../config/database.php';

require_login();

$page_title = 'YouTube Vlogs Manager';
$current_page = 'vlogs';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    db_query("DELETE FROM youtube_vlogs WHERE id = ?", [$id], 'i');
    redirect_with_message('/Batool/admin/pages/vlogs-manager.php', 'Vlog deleted successfully!', 'success');
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $title = trim($_POST['title']);
    $video_url = trim($_POST['video_url']);
    $display_order = intval($_POST['display_order']);
    
    // Handle Image Upload
    $image_path = $_POST['current_image'] ?? '';
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === 0) {
        $upload_dir = '../../img/vlogs/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $filename = 'vlog_' . time() . '_' . uniqid() . '.' . pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $upload_dir . $filename)) {
            $image_path = 'img/vlogs/' . $filename;
        }
    }

    if ($id) {
        // Update
        $sql = "UPDATE youtube_vlogs SET title = ?, video_url = ?, thumbnail_path = ?, display_order = ? WHERE id = ?";
        db_query($sql, [$title, $video_url, $image_path, $display_order, $id], 'sssii');
        redirect_with_message('/Batool/admin/pages/vlogs-manager.php', 'Vlog updated successfully!', 'success');
    } else {
        // Add
        $sql = "INSERT INTO youtube_vlogs (title, video_url, thumbnail_path, display_order) VALUES (?, ?, ?, ?)";
        db_query($sql, [$title, $video_url, $image_path, $display_order], 'sssi');
        redirect_with_message('/Batool/admin/pages/vlogs-manager.php', 'Vlog added successfully!', 'success');
    }
}

// Get vlog for editing
$edit_vlog = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_vlog = db_fetch_single("SELECT * FROM youtube_vlogs WHERE id = ?", [$edit_id], 'i');
}

// Get all vlogs
$vlogs = db_fetch_all("SELECT * FROM youtube_vlogs ORDER BY display_order ASC");

include '../includes/header.php';
?>

<div class="content-box">
    <h2><?php echo $edit_vlog ? 'Edit' : 'Add'; ?> YouTube Vlog</h2>
    
    <form method="POST" class="form" enctype="multipart/form-data" style="max-width: 800px;">
        <?php if ($edit_vlog): ?>
        <input type="hidden" name="id" value="<?php echo $edit_vlog['id']; ?>">
        <input type="hidden" name="current_image" value="<?php echo $edit_vlog['thumbnail_path']; ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label for="title">Video Title *</label>
            <input type="text" id="title" name="title" class="form-control" 
                   value="<?php echo h($edit_vlog['title'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="video_url">YouTube Video URL *</label>
            <input type="url" id="video_url" name="video_url" class="form-control" 
                   value="<?php echo h($edit_vlog['video_url'] ?? ''); ?>" required
                   placeholder="https://youtube.com/...">
        </div>
        
        <div class="form-group">
            <label for="thumbnail">Thumbnail Image</label>
            <?php if ($edit_vlog && $edit_vlog['thumbnail_path']): ?>
                <div style="margin-bottom: 10px;">
                    <img src="../../<?php echo $edit_vlog['thumbnail_path']; ?>" alt="Current Thumbnail" style="height: 100px; border-radius: 8px;">
                </div>
            <?php endif; ?>
            <input type="file" id="thumbnail" name="thumbnail" class="form-control" accept="image/*" <?php echo $edit_vlog ? '' : 'required'; ?>>
            <small style="color: #666;">Recommended size: 400x300px (Aspect Ratio 4:3)</small>
        </div>
        
        <div class="form-group">
            <label for="display_order">Display Order</label>
            <input type="number" id="display_order" name="display_order" class="form-control" 
                   value="<?php echo h($edit_vlog['display_order'] ?? 0); ?>" min="0">
        </div>
        
        <div style="display: flex; gap: 15px; margin-top: 25px;">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                <?php echo $edit_vlog ? 'Update' : 'Add'; ?> Vlog
            </button>
            <?php if ($edit_vlog): ?>
            <a href="/Batool/admin/pages/vlogs-manager.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="content-box">
    <h2>My Vlogs (<?php echo count($vlogs); ?>)</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
        <?php foreach ($vlogs as $vlog): ?>
        <div style="background: #f9f9f9; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="height: 150px; overflow: hidden;">
                <img src="../../<?php echo h($vlog['thumbnail_path']); ?>" alt="<?php echo h($vlog['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div style="padding: 15px;">
                <h4 style="margin: 0 0 10px 0; font-size: 16px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo h($vlog['title']); ?></h4>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-size: 12px; background: #A67B5B; color: white; padding: 2px 8px; border-radius: 10px;">Order: <?php echo $vlog['display_order']; ?></span>
                </div>
                <div style="display: flex; gap: 5px;">
                    <a href="?edit=<?php echo $vlog['id']; ?>" class="btn btn-small btn-secondary" style="flex: 1; text-align: center;">Edit</a>
                    <a href="?delete=<?php echo $vlog['id']; ?>" class="btn btn-small btn-danger" style="flex: 1; text-align: center;" onclick="return confirm('Delete this vlog?')">Delete</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
