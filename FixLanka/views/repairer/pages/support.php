<?php
// Page configuration
$currentPage = 'support';
$pageTitle = 'Support';
$pageSubtitle = 'Get help and manage your support tickets';
$searchPlaceholder = 'Search requests, repairers, projects...';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - FixLanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/global.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/support.css">
</head>
<body>
    <!-- Sidebar Toggle Checkbox -->
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">
    
    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Include Topbar -->
        <?php require_once __DIR__ . '/../common/topbar.php'; ?>

        <!-- Include Sidebar -->
        <?php require_once __DIR__ . '/../common/sidebar.php'; ?>

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
                                                <tr id="tickets-loading-row">
                                                    <td colspan="5" style="text-align:center;padding:40px;color:var(--text-secondary)">
                                                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                                                        <p style="margin-top:12px">Loading tickets...</p>
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
                    <span class="ticket-id-badge" id="modal-ticket-id"></span>
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
                            <span class="meta-value" id="modal-ticket-created">—</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Last Updated:</span>
                            <span class="meta-value" id="modal-ticket-updated">—</span>
                        </div>
                    </div>
                </div>
                
                <div class="conversation-view" id="conversation-view">
                    <div class="conversation-header">
                        <h4>Conversation</h4>
                    </div>
                    
                    <div class="chat-container" id="chat-container">
                        <!-- Conversation loaded dynamically -->
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
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/common/common.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/support.js"></script>
</body>
</html>

