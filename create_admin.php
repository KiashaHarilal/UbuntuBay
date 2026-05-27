<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/database.php';

echo "<h1>Admin Setup</h1>";

// Admin credentials
$name = 'Administrator';
$email = 'admin@UbuntuBay.co.za';
$password = 'admin123';
$role = 'admin';
$province = 'KwaZulu-Natal';

// Generate proper password hash
$password_hash = password_hash($password, PASSWORD_DEFAULT);

echo "Generated hash: " . $password_hash . "<br>";

// Check connection
if (!$conn) {
    die("Database connection failed!");
}

// Check if admin already exists
$check = mysqli_query($conn, "SELECT user_id FROM users WHERE email = '$email'");

if (!$check) {
    die("Query failed: " . mysqli_error($conn));
}

if (mysqli_num_rows($check) > 0) {
    echo "Admin exists, updating password...<br>";
    $sql = "UPDATE users SET password_hash = '$password_hash', role = 'admin' WHERE email = '$email'";
} else {
    echo "Creating new admin...<br>";
    $sql = "INSERT INTO users (name, email, password_hash, role, province, is_active) 
            VALUES ('$name', '$email', '$password_hash', '$role', '$province', 1)";
}

if (mysqli_query($conn, $sql)) {
    echo "<h2 style='color:green;'>✅ Admin Created Successfully!</h2>";
    echo "<p><strong>Email:</strong> admin@UbuntuBay.co.za</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<hr>";
    echo "<a href='admin/login.php' style='background:#ffd175; color:#000; padding:10px 20px; text-decoration:none; border-radius:25px;'>Go to Admin Login →</a>";
} else {
    echo "<h2 style='color:red;'>Error:</h2>";
    echo mysqli_error($conn);
}
?>