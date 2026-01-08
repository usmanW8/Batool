<?php
session_start();
// Connect to database and fetch content
require_once 'admin/config/database.php';
require_once 'admin/includes/helpers.php';

// Function to generate unique order ID
function generateOrderId($prefix = 'ORD') {
    return $prefix . '-' . strtoupper(substr(uniqid(), -8)) . '-' . rand(1000, 9999);
}

// Handle custom request form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_custom_request'])) {
    $customer_name = trim($_POST['customer_name'] ?? '');
    $whatsapp_contact = trim($_POST['whatsapp_contact'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $shipping_address = trim($_POST['shipping_address'] ?? '');
    $ingredients = trim($_POST['ingredients'] ?? '');
    $additional_comments = trim($_POST['additional_comments'] ?? '');
    
    $error = '';
    
    // Validation
    if (empty($customer_name) || empty($whatsapp_contact) || empty($email) || empty($shipping_address)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please provide a valid email address.';
    } elseif (!isset($_FILES['reference_image']) || $_FILES['reference_image']['error'] !== 0) {
        $error = 'Please upload a reference image.';
    } else {
        // Handle image upload
        $upload_dir = __DIR__ . '/img/custom-requests/';
        $upload_dir_relative = 'img/custom-requests/';
        
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file = $_FILES['reference_image'];
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowed_types)) {
            $error = 'Please upload a JPG or PNG image.';
        } elseif ($file['size'] > $max_size) {
            $error = 'Image size must be less than 5MB.';
        } else {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'custom_' . time() . '_' . uniqid() . '.' . $extension;
            $filepath_absolute = $upload_dir . $filename;
            $filepath_relative = $upload_dir_relative . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath_absolute)) {
                // Generate unique order ID
                $order_id = generateOrderId('CRQ');
                
                // Save to database with relative path
                $sql = "INSERT INTO custom_requests 
                        (order_id, customer_name, whatsapp_contact, email, shipping_address, reference_image, ingredients, additional_comments) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = db_query($sql, [
                    $order_id, $customer_name, $whatsapp_contact, $email, $shipping_address, $filepath_relative, $ingredients, $additional_comments
                ], 'ssssssss');
                
                if ($stmt) {
                    // Send confirmation email
                    require_once 'admin/includes/email-helper.php';
                    $emailData = [
                        'order_id' => $order_id,
                        'customer_name' => $customer_name,
                        'whatsapp_contact' => $whatsapp_contact,
                        'email' => $email,
                        'shipping_address' => $shipping_address,
                        'ingredients' => $ingredients,
                        'additional_comments' => $additional_comments,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    sendCustomRequestConfirmation($email, $emailData);
                    
                    // Success - set session message and redirect
                    $_SESSION['custom_request_success'] = true;
                    header('Location: index.php#custom-request');
                    exit;
                } else {
                    $error = 'Failed to submit request. Please try again.';
                }
            } else {
                $error = 'Failed to upload image. Please check folder permissions.';
            }
        }
    }
    
    if ($error) {
        echo "<script>alert(" . json_encode($error) . "); window.location.href = 'index.php#custom-request';</script>";
        exit;
    }
}


// Fetch hero section content
$hero = db_fetch_single("SELECT * FROM hero_section WHERE id = 1");

// Fetch about content
$about_result = db_fetch_all("SELECT * FROM about_content ORDER BY display_order");
$about_sections = [];
foreach ($about_result as $row) {
    $about_sections[$row['section_name']] = $row;
}

// Fetch portfolio categories and items
$portfolio_categories = [];
$categories_result = db_fetch_all("SELECT * FROM portfolio_categories ORDER BY display_order");
foreach ($categories_result as $cat) {
    $cat['items'] = [];
    // Fetch items for this category
    $items_result = db_fetch_all("SELECT * FROM portfolio_items WHERE category_id = ? ORDER BY display_order", [$cat['id']], 'i');
    foreach ($items_result as $item) {
        $cat['items'][] = $item;
    }
    $portfolio_categories[] = $cat;
}

// Fetch featured portfolio for homepage
$featured_items = db_fetch_all("SELECT * FROM featured_portfolio ORDER BY display_order LIMIT 6");

// Fetch business images
$business_result = db_fetch_all("SELECT * FROM business_images ORDER BY display_order");
$business_images = [];
foreach ($business_result as $row) {
    $business_images[$row['image_type']] = $row;
}

// Fetch digital content
$digital_result = db_fetch_all("SELECT * FROM digital_content ORDER BY display_order");
$digital_content = [];
foreach ($digital_result as $row) {
    $digital_content[$row['content_type']] = $row;
}

$culture_result = db_fetch_all("SELECT * FROM culture_content ORDER BY id");
$culture_sections = [];
foreach ($culture_result as $row) {
    $culture_sections[$row['section_name']] = $row;
}

// Fetch YouTube Vlogs
$youtube_vlogs = db_fetch_all("SELECT * FROM youtube_vlogs ORDER BY display_order ASC LIMIT 6");

// Fetch Shop Data
$shop_categories = db_fetch_all("SELECT * FROM shop_categories ORDER BY display_order ASC");
$shop_snugglets = db_fetch_all("SELECT * FROM shop_snugglets ORDER BY display_order ASC");

// Fetch contact info
$contact = db_fetch_single("SELECT * FROM contact_info WHERE id = 1");

// Fetch logo from database
$logo_result = db_fetch_single("SELECT logo_path FROM site_logo WHERE id = 1");
$logo_path = $logo_result['logo_path'] ?? 'img/Logo/batoollogo.png';

// Helper function to safely output text
function e($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khansa Batool | Batool's Aptitude - Creative Artist & Cultural Entrepreneur</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind Config for Custom Colors -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'art-cream': '#FAF7F2',
                        'art-brown': '#3E3228',
                        'art-accent': '#A67B5B', // Terracotta/Brownish
                        'art-beige': '#E5DCC5',
                        'art-dark-bg': '#292524',
                        'art-dark-card': '#292524',
                        'art-dark-card': '#292524',
                        'art-dark-text': '#E7E5E4',
                        'art-dark-orange': '#78350F', // Dark Brown/Amber-900
                    },
                    fontFamily: {
                        serif: ['"Poppins"', 'sans-serif'], // Replacing serif with Poppins as requested
                        sans: ['"Poppins"', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.8s ease-out forwards',
                        'slide-up': 'slideUp 0.8s ease-out forwards',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>


    <!-- Custom Styles -->
    <link rel="stylesheet" href="css/styles.css">
</head>

<body
    class="bg-art-cream text-art-brown transition-colors duration-300 dark:bg-art-dark-bg dark:text-art-dark-text font-sans min-h-screen flex flex-col">

    <!-- Navigation Bar -->
    <nav
        class="fixed top-0 w-full z-50 bg-art-cream/95 dark:bg-art-dark-bg/95 backdrop-blur-md border-b border-art-brown/10 dark:border-white/10 transition-colors duration-300 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center cursor-pointer" onclick="navigateTo('home')">
                    <img src="<?php echo h($logo_path); ?>" alt="Batool's Aptitude" class="h-24 w-auto block transition-transform duration-300 ease-in-out hover:scale-110 hover:rotate-3">
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center relative" id="desktop-nav">
                    <div class="nav-item-wrapper" data-nav="home">
                        <button onclick="navigateTo('home')"
                            class="nav-link hover:text-art-accent transition-colors text-xs uppercase tracking-[0.15em] font-medium px-4">Home</button>
                    </div>
                    <div class="nav-item-wrapper" data-nav="about">
                        <button onclick="navigateTo('about')"
                            class="nav-link hover:text-art-accent transition-colors text-xs uppercase tracking-[0.15em] font-medium px-4">About</button>
                    </div>
                    <div class="nav-item-wrapper" data-nav="portfolio">
                        <button onclick="navigateTo('portfolio')"
                            class="nav-link hover:text-art-accent transition-colors text-xs uppercase tracking-[0.15em] font-medium px-4">Portfolio</button>
                    </div>

                    <div class="nav-item-wrapper dropdown relative group h-full flex items-center" data-nav="professional">
                        <button
                            class="nav-link hover:text-art-accent transition-colors text-xs uppercase tracking-[0.15em] font-medium flex items-center gap-1 px-4">
                            Professional <i data-lucide="chevron-down" class="w-3 h-3"></i>
                        </button>
                        <div class="dropdown-content">
                            <button onclick="navigateTo('business')"
                                class="block w-full text-left px-4 py-3 hover:bg-art-accent/10 hover:text-art-accent text-xs uppercase tracking-wider">Business</button>
                            <button onclick="navigateTo('digital')"
                                class="block w-full text-left px-4 py-3 hover:bg-art-accent/10 hover:text-art-accent text-xs uppercase tracking-wider">Digital
                                Exp.</button>
                            <button onclick="navigateTo('culture')"
                                class="block w-full text-left px-4 py-3 hover:bg-art-accent/10 hover:text-art-accent text-xs uppercase tracking-wider">Culture
                                & Vision</button>
                        </div>
                    </div>

                    <div class="nav-item-wrapper" data-nav="contact">
                        <button onclick="navigateTo('contact')"
                            class="nav-link hover:text-art-accent transition-colors text-xs uppercase tracking-[0.15em] font-medium px-4">Contact</button>
                    </div>

                    <!-- Animated Underline -->
                    <div id="nav-indicator" style="position: absolute; bottom: -4px; height: 3px; background: linear-gradient(90deg, #A67B5B, #D4A574); border-radius: 2px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); pointer-events: none; opacity: 0;"></div>

                    <!-- Dark Mode Toggle -->
                    <button id="theme-toggle"
                        class="p-2 rounded-full hover:bg-black/5 dark:hover:bg-white/10 transition-colors ml-4">
                        <i data-lucide="moon" class="w-5 h-5 block dark:hidden"></i>
                        <i data-lucide="sun" class="w-5 h-5 hidden dark:block"></i>
                    </button>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="theme-toggle-mobile"
                        class="p-2 mr-2 rounded-full hover:bg-black/5 dark:hover:bg-white/10 transition-colors">
                        <i data-lucide="moon" class="w-5 h-5 block dark:hidden"></i>
                        <i data-lucide="sun" class="w-5 h-5 hidden dark:block"></i>
                    </button>
                    <button onclick="toggleMobileMenu()" class="text-art-brown dark:text-art-dark-text p-2">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div id="mobile-menu"
            class="hidden md:hidden bg-art-cream dark:bg-art-dark-bg border-t border-gray-200 dark:border-gray-800 shadow-lg absolute w-full left-0 top-20">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 text-center font-serif">
                <button onclick="navigateTo('home')"
                    class="block w-full px-3 py-3 text-lg hover:text-art-accent">Home</button>
                <button onclick="navigateTo('about')"
                    class="block w-full px-3 py-3 text-lg hover:text-art-accent">About</button>
                <button onclick="navigateTo('portfolio')"
                    class="block w-full px-3 py-3 text-lg hover:text-art-accent">Portfolio</button>
                <div
                    class="border-t border-b border-art-brown/10 dark:border-white/10 py-2 bg-art-brown/5 dark:bg-white/5">
                    <button onclick="navigateTo('business')"
                        class="block w-full px-3 py-2 text-base hover:text-art-accent">Business</button>
                    <button onclick="navigateTo('digital')"
                        class="block w-full px-3 py-2 text-base hover:text-art-accent">Digital Experience</button>
                    <button onclick="navigateTo('culture')"
                        class="block w-full px-3 py-2 text-base hover:text-art-accent">Culture & Vision</button>
                </div>
                <button onclick="navigateTo('contact')"
                    class="block w-full px-3 py-3 text-lg hover:text-art-accent">Contact</button>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="flex-grow pt-20">

        <!-- PAGE 1: HOME -->
        <section id="home" class="page-section active">
            <!-- Hero Section -->
            <div class="relative min-h-screen flex items-center justify-center overflow-hidden">
                <div class="absolute inset-0 z-0 opacity-40 dark:opacity-20">
                    <!-- Background image from database -->
                    <img src="<?php echo e($hero['background_image'] ?? 'img/hero/hero-background.jpg'); ?>" 
                         class="w-full h-full object-cover filter blur-[2px]"
                         alt="Art Studio Background">
                    <!-- Dark overlay -->
                    <div class="absolute inset-0 bg-black/30"></div>
                </div>

                <div class="relative z-10 max-w-6xl mx-auto px-4 mt-4 mb-8 pb-4 grid grid-cols-12 gap-8 items-center">
                    <!-- Left: Main Content -->
                    <div class="col-span-12 md:col-span-8 text-left">

                        <h1 class="text-4xl md:text-5xl font-serif font-bold mb-5 leading-tight animate-slide-up text-art-brown dark:text-art-cream"
                            style="animation-delay: 0.1s;">
                            <?php echo nl2br(e($hero['main_title'] ?? 'Creative Artist & Emerging Cultural Entrepreneur')); ?>
                        </h1>
                        <p class="text-lg md:text-xl font-serif italic opacity-90 mb-6 text-art-brown dark:text-art-beige"
                            style="animation-delay: 0.2s;">
                            <span id="typewriter-text" class="typewriter-text" data-text="<?php echo e($hero['typewriter_text'] ?? 'Blending handmade art, digital creativity, and cultural expression.'); ?>"></span>
                        </p>
                    </div>
                    

                    
                    <!-- Centered Button (full width) -->
                    <div class="col-span-12 text-center">
                        <button onclick="navigateTo('portfolio')"
                            class="bg-art-brown dark:bg-art-accent text-white px-10 py-4 rounded-full hover:bg-art-accent dark:hover:bg-art-brown transition-all transform hover:-translate-y-1 shadow-xl animate-slide-up text-sm uppercase tracking-widest font-bold"
                            style="animation-delay: 0.3s;">
                            <?php echo e($hero['cta_text'] ?? 'View Portfolio'); ?>
                        </button>
                    </div>
                    
                    <!-- AI Image Generator Chatbox -->
                    <!-- AI Image Generator Chatbox -->
                    <div class="col-span-12 mt-2 flex justify-center animate-slide-up" style="animation-delay: 0.4s;">
                        <div class="w-full max-w-2xl rounded-3xl p-3 md:p-6">
                            <div class="text-center mb-4">
                                <div class="flex items-center justify-center gap-2 mb-2">
                                    <h3 class="text-sm font-bold uppercase tracking-widest text-art-brown dark:text-art-beige">AI Art Generator</h3>
                                </div>
                                <p class="text-xs opacity-80 italic">Convert your imagination into art</p>
                            </div>
                            <form id="ai-image-form" onsubmit="handleImageGeneration(event)" class="flex gap-2 md:gap-3">
                                <input 
                                    type="text" 
                                    id="ai-prompt-input"
                                    placeholder="Describe your imagination..." 
                                    class="flex-grow min-w-0 px-4 py-3 rounded-full bg-white/20 dark:bg-white/10 border border-white/40 dark:border-white/30 text-art-brown dark:text-art-beige placeholder-art-brown/50 dark:placeholder-art-beige/50 focus:outline-none focus:ring-2 focus:ring-art-accent focus:border-transparent transition-all"
                                    required
                                    maxlength="500"
                                />
                                <button 
                                    type="submit"
                                    class="flex-shrink-0 bg-art-accent hover:bg-art-brown dark:hover:bg-art-accent/80 text-white px-4 md:px-6 py-3 rounded-full transition-all transform hover:scale-105 shadow-lg flex items-center gap-2 font-bold text-sm uppercase tracking-wider"
                                >
                                    <i data-lucide="wand-2" class="w-4 h-4"></i>
                                    <span>Generate</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Identity -->
            <div class="py-24 bg-white dark:bg-white/5 relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-96 h-96 bg-art-accent/10 rounded-full filter blur-3xl opacity-50 dark:opacity-20">
                </div>

                <div class="max-w-5xl mx-auto px-4 grid md:grid-cols-2 gap-16 items-center relative z-10">
                    <div class="order-2 md:order-1 flex justify-center">
                        <div class="relative">
                            <div
                                class="absolute inset-0 border-4 border-art-accent/30 rounded-full transform translate-x-4 translate-y-4">
                            </div>
                            <!-- Artist Profile Image from database -->
                            <img src="<?php echo e($hero['profile_image'] ?? 'img/hero/artist-profile.jpg'); ?>" 
                                 alt="Artist working"
                                 class="rounded-full w-72 h-72 md:w-96 md:h-96 object-cover shadow-2xl relative z-10 border-8 border-art-cream dark:border-art-dark-bg">
                        </div>
                    </div>
                    <div class="order-1 md:order-2 space-y-8">
                        <h2 class="text-4xl font-serif font-bold text-art-brown dark:text-art-beige">My Identity</h2>
                        <p class="text-lg leading-relaxed font-light">
                            <?php echo nl2br(e($hero['identity_description'] ?? 'I am an artist with an IT background, developing handmade artworks while building a creative business through digital platforms. My work is inspired by culture, emotion, and identity, driven by a strong desire to manage creative systems globally.')); ?>
                        </p>
                        <div class="grid grid-cols-2 gap-y-4 gap-x-8 pt-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-art-brown/10 dark:bg-white/10 p-2 rounded-full text-art-accent"><i
                                        data-lucide="brush" class="w-5 h-5"></i></div>
                                <span class="font-bold uppercase tracking-wider text-sm">Artist</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="bg-art-brown/10 dark:bg-white/10 p-2 rounded-full text-art-accent"><i
                                        data-lucide="code" class="w-5 h-5"></i></div>
                                <span class="font-bold uppercase tracking-wider text-sm">IT Background</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="bg-art-brown/10 dark:bg-white/10 p-2 rounded-full text-art-accent"><i
                                        data-lucide="globe" class="w-5 h-5"></i></div>
                                <span class="font-bold uppercase tracking-wider text-sm">Culture</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="bg-art-brown/10 dark:bg-white/10 p-2 rounded-full text-art-accent"><i
                                        data-lucide="briefcase" class="w-5 h-5"></i></div>
                                <span class="font-bold uppercase tracking-wider text-sm">Digital Business</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Highlight Boxes -->
            <div class="py-16 px-4 bg-art-beige/30 dark:bg-black/20">
                <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-8">
                    <div
                        class="glass-card p-8 rounded-3xl text-center hover:-translate-y-2 transition-all duration-300 shadow-lg hover:shadow-xl border-b-4 border-transparent hover:border-art-accent group">
                        <div
                            class="w-20 h-20 bg-art-accent/10 group-hover:bg-art-accent/20 rounded-full flex items-center justify-center mx-auto mb-6 text-art-accent transition-colors">
                            <i data-lucide="palette" class="w-10 h-10"></i>
                        </div>
                        <h3 class="text-2xl font-serif font-bold mb-3">Creative Artist</h3>
                        <p class="opacity-80 leading-relaxed">Crafting unique visual stories through handmade excellence
                            and material exploration.</p>
                    </div>
                    <div
                        class="glass-card p-8 rounded-3xl text-center hover:-translate-y-2 transition-all duration-300 shadow-lg hover:shadow-xl border-b-4 border-transparent hover:border-art-accent group">
                        <div
                            class="w-20 h-20 bg-art-accent/10 group-hover:bg-art-accent/20 rounded-full flex items-center justify-center mx-auto mb-6 text-art-accent transition-colors">
                            <i data-lucide="store" class="w-10 h-10"></i>
                        </div>
                        <h3 class="text-2xl font-serif font-bold mb-3">Small Business Owner</h3>
                        <p class="opacity-80 leading-relaxed">Managing orders, pricing strategies, marketing campaigns,
                            and customer delight.</p>
                    </div>
                    <div
                        class="glass-card p-8 rounded-3xl text-center hover:-translate-y-2 transition-all duration-300 shadow-lg hover:shadow-xl border-b-4 border-transparent hover:border-art-accent group">
                        <div
                            class="w-20 h-20 bg-art-accent/10 group-hover:bg-art-accent/20 rounded-full flex items-center justify-center mx-auto mb-6 text-art-accent transition-colors">
                            <i data-lucide="lightbulb" class="w-10 h-10"></i>
                        </div>
                        <h3 class="text-2xl font-serif font-bold mb-3">Cultural Thinker</h3>
                        <p class="opacity-80 leading-relaxed">Preserving heritage while innovating for the future global
                            creative economy.</p>
                    </div>
                </div>
            </div>

            <!-- SHOP BY PRODUCT -->
            <div class="py-16 bg-white dark:bg-black/5">
                <div class="max-w-7xl mx-auto px-4">
                    <div class="text-left mb-12">
                        <h2 class="text-3xl font-serif font-bold text-art-dark-orange dark:text-art-beige">Shop by Product</h2>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                        <?php foreach ($shop_categories as $cat): ?>
                        <a href="shop-category.php?id=<?php echo $cat['id']; ?>" class="block text-center group">
                            <div class="aspect-square rounded-[30px] overflow-hidden mb-4 shadow-sm border border-transparent hover:border-art-accent transition-all shop-cat-card relative">
                                <img src="<?php echo htmlspecialchars($cat['image_path'] ?: 'img/shop/placeholder.jpg'); ?>" class="w-full h-full object-cover shop-category-img bg-gray-100" alt="<?php echo htmlspecialchars($cat['name']); ?>">
                            </div>
                            <h3 class="font-bold text-art-brown dark:text-art-beige group-hover:text-art-accent transition-colors flex items-center justify-center gap-1">
                                <?php echo htmlspecialchars($cat['name']); ?>
                                <i data-lucide="arrow-right" class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                            </h3>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- SHOP BY SNUGGLETS -->
            <div class="py-16 bg-white dark:bg-black/5">
                <div class="max-w-7xl mx-auto px-4">
                    <div class="text-left mb-12">
                        <h2 class="text-3xl font-serif font-bold text-art-dark-orange dark:text-art-beige">Shop by Snugglets</h2>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                        <?php foreach ($shop_snugglets as $snug): ?>
                        <a href="shop-snugglet.php?id=<?php echo $snug['id']; ?>" class="block text-center group">
                            <div class="aspect-square rounded-[30px] overflow-hidden mb-4 p-4 flex items-center justify-center shadow-sm snugglet-card relative" style="background-color: <?php echo htmlspecialchars($snug['bg_color'] ?: '#FFF'); ?>;">
                                <img src="<?php echo htmlspecialchars($snug['image_path'] ?: 'img/shop/placeholder.jpg'); ?>" class="w-full h-full object-contain snugglet-img" alt="<?php echo htmlspecialchars($snug['name']); ?>">
                            </div>
                            <h3 class="font-bold text-art-brown dark:text-art-beige group-hover:text-art-accent transition-colors flex items-center justify-center gap-1">
                                <?php echo htmlspecialchars($snug['name']); ?>
                                <i data-lucide="arrow-right" class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                            </h3>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Final Line -->
            <div class="py-28 text-center px-4 bg-art-accent/10 dark:bg-white/5">
                <h2 class="text-3xl md:text-4xl font-serif italic mb-10 text-art-brown dark:text-art-beige">"My goal is
                    to grow from artist to cultural leader."</h2>
                <button onclick="navigateTo('about')"
                    class="bg-transparent border-2 border-art-brown dark:border-art-beige text-art-brown dark:text-art-beige hover:bg-art-brown hover:text-white dark:hover:bg-art-beige dark:hover:text-art-brown px-10 py-4 rounded-full transition-all text-sm uppercase tracking-widest font-bold">
                    Explore My Journey
                </button>
            </div>

            <!-- YouTube Vlogs & Testimonials (Moved to Home Section) -->
            <div class="py-24 bg-white dark:bg-black/5">
                <div class="max-w-7xl mx-auto px-4">
                    <!-- Section: My YouTube Vlogs -->
                    <div class="mb-24">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-serif font-bold text-art-dark-orange dark:text-art-beige">My YouTube Vlogs</h2>
                    <p class="mt-4 text-art-accent opacity-80 font-serif italic text-lg">You can check out my latest YouTube vlogs below:</p>
                </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php if (empty($youtube_vlogs)): ?>
                                <p class="col-span-3 text-center opacity-60">No vlogs found. Please add them from the admin panel.</p>
                            <?php else: ?>
                                <?php foreach ($youtube_vlogs as $vlog): ?>
                                <a href="<?php echo htmlspecialchars($vlog['video_url']); ?>" target="_blank" class="block group relative rounded-2xl overflow-hidden shadow-lg transition-transform hover:-translate-y-2">
                                    <div class="aspect-video bg-gray-200 relative">
                                        <img src="<?php echo htmlspecialchars($vlog['thumbnail_path']); ?>" alt="<?php echo htmlspecialchars($vlog['title']); ?>" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                                            <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg">
                                                <i data-lucide="play" class="w-5 h-5 text-red-600 fill-current ml-1"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Section: WhimsyVille Review Board (Testimonials) -->
                    <?php
                    $testimonials = db_fetch_all("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC");
                    $total_reviews = count($testimonials);
                    
                    $avg_rating = 5;
                    if ($total_reviews > 0) {
                        $sum = 0;
                        foreach ($testimonials as $t) $sum += $t['rating'];
                        $avg_rating = round($sum / $total_reviews, 1);
                    }
                    ?>
                <div class="mb-24 text-center">
                    <h2 class="text-3xl md:text-4xl font-serif font-bold mb-2 text-art-dark-orange dark:text-art-beige">WhimsyVille Review Board</h2>
                    
                    <div class="flex items-center justify-center gap-1 mb-2 text-art-dark-orange dark:text-art-accent">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <i data-lucide="star" class="w-5 h-5 <?php echo $i <= round($avg_rating) ? 'fill-current' : ''; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        
                        <p class="text-sm opacity-70 mb-12">from <?php echo $total_reviews; ?> reviews <i data-lucide="check-circle" class="w-4 h-4 inline-block text-blue-500"></i></p>
                        
                        <!-- Carousel Container -->
                        <div class="relative max-w-7xl mx-auto px-12">
                        <!-- Arrows -->
                        <button id="prevReview" class="absolute left-0 top-1/2 -translate-y-1/2 text-gray-300 hover:text-art-dark-orange transition-colors">
                            <i data-lucide="chevron-left" class="w-10 h-10"></i>
                        </button>
                        <button id="nextReview" class="absolute right-0 top-1/2 -translate-y-1/2 text-gray-300 hover:text-art-dark-orange transition-colors">
                            <i data-lucide="chevron-right" class="w-10 h-10"></i>
                        </button>

                            <!-- Reviews Slider -->
                            <div id="reviews-slider" class="overflow-hidden">
                                <div class="flex transition-transform duration-500 ease-in-out" id="reviews-track">
                                    <?php 
                                    $chunks = array_chunk($testimonials, 4);
                                    foreach ($chunks as $chunk): 
                                    ?>
                                    <div class="w-full flex-shrink-0 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 px-4">
                                        <?php foreach ($chunk as $t): ?>
                                        <div class="text-center">
                                            <div class="flex justify-center gap-1 text-art-dark-orange dark:text-art-accent mb-3">
                                                <?php for($i=1; $i<=5; $i++): ?>
                                                    <i data-lucide="star" class="w-4 h-4 <?php echo $i <= $t['rating'] ? 'fill-current' : ''; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <h4 class="font-bold text-lg mb-2 text-art-dark-orange dark:text-art-beige"><?php echo htmlspecialchars($t['reviewer_name']); ?></h4>
                                            <h5 class="font-bold text-art-accent text-sm mb-2">
                                                <?php echo $t['rating'] == 5 ? 'Excellent!' : 'Great Review!'; ?>
                                            </h5>
                                            <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed mb-4">
                                                "<?php echo htmlspecialchars($t['review_text']); ?>"
                                            </p>
                                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider"><?php echo htmlspecialchars($t['reviewer_name']); ?></p>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const track = document.getElementById('reviews-track');
                            const prevBtn = document.getElementById('prevReview');
                            const nextBtn = document.getElementById('nextReview');
                            let currentIndex = 0;
                            // Ensure totalSlides logic works even if chunks is empty or singular
                            const totalSlides = <?php echo count($chunks) ?: 1; ?>;

                            function updateSlide() {
                                track.style.transform = `translateX(-${currentIndex * 100}%)`;
                            }

                            nextBtn.addEventListener('click', () => {
                                currentIndex = (currentIndex + 1) % totalSlides;
                                updateSlide();
                            });

                            prevBtn.addEventListener('click', () => {
                                currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
                                updateSlide();
                            });
                        });
                    </script>
                    
                    <!-- Email Signup -->
                    <div class="mb-24 text-center">
                        <div class="flex justify-center items-center gap-2 mb-4 text-2xl font-serif text-art-dark-orange dark:text-art-beige">
                            <span>*°✿°*</span>
                            <h3 class="font-bold">Want some whimsical emails?</h3>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-xl mx-auto text-sm leading-relaxed">
                            Join our tiny email community of whimsy hoomans for discounts, newsletters and stories from WhimsyVille! (+You'll get a 10% discount coupon.)
                        </p>
                        <form onsubmit="event.preventDefault(); alert('Subscribed!');" class="max-w-md mx-auto relative">
                            <input type="email" placeholder="Email" class="w-full pl-6 pr-12 py-3 rounded-full border border-art-brown/20 dark:border-white/20 focus:outline-none focus:border-art-accent bg-transparent text-art-brown dark:text-art-beige">
                            <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-art-brown transition-colors p-2">
                                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- PAGE 2: ABOUT ME -->
        <section id="about" class="page-section">
            <div class="max-w-4xl mx-auto px-4 py-20">
                <h1
                    class="text-4xl md:text-6xl font-serif font-bold mb-16 text-center text-art-brown dark:text-art-beige relative">
                    My Story
                    <span class="block w-20 h-2 bg-art-accent mx-auto mt-4 rounded-full"></span>
                </h1>

                <div class="space-y-20">
                    <div class="flex flex-col md:flex-row gap-10 items-start">
                        <div class="md:w-1/3">
                            <h3 class="text-2xl font-serif font-bold text-art-accent mb-2">Background & Education</h3>
                            <div class="h-1 w-12 bg-art-brown/20 dark:bg-white/20"></div>
                        </div>
                        <div class="md:w-2/3 glass-card p-8 rounded-2xl shadow-sm">
                            <p class="text-lg leading-relaxed opacity-90 font-light">
                                I began my journey in the world of Information Technology, gaining a structured
                                understanding of digital systems. However, my heart always leaned towards creation.
                                Combining my technical education with my passion for art, I bridge the gap between
                                traditional craftsmanship and modern digital presence. This dual background allows me to
                                approach art not just as expression, but as a scalable system.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row gap-10 items-start">
                        <div class="md:w-1/3">
                            <h3 class="text-2xl font-serif font-bold text-art-accent mb-2">Why I Love Art</h3>
                            <div class="h-1 w-12 bg-art-brown/20 dark:bg-white/20"></div>
                        </div>
                        <div class="md:w-2/3 glass-card p-8 rounded-2xl shadow-sm">
                            <p class="text-lg leading-relaxed opacity-90 font-light">
                                Art is more than just aesthetics; it is a language of emotion and heritage. It allows me
                                to express unspoken cultural narratives and connect with people on a human level. The
                                tactile nature of clay, the flow of paint, and the structure of design bring me a sense
                                of purpose that nothing else does. It is the medium through which I explore identity and
                                connect with the world.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row gap-10 items-start">
                        <div class="md:w-1/3">
                            <h3 class="text-2xl font-serif font-bold text-art-accent mb-2">Future Vision</h3>
                            <div class="h-1 w-12 bg-art-brown/20 dark:bg-white/20"></div>
                        </div>
                        <div class="md:w-2/3 glass-card p-8 rounded-2xl shadow-sm">
                            <p class="text-lg leading-relaxed opacity-90 font-light">
                                My ambition extends beyond the studio. I aim to evolve into a global cultural leader,
                                curating spaces where tradition meets innovation. I envision leading platforms that
                                empower other artists, using my IT background to build sustainable systems for the
                                creative economy. My goal is to foster global creativity while ensuring cultural
                                responsibility remains at the core of entrepreneurial growth.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PAGE 3: PORTFOLIO -->
        <section id="portfolio" class="page-section">
            <div class="max-w-7xl mx-auto px-4 py-12">
                <div
                    class="flex flex-col md:flex-row justify-between items-end mb-16 border-b border-art-brown/10 dark:border-white/10 pb-6">
                    <div>
                        <h1 class="text-5xl md:text-6xl font-serif font-bold text-art-brown dark:text-art-beige">
                            Portfolio</h1>
                        <p class="mt-4 text-xl opacity-70 font-serif italic">A curated collection of my artistic
                            journey.</p>
                    </div>
                    <button onclick="navigateTo('business')"
                        class="mt-6 md:mt-0 px-6 py-2 border border-art-accent text-art-accent hover:bg-art-accent hover:text-white transition-all rounded-full text-sm uppercase tracking-widest font-bold">
                        View Business Side
                    </button>
                </div>

                <!-- Dynamic Portfolio Grid from Database -->
                <?php foreach ($portfolio_categories as $category): ?>
                <?php if (!empty($category['items'])): ?>
                <div class="mb-20">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="p-3 bg-art-accent/10 rounded-full text-art-accent">
                            <i data-lucide="<?php echo e($category['icon_name']); ?>" class="w-6 h-6"></i>
                        </div>
                        <h2 class="text-3xl font-serif font-bold text-art-brown dark:text-art-beige">
                            <?php echo e($category['category_name']); ?>
                        </h2>
                    </div>
                    <div class="grid md:grid-cols-3 gap-8">
                        <?php foreach ($category['items'] as $item): ?>
                        <a href="portfolio-detail.php?id=<?php echo $item['id']; ?>" 
                           class="bg-white dark:bg-art-dark-card rounded-2xl shadow-lg overflow-hidden group cursor-pointer hover:shadow-2xl transition-all duration-300 block">
                            <div class="h-72 overflow-hidden">
                                <img src="<?php echo e($item['image_path']); ?>"
                                     alt="<?php echo e($item['title']); ?>"
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            </div>
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-2">
                                    <h3 class="font-serif font-bold text-xl"><?php echo e($item['title']); ?></h3>
                                    <span class="text-xs font-bold text-art-accent border border-art-accent px-2 py-1 rounded">
                                        <?php echo e($item['year']); ?>
                                    </span>
                                </div>
                                <p class="text-sm opacity-70 mb-4"><?php echo e($item['materials'] ?: $item['medium']); ?></p>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
                
                <!-- Custom Product CTA -->
                <div class="text-center mt-20">
                    <div class="inline-block p-8 bg-gradient-to-r from-art-cream to-art-beige dark:from-art-dark-card dark:to-art-dark-bg rounded-3xl shadow-2xl">
                        <div class="mb-4">
                            <i data-lucide="sparkles" class="w-16 h-16 text-art-accent mx-auto"></i>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-serif font-bold text-art-brown dark:text-art-beige mb-3">
                            Have a Custom Idea?
                        </h3>
                        <p class="text-lg opacity-80 mb-6 max-w-2xl mx-auto">
                            Bring your vision to life! Share your design and we'll create a unique, handcrafted piece just for you.
                        </p>
                        <button onclick="navigateTo('custom-request')" class="inline-block bg-art-brown hover:bg-art-accent dark:bg-art-accent dark:hover:bg-art-brown text-white px-10 py-4 rounded-full transition-all transform hover:-translate-y-1 hover:scale-105 shadow-xl font-bold text-lg uppercase tracking-wider">
                            <i data-lucide="palette" class="w-5 h-5 inline-block mr-2"></i>
                            Create Your Custom Product Now
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- PAGE 4: CREATIVE BUSINESS -->
        <section id="business" class="page-section">
            <div class="max-w-7xl mx-auto px-4 py-24">
                <div class="text-center mb-20">
                    <span class="text-art-accent font-bold tracking-[0.2em] uppercase text-sm">Entrepreneurship</span>
                    <h1 class="text-4xl md:text-6xl font-serif font-bold mt-4">Creative Business Management</h1>
                </div>

                <!-- Section 1: Photos (Products, Orders, Packaging) - FROM DATABASE -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-24">
                    <div class="rounded-2xl shadow-lg overflow-hidden h-64 relative group">
                        <img src="<?php echo e($business_images['products']['image_path'] ?? 'img/business/products.jpg'); ?>" class="w-full h-full object-cover">
                        <div
                            class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="text-white font-bold uppercase tracking-widest text-sm"><?php echo e($business_images['products']['caption'] ?? 'Products'); ?></span>
                        </div>
                    </div>
                    <div class="rounded-2xl shadow-lg overflow-hidden h-64 relative group mt-8 md:mt-0">
                        <img src="<?php echo e($business_images['packaging']['image_path'] ?? 'img/business/packaging.jpg'); ?>" class="w-full h-full object-cover">
                        <div
                            class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="text-white font-bold uppercase tracking-widest text-sm"><?php echo e($business_images['packaging']['caption'] ?? 'Packaging'); ?></span>
                        </div>
                    </div>
                    <div class="rounded-2xl shadow-lg overflow-hidden h-64 relative group">
                        <img src="<?php echo e($business_images['orders']['image_path'] ?? 'img/business/orders.jpg'); ?>" class="w-full h-full object-cover">
                        <div
                            class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="text-white font-bold uppercase tracking-widest text-sm"><?php echo e($business_images['orders']['caption'] ?? 'Orders'); ?></span>
                        </div>
                    </div>
                    <div class="rounded-2xl shadow-lg overflow-hidden h-64 relative group mt-8 md:mt-0">
                        <img src="<?php echo e($business_images['details']['image_path'] ?? 'img/business/details.jpg'); ?>" class="w-full h-full object-cover">
                        <div
                            class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="text-white font-bold uppercase tracking-widest text-sm"><?php echo e($business_images['details']['caption'] ?? 'Details'); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Management -->
                <div
                    class="mb-24 bg-art-brown text-art-cream dark:bg-art-dark-card p-12 rounded-3xl shadow-xl relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-64 h-64 bg-white/5 rounded-full filter blur-3xl">
                    </div>
                    <h2 class="text-3xl font-serif font-bold mb-10 text-center relative z-10">Operational Management
                    </h2>
                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 relative z-10">
                        <div
                            class="flex flex-col items-center text-center p-6 bg-white/5 rounded-2xl backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-colors">
                            <div class="bg-art-accent p-4 rounded-full text-white mb-4 shadow-lg"><i data-lucide="tag"
                                    class="w-6 h-6"></i></div>
                            <h4 class="font-bold uppercase tracking-widest text-sm mb-2">I Manage Pricing</h4>
                            <p class="text-sm opacity-70">Calculated cost analysis and market value assessment.</p>
                        </div>
                        <div
                            class="flex flex-col items-center text-center p-6 bg-white/5 rounded-2xl backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-colors">
                            <div class="bg-art-accent p-4 rounded-full text-white mb-4 shadow-lg"><i
                                    data-lucide="shopping-bag" class="w-6 h-6"></i></div>
                            <h4 class="font-bold uppercase tracking-widest text-sm mb-2">I Manage Orders</h4>
                            <p class="text-sm opacity-70">Streamlined processing from purchase to fulfillment.</p>
                        </div>
                        <div
                            class="flex flex-col items-center text-center p-6 bg-white/5 rounded-2xl backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-colors">
                            <div class="bg-art-accent p-4 rounded-full text-white mb-4 shadow-lg"><i data-lucide="users"
                                    class="w-6 h-6"></i></div>
                            <h4 class="font-bold uppercase tracking-widest text-sm mb-2">I Manage Customers</h4>
                            <p class="text-sm opacity-70">Handling inquiries and building long-term loyalty.</p>
                        </div>
                        <div
                            class="flex flex-col items-center text-center p-6 bg-white/5 rounded-2xl backdrop-blur-sm border border-white/10 hover:bg-white/10 transition-colors">
                            <div class="bg-art-accent p-4 rounded-full text-white mb-4 shadow-lg"><i
                                    data-lucide="megaphone" class="w-6 h-6"></i></div>
                            <h4 class="font-bold uppercase tracking-widest text-sm mb-2">I Manage Marketing</h4>
                            <p class="text-sm opacity-70">Social media campaigns and brand positioning.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 3: My YouTube Vlogs -->
                <div class="mb-24">
                    <div class="text-center mb-12">
                        <h2 class="text-4xl font-serif font-bold text-art-brown">My YouTube Vlogs</h2>
                        <p class="mt-4 text-art-accent opacity-80 font-serif italic text-lg">You can check out my latest YouTube vlogs below:</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php if (empty($youtube_vlogs)): ?>
                            <p class="col-span-3 text-center opacity-60">No vlogs found. Please add them from the admin panel.</p>
                        <?php else: ?>
                            <?php foreach ($youtube_vlogs as $vlog): ?>
                            <a href="<?php echo htmlspecialchars($vlog['video_url']); ?>" target="_blank" class="block group relative rounded-2xl overflow-hidden shadow-lg transition-transform hover:-translate-y-2">
                                <div class="aspect-video bg-gray-200 relative">
                                    <img src="<?php echo htmlspecialchars($vlog['thumbnail_path']); ?>" alt="<?php echo htmlspecialchars($vlog['title']); ?>" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                                        <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg">
                                            <i data-lucide="play" class="w-5 h-5 text-red-600 fill-current ml-1"></i>
                                        </div>
                                    </div>
                                </div>
                                <!-- Title overlay (optional, based on design it might be on image or below) - Design shows text on image sometimes or clean. We'll keep it clean as per image or simple title below if needed. Image in design helps. Let's add a subtle title bar at bottom if needed, but design looks like just thumbnails. I will rely on the image mainly, but add title for accessibility/fallback -->
                            </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Section 4: WhimsyVille Review Board (Testimonials) -->
                <?php
                // Fetch all active testimonials
                // $testimonials already fetched in previous block but let's re-fetch or use variable if scope allows. 
                // The previous code fetched it inside the loop which is bad practice. Let's fetch it properly here.
                $testimonials = db_fetch_all("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC");
                $total_reviews = count($testimonials);
                
                // Calculate average rating
                $avg_rating = 5; // Default
                if ($total_reviews > 0) {
                    $sum = 0;
                    foreach ($testimonials as $t) $sum += $t['rating'];
                    $avg_rating = round($sum / $total_reviews, 1);
                }
                ?>
                <div class="mb-24 text-center">
                    <h2 class="text-3xl md:text-4xl font-serif font-bold mb-2 text-art-brown">WhimsyVille Review Board</h2>
                    
                    <div class="flex items-center justify-center gap-1 mb-2 text-art-brown">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <i data-lucide="star" class="w-5 h-5 <?php echo $i <= round($avg_rating) ? 'fill-current' : ''; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    
                    <p class="text-sm opacity-70 mb-12">from <?php echo $total_reviews; ?> reviews <i data-lucide="check-circle" class="w-4 h-4 inline-block text-blue-500"></i></p>
                    
                    <!-- Carousel Container -->
                    <div class="relative max-w-4xl mx-auto px-12">
                        <!-- Arrows -->
                        <button id="prevReview" class="absolute left-0 top-1/2 -translate-y-1/2 text-gray-300 hover:text-art-brown transition-colors">
                            <i data-lucide="chevron-left" class="w-10 h-10"></i>
                        </button>
                        <button id="nextReview" class="absolute right-0 top-1/2 -translate-y-1/2 text-gray-300 hover:text-art-brown transition-colors">
                            <i data-lucide="chevron-right" class="w-10 h-10"></i>
                        </button>

                        <!-- Reviews Slider -->
                        <div id="reviews-slider" class="overflow-hidden">
                            <div class="flex transition-transform duration-500 ease-in-out" id="reviews-track">
                                <?php 
                                // Group testimonials into pairs for the slide view (2 per slide as per design seems like 2 columns)
                                // Design image shows 2 reviews side-by-side.
                                $chunks = array_chunk($testimonials, 2);
                                foreach ($chunks as $chunk): 
                                ?>
                                <div class="w-full flex-shrink-0 grid grid-cols-1 md:grid-cols-2 gap-8 px-4">
                                    <?php foreach ($chunk as $t): ?>
                                    <div class="text-center">
                                        <div class="flex justify-center gap-1 text-art-brown mb-3">
                                            <?php for($i=1; $i<=5; $i++): ?>
                                                <i data-lucide="star" class="w-4 h-4 <?php echo $i <= $t['rating'] ? 'fill-current' : ''; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <h4 class="font-bold text-lg mb-2 text-art-brown"><?php echo htmlspecialchars($t['reviewer_name']); ?></h4> <!-- Using Name as Title placeholder if title missing, or just review text -->
                                        <!-- Actually design shows "Looks adorable!" as title and then text. Our DB doesn't have title. I'll use a truncation of review or just the review. 
                                            Design: Stars -> Title -> Text -> Author. 
                                            We have: Name, Text, Rating.
                                            I will Format: Stars -> "Excellent" (Generic based on rating) -> Text -> Name
                                        -->
                                        <h5 class="font-bold text-art-accent text-sm mb-2">
                                            <?php echo $t['rating'] == 5 ? 'Excellent!' : 'Great Review!'; ?>
                                        </h5>
                                        <p class="text-gray-500 text-sm leading-relaxed mb-4">
                                            "<?php echo htmlspecialchars($t['review_text']); ?>"
                                        </p>
                                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider"><?php echo htmlspecialchars($t['reviewer_name']); ?></p>
                                    </div>
                                    <?php endforeach; ?>
                                    
                                    <!-- If odd number in chunk, fill empty space or center? Grid handles it. -->
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const track = document.getElementById('reviews-track');
                        const prevBtn = document.getElementById('prevReview');
                        const nextBtn = document.getElementById('nextReview');
                        let currentIndex = 0;
                        const totalSlides = <?php echo count($chunks); ?>;

                        function updateSlide() {
                            track.style.transform = `translateX(-${currentIndex * 100}%)`;
                        }

                        nextBtn.addEventListener('click', () => {
                            currentIndex = (currentIndex + 1) % totalSlides;
                            updateSlide();
                        });

                        prevBtn.addEventListener('click', () => {
                            currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
                            updateSlide();
                        });
                    });
                </script>
                
                <!-- Email Signup (Whimsical Emails) - Design Requirement -->
                <div class="mb-24 text-center">
                    <div class="flex justify-center items-center gap-2 mb-4 text-2xl font-serif text-art-brown">
                        <span>*°✿°*</span>
                        <h3 class="font-bold">Want some whimsical emails?</h3>
                    </div>
                    <p class="text-gray-500 mb-8 max-w-xl mx-auto text-sm leading-relaxed">
                        Join our tiny email community of whimsy hoomans for discounts, newsletters and stories from WhimsyVille! (+You'll get a 10% discount coupon.)
                    </p>
                    <form onsubmit="event.preventDefault(); alert('Subscribed!');" class="max-w-md mx-auto relative">
                        <input type="email" placeholder="Email" class="w-full pl-6 pr-12 py-3 rounded-full border border-art-brown/20 focus:outline-none focus:border-art-accent bg-transparent">
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-art-brown p-2">
                            <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        </button>
                    </form>
                </div>

                <!-- Delivery Image (Already there or need to restore?) -->
                    
                    <!-- Delivery Image -->
                    <div class="h-80 md:h-96 rounded-2xl overflow-hidden relative shadow-lg">
                        <img src="<?php echo e($business_images['delivery']['image_path'] ?? 'img/business/delivery.jpg'); ?>" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/30 flex items-end p-6">
                            <p class="text-white font-bold uppercase tracking-widest text-sm"><?php echo e($business_images['delivery']['caption'] ?? 'Secure Delivery'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PAGE 5: DIGITAL EXPERIENCE -->
        <section id="digital" class="page-section">
            <div class="max-w-6xl mx-auto px-4 py-24">
                <div class="text-center mb-16">
                    <h1 class="text-4xl md:text-6xl font-serif font-bold mb-6 text-art-brown dark:text-art-beige">
                        Digital Experience</h1>
                    <p class="max-w-2xl mx-auto text-xl opacity-80 font-serif italic">
                        "Connecting culture, heritage, and entrepreneurship through technology."
                    </p>
                </div>

                <!-- Screenshot of your website -->
                <div class="mb-24 relative">
                    <div
                        class="absolute -inset-4 bg-art-accent/20 dark:bg-art-accent/10 rounded-[2.5rem] rotate-1 filter blur-md">
                    </div>
                    <div
                        class="relative bg-art-cream dark:bg-art-dark-bg rounded-[2rem] p-4 shadow-2xl border border-art-brown/10 dark:border-white/10">
                        <!-- Browser Mockup Header -->
                        <div
                            class="bg-gray-100 dark:bg-gray-800 rounded-t-[1.5rem] p-3 flex gap-2 items-center border-b border-gray-200 dark:border-gray-700">
                            <div class="flex gap-1.5 ml-2">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                            </div>
                            <div
                                class="bg-white dark:bg-gray-900 rounded-full flex-grow mx-4 py-1 px-4 text-xs opacity-50 text-center font-mono">
                                batoolsaptitude.com</div>
                        </div>
                        <!-- Mockup Content - Laptop image from database -->
                        <div
                            class="bg-white dark:bg-gray-900 rounded-b-[1.5rem] overflow-hidden h-[450px] relative group">
                            <img src="<?php echo e($digital_content['laptop_mockup']['image_path'] ?? 'img/digital/laptop-mockup.jpg'); ?>"
                                class="w-full h-full object-cover opacity-30 dark:opacity-20 hover:opacity-10 transition-all duration-500">
                            <div class="absolute inset-0 flex flex-col items-center justify-center p-8">
                                <h3 class="text-3xl font-serif font-bold mb-8 text-center">Integrated Digital Portfolio
                                    Platform</h3>
                                <p class="text-center max-w-md mb-10 opacity-80">Presenting art professionally while
                                    connecting culture and entrepreneurship.</p>
                                <div class="flex flex-wrap justify-center gap-6">
                                    <button
                                        class="bg-art-brown text-white px-6 py-3 rounded-full flex items-center gap-3 hover:bg-art-accent transition-colors shadow-md">
                                        <i data-lucide="instagram" class="w-5 h-5"></i> <span
                                            class="font-bold text-sm uppercase tracking-wider">Instagram</span>
                                    </button>
                                    <button
                                        class="bg-art-brown text-white px-6 py-3 rounded-full flex items-center gap-3 hover:bg-art-accent transition-colors shadow-md">
                                        <i data-lucide="youtube" class="w-5 h-5"></i> <span
                                            class="font-bold text-sm uppercase tracking-wider">YouTube</span>
                                    </button>
                                    <button
                                        class="bg-white text-art-brown border-2 border-art-brown px-6 py-3 rounded-full flex items-center gap-3 hover:bg-art-brown hover:text-white transition-colors shadow-md dark:bg-transparent dark:text-white dark:border-white dark:hover:bg-white dark:hover:text-art-brown">
                                        <i data-lucide="video" class="w-5 h-5"></i> <span
                                            class="font-bold text-sm uppercase tracking-wider">Work Videos</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Brand Collaboration - FIXED BOX IMAGES -->
                <div class="grid md:grid-cols-5 gap-12 items-center mb-24">
                    <div class="md:col-span-3">
                        <h2 class="text-3xl font-serif font-bold mb-6">Brand Collaboration & <br /> Product Content
                            Creation</h2>
                        <p class="mb-8 opacity-90 leading-relaxed text-lg">
                            As a content creator, I have worked with product-based platforms by reviewing items and
                            creating visual content for online audiences.
                        </p>
                        <div class="grid grid-cols-2 gap-4">
                            <ul class="space-y-4 font-medium">
                                <li class="flex items-center gap-3">
                                    <div class="bg-art-accent/10 p-1 rounded-full text-art-accent"><i
                                            data-lucide="check" class="w-4 h-4"></i></div> Product presentation
                                </li>
                                <li class="flex items-center gap-3">
                                    <div class="bg-art-accent/10 p-1 rounded-full text-art-accent"><i
                                            data-lucide="check" class="w-4 h-4"></i></div> Visual content creation
                                </li>
                            </ul>
                            <ul class="space-y-4 font-medium">
                                <li class="flex items-center gap-3">
                                    <div class="bg-art-accent/10 p-1 rounded-full text-art-accent"><i
                                            data-lucide="check" class="w-4 h-4"></i></div> Honest reviews
                                </li>
                                <li class="flex items-center gap-3">
                                    <div class="bg-art-accent/10 p-1 rounded-full text-art-accent"><i
                                            data-lucide="check" class="w-4 h-4"></i></div> Online communication
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="md:col-span-2 grid grid-cols-2 gap-4 relative">
                        <!-- Box 1: Parcels (New Image) -->
                        <div class="glass-card p-3 rounded-xl shadow-sm rotate-3 hover:rotate-0 transition-transform">
                            <div class="rounded-lg w-full h-32 overflow-hidden mb-2">
                                <img src="<?php echo e($digital_content['parcels']['image_path'] ?? 'img/digital/parcels.jpg'); ?>" class="w-full h-full object-cover">
                            </div>
                            <p class="text-[10px] text-center font-bold uppercase tracking-widest">Parcels Received</p>
                        </div>
                        <!-- Box 2: Reviews (New Image) -->
                        <div
                            class="glass-card p-3 rounded-xl shadow-sm -rotate-3 hover:rotate-0 transition-transform mt-6">
                            <div class="rounded-lg w-full h-32 overflow-hidden mb-2 relative">
                                <img src="<?php echo e($digital_content['reviews']['image_path'] ?? 'img/digital/reviews.jpg'); ?>" class="w-full h-full object-cover opacity-80">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="bg-white/80 dark:bg-black/50 p-2 rounded-full"><i data-lucide="star"
                                            class="w-4 h-4 text-yellow-500 fill-current"></i></div>
                                </div>
                            </div>
                            <p class="text-[10px] text-center font-bold uppercase tracking-widest">Review Posts</p>
                        </div>
                        <!-- Box 3: Reel (New Image) -->
                        <div class="glass-card p-3 rounded-xl shadow-sm rotate-2 hover:rotate-0 transition-transform">
                            <div class="rounded-lg w-full h-32 overflow-hidden mb-2 relative">
                                <img src="<?php echo e($digital_content['content_reel']['image_path'] ?? 'img/digital/content-reel.jpg'); ?>" class="w-full h-full object-cover opacity-80">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <i data-lucide="play-circle" class="w-8 h-8 text-white drop-shadow-md"></i>
                                </div>
                            </div>
                            <p class="text-[10px] text-center font-bold uppercase tracking-widest">Content Reel</p>
                        </div>
                        <!-- Box 4: Visuals (New Image) -->
                        <div
                            class="glass-card p-3 rounded-xl shadow-sm -rotate-2 hover:rotate-0 transition-transform mt-6">
                            <div class="rounded-lg w-full h-32 overflow-hidden mb-2">
                                <img src="<?php echo e($digital_content['visuals']['image_path'] ?? 'img/digital/visuals.jpg'); ?>" class="w-full h-full object-cover">
                            </div>
                            <p class="text-[10px] text-center font-bold uppercase tracking-widest">Visuals</p>
                        </div>
                    </div>
                </div>

                <!-- Skills -->
                <div class="mb-24">
                    <h3 class="text-2xl font-serif font-bold mb-8 text-center">Skills Learned</h3>
                    <div class="grid grid-cols-3 md:grid-cols-6 gap-4">
                        <div
                            class="bg-art-beige/30 dark:bg-white/5 p-6 rounded-2xl text-center hover:bg-art-accent/20 transition-colors group">
                            <i data-lucide="video"
                                class="w-8 h-8 mx-auto mb-3 text-art-accent group-hover:scale-110 transition-transform"></i>
                            <h4 class="font-bold text-[10px] uppercase tracking-wider">Video Editing</h4>
                        </div>
                        <div
                            class="bg-art-beige/30 dark:bg-white/5 p-6 rounded-2xl text-center hover:bg-art-accent/20 transition-colors group">
                            <i data-lucide="sun"
                                class="w-8 h-8 mx-auto mb-3 text-art-accent group-hover:scale-110 transition-transform"></i>
                            <h4 class="font-bold text-[10px] uppercase tracking-wider">Lighting</h4>
                        </div>
                        <div
                            class="bg-art-beige/30 dark:bg-white/5 p-6 rounded-2xl text-center hover:bg-art-accent/20 transition-colors group">
                            <i data-lucide="message-circle"
                                class="w-8 h-8 mx-auto mb-3 text-art-accent group-hover:scale-110 transition-transform"></i>
                            <h4 class="font-bold text-[10px] uppercase tracking-wider">Comms</h4>
                        </div>
                        <div
                            class="bg-art-beige/30 dark:bg-white/5 p-6 rounded-2xl text-center hover:bg-art-accent/20 transition-colors group">
                            <i data-lucide="clock"
                                class="w-8 h-8 mx-auto mb-3 text-art-accent group-hover:scale-110 transition-transform"></i>
                            <h4 class="font-bold text-[10px] uppercase tracking-wider">Time Mgmt</h4>
                        </div>
                        <div
                            class="bg-art-beige/30 dark:bg-white/5 p-6 rounded-2xl text-center hover:bg-art-accent/20 transition-colors group">
                            <i data-lucide="pen-tool"
                                class="w-8 h-8 mx-auto mb-3 text-art-accent group-hover:scale-110 transition-transform"></i>
                            <h4 class="font-bold text-[10px] uppercase tracking-wider">Storytelling</h4>
                        </div>
                        <div
                            class="bg-art-beige/30 dark:bg-white/5 p-6 rounded-2xl text-center hover:bg-art-accent/20 transition-colors group">
                            <i data-lucide="trending-up"
                                class="w-8 h-8 mx-auto mb-3 text-art-accent group-hover:scale-110 transition-transform"></i>
                            <h4 class="font-bold text-[10px] uppercase tracking-wider">Promotion</h4>
                        </div>
                    </div>
                </div>

                <!-- Optional button -->
                <div class="text-center">
                    <button onclick="navigateTo('culture')"
                        class="group inline-flex items-center gap-2 text-art-accent font-serif italic text-xl hover:text-art-brown dark:hover:text-art-beige transition-colors relative">
                        <span>View Culture & Vision</span>
                        <i data-lucide="arrow-right" class="w-6 h-6 transition-transform group-hover:translate-x-2"></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- PAGE 6: CULTURE & VISION -->
        <section id="culture" class="page-section">
            <div class="max-w-5xl mx-auto px-4 py-24">
                <div class="text-center mb-20 relative">
                    <h1 class="text-4xl md:text-6xl font-serif font-bold mb-6 relative z-10">Culture & Vision</h1>
                    <div class="w-32 h-2 bg-art-accent mx-auto rounded-full relative z-10"></div>
                </div>

                <div class="grid md:grid-cols-2 gap-16 items-center mb-20">
                    <div class="prose dark:prose-invert prose-lg text-justify opacity-90 font-light leading-loose">
                        <p
                            class="mb-8 first-letter:text-5xl first-letter:font-serif first-letter:text-art-accent first-letter:mr-2 first-letter:float-left">
                            <strong class="text-art-brown dark:text-art-beige font-bold">Heritage as a
                                Foundation:</strong> My work is deeply rooted in the traditions and stories of my
                            culture. I believe that art is not just created in a vacuum but is a continuation of a long
                            lineage of human expression. By integrating traditional motifs with modern techniques, I aim
                            to keep these stories alive for a new generation.
                        </p>
                        <p class="mb-8">
                            <strong class="text-art-brown dark:text-art-beige font-bold">Responsibility of the
                                Artist:</strong> As creatives, we carry the responsibility of representation. It is our
                            duty to showcase our identity with authenticity and pride. I strive to create art that
                            respects its origins while inviting global dialogue, fostering a sense of shared humanity
                            through visual language.
                        </p>
                        <p>
                            <strong class="text-art-brown dark:text-art-beige font-bold">Global Creativity &
                                Leadership:</strong> The future of art is collaborative and borderless. My vision is to
                            establish platforms that allow local artisans to reach global markets without losing their
                            cultural essence. By merging IT strategies with artistic sensitivity, I plan to build
                            bridges that sustain cultural economies and celebrate diversity.
                        </p>
                    </div>
                    <!-- Fixed Large Culture Image -->
                    <div
                        class="h-[500px] rounded-[3rem] overflow-hidden shadow-2xl relative group rotate-3 hover:rotate-0 transition-all duration-700 bg-gray-100">
                        <img src="img/culture/heritage.jpg"
                            class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700 scale-110 group-hover:scale-100">
                        <div
                            class="absolute inset-0 bg-art-accent/20 mix-blend-multiply group-hover:bg-transparent transition-all duration-700">
                        </div>
                        <div
                            class="absolute bottom-0 left-0 right-0 p-8 bg-gradient-to-t from-black/80 to-transparent text-white opacity-0 group-hover:opacity-100 transition-opacity duration-700">
                            <p class="font-serif italic text-xl">"Preserving the threads of tradition."</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PAGE 7: CONTACT -->
        <section id="contact" class="page-section">
            <div class="max-w-6xl mx-auto px-4 py-24">
                <div class="grid md:grid-cols-2 gap-20 items-start">
                    <div class="flex flex-col justify-center">
                        <h1
                            class="text-5xl md:text-7xl font-serif font-bold mb-10 text-art-brown dark:text-art-beige leading-none">
                            Let's<br />Connect.</h1>
                        <p class="mb-16 text-xl opacity-80 font-light">I'm always open to discussing new projects,
                            creative opportunities, or cultural collaborations.</p>

                        <div class="space-y-10">
                            <div class="flex items-start gap-6 group">
                                <div
                                    class="w-16 h-16 bg-art-accent/10 rounded-2xl flex items-center justify-center text-art-accent group-hover:bg-art-accent group-hover:text-white transition-colors shadow-sm">
                                    <i data-lucide="mail" class="w-8 h-8"></i>
                                </div>
                                <div>
                                    <p class="text-sm uppercase tracking-widest opacity-60 mb-2 font-bold">Email</p>
                                    <p class="font-serif text-2xl font-bold">khansabatool@batoolsaptitude.com</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-6 group">
                                <div
                                    class="w-16 h-16 bg-art-accent/10 rounded-2xl flex items-center justify-center text-art-accent group-hover:bg-art-accent group-hover:text-white transition-colors shadow-sm">
                                    <i data-lucide="map-pin" class="w-8 h-8"></i>
                                </div>
                                <div>
                                    <p class="text-sm uppercase tracking-widest opacity-60 mb-2 font-bold">Location</p>
                                    <p class="font-serif text-2xl font-bold">Lahore, Pakistan</p>
                                </div>
                            </div>
                            <div class="flex gap-6 mt-12">
                                <a href="#"
                                    class="w-16 h-16 border-2 border-art-brown dark:border-art-beige rounded-full flex items-center justify-center hover:bg-art-brown hover:text-white dark:hover:bg-art-beige dark:hover:text-art-brown transition-all transform hover:-translate-y-2">
                                    <i data-lucide="instagram" class="w-8 h-8"></i>
                                </a>
                                <a href="#"
                                    class="w-16 h-16 border-2 border-art-brown dark:border-art-beige rounded-full flex items-center justify-center hover:bg-art-brown hover:text-white dark:hover:bg-art-beige dark:hover:text-art-brown transition-all transform hover:-translate-y-2">
                                    <i data-lucide="youtube" class="w-8 h-8"></i>
                                </a>
                                <a href="#"
                                    class="w-16 h-16 border-2 border-art-brown dark:border-art-beige rounded-full flex items-center justify-center hover:bg-art-brown hover:text-white dark:hover:bg-art-beige dark:hover:text-art-brown transition-all transform hover:-translate-y-2">
                                    <i data-lucide="linkedin" class="w-8 h-8"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div
                        class="glass-card p-10 md:p-12 rounded-[3rem] shadow-2xl relative overflow-hidden bg-white/80 dark:bg-white/5">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-art-accent/20 rounded-bl-[3rem] -z-10"></div>
                        <div
                            class="absolute bottom-0 left-0 w-32 h-32 bg-art-brown/10 dark:bg-white/5 rounded-tr-[3rem] -z-10">
                        </div>
                        <h3 class="text-3xl font-serif font-bold mb-10">Send a Message</h3>
                        <form onsubmit="event.preventDefault(); alert('Message sent successfully!');" class="space-y-8">
                            <div class="relative group">
                                <input type="text" id="name" required
                                    class="peer w-full p-4 pt-6 rounded-xl bg-white dark:bg-black/20 border-2 border-transparent focus:border-art-accent outline-none transition-all placeholder-transparent shadow-sm font-medium"
                                    placeholder="Name">
                                <label for="name"
                                    class="absolute left-4 top-1 text-xs font-bold uppercase tracking-widest opacity-60 transition-all peer-placeholder-shown:top-5 peer-placeholder-shown:text-base peer-placeholder-shown:opacity-40 peer-focus:top-1 peer-focus:text-xs peer-focus:opacity-60 peer-focus:text-art-accent">Name</label>
                            </div>
                            <div class="relative group">
                                <input type="email" id="email" required
                                    class="peer w-full p-4 pt-6 rounded-xl bg-white dark:bg-black/20 border-2 border-transparent focus:border-art-accent outline-none transition-all placeholder-transparent shadow-sm font-medium"
                                    placeholder="Email">
                                <label for="email"
                                    class="absolute left-4 top-1 text-xs font-bold uppercase tracking-widest opacity-60 transition-all peer-placeholder-shown:top-5 peer-placeholder-shown:text-base peer-placeholder-shown:opacity-40 peer-focus:top-1 peer-focus:text-xs peer-focus:opacity-60 peer-focus:text-art-accent">Email</label>
                            </div>
                            <div class="relative group">
                                <textarea id="message" required
                                    class="peer w-full p-4 pt-8 rounded-xl bg-white dark:bg-black/20 border-2 border-transparent focus:border-art-accent outline-none transition-all placeholder-transparent shadow-sm font-medium h-48 resize-none"
                                    placeholder="Message"></textarea>
                                <label for="message"
                                    class="absolute left-4 top-1 text-xs font-bold uppercase tracking-widest opacity-60 transition-all peer-placeholder-shown:top-6 peer-placeholder-shown:text-base peer-placeholder-shown:opacity-40 peer-focus:top-1 peer-focus:text-xs peer-focus:opacity-60 peer-focus:text-art-accent">Message</label>
                            </div>
                            <button type="submit"
                                class="w-full bg-art-brown text-white py-5 rounded-xl font-bold tracking-[0.2em] uppercase hover:bg-art-accent transition-all transform hover:scale-[1.02] shadow-lg text-sm">
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- PAGE 7: CUSTOM REQUEST -->
        <section id="custom-request" class="page-section">
            <div class="max-w-4xl mx-auto px-4 py-24">
                <div class="text-center mb-16">
                    <div class="inline-block p-4 bg-art-accent/10 rounded-full mb-6">
                        <i data-lucide="palette" class="w-16 h-16 text-art-accent"></i>
                    </div>
                    <h1 class="text-5xl md:text-7xl font-serif font-bold mb-6 text-art-brown dark:text-art-beige">Create Your Custom Product</h1>
                    <p class="text-xl opacity-80 font-light">Share your vision with us, and we'll bring it to life</p>
                </div>

                <div class="glass-card rounded-3xl p-8 md:p-12 shadow-2xl">
                    <form method="POST" action="index.php#custom-request" enctype="multipart/form-data" class="space-y-6">
                        <!-- Name -->
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

                        <!-- WhatsApp -->
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

                        <!-- Email -->
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

                        <!-- Shipping Address -->
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

                        <!-- Reference Image -->
                        <div>
                            <label class="block text-sm font-bold mb-2">
                                Reference Image/Sketch <span class="text-red-500">*</span>
                            </label>
                            <label for="reference_image" class="block border-2 border-dashed border-art-accent rounded-2xl p-8 text-center cursor-pointer hover:border-art-brown hover:bg-art-accent/5 transition-all" id="upload-label">
                                <i data-lucide="upload-cloud" class="w-12 h-12 mx-auto mb-3 text-art-accent"></i>
                                <p class="font-bold mb-1" id="upload-text">Click to upload your design sketch or reference image</p>
                                <p class="text-sm opacity-60">JPG or PNG (Max 5MB)</p>
                                <p class="text-xs opacity-40 mt-2" id="file-name"></p>
                            </label>
                            <input 
                                type="file" 
                                name="reference_image" 
                                id="reference_image"
                                accept="image/jpeg,image/jpg,image/png"
                                required
                                style="display: none;"
                            >
                        </div>

                        <!-- Ingredients -->
                        <div>
                            <label class="block text-sm font-bold mb-2">
                                Materials/Ingredients <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <textarea 
                                name="ingredients" 
                                rows="2"
                                class="w-full px-4 py-3 rounded-2xl border-2 border-art-accent/30 focus:border-art-accent focus:outline-none transition-all resize-none bg-white dark:bg-art-dark-card"
                                placeholder="E.g., Clay, beads, specific colors, materials you'd like us to use"
                            ></textarea>
                        </div>

                        <!-- Additional Comments -->
                        <div>
                            <label class="block text-sm font-bold mb-2">
                                Additional Comments <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <textarea 
                                name="additional_comments" 
                                rows="4"
                                class="w-full px-4 py-3 rounded-2xl border-2 border-art-accent/30 focus:border-art-accent focus:outline-none transition-all resize-none bg-white dark:bg-art-dark-card"
                                placeholder="Any specific details, preferences, or special requests?"
                            ></textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button 
                                type="submit"
                                name="submit_custom_request"
                                class="w-full bg-art-brown hover:bg-art-accent text-white px-8 py-4 rounded-full transition-all transform hover:scale-105 shadow-xl text-lg font-bold uppercase tracking-wider flex items-center justify-center gap-3"
                            >
                                <i data-lucide="send" class="w-5 h-5"></i>
                                <span>Submit Request</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Info Section -->
                <div class="mt-12 text-center opacity-70">
                    <p class="text-sm">We typically respond within 24-48 hours. You'll receive a confirmation email shortly.</p>
                </div>
            </div>
        </section>

        <!-- Success Modal -->
        <div id="successModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;">
            <div class="bg-white dark:bg-art-dark-card rounded-3xl p-8 md:p-12 max-w-md mx-4 shadow-2xl transform scale-95 opacity-0 transition-all duration-300" id="modalContent">
                <div class="text-center">
                    <!-- Success Icon -->
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full mb-6">
                        <svg class="w-12 h-12 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    
                    <!-- Success Message -->
                    <h2 class="text-3xl font-serif font-bold mb-4 text-art-brown dark:text-art-beige">Order Placed Successfully!</h2>
                    <p class="text-lg opacity-80 mb-8">Thank you for your custom product request. We'll review your details and contact you within 24-48 hours.</p>
                    
                    <!-- Close Button -->
                    <button onclick="closeSuccessModal()" class="bg-art-brown hover:bg-art-accent text-white px-8 py-3 rounded-full transition-all transform hover:scale-105 shadow-lg font-bold uppercase tracking-wider">
                        Close
                    </button>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-art-brown text-art-cream py-8 text-center mt-auto border-t-4 border-art-accent/30">
        <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-4 opacity-80">
            <p class="font-serif italic text-sm">&copy; 2024 Khansa Batool | Batool's Aptitude. All Rights Reserved.</p>
            <div class="flex gap-6 text-sm font-bold uppercase tracking-widest">
                <a href="#" class="hover:text-art-accent transition-colors">Privacy</a>
                <a href="#" class="hover:text-art-accent transition-colors">Terms</a>
            </div>
        </div>
    </footer>

    <!-- Lightbox Modal -->
    <div id="lightbox" onclick="closeLightbox()">
        <img id="lightbox-img" src="">
        <div id="lightbox-caption"></div>
    </div>

    <!-- AI Image Generator Modal -->
    <div id="ai-image-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4" onclick="closeImageModal(event)">
        <div class="relative max-w-2xl w-full bg-art-cream dark:bg-art-dark-card rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col" onclick="event.stopPropagation()">
            <!-- Close Button -->
            <button onclick="closeImageModal()" class="absolute top-4 right-4 z-10 w-10 h-10 flex items-center justify-center bg-art-brown/80 hover:bg-art-brown text-white rounded-full transition-all transform hover:scale-110">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            
            <!-- Modal Content -->
            <div class="p-6 overflow-y-auto">
                <div class="text-center mb-4">
                    <div class="flex items-center justify-center gap-2 mb-2">
                        <i data-lucide="sparkles" class="w-5 h-5 text-art-accent"></i>
                        <h2 class="text-xl font-serif font-bold text-art-brown dark:text-art-beige">AI Generated Art</h2>
                    </div>
                    <p id="ai-prompt-display" class="text-xs opacity-70 italic"></p>
                </div>
                
                <!-- Loading State -->
                <div id="ai-loading" class="flex flex-col items-center justify-center py-16 hidden">
                    <div class="w-16 h-16 border-4 border-art-accent border-t-transparent rounded-full animate-spin mb-4"></div>
                    <p class="text-art-accent font-bold">Generating your artwork...</p>
                    <p class="text-sm opacity-60 mt-2">This may take a few seconds</p>
                </div>
                
                <!-- Generated Image -->
                <div id="ai-image-container" class="hidden">
                    <div class="bg-white dark:bg-art-dark-bg rounded-2xl p-3 shadow-lg mb-4">
                        <img id="ai-generated-image" src="" alt="AI Generated Art" class="w-full h-auto max-h-96 object-contain rounded-lg">
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <!-- Primary Actions -->
                        <div class="flex gap-3 justify-center flex-wrap">

                            <button onclick="openFullImage()" class="bg-art-brown hover:bg-art-accent text-white px-6 py-3 rounded-full transition-all transform hover:scale-105 shadow-lg flex items-center gap-2 font-bold text-sm uppercase tracking-wider">
                                <i data-lucide="external-link" class="w-4 h-4"></i>
                                <span>Open Full Image</span>
                            </button>
                        </div>
                        
                        <!-- Download Actions -->
                        <div class="flex gap-3 justify-center flex-wrap">
                            <button onclick="downloadImage('png')" class="bg-white dark:bg-art-dark-bg hover:bg-gray-100 dark:hover:bg-art-brown text-art-brown dark:text-art-beige border-2 border-art-brown dark:border-art-beige px-5 py-2 rounded-full transition-all flex items-center gap-2 font-bold text-xs uppercase tracking-wider">
                                <i data-lucide="download" class="w-3 h-3"></i>
                                <span>PNG</span>
                            </button>
                            <button onclick="downloadImage('jpg')" class="bg-white dark:bg-art-dark-bg hover:bg-gray-100 dark:hover:bg-art-brown text-art-brown dark:text-art-beige border-2 border-art-brown dark:border-art-beige px-5 py-2 rounded-full transition-all flex items-center gap-2 font-bold text-xs uppercase tracking-wider">
                                <i data-lucide="download" class="w-3 h-3"></i>
                                <span>JPG</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Error State -->
                <div id="ai-error" class="hidden text-center py-12">
                    <div class="text-red-500 mb-4">
                        <i data-lucide="alert-circle" class="w-16 h-16 mx-auto mb-2"></i>
                        <p class="font-bold text-lg">Oops! Something went wrong</p>
                    </div>
                    <p class="opacity-70 mb-6" id="ai-error-message">Failed to generate image. Please try again.</p>
                    <button onclick="closeImageModal()" class="bg-art-brown text-white px-6 py-3 rounded-full hover:bg-art-accent transition-all">
                        Try Again
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Custom Order Button -->
    <button onclick="navigateTo('custom-request')" class="floating-custom-btn" title="Create Custom Product">
        <i data-lucide="palette" class="w-6 h-6"></i>
        <span>Custom Order</span>
    </button>
    
    <style>
        .floating-custom-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 999;
            background: linear-gradient(135deg, #D4A574 0%, #A67B5B 100%);
            color: white;
            padding: 16px 24px;
            border-radius: 50px;
            box-shadow: 0 8px 24px rgba(212, 165, 116, 0.4);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            animation: pulse-glow 2s infinite;
        }
        
        .floating-custom-btn:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 12px 32px rgba(212, 165, 116, 0.6);
            background: linear-gradient(135deg, #A67B5B 0%, #8B6548 100%);
        }
        
        @keyframes pulse-glow {
            0%, 100% {
                box-shadow: 0 8px 24px rgba(212, 165, 116, 0.4);
            }
            50% {
                box-shadow: 0 8px 32px rgba(212, 165, 116, 0.7);
            }
        }
        
        @media (max-width: 768px) {
            .floating-custom-btn {
                bottom: 20px;
                right: 20px;
                padding: 14px 20px;
                font-size: 0.85rem;
            }
            
            .floating-custom-btn span {
                display: none;
            }
            
            .floating-custom-btn {
                width: 56px;
                height: 56px;
                border-radius: 50%;
                justify-content: center;
                padding: 0;
            }
        }
    </style>

    <!-- File Upload Preview Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('reference_image');
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const label = document.getElementById('upload-label');
                    const fileName = document.getElementById('file-name');
                    const uploadText = document.getElementById('upload-text');
                    
                    if (this.files && this.files[0]) {
                        // Visual feedback - change border and background
                        if (label) {
                            label.style.borderColor = '#4A3728';
                            label.style.borderWidth = '3px';
                            label.style.backgroundColor = 'rgba(212, 165, 116, 0.15)';
                        }
                        
                        // Show filename with checkmark
                        if (fileName) {
                            fileName.textContent = '✓ File Selected: ' + this.files[0].name;
                            fileName.style.color = '#4A3728';
                            fileName.style.fontWeight = 'bold';
                            fileName.style.fontSize = '14px';
                        }
                        
                        // Update upload text
                        if (uploadText) {
                            uploadText.textContent = 'Image uploaded successfully!';
                            uploadText.style.color = '#4A3728';
                            uploadText.style.fontWeight = 'bold';
                        }
                    } else {
                        // Reset styling if no file
                        if (label) {
                            label.style.borderColor = '#D4A574';
                            label.style.borderWidth = '2px';
                            label.style.backgroundColor = '';
                        }
                        if (fileName) {
                            fileName.textContent = '';
                        }
                        if (uploadText) {
                            uploadText.textContent = 'Click to upload your design sketch or reference image';
                            uploadText.style.color = '';
                            uploadText.style.fontWeight = '';
                        }
                    }
                });
            }

            // Check for success message and show modal
            <?php
            if (isset($_SESSION['custom_request_success']) && $_SESSION['custom_request_success'] === true) {
                unset($_SESSION['custom_request_success']);
                echo "navigateTo('custom-request'); setTimeout(() => showSuccessModal(), 300);";
            }
            ?>
        });

        function showSuccessModal() {
            const modal = document.getElementById('successModal');
            const content = document.getElementById('modalContent');
            
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
            const content = document.getElementById('modalContent');
            
            if (content) {
                content.style.transform = 'scale(0.95)';
                content.style.opacity = '0';
            }
            
            setTimeout(() => {
                if (modal) modal.style.display = 'none';
            }, 300);
        }

        // Close modal on backdrop click
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('successModal');
            if (e.target === modal) {
                closeSuccessModal();
            }
        });
    </script>

    <!-- Custom JavaScript -->
    <script src="js/main.js" defer></script>
</body>

</html>