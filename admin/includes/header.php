<?php
$admin_user = get_admin_user();
$page_title = $page_title ?? 'Admin Dashboard';

// Fetch logo from database
$admin_logo_result = db_fetch_single("SELECT logo_path FROM site_logo WHERE id = 1");
$admin_logo_path = $admin_logo_result['logo_path'] ?? 'img/Logo/batoollogo.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($page_title); ?> - Batool's Aptitude</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
    <link rel="stylesheet" href="/Batool/admin/assets/css/admin-style.css">
    
   <!-- Cropper.js for Image Cropping -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" rel="stylesheet">
    <link href="/Batool/admin/assets/css/cropper-custom.css" rel="stylesheet">
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <script src="/Batool/admin/assets/js/crop-config.js"></script>
    <script src="/Batool/admin/assets/js/image-cropper.js"></script>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-logo">
                <img src="/Batool/<?php echo h($admin_logo_path); ?>" alt="Batool's Aptitude" style="max-width: 180px; height: auto;">
            </div>
            
            <nav>
                <ul class="sidebar-nav">
                    <li>
                        <a href="/Batool/admin/index.php" class="<?php echo ($current_page ?? '') === 'dashboard' ? 'active' : ''; ?>">
                            <i data-lucide="layout-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="/Batool/admin/pages/logo-manager.php" class="<?php echo ($current_page ?? '') === 'logo' ? 'active' : ''; ?>">
                            <i data-lucide="image"></i>
                            <span>Logo Manager</span>
                        </a>
                    </li>
                    <li>
                        <a href="/Batool/admin/pages/hero-manager.php" class="<?php echo ($current_page ?? '') === 'hero' ? 'active' : ''; ?>">
                            <i data-lucide="home"></i>
                            <span>Hero Section</span>
                        </a>
                    </li>
                    <li>
                        <a href="/Batool/admin/pages/about-manager.php" class="<?php echo ($current_page ?? '') === 'about' ? 'active' : ''; ?>">
                            <i data-lucide="user"></i>
                            <span>About Section</span>
                        </a>
                    </li>
                    <li>
                        <a href="/Batool/admin/pages/portfolio-manager.php" class="<?php echo ($current_page ?? '') === 'portfolio' ? 'active' : ''; ?>">
                            <i data-lucide="image"></i>
                            <span>Portfolio</span>
                        </a>
                    </li>
                    <li>
                        <a href="/Batool/admin/pages/featured-manager.php" class="<?php echo ($current_page ?? '') === 'featured' ? 'active' : ''; ?>">
                            <i data-lucide="star"></i>
                            <span>Featured Portfolio</span>
                        </a>
                    </li>
                    <li>
                        <a href="/Batool/admin/pages/business-manager.php" class="<?php echo ($current_page ?? '') === 'business' ? 'active' : ''; ?>">
                            <i data-lucide="briefcase"></i>
                            <span>Business Section</span>
                        </a>
                    </li>
                    <li>
                        <a href="/Batool/admin/pages/digital-manager.php" class="<?php echo ($current_page ?? '') === 'digital' ? 'active' : ''; ?>">
                            <i data-lucide="monitor"></i>
                            <span>Digital Experience</span>
                        </a>
                    </li>
                    <li>
                        <a href="/Batool/admin/pages/culture-manager.php" class="<?php echo ($current_page ?? '') === 'culture' ? 'active' : ''; ?>">
                            <i data-lucide="globe"></i>
                            <span>Culture & Vision</span>
                        </a>
                    </li>
                    <li>
                        <a href="/Batool/admin/pages/contact-manager.php" class="<?php echo ($current_page ?? '') === 'contact' ? 'active' : ''; ?>">
                            <i data-lucide="mail"></i>
                            <span>Contact Info</span>
                        </a>
                    </li>
                    <li>
                        <a href="/Batool/admin/pages/custom-requests.php" class="<?php echo ($current_page ?? '') === 'custom-requests' ? 'active' : ''; ?>">
                            <i data-lucide="package"></i>
                            <span>Custom Requests</span>
                        </a>
                    </li>
                    <li>
                        <a href="/Batool/admin/pages/portfolio-orders.php" class="<?php echo ($current_page ?? '') === 'portfolio-orders' ? 'active' : ''; ?>">
                            <i data-lucide="shopping-bag"></i>
                            <span>Portfolio Orders</span>
                        </a>
                    </li>
                    <li>
                        <a href="/Batool/admin/pages/shop-orders.php" class="<?php echo ($current_page ?? '') === 'shop-orders' ? 'active' : ''; ?>">
                            <i data-lucide="shopping-basket"></i>
                            <span>Shop Orders</span>
                        </a>
                    </li>
                    <li>
                        <a href="/Batool/admin/pages/testimonials-manager.php" class="<?php echo ($current_page ?? '') === 'testimonials' ? 'active' : ''; ?>">
                            <i data-lucide="star"></i>
                            <span>Testimonials</span>
                        </a>
                    </li>
                    <li>
                    <a href="/Batool/admin/pages/shop-manager.php" class="<?php echo ($current_page ?? '') === 'shop' ? 'active' : ''; ?>">
                        <i data-lucide="shopping-bag"></i>
                        <span>Shop Manager</span>
                    </a>
                </li>
                <li>
                    <a href="/Batool/admin/pages/vlogs-manager.php" class="<?php echo ($current_page ?? '') === 'vlogs' ? 'active' : ''; ?>">
                            <i data-lucide="video"></i>
                            <span>YouTube Vlogs</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="sidebar-footer">
                <a href="/Batool/" target="_blank" style="color: rgba(255,255,255,0.6); text-decoration: none; display: block; margin-bottom: 15px; font-size: 0.9rem;">
                    <i data-lucide="external-link" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;"></i>
                    View Website
                </a>
                <form action="/Batool/admin/logout.php" method="POST">
                    <button type="submit" class="btn-logout">
                        <i data-lucide="log-out" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;"></i>
                        Logout
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1><?php echo h($page_title); ?></h1>
                <div class="admin-user">
                    <div class="admin-user-avatar">
                        <?php echo strtoupper(substr($admin_user['username'] ?? 'A', 0, 1)); ?>
                    </div>
                    <span><?php echo h($admin_user['username'] ?? 'Admin'); ?></span>
                </div>
            </div>
            
            <?php echo display_flash_message(); ?>
