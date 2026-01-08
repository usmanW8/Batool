# Batool's Aptitude - Artist Portfolio & CMS

> A comprehensive portfolio website with full-featured admin dashboard for managing content, built for creative artists and cultural entrepreneurs.

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Usage](#usage)
- [Database Schema](#database-schema)
- [Admin Features](#admin-features)
- [Contributing](#contributing)

---

## 🎨 Overview

**Batool's Aptitude** is a professional portfolio website showcasing the work of Khansa Batool, a creative artist and cultural entrepreneur. The site features a fully dynamic content management system that allows the artist to manage all content through an intuitive admin dashboard.

### Key Highlights:
- **100% Database-Driven**: All content is managed through MySQL database
- **Comprehensive Admin CMS**: Edit every section of the website without touching code
- **Portfolio Management**: Category-based portfolio with detailed item pages
- **Responsive Design**: Beautiful UI using Tailwind CSS
- **SEO-Friendly**: Clean URLs, meta tags, and semantic HTML

---

## ✨ Features

### Frontend Features
- **Hero Section**: Dynamic typewriter effect with custom messaging
- **About Section**: Three-part narrative (Background, Why Art, Future Vision)
- **Portfolio System**: 
  - 5 categories (Sketching & Painting, Clay & Jewellery, Photography, Sculpture & Ceramics, Digital Illustration)
  - 15 portfolio items with detailed pages
  - **Multimedia Support**: YouTube video embedding for artworks
  - Featured homepage gallery
  - Dynamic category filtering
- **Shop Section**:
  - Organized by Product Categories and Snugglets 
  - Sold Out status indicators
- **Testimonials Board**: Interactive carousel showing client reviews
- **Business Section**: Product showcase with image gallery
- **Digital Experience**: Mockup and content displays
- **Culture & Vision**: Multi-section cultural narrative
- **Contact Information**: Email, location, social media links

### Admin Features
- **Secure Authentication**: Login/logout system with session management
- **Dashboard Overview**: Quick stats and navigation
- **12 Content Managers**:
  1. Hero Section Manager
  2. About Section Manager
  3. Portfolio Manager (Category-based)
  4. Featured Portfolio Manager
  5. Business Section Manager
  6. Digital Experience Manager
  7. Culture & Vision Manager
  8. Contact Info Manager
  9. Portfolio Category Manager
  10. Portfolio Item Editor (with Video Support)
  11. Testimonials Manager
  12. Shop Product Manager (Availability Status)

---

## 🛠️ Tech Stack

### Frontend
- **HTML5** - Semantic markup
- **Tailwind CSS** - Utility-first CSS framework
- **JavaScript** - Interactive elements & AJAX
- **Lucide Icons** - Icon library
- **Google Fonts** - Playfair Display & Lato

### Backend
- **PHP 8.2+** - Server-side logic
- **MySQL/MariaDB** - Database
- **Apache** - Web server (XAMPP)
- **PHPMailer** - Email transmission library

### Development
- **XAMPP** - Local development environment
- **phpMyAdmin** - Database management

---

## 📁 Project Structure

```
Batool/
├── admin/                          # Admin dashboard
│   ├── assets/                     # Admin CSS/JS
│   ├── config/                     # Admin configuration
│   ├── includes/                   # Admin components
│   │   ├── auth.php               # Authentication logic
│   │   ├── footer.php             # Admin footer
│   │   ├── header.php             # Admin header & navigation
│   │   └── helpers.php            # Helper functions
│   ├── pages/                      # Admin manager pages
│   │   ├── about-manager.php      # Manage about content
│   │   ├── business-manager.php   # Manage business section
│   │   ├── contact-manager.php    # Manage contact info
│   │   ├── culture-manager.php    # Manage culture content
│   │   ├── digital-manager.php    # Manage digital section
│   │   ├── featured-manager.php   # Manage featured images
│   │   ├── hero-manager.php       # Manage hero section
│   │   ├── portfolio-manager.php  # Manage categories
│   │   ├── portfolio-category.php # Category item list
│   │   ├── portfolio-edit-item.php # Edit portfolio items
│   │   ├── shop-manager.php       # Manage shop products
│   │   └── testimonials-manager.php # Manage reviews
│   ├── index.php                  # Admin dashboard home
│   ├── login.php                  # Admin login page
│   └── logout.php                 # Logout handler
├── api/                            # API Endpoints
│   └── get_shop_data.php          # Fetch product data via AJAX
├── config/                         # Application configuration
│   └── database.php               # Database connection
├── css/                            # Frontend styles
│   └── style.css                  # Custom styles
├── database/                       # Database files
│   └── batool.sql                 # Complete database dump
├── img/                            # Image assets
│   ├── business/                  # Business section images
│   ├── culture/                   # Culture section images
│   ├── digital/                   # Digital section images
│   ├── hero/                      # Hero section images
│   └── portfolio/                 # Portfolio images
├── includes_php/                   # PHP Libraries
│   └── PHPMailer/                 # Mail sending library
├── js/                             # Frontend scripts
│   └── main.js                    # Main JavaScript
├── uploads/                        # Admin uploaded files
├── index.php                       # Homepage (frontend)
├── portfolio-detail.php            # Portfolio item detail page
├── shop-category.php              # Shop category view
├── shop-product.php               # Individual product view
├── shop-snugglet.php              # Snugglet view
└── .htaccess                       # URL rewriting rules
```

---

## 🚀 Installation

### Prerequisites
- **XAMPP** (or any PHP 8+ & MySQL environment)
- **Web Browser** (Chrome, Firefox, Edge, etc.)

### Step-by-Step Setup

#### 1. Clone/Download Project
```bash
# Place project in XAMPP htdocs folder
C:\xampp\htdocs\Batool\
```

#### 2. Import Database
1. Start **XAMPP** (Apache & MySQL)
2. Open **phpMyAdmin**: `http://localhost/phpmyadmin`
3. Create new database named: `batool`
4. Import SQL file:
   - Click on `batool` database
   - Go to **Import** tab
   - Choose file: `database/batool.sql`
   - Click **Go**

#### 3. Configure Database Connection
Edit `config/database.php` if needed (default settings work for XAMPP):
```php
$host = 'localhost';
$dbname = 'batool';
$username = 'root';      // Default XAMPP username
$password = '';          // Default XAMPP password (empty)
```

#### 4. Set Permissions (if on Linux/Mac)
```bash
chmod -R 755 Batool/
chmod -R 777 Batool/uploads/
chmod -R 777 Batool/img/
```

#### 5. Access the Website
- **Frontend**: `http://localhost/Batool/`
- **Admin**: `http://localhost/Batool/admin/`

### Default Admin Credentials
```
Username: admin
Password: admin123
```
⚠️ **Important**: Change default password after first login!

---

## 📖 Usage

### Frontend Navigation
- Browse portfolio categories
- Click on items for detailed view
- View artist information and vision
- Contact information and social links

### Admin Dashboard Workflow

#### Logging In
1. Go to: `localhost/Batool/admin/`
2. Enter credentials
3. Access dashboard

#### Managing Content

**Hero Section**:
- Update main title, subtitle
- Change typewriter text (comma-separated)
- Upload profile and background images
- Edit call-to-action text
- Update quote

**Portfolio**:
1. **Categories**: Click "Portfolio" → See all categories → Click "Edit" to change name/icon
2. **Items**: Click "View Items" on a category → See all artworks → Click "Edit Item"
3. **Edit Details**: Update title, descriptions, materials, price, status, image
4. **Featured**: Click "Featured Portfolio" → Edit 6 homepage showcase images

**Business/Digital/Culture/Contact**:
- Upload/replace images
- Edit text content
- Update descriptions

---

## 🗄️ Database Schema

### Core Tables (10)

#### 1. `admin_users`
Admin authentication
- `id`, `username`, `password`, `email`, `created_at`, `last_login`

#### 2. `hero_section`
Homepage hero content
- `main_title`, `subtitle`, `typewriter_text`, `profile_image`, `background_image`, `cta_text`, `quote_text`

#### 3. `about_content`
About section (3 subsections)
- `section_name`, `section_title`, `content_text`, `display_order`

#### 4. `portfolio_categories`
Portfolio categories
- `category_name`, `icon_name`, `display_order`

#### 5. `portfolio_items`
Portfolio artworks
- `category_id`, `title`, `slug`, `description`, `long_description`, `image_path`, `year`, `materials`, `dimensions`, `price`, `status`

#### 6. `featured_portfolio`
Homepage featured gallery (6 images)
- `image_path`, `title`, `display_order`

#### 7. `business_images`
Business section gallery
- `image_type`, `image_path`, `caption`, `display_order`

#### 8. `digital_content`
Digital experience section
- `content_type`, `image_path`, `title`, `description`, `display_order`

#### 9. `culture_content`
Culture & vision content
- `section_name`, `section_title`, `content_text`, `image_path`

#### 10. `contact_info`
Contact information
- `email`, `location`, `social_instagram`, `social_youtube`, `social_linkedin`

---

## 🔐 Admin Features

### Authentication System
default username and password is admin & admin123
- Secure session management
- Login/logout functionality
- Access control for admin routes
- Password hashing (⚠️ **Note**: Currently plain text - should be hashed in production)

### Content Management
- **WYSIWYG-style editing**: Direct text editing in forms
- **Image Upload**: File upload with preview
- **Image Replacement**: Update images without deleting old ones
- **Real-time Preview**: Changes reflect immediately on frontend

### Navigation Flow

```
Dashboard (index.php)
├── Hero Section (hero-manager.php)
├── About Section (about-manager.php)
├── Portfolio (portfolio-manager.php)
│   └── Category Items (portfolio-category.php)
│       └── Edit Item (portfolio-edit-item.php)
├── Featured Portfolio (featured-manager.php)
├── Business Section (business-manager.php)
├── Digital Experience (digital-manager.php)
├── Culture & Vision (culture-manager.php)
└── Contact Info (contact-manager.php)
```

---

## 🎯 Key Concepts

### Database-Driven Design
Every piece of content is stored in MySQL and fetched dynamically. No hardcoded content in HTML.

### Category-First Portfolio
Portfolio system uses a drill-down approach:
1. **Categories Overview** → See all portfolio categories
2. **Category Items** → See all artworks in a category
3. **Item Details** → Edit individual artwork

### Unified Image Management
- Frontend pulls images from `img/` directory
- Admin uploads append to same directory
- Single source of truth for all images

### Materials vs Medium
- **Materials field** displays on BOTH portfolio grid and detail pages
- **Medium field** (legacy) - not currently used on frontend

---

## 🤝 Contributing

### For Developers
See [DEVELOPER_GUIDE.md](./DEVELOPER_GUIDE.md) for:
- Code architecture details
- Adding new features
- Database conventions
- PHP helper functions
- Security best practices

---

## 📄 License

This project is proprietary software developed for Khansa Batool / Batool's Aptitude.

---


---

## 🔄 Version History

- **v1.0** (December 2025) - Initial release with full CMS
  - 10 database tables
  - 10 admin managers
  - 5 portfolio categories
  - 15 portfolio items
  - Fully responsive design

---

**Built with ❤️ for Artists by Artists**
