<?php
/**
 * Login Debug Script
 * This will help us find out why login is failing
 */

require_once 'config/database.php';

echo "<h2>Login Debug Test</h2>";
echo "<pre>";

// Test 1: Database Connection
echo "1. Testing database connection...\n";
if ($conn->connect_error) {
    echo "   ❌ FAILED: " . $conn->connect_error . "\n";
} else {
    echo "   ✅ SUCCESS: Connected to database 'batool'\n";
}

// Test 2: Check if admin_users table exists
echo "\n2. Checking if admin_users table exists...\n";
$result = $conn->query("SHOW TABLES LIKE 'admin_users'");
if ($result->num_rows > 0) {
    echo "   ✅ SUCCESS: admin_users table exists\n";
} else {
    echo "   ❌ FAILED: admin_users table does not exist\n";
}

// Test 3: Check if admin user exists
echo "\n3. Checking for admin user...\n";
$stmt = $conn->prepare("SELECT id, username, password, email FROM admin_users WHERE username = ?");
$username = 'admin';
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "   ✅ SUCCESS: Admin user found\n";
    echo "   User ID: " . $user['id'] . "\n";
    echo "   Username: " . $user['username'] . "\n";
    echo "   Email: " . $user['email'] . "\n";
    echo "   Password Hash: " . substr($user['password'], 0, 20) . "...\n";
    
    // Test 4: Test password verification
    echo "\n4. Testing password verification...\n";
    $test_password = 'admin123';
    
    echo "   Testing password: 'admin123'\n";
    echo "   Hash from DB: " . $user['password'] . "\n";
    
    if (password_verify($test_password, $user['password'])) {
        echo "   ✅ SUCCESS: Password 'admin123' is CORRECT!\n";
        echo "\n";
        echo "=========================================\n";
        echo "LOGIN SHOULD WORK WITH:\n";
        echo "Username: admin\n";
        echo "Password: admin123\n";
        echo "=========================================\n";
    } else {
        echo "   ❌ FAILED: Password verification failed\n";
        echo "\n   Let's create a new hash for 'admin123':\n";
        $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
        echo "   New hash: $new_hash\n";
        echo "\n   Run this SQL to update:\n";
        echo "   UPDATE admin_users SET password = '$new_hash' WHERE username = 'admin';\n";
    }
} else {
    echo "   ❌ FAILED: No admin user found\n";
    echo "\n   Run this SQL to create admin user:\n";
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    echo "   INSERT INTO admin_users (username, password, email) VALUES ('admin', '$hash', 'admin@batoolsaptitude.com');\n";
}

echo "</pre>";
?>
