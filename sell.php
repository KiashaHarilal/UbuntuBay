<?php
require_once 'includes/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if user is a seller or both
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

if ($user_role !== 'seller' && $user_role !== 'both' && $user_role !== 'admin') {
    echo "<script>alert('You need to be a seller to list products. Update your role in profile.'); window.location='profile.php';</script>";
    exit();
}

$error = '';
$success = '';

// Get categories for dropdown
$categories_result = mysqli_query($conn, "SELECT category_id, category_name FROM categories ORDER BY category_name");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $category_id = intval($_POST['category_id']);
    $delivery_courier = isset($_POST['delivery_courier']) ? 1 : 0;
    $delivery_pickup = isset($_POST['delivery_pickup']) ? 1 : 0;
    
    if (empty($title) || empty($description) || $price <= 0 || $quantity <= 0 || $category_id <= 0) {
        $error = "Please fill in all required fields correctly.";
    } else {
        // Insert product
        $insert_sql = "INSERT INTO products (seller_id, category_id, title, description, price, quantity, delivery_courier, delivery_pickup, status) 
                       VALUES ($user_id, $category_id, '$title', '$description', $price, $quantity, $delivery_courier, $delivery_pickup, 'active')";
        
        if (mysqli_query($conn, $insert_sql)) {
            $product_id = mysqli_insert_id($conn);
            $success = "Product listed successfully! <a href='product.php?id=$product_id'>View your listing</a>";
            
            // Clear form
            $_POST = array();
        } else {
            $error = "Failed to list product: " . mysqli_error($conn);
        }
    }
}

require_once 'includes/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <div class="hero-tag"> UbuntuBay · Sell</div>
        <h1>List Your <strong>Product</strong></h1>
        <p>Reach thousands of buyers across South Africa. List your item and start selling today.</p>
    </div>
</section>

<div class="container" style="max-width: 800px; margin-bottom: 60px;">
    <div class="card" style="background: rgba(0,0,0,0.55); backdrop-filter: blur(15px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.12); padding: 40px;">
        <h2 style="color: #ffd175; margin-bottom: 10px;">Sell an Item</h2>
        <p style="color: rgba(255,255,255,0.6); margin-bottom: 30px;">Fill in the details below to list your product</p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <!-- Product Title -->
            <div class="mb-3">
                <label style="color: white; margin-bottom: 8px; display: block;">Product Title *</label>
                <input type="text" name="title" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white; padding: 12px;" required>
            </div>
            
            <!-- Category -->
            <div class="mb-3">
                <label style="color: white; margin-bottom: 8px; display: block;">Category *</label>
                <select name="category_id" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white; padding: 12px;" required>
                    <option value="" style="background: #1a1a2e;">Select a category</option>
                    <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                        <option value="<?php echo $cat['category_id']; ?>" style="background: #1a1a2e;"><?php echo $cat['category_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <!-- Description -->
            <div class="mb-3">
                <label style="color: white; margin-bottom: 8px; display: block;">Description *</label>
                <textarea name="description" rows="5" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white; padding: 12px;" required placeholder="Describe your product in detail..."></textarea>
            </div>
            
            <div class="row">
                <!-- Price -->
                <div class="col-md-6 mb-3">
                    <label style="color: white; margin-bottom: 8px; display: block;">Price (R) *</label>
                    <input type="number" step="0.01" name="price" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white; padding: 12px;" required>
                </div>
                
                <!-- Quantity -->
                <div class="col-md-6 mb-3">
                    <label style="color: white; margin-bottom: 8px; display: block;">Quantity *</label>
                    <input type="number" name="quantity" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white; padding: 12px;" required>
                </div>
            </div>
            
            <!-- Delivery Options -->
            <div class="mb-3">
                <label style="color: white; margin-bottom: 8px; display: block;">Delivery Options</label>
                <div class="row">
                    <div class="col-md-6">
                        <label style="color: white; cursor: pointer;">
                            <input type="checkbox" name="delivery_courier" value="1" style="margin-right: 8px;"> Courier Delivery
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label style="color: white; cursor: pointer;">
                            <input type="checkbox" name="delivery_pickup" value="1" style="margin-right: 8px;"> Local Pickup
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" class="btn w-100" style="background: rgba(209,149,36,0.85); color: white; padding: 14px; border-radius: 30px; font-weight: bold; margin-top: 20px;">List Product →</button>
        </form>
    </div>
    
    <!-- Tips Section -->
    <div class="card" style="background: rgba(0,0,0,0.55); backdrop-filter: blur(15px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.12); padding: 25px; margin-top: 25px; text-align: center;">
        <h3 style="color: #ffd175; margin-bottom: 15px;">Tips for Selling</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
            <div style="flex: 1; min-width: 150px;">
                <div style="font-size: 30px; margin-bottom: 10px;"></div>
                <p style="color: white; font-size: 14px;">Add clear photos</p>
                <p style="color: rgba(255,255,255,0.5); font-size: 12px;">Products with photos sell faster</p>
            </div>
            <div style="flex: 1; min-width: 150px;">
                <div style="font-size: 30px; margin-bottom: 10px;"></div>
                <p style="color: white; font-size: 14px;">Price competitively</p>
                <p style="color: rgba(255,255,255,0.5); font-size: 12px;">Research similar items</p>
            </div>
            <div style="flex: 1; min-width: 150px;">
                <div style="font-size: 30px; margin-bottom: 10px;"></div>
                <p style="color: white; font-size: 14px;">Detailed description</p>
                <p style="color: rgba(255,255,255,0.5); font-size: 12px;">Include condition, size, brand</p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>