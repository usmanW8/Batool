<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../config/database.php';

require_login();

$page_title = 'Testimonials Manager';
$current_page = 'testimonials';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    db_query("DELETE FROM testimonials WHERE id = ?", [$id], 'i');
    redirect_with_message('/Batool/admin/pages/testimonials-manager.php', 'Testimonial deleted successfully!', 'success');
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $reviewer_name = trim($_POST['reviewer_name']);
    $review_text = trim($_POST['review_text']);
    $rating = intval($_POST['rating']);
    $display_order = intval($_POST['display_order']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if ($id) {
        // Update existing
        $sql = "UPDATE testimonials SET reviewer_name = ?, review_text = ?, rating = ?, display_order = ?, is_active = ? WHERE id = ?";
        db_query($sql, [$reviewer_name, $review_text, $rating, $display_order, $is_active, $id], 'ssiiii');
        redirect_with_message('/Batool/admin/pages/testimonials-manager.php', 'Testimonial updated successfully!', 'success');
    } else {
        // Add new
        $sql = "INSERT INTO testimonials (reviewer_name, review_text, rating, display_order, is_active) VALUES (?, ?, ?, ?, ?)";
        db_query($sql, [$reviewer_name, $review_text, $rating, $display_order, $is_active], 'ssiii');
        redirect_with_message('/Batool/admin/pages/testimonials-manager.php', 'Testimonial added successfully!', 'success');
    }
}

// Get testimonial for editing
$edit_testimonial = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_testimonial = db_fetch_single("SELECT * FROM testimonials WHERE id = ?", [$edit_id], 'i');
}

// Get all testimonials
$testimonials = db_fetch_all("SELECT * FROM testimonials ORDER BY display_order ASC, created_at DESC");

include '../includes/header.php';
?>

<div class="content-box">
    <h2><?php echo $edit_testimonial ? 'Edit' : 'Add'; ?> Testimonial</h2>
    
    <form method="POST" class="form" style="max-width: 800px;">
        <?php if ($edit_testimonial): ?>
        <input type="hidden" name="id" value="<?php echo $edit_testimonial['id']; ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label for="reviewer_name">Reviewer Name *</label>
            <input type="text" 
                   id="reviewer_name" 
                   name="reviewer_name" 
                   class="form-control" 
                   value="<?php echo h($edit_testimonial['reviewer_name'] ?? ''); ?>" 
                   required 
                   placeholder="e.g., Sarah M.">
        </div>
        
        <div class="form-group">
            <label for="review_text">Review Text *</label>
            <textarea id="review_text" 
                      name="review_text" 
                      class="form-control" 
                      rows="4" 
                      required 
                      placeholder="Enter the customer's review..."><?php echo h($edit_testimonial['review_text'] ?? ''); ?></textarea>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="rating">Rating *</label>
                <select id="rating" name="rating" class="form-control" required>
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($edit_testimonial && $edit_testimonial['rating'] == $i) ? 'selected' : ($i == 5 ? 'selected' : ''); ?>>
                        <?php echo $i; ?> Star<?php echo $i > 1 ? 's' : ''; ?>
                    </option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="display_order">Display Order</label>
                <input type="number" 
                       id="display_order" 
                       name="display_order" 
                       class="form-control" 
                       value="<?php echo h($edit_testimonial['display_order'] ?? 0); ?>" 
                       min="0">
                <small style="color: #666;">Lower numbers appear first</small>
            </div>
            
            <div class="form-group">
                <label for="is_active" style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" 
                           id="is_active" 
                           name="is_active" 
                           <?php echo (!$edit_testimonial || $edit_testimonial['is_active']) ? 'checked' : ''; ?>>
                    <span>Active (Show on website)</span>
                </label>
            </div>
        </div>
        
        <div style="display: flex; gap: 15px; margin-top: 25px;">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                <?php echo $edit_testimonial ? 'Update' : 'Add'; ?> Testimonial
            </button>
            <?php if ($edit_testimonial): ?>
            <a href="/Batool/admin/pages/testimonials-manager.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="content-box">
    <h2>All Testimonials (<?php echo count($testimonials); ?>)</h2>
    
    <?php if (empty($testimonials)): ?>
        <p style="color: #666; text-align: center; padding: 40px;">No testimonials yet. Add your first one above!</p>
    <?php else: ?>
        <div style="display: grid; gap: 20px;">
            <?php foreach ($testimonials as $testimonial): ?>
            <div style="background: #f9f9f9; border-radius: 12px; padding: 20px; border-left: 4px solid <?php echo $testimonial['is_active'] ? '#4CAF50' : '#999'; ?>;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                    <div>
                        <h3 style="margin: 0 0 5px 0;"><?php echo h($testimonial['reviewer_name']); ?></h3>
                        <div style="color: #f59e0b; font-size: 18px;">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <?php if ($i < $testimonial['rating']): ?>
                                    ★
                                <?php else: ?>
                                    ☆
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <span style="background: <?php echo $testimonial['is_active'] ? '#4CAF50' : '#999'; ?>; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">
                            <?php echo $testimonial['is_active'] ? 'ACTIVE' : 'INACTIVE'; ?>
                        </span>
                        <span style="background: #A67B5B; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">
                            Order: <?php echo $testimonial['display_order']; ?>
                        </span>
                    </div>
                </div>
                
                <p style="color: #666; line-height: 1.6; margin: 15px 0;">"<?php echo h($testimonial['review_text']); ?>"</p>
                
                <div style="display: flex; gap: 10px; margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
                    <a href="?edit=<?php echo $testimonial['id']; ?>" class="btn btn-small btn-secondary">
                        <i data-lucide="edit" style="width: 16px; height: 16px;"></i> Edit
                    </a>
                    <a href="?delete=<?php echo $testimonial['id']; ?>" 
                       class="btn btn-small btn-danger"
                       onclick="return confirm('Are you sure you want to delete this testimonial?')">
                        <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i> Delete
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
