<?php
require_once 'includes/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    $sql = "SELECT * FROM users WHERE email = '$email' AND is_active = 1";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            if ($user['role'] === 'admin') {
                header('Location: admin/index.php');
            } else {
                header('Location: index.php');
            }
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "User not found or account inactive!";
    }
}

require_once 'includes/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <div class="hero-tag"> UbuntuBay · Login</div>
        <h1>Welcome <strong>Back</strong></h1>
        <p>Login to your UbuntuBay account and continue trading.</p>
    </div>
</section>

<div class="container" style="max-width: 500px; margin-bottom: 60px;">
    <div class="card" style="background: rgba(0,0,0,0.55); backdrop-filter: blur(15px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.12); padding: 40px;">
        <h2 style="color: white; text-align: center; margin-bottom: 30px;">Login</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label style="color: white;">Email Address</label>
                <input type="email" name="email" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white;" required>
            </div>
            <div class="mb-3">
                <label style="color: white;">Password</label>
                <input type="password" name="password" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white;" required>
            </div>
            <button type="submit" class="btn w-100" style="background: rgba(209,149,36,0.85); color: white; padding: 12px; border-radius: 30px;">Login</button>
        </form>
        <p class="text-center mt-3" style="color: rgba(255,255,255,0.7);">Don't have an account? <a href="register.php" style="color: #ffd175;">Register here</a></p>
        <div class="mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,0.1);">
            <p style="color: rgba(255,255,255,0.5); font-size: 12px;">Test Accounts:</p>
            <small style="color: rgba(255,255,255,0.4);">Admin: admin@UbuntuBay.co.za / admin123</small><br>
            <small style="color: rgba(255,255,255,0.4);">Buyer: buyer@test.com / buyer123</small><br>
            <small style="color: rgba(255,255,255,0.4);">Seller: seller@test.com / seller123</small>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
