<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UbuntuBay - Buy & Sell Locally in South Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            min-height: 100vh;
            background-color: #0f0e0e;
            -webkit-font-smoothing: antialiased;
        }

        .hero-wrapper {
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: linear-gradient(180deg, rgba(14, 25, 22, 0.45) 0%, rgba(18, 16, 13, 0.82) 100%), 
                url('assets/images/hero-concert.jpg');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
        }

        .navbar-custom {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 50px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            margin: 30px auto 0 auto;
            padding: 12px 30px;
            z-index: 100;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
            max-width: 1350px;
            width: calc(100% - 40px);
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .logo {
            color: white;
            font-size: 22px;
            font-weight: bold;
            text-decoration: none;
            letter-spacing: 0.5px;
        }

        .nav-links {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 20px;
            transition: all 0.3s ease;
            font-size: 13px;
            font-weight: 500;
        }

        .nav-links a:hover {
            color: white;
            background: rgba(255, 255, 255, 0.08);
        }

        .btn-outline-light {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 20px !important;
            padding: 6px 14px !important;
        }

        .btn-warning-custom {
            background: rgba(209, 149, 36, 0.2) !important;
            border: 1px solid rgba(209, 149, 36, 0.4) !important;
            color: #ffd175 !important;
            border-radius: 20px !important;
            padding: 6px 14px !important;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-warning-custom:hover {
            background: rgba(209, 149, 36, 0.35) !important;
            color: white !important;
        }

        .hero {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 60px 20px 80px 20px;
            min-height: 40vh;
        }

        .hero-content {
            max-width: 950px;
            width: 100%;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 6px 16px;
            border-radius: 20px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 13px;
            margin-bottom: 25px;
            backdrop-filter: blur(5px);
        }

        .hero h1 {
            font-size: 64px;
            font-weight: 400;
            line-height: 1.1;
            margin-bottom: 24px;
            color: #ffffff;
            letter-spacing: -1px;
        }

        .hero h1 strong {
            font-weight: 700;
            color: #ffd175;
        }

        .hero p {
            font-size: 20px;
            line-height: 1.5;
            margin: 0 auto 30px auto;
            max-width: 725px;
            color: rgba(255, 255, 255, 0.95);
        }

        footer {
            background: #1a1a2e;
            color: white;
            text-align: center;
            padding: 30px;
            margin-top: 0;
        }

        footer a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }

        footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 1024px) {
            .navbar-custom {
                border-radius: 24px;
            }
            .nav-links {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .hero h1 { font-size: 36px; }
            .hero p { font-size: 16px; }
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
                <a href="products.php">Browse</a>
                <a href="sell.php">Sell Item</a>
                <a href="chat.php">Messages</a>
                <a href="delivery.php">Delivery</a>
                <a href="admin/index.php">Admin Portal</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">Profile</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn-outline-light">Login</a>
                    <a href="register.php" class="btn-warning-custom">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <div class="hero-tag"> UbuntuBay · <?php echo ucfirst(str_replace('.php', '', basename($_SERVER['PHP_SELF']))); ?></div>
            <h1>Welcome to <strong>UbuntuBay</strong></h1>
            <p>Buy and sell locally in South Africa with trusted community members</p>
        </div>
    </section>