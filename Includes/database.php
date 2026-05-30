<?php
// Start session FIRST
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'UbuntuBay_db'; // Change this to your actual database name

// Create MySQLi connection
$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

// ALSO create PDO connection (for products.php and other modern code)
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Don't die, just log - the page will still work with mysqli
    error_log("PDO Connection failed: " . $e->getMessage());
}
?>