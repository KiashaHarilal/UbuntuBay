<?php
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Handle product deletion
if (isset($_GET['delete'])) {
    $product_id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE product_id = $product_id");
    header('Location: listings.php');
    exit();
}

// Handle status update
if (isset($_GET['status']) && isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    $status = $_GET['status'];
    mysqli_query($conn, "UPDATE products SET status = '$status' WHERE product_id = $product_id");
    header('Location: listings.php');
    exit();
}

$products = mysqli_query($conn, "SELECT p.*, u.name as seller_name FROM products p JOIN users u ON p.seller_id = u.user_id ORDER BY p.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Listings - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #0f0e0e;
            background-image: linear-gradient(180deg, rgba(14, 25, 22, 0.45) 0%, rgba(18, 16, 13, 0.82) 100%), 
                              url('../assets/images/hero-concert.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed; /* Keeps background locked full-screen */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .hero-wrapper {
            width: 100%;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .navbar-custom {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border-radius: 50px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            margin: 30px auto 0 auto;
            padding: 12px 30px;
            max-width: 1350px;
            width: calc(100% - 40px);
        }

        .nav-container { display: flex; justify-content: space-between; align-items: center; }
        .logo { color: white; font-size: 22px; font-weight: bold; text-decoration: none; }
        
        .nav-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 14px;
        }
        .nav-links a:hover { background: rgba(255, 255, 255, 0.08); color: white; }
        
        .btn-outline-light {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 20px !important;
            padding: 6px 16px !important;
        }

        .admin-container { 
            display: flex; 
            padding: 30px; 
            gap: 25px; 
            max-width: 1400px; 
            margin: 0 auto; 
            width: 100%;
            flex: 1;
        }

        .sidebar {
            width: 280px;
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(15px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 25px 20px;
            height: fit-content;
        }

        .sidebar h3 { color: #ffd175; text-align: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar a { display: flex; align-items: center; gap: 12px; color: rgba(255,255,255,0.8); text-decoration: none; padding: 12px 15px; border-radius: 16px; margin-bottom: 8px; transition: all 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,209,117,0.15); color: #ffd175; }
        
        .main-content { flex: 1; }
        
        .content-card {
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(15px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 25px;
        }

        .content-card h1 { color: #ffd175; margin-bottom: 20px; font-size: 24px; }
        table { width: 100%; color: white; }
        th { text-align: left; padding: 12px; color: #ffd175; border-bottom: 1px solid rgba(255,255,255,0.1); }
        td { padding: 12px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        
        .badge-active { background: rgba(40,167,69,0.2); color: #6bcb77; padding: 4px 10px; border-radius: 20px; font-size: 12px; display: inline-block; }
        .badge-flagged { background: rgba(220,53,69,0.2); color: #ff8a92; padding: 4px 10px; border-radius: 20px; font-size: 12px; display: inline-block; }
        .badge-sold { background: rgba(108,117,125,0.2); color: #adb5bd; padding: 4px 10px; border-radius: 20px; font-size: 12px; display: inline-block; }
        
        .btn-sm { padding: 5px 12px; border-radius: 20px; text-decoration: none; font-size: 12px; display: inline-block; margin: 0 2px; }
        .btn-approve { background: rgba(40,167,69,0.2); color: #6bcb77; }
        .btn-flag { background: rgba(220,53,69,0.2); color: #ff8a92; }
        .btn-sold { background: rgba(108,117,125,0.2); color: #adb5bd; }
        .btn-delete { background: rgba(220,53,69,0.2); color: #ff8a92; }
        .back-link { color: #ffd175; text-decoration: none; margin-bottom: 20px; display: inline-block; }

        @media (max-width: 768px) { 
            .admin-container { flex-direction: column; } 
            .sidebar { width: 100%; } 
            .nav-links { display: none; } 
            table { font-size: 12px; } 
        }
    </style>
</head>
<body>
<div class="hero-wrapper">
    <nav class="navbar-custom">
        <div class="nav-container">
            <a href="../index.php" class="logo">UbuntuBay</a>
            <div class="nav-links">
                <a href="../index.php">Home</a>
                <a href="../products.php">Browse</a>
                <a href="../sell.php">Sell Item</a>
                <a href="../chat.php">Messages</a>
                <a href="../about.php">About</a>
                <a href="../contact.php">Contact</a>
                <a href="logout.php" class="btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <div class="sidebar">
            <h3>Admin Panel</h3>
            <a href="index.php">Dashboard</a>
            <a href="users.php">Manage Users</a>
            <a href="listings.php" class="active">Manage Listings</a>
            <a href="contact_messages.php">Contact Messages</a>
            <a href="logout.php">Logout</a>
        </div>

        <div class="main-content">
            <div class="content-card">
                <a href="index.php" class="back-link">← Back to Dashboard</a>
                <h1>Manage Product Listings</h1>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr><th>ID</th><th>Title</th><th>Seller</th><th>Price</th><th>Status</th><th>Date</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php while ($product = mysqli_fetch_assoc($products)): ?>
                            <tr>
                                <td><?php echo $product['product_id']; ?></td>
                                <td><?php echo htmlspecialchars($product['title']); ?></td>
                                <td><?php echo htmlspecialchars($product['seller_name']); ?></td>
                                <td>R <?php echo number_format($product['price'], 2); ?></td>
                                <td>
                                    <?php if ($product['status'] == 'active'): ?>
                                        <span class="badge-active">Active</span>
                                    <?php elseif ($product['status'] == 'flagged'): ?>
                                        <span class="badge-flagged">Flagged</span>
                                    <?php else: ?>
                                        <span class="badge-sold">Sold</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($product['created_at'])); ?></td>
                                <td>
                                    <?php if ($product['status'] != 'active'): ?>
                                        <a href="?id=<?php echo $product['product_id']; ?>&status=active" class="btn-sm btn-approve">Approve</a>
                                    <?php endif; ?>
                                    <?php if ($product['status'] != 'flagged'): ?>
                                        <a href="?id=<?php echo $product['product_id']; ?>&status=flagged" class="btn-sm btn-flag">Flag</a>
                                    <?php endif; ?>
                                    <?php if ($product['status'] != 'sold'): ?>
                                        <a href="?id=<?php echo $product['product_id']; ?>&status=sold" class="btn-sm btn-sold">Mark Sold</a>
                                    <?php endif; ?>
                                    <a href="?delete=<?php echo $product['product_id']; ?>" class="btn-sm btn-delete" onclick="return confirm('Delete this product?')">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>