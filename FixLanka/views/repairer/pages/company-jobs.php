<?php
require_once __DIR__ . '/../../../config/session.php';

// Ensure repairer is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /2nd-Year-Group-Project/FixLanka/views/auth/login.php');
    exit;
}

// Page configuration
$currentPage = 'company-jobs';
$pageTitle = 'Company Jobs';
$pageSubtitle = 'Side projects from companies - Browse, apply, and manage contracts';
$searchPlaceholder = 'Search company jobs...';

// Get repairer ID from session (falls back to 1 for dev purposes)
$currentRepairerId = $_SESSION['user_id'] ?? 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Jobs - FixLanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/global.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/repairer-pages.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/jobs.css">
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
        <div class="main-content-wrapper">
            <main class="main-content">
                <div class="content-wrapper">
                    <!-- Page Header -->
                    <section class="page-header-section">
                        <div class="page-header">
                            <div class="page-header-text">
                                <h1 class="page-header-title">Company Jobs</h1>
                                <p class="page-header-subtitle">Side projects from companies - Browse, apply, and manage your contracts</p>
                            </div>
                            <div class="page-header-actions">
                                <button class="btn-header btn-status" id="statusToggleBtn" onclick="toggleAvailabilityStatus()">
                                    <i class="fas fa-circle available-dot"></i> <span id="statusText">Available</span>
                                </button>
                                <button class="btn-header btn-secondary" onclick="refreshCurrentTab()">
                                    <i class="fas fa-sync-alt"></i> Refresh
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Main Tabs -->
                    <div class="tabs-container">
                        <div class="tabs">
                            <button class="tab-btn active" onclick="switchMainTab('browse')">
                                <i class="fas fa-search"></i> Browse Jobs
                            </button>
                            <button class="tab-btn" onclick="switchMainTab('applications')">
                                <i class="fas fa-file-alt"></i> My Applications
                                <span class="badge" id="applicationsBadge">0</span>
                            </button>
                            <button class="tab-btn" onclick="switchMainTab('contracts')">
                                <i class="fas fa-handshake"></i> Active Contracts
                                <span class="badge" id="contractsBadge">0</span>
                            </button>
                            <button class="tab-btn" onclick="switchMainTab('assignments')">
                                <i class="fas fa-clipboard-list"></i> Job Assignments
                                <span class="badge" id="assignmentsBadge">0</span>
                            </button>
                            <button class="tab-btn" onclick="switchMainTab('messages')">
                                <i class="fas fa-comments"></i> Messages
                                <span class="badge" id="messagesBadge">0</span>
                            </button>
                        </div>
                    </div>

                    <!-- TAB 1: Browse Job Postings -->
                    <div class="tab-content active" id="browseTab">
                        <!-- Filter & Search Section -->
                        <section class="filters-section">
                            <div class="filter-group">
                                <label for="categoryFilter" class="filter-label">
                                    <i class="fas fa-filter"></i> Filter by Category
                                </label>
                                <select id="categoryFilter" class="filter-select" onchange="filterJobsByCategory(this.value)">
                                    <option value="all">All Jobs</option>
                                    <option value="hvac">HVAC</option>
                                    <option value="electrical">Electrical</option>
                                    <option value="plumbing">Plumbing</option>
                                    <option value="carpentry">Carpentry</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="search-bar">
                                <i class="fas fa-search"></i>
                                <input type="text" id="jobSearchInput" placeholder="Search by job title, category, or location..." onkeyup="searchJobs()">
                            </div>
                        </section>

                        <!-- Job Postings Grid -->
                        <section class="jobs-section">
                            <div class="section-header">
                                <h2 class="section-title">Available Company Jobs</h2>
                                <span class="section-subtitle" id="jobCount">Loading jobs...</span>
                            </div>
                            <div class="jobs-grid" id="jobsGrid">
                                <!-- Jobs will be loaded here dynamically -->
                            </div>
                        </section>
                    </div>

                    <!-- TAB 2: My Applications -->
                    <div class="tab-content" id="applicationsTab">
                        <!-- Stats Overview -->
                        <section class="stats-section">
                            <div class="stats-grid">
                                <div class="stat-card pending">
                                    <div class="stat-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-number" id="pendingCount">—</span>
                                        <span class="stat-label">Pending Review</span>
                                    </div>
                                </div>
                                <div class="stat-card accepted">
                                    <div class="stat-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-number" id="acceptedCount">—</span>
                                        <span class="stat-label">Accepted</span>
                                    </div>
                                </div>
                                <div class="stat-card rejected">
                                    <div class="stat-icon">
                                        <i class="fas fa-times-circle"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-number" id="rejectedCount">—</span>
                                        <span class="stat-label">Rejected</span>
                                    </div>
                                </div>
                                <div class="stat-card total">
                                    <div class="stat-icon">
                                        <i class="fas fa-list"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-number" id="totalCount">—</span>
                                        <span class="stat-label">Total Applications</span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Filter Section -->
                        <section class="filters-section">
                            <div class="filter-chips">
                                <button class="filter-chip active" onclick="filterApplications('all', this)">
                                    <i class="fas fa-th"></i> All Applications
                                </button>
                                <button class="filter-chip" onclick="filterApplications('pending', this)">
                                    <i class="fas fa-clock"></i> Pending
                                </button>
                                <button class="filter-chip" onclick="filterApplications('accepted', this)">
                                    <i class="fas fa-check-circle"></i> Accepted
                                </button>
                                <button class="filter-chip" onclick="filterApplications('rejected', this)">
                                    <i class="fas fa-times-circle"></i> Rejected
                                </button>
                            </div>
                            <div class="search-bar">
                                <i class="fas fa-search"></i>
                                <input type="text" id="applicationSearchInput" placeholder="Search applications..." onkeyup="searchApplications()">
                            </div>
                        </section>

                        <!-- Applications List -->
                        <section class="applications-section">
                            <div class="section-header">
                                <h2 class="section-title">Your Applications</h2>
                                <span class="section-subtitle" id="appCount">Loading applications...</span>
                            </div>
                            <div class="applications-list" id="applicationsList">
                                <!-- Applications will be loaded here dynamically -->
                            </div>
                        </section>
                    </div>

                    <!-- TAB 3: Active Contracts -->
                    <div class="tab-content" id="contractsTab">
                        <!-- Stats Overview -->
                        <section class="stats-section">
                            <div class="stats-grid">
                                <div class="stat-card active">
                                    <div class="stat-icon">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-number" id="activeContractsCount">—</span>
                                        <span class="stat-label">Active Contracts</span>
                                    </div>
                                </div>
                                <div class="stat-card total">
                                    <div class="stat-icon">
                                        <i class="fas fa-coins"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-number" id="totalContractEarnings">—</span>
                                        <span class="stat-label">Total Earned</span>
                                    </div>
                                </div>
                                <div class="stat-card pending">
                                    <div class="stat-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-number" id="pendingPayments">—</span>
                                        <span class="stat-label">Pending Payments</span>
                                    </div>
                                </div>
                                <div class="stat-card info">
                                    <div class="stat-icon">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-number" id="completedJobsCount">—</span>
                                        <span class="stat-label">Completed Jobs</span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Contracts Grid -->
                        <section class="contracts-section">
                            <div class="section-header">
                                <h2 class="section-title">Active Contracts</h2>
                                <span class="section-subtitle" id="contractCount">Loading...</span>
                            </div>
                            <div class="contracts-grid" id="contractsGrid">
                                <!-- Contracts will be loaded here -->
                            </div>
                        </section>
                    </div>

                    <!-- TAB 4: Job Assignments -->
                    <div class="tab-content" id="assignmentsTab">
                        <!-- Stats Overview -->
                        <section class="stats-section">
                            <div class="stats-grid">
                                <div class="stat-card active">
                                    <div class="stat-icon">
                                        <i class="fas fa-tasks"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-number" id="activeAssignmentsCount">—</span>
                                        <span class="stat-label">Active Assignments</span>
                                    </div>
                                </div>
                                <div class="stat-card pending">
                                    <div class="stat-icon">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-number" id="pendingAssignments">—</span>
                                        <span class="stat-label">Pending Start</span>
                                    </div>
                                </div>
                                <div class="stat-card success">
                                    <div class="stat-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-number" id="completedAssignments">—</span>
                                        <span class="stat-label">Completed</span>
                                    </div>
                                </div>
                                <div class="stat-card info">
                                    <div class="stat-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-number" id="totalHours">—</span>
                                        <span class="stat-label">Total Hours</span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Assignments List -->
                        <section class="assignments-section">
                            <div class="section-header">
                                <h2 class="section-title">Job Assignments</h2>
                                <span class="section-subtitle" id="assignmentCount">Loading...</span>
                            </div>
                            <div class="assignments-list" id="assignmentsList">
                                <!-- Assignments will be loaded here -->
                            </div>
                        </section>
                    </div>

                    <!-- TAB 5: Messages -->
                    <div class="tab-content" id="messagesTab">
                        <!-- Messages Chat Layout (Two Column) -->
                        <section class="messages-chat-section">
                            <div class="messages-layout">
                                <!-- Left Side: Conversation List -->
                                <div class="conversations-sidebar">
                                    <div class="conversations-header">
                                        <h3>Messages</h3>
                                        <span class="unread-count" id="sidebarUnreadCount">0</span>
                                    </div>
                                    <div class="conversations-list" id="conversationsList">
                                        <!-- Conversation cards will be loaded here -->
                                    </div>
                                </div>

                                <!-- Right Side: Chat Area -->
                                <div class="chat-area" id="chatArea">
                                    <div class="chat-empty-state">
                                        <i class="fas fa-comments"></i>
                                        <h3>Select a conversation</h3>
                                        <p>Choose a company from the left to start messaging</p>
                                    </div>

                                    <!-- Active Chat (hidden by default) -->
                                    <div class="chat-active" id="chatActive" style="display: none;">
                                        <div class="chat-header-bar">
                                            <div class="chat-header-info">
                                                <div class="company-avatar-circle" id="activeChatAvatar"></div>
                                                <div>
                                                    <h3 id="activeChatCompany"></h3>
                                                    <p id="activeChatProject"></p>
                                                </div>
                                            </div>
                                            <button class="btn-icon" onclick="closeChatView()" title="Close chat">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>

                                        <div class="chat-messages-area" id="chatMessagesArea">
                                            <!-- Messages will be loaded here -->
                                        </div>

                                        <div class="chat-input-bar">
                                            <textarea 
                                                id="chatInput" 
                                                placeholder="Type your message..." 
                                                rows="1"
                                                onkeypress="handleChatKeyPress(event)"></textarea>
                                            <button class="btn btn-primary send-btn-circle" onclick="sendChatMessage()">
                                                <i class="fas fa-paper-plane"></i>
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
    </div>

    <!-- Job Details Drawer (from job-postings.php) -->
    <div class="drawer" id="jobDetailsDrawer">
        <div class="drawer-overlay" onclick="closeJobDetailsDrawer()"></div>
        <div class="drawer-content large">
            <div class="drawer-header">
                <h3><i class="fas fa-briefcase"></i> Job Details</h3>
                <button class="drawer-close" onclick="closeJobDetailsDrawer()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="drawer-body">
                <!-- Job Header -->
                <div class="job-detail-header">
                    <div class="company-info">
                        <div class="company-avatar" id="jobCompanyAvatar"></div>
                        <div class="company-details">
                            <h2 id="jobDetailTitle"></h2>
                            <p class="company-name" id="jobCompanyName"></p>
                            <div class="job-meta">
                                <span><i class="fas fa-calendar"></i> <span id="jobPostedDate"></span></span>
                                <span><i class="fas fa-users"></i> <span id="jobApplicationCount"></span></span>
                                <span><i class="fas fa-map-marker-alt"></i> <span id="jobLocation"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="job-badge-container">
                        <span class="job-badge active" id="jobStatusBadge">Active</span>
                    </div>
                </div>

                <!-- Job Information -->
                <div class="job-info-section">
                    <h4><i class="fas fa-info-circle"></i> Job Information</h4>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Category</span>
                            <span class="info-value" id="jobCategory"></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Employment Type</span>
                            <span class="info-value" id="jobEmploymentType"></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Budget Range</span>
                            <span class="info-value" id="jobBudget"></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Experience Required</span>
                            <span class="info-value" id="jobExperience"></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Priority</span>
                            <span class="info-value" id="jobPriority"></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Application Deadline</span>
                            <span class="info-value" id="jobDeadline"></span>
                        </div>
                    </div>
                </div>

                <!-- Job Description -->
                <div class="job-description-section">
                    <h4><i class="fas fa-align-left"></i> Job Description</h4>
                    <p id="jobDescription"></p>
                </div>

                <!-- Required Skills -->
                <div class="job-skills-section">
                    <h4><i class="fas fa-tools"></i> Required Skills</h4>
                    <div class="skills-tags" id="jobSkillsTags"></div>
                </div>

                <!-- Location Requirements -->
                <div class="job-location-section">
                    <h4><i class="fas fa-map-marker-alt"></i> Location Requirements</h4>
                    <p id="jobLocationRequirements"></p>
                </div>
            </div>
            <div class="drawer-footer">
                <button class="btn btn-secondary" onclick="closeJobDetailsDrawer()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button class="btn btn-primary" id="jobApplyBtn" onclick="openApplicationForm()">
                    <i class="fas fa-paper-plane"></i> Apply Now
                </button>
            </div>
        </div>
    </div>

    <!-- Application Form Drawer -->
    <div class="drawer" id="applicationFormDrawer">
        <div class="drawer-overlay" onclick="closeApplicationFormDrawer()"></div>
        <div class="drawer-content large">
            <div class="drawer-header">
                <h3><i class="fas fa-file-alt"></i> Submit Application</h3>
                <button class="drawer-close" onclick="closeApplicationFormDrawer()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="drawer-body">
                <form id="jobApplicationForm">
                    <div class="application-job-summary">
                        <h4>Applying for:</h4>
                        <p class="applying-job-title" id="applyingJobTitle"></p>
                        <p class="applying-company" id="applyingCompanyName"></p>
                    </div>

                    <div class="form-section">
                        <h4><i class="fas fa-money-bill-wave"></i> Rate & Availability</h4>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="proposedRate">Your Hourly Rate (LKR) <span class="required">*</span></label>
                                <input type="number" id="proposedRate" min="500" max="10000" placeholder="2500" required>
                            </div>
                            <div class="form-group">
                                <label for="availability">When can you start? <span class="required">*</span></label>
                                <select id="availability" required>
                                    <option value="">Select availability</option>
                                    <option value="immediate">Immediately</option>
                                    <option value="1-week">Within 1 week</option>
                                    <option value="2-weeks">Within 2 weeks</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4><i class="fas fa-file-alt"></i> Cover Letter</h4>
                        <div class="form-group">
                            <label for="coverLetter">Tell the company why you're the right fit <span class="required">*</span></label>
                            <textarea id="coverLetter" rows="6" placeholder="Describe your relevant experience..." required></textarea>
                            <div class="cover-letter-counter"><span id="coverLetterCount">0</span> characters</div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="drawer-footer">
                <button class="btn btn-secondary" onclick="closeApplicationFormDrawer()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button class="btn btn-primary" onclick="submitApplication()">
                    <i class="fas fa-paper-plane"></i> Submit Application
                </button>
            </div>
        </div>
    </div>

    <!-- Application Details Drawer -->
    <div class="drawer" id="applicationDetailsDrawer">
        <div class="drawer-overlay" onclick="closeApplicationDetailsDrawer()"></div>
        <div class="drawer-content large">
            <div class="drawer-header">
                <h3><i class="fas fa-file-alt"></i> Application Details</h3>
                <button class="drawer-close" onclick="closeApplicationDetailsDrawer()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="drawer-body">
                <div class="application-status-banner" id="appStatusBanner">
                    <div class="status-icon" id="appStatusIcon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="status-content">
                        <h4 id="appStatusTitle">Application Under Review</h4>
                        <p id="appStatusMessage">Your application is being reviewed by the company</p>
                    </div>
                </div>

                <div class="detail-section">
                    <h4><i class="fas fa-briefcase"></i> Job Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Job Title</span>
                            <span class="detail-value" id="appJobTitle"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Company</span>
                            <span class="detail-value" id="appCompanyName"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Category</span>
                            <span class="detail-value" id="appJobCategory"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Job Budget</span>
                            <span class="detail-value" id="appJobBudget"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Applied On</span>
                            <span class="detail-value" id="appAppliedDate"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Status</span>
                            <span class="detail-value">
                                <span class="status-badge" id="appStatusBadge">Pending</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <h4><i class="fas fa-money-bill-wave"></i> Your Proposal</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Proposed Rate</span>
                            <span class="detail-value" id="appProposedRate"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Availability</span>
                            <span class="detail-value" id="appAvailability"></span>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <h4><i class="fas fa-file-alt"></i> Cover Letter</h4>
                    <p id="appCoverLetter" style="color: var(--text-secondary); line-height: 1.6; background: var(--bg-secondary); padding: 16px; border-radius: 8px; border-left: 4px solid var(--primary-color);"></p>
                </div>

                <div class="detail-section">
                    <h4><i class="fas fa-history"></i> Application Timeline</h4>
                    <div id="appTimeline" class="timeline">
                        <!-- Timeline items will be loaded here dynamically -->
                    </div>
                </div>
            </div>
            <div class="drawer-footer">
                <button class="btn btn-secondary" onclick="closeApplicationDetailsDrawer()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button class="btn btn-danger" id="withdrawBtn" style="display: none;">
                    <i class="fas fa-ban"></i> Withdraw Application
                </button>
            </div>
        </div>
    </div>

    <!-- Contract Details Drawer -->
    <div class="drawer" id="contractDetailsDrawer">
        <div class="drawer-overlay" onclick="closeContractDetailsDrawer()"></div>
        <div class="drawer-content large">
            <div class="drawer-header">
                <h3><i class="fas fa-file-contract"></i> Contract Details</h3>
                <button class="drawer-close" onclick="closeContractDetailsDrawer()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="drawer-body">
                <div class="contract-header">
                    <div class="company-info">
                        <div class="company-avatar" id="contractCompanyAvatar"></div>
                        <div class="company-details">
                            <h3 id="contractCompanyName"></h3>
                            <p class="contract-role" id="contractRole"></p>
                        </div>
                    </div>
                    <div class="contract-status-badge">
                        <span class="status-badge active" id="contractStatus">Active</span>
                    </div>
                </div>

                <div class="detail-section">
                    <h4><i class="fas fa-info-circle"></i> Contract Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Contract Type</span>
                            <span class="detail-value" id="contractType"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Start Date</span>
                            <span class="detail-value" id="contractStartDate"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Hourly Rate</span>
                            <span class="detail-value" id="contractRate"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Total Assignments</span>
                            <span class="detail-value" id="totalAssignments"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Completed</span>
                            <span class="detail-value" id="completedAssignments"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Total Earnings</span>
                            <span class="detail-value" id="totalEarnings"></span>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <h4><i class="fas fa-address-book"></i> Contact Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Email</span>
                            <span class="detail-value" id="companyEmail"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Phone</span>
                            <span class="detail-value" id="companyPhone"></span>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <h4><i class="fas fa-clipboard-list"></i> Recent Assignments</h4>
                    <div id="contractAssignments" style="display: flex; flex-direction: column; gap: 12px;">
                        <!-- Assignments will be loaded here dynamically -->
                    </div>
                </div>
            </div>
            <div class="drawer-footer">
                <button class="btn btn-secondary" onclick="closeContractDetailsDrawer()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button class="btn btn-primary" onclick="sendMessageToCompany()">
                    <i class="fas fa-envelope"></i> Send Message
                </button>
            </div>
        </div>
    </div>

    <!-- Assignment Details Drawer -->
    <div class="drawer" id="assignmentDetailsDrawer">
        <div class="drawer-overlay" onclick="closeAssignmentDetailsDrawer()"></div>
        <div class="drawer-content large">
            <div class="drawer-header">
                <h3><i class="fas fa-clipboard-list"></i> Assignment Details</h3>
                <button class="drawer-close" onclick="closeAssignmentDetailsDrawer()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="drawer-body">
                <div class="assignment-header">
                    <div class="assignment-info">
                        <h3 id="assignmentTitle"></h3>
                        <p class="assignment-company" id="assignmentCompany"></p>
                    </div>
                    <div class="assignment-status-badge">
                        <span class="status-badge in-progress" id="assignmentStatus">In Progress</span>
                    </div>
                </div>

                <div class="detail-section">
                    <h4><i class="fas fa-info-circle"></i> Assignment Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Assignment Date</span>
                            <span class="detail-value" id="assignmentDate"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Time</span>
                            <span class="detail-value" id="assignmentTime"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Location</span>
                            <span class="detail-value" id="assignmentLocation"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Estimated Hours</span>
                            <span class="detail-value" id="estimatedHours"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Priority</span>
                            <span class="detail-value" id="assignmentPriority"></span>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <h4><i class="fas fa-align-left"></i> Description</h4>
                    <p id="assignmentDescription" style="color: var(--text-secondary); line-height: 1.6;"></p>
                </div>

                <div class="detail-section" id="assignmentUpdateSection">
                    <h4><i class="fas fa-tasks"></i> Update Status</h4>
                    <select id="jobStatus" class="form-select" onchange="updateAssignmentStatus()" style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 14px;">
                        <option value="assigned">Assigned</option>
                        <option value="in-progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <div class="detail-section" id="assignmentNotesSection">
                    <h4><i class="fas fa-sticky-note"></i> Progress Notes</h4>
                    <textarea id="progressNote" rows="4" placeholder="Add notes about progress..." style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 14px; resize: vertical;"></textarea>
                </div>
            </div>
            <div class="drawer-footer">
                <button class="btn btn-secondary" onclick="closeAssignmentDetailsDrawer()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button class="btn btn-primary" id="assignmentAcceptBtn" onclick="acceptCurrentOffer()" style="display:none;">
                    <i class="fas fa-check"></i> Accept Offer
                </button>
                <button class="btn btn-outline" id="assignmentDeclineBtn" onclick="declineCurrentOffer()" style="display:none;">
                    <i class="fas fa-times"></i> Decline Offer
                </button>
            </div>
        </div>
    </div>

    <!-- Message Thread Drawer (Chat Style) -->
    <div class="drawer" id="messageThreadDrawer">
        <div class="drawer-overlay" onclick="closeMessageThreadDrawer()"></div>
        <div class="drawer-content large">
            <div class="drawer-header">
                <div class="chat-header-info">
                    <div class="company-avatar-small" id="chatCompanyAvatar"></div>
                    <div class="chat-header-text">
                        <h3 id="chatCompanyName"></h3>
                        <p id="chatProjectName"></p>
                    </div>
                </div>
                <button class="drawer-close" onclick="closeMessageThreadDrawer()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="drawer-body chat-drawer-body">
                <div class="chat-messages-container" id="messagesContainer">
                    <!-- Messages will be loaded here dynamically -->
                </div>

                <div class="chat-input-container">
                    <textarea 
                        id="messageInput" 
                        placeholder="Type your message..." 
                        rows="2"
                        onkeypress="handleMessageKeyPress(event)"></textarea>
                    <button class="btn btn-primary send-btn" onclick="sendMessage()">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Inject repairer ID from PHP session into JS scope
        window.CURRENT_REPAIRER_ID = <?php echo (int)$currentRepairerId; ?>;
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/common/common.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/jobs.js"></script>
</body>
</html>