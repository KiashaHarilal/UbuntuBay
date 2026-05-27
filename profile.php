<?php
require_once 'includes/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';
$user_role = $_SESSION['user_role'];

// Handle profile update
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = trim($_POST['name']);
        $province = trim($_POST['province']);
        $role = trim($_POST['role']);
        
        $update_sql = "UPDATE users SET name = '$name', province = '$province', role = '$role' WHERE user_id = $user_id";
        if (mysqli_query($conn, $update_sql)) {
            $_SESSION['user_name'] = $name;
            $_SESSION['user_role'] = $role;
            $success = "Profile updated successfully!";
        } else {
            $error = "Failed to update profile";
        }
    }
    
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Verify current password
        $pass_sql = "SELECT password_hash FROM users WHERE user_id = $user_id";
        $pass_result = mysqli_query($conn, $pass_sql);
        $user_data = mysqli_fetch_assoc($pass_result);
        
        if (password_verify($current_password, $user_data['password_hash'])) {
            if ($new_password === $confirm_password && strlen($new_password) >= 6) {
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                mysqli_query($conn, "UPDATE users SET password_hash = '$new_hash' WHERE user_id = $user_id");
                $success = "Password changed successfully!";
            } else {
                $error = "New password must match and be at least 6 characters";
            }
        } else {
            $error = "Current password is incorrect";
        }
    }
}

// Get user's products (if seller)
$user_products = [];
if ($user_role === 'seller' || $user_role === 'both' || $user_role === 'admin') {
    $products_sql = "SELECT * FROM products WHERE seller_id = $user_id ORDER BY created_at DESC";
    $products_result = mysqli_query($conn, $products_sql);
    $user_products = mysqli_fetch_all($products_result, MYSQLI_ASSOC);
}

// Get user's purchases (if buyer)
$user_purchases = [];
if ($user_role === 'buyer' || $user_role === 'both' || $user_role === 'admin') {
    $purchases_sql = "SELECT o.*, p.title as product_title, p.price as product_price, u.name as seller_name
                      FROM orders o
                      JOIN products p ON o.product_id = p.product_id
                      JOIN users u ON o.seller_id = u.user_id
                      WHERE o.buyer_id = $user_id
                      ORDER BY o.created_at DESC";
    $purchases_result = mysqli_query($conn, $purchases_sql);
    $user_purchases = mysqli_fetch_all($purchases_result, MYSQLI_ASSOC);
}

require_once 'includes/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <div class="hero-tag"> UbuntuBay · Profile</div>
        <h1>My <strong>Account</strong></h1>
        <p>Manage your profile, listings, and orders</p>
    </div>
</section>

<div class="container" style="max-width: 1200px; margin-bottom: 60px;">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card" style="background: rgba(0,0,0,0.55); backdrop-filter: blur(15px); border-radius: 24px; border: 1px solid rgba(255,255,255,0.12); padding: 20px; margin-bottom: 20px;">
                <div style="text-align: center; margin-bottom: 20px;">
                    <div style="font-size: 60px; margin-bottom: 10px;"></div>
                    <h4 style="color: white;"><?php echo htmlspecialchars($user_name); ?></h4>
                    <p style="color: rgba(255,255,255,0.6); font-size: 12px;"><?php echo ucfirst($user_role); ?></p>
                </div>
                <hr style="border-color: rgba(255,255,255,0.1);">
                <div style="margin-top: 15px;">
                    <p style="color: rgba(255,255,255,0.7); margin-bottom: 8px;"> <?php echo htmlspecialchars($user_email); ?></p>
                </div>
            </div>
            
            <div class="card" style="background: rgba(0,0,0,0.55); backdrop-filter: blur(15px); border-radius: 24px; border: 1px solid rgba(255,255,255,0.12); padding: 20px;">
                <h4 style="color: #ffd175; margin-bottom: 15px;">Quick Links</h4>
                <a href="#edit-profile" style="display: block; color: white; text-decoration: none; padding: 8px 0;"> Edit Profile</a>
                <a href="#change-password" style="display: block; color: white; text-decoration: none; padding: 8px 0;"> Change Password</a>
                <?php if ($user_role === 'seller' || $user_role === 'both'): ?>
                    <a href="sell.php" style="display: block; color: white; text-decoration: none; padding: 8px 0;"> List New Product</a>
                <?php endif; ?>
                <a href="delivery.php" style="display: block; color: white; text-decoration: none; padding: 8px 0;"> My Orders</a>
                <a href="logout.php" style="display: block; color: #ff8a92; text-decoration: none; padding: 8px 0;"> Logout</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Edit Profile Section -->
            <div id="edit-profile" class="card" style="background: rgba(0,0,0,0.55); backdrop-filter: blur(15px); border-radius: 24px; border: 1px solid rgba(255,255,255,0.12); padding: 25px; margin-bottom: 25px;">
                <h3 style="color: #ffd175; margin-bottom: 20px;"> Edit Profile</h3>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label style="color: white;">Full Name</label>
                        <input type="text" name="name" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white;" value="<?php echo htmlspecialchars($user_name); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label style="color: white;">Email</label>
                        <input type="email" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white;" value="<?php echo htmlspecialchars($user_email); ?>" disabled>
                        <small style="color: rgba(255,255,255,0.4);">Email cannot be changed</small>
                    </div>
                    <div class="mb-3">
                        <label style="color: white;">Province</label>
                        <select name="province" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white;">
                            <option>Eastern Cape</option>
                            <option>Free State</option>
                            <option>Gauteng</option>
                            <option>KwaZulu-Natal</option>
                            <option>Limpopo</option>
                            <option>Mpumalanga</option>
                            <option>Northern Cape</option>
                            <option>North West</option>
                            <option>Western Cape</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label style="color: white;">Role</label>
                        <select name="role" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white;">
                            <option value="buyer" <?php echo $user_role == 'buyer' ? 'selected' : ''; ?>>Buyer</option>
                            <option value="seller" <?php echo $user_role == 'seller' ? 'selected' : ''; ?>>Seller</option>
                            <option value="both" <?php echo $user_role == 'both' ? 'selected' : ''; ?>>Both</option>
                        </select>
                    </div>
                    <button type="submit" name="update_profile" class="btn" style="background: rgba(209,149,36,0.85); color: white; padding: 12px 25px; border-radius: 30px;">Save Changes</button>
                </form>
            </div>
            
            <!-- Change Password Section -->
            <div id="change-password" class="card" style="background: rgba(0,0,0,0.55); backdrop-filter: blur(15px); border-radius: 24px; border: 1px solid rgba(255,255,255,0.12); padding: 25px; margin-bottom: 25px;">
                <h3 style="color: #ffd175; margin-bottom: 20px;"> Change Password</h3>
                <form method="POST">
                    <div class="mb-3">
                        <label style="color: white;">Current Password</label>
                        <input type="password" name="current_password" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white;" required>
                    </div>
                    <div class="mb-3">
                        <label style="color: white;">New Password</label>
                        <input type="password" name="new_password" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white;" required>
                    </div>
                    <div class="mb-3">
                        <label style="color: white;">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white;" required>
                    </div>
                    <button type="submit" name="change_password" class="btn" style="background: rgba(209,149,36,0.85); color: white; padding: 12px 25px; border-radius: 30px;">Change Password</button>
                </form>
            </div>
            
            <!-- My Listings (for sellers) -->
            <?php if (($user_role === 'seller' || $user_role === 'both') && count($user_products) > 0): ?>
            <div class="card" style="background: rgba(0,0,0,0.55); backdrop-filter: blur(15px); border-radius: 24px; border: 1px solid rgba(255,255,255,0.12); padding: 25px; margin-bottom: 25px;">
                <h3 style="color: #ffd175; margin-bottom: 20px;"> My Listings</h3>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; color: white;">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                                <th style="padding: 10px;">Product</th>
                                <th style="padding: 10px;">Price</th>
                                <th style="padding: 10px;">Status</th>
                                <th style="padding: 10px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($user_products as $product): ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td style="padding: 10px;"><?php echo htmlspecialchars($product['title']); ?></td>
                                <td style="padding: 10px;">R <?php echo number_format($product['price'], 2); ?></td>
                                <td style="padding: 10px;">
                                    <span style="background: <?php echo $product['status'] == 'active' ? 'rgba(40,167,69,0.2)' : 'rgba(108,117,125,0.2)'; ?>; color: <?php echo $product['status'] == 'active' ? '#6bcb77' : '#adb5bd'; ?>; padding: 4px 10px; border-radius: 20px; font-size: 12px;">
                                        <?php echo ucfirst($product['status']); ?>
                                    </span>
                                </td>
                                <td style="padding: 10px;">
                                    <a href="product.php?id=<?php echo $product['product_id']; ?>" style="color: #ffd175; text-decoration: none;">View</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Purchase History (for buyers) -->
            <?php if (($user_role === 'buyer' || $user_role === 'both') && count($user_purchases) > 0): ?>
            <div class="card" style="background: rgba(0,0,0,0.55); backdrop-filter: blur(15px); border-radius: 24px; border: 1px solid rgba(255,255,255,0.12); padding: 25px;">
                <h3 style="color: #ffd175; margin-bottom: 20px;">🛒 Purchase History</h3>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; color: white;">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                                <th style="padding: 10px;">Order #</th>
                                <th style="padding: 10px;">Product</th>
                                <th style="padding: 10px;">Amount</th>
                                <th style="padding: 10px;">Status</th>
                                <th style="padding: 10px;">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($user_purchases as $purchase): ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td style="padding: 10px;">#<?php echo $purchase['order_id']; ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($purchase['product_title']); ?></td>
                                <td style="padding: 10px;">R <?php echo number_format($purchase['total_amount'], 2); ?></td>
                                <td style="padding: 10px;">
                                    <span style="background: <?php echo $purchase['order_status'] == 'delivered' ? 'rgba(40,167,69,0.2)' : 'rgba(255,209,117,0.2)'; ?>; color: <?php echo $purchase['order_status'] == 'delivered' ? '#6bcb77' : '#ffd175'; ?>; padding: 4px 10px; border-radius: 20px; font-size: 12px;">
                                        <?php echo ucfirst($purchase['order_status']); ?>
                                    </span>
                                </td>
                                <td style="padding: 10px;"><?php echo date('Y-m-d', strtotime($purchase['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>