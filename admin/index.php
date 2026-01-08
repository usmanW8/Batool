<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/helpers.php';
require_once 'config/database.php';

// Require authentication
require_login();

$page_title = 'Dashboard';
$current_page = 'dashboard';

// Get statistics
$custom_orders_count = db_fetch_single("SELECT COUNT(*) as count FROM custom_requests")['count'] ?? 0;
$shop_orders_count = db_fetch_single("SELECT COUNT(*) as count FROM shop_orders")['count'] ?? 0;
$portfolio_orders_count = db_fetch_single("SELECT COUNT(*) as count FROM portfolio_products_order")['count'] ?? 0;
$last_updated = db_fetch_single("SELECT MAX(updated_at) as last_update FROM hero_section")['last_update'] ?? 'N/A';

include 'includes/header.php';
?>

<div class="dashboard-grid">
    <div class="dashboard-card">
        <div class="dashboard-card-icon">
            <i data-lucide="package"></i>
        </div>
        <h3>Custom Orders</h3>
        <div class="dashboard-card-value"><?php echo $custom_orders_count; ?></div>
        <a href="/Batool/admin/pages/custom-requests.php" class="dashboard-card-link">
            View Orders <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
        </a>
    </div>

    <div class="dashboard-card">
        <div class="dashboard-card-icon">
            <i data-lucide="shopping-basket"></i>
        </div>
        <h3>Shop Orders</h3>
        <div class="dashboard-card-value"><?php echo $shop_orders_count; ?></div>
        <a href="/Batool/admin/pages/shop-orders.php" class="dashboard-card-link">
            View Orders <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
        </a>
    </div>
    
    <div class="dashboard-card">
        <div class="dashboard-card-icon">
            <i data-lucide="shopping-bag"></i>
        </div>
        <h3>Portfolio Orders</h3>
        <div class="dashboard-card-value"><?php echo $portfolio_orders_count; ?></div>
        <a href="/Batool/admin/pages/portfolio-orders.php" class="dashboard-card-link">
            View Orders <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
        </a>
    </div>
    
    <div class="dashboard-card">
        <div class="dashboard-card-icon">
            <i data-lucide="clock"></i>
        </div>
        <h3>Last Updated</h3>
        <div class="dashboard-card-value" style="font-size: 1.2rem;">
            <?php echo $last_updated !== 'N/A' ? format_date($last_updated) : 'N/A'; ?>
        </div>
        <a href="/Batool/admin/pages/hero-manager.php" class="dashboard-card-link">
            Update Content <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
        </a>
    </div>
</div>

<div class="content-box">
    <h2>Welcome to Your Admin Dashboard</h2>
    <p style="font-size: 1.05rem; line-height: 1.8; color: #666; margin-bottom: 25px;">
        Manage all aspects of your portfolio website from this dashboard. You can update images, edit content, 
        manage your portfolio items, and control all sections of your website.
    </p>
    
    <h3 style="font-family: 'Playfair Display', serif; margin: 30px 0 20px; font-size: 1.3rem;">Quick Actions</h3>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
        <a href="/Batool/admin/pages/hero-manager.php" class="btn btn-primary" style="display: block;">
            <i data-lucide="home" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;"></i>
            Edit Hero Section
        </a>
        <a href="/Batool/admin/pages/portfolio-manager.php" class="btn btn-secondary" style="display: block;">
            <i data-lucide="image" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;"></i>
            Manage Portfolio
        </a>
        <a href="/Batool/admin/pages/business-manager.php" class="btn btn-secondary" style="display: block;">
            <i data-lucide="briefcase" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;"></i>
            Business Images
        </a>
        <a href="/Batool/admin/pages/contact-manager.php" class="btn btn-secondary" style="display: block;">
            <i data-lucide="mail" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;"></i>
            Update Contact
        </a>
    </div>
</div>

<div class="content-box">
    <h2>Recent Activity</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Section</th>
                <th>Last Modified</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $recent_updates = [
                ['name' => 'Hero Section', 'table' => 'hero_section', 'url' => 'hero-manager.php'],
                ['name' => 'Portfolio Items', 'table' => 'portfolio_items', 'url' => 'portfolio-manager.php'],
                ['name' => 'Business Images', 'table' => 'business_images', 'url' => 'business-manager.php'],
                ['name' => 'Contact Info', 'table' => 'contact_info', 'url' => 'contact-manager.php'],
            ];
            
            foreach ($recent_updates as $section) {
                $last_update = db_fetch_single("SELECT MAX(updated_at) as last_update FROM {$section['table']}")['last_update'] ?? null;
                ?>
                <tr>
                    <td><strong><?php echo h($section['name']); ?></strong></td>
                    <td><?php echo $last_update ? format_datetime($last_update) : 'Never'; ?></td>
                    <td>
                        <a href="/Batool/admin/pages/<?php echo h($section['url']); ?>" class="btn btn-small btn-secondary">
                            Edit
                        </a>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
