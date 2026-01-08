# Developer Guide - Batool's Aptitude

> Technical documentation for developers working on the Batool's Aptitude portfolio CMS system.

## 📋 Table of Contents

- [Architecture Overview](#architecture-overview)
- [Code Structure](#code-structure)
- [Database Layer](#database-layer)
- [Admin System](#admin-system)
- [Frontend System](#frontend-system)
- [Helper Functions](#helper-functions)
- [Adding New Features](#adding-new-features)
- [Security Considerations](#security-considerations)
- [Best Practices](#best-practices)
- [Troubleshooting](#troubleshooting)

---

## 🏗️ Architecture Overview

### MVC-Inspired Structure
The project follows a simplified MVC (Model-View-Controller) pattern:

- **Model**: Database layer (`config/database.php`, `admin/config/database.php`)
- **View**: PHP templates with inline HTML
- **Controller**: PHP logic in page files

### Request Flow

```
User Request
    ↓
index.php / admin/pages/*.php
    ↓
Database Query (config/database.php)
    ↓
Data Fetch
    ↓
HTML Template Rendering
    ↓
Response to User
```

### Database-Driven Design Philosophy
**Everything is in the database** - no hardcoded content. This allows:
- Real-time updates via admin
- Easy content management
- SEO-friendly dynamic content
- Scalable architecture

---

## 📂 Code Structure

### Frontend Structure

```php
// index.php - Homepage
<?php
// 1. Include database config
require_once 'config/database.php';

// 2. Fetch all content from database
$hero = fetch_hero_content();
$portfolio_categories = fetch_portfolio();
// ...etc

// 3. Render HTML with fetched data
?>
<!DOCTYPE html>
<html>
  <!-- Template uses $hero, $portfolio_categories, etc -->
</html>
```

### Admin Structure

```php
// admin/pages/hero-manager.php
<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../config/database.php';

require_login(); // Protect route

// Handle POST (form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form data
    // Update database
    // Redirect with success message
}

// Fetch current data
$data = fetch_data();

// Include admin header (with navigation)
include '../includes/header.php';
?>

<!-- HTML Form -->
<form method="POST" enctype="multipart/form-data">
  <!-- Form fields -->
</form>

<?php include '../includes/footer.php'; ?>
```

### API Structure (`api/`)

- `get_shop_data.php`: Returns JSON data for dynamic shop interactions (modals, filtering) without page reloads.

### External Libraries (`includes_php/`)

- `PHPMailer`: Handles email transmission for the contact form.

---

## 🗄️ Database Layer

### Core Tables

#### 1. `admin_users`
- `id`, `username`, `password`, `email`, `created_at`, `last_login`

#### 2. `hero_section`
- `main_title`, `subtitle`, `typewriter_text`, `profile_image`, `background_image`, `cta_text`, `quote_text`

#### 3. `about_content`
- `section_name`, `section_title`, `content_text`, `display_order`

#### 4. `portfolio_categories`
- `category_name`, `icon_name`, `display_order`

#### 5. `portfolio_items`
- `category_id`, `title`, `slug`, `description`, `long_description`, `image_path`, `year`, `materials`, `dimensions`, `price`, `status`, `youtube_url`

#### 6. `featured_portfolio`
- `image_path`, `title`, `display_order`

#### 7. `business_images`
- `image_type`, `image_path`, `caption`, `display_order`

#### 8. `digital_content`
- `content_type`, `image_path`, `title`, `description`, `display_order`

#### 9. `culture_content`
- `section_name`, `section_title`, `content_text`, `image_path`

#### 10. `contact_info`
- `email`, `location`, `social_instagram`, `social_youtube`, `social_linkedin`

#### 11. `shop_categories`
- `name`, `slug`, `image_path`, `display_order`

#### 12. `shop_snugglets`
- `name`, `slug`, `image_path`, `bg_color`, `display_order`

#### 13. `shop_products`
- `name`, `subtitle`, `description`, `detailed_description`, `materials`, `dimensions`, `price`, `is_sold_out` (0/1), `image_path`, `category_id`, `snugglet_id`

#### 14. `testimonials`
- `reviewer_name`, `review_text`, `rating`, `display_order`, `is_active`

### Connection File: `config/database.php`

```php
// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'batool');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset("utf8mb4");

// Helper function
function get_data($table, $where = '') {
    global $conn;
    $sql = "SELECT * FROM `$table`";
    if ($where) {
        $sql .= " WHERE $where";
    }
    return $conn->query($sql);
}
```

### Admin Database Functions: `admin/config/database.php`

```php
// Prepared statement wrapper
function db_query($sql, $params = [], $types = '') {
    global $conn;
    $stmt = $conn->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt;
}

// Fetch single row
function db_fetch_single($sql, $params = [], $types = '') {
    $stmt = db_query($sql, $params, $types);
    $result = $stmt->get_result();
    return $result ? $result->fetch_assoc() : null;
}

// Fetch all rows
function db_fetch_all($sql, $params = [], $types = '') {
    $stmt = db_query($sql, $params, $types);
    $result = $stmt->get_result();
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
```

### Type Strings for Prepared Statements

| Type | PHP Type | Example |
|------|----------|---------|
| `i` | Integer | `user_id`, `display_order` |
| `d` | Double/Float | `price`, `rating` |
| `s` | String | `title`, `description` |
| `b` | Blob | Binary data |

**Example**:
```php
// [$title, $description, $price, $id]
// Types: 's', 's', 'd', 'i'
$stmt = db_query($sql, [$title, $description, $price, $id], 'ssdi');
```

---

## 🔐 Admin System

### Authentication Flow

#### 1. Login (`admin/login.php`)
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if (login_user($username, $password)) {
        // Session created, redirect to dashboard
        header('Location: /Batool/admin/index.php');
    } else {
        $error = "Invalid credentials";
    }
}
```

#### 2. Session Management (`admin/includes/auth.php`)
```php
function login_user($username, $password) {
    // Fetch user from database
    // Verify password (currently plain text - see Security)
    // Set session variables
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = $user['id'];
    $_SESSION['admin_username'] = $user['username'];
    return true;
}
```

#### 3. Route Protection
```php
require_login(); // At top of every admin page

function require_login() {
    if (!is_logged_in()) {
        header('Location: /Batool/admin/login.php');
        exit();
    }
}
```

### Admin Page Pattern

Every admin manager follows this pattern:

```php
<?php
// 1. Initialization
session_start();
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../config/database.php';

require_login();

$page_title = 'Section Manager';
$current_page = 'section'; // For active nav highlight

// 2. Handle POST (Updates)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $field = $_POST['field'] ?? '';
    
    // Handle file upload (if applicable)
    if (isset($_FILES['image'])) {
        $upload = upload_image($_FILES['image'], 'img/section/');
        if ($upload['success']) {
            $image_path = $upload['path'];
        }
    }
    
    // Update database
    $sql = "UPDATE table SET field = ? WHERE id = ?";
    db_query($sql, [$field, $id], 'si');
    
    // Redirect with success message
    redirect_with_message('/Batool/admin/pages/manager.php', 'Updated successfully!', 'success');
}

// 3. Fetch Current Data
$data = db_fetch_single("SELECT * FROM table WHERE id = 1");

// 4. Include Header
include '../includes/header.php';
?>

<!-- 5. HTML Form -->
<div class="content-box">
    <h2><?php echo $page_title; ?></h2>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="field">Field Name</label>
            <input type="text" id="field" name="field" 
                   value="<?php echo h($data['field']); ?>" 
                   class="form-control" required>
        </div>
        
        <!-- Image Upload (if applicable) -->
        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" id="image" name="image" accept="image/*">
            <div class="current-image">
                <img src="<?php echo get_image_url($data['image_path']); ?>">
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
```

---

## 🎨 Frontend System

### Homepage Data Fetching (`index.php`)

```php
<?php
require_once 'config/database.php';

// Helper function for safe output
function e($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

// Fetch hero section
$hero_result = get_data('hero_section', 'id = 1');
$hero = $hero_result ? $hero_result->fetch_assoc() : null;

// Fetch portfolio categories
$portfolio_categories = [];
$categories_result = get_data('portfolio_categories', '1=1 ORDER BY display_order');
if ($categories_result) {
    while ($cat = $categories_result->fetch_assoc()) {
        $cat['items'] = [];
        
        // Fetch items for this category
        $items_result = get_data('portfolio_items', 
            "category_id = {$cat['id']} ORDER BY display_order");
        
        if ($items_result) {
            while ($item = $items_result->fetch_assoc()) {
                $cat['items'][] = $item;
            }
        }
        
        $portfolio_categories[] = $cat;
    }
}

// Fetch featured portfolio  
$featured_result = get_data('featured_portfolio', 
    '1=1 ORDER BY display_order LIMIT 6');
$featured_items = [];
if ($featured_result) {
    while ($row = $featured_result->fetch_assoc()) {
        $featured_items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo e($hero['main_title']); ?></title>
</head>
<body>
    <!-- Use fetched data in templates -->
    <h1><?php echo e($hero['main_title']); ?></h1>
    
    <!-- Portfolio Categories -->
    <?php foreach ($portfolio_categories as $category): ?>
        <h2><?php echo e($category['category_name']); ?></h2>
        
        <?php foreach ($category['items'] as $item): ?>
            <a href="/Batool/portfolio-detail.php?id=<?php echo $item['id']; ?>">
                <img src="<?php echo e($item['image_path']); ?>">
                <h3><?php echo e($item['title']); ?></h3>
            </a>
        <?php endforeach; ?>
    <?php endforeach; ?>
</body>
</html>
```

### Portfolio Detail Page Pattern

```php
<?php
require_once 'config/database.php';

// Get item ID from URL
$id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch item
$result = get_data('portfolio_items', "id = $id");
$item = $result ? $result->fetch_assoc() : null;

if (!$item) {
    header('Location: /Batool/#portfolio');
    exit;
}

// Fetch category
$cat_result = get_data('portfolio_categories', "id = {$item['category_id']}");
$category = $cat_result ? $cat_result->fetch_assoc() : null;
?>
```

---

## 🛠️ Helper Functions

### Output Safety: `h()`
```php
// Always use when outputting user data
echo h($data['title']);

// Prevents XSS attacks
// Input: <script>alert('XSS')</script>
// Output: &lt;script&gt;alert('XSS')&lt;/script&gt;
```

### Image Upload: `upload_image()`
```php
$result = upload_image($_FILES['image'], 'img/portfolio/');

if ($result['success']) {
    $image_path = $result['path']; // e.g., img/portfolio/photo.jpg
} else {
    $error = $result['message'];
}

// Validation includes:
// - File type (jpg, jpeg, png, gif, webp)
// - File size (max 5MB)
// - Is actual image
// - Safe filename generation
```

### Flash Messages
```php
// Set message
redirect_with_message('/admin/page.php', 'Success!', 'success');

// Display message
echo display_flash_message(); // In header.php
```

### Database Helpers
```php
// Single row
$item = db_fetch_single(
    "SELECT * FROM portfolio_items WHERE id = ?", 
    [$id], 
    'i'
);

// Multiple rows
$items = db_fetch_all(
    "SELECT * FROM portfolio_items WHERE category_id = ? ORDER BY display_order",
    [$category_id],
    'i'
);

// Insert/Update/Delete
db_query(
    "UPDATE portfolio_items SET title = ?, price = ? WHERE id = ?",
    [$title, $price, $id],
    'sdi' // string, double, integer
);
```

---

## ➕ Adding New Features

### Adding a New Admin Manager

**Example**: Add "Testimonials" section

#### 1. Create Database Table
```sql
CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_name` varchar(255) NOT NULL,
  `client_role` varchar(255) DEFAULT NULL,
  `testimonial_text` text NOT NULL,
  `client_image` varchar(255) DEFAULT NULL,
  `rating` int(1) DEFAULT 5,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### 2. Create Admin Manager: `admin/pages/testimonials-manager.php`
```php
<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../config/database.php';

require_login();

$page_title = 'Testimonials Manager';
$current_page = 'testimonials';

// Handle ADD/EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $client_name = $_POST['client_name'] ?? '';
    $testimonial_text = $_POST['testimonial_text'] ?? '';
    
    if ($id) {
        // UPDATE
        $sql = "UPDATE testimonials SET client_name = ?, testimonial_text = ? WHERE id = ?";
        db_query($sql, [$client_name, $testimonial_text, $id], 'ssi');
    } else {
        // INSERT
        $sql = "INSERT INTO testimonials (client_name, testimonial_text) VALUES (?, ?)";
        db_query($sql, [$client_name, $testimonial_text], 'ss');
    }
    
    redirect_with_message('testimonials-manager.php', 'Saved successfully!', 'success');
}

// Fetch all testimonials
$testimonials = db_fetch_all("SELECT * FROM testimonials ORDER BY display_order");

include '../includes/header.php';
?>

<!-- Display list and add/edit form -->

<?php include '../includes/footer.php'; ?>
```

#### 3. Add Menu Item: `admin/includes/header.php`
```php
<li>
    <a href="/Batool/admin/pages/testimonials-manager.php" 
       class="<?php echo ($current_page ?? '') === 'testimonials' ? 'active' : ''; ?>">
        <i data-lucide="message-square"></i>
        <span>Testimonials</span>
    </a>
</li>
```

#### 4. Display on Frontend: `index.php`
```php
// Fetch testimonials
$testimonials_result = get_data('testimonials', '1=1 ORDER BY display_order LIMIT 3');
$testimonials = [];
if ($testimonials_result) {
    while ($row = $testimonials_result->fetch_assoc()) {
        $testimonials[] = $row;
    }
}

// In HTML
foreach ($testimonials as $test): ?>
    <div class="testimonial">
        <p><?php echo e($test['testimonial_text']); ?></p>
        <strong><?php echo e($test['client_name']); ?></strong>
    </div>
<?php endforeach;
```

---

## 🔒 Security Considerations

### ⚠️ CRITICAL - Password Hashing
**Current State**: Passwords stored in plain text
```php
// Current (INSECURE)
if ($password === $user['password']) { ... }
```

**Production Fix** (Required before deploying):
```php
// Registration
$hashed = password_hash($password, PASSWORD_BCRYPT);
db_query("INSERT INTO admin_users (username, password) VALUES (?, ?)", 
         [$username, $hashed], 'ss');

// Login
if (password_verify($password, $user['password'])) {
    // Authenticated
}
```

### SQL Injection Prevention
✅ **Already Implemented**: Using prepared statements
```php
// SAFE
$stmt = db_query("SELECT * FROM users WHERE id = ?", [$id], 'i');

// UNSAFE (Never do this)
$result = $conn->query("SELECT * FROM users WHERE id = $id");
```

### XSS Prevention
✅ **Already Implemented**: Using `h()` helper
```php
// SAFE
echo h($user_input);

// UNSAFE
echo $user_input;
```

### File Upload Security
✅ **Already Implemented**: Validation in `upload_image()`
- File type whitelist
- File size limit (5MB)
- Image verification
- Safe filename sanitization

### Additional Recommendations

1. **Add CSRF Protection** (functions already exist):
```php
// In form
<input type="hidden" name="csrf_token" 
       value="<?php echo generate_csrf_token(); ?>">

// On submit
if (!verify_csrf_token($_POST['csrf_token'])) {
    die('CSRF token validation failed');
}
```

2. **HTTPS in Production**: Force HTTPS
```php
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit;
}
```

3. **Session Security**:
```php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);   // Only over HTTPS
ini_set('session.use_only_cookies', 1);
```

---

## ✅ Best Practices

### 1. Always Use Prepared Statements
```php
// ✅ GOOD
db_query("SELECT * FROM items WHERE id = ?", [$id], 'i');

// ❌ BAD
$conn->query("SELECT * FROM items WHERE id = $id");
```

### 2. Always Escape Output
```php
// ✅ GOOD
echo h($data['title']);

// ❌ BAD
echo $data['title'];
```

### 3. Validate User Input
```php
// Check required fields
if (empty($_POST['title'])) {
    die('Title is required');
}

// Validate integers
$id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Invalid email');
}
```

### 4. Use Consistent Naming
- **Database**: `snake_case` (e.g., `portfolio_items`, `category_id`)
- **PHP Variables**: `snake_case` (e.g., `$portfolio_items`, `$category_id`)
- **Functions**: `snake_case` (e.g., `upload_image()`, `db_fetch_single()`)
- **CSS Classes**: `kebab-case` (e.g., `.content-box`, `.glass-card`)

### 5. Comment Your Code
```php
/**
 * Upload image file to server
 * 
 * @param array $file The $_FILES array element
 * @param string $target_dir Target directory (e.g., 'img/portfolio/')
 * @return array ['success' => bool, 'message' => string, 'path' => string]
 */
function upload_image($file, $target_dir) { ... }
```

---

## 🐛 Troubleshooting

### Database Connection Errors

**Error**: `Connection failed: Access denied for user 'root'@'localhost'`

**Fix**: Check `config/database.php` credentials

**Error**: `Unknown database 'batool'`

**Fix**: Import `database/batool.sql` in phpMyAdmin

### Image Upload Issues

**Error**: `Failed to upload image`

**Fix**: Check folder permissions
```bash
chmod -R 777 img/
chmod -R 777 uploads/
```

**Error**: `File size must be less than 5MB`

**Fix**: Reduce image size or increase limit in `helpers.php`

### Session Issues

**Error**: Session not persisting

**Fix**: Ensure `session_start()` is at the very top of PHP files (before any output)

### URL Routing Issues

**Error**: 404 on admin pages

**Fix**: Check `.htaccess` files exist and Apache `mod_rewrite` is enabled

---

## 📚 Additional Resources

### Database Management
- **phpMyAdmin**: `http://localhost/phpmyadmin`
- **Export Database**: Regularly backup via phpMyAdmin → Export

### Tailwind CSS
- **Docs**: https://tailwindcss.com/docs
- **CDN** (used in project): `https://cdn.tailwindcss.com`

### Lucide Icons
- **Browse Icons**: https://lucide.dev/icons
- **Usage**: `<i data-lucide="icon-name"></i>`

### PHP Documentation
- **MySQLi**: https://www.php.net/manual/en/book.mysqli.php
- **File Upload**: https://www.php.net/manual/en/features.file-upload.php
- **Sessions**: https://www.php.net/manual/en/book.session.php

---

## 🚀 Deployment Checklist

Before deploying to production:

- [ ] Hash admin passwords using `password_hash()`
- [ ] Enable HTTPS
- [ ] Secure session cookies
- [ ] Add CSRF protection to all forms
- [ ] Set proper file permissions (644 for files, 755 for directories)
- [ ] Remove `test-*.php` files
- [ ] Update database credentials in `config/database.php`
- [ ] Change default admin password
- [ ] Test all admin functionality
- [ ] Test all frontend pages
- [ ] Optimize images
- [ ] Enable error logging (disable display_errors)
- [ ] Create database backup strategy

---

**Developer Documentation maintained by the Batool's Aptitude development team**

*Last Updated: December 2025*
