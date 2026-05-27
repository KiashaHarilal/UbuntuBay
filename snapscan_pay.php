<?php
require_once 'includes/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_id <= 0) {
    header('Location: index.php');
    exit();
}

// Get order details
$order_sql = "SELECT o.*, p.title as product_title FROM orders o JOIN products p ON o.product_id = p.product_id WHERE o.order_id = $order_id";
$order_result = mysqli_query($conn, $order_sql);
$order = mysqli_fetch_assoc($order_result);

// Update order to confirmed
mysqli_query($conn, "UPDATE orders SET order_status = 'confirmed' WHERE order_id = $order_id");

require_once 'includes/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <div class="hero-tag"> SnapScan · Payment</div>
        <h1>Scan to <strong>Pay</strong></h1>
        <p>Complete your payment using SnapScan</p>
    </div>
</section>

<div class="container" style="max-width: 500px; margin-bottom: 60px;">
    <div class="card" style="background: rgba(0,0,0,0.55); backdrop-filter: blur(15px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.12); padding: 40px; text-align: center;">
        <div style="background: white; border-radius: 20px; padding: 20px; width: 200px; height: 200px; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                            <div style="font-size: 60px;">📱</div>
        </div>
        <h2 style="color: #ffd175; margin-bottom: 15px;">Scan QR Code</h2>
        <p style="color: rgba(255,255,255,0.7); margin-bottom: 10px;">Amount: R <?php echo number_format($order['total_amount'], 2); ?></p>
        <p style="color: rgba(255,255,255,0.5); margin-bottom: 20px;">Order #<?php echo $order_id; ?></p>
        <p style="color: rgba(255,255,255,0.6); font-size: 14px;">Open SnapScan app and scan this code</p>
        
        <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 0;">
        
        <p style="color: rgba(255,255,255,0.6);">After scanning, your order will be confirmed</p>
        <a href="delivery.php" class="btn" style="background: rgba(209,149,36,0.85); color: white; padding: 12px 25px; border-radius: 30px; display: inline-block; margin-top: 20px;">Go to My Orders</a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>