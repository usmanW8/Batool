<?php
/**
 * Helper Functions for Admin Panel
 */

/**
 * Sanitize output for HTML display
 */
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Format date for display
 */
function format_date($date) {
    return date('M d, Y', strtotime($date));
}

/**
 * Format datetime for display
 */
function format_datetime($datetime) {
    return date('M d, Y g:i A', strtotime($datetime));
}

/**
 * Check if image file exists
 */
function image_exists($path) {
    $full_path = $_SERVER['DOCUMENT_ROOT'] . '/Batool/' . $path;
    return file_exists($full_path);
}

/**
 * Get image URL
 */
function get_image_url($path) {
    return '/Batool/' . $path;
}

/**
 * Generate success message
 */
function success_message($message) {
    return '<div class="alert alert-success">' . h($message) . '</div>';
}

/**
 * Generate error message
 */
function error_message($message) {
    return '<div class="alert alert-error">' . h($message) . '</div>';
}

/**
 * Redirect with message
 */
function redirect_with_message($url, $message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
    header('Location: ' . $url);
    exit();
}

/**
 * Display flash message
 */
function display_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'success';
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        if ($type === 'success') {
            return success_message($message);
        } else {
            return error_message($message);
        }
    }
    return '';
}

/**
 * Upload image file
 */
function upload_image($file, $target_dir, $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp']) {
    $target_dir = rtrim($target_dir, '/') . '/';
    $file_name = basename($file['name']);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $file_size = $file['size'];
    $file_tmp = $file['tmp_name'];
    
    // Validate file extension
    if (!in_array($file_ext, $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowed_types)];
    }
    
    // Validate file size (5MB max)
    if ($file_size > 5242880) {
        return ['success' => false, 'message' => 'File size must be less than 5MB'];
    }
    
    // Validate it's an actual image
    $check = getimagesize($file_tmp);
    if ($check === false) {
        return ['success' => false, 'message' => 'File is not a valid image'];
    }
    
    // Create directory if doesn't exist
    $full_dir = $_SERVER['DOCUMENT_ROOT'] . '/Batool/' . $target_dir;
    if (!is_dir($full_dir)) {
        mkdir($full_dir, 0755, true);
    }
    
    // Generate safe filename
    $safe_name = preg_replace('/[^a-zA-Z0-9-_\.]/', '', $file_name);
    $target_file = $full_dir . $safe_name;
    
    // Move uploaded file
    if (move_uploaded_file($file_tmp, $target_file)) {
        return [
            'success' => true,
            'message' => 'Image uploaded successfully',
            'path' => $target_dir . $safe_name
        ];
    } else {
        return ['success' => false, 'message' => 'Failed to upload image'];
    }
}

/**
 * Delete image file
 */
function delete_image($path) {
    $full_path = $_SERVER['DOCUMENT_ROOT'] . '/Batool/' . $path;
    if (file_exists($full_path)) {
        return unlink($full_path);
    }
    return false;
}
?>
