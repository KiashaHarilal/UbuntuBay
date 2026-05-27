<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['user_id'])) {
    exit();
}

$current_user_id = $_SESSION['user_id'];
$other_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if ($other_user_id > 0) {
    try {
        $query = "SELECT m.*, u.name as sender_name 
                  FROM messages m
                  JOIN users u ON m.sender_id = u.user_id
                  WHERE ((m.sender_id = ? AND m.receiver_id = ?) 
                     OR (m.sender_id = ? AND m.receiver_id = ?))
                    AND m.sent_at > DATE_SUB(NOW(), INTERVAL 30 SECOND)
                  ORDER BY m.sent_at ASC";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$current_user_id, $other_user_id, $other_user_id, $current_user_id]);
        $new_messages = $stmt->fetchAll();
        
        foreach($new_messages as $msg) {
            $is_sent = ($msg['sender_id'] == $current_user_id);
            $class = $is_sent ? 'message-sent ms-auto' : 'message-received me-auto';
            ?>
            <div class="message-bubble <?php echo $class; ?>">
                <div><?php echo nl2br(htmlspecialchars($msg['message_body'])); ?></div>
                <div class="message-time">
                    <?php 
                    $time = new DateTime($msg['sent_at']);
                    echo $time->format('M j, g:i A');
                    ?>
                </div>
            </div>
            <?php
        }
    } catch (PDOException $e) {
        // Silent fail
    }
}
?>