<?php
require_once 'includes/database.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$sender_id = $_SESSION['user_id'];

// Debug - see what's being sent
// Uncomment the line below to see the POST data
// var_dump($_POST); exit();

$receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if ($receiver_id > 0 && !empty($message)) {
    $message = mysqli_real_escape_string($conn, $message);
    
    $sql = "INSERT INTO messages (sender_id, receiver_id, message_body, sent_at, is_read) 
            VALUES ($sender_id, $receiver_id, '$message', NOW(), 0)";
    
    if (mysqli_query($conn, $sql)) {
        // Success - redirect back to chat
        header("Location: chat.php?user=" . $receiver_id);
        exit();
    } else {
        // Database error
        echo "Database error: " . mysqli_error($conn);
        exit();
    }
} else {
    // Missing data - show what's missing
    echo "Error: Missing data. receiver_id: $receiver_id, message: " . ($message ? $message : 'empty');
    exit();
}
?>