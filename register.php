<?php
require_once 'includes/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $province = trim($_POST['province']);
    $role = $_POST['role'];
    
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } else {
        $check_sql = "SELECT user_id FROM users WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_sql);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = "Email already registered!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $insert_sql = "INSERT INTO users (name, email, password_hash, role, province) 
                           VALUES ('$name', '$email', '$hashed_password', '$role', '$province')";
            
            if (mysqli_query($conn, $insert_sql)) {
                $success = "Registration successful! <a href='login.php'>Login here</a>";
            } else {
                $error = "Registration failed: " . mysqli_error($conn);
            }
        }
    }
}

require_once 'includes/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <div class="hero-tag"> UbuntuBay · Join Us</div>
        <h1>Create <strong>Account</strong></h1>
        <p>Join UbuntuBay and start buying and selling in your community.</p>
    </div>
</section>

<div class="container" style="max-width: 550px; margin-bottom: 60px;">
    <div class="card" style="background: rgba(0,0,0,0.55); backdrop-filter: blur(15px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.12); padding: 40px;">
        <h2 style="color: white; text-align: center; margin-bottom: 30px;">Register</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label style="color: white;">Full Name</label>
                <input type="text" name="name" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white;" required>
            </div>
            <div class="mb-3">
                <label style="color: white;">Email Address</label>
                <input type="email" name="email" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white;" required>
            </div>
            <div class="mb-3">
                <label style="color: white;">Password</label>
                <input type="password" name="password" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white;" required>
            </div>
            <div class="mb-3">
                <label style="color: white;">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white;" required>
            </div>
            <div class="mb-3">
                <label style="color: white;">Province</label>
                <select name="province" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white;" required>
                    <option value="">Select province</option>
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
                <label style="color: white;">I want to</label>
                <select name="role" class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: white;" required>
                    <option value="buyer">Buy products</option>
                    <option value="seller">Sell products</option>
                    <option value="both">Both buy and sell</option>
                </select>
            </div>
            <button type="submit" class="btn w-100" style="background: rgba(209,149,36,0.85); color: white; padding: 12px; border-radius: 30px;">Sign Up</button>
        </form>
        <p class="text-center mt-3" style="color: rgba(255,255,255,0.7);">Already have an account? <a href="login.php" style="color: #ffd175;">Log in</a></p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>