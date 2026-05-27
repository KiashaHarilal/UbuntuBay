<?php
require_once 'includes/database.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: products.php');
    exit();
}

// Get product details
$sql = "SELECT p.*, u.name as seller_name, u.province as seller_province, u.user_id as seller_id,
        c.category_name
        FROM products p 
        JOIN users u ON p.seller_id = u.user_id 
        JOIN categories c ON p.category_id = c.category_id
        WHERE p.product_id = $product_id AND p.status = 'active'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) === 0) {
    header('Location: products.php');
    exit();
}

$product = mysqli_fetch_assoc($result);
$is_owner = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $product['seller_id']);

require_once 'includes/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <div class="hero-tag"> UbuntuBay · Product</div>
        <h1><?php echo htmlspecialchars($product['title']); ?></h1>
    </div>
</section>

<div class="container" style="max-width: 1200px; margin-bottom: 60px;">
    <div class="row">
        <!-- Product Image -->
        <div class="col-md-6">
            <div class="card" style="background: rgba(0,0,0,0.55); backdrop-filter: blur(15px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.12); padding: 30px; text-align: center;">
                <div style="background: rgba(255,255,255,0.05); border-radius: 20px; padding: 60px; margin-bottom: 20px;">
                    <div style="font-size: 80px;"></div>
                </div>
                <p style="color: rgba(255,255,255,0.5);">Product Image</p>
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="col-md-6">
            <div class="card" style="background: rgba(0,0,0,0.55); backdrop-filter: blur(15px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.12); padding: 30px;">
                <h2 style="color: #ffd175; margin-bottom: 15px;">R <?php echo number_format($product['price'], 2); ?></h2>
                
                <div style="margin-bottom: 20px;">
                    <p style="color: rgba(255,255,255,0.6); margin-bottom: 5px;"> Seller: <?php echo htmlspecialchars($product['seller_name']); ?></p>
                    <p style="color: rgba(255,255,255,0.6); margin-bottom: 5px;"> Location: <?php echo htmlspecialchars($product['seller_province']); ?></p>
                    <p style="color: rgba(255,255,255,0.6); margin-bottom: 5px;"> Category: <?php echo htmlspecialchars($product['category_name']); ?></p>
                    <p style="color: rgba(255,255,255,0.6);"> Stock: <?php echo $product['quantity']; ?> available</p>
                </div>
                
                <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 0;">
                
                <h3 style="color: white; margin-bottom: 10px;">Description</h3>
                <p style="color: rgba(255,255,255,0.7); line-height: 1.6;"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                
                <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 0;">
                
                <h3 style="color: white; margin-bottom: 10px;">Delivery Options</h3>
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <?php if ($product['delivery_courier'] == 1): ?>
                        <span style="background: rgba(40,167,69,0.2); color: #6bcb77; padding: 8px 15px; border-radius: 25px;"> Courier Available</span>
                    <?php endif; ?>
                    <?php if ($product['delivery_pickup'] == 1): ?>
                        <span style="background: rgba(0,123,255,0.2); color: #6ea8fe; padding: 8px 15px; border-radius: 25px;"> Local Pickup Available</span>
                    <?php endif; ?>
                </div>
                
                <div class="mt-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($is_owner): ?>
                            <a href="sell.php?edit=<?php echo $product['product_id']; ?>" class="btn w-100" style="background: rgba(255,209,117,0.2); color: #ffd175; padding: 12px; border-radius: 30px; margin-bottom: 10px;"> Edit Listing</a>
                        <?php else: ?>
                            <a href="checkout.php?id=<?php echo $product['product_id']; ?>" class="btn w-100" style="background: rgba(209,149,36,0.85); color: white; padding: 12px; border-radius: 30px; margin-bottom: 10px;">Buy Now</a>
                            <a href="chat.php?product=<?php echo $product['product_id']; ?>&user=<?php echo $product['seller_id']; ?>" class="btn w-100" style="background: rgba(255,255,255,0.1); color: white; padding: 12px; border-radius: 30px;">chat with Seller</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="login.php" class="btn w-100" style="background: rgba(209,149,36,0.85); color: white; padding: 12px; border-radius: 30px;">Login to Purchase</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>