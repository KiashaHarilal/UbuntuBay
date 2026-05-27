<?php
require_once 'includes/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];
$success = '';
$error = '';

// Handle order status update (for sellers)
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['order_status'];
    $tracking_number = isset($_POST['tracking_number']) ? trim($_POST['tracking_number']) : '';
    
    $update_sql = "UPDATE orders SET order_status = '$new_status', tracking_number = '$tracking_number' WHERE order_id = $order_id";
    if (mysqli_query($conn, $update_sql)) {
        $success = "Order status updated successfully!";
    } else {
        $error = "Failed to update status";
    }
}

// Get orders where user is buyer or seller
if ($user_role === 'admin') {
    $orders_sql = "SELECT o.*, p.title as product_title, u.name as seller_name, 
                   buyer.name as buyer_name
                   FROM orders o
                   JOIN products p ON o.product_id = p.product_id
                   JOIN users u ON o.seller_id = u.user_id
                   JOIN users buyer ON o.buyer_id = buyer.user_id
                   ORDER BY o.created_at DESC";
} else {
    $orders_sql = "SELECT o.*, p.title as product_title, u.name as seller_name,
                   buyer.name as buyer_name
                   FROM orders o
                   JOIN products p ON o.product_id = p.product_id
                   JOIN users u ON o.seller_id = u.user_id
                   JOIN users buyer ON o.buyer_id = buyer.user_id
                   WHERE o.buyer_id = $user_id OR o.seller_id = $user_id
                   ORDER BY o.created_at DESC";
}

$orders_result = mysqli_query($conn, $orders_sql);

require_once 'includes/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <div class="hero-tag"> UbuntuBay · Delivery</div>
        <h1>Track Your <strong>Orders</strong></h1>
        <p>View order status and track your deliveries</p>
    </div>
</section>

<div class="container" style="max-width: 1200px; margin-bottom: 60px;">
    <div class="card" style="background: rgba(0,0,0,0.55); backdrop-filter: blur(15px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.12); padding: 30px;">
        <h2 style="color: #ffd175; margin-bottom: 20px;"> My Orders</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (mysqli_num_rows($orders_result) > 0): ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; color: white;">
                    <thead>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <th style="padding: 12px;">Order #</th>
                            <th style="padding: 12px;">Product</th>
                            <th style="padding: 12px;">Amount</th>
                            <th style="padding: 12px;">Delivery</th>
                            <th style="padding: 12px;">Status</th>
                            <th style="padding: 12px;">Date</th>
                            <th style="padding: 12px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td style="padding: 12px;">#<?php echo $order['order_id']; ?></td>
                                <td style="padding: 12px;"><?php echo htmlspecialchars($order['product_title']); ?></td>
                                <td style="padding: 12px;">R <?php echo number_format($order['total_amount'], 2); ?></td>
                                <td style="padding: 12px;">
                                    <?php if ($order['delivery_method'] == 'courier'): ?>
                                         Courier
                                    <?php else: ?>
                                         Pickup
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px;">
                                    <?php
                                    $status_colors = [
                                        'pending' => '#ff8a92',
                                        'confirmed' => '#ffd175',
                                        'dispatched' => '#6ea8fe',
                                        'delivered' => '#6bcb77'
                                    ];
                                    $color = $status_colors[$order['order_status']] ?? '#fff';
                                    ?>
                                    <span style="background: rgba(<?php echo $color == '#ffd175' ? '209,149,36' : ($color == '#6bcb77' ? '40,167,69' : ($color == '#6ea8fe' ? '0,123,255' : '220,53,69')); ?>, 0.2); padding: 5px 12px; border-radius: 20px; color: <?php echo $color; ?>;">
                                        <?php echo ucfirst($order['order_status']); ?>
                                    </span>
                                </td>
                                <td style="padding: 12px;"><?php echo date('Y-m-d', strtotime($order['created_at'])); ?></td>
                                <td style="padding: 12px;">
                                    <button type="button" class="btn" style="background: rgba(255,209,117,0.2); color: #ffd175; padding: 5px 15px; border-radius: 20px; font-size: 12px;" data-bs-toggle="modal" data-bs-target="#orderModal<?php echo $order['order_id']; ?>">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Order Details Modal -->
                            <div class="modal fade" id="orderModal<?php echo $order['order_id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content" style="background: rgba(0,0,0,0.95); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.12);">
                                        <div class="modal-header" style="border-bottom-color: rgba(255,255,255,0.1);">
                                            <h5 class="modal-title" style="color: #ffd175;">Order #<?php echo $order['order_id']; ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body" style="color: white;">
                                            <p><strong>Product:</strong> <?php echo htmlspecialchars($order['product_title']); ?></p>
                                            <p><strong>Buyer:</strong> <?php echo htmlspecialchars($order['buyer_name']); ?></p>
                                            <p><strong>Seller:</strong> <?php echo htmlspecialchars($order['seller_name']); ?></p>
                                            <p><strong>Total Amount:</strong> R <?php echo number_format($order['total_amount'], 2); ?></p>
                                            <p><strong>Delivery Method:</strong> <?php echo ucfirst($order['delivery_method']); ?></p>
                                            <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['shipping_address'] . ', ' . $order['shipping_city']); ?></p>
                                            <?php if ($order['tracking_number']): ?>
                                                <p><strong>Tracking #:</strong> <?php echo htmlspecialchars($order['tracking_number']); ?></p>
                                            <?php endif; ?>
                                            
                                            <?php if ($user_role === 'seller' || $user_role === 'admin' || $order['seller_id'] == $user_id): ?>
                                                <hr style="border-color: rgba(255,255,255,0.1);">
                                                <h4 style="color: #ffd175;">Update Status</h4>
                                                <form method="POST">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                    <select name="order_status" class="form-control" style="background: rgba(255,255,255,0.1); color: white; margin-bottom: 10px;">
                                                        <option value="pending" <?php echo $order['order_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="confirmed" <?php echo $order['order_status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                        <option value="dispatched" <?php echo $order['order_status'] == 'dispatched' ? 'selected' : ''; ?>>Dispatched</option>
                                                        <option value="delivered" <?php echo $order['order_status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                    </select>
                                                    <input type="text" name="tracking_number" class="form-control" style="background: rgba(255,255,255,0.1); color: white; margin-bottom: 10px;" placeholder="Tracking Number (optional)" value="<?php echo $order['tracking_number']; ?>">
                                                    <button type="submit" name="update_status" class="btn w-100" style="background: rgba(209,149,36,0.85); color: white; padding: 10px; border-radius: 30px;">Update Status</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 50px;">
                <div style="font-size: 60px; margin-bottom: 20px;"></div>
                <h3 style="color: white;">No orders yet</h3>
                <p style="color: rgba(255,255,255,0.6);">Start shopping to see your orders here</p>
                <a href="products.php" class="btn" style="background: rgba(209,149,36,0.85); color: white; padding: 12px 30px; border-radius: 30px; display: inline-block; margin-top: 20px;">Browse Products</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>