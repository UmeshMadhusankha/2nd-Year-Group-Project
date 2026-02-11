<?php
/**
 * Customer Contracts Page
 * 
 * Read-only view of all contracts sent to the logged-in customer.
 * Customers can view contract details and accept/decline pending contracts.
 * 
 * @package FixLanka\Views\User
 */

require_once '../../config/session.php';
requireRole('user');

$userData = getUserData();
$userId = $userData['id'] ?? null;

if (!$userId) {
    header('Location: /2nd-Year-Group-Project/FixLanka/login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Contracts - Fix Lanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/navbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/contracts.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <main class="main-content">
        <div class="contracts-page">
            <!-- Page Header -->
            <div class="contracts-page-header">
                <h1><i class="fas fa-file-contract"></i> My Contracts</h1>
                <p>View and manage contracts from your service providers</p>
            </div>

            <!-- Stats -->
            <div class="contracts-stats">
                <div class="cstat-card">
                    <span class="cstat-value info" id="statTotal">0</span>
                    <span class="cstat-label">Total</span>
                </div>
                <div class="cstat-card">
                    <span class="cstat-value primary" id="statActive">0</span>
                    <span class="cstat-label">Active</span>
                </div>
                <div class="cstat-card">
                    <span class="cstat-value warning" id="statPending">0</span>
                    <span class="cstat-label">Pending</span>
                </div>
                <div class="cstat-card">
                    <span class="cstat-value success" id="statCompleted">0</span>
                    <span class="cstat-label">Completed</span>
                </div>
            </div>

            <!-- Filters -->
            <div class="contracts-filter-bar">
                <select class="filter-select" id="custStatusFilter">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="terminated">Terminated</option>
                </select>
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" class="search-input" id="custSearchInput" placeholder="Search by title, company or contract number...">
                </div>
            </div>

            <!-- Contract Cards List -->
            <div class="contracts-list" id="contractsList">
                <div class="contracts-loading">
                    <div class="spinner"></div>
                    <p>Loading your contracts...</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Contract Detail Modal -->
    <div class="cd-overlay" id="contractDetailOverlay">
        <div class="cd-modal">
            <div class="cd-header">
                <div class="cd-header-left">
                    <div class="cd-header-icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div class="cd-header-text">
                        <h2 id="cdHeaderTitle">Contract Details</h2>
                        <span id="cdHeaderSub"></span>
                    </div>
                </div>
                <button class="cd-close" id="cdCloseBtn"><i class="fas fa-times"></i></button>
            </div>
            <div class="cd-body" id="contractDetailBody">
                <!-- Populated by JS -->
            </div>
            <div class="cd-footer" id="cdFooter">
                <button class="cd-btn secondary" onclick="closeContractDetail()"><i class="fas fa-times"></i> Close</button>
            </div>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/contracts.js"></script>
</body>
</html>
