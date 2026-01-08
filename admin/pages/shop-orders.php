<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../config/database.php';

require_login();

$page_title = 'Shop Orders';
$current_page = 'shop-orders';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    // Get order details before update
    $order = db_fetch_single("SELECT * FROM shop_orders WHERE id = ?", [$order_id], 'i');
    
    // Update status
    $sql = "UPDATE shop_orders SET status = ? WHERE id = ?";
    db_query($sql, [$new_status, $order_id], 'si');
    
    // Send status update email
    require_once '../includes/email-helper.php';
    require_once '../includes/email-status-updates.php';
    $productInfo = [
        'product_title' => $order['product_name'],
        'product_price' => '$' . number_format($order['product_price'], 2)
    ];
    sendStatusUpdateEmail($order['email'], $order['order_id'], $order['customer_name'], $new_status, 'shop_order', $productInfo);
    
    redirect_with_message('/Batool/admin/pages/shop-orders.php', 'Status updated successfully! Email sent to customer.', 'success');
}

// Handle delete
if (isset($_GET['delete'])) {
    $order_id = $_GET['delete'];
    
    $sql = "DELETE FROM shop_orders WHERE id = ?";
    db_query($sql, [$order_id], 'i');
    
    redirect_with_message('/Batool/admin/pages/shop-orders.php', 'Order deleted successfully!', 'success');
}

// Get all shop orders with product image
$orders = db_fetch_all("
    SELECT o.*, p.image_path, p.subtitle, p.materials, p.dimensions 
    FROM shop_orders o 
    LEFT JOIN shop_products p ON o.product_id = p.id 
    ORDER BY o.created_at DESC
");

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
    .status-processing { background: #DBEAFE; color: #1E40AF; }
    .status-shipped { background: #E0E7FF; color: #3730A3; }
    .status-delivered { background: #D1FAE5; color: #065F46; }
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
</style>

<div class="content-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h2>Shop Orders</h2>
            <p style="color: #666; margin-top: 5px;">
                View and manage customer shop orders
            </p>
        </div>
        <div style="font-size: 2rem; font-weight: bold; color: var(--primary-color);">
            <?php echo count($orders); ?> Orders
        </div>
    </div>
    
    <?php if (empty($orders)): ?>
        <div style="text-align: center; padding: 60px 20px; background: #f9f9f9; border-radius: 12px;">
            <i data-lucide="shopping-basket" style="width: 80px; height: 80px; color: #ccc; margin: 0 auto 20px;"></i>
            <h3 style="color: #666; margin-bottom: 10px;">No Orders Yet</h3>
            <p style="color: #999;">Orders will appear here when customers purchase items from the shop</p>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="request-card">
                <div style="display: grid; grid-template-columns: 1fr auto; gap: 30px;">
                    <!-- Request Details -->
                    <div>
                        <!-- Order ID Badge -->
                        <div style="display: inline-block; background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); color: white; padding: 8px 16px; border-radius: 8px; font-weight: bold; font-size: 0.95rem; margin-bottom: 15px; letter-spacing: 0.5px;">
                            <i data-lucide="hash" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle; margin-right: 5px;"></i>
                            <?php echo h($order['order_id']); ?>
                        </div>
                        
                        <!-- Product Info -->
                        <div style="background: #f9f9f9; border-radius: 8px; padding: 15px; margin-bottom: 15px;">
                            <div style="display: flex; gap: 15px; align-items: center; margin-bottom: 10px;">
                                <?php if ($order['image_path']): ?>
                                <img src="../../<?php echo h($order['image_path']); ?>" alt="Product" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                <?php else: ?>
                                <div style="width: 80px; height: 80px; background: #eee; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i data-lucide="image-off" style="color: #999;"></i>
                                </div>
                                <?php endif; ?>
                                
                                <div style="flex: 1;">
                                    <h3 style="margin: 0 0 5px 0; font-size: 1.3rem;"><?php echo h($order['product_name']); ?></h3>
                                    <p style="font-size: 1.2rem; font-weight: bold; color: var(--primary-color); margin: 0;">
                                        $<?php echo number_format($order['product_price'], 2); ?>
                                    </p>
                                </div>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                                </span>
                            </div>
                            
                            <?php if ($order['subtitle']): ?>
                            <div style="font-size: 0.85rem; color: #666; margin-top: 5px; font-style: italic;">
                                "<?php echo h($order['subtitle']); ?>"
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Customer Info -->
                        <div style="margin-bottom: 15px;">
                            <h4 style="margin: 0 0 10px 0; font-size: 1rem; color: #666;">Customer Information</h4>
                            <div style="display: flex; justify-content: space-between; align-items: start;">
                                <div style="flex: 1;">
                                    <p style="margin: 0 0 5px 0; font-weight: 600;"><?php echo h($order['customer_name']); ?></p>
                                    <p style="color: #666; font-size: 0.9rem; margin: 0;">
                                        <i data-lucide="calendar" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i>
                                        <?php echo date('M d, Y - h:i A', strtotime($order['created_at'])); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <div>
                                <label style="font-size: 0.85rem; color: #666; display: block; margin-bottom: 3px;">
                                    <i data-lucide="smartphone" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i>
                                    WhatsApp:
                                </label>
                                <p style="margin: 0; font-weight: 600;"><?php echo h($order['whatsapp_contact']); ?></p>
                            </div>
                            <div>
                                <label style="font-size: 0.85rem; color: #666; display: block; margin-bottom: 3px;">
                                    <i data-lucide="mail" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i>
                                    Email:
                                </label>
                                <p style="margin: 0; font-weight: 600;"><?php echo h($order['email']); ?></p>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <label style="font-size: 0.85rem; color: #666; display: block; margin-bottom: 5px;">
                                <i data-lucide="map-pin" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i>
                                Shipping Address:
                            </label>
                            <p style="margin: 0; padding: 10px; background: #f9f9f9; border-radius: 8px;">
                                <?php echo nl2br(h($order['shipping_address'])); ?>
                            </p>
                        </div>
                        
                        <!-- Actions -->
                        <div style="margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                            <form method="POST" style="display: inline-block;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" class="form-control" style="display: inline-block; width: auto; padding: 8px 12px;" onchange="this.form.submit()">
                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                            
                            <a href="../../shop-product.php?id=<?php echo $order['product_id']; ?>" target="_blank" class="btn btn-secondary" style="padding: 8px 16px; text-decoration: none;">
                                <i data-lucide="external-link" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;"></i>
                                View Product
                            </a>
                            
                            <a href="?delete=<?php echo $order['id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this order?')" 
                               style="color: #dc3545; text-decoration: none; padding: 8px 16px; border: 2px solid #dc3545; border-radius: 6px; font-weight: 600;">
                                <i data-lucide="trash-2" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;"></i>
                                Delete
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
