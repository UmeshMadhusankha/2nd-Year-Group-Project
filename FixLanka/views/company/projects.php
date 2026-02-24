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
require_once '../../config/session.php';
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
                                    <button class="action-btn primary" id="startProjectBtn" onclick="openProjectModal()">
                                        <i class="fas fa-plus"></i> Create New Project
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

    <!-- Create/Edit Project Modal -->
    <div class="modal-overlay" id="project-modal">
        <div class="modal-container edit-modal">
            <div class="modal-header">
                <h3><i class="fas fa-project-diagram"></i> <span id="modal-title">Create New Project</span></h3>
                <button class="close-modal" onclick="closeProjectModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="project-form">
                    <input type="hidden" id="project-id" name="project_id">
                    
                    <div class="form-group">
                        <label><i class="fas fa-heading"></i> Project Title *</label>
                        <input type="text" id="project-title" name="title" required placeholder="Enter project title">
                    </div>

                    <div class="form-group-row">
                        <div class="form-group">
                            <label><i class="fas fa-tag"></i> Project Type *</label>
                            <select id="project-type" name="project_type" required>
                                <option value="">Select type</option>
                                <option value="Plumbing">Plumbing</option>
                                <option value="Electrical">Electrical</option>
                                <option value="Carpentry">Carpentry</option>
                                <option value="Painting">Painting</option>
                                <option value="Renovation">Renovation</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt"></i> Location *</label>
                            <input type="text" id="project-location" name="location" required placeholder="Project location">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-dollar-sign"></i> Budget (LKR) *</label>
                        <input type="number" id="project-budget" name="budget" required min="0" step="0.01" placeholder="0.00">
                    </div>

                    <div class="form-group-row">
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i> Start Date *</label>
                            <input type="date" id="project-start-date" name="start_date" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-calendar-check"></i> End Date *</label>
                            <input type="date" id="project-end-date" name="end_date" required>
                        </div>
                    </div>

                    <div class="form-group-row">
                        <div class="form-group">
                            <label><i class="fas fa-tasks"></i> Status *</label>
                            <select id="project-status" name="status" required>
                                <option value="planned">Planned</option>
                                <option value="in_progress">In Progress</option>
                                <option value="on_hold">On Hold</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-percentage"></i> Progress</label>
                            <input type="number" id="project-progress" name="progress" min="0" max="100" value="0" placeholder="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-align-left"></i> Description</label>
                        <textarea id="project-description" name="description" rows="4" placeholder="Enter project description..."></textarea>
                    </div>

                    <div class="form-actions" style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                        <button type="button" class="btn-secondary" onclick="closeProjectModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Save Project
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
                <button class="drawer-tab" data-tab="customer">
                    <i class="fas fa-user"></i> Customer
                </button>
                <button class="drawer-tab" data-tab="timeline">
                    <i class="fas fa-clock"></i> Timeline
                </button>
                <button class="drawer-tab" data-tab="financial">
                    <i class="fas fa-money-bill-wave"></i> Financial
                </button>
            </div>

            <div class="drawer-content">
                <div class="drawer-tab-content active" id="tab-overview">
                    <!-- Content will be dynamically populated -->
                </div>
                <div class="drawer-tab-content" id="tab-customer">
                    <!-- Content will be dynamically populated -->
                </div>
                <div class="drawer-tab-content" id="tab-timeline">
                    <!-- Content will be dynamically populated -->
                </div>
                <div class="drawer-tab-content" id="tab-financial">
                    <!-- Content will be dynamically populated -->
                </div>
            </div>
        </div>
    </div>

    <!-- Load Sidebar JavaScript -->
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/sidebar.js"></script>
    
    <!-- Load Projects JavaScript -->
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/projects-db.js"></script>

    <script>
        // Additional project-specific scripts can go here
    </script>

</body>

</html>
