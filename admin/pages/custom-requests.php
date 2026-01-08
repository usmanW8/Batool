<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../config/database.php';

require_login();

$page_title = 'Custom Requests';
$current_page = 'custom-requests';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $request_id = $_POST['request_id'];
    $new_status = $_POST['status'];
    
    // Get request details before update
    $request = db_fetch_single("SELECT * FROM custom_requests WHERE id = ?", [$request_id], 'i');
    
    // Update status
    $sql = "UPDATE custom_requests SET status = ? WHERE id = ?";
    db_query($sql, [$new_status, $request_id], 'si');
    
    // Send status update email
    require_once '../includes/email-helper.php';
    require_once '../includes/email-status-updates.php';
    sendStatusUpdateEmail($request['email'], $request['order_id'], $request['customer_name'], $new_status, 'custom_request', []);
    
    redirect_with_message('/Batool/admin/pages/custom-requests.php', 'Status updated successfully! Email sent to customer.', 'success');
}

// Handle delete
if (isset($_GET['delete'])) {
    $request_id = $_GET['delete'];
    
    // Get image path and delete file
    $request = db_fetch_single("SELECT reference_image FROM custom_requests WHERE id = ?", [$request_id], 'i');
    if ($request && file_exists('../../' . $request['reference_image'])) {
        unlink('../../' . $request['reference_image']);
    }
    
    $sql = "DELETE FROM custom_requests WHERE id = ?";
    db_query($sql, [$request_id], 'i');
    
    redirect_with_message('/Batool/admin/pages/custom-requests.php', 'Request deleted successfully!', 'success');
}

// Get all custom requests
$requests = db_fetch_all("SELECT * FROM custom_requests ORDER BY created_at DESC");

include '../includes/header.php';
?>

<style>
    .status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-pending { background: #FEF3C7; color: #92400E; }
    .status-in_progress { background: #DBEAFE; color: #1E40AF; }
    .status-completed { background: #D1FAE5; color: #065F46; }
    .status-cancelled { background: #FEE2E2; color: #991B1B; }
    
    .request-card {
        background: white;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    
    .request-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-color: var(--primary-color);
    }
    
    .image-preview {
        max-width: 200px;
        max-height: 200px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
    }
    
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.9);
    }
    
    .modal img {
        margin: auto;
        display: block;
        max-width: 90%;
        max-height: 90vh;
    }
    
    .modal-close {
        position: absolute;
        top: 30px;
        right: 50px;
        color: white;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
    }
</style>

<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2>Custom Product Requests</h2>
            <p style="color: #666; margin-top: 5px;">
                View and manage customer customization requests
            </p>
        </div>
        <div style="font-size: 2rem; font-weight: bold; color: var(--primary-color);">
            <?php echo count($requests); ?> Requests
        </div>
    </div>
    
    <?php if (empty($requests)): ?>
        <div style="text-align: center; padding: 60px 20px; background: #f9f9f9; border-radius: 12px;">
            <i data-lucide="inbox" style="width: 80px; height: 80px; color: #ccc; margin: 0 auto 20px;"></i>
            <h3 style="color: #666; margin-bottom: 10px;">No Requests Yet</h3>
            <p style="color: #999;">Customer requests will appear here when submitted</p>
        </div>
    <?php else: ?>
        <?php foreach ($requests as $request): ?>
            <div class="request-card">
                <div style="display: grid; grid-template-columns: 1fr auto; gap: 30px;">
                    <!-- Request Details -->
                    <div>
                        <!-- Order ID Badge -->
                        <div style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 8px 16px; border-radius: 8px; font-weight: bold; font-size: 0.95rem; margin-bottom: 15px; letter-spacing: 0.5px;">
                            <i data-lucide="hash" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle; margin-right: 5px;"></i>
                            <?php echo h($request['order_id']); ?>
                        </div>
                        
                        <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 15px;">
                            <div style="flex: 1;">
                                <h3 style="margin: 0 0 5px 0; font-size: 1.3rem;">
                                    <?php echo h($request['customer_name']); ?>
                                </h3>
                                <p style="color: #666; font-size: 0.9rem; margin: 3px 0;">
                                    <i data-lucide="calendar" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i>
                                    <?php echo date('M d, Y - h:i A', strtotime($request['created_at'])); ?>
                                </p>
                            </div>
                            <span class="status-badge status-<?php echo $request['status']; ?>">
                                <?php echo str_replace('_', ' ', $request['status']); ?>
                            </span>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 20px 0;">
                            <div>
                                <label style="font-size: 0.85rem; color: #666; display: block; margin-bottom: 3px;">
                                    <i data-lucide="smartphone" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i>
                                    WhatsApp:
                                </label>
                                <p style="margin: 0; font-weight: 600;"><?php echo h($request['whatsapp_contact']); ?></p>
                            </div>
                            <div>
                                <label style="font-size: 0.85rem; color: #666; display: block; margin-bottom: 3px;">
                                    <i data-lucide="mail" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i>
                                    Email:
                                </label>
                                <p style="margin: 0; font-weight: 600;"><?php echo h($request['email']); ?></p>
                            </div>
                        </div>
                        
                        <div style="margin: 15px 0;">
                            <label style="font-size: 0.85rem; color: #666; display: block; margin-bottom: 5px;">
                                <i data-lucide="map-pin" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i>
                                Shipping Address:
                            </label>
                            <p style="margin: 0; padding: 10px; background: #f9f9f9; border-radius: 8px;">
                                <?php echo nl2br(h($request['shipping_address'])); ?>
                            </p>
                        </div>
                        
                        <?php if ($request['ingredients']): ?>
                            <div style="margin: 15px 0;">
                                <label style="font-size: 0.85rem; color: #666; display: block; margin-bottom: 5px;">
                                    <i data-lucide="package" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i>
                                    Materials/Ingredients:
                                </label>
                                <p style="margin: 0;"><?php echo nl2br(h($request['ingredients'])); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($request['additional_comments']): ?>
                            <div style="margin: 15px 0;">
                                <label style="font-size: 0.85rem; color: #666; display: block; margin-bottom: 5px;">
                                    <i data-lucide="message-square" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i>
                                    Additional Comments:
                                </label>
                                <p style="margin: 0; padding: 10px; background: #f9f9f9; border-radius: 8px;">
                                    <?php echo nl2br(h($request['additional_comments'])); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Actions -->
                        <div style="margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                <select name="status" class="form-control" style="display: inline-block; width: auto; padding: 8px 12px;" onchange="this.form.submit()">
                                    <option value="pending" <?php echo $request['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="in_progress" <?php echo $request['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="completed" <?php echo $request['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $request['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                            
                            <?php if ($request['reference_image']): ?>
                                <a href="../../<?php echo $request['reference_image']; ?>" download class="btn btn-secondary" style="padding: 8px 16px; text-decoration: none;">
                                    <i data-lucide="download" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;"></i>
                                    Download Image
                                </a>
                            <?php endif; ?>
                            
                            <a href="?delete=<?php echo $request['id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this request?')" 
                               style="color: #dc3545; text-decoration: none; padding: 8px 16px; border: 2px solid #dc3545; border-radius: 6px; font-weight: 600;">
                                <i data-lucide="trash-2" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;"></i>
                                Delete
                            </a>
                        </div>
                    </div>
                    
                    <!-- Reference Image -->
                    <?php if ($request['reference_image']): ?>
                        <div>
                            <label style="font-size: 0.85rem; color: #666; display: block; margin-bottom: 8px;">Reference Image:</label>
                            <img src="../../<?php echo $request['reference_image']; ?>" 
                                 alt="Reference" 
                                 class="image-preview"
                                 onclick="openImageModal('<?php echo $request['reference_image']; ?>')">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Image Modal -->
<div id="imageModal" class="modal">
    <span class="modal-close" onclick="closeImageModal()">&times;</span>
    <img id="modalImage" src="" alt="Full size">
</div>

<script>
    function openImageModal(imagePath) {
        document.getElementById('imageModal').style.display = 'block';
        document.getElementById('modalImage').src = '../../' + imagePath;
    }
    
    function closeImageModal() {
        document.getElementById('imageModal').style.display = 'none';
    }
    
    // Close modal on click outside
    window.onclick = function(event) {
        const modal = document.getElementById('imageModal');
        if (event.target === modal) {
            closeImageModal();
        }
    }
</script>

<?php include '../includes/footer.php'; ?>
