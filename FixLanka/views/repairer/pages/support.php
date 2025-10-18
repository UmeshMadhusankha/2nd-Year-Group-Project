<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - FixLanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../common/global.css">
    <link rel="stylesheet" href="../common/variables.css">
    <link rel="stylesheet" href="../common/topbar.css">
    <link rel="stylesheet" href="../common/sidebar.css">
    <link rel="stylesheet" href="support.css">
</head>
<body>
    <!-- Sidebar Toggle Checkbox -->
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">
    
    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Include Topbar -->
        <header class="header">
            <div class="header-left">
                <label for="sidebar-toggle" class="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </label>
                <div class="logo">
                    <img src="../common/fixlanka.png" alt="FixLanka" class="logo-image">
                </div>
                <div class="page-info">
                    <h1 class="page-title">Support</h1>
                    <p class="page-subtitle">Get help and manage your support tickets</p>
                </div>
            </div>
            <div class="header-right">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search requests, repairers, projects...">
                </div>
                <div class="notification-bell">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                <div class="profile-menu">
                    <img src="../common/user.png" alt="Admin" class="profile-avatar">
                    <div class="profile-dropdown">
                        <div class="profile-dropdown-header">
                            <h4 class="profile-dropdown-name">John Doe</h4>
                            <p class="profile-dropdown-email">john.doe@fixlanka.com</p>
                        </div>
                        <ul class="profile-dropdown-menu">
                            <li class="profile-dropdown-item">
                                <a href="profile.php" class="profile-dropdown-link" data-action="profile">
                                    <i class="fas fa-user"></i>
                                    <span>My Profile</span>
                                </a>
                            </li>
                            <li class="profile-dropdown-item">
                                <a href="settings.php" class="profile-dropdown-link" data-action="settings">
                                    <i class="fas fa-cog"></i>
                                    <span>Settings</span>
                                </a>
                            </li>
                            <li class="profile-dropdown-item">
                                <a href="upgrade.php" class="profile-dropdown-link" data-action="upgrade">
                                    <i class="fas fa-crown"></i>
                                    <span>Upgrade</span>
                                </a>
                            </li>
                            <div class="profile-dropdown-divider"></div>
                            <li class="profile-dropdown-item">
                                <a href="support.php" class="profile-dropdown-link" data-action="support">
                                    <i class="fas fa-life-ring"></i>
                                    <span>Support</span>
                                </a>
                            </li>
                            <li class="profile-dropdown-item">
                                <a href="#" class="profile-dropdown-link logout" data-action="logout">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <nav class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="welcome.php" class="nav-link">
                            <i class="fas fa-home"></i>
                            <span>Welcome</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="available-jobs.php" class="nav-link">
                            <i class="fas fa-briefcase"></i>
                            <span>Available Jobs</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="my-jobs.php" class="nav-link">
                            <i class="fas fa-tasks"></i>
                            <span>My Jobs</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="earnings.php" class="nav-link">
                            <i class="fas fa-wallet"></i>
                            <span>Earnings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="reviews.php" class="nav-link">
                            <i class="fas fa-star"></i>
                            <span>Reviews</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="profile.php" class="nav-link">
                            <i class="fas fa-user"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a href="support.php" class="nav-link">
                            <i class="fas fa-life-ring"></i>
                            <span>Support</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="upgrade.php" class="nav-link">
                            <i class="fas fa-crown"></i>
                            <span>Upgrade</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#settings" class="nav-link">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Footer in Sidebar -->
            <footer class="sidebar-footer">
                <p>© 2025 FixLanka<br>
                   <a href="#terms">Terms</a> | 
                   <a href="#privacy">Privacy</a> | 
                   <a href="#help">Help</a>
                </p>
            </footer>
        </aside>

        <!-- Main Content -->
        <main class="main-content-wrapper">
            <div class="main-content">
                <div class="content-wrapper">
                    <!-- Page Header -->
                    <section class="page-header">
                        <div class="page-header-content">
                            <div class="page-header-text">
                                <h2 class="page-title">Support</h2>
                                <p class="page-description">Get help and manage your support tickets</p>
                            </div>
                            <div class="page-header-actions">
                                <button class="btn btn-primary" id="new-ticket-btn">
                                    <i class="fas fa-plus"></i>
                                    <span>New Ticket</span>
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Support Content -->
                    <section class="support-section">
                        <div class="support-container">
                            <!-- Report Issue Form -->
                            <div class="support-form-section" id="support-form-section">
                                <div class="section-header">
                                    <h3 class="section-title">
                                        <i class="fas fa-exclamation-circle"></i>
                                        Report Issue
                                    </h3>
                                    <p class="section-description">Describe your issue and we'll help you resolve it</p>
                                </div>
                                
                                <form class="support-form" id="support-form">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="issue-subject" class="form-label">Subject *</label>
                                            <input type="text" id="issue-subject" name="subject" class="form-input" 
                                                   placeholder="Brief description of your issue" required>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="issue-message" class="form-label">Message *</label>
                                            <textarea id="issue-message" name="message" class="form-textarea" 
                                                      rows="6" placeholder="Please describe your issue in detail..." required></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="issue-file" class="form-label">Attachment (Optional)</label>
                                            <div class="file-upload-container">
                                                <input type="file" id="issue-file" name="attachment" class="file-input" 
                                                       accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                                                <label for="issue-file" class="file-upload-label">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <span class="file-upload-text">Choose file or drag here</span>
                                                </label>
                                                <div class="file-upload-info">
                                                    <small>Max file size: 10MB. Allowed formats: JPG, PNG, PDF, DOC</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-actions">
                                        <button type="button" class="btn btn-secondary" id="cancel-btn">
                                            <i class="fas fa-times"></i>
                                            <span>Cancel</span>
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submit-btn">
                                            <i class="fas fa-paper-plane"></i>
                                            <span>Submit</span>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Support Tickets Section -->
                            <div class="support-tickets-section" id="support-tickets-section">
                                <div class="section-header">
                                    <h3 class="section-title">
                                        <i class="fas fa-ticket-alt"></i>
                                        Support Tickets
                                    </h3>
                                    <p class="section-description">Track your support requests and their status</p>
                                </div>
                                
                                <div class="tickets-container">
                                    <div class="tickets-table-wrapper">
                                        <table class="tickets-table">
                                            <thead>
                                                <tr>
                                                    <th>Ticket ID</th>
                                                    <th>Subject</th>
                                                    <th>Status</th>
                                                    <th>Last Updated</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tickets-tbody">
                                                <tr class="ticket-row" data-ticket-id="TKT-2025-001">
                                                    <td class="ticket-id">#TKT-2025-001</td>
                                                    <td class="ticket-subject">Payment issue with job completion</td>
                                                    <td class="ticket-status">
                                                        <span class="status-badge status-open">Open</span>
                                                    </td>
                                                    <td class="ticket-updated">Aug 30, 2025 2:30 PM</td>
                                                    <td class="ticket-actions">
                                                        <button class="btn-icon view-ticket" data-ticket-id="TKT-2025-001" title="View Ticket">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr class="ticket-row" data-ticket-id="TKT-2025-002">
                                                    <td class="ticket-id">#TKT-2025-002</td>
                                                    <td class="ticket-subject">Unable to upload profile picture</td>
                                                    <td class="ticket-status">
                                                        <span class="status-badge status-resolved">Resolved</span>
                                                    </td>
                                                    <td class="ticket-updated">Aug 28, 2025 11:15 AM</td>
                                                    <td class="ticket-actions">
                                                        <button class="btn-icon view-ticket" data-ticket-id="TKT-2025-002" title="View Ticket">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr class="ticket-row" data-ticket-id="TKT-2025-003">
                                                    <td class="ticket-id">#TKT-2025-003</td>
                                                    <td class="ticket-subject">App crashes on job search</td>
                                                    <td class="ticket-status">
                                                        <span class="status-badge status-in-progress">In Progress</span>
                                                    </td>
                                                    <td class="ticket-updated">Aug 25, 2025 4:45 PM</td>
                                                    <td class="ticket-actions">
                                                        <button class="btn-icon view-ticket" data-ticket-id="TKT-2025-003" title="View Ticket">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Empty State -->
                                    <div class="empty-state" id="tickets-empty-state" style="display: none;">
                                        <div class="empty-icon">
                                            <i class="fas fa-ticket-alt"></i>
                                        </div>
                                        <h4 class="empty-title">No Support Tickets</h4>
                                        <p class="empty-description">You haven't submitted any support tickets yet.</p>
                                        <button class="btn btn-primary" id="create-first-ticket">
                                            <i class="fas fa-plus"></i>
                                            Create Your First Ticket
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>

    <!-- Ticket Detail Modal -->
    <div class="modal-overlay" id="ticket-modal-overlay">
        <div class="ticket-modal" id="ticket-modal">
            <div class="modal-header">
                <div class="modal-title-section">
                    <h3 class="modal-title" id="modal-ticket-title">Ticket Details</h3>
                    <span class="ticket-id-badge" id="modal-ticket-id">#TKT-2025-001</span>
                </div>
                <button class="modal-close" id="close-ticket-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="ticket-info">
                    <div class="ticket-meta">
                        <div class="meta-item">
                            <span class="meta-label">Status:</span>
                            <span class="status-badge" id="modal-ticket-status">Open</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Created:</span>
                            <span class="meta-value" id="modal-ticket-created">Aug 30, 2025 2:30 PM</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Last Updated:</span>
                            <span class="meta-value" id="modal-ticket-updated">Aug 30, 2025 2:30 PM</span>
                        </div>
                    </div>
                </div>
                
                <div class="conversation-view" id="conversation-view">
                    <div class="conversation-header">
                        <h4>Conversation</h4>
                    </div>
                    
                    <div class="chat-container" id="chat-container">
                        <!-- User Message -->
                        <div class="chat-message user-message">
                            <div class="message-avatar">
                                <img src="../common/user.png" alt="You" class="avatar-img">
                            </div>
                            <div class="message-content">
                                <div class="message-header">
                                    <span class="message-author">You</span>
                                    <span class="message-time">Aug 30, 2025 2:30 PM</span>
                                </div>
                                <div class="message-text">
                                    <p>I completed a repair job yesterday but the payment hasn't been processed yet. The job was marked as complete by the customer but I still don't see the payment in my earnings. Can you please help me with this issue?</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Support Response -->
                        <div class="chat-message support-message">
                            <div class="message-avatar">
                                <div class="support-avatar">
                                    <i class="fas fa-headset"></i>
                                </div>
                            </div>
                            <div class="message-content">
                                <div class="message-header">
                                    <span class="message-author">FixLanka Support</span>
                                    <span class="message-time">Aug 30, 2025 3:45 PM</span>
                                </div>
                                <div class="message-text">
                                    <p>Hello John! Thank you for contacting us about your payment issue. I understand your concern about the delayed payment processing.</p>
                                    <p>I've checked your account and can see the completed job. Payment processing typically takes 1-2 business days after job completion. Since you completed the job yesterday, the payment should be processed by tomorrow.</p>
                                    <p>I'll monitor your case and if the payment doesn't appear by tomorrow evening, I'll escalate this to our payments team immediately.</p>
                                    <p>Is there anything else I can help you with regarding this issue?</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Reply Form -->
                    <div class="reply-form" id="reply-form">
                        <div class="reply-input-container">
                            <textarea id="reply-message" class="reply-input" rows="3" placeholder="Type your reply..."></textarea>
                            <div class="reply-actions">
                                <button class="btn btn-secondary btn-sm" id="attach-file-btn">
                                    <i class="fas fa-paperclip"></i>
                                    Attach
                                </button>
                                <button class="btn btn-primary btn-sm" id="send-reply-btn">
                                    <i class="fas fa-paper-plane"></i>
                                    Send
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include JavaScript -->
    <script src="../common/common.js"></script>
    <script src="support.js"></script>
</body>
</html>
