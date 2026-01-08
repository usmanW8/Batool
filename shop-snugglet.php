<?php
require_once 'admin/config/database.php';
require_once 'admin/includes/helpers.php';

$id = intval($_GET['id'] ?? 0);
$snugglet = db_fetch_single("SELECT * FROM shop_snugglets WHERE id = ?", [$id], 'i');

if (!$snugglet) {
    header('Location: index.php');
    exit;
}

$products = db_fetch_all("SELECT * FROM shop_products WHERE snugglet_id = ?", [$id], 'i');
$bgColor = $snugglet['bg_color'] ?: '#FFF';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($snugglet['name']); ?> - Batool's Aptitude</title>
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
    <style>
        .snugglet-hero {
            background-color: <?php echo htmlspecialchars($bgColor); ?>;
        }
    </style>
</head>
<body class="bg-art-cream text-art-brown dark:bg-art-dark-bg dark:text-art-dark-text font-sans">
    
    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-white/90 dark:bg-art-dark-bg/95 backdrop-blur-md border-b border-art-brown/10 dark:border-white/10 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="/Batool/" class="flex-shrink-0 flex items-center">
                    <img src="img/Logo/batoollogo.png" alt="Batool's Aptitude" class="h-24 w-auto block transition-transform duration-300 ease-in-out hover:scale-110 hover:rotate-3">
                </a>
                <div class="flex items-center gap-4">
                    <a href="/Batool/" class="text-sm uppercase tracking-wider hover:text-art-accent transition-colors">
                        <i data-lucide="arrow-left" class="w-4 h-4 inline-block mr-1"></i> Back to Home
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="min-h-screen pt-20">
        <!-- Hero Section -->
        <div class="snugglet-hero py-20 mb-12 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10 bg-[url('img/pattern.png')]"></div>
            <div class="max-w-7xl mx-auto px-4 relative z-10 flex flex-col md:flex-row items-center justify-center gap-10">
                <div class="w-48 h-48 md:w-64 md:h-64 bg-white/50 backdrop-blur-sm rounded-full p-4 shadow-xl animate-float">
                    <img src="<?php echo htmlspecialchars($snugglet['image_path'] ?: 'img/shop/placeholder.jpg'); ?>" 
                         class="w-full h-full object-contain drop-shadow-lg" 
                         alt="<?php echo htmlspecialchars($snugglet['name']); ?>">
                </div>
                <div class="text-center md:text-left">
                    <h1 class="text-5xl md:text-7xl font-serif font-bold text-art-brown mb-4">
                        <?php echo htmlspecialchars($snugglet['name']); ?>
                    </h1>
                    <p class="text-xl opacity-75 font-serif italic">Discover the <?php echo htmlspecialchars($snugglet['name']); ?> Collection</p>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 pb-24">
            <!-- Product Grid -->
            <?php if (empty($products)): ?>
                <div class="text-center py-20 opacity-60">
                    <i data-lucide="package-open" class="w-16 h-16 mx-auto mb-4 text-gray-300"></i>
                    <p class="text-xl font-serif">Empty shelves! Check back later.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                    <?php foreach ($products as $product): ?>
                    <?php 
                        $isSoldOut = $product['is_sold_out'] == 1;
                        $priceHtml = $product['sale_price'] > 0 
                            ? '<span class="text-gray-400 line-through text-xs mr-2">$'.$product['price'].'</span><span class="text-red-500 font-bold">$'.$product['sale_price'].'</span>' 
                            : '<span class="font-bold text-art-dark-orange dark:text-art-beige">$'.$product['price'].'</span>';
                    ?>
                    <a href="shop-product.php?id=<?php echo $product['id']; ?>" class="group block">
                        <div class="bg-white dark:bg-white/5 rounded-3xl p-4 transition-all duration-300 hover:shadow-xl hover:-translate-y-2 border border-transparent hover:border-art-accent/20 h-full flex flex-col">
                            <div class="aspect-square rounded-2xl overflow-hidden mb-4 bg-gray-100 relative">
                                <?php if ($isSoldOut): ?>
                                    <div class="absolute top-3 left-3 bg-black/70 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-md z-10">Sold Out</div>
                                <?php else: ?>
                                    <div class="absolute top-3 left-3 bg-art-dark-orange/90 text-white text-xs font-bold px-3 py-1 rounded-full backdrop-blur-md z-10">In Stock</div>
                                <?php endif; ?>
                                <img src="<?php echo htmlspecialchars($product['image_path'] ?: 'img/shop/placeholder.jpg'); ?>" 
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                                
                                <?php if (!$isSoldOut): ?>
                                <div class="absolute bottom-3 right-3 bg-white text-art-brown p-3 rounded-full shadow-lg transform translate-y-12 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300 hover:bg-art-accent hover:text-white">
                                    <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="flex-1 flex flex-col">
                                <h3 class="font-bold text-lg mb-1 text-art-brown dark:text-art-beige line-clamp-2 group-hover:text-art-accent transition-colors">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </h3>
                                <div class="mt-auto pt-2 flex items-center justify-between">
                                    <div><?php echo $priceHtml; ?></div>
                                    <span class="text-xs text-art-accent opacity-0 group-hover:opacity-100 transition-opacity font-medium uppercase tracking-wider">Adopt Me</span>
                                </div>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="bg-white dark:bg-black/20 py-8 text-center border-t border-art-brown/5 dark:border-white/5">
        <p class="opacity-60 text-sm">© <?php echo date('Y'); ?> Batool's Aptitude. All rights reserved.</p>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
