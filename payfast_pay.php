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

if (!$order) {
    header('Location: index.php');
    exit();
}

// Update order to confirmed (simulate payment)
mysqli_query($conn, "UPDATE orders SET order_status = 'confirmed' WHERE order_id = $order_id");

require_once 'includes/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <div class="hero-tag"> PayFast · Payment</div>
        <h1>Payment <strong>Successful</strong></h1>
        <p>Your order has been confirmed!</p>
    </div>
</section>

<div class="container" style="max-width: 600px; margin-bottom: 60px;">
    <div class="card" style="background: rgba(0,0,0,0.55); backdrop-filter: blur(15px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.12); padding: 40px; text-align: center;">
        <div style="font-size: 70px; margin-bottom: 20px;"></div>
        <h2 style="color: #ffd175; margin-bottom: 15px;">Payment Successful!</h2>
        <p style="color: rgba(255,255,255,0.7); margin-bottom: 20px;">Your order #<?php echo $order_id; ?> has been confirmed.</p>
        <p style="color: rgba(255,255,255,0.5); margin-bottom: 30px;">You will receive an email confirmation shortly.</p>
        <div style="display: flex; gap: 15px; justify-content: center;">
            <a href="delivery.php" class="btn" style="background: rgba(209,149,36,0.85); color: white; padding: 12px 25px; border-radius: 30px;">Track Order</a>
            <a href="products.php" class="btn" style="background: rgba(255,255,255,0.1); color: white; padding: 12px 25px; border-radius: 30px;">Continue Shopping</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>