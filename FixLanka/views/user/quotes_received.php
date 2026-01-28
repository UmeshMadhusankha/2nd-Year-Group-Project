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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quotes Received - Fix Lanka</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/navbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/profile.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/quotes_received.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <main class="main-content quotes-page">
        <div class="content-wrapper">
            <div class="page-header">
                <div class="page-title-section">
                    <h1 class="page-title">Quotes Received</h1>
                    <p class="page-subtitle">Review and respond to quotations from repairers and companies</p>
                </div>
            </div>

            <div class="profile-card quotes-card quotes-page-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-invoice-dollar"></i>
                        Quotes
                    </h3>
                    <span class="quotes-badge" id="quotesPendingBadge" style="display:none;">0 new</span>
                </div>

                <div class="card-content">
                    <div class="quotes-toolbar">
                        <label class="quotes-filter">
                            <span>Status:</span>
                            <select id="quoteStatusFilter">
                                <option value="">All</option>
                                <option value="pending">Pending</option>
                                <option value="accepted">Accepted</option>
                                <option value="rejected">Rejected</option>
                                <option value="expired">Expired</option>
                                <option value="successful">Successful</option>
                            </select>
                        </label>
                    </div>

                    <div class="quotes-list" id="quotesList">
                        <div class="quote-item">
                            <div class="quote-details">
                                <span class="quote-job">Loading...</span>
                            </div>
                        </div>
                    </div>

                    <div class="quotes-footer">
                        <button class="btn-secondary" id="loadMoreQuotesBtn" style="display:none;">
                            Load More
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/quotes_received.js"></script>
</body>
</html>
