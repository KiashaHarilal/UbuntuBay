<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'includes/database.php';

// Get filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 0;

// Build query with image
$query = "SELECT p.*, u.name as seller_name,
          (SELECT image_path FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) as image_path
          FROM products p 
          JOIN users u ON p.seller_id = u.user_id 
          WHERE p.status = 'active'";

$params = [];

if (!empty($search)) {
    $query .= " AND (p.title LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $query .= " AND p.category_id = ?";
    $params[] = $category;
}

if ($min_price > 0) {
    $query .= " AND p.price >= ?";
    $params[] = $min_price;
}

if ($max_price > 0) {
    $query .= " AND p.price <= ?";
    $params[] = $max_price;
}

$query .= " ORDER BY p.created_at DESC";

// Execute query
$products = [];
try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
}

// Get categories
$categories = [];
try {
    $stmt = $pdo->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $categories = [
        ['category_id' => 1, 'category_name' => 'Clothing'],
        ['category_id' => 2, 'category_name' => 'Electronics'],
        ['category_id' => 3, 'category_name' => 'Food & Beverages'],
        ['category_id' => 4, 'category_name' => 'Furniture'],
        ['category_id' => 5, 'category_name' => 'Services'],
        ['category_id' => 6, 'category_name' => 'Other']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UbuntuBay - Browse Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; min-height: 100vh; background-color: #0f0e0e; }

        .hero-wrapper {
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: linear-gradient(180deg, rgba(14, 25, 22, 0.45) 0%, rgba(18, 16, 13, 0.82) 100%), 
                url('assets/images/hero-concert.jpg');
            background-size: cover;
            background-position: center;
        }

        .navbar-custom {
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(20px);
            border-radius: 50px;
            border: 1px solid rgba(255,255,255,0.08);
            margin: 30px auto 0 auto;
            padding: 12px 30px;
            max-width: 1350px;
            width: calc(100% - 40px);
        }

        .nav-container { display: flex; justify-content: space-between; align-items: center; }
        .logo { color: white; font-size: 22px; font-weight: bold; text-decoration: none; }
        .nav-links { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .nav-links a { color: rgba(255,255,255,0.8); text-decoration: none; padding: 8px 14px; border-radius: 20px; font-size: 14px; }
        .nav-links a:hover { color: white; background: rgba(255,255,255,0.08); }
        .nav-links a.active { color: #ffd175; }

        .hamburger-btn {
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 10px;
            cursor: pointer;
            gap: 5px;
        }
        .hamburger-btn span { display: block; width: 20px; height: 2px; background: white; border-radius: 2px; }
        .hamburger-btn.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .hamburger-btn.open span:nth-child(2) { opacity: 0; }
        .hamburger-btn.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        .mobile-menu {
            display: none;
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            right: 0;
            background: rgba(15,14,14,0.97);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 16px;
            z-index: 999;
            flex-direction: column;
        }
        .mobile-menu.open { display: flex; }
        .mobile-menu a { color: rgba(255,255,255,0.85); text-decoration: none; padding: 12px 16px; border-radius: 12px; font-size: 15px; }
        .mobile-divider { height: 1px; background: rgba(255,255,255,0.08); margin: 6px 0; }
        .mobile-auth { display: flex; gap: 8px; padding: 8px 0 4px 0; }

        .hero { flex: 1; display: flex; align-items: center; justify-content: center; text-align: center; padding: 40px 20px 60px; }
        .hero-content { max-width: 950px; }
        .hero-tag { display: inline-flex; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); padding: 6px 16px; border-radius: 20px; color: rgba(255,255,255,0.9); font-size: 13px; margin-bottom: 25px; }
        .hero h1 { font-size: 72px; font-weight: 400; color: white; margin-bottom: 24px; }
        .hero h1 strong { font-weight: 700; color: #ffd175; }
        .hero p { font-size: 21px; color: rgba(255,255,255,0.95); max-width: 725px; margin: 0 auto; }

        .products-container { max-width: 1350px; margin: 0 auto; padding: 40px 20px 80px; }
        .filter-card { background: rgba(255,255,255,0.03); backdrop-filter: blur(10px); border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); padding: 25px; margin-bottom: 40px; }
        .filter-card label { color: rgba(255,255,255,0.7); font-size: 13px; display: block; margin-bottom: 8px; }
        .filter-input, .filter-select { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); border-radius: 12px; padding: 10px 15px; color: white; width: 100%; outline: none; }
        .filter-btn { background: rgba(209,149,36,0.2); border: 1px solid rgba(209,149,36,0.4); color: #ffd175; border-radius: 12px; padding: 10px 20px; cursor: pointer; width: 100%; font-weight: 500; }
        .reset-btn { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); color: rgba(255,255,255,0.7); border-radius: 12px; padding: 10px 20px; text-align: center; display: inline-block; text-decoration: none; }
        .results-count { color: rgba(255,255,255,0.5); margin-bottom: 20px; font-size: 14px; }

        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; }
        .product-card { background: rgba(255,255,255,0.03); backdrop-filter: blur(10px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.08); overflow: hidden; transition: all 0.3s; }
        .product-card:hover { transform: translateY(-5px); border-color: rgba(209,149,36,0.3); }
        .product-image { height: 200px; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .product-image img { width: 100%; height: 100%; object-fit: cover; }
        .product-info { padding: 20px; }
        .product-title { color: white; font-size: 18px; font-weight: 600; text-decoration: none; display: block; margin-bottom: 8px; }
        .product-title:hover { color: #ffd175; }
        .product-price { color: #ffd175; font-size: 22px; font-weight: bold; margin-bottom: 10px; }
        .product-seller { color: rgba(255,255,255,0.5); font-size: 12px; margin-bottom: 15px; }
        .product-description { color: rgba(255,255,255,0.6); font-size: 13px; margin-bottom: 15px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .product-delivery { display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap; }
        .delivery-badge { background: rgba(255,255,255,0.08); border-radius: 20px; padding: 4px 10px; font-size: 10px; color: rgba(255,255,255,0.6); }
        .view-btn { background: rgba(209,149,36,0.2); border: 1px solid rgba(209,149,36,0.4); color: #ffd175; border-radius: 25px; padding: 10px; text-align: center; display: block; text-decoration: none; font-size: 14px; }

        @media (max-width: 1024px) {
            .nav-links { display: none; }
            .hamburger-btn { display: flex; }
        }
        @media (max-width: 768px) {
            .hero h1 { font-size: 42px; }
            .hero p { font-size: 17px; }
            .products-grid { grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px; }
        }
    </style>
</head>
<body>

<div class="hero-wrapper">
    <nav class="navbar-custom">
        <div class="nav-container">
            <a href="index.php" class="logo">UbuntuBay</a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="products.php" class="active">Browse</a>
                <a href="sell.php">Sell Item</a>
                <a href="chat.php">Messages</a>
                <a href="delivery.php">Delivery</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">Profile</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light">Login</a>
                    <a href="register.php" class="btn-warning-custom">Register</a>
                <?php endif; ?>
            </div>
            <button class="hamburger-btn" id="hamburgerBtn" onclick="toggleMenu()" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
        <div class="mobile-menu" id="mobileMenu">
            <a href="index.php">Home</a>
            <a href="products.php">Browse</a>
            <a href="sell.php">Sell Item</a>
            <a href="chat.php">Messages</a>
            <a href="delivery.php">Delivery</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
            <div class="mobile-divider"></div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <div class="mobile-auth">
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <div class="hero-tag">UbuntuBay · Marketplace</div>
            <h1>Discover <strong>Local Treasures</strong></h1>
            <p>Browse items from sellers across South Africa. Find what you need, support local entrepreneurs.</p>
        </div>
    </section>

    <div class="products-container">
        <div class="filter-card">
            <form method="GET" action="">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label>Search Products</label>
                        <input type="text" name="search" class="filter-input" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-3">
                        <label>Category</label>
                        <select name="category" class="filter-select">
                            <option value="">All Categories</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['category_id']; ?>" <?php echo ($category == $cat['category_id']) ? 'selected' : ''; ?>><?php echo $cat['category_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Min Price</label>
                        <input type="number" name="min_price" class="filter-input" placeholder="Min" value="<?php echo $min_price > 0 ? $min_price : ''; ?>">
                    </div>
                    <div class="col-md-2">
                        <label>Max Price</label>
                        <input type="number" name="max_price" class="filter-input" placeholder="Max" value="<?php echo $max_price > 0 ? $max_price : ''; ?>">
                    </div>
                    <div class="col-md-1">
                        <label>&nbsp;</label>
                        <button type="submit" class="filter-btn">Filter</button>
                    </div>
                </div>
                <?php if($search || $category || $min_price > 0 || $max_price > 0): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <a href="products.php" class="reset-btn">Clear Filters</a>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <div class="results-count">Found <?php echo count($products); ?> product(s)</div>

        <?php if(count($products) > 0): ?>
            <div class="products-grid">
                <?php foreach($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if (!empty($product['image_path'])): ?>
                                <img src="Uploads/<?php echo $product['image_path']; ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                            <?php else: ?>
                                <img src="assets/images/no-image.png" alt="No Image">
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <a href="product.php?id=<?php echo $product['product_id']; ?>" class="product-title"><?php echo htmlspecialchars($product['title']); ?></a>
                            <div class="product-price">R <?php echo number_format($product['price'], 2); ?></div>
                            <div class="product-seller">Seller: <?php echo htmlspecialchars($product['seller_name']); ?></div>
                            <div class="product-description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>...</div>
                            <div class="product-delivery">
                                <?php if($product['delivery_courier'] == 1): ?><span class="delivery-badge">🚚 Courier</span><?php endif; ?>
                                <?php if($product['delivery_pickup'] == 1): ?><span class="delivery-badge">📍 Pickup</span><?php endif; ?>
                            </div>
                            <a href="product.php?id=<?php echo $product['product_id']; ?>" class="view-btn">View Details →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center" style="padding: 60px; color: rgba(255,255,255,0.5);">
                <h4>No products found</h4>
                <p>Try adjusting your search or filter criteria</p>
                <a href="products.php" class="btn-warning-custom" style="display: inline-block; margin-top: 15px;">View All Products</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer class="bg-dark text-white mt-5 py-4">
    <div class="container text-center">
        <p><strong>UbuntuBay</strong> – Empowering South Africa's Informal Traders</p>
        <p><a href="about.php">About</a> | <a href="contact.php">Contact</a> | <a href="products.php">Products</a></p>
        <small>© 2026 UbuntuBay. All rights reserved.</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleMenu() {
        document.getElementById('hamburgerBtn').classList.toggle('open');
        document.getElementById('mobileMenu').classList.toggle('open');
    }
    document.addEventListener('click', function(e) {
        var nav = document.querySelector('.navbar-custom');
        if (nav && !nav.contains(e.target)) {
            document.getElementById('hamburgerBtn').classList.remove('open');
            document.getElementById('mobileMenu').classList.remove('open');
        }
    });
</script>
</body>
</html>