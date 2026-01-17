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

    <!-- Load Sidebar JavaScript -->
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/sidebar.js"></script>
    
    <!-- Load Projects JavaScript -->
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/projects-db.js"></script>

    <script>
        // Additional project-specific scripts can go here
    </script>

</body>

</html>
