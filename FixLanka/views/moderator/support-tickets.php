<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/../../includes/admin-modarator/auth.php';

requireRole('moderator');

$basePath = '../..';
$currentPath = '/2nd-Year-Group-Project/FixLanka/moderator-support-tickets';
$pageTitle = 'Support Tickets';
$pageDescription = 'Manage support tickets and reply to users.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin-moderator/support-tickets.css">
</head>
<body class="bg-background text-foreground">
<input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

<div class="dashboard-container">
    <?php renderModeratorSidebar($currentPath, $basePath); ?>

    <div class="dashboard-main">
        <?php renderPageHeader($basePath, 'Support Tickets', 'Manage support tickets and responses'); ?>

        <main class="support-tickets-page">
            <div class="support-tickets-header">
                <h2>Support Tickets</h2>
            </div>

            <section class="support-tickets-stats" id="supportTicketsStats">
                <div class="stats-card">
                    <span class="stats-label">Total</span>
                    <span class="stats-value" id="statsTotal">0</span>
                </div>
                <div class="stats-card">
                    <span class="stats-label">Open</span>
                    <span class="stats-value" id="statsOpen">0</span>
                </div>
                <div class="stats-card">
                    <span class="stats-label">Pending</span>
                    <span class="stats-value" id="statsPending">0</span>
                </div>
                <div class="stats-card">
                    <span class="stats-label">In Progress</span>
                    <span class="stats-value" id="statsInProgress">0</span>
                </div>
                <div class="stats-card">
                    <span class="stats-label">Resolved</span>
                    <span class="stats-value" id="statsResolved">0</span>
                </div>
                <div class="stats-card">
                    <span class="stats-label">Closed</span>
                    <span class="stats-value" id="statsClosed">0</span>
                </div>
            </section>

            <div class="support-tickets-filters">
                <div class="filter-group">
                    <label for="filterUserType">User Type</label>
                    <select id="filterUserType">
                        <option value="">All</option>
                        <option value="user">User</option>
                        <option value="repairer">Repairer</option>
                        <option value="company">Company</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filterStatus">Status</label>
                    <select id="filterStatus">
                        <option value="">All</option>
                        <option value="open">Open</option>
                        <option value="in-progress">In Progress</option>
                        <option value="pending">Pending</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filterCategory">Category</label>
                    <select id="filterCategory">
                        <option value="">All</option>
                        <option value="technical">Technical</option>
                        <option value="account">Account</option>
                        <option value="payment">Payment</option>
                        <option value="billing">Billing</option>
                        <option value="feature">Feature</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <button class="filter-reset" id="filterResetBtn">Reset</button>
            </div>

            <table class="support-tickets-table">
                <thead>
                    <tr>
                        <th>Ticket</th>
                        <th>User Type</th>
                        <th>User ID</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="supportTicketsBody"></tbody>
            </table>
        </main>
    </div>
</div>

<div class="ticket-modal-overlay" id="ticketModalOverlay">
    <div class="ticket-modal">
        <div class="ticket-modal-header">
            <h3 id="ticketModalTitle">Ticket Details</h3>
            <button class="ticket-close-btn" id="ticketModalClose">&times;</button>
        </div>
        <div class="ticket-modal-body">
            <div>
                <div class="ticket-conversation" id="ticketConversation"></div>
            </div>
            <div>
                <div class="ticket-meta" id="ticketMeta"></div>
                <form class="reply-form" id="ticketReplyForm">
                    <label>
                        Status
                        <select id="ticketStatusSelect">
                            <option value="open">Open</option>
                            <option value="in-progress">In Progress</option>
                            <option value="pending">Pending</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </label>
                    <label>
                        Reply
                        <textarea id="ticketReplyMessage" placeholder="Write a reply..."></textarea>
                    </label>
                    <button type="submit">Send Reply</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>
<script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/support-tickets.js"></script>
</body>
</html>
