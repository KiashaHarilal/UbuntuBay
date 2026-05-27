<?php
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Get stats
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'];
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products"))['count'];
$total_messages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM contact_messages"))['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - UbuntuBay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #0f0e0e;
            -webkit-font-smoothing: antialiased;
        }
        .hero-wrapper {
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: linear-gradient(180deg, rgba(14, 25, 22, 0.45) 0%, rgba(18, 16, 13, 0.82) 100%), 
                url('../assets/images/hero-concert.jpg');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
        }
        /* Navbar */
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
        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            color: white;
            font-size: 22px;
            font-weight: bold;
            text-decoration: none;
        }
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
        /* Admin Container */
        .admin-container {
            display: flex;
            padding: 30px;
            gap: 25px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }
        /* Sidebar Glass */
        .sidebar {
            width: 280px;
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(15px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 25px 20px;
            height: fit-content;
        }
        .sidebar h3 {
            color: #ffd175;
            font-size: 20px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 16px;
            margin-bottom: 8px;
            transition: all 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255, 209, 117, 0.15);
            color: #ffd175;
        }
        /* Main Content */
        .main-content {
            flex: 1;
        }
        .welcome-card {
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(15px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 25px 30px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .welcome-card h2 {
            color: white;
            font-size: 24px;
            margin: 0;
        }
        .welcome-card h2 span {
            color: #ffd175;
        }
        .logout-btn {
            background: rgba(220,53,69,0.2);
            border: 1px solid rgba(220,53,69,0.4);
            color: #ff8a92;
            padding: 8px 20px;
            border-radius: 30px;
            text-decoration: none;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(15px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 30px;
            text-align: center;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255,209,117,0.3);
        }
        .stat-card h2 {
            font-size: 48px;
            color: #ffd175;
            margin-bottom: 10px;
        }
        .stat-card p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            margin: 0;
        }
        .quick-actions {
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(15px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 25px;
        }
        .quick-actions h3 {
            color: white;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .action-btn {
            background: rgba(255, 209, 117, 0.15);
            border: 1px solid rgba(255, 209, 117, 0.3);
            color: #ffd175;
            padding: 10px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }
        .action-btn:hover {
            background: rgba(255, 209, 117, 0.3);
            color: white;
        }
        @media (max-width: 768px) {
            .admin-container { flex-direction: column; }
            .sidebar { width: 100%; }
            .stats-grid { grid-template-columns: 1fr; }
            .nav-links { display: none; }
        }
    </style>
</head>
<body>
<div class="hero-wrapper">
    <!-- Navbar -->
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

    <!-- Admin Content -->
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h3>Admin Panel</h3>
            <a href="index.php" class="active">Dashboard</a>
            <a href="users.php"> Manage Users</a>
            <a href="listings.php"> Manage Listings</a>
            <a href="contact_messages.php"> Contact Messages</a>
            <a href="logout.php">Logout</a>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="welcome-card">
                <h2>Welcome, <span><?php echo $_SESSION['user_name']; ?></span></h2>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h2><?php echo $total_users; ?></h2>
                    <p>Total Users</p>
                </div>
                <div class="stat-card">
                    <h2><?php echo $total_products; ?></h2>
                    <p>Total Products</p>
                </div>
                <div class="stat-card">
                    <h2><?php echo $total_messages; ?></h2>
                    <p>Contact Messages</p>
                </div>
            </div>

            <div class="quick-actions">
                <h3>Quick Actions</h3>
                <div class="action-buttons">
                    <a href="users.php" class="action-btn"> Manage Users</a>
                    <a href="listings.php" class="action-btn"> Review Listings</a>
                    <a href="contact_messages.php" class="action-btn"> View Messages</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>