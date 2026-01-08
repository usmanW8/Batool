-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 02, 2026 at 05:40 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `batool`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_content`
--

CREATE TABLE `about_content` (
  `id` int(11) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `section_title` varchar(255) NOT NULL,
  `content_text` text NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `about_content`
--

INSERT INTO `about_content` (`id`, `section_name`, `section_title`, `content_text`, `display_order`, `updated_at`) VALUES
(1, 'background', 'Background & Education', 'I began my journey in the world of Information Technology, gaining a structured understanding of digital systems. However, my heart always leaned towards creation. Combining my technical education with my passion for art, I bridge the gap between traditional craftsmanship and modern digital presence. This dual background allows me to approach art not just as expression, but as a scalable system.', 1, '2025-12-10 15:48:51'),
(2, 'why_art', 'Why I Love Art', 'Art is more than just aesthetics; it is a language of emotion and heritage. It allows me to express unspoken cultural narratives and connect with people on a human level. The tactile nature of clay, the flow of paint, and the structure of design bring me a sense of purpose that nothing else does. It is the medium through which I explore identity and connect with the world.', 2, '2025-12-10 15:48:51'),
(3, 'future', 'Future Vision', 'My ambition extends beyond the studio. I aim to evolve into a global cultural leader, curating spaces where tradition meets innovation. I envision leading platforms that empower other artists, using my IT background to build sustainable systems for the creative economy. My goal is to foster global creativity while ensuring cultural responsibility remains at the core of entrepreneurial growth.', 3, '2025-12-10 15:48:51');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `email`, `created_at`, `last_login`) VALUES
(3, 'admin', 'admin123', 'admin@batoolsaptitude.com', '2025-12-10 15:57:40', '2026-01-02 16:23:24');

-- --------------------------------------------------------

--
-- Table structure for table `business_images`
--

CREATE TABLE `business_images` (
  `id` int(11) NOT NULL,
  `image_type` varchar(50) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `business_images`
--

INSERT INTO `business_images` (`id`, `image_type`, `image_path`, `caption`, `display_order`, `updated_at`) VALUES
(1, 'products', 'img/business/products.jpg', 'Products', 1, '2025-12-10 15:48:52'),
(2, 'packaging', 'img/business/packaging.jpg', 'Packaging', 2, '2025-12-10 15:48:52'),
(3, 'orders', 'img/business/orders.jpg', 'Orders', 3, '2025-12-10 15:48:52'),
(4, 'details', 'img/business/details.jpg', 'Details', 4, '2025-12-10 15:48:52'),
(5, 'delivery', 'img/business/delivery.jpg', 'Secure Delivery', 5, '2025-12-10 15:48:52');

-- --------------------------------------------------------

--
-- Table structure for table `contact_info`
--

CREATE TABLE `contact_info` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `location` varchar(255) NOT NULL,
  `social_instagram` varchar(255) DEFAULT NULL,
  `social_youtube` varchar(255) DEFAULT NULL,
  `social_linkedin` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_info`
--

INSERT INTO `contact_info` (`id`, `email`, `location`, `social_instagram`, `social_youtube`, `social_linkedin`, `updated_at`) VALUES
(1, 'khansabatool@batoolsaptitude.com', 'Lahore, Pakistan', '#', '#', '#', '2025-12-10 15:48:52');

-- --------------------------------------------------------

--
-- Table structure for table `culture_content`
--

CREATE TABLE `culture_content` (
  `id` int(11) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `section_title` varchar(255) NOT NULL,
  `content_text` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `culture_content`
--

INSERT INTO `culture_content` (`id`, `section_name`, `section_title`, `content_text`, `image_path`, `updated_at`) VALUES
(1, 'heritage', 'Heritage as a Foundation', 'My work is deeply rooted in the traditions and stories of my culture. I believe that art is not just created in a vacuum but is a continuation of a long lineage of human expression. By integrating traditional motifs with modern techniques, I aim to keep these stories alive for a new generation.', 'img/culture/heritage.jpg', '2025-12-10 15:48:52'),
(2, 'responsibility', 'Responsibility of the Artist', 'As creatives, we carry the responsibility of representation. It is our duty to showcase our identity with authenticity and pride. I strive to create art that respects its origins while inviting global dialogue, fostering a sense of shared humanity through visual language.', NULL, '2025-12-10 15:48:52'),
(3, 'leadership', 'Global Creativity & Leadership', 'The future of art is collaborative and borderless. My vision is to establish platforms that allow local artisans to reach global markets without losing their cultural essence. By merging IT strategies with artistic sensitivity, I plan to build bridges that sustain cultural economies and celebrate diversity.', NULL, '2025-12-10 15:48:52');

-- --------------------------------------------------------

--
-- Table structure for table `custom_requests`
--

CREATE TABLE `custom_requests` (
  `id` int(11) NOT NULL,
  `order_id` varchar(20) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `whatsapp_contact` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `shipping_address` text NOT NULL,
  `reference_image` varchar(255) DEFAULT NULL,
  `ingredients` text DEFAULT NULL,
  `additional_comments` text DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `digital_content`
--

CREATE TABLE `digital_content` (
  `id` int(11) NOT NULL,
  `content_type` varchar(50) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `digital_content`
--

INSERT INTO `digital_content` (`id`, `content_type`, `image_path`, `title`, `description`, `display_order`, `updated_at`) VALUES
(1, 'laptop_mockup', 'img/digital/laptop-mockup.jpg', 'Integrated Digital Portfolio Platform', 'Presenting art professionally while connecting culture and entrepreneurship.', 1, '2025-12-10 15:48:52'),
(2, 'parcels', 'img/digital/parcels.jpg', 'Parcels Received', 'Product collaboration parcels', 2, '2025-12-10 15:48:52'),
(3, 'reviews', 'img/digital/reviews.jpg', 'Review Posts', 'Honest product reviews', 3, '2025-12-13 06:37:36'),
(4, 'content_reel', 'img/digital/content-reel.jpg', 'Content Reel', 'Visual content creation', 4, '2025-12-13 06:37:36'),
(5, 'visuals', 'img/digital/visuals.jpg', 'Visuals', 'Professional visual content', 5, '2025-12-10 15:48:52');

-- --------------------------------------------------------

--
-- Table structure for table `featured_portfolio`
--

CREATE TABLE `featured_portfolio` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `featured_portfolio`
--

INSERT INTO `featured_portfolio` (`id`, `image_path`, `title`, `display_order`) VALUES
(1, 'img/portfolio/featured-1.jpg', 'Artwork 1', 1),
(2, 'img/portfolio/featured-2.jpg', 'Artwork 2', 2),
(3, 'img/portfolio/featured-3.jpg', 'Artwork 3', 3),
(4, 'img/portfolio/featured-4.jpg', 'Artwork 4', 4),
(5, 'img/portfolio/featured-5.jpg', 'Artwork 5', 5),
(6, 'img/portfolio/featured-6.jpg', 'Artwork 6', 6);

-- --------------------------------------------------------

--
-- Table structure for table `hero_section`
--

CREATE TABLE `hero_section` (
  `id` int(11) NOT NULL,
  `main_title` varchar(255) NOT NULL,
  `subtitle` varchar(255) NOT NULL,
  `typewriter_text` text NOT NULL,
  `profile_image` varchar(255) NOT NULL,
  `background_image` varchar(255) NOT NULL,
  `cta_text` varchar(100) NOT NULL,
  `quote_text` text NOT NULL,
  `identity_description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hero_section`
--

INSERT INTO `hero_section` (`id`, `main_title`, `subtitle`, `typewriter_text`, `profile_image`, `background_image`, `cta_text`, `quote_text`, `identity_description`, `updated_at`) VALUES
(1, 'Creative Artist & Emerging Cultural Entrepreneur', 'KHANSA BATOOL', 'Blending tradition with innovation, creating art that speaks across cultures.', 'img/hero/ai-artwork-1767366831973.png', 'img/hero/hero-background.jpg', 'View Portfolio', 'My goal is to grow from artist to cultural leader.', 'I am an artist with an IT background, developing handmade artworks while building a creative business through digital platforms. My work is inspired by culture, emotion, and identity, driven by a strong desire to manage creative systems globally.', '2026-01-02 16:39:05');

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_categories`
--

CREATE TABLE `portfolio_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `icon_name` varchar(50) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `portfolio_categories`
--

INSERT INTO `portfolio_categories` (`id`, `category_name`, `icon_name`, `display_order`) VALUES
(1, 'Sketching & Painting', 'pen-tool', 1),
(2, 'Clay & Jewellery', 'gem', 2),
(3, 'Photography', 'camera', 3),
(4, 'Sculpture & Ceramics', 'box', 4),
(5, 'Digital Illustration', 'monitor', 5);

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_items`
--

CREATE TABLE `portfolio_items` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `long_description` text DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `year` varchar(10) NOT NULL,
  `medium` varchar(100) NOT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `materials` varchar(255) DEFAULT NULL,
  `youtube_url` varchar(500) DEFAULT NULL,
  `status` enum('available','sold','private') DEFAULT 'available',
  `is_featured` tinyint(1) DEFAULT 0,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `portfolio_items`
--

INSERT INTO `portfolio_items` (`id`, `category_id`, `title`, `slug`, `description`, `long_description`, `image_path`, `year`, `medium`, `dimensions`, `price`, `materials`, `youtube_url`, `status`, `is_featured`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Ethereal Sketch', 'ethereal-sketch', 'A captivating graphite piece exploring human emotion', 'A captivating graphite piece exploring human emotion\r\n\r\nThis piece represents a unique exploration of artistic expression, combining traditional techniques with contemporary vision.', 'img/portfolio/sketching-1.jpg', '2023', 'Graphite on Paper', '16x20 inches', 12.00, 'Graphics on Paper', 'https://youtube.com/shorts/dnguqSqMjhY?si=-rBYDRRscg-B3ME8', 'available', 1, 1, '2025-12-10 15:48:51', '2025-12-23 08:37:11'),
(2, 1, 'Modern Botanicals', 'modern-botanicals', 'Contemporary interpretation of natural forms', 'Contemporary interpretation of natural forms\r\n\r\nThis piece represents a unique exploration of artistic expression, combining traditional techniques with contemporary vision.', 'img/portfolio/sketching-2.jpg', '2023', 'Acrylic on Canvas', '16x20 inches', 20.00, 'Acrylic on Canvas', NULL, 'available', 1, 2, '2025-12-10 15:48:51', '2025-12-22 07:28:36'),
(3, 1, 'Abstract Flow', 'abstract-flow', 'Fluid watercolor exploring movement and energy', 'Fluid watercolor exploring movement and energy\n\nThis piece represents a unique exploration of artistic expression, combining traditional techniques with contemporary vision.', 'img/portfolio/sketching-3.jpg', '2022', 'Watercolor', '16x20 inches', NULL, 'Watercolor', NULL, 'available', 1, 3, '2025-12-10 15:48:51', '2025-12-20 07:14:46'),
(4, 2, 'Terracotta Charm', 'terracotta-charm', 'Handcrafted polymer clay jewelry piece', 'Handcrafted polymer clay jewelry piece\n\nThis piece represents a unique exploration of artistic expression, combining traditional techniques with contemporary vision.', 'img/portfolio/clay-1.jpg', '2024', 'Polymer Clay', '16x20 inches', NULL, 'Polymer Clay', NULL, 'available', 1, 1, '2025-12-10 15:48:51', '2025-12-20 07:36:22'),
(5, 2, 'Minimalist Charms', 'minimalist-charms', 'Contemporary sculptural jewelry design', 'Contemporary sculptural jewelry design\n\nThis piece represents a unique exploration of artistic expression, combining traditional techniques with contemporary vision.', 'img/portfolio/featured-5.jpg', '2024', 'Sculpting', '16x20 inches', NULL, 'Sculpting', NULL, 'available', 1, 2, '2025-12-10 15:48:51', '2025-12-20 07:36:22'),
(6, 2, 'Handmade Earrings', 'handmade-earrings', 'Mixed media earrings with cultural motifs', 'Mixed media earrings with cultural motifs\n\nThis piece represents a unique exploration of artistic expression, combining traditional techniques with contemporary vision.', 'img/portfolio/clay-3.jpg', '2024', 'Mixed Media', '16x20 inches', NULL, 'Mixed Media', NULL, 'available', 1, 3, '2025-12-10 15:48:51', '2025-12-20 07:36:22'),
(7, 3, 'Mountain Vista', 'mountain-vista', 'Breathtaking landscape photography', 'Captured during golden hour in the Swiss Alps, this photograph showcases the dramatic interplay of light and shadow across mountain peaks. The composition emphasizes the scale and majesty of nature.', 'img/portfolio/photo-1.jpg', '2023', '', '24x36 inches', 150.00, 'Fine Art Print on Paper', NULL, 'available', 1, 1, '2025-12-20 06:59:00', '2025-12-20 07:14:46'),
(8, 3, 'Urban Reflections', 'urban-reflections', 'City architecture through a lens', 'An exploration of geometric patterns and reflections in modern urban architecture, highlighting the beauty of structural design. This piece captures the intersection of natural light and man-made forms.', 'img/portfolio/photo-2.jpg', '2024', '', '20x30 inches', 120.00, 'Digital Photography Print', NULL, 'available', 1, 2, '2025-12-20 06:59:00', '2025-12-20 07:14:46'),
(9, 3, 'Forest Path', 'forest-path', 'Nature photography in autumn', 'A serene forest trail captured during peak autumn colors, inviting viewers into a peaceful natural sanctuary. The dappled light through the canopy creates an ethereal atmosphere.', 'img/portfolio/photo-3.jpg', '2022', '', '18x24 inches', 100.00, 'Fine Art Print on Canvas', NULL, 'available', 1, 3, '2025-12-20 06:59:00', '2025-12-20 07:14:46'),
(10, 4, 'Abstract Form', 'abstract-form', 'Contemporary ceramic sculpture', 'A modern interpretation of organic forms, this ceramic piece explores the relationship between negative and positive space. Hand-built using traditional coiling techniques with a contemporary aesthetic.', 'img/portfolio/sculpture-1.jpg', '2024', '', '12x8x6 inches', 350.00, 'Stoneware Clay, Oxide Glaze', NULL, 'available', 1, 1, '2025-12-20 06:59:00', '2025-12-20 07:36:22'),
(11, 4, 'Terra Vessel', 'terra-vessel', 'Handcrafted pottery art', 'Traditional pottery techniques meet contemporary aesthetics in this functional art piece. The vessel features intricate surface textures created through burnishing and slip decoration.', 'img/portfolio/sculpture-2.jpg', '2023', '', '10x10x8 inches', 280.00, 'Terracotta Clay, Natural Finish', NULL, 'sold', 1, 2, '2025-12-20 06:59:00', '2025-12-20 07:36:22'),
(12, 4, 'Stone Harmony', 'stone-harmony', 'Carved stone sculpture', 'Hand-carved from natural stone, this piece represents balance and tranquility through minimalist design. Each curve is carefully considered to create a sense of flowing movement.', 'img/portfolio/sculpture-3.jpg', '2023', '', '15x6x6 inches', 420.00, 'Limestone, Hand-Carved', NULL, 'available', 1, 3, '2025-12-20 06:59:00', '2025-12-20 07:36:22'),
(13, 5, 'Dreamscape', 'dreamscape', 'Surreal digital painting', 'A vibrant digital illustration blending reality and imagination, created using advanced digital painting techniques. The color palette explores complementary contrasts to create visual depth.', 'img/portfolio/digital-1.jpg', '2024', '', '4000x3000 pixels', 200.00, 'Digital Painting (Procreate)', NULL, 'available', 1, 1, '2025-12-20 06:59:00', '2025-12-20 07:36:22'),
(14, 5, 'Neon Streets', 'neon-streets', 'Cyberpunk cityscape illustration', 'A futuristic urban scene featuring bold neon colors and atmospheric perspective, inspired by cyberpunk aesthetics. This piece combines digital painting with photo manipulation techniques.', 'img/portfolio/digital-2.jpg', '2024', '', '3840x2160 pixels', 180.00, 'Digital Art (Photoshop)', NULL, 'available', 1, 2, '2025-12-20 06:59:00', '2025-12-20 07:36:22'),
(15, 5, 'Ethereal Portrait', 'ethereal-portrait', 'Character design illustration', 'An expressive character portrait showcasing advanced digital illustration techniques and emotional storytelling. The lighting and color work together to create mood and atmosphere.', 'img/portfolio/digital-3.jpg', '2023', '', '3000x4000 pixels', 220.00, 'Digital Illustration (Clip Studio)', NULL, 'private', 1, 3, '2025-12-20 06:59:00', '2025-12-20 07:36:22');

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_products_order`
--

CREATE TABLE `portfolio_products_order` (
  `id` int(11) NOT NULL,
  `order_id` varchar(20) NOT NULL,
  `portfolio_item_id` int(11) NOT NULL,
  `product_title` varchar(255) NOT NULL,
  `product_image` varchar(500) DEFAULT NULL,
  `product_price` varchar(50) DEFAULT NULL,
  `product_dimensions` varchar(100) DEFAULT NULL,
  `product_materials` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `whatsapp_contact` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `shipping_address` text NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `portfolio_products_order`
--

INSERT INTO `portfolio_products_order` (`id`, `order_id`, `portfolio_item_id`, `product_title`, `product_image`, `product_price`, `product_dimensions`, `product_materials`, `customer_name`, `whatsapp_contact`, `email`, `shipping_address`, `status`, `created_at`) VALUES
(1, 'POR-AA881076-5144', 2, 'Modern Botanicals', 'img/portfolio/sketching-2.jpg', '20.00', '16x20 inches', 'Acrylic on Canvas', 'Muhammad Usman', '+92 3247895022', 'm.usmn318@gmail.com', 'Chak No 22 NB TEHSIL BHALWAL DISTRICT SARGODHA', 'processing', '2025-12-23 07:54:16'),
(2, 'POR-166D7BCD-8105', 1, 'Ethereal Sketch', 'img/portfolio/sketching-1.jpg', '12.00', '16x20 inches', 'Graphics on Paper', 'Muhammad Usman', '+92 3247895022', 'digiskillsacc2@gmail.com', 'Chak No 22 NB\r\nChak No 22 NB', 'pending', '2026-01-02 15:16:54');

-- --------------------------------------------------------

--
-- Table structure for table `shop_categories`
--

CREATE TABLE `shop_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shop_categories`
--

INSERT INTO `shop_categories` (`id`, `name`, `slug`, `image_path`, `display_order`, `created_at`) VALUES
(1, 'Hair Clips', 'hair-clips', 'img/shop/cat_1767026526_6952af5e324bd.jpg', 1, '2025-12-29 14:26:37'),
(2, 'Earrings', 'earrings', 'img/shop/cat_1767026551_6952af77cb769.jpg', 2, '2025-12-29 14:26:37'),
(3, 'Pins & Tiny Charms', 'pins-tiny-charms', 'img/shop/cat_1767026564_6952af84a3454.jpg', 3, '2025-12-29 14:26:37'),
(4, 'Keychains', 'keychains', 'img/shop/cat_1767026578_6952af92491cc.jpg', 4, '2025-12-29 14:26:37'),
(5, 'Oopsie Mystery Boxes', 'oopsie-mystery-boxes', 'img/shop/cat_1767026602_6952afaa14542.jpg', 5, '2025-12-29 14:26:37'),
(6, 'Clay Magnets', 'clay-magnets', 'img/shop/cat_1767026615_6952afb7d4f77.jpg', 6, '2025-12-29 14:26:37'),
(7, 'Desk Friend', 'desk-friend', 'img/shop/cat_1767026630_6952afc60f80e.jpg', 7, '2025-12-29 14:26:37'),
(8, 'Stickers', 'stickers', 'img/shop/cat_1767026668_6952afec643c5.jpg', 8, '2025-12-29 14:26:37'),
(9, 'Bookmarks', 'bookmarks', 'img/shop/cat_1767026685_6952affd5471d.jpg', 9, '2025-12-29 14:26:37');

-- --------------------------------------------------------

--
-- Table structure for table `shop_orders`
--

CREATE TABLE `shop_orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `whatsapp_contact` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `shipping_address` text NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shop_products`
--

CREATE TABLE `shop_products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `detailed_description` text DEFAULT NULL,
  `materials` text DEFAULT NULL,
  `dimensions` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `is_sold_out` tinyint(1) NOT NULL DEFAULT 0,
  `image_path` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `snugglet_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shop_products`
--

INSERT INTO `shop_products` (`id`, `name`, `subtitle`, `description`, `detailed_description`, `materials`, `dimensions`, `price`, `sale_price`, `is_sold_out`, `image_path`, `category_id`, `snugglet_id`, `created_at`, `is_available`) VALUES
(1, 'Waffle Hair Clip', NULL, NULL, NULL, NULL, NULL, 12.00, NULL, 0, 'img/shop/prod_1.jpg', 1, NULL, '2025-12-29 14:26:37', 1),
(2, 'Strawberry Earrings', NULL, NULL, NULL, NULL, NULL, 15.00, NULL, 0, 'img/shop/prod_2.jpg', 2, NULL, '2025-12-29 14:26:37', 1),
(3, 'Frog Pin', NULL, NULL, NULL, NULL, NULL, 8.00, NULL, 0, 'img/shop/prod_3.jpg', 3, NULL, '2025-12-29 14:26:37', 1),
(4, 'Bear Keychain', NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, 'img/shop/prod_4.jpg', 4, NULL, '2025-12-29 14:26:37', 1),
(5, 'Strawbibi Snuggle Snout: Keychain', 'Meet Your New Travel Buddy: Strawbibi Snuggle Snout Keychain!', 'Snuggle Snout is ready to join you on all your daily adventures. Attach this adorable keychain to your keys or bag and carry a piece of charm wherever you go!', 'Each clay product is handmade individually, making no two exactly alike. Variations from the displayed images are expected due to the handcrafted nature of each piece, adding to their uniqueness and charm. Embrace the slight imperfections that make this clay product not just a decor item, but a special piece of art that stands out from the mass-produced crowd.\r\nThe clay product that you\'ll receive will be glazed with a glossy resin coating to make it more durable. Kindly note that the resin (glossy) coating will be textured and not smooth.\r\nSince these are made from clay, it is important to handle them with care. Rough handling of your clay product might lead to damage, or the clay piece might break or even separate from the keychain. Hence, please take care of them!', 'Handcrafted with love from cold porcelain clay.', '3 inch', 15.00, NULL, 0, 'img/shop/prod_1767021812_69529cf4c89a5.jpg', NULL, 1, '2025-12-29 15:23:32', 1);

-- --------------------------------------------------------

--
-- Table structure for table `shop_settings`
--

CREATE TABLE `shop_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shop_settings`
--

INSERT INTO `shop_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'shop_products_heading', 'Shop by Product', '2025-12-29 15:54:11'),
(2, 'shop_snugglets_heading', 'Shop by Snugglets', '2025-12-29 15:54:11');

-- --------------------------------------------------------

--
-- Table structure for table `shop_snugglets`
--

CREATE TABLE `shop_snugglets` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `bg_color` varchar(50) DEFAULT '#FFF',
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shop_snugglets`
--

INSERT INTO `shop_snugglets` (`id`, `name`, `slug`, `image_path`, `bg_color`, `display_order`, `created_at`) VALUES
(1, 'Snuggle Snouttt', 'snuggle-snouttt', 'img/shop/snug_1767021024_695299e0437d4.jpg', '#eeffe0', 1, '2025-12-29 14:26:37'),
(2, 'Cocoa', 'cocoa', 'img/shop/snug_1767026440_6952af082017f.jpg', '#fefce8', 2, '2025-12-29 14:26:37'),
(3, 'Lady Barbara', 'lady-barbara', 'img/shop/snug_1767026453_6952af15264ae.jpg', '#fff7ed', 3, '2025-12-29 14:26:37'),
(4, 'Minnie', 'minnie', 'img/shop/snug_1767026465_6952af21578bb.jpg', '#fffbeb', 4, '2025-12-29 14:26:37'),
(5, 'Stu', 'stu', 'img/shop/snug_1767026478_6952af2e970fc.jpg', '#fff1f2', 5, '2025-12-29 14:26:37'),
(6, 'Pip', 'pip', 'img/shop/snug_1767026490_6952af3a370a2.jpg', '#fffbeb', 6, '2025-12-29 14:26:37'),
(7, 'Americano Brothers', 'americano-brothers', 'img/shop/snug_1767026504_6952af48b1ad8.jpg', '#fff7ed', 7, '2025-12-29 14:26:37');

-- --------------------------------------------------------

--
-- Table structure for table `site_logo`
--

CREATE TABLE `site_logo` (
  `id` int(11) NOT NULL,
  `logo_path` varchar(255) NOT NULL DEFAULT 'img/Logo/batoollogo.png',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_logo`
--

INSERT INTO `site_logo` (`id`, `logo_path`, `updated_at`, `updated_by`) VALUES
(1, 'img/Logo/batoollogo.png', '2026-01-02 15:54:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `reviewer_name` varchar(100) NOT NULL,
  `review_text` text NOT NULL,
  `rating` int(1) NOT NULL DEFAULT 5,
  `profile_image` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `reviewer_name`, `review_text`, `rating`, `profile_image`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Sarah M.', 'The packaging was absolutely beautiful, and the art piece is even better in person! The attention to detail is incredible.', 5, NULL, 1, 1, '2025-12-23 15:44:43', '2025-12-23 15:44:43'),
(2, 'David K.', 'Professional service and quick delivery. A true artist who understands the business side as well.', 5, NULL, 2, 1, '2025-12-23 15:44:43', '2025-12-23 15:44:43'),
(3, 'Emily R.', 'I purchased a custom piece and the entire process was seamless. Batool was very responsive and brought my vision to life perfectly!', 5, NULL, 3, 1, '2025-12-23 15:44:43', '2025-12-23 15:44:43'),
(4, 'Michael T.', 'Outstanding quality and craftsmanship. The artwork exceeded my expectations. Highly recommended!', 5, NULL, 4, 1, '2025-12-23 15:44:43', '2025-12-23 15:44:43'),
(5, 'Jennifer L.', 'Beautiful artwork with exceptional attention to detail. The colors are vibrant and the piece is exactly as described.', 5, NULL, 5, 1, '2025-12-23 15:44:43', '2025-12-23 15:44:43'),
(6, 'Robert H.', 'Fast shipping, secure packaging, and the most stunning piece of art I\'ve ever purchased. Worth every penny!', 5, NULL, 6, 1, '2025-12-23 15:44:43', '2025-12-23 15:44:43'),
(7, 'Amanda S.', 'I\'ve ordered three pieces now and each one is more beautiful than the last. Truly talented artist!', 5, NULL, 7, 1, '2025-12-23 15:44:43', '2025-12-23 15:44:43'),
(8, 'James P.', 'The custom portrait was absolutely perfect. Great communication throughout the process and delivered on time.', 5, NULL, 8, 1, '2025-12-23 15:44:43', '2025-12-23 15:44:43'),
(9, 'Lisa W.', 'Amazing experience from start to finish. The artwork is museum-quality and I couldn\'t be happier!', 4, NULL, 9, 1, '2025-12-23 15:44:43', '2025-12-23 15:44:43'),
(10, 'Christopher B.', 'Exceptional talent and professionalism. The piece I ordered is the centerpiece of my living room!', 5, NULL, 10, 1, '2025-12-23 15:44:43', '2025-12-23 15:44:43');

-- --------------------------------------------------------

--
-- Table structure for table `youtube_vlogs`
--

CREATE TABLE `youtube_vlogs` (
  `id` int(11) NOT NULL,
  `video_url` varchar(500) NOT NULL,
  `thumbnail_path` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `youtube_vlogs`
--

INSERT INTO `youtube_vlogs` (`id`, `video_url`, `thumbnail_path`, `title`, `display_order`, `updated_at`) VALUES
(1, 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'img/vlogs/vlog_1.jpg', 'Clay Keychain Making', 1, '2025-12-29 13:08:41'),
(2, 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'img/vlogs/vlog_2.jpg', 'Glazing Tools', 2, '2025-12-29 13:08:41'),
(3, 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'img/vlogs/vlog_3.jpg', 'Minnie the Baker', 3, '2025-12-29 13:08:41'),
(4, 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'img/vlogs/vlog_4.jpg', 'Growing my Art Account', 4, '2025-12-29 13:08:41'),
(5, 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'img/vlogs/vlog_5.jpg', 'My Clay Army', 5, '2025-12-29 13:08:41'),
(6, 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'img/vlogs/vlog_6.jpg', 'Whimsy Talk EP1', 6, '2025-12-29 13:08:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_content`
--
ALTER TABLE `about_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_display_order` (`display_order`),
  ADD KEY `idx_section` (`section_name`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username` (`username`);

--
-- Indexes for table `business_images`
--
ALTER TABLE `business_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_image_type` (`image_type`),
  ADD KEY `idx_updated` (`updated_at`);

--
-- Indexes for table `contact_info`
--
ALTER TABLE `contact_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `culture_content`
--
ALTER TABLE `culture_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_section` (`section_name`);

--
-- Indexes for table `custom_requests`
--
ALTER TABLE `custom_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `digital_content`
--
ALTER TABLE `digital_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_content_type` (`content_type`),
  ADD KEY `idx_updated` (`updated_at`);

--
-- Indexes for table `featured_portfolio`
--
ALTER TABLE `featured_portfolio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_display_order` (`display_order`);

--
-- Indexes for table `hero_section`
--
ALTER TABLE `hero_section`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_updated` (`updated_at`);

--
-- Indexes for table `portfolio_categories`
--
ALTER TABLE `portfolio_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_display_order` (`display_order`);

--
-- Indexes for table `portfolio_items`
--
ALTER TABLE `portfolio_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_display_order` (`display_order`),
  ADD KEY `idx_year` (`year`);

--
-- Indexes for table `portfolio_products_order`
--
ALTER TABLE `portfolio_products_order`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `portfolio_item_id` (`portfolio_item_id`),
  ADD KEY `status` (`status`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `shop_categories`
--
ALTER TABLE `shop_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shop_orders`
--
ALTER TABLE `shop_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `shop_products`
--
ALTER TABLE `shop_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `snugglet_id` (`snugglet_id`);

--
-- Indexes for table `shop_settings`
--
ALTER TABLE `shop_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `shop_snugglets`
--
ALTER TABLE `shop_snugglets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_logo`
--
ALTER TABLE `site_logo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_setting_key` (`setting_key`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `youtube_vlogs`
--
ALTER TABLE `youtube_vlogs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_content`
--
ALTER TABLE `about_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `business_images`
--
ALTER TABLE `business_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact_info`
--
ALTER TABLE `contact_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `culture_content`
--
ALTER TABLE `culture_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `custom_requests`
--
ALTER TABLE `custom_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `digital_content`
--
ALTER TABLE `digital_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `featured_portfolio`
--
ALTER TABLE `featured_portfolio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `hero_section`
--
ALTER TABLE `hero_section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `portfolio_categories`
--
ALTER TABLE `portfolio_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `portfolio_items`
--
ALTER TABLE `portfolio_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `portfolio_products_order`
--
ALTER TABLE `portfolio_products_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `shop_categories`
--
ALTER TABLE `shop_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `shop_orders`
--
ALTER TABLE `shop_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `shop_products`
--
ALTER TABLE `shop_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `shop_settings`
--
ALTER TABLE `shop_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `shop_snugglets`
--
ALTER TABLE `shop_snugglets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `site_logo`
--
ALTER TABLE `site_logo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `youtube_vlogs`
--
ALTER TABLE `youtube_vlogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `portfolio_items`
--
ALTER TABLE `portfolio_items`
  ADD CONSTRAINT `fk_portfolio_category` FOREIGN KEY (`category_id`) REFERENCES `portfolio_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shop_orders`
--
ALTER TABLE `shop_orders`
  ADD CONSTRAINT `shop_orders_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `shop_products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
