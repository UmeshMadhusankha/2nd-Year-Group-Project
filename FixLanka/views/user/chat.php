<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\user\chat.php
require_once __DIR__ . '/../../config/session.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: /2nd-Year-Group-Project/FixLanka/login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chats - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/chat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Main Chat Container -->
    <main class="main-content">
        <div class="chat-container">
            <!-- Left Sidebar - Chat List -->
            <div class="chat-sidebar" id="chatSidebar">
                <div class="sidebar-header">
                    <h2 class="sidebar-title">Messages</h2>
                    <button class="back-btn" id="backToList" style="display: none;">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                </div>

                <!-- Search Bar -->
                <div class="chat-search">
                    <i class="fas fa-search search-icon"></i>
                    <input 
                        type="text" 
                        id="chatSearchInput" 
                        class="search-input" 
                        placeholder="Search conversations..."
                        aria-label="Search conversations"
                    >
                </div>

                <!-- Filter Tabs -->
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="all">All</button>
                    <button class="filter-tab" data-filter="companies">Companies</button>
                    <button class="filter-tab" data-filter="individuals">Individuals</button>
                </div>

                <!-- Chat List -->
                <div class="chat-list" id="chatList">
                    <!-- Chat items will be populated by JavaScript -->
                </div>
            </div>

            <!-- Right Panel - Active Chat -->
            <div class="chat-panel" id="chatPanel">
                <!-- Empty State -->
                <div class="empty-chat-state" id="emptyChatState">
                    <div class="empty-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3>Select a conversation</h3>
                    <p>Choose a conversation from the list to start messaging</p>
                </div>

                <!-- Active Chat -->
                <div class="active-chat" id="activeChat" style="display: none;">
                    <!-- Chat Header -->
                    <div class="chat-header">
                        <div class="chat-user-info">
                            <img id="chatAvatar" src="" alt="User" class="chat-avatar">
                            <div class="chat-user-details">
                                <h3 id="chatUserName" class="chat-user-name"></h3>
                                <p id="chatUserStatus" class="chat-user-status">Online</p>
                            </div>
                        </div>
                        <div class="chat-actions">
                            <button class="chat-action-btn" id="sendAgreementBtn" title="Send Agreement">
                                <i class="fas fa-file-contract"></i>
                            </button>
                            <button class="chat-action-btn" id="makePaymentBtn" title="Make Payment" style="display: none;">
                                <i class="fas fa-credit-card"></i>
                            </button>
                            <button class="chat-action-btn" title="More options">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Agreement Badge (if exists) -->
                    <div class="agreement-badge" id="agreementBadge" style="display: none;">
                        <i class="fas fa-info-circle"></i>
                        <span id="agreementStatus"></span>
                    </div>

                    <!-- Chat Messages -->
                    <div class="chat-messages" id="chatMessages">
                        <!-- Messages will be populated by JavaScript -->
                    </div>

                    <!-- Chat Input -->
                    <div class="chat-input-container">
                        <button class="attach-btn" id="attachBtn" title="Attach file">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <input type="file" id="fileInput" style="display: none;" accept="image/*,video/*,.pdf,.doc,.docx">
                        <textarea 
                            id="messageInput" 
                            class="message-input" 
                            placeholder="Type a message..."
                            rows="1"
                        ></textarea>
                        <button class="send-btn" id="sendBtn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Agreement Modal -->
    <div class="modal-overlay" id="agreementModal">
        <div class="modal-container agreement-modal">
            <div class="modal-header">
                <h3 class="modal-title">Create Job Agreement</h3>
                <button class="modal-close" id="agreementModalClose" aria-label="Close modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="modal-content">
                <form id="agreementForm">
                    <div class="form-group">
                        <label for="agreementJobTitle">Job Title <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="agreementJobTitle" 
                            class="form-input"
                            placeholder="e.g., Kitchen Sink Repair"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="agreementDescription">Job Description <span class="required">*</span></label>
                        <textarea 
                            id="agreementDescription" 
                            class="form-textarea"
                            rows="4"
                            placeholder="Describe the job details..."
                            required
                        ></textarea>
                    </div>

                    <div class="form-group">
                        <label for="agreementDeadline">Deadline <span class="required">*</span></label>
                        <input 
                            type="date" 
                            id="agreementDeadline" 
                            class="form-input"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="milestonePayment">
                            <span>Enable milestone-based payments</span>
                        </label>
                    </div>

                    <div id="milestoneDetails" style="display: none;">
                        <div class="form-group">
                            <label for="milestoneCount">Number of Milestones <span class="required">*</span></label>
                            <select id="milestoneCount" class="form-select">
                                <option value="2">2 Milestones</option>
                                <option value="3">3 Milestones</option>
                                <option value="4">4 Milestones</option>
                                <option value="5">5 Milestones</option>
                            </select>
                        </div>

                        <div id="milestonesList"></div>
                    </div>

                    <div class="form-group">
                        <label for="agreementBudget">Total Budget (LKR) <span class="required">*</span></label>
                        <input 
                            type="number" 
                            id="agreementBudget" 
                            class="form-input"
                            placeholder="e.g., 5000"
                            min="0"
                            step="100"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="paymentMethod">Payment Method <span class="required">*</span></label>
                        <select id="paymentMethod" class="form-select" required>
                            <option value="">Select payment method</option>
                            <option value="card">Credit/Debit Card</option>
                            <option value="bank">Bank Transfer</option>
                            <option value="mobile">Mobile Payment</option>
                        </select>
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" id="cancelAgreement">Cancel</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane"></i>
                            Send Agreement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Payment Confirmation Modal -->
    <div class="modal-overlay" id="paymentModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Make Payment</h3>
                <button class="modal-close" id="paymentModalClose" aria-label="Close modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="modal-content">
                <div class="payment-info">
                    <p>You will be redirected to the payment page to complete the transaction.</p>
                    <div class="payment-details" id="paymentDetails"></div>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="cancelPayment">Cancel</button>
                    <button type="button" class="btn-primary" id="proceedPayment">
                        <i class="fas fa-credit-card"></i>
                        Proceed to Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <div class="toast-content">
            <i class="fas fa-check-circle toast-icon"></i>
            <span class="toast-message" id="toastMessage">Success message</span>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/chat.js"></script>
</body>
</html>
