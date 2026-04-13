<?php
/**
 * Company Projects Page
 * 
 * This page allows companies to:
 * - View all their projects
 * - Create new projects
 * - Update project status and progress
 * - Delete projects
 * - View project statistics
 * 
 * Authentication: Requires logged-in company user
 * 
 * @package FixLanka\Views\Company
 * @version 1.0.0
 */

// Start session and verify authentication
require_once __DIR__ . '/../../config/session.php';
requireRole('company');

// Retrieve logged-in user data from session
$userData = getUserData();
$companyId = $userData['id'] ?? null;

// Ensure user is authenticated
if (!$companyId) {
    die('Error: Company not authenticated');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects - FixLanka Dashboard</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/common.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/modals.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/buttons.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/progress-bars.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/projects.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Pass company ID to JavaScript -->
    <script>
        window.CURRENT_COMPANY_ID = <?php echo json_encode($companyId); ?>;
    </script>
</head>

<body>
    <!-- Hidden checkbox for CSS toggle -->
    <input type="checkbox" id="sidebar-toggle">

    <div class="dashboard-container">
        <!-- Sidebar Component -->
        <!-- Sidebar Component -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header Component -->
            <!-- Header Component -->
            <?php include 'topbar.php'; ?>

            <div class="projects-container">
                <!-- Page Header -->
                <header class="page-header">
                    <div class="header-content">
                        <div class="header-main">
                            <div class="title-section">
                                <h1><i class="fas fa-project-diagram"></i> Projects</h1>
                                <p class="subtitle">Manage and track all your projects from start to completion</p>
                                <nav class="breadcrumbs">
                                    <a href="/2nd-Year-Group-Project/FixLanka/views/company/dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                                    <span class="separator">/</span>
                                    <span class="current">Projects</span>
                                </nav>
                            </div>
                            <div class="header-actions">
                                <div class="quick-stats">
                                    <div class="stat-item">
                                        <span class="stat-number" id="in-progress-projects">0</span>
                                        <span class="stat-label">In Progress</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number" id="completed-projects">0</span>
                                        <span class="stat-label">Completed</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number" id="total-projects">0</span>
                                        <span class="stat-label">Total</span>
                                    </div>
                                </div>
                                <div class="action-buttons">
                                    <button class="action-btn primary" id="startProjectBtn" onclick="openStartProjectModal()">
                                        <i class="fas fa-rocket"></i> Start a Project
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Filters & Controls -->
                <section class="controls-section">
                    <div class="filters-bar">
                        <div class="filter-group">
                            <select class="filter-select">
                                <option value="all">All Status</option>
                                <option value="planned">Planned</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="on_hold">On Hold</option>
                            </select>
                        </div>
                        <div class="view-controls">
                            <button class="view-toggle active" data-view="table" title="Table View">
                                <i class="fas fa-list"></i>
                            </button>
                            <button class="view-toggle" data-view="cards" title="Card View">
                                <i class="fas fa-th-large"></i>
                            </button>
                        </div>
                    </div>
                </section>

                <!-- Project List Section -->
                <section class="project-list-section">
                    <!-- Table View -->
                    <div id="table-view" class="table-view" style="display: block;">
                        <div class="table-container">
                            <table class="projects-table">
                                <thead>
                                    <tr>
                                        <th>Project Title</th>
                                        <th>Customer</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Budget</th>
                                        <th>Timeline</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="projects-table-body">
                                    <!-- Projects will be loaded dynamically -->
                                    <tr>
                                        <td colspan="8" class="loading">
                                            <i class="fas fa-spinner fa-spin"></i> Loading projects...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Card View -->
                    <div id="card-view" class="card-view" style="display: none;">
                        <div id="projects-card-container" class="projects-grid">
                            <!-- Project cards will be loaded dynamically -->
                            <div class="loading-card">
                                <i class="fas fa-spinner fa-spin"></i>
                                <p>Loading projects...</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Loader -->
    <div id="loader" class="loader" style="display: none;">
        <div class="loader-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Processing...</p>
        </div>
    </div>

    <!-- Start Project Modal -->
    <div class="modal-overlay" id="project-modal">
        <div class="modal-container edit-modal" style="max-width: 500px;">
            <div class="modal-header">
                <h3><i class="fas fa-rocket"></i> <span id="modal-title">Start a Project</span></h3>
                <button class="close-modal" onclick="closeProjectModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="project-form">
                    <p style="margin-bottom: 20px; color: var(--text-secondary); font-size: 0.95rem;">
                        Projects are automatically generated from contracts that have been <strong>accepted</strong> by the customer. Select a contract below to begin.
                    </p>
                    
                    <div class="form-group">
                        <label for="contract-selector"><i class="fas fa-file-contract"></i> Select Accepted Contract *</label>
                        <select id="contract-selector" name="contract_id" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color);">
                            <option value="">Loading available contracts...</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-top: 16px;">
                        <label><i class="fas fa-users"></i> Assign Company Employees (Optional)</label>
                        <p style="margin: 6px 0 10px; color: var(--text-secondary); font-size: 0.9rem;">
                            Select any employees you want to work on this project.
                        </p>
                        <div id="start-project-employees" style="border: 1px solid var(--border-color); border-radius: 8px; padding: 10px;">
                            <div style="color: var(--text-secondary); font-size: 0.9rem;"><i class="fas fa-spinner fa-spin"></i> Loading employees...</div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 16px;">
                        <label><i class="fas fa-user-tie"></i> Assign Hired Freelancers (Accepted Offers) (Optional)</label>
                        <p style="margin: 6px 0 10px; color: var(--text-secondary); font-size: 0.9rem;">
                            Select accepted freelancer offers to attach to this project.
                        </p>
                        <div id="start-project-freelancers" style="border: 1px solid var(--border-color); border-radius: 8px; padding: 10px; max-height: 180px; overflow: auto;">
                            <div style="color: var(--text-secondary); font-size: 0.9rem;">Select a contract to load freelancers.</div>
                        </div>
                    </div>

                    <div class="form-actions" style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                        <button type="button" class="btn-secondary" onclick="closeProjectModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn-primary" id="start-project-submit-btn">
                            <i class="fas fa-rocket"></i> Start Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Project Details Drawer -->
    <div class="drawer-overlay" id="project-details-drawer">
        <div class="drawer-panel">
            <div class="drawer-header">
                <h3><i class="fas fa-info-circle"></i> Project Details</h3>
                <button class="close-drawer" onclick="closeProjectDrawer()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="drawer-tabs">
                <button class="drawer-tab active" data-tab="overview">
                    <i class="fas fa-eye"></i> Overview
                </button>
                <button class="drawer-tab" data-tab="timeline">
                    <i class="fas fa-clock"></i> Timeline
                </button>
                <button class="drawer-tab" data-tab="financial">
                    <i class="fas fa-money-bill-wave"></i> Financial
                </button>
                <button class="drawer-tab" data-tab="chat">
                    <i class="fas fa-comments"></i> Chat
                </button>
            </div>

            <div class="drawer-content">
                <div class="drawer-tab-content active" id="tab-overview">
                    <!-- Content will be dynamically populated -->
                </div>
                <div class="drawer-tab-content" id="tab-timeline">
                    <!-- Content will be dynamically populated -->
                </div>
                <div class="drawer-tab-content" id="tab-financial">
                    <!-- Content will be dynamically populated -->
                </div>
                <div class="drawer-tab-content" id="tab-chat">
                    <!-- Chat UI will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Proof of Work Modal -->
    <div class="modal-overlay" id="proof-modal">
        <div class="modal-container edit-modal" style="max-width: 500px;">
            <div class="modal-header">
                <h3><i class="fas fa-file-upload"></i> <span id="proof-modal-title">Submit Proof of Work</span></h3>
                <button class="close-modal" onclick="closeProofModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="proof-form" enctype="multipart/form-data">
                    <input type="hidden" id="proof-milestone-id" name="milestone_id">

                    <div class="form-group" id="proof-unit-info" style="display:none;">
                        <label><i class="fas fa-ruler-combined"></i> Unit Billing</label>
                        <div style="display:flex; gap:10px; flex-wrap:wrap;">
                            <div style="flex:1; min-width:180px;">
                                <small style="display:block; color:#64748b;">Agreed rate</small>
                                <div id="proof-agreed-rate" style="font-weight:600;">-</div>
                            </div>
                            <div style="flex:1; min-width:180px;">
                                <small style="display:block; color:#64748b;">Unit</small>
                                <div id="proof-unit-label" style="font-weight:600;">-</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="proof-actual-qty-row" style="display:none;">
                        <label><i class="fas fa-hashtag"></i> Actual Units Completed <span class="required">*</span></label>
                        <input type="number" id="proof-actual-qty" name="actual_quantity" min="0" step="0.01" placeholder="e.g., 12.5" />
                        <small>Enter the actual completed units for this milestone.</small>
                    </div>

                    <div class="form-group" id="proof-actual-rate-row" style="display:none;">
                        <label><i class="fas fa-tag"></i> Actual Unit Rate (Material variation)</label>
                        <input type="number" id="proof-actual-rate" name="actual_unit_rate" min="0" step="0.01" placeholder="Leave blank if unchanged" />
                        <small>Only use if material prices changed from the agreed rate.</small>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-align-left"></i> Description *</label>
                        <textarea id="proof-description" name="description" rows="4" required placeholder="Describe the work completed..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-images"></i> Attach Evidence (Images/Docs) *</label>
                        <div class="file-upload-wrapper" style="border: 2px dashed #cbd5e1; padding: 20px; text-align: center; border-radius: 8px; cursor: pointer; position: relative;">
                            <input type="file" id="proof-files" name="proof_files[]" multiple required style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 24px; color: var(--primary-color); margin-bottom: 8px;"></i>
                            <p style="margin: 0; color: #64748b; font-size: 14px;">Click or drag files to upload</p>
                            <div id="file-list" style="margin-top: 10px; font-size: 12px; color: #334155; text-align: left;"></div>
                        </div>
                    </div>

                    <div class="form-actions" style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                        <button type="button" class="btn-secondary" onclick="closeProofModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-check-circle"></i> Submit for Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Load Sidebar JavaScript -->
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/common/common.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/sidebar.js"></script>
    
    <!-- Load Projects JavaScript -->
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/projects-db.js?v=<?= time() ?>"></script>
    
    <!-- Load Chat JavaScript -->
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/common/chat.js"></script>

    <script>
        // Additional project-specific scripts can go here
    </script>
</body>

</html>
