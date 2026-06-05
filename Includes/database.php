<?php
// Start session FIRST
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auto-detect environment (localhost vs online server)
$is_local = ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_ADDR'] == '127.0.0.1');

if ($is_local) {
    // LOCALHOST settings (XAMPP)
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $dbname = 'UbuntuBay_db'; // Change this to your local database name (kasklart_db if that's what you have)
    $port = 3306;
} else {
    // ONLINE (InfinityFree) settings
    $host = 'sql111.infinityfree.com';
    $user = 'if0_42032208';
    $pass = 'UbuntuBay123';
    $dbname = 'if0_42032208_ubuntubay_db';
    $port = 3306;
}

// Create MySQLi connection
$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

// ALSO create PDO connection (for products.php and other modern code)
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Don't die, just log - the page will still work with mysqli
    error_log("PDO Connection failed: " . $e->getMessage());
}
?>