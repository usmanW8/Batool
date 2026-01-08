<?php
/**
 * Database Configuration for Admin Panel
 * Batool Portfolio CMS
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'batool');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Error. Please check your configuration.");
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Prepared statement helper function
function db_query($sql, $params = [], $types = '') {
    global $conn;
    
    if (empty($params)) {
        return $conn->query($sql);
    }
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        return false;
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    return $stmt;
}

// Fetch single row
function db_fetch_single($sql, $params = [], $types = '') {
    global $conn;
    
    if (empty($params)) {
        $result = $conn->query($sql);
        if ($result === false) {
            return null;
        }
        return $result->fetch_assoc();
    }
    
    $stmt = db_query($sql, $params, $types);
    if ($stmt === false) {
        return null;
    }
    
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Fetch all rows
function db_fetch_all($sql, $params = [], $types = '') {
    global $conn;
    
    if (empty($params)) {
        $result = $conn->query($sql);
        if ($result === false) {
            return [];
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    $stmt = db_query($sql, $params, $types);
    if ($stmt === false) {
        return [];
    }
    
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Escape string
function escape_string($str) {
    global $conn;
    return $conn->real_escape_string($str);
}
?>
