<?php
/**
 * Customer Contracts Page
 * 
 * Read-only view of all contracts sent to the logged-in customer.
 * Customers can view contract details and accept/decline pending contracts.
 * 
 * @package FixLanka\Views\User
 */

require_once __DIR__ . '/../../config/session.php';
requireRole(['user', 'customer']);

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
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/common.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/modals.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/navbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/buttons.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/contracts.css?v=1.1">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/chat.css">
</head>
<body>
    <?php include __DIR__ . '/navbar.php'; ?>

    <main class="main-content">
        <div class="page-container contracts-page">
            <!-- Page Header (match other user pages) -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">My Contracts</h1>
                    <p class="page-subtitle">View and manage contracts from your service providers</p>
                </div>
                <a href="/2nd-Year-Group-Project/FixLanka/views/user/landing.php" class="btn-home">
                    <i class="fas fa-home"></i> Home
                </a>
            </div>

            <!-- Filter Tabs + Search (match Job Requests style) -->
            <div class="filter-controls">
                <div class="filter-tabs" id="contractStatusTabs">
                    <button class="filter-tab active" data-status="">
                        All Contracts <span class="tab-count" id="statTotal">0</span>
                    </button>
                    <button class="filter-tab" data-status="pending">
                        Pending <span class="tab-count" id="statPending">0</span>
                    </button>
                    <button class="filter-tab" data-status="active">
                        Active <span class="tab-count" id="statActive">0</span>
                    </button>
                    <button class="filter-tab" data-status="completed">
                        Completed <span class="tab-count" id="statCompleted">0</span>
                    </button>
                    <button class="filter-tab" data-status="terminated">
                        Terminated <span class="tab-count" id="statTerminated">0</span>
                    </button>
                </div>

                <div class="search-controls">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="search-input" id="custSearchInput" placeholder="Search by title, company or contract number...">
                    </div>
                </div>
            </div>

            <!-- Contracts List (company-style row layout) -->
            <div class="contracts-container list-view" id="contractsList">
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
                <button class="btn-secondary" onclick="closeContractDetail()"><i class="fas fa-times"></i> Close</button>
            </div>
        </div>
    </div>

    <!-- Proof Review Modal -->
    <div class="cd-overlay" id="proofReviewOverlay" style="z-index: 1001;">
        <div class="cd-modal" style="max-width: 600px;">
            <div class="cd-header">
                <div class="cd-header-left">
                    <div class="cd-header-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="cd-header-text">
                        <h2>Review Phase</h2>
                        <span id="proofPhaseTitle"></span>
                    </div>
                </div>
                <button class="cd-close" onclick="closeProofModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="cd-body" id="proofReviewBody">
                <!-- Populated by JS -->
            </div>
            <div class="cd-footer">
                <button class="action-btn danger small" onclick="verifyMilestoneCurrent('reject')"><i class="fas fa-times"></i> Reject</button>
                <button class="action-btn success small" onclick="verifyMilestoneCurrent('approve')"><i class="fas fa-check"></i> Approve & Pay</button>
            </div>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/common/common.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/common/chat.js?v=6.8"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/shared/contract-preview.js?v=1.1"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/contracts.js?v=2.7"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/shared/budget-adjustment.js"></script>
    <script>
        // Customer-specific budget adjustment functions
        
        // Display pending budget adjustment alert
        async function displayPendingBudgetAdjustment(contractId) {
            try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/contracts.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'get_budget_adjustments',
                        contract_id: contractId
                    })
                });
                
                const result = await response.json();
                
                if (result.success && result.adjustments) {
                    const pending = result.adjustments.find(adj => adj.status === 'pending');
                    
                    if (pending) {
                        showBudgetAdjustmentAlert(pending, contractId);
                    }
                }
            } catch (error) {
                console.error('Error loading budget adjustments:', error);
            }
        }
        
        // Show budget adjustment alert in modal
        function showBudgetAdjustmentAlert(adjustment, contractId) {
            const alertHTML = `
                <div class="budget-adjustment-alert" style="
                    background: #fff3cd;
                    border: 2px solid #ffc107;
                    border-radius: 8px;
                    padding: 20px;
                    margin: 20px 0;
                ">
                    <h4 style="margin-top: 0; color: var(--warning-dark); display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-exclamation-triangle"></i> Budget Adjustment Request Pending
                    </h4>
                    
                    <table style="width: 100%; margin: 15px 0; border-collapse: collapse;">
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 10px; color: var(--text-muted);">Original Budget:</td>
                            <td style="padding: 10px; font-weight: bold;">LKR ${formatNumber(adjustment.original_amount)}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 10px; color: var(--text-muted);">Requested Budget:</td>
                            <td style="padding: 10px; font-weight: bold;">LKR ${formatNumber(adjustment.requested_amount)}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 10px; color: var(--text-muted);">Change:</td>
                            <td style="padding: 10px; font-weight: bold; color: ${adjustment.adjustment_amount > 0 ? 'var(--danger-color)' : 'var(--success-color)'};">
                                ${adjustment.adjustment_amount > 0 ? '+' : ''}LKR ${formatNumber(Math.abs(adjustment.adjustment_amount))}
                                (${adjustment.adjustment_percentage.toFixed(1)}%)
                            </td>
                        </tr>
                    </table>
                    
                    <div style="background: var(--bg-card); padding: 15px; border-radius: var(--border-radius-sm); margin: 15px 0; border: 1px solid var(--border-color);">
                        <h5 style="margin-top: 0; color: var(--text-primary);">Reason for Adjustment:</h5>
                        <p style="color: var(--text-muted); line-height: 1.6;">${escapeHtml(adjustment.reason)}</p>
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 20px; flex-wrap: wrap;">
                        <button onclick="rejectBudgetAdjustment(${adjustment.adjustment_id})" 
                                class="action-btn danger small" 
                                style="flex: 1; min-width: 150px;">
                            <i class="fas fa-times"></i> Reject Adjustment
                        </button>
                        <button onclick="approveBudgetAdjustment(${adjustment.adjustment_id})" 
                                class="action-btn success small" 
                                style="flex: 1; min-width: 150px;">
                            <i class="fas fa-check"></i> Approve Adjustment
                        </button>
                    </div>
                </div>
            `;
            
            // Insert alert at the top of contract detail body
            const detailBody = document.getElementById('contractDetailBody');
            if (detailBody) {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = alertHTML;
                detailBody.insertBefore(tempDiv.firstElementChild, detailBody.firstChild);
            }
        }
        
        // Helper functions
        function formatNumber(num) {
            return new Intl.NumberFormat('en-LK', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num);
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Extend the contract detail view to show budget adjustments
        const originalShowContractDetail = window.showContractDetail || function() {};
        
        window.showContractDetail = function(contractId) {
            // Call original function
            if (typeof originalShowContractDetail === 'function') {
                originalShowContractDetail(contractId);
            }
            
            // Load budget adjustment info
            setTimeout(() => {
                displayPendingBudgetAdjustment(contractId);
            }, 500);
        };
    </script>
</body>
</html>
