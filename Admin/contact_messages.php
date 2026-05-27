<?php
session_start(); // Added session_start() just in case it was missing from your snippet
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if (isset($_GET['delete'])) {
    $msg_id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM contact_messages WHERE message_id = $msg_id");
    header('Location: contact_messages.php');
    exit();
}

$messages = mysqli_query($conn, "SELECT * FROM contact_messages ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Admin</title>
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
        .message-preview { max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        
        .btn-delete { background: rgba(220,53,69,0.2); color: #ff8a92; padding: 5px 12px; border-radius: 20px; text-decoration: none; font-size: 12px; display: inline-block; }
        .btn-delete:hover { background: rgba(220,53,69,0.4); }
        .back-link { color: #ffd175; text-decoration: none; margin-bottom: 20px; display: inline-block; }

        @media (max-width: 768px) { 
            .admin-container { flex-direction: column; } 
            .sidebar { width: 100%; } 
            .nav-links { display: none; } 
            table { font-size: 12px; } 
            .message-preview { max-width: 100px; } 
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
            <a href="listings.php">Manage Listings</a>
            <a href="contact_messages.php" class="active">Contact Messages</a>
            <a href="logout.php">Logout</a>
        </div>

        <div class="main-content">
            <div class="content-card">
                <a href="index.php" class="back-link">← Back to Dashboard</a>
                <h1>Contact Messages</h1>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr><th>ID</th><th>Name</th><th>Email</th><th>Subject</th><th>Message</th><th>Date</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <?php while ($msg = mysqli_fetch_assoc($messages)): ?>
                            <tr>
                                <td><?php echo $msg['message_id']; ?></td>
                                <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                                <td class="message-preview"><?php echo htmlspecialchars(substr($msg['message'], 0, 80)); ?>...</td>
                                <td><?php echo $msg['created_at']; ?></td>
                                <td><a href="?delete=<?php echo $msg['message_id']; ?>" class="btn-delete" onclick="return confirm('Delete this message?')">Delete</a></td>
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