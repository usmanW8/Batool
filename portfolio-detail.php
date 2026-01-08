<?php
session_start();
// Connect to database and fetch portfolio item
require_once 'admin/config/database.php';
require_once 'admin/includes/helpers.php';

// Function to generate unique order ID
function generateOrderId($prefix = 'ORD') {
    return $prefix . '-' . strtoupper(substr(uniqid(), -8)) . '-' . rand(1000, 9999);
}

// Handle portfolio product order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_portfolio_order'])) {
    $portfolio_item_id = intval($_POST['portfolio_item_id'] ?? 0);
    $product_title = trim($_POST['product_title'] ?? '');
    $product_image = trim($_POST['product_image'] ?? '');
    $product_price = trim($_POST['product_price'] ?? '');
    $product_dimensions = trim($_POST['product_dimensions'] ?? '');
    $product_materials = trim($_POST['product_materials'] ?? '');
    $customer_name = trim($_POST['customer_name'] ?? '');
    $whatsapp_contact = trim($_POST['whatsapp_contact'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $shipping_address = trim($_POST['shipping_address'] ?? '');
    
    $error = '';
    
    // Validation
    if (empty($customer_name) || empty($whatsapp_contact) || empty($email) || empty($shipping_address)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please provide a valid email address.';
    } else {
        // Generate unique order ID
        $order_id = generateOrderId('POR');
        
        // Save to database
        $sql = "INSERT INTO portfolio_products_order 
                (order_id, portfolio_item_id, product_title, product_image, product_price, product_dimensions, product_materials, customer_name, whatsapp_contact, email, shipping_address) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = db_query($sql, [
            $order_id, $portfolio_item_id, $product_title, $product_image, $product_price, $product_dimensions, $product_materials, $customer_name, $whatsapp_contact, $email, $shipping_address
        ], 'sisssssssss');
        
        if ($stmt) {
            // Send confirmation email
            require_once 'admin/includes/email-helper.php';
            $emailData = [
                'order_id' => $order_id,
                'customer_name' => $customer_name,
                'whatsapp_contact' => $whatsapp_contact,
                'email' => $email,
                'shipping_address' => $shipping_address,
                'product_title' => $product_title,
                'product_image' => $product_image,
                'product_price' => $product_price,
                'product_dimensions' => $product_dimensions,
                'product_materials' => $product_materials,
                'created_at' => date('Y-m-d H:i:s')
            ];
            sendPortfolioOrderConfirmation($email, $emailData);
            
            // Success - set session message and redirect
            $_SESSION['portfolio_order_success'] = true;
            header('Location: portfolio-detail.php?id=' . $portfolio_item_id);
            exit;
        } else {
            $error = 'Failed to submit order. Please try again.';
        }
    }
    
    if ($error) {
        echo "<script>alert(" . json_encode($error) . ");</script>";
    }
}

// Get item by ID or slug
$item = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $item = db_fetch_single("SELECT * FROM portfolio_items WHERE id = ?", [$id], 'i');
} elseif (isset($_GET['slug'])) {
    $slug = $_GET['slug'];
    $item = db_fetch_single("SELECT * FROM portfolio_items WHERE slug = ?", [$slug], 's');
}

// Redirect if item not found
if (!$item) {
    header('Location: /Batool/#portfolio');
    exit();
}

// Get category name
$category = db_fetch_single("SELECT * FROM portfolio_categories WHERE id = ?", [$item['category_id']], 'i');

// Helper functions
function e($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

// Convert YouTube URL to embed URL
function getYouTubeEmbedUrl($url) {
    if (empty($url)) return null;
    
    // Extract video ID from various YouTube URL formats
    // Supports: youtube.com/watch?v=ID, youtu.be/ID, youtube.com/embed/ID, youtube.com/shorts/ID
    preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([^&\?\/]+)/', $url, $matches);
    
    if (!empty($matches[1])) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    }
    
    return null;
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($item['title']); ?> - Batool's Aptitude</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'art-cream': '#FAF7F2',
                        'art-brown': '#3E3228',
                        'art-accent': '#A67B5B',
                        'art-beige': '#E5DCC5',
                        'art-dark-bg': '#292524',
                        'art-dark-card': '#292524',
                        'art-dark-text': '#E7E5E4'
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'serif'],
                        sans: ['"Lato"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-art-cream text-art-brown dark:bg-art-dark-bg dark:text-art-dark-text font-sans">

    <!-- Navigation Bar -->
    <nav class="fixed top-0 w-full z-50 bg-art-cream/95 dark:bg-art-dark-bg/95 backdrop-blur-md border-b border-art-brown/10 dark:border-white/10 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="/Batool/" class="flex-shrink-0 flex items-center">
                    <img src="img/Logo/batoollogo.png" alt="Batool's Aptitude" class="h-24 w-auto block transition-transform duration-300 ease-in-out hover:scale-110 hover:rotate-3">
                </a>
                
                <div class="flex items-center gap-4">
                    <a href="/Batool/#portfolio" class="text-sm uppercase tracking-wider hover:text-art-accent transition-colors">
                        ← Back to Portfolio
                    </a>
                    <button id="theme-toggle" class="p-2 rounded-full hover:bg-black/5 dark:hover:bg-white/10 transition-colors">
                        <i data-lucide="moon" class="w-5 h-5 block dark:hidden"></i>
                        <i data-lucide="sun" class="w-5 h-5 hidden dark:block"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-24 pb-12 min-h-screen">
        <div class="max-w-6xl mx-auto px-4">
            
            <!-- Breadcrumb -->
            <div class="mb-6 text-sm">
                <a href="/Batool/" class="text-art-accent hover:underline">Home</a>
                <span class="mx-2">/</span>
                <a href="/Batool/#portfolio" class="text-art-accent hover:underline">Portfolio</a>
                <span class="mx-2">/</span>
                <span class="opacity-60"><?php echo e($item['title']); ?></span>
            </div>

            <!-- Content Grid -->
            <div class="grid md:grid-cols-2 gap-8 items-start">
                
                <!-- Image Column -->
                <div class="space-y-4">
                    <div class="relative group cursor-pointer" onclick="openLightbox('<?php echo e($item['image_path']); ?>', '<?php echo e($item['title']); ?>')">
                        <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl">
                            <img src="<?php echo e($item['image_path']); ?>" 
                                 alt="<?php echo e($item['title']); ?>"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        </div>
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300 rounded-2xl flex items-center justify-center">
                            <i data-lucide="zoom-in" class="w-12 h-12 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300"></i>
                        </div>
                    </div>
                    
                    <?php if ($category): ?>
                    <div class="flex items-center gap-2 text-art-accent">
                        <i data-lucide="<?php echo e($category['icon_name']); ?>" class="w-5 h-5"></i>
                        <span class="font-medium"><?php echo e($category['category_name']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Details Column -->
                <div class="space-y-6">
                    
                    <!-- Title & Year -->
                    <div>
                        <h1 class="font-serif text-3xl md:text-4xl font-bold mb-3"><?php echo e($item['title']); ?></h1>
                        <div class="flex flex-wrap items-center gap-3 text-base opacity-75">
                            <span class="font-medium"><?php echo e($item['year']); ?></span>
                            <?php if ($item['materials']): ?>
                            <span>•</span>
                            <span><?php echo e($item['materials']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="prose prose-base dark:prose-invert max-w-none">
                        <p class="leading-relaxed">
                            <?php echo nl2br(e($item['long_description'] ?? $item['description'])); ?>
                        </p>
                    </div>

                    <!-- Specifications -->
                    <div class="border-t border-art-brown/10 dark:border-white/10 pt-5 space-y-3">
                        <?php if ($item['dimensions']): ?>
                        <div class="flex items-start gap-3">
                            <i data-lucide="maximize-2" class="w-4 h-4 text-art-accent mt-1"></i>
                            <div>
                                <div class="font-bold text-xs uppercase tracking-wider opacity-60 mb-1">Dimensions</div>
                                <div class="text-sm"><?php echo e($item['dimensions']); ?></div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($item['materials']): ?>
                        <div class="flex items-start gap-3">
                            <i data-lucide="palette" class="w-4 h-4 text-art-accent mt-1"></i>
                            <div>
                                <div class="font-bold text-xs uppercase tracking-wider opacity-60 mb-1">Materials</div>
                                <div class="text-sm"><?php echo e($item['materials']); ?></div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($item['status']): ?>
                        <div class="flex items-start gap-3">
                            <i data-lucide="info" class="w-4 h-4 text-art-accent mt-1"></i>
                            <div>
                                <div class="font-bold text-xs uppercase tracking-wider opacity-60 mb-1">Availability</div>
                                <div class="inline-block px-2 py-1 rounded-full text-xs font-medium
                                    <?php echo $item['status'] === 'available' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                              ($item['status'] === 'sold' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                              'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'); ?>">
                                    <?php echo ucfirst(e($item['status'])); ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($item['price'] && $item['price'] > 0): ?>
                        <div class="flex items-start gap-3">
                            <i data-lucide="tag" class="w-4 h-4 text-art-accent mt-1"></i>
                            <div>
                                <div class="font-bold text-xs uppercase tracking-wider opacity-60 mb-1">Price</div>
                                <div class="text-xl font-serif font-bold">$<?php echo number_format($item['price'], 2); ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 space-y-3">
                        <?php if ($item['status'] === 'available' && isset($item['price']) && $item['price'] > 0): ?>
                        <button onclick="openOrderModal()" 
                           class="block w-full text-center bg-art-accent hover:bg-art-brown text-white px-6 py-4 rounded-full transition-all text-sm uppercase tracking-widest font-bold shadow-lg transform hover:scale-105">
                            <i data-lucide="shopping-cart" class="w-5 h-5 inline-block mr-2"></i>
                            Buy Now - $<?php echo number_format($item['price'], 2); ?>
                        </button>
                        <?php endif; ?>
                        
                        <a href="/Batool/#portfolio" 
                           class="block w-full text-center border-2 border-art-brown dark:border-art-accent text-art-brown dark:text-art-accent px-6 py-3 rounded-full hover:bg-art-brown hover:text-white dark:hover:bg-art-accent dark:hover:text-white transition-all text-sm uppercase tracking-widest font-bold">
                            <i data-lucide="arrow-left" class="w-4 h-4 inline-block mr-2"></i>
                            Back to Portfolio
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- YouTube Video Section -->
        <?php 
        // Display YouTube video if URL exists
        $embedUrl = getYouTubeEmbedUrl($item['youtube_url'] ?? '');
        if ($embedUrl): 
        ?>
        <div class="container mx-auto px-4 md:px-8 py-12 max-w-7xl">
            <div class="bg-white dark:bg-art-dark-card rounded-3xl p-8 md:p-10 shadow-lg">
                <h2 class="text-3xl md:text-4xl font-serif font-bold mb-6 text-art-brown dark:text-art-beige flex items-center gap-3">
                    <i data-lucide="play-circle" class="w-8 h-8"></i>
                    Product Showcase Video
                </h2>
                <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 16px; box-shadow: 0 8px 30px rgba(0,0,0,0.2);">
                    <iframe 
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; border-radius: 16px;"
                        src="<?php echo e($embedUrl); ?>" 
                        title="Product Showcase Video"
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <!-- Order Form Modal -->
    <div id="orderModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;">
        <div class="bg-white dark:bg-art-dark-card rounded-3xl p-8 md:p-10 max-w-2xl mx-4 shadow-2xl transform scale-95 opacity-0 transition-all duration-300 max-h-[90vh] overflow-y-auto" id="orderModalContent">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-serif font-bold text-art-brown dark:text-art-beige">Place Your Order</h2>
                <button onclick="closeOrderModal()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <form method="POST" class="space-y-6">
                <!-- Product Info (Read-only) -->
                <div class="bg-art-beige/30 dark:bg-gray-800 rounded-2xl p-6 space-y-4">
                    <h3 class="font-bold text-lg mb-4">Product Details</h3>
                    
                    <div class="flex gap-4">
                        <img src="<?php echo e($item['image_path']); ?>" alt="<?php echo e($item['title']); ?>" class="w-24 h-24 object-cover rounded-lg">
                        <div class="flex-1">
                            <p class="font-bold text-lg"><?php echo e($item['title']); ?></p>
                            <p class="text-2xl font-serif font-bold text-art-accent mt-1">$<?php echo number_format($item['price'], 2); ?></p>
                        </div>
                    </div>

                    <?php if ($item['dimensions']): ?>
                    <div class="text-sm">
                        <span class="font-bold">Dimensions:</span> <?php echo e($item['dimensions']); ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($item['materials']): ?>
                    <div class="text-sm">
                        <span class="font-bold">Materials:</span> <?php echo e($item['materials']); ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Hidden Fields -->
                <input type="hidden" name="portfolio_item_id" value="<?php echo $item['id']; ?>">
                <input type="hidden" name="product_title" value="<?php echo e($item['title']); ?>">
                <input type="hidden" name="product_image" value="<?php echo e($item['image_path']); ?>">
                <input type="hidden" name="product_price" value="<?php echo e($item['price']); ?>">
                <input type="hidden" name="product_dimensions" value="<?php echo e($item['dimensions'] ?? ''); ?>">
                <input type="hidden" name="product_materials" value="<?php echo e($item['materials'] ?? ''); ?>">

                <!-- Customer Info -->
                <div class="space-y-4">
                    <h3 class="font-bold text-lg">Your Information</h3>

                    <div>
                        <label class="block text-sm font-bold mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="customer_name" 
                            required
                            class="w-full px-4 py-3 rounded-full border-2 border-art-accent/30 focus:border-art-accent focus:outline-none transition-all bg-white dark:bg-art-dark-card"
                            placeholder="Enter your full name"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">
                            WhatsApp Contact <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="tel" 
                            name="whatsapp_contact" 
                            required
                            class="w-full px-4 py-3 rounded-full border-2 border-art-accent/30 focus:border-art-accent focus:outline-none transition-all bg-white dark:bg-art-dark-card"
                            placeholder="+1 234 567 8900"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            required
                            class="w-full px-4 py-3 rounded-full border-2 border-art-accent/30 focus:border-art-accent focus:outline-none transition-all bg-white dark:bg-art-dark-card"
                            placeholder="your@email.com"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">
                            Shipping Address <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="shipping_address" 
                            required
                            rows="3"
                            class="w-full px-4 py-3 rounded-2xl border-2 border-art-accent/30 focus:border-art-accent focus:outline-none transition-all resize-none bg-white dark:bg-art-dark-card"
                            placeholder="Enter your complete shipping address"
                        ></textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-3">
                    <button 
                        type="submit"
                        name="submit_portfolio_order"
                        class="flex-1 bg-art-accent hover:bg-art-brown text-white px-8 py-4 rounded-full transition-all transform hover:scale-105 shadow-xl text-lg font-bold uppercase tracking-wider flex items-center justify-center gap-3"
                    >
                        <i data-lucide="check" class="w-5 h-5"></i>
                        <span>Confirm Order</span>
                    </button>
                    <button 
                        type="button"
                        onclick="closeOrderModal()"
                        class="px-8 py-4 border-2 border-gray-300 hover:border-gray-400 rounded-full transition-all font-bold"
                    >
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;">
        <div class="bg-white dark:bg-art-dark-card rounded-3xl p-8 md:p-12 max-w-md mx-4 shadow-2xl transform scale-95 opacity-0 transition-all duration-300" id="successModalContent">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full mb-6">
                    <svg class="w-12 h-12 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                
                <h2 class="text-3xl font-serif font-bold mb-4 text-art-brown dark:text-art-beige">Order Placed Successfully!</h2>
                <p class="text-lg opacity-80 mb-8">Thank you for your order. We'll contact you within 24-48 hours to confirm shipping details.</p>
                
                <button onclick="closeSuccessModal()" class="bg-art-brown hover:bg-art-accent text-white px-8 py-3 rounded-full transition-all transform hover:scale-105 shadow-lg font-bold uppercase tracking-wider">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Lightbox -->
    <div id="lightbox" class="lightbox">
        <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
        <img id="lightbox-img" class="lightbox-content">
        <div id="lightbox-caption" class="lightbox-caption"></div>
    </div>

    <script>
        // Initialize icons
        lucide.createIcons();

        // Dark mode toggle
        document.getElementById('theme-toggle').addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
            setTimeout(() => { lucide.createIcons(); }, 100);
        });

        // Order Modal Functions
        function openOrderModal() {
            const modal = document.getElementById('orderModal');
            const content = document.getElementById('orderModalContent');
            
            if (modal && content) {
                modal.style.display = 'flex';
                setTimeout(() => {
                    content.style.transform = 'scale(1)';
                    content.style.opacity = '1';
                    lucide.createIcons();
                }, 10);
            }
        }

        function closeOrderModal() {
            const modal = document.getElementById('orderModal');
            const content = document.getElementById('orderModalContent');
            
            if (content) {
                content.style.transform = 'scale(0.95)';
                content.style.opacity = '0';
            }
            
            setTimeout(() => {
                if (modal) modal.style.display = 'none';
            }, 300);
        }

        // Success Modal Functions
        function showSuccessModal() {
            const modal = document.getElementById('successModal');
            const content = document.getElementById('successModalContent');
            
            if (modal && content) {
                modal.style.display = 'flex';
                setTimeout(() => {
                    content.style.transform = 'scale(1)';
                    content.style.opacity = '1';
                }, 10);
            }
        }

        function closeSuccessModal() {
            const modal = document.getElementById('successModal');
            const content = document.getElementById('successModalContent');
            
            if (content) {
                content.style.transform = 'scale(0.95)';
                content.style.opacity = '0';
            }
            
            setTimeout(() => {
                if (modal) modal.style.display = 'none';
            }, 300);
        }

        // Close modals on backdrop click
        document.addEventListener('click', function(e) {
            const orderModal = document.getElementById('orderModal');
            const successModal = document.getElementById('successModal');
            
            if (e.target === orderModal) {
                closeOrderModal();
            }
            if (e.target === successModal) {
                closeSuccessModal();
            }
        });

        // Check for success message
        <?php
        if (isset($_SESSION['portfolio_order_success']) && $_SESSION['portfolio_order_success'] === true) {
            unset($_SESSION['portfolio_order_success']);
            echo "document.addEventListener('DOMContentLoaded', () => { setTimeout(() => showSuccessModal(), 300); });";
        }
        ?>

        // Lightbox functions
        function openLightbox(src, title) {
            document.getElementById('lightbox').classList.add('show');
            document.getElementById('lightbox-img').src = src;
            document.getElementById('lightbox-caption').innerText = title;
        }

        function closeLightbox() {
            document.getElementById('lightbox').classList.remove('show');
        }
    </script>
</body>
</html>
