\<?php
require_once 'includes/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$error = '';  // ADD THIS LINE - you were missing this!

// Get product ID from URL - if no ID, redirect to products
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    // No product selected, redirect to products page
    header('Location: products.php');
    exit();
}

// Get product details
$product_sql = "SELECT p.*, u.name as seller_name, u.user_id as seller_id, u.province as seller_province 
                FROM products p 
                JOIN users u ON p.seller_id = u.user_id 
                WHERE p.product_id = $product_id AND p.status = 'active'";
$product_result = mysqli_query($conn, $product_sql);

if (mysqli_num_rows($product_result) === 0) {
    header('Location: products.php');
    exit();
}

$product = mysqli_fetch_assoc($product_result);
$user_id = $_SESSION['user_id'];

// Get buyer info
$buyer_sql = "SELECT * FROM users WHERE user_id = $user_id";
$buyer_result = mysqli_query($conn, $buyer_sql);
$buyer = mysqli_fetch_assoc($buyer_result);

// Calculate shipping cost
$shipping_cost = 80; // Base rate
if ($buyer['province'] != $product['seller_province']) {
    $shipping_cost = 120; // Different province
}

$total_amount = $product['price'] + $shipping_cost;
$delivery_method = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delivery_method = $_POST['delivery_method'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $postal_code = mysqli_real_escape_string($conn, $_POST['postal_code']);
    $payment_method = $_POST['payment_method'];
    
    if (empty($address) || empty($city)) {
        $error = "Please fill in your shipping address";
    } else {
        // Create order
        $order_sql = "INSERT INTO orders (buyer_id, seller_id, product_id, quantity, total_amount, delivery_method, shipping_address, shipping_city, shipping_postal, order_status) 
                      VALUES ($user_id, {$product['seller_id']}, $product_id, 1, $total_amount, '$delivery_method', '$address', '$city', '$postal_code', 'pending')";
        
        if (mysqli_query($conn, $order_sql)) {
            $order_id = mysqli_insert_id($conn);
            
            // Redirect to payment based on selection
            if ($payment_method == 'payfast') {
                header("Location: payfast_pay.php?order_id=$order_id");
                exit();
            } elseif ($payment_method == 'snapscan') {
                header("Location: snapscan_pay.php?order_id=$order_id");
                exit();
            }
        } else {
            $error = "Error creating order: " . mysqli_error($conn);
        }
    }
}

require_once 'includes/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <div class="hero-tag"> UbuntuBay · Checkout</div>
        <h1>Complete Your <strong>Purchase</strong></h1>
        <p>Review your order and choose payment method</p>
    </div>
</section>

<div class="container" style="max-width: 1000px; margin-bottom: 60px;">
    <div class="row">
        <!-- Order Summary -->
        <div class="col-md-5">
            <div class="card" style="background: rgba(0,0,0,0.55); backdrop-filter: blur(15px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.12); padding: 25px;">
                <h3 style="color: #ffd175; margin-bottom: 20px;">Order Summary</h3>
                
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                        <div style="background: rgba(255,255,255,0.05); border-radius: 15px; padding: 15px; text-align: center;">
                            <div style="font-size: 40px;"></div>
                        </div>
                        <div>
                            <h4 style="color: white;"><?php echo htmlspecialchars($product['title']); ?></h4>
                            <p style="color: rgba(255,255,255,0.6);">Seller: <?php echo htmlspecialchars($product['seller_name']); ?></p>
                            <p style="color: rgba(255,255,255,0.6);">Quantity: 1</p>
                        </div>
                    </div>
                </div>
                
                <hr style="border-color: rgba(255,255,255,0.1);">
                
                <div style="margin-top: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="color: rgba(255,255,255,0.7);">Subtotal:</span>
                        <span style="color: white;">R <?php echo number_format($product['price'], 2); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="color: rgba(255,255,255,0.7);">Shipping:</span>
                        <span style="color: white;">R <?php echo number_format($shipping_cost, 2); ?></span>
                    </div>
                    <hr style="border-color: rgba(255,255,255,0.1);">
                    <div style="display: flex; justify-content: space-between; margin-top: 10px;">
                        <span style="color: #ffd175; font-size: 18px;">Total:</span>
                        <span style="color: #ffd175; font-size: 24px; font-weight: bold;">R <?php echo number_format($total_amount, 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Checkout Form -->
        <div class="col-md-7">
            <div class="card" style="background: rgba(0,0,0,0.55); backdrop-filter: blur(15px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.12); padding: 30px;">
                <h3 style="color: #ffd175; margin-bottom: 20px;">Shipping & Payment</h3>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <!-- Delivery Method -->
                    <div class="mb-4">
                        <label style="color: white; margin-bottom: 10px; display: block;">Delivery Method</label>
                        <div style="display: flex; gap: 20px;">
                            <label style="color: white; cursor: pointer; display: flex; align-items: center;">
                                <input type="radio" name="delivery_method" value="courier" required style="margin-right: 8px;">  Courier (R <?php echo $shipping_cost; ?>)
                            </label>
                            <label style="color: white; cursor: pointer; display: flex; align-items: center;">
                                <input type="radio" name="delivery_method" value="pickup" style="margin-right: 8px;">  Local Pickup (Free)
                            </label>
                        </div>
                    </div>
                    
                    <!-- Shipping Address -->
                    <div class="mb-3">
                        <label style="color: white; margin-bottom: 8px; display: block;">Street Address *</label>
                        <input type="text" name="address" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white; padding: 12px;" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label style="color: white; margin-bottom: 8px; display: block;">City/Town *</label>
                            <input type="text" name="city" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white; padding: 12px;" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label style="color: white; margin-bottom: 8px; display: block;">Postal Code</label>
                            <input type="text" name="postal_code" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white; padding: 12px;">
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="mb-4">
                        <label style="color: white; margin-bottom: 10px; display: block;">Select Payment Method</label>
                        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                            <label style="background: rgba(255,255,255,0.05); padding: 15px 25px; border-radius: 16px; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="payment_method" value="payfast" required>
                                <span style="color: white;"> PayFast</span>
                            </label>
                            <label style="background: rgba(255,255,255,0.05); padding: 15px 25px; border-radius: 16px; cursor: pointer; display: flex; align-items: center; gap: 10px;">
                                <input type="radio" name="payment_method" value="snapscan" required>
                                <span style="color: white;"> SnapScan</span>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn w-100" style="background: rgba(209,149,36,0.85); color: white; padding: 14px; border-radius: 30px; font-weight: bold;">Proceed to Payment →</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>