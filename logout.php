<?php
session_start();
session_destroy();
header('Location: index.php');
exit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out - UbuntuBay</title>
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

        /* Hero Wrapper with same background */
        .hero-wrapper {
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: 
                linear-gradient(180deg, rgba(14, 25, 22, 0.45) 0%, rgba(18, 16, 13, 0.82) 100%), 
                url('assets/images/hero-concert.jpg');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
        }

        /* Navbar - Floating Glass Layout */
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

        /* Logout Message Container */
        .logout-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
        }

        .logout-card {
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: 32px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 50px 60px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.3);
        }

        .logout-icon {
            font-size: 70px;
            margin-bottom: 20px;
        }

        .logout-card h2 {
            color: white;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .logout-card p {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 30px;
            font-size: 16px;
        }

        .btn-home {
            display: inline-block;
            padding: 12px 30px;
            background: rgba(209, 149, 36, 0.85);
            border: none;
            border-radius: 30px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-home:hover {
            background: rgba(209, 149, 36, 1);
            transform: translateY(-2px);
            color: white;
        }

        .spinner {
            width: 40px;
            height: 40px;
            margin: 20px auto;
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-top: 3px solid #ffd175;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .logout-card {
                padding: 35px 25px;
            }
            .logout-icon {
                font-size: 50px;
            }
            .logout-card h2 {
                font-size: 24px;
            }
            .nav-links {
                display: none;
            }
        }
    </style>
    <meta http-equiv="refresh" content="3; url=index.php">
</head>
<body>

<div class="hero-wrapper">
    <!-- Same Navbar -->
    <nav class="navbar-custom">
        <div class="nav-container">
            <a href="index.php" class="logo">UbuntuBay</a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="products.php">Browse</a>
                <a href="sell.php">Sell Item</a>
                <a href="chat.php">Messages</a>
                <a href="delivery.php">Delivery</a>
                <a href="checkout.php">Checkout</a>
                <a href="admin.php">Admin Portal</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <a href="login.php" class="btn btn-outline-light">Login</a>
                <a href="register.php" class="btn-warning-custom">Register</a>
            </div>
        </div>
    </nav>

    <!-- Logout Message -->
    <div class="logout-container">
        <div class="logout-card">
            <div class="logout-icon">👋</div>
            <h2>You've been logged out</h2>
            <p>Thank you for using UbuntuBay. See you again soon!</p>
            <div class="spinner"></div>
            <a href="index.php" class="btn-home">Return to Home</a>
        </div>
    </div>
</div>

<!-- Same Footer -->
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

</body>
</html>