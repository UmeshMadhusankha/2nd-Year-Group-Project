<?php
/**
 * Company Workforce Page
 * 
 * Manage company employees and freelance contractors
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
    <title>Workforce - FixLanka Dashboard</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/dashboard.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/workforce.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/buttons.css">
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

            <!-- Workforce Container -->
            <div class="workforce-container">
                <!-- Page Header -->
                <header class="page-header">
                    <div class="header-content">
                        <div class="header-main">
                            <div class="title-section">
                                <h1><i class="fas fa-users-cog"></i> Workforce Management</h1>
                                <p class="subtitle">Manage your company employees and freelance contractors</p>
                                <nav class="breadcrumbs">
                                    <a href="/2nd-Year-Group-Project/FixLanka/company-dashboard"><i class="fas fa-home"></i> Dashboard</a>
                                    <span class="separator">/</span>
                                    <span class="current">Workforce</span>
                                </nav>
                            </div>
                            <div class="header-actions">
                                <div class="action-buttons">
                                    <button class="action-btn secondary">
                                        <i class="fas fa-file-export"></i> Export
                                    </button>
                                    <button class="action-btn primary" onclick="openJobPostingModal()">
                                        <i class="fas fa-bullhorn"></i> Create Job Posting
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Dashboard Preview Cards -->
                <div class="dashboard-grid">
                    <!-- Company Staff Card -->
                    <div class="preview-card staff-card" data-section="employees">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fas fa-building"></i>
                                <h3>Company Employees</h3>
                            </div>
                            <div class="card-badge staff-badge">Staff</div>
                        </div>
                        <div class="card-content">
                            <div class="stats-overview">
                                <div class="main-stat">
                                    <span class="stat-number">0</span>
                                    <span class="stat-label">Total Staff</span>
                                </div>
                                <div class="sub-stats">
                                    <div class="sub-stat">
                                        <span class="sub-number">0</span>
                                        <span class="sub-label">Active</span>
                                    </div>
                                    <div class="sub-stat">
                                        <span class="sub-number">0.0</span>
                                        <span class="sub-label">? Rating</span>
                                    </div>
                                </div>
                            </div>
                            <div class="specialties-preview">
                                <h4>Specialties Overview</h4>
                                <div class="specialty-items">
                                    <!-- Populated dynamically by company-employees-db.js -->
                                </div>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="action-btn primary" onclick="expandSection('employees')">
                                <i class="fas fa-users"></i> View All Staff
                            </button>
                        </div>
                    </div>

                    <!-- Freelancers Card -->
                    <div class="preview-card freelancers-card" data-section="freelancers">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fas fa-user-tie"></i>
                                <h3>Available Freelancers</h3>
                            </div>
                            <div class="card-badge freelancers-badge">Freelance</div>
                        </div>
                        <div class="card-content">
                            <div class="stats-overview">
                                <div class="main-stat">
                                    <span class="stat-number" id="freelancersAvailable">0</span>
                                    <span class="stat-label">Available</span>
                                </div>
                                <div class="sub-stats">
                                    <div class="sub-stat">
                                        <span class="sub-number" id="freelancersActive">0</span>
                                        <span class="sub-label">Active</span>
                                    </div>
                                    <div class="sub-stat">
                                        <span class="sub-number" id="freelancersFree">0</span>
                                        <span class="sub-label">Free</span>
                                    </div>
                                </div>
                            </div>
                            <div class="freelancers-preview">
                                <h4>Top Freelancers</h4>
                                <div class="freelancer-items" id="topFreelancersPreview">
                                    <p class="empty-preview-message">
                                        <i class="fas fa-info-circle"></i>
                                        No freelancers available yet
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="action-btn primary" onclick="expandSection('freelancers')">
                                <i class="fas fa-handshake"></i> View Freelancers
                            </button>
                        </div>
                    </div>

                    <!-- Applications Card -->
                    <div class="preview-card applications-card" data-section="applications">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fas fa-file-alt"></i>
                                <h3>Pending Applications</h3>
                            </div>
                            <div class="card-badge applications-badge">Applications</div>
                        </div>
                        <div class="card-content">
                            <div class="stats-overview">
                                <div class="main-stat">
                                    <span class="stat-number" id="applicationCount">0</span>
                                    <span class="stat-label">Pending</span>
                                </div>
                                <div class="sub-stats">
                                    <div class="sub-stat">
                                        <span class="sub-number" id="newTodayCount">0</span>
                                        <span class="sub-label">New Today</span>
                                    </div>
                                    <div class="sub-stat">
                                        <span class="sub-number" id="reviewedCount">0</span>
                                        <span class="sub-label">Reviewed</span>
                                    </div>
                                </div>
                            </div>
                            <div class="applications-preview">
                                <h4>Recent Applications</h4>
                                <div class="application-items" id="recentApplicationsList">
                                    <!-- Applications will be loaded dynamically -->
                                    <p style="text-align: center; color: #718096; padding: 20px;">No applications yet</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="action-btn primary" onclick="expandSection('applications')">
                                <i class="fas fa-clipboard-check"></i> Review Applications
                            </button>
                        </div>
                    </div>
                    <!-- Job Postings Card -->
                    <div class="preview-card job-postings-card" data-section="job-postings">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fas fa-bullhorn"></i>
                                <h3>Active Job Postings</h3>
                            </div>
                            <div class="card-badge job-postings-badge">Recruiting</div>
                        </div>
                        <div class="card-content">
                            <div class="stats-overview">
                                <div class="main-stat">
                                    <span class="stat-number" id="activeJobPostsCount">0</span>
                                    <span class="stat-label">Active Posts</span>
                                </div>
                                <div class="sub-stats">
                                    <div class="sub-stat">
                                        <span class="sub-number" id="draftPostsCount">0</span>
                                        <span class="sub-label">Drafts</span>
                                    </div>
                                    <div class="sub-stat">
                                        <span class="sub-number" id="totalJobApplicationsCount">0</span>
                                        <span class="sub-label">Applications</span>
                                    </div>
                                </div>
                            </div>
                            <div class="job-postings-preview">
                                <h4>Recent Postings</h4>
                                <div class="job-posting-items" id="recentJobPostingsList">
                                    <!-- Job postings will be loaded dynamically -->
                                    <p style="text-align: center; color: #718096; padding: 20px;">No job postings yet</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="action-btn primary" onclick="expandSection('job-postings')">
                                <i class="fas fa-eye"></i> Manage Postings
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Detailed Sections (Initially Hidden) -->
                <div class="detailed-sections" style="display: none;">
                    <!-- Back to Dashboard Button -->
                    <div class="section-header">
                        <div style="align-items: center;">
                            <button class="action-btn secondary" onclick="backToDashboard()">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                        </div>
                    </div>

                    <!-- Company Employees Summary (Category View) -->
                    <div class="workforce-section" id="employeesSection">
                        <div class="section-header">
                            <div>
                                <h2><i class="fas fa-id-badge"></i> Company Employees</h2>
                                <p class="section-subtitle">Overview of your permanent staff by specialty</p>
                            </div>
                            <div style="display: flex; gap: 12px;">
                                <button class="action-btn primary" onclick="openBulkStaffModal()" style="padding: var(--spacing-md);">
                                    <i class="fas fa-user-plus"></i> Add Staff
                                </button>
                                <button class="action-btn secondary" onclick="openReduceStaffModal()" style="padding: var(--spacing-md);">
                                    <i class="fas fa-user-minus"></i> Reduce Staff
                                </button>
                            </div>
                        </div>

                        <div class="employee-categories">
                            <!-- Populated dynamically by company-employees-db.js -->
                        </div>
                    </div>

                    <!-- Freelancers Section -->
                    <div class="workforce-section" id="freelancersSection">
                        <div class="section-header">
                            <h2><i class="fas fa-handshake"></i> Available Freelancers</h2>
                            <p class="section-subtitle">Independent contractors ready for assignments</p>
                        </div>

                        <!-- Freelancer Filters -->
                        <div class="freelancer-filter-tabs">
                            <button class="tab-btn active" onclick="applyFreelancerFilter('status', 'all', this)">
                                <i class="fas fa-list"></i> All Freelancers
                            </button>
                            <button class="tab-btn" onclick="applyFreelancerFilter('status', 'Available', this)">
                                <i class="fas fa-check-circle"></i> Available
                            </button>
                            <button class="tab-btn" onclick="applyFreelancerFilter('status', 'Busy', this)">
                                <i class="fas fa-clock"></i> Busy
                            </button>
                            <button class="tab-btn" onclick="applyFreelancerFilter('status', 'assigned', this)">
                                <i class="fas fa-briefcase"></i> Assigned
                            </button>
                        </div>

                        <div class="freelancer-list">
                            <!-- Freelancer cards will be populated here -->
                        </div>
                    </div>

                    <!-- Applications Section -->
                    <div class="workforce-section" id="applicationsSection">
                        <div class="section-header">
                            <div>
                                <h2><i class="fas fa-user-plus"></i> Pending Applications</h2>
                                <p class="section-subtitle">Review and manage incoming repairer applications</p>
                            </div>
                        </div>

                        <!-- Application Filter Tabs -->
                        <div class="application-tabs">
                            <button class="tab-btn active" onclick="filterApplications('all')">
                                <i class="fas fa-list"></i> All Applications
                            </button>
                            <button class="tab-btn" onclick="filterApplications('new')">
                                <i class="fas fa-star"></i> New
                            </button>
                            <button class="tab-btn" onclick="filterApplications('reviewed')">
                                <i class="fas fa-eye"></i> Reviewed
                            </button>
                            <button class="tab-btn" onclick="filterApplications('interview')">
                                <i class="fas fa-comments"></i> Interview
                            </button>
                        </div>

                        <div class="applications-list">
                            <!-- Application cards will be populated here -->
                        </div>
                    </div>

                    <!-- Job Postings Management Section -->
                    <div class="workforce-section" id="job-postingsSection">
                        <div class="section-header">
                            <div>
                                <h2><i class="fas fa-bullhorn"></i> Job Postings Management</h2>
                                <p class="section-subtitle">Manage your recruitment postings and track applications</p>
                            </div>
                            <div class="action-buttons">
                                <button class="action-btn primary" onclick="openJobPostingModal()" style="padding: var(--spacing-md);">
                                    <i class="fas fa-plus"></i> Create New Posting
                                </button>
                            </div>
                        </div>

                        <!-- Job Postings Filter Tabs -->
                        <div class="job-posting-tabs">
                            <button class="tab-btn active" onclick="filterJobPostings('all')">
                                <i class="fas fa-list"></i> All Posts
                            </button>
                            <button class="tab-btn" onclick="filterJobPostings('active')">
                                <i class="fas fa-eye"></i> Active
                            </button>
                            <button class="tab-btn" onclick="filterJobPostings('draft')">
                                <i class="fas fa-edit"></i> Drafts
                            </button>
                            <button class="tab-btn" onclick="filterJobPostings('expired')">
                                <i class="fas fa-clock"></i> Expired
                            </button>
                        </div>

                        <!-- Job Postings List -->
                        <div class="job-postings-list">
                            <!-- Job posting cards will be populated here -->
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div class="empty-state" id="emptyState" style="display: none;">
                        <i class="fas fa-users-slash"></i>
                        <h3>No Workforce Found</h3>
                        <p>No workforce members match your current filters.</p>
                    </div>
                </div>
        </main>
    </div>



    <!-- Scripts -->
    <script>
        // Mock data arrays removed - kept as empty to prevent undefined errors
        const freelancersData = [];
        
        // Ensure company ID is globally available
        var currentCompanyId = window.CURRENT_COMPANY_ID;
        window.currentCompanyId = currentCompanyId;
        
        document.addEventListener('DOMContentLoaded', function () {
             setTimeout(() => {
                initializePage();
            }, 500);
        });

        function initializePage() {
            // Preload data if available
            if (window.freelancersDb) window.freelancersDb.load();
            if (window.applicationsDb) window.applicationsDb.load();
        }

        // Global variables
        let currentTab = 'employees';
        let selectedEmployee = null;
        let isDashboardView = true;

        // Dashboard Functions
        function expandSection(section) {
            // Hide dashboard grid
            document.querySelector('.dashboard-grid').style.display = 'none';

            // Show detailed sections container
            document.querySelector('.detailed-sections').style.display = 'block';

            // Hide all detailed sections first
            document.querySelectorAll('.workforce-section').forEach(sec => {
                sec.style.display = 'none';
            });

            // Show the selected section
            const sectionId = section + 'Section';
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.style.display = 'block';
                
                // Load data for specific sections
                if (section === 'job-postings') {
                    // loadJobPostings();
                } else if (section === 'freelancers') {
                    if (window.freelancersDb) window.freelancersDb.load();
                } else if (section === 'applications') {
                    if (window.applicationsDb) window.applicationsDb.load();
                } else if (section === 'employees') {
                    // loadEmployeeCategories();
                }
            }

            isDashboardView = false;
            currentTab = section;

            // Update search functionality for the current section
            initializeSearch();
        }

        function backToDashboard() {
            // Hide detailed sections
            document.querySelector('.detailed-sections').style.display = 'none';
            document.querySelectorAll('.workforce-section').forEach(sec => {
                sec.style.display = 'none';
            });

            // Show dashboard grid
            document.querySelector('.dashboard-grid').style.display = 'grid';

            isDashboardView = true;

            // Clear search when returning to dashboard
            const searchInput = document.getElementById('workforceSearch');
            if (searchInput) {
                searchInput.value = '';
            }
        }

        // Utility functions
        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffTime = Math.abs(now - date);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays === 1) return '1 day ago';
            if (diffDays < 7) return `${diffDays} days ago`;
            if (diffDays < 30) return `${Math.ceil(diffDays / 7)} weeks ago`;
            return date.toLocaleDateString();
        }

        // Action functions
        let currentFreelancerId = null;

        function assignJob(freelancerId) {
            currentFreelancerId = freelancerId;
            // TODO: Fetch freelancer from API instead of mock data
            console.warn('assignJob: Mock data removed - implement API call to fetch freelancer details');
            alert('This feature requires API integration. Mock data has been removed.');
            return;

            /* Original mock code - to be replaced with API call
            const freelancer = freelancersData.find(f => f.id === freelancerId);
            if (!freelancer) return;

            // Populate freelancer info in assignment drawer
            document.getElementById('assignFreelancerAvatar').textContent = freelancer.avatar;
            document.getElementById('assignFreelancerName').textContent = `${freelancer.firstName} ${freelancer.lastName}`;
            document.getElementById('assignFreelancerSpecialty').textContent = freelancer.specialty;
            document.getElementById('agreedRate').value = freelancer.hourlyRate;

            // Set default start date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('assignmentStartDate').value = today;

            // Close freelancer details drawer if open
            document.getElementById('freelancerDetailsDrawer').classList.remove('active');
            
            // Open assignment drawer
            document.getElementById('assignJobDrawer').classList.add('active');
            updateCostSummary();
            */
        }

        function viewFreelancerDetails(freelancerId) {
            currentFreelancerId = freelancerId;
            // TODO: Fetch freelancer from API instead of mock data
            console.warn('viewFreelancerDetails: Mock data removed - implement API call to fetch freelancer details');
            alert('This feature requires API integration. Mock data has been removed.');
            return;

            /* Original mock code - to be replaced with API call
            const freelancer = freelancersData.find(f => f.id === freelancerId);
            if (!freelancer) return;

            // Populate drawer with freelancer data
            document.getElementById('freelancerAvatar').textContent = freelancer.avatar;
            document.getElementById('freelancerFullName').textContent = `${freelancer.firstName} ${freelancer.lastName}`;
            document.getElementById('freelancerSpecialty').textContent = freelancer.specialty;
            document.getElementById('freelancerRating').textContent = freelancer.rating;
            document.getElementById('freelancerCompletedJobs').textContent = freelancer.completedJobs || 0;
            document.getElementById('freelancerResponseTime').textContent = freelancer.responseTime || '< 1 hour';
            document.getElementById('freelancerEmail').textContent = freelancer.email;
            document.getElementById('freelancerPhone').textContent = freelancer.phone;
            document.getElementById('freelancerExperience').textContent = `${freelancer.experience} years`;
            document.getElementById('freelancerHourlyRate').textContent = `LKR ${freelancer.hourlyRate.toLocaleString()}/hr`;

            // Update availability status
            const statusContainer = document.getElementById('freelancerAvailabilityStatus');
            let statusClass = 'available';
            let statusText = 'AVAILABLE';
            
            if (freelancer.status.toLowerCase() === 'busy') {
                statusClass = 'busy';
                statusText = 'BUSY';
            } else if (freelancer.status.toLowerCase() === 'unavailable') {
                statusClass = 'unavailable';
                statusText = 'UNAVAILABLE';
            }

            statusContainer.innerHTML = `<span class="status-badge ${statusClass}">${statusText}</span>`;

            // Show/hide assign button based on availability
            const assignJobBtn = document.querySelector('#freelancerDetailsDrawer .detail-actions .action-btn.primary');
            const isAvailable = freelancer.status.toLowerCase() === 'available';
            
            if (assignJobBtn) {
                if (isAvailable) {
                    assignJobBtn.style.display = 'flex';
                    assignJobBtn.disabled = false;
                } else {
                    assignJobBtn.style.display = 'none';
                }
            }

            // Open drawer
            document.getElementById('freelancerDetailsDrawer').classList.add('active');
            */
        }

        function closeFreelancerDetailsDrawer() {
            document.getElementById('freelancerDetailsDrawer').classList.remove('active');
        }

        function openAssignJobDrawer() {
            if (currentFreelancerId) {
                assignJob(currentFreelancerId);
            }
        }

        function closeAssignJobDrawer() {
            document.getElementById('assignJobDrawer').classList.remove('active');
            document.getElementById('assignJobForm').reset();
            updateCostSummary();
        }

        // Chat Data Storage
        const chatMessagesData = {};

        function contactFreelancer() {
            // TODO: Fetch freelancer from API instead of mock data
            console.warn('contactFreelancer: Mock data removed - implement API call to fetch freelancer details');
            alert('This feature requires API integration. Mock data has been removed.');
            return;

            /* Original mock code - to be replaced with API call
            const freelancer = freelancersData.find(f => f.id === currentFreelancerId);
            if (!freelancer) return;
            
            // Populate chat drawer with freelancer info
            document.getElementById('chatPersonName').textContent = freelancer.name;
            document.getElementById('chatPersonAvatar').textContent = freelancer.avatar;
            document.getElementById('chatPersonSpecialty').textContent = freelancer.specialty;
            updateOnlineStatus(freelancer);
            
            // Store current freelancer ID for chat
            window.currentChatFreelancerId = currentFreelancerId;
            
            // Clear previous messages and load chat history (dummy data for now)
            loadChatMessages(currentFreelancerId);
            
            // Open chat drawer
            document.getElementById('chatDrawer').classList.add('active');
            */
        }

        function openChatWithFreelancer(freelancerId) {
            // TODO: Fetch freelancer from API instead of mock data
            console.warn('openChatWithFreelancer: Mock data removed - implement API call');
            alert('This feature requires API integration. Mock data has been removed.');
            return;

            /* Original mock code - to be replaced with API call
            const freelancer = freelancersData.find(f => f.id === freelancerId);
            if (!freelancer) return;
            
            // Populate chat drawer with freelancer info
            document.getElementById('chatPersonName').textContent = `${freelancer.firstName} ${freelancer.lastName}`;
            document.getElementById('chatPersonAvatar').textContent = freelancer.avatar;
            document.getElementById('chatPersonSpecialty').textContent = freelancer.specialty;
            updateOnlineStatus(freelancer);
            
            // Store current freelancer ID for chat
            window.currentChatFreelancerId = freelancerId;
            
            // Clear previous messages and load chat history
            loadChatMessages(freelancerId);
            
            // Open chat drawer
            document.getElementById('chatDrawer').classList.add('active');
            */
        }

        function closeChatDrawer() {
            document.getElementById('chatDrawer').classList.remove('active');
            const input = document.getElementById('chatMessageInput');
            input.value = '';
            input.style.height = 'auto';
        }

        function updateOnlineStatus(freelancer) {
            const statusElement = document.getElementById('chatOnlineStatus');
            const isOnline = freelancer.status === 'Available';
            
            if (isOnline) {
                statusElement.innerHTML = '<i class="fas fa-circle"></i> Online';
                statusElement.className = 'chat-online-status online';
            } else {
                statusElement.innerHTML = '<i class="fas fa-circle"></i> Offline';
                statusElement.className = 'chat-online-status offline';
            }
        }

        function loadChatMessages(freelancerId) {
            const chatContainer = document.getElementById('chatMessagesContainer');
            
            // Initialize chat messages for this freelancer if not exists
            if (!chatMessagesData[freelancerId]) {
                chatMessagesData[freelancerId] = [
                    {
                        id: 1,
                        sender: 'freelancer',
                        name: 'Kasun Perera',
                        avatar: 'KP',
                        message: 'Hello! I received the job assignment notification. When should I start?',
                        time: '10:30 AM',
                        date: 'Today',
                        timestamp: new Date().getTime()
                    },
                    {
                        id: 2,
                        sender: 'company',
                        name: 'FixLanka Team',
                        avatar: 'FL',
                        message: 'Great! You can start tomorrow morning. The customer will be available from 9 AM.',
                        time: '10:45 AM',
                        date: 'Today',
                        timestamp: new Date().getTime()
                    },
                    {
                        id: 3,
                        sender: 'freelancer',
                        name: 'Kasun Perera',
                        avatar: 'KP',
                        message: 'Perfect! Do I need to bring any specific tools or parts?',
                        time: '11:00 AM',
                        date: 'Today',
                        timestamp: new Date().getTime()
                    },
                    {
                        id: 4,
                        sender: 'company',
                        name: 'FixLanka Team',
                        avatar: 'FL',
                        message: 'Yes, please bring your standard mobile repair toolkit. The replacement screen will be provided by the customer.',
                        time: '11:15 AM',
                        date: 'Today',
                        timestamp: new Date().getTime()
                    }
                ];
            }
            
            const chatHistory = chatMessagesData[freelancerId];
            chatContainer.innerHTML = '';
            
            let currentDate = '';
            
            chatHistory.forEach((msg, index) => {
                // Add date separator if date changes
                if (msg.date !== currentDate) {
                    currentDate = msg.date;
                    const dateSeparator = document.createElement('div');
                    dateSeparator.className = 'chat-date-separator';
                    dateSeparator.innerHTML = `<span>${msg.date}</span>`;
                    chatContainer.appendChild(dateSeparator);
                }
                
                const messageDiv = document.createElement('div');
                messageDiv.className = `chat-message ${msg.sender}`;
                messageDiv.setAttribute('data-message-id', msg.id);
                
                // Check if message has attachment
                const attachmentHTML = msg.attachment ? `
                    <div class="chat-attachment">
                        <i class="fas fa-${getFileIcon(msg.attachment.type)}"></i>
                        <span>${msg.attachment.name}</span>
                        <button class="action-btn-sm info" onclick="downloadAttachment('${msg.attachment.url}')">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                ` : '';
                
                messageDiv.innerHTML = `
                    <div class="chat-message-avatar">${msg.avatar}</div>
                    <div class="chat-message-content">
                        <div class="chat-message-header">
                            <span class="chat-sender">${msg.name}</span>
                            <span class="chat-time">${msg.time}</span>
                        </div>
                        <div class="chat-message-text">${msg.message}</div>
                        ${attachmentHTML}
                        ${msg.sender === 'company' ? `
                            <div class="message-status">
                                <i class="fas fa-check-double ${msg.read ? 'read' : ''}"></i>
                            </div>
                        ` : ''}
                    </div>
                `;
                chatContainer.appendChild(messageDiv);
            });
            
            // Scroll to bottom smoothly
            setTimeout(() => {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }, 100);
        }

        function sendChatMessage() {
            const input = document.getElementById('chatMessageInput');
            const message = input.value.trim();
            
            if (!message) return;
            
            const now = new Date();
            const hours = now.getHours();
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const displayHours = hours % 12 || 12;
            const timeStr = `${displayHours}:${minutes} ${ampm}`;
            
            const newMessage = {
                id: Date.now(),
                sender: 'company',
                name: 'FixLanka Team',
                avatar: 'FL',
                message: message,
                time: timeStr,
                date: 'Today',
                timestamp: now.getTime(),
                read: false
            };
            
            // Add to chat data
            if (!chatMessagesData[window.currentChatFreelancerId]) {
                chatMessagesData[window.currentChatFreelancerId] = [];
            }
            chatMessagesData[window.currentChatFreelancerId].push(newMessage);
            
            // Add to UI
            const chatContainer = document.getElementById('chatMessagesContainer');
            const messageDiv = document.createElement('div');
            messageDiv.className = 'chat-message company';
            messageDiv.setAttribute('data-message-id', newMessage.id);
            
            messageDiv.innerHTML = `
                <div class="chat-message-avatar">FL</div>
                <div class="chat-message-content">
                    <div class="chat-message-header">
                        <span class="chat-sender">FixLanka Team</span>
                        <span class="chat-time">${timeStr}</span>
                    </div>
                    <div class="chat-message-text">${message}</div>
                    <div class="message-status">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            `;
            
            chatContainer.appendChild(messageDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;
            
            input.value = '';
            input.style.height = 'auto';
            
            // Simulate message sent status
            setTimeout(() => {
                const statusIcon = messageDiv.querySelector('.message-status i');
                statusIcon.className = 'fas fa-check-double';
            }, 1000);
            
            // Simulate message read status
            setTimeout(() => {
                const statusIcon = messageDiv.querySelector('.message-status i');
                statusIcon.classList.add('read');
                newMessage.read = true;
            }, 3000);
            
            // Show typing indicator briefly (simulate response)
            setTimeout(() => {
                showTypingIndicator();
                setTimeout(() => {
                    hideTypingIndicator();
                    simulateFreelancerResponse();
                }, 2000);
            }, 1000);
        }

        function simulateFreelancerResponse() {
            const responses = [
                "Thank you for the information!",
                "Got it, I'll make sure to handle that.",
                "Understood. I'll get started on it right away.",
                "Perfect! I appreciate the update.",
                "Thanks! I'll keep you posted on the progress."
            ];
            
            const randomResponse = responses[Math.floor(Math.random() * responses.length)];
            const now = new Date();
            const hours = now.getHours();
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const displayHours = hours % 12 || 12;
            const timeStr = `${displayHours}:${minutes} ${ampm}`;
            
            const freelancer = freelancersData.find(f => f.id === window.currentChatFreelancerId);
            if (!freelancer) return;
            
            const newMessage = {
                id: Date.now(),
                sender: 'freelancer',
                name: `${freelancer.firstName} ${freelancer.lastName}`,
                avatar: freelancer.avatar,
                message: randomResponse,
                time: timeStr,
                date: 'Today',
                timestamp: now.getTime()
            };
            
            chatMessagesData[window.currentChatFreelancerId].push(newMessage);
            
            const chatContainer = document.getElementById('chatMessagesContainer');
            const messageDiv = document.createElement('div');
            messageDiv.className = 'chat-message freelancer';
            messageDiv.setAttribute('data-message-id', newMessage.id);
            
            messageDiv.innerHTML = `
                <div class="chat-message-avatar">${freelancer.avatar}</div>
                <div class="chat-message-content">
                    <div class="chat-message-header">
                        <span class="chat-sender">${freelancer.firstName} ${freelancer.lastName}</span>
                        <span class="chat-time">${timeStr}</span>
                    </div>
                    <div class="chat-message-text">${randomResponse}</div>
                </div>
            `;
            
            chatContainer.appendChild(messageDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function showTypingIndicator() {
            const indicator = document.getElementById('chatTypingIndicator');
            indicator.style.display = 'flex';
        }

        function hideTypingIndicator() {
            const indicator = document.getElementById('chatTypingIndicator');
            indicator.style.display = 'none';
        }

        function attachChatFile() {
            document.getElementById('chatFileInput').click();
        }

        function autoResizeChatInput(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
        }

        function getFileIcon(fileType) {
            const iconMap = {
                'pdf': 'file-pdf',
                'doc': 'file-word',
                'docx': 'file-word',
                'image': 'file-image',
                'video': 'file-video'
            };
            return iconMap[fileType] || 'file';
        }

        function downloadAttachment(url) {
            window.open(url, '_blank');
        }

        // Allow Enter key to send message, Shift+Enter for new line
        function handleChatKeyPress(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendChatMessage();
            }
        }

        // Handle file input change
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('chatFileInput');
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        // Simulate file upload
                        showNotification('File attachment feature coming soon!', 'info');
                        // TODO: Implement actual file upload
                        fileInput.value = '';
                    }
                });
            }
        });

        function approveApplication(applicationId) {
            if (confirm('Are you sure you want to accept this application?')) {
                alert(`Application ${applicationId} approved`);
                // Implementation for approving application
            }
        }

        function rejectApplication(applicationId) {
            if (confirm('Are you sure you want to decline this application?')) {
                alert(`Application ${applicationId} rejected`);
                // Implementation for rejecting application
            }
        }

        function updateStats() {
            // TODO: Update stats from real API/database data
            // Mock data has been removed - implement API calls here
            console.log('Update stats from database');
        }

        // Initialize search functionality
        function initializeSearch() {
            const searchInput = document.getElementById('workforceSearch') || document.getElementById('searchInput');
            if (!searchInput) return;
            if (searchInput.dataset.searchBound === '1') return;
            searchInput.dataset.searchBound = '1';

            searchInput.addEventListener('input', (e) => {
                const query = (e.target.value || '').toLowerCase();
                filterContent(query);
            });
        }

        // Filter content based on search query
        function filterContent(query) {
            // Filter freelancers
            const freelancerItems = document.querySelectorAll('.freelancer-item');
            freelancerItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(query) ? 'flex' : 'none';
            });

            // Filter applications
            const applicationItems = document.querySelectorAll('.application-item');
            applicationItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(query) ? 'flex' : 'none';
            });

            // Filter employee categories
            const categoryCards = document.querySelectorAll('.category-card');
            categoryCards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(query) ? 'block' : 'none';
            });
        }

        // ================================================
        // FREELANCER FILTERING SYSTEM
        // ================================================
        
        function filterFreelancers(status = 'all') {
            const freelancerCards = document.querySelectorAll('.freelancer-card');
            let visibleCount = 0;

            // Update active tab
            document.querySelectorAll('.freelancer-filter-tabs .tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');

            freelancerCards.forEach(card => {
                let shouldShow = true;

                // Status-based filtering
                const cardStatus = card.dataset.status.toLowerCase();
                
                if (status === 'all') {
                    shouldShow = true;
                } else if (status === 'available') {
                    shouldShow = cardStatus === 'available';
                } else if (status === 'busy') {
                    shouldShow = cardStatus === 'busy';
                } else if (status === 'assigned') {
                    // Show freelancers with current assignments
                    const hasAssignment = card.querySelector('.freelancer-assignment') !== null;
                    shouldShow = hasAssignment;
                }

                card.style.display = shouldShow ? 'block' : 'none';
                if (shouldShow) visibleCount++;
            });

            // Update empty state
            const freelancerList = document.querySelector('.freelancer-list');
            let emptyState = freelancerList.querySelector('.empty-state');
            
            if (visibleCount === 0) {
                if (!emptyState) {
                    emptyState = document.createElement('div');
                    emptyState.className = 'empty-state';
                    emptyState.style.gridColumn = '1 / -1';
                    emptyState.style.textAlign = 'center';
                    emptyState.style.padding = '60px 20px';
                    emptyState.innerHTML = `
                        <i class="fas fa-filter" style="font-size: 48px; color: #d1d5db; margin-bottom: 15px;"></i>
                        <h3 style="color: #6b7280; margin-bottom: 8px;">No freelancers found</h3>
                        <p style="color: #9ca3af;">No ${status === 'all' ? '' : status} freelancers at the moment</p>
                    `;
                    freelancerList.appendChild(emptyState);
                }
            } else if (emptyState) {
                emptyState.remove();
            }
        }

        // Initialize page when DOM is loaded
        document.addEventListener('DOMContentLoaded', initializePage);

        // Application modal functions
        let currentApplicationId = null;

        function viewApplicationDetails(applicationId) {
            const application = applicationsData.find(app => app.id === applicationId);
            if (!application) return;

            currentApplicationId = applicationId;

            // Populate drawer with application data
            document.getElementById('modalFullName').textContent = `${application.firstName} ${application.lastName}`;
            document.getElementById('modalEmail').textContent = application.email;
            document.getElementById('modalPhone').textContent = application.phone;
            document.getElementById('modalApplicationDate').textContent = formatDate(application.applicationDate);
            document.getElementById('modalSpecialty').textContent = application.specialty;
            document.getElementById('modalExperience').textContent = `${application.experience} years`;
            document.getElementById('modalExpectedRate').textContent = `LKR ${application.expectedRate.toLocaleString()}/hr`;

            // Update cover letter
            document.getElementById('modalCoverLetter').textContent = application.coverLetter || 'No cover letter provided.';

            // Update skills
            const skillsContainer = document.getElementById('modalSkills');
            skillsContainer.innerHTML = '';
            const normalizedSkills = Array.isArray(application.skills)
                ? application.skills
                : String(application.skills || '')
                    .split(',')
                    .map(s => s.trim())
                    .filter(Boolean);

            if (normalizedSkills.length > 0) {
                normalizedSkills.forEach(skill => {
                    const skillTag = document.createElement('span');
                    skillTag.className = 'skill-tag';
                    skillTag.textContent = skill;
                    skillsContainer.appendChild(skillTag);
                });
            } else {
                skillsContainer.innerHTML = '<span class="skill-tag">No specific skills listed</span>';
            }

            // Show drawer
            document.getElementById('applicationDetailsDrawer').classList.add('active');
        }

        function closeApplicationDetailsDrawer() {
            document.getElementById('applicationDetailsDrawer').classList.remove('active');
            currentApplicationId = null;
        }

        function approveApplicationFromDrawer() {
            if (currentApplicationId) {
                approveApplication(currentApplicationId);
                closeApplicationDetailsDrawer();
            }
        }

        function rejectApplicationFromDrawer() {
            if (currentApplicationId) {
                rejectApplication(currentApplicationId);
                closeApplicationDetailsDrawer();
            }
        }

        // Initialize drawer tab functionality
        document.addEventListener('DOMContentLoaded', function () {
            // Drawer tab switching
            const drawerTabs = document.querySelectorAll('.drawer-tab');
            const drawerTabContents = document.querySelectorAll('.drawer-tab-content');

            drawerTabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const targetTab = tab.getAttribute('data-tab');

                    // Remove active class from all tabs and contents
                    drawerTabs.forEach(t => t.classList.remove('active'));
                    drawerTabContents.forEach(content => content.classList.remove('active'));

                    // Add active class to clicked tab and corresponding content
                    tab.classList.add('active');
                    document.getElementById(targetTab).classList.add('active');
                });
            });

            // Close drawer when clicking outside
            const drawerOverlays = document.querySelectorAll('.drawer-overlay');
            drawerOverlays.forEach(overlay => {
                overlay.addEventListener('click', (e) => {
                    if (e.target === overlay) {
                        overlay.classList.remove('active');
                    }
                });
            });
        });

        // Load dashboard preview data
        async function loadDashboardPreviews() {
            const companyId = <?php echo $_SESSION['user_id'] ?? 0; ?>;
            if (companyId === 0) return;

            try {
                // Load applications preview
                const appResponse = await fetch(`/2nd-Year-Group-Project/FixLanka/api/repairer-applications.php?action=list&company_id=${companyId}`);
                if (appResponse.ok) {
                    const appData = await appResponse.json();
                    if (appData && appData.success) {
                        updateApplicationsPreview(appData.applications || []);
                    } else {
                        updateApplicationsPreview([]);
                    }
                }

                // Load job postings preview
                const jobResponse = await fetch(`/2nd-Year-Group-Project/FixLanka/api/job-postings.php?action=list&company_id=${companyId}`);
                if (jobResponse.ok) {
                    const jobData = await jobResponse.json();
                    if (jobData.success && jobData.postings) {
                        updateJobPostingsPreview(jobData.postings);
                    }
                }
            } catch (error) {
                console.error('Error loading dashboard previews:', error);
            }
        }

        // Update applications preview in dashboard
        function updateApplicationsPreview(applications) {
            // Update counts
            const total = Array.isArray(applications) ? applications.length : 0;
            const today = new Date().toDateString();
            const newToday = (Array.isArray(applications) ? applications : []).filter(app => new Date(app.applied_date).toDateString() === today).length;
            const reviewed = (Array.isArray(applications) ? applications : []).filter(app => app.status === 'reviewed').length;

            document.getElementById('applicationCount').textContent = total;
            document.getElementById('newTodayCount').textContent = newToday;
            document.getElementById('reviewedCount').textContent = reviewed;

            // Update recent applications list
            const listContainer = document.getElementById('recentApplicationsList');
            if (applications.length === 0) {
                listContainer.innerHTML = '<p style="text-align: center; color: #718096; padding: 20px;">No applications yet</p>';
                return;
            }

            // Sort by date and get latest 3
            const recentApps = applications
                .sort((a, b) => new Date(b.applied_date) - new Date(a.applied_date))
                .slice(0, 3);

            listContainer.innerHTML = recentApps.map(app => {
                const daysAgo = Math.floor((new Date() - new Date(app.applied_date)) / (1000 * 60 * 60 * 24));
                const timeText = daysAgo === 0 ? 'Today' : daysAgo === 1 ? '1 day ago' : `${daysAgo} days ago`;
                const initials = (app.first_name?.charAt(0) || '') + (app.last_name?.charAt(0) || '');
                const statusClass = app.status === 'pending' ? 'pending' : (app.status === 'reviewed' ? 'reviewed' : 'new');

                return `
                    <div class="application-item">
                        <div class="application-avatar">${initials}</div>
                        <div class="application-info">
                            <span class="application-name">${app.first_name} ${app.last_name}</span>
                            <span class="application-details">${app.specialty || 'General'} • ${timeText}</span>
                        </div>
                        <div class="application-status ${statusClass}">${app.status || 'New'}</div>
                    </div>
                `;
            }).join('');
        }

        // Update job postings preview in dashboard
        function updateJobPostingsPreview(postings) {
            // Update counts
            const activeCount = postings.filter(p => p.status === 'open').length;
            const draftCount = postings.filter(p => p.status === 'draft').length;
            const totalApps = postings.reduce((sum, p) => sum + (parseInt(p.application_count) || 0), 0);

            document.getElementById('activeJobPostsCount').textContent = activeCount;
            document.getElementById('draftPostsCount').textContent = draftCount;
            document.getElementById('totalJobApplicationsCount').textContent = totalApps;

            // Update recent postings list
            const listContainer = document.getElementById('recentJobPostingsList');
            if (postings.length === 0) {
                listContainer.innerHTML = '<p style="text-align: center; color: #718096; padding: 20px;">No job postings yet</p>';
                return;
            }

            // Sort by date and get latest 3 active postings
            const recentPosts = postings
                .filter(p => p.status === 'open')
                .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
                .slice(0, 3);

            listContainer.innerHTML = recentPosts.map(post => {
                const daysAgo = Math.floor((new Date() - new Date(post.created_at)) / (1000 * 60 * 60 * 24));
                const timeText = daysAgo === 0 ? 'today' : daysAgo === 1 ? '1 day ago' : 
                               daysAgo < 7 ? `${daysAgo} days ago` : 
                               daysAgo < 30 ? `${Math.floor(daysAgo / 7)} week${Math.floor(daysAgo / 7) > 1 ? 's' : ''} ago` : 
                               `${Math.floor(daysAgo / 30)} month${Math.floor(daysAgo / 30) > 1 ? 's' : ''} ago`;
                const appCount = parseInt(post.application_count) || 0;

                return `
                    <div class="job-posting-item">
                        <div class="job-posting-info">
                            <span class="job-title">${post.title}</span>
                            <span class="job-details">${appCount} application${appCount !== 1 ? 's' : ''} • ${timeText}</span>
                        </div>
                        <div class="job-status active">Active</div>
                    </div>
                `;
            }).join('');
        }

        // Initialize page functionality
        function initializePage() {
            initializeFilters();
            initializeSearch();
            loadFreelancers();
            loadApplications();
            updateStats();
            loadDashboardPreviews(); // Load dashboard preview data
            
            // Load job postings on page load
            const companyId = <?php echo $_SESSION['user_id'] ?? 0; ?>;
            if (companyId > 0) {
                loadJobPostings(companyId);
            }
        }

        // Initialize filter functionality
        function initializeFilters() {
            const filterBtns = document.querySelectorAll('.filter-btn');
            const sections = document.querySelectorAll('.workforce-section');

            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const filter = btn.getAttribute('data-filter');

                    // Update active filter button
                    filterBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');

                    // Show/hide sections based on filter
                    sections.forEach(section => {
                        if (filter === 'employees' && section.id === 'employeesSection') {
                            section.style.display = 'block';
                        } else if (filter === 'freelancers' && section.id === 'freelancersSection') {
                            section.style.display = 'block';
                        } else if (filter === 'applications' && section.id === 'applicationsSection') {
                            section.style.display = 'block';
                        } else {
                            section.style.display = 'none';
                        }
                    });
                });
            });
        }

        // Load freelancers
        function loadFreelancers() {
            // Delegate to DB integration (assets/javascript/company/freelancers-db.js)
            if (window.freelancersDb && typeof window.freelancersDb.load === 'function') {
                window.freelancersDb.load();
                return;
            }

            const container = document.querySelector('.freelancer-list');
            if (!container) return;
            container.innerHTML = `
                <div class="empty-state" style="padding: 60px 20px; text-align: center;">
                    <i class="fas fa-user-tie" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
                    <h3 style="color: #666; margin-bottom: 10px;">No Freelancers Available</h3>
                    <p style="color: #999;">Freelancers will appear here when they are available in the system.</p>
                </div>
            `;
        }

        // Create freelancer item
        function createFreelancerItem(freelancer) {
            const item = document.createElement('div');
            item.className = 'freelancer-card';
            item.dataset.status = freelancer.status;

            // Determine if freelancer is available for assignment
            const isAvailable = freelancer.status.toLowerCase() === 'available';
            const hasCompletedWork = freelancer.currentAssignment && 
                                     freelancer.currentAssignment.workStatus === 'completed' &&
                                     freelancer.currentAssignment.paymentStatus === 'payment-due';

            // Status badge configuration
            const statusConfig = {
                'Available': { class: 'available', icon: 'fas fa-check-circle', label: 'Available' },
                'Busy': { class: 'busy', icon: 'fas fa-clock', label: 'Busy' },
                'Unavailable': { class: 'unavailable', icon: 'fas fa-times-circle', label: 'Unavailable' }
            };

            const status = statusConfig[freelancer.status] || statusConfig['Available'];

            // Get initials for avatar
            const initials = freelancer.avatar || (freelancer.firstName.charAt(0) + freelancer.lastName.charAt(0)).toUpperCase();

            // Assignment info HTML
            let assignmentHTML = '';
            if (freelancer.currentAssignment) {
                const assignment = freelancer.currentAssignment;
                const statusClass = getAssignmentStatusClass(assignment.workStatus, assignment.paymentStatus);
                const statusText = getAssignmentStatusText(assignment.workStatus, assignment.paymentStatus);
                
                assignmentHTML = `
                    <div class="freelancer-assignment">
                        <h6><i class="fas fa-briefcase"></i> Current Assignment</h6>
                        <p class="assignment-title">${assignment.jobTitle}</p>
                        <div class="assignment-progress-bar">
                            <div class="progress-fill" style="width: ${assignment.workProgress}%"></div>
                        </div>
                        <div class="assignment-meta">
                            <span class="assignment-status ${statusClass}">
                                <i class="fas ${getStatusIcon(assignment.workStatus, assignment.paymentStatus)}"></i>
                                ${statusText}
                            </span>
                            ${assignment.workStatus === 'completed' ? `
                                <span class="assignment-amount">
                                    LKR ${assignment.totalAmount.toLocaleString()}
                                </span>
                            ` : `
                                <span class="assignment-progress-text">${assignment.workProgress}%</span>
                            `}
                        </div>
                    </div>
                `;
            }

            item.innerHTML = `
                <div class="freelancer-header">
                    <div class="freelancer-profile">
                        <div class="freelancer-avatar-modern">${initials}</div>
                        <div class="freelancer-info-modern">
                            <h4>${freelancer.firstName} ${freelancer.lastName}</h4>
                            <span class="freelancer-specialty-badge">
                                <i class="fas fa-wrench"></i> ${freelancer.specialty}
                            </span>
                        </div>
                    </div>
                    <div class="freelancer-badges">
                        <span class="status-badge-fl ${status.class}">
                            <i class="${status.icon}"></i> ${status.label}
                        </span>
                    </div>
                </div>

                <div class="freelancer-body">
                    <div class="freelancer-details-grid">
                        <div class="detail-item">
                            <label>
                                <span class="detail-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                                Experience
                            </label>
                            <span>${freelancer.experience} years</span>
                        </div>
                        
                        <div class="detail-item">
                            <label>
                                <span class="detail-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </span>
                                Hourly Rate
                            </label>
                            <span>LKR ${freelancer.hourlyRate.toLocaleString()}/hr</span>
                        </div>
                        
                        <div class="detail-item">
                            <label>
                                <span class="detail-icon">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                Email
                            </label>
                            <span>${freelancer.email}</span>
                        </div>
                        
                        <div class="detail-item">
                            <label>
                                <span class="detail-icon">
                                    <i class="fas fa-star"></i>
                                </span>
                                Rating
                            </label>
                            <span>${freelancer.rating} <i class="fas fa-star" style="color: #fbbf24; font-size: 11px;"></i></span>
                        </div>
                    </div>

                    ${assignmentHTML}

                    <div class="freelancer-actions-modern">
                        <button class="action-btn-sm info" onclick="viewFreelancerDetails('${freelancer.id}')" title="View Full Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${isAvailable ? `
                        <button class="action-btn-sm success" onclick="assignJob('${freelancer.id}')" title="Assign to Job">
                            <i class="fas fa-plus-circle"></i>
                        </button>
                        ` : ''}
                        ${hasCompletedWork ? `
                        <button class="action-btn-sm primary" onclick="processPayment('${freelancer.id}')" title="Process Payment">
                            <i class="fas fa-credit-card"></i>
                        </button>
                        ` : ''}
                        <button class="action-btn-sm secondary" onclick="openChatWithFreelancer('${freelancer.id}')" title="Chat">
                            <i class="fas fa-comments"></i>
                        </button>
                    </div>
                </div>
            `;

            return item;
        }

        // Load applications
        function loadApplications() {
            const container = document.querySelector('.applications-list');
            container.innerHTML = '';

            applicationsData.forEach(application => {
                const item = createApplicationItem(application);
                container.appendChild(item);
            });
        }

        // Create application item
        function createApplicationItem(application) {
            const item = document.createElement('div');
            item.className = 'application-card';
            item.dataset.status = application.status || 'new';
            item.style.cursor = 'pointer';

            item.addEventListener('click', () => {
                viewApplicationDetails(application.id);
            });

            // Calculate days since application
            const appliedDate = new Date(application.applicationDate);
            const today = new Date();
            const daysSince = Math.floor((today - appliedDate) / (1000 * 60 * 60 * 24));
            const timeText = daysSince === 0 ? 'Today' : daysSince === 1 ? 'Yesterday' : `${daysSince} days ago`;

            // Status badge configuration
            const statusConfig = {
                'new': { class: 'new', icon: 'fas fa-star', label: 'New' },
                'reviewed': { class: 'reviewed', icon: 'fas fa-eye', label: 'Reviewed' },
                'interview': { class: 'interview', icon: 'fas fa-comments', label: 'Interview' },
                'pending': { class: 'pending', icon: 'fas fa-clock', label: 'Pending' }
            };

            const status = statusConfig[application.status || 'new'] || statusConfig['pending'];

            // Get initials for avatar
            const initials = application.avatar || (application.firstName.charAt(0) + application.lastName.charAt(0)).toUpperCase();

            item.innerHTML = `
                <div class="application-header">
                    <div class="applicant-profile">
                        <div class="applicant-avatar">${initials}</div>
                        <div class="applicant-info">
                            <h4>${application.firstName} ${application.lastName}</h4>
                            <span class="applicant-specialty">
                                <i class="fas fa-wrench"></i> ${application.specialty} Specialist
                            </span>
                        </div>
                    </div>
                    <div class="application-badges">
                        <span class="status-badge ${status.class}">
                            <i class="${status.icon}"></i> ${status.label}
                        </span>
                        <span class="time-badge">
                            <i class="fas fa-clock"></i> ${timeText}
                        </span>
                    </div>
                </div>

                <div class="application-details-grid">
                    <div class="detail-item">
                        <label>
                            <span class="detail-icon experience">
                                <i class="fas fa-briefcase"></i>
                            </span>
                            Experience
                        </label>
                        <span>${application.experience} years</span>
                    </div>
                    
                    <div class="detail-item">
                        <label>
                            <span class="detail-icon rate">
                                <i class="fas fa-money-bill-wave"></i>
                            </span>
                            Hourly Rate
                        </label>
                        <span>LKR ${application.expectedRate.toLocaleString()}/hr</span>
                    </div>
                    
                    <div class="detail-item">
                        <label>
                            <span class="detail-icon email">
                                <i class="fas fa-envelope"></i>
                            </span>
                            Email
                        </label>
                        <span>${application.email}</span>
                    </div>
                    
                    <div class="detail-item">
                        <label>
                            <span class="detail-icon phone">
                                <i class="fas fa-phone"></i>
                            </span>
                            Phone
                        </label>
                        <span>${application.phone}</span>
                    </div>
                </div>

                ${application.coverLetter ? `
                <div class="application-cover-letter">
                    <h6>
                        <i class="fas fa-file-alt"></i> Cover Letter
                    </h6>
                    <p>${application.coverLetter.substring(0, 150)}${application.coverLetter.length > 150 ? '... <a href="#" onclick="event.stopPropagation(); viewApplicationDetails(\'${application.id}\'); return false;" style="color: var(--primary-color); font-weight: 600;">Read more</a>' : ''}</p>
                </div>
                ` : ''}

                <div class="application-actions">
                    <button class="action-btn-sm success" onclick="event.stopPropagation(); approveApplication('${application.id}')" title="Accept Application">
                        <i class="fas fa-check-circle"></i> Accept
                    </button>
                    <button class="action-btn-sm danger" onclick="event.stopPropagation(); rejectApplication('${application.id}')" title="Decline Application">
                        <i class="fas fa-times-circle"></i> Decline
                    </button>
                </div>
            `;

            return item;
        }

        // Create employee HTML
        function createEmployeeHTML(employee) {
            const statusClass = getStatusClass(employee.status);

            return `
                <div class="card-header">
                    <div class="employee-info">
                        <div class="employee-avatar">${employee.avatar}</div>
                        <div class="employee-details">
                            <h4>${employee.firstName} ${employee.lastName}</h4>
                            <p class="employee-title">${employee.specialty} Specialist</p>
                            <p class="employee-contact">
                                <i class="fas fa-envelope"></i> ${employee.email && employee.email !== 'NULL' ? employee.email : 'No email provided'}
                            </p>
                            ${employee.phone && employee.phone !== 'NULL' ? `
                            <p class="employee-contact">
                                <i class="fas fa-phone"></i> ${employee.phone}
                            </p>
                            ` : ''}
                        </div>
                    </div>
                    <div class="employee-badges">
                        <span class="status-badge ${statusClass}">${employee.status}</span>
                        <span class="employment-badge">${employee.employmentType}</span>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="employee-stats">
                        <div class="stat-item">
                            <i class="fas fa-briefcase"></i>
                            <div>
                                <span class="stat-value">${employee.currentJobs}</span>
                                <span class="stat-label">Active Jobs</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <span class="stat-value">${employee.completedJobs}</span>
                                <span class="stat-label">Completed</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-star"></i>
                            <div>
                                <span class="stat-value">${employee.rating}</span>
                                <span class="stat-label">Rating</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="employee-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar"></i>
                            <span>${employee.experience} years experience</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-money-bill"></i>
                            <span>LKR ${employee.hourlyRate.toLocaleString()}/hour</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${employee.address}</span>
                        </div>
                    </div>
                </div>
                
                <div class="card-actions">
                    <button class="wf-btn wf-btn-view" onclick="viewEmployeeDetails('${employee.id}')" title="View Employee Details">
                        <i class="fas fa-user"></i>
                        <span>View Profile</span>
                    </button>
                    <button class="wf-btn wf-btn-assign" onclick="assignJob('${employee.id}')" title="Assign to Job">
                        <i class="fas fa-briefcase"></i>
                        <span>Assign Job</span>
                    </button>
                    <button class="wf-btn wf-btn-edit" onclick="editEmployee('${employee.id}')" title="Edit Employee">
                        <i class="fas fa-edit"></i>
                        <span>Edit</span>
                    </button>
                </div>
            `;
        }

        // Create freelancer HTML
        function createFreelancerHTML(freelancer) {
            const statusClass = getStatusClass(freelancer.status);

            return `
                <div class="card-header">
                    <div class="employee-info">
                        <div class="employee-avatar freelancer">${freelancer.avatar}</div>
                        <div class="employee-details">
                            <h4>${freelancer.firstName} ${freelancer.lastName}</h4>
                            <p class="employee-title">${freelancer.specialty} Contractor</p>
                            <p class="employee-contact">
                                <i class="fas fa-envelope"></i> ${freelancer.email}
                            </p>
                        </div>
                    </div>
                    <div class="employee-badges">
                        <span class="status-badge ${statusClass}">${freelancer.status}</span>
                        <span class="freelancer-badge">Freelancer</span>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="employee-stats">
                        <div class="stat-item">
                            <i class="fas fa-briefcase"></i>
                            <div>
                                <span class="stat-value">${freelancer.currentJobs}</span>
                                <span class="stat-label">Active Jobs</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <span class="stat-value">${freelancer.completedJobs}</span>
                                <span class="stat-label">Completed</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-star"></i>
                            <div>
                                <span class="stat-value">${freelancer.rating}</span>
                                <span class="stat-label">Rating</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="employee-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar"></i>
                            <span>${freelancer.experience} years experience</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-money-bill"></i>
                            <span>LKR ${freelancer.hourlyRate.toLocaleString()}/hour</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-handshake"></i>
                            <span>Contract since ${formatDate(freelancer.contractDate)}</span>
                        </div>
                    </div>
                </div>
                
                <div class="card-actions">
                    <button class="wf-btn wf-btn-view" onclick="viewEmployeeDetails('${freelancer.id}')" title="View Freelancer Details">
                        <i class="fas fa-user"></i>
                        <span>View Profile</span>
                    </button>
                    <button class="wf-btn wf-btn-assign" onclick="assignJob('${freelancer.id}')" title="Assign to Job">
                        <i class="fas fa-briefcase"></i>
                        <span>Assign Job</span>
                    </button>
                    <button class="wf-btn wf-btn-secondary" onclick="renewContract('${freelancer.id}')" title="Renew Contract">
                        <i class="fas fa-file-contract"></i>
                        <span>Renew</span>
                    </button>
                </div>
            `;
        }

        // Create application HTML
        function createApplicationHTML(application) {
            return `
                <div class="card-header">
                    <div class="employee-info">
                        <div class="employee-avatar pending">${application.avatar}</div>
                        <div class="employee-details">
                            <h4>${application.firstName} ${application.lastName}</h4>
                            <p class="employee-title">${application.specialty} Applicant</p>
                            <p class="employee-contact">
                                <i class="fas fa-envelope"></i> ${application.email}
                            </p>
                        </div>
                    </div>
                    <div class="employee-badges">
                        <span class="status-badge status-pending">Pending Review</span>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="employee-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar"></i>
                            <span>${application.experience} years experience</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-money-bill"></i>
                            <span>Expected: LKR ${application.expectedRate.toLocaleString()}/hour</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <span>Applied ${formatDate(application.applicationDate)}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-phone"></i>
                            <span>${application.phone}</span>
                        </div>
                    </div>
                </div>
                
                <div class="card-actions">
                    <button class="wf-btn wf-btn-view" onclick="viewApplication('${application.id}')" title="Review Application">
                        <i class="fas fa-file-alt"></i>
                        <span>Review</span>
                    </button>
                    <button class="wf-btn wf-btn-success" onclick="approveApplication('${application.id}')" title="Accept Application">
                        <i class="fas fa-check-circle"></i>
                        <span>Accept</span>
                    </button>
                    <button class="wf-btn wf-btn-danger" onclick="rejectApplication('${application.id}')" title="Decline Application">
                        <i class="fas fa-times-circle"></i>
                        <span>Decline</span>
                    </button>
                </div>
            `;
        }

        // =============================================
        // ACTION HANDLER FUNCTIONS - FULLY FUNCTIONAL
        // =============================================

        // View Employee/Freelancer Details
        function viewEmployeeDetails(personId) {
            // Find person in employees or freelancers
            let person = employeesData.find(e => e.id === personId) || 
                        freelancersData.find(f => f.id === personId);
            
            if (!person) {
                showNotification('Person not found', 'error');
                return;
            }

            // Open employee details drawer with full information
            currentApplicationId = personId;
            
            showNotification(`Opening details for ${person.firstName} ${person.lastName}`, 'info');
            
            // TODO: Implement full details drawer
            console.log('View Employee Details:', person);
        }

        // View Freelancer Details - FULLY FUNCTIONAL
        function viewFreelancerDetails(freelancerId) {
            const freelancer = freelancersData.find(f => f.id === freelancerId);
            
            if (!freelancer) {
                showNotification('Freelancer not found', 'error');
                return;
            }

            // Populate freelancer details drawer
            const drawer = document.getElementById('freelancerDetailsDrawer');
            if (drawer) {
                // Personal Information
                document.getElementById('freelancerName').textContent = 
                    `${freelancer.firstName} ${freelancer.lastName}`;
                document.getElementById('freelancerAvatar').textContent = freelancer.avatar;
                document.getElementById('freelancerEmail').textContent = freelancer.email;
                document.getElementById('freelancerPhone').textContent = freelancer.phone || 'Not provided';
                document.getElementById('freelancerAddress').textContent = freelancer.address || 'Not specified';
                
                // Professional Information
                document.getElementById('freelancerSpecialty').textContent = freelancer.specialty;
                document.getElementById('freelancerSpecialty2').textContent = freelancer.specialty;
                document.getElementById('freelancerExperience').textContent = `${freelancer.experience} years`;
                document.getElementById('freelancerRating').textContent = freelancer.rating;
                document.getElementById('freelancerRating2').textContent = freelancer.rating;
                document.getElementById('freelancerHourlyRate').textContent = 
                    `LKR ${freelancer.hourlyRate.toLocaleString()}/hour`;
                document.getElementById('freelancerHourlyRate2').textContent = 
                    `LKR ${freelancer.hourlyRate.toLocaleString()}/hour`;
                
                // Work Statistics
                document.getElementById('freelancerCurrentJobs').textContent = freelancer.currentJobs || 0;
                document.getElementById('freelancerCompletedJobs').textContent = freelancer.completedJobs || 0;
                document.getElementById('freelancerTotalEarnings').textContent = 
                    `LKR ${(freelancer.completedJobs * freelancer.hourlyRate * 8).toLocaleString()}`;
                
                // Status and Availability
                document.getElementById('freelancerStatus').textContent = freelancer.status;
                document.getElementById('freelancerStatus').className = 
                    `status-badge ${freelancer.status.toLowerCase()}`;
                document.getElementById('freelancerAvailability').textContent = 
                    freelancer.status === 'Available' ? 'Available Now' : 'Currently Busy';
                
                // Contract Information
                if (freelancer.contractDate) {
                    document.getElementById('freelancerContractDate').textContent = 
                        formatDate(freelancer.contractDate);
                    document.getElementById('freelancerContractDuration').textContent = 
                        calculateContractDuration(freelancer.contractDate);
                } else {
                    document.getElementById('freelancerContractDate').textContent = 'N/A';
                    document.getElementById('freelancerContractDuration').textContent = 'No active contract';
                }
                
                // Store current freelancer ID for action buttons
                window.currentViewFreelancerId = freelancerId;
                
                // Show/hide assign button based on availability
                const assignBtn = drawer.querySelector('.assign-freelancer-btn');
                if (assignBtn) {
                    if (freelancer.status.toLowerCase() === 'available') {
                        assignBtn.style.display = 'inline-flex';
                    } else {
                        assignBtn.style.display = 'none';
                    }
                }
                
                // Open drawer
                drawer.classList.add('active');
                showNotification(`Viewing ${freelancer.firstName} ${freelancer.lastName}'s profile`, 'info');
            } else {
                showNotification(`Opening profile for ${freelancer.firstName} ${freelancer.lastName}`, 'info');
                console.log('Freelancer Details:', freelancer);
            }
        }

        // Close Freelancer Details Drawer
        function closeFreelancerDetailsDrawer() {
            document.getElementById('freelancerDetailsDrawer').classList.remove('active');
            window.currentViewFreelancerId = null;
        }

        // Assign from Freelancer Details Drawer
        function assignFromFreelancerDrawer() {
            if (window.currentViewFreelancerId) {
                closeFreelancerDetailsDrawer();
                assignJob(window.currentViewFreelancerId);
            }
        }

        // Edit from Freelancer Details Drawer
        function editFromFreelancerDrawer() {
            // Open chat instead of edit
            if (window.currentViewFreelancerId) {
                closeFreelancerDetailsDrawer();
                openChatWithFreelancer(window.currentViewFreelancerId);
            }
        }

        // Calculate contract duration
        function calculateContractDuration(contractDate) {
            const start = new Date(contractDate);
            const now = new Date();
            const months = Math.floor((now - start) / (1000 * 60 * 60 * 24 * 30));
            
            if (months < 1) {
                const days = Math.floor((now - start) / (1000 * 60 * 60 * 24));
                return `${days} days`;
            } else if (months < 12) {
                return `${months} months`;
            } else {
                const years = Math.floor(months / 12);
                const remainingMonths = months % 12;
                return remainingMonths > 0 
                    ? `${years} year${years > 1 ? 's' : ''}, ${remainingMonths} month${remainingMonths > 1 ? 's' : ''}`
                    : `${years} year${years > 1 ? 's' : ''}`;
            }
        }

        // Edit Employee
        function editEmployee(employeeId) {
            // Redirect to chat instead of edit
            openChatWithFreelancer(employeeId);
        }

        // Chat with Freelancer/Repairer - Replaces Edit Function
        function editFreelancer(freelancerId) {
            // Redirect to chat instead of edit
            openChatWithFreelancer(freelancerId);
        }

        // Renew Contract
        function renewContract(freelancerId) {
            const freelancer = freelancersData.find(f => f.id === freelancerId);
            
            if (!freelancer) {
                showNotification('Freelancer not found', 'error');
                return;
            }

            if (confirm(`Renew contract for ${freelancer.firstName} ${freelancer.lastName}?`)) {
                showNotification('Contract renewal initiated. Opening contract form...', 'success');
                console.log('Renew Contract:', freelancer);
                
                // TODO: Open contract renewal drawer with pre-filled data
            }
        }

        // View Application Details - FULLY FUNCTIONAL
        function viewApplicationDetails(applicationId) {
            const application = applicationsData.find(app => app.id === applicationId);
            
            if (!application) {
                showNotification('Application not found', 'error');
                return;
            }

            currentApplicationId = applicationId;
            
            // Populate application details drawer
            const drawer = document.getElementById('applicationDetailsDrawer');
            if (drawer) {
                // Personal Information Tab
                document.getElementById('modalFullName').textContent = 
                    `${application.firstName} ${application.lastName}`;
                document.getElementById('modalEmail').textContent = application.email;
                document.getElementById('modalPhone').textContent = application.phone || 'Not provided';
                document.getElementById('modalApplicationDate').textContent = 
                    formatDate(application.applicationDate);
                
                // Professional Information Tab
                document.getElementById('modalSpecialty').textContent = application.specialty;
                document.getElementById('modalExperience').textContent = `${application.experience} years`;
                document.getElementById('modalExpectedRate').textContent = 
                    `LKR ${application.expectedRate.toLocaleString()}/hour`;
                document.getElementById('modalAvailability').textContent = 
                    application.availability || 'Immediate';
                
                // Additional Information Tab
                const coverLetter = application.coverLetter || 
                    `I am ${application.firstName} ${application.lastName}, a ${application.specialty} specialist with ${application.experience} years of experience. I am passionate about delivering quality repair services and ensuring customer satisfaction. I am available to start immediately and committed to maintaining high standards of work.`;
                document.getElementById('modalCoverLetter').textContent = coverLetter;
                
                // Skills
                const skillsContainer = document.getElementById('modalSkills');
                skillsContainer.innerHTML = '';
                const skills = application.skills || ['Problem Solving', 'Customer Service', 'Technical Repair', application.specialty];
                skills.forEach(skill => {
                    const skillTag = document.createElement('span');
                    skillTag.className = 'skill-tag';
                    skillTag.textContent = skill;
                    skillsContainer.appendChild(skillTag);
                });
                
                // Open drawer
                drawer.classList.add('active');
                showNotification(`Viewing application from ${application.firstName} ${application.lastName}`, 'info');
            } else {
                showNotification(`Opening application from ${application.firstName} ${application.lastName}`, 'info');
            }
            
            console.log('Application Details:', application);
        }

        // View Application (alternate)
        function viewApplication(applicationId) {
            viewApplicationDetails(applicationId);
        }

        // Assign Job to Employee/Freelancer
        function assignJob(employeeId) {
            // Find the employee/freelancer
            let person = null;
            let isFreelancer = false;
            
            // Check in freelancers
            person = freelancersData.find(f => f.id === employeeId);
            if (person) {
                isFreelancer = true;
            } else {
                // Check in employees
                person = employeesData.find(e => e.id === employeeId);
            }
            
            if (!person) {
                showNotification('Person not found', 'error');
                return;
            }
            
            // Store current assignment context
            window.currentAssignmentId = employeeId;
            window.currentAssignmentType = isFreelancer ? 'freelancer' : 'employee';
            
            // Populate the assignment form
            document.getElementById('assignPersonName').textContent = person.name;
            document.getElementById('assignPersonSpecialty').textContent = person.specialty;
            document.getElementById('assignPersonAvatar').textContent = person.avatar;
            document.getElementById('assignHourlyRate').value = person.hourlyRate;
            
            // Set default start date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('assignStartDate').value = today;
            
            // Clear other fields
            document.getElementById('assignJobSelect').value = '';
            document.getElementById('assignDeadline').value = '';
            document.getElementById('assignEstimatedHours').value = '';
            document.getElementById('assignNotes').value = '';
            
            // Update cost summary
            updateAssignmentCost();
            
            // Open the drawer
            document.getElementById('assignJobDrawer').classList.add('active');
        }

        function editEmployee(employeeId) {
            // Redirect to chat instead of edit
            openChatWithFreelancer(employeeId);
        }

        function closeAssignJobDrawer() {
            document.getElementById('assignJobDrawer').classList.remove('active');
            // Reset form after animation
            setTimeout(() => {
                document.getElementById('assignJobForm').reset();
                window.currentAssignmentId = null;
                window.currentAssignmentType = null;
            }, 300);
        }

        function updateAssignmentCost() {
            const hours = parseFloat(document.getElementById('assignEstimatedHours').value) || 0;
            const rate = parseFloat(document.getElementById('assignHourlyRate').value) || 0;
            const totalCost = hours * rate;
            
            document.getElementById('assignCostHours').textContent = hours.toFixed(1);
            document.getElementById('assignCostRate').textContent = rate.toLocaleString();
            document.getElementById('assignCostTotal').textContent = totalCost.toLocaleString();
        }

        function handleJobAssignment(event) {
            event.preventDefault();
            
            const jobSelect = document.getElementById('assignJobSelect');
            const startDate = document.getElementById('assignStartDate');
            const deadline = document.getElementById('assignDeadline');
            const hours = document.getElementById('assignEstimatedHours');
            const rate = document.getElementById('assignHourlyRate');
            const notes = document.getElementById('assignNotes');
            
            // Validation
            if (!jobSelect.value) {
                showNotification('Please select a job/project', 'error');
                jobSelect.focus();
                return false;
            }
            
            if (!startDate.value) {
                showNotification('Please set a start date', 'error');
                startDate.focus();
                return false;
            }
            
            if (!deadline.value) {
                showNotification('Please set a deadline', 'error');
                deadline.focus();
                return false;
            }
            
            if (new Date(deadline.value) <= new Date(startDate.value)) {
                showNotification('Deadline must be after start date', 'error');
                deadline.focus();
                return false;
            }
            
            if (!hours.value || hours.value <= 0) {
                showNotification('Please enter valid estimated hours', 'error');
                hours.focus();
                return false;
            }
            
            // Note: Hourly rate is pre-filled from the repairer's application and is read-only
            // No validation needed as it's always a valid value from their profile
            
            // Show loading state
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Assigning...';
            
            // Simulate API call
            setTimeout(() => {
                // Find and update the person's status
                if (window.currentAssignmentType === 'freelancer') {
                    const freelancer = freelancersData.find(f => f.id === window.currentAssignmentId);
                    if (freelancer) {
                        freelancer.status = 'Busy';
                    }
                } else {
                    const employee = employeesData.find(e => e.id === window.currentAssignmentId);
                    if (employee) {
                        employee.status = 'Busy';
                    }
                }
                
                // Reload workforce display
                loadWorkforce();
                
                // Show success and close drawer
                const personName = document.getElementById('assignPersonName').textContent;
                showNotification(`Job successfully assigned to ${personName}!`, 'success');
                closeAssignJobDrawer();
                
                // Reset button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }, 1000);
            
            return false;
        }

        function renewContract(freelancerId) {
            showNotification('Contract renewal would start here', 'info');
        }

        // ====================================
        // PAYMENT PROCESSING FUNCTIONS
        // ====================================

        function processPayment(freelancerId) {
            const freelancer = freelancersData.find(f => f.id === freelancerId);
            
            if (!freelancer || !freelancer.currentAssignment) {
                showNotification('No payment information available', 'error');
                return;
            }

            const assignment = freelancer.currentAssignment;
            
            if (assignment.workStatus !== 'completed') {
                showNotification('Work must be completed before processing payment', 'warning');
                return;
            }

            if (assignment.paymentStatus === 'paid') {
                showNotification('Payment has already been processed', 'info');
                return;
            }

            // Populate payment drawer
            document.getElementById('paymentFreelancerAvatar').textContent = freelancer.avatar;
            document.getElementById('paymentFreelancerName').textContent = `${freelancer.firstName} ${freelancer.lastName}`;
            document.getElementById('paymentFreelancerSpecialty').textContent = freelancer.specialty;
            
            document.getElementById('paymentJobTitle').textContent = assignment.jobTitle;
            document.getElementById('paymentJobId').textContent = `#${assignment.jobId.toUpperCase()}`;
            document.getElementById('paymentAssignedDate').textContent = formatDate(assignment.assignedDate);
            document.getElementById('paymentCompletedDate').textContent = formatDate(assignment.completedDate);
            document.getElementById('paymentEstimatedHours').textContent = assignment.estimatedHours;
            document.getElementById('paymentHourlyRate').textContent = `LKR ${assignment.agreedRate.toLocaleString()}`;
            document.getElementById('paymentTotalAmount').textContent = `LKR ${assignment.totalAmount.toLocaleString()}`;
            
            // Show completion evidence if available
            const evidenceSection = document.getElementById('paymentEvidence');
            if (assignment.completionEvidence) {
                evidenceSection.style.display = 'block';
                document.getElementById('paymentEvidenceText').textContent = assignment.completionEvidence;
            } else {
                evidenceSection.style.display = 'none';
            }

            // Calculate days pending
            const completedDate = new Date(assignment.completedDate);
            const today = new Date();
            const daysPending = Math.floor((today - completedDate) / (1000 * 60 * 60 * 24));
            document.getElementById('paymentDaysPending').textContent = daysPending;

            if (daysPending > 3) {
                document.getElementById('paymentDelayWarning').style.display = 'block';
            } else {
                document.getElementById('paymentDelayWarning').style.display = 'none';
            }

            // Set default payment method
            document.getElementById('paymentMethod').value = 'bank-transfer';
            updatePaymentMethodFields();

            // Store current freelancer ID for payment
            window.currentPaymentFreelancerId = freelancerId;

            // Open payment drawer
            document.getElementById('paymentDrawer').classList.add('active');
        }

        function closePaymentDrawer() {
            document.getElementById('paymentDrawer').classList.remove('active');
            window.currentPaymentFreelancerId = null;
        }

        function updatePaymentMethodFields() {
            const method = document.getElementById('paymentMethod').value;
            const bankFields = document.getElementById('bankTransferFields');
            const mobileFields = document.getElementById('mobileMoneyFields');
            const cashFields = document.getElementById('cashPaymentFields');

            bankFields.style.display = method === 'bank-transfer' ? 'block' : 'none';
            mobileFields.style.display = method === 'mobile-money' ? 'block' : 'none';
            cashFields.style.display = method === 'cash' ? 'block' : 'none';
        }

        function confirmPayment() {
            if (!window.currentPaymentFreelancerId) return;

            const freelancer = freelancersData.find(f => f.id === window.currentPaymentFreelancerId);
            if (!freelancer || !freelancer.currentAssignment) return;

            const method = document.getElementById('paymentMethod').value;
            const notes = document.getElementById('paymentNotes').value;

            // Validate based on payment method
            if (method === 'bank-transfer') {
                const accountNumber = document.getElementById('bankAccountNumber').value;
                const bank = document.getElementById('bankName').value;
                if (!accountNumber || !bank) {
                    showNotification('Please fill in all bank details', 'error');
                    return;
                }
            } else if (method === 'mobile-money') {
                const mobileNumber = document.getElementById('mobileMoneyNumber').value;
                const provider = document.getElementById('mobileMoneyProvider').value;
                if (!mobileNumber || !provider) {
                    showNotification('Please fill in all mobile money details', 'error');
                    return;
                }
            }

            // Show loading state
            const confirmBtn = document.querySelector('#paymentDrawer .payment-confirm-btn');
            const originalText = confirmBtn.innerHTML;
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing Payment...';

            // Simulate payment processing
            setTimeout(() => {
                // Update payment status
                freelancer.currentAssignment.paymentStatus = 'paid';
                freelancer.currentAssignment.paidDate = new Date().toISOString().split('T')[0];
                
                // Move to history
                freelancer.assignmentHistory.unshift({
                    jobId: freelancer.currentAssignment.jobId,
                    jobTitle: freelancer.currentAssignment.jobTitle,
                    completedDate: freelancer.currentAssignment.completedDate,
                    hoursWorked: freelancer.currentAssignment.estimatedHours,
                    amount: freelancer.currentAssignment.totalAmount,
                    paymentStatus: 'paid',
                    paidDate: freelancer.currentAssignment.paidDate
                });
                
                // Clear current assignment and update status
                freelancer.currentAssignment = null;
                freelancer.status = 'Available';

                confirmBtn.disabled = false;
                confirmBtn.innerHTML = originalText;

                closePaymentDrawer();
                showNotification(`Payment of LKR ${freelancer.assignmentHistory[0].amount.toLocaleString()} processed successfully!`, 'success');

                // Refresh freelancer list
                setTimeout(() => {
                    loadFreelancers();
                }, 500);
            }, 2000);
        }

        // Store current application being processed
        let currentApplicationForContract = null;

        function approveApplication(applicationId) {
            // Open contract creation drawer instead of immediate approval
            openContractCreationDrawer(applicationId);
        }

        function openContractCreationDrawer(applicationId) {
            const application = applicationsData.find(app => app.id === applicationId);
            if (!application) return;

            currentApplicationForContract = application;

            // Populate drawer with applicant details
            document.getElementById('contractApplicantName').textContent = application.name;
            document.getElementById('summaryName').textContent = application.name;
            document.getElementById('summaryPosition').textContent = application.role || 'Repairer';

            // Set default start date to tomorrow
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            document.getElementById('contractStartDate').value = tomorrow.toISOString().split('T')[0];

            // Reset form
            document.getElementById('contractForm').reset();
            updateContractSummary();

            // Show drawer
            document.getElementById('contractCreationDrawer').classList.add('active');
        }

        function closeContractDrawer() {
            document.getElementById('contractCreationDrawer').classList.remove('active');
            currentApplicationForContract = null;
        }

        function updatePaymentFields() {
            const structure = document.getElementById('paymentStructure').value;
            const salaryGroup = document.getElementById('salaryGroup');
            const salaryLabel = salaryGroup.querySelector('label');
            const salaryInput = document.getElementById('salaryAmount');

            switch (structure) {
                case 'monthly':
                    salaryLabel.textContent = 'Monthly Salary (LKR)';
                    salaryInput.placeholder = 'e.g., 75000';
                    break;
                case 'hourly':
                    salaryLabel.textContent = 'Hourly Rate (LKR)';
                    salaryInput.placeholder = 'e.g., 500';
                    break;
                case 'project':
                    salaryLabel.textContent = 'Project Rate (LKR)';
                    salaryInput.placeholder = 'e.g., 25000';
                    break;
                case 'commission':
                    salaryLabel.textContent = 'Commission Rate (%)';
                    salaryInput.placeholder = 'e.g., 15';
                    break;
            }
            updateContractSummary();
        }

        function updateContractSummary() {
            // Update contract type
            const selectedType = document.querySelector('input[name="contractType"]:checked');
            if (selectedType) {
                const typeText = selectedType.parentElement.querySelector('h5').textContent;
                document.getElementById('summaryType').textContent = typeText;
            }

            // Update duration
            const duration = document.getElementById('contractDuration').value;
            const startDate = document.getElementById('contractStartDate').value;
            let durationText = '-';
            if (startDate) {
                if (duration === 'permanent') {
                    durationText = `From ${startDate} (Permanent)`;
                } else if (duration === 'custom') {
                    const endDate = document.getElementById('contractEndDate').value;
                    durationText = endDate ? `${startDate} to ${endDate}` : `From ${startDate}`;
                } else {
                    durationText = `${duration} months from ${startDate}`;
                }
            }
            document.getElementById('summaryDuration').textContent = durationText;

            // Update compensation
            const paymentStructure = document.getElementById('paymentStructure').value;
            const amount = document.getElementById('salaryAmount').value;
            const frequency = document.getElementById('paymentFrequency').value;
            let compensationText = '-';
            if (amount) {
                const structureLabels = {
                    'monthly': 'LKR',
                    'hourly': 'LKR/hour',
                    'project': 'LKR/project',
                    'commission': '%'
                };
                compensationText = `${amount} ${structureLabels[paymentStructure]} (${frequency})`;
            }
            document.getElementById('summaryCompensation').textContent = compensationText;

            // Update benefits
            const selectedBenefits = Array.from(document.querySelectorAll('input[name="benefits"]:checked'))
                .map(cb => cb.parentElement.querySelector('span').textContent.trim())
                .slice(0, 3);
            const benefitsText = selectedBenefits.length > 0 
                ? selectedBenefits.join(', ') + (selectedBenefits.length < document.querySelectorAll('input[name="benefits"]:checked').length ? '...' : '')
                : 'None selected';
            document.getElementById('summaryBenefits').textContent = benefitsText;
        }

        function saveContractAsDraft() {
            if (!currentApplicationForContract) return;

            const contractData = gatherContractData();
            console.log('Saving contract as draft:', contractData);
            
            showNotification('Contract saved as draft', 'success');
            closeContractDrawer();
        }

        function gatherContractData() {
            const benefits = Array.from(document.querySelectorAll('input[name="benefits"]:checked'))
                .map(cb => cb.value);
            const terms = Array.from(document.querySelectorAll('input[name="contractTerms"]:checked'))
                .map(cb => cb.value);

            return {
                applicationId: currentApplicationForContract.id,
                employeeName: currentApplicationForContract.name,
                employeeEmail: currentApplicationForContract.email,
                contractType: document.querySelector('input[name="contractType"]:checked')?.value,
                startDate: document.getElementById('contractStartDate').value,
                duration: document.getElementById('contractDuration').value,
                endDate: document.getElementById('contractEndDate').value,
                paymentStructure: document.getElementById('paymentStructure').value,
                salaryAmount: document.getElementById('salaryAmount').value,
                paymentFrequency: document.getElementById('paymentFrequency').value,
                hoursPerWeek: document.getElementById('hoursPerWeek').value,
                workSchedule: document.getElementById('workSchedule').value,
                overtimePolicy: document.getElementById('overtimePolicy').value,
                benefits: benefits,
                additionalBenefits: document.getElementById('additionalBenefits').value,
                annualLeave: document.getElementById('annualLeave').value,
                sickLeave: document.getElementById('sickLeave').value,
                casualLeave: document.getElementById('casualLeave').value,
                noticeEmployer: document.getElementById('noticeEmployer').value,
                noticeEmployee: document.getElementById('noticeEmployee').value,
                severancePay: document.getElementById('severancePay').value,
                specialClauses: document.getElementById('specialClauses').value,
                contractTerms: terms,
                createdAt: new Date().toISOString()
            };
        }

        // Handle contract form submission
        document.addEventListener('DOMContentLoaded', function() {
            const contractForm = document.getElementById('contractForm');
            if (contractForm) {
                contractForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    if (!currentApplicationForContract) return;

                    const contractData = gatherContractData();
                    
                    // Validate required fields
                    if (!contractData.startDate || !contractData.salaryAmount) {
                        showNotification('Please fill in all required fields', 'error');
                        return;
                    }

                    console.log('Creating contract:', contractData);

                    // Here you would send contractData to backend
                    // For now, simulate success
                    showNotification('Contract generated and sent successfully!', 'success');

                    // Finalize the hiring process
                    finalizeHiring(currentApplicationForContract.id, contractData);
                });

                // Update summary on input changes
                const formInputs = contractForm.querySelectorAll('input, select, textarea');
                formInputs.forEach(input => {
                    input.addEventListener('change', updateContractSummary);
                });

                // Handle contract duration changes
                document.getElementById('contractDuration').addEventListener('change', function() {
                    const customGroup = document.getElementById('customEndDateGroup');
                    customGroup.style.display = this.value === 'custom' ? 'block' : 'none';
                    updateContractSummary();
                });
            }
        });

        function finalizeHiring(applicationId, contractData) {
            // Remove from applications array and add to employees
            const appIndex = applicationsData.findIndex(app => app.id === applicationId);
            if (appIndex > -1) {
                const employee = applicationsData[appIndex];
                employee.contractData = contractData;
                employee.hiredDate = new Date().toISOString();
                
                applicationsData.splice(appIndex, 1);
                
                // Add to employees (in real app, this would be sent to backend)
                showNotification(`${employee.name} hired successfully! Contract sent to their email.`, 'success');
                
                closeContractDrawer();
                loadWorkforce();
                updateStats();
            }
        }

        function rejectApplication(applicationId) {
            if (confirm('Reject this application? This action cannot be undone.')) {
                showNotification('Application rejected', 'info');
                const appIndex = applicationsData.findIndex(app => app.id === applicationId);
                if (appIndex > -1) {
                    applicationsData.splice(appIndex, 1);
                    loadWorkforce();
                    updateStats();
                }
            }
        }

        function viewApplication(applicationId) {
            showNotification('Application details would open here', 'info');
        }

        function openJobPostingModal() {
            // Reset form for creating new job
            resetJobPostingForm();
            
            // Update drawer title for creating new job
            const drawerHeader = document.querySelector('#jobPostingDrawer .job-posting-header h3');
            if (drawerHeader) {
                drawerHeader.innerHTML = '<i class="fas fa-bullhorn"></i> Create New Job Posting';
            }
            
            // Remove editing ID
            delete document.getElementById('jobPostingForm').dataset.editingId;
            
            document.getElementById('jobPostingDrawer').classList.add('active');
            showStep(1);
        }

        function closeJobPostingDrawer() {
            document.getElementById('jobPostingDrawer').classList.remove('active');
            
            // Reset form after closing animation
            setTimeout(() => {
                resetJobPostingForm();
                
                // Reset drawer title
                const drawerHeader = document.querySelector('#jobPostingDrawer .job-posting-header h3');
                if (drawerHeader) {
                    drawerHeader.innerHTML = '<i class="fas fa-bullhorn"></i> Create New Job Posting';
                }
                
                // Remove editing ID
                delete document.getElementById('jobPostingForm').dataset.editingId;
            }, 300);
        }

        function resetJobPostingForm() {
            document.getElementById('jobPostingForm').reset();
            showStep(1);
            updatePreview();
        }

        // Job Posting Step Navigation
        let currentStep = 1;
        const totalSteps = 3;

        function showStep(step) {
            currentStep = step;
            
            // Hide all steps
            document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
            
            // Show current step
            document.getElementById(`step-${step}`).classList.add('active');
            
            // Update step indicators
            document.querySelectorAll('.step-item').forEach((item, index) => {
                const stepNum = index + 1;
                item.classList.remove('active', 'completed');
                
                if (stepNum === step) {
                    item.classList.add('active');
                } else if (stepNum < step) {
                    item.classList.add('completed');
                }
            });
            
            // Update navigation buttons
            updateNavigationButtons();
        }

        function nextStep() {
            if (validateCurrentStep() && currentStep < totalSteps) {
                showStep(currentStep + 1);
                if (currentStep === 3) {
                    updatePreview();
                }
            }
        }

        function previousStep() {
            if (currentStep > 1) {
                showStep(currentStep - 1);
            }
        }

        function updateNavigationButtons() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const publishBtn = document.getElementById('publishBtn');
            
            prevBtn.style.display = currentStep === 1 ? 'none' : 'block';
            nextBtn.style.display = currentStep === totalSteps ? 'none' : 'block';
            publishBtn.style.display = currentStep === totalSteps ? 'block' : 'none';
        }

        function validateCurrentStep() {
            const requiredFields = document.querySelectorAll(`#step-${currentStep} [required]`);
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#e74c3c';
                    isValid = false;
                } else {
                    field.style.borderColor = '';
                }
            });
            
            if (!isValid) {
                showNotification('Please fill in all required fields', 'error');
            }
            
            return isValid;
        }

        function updatePreview() {
            const title = document.getElementById('jobTitle').value || 'Job Title';
            const category = document.getElementById('jobCategory').value || 'Category';
            const type = document.getElementById('employmentType').value || 'Type';
            const minBudget = document.getElementById('minBudget').value || '0';
            const maxBudget = document.getElementById('maxBudget').value || '0';
            const description = document.getElementById('jobDescription').value || 'Job description will appear here...';
            const skills = document.getElementById('requiredSkills').value;
            const experienceSelect = document.getElementById('minExperience');
            const experienceValue = experienceSelect ? experienceSelect.value : '';
            const experienceLabel = experienceSelect && experienceSelect.selectedIndex >= 0
                ? experienceSelect.options[experienceSelect.selectedIndex].textContent
                : '';
            const location = document.getElementById('locationRequirements').value;
            
            document.getElementById('previewTitle').textContent = title;
            document.getElementById('previewCategory').textContent = category;
            document.getElementById('previewType').textContent = type;
            document.getElementById('previewBudget').textContent = `LKR ${minBudget} - ${maxBudget}/hr`;
            document.getElementById('previewDescription').textContent = description;
            
            // Update requirements list
            const requirementsList = document.getElementById('previewRequirements');
            requirementsList.innerHTML = '';
            
            if (experienceValue) {
                const li = document.createElement('li');
                li.textContent = `Minimum experience: ${experienceLabel || experienceValue}`;
                requirementsList.appendChild(li);
            }
            
            if (skills) {
                skills.split('\n').forEach(skill => {
                    if (skill.trim()) {
                        const li = document.createElement('li');
                        li.textContent = skill.trim();
                        requirementsList.appendChild(li);
                    }
                });
            }
            
            if (location) {
                const li = document.createElement('li');
                li.textContent = `Location: ${location}`;
                requirementsList.appendChild(li);
            }
        }

        function saveAsDraft() {
            saveDraftJobPosting();
        }

        async function publishJobPosting() {
            if (!validateCurrentStep()) return;
            
            try {
                const form = document.getElementById('jobPostingForm');
                const editingId = form.dataset.editingId;
                const formData = collectFormData();
                formData.status = 'open'; // Published status
                formData.company_id = window.CURRENT_COMPANY_ID;
                
                let response;
                if (editingId) {
                    // Update existing job posting
                    formData.posting_id = parseInt(editingId);
                    response = await fetch('/2nd-Year-Group-Project/FixLanka/api/job-postings.php', {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(formData)
                    });
                } else {
                    // Create new job posting
                    response = await fetch('/2nd-Year-Group-Project/FixLanka/api/job-postings.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(formData)
                    });
                }
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification(editingId ? 'Job posting updated successfully!' : 'Job posting published successfully!', 'success');
                    closeJobPostingDrawer();
                    
                    // Refresh job postings list
                    const companyId = <?php echo $_SESSION['user_id'] ?? 0; ?>;
                    setTimeout(() => {
                        loadJobPostings(companyId);
                    }, 300);
                } else {
                    throw new Error(result.error || 'Failed to publish job posting');
                }
            } catch (error) {
                console.error('Error publishing job posting:', error);
                showNotification('Error: ' + error.message, 'error');
            }
        }

        async function saveAsDraft() {
            try {
                const form = document.getElementById('jobPostingForm');
                const editingId = form.dataset.editingId;
                const formData = collectFormData();
                formData.status = 'draft';
                formData.company_id = window.CURRENT_COMPANY_ID;
                
                let response;
                if (editingId) {
                    // Update existing draft
                    formData.posting_id = parseInt(editingId);
                    response = await fetch('/2nd-Year-Group-Project/FixLanka/api/job-postings.php', {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(formData)
                    });
                } else {
                    // Create new draft
                    response = await fetch('/2nd-Year-Group-Project/FixLanka/api/job-postings.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(formData)
                    });
                }
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('Job posting saved as draft!', 'success');
                    closeJobPostingDrawer();
                    
                    // Refresh job postings list
                    const companyId = <?php echo $_SESSION['user_id'] ?? 0; ?>;
                    setTimeout(() => {
                        loadJobPostings(companyId);
                    }, 300);
                } else {
                    throw new Error(result.error || 'Failed to save draft');
                }
            } catch (error) {
                console.error('Error saving draft:', error);
                showNotification('Error: ' + error.message, 'error');
            }
        }

        function collectFormData() {
            const notifyCheckbox = document.getElementById('notifyRelevantRepairers');
            const directAppCheckbox = document.getElementById('allowDirectApplications');
            
            return {
                title: document.getElementById('jobTitle').value,
                category: document.getElementById('jobCategory').value,
                employment_type: document.getElementById('employmentType').value,
                related_project_id: document.getElementById('relatedProject').value || null,
                description: document.getElementById('jobDescription').value,
                min_experience: Number(document.getElementById('minExperience').value),
                priority_level: document.getElementById('priorityLevel').value,
                min_budget: parseFloat(document.getElementById('minBudget').value) || 0,
                max_budget: parseFloat(document.getElementById('maxBudget').value) || 0,
                application_deadline: document.getElementById('applicationDeadline').value || null,
                required_skills: document.getElementById('requiredSkills').value || null,
                location: document.getElementById('locationRequirements').value || 'Not specified',
                location_requirements: document.getElementById('locationRequirements').value || null,
                notify_repairers: notifyCheckbox ? notifyCheckbox.checked : true,
                allow_direct_applications: directAppCheckbox ? directAppCheckbox.checked : true
            };
        }

        // Load employee categories from database
        async function loadEmployeeCategories() {
            const container = document.querySelector('.employee-categories');
            
            if (!container) {
                console.error('Employee categories container not found');
                return;
            }

            // Show loading state
            container.innerHTML = '<div style="text-align: center; padding: 40px; color: #999;"><i class="fas fa-spinner fa-spin"></i> Loading employees...</div>';

            try {
                const companyId = window.CURRENT_COMPANY_ID;
                const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/company-employees.php?action=stats&company_id=${companyId}`);
                const data = await response.json();

                if (!data.specialties || data.specialties.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state" style="padding: 60px 20px; text-align: center;">
                            <i class="fas fa-users" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
                            <h3 style="color: #666; margin-bottom: 10px;">No Employees Yet</h3>
                            <p style="color: #999; margin-bottom: 20px;">Add your first employee to start building your team.</p>
                            <button class="action-btn primary" onclick="openBulkStaffModal()">
                                <i class="fas fa-user-plus"></i> Add Staff
                            </button>
                        </div>
                    `;
                    return;
                }

                // Build category cards
                let html = '<div class="employee-categories-grid">';
                
                data.specialties.forEach(specialty => {
                    const avgRate = parseFloat(specialty.avg_hourly_rate || 0).toFixed(2);
                    const minRate = parseFloat(specialty.min_hourly_rate || 0).toFixed(2);
                    const maxRate = parseFloat(specialty.max_hourly_rate || 0).toFixed(2);
                    const avgRating = parseFloat(specialty.avg_rating || 0).toFixed(1);
                    
                    html += `
                        <div class="category-card" data-specialty="${specialty.specialty}">
                            <div class="category-header">
                                <div class="category-icon">
                                    <i class="fas fa-${getCategoryIcon(specialty.specialty)}"></i>
                                </div>
                                <h3>${specialty.specialty}</h3>
                            </div>
                            <div class="category-stats">
                                <div class="stat-row">
                                    <span class="stat-label">Total Employees:</span>
                                    <span class="stat-value">${specialty.total_count || 0}</span>
                                </div>
                                <div class="stat-row">
                                    <span class="stat-label">Active:</span>
                                    <span class="stat-value success">${specialty.active_count || 0}</span>
                                </div>
                                <div class="stat-row">
                                    <span class="stat-label">Inactive:</span>
                                    <span class="stat-value secondary">${specialty.inactive_count || 0}</span>
                                </div>
                                <div class="stat-row">
                                    <span class="stat-label">Average Rating:</span>
                                    <span class="stat-value">
                                        <i class="fas fa-star" style="color: #fbbf24;"></i> ${avgRating}
                                    </span>
                                </div>
                                <div class="stat-row">
                                    <span class="stat-label">Hourly Rate:</span>
                                    <span class="stat-value">LKR ${minRate} - ${maxRate}</span>
                                </div>
                            </div>
                            <div class="category-actions">
                                <button class="action-btn-sm info" onclick="viewEmployeesBySpecialty('${specialty.specialty}')" title="View Employees">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="action-btn-sm primary" onclick="addEmployeeToSpecialty('${specialty.specialty}')" title="Add Employee">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });

                html += '</div>';
                container.innerHTML = html;

            } catch (error) {
                console.error('Error loading employee categories:', error);
                container.innerHTML = '<div style="text-align: center; padding: 40px; color: red;"><i class="fas fa-exclamation-circle"></i> Error loading employees</div>';
            }
        }

        // Helper function to get icon for each category
        function getCategoryIcon(specialty) {
            const icons = {
                'Electrician': 'bolt',
                'Plumber': 'wrench',
                'Painter': 'paint-roller',
                'Carpenter': 'hammer',
                'HVAC': 'fan',
                'Mason': 'hard-hat',
                'Welder': 'fire',
                'Mechanic': 'cog',
                'Technician': 'tools'
            };
            return icons[specialty] || 'user';
        }

        // View employees by specialty
        function viewEmployeesBySpecialty(specialty) {
            // TODO: Implement detailed view of employees by specialty
            alert(`View all ${specialty} employees - Feature coming soon!`);
        }

        // Add employee to specialty
        function addEmployeeToSpecialty(specialty) {
            openBulkStaffModal();
            // Pre-select the specialty in the modal
            setTimeout(() => {
                const specialtyInputs = document.querySelectorAll('.staff-specialty');
                if (specialtyInputs.length > 0) {
                    specialtyInputs[0].value = specialty;
                }
            }, 100);
        }

        // Load job postings from database
        async function loadJobPostings() {
            try {
                const companyId = window.CURRENT_COMPANY_ID;
                const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/job-postings.php?action=list&company_id=${companyId}`);
                const result = await response.json();
                
                if (result.success) {
                    displayJobPostings(result.postings);
                } else {
                    console.error('Failed to load job postings:', result.error);
                }
            } catch (error) {
                console.error('Error loading job postings:', error);
            }
        }

        async function displayJobPostings(postings) {
            const container = document.querySelector('.job-postings-list');
            
            if (!container) {
                console.error('Job postings container not found');
                return;
            }

            // If no postings, show empty state
            if (!postings || postings.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-briefcase"></i>
                        <h3>No Job Postings Yet</h3>
                        <p>Create your first job posting to start recruiting repairers.</p>
                        <button class="btn-primary" onclick="openJobPostingModal()">
                            <i class="fas fa-plus"></i> Create Job Posting
                        </button>
                    </div>
                `;
                
                // Update preview card stats
                updateJobPostingsStats({ total: 0, drafts: 0, applications: 0 });
                return;
            }

            // Clear container
            container.innerHTML = '';

            // Get application counts for each posting
            const companyId = <?php echo $_SESSION['user_id'] ?? 0; ?>;
            
            // Calculate stats
            let stats = {
                total: postings.length,
                drafts: postings.filter(p => p.status === 'draft').length,
                applications: 0,
                open: postings.filter(p => p.status === 'open').length
            };

            // Create and append cards
            for (const posting of postings) {
                try {
                    // Get application count for this posting
                    const appResponse = await fetch(`/2nd-Year-Group-Project/FixLanka/api/job-postings.php?action=applications&posting_id=${posting.posting_id}`);
                    const appData = await appResponse.json();
                    const applicationCount = appData.success ? appData.count : 0;
                    
                    stats.applications += applicationCount;
                    
                    const postingCard = createJobPostingCard(posting, applicationCount);
                    container.appendChild(postingCard);
                } catch (error) {
                    console.error(`Error loading applications for posting ${posting.posting_id}:`, error);
                    const postingCard = createJobPostingCard(posting, 0);
                    container.appendChild(postingCard);
                }
            }

            // Update preview card stats
            updateJobPostingsStats(stats);
        }

        function updateJobPostingsStats(stats) {
            // Update the preview card on dashboard
            const mainStatNumber = document.querySelector('.job-postings-card .main-stat .stat-number');
            const draftsNumber = document.querySelector('.job-postings-card .sub-stats .sub-stat:first-child .sub-number');
            const applicationsNumber = document.querySelector('.job-postings-card .sub-stats .sub-stat:last-child .sub-number');
            
            if (mainStatNumber) mainStatNumber.textContent = stats.open || stats.total || 0;
            if (draftsNumber) draftsNumber.textContent = stats.drafts || 0;
            if (applicationsNumber) applicationsNumber.textContent = stats.applications || 0;
        }

        // Job Postings Management Functions
        function filterJobPostings(filter) {
            // Update active tab
            document.querySelectorAll('.job-posting-tabs .tab-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            // Filter job postings
            const postings = document.querySelectorAll('.job-posting-card');
            let visibleCount = 0;
            
            postings.forEach(posting => {
                const status = posting.dataset.status;
                
                // Map 'active' filter to 'open' status
                const targetStatus = filter === 'active' ? 'open' : filter;
                
                if (filter === 'all' || status === targetStatus) {
                    posting.style.display = 'block';
                    visibleCount++;
                } else {
                    posting.style.display = 'none';
                }
            });

            // Show empty state if no postings match filter
            const container = document.querySelector('.job-postings-list');
            const existingEmpty = container.querySelector('.empty-state');
            
            if (visibleCount === 0 && !existingEmpty) {
                const emptyDiv = document.createElement('div');
                emptyDiv.className = 'empty-state filter-empty';
                emptyDiv.innerHTML = `
                    <i class="fas fa-filter"></i>
                    <h3>No ${filter === 'all' ? '' : filter.charAt(0).toUpperCase() + filter.slice(1)} Postings</h3>
                    <p>No job postings match the selected filter.</p>
                `;
                container.appendChild(emptyDiv);
            } else if (visibleCount > 0) {
                // Remove filter empty state if exists
                const filterEmpty = container.querySelector('.filter-empty');
                if (filterEmpty) filterEmpty.remove();
            }
        }

        // Filter applications by status
        function filterApplications(filter) {
            // Update active tab
            document.querySelectorAll('.application-tabs button').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            // Filter applications
            const applications = document.querySelectorAll('.application-card');
            let visibleCount = 0;
            
            applications.forEach(application => {
                const status = application.dataset.status;
                
                if (filter === 'all' || status === filter) {
                    application.style.display = 'block';
                    visibleCount++;
                } else {
                    application.style.display = 'none';
                }
            });

            // Show empty state if no applications match filter
            const container = document.querySelector('.applications-list');
            const existingEmpty = container.querySelector('.applications-empty');
            
            if (visibleCount === 0 && !existingEmpty) {
                const emptyDiv = document.createElement('div');
                emptyDiv.className = 'applications-empty filter-empty';
                
                const filterLabels = {
                    'all': 'Applications',
                    'new': 'New Applications',
                    'reviewed': 'Reviewed Applications',
                    'interview': 'Interview Applications'
                };
                
                emptyDiv.innerHTML = `
                    <i class="fas fa-inbox"></i>
                    <h3>No ${filterLabels[filter]}</h3>
                    <p>There are no applications matching the selected filter.</p>
                `;
                container.appendChild(emptyDiv);
            } else if (visibleCount > 0) {
                // Remove filter empty state if exists
                const filterEmpty = container.querySelector('.filter-empty');
                if (filterEmpty) filterEmpty.remove();
            }
        }

        // This function is now handled by the loadJobPostings function defined earlier (line ~3444)
        // which calls the API and then displayJobPostings()

        function createJobPostingCard(posting, applicationCount = 0) {
            const card = document.createElement('div');
            card.className = 'job-posting-card';
            card.dataset.status = posting.status;
            
            // Status badge styling
            const statusConfig = {
                'open': { class: 'success', label: 'Open', icon: 'fas fa-check-circle' },
                'draft': { class: 'warning', label: 'Draft', icon: 'fas fa-edit' },
                'closed': { class: 'danger', label: 'Closed', icon: 'fas fa-times-circle' },
                'filled': { class: 'info', label: 'Filled', icon: 'fas fa-user-check' }
            };
            
            const status = statusConfig[posting.status] || statusConfig['draft'];
            
            // Format category display
            const categoryDisplay = posting.category.charAt(0).toUpperCase() + posting.category.slice(1).replace('-', ' ');
            
            // Format employment type
            const employmentDisplay = posting.employment_type 
                ? posting.employment_type.charAt(0).toUpperCase() + posting.employment_type.slice(1).replace('-', ' ')
                : 'Not specified';
            
            // Format budget
            const budget = posting.min_budget && posting.max_budget
                ? `LKR ${posting.min_budget.toLocaleString()} - ${posting.max_budget.toLocaleString()}/hr`
                : 'Budget not set';
            
            // Format deadline (schema drift safe)
            const deadlineRaw = posting.application_deadline ?? posting.applicationDeadline;
            const deadline = deadlineRaw ? formatDate(deadlineRaw) : 'No deadline';
            
            // Format priority
            const priorityConfig = {
                'urgent': { class: 'danger', icon: 'fas fa-exclamation-circle', label: 'Urgent' },
                'high': { class: 'warning', icon: 'fas fa-arrow-up', label: 'High' },
                'medium': { class: 'info', icon: 'fas fa-minus', label: 'Medium' },
                'normal': { class: 'secondary', icon: 'fas fa-arrow-down', label: 'Normal' }
            };
            
            const priority = priorityConfig[posting.priority_level] || priorityConfig['normal'];
            
            // Show edit button for draft, closed, or filled posts
            const canEdit = ['draft', 'closed', 'filled'].includes(posting.status);
            
            // Show status change options based on current status
            let statusActions = '';
            if (posting.status === 'draft') {
                statusActions = `
                    <button class="action-btn-sm success" onclick="changeJobStatus(${posting.posting_id}, 'open')" title="Publish">
                        <i class="fas fa-paper-plane"></i> Publish
                    </button>
                `;
            } else if (posting.status === 'open') {
                statusActions = `
                    <button class="action-btn-sm warning" onclick="changeJobStatus(${posting.posting_id}, 'closed')" title="Close">
                        <i class="fas fa-ban"></i> Close
                    </button>
                    <button class="action-btn-sm info" onclick="changeJobStatus(${posting.posting_id}, 'filled')" title="Mark as Filled">
                        <i class="fas fa-check"></i> Filled
                    </button>
                `;
            } else if (posting.status === 'closed') {
                statusActions = `
                    <button class="action-btn-sm success" onclick="changeJobStatus(${posting.posting_id}, 'open')" title="Reopen">
                        <i class="fas fa-redo"></i> Reopen
                    </button>
                `;
            }
            
            card.innerHTML = `
                <div class="posting-header">
                    <div class="posting-title-section">
                        <h5>${escapeHtml(posting.title)}</h5>
                        <div class="posting-badges">
                            <span class="badge badge-${status.class}">
                                <i class="${status.icon}"></i> ${status.label}
                            </span>
                            <span class="badge badge-secondary">${categoryDisplay}</span>
                            <span class="badge badge-${priority.class}">
                                <i class="${priority.icon}"></i> ${priority.label}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="posting-description">
                    ${escapeHtml(posting.description || '').substring(0, 150)}${posting.description && posting.description.length > 150 ? '...' : ''}
                </div>
                <div class="posting-meta">
                    <div class="meta-row">
                        <span><i class="fas fa-briefcase"></i> ${employmentDisplay}</span>
                        <span><i class="fas fa-users"></i> ${applicationCount} application${applicationCount !== 1 ? 's' : ''}</span>
                    </div>
                    <div class="meta-row">
                        <span><i class="fas fa-money-bill-wave"></i> ${budget}</span>
                        <span><i class="fas fa-clock"></i> Deadline: ${deadline}</span>
                    </div>
                    <div class="meta-row">
                        <span><i class="fas fa-map-marker-alt"></i> ${escapeHtml(posting.location || 'Location not specified')}</span>
                        <span><i class="fas fa-calendar-plus"></i> Posted: ${formatDate(posting.created_at ?? posting.posted_date ?? posting.postedDate ?? posting.createdAt ?? posting.date_created ?? posting.dateCreated ?? '')}</span>
                    </div>
                </div>
                <div class="posting-actions">
                    ${canEdit ? `
                        <button class="action-btn-sm secondary" onclick="editJobPosting(${posting.posting_id})" title="Edit">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    ` : ''}
                    <button class="action-btn-sm primary" onclick="viewJobDetails(${posting.posting_id})" title="View Details">
                        <i class="fas fa-eye"></i> Details
                    </button>
                    ${statusActions}
                    <button class="action-btn-sm danger" onclick="deleteJobPosting(${posting.posting_id})" title="Delete">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            `;
            
            return card;
        }

        // Helper function to escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        async function editJobPosting(postingId) {
            try {
                // Fetch the posting data from API
                const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/job-postings.php?action=get&posting_id=${postingId}`);
                const result = await response.json();
                
                if (!result.success) {
                    showNotification(result.message || 'Failed to load job posting', 'error');
                    return;
                }
                
                const posting = result.posting;
                
                // Update drawer title to indicate editing
                const drawerHeader = document.querySelector('#jobPostingDrawer .job-posting-header h3');
                if (drawerHeader) {
                    drawerHeader.innerHTML = '<i class="fas fa-edit"></i> Edit Job Posting';
                }
                
                // Populate form fields with existing data
                document.getElementById('jobTitle').value = posting.title || '';
                document.getElementById('jobCategory').value = posting.category || '';
                document.getElementById('employmentType').value = posting.employment_type || '';
                document.getElementById('jobDescription').value = posting.description || '';
                document.getElementById('minExperience').value = posting.min_experience || '';
                document.getElementById('priorityLevel').value = posting.priority_level || 'normal';
                document.getElementById('minBudget').value = posting.min_budget || '';
                document.getElementById('maxBudget').value = posting.max_budget || '';
                document.getElementById('requiredSkills').value = posting.required_skills || '';
                document.getElementById('locationRequirements').value = posting.location || '';
                
                const deadlineRaw = posting.application_deadline ?? posting.applicationDeadline;
                if (deadlineRaw) {
                    document.getElementById('applicationDeadline').value = deadlineRaw;
                }
                
                // Set checkboxes
                if (posting.notify_repairers !== undefined) {
                    document.getElementById('notifyRepairers').checked = posting.notify_repairers == 1;
                }
                if (posting.allow_direct_applications !== undefined) {
                    document.getElementById('allowDirectApplications').checked = posting.allow_direct_applications == 1;
                }
                
                // Store the job ID for updating
                document.getElementById('jobPostingForm').dataset.editingId = postingId;
                
                // Open the drawer
                openJobPostingModal();
                
            } catch (error) {
                console.error('Error loading job posting:', error);
                showNotification('Failed to load job posting for editing', 'error');
            }
        }

        function viewApplications(jobId, jobTitle) {
            // Get applications for this specific job posting
            // In a real app, you would filter applications by jobId from the server
            // For now, we'll show all applications as sample data
            const jobApplications = applicationsData.filter(app => {
                // Mock: assign applications to jobs randomly for demo
                // In production, applications would have a jobId field
                return true; // Show all for demo purposes
            });

            // Update drawer title with job title
            document.getElementById('jobApplicationsTitle').textContent = jobTitle;
            document.getElementById('jobApplicationsCount').textContent = `${jobApplications.length} Application${jobApplications.length !== 1 ? 's' : ''}`;
            
            // Populate applications list
            const container = document.getElementById('jobApplicationsList');
            container.innerHTML = '';
            
            if (jobApplications.length === 0) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: var(--text-secondary);">
                        <i class="fas fa-inbox" style="font-size: 48px; opacity: 0.3; margin-bottom: 16px;"></i>
                        <p>No applications received yet for this job posting.</p>
                    </div>
                `;
            } else {
                jobApplications.forEach(application => {
                    const appCard = createJobApplicationCard(application);
                    container.appendChild(appCard);
                });
            }
            
            // Show drawer
            document.getElementById('viewApplicationsDrawer').classList.add('active');
        }

        function createJobApplicationCard(application) {
            const card = document.createElement('div');
            card.className = 'job-application-card';
            
            const statusBadge = application.status === 'new' 
                ? '<span class="status-badge new">New</span>'
                : '<span class="status-badge pending">Pending Review</span>';
            
            card.innerHTML = `
                <div class="job-app-header">
                    <div class="job-app-avatar">${application.avatar}</div>
                    <div class="job-app-info">
                        <h4>${application.firstName} ${application.lastName}</h4>
                        <span class="job-app-specialty">${application.specialty}</span>
                        <div class="job-app-meta">
                            <span><i class="fas fa-calendar"></i> ${application.experience} years exp</span>
                            <span><i class="fas fa-money-bill"></i> LKR ${application.expectedRate.toLocaleString()}/hr</span>
                        </div>
                    </div>
                    ${statusBadge}
                </div>
                <div class="job-app-contact">
                    <span><i class="fas fa-envelope"></i> ${application.email}</span>
                    <span><i class="fas fa-phone"></i> ${application.phone}</span>
                    <span><i class="fas fa-clock"></i> Applied ${formatDate(application.applicationDate)}</span>
                </div>
                ${application.coverLetter ? `
                    <div class="job-app-cover-letter">
                        <strong><i class="fas fa-file-alt"></i> Cover Letter:</strong>
                        <p>${application.coverLetter.substring(0, 150)}${application.coverLetter.length > 150 ? '...' : ''}</p>
                    </div>
                ` : ''}
                <div class="job-app-actions">
                    <button class="action-btn-sm info" onclick="viewApplicationDetailsFromJobDrawer('${application.id}')">
                        <i class="fas fa-eye"></i> View Details
                    </button>
                    <button class="action-btn-sm success" onclick="approveApplicationFromList('${application.id}')">
                        <i class="fas fa-check"></i> Accept
                    </button>
                    <button class="action-btn-sm danger" onclick="rejectApplicationFromList('${application.id}')">
                        <i class="fas fa-times"></i> Decline
                    </button>
                </div>
            `;
            
            return card;
        }

        function viewApplicationDetailsFromJobDrawer(applicationId) {
            // Close the job applications drawer first
            closeViewApplicationsDrawer();
            
            // Switch to the applications section
            const applicationsCard = document.querySelector('[data-section="applications"]');
            if (applicationsCard) {
                // Remove active class from all preview cards
                document.querySelectorAll('.preview-card').forEach(card => {
                    card.classList.remove('active');
                });
                
                // Add active class to applications card
                applicationsCard.classList.add('active');
                
                // Hide all sections
                document.querySelectorAll('.section-content').forEach(section => {
                    section.classList.remove('active');
                });
                
                // Show applications section
                const applicationsSection = document.getElementById('applications');
                if (applicationsSection) {
                    applicationsSection.classList.add('active');
                }
            }
            
            // Wait a moment for the transition, then open the application details
            setTimeout(() => {
                viewApplicationDetails(applicationId);
            }, 300);
        }

        function closeViewApplicationsDrawer() {
            document.getElementById('viewApplicationsDrawer').classList.remove('active');
        }

        function approveApplicationFromList(applicationId) {
            approveApplication(applicationId);
            // Refresh the applications list after a short delay
            setTimeout(() => {
                const jobTitle = document.getElementById('jobApplicationsTitle').textContent;
                const jobId = 1; // You would track this properly in production
                viewApplications(jobId, jobTitle);
            }, 500);
        }

        function rejectApplicationFromList(applicationId) {
            rejectApplication(applicationId);
            // Refresh the applications list after a short delay
            setTimeout(() => {
                const jobTitle = document.getElementById('jobApplicationsTitle').textContent;
                const jobId = 1; // You would track this properly in production
                viewApplications(jobId, jobTitle);
            }, 500);
        }

        async function deleteJobPosting(postingId) {
            if (!confirm('Are you sure you want to delete this job posting? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/job-postings.php?posting_id=${postingId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showNotification('Job posting deleted successfully', 'success');
                    
                    // Reload job postings
                    const companyId = <?php echo $_SESSION['user_id'] ?? 0; ?>;
                    await loadJobPostings(companyId);
                } else {
                    showNotification(result.message || 'Failed to delete job posting', 'error');
                }
            } catch (error) {
                console.error('Error deleting job posting:', error);
                showNotification('Failed to delete job posting', 'error');
            }
        }

        async function changeJobStatus(postingId, newStatus) {
            const statusLabels = {
                'open': 'publish',
                'closed': 'close',
                'filled': 'mark as filled'
            };
            
            const action = statusLabels[newStatus] || 'update';
            
            if (!confirm(`Are you sure you want to ${action} this job posting?`)) {
                return;
            }

            try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/job-postings.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        posting_id: postingId,
                        action: 'status',
                        status: newStatus
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showNotification(`Job posting ${action}ed successfully`, 'success');
                    
                    // Reload job postings
                    const companyId = <?php echo $_SESSION['user_id'] ?? 0; ?>;
                    await loadJobPostings(companyId);
                } else {
                    showNotification(result.message || 'Failed to update job status', 'error');
                }
            } catch (error) {
                console.error('Error updating job status:', error);
                showNotification('Failed to update job status', 'error');
            }
        }

        async function viewJobDetails(postingId) {
            try {
                // Fetch the posting data from API
                const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/job-postings.php?action=get&posting_id=${postingId}`);
                const result = await response.json();
                
                if (!result.success) {
                    showNotification(result.message || 'Failed to load job details', 'error');
                    return;
                }
                
                const posting = result.posting;
                
                // Format the details for display
                const statusConfig = {
                    'open': { class: 'success', label: 'Open' },
                    'draft': { class: 'warning', label: 'Draft' },
                    'closed': { class: 'danger', label: 'Closed' },
                    'filled': { class: 'info', label: 'Filled' }
                };
                
                const status = statusConfig[posting.status] || statusConfig['draft'];
                
                // Show a modal with full details
                const modalHtml = `
                    <div class="modal-overlay" id="jobDetailsModal" onclick="if(event.target === this) closeJobDetailsModal()">
                        <div class="modal-content large">
                            <div class="modal-header">
                                <h3><i class="fas fa-info-circle"></i> Job Posting Details</h3>
                                <button class="close-btn" onclick="closeJobDetailsModal()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="detail-section">
                                    <div class="detail-header">
                                        <h4>${escapeHtml(posting.title)}</h4>
                                        <span class="badge badge-${status.class}">${status.label}</span>
                                    </div>
                                    
                                    <div class="detail-grid">
                                        <div class="detail-item">
                                            <label><i class="fas fa-tag"></i> Category</label>
                                            <span>${escapeHtml(posting.category)}</span>
                                        </div>
                                        <div class="detail-item">
                                            <label><i class="fas fa-briefcase"></i> Employment Type</label>
                                            <span>${escapeHtml(posting.employment_type || 'Not specified')}</span>
                                        </div>
                                        <div class="detail-item">
                                            <label><i class="fas fa-star"></i> Experience Level</label>
                                            <span>${(posting.min_experience === 0 || posting.min_experience === '0')
                                                ? 'No experience required'
                                                : (posting.min_experience ? escapeHtml(`${posting.min_experience}+ years`) : 'Not specified')}
                                            </span>
                                        </div>
                                        <div class="detail-item">
                                            <label><i class="fas fa-exclamation-circle"></i> Priority</label>
                                            <span>${escapeHtml(posting.priority_level || 'Normal')}</span>
                                        </div>
                                        <div class="detail-item">
                                            <label><i class="fas fa-money-bill-wave"></i> Budget Range</label>
                                            <span>LKR ${posting.min_budget?.toLocaleString() || 0} - ${posting.max_budget?.toLocaleString() || 0}/hr</span>
                                        </div>
                                        <div class="detail-item">
                                            <label><i class="fas fa-clock"></i> Application Deadline</label>
                                            <span>${(posting.application_deadline || posting.applicationDeadline) ? formatDate(posting.application_deadline || posting.applicationDeadline) : 'No deadline'}</span>
                                        </div>
                                        <div class="detail-item full-width">
                                            <label><i class="fas fa-map-marker-alt"></i> Location</label>
                                            <span>${escapeHtml(posting.location || 'Not specified')}</span>
                                        </div>
                                        <div class="detail-item full-width">
                                            <label><i class="fas fa-align-left"></i> Description</label>
                                            <p>${escapeHtml(posting.description || 'No description provided')}</p>
                                        </div>
                                        <div class="detail-item full-width">
                                            <label><i class="fas fa-tools"></i> Required Skills</label>
                                            <p>${escapeHtml(posting.required_skills || 'No specific skills listed')}</p>
                                        </div>
                                        <div class="detail-item">
                                            <label><i class="fas fa-bell"></i> Notify Repairers</label>
                                            <span>${posting.notify_repairers == 1 ? 'Yes' : 'No'}</span>
                                        </div>
                                        <div class="detail-item">
                                            <label><i class="fas fa-paper-plane"></i> Direct Applications</label>
                                            <span>${posting.allow_direct_applications == 1 ? 'Allowed' : 'Not allowed'}</span>
                                        </div>
                                        <div class="detail-item">
                                            <label><i class="fas fa-calendar-plus"></i> Posted</label>
                                            <span>${formatDate(posting.created_at)}</span>
                                        </div>
                                        <div class="detail-item">
                                            <label><i class="fas fa-calendar-check"></i> Last Updated</label>
                                            <span>${posting.updated_at ? formatDate(posting.updated_at) : 'Never'}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn-secondary" onclick="closeJobDetailsModal()">Close</button>
                                ${posting.status !== 'open' ? `<button class="btn-primary" onclick="closeJobDetailsModal(); editJobPosting(${postingId})">Edit</button>` : ''}
                            </div>
                        </div>
                    </div>
                `;
                
                // Append modal to body
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                
            } catch (error) {
                console.error('Error loading job details:', error);
                showNotification('Failed to load job details', 'error');
            }
        }

        function closeJobDetailsModal() {
            const modal = document.getElementById('jobDetailsModal');
            if (modal) {
                modal.remove();
            }
        }

        function addEmployee() {
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const specialty = document.getElementById('specialty').value;
            const experience = document.getElementById('experience').value;
            const hourlyRate = document.getElementById('hourlyRate').value;
            const employmentType = document.getElementById('employmentType').value;

            if (!firstName || !lastName || !email || !phone || !specialty || !experience || !hourlyRate || !employmentType) {
                showNotification('Please fill all required fields', 'error');
                return;
            }

            // Create new employee object
            const newEmployee = {
                id: 'EMP' + String(Date.now()).slice(-3),
                firstName,
                lastName,
                email,
                phone,
                specialty,
                experience: parseInt(experience),
                hourlyRate: parseInt(hourlyRate),
                employmentType,
                status: 'active',
                currentJobs: 0,
                completedJobs: 0,
                rating: 0,
                address: document.getElementById('address').value || 'Not specified',
                joinDate: new Date().toISOString().split('T')[0],
                avatar: firstName.charAt(0) + lastName.charAt(0)
            };

            // Add to employees data (you would normally send this to server)
            console.log('New employee added:', newEmployee);

            closeAddEmployeeDrawer();
            showNotification('Employee added successfully!', 'success');

            // Refresh display if needed
            updateStats();
        }

        // Utility functions
        function updateStats() {
            // TODO: Update stats from real API/database data
            // Mock data has been removed - implement API calls here
            console.log('Update stats from database');
        }

        function getStatusClass(status) {
            const classes = {
                'active': 'status-active',
                'busy': 'status-busy',
                'available': 'status-available',
                'offline': 'status-offline',
                'pending': 'status-pending'
            };
            return classes[status] || 'status-default';
        }

        // Helper functions for freelancer assignment status
        function getFreelancerStatusClass(status) {
            const statusLower = status.toLowerCase().replace(/\s+/g, '-');
            const classes = {
                'available': 'status-available',
                'assigned': 'status-assigned',
                'work-completed': 'status-work-completed',
                'payment-pending': 'status-payment-pending',
                'busy': 'status-busy'
            };
            return classes[statusLower] || 'status-default';
        }

        function getAssignmentStatusClass(workStatus, paymentStatus) {
            if (workStatus === 'completed' && paymentStatus === 'payment-due') {
                return 'assignment-status-payment-due';
            }
            if (workStatus === 'completed' && paymentStatus === 'processing') {
                return 'assignment-status-processing';
            }
            if (workStatus === 'in-progress') {
                return 'assignment-status-in-progress';
            }
            if (workStatus === 'pending') {
                return 'assignment-status-pending';
            }
            return 'assignment-status-default';
        }

        function getAssignmentStatusText(workStatus, paymentStatus) {
            if (workStatus === 'completed' && paymentStatus === 'payment-due') {
                return 'Payment Due';
            }
            if (workStatus === 'completed' && paymentStatus === 'processing') {
                return 'Processing Payment';
            }
            if (workStatus === 'completed' && paymentStatus === 'paid') {
                return 'Paid';
            }
            if (workStatus === 'in-progress') {
                return 'Work in Progress';
            }
            if (workStatus === 'pending') {
                return 'Not Started';
            }
            return 'Unknown';
        }

        function getStatusIcon(workStatus, paymentStatus) {
            if (workStatus === 'completed' && paymentStatus === 'payment-due') {
                return 'fa-exclamation-circle';
            }
            if (workStatus === 'completed' && paymentStatus === 'processing') {
                return 'fa-spinner fa-spin';
            }
            if (workStatus === 'completed' && paymentStatus === 'paid') {
                return 'fa-check-circle';
            }
            if (workStatus === 'in-progress') {
                return 'fa-hourglass-half';
            }
            return 'fa-clock';
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }

        // Updated for drawer system - no longer needed
        // function closeModal(modalId) {
        //     document.getElementById(modalId).classList.remove('active');
        // }

        function showEmptyState() {
            document.getElementById('emptyState').style.display = 'block';
        }

        function hideEmptyState() {
            document.getElementById('emptyState').style.display = 'none';
        }

        function showNotification(message, type) {
            // Remove existing notifications
            document.querySelectorAll('.notification').forEach(n => n.remove());

            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }

        // ====================================
        // BULK STAFF MANAGEMENT FUNCTIONS
        // ====================================

        let staffRowCounter = 0;

        function openBulkStaffModal() {
            const drawer = document.getElementById('bulkStaffDrawer');
            drawer.classList.add('active');
            
            // Initialize with one row if empty
            const container = document.getElementById('staffRowsContainer');
            if (container.children.length === 0) {
                addStaffRow();
            }
            
            // Focus first select
            setTimeout(() => {
                const firstSelect = container.querySelector('.skill-category-select');
                if (firstSelect) firstSelect.focus();
            }, 300);
        }

        function closeBulkStaffDrawer() {
            document.getElementById('bulkStaffDrawer').classList.remove('active');
            // Reset form
            document.getElementById('bulkStaffForm').reset();
            document.getElementById('staffRowsContainer').innerHTML = '';
            document.getElementById('uploadedFiles').innerHTML = '';
            staffRowCounter = 0;
            updateBulkStaffSummary();
        }

        function addStaffRow() {
            staffRowCounter++;
            const container = document.getElementById('staffRowsContainer');

            const staffRow = document.createElement('div');
            staffRow.className = 'staff-row';
            staffRow.id = `staffRow${staffRowCounter}`;
            staffRow.style.opacity = '0';
            staffRow.style.transform = 'translateY(20px)';

            staffRow.innerHTML = `
                <div class="staff-row-content">
                    <div class="skill-category-group">
                        <label>Skill Category</label>
                        <select class="skill-category-select" onchange="updateBulkStaffSummary()">
                            <option value="">Select skill category</option>
                            <option value="Carpenter">Carpenter</option>
                            <option value="Electrician">Electrician</option>
                            <option value="Plumber">Plumber</option>
                            <option value="Painter">Painter</option>
                            <option value="HVAC Technician">HVAC Technician</option>
                            <option value="Mechanic">Mechanic</option>
                            <option value="Welder">Welder</option>
                            <option value="Mason">Mason</option>
                            <option value="Roofer">Roofer</option>
                            <option value="Landscaper">Landscaper</option>
                            <option value="General Labor">General Labor</option>
                        </select>
                    </div>
                    
                    <div class="quantity-group">
                        <label>Number of Staff</label>
                        <div class="quantity-controls">
                            <button type="button" class="quantity-btn minus" onclick="changeQuantity(${staffRowCounter}, -1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="quantity-input" value="1" min="1" max="99" 
                                   id="quantity${staffRowCounter}" onchange="updateBulkStaffSummary()">
                            <button type="button" class="quantity-btn plus" onclick="changeQuantity(${staffRowCounter}, 1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="row-actions">
                        <button type="button" class="action-btn-sm danger" onclick="removeStaffRow(${staffRowCounter})" 
                                ${staffRowCounter === 1 ? 'style="display: none;"' : ''}>
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;

            container.appendChild(staffRow);
            
            // Animate in
            setTimeout(() => {
                staffRow.style.transition = 'all 0.3s ease-out';
                staffRow.style.opacity = '1';
                staffRow.style.transform = 'translateY(0)';
            }, 10);
            
            updateBulkStaffSummary();

            // Show remove button for all rows if there's more than one
            updateRemoveButtons();
        }

        function removeStaffRow(rowId) {
            const row = document.getElementById(`staffRow${rowId}`);
            if (row) {
                // Animate out
                row.style.transition = 'all 0.3s ease-out';
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                
                setTimeout(() => {
                    row.remove();
                    updateBulkStaffSummary();
                    updateRemoveButtons();
                }, 300);
            }
        }

        function changeQuantity(rowId, change) {
            const quantityInput = document.getElementById(`quantity${rowId}`);
            if (quantityInput) {
                let currentValue = parseInt(quantityInput.value) || 1;
                currentValue = Math.max(1, Math.min(99, currentValue + change));
                quantityInput.value = currentValue;
                updateBulkStaffSummary();
            }
        }

        function updateRemoveButtons() {
            const staffRows = document.querySelectorAll('.staff-row');
            
            // Update ALL remove buttons in staff rows
            staffRows.forEach((row, index) => {
                const removeButton = row.querySelector('.action-btn-sm.danger');
                if (removeButton) {
                    // Show button if there's more than one row
                    removeButton.style.display = staffRows.length > 1 ? 'flex' : 'none';
                }
            });
        }

        function updateBulkStaffSummary() {
            const staffRows = document.querySelectorAll('.staff-row');
            let totalCategories = 0;
            let totalStaff = 0;

            staffRows.forEach(row => {
                const skillSelect = row.querySelector('.skill-category-select');
                const quantityInput = row.querySelector('.quantity-input');

                if (skillSelect && skillSelect.value) {
                    totalCategories++;
                }

                if (quantityInput && quantityInput.value) {
                    totalStaff += parseInt(quantityInput.value) || 0;
                }
            });

            const totalCategoriesEl = document.getElementById('totalCategories');
            const totalStaffEl = document.getElementById('totalStaff');
            
            // Animate the number change
            if (totalCategoriesEl.textContent !== totalCategories.toString()) {
                totalCategoriesEl.textContent = totalCategories;
                totalCategoriesEl.classList.add('pulse');
                setTimeout(() => totalCategoriesEl.classList.remove('pulse'), 500);
            }
            
            if (totalStaffEl.textContent !== totalStaff.toString()) {
                totalStaffEl.textContent = totalStaff;
                totalStaffEl.classList.add('pulse');
                setTimeout(() => totalStaffEl.classList.remove('pulse'), 500);
            }
        }

        function saveBulkStaff() {
            const staffRows = document.querySelectorAll('.staff-row');
            const staffData = [];
            let isValid = true;
            const selectedCategories = new Set();

            staffRows.forEach(row => {
                const skillSelect = row.querySelector('.skill-category-select');
                const quantityInput = row.querySelector('.quantity-input');

                if (!skillSelect.value) {
                    showNotification('Please select a skill category for all rows', 'error');
                    skillSelect.focus();
                    isValid = false;
                    return;
                }

                // Check for duplicate categories
                if (selectedCategories.has(skillSelect.value)) {
                    showNotification(`Duplicate category "${skillSelect.value}" detected. Please combine or remove duplicates.`, 'error');
                    skillSelect.focus();
                    isValid = false;
                    return;
                }
                selectedCategories.add(skillSelect.value);

                if (!quantityInput.value || parseInt(quantityInput.value) < 1) {
                    showNotification('Please enter a valid quantity for all rows', 'error');
                    quantityInput.focus();
                    isValid = false;
                    return;
                }

                staffData.push({
                    skillCategory: skillSelect.value,
                    quantity: parseInt(quantityInput.value)
                });
            });

            if (!isValid) return;

            if (staffData.length === 0) {
                showNotification('Please add at least one staff category', 'error');
                return;
            }

            // Get uploaded files
            const fileInput = document.getElementById('verificationDocs');
            const files = fileInput.files;

            // Show loading state
            const saveBtn = event.target;
            saveBtn.classList.add('loading');
            saveBtn.disabled = true;

            // Transform staffData to employee format for API
            const employees = [];
            staffData.forEach(item => {
                for (let i = 0; i < item.quantity; i++) {
                    employees.push({
                        first_name: `Employee`,
                        last_name: `${item.skillCategory} ${i + 1}`,
                        specialty: item.skillCategory,
                        hourly_rate: 2500.00, // Default rate
                        status: 'active'
                    });
                }
            });

            // Add staff via API
            bulkAddStaffViaAPI(employees)
                .then(() => {
                    saveBtn.classList.remove('loading');
                    saveBtn.disabled = false;
                    
                    const totalStaff = staffData.reduce((sum, item) => sum + item.quantity, 0);
                    showNotification(`Successfully added ${totalStaff} staff members across ${staffData.length} categories!`, 'success');
                    closeBulkStaffDrawer();
                    
                    // Reload page to refresh statistics
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                })
                .catch(error => {
                    saveBtn.classList.remove('loading');
                    saveBtn.disabled = false;
                    showNotification('Failed to add staff: ' + error, 'error');
                });
        }

        // Function to bulk add staff via API
        async function bulkAddStaffViaAPI(employees) {
            const companyId = window.CURRENT_COMPANY_ID;
            const apiUrl = '/2nd-Year-Group-Project/FixLanka/api/company-employees.php';
            
            try {
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        bulk: true,
                        company_id: companyId,
                        employees: employees
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    return true;
                } else {
                    throw new Error(result.message || 'Failed to add employees');
                }
                
            } catch (error) {
                console.error('Error bulk adding employees:', error);
                throw error.message || 'Failed to add employees';
            }
        }

        // File upload handling
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('verificationDocs');
            const uploadArea = document.querySelector('.file-upload-area');
            const uploadedFilesContainer = document.getElementById('uploadedFiles');

            if (fileInput && uploadArea) {
                // Click to upload
                uploadArea.addEventListener('click', () => {
                    fileInput.click();
                });

                // Drag and drop
                uploadArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    uploadArea.classList.add('drag-over');
                });

                uploadArea.addEventListener('dragleave', () => {
                    uploadArea.classList.remove('drag-over');
                });

                uploadArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    uploadArea.classList.remove('drag-over');
                    const files = e.dataTransfer.files;
                    handleFileUpload(files);
                });

                // File input change
                fileInput.addEventListener('change', (e) => {
                    handleFileUpload(e.target.files);
                });
            }
        });

        function handleFileUpload(files) {
            const uploadedFilesContainer = document.getElementById('uploadedFiles');
            uploadedFilesContainer.innerHTML = '';

            Array.from(files).forEach(file => {
                const fileItem = document.createElement('div');
                fileItem.className = 'uploaded-file-item';

                const fileIcon = getFileIcon(file.name);
                const fileSize = formatFileSize(file.size);

                fileItem.innerHTML = `
                    <div class="file-info">
                        <i class="fas ${fileIcon}"></i>
                        <div class="file-details">
                            <span class="file-name">${file.name}</span>
                            <span class="file-size">${fileSize}</span>
                        </div>
                    </div>
                    <button type="button" class="remove-file-btn" onclick="removeFile(this)">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                uploadedFilesContainer.appendChild(fileItem);
            });
        }

        function getFileIcon(fileName) {
            const extension = fileName.split('.').pop().toLowerCase();
            switch (extension) {
                case 'pdf': return 'fa-file-pdf';
                case 'doc':
                case 'docx': return 'fa-file-word';
                case 'jpg':
                case 'jpeg':
                case 'png': return 'fa-file-image';
                default: return 'fa-file';
            }
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function removeFile(button) {
            button.parentElement.remove();
        }

        // ====================================
        // STAFF REDUCTION MANAGEMENT FUNCTIONS
        // ====================================

        // Mock current staff data - in real app this would come from server
        // Store current staff data from API
        let currentStaffData = {};

        let reductionRowCounter = 0;

        function openReduceStaffModal() {
            const drawer = document.getElementById('reduceStaffDrawer');
            drawer.classList.add('active');
            loadCurrentStaffOverview();
            
            // Initialize with one row if empty
            const container = document.getElementById('reductionRowsContainer');
            if (container.children.length === 0) {
                addReductionRow();
            }
            
            // Focus first select
            setTimeout(() => {
                const firstSelect = container.querySelector('.reduction-category-select');
                if (firstSelect) firstSelect.focus();
            }, 300);
        }

        function closeReduceStaffDrawer() {
            document.getElementById('reduceStaffDrawer').classList.remove('active');
            // Reset form
            document.getElementById('reduceStaffForm').reset();
            document.getElementById('reductionRowsContainer').innerHTML = '';
            document.getElementById('reductionUploadedFiles').innerHTML = '';
            reductionRowCounter = 0;
            updateReductionSummary();
        }

        async function loadCurrentStaffOverview() {
            const container = document.getElementById('currentStaffOverview');
            container.innerHTML = '<p style="text-align: center; padding: 20px;">Loading...</p>';
            
            try {
                // Get statistics from API
                const companyId = window.CURRENT_COMPANY_ID;
                const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/company-employees.php?action=stats&company_id=${companyId}`);
                const data = await response.json();
                
                if (!data.specialties || data.specialties.length === 0) {
                    container.innerHTML = '<p style="text-align: center; padding: 20px; color: #999;">No employees found</p>';
                    currentStaffData = {}; // Clear the data
                    return;
                }
                
                // Update currentStaffData from API
                currentStaffData = {};
                data.specialties.forEach(specialty => {
                    currentStaffData[specialty.specialty] = {
                        current: specialty.total_count || 0,
                        active: specialty.active_count || 0
                    };
                });
                
                let html = '<div class="current-staff-grid">';
                
                data.specialties.forEach(specialty => {
                    html += `
                        <div class="current-staff-item">
                            <div class="staff-category-name">${specialty.specialty}</div>
                            <div class="staff-counts">
                                <span class="current-count">${specialty.total_count || 0} Total</span>
                                <span class="active-count">${specialty.active_count || 0} Active</span>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                container.innerHTML = html;
                
            } catch (error) {
                console.error('Error loading staff overview:', error);
                container.innerHTML = '<p style="text-align: center; padding: 20px; color: red;">Error loading data</p>';
                currentStaffData = {}; // Clear the data on error
            }
        }

        async function addReductionRow() {
            reductionRowCounter++;
            const container = document.getElementById('reductionRowsContainer');
            
            // Fetch current staff data from API
            let staffOptions = '';
            try {
                const companyId = window.CURRENT_COMPANY_ID;
                const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/company-employees.php?action=stats&company_id=${companyId}`);
                const data = await response.json();
                
                if (data.specialties && data.specialties.length > 0) {
                    staffOptions = data.specialties.map(specialty => 
                        `<option value="${specialty.specialty}" data-current="${specialty.total_count}" data-active="${specialty.active_count}">${specialty.specialty} (${specialty.total_count} current)</option>`
                    ).join('');
                }
            } catch (error) {
                console.error('Error fetching staff data:', error);
                staffOptions = '<option value="">Error loading categories</option>';
            }
            
            const reductionRow = document.createElement('div');
            reductionRow.className = 'reduction-row';
            reductionRow.id = `reductionRow${reductionRowCounter}`;
            reductionRow.style.opacity = '0';
            reductionRow.style.transform = 'translateY(20px)';
            
            reductionRow.innerHTML = `
                <div class="reduction-row-content">
                    <div class="skill-category-group">
                        <label>Skill Category</label>
                        <select class="reduction-category-select" onchange="updateReductionMaxAndSummary(${reductionRowCounter})" data-row-id="${reductionRowCounter}">
                            <option value="">Select skill category</option>
                            ${staffOptions}
                        </select>
                    </div>
                    
                    <div class="reduction-quantity-group">
                        <label>Number to Reduce</label>
                        <div class="quantity-controls">
                            <button type="button" class="quantity-btn minus" onclick="changeReductionQuantity(${reductionRowCounter}, -1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="reduction-quantity-input" value="1" min="1" max="1" 
                                   id="reductionQuantity${reductionRowCounter}" onchange="updateReductionSummary()" readonly>
                            <button type="button" class="quantity-btn plus" onclick="changeReductionQuantity(${reductionRowCounter}, 1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="reduction-info">
                            <span class="max-reduction" id="maxReduction${reductionRowCounter}">
                                <i class="fas fa-info-circle"></i> Max: 0
                            </span>
                        </div>
                    </div>
                    
                    <div class="row-actions">
                        <button type="button" class="action-btn-sm danger" onclick="removeReductionRow(${reductionRowCounter})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            
            container.appendChild(reductionRow);
            
            // Animate in
            setTimeout(() => {
                reductionRow.style.transition = 'all 0.3s ease-out';
                reductionRow.style.opacity = '1';
                reductionRow.style.transform = 'translateY(0)';
            }, 10);
            
            updateReductionSummary();
            updateReductionRemoveButtons();
        }

        function removeReductionRow(rowId) {
            const row = document.getElementById(`reductionRow${rowId}`);
            if (row) {
                // Animate out
                row.style.transition = 'all 0.3s ease-out';
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                
                setTimeout(() => {
                    row.remove();
                    updateReductionSummary();
                    updateReductionRemoveButtons();
                }, 300);
            }
        }

        function changeReductionQuantity(rowId, change) {
            const quantityInput = document.getElementById(`reductionQuantity${rowId}`);
            const categorySelect = document.querySelector(`#reductionRow${rowId} .reduction-category-select`);
            const minusBtn = document.querySelector(`#reductionRow${rowId} .quantity-btn.minus`);
            const plusBtn = document.querySelector(`#reductionRow${rowId} .quantity-btn.plus`);
            
            if (quantityInput && categorySelect.value) {
                const selectedOption = categorySelect.selectedOptions[0];
                const maxReduction = parseInt(selectedOption.dataset.current) || 0;
                
                let currentValue = parseInt(quantityInput.value) || 1;
                currentValue = Math.max(1, Math.min(maxReduction, currentValue + change));
                quantityInput.value = currentValue;
                
                // Update button states
                if (minusBtn) minusBtn.disabled = currentValue <= 1;
                if (plusBtn) plusBtn.disabled = currentValue >= maxReduction;
                
                updateReductionSummary();
            }
        }

        function updateReductionRemoveButtons() {
            const reductionRows = document.querySelectorAll('.reduction-row');
            
            // Update ALL remove buttons in reduction rows
            reductionRows.forEach((row, index) => {
                const removeButton = row.querySelector('.action-btn-sm.danger');
                if (removeButton) {
                    // Show button if there's more than one row
                    removeButton.style.display = reductionRows.length > 1 ? 'flex' : 'none';
                }
            });
        }

        function updateReductionMaxAndSummary(rowId) {
            // Update the max value for the specific row when category changes
            const row = document.getElementById(`reductionRow${rowId}`);
            if (row) {
                const categorySelect = row.querySelector('.reduction-category-select');
                const quantityInput = row.querySelector('.reduction-quantity-input');
                const maxReductionSpan = row.querySelector('.max-reduction');
                
                if (categorySelect && categorySelect.value) {
                    const selectedOption = categorySelect.selectedOptions[0];
                    const currentStaff = parseInt(selectedOption.dataset.current) || 0;
                    
                    // Update max display
                    maxReductionSpan.innerHTML = `<i class="fas fa-info-circle"></i> Max: ${currentStaff}`;
                    
                    // Update input constraints
                    quantityInput.max = currentStaff;
                    quantityInput.value = 1; // Reset to 1 when category changes
                }
            }
            
            // Update the overall summary
            updateReductionSummary();
        }

        function updateReductionSummary() {
            const reductionRows = document.querySelectorAll('.reduction-row');
            let totalCategories = 0;
            let totalReduction = 0;
            let totalRemaining = 0;
            const usedCategories = new Set();
            
            reductionRows.forEach(row => {
                const categorySelect = row.querySelector('.reduction-category-select');
                const quantityInput = row.querySelector('.reduction-quantity-input');
                const maxReductionSpan = row.querySelector('.max-reduction');
                
                if (categorySelect && categorySelect.value) {
                    totalCategories++;
                    usedCategories.add(categorySelect.value);
                    
                    const selectedOption = categorySelect.selectedOptions[0];
                    const currentStaff = parseInt(selectedOption.dataset.current) || 0;
                    const reduction = parseInt(quantityInput.value) || 0;
                    
                    totalReduction += reduction;
                    
                    // Update max reduction display with icon
                    maxReductionSpan.innerHTML = `<i class="fas fa-info-circle"></i> Max: ${currentStaff}`;
                    
                    // Update quantity input max attribute
                    quantityInput.max = currentStaff;
                    
                    // Validate current value doesn't exceed max
                    if (quantityInput.value > currentStaff) {
                        quantityInput.value = currentStaff;
                    }
                    
                    // Visual feedback for input validation
                    if (reduction >= currentStaff) {
                        quantityInput.classList.add('at-max');
                    } else {
                        quantityInput.classList.remove('at-max');
                    }
                } else {
                    // Reset when no category selected
                    maxReductionSpan.innerHTML = `<i class="fas fa-info-circle"></i> Max: 0`;
                    quantityInput.max = 1;
                    quantityInput.value = 1;
                    quantityInput.classList.remove('at-max');
                }
            });
            
            // Calculate total remaining staff across all categories
            Object.values(currentStaffData).forEach(data => {
                totalRemaining += data.current;
            });
            totalRemaining -= totalReduction;
            
            // Update with animation
            const totalCategoriesEl = document.getElementById('totalReductionCategories');
            const totalReductionEl = document.getElementById('totalStaffReduction');
            const totalRemainingEl = document.getElementById('totalRemainingStaff');
            
            if (totalCategoriesEl.textContent !== totalCategories.toString()) {
                totalCategoriesEl.textContent = totalCategories;
                totalCategoriesEl.classList.add('pulse');
                setTimeout(() => totalCategoriesEl.classList.remove('pulse'), 500);
            }
            
            if (totalReductionEl.textContent !== totalReduction.toString()) {
                totalReductionEl.textContent = totalReduction;
                totalReductionEl.classList.add('pulse');
                setTimeout(() => totalReductionEl.classList.remove('pulse'), 500);
            }
            
            if (totalRemainingEl.textContent !== totalRemaining.toString()) {
                totalRemainingEl.textContent = totalRemaining;
                totalRemainingEl.classList.add('pulse');
                setTimeout(() => totalRemainingEl.classList.remove('pulse'), 500);
            }
        }

        function confirmStaffReduction() {
            const reductionRows = document.querySelectorAll('.reduction-row');
            const reductionData = [];
            let isValid = true;
            const usedCategories = new Set();
            
            reductionRows.forEach(row => {
                const categorySelect = row.querySelector('.reduction-category-select');
                const quantityInput = row.querySelector('.reduction-quantity-input');
                
                if (!categorySelect.value) {
                    showNotification('Please select a skill category for all rows', 'error');
                    categorySelect.focus();
                    isValid = false;
                    return;
                }
                
                // Check for duplicate categories
                if (usedCategories.has(categorySelect.value)) {
                    showNotification(`Duplicate category "${categorySelect.value}" detected. Please combine or remove duplicates.`, 'error');
                    categorySelect.focus();
                    isValid = false;
                    return;
                }
                usedCategories.add(categorySelect.value);
                
                const reduction = parseInt(quantityInput.value);
                const selectedOption = categorySelect.selectedOptions[0];
                const currentStaff = parseInt(selectedOption.dataset.current);
                
                if (!reduction || reduction < 1) {
                    showNotification('Please enter a valid reduction quantity for all rows', 'error');
                    quantityInput.focus();
                    isValid = false;
                    return;
                }
                
                if (reduction > currentStaff) {
                    showNotification(`Cannot reduce more staff than currently employed in ${categorySelect.value}`, 'error');
                    quantityInput.focus();
                    isValid = false;
                    return;
                }
                
                reductionData.push({
                    skillCategory: categorySelect.value,
                    reductionQuantity: reduction,
                    currentStaff: currentStaff,
                    remainingStaff: currentStaff - reduction
                });
            });
            
            if (!isValid) return;
            
            if (reductionData.length === 0) {
                showNotification('Please add at least one reduction category', 'error');
                return;
            }
            
            // Show confirmation dialog
            const totalReduction = reductionData.reduce((sum, item) => sum + item.reductionQuantity, 0);
            const confirmMessage = `Are you sure you want to reduce ${totalReduction} staff member${totalReduction > 1 ? 's' : ''} across ${reductionData.length} categor${reductionData.length > 1 ? 'ies' : 'y'}?\n\nThis action cannot be undone.`;
            
            if (confirm(confirmMessage)) {
                // Get uploaded files
                const fileInput = document.getElementById('reductionDocs');
                const files = fileInput.files;
                
                // Show loading state
                const confirmBtn = event.target;
                confirmBtn.classList.add('loading');
                confirmBtn.disabled = true;
                
                // Reduce staff via API
                reduceStaffViaAPI(reductionData)
                    .then(() => {
                        confirmBtn.classList.remove('loading');
                        confirmBtn.disabled = false;
                        
                        showNotification(`Successfully reduced ${totalReduction} staff members!`, 'success');
                        closeReduceStaffDrawer();
                        
                        // Reload page to refresh statistics
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    })
                    .catch(error => {
                        confirmBtn.classList.remove('loading');
                        confirmBtn.disabled = false;
                        showNotification('Failed to reduce staff: ' + error, 'error');
                    });
            }
        }

        // Function to reduce staff via API
        async function reduceStaffViaAPI(reductionData) {
            const companyId = window.CURRENT_COMPANY_ID;
            const apiUrl = '/2nd-Year-Group-Project/FixLanka/api/company-employees.php';
            
            try {
                // Process each reduction category
                for (const reduction of reductionData) {
                    // Get employees by specialty
                    const response = await fetch(`${apiUrl}?company_id=${companyId}&specialty=${encodeURIComponent(reduction.skillCategory)}`);
                    
                    if (!response.ok) {
                        throw new Error(`Failed to fetch ${reduction.skillCategory} employees`);
                    }
                    
                    const employees = await response.json();
                    
                    if (!employees || employees.length === 0) {
                        throw new Error(`No ${reduction.skillCategory} employees found`);
                    }
                    
                    if (employees.length < reduction.reductionQuantity) {
                        throw new Error(`Only ${employees.length} ${reduction.skillCategory} employees available`);
                    }
                    
                    // Delete the first N employees
                    const employeesToDelete = employees.slice(0, reduction.reductionQuantity);
                    
                    for (const emp of employeesToDelete) {
                        const deleteResponse = await fetch(`${apiUrl}?employee_id=${emp.employee_id}`, {
                            method: 'DELETE'
                        });
                        
                        if (!deleteResponse.ok) {
                            throw new Error(`Failed to delete employee ${emp.employee_id}`);
                        }
                    }
                }
                
                return true;
            } catch (error) {
                console.error('Error reducing staff:', error);
                throw error.message || 'Failed to reduce staff';
            }
        }

        // File upload handling for reduction docs (similar to bulk staff)
        document.addEventListener('DOMContentLoaded', function() {
            const reductionFileInput = document.getElementById('reductionDocs');
            const reductionUploadArea = document.querySelector('#reduceStaffDrawer .file-upload-area');
            const reductionUploadedFilesContainer = document.getElementById('reductionUploadedFiles');
            
            if (reductionFileInput && reductionUploadArea) {
                // Click to upload
                reductionUploadArea.addEventListener('click', () => {
                    reductionFileInput.click();
                });
                
                // Drag and drop
                reductionUploadArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    reductionUploadArea.classList.add('drag-over');
                });
                
                reductionUploadArea.addEventListener('dragleave', () => {
                    reductionUploadArea.classList.remove('drag-over');
                });
                
                reductionUploadArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    reductionUploadArea.classList.remove('drag-over');
                    const files = e.dataTransfer.files;
                    handleReductionFileUpload(files);
                });
                
                // File input change
                reductionFileInput.addEventListener('change', (e) => {
                    handleReductionFileUpload(e.target.files);
                });
            }
        });

        function handleReductionFileUpload(files) {
            const uploadedFilesContainer = document.getElementById('reductionUploadedFiles');
            uploadedFilesContainer.innerHTML = '';
            
            Array.from(files).forEach(file => {
                const fileItem = document.createElement('div');
                fileItem.className = 'uploaded-file-item';
                
                const fileIcon = getFileIcon(file.name);
                const fileSize = formatFileSize(file.size);
                
                fileItem.innerHTML = `
                    <div class="file-info">
                        <i class="fas ${fileIcon}"></i>
                        <div class="file-details">
                            <span class="file-name">${file.name}</span>
                            <span class="file-size">${fileSize}</span>
                        </div>
                    </div>
                    <button type="button" class="remove-file-btn" onclick="removeReductionFile(this)">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                
                uploadedFilesContainer.appendChild(fileItem);
            });
        }

        function removeReductionFile(button) {
            button.parentElement.remove();
        }
    </script>

    <!-- Add Employee Drawer -->
    <!-- Job Posting Drawer -->
    <div class="drawer-overlay" id="jobPostingDrawer">
        <div class="drawer-panel large-drawer">
            <div class="job-posting-header">
                <h3><i class="fas fa-bullhorn"></i> Create New Job Posting</h3>
                <button class="close-drawer" onclick="closeJobPostingDrawer()">
                    <i class="fas fa-times"></i>
                </button>
                <div class="step-progress">
                    <div class="step-item active" data-step="1">
                        <div class="step-circle">1</div>
                        <span class="step-label">Basic Info</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step-item" data-step="2">
                        <div class="step-circle">2</div>
                        <span class="step-label">Details</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step-item" data-step="3">
                        <div class="step-circle">3</div>
                        <span class="step-label">Review</span>
                    </div>
                </div>
            </div>

            <div class="job-posting-content">
                <form id="jobPostingForm" class="job-posting-form">
                    <!-- Step 1: Basic Information -->
                    <div class="form-step active" id="step-1">
                        <div class="step-content">
                            <h4>Basic Information</h4>
                            <p class="step-description">Let's start with the basics about your job posting</p>
                            
                            <div class="form-group">
                                <label>Job Title *</label>
                                <input type="text" id="jobTitle" placeholder="e.g., Senior HVAC Technician" required>
                                <small>Make it clear and specific to attract the right candidates</small>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Category *</label>
                                    <select id="jobCategory" required>
                                        <option value="">Select a category</option>
                                        <option value="plumbing">Plumbing</option>
                                        <option value="electrical">Electrical</option>
                                        <option value="hvac">HVAC</option>
                                        <option value="carpentry">Carpentry</option>
                                        <option value="roofing">Roofing</option>
                                        <option value="painting">Painting</option>
                                        <option value="general">General Repairs</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Employment Type *</label>
                                    <select id="employmentType" required>
                                        <option value="">Select type</option>
                                        <option value="freelance">Freelance</option>
                                        <option value="contract">Contract</option>
                                        <option value="part_time">Part Time</option>
                                        <option value="full_time">Full Time</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Related Project (Optional)</label>
                                <select id="relatedProject">
                                    <option value="">Select a project if applicable</option>
                                    <option value="project1">Commercial Building Renovation</option>
                                    <option value="project2">Residential Complex Maintenance</option>
                                    <option value="project3">Hospital HVAC System Upgrade</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Details -->
                    <div class="form-step" id="step-2">
                        <div class="step-content">
                            <h4>Job Details</h4>
                            <p class="step-description">Provide detailed information about the position</p>
                            
                            <div class="form-group">
                                <label>Job Description *</label>
                                <textarea id="jobDescription" rows="6" placeholder="Describe the role, responsibilities, and what the freelancer will be doing..." required></textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Minimum Experience *</label>
                                    <select id="minExperience" required>
                                        <option value="">Select experience level</option>
                                        <option value="0">Entry Level (0-1 years)</option>
                                        <option value="1">Junior (1-3 years)</option>
                                        <option value="3">Mid Level (3-5 years)</option>
                                        <option value="5">Senior (5-10 years)</option>
                                        <option value="10">Expert (10+ years)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Priority Level *</label>
                                    <select id="priorityLevel" required>
                                        <option value="">Select priority</option>
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Budget Range (LKR/hour) *</label>
                                    <div class="budget-range">
                                        <input type="number" id="minBudget" placeholder="Min" min="0" step="100" required>
                                        <span>to</span>
                                        <input type="number" id="maxBudget" placeholder="Max" min="0" step="100" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Application Deadline</label>
                                    <input type="date" id="applicationDeadline">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Required Skills & Qualifications</label>
                                <textarea id="requiredSkills" rows="4" placeholder="List specific skills, certifications, tools familiarity, etc."></textarea>
                            </div>

                            <div class="form-group">
                                <label>Location Requirements</label>
                                <input type="text" id="locationRequirements" placeholder="e.g., Colombo, Must have own transportation">
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Review -->
                    <div class="form-step" id="step-3">
                        <div class="step-content">
                            <h4>Review & Publish</h4>
                            <p class="step-description">Review your job posting before publishing</p>
                            
                            <div class="job-preview">
                                <div class="preview-header">
                                    <h5 id="previewTitle">Job Title</h5>
                                    <div class="preview-meta">
                                        <span id="previewCategory">Category</span> � 
                                        <span id="previewType">Type</span> � 
                                        <span id="previewBudget">Budget</span>
                                    </div>
                                </div>
                                
                                <div class="preview-description">
                                    <p id="previewDescription">Job description will appear here...</p>
                                </div>
                                
                                <div class="preview-requirements">
                                    <strong>Requirements:</strong>
                                    <ul id="previewRequirements"></ul>
                                </div>
                            </div>

                            <div class="posting-options">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" id="notifyRelevantRepairers" checked>
                                        Notify relevant repairers in the system
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" id="allowDirectApplications" checked>
                                        Allow direct applications through the platform
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="form-navigation">
                        <button type="button" class="nav-btn secondary" id="prevBtn" onclick="previousStep()" style="display: none;">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        <div class="nav-actions">
                            <button type="button" class="nav-btn secondary" onclick="saveAsDraft()">
                                <i class="fas fa-save"></i> Save as Draft
                            </button>
                            <button type="button" class="nav-btn primary" id="nextBtn" onclick="nextStep()">
                                Next <i class="fas fa-arrow-right"></i>
                            </button>
                            <button type="button" class="nav-btn success" id="publishBtn" onclick="publishJobPosting()" style="display: none;">
                                <i class="fas fa-bullhorn"></i> Publish Job Posting
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Freelancer Details Drawer -->
    <div class="drawer-overlay" id="freelancerDetailsDrawer">
        <div class="drawer-panel">
            <div class="drawer-header">
                <div>
                    <h3><i class="fas fa-user-tie"></i> Freelancer Profile</h3>
                    <p style="margin: 4px 0 0 0; color: var(--text-secondary); font-size: 14px;">
                        Comprehensive freelancer information
                    </p>
                </div>
                <button class="close-drawer" onclick="closeFreelancerDetailsDrawer()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="drawer-content">
                <!-- Freelancer Header Card -->
                <div class="freelancer-header-card">
                    <div class="freelancer-avatar-large" id="freelancerAvatar">??</div>
                    <div class="freelancer-header-info">
                        <h2 id="freelancerName">Freelancer Name</h2>
                        <p class="freelancer-specialty-large" id="freelancerSpecialty">Specialty</p>
                        <div class="freelancer-quick-stats">
                            <span id="freelancerStatus" class="status-badge available">Available</span>
                            <span class="quick-stat">
                                <i class="fas fa-star"></i> 
                                <strong id="freelancerRating">4.8</strong> Rating
                            </span>
                            <span class="quick-stat">
                                <i class="fas fa-money-bill"></i> 
                                <strong id="freelancerHourlyRate">LKR 0</strong>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Tabbed Content -->
                <div class="drawer-tabs">
                    <button class="drawer-tab active" data-tab="freelancer-personal">Personal</button>
                    <button class="drawer-tab" data-tab="freelancer-professional">Professional</button>
                    <button class="drawer-tab" data-tab="freelancer-work">Work History</button>
                    <button class="drawer-tab" data-tab="freelancer-contract">Contract</button>
                </div>

                <!-- Personal Information Tab -->
                <div class="drawer-tab-content active" id="freelancer-personal">
                    <div class="details-section">
                        <h4><i class="fas fa-id-card"></i> Contact Information</h4>
                        <div class="details-grid">
                            <div class="detail-item">
                                <label><i class="fas fa-envelope"></i> Email:</label>
                                <span id="freelancerEmail">-</span>
                            </div>
                            <div class="detail-item">
                                <label><i class="fas fa-phone"></i> Phone:</label>
                                <span id="freelancerPhone">-</span>
                            </div>
                            <div class="detail-item full-width">
                                <label><i class="fas fa-map-marker-alt"></i> Address:</label>
                                <span id="freelancerAddress">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Professional Information Tab -->
                <div class="drawer-tab-content" id="freelancer-professional">
                    <div class="details-section">
                        <h4><i class="fas fa-briefcase"></i> Professional Details</h4>
                        <div class="details-grid">
                            <div class="detail-item">
                                <label><i class="fas fa-tools"></i> Specialty:</label>
                                <span id="freelancerSpecialty2" class="specialty-text">-</span>
                            </div>
                            <div class="detail-item">
                                <label><i class="fas fa-calendar-alt"></i> Experience:</label>
                                <span id="freelancerExperience">-</span>
                            </div>
                            <div class="detail-item">
                                <label><i class="fas fa-star"></i> Rating:</label>
                                <span class="rating-display">
                                    <strong id="freelancerRating2">-</strong> / 5.0
                                </span>
                            </div>
                            <div class="detail-item">
                                <label><i class="fas fa-money-bill-wave"></i> Hourly Rate:</label>
                                <span id="freelancerHourlyRate2" class="rate-text">-</span>
                            </div>
                            <div class="detail-item">
                                <label><i class="fas fa-check-circle"></i> Availability:</label>
                                <span id="freelancerAvailability">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Work History Tab -->
                <div class="drawer-tab-content" id="freelancer-work">
                    <div class="details-section">
                        <h4><i class="fas fa-chart-line"></i> Work Statistics</h4>
                        <div class="stats-cards">
                            <div class="stat-card">
                                <div class="stat-icon active">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-value" id="freelancerCurrentJobs">0</span>
                                    <span class="stat-label">Active Jobs</span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon success">
                                    <i class="fas fa-check-double"></i>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-value" id="freelancerCompletedJobs">0</span>
                                    <span class="stat-label">Completed</span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon earnings">
                                    <i class="fas fa-coins"></i>
                                </div>
                                <div class="stat-info">
                                    <span class="stat-value" id="freelancerTotalEarnings">LKR 0</span>
                                    <span class="stat-label">Total Earnings</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contract Information Tab -->
                <div class="drawer-tab-content" id="freelancer-contract">
                    <div class="details-section">
                        <h4><i class="fas fa-file-contract"></i> Contract Information</h4>
                        <div class="details-grid">
                            <div class="detail-item">
                                <label><i class="fas fa-calendar-check"></i> Contract Start Date:</label>
                                <span id="freelancerContractDate">-</span>
                            </div>
                            <div class="detail-item">
                                <label><i class="fas fa-hourglass-half"></i> Contract Duration:</label>
                                <span id="freelancerContractDuration">-</span>
                            </div>
                            <div class="detail-item full-width">
                                <div class="contract-status-box">
                                    <i class="fas fa-info-circle"></i>
                                    <p>Freelancer is working on a contract basis. Contract can be renewed or terminated with proper notice.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="drawer-actions">
                    <button class="btn-cancel" onclick="closeFreelancerDetailsDrawer()">
                        <i class="fas fa-times"></i> Close
                    </button>
                    <button class="btn-primary success assign-freelancer-btn" onclick="assignFromFreelancerDrawer()">
                        <i class="fas fa-briefcase"></i> Assign to Job
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Details Drawer -->
    <div class="drawer-overlay" id="applicationDetailsDrawer">
        <div class="drawer-panel">
            <div class="drawer-header">
                <h3><i class="fas fa-file-alt"></i> Application Details</h3>
                <button class="close-drawer" onclick="closeApplicationDetailsDrawer()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="drawer-content">
                <div class="drawer-tabs">
                    <button class="drawer-tab active" data-tab="personal">Personal Info</button>
                    <button class="drawer-tab" data-tab="professional">Professional</button>
                    <button class="drawer-tab" data-tab="additional">Additional</button>
                    <button class="drawer-tab" data-tab="history">Work History</button>
                </div>

                <!-- Personal Information Tab -->
                <div class="drawer-tab-content active" id="personal">
                    <div class="details-section">
                        <h4><i class="fas fa-user"></i> Personal Information</h4>
                        <div class="details-grid">
                            <div class="detail-item">
                                <label>Full Name:</label>
                                <span id="modalFullName">-</span>
                            </div>
                            <div class="detail-item">
                                <label>Email:</label>
                                <span id="modalEmail">-</span>
                            </div>
                            <div class="detail-item">
                                <label>Phone:</label>
                                <span id="modalPhone">-</span>
                            </div>
                            <div class="detail-item">
                                <label>Application Date:</label>
                                <span id="modalApplicationDate">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Professional Information Tab -->
                <div class="drawer-tab-content" id="professional">
                    <div class="details-section">
                        <h4><i class="fas fa-briefcase"></i> Professional Details</h4>
                        <div class="details-grid">
                            <div class="detail-item">
                                <label>Specialty:</label>
                                <span id="modalSpecialty">-</span>
                            </div>
                            <div class="detail-item">
                                <label>Experience:</label>
                                <span id="modalExperience">-</span>
                            </div>
                            <div class="detail-item">
                                <label>Expected Rate:</label>
                                <span id="modalExpectedRate">-</span>
                            </div>
                            <div class="detail-item">
                                <label>Availability:</label>
                                <span id="modalAvailability">Immediate</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information Tab -->
                <div class="drawer-tab-content" id="additional">
                    <div class="details-section">
                        <h4><i class="fas fa-info-circle"></i> Additional Information</h4>
                        <div class="detail-item full-width">
                            <label>Cover Letter:</label>
                            <div class="cover-letter" id="modalCoverLetter">
                                Professional with strong background in repair services. Committed to quality work and
                                customer satisfaction.
                            </div>
                        </div>
                        <div class="detail-item full-width">
                            <label>Skills:</label>
                            <div class="skills-list" id="modalSkills">
                                <span class="skill-tag">Problem Solving</span>
                                <span class="skill-tag">Customer Service</span>
                                <span class="skill-tag">Technical Repair</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Work History Tab (Read-only) -->
                <div class="drawer-tab-content" id="history">
                    <div class="details-section">
                        <h4><i class="fas fa-history"></i> Work History</h4>
                        <div class="detail-item full-width">
                            <label>Completed Work:</label>
                            <div id="modalWorkHistory">
                                <p style="margin: 0; color: var(--text-secondary);">No history loaded.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="drawer-actions">
                    <button class="btn-cancel" onclick="closeApplicationDetailsDrawer()">
                        <i class="fas fa-times"></i> Close
                    </button>
                    <button class="btn-primary danger" onclick="rejectApplicationFromDrawer()">
                        <i class="fas fa-times-circle"></i> Decline
                    </button>
                    <button class="btn-primary success" onclick="approveApplicationFromDrawer()">
                        <i class="fas fa-check-circle"></i> Accept Application
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contract Creation Drawer -->
    <div class="drawer-overlay" id="contractCreationDrawer">
        <div class="drawer-panel large-drawer">
            <div class="drawer-header">
                <div>
                    <h3><i class="fas fa-file-contract"></i> Create Employment Contract</h3>
                    <p style="margin: 4px 0 0 0; color: var(--text-secondary); font-size: 14px;">
                        Prepare contract for <span id="contractApplicantName" style="color: var(--primary-color); font-weight: 600;">-</span>
                    </p>
                </div>
                <button class="close-drawer" onclick="closeContractDrawer()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="drawer-content">
                <form id="contractForm">
                    <!-- Contract Type Selection -->
                    <div class="contract-section">
                        <h4><i class="fas fa-briefcase"></i> Employment Type</h4>
                        <div class="contract-type-grid">
                            <label class="contract-type-card">
                                <input type="radio" name="contractType" value="full-time" checked>
                                <div class="type-card-inner">
                                    <i class="fas fa-user-tie"></i>
                                    <h5>Full-Time Employee</h5>
                                    <p>Permanent position with full benefits</p>
                                </div>
                            </label>
                            <label class="contract-type-card">
                                <input type="radio" name="contractType" value="freelance">
                                <div class="type-card-inner">
                                    <i class="fas fa-user-clock"></i>
                                    <h5>Freelance/Contractor</h5>
                                    <p>Project-based or flexible hours</p>
                                </div>
                            </label>
                            <label class="contract-type-card">
                                <input type="radio" name="contractType" value="part-time">
                                <div class="type-card-inner">
                                    <i class="fas fa-user-check"></i>
                                    <h5>Part-Time</h5>
                                    <p>Fixed schedule, fewer hours</p>
                                </div>
                            </label>
                            <label class="contract-type-card">
                                <input type="radio" name="contractType" value="trial">
                                <div class="type-card-inner">
                                    <i class="fas fa-user-clock"></i>
                                    <h5>Trial Period</h5>
                                    <p>3-month probationary period</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Contract Duration -->
                    <div class="contract-section">
                        <h4><i class="fas fa-calendar-alt"></i> Contract Duration</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" id="contractStartDate" required>
                            </div>
                            <div class="form-group">
                                <label>Contract Duration</label>
                                <select id="contractDuration">
                                    <option value="permanent">Permanent/Indefinite</option>
                                    <option value="3">3 Months (Trial)</option>
                                    <option value="6">6 Months</option>
                                    <option value="12">1 Year</option>
                                    <option value="24">2 Years</option>
                                    <option value="36">3 Years</option>
                                    <option value="custom">Custom Duration</option>
                                </select>
                            </div>
                            <div class="form-group" id="customEndDateGroup" style="display: none;">
                                <label>End Date</label>
                                <input type="date" id="contractEndDate">
                            </div>
                        </div>
                    </div>

                    <!-- Compensation -->
                    <div class="contract-section">
                        <h4><i class="fas fa-money-bill-wave"></i> Compensation</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Payment Structure</label>
                                <select id="paymentStructure" onchange="updatePaymentFields()">
                                    <option value="monthly">Monthly Salary</option>
                                    <option value="hourly">Hourly Rate</option>
                                    <option value="project">Project-Based</option>
                                    <option value="commission">Commission-Based</option>
                                </select>
                            </div>
                            <div class="form-group" id="salaryGroup">
                                <label>Amount (LKR)</label>
                                <input type="number" id="salaryAmount" placeholder="e.g., 75000" min="0">
                            </div>
                            <div class="form-group">
                                <label>Payment Frequency</label>
                                <select id="paymentFrequency">
                                    <option value="monthly">Monthly</option>
                                    <option value="bi-weekly">Bi-weekly</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="per-project">Per Project</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Working Hours -->
                    <div class="contract-section">
                        <h4><i class="fas fa-clock"></i> Working Hours & Schedule</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Hours Per Week</label>
                                <input type="number" id="hoursPerWeek" placeholder="e.g., 40" min="0" max="168">
                            </div>
                            <div class="form-group">
                                <label>Work Schedule</label>
                                <select id="workSchedule">
                                    <option value="regular">Regular (Mon-Fri, 9-5)</option>
                                    <option value="flexible">Flexible Hours</option>
                                    <option value="shifts">Shift-Based</option>
                                    <option value="on-call">On-Call Basis</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Overtime Policy</label>
                            <select id="overtimePolicy">
                                <option value="none">No Overtime</option>
                                <option value="paid-1.5x">Paid at 1.5x Rate</option>
                                <option value="paid-2x">Paid at 2x Rate</option>
                                <option value="comp-time">Compensatory Time Off</option>
                            </select>
                        </div>
                    </div>

                    <!-- Benefits & Allowances -->
                    <div class="contract-section">
                        <h4><i class="fas fa-gift"></i> Benefits & Allowances</h4>
                        <div class="benefits-grid">
                            <label class="benefit-checkbox">
                                <input type="checkbox" name="benefits" value="health-insurance">
                                <span><i class="fas fa-heartbeat"></i> Health Insurance</span>
                            </label>
                            <label class="benefit-checkbox">
                                <input type="checkbox" name="benefits" value="paid-leave">
                                <span><i class="fas fa-umbrella-beach"></i> Paid Annual Leave</span>
                            </label>
                            <label class="benefit-checkbox">
                                <input type="checkbox" name="benefits" value="sick-leave">
                                <span><i class="fas fa-notes-medical"></i> Sick Leave</span>
                            </label>
                            <label class="benefit-checkbox">
                                <input type="checkbox" name="benefits" value="transport">
                                <span><i class="fas fa-bus"></i> Transport Allowance</span>
                            </label>
                            <label class="benefit-checkbox">
                                <input type="checkbox" name="benefits" value="mobile">
                                <span><i class="fas fa-mobile-alt"></i> Mobile Allowance</span>
                            </label>
                            <label class="benefit-checkbox">
                                <input type="checkbox" name="benefits" value="tools">
                                <span><i class="fas fa-tools"></i> Tools & Equipment</span>
                            </label>
                            <label class="benefit-checkbox">
                                <input type="checkbox" name="benefits" value="training">
                                <span><i class="fas fa-graduation-cap"></i> Training & Development</span>
                            </label>
                            <label class="benefit-checkbox">
                                <input type="checkbox" name="benefits" value="bonus">
                                <span><i class="fas fa-trophy"></i> Performance Bonus</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Additional Benefits/Notes</label>
                            <textarea id="additionalBenefits" rows="2" placeholder="Specify any additional benefits, allowances, or special arrangements..."></textarea>
                        </div>
                    </div>

                    <!-- Leave Entitlements -->
                    <div class="contract-section">
                        <h4><i class="fas fa-calendar-check"></i> Leave Entitlements</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Annual Leave (Days/Year)</label>
                                <input type="number" id="annualLeave" placeholder="e.g., 14" min="0" max="60">
                            </div>
                            <div class="form-group">
                                <label>Sick Leave (Days/Year)</label>
                                <input type="number" id="sickLeave" placeholder="e.g., 7" min="0" max="60">
                            </div>
                            <div class="form-group">
                                <label>Casual Leave (Days/Year)</label>
                                <input type="number" id="casualLeave" placeholder="e.g., 7" min="0" max="60">
                            </div>
                        </div>
                    </div>

                    <!-- Termination Clauses -->
                    <div class="contract-section">
                        <h4><i class="fas fa-exclamation-triangle"></i> Termination & Notice Period</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Notice Period (Employee)</label>
                                <select id="noticeEmployer">
                                    <option value="0">No Notice Required</option>
                                    <option value="7">1 Week</option>
                                    <option value="14">2 Weeks</option>
                                    <option value="30">1 Month</option>
                                    <option value="60">2 Months</option>
                                    <option value="90">3 Months</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Notice Period (Company)</label>
                                <select id="noticeEmployee">
                                    <option value="0">No Notice Required</option>
                                    <option value="7">1 Week</option>
                                    <option value="14">2 Weeks</option>
                                    <option value="30">1 Month</option>
                                    <option value="60">2 Months</option>
                                    <option value="90">3 Months</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Severance Pay</label>
                            <select id="severancePay">
                                <option value="none">No Severance Pay</option>
                                <option value="statutory">As Per Labor Law</option>
                                <option value="1month">1 Month Salary</option>
                                <option value="2months">2 Months Salary</option>
                                <option value="custom">Custom Agreement</option>
                            </select>
                        </div>
                    </div>

                    <!-- Contract Terms & Conditions -->
                    <div class="contract-section">
                        <h4><i class="fas fa-file-signature"></i> Additional Terms & Conditions</h4>
                        <div class="form-group">
                            <label>Special Clauses</label>
                            <textarea id="specialClauses" rows="4" placeholder="Add any specific terms, confidentiality clauses, non-compete agreements, or special conditions..."></textarea>
                        </div>
                        <div class="terms-checkboxes">
                            <label class="terms-checkbox">
                                <input type="checkbox" name="contractTerms" value="confidentiality" checked>
                                <span>Include Confidentiality Agreement</span>
                            </label>
                            <label class="terms-checkbox">
                                <input type="checkbox" name="contractTerms" value="ip-rights">
                                <span>Intellectual Property Rights Assignment</span>
                            </label>
                            <label class="terms-checkbox">
                                <input type="checkbox" name="contractTerms" value="non-compete">
                                <span>Non-Compete Clause (12 months)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Contract Summary Preview -->
                    <div class="contract-section contract-preview">
                        <h4><i class="fas fa-file-contract"></i> Contract Summary</h4>
                        <div class="contract-summary-box">
                            <div class="summary-row">
                                <span class="summary-label">Employee:</span>
                                <span class="summary-value" id="summaryName">-</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Position:</span>
                                <span class="summary-value" id="summaryPosition">-</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Contract Type:</span>
                                <span class="summary-value" id="summaryType">-</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Duration:</span>
                                <span class="summary-value" id="summaryDuration">-</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Compensation:</span>
                                <span class="summary-value" id="summaryCompensation">-</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Benefits:</span>
                                <span class="summary-value" id="summaryBenefits">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="drawer-actions">
                        <button type="button" class="btn-cancel" onclick="closeContractDrawer()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="button" class="btn-secondary" onclick="saveContractAsDraft()">
                            <i class="fas fa-save"></i> Save as Draft
                        </button>
                        <button type="submit" class="btn-primary success">
                            <i class="fas fa-check-circle"></i> Generate & Send Contract
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Job Applications Drawer -->
    <div class="drawer-overlay" id="viewApplicationsDrawer">
        <div class="drawer-panel drawer-panel-wide">
            <div class="drawer-header">
                <div>
                    <h3><i class="fas fa-clipboard-list"></i> <span id="jobApplicationsTitle">Job Applications</span></h3>
                    <p style="margin: 4px 0 0 0; color: var(--text-secondary); font-size: 14px;" id="jobApplicationsCount">0 Applications</p>
                </div>
                <button class="close-drawer" onclick="closeViewApplicationsDrawer()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="drawer-content">
                <div id="jobApplicationsList" class="job-applications-container">
                    <!-- Applications will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add Bulk Staff Drawer -->
    <div class="drawer-overlay" id="bulkStaffDrawer">
        <div class="drawer-panel">
            <div class="drawer-header">
                <h3><i class="fas fa-users"></i> Add Permanent Staff Summary</h3>
                <button class="close-drawer" onclick="closeBulkStaffDrawer()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="drawer-content">
                <form id="bulkStaffForm" class="bulk-staff-form">
                    <div class="form-section">
                        <h4><i class="fas fa-layer-group"></i> Staff Categories</h4>
                        <p class="section-description">Add multiple skill categories with the number of staff needed for
                            each.</p>

                        <div class="staff-rows-container" id="staffRowsContainer">
                            <!-- Initial row will be added by JavaScript -->
                        </div>

                        <button type="button" class="btn-secondary" onclick="addStaffRow()">
                            <i class="fas fa-plus"></i> Add Another Category
                        </button>
                    </div>

                    <div class="form-section">
                        <h4><i class="fas fa-file-upload"></i> Verification Documents</h4>
                        <p class="section-description">Upload any relevant verification documents for the staff
                            (optional).</p>

                        <div class="file-upload-area">
                            <input type="file" id="verificationDocs" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <div class="file-upload-content">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload or drag files here</p>
                                <span class="file-types">Supported: PDF, DOC, DOCX, JPG, PNG</span>
                            </div>
                        </div>

                        <div class="uploaded-files" id="uploadedFiles"></div>
                    </div>

                    <div class="form-section">
                        <h4><i class="fas fa-chart-bar"></i> Summary</h4>
                        <div class="staff-summary">
                            <div class="summary-item">
                                <span class="summary-label">Total Categories:</span>
                                <span class="summary-value" id="totalCategories">0</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Total Staff:</span>
                                <span class="summary-value" id="totalStaff">0</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeBulkStaffDrawer()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="button" class="btn-primary" onclick="saveBulkStaff()">
                            <i class="fas fa-save"></i> Save Staff Summary
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reduce Staff Drawer -->
    <div class="drawer-overlay" id="reduceStaffDrawer">
        <div class="drawer-panel">
            <div class="drawer-header">
                <h3><i class="fas fa-user-minus"></i> Reduce Staff Count</h3>
                <button class="close-drawer" onclick="closeReduceStaffDrawer()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="drawer-content">
                <form id="reduceStaffForm" class="reduce-staff-form">
                    <div class="form-section">
                        <h4><i class="fas fa-info-circle"></i> Current Staff Overview</h4>
                        <p class="section-description">Select categories to reduce staff count. Current staffing levels are shown for reference.</p>
                        
                        <div class="current-staff-overview" id="currentStaffOverview">
                            <!-- Current staff data will be loaded here -->
                        </div>
                    </div>

                    <div class="form-section">
                        <h4><i class="fas fa-layer-group"></i> Reduction Categories</h4>
                        <p class="section-description">Select skill categories and specify how many staff to reduce from each.</p>

                        <div class="reduction-rows-container" id="reductionRowsContainer">
                            <!-- Reduction rows will be added by JavaScript -->
                        </div>

                        <button type="button" class="btn-secondary" onclick="addReductionRow()">
                            <i class="fas fa-plus"></i> Add Another Category
                        </button>
                    </div>

                    <div class="form-section">
                        <h4><i class="fas fa-file-upload"></i> Documentation</h4>
                        <p class="section-description">Upload any relevant documentation for the staff reduction (optional).</p>

                        <div class="file-upload-area">
                            <input type="file" id="reductionDocs" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <div class="file-upload-content">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload or drag files here</p>
                                <span class="file-types">Supported: PDF, DOC, DOCX, JPG, PNG</span>
                            </div>
                        </div>

                        <div class="uploaded-files" id="reductionUploadedFiles"></div>
                    </div>

                    <div class="form-section">
                        <h4><i class="fas fa-exclamation-triangle"></i> Reduction Impact</h4>
                        <div class="reduction-summary">
                            <div class="impact-warning">
                                <i class="fas fa-warning"></i>
                                <span>Please review the impact before confirming staff reduction.</span>
                            </div>
                            <div class="summary-grid">
                                <div class="summary-item">
                                    <span class="summary-value reduction-total" id="totalReductionCategories">0</span>
                                    <span class="summary-label">Categories Affected</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-value reduction-total" id="totalStaffReduction">0</span>
                                    <span class="summary-label">Total Staff Reduction</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-value remaining-total" id="totalRemainingStaff">0</span>
                                    <span class="summary-label">Remaining Staff</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeReduceStaffDrawer()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="button" class="btn-primary danger" onclick="confirmStaffReduction()">
                            <i class="fas fa-user-minus"></i> Confirm Reduction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Freelancer Details Drawer -->
    <div class="drawer-overlay" id="freelancerDetailsDrawer">
        <div class="drawer-panel">
            <div class="drawer-header">
                <h3><i class="fas fa-user-circle"></i> Freelancer Details</h3>
                <button class="close-drawer" onclick="closeFreelancerDetailsDrawer()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="drawer-content">
                <div class="freelancer-details-content">
                    <!-- Profile Section -->
                    <div class="detail-section">
                        <div class="freelancer-profile-header">
                            <div class="freelancer-profile-avatar" id="freelancerAvatar">KP</div>
                            <div class="freelancer-profile-info">
                                <h4 id="freelancerFullName">Kasun Perera</h4>
                                <span class="freelancer-profile-specialty" id="freelancerSpecialty">MOBILE PHONE REPAIR</span>
                                <div class="freelancer-profile-stats">
                                    <span class="stat-item">
                                        <i class="fas fa-star"></i>
                                        <span id="freelancerRating">4.8</span>
                                    </span>
                                    <span class="stat-item">
                                        <i class="fas fa-check-circle"></i>
                                        <span id="freelancerCompletedJobs">0</span> jobs
                                    </span>
                                    <span class="stat-item">
                                        <i class="fas fa-clock"></i>
                                        <span id="freelancerResponseTime">0</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="detail-section">
                        <h4 class="section-title"><i class="fas fa-address-book"></i> Contact Information</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label"><i class="fas fa-envelope"></i> Email</span>
                                <span class="info-value" id="freelancerEmail">kasun.perera@email.com</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="fas fa-phone"></i> Phone</span>
                                <span class="info-value" id="freelancerPhone">+94 77 123 4567</span>
                            </div>
                        </div>
                    </div>

                    <!-- Experience & Rate -->
                    <div class="detail-section">
                        <h4 class="section-title"><i class="fas fa-briefcase"></i> Experience & Rate</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label"><i class="fas fa-calendar-alt"></i> Experience</span>
                                <span class="info-value" id="freelancerExperience">5 years</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="fas fa-money-bill-wave"></i> Hourly Rate</span>
                                <span class="info-value" id="freelancerHourlyRate">LKR 2,500/hr</span>
                            </div>
                        </div>
                    </div>

                    <!-- Availability Status -->
                    <div class="detail-section">
                        <h4 class="section-title"><i class="fas fa-info-circle"></i> Availability</h4>
                        <div class="availability-status" id="freelancerAvailabilityStatus">
                            <span class="status-badge available">AVAILABLE</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="detail-actions">
                        <button class="action-btn primary" onclick="openAssignJobDrawer()">
                            <i class="fas fa-plus"></i> Assign to Job
                        </button>
                        <button class="action-btn secondary" onclick="contactFreelancer()">
                            <i class="fas fa-envelope"></i> Send Message
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Job Drawer -->
    <div class="drawer-overlay" id="assignJobDrawer">
        <div class="drawer-panel">
            <div class="drawer-header">
                <h3><i class="fas fa-briefcase"></i> Assign Job</h3>
                <button class="close-drawer" onclick="closeAssignJobDrawer()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="drawer-content">
                <form id="assignJobForm" class="assign-job-form" onsubmit="return handleJobAssignment(event)">
                    <!-- Person Info (Read-only) -->
                    <div class="form-section">
                        <h4><i class="fas fa-user"></i> Assigned To</h4>
                        <div class="selected-person-info">
                            <div class="person-mini-card">
                                <div class="person-mini-avatar" id="assignPersonAvatar">KP</div>
                                <div class="person-mini-details">
                                    <div class="person-mini-name" id="assignPersonName">Kasun Perera</div>
                                    <div class="person-mini-specialty" id="assignPersonSpecialty">Mobile Phone Repair</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job Selection -->
                    <div class="form-section">
                        <h4><i class="fas fa-tasks"></i> Select Job/Project</h4>
                        <div class="form-group">
                            <label for="assignJobSelect">
                                <i class="fas fa-briefcase"></i> Available Jobs <span class="required">*</span>
                            </label>
                            <select id="assignJobSelect" required>
                                <option value="">-- Select a project --</option>
                                <option value="job1">Mobile Repair - Customer A (Project #12345)</option>
                                <option value="job2">Screen Replacement - Customer B (Project #12346)</option>
                                <option value="job3">Battery Replacement - Customer C (Project #12347)</option>
                                <option value="job4">Device Diagnostics - Customer D (Project #12348)</option>
                                <option value="job5">Water Damage Repair - Customer E (Project #12349)</option>
                            </select>
                            <small>Select the project you want to assign to this freelancer</small>
                        </div>
                    </div>

                    <!-- Assignment Details -->
                    <div class="form-section">
                        <h4><i class="fas fa-calendar"></i> Assignment Details</h4>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="assignStartDate">Start Date <span class="required">*</span></label>
                                <small>When should work begin?</small>
                                <input type="date" id="assignStartDate" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="assignDeadline">Deadline <span class="required">*</span></label>
                                <small>Expected completion date</small>
                                <input type="date" id="assignDeadline" required>
                            </div>
                        </div>

                        <!-- Pricing Model Selection -->
                        <div class="form-group">
                            <label><i class="fas fa-file-invoice-dollar"></i> Pricing Model <span class="required">*</span></label>
                            <div style="display: flex; gap: 15px; margin-top: 5px;">
                                <label style="display: flex; align-items: center; gap: 5px; cursor: pointer; font-weight: normal;">
                                    <input type="radio" name="pricingModel" value="hourly" checked onchange="togglePricingModel()"> Hourly Rate
                                </label>
                                <label style="display: flex; align-items: center; gap: 5px; cursor: pointer; font-weight: normal;">
                                    <input type="radio" name="pricingModel" value="fixed" onchange="togglePricingModel()"> Fixed Price
                                </label>
                            </div>
                        </div>

                        <div class="form-row" id="hourlyPricingMode">
                            <div class="form-group">
                                <label for="assignEstimatedHours">Estimated Hours <span class="required">*</span></label>
                                <small>Approximate hours needed</small>
                                <input type="number" id="assignEstimatedHours" 
                                       min="1" step="0.5" placeholder="e.g., 8 or 8.5" 
                                       oninput="updateAssignmentCost()" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="assignHourlyRate">
                                    <i class="fas fa-lock"></i> Hourly Rate (LKR)
                                </label>
                                <small><i class="fas fa-info-circle"></i> Base rate</small>
                                <input type="number" id="assignHourlyRate" 
                                       oninput="updateAssignmentCost()"
                                       min="100" step="100" placeholder="e.g., 2500" required>
                            </div>
                        </div>

                        <div class="form-row" id="fixedPricingMode" style="display: none;">
                            <div class="form-group">
                                <label for="assignFixedPrice"><i class="fas fa-tag"></i> Fixed Task Price (LKR) <span class="required">*</span></label>
                                <small>Total amount to pay upon completion</small>
                                <input type="number" id="assignFixedPrice" min="0" step="100" placeholder="e.g., 15000" oninput="updateAssignmentCost()">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="assignNotes">Additional Notes</label>
                            <small>Optional: Include any specific requirements or instructions</small>
                            <textarea id="assignNotes" rows="4" 
                                      placeholder="Add any special instructions, requirements, or notes..."></textarea>
                        </div>
                    </div>

                    <!-- Cost Summary -->
                    <div class="form-section cost-summary">
                        <h4><i class="fas fa-calculator"></i> Cost Estimate</h4>
                        
                        <div class="cost-breakdown" id="hourlyCostSummary">
                            <div class="cost-item">
                                <span class="cost-label"><i class="fas fa-clock"></i> Estimated Hours:</span>
                                <span class="cost-value"><span id="assignCostHours">0</span> hrs</span>
                            </div>
                            <div class="cost-item">
                                <span class="cost-label"><i class="fas fa-money-bill-wave"></i> Hourly Rate:</span>
                                <span class="cost-value">LKR <span id="assignCostRate">0</span></span>
                            </div>
                            <div class="cost-divider"></div>
                            <div class="cost-item total">
                                <span class="cost-label"><i class="fas fa-calculator"></i> Total Estimated Cost:</span>
                                <span class="cost-value total-value">LKR <span id="assignCostTotal">0</span></span>
                            </div>
                        </div>

                        <div class="cost-breakdown" id="fixedCostSummary" style="display: none;">
                            <div class="cost-item total">
                                <span class="cost-label"><i class="fas fa-tag"></i> Total Fixed Price:</span>
                                <span class="cost-value total-value">LKR <span id="assignCostFixedTotal">0</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="modal-actions">
                        <button type="button" class="btn-cancel" onclick="closeAssignJobDrawer()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane"></i> Send Job Offer
                        </button>
                    </div>
                </form>
            </div>
            
            <script>
                function togglePricingModel() {
                    const model = document.querySelector('input[name="pricingModel"]:checked').value;
                    const hourlyDiv = document.getElementById('hourlyPricingMode');
                    const fixedDiv = document.getElementById('fixedPricingMode');
                    const hourlySummary = document.getElementById('hourlyCostSummary');
                    const fixedSummary = document.getElementById('fixedCostSummary');

                    if (model === 'hourly') {
                        hourlyDiv.style.display = 'flex';
                        fixedDiv.style.display = 'none';
                        hourlySummary.style.display = 'block';
                        fixedSummary.style.display = 'none';
                        // Add required attrs to hourly, remove from fixed
                        document.getElementById('assignEstimatedHours').setAttribute('required', 'required');
                        document.getElementById('assignHourlyRate').setAttribute('required', 'required');
                        document.getElementById('assignFixedPrice').removeAttribute('required');
                    } else {
                        hourlyDiv.style.display = 'none';
                        fixedDiv.style.display = 'flex';
                        hourlySummary.style.display = 'none';
                        fixedSummary.style.display = 'block';
                        // Add required attrs to fixed, remove from hourly
                        document.getElementById('assignFixedPrice').setAttribute('required', 'required');
                        document.getElementById('assignEstimatedHours').removeAttribute('required');
                        document.getElementById('assignHourlyRate').removeAttribute('required');
                    }
                    
                    if (typeof updateAssignmentCost === 'function') updateAssignmentCost();
                }

                function updateAssignmentCost() {
                    const model = document.querySelector('input[name="pricingModel"]:checked').value;
                    
                    if (model === 'hourly') {
                        const hours = parseFloat(document.getElementById('assignEstimatedHours').value) || 0;
                        const rateStr = document.getElementById('assignHourlyRate').value;
                        const rate = parseFloat(rateStr) || 0;
                        const total = hours * rate;

                        document.getElementById('assignCostHours').textContent = hours;
                        document.getElementById('assignCostRate').textContent = rate.toLocaleString();
                        document.getElementById('assignCostTotal').textContent = total.toLocaleString();
                    } else {
                        const fixedPrice = parseFloat(document.getElementById('assignFixedPrice').value) || 0;
                        document.getElementById('assignCostFixedTotal').textContent = fixedPrice.toLocaleString();
                    }
                }
            </script>
        </div>
    </div>

    <!-- Chat Drawer -->
    <div class="drawer-overlay" id="chatDrawer">
        <div class="drawer-panel">
            <div class="drawer-header">
                <div class="chat-header-info">
                    <div class="chat-header-avatar" id="chatPersonAvatar">KP</div>
                    <div class="chat-header-details">
                        <h3 id="chatPersonName">Kasun Perera</h3>
                        <span class="chat-header-specialty" id="chatPersonSpecialty">Mobile Phone Repair</span>
                        <span class="chat-online-status" id="chatOnlineStatus">
                            <i class="fas fa-circle"></i> Online
                        </span>
                    </div>
                </div>
                <button class="close-drawer" onclick="closeChatDrawer()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="drawer-content chat-content">
                <!-- Date Separator -->
                <div class="chat-date-separator" id="chatDateSeparator">
                    <span>Today</span>
                </div>
                
                <div class="chat-messages-container" id="chatMessagesContainer">
                    <!-- Messages will be loaded dynamically -->
                </div>
            </div>

            <div class="chat-input-section">
                <button class="chat-attach-btn" onclick="attachChatFile()" title="Attach file">
                    <i class="fas fa-paperclip"></i>
                </button>
                <input type="file" id="chatFileInput" style="display: none;" accept="image/*,video/*,.pdf,.doc,.docx">
                <textarea 
                    id="chatMessageInput" 
                    class="chat-input" 
                    placeholder="Type your message..." 
                    rows="1"
                    onkeypress="handleChatKeyPress(event)"
                    oninput="autoResizeChatInput(this)"></textarea>
                <button class="chat-send-btn" onclick="sendChatMessage()">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            <!-- Typing Indicator -->
            <div class="chat-typing-indicator" id="chatTypingIndicator" style="display: none;">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <span class="typing-text">Typing...</span>
            </div>
        </div>
    </div>

    <!-- Payment Processing Drawer -->
    <div class="drawer-overlay" id="paymentDrawer">
        <div class="drawer-panel large-drawer">
            <div class="drawer-header">
                <div>
                    <h3><i class="fas fa-credit-card"></i> Process Payment</h3>
                    <p class="drawer-subtitle">Complete payment for completed work</p>
                </div>
                <button class="close-drawer" onclick="closePaymentDrawer()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="drawer-content">
                <!-- Freelancer Info -->
                <div class="payment-freelancer-info">
                    <div class="payment-freelancer-avatar" id="paymentFreelancerAvatar">RS</div>
                    <div class="payment-freelancer-details">
                        <h4 id="paymentFreelancerName">Rohan Silva</h4>
                        <p id="paymentFreelancerSpecialty">TV Repair</p>
                    </div>
                    <div class="payment-delay-warning" id="paymentDelayWarning" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Payment is overdue</span>
                    </div>
                </div>

                <!-- Job Details -->
                <div class="payment-section">
                    <h4 class="payment-section-title">
                        <i class="fas fa-briefcase"></i> Job Details
                    </h4>
                    <div class="payment-info-grid">
                        <div class="payment-info-item">
                            <label>Job Title</label>
                            <div class="payment-info-value">
                                <span id="paymentJobTitle">Smart TV Display Repair</span>
                                <span class="payment-job-id" id="paymentJobId">#J098</span>
                            </div>
                        </div>
                        <div class="payment-info-item">
                            <label>Assigned Date</label>
                            <div class="payment-info-value" id="paymentAssignedDate">Oct 15, 2025</div>
                        </div>
                        <div class="payment-info-item">
                            <label>Completed Date</label>
                            <div class="payment-info-value" id="paymentCompletedDate">Oct 21, 2025</div>
                        </div>
                        <div class="payment-info-item">
                            <label>Days Pending</label>
                            <div class="payment-info-value">
                                <span id="paymentDaysPending">2</span> days
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Work Completion Evidence -->
                <div class="payment-section" id="paymentEvidence">
                    <h4 class="payment-section-title">
                        <i class="fas fa-check-circle"></i> Work Completion Evidence
                    </h4>
                    <div class="payment-evidence-box">
                        <p id="paymentEvidenceText">Replaced display panel, tested all functions. Customer verified.</p>
                    </div>
                </div>

                <!-- Payment Calculation -->
                <div class="payment-section">
                    <h4 class="payment-section-title">
                        <i class="fas fa-calculator"></i> Payment Calculation
                    </h4>
                    <div class="payment-calculation">
                        <div class="payment-calc-row">
                            <span class="payment-calc-label">Hours Worked</span>
                            <span class="payment-calc-value"><span id="paymentEstimatedHours">6</span> hrs</span>
                        </div>
                        <div class="payment-calc-row">
                            <span class="payment-calc-label">Hourly Rate</span>
                            <span class="payment-calc-value" id="paymentHourlyRate">LKR 2,800</span>
                        </div>
                        <div class="payment-calc-divider"></div>
                        <div class="payment-calc-row payment-total-row">
                            <span class="payment-calc-label">Total Amount</span>
                            <span class="payment-calc-total" id="paymentTotalAmount">LKR 16,800</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="payment-section">
                    <h4 class="payment-section-title">
                        <i class="fas fa-money-check-alt"></i> Payment Method
                    </h4>
                    <div class="form-group">
                        <select id="paymentMethod" class="form-control" onchange="updatePaymentMethodFields()">
                            <option value="bank-transfer">Bank Transfer</option>
                            <option value="mobile-money">Mobile Money (eSewa/Khalti)</option>
                            <option value="cash">Cash Payment</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>

                    <!-- Bank Transfer Fields -->
                    <div id="bankTransferFields" class="payment-method-fields">
                        <div class="form-group">
                            <label>Bank Account Number</label>
                            <input type="text" id="bankAccountNumber" class="form-control" placeholder="Enter account number">
                        </div>
                        <div class="form-group">
                            <label>Bank Name</label>
                            <input type="text" id="bankName" class="form-control" placeholder="e.g., Commercial Bank">
                        </div>
                        <div class="form-group">
                            <label>Account Holder Name</label>
                            <input type="text" id="accountHolderName" class="form-control" placeholder="Account holder name">
                        </div>
                    </div>

                    <!-- Mobile Money Fields -->
                    <div id="mobileMoneyFields" class="payment-method-fields" style="display: none;">
                        <div class="form-group">
                            <label>Mobile Number</label>
                            <input type="tel" id="mobileMoneyNumber" class="form-control" placeholder="07X XXX XXXX">
                        </div>
                        <div class="form-group">
                            <label>Provider</label>
                            <select id="mobileMoneyProvider" class="form-control">
                                <option value="">Select provider</option>
                                <option value="esewa">eSewa</option>
                                <option value="khalti">Khalti</option>
                                <option value="imepay">IME Pay</option>
                            </select>
                        </div>
                    </div>

                    <!-- Cash Payment Fields -->
                    <div id="cashPaymentFields" class="payment-method-fields" style="display: none;">
                        <div class="cash-payment-notice">
                            <i class="fas fa-info-circle"></i>
                            <p>Please confirm that cash payment will be made in person. Ensure to get a signed receipt.</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Notes -->
                <div class="payment-section">
                    <h4 class="payment-section-title">
                        <i class="fas fa-sticky-note"></i> Payment Notes (Optional)
                    </h4>
                    <div class="form-group">
                        <textarea id="paymentNotes" class="form-control" rows="3" placeholder="Add any notes about this payment..."></textarea>
                    </div>
                </div>

                <!-- Payment Summary Box -->
                <div class="payment-summary-box">
                    <div class="payment-summary-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="payment-summary-details">
                        <p class="payment-summary-label">You are about to pay</p>
                        <h3 class="payment-summary-amount" id="paymentSummaryAmount">LKR 16,800</h3>
                        <p class="payment-summary-to">to <span id="paymentSummaryFreelancer">Rohan Silva</span></p>
                    </div>
                </div>
            </div>

            <div class="drawer-footer">
                <button type="button" class="btn-secondary" onclick="closePaymentDrawer()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn-primary payment-confirm-btn" onclick="confirmPayment()">
                    <i class="fas fa-check-circle"></i> Confirm Payment
                </button>
            </div>
        </div>
    </div>

    <!-- Load Company Employees Database Integration -->
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/company-employees-db.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/freelancers-db.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/applications-db.js"></script>

</body>

</html>



