<?php
/**
 * Database Configuration for Frontend
 * Batool Portfolio Website
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
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for proper emoji and special character support
$conn->set_charset("utf8mb4");

// Function to safely escape strings
function escape_string($str) {
    global $conn;
    return $conn->real_escape_string($str);
}

// Function to safely get data
function get_data($table, $where = '') {
    global $conn;
    $sql = "SELECT * FROM `$table`";
    if ($where) {
        $sql .= " WHERE $where";
    }
    $result = $conn->query($sql);
    return $result;
}
?>
