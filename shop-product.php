<?php
session_start();
require_once 'admin/config/database.php';
require_once 'admin/includes/helpers.php';
require_once 'admin/includes/email-helper.php';

$id = intval($_GET['id'] ?? 0);
$product = db_fetch_single("SELECT * FROM shop_products WHERE id = ?", [$id], 'i');

if (!$product) {
    header('Location: index.php');
    exit;
}

// Get Category Name
$category = null;
if ($product['category_id']) {
    $category = db_fetch_single("SELECT name FROM shop_categories WHERE id = ?", [$product['category_id']], 'i');
}

// Generate Order ID
function generateOrderId() {
    return 'SHOP-' . strtoupper(substr(uniqid(), -5)) . '-' . rand(100, 999);
}

// Handle Order Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_shop_order'])) {
    $order_id = generateOrderId();
    $product_id = $product['id'];
    $product_name = $product['name'];
    $product_price = $product['sale_price'] > 0 ? $product['sale_price'] : $product['price'];
    
    $customer_name = trim($_POST['customer_name'] ?? '');
    $whatsapp = trim($_POST['whatsapp_contact'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['shipping_address'] ?? '');

    if ($customer_name && $whatsapp && $email && $address) {
        $sql = "INSERT INTO shop_orders (order_id, product_id, product_name, product_price, customer_name, whatsapp_contact, email, shipping_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = db_query($sql, [$order_id, $product_id, $product_name, $product_price, $customer_name, $whatsapp, $email, $address], 'sisdssss');

        if ($stmt) {
            // Prepare Email Data
            $emailData = [
                'order_id' => $order_id,
                'customer_name' => $customer_name,
                'whatsapp_contact' => $whatsapp,
                'email' => $email,
                'shipping_address' => $address,
                'product_name' => $product_name,
                'product_price' => '$' . number_format($product_price, 2),
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Send Email
            sendShopOrderConfirmation($email, $emailData);

            $_SESSION['shop_order_success'] = true;
            header("Location: shop-product.php?id=$id");
            exit;
        } else {
            $error = "Failed to place order. Please try again.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Batool's Aptitude</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
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
                        'art-dark-text': '#E7E5E4',
                        'art-dark-orange': '#78350F'
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'serif'],
                        sans: ['"Poppins"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-art-cream text-art-brown dark:bg-art-dark-bg dark:text-art-dark-text font-sans">
    
    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-art-cream/95 dark:bg-art-dark-bg/95 backdrop-blur-md border-b border-art-brown/10 dark:border-white/10 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="/Batool/" class="flex-shrink-0 flex items-center">
                    <img src="img/Logo/batoollogo.png" alt="Batool's Aptitude" class="h-24 w-auto block transition-transform duration-300 ease-in-out hover:scale-110 hover:rotate-3">
                </a>
                <div class="flex items-center gap-4">
                    <a href="javascript:history.back()" class="text-sm uppercase tracking-wider hover:text-art-accent transition-colors">
                        <i data-lucide="arrow-left" class="w-4 h-4 inline-block mr-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-32 pb-20 min-h-screen">
        <div class="max-w-6xl mx-auto px-4">
            
            <div class="grid md:grid-cols-2 gap-12 items-start">
                
                <!-- Image Column -->
                <div class="relative group">
                    <div class="aspect-square bg-white dark:bg-white/5 rounded-[40px] overflow-hidden shadow-2xl border-4 border-white dark:border-white/10">
                        <img src="<?php echo htmlspecialchars($product['image_path'] ?: 'img/shop/placeholder.jpg'); ?>" 
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                    <?php if ($product['is_sold_out']): ?>
                        <div class="absolute top-6 left-6 bg-black/80 text-white text-lg font-bold px-6 py-2 rounded-full backdrop-blur-md shadow-lg">Sold Out</div>
                    <?php else: ?>
                        <div class="absolute top-6 left-6 bg-art-dark-orange/90 text-white text-lg font-bold px-6 py-2 rounded-full backdrop-blur-md shadow-lg">In Stock</div>
                    <?php endif; ?>
                </div>

                <!-- Product Details -->
                <div class="space-y-8 pt-4">
                    <div>
                        <?php if ($category): ?>
                            <span class="text-art-accent font-bold tracking-widest text-sm uppercase mb-2 block"><?php echo htmlspecialchars($category['name']); ?></span>
                        <?php endif; ?>
                        
                        <h1 class="text-4xl md:text-5xl font-serif font-bold text-art-brown dark:text-art-beige mb-4 leading-tight">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h1>

                        <!-- Subtitle / Hook -->
                        <?php if (!empty($product['subtitle'])): ?>
                            <h2 class="text-xl font-serif italic text-art-accent mb-4">
                                <?php echo htmlspecialchars($product['subtitle']); ?>
                            </h2>
                        <?php endif; ?>
                        
                        <div class="flex items-baseline gap-4 mb-6">
                            <?php if ($product['sale_price'] > 0): ?>
                                <span class="text-3xl font-bold text-red-500">$<?php echo number_format($product['sale_price'], 2); ?></span>
                                <span class="text-xl text-gray-400 line-through">$<?php echo number_format($product['price'], 2); ?></span>
                            <?php else: ?>
                                <span class="text-3xl font-bold text-art-dark-orange dark:text-art-beige">$<?php echo number_format($product['price'], 2); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Buy Actions -->
                    <div>
                        <?php if (!$product['is_sold_out']): ?>
                            <button onclick="openOrderModal()" class="w-full md:w-auto bg-art-dark-orange hover:bg-art-accent text-white text-lg font-bold py-4 px-12 rounded-full shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                                <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                                Buy Now
                            </button>
                        <?php else: ?>
                            <button disabled class="w-full md:w-auto bg-gray-300 dark:bg-gray-700 text-gray-500 text-lg font-bold py-4 px-12 rounded-full cursor-not-allowed flex items-center justify-center gap-2">
                                <i data-lucide="slash" class="w-5 h-5"></i>
                                Sold Out
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Detailed Info Section -->
                    <div class="pt-8 space-y-8 divide-y divide-art-brown/10 dark:divide-white/10">
                        
                        <!-- Description -->
                        <div class="prose prose-lg dark:prose-invert opacity-90 leading-relaxed text-art-brown dark:text-art-dark-text">
                            <p><?php echo nl2br(htmlspecialchars($product['detailed_description'] ?: $product['description'])); ?></p>
                        </div>

                        <!-- Materials & Dimensions -->
                        <div class="pt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php if (!empty($product['materials'])): ?>
                            <div>
                                <h3 class="font-bold text-lg text-art-dark-orange dark:text-art-beige mb-2">Materials:</h3>
                                <ul class="list-disc list-inside opacity-80 text-sm leading-relaxed">
                                    <?php 
                                    // Split by newline and list
                                    $mats = explode("\n", $product['materials']);
                                    foreach($mats as $mat) {
                                        if(trim($mat)) echo "<li>" . htmlspecialchars(trim($mat)) . "</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($product['dimensions'])): ?>
                            <div>
                                <h3 class="font-bold text-lg text-art-dark-orange dark:text-art-beige mb-2">Size:</h3>
                                <ul class="list-disc list-inside opacity-80 text-sm leading-relaxed">
                                    <?php 
                                    $dims = explode("\n", $product['dimensions']);
                                    foreach($dims as $dim) {
                                        if(trim($dim)) echo "<li>" . htmlspecialchars(trim($dim)) . "</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Notes / Extras -->
                        <div class="pt-6">
                            <div class="bg-art-accent/10 dark:bg-white/5 p-6 rounded-2xl">
                                <h4 class="font-bold text-art-brown dark:text-art-beige mb-2 flex items-center gap-2">
                                    <i data-lucide="gift" class="w-4 h-4"></i>
                                    Unboxing Essential
                                </h4>
                                <p class="text-sm opacity-80 mb-4">
                                    Kindly record an uncut and unedited full unboxing video while opening the package. It'll help us validate and facilitate a replacement in case you ever receive a damaged product.
                                </p>

                                <h4 class="font-bold text-art-brown dark:text-art-beige mb-2 flex items-center gap-2">
                                    <i data-lucide="info" class="w-4 h-4"></i>
                                    Note
                                </h4>
                                <p class="text-sm opacity-80">
                                    Each product is handmade individually, making no two exactly alike. Variations from the displayed images are expected due to the handmade nature.
                                </p>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
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
                        <img src="<?php echo htmlspecialchars($product['image_path'] ?: 'img/shop/placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-24 h-24 object-cover rounded-lg">
                        <div class="flex-1">
                            <p class="font-bold text-lg"><?php echo htmlspecialchars($product['name']); ?></p>
                            <p class="text-2xl font-serif font-bold text-art-accent mt-1">$<?php echo number_format($product['sale_price'] > 0 ? $product['sale_price'] : $product['price'], 2); ?></p>
                        </div>
                    </div>

                    <?php if (!empty($product['dimensions'])): ?>
                    <div class="text-sm">
                        <span class="font-bold">Dimensions:</span>
                        <ul class="list-disc list-inside inline-block align-top ml-1">
                            <?php 
                            $dims = explode("\n", $product['dimensions']);
                            foreach($dims as $dim) {
                                if(trim($dim)) echo "<li>" . htmlspecialchars(trim($dim)) . "</li>";
                            }
                            ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($product['materials'])): ?>
                    <div class="text-sm">
                        <span class="font-bold">Materials:</span>
                        <ul class="list-disc list-inside inline-block align-top ml-1">
                            <?php 
                            $mats = explode("\n", $product['materials']);
                            foreach($mats as $mat) {
                                if(trim($mat)) echo "<li>" . htmlspecialchars(trim($mat)) . "</li>";
                            }
                            ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>

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
                        name="submit_shop_order"
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
                <p class="text-lg opacity-80 mb-8">Thank you for your order. We'll contact you within 24-48 hours via WhatsApp or Email to confirm shipping details.</p>
                
                <button onclick="closeSuccessModal()" class="bg-art-brown hover:bg-art-accent text-white px-8 py-3 rounded-full transition-all transform hover:scale-105 shadow-lg font-bold uppercase tracking-wider">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        // Initialize icons
        lucide.createIcons();

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
        if (isset($_SESSION['shop_order_success']) && $_SESSION['shop_order_success'] === true) {
            unset($_SESSION['shop_order_success']);
            echo "document.addEventListener('DOMContentLoaded', () => { setTimeout(() => showSuccessModal(), 300); });";
        }
        ?>
    </script>
</body>
</html>
