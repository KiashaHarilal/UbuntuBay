<?php
require_once 'includes/database.php';
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

        /* Hero Wrapper handles the full-bleed background starting right from the top screen edge */
        .hero-wrapper {
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            
            /* Stacking gradient treatments matching your design mockup perfectly */
            background-image: 
                linear-gradient(180deg, rgba(14, 25, 22, 0.45) 0%, rgba(18, 16, 13, 0.82) 100%), 
                url('assets/images/hero-concert.jpg');
            
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
        }

        /* Navbar - Floating Glass Layout sitting on top of the hero image */
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
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 20px;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .nav-links a:hover {
            color: white;
            background: rgba(255, 255, 255, 0.08);
        }

        /* Custom adjustments on top of Bootstrap buttons */
        .btn-outline-light {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 20px !important;
            padding: 6px 16px !important;
        }
        .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.15) !important;
        }

        .btn-warning-custom {
            background: rgba(209, 149, 36, 0.2) !important;
            border: 1px solid rgba(209, 149, 36, 0.4) !important;
            color: #ffd175 !important;
            border-radius: 20px !important;
            padding: 6px 16px !important;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-warning-custom:hover {
            background: rgba(209, 149, 36, 0.35) !important;
            color: white !important;
        }

        /* Hero Content centered over background image area */
        .hero {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px 80px 20px;
        }

        .hero-content {
            max-width: 950px;
            width: 100%;
        }

        /* Mockup Glass Tag */
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
            -webkit-backdrop-filter: blur(5px);
        }

        /* Clean Typography layout floating gracefully over market image scenery */
        .hero h1 {
            font-size: 72px;
            font-weight: 400;
            line-height: 1.1;
            margin-bottom: 24px;
            color: #ffffff;
            letter-spacing: -1px;
        }

        .hero h1 strong {
            font-weight: 700;
        }

        .hero p {
            font-size: 21px;
            line-height: 1.5;
            margin: 0 auto 40px auto;
            max-width: 725px;
            color: rgba(255, 255, 255, 0.95);
            font-weight: 400;
        }

        /* Bottom Trending Label */
        .trending-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
            margin-top: 40px;
        }

        .trending-label span {
            color: #ffd175;
            font-weight: 600;
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
            .hero h1 { font-size: 42px; }
            .hero p { font-size: 17px; }
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
                <a href="/UbuntuBay/admin/index.php">Admin Portal</a>
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
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <div class="hero-tag"> UbuntuBay · South Africa</div>
            <h1>Township to Township.<br><strong>Trade without borders.</strong></h1>
            <p>Low-data marketplace built for South African informal traders. </p>
    </section>
</div>

<footer class="bg-dark text-white mt-5 py-4">
    <div class="container text-center">
        <p class="mb-1">
            <strong>UbuntuBay</strong> – Empowering South Africa's Informal Traders
        </p>
        <p class="mb-1">
            <a href="/UbuntuBay/about.php" class="text-light me-3">About</a>
            <a href="/UbuntuBay/contact.php" class="text-light me-3">Contact</a>
            <a href="/UbuntuBay/products.php" class="text-light">Products</a>
        </p>
        <small class="text-muted">© 2026 UbuntuBay. All rights reserved.</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="/UbuntuBay/assets/js/main.js"></script>

</body>
</html>