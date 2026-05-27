<?php
require_once '../includes/database.php';

if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin') {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Use prepared statement to prevent SQL injection
    $sql = "SELECT * FROM users WHERE email = ? AND is_active = 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $user['password_hash']) && $user['role'] === 'admin') {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            header('Location: index.php');
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - UbuntuBay</title>
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
            background-image: 
                linear-gradient(180deg, rgba(14, 25, 22, 0.45) 0%, rgba(18, 16, 13, 0.82) 100%), 
                url('../assets/images/hero-concert.jpg');
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

        .login-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .login-card {
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(15px);
            border-radius: 32px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 45px 50px;
            max-width: 450px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.3);
        }

        .login-card h2 {
            color: #ffd175;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 30px;
            font-size: 14px;
        }

        .login-card input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            color: white;
            margin-bottom: 20px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .login-card input:focus {
            outline: none;
            border-color: #ffd175;
            background: rgba(255, 255, 255, 0.12);
        }

        .login-card input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .login-card button {
            width: 100%;
            padding: 14px;
            background: rgba(209, 149, 36, 0.85);
            border: none;
            border-radius: 30px;
            color: white;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .login-card button:hover {
            background: rgba(209, 149, 36, 1);
            transform: translateY(-2px);
        }

        .error {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.4);
            color: #ff8a92;
            padding: 12px;
            border-radius: 16px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .back-link {
            margin-top: 25px;
        }

        .back-link a {
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }

        .back-link a:hover {
            color: #ffd175;
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

        @media (max-width: 768px) {
            .login-card { padding: 30px 25px; margin: 20px; }
            .login-card h2 { font-size: 24px; }
            .nav-links { display: none; }
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
                <a href="../delivery.php">Delivery</a>
                <a href="../about.php">About</a>
                <a href="../contact.php">Contact</a>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="../profile.php">Profile</a>
                    <a href="../logout.php">Logout</a>
                <?php else: ?>
                    <a href="../login.php" class="btn-outline-light">Login</a>
                    <a href="../register.php" class="btn-warning-custom">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="login-container">
        <div class="login-card">
            <h2>Admin Portal</h2>
            <div class="subtitle">UbuntuBay Administration</div>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="email" name="email" placeholder="admin@UbuntuBay.co.za" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login to Admin</button>
            </form>
            
            <div class="back-link">
                <a href="../index.php">← Back to UbuntuBay</a>
            </div>
        </div>
    </div>
</div>

<footer>
    <div class="container text-center">
        <p class="mb-1">
            <strong>UbuntuBay</strong> – Empowering South Africa's Informal Traders
        </p>
        <p class="mb-1">
            <a href="../about.php" class="text-light me-3">About</a>
            <a href="../contact.php" class="text-light me-3">Contact</a>
            <a href="../products.php" class="text-light">Products</a>
        </p>
        <small class="text-muted">© 2026 UbuntuBay. All rights reserved.</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>