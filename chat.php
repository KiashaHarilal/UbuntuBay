<?php
require_once 'includes/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Get all conversations for this user
$conversations_sql = "SELECT DISTINCT 
    CASE 
        WHEN m.sender_id = $user_id THEN m.receiver_id
        ELSE m.sender_id
    END as other_user_id,
    u.name as other_user_name,
    (SELECT message_body FROM messages WHERE 
        (sender_id = m.sender_id AND receiver_id = m.receiver_id) OR 
        (sender_id = m.receiver_id AND receiver_id = m.sender_id) 
     ORDER BY sent_at DESC LIMIT 1) as last_message,
    (SELECT sent_at FROM messages WHERE 
        (sender_id = m.sender_id AND receiver_id = m.receiver_id) OR 
        (sender_id = m.receiver_id AND receiver_id = m.sender_id) 
     ORDER BY sent_at DESC LIMIT 1) as last_time
FROM messages m
JOIN users u ON u.user_id = CASE 
    WHEN m.sender_id = $user_id THEN m.receiver_id
    ELSE m.sender_id
END
WHERE m.sender_id = $user_id OR m.receiver_id = $user_id
ORDER BY last_time DESC";

$conversations_result = mysqli_query($conn, $conversations_sql);

// Get selected conversation
$selected_user_id = isset($_GET['user']) ? (int)$_GET['user'] : 0;
$messages = [];

if ($selected_user_id) {
    $messages_sql = "SELECT m.*, u.name as sender_name 
                     FROM messages m
                     JOIN users u ON m.sender_id = u.user_id
                     WHERE (sender_id = $user_id AND receiver_id = $selected_user_id)
                        OR (sender_id = $selected_user_id AND receiver_id = $user_id)
                     ORDER BY sent_at ASC";
    $messages_result = mysqli_query($conn, $messages_sql);
    while ($row = mysqli_fetch_assoc($messages_result)) {
        $messages[] = $row;
    }
    
    // Mark messages as read
    $update_sql = "UPDATE messages SET is_read = 1 
                   WHERE sender_id = $selected_user_id AND receiver_id = $user_id AND is_read = 0";
    mysqli_query($conn, $update_sql);
}

// Get all users for new conversation (except current user)
$users_sql = "SELECT user_id, name, province FROM users WHERE user_id != $user_id ORDER BY name";
$users_result = mysqli_query($conn, $users_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UbuntuBay - Messages</title>
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
        }

        .hero-wrapper {
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: linear-gradient(180deg, rgba(14, 25, 22, 0.85) 0%, rgba(18, 16, 13, 0.95) 100%);
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
            font-size: 14px;
        }

        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
        }

        .btn-outline-light {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 20px !important;
            padding: 6px 16px !important;
        }

        .btn-warning-custom {
            background: rgba(209, 149, 36, 0.2) !important;
            border: 1px solid rgba(209, 149, 36, 0.4) !important;
            color: #ffd175 !important;
            border-radius: 20px !important;
            padding: 6px 16px !important;
        }

        /* Chat Container */
        .chat-container {
            flex: 1;
            padding: 40px 20px;
        }

        .chat-wrapper {
            max-width: 1300px;
            margin: 0 auto;
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(15px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            overflow: hidden;
            display: flex;
            min-height: 600px;
        }

        /* Conversations Sidebar */
        .conversations-sidebar {
            width: 320px;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h3 {
            color: white;
            font-size: 18px;
            margin: 0;
        }

        .conversations-list {
            flex: 1;
            overflow-y: auto;
        }

        .conversation-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }

        .conversation-item:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .conversation-item.active {
            background: rgba(209, 149, 36, 0.2);
            border-left: 3px solid #ffd175;
        }

        .conversation-avatar {
            width: 50px;
            height: 50px;
            background: rgba(209, 149, 36, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .conversation-info {
            flex: 1;
        }

        .conversation-name {
            color: white;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .conversation-last {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 180px;
        }

        .conversation-time {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.4);
        }

        /* Chat Area */
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chat-header-avatar {
            width: 45px;
            height: 45px;
            background: rgba(209, 149, 36, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .chat-header-info h4 {
            color: white;
            margin: 0;
            font-size: 18px;
        }

        .chat-header-info p {
            color: rgba(255, 255, 255, 0.5);
            margin: 0;
            font-size: 12px;
        }

        .messages-area {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .message {
            display: flex;
            flex-direction: column;
            max-width: 70%;
        }

        .message-sent {
            align-self: flex-end;
        }

        .message-received {
            align-self: flex-start;
        }

        .message-bubble {
            padding: 10px 16px;
            border-radius: 20px;
            font-size: 14px;
            word-wrap: break-word;
        }

        .message-sent .message-bubble {
            background: rgba(209, 149, 36, 0.85);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message-received .message-bubble {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-bottom-left-radius: 4px;
        }

        .message-time {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.4);
            margin-top: 5px;
            margin-left: 10px;
        }

        .message-sent .message-time {
            text-align: right;
        }

        /* Message Input */
        .message-input-area {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            gap: 10px;
        }

        .message-input {
            flex: 1;
            padding: 12px 18px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 30px;
            color: white;
            font-size: 14px;
        }

        .message-input:focus {
            outline: none;
            border-color: rgba(209, 149, 36, 0.6);
        }

        .btn-send {
            padding: 12px 25px;
            background: rgba(209, 149, 36, 0.85);
            border: none;
            border-radius: 30px;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-send:hover {
            background: rgba(209, 149, 36, 1);
        }

        .no-conversation {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
            padding: 40px;
        }

        .new-chat-btn {
            margin: 15px 20px;
            padding: 10px;
            background: rgba(209, 149, 36, 0.2);
            border: 1px solid rgba(209, 149, 36, 0.4);
            border-radius: 30px;
            color: #ffd175;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            display: block;
            font-size: 14px;
        }

        .new-chat-btn:hover {
            background: rgba(209, 149, 36, 0.4);
        }

        footer {
            background: #1a1a2e;
            color: white;
            text-align: center;
            padding: 30px;
        }

        @media (max-width: 768px) {
            .chat-wrapper {
                flex-direction: column;
            }
            .conversations-sidebar {
                width: 100%;
                max-height: 300px;
            }
            .nav-links { display: none; }
        }
    </style>
</head>
<body>

<div class="hero-wrapper">
    <!-- Navbar -->
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
                    <a href="login.php" class="btn-outline-light">Login</a>
                    <a href="register.php" class="btn-warning-custom">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Chat Section -->
    <div class="chat-container">
        <div class="chat-wrapper">
            <!-- Conversations Sidebar -->
            <div class="conversations-sidebar">
                <div class="sidebar-header">
                    <h3> Messages</h3>
                </div>
                <div class="conversations-list">
                    <?php if (mysqli_num_rows($conversations_result) > 0): ?>
                        <?php while ($conv = mysqli_fetch_assoc($conversations_result)): ?>
                            <a href="chat.php?user=<?php echo $conv['other_user_id']; ?>" class="conversation-item <?php echo $selected_user_id == $conv['other_user_id'] ? 'active' : ''; ?>">
                                <div class="conversation-avatar">👤</div>
                                <div class="conversation-info">
                                    <div class="conversation-name"><?php echo htmlspecialchars($conv['other_user_name']); ?></div>
                                    <div class="conversation-last"><?php echo htmlspecialchars(substr($conv['last_message'], 0, 40)); ?></div>
                                </div>
                                <div class="conversation-time">
                                    <?php echo date('H:i', strtotime($conv['last_time'])); ?>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="padding: 40px; text-align: center; color: rgba(255,255,255,0.5);">
                            No conversations yet
                        </div>
                    <?php endif; ?>
                </div>
                <a href="#" class="new-chat-btn" data-bs-toggle="modal" data-bs-target="#newChatModal">+ New Conversation</a>
            </div>

            <!-- Chat Area -->
            <div class="chat-area">
                <?php if ($selected_user_id > 0): 
                    $user_info_sql = "SELECT name FROM users WHERE user_id = $selected_user_id";
                    $user_info_result = mysqli_query($conn, $user_info_sql);
                    $selected_user = mysqli_fetch_assoc($user_info_result);
                ?>
                    <div class="chat-header">
                        <div class="chat-header-avatar">👤</div>
                        <div class="chat-header-info">
                            <h4><?php echo htmlspecialchars($selected_user['name']); ?></h4>
                            <p>Online</p>
                        </div>
                    </div>

                    <div class="messages-area" id="messages-area">
                        <?php if (count($messages) > 0): ?>
                            <?php foreach ($messages as $msg): ?>
                                <div class="message <?php echo $msg['sender_id'] == $user_id ? 'message-sent' : 'message-received'; ?>">
                                    <div class="message-bubble">
                                        <?php echo htmlspecialchars($msg['message_body']); ?>
                                    </div>
                                    <div class="message-time">
                                        <?php echo date('H:i', strtotime($msg['sent_at'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="text-align: center; color: rgba(255,255,255,0.5); padding: 40px;">
                                No messages yet. Send a message to start the conversation!
                            </div>
                        <?php endif; ?>
                    </div>

                    <form method="POST" action="send_message.php" class="message-input-area">
                        <input type="hidden" name="receiver_id" value="<?php echo $selected_user_id; ?>">
                        <input type="text" name="message" class="message-input" placeholder="Type a message..." required>
                        <button type="submit" class="btn-send">Send →</button>
                    </form>
                <?php else: ?>
                    <div class="no-conversation">
                        <div>
                            <h3> Select a conversation</h3>
                            <p>Choose a chat from the sidebar or start a new conversation</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- New Chat Modal -->
<div class="modal fade" id="newChatModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: rgba(0,0,0,0.9); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.1);">
            <div class="modal-header" style="border-bottom-color: rgba(255,255,255,0.1);">
                <h5 class="modal-title" style="color: white;">Start New Conversation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <select id="newChatUser" class="form-control" style="background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2);">
                    <option value="" style="background: #333;">Select a user</option>
                    <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                        <option value="<?php echo $user['user_id']; ?>" style="background: #333;">
                            <?php echo htmlspecialchars($user['name']); ?> (<?php echo $user['province']; ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="modal-footer" style="border-top-color: rgba(255,255,255,0.1);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="startNewChat()" style="background: #ffd175; border: none; color: #000;">Start Chat</button>
            </div>
        </div>
    </div>
</div>

<footer>
    <p><strong>UbuntuBay</strong> – Empowering South Africa's Informal Traders</p>
    <p style="color:#888;">© 2026 UbuntuBay. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function startNewChat() {
        var userId = document.getElementById('newChatUser').value;
        if (userId) {
            window.location.href = 'chat.php?user=' + userId;
        } else {
            alert('Please select a user');
        }
    }
    
    // Auto-scroll to bottom of messages
    var messagesArea = document.getElementById('messages-area');
    if (messagesArea) {
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }
</script>
</body>
</html>