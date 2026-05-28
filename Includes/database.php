<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host   = 'sql111.infinityfree.com';
$user   = 'if0_42032208';
$pass   = 'UbuntuBay123';
$dbname = 'if0_42032208_ubuntubay_db';
$port   = 3306;

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("PDO Connection failed: " . $e->getMessage());
}
?>