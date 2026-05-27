<?php
require_once 'includes/database.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "Please fill in all fields";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address";
    } else {
        // Save to database
        $insert_sql = "INSERT INTO contact_messages (name, email, subject, message) 
                       VALUES ('$name', '$email', '$subject', '$message')";
        
        if (mysqli_query($conn, $insert_sql)) {
            $success = "Thank you for contacting us! We'll get back to you within 24 hours.";
        } else {
            $error = "Failed to send message. Please try again.";
        }
    }
}

require_once 'includes/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <div class="hero-tag"> UbuntuBay · Contact</div>
        <h1>Get in <strong>Touch</strong></h1>
        <p>Have questions, feedback, or need support? We'd love to hear from you.</p>
    </div>
</section>

<div class="container" style="max-width: 1200px; margin-bottom: 60px;">
    <div class="row">
        <!-- Map Section -->
        <div class="col-md-5">
            <div class="card" style="background: rgba(0,0,0,0.55); backdrop-filter: blur(15px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.12); padding: 20px; margin-bottom: 20px;">
                <h3 style="color: #ffd175; margin-bottom: 15px; text-align: center;"> South Africa</h3>
                
                <div style="position: relative; height: 350px; width: 100%; border-radius: 20px; overflow: hidden;">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d8651501.702354675!2d16.000051999999998!3d-30.5594825!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1c34b1d9b6b1b1b1%3A0x1b1b1b1b1b1b1b1b!2sSouth%20Africa!5e0!3m2!1sen!2sza!4v1700000000000!5m2!1sen!2sza"
                        style="width: 100%; height: 100%; border: 0;"
                        allowfullscreen=""
                        loading="lazy">
                    </iframe>
                </div>
                
                <div style="margin-top: 15px;">
                    <p style="color: rgba(255,255,255,0.7); font-size: 12px; text-align: center;"> UbuntuBay serves all 9 provinces of South Africa</p>
                    <div style="display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; margin-top: 10px;">
                        <span style="background: rgba(209,149,36,0.2); padding: 4px 10px; border-radius: 20px; font-size: 10px; color: #ffd175;"> Eastern Cape</span>
                        <span style="background: rgba(209,149,36,0.2); padding: 4px 10px; border-radius: 20px; font-size: 10px; color: #ffd175;"> Free State</span>
                        <span style="background: rgba(209,149,36,0.2); padding: 4px 10px; border-radius: 20px; font-size: 10px; color: #ffd175;"> Gauteng</span>
                        <span style="background: rgba(209,149,36,0.2); padding: 4px 10px; border-radius: 20px; font-size: 10px; color: #ffd175;"> KwaZulu-Natal</span>
                        <span style="background: rgba(209,149,36,0.2); padding: 4px 10px; border-radius: 20px; font-size: 10px; color: #ffd175;"> Limpopo</span>
                        <span style="background: rgba(209,149,36,0.2); padding: 4px 10px; border-radius: 20px; font-size: 10px; color: #ffd175;"> Mpumalanga</span>
                        <span style="background: rgba(209,149,36,0.2); padding: 4px 10px; border-radius: 20px; font-size: 10px; color: #ffd175;"> Northern Cape</span>
                        <span style="background: rgba(209,149,36,0.2); padding: 4px 10px; border-radius: 20px; font-size: 10px; color: #ffd175;"> North West</span>
                        <span style="background: rgba(209,149,36,0.2); padding: 4px 10px; border-radius: 20px; font-size: 10px; color: #ffd175;"> Western Cape</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contact Info & Form -->
        <div class="col-md-7">
            <div class="card" style="background: rgba(0,0,0,0.55); backdrop-filter: blur(15px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.12); padding: 30px;">
                <h3 style="color: #ffd175; margin-bottom: 20px;">Contact Information</h3>
                
                <div style="margin-bottom: 25px;">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <div style="font-size: 24px;"></div>
                        <div>
                            <p style="color: white; font-weight: bold; margin: 0;">Email</p>
                            <p style="color: rgba(255,255,255,0.7); margin: 0;">support@UbuntuBay.co.za</p>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <div style="font-size: 24px;"></div>
                        <div>
                            <p style="color: white; font-weight: bold; margin: 0;">Phone</p>
                            <p style="color: rgba(255,255,255,0.7); margin: 0;">+27 78 019 2157</p>
                        </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="font-size: 24px;"></div>
                        <div>
                            <p style="color: white; font-weight: bold; margin: 0;">WhatsApp</p>
                            <p style="color: rgba(255,255,255,0.7); margin: 0;">+27 78 019 2157</p>
                        </div>
                    </div>
                </div>
                
                <hr style="border-color: rgba(255,255,255,0.1); margin: 25px 0;">
                
                <h3 style="color: #ffd175; margin-bottom: 20px;">Send us a Message</h3>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Your Name" style="background: rgba(255, 255, 255, 0.47); border: 1px solid rgba(255,255,255,0.15); color: white;" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Your Email" style="background: rgba(255, 255, 255, 0.47); border: 1px solid rgba(255,255,255,0.15); color: white;" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" name="subject" class="form-control" placeholder="Subject" style="background: rgba(255, 255, 255, 0.47); border: 1px solid rgba(255,255,255,0.15); color: white;" required>
                    </div>
                    <div class="mb-3">
                        <textarea name="message" rows="5" class="form-control" placeholder="Your Message..." style="background: rgba(255, 255, 255, 0.47); border: 1px solid rgba(255,255,255,0.15); color: white;" required></textarea>
                    </div>
                    <button type="submit" class="btn w-100" style="background: rgba(209,149,36,0.85); color: white; padding: 12px; border-radius: 30px;">Send Message →</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>