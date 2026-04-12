<?php
require_once __DIR__ . '/../../config/session.php';

if (!isLoggedIn()) {
    header('Location: /2nd-Year-Group-Project/FixLanka/login');
    exit;
}

$userData = getUserData();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Fix Lanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/navbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/chat.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="chat-container">
        <aside class="conversations-sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title">Messages</h2>
                <button class="new-chat-btn"><i class="fas fa-plus"></i></button>
            </div>
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search..." id="searchConversations">
            </div>
            <div class="conversations-list" id="conversationsList">
                <div class="conversation-item active" data-user-id="1">
                    <div class="conversation-avatar">
                        <img src="https://via.placeholder.com/50" alt="User">
                        <span class="online-indicator"></span>
                    </div>
                    <div class="conversation-info">
                        <div class="conversation-header">
                            <h3 class="conversation-name">Kasun Silva</h3>
                            <span class="conversation-time">2m</span>
                        </div>
                        <div class="conversation-preview">
                            <p class="last-message">Sure, I can help with that</p>
                            <span class="unread-badge">3</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        <main class="chat-main">
            <div class="chat-header">
                <div class="chat-user-info">
                    <div class="chat-avatar">
                        <img src="https://via.placeholder.com/40" alt="User">
                        <span class="online-indicator"></span>
                    </div>
                    <div class="chat-user-details">
                        <h2 class="chat-user-name">Kasun Silva</h2>
                        <p class="chat-user-status">Active now</p>
                    </div>
                </div>
            </div>
            <div class="messages-area" id="messagesArea">
                <div class="date-divider"><span>Today</span></div>
                <div class="message received">
                    <div class="message-avatar">
                        <img src="https://via.placeholder.com/35" alt="User">
                    </div>
                    <div class="message-content">
                        <div class="message-bubble"><p>Hi! How can I help?</p></div>
                        <span class="message-time">10:30 AM</span>
                    </div>
                </div>
                <div class="message sent">
                    <div class="message-content">
                        <div class="message-bubble"><p>I need a quote</p></div>
                        <span class="message-time">10:32 AM</span>
                    </div>
                </div>
            </div>
            <div class="message-input-container">
                <button class="attachment-btn"><i class="fas fa-paperclip"></i></button>
                <button class="emoji-btn"><i class="far fa-smile"></i></button>
                <div class="input-wrapper">
                    <textarea class="message-input" id="messageInput" placeholder="Type a message..." rows="1"></textarea>
                </div>
                <button class="send-btn" id="sendBtn"><i class="fas fa-paper-plane"></i></button>
            </div>
        </main>
    </div>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/chat.js"></script>
</body>
</html>
