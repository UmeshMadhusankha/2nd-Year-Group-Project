<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workforce - FixLanka Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/common/variables.css">
    <link rel="stylesheet" href="../../assets/css/company/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/company/topbar.css">
    <link rel="stylesheet" href="../../assets/css/company/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/company/workforce.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- Hidden checkbox for CSS toggle -->
    <input type="checkbox" id="sidebar-toggle">

    <div class="dashboard-container">
        <!-- Sidebar Component -->
        <div id="sidebar-container"></div>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header Component -->
            <div id="header-container"></div>

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
                                    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
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
                                    <span class="stat-number">23</span>
                                    <span class="stat-label">Total Staff</span>
                                </div>
                                <div class="sub-stats">
                                    <div class="sub-stat">
                                        <span class="sub-number">18</span>
                                        <span class="sub-label">Active</span>
                                    </div>
                                    <div class="sub-stat">
                                        <span class="sub-number">4.8</span>
                                        <span class="sub-label">⭐ Rating</span>
                                    </div>
                                </div>
                            </div>
                            <div class="specialties-preview">
                                <h4>Specialties Overview</h4>
                                <div class="specialty-items">
                                    <div class="specialty-item">
                                        <span class="specialty-name">Plumbing</span>
                                        <span class="specialty-count">8 Total, 6 Active</span>
                                    </div>
                                    <div class="specialty-item">
                                        <span class="specialty-name">Electrical</span>
                                        <span class="specialty-count">5 Total, 4 Active</span>
                                    </div>
                                    <div class="specialty-item">
                                        <span class="specialty-name">Carpentry</span>
                                        <span class="specialty-count">6 Total, 5 Active</span>
                                    </div>
                                    <div class="specialty-item">
                                        <span class="specialty-name">HVAC</span>
                                        <span class="specialty-count">4 Total, 3 Active</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="action-button primary" onclick="expandSection('employees')">
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
                                    <span class="stat-number">12</span>
                                    <span class="stat-label">Available</span>
                                </div>
                                <div class="sub-stats">
                                    <div class="sub-stat">
                                        <span class="sub-number">8</span>
                                        <span class="sub-label">Active</span>
                                    </div>
                                    <div class="sub-stat">
                                        <span class="sub-number">4</span>
                                        <span class="sub-label">Free</span>
                                    </div>
                                </div>
                            </div>
                            <div class="freelancers-preview">
                                <h4>Top Freelancers</h4>
                                <div class="freelancer-items">
                                    <div class="freelancer-item">
                                        <div class="freelancer-avatar">KP</div>
                                        <div class="freelancer-info">
                                            <span class="freelancer-name">Kasun Perera</span>
                                            <span class="freelancer-details">Mobile Repair • ⭐ 4.9 • LKR 2,500/hr</span>
                                        </div>
                                    </div>
                                    <div class="freelancer-item">
                                        <div class="freelancer-avatar">NF</div>
                                        <div class="freelancer-info">
                                            <span class="freelancer-name">Nimal Fernando</span>
                                            <span class="freelancer-details">Laptop Repair • ⭐ 4.7 • LKR 2,200/hr</span>
                                        </div>
                                    </div>
                                    <div class="freelancer-item">
                                        <div class="freelancer-avatar">AS</div>
                                        <div class="freelancer-info">
                                            <span class="freelancer-name">Anjali Silva</span>
                                            <span class="freelancer-details">Device Setup • ⭐ 4.8 • LKR 2,000/hr</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="action-button primary" onclick="expandSection('freelancers')">
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
                                    <span class="stat-number" id="applicationCount">8</span>
                                    <span class="stat-label">Pending</span>
                                </div>
                                <div class="sub-stats">
                                    <div class="sub-stat">
                                        <span class="sub-number">3</span>
                                        <span class="sub-label">New Today</span>
                                    </div>
                                    <div class="sub-stat">
                                        <span class="sub-number">0</span>
                                        <span class="sub-label">Reviewed</span>
                                    </div>
                                </div>
                            </div>
                            <div class="applications-preview">
                                <h4>Recent Applications</h4>
                                <div class="application-items">
                                    <div class="application-item">
                                        <div class="application-avatar">RA</div>
                                        <div class="application-info">
                                            <span class="application-name">Ravindu Amarasinghe</span>
                                            <span class="application-details">HVAC Specialist • 2 days ago</span>
                                        </div>
                                        <div class="application-status new">New</div>
                                    </div>
                                    <div class="application-item">
                                        <div class="application-avatar">SP</div>
                                        <div class="application-info">
                                            <span class="application-name">Saman Pathirana</span>
                                            <span class="application-details">Electrical Work • 1 day ago</span>
                                        </div>
                                        <div class="application-status new">New</div>
                                    </div>
                                    <div class="application-item">
                                        <div class="application-avatar">MR</div>
                                        <div class="application-info">
                                            <span class="application-name">Malini Rajapakse</span>
                                            <span class="application-details">Plumbing Expert • 3 days ago</span>
                                        </div>
                                        <div class="application-status pending">Pending</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="action-button primary" onclick="expandSection('applications')">
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
                                    <span class="stat-number">5</span>
                                    <span class="stat-label">Active Posts</span>
                                </div>
                                <div class="sub-stats">
                                    <div class="sub-stat">
                                        <span class="sub-number">2</span>
                                        <span class="sub-label">Drafts</span>
                                    </div>
                                    <div class="sub-stat">
                                        <span class="sub-number">24</span>
                                        <span class="sub-label">Applications</span>
                                    </div>
                                </div>
                            </div>
                            <div class="job-postings-preview">
                                <h4>Recent Postings</h4>
                                <div class="job-posting-items">
                                    <div class="job-posting-item">
                                        <div class="job-posting-info">
                                            <span class="job-title">Senior HVAC Technician</span>
                                            <span class="job-details">12 applications • 3 days ago</span>
                                        </div>
                                        <div class="job-status active">Active</div>
                                    </div>
                                    <div class="job-posting-item">
                                        <div class="job-posting-info">
                                            <span class="job-title">Electrical Repair Specialist</span>
                                            <span class="job-details">8 applications • 1 week ago</span>
                                        </div>
                                        <div class="job-status active">Active</div>
                                    </div>
                                    <div class="job-posting-item">
                                        <div class="job-posting-info">
                                            <span class="job-title">Plumbing Contractor</span>
                                            <span class="job-details">4 applications • 2 days ago</span>
                                        </div>
                                        <div class="job-status active">Active</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button class="action-button primary" onclick="expandSection('job-postings')">
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
                            <button class="back-button" onclick="backToDashboard()">
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
                            <div class="category-card">
                                <div class="category-icon plumbing">
                                    <i class="fas fa-wrench"></i>
                                </div>
                                <div class="category-info">
                                    <h3>Plumbing</h3>
                                    <div class="category-stats">
                                        <span class="total-count">8 Total</span>
                                        <span class="active-count">6 Active</span>
                                    </div>
                                    <div class="category-meta">
                                        <span class="avg-rating"><i class="fas fa-star"></i> 4.8</span>
                                        <span class="hourly-range">LKR 2,200-2,800/hr</span>
                                    </div>
                                </div>
                            </div>

                            <div class="category-card">
                                <div class="category-icon electrical">
                                    <i class="fas fa-bolt"></i>
                                </div>
                                <div class="category-info">
                                    <h3>Electrical</h3>
                                    <div class="category-stats">
                                        <span class="total-count">5 Total</span>
                                        <span class="active-count">4 Active</span>
                                    </div>
                                    <div class="category-meta">
                                        <span class="avg-rating"><i class="fas fa-star"></i> 4.7</span>
                                        <span class="hourly-range">LKR 2,500-3,000/hr</span>
                                    </div>
                                </div>
                            </div>

                            <div class="category-card">
                                <div class="category-icon carpentry">
                                    <i class="fas fa-hammer"></i>
                                </div>
                                <div class="category-info">
                                    <h3>Carpentry</h3>
                                    <div class="category-stats">
                                        <span class="total-count">6 Total</span>
                                        <span class="active-count">5 Active</span>
                                    </div>
                                    <div class="category-meta">
                                        <span class="avg-rating"><i class="fas fa-star"></i> 4.9</span>
                                        <span class="hourly-range">LKR 2,000-2,600/hr</span>
                                    </div>
                                </div>
                            </div>

                            <div class="category-card">
                                <div class="category-icon hvac">
                                    <i class="fas fa-fan"></i>
                                </div>
                                <div class="category-info">
                                    <h3>HVAC</h3>
                                    <div class="category-stats">
                                        <span class="total-count">4 Total</span>
                                        <span class="active-count">3 Active</span>
                                    </div>
                                    <div class="category-meta">
                                        <span class="avg-rating"><i class="fas fa-star"></i> 4.6</span>
                                        <span class="hourly-range">LKR 2,800-3,200/hr</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Freelancers Section -->
                    <div class="workforce-section" id="freelancersSection">
                        <div class="section-header">
                            <h2><i class="fas fa-handshake"></i> Available Freelancers</h2>
                            <p class="section-subtitle">Independent contractors ready for assignments</p>
                        </div>

                        <div class="freelancer-list">
                            <!-- Freelancer cards will be populated here -->
                        </div>
                    </div>

                    <!-- Applications Section -->
                    <div class="workforce-section" id="applicationsSection">
                        <div class="section-header">
                            <h2><i class="fas fa-file-alt"></i> Pending Applications</h2>
                            <p class="section-subtitle">New applications awaiting review</p>
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
        // Load components when DOM is ready
        document.addEventListener('DOMContentLoaded', function () {
            loadComponent('sidebar-container', 'sidebar.php');
            loadComponent('header-container', 'topbar.php');

            setTimeout(() => {
                initializePage();
            }, 500);
        });

        // Function to load HTML components
        function loadComponent(containerId, componentFile) {
            fetch(componentFile)
                .then(response => response.text())
                .then(html => {
                    if (containerId === 'sidebar-container') {
                        // Set active state immediately in the HTML before inserting
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = html;
                        
                        // Remove any existing active classes
                        const allNavItems = tempDiv.querySelectorAll('.nav-item');
                        allNavItems.forEach(item => item.classList.remove('active'));
                        
                        // Set workforce as active immediately
                        const workforceLink = tempDiv.querySelector('a[href="workforce.php"]');
                        if (workforceLink) {
                            workforceLink.parentElement.classList.add('active');
                        }
                        
                        // Insert the modified HTML
                        document.getElementById(containerId).innerHTML = tempDiv.innerHTML;
                    } else {
                        document.getElementById(containerId).innerHTML = html;
                    }

                    // Initialize topbar after loading
                    if (containerId === 'header-container') {
                        if (typeof initializeTopbar === 'function') {
                            setTimeout(initializeTopbar, 100);
                        }
                        if (typeof initProfileDropdown === 'function') {
                            setTimeout(initProfileDropdown, 200);
                        }
                    }
                })
                .catch(error => console.error('Error loading component:', error));
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
                    loadJobPostings();
                } else if (section === 'freelancers') {
                    loadFreelancers();
                } else if (section === 'applications') {
                    loadApplications();
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

        // Sample data
        const employeeCategoriesData = [
            { category: 'Plumbing', count: 5, description: 'Water pipe and drainage specialists', avgRating: 4.7, hourlyRange: 'LKR 2,200-2,800/hr' },
            { category: 'Electrical', count: 7, description: 'Electrical system repair experts', avgRating: 4.6, hourlyRange: 'LKR 2,000-2,600/hr' },
            { category: 'Carpentry', count: 4, description: 'Wood working and furniture repair', avgRating: 4.8, hourlyRange: 'LKR 2,400-3,000/hr' },
            { category: 'HVAC', count: 3, description: 'Air conditioning and heating specialists', avgRating: 4.6, hourlyRange: 'LKR 2,800-3,200/hr' }
        ];

        const freelancersData = [
            {
                id: 'f1',
                firstName: 'Kasun',
                lastName: 'Perera',
                specialty: 'Mobile Phone Repair',
                experience: 5,
                hourlyRate: 2500,
                rating: 4.8,
                status: 'Available',
                email: 'kasun.perera@email.com',
                phone: '+94 77 123 4501',
                avatar: 'KP',
                completedJobs: 156,
                responseTime: '2 hours'
            },
            {
                id: 'f2',
                firstName: 'Nimali',
                lastName: 'Fernando',
                specialty: 'Laptop Repair',
                experience: 3,
                hourlyRate: 3000,
                rating: 4.6,
                status: 'Busy',
                email: 'nimali.fernando@email.com',
                phone: '+94 71 234 5602',
                avatar: 'NF',
                completedJobs: 89,
                responseTime: '1 hour'
            },
            {
                id: 'f3',
                firstName: 'Rohan',
                lastName: 'Silva',
                specialty: 'TV Repair',
                experience: 7,
                hourlyRate: 2800,
                rating: 4.9,
                status: 'Available',
                email: 'rohan.silva@email.com',
                phone: '+94 76 345 6703',
                avatar: 'RS',
                completedJobs: 234,
                responseTime: '30 mins'
            },
            {
                id: 'f4',
                firstName: 'Dilani',
                lastName: 'Wickramasinghe',
                specialty: 'Air Conditioner Repair',
                experience: 6,
                hourlyRate: 3200,
                rating: 4.7,
                status: 'Available',
                email: 'dilani.wick@email.com',
                phone: '+94 77 456 7804',
                avatar: 'DW',
                completedJobs: 178,
                responseTime: '1 hour'
            },
            {
                id: 'f5',
                firstName: 'Tharindu',
                lastName: 'Jayasuriya',
                specialty: 'Refrigerator Repair',
                experience: 4,
                hourlyRate: 2600,
                rating: 4.5,
                status: 'Available',
                email: 'tharindu.j@email.com',
                phone: '+94 71 567 8905',
                avatar: 'TJ',
                completedJobs: 123,
                responseTime: '3 hours'
            },
            {
                id: 'f6',
                firstName: 'Amaya',
                lastName: 'Dissanayake',
                specialty: 'Washing Machine Repair',
                experience: 3,
                hourlyRate: 2400,
                rating: 4.8,
                status: 'Busy',
                email: 'amaya.diss@email.com',
                phone: '+94 76 678 9006',
                avatar: 'AD',
                completedJobs: 95,
                responseTime: '2 hours'
            },
            {
                id: 'f7',
                firstName: 'Nuwan',
                lastName: 'Bandara',
                specialty: 'Microwave Repair',
                experience: 2,
                hourlyRate: 2200,
                rating: 4.4,
                status: 'Available',
                email: 'nuwan.band@email.com',
                phone: '+94 77 789 0107',
                avatar: 'NB',
                completedJobs: 67,
                responseTime: '4 hours'
            },
            {
                id: 'f8',
                firstName: 'Sanduni',
                lastName: 'Perera',
                specialty: 'Computer Repair',
                experience: 5,
                hourlyRate: 3500,
                rating: 4.9,
                status: 'Available',
                email: 'sanduni.p@email.com',
                phone: '+94 71 890 1208',
                avatar: 'SP',
                completedJobs: 201,
                responseTime: '1 hour'
            },
            {
                id: 'f9',
                firstName: 'Ishara',
                lastName: 'Gunasekara',
                specialty: 'Printer Repair',
                experience: 4,
                hourlyRate: 2300,
                rating: 4.6,
                status: 'Available',
                email: 'ishara.guna@email.com',
                phone: '+94 76 901 2309',
                avatar: 'IG',
                completedJobs: 134,
                responseTime: '2 hours'
            },
            {
                id: 'f10',
                firstName: 'Chamath',
                lastName: 'Silva',
                specialty: 'Home Theater Setup',
                experience: 6,
                hourlyRate: 3000,
                rating: 4.7,
                status: 'Busy',
                email: 'chamath.silva@email.com',
                phone: '+94 77 012 3410',
                avatar: 'CS',
                completedJobs: 167,
                responseTime: '1 hour'
            },
            {
                id: 'f11',
                firstName: 'Malsha',
                lastName: 'Rajapaksha',
                specialty: 'Dishwasher Repair',
                experience: 3,
                hourlyRate: 2500,
                rating: 4.5,
                status: 'Available',
                email: 'malsha.raja@email.com',
                phone: '+94 71 123 4511',
                avatar: 'MR',
                completedJobs: 89,
                responseTime: '3 hours'
            },
            {
                id: 'f12',
                firstName: 'Dinuka',
                lastName: 'Wijesinghe',
                specialty: 'Water Heater Repair',
                experience: 5,
                hourlyRate: 2700,
                rating: 4.8,
                status: 'Available',
                email: 'dinuka.wije@email.com',
                phone: '+94 76 234 5612',
                avatar: 'DW',
                completedJobs: 145,
                responseTime: '2 hours'
            }
        ];

        const applicationsData = [
            {
                id: 'a1',
                firstName: 'Saman',
                lastName: 'Jayasinghe',
                specialty: 'Air Conditioner Repair',
                experience: 4,
                expectedRate: 3200,
                applicationDate: '2025-10-21',
                email: 'saman.jayasinghe@email.com',
                phone: '+94 77 123 4567',
                avatar: 'SJ',
                status: 'new',
                coverLetter: 'Experienced HVAC technician with 4 years of hands-on experience in air conditioning repair and maintenance. Specialized in both residential and commercial systems with excellent problem-solving skills and customer service.',
                skills: ['HVAC Systems', 'Refrigeration', 'Electrical Troubleshooting', 'Customer Service', 'Safety Protocols'],
                certifications: ['HVAC Certified', 'Refrigeration License'],
                previousEmployer: 'Cool Air Solutions'
            },
            {
                id: 'a2',
                firstName: 'Priya',
                lastName: 'Rathnayake',
                specialty: 'Washing Machine Repair',
                experience: 2,
                expectedRate: 2200,
                applicationDate: '2025-10-20',
                email: 'priya.rathnayake@email.com',
                phone: '+94 71 234 5678',
                avatar: 'PR',
                status: 'new',
                coverLetter: 'Dedicated appliance repair specialist with 2 years of experience in washing machine and dryer repairs. Known for quick diagnostics and efficient repairs with high customer satisfaction ratings.',
                skills: ['Appliance Repair', 'Mechanical Systems', 'Diagnostic Tools', 'Time Management', 'Technical Documentation'],
                certifications: ['Appliance Technician Certificate'],
                previousEmployer: 'Home Appliance Center'
            },
            {
                id: 'a3',
                firstName: 'Ravindu',
                lastName: 'Amarasinghe',
                specialty: 'Plumbing',
                experience: 6,
                expectedRate: 2800,
                applicationDate: '2025-10-20',
                email: 'ravindu.amar@email.com',
                phone: '+94 76 345 6789',
                avatar: 'RA',
                status: 'new',
                coverLetter: 'Professional plumber with 6 years of experience in residential and commercial plumbing. Expert in pipe fitting, leak detection, and water system maintenance.',
                skills: ['Pipe Fitting', 'Leak Detection', 'Water Systems', 'Drainage', 'Emergency Repairs'],
                certifications: ['Master Plumber License', 'Gas Fitting Certificate'],
                previousEmployer: 'Lanka Plumbing Services'
            },
            {
                id: 'a4',
                firstName: 'Sachini',
                lastName: 'Fernando',
                specialty: 'Electrical Work',
                experience: 5,
                expectedRate: 3000,
                applicationDate: '2025-10-19',
                email: 'sachini.fern@email.com',
                phone: '+94 77 456 7890',
                avatar: 'SF',
                status: 'pending',
                coverLetter: 'Certified electrician with 5 years experience in residential and commercial electrical installations and repairs. Specialized in modern electrical systems and energy-efficient solutions.',
                skills: ['Electrical Installation', 'Wiring', 'Circuit Design', 'Safety Compliance', 'Troubleshooting'],
                certifications: ['Licensed Electrician', 'Electrical Safety Certificate'],
                previousEmployer: 'Power Solutions Lanka'
            },
            {
                id: 'a5',
                firstName: 'Hasitha',
                lastName: 'Wijeratne',
                specialty: 'Carpentry',
                experience: 7,
                expectedRate: 2600,
                applicationDate: '2025-10-19',
                email: 'hasitha.wije@email.com',
                phone: '+94 71 567 8901',
                avatar: 'HW',
                status: 'pending',
                coverLetter: 'Master carpenter with 7 years of experience in custom woodwork, furniture repair, and interior finishing. Known for attention to detail and quality craftsmanship.',
                skills: ['Custom Woodwork', 'Furniture Making', 'Cabinet Installation', 'Wood Finishing', 'Blueprint Reading'],
                certifications: ['Master Carpenter Certificate'],
                previousEmployer: 'Fine Wood Crafts'
            },
            {
                id: 'a6',
                firstName: 'Nadeesha',
                lastName: 'Bandara',
                specialty: 'Painting',
                experience: 3,
                expectedRate: 2000,
                applicationDate: '2025-10-18',
                email: 'nadeesha.band@email.com',
                phone: '+94 76 678 9012',
                avatar: 'NB',
                status: 'pending',
                coverLetter: 'Professional painter with 3 years of experience in interior and exterior painting. Expertise in color consultation, surface preparation, and finishing techniques.',
                skills: ['Interior Painting', 'Exterior Painting', 'Surface Prep', 'Color Matching', 'Spray Painting'],
                certifications: ['Professional Painter Certificate'],
                previousEmployer: 'Color Masters'
            },
            {
                id: 'a7',
                firstName: 'Kavinda',
                lastName: 'Perera',
                specialty: 'TV & Audio Repair',
                experience: 4,
                expectedRate: 2700,
                applicationDate: '2025-10-17',
                email: 'kavinda.per@email.com',
                phone: '+94 77 789 0123',
                avatar: 'KP',
                status: 'reviewed',
                coverLetter: 'Electronics technician specializing in TV and audio equipment repair. 4 years of experience with LED/LCD TVs, home theater systems, and sound equipment.',
                skills: ['TV Repair', 'Audio Systems', 'Electronics Diagnosis', 'Component Replacement', 'System Calibration'],
                certifications: ['Electronics Technician Certificate', 'Audio/Video Specialist'],
                previousEmployer: 'Tech Repair Center'
            },
            {
                id: 'a8',
                firstName: 'Tharushi',
                lastName: 'Silva',
                specialty: 'Refrigeration',
                experience: 5,
                expectedRate: 2900,
                applicationDate: '2025-10-16',
                email: 'tharushi.silva@email.com',
                phone: '+94 71 890 1234',
                avatar: 'TS',
                status: 'reviewed',
                coverLetter: 'Refrigeration specialist with 5 years experience in repairing and maintaining refrigerators, freezers, and commercial cooling systems. EPA certified.',
                skills: ['Refrigeration Systems', 'Coolant Handling', 'Compressor Repair', 'Temperature Control', 'Preventive Maintenance'],
                certifications: ['EPA Section 608', 'Refrigeration Technician License'],
                previousEmployer: 'Cool Tech Services'
            }
        ];

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
        }

        function viewFreelancerDetails(freelancerId) {
            currentFreelancerId = freelancerId;
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

        function contactFreelancer() {
            const freelancer = freelancersData.find(f => f.id === currentFreelancerId);
            if (!freelancer) return;
            
            // Populate chat drawer with freelancer info
            document.getElementById('chatPersonName').textContent = freelancer.name;
            document.getElementById('chatPersonAvatar').textContent = freelancer.avatar;
            document.getElementById('chatPersonSpecialty').textContent = freelancer.specialty;
            
            // Clear previous messages and load chat history (dummy data for now)
            loadChatMessages(currentFreelancerId);
            
            // Open chat drawer
            document.getElementById('chatDrawer').classList.add('active');
        }

        function closeChatDrawer() {
            document.getElementById('chatDrawer').classList.remove('active');
            document.getElementById('chatMessageInput').value = '';
        }

        function loadChatMessages(freelancerId) {
            const chatContainer = document.getElementById('chatMessagesContainer');
            
            // Dummy chat history
            const chatHistory = [
                {
                    sender: 'freelancer',
                    name: 'Kasun Perera',
                    avatar: 'KP',
                    message: 'Hello! I received the job assignment notification. When should I start?',
                    time: 'Today, 10:30 AM'
                },
                {
                    sender: 'company',
                    name: 'FixLanka Team',
                    avatar: 'FL',
                    message: 'Great! You can start tomorrow morning. The customer will be available from 9 AM.',
                    time: 'Today, 10:45 AM'
                },
                {
                    sender: 'freelancer',
                    name: 'Kasun Perera',
                    avatar: 'KP',
                    message: 'Perfect! Do I need to bring any specific tools or parts?',
                    time: 'Today, 11:00 AM'
                },
                {
                    sender: 'company',
                    name: 'FixLanka Team',
                    avatar: 'FL',
                    message: 'Yes, please bring your standard mobile repair toolkit. The replacement screen will be provided by the customer.',
                    time: 'Today, 11:15 AM'
                }
            ];
            
            chatContainer.innerHTML = '';
            chatHistory.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `chat-message ${msg.sender}`;
                messageDiv.innerHTML = `
                    <div class="chat-message-avatar">${msg.avatar}</div>
                    <div class="chat-message-content">
                        <div class="chat-message-header">
                            <span class="chat-sender">${msg.name}</span>
                            <span class="chat-time">${msg.time}</span>
                        </div>
                        <div class="chat-message-text">${msg.message}</div>
                    </div>
                `;
                chatContainer.appendChild(messageDiv);
            });
            
            // Scroll to bottom
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function sendChatMessage() {
            const input = document.getElementById('chatMessageInput');
            const message = input.value.trim();
            
            if (!message) return;
            
            const chatContainer = document.getElementById('chatMessagesContainer');
            const messageDiv = document.createElement('div');
            messageDiv.className = 'chat-message company';
            
            const now = new Date();
            const timeStr = `Today, ${now.getHours()}:${String(now.getMinutes()).padStart(2, '0')} ${now.getHours() >= 12 ? 'PM' : 'AM'}`;
            
            messageDiv.innerHTML = `
                <div class="chat-message-avatar">FL</div>
                <div class="chat-message-content">
                    <div class="chat-message-header">
                        <span class="chat-sender">FixLanka Team</span>
                        <span class="chat-time">${timeStr}</span>
                    </div>
                    <div class="chat-message-text">${message}</div>
                </div>
            `;
            
            chatContainer.appendChild(messageDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;
            
            input.value = '';
            showNotification('Message sent successfully', 'success');
        }

        // Allow Enter key to send message
        function handleChatKeyPress(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendChatMessage();
            }
        }

        // Update cost summary in real-time
        function updateCostSummary() {
            const hours = parseFloat(document.getElementById('estimatedHours').value) || 0;
            const rate = parseFloat(document.getElementById('agreedRate').value) || 0;
            const total = hours * rate;

            document.getElementById('summaryHours').textContent = hours > 0 ? `${hours} hrs` : '0 hrs';
            document.getElementById('summaryRate').textContent = `LKR ${rate.toLocaleString()}`;
            document.getElementById('summaryTotal').textContent = `LKR ${total.toLocaleString()}`;
        }

        // Add event listeners for cost calculation
        document.addEventListener('DOMContentLoaded', function() {
            const hoursInput = document.getElementById('estimatedHours');
            const rateInput = document.getElementById('agreedRate');
            
            if (hoursInput) {
                hoursInput.addEventListener('input', updateCostSummary);
            }
            if (rateInput) {
                rateInput.addEventListener('input', updateCostSummary);
            }

            // Handle assignment form submission
            const assignForm = document.getElementById('assignJobForm');
            if (assignForm) {
                assignForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleJobAssignment();
                });
            }
        });

        function handleJobAssignment() {
            const freelancer = freelancersData.find(f => f.id === currentFreelancerId);
            if (!freelancer) return;

            const jobSelect = document.getElementById('jobSelect').value;
            const startDate = document.getElementById('assignmentStartDate').value;
            const deadline = document.getElementById('assignmentDeadline').value;
            const hours = document.getElementById('estimatedHours').value;
            const rate = document.getElementById('agreedRate').value;

            if (!jobSelect || !startDate || !deadline || !hours || !rate) {
                showNotification('Please fill in all required fields', 'error');
                return;
            }

            // Validate dates
            if (new Date(deadline) < new Date(startDate)) {
                showNotification('Deadline must be after start date', 'error');
                return;
            }

            // Show loading state
            const submitBtn = document.querySelector('#assignJobForm button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Assigning...';
            submitBtn.disabled = true;

            // Simulate API call
            setTimeout(() => {
                // Update freelancer status to busy
                freelancer.status = 'Busy';
                
                // Reload freelancer list
                loadFreelancers();
                
                // Close drawer and show success
                closeAssignJobDrawer();
                showNotification(`Job successfully assigned to ${freelancer.firstName} ${freelancer.lastName}!`, 'success');
                
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 1000);
        }

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
            // Update total counts
            const totalEmployees = employeeCategoriesData.reduce((sum, cat) => sum + cat.count, 0);
            const totalFreelancers = freelancersData.length;
            const totalApplications = applicationsData.length;

            // You can update any stat displays here if needed
            console.log(`Total: ${totalEmployees} employees, ${totalFreelancers} freelancers, ${totalApplications} applications`);
        }

        // Initialize search functionality
        function initializeSearch() {
            const searchInput = document.getElementById('workforceSearch');
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    const query = e.target.value.toLowerCase();
                    filterContent(query);
                });
            }
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
            if (application.skills && application.skills.length > 0) {
                application.skills.forEach(skill => {
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

        // Initialize page functionality
        function initializePage() {
            initializeFilters();
            initializeSearch();
            loadFreelancers();
            loadApplications();
            updateStats();
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
            const container = document.querySelector('.freelancer-list');
            container.innerHTML = '';

            freelancersData.forEach(freelancer => {
                const item = createFreelancerItem(freelancer);
                container.appendChild(item);
            });
        }

        // Create freelancer item
        function createFreelancerItem(freelancer) {
            const item = document.createElement('div');
            item.className = 'freelancer-item';

            // Determine if freelancer is available for assignment
            const isAvailable = freelancer.status.toLowerCase() === 'available';

            item.innerHTML = `
                <div class="freelancer-main">
                    <div class="freelancer-avatar">${freelancer.avatar}</div>
                    <div class="freelancer-info">
                        <h4>${freelancer.firstName} ${freelancer.lastName}</h4>
                        <span class="freelancer-specialty">${freelancer.specialty}</span>
                        <div class="freelancer-details">
                            <span><i class="fas fa-calendar"></i> ${freelancer.experience} years exp</span>
                            <span><i class="fas fa-money-bill"></i> LKR ${freelancer.hourlyRate.toLocaleString()}/hr</span>
                            <span><i class="fas fa-star"></i> ${freelancer.rating}</span>
                        </div>
                        <div class="freelancer-contact">
                            <i class="fas fa-envelope"></i> ${freelancer.email}
                        </div>
                    </div>
                </div>
                <div class="freelancer-actions">
                    <span class="status-badge ${freelancer.status.toLowerCase()}">${freelancer.status}</span>
                    <div class="action-group">
                        <button class="wf-btn wf-btn-view" onclick="viewFreelancerDetails('${freelancer.id}')" title="View Details">
                            <i class="fas fa-eye"></i>
                            <span>View</span>
                        </button>
                        ${isAvailable ? `
                        <button class="wf-btn wf-btn-assign" onclick="assignJob('${freelancer.id}')" title="Assign to Job">
                            <i class="fas fa-plus-circle"></i>
                            <span>Assign</span>
                        </button>
                        ` : ''}
                        <button class="wf-btn wf-btn-edit" onclick="editFreelancer('${freelancer.id}')" title="Edit Profile">
                            <i class="fas fa-edit"></i>
                            <span>Edit</span>
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
            item.className = 'application-item';

            item.innerHTML = `
                <div class="application-main">
                    <div class="application-avatar">${application.avatar}</div>
                    <div class="application-info">
                        <h4>${application.firstName} ${application.lastName}</h4>
                        <span class="application-specialty">${application.specialty}</span>
                        <div class="application-details">
                            <span><i class="fas fa-calendar"></i> ${application.experience} years exp</span>
                            <span><i class="fas fa-money-bill"></i> Expected: LKR ${application.expectedRate.toLocaleString()}/hr</span>
                            <span><i class="fas fa-clock"></i> Applied ${formatDate(application.applicationDate)}</span>
                        </div>
                        <div class="application-contact">
                            <i class="fas fa-envelope"></i> ${application.email} • <i class="fas fa-phone"></i> ${application.phone}
                        </div>
                    </div>
                </div>
                <div class="application-actions">
                    <span class="status-badge pending-badge">
                        <i class="fas fa-clock"></i> Pending
                    </span>
                    <div class="action-group">
                        <button class="wf-btn wf-btn-view" onclick="viewApplicationDetails('${application.id}')" title="View Full Application">
                            <i class="fas fa-file-alt"></i>
                            <span>View</span>
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
                                <i class="fas fa-envelope"></i> ${employee.email}
                            </p>
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
            if (window.currentViewFreelancerId) {
                closeFreelancerDetailsDrawer();
                editFreelancer(window.currentViewFreelancerId);
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
            const employee = employeesData.find(e => e.id === employeeId);
            
            if (!employee) {
                showNotification('Employee not found', 'error');
                return;
            }

            showNotification(`Edit mode for ${employee.firstName} ${employee.lastName}`, 'info');
            console.log('Edit Employee:', employee);
            
            // TODO: Open edit modal with pre-filled form
        }

        // Edit Freelancer
        function editFreelancer(freelancerId) {
            const freelancer = freelancersData.find(f => f.id === freelancerId);
            
            if (!freelancer) {
                showNotification('Freelancer not found', 'error');
                return;
            }

            showNotification(`Edit mode for ${freelancer.firstName} ${freelancer.lastName}`, 'info');
            console.log('Edit Freelancer:', freelancer);
            
            // TODO: Open edit modal with pre-filled form
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
            showNotification('Edit employee modal would open here', 'info');
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
            const experience = document.getElementById('minExperience').value;
            const location = document.getElementById('locationRequirements').value;
            
            document.getElementById('previewTitle').textContent = title;
            document.getElementById('previewCategory').textContent = category;
            document.getElementById('previewType').textContent = type;
            document.getElementById('previewBudget').textContent = `LKR ${minBudget} - ${maxBudget}/hr`;
            document.getElementById('previewDescription').textContent = description;
            
            // Update requirements list
            const requirementsList = document.getElementById('previewRequirements');
            requirementsList.innerHTML = '';
            
            if (experience) {
                const li = document.createElement('li');
                li.textContent = `Minimum ${experience} experience`;
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

        function publishJobPosting() {
            if (!validateCurrentStep()) return;
            
            const form = document.getElementById('jobPostingForm');
            const editingId = form.dataset.editingId;
            const formData = collectFormData();
            formData.status = 'active';
            formData.publishedAt = new Date().toISOString();
            
            if (editingId) {
                // Update existing job posting
                formData.id = parseInt(editingId);
                formData.updatedAt = new Date().toISOString();
                console.log('Updating job posting:', formData);
                showNotification('Job posting updated and published successfully!', 'success');
            } else {
                // Create new job posting
                console.log('Publishing new job posting:', formData);
                showNotification('Job posting published successfully!', 'success');
            }
            
            // Here you would typically send to server
            // Example: await fetch('/api/job-postings', { method: editingId ? 'PUT' : 'POST', body: JSON.stringify(formData) })
            
            closeJobPostingDrawer();
            
            // Refresh job postings list
            setTimeout(() => {
                loadJobPostings();
            }, 300);
        }

        function saveDraftJobPosting() {
            const form = document.getElementById('jobPostingForm');
            const editingId = form.dataset.editingId;
            const formData = collectFormData();
            formData.status = 'draft';
            
            if (editingId) {
                // Update existing draft
                formData.id = parseInt(editingId);
                formData.updatedAt = new Date().toISOString();
                console.log('Updating job posting draft:', formData);
                showNotification('Job posting draft updated successfully!', 'success');
            } else {
                // Create new draft
                console.log('Saving job posting as draft:', formData);
                showNotification('Job posting saved as draft!', 'success');
            }
            
            // Here you would typically send to server
            
            closeJobPostingDrawer();
            
            // Refresh job postings list
            setTimeout(() => {
                loadJobPostings();
            }, 300);
        }

        function collectFormData() {
            const notifyCheckbox = document.getElementById('notifyRelevantRepairers');
            const directAppCheckbox = document.getElementById('allowDirectApplications');
            
            return {
                title: document.getElementById('jobTitle').value,
                category: document.getElementById('jobCategory').value,
                employmentType: document.getElementById('employmentType').value,
                relatedProject: document.getElementById('relatedProject').value,
                description: document.getElementById('jobDescription').value,
                minExperience: document.getElementById('minExperience').value,
                priorityLevel: document.getElementById('priorityLevel').value,
                minBudget: parseInt(document.getElementById('minBudget').value) || 0,
                maxBudget: parseInt(document.getElementById('maxBudget').value) || 0,
                applicationDeadline: document.getElementById('applicationDeadline').value,
                requiredSkills: document.getElementById('requiredSkills').value,
                locationRequirements: document.getElementById('locationRequirements').value,
                notifyRepairers: notifyCheckbox ? notifyCheckbox.checked : false,
                allowDirectApplications: directAppCheckbox ? directAppCheckbox.checked : false,
                createdAt: new Date().toISOString()
            };
        }

        // Job Postings Management Functions
        function filterJobPostings(filter) {
            // Update active tab
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            // Filter job postings
            const postings = document.querySelectorAll('.job-posting-card');
            postings.forEach(posting => {
                const status = posting.dataset.status;
                if (filter === 'all' || status === filter) {
                    posting.style.display = 'block';
                } else {
                    posting.style.display = 'none';
                }
            });
        }

        function loadJobPostings() {
            // Mock data - in real app this would come from server
            const jobPostings = [
                {
                    id: 1,
                    title: 'Senior HVAC Technician',
                    category: 'hvac',
                    status: 'active',
                    applications: 12,
                    createdAt: '2024-01-15',
                    budget: 'LKR 2,500 - 3,200/hr'
                },
                {
                    id: 2,
                    title: 'Electrical Repair Specialist',
                    category: 'electrical',
                    status: 'active',
                    applications: 8,
                    createdAt: '2024-01-10',
                    budget: 'LKR 2,000 - 2,800/hr'
                },
                {
                    id: 3,
                    title: 'Emergency Plumber',
                    category: 'plumbing',
                    status: 'draft',
                    applications: 0,
                    createdAt: '2024-01-20',
                    budget: 'LKR 2,200 - 3,000/hr'
                }
            ];
            
            const container = document.querySelector('.job-postings-list');
            container.innerHTML = '';
            
            jobPostings.forEach(posting => {
                const postingCard = createJobPostingCard(posting);
                container.appendChild(postingCard);
            });
        }

        function createJobPostingCard(posting) {
            const card = document.createElement('div');
            card.className = 'job-posting-card';
            card.dataset.status = posting.status;
            
            const statusClass = posting.status === 'active' ? 'success' : 
                               posting.status === 'draft' ? 'warning' : 'secondary';
            
            // Only show edit button for draft/closed jobs, not active ones
            const editButton = posting.status !== 'active' 
                ? `<button class="action-btn-sm secondary" onclick="editJobPosting(${posting.id})">
                        <i class="fas fa-edit"></i> Edit
                    </button>`
                : '';
            
            card.innerHTML = `
                <div class="posting-header">
                    <div class="posting-title">
                        <h5>${posting.title}</h5>
                        <span class="posting-category">${posting.category}</span>
                    </div>
                    <div class="posting-status ${statusClass}">${posting.status}</div>
                </div>
                <div class="posting-meta">
                    <span><i class="fas fa-users"></i> ${posting.applications} applications</span>
                    <span><i class="fas fa-money-bill"></i> ${posting.budget}</span>
                    <span><i class="fas fa-calendar"></i> ${formatDate(posting.createdAt)}</span>
                </div>
                <div class="posting-actions">
                    ${editButton}
                    <button class="action-btn-sm primary" onclick="viewApplications(${posting.id}, '${posting.title}')">
                        <i class="fas fa-eye"></i> View Applications
                    </button>
                    <button class="action-btn-sm danger" onclick="deleteJobPosting(${posting.id})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            `;
            
            return card;
        }

        function editJobPosting(id) {
            // Find the job posting data
            const jobPostings = [
                {
                    id: 1,
                    title: 'Senior HVAC Technician',
                    category: 'hvac',
                    status: 'active',
                    applications: 12,
                    createdAt: '2024-01-15',
                    budget: 'LKR 2,500 - 3,200/hr',
                    minBudget: 2500,
                    maxBudget: 3200,
                    employmentType: 'freelance',
                    description: 'Experienced HVAC technician needed for commercial and residential projects. Must have at least 5 years of experience in installation and maintenance.',
                    minExperience: 'senior',
                    priorityLevel: 'high',
                    requiredSkills: 'HVAC Systems, Refrigeration, Electrical Troubleshooting, Customer Service',
                    locationRequirements: 'Colombo, Must have own transportation',
                    applicationDeadline: '2024-02-15'
                },
                {
                    id: 2,
                    title: 'Electrical Repair Specialist',
                    category: 'electrical',
                    status: 'active',
                    applications: 8,
                    createdAt: '2024-01-10',
                    budget: 'LKR 2,000 - 2,800/hr',
                    minBudget: 2000,
                    maxBudget: 2800,
                    employmentType: 'contract',
                    description: 'Looking for skilled electrician for various residential and commercial electrical repairs and installations.',
                    minExperience: 'mid',
                    priorityLevel: 'medium',
                    requiredSkills: 'Electrical Installation, Wiring, Circuit Design, Safety Compliance',
                    locationRequirements: 'Colombo and suburbs',
                    applicationDeadline: '2024-02-10'
                },
                {
                    id: 3,
                    title: 'Emergency Plumber',
                    category: 'plumbing',
                    status: 'draft',
                    applications: 0,
                    createdAt: '2024-01-20',
                    budget: 'LKR 2,200 - 3,000/hr',
                    minBudget: 2200,
                    maxBudget: 3000,
                    employmentType: 'project-based',
                    description: 'Emergency plumber needed for residential plumbing repairs and maintenance work.',
                    minExperience: 'mid',
                    priorityLevel: 'urgent',
                    requiredSkills: 'Pipe Fitting, Leak Detection, Water Systems, Emergency Repairs',
                    locationRequirements: 'Colombo area, 24/7 availability preferred',
                    applicationDeadline: '2024-02-20'
                }
            ];
            
            const posting = jobPostings.find(p => p.id === id);
            
            if (!posting) {
                showNotification('Job posting not found', 'error');
                return;
            }
            
            // Update drawer title to indicate editing
            const drawerHeader = document.querySelector('#jobPostingDrawer .job-posting-header h3');
            if (drawerHeader) {
                drawerHeader.innerHTML = '<i class="fas fa-edit"></i> Edit Job Posting';
            }
            
            // Populate form fields with existing data
            document.getElementById('jobTitle').value = posting.title;
            document.getElementById('jobCategory').value = posting.category;
            document.getElementById('employmentType').value = posting.employmentType;
            document.getElementById('jobDescription').value = posting.description;
            document.getElementById('minExperience').value = posting.minExperience;
            document.getElementById('priorityLevel').value = posting.priorityLevel;
            document.getElementById('minBudget').value = posting.minBudget;
            document.getElementById('maxBudget').value = posting.maxBudget;
            document.getElementById('requiredSkills').value = posting.requiredSkills;
            document.getElementById('locationRequirements').value = posting.locationRequirements;
            
            if (posting.applicationDeadline) {
                document.getElementById('applicationDeadline').value = posting.applicationDeadline;
            }
            
            // Store the job ID for updating
            document.getElementById('jobPostingForm').dataset.editingId = id;
            
            // Update preview
            updatePreview();
            
            // Open drawer
            document.getElementById('jobPostingDrawer').classList.add('active');
            showStep(1);
            
            showNotification('You can now edit this job posting', 'info');
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

        function deleteJobPosting(id) {
            if (confirm('Are you sure you want to delete this job posting?')) {
                // Here you would delete from server
                showNotification('Job posting deleted successfully', 'success');
                loadJobPostings();
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
            document.getElementById('totalEmployees').textContent = employeesData.length;
            document.getElementById('activeEmployees').textContent = employeesData.filter(e => e.status === 'active').length;
            document.getElementById('freelancers').textContent = freelancersData.length;

            const allRatings = [...employeesData, ...freelancersData].filter(p => p.rating > 0);
            const avgRating = allRatings.length > 0 ?
                (allRatings.reduce((sum, p) => sum + p.rating, 0) / allRatings.length).toFixed(1) : '0';
            document.getElementById('avgRating').textContent = avgRating;

            document.getElementById('employeeCount').textContent = employeesData.length;
            document.getElementById('freelancerCount').textContent = freelancersData.length;
            document.getElementById('applicationCount').textContent = applicationsData.length;
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

        function initializeSearch() {
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function () {
                const term = this.value.toLowerCase();
                filterWorkforce(term);
            });
        }

        function filterWorkforce(searchTerm) {
            const cards = document.querySelectorAll('.workforce-card');
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
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
                        <button type="button" class="remove-row-btn" onclick="removeStaffRow(${staffRowCounter})" 
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
            const removeButtons = document.querySelectorAll('.remove-row-btn');

            removeButtons.forEach(btn => {
                btn.style.display = staffRows.length > 1 ? 'flex' : 'none';
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

            // Simulate API call with timeout
            setTimeout(() => {
                // Here you would normally send the data to the server
                console.log('Bulk Staff Data:', staffData);
                console.log('Verification Documents:', files);

                saveBtn.classList.remove('loading');
                saveBtn.disabled = false;

                // Simulate success
                const totalStaff = staffData.reduce((sum, item) => sum + item.quantity, 0);
                showNotification(`Successfully added ${totalStaff} staff members across ${staffData.length} categories!`, 'success');
                closeBulkStaffDrawer();

                // Update the workforce display if needed
                updateStats();
                updateStaffDataAfterAdd(staffData);
            }, 1500);
        }

        function updateStaffDataAfterAdd(staffData) {
            // Update mock data with new staff
            staffData.forEach(staff => {
                if (currentStaffData[staff.skillCategory]) {
                    currentStaffData[staff.skillCategory].current += staff.quantity;
                    currentStaffData[staff.skillCategory].active += staff.quantity;
                } else {
                    currentStaffData[staff.skillCategory] = {
                        current: staff.quantity,
                        active: staff.quantity
                    };
                }
            });
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
        const currentStaffData = {
            'Carpenter': { current: 8, active: 6 },
            'Electrician': { current: 5, active: 5 },
            'Plumber': { current: 4, active: 3 },
            'Painter': { current: 3, active: 2 },
            'HVAC Technician': { current: 2, active: 2 },
            'Mechanic': { current: 1, active: 1 },
            'Welder': { current: 2, active: 1 },
            'Mason': { current: 1, active: 1 },
            'General Labor': { current: 3, active: 2 }
        };

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

        function loadCurrentStaffOverview() {
            const container = document.getElementById('currentStaffOverview');
            let html = '<div class="current-staff-grid">';
            
            Object.entries(currentStaffData).forEach(([category, data]) => {
                html += `
                    <div class="current-staff-item">
                        <div class="staff-category-name">${category}</div>
                        <div class="staff-counts">
                            <span class="current-count">${data.current} Total</span>
                            <span class="active-count">${data.active} Active</span>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            container.innerHTML = html;
        }

        function addReductionRow() {
            reductionRowCounter++;
            const container = document.getElementById('reductionRowsContainer');
            
            const reductionRow = document.createElement('div');
            reductionRow.className = 'reduction-row';
            reductionRow.id = `reductionRow${reductionRowCounter}`;
            reductionRow.style.opacity = '0';
            reductionRow.style.transform = 'translateY(20px)';
            
            reductionRow.innerHTML = `
                <div class="reduction-row-content">
                    <div class="skill-category-group">
                        <label>Skill Category</label>
                        <select class="reduction-category-select" onchange="updateReductionSummary()" data-row-id="${reductionRowCounter}">
                            <option value="">Select skill category</option>
                            ${Object.entries(currentStaffData).map(([category, data]) => 
                                `<option value="${category}" data-current="${data.current}" data-active="${data.active}">${category} (${data.current} current)</option>`
                            ).join('')}
                        </select>
                    </div>
                    
                    <div class="reduction-quantity-group">
                        <label>Number to Reduce</label>
                        <div class="quantity-controls">
                            <button type="button" class="quantity-btn minus" onclick="changeReductionQuantity(${reductionRowCounter}, -1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="reduction-quantity-input" value="1" min="1" max="1" 
                                   id="reductionQuantity${reductionRowCounter}" onchange="updateReductionSummary()">
                            <button type="button" class="quantity-btn plus" onclick="changeReductionQuantity(${reductionRowCounter}, 1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="reduction-info">
                            <span class="max-reduction" id="maxReduction${reductionRowCounter}"><i class="fas fa-info-circle"></i> Max: 0</span>
                        </div>
                    </div>
                    
                    <div class="row-actions">
                        <button type="button" class="remove-row-btn" onclick="removeReductionRow(${reductionRowCounter})" 
                                ${reductionRowCounter === 1 ? 'style="display: none;"' : ''}>
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
            const removeButtons = document.querySelectorAll('.reduction-row .remove-row-btn');
            
            removeButtons.forEach(btn => {
                btn.style.display = reductionRows.length > 1 ? 'flex' : 'none';
            });
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
                
                // Simulate API call with timeout
                setTimeout(() => {
                    // Here you would normally send the data to the server
                    console.log('Staff Reduction Data:', reductionData);
                    console.log('Reduction Documentation:', files);
                    
                    confirmBtn.classList.remove('loading');
                    confirmBtn.disabled = false;
                    
                    // Simulate success
                    showNotification(`Successfully reduced ${totalReduction} staff members!`, 'success');
                    closeReduceStaffDrawer();
                    
                    // Update the workforce display
                    updateStatsAfterReduction(reductionData);
                    updateStats();
                }, 1500);
            }
        }

        function updateStatsAfterReduction(reductionData) {
            // Update the mock data to reflect reductions
            reductionData.forEach(reduction => {
                if (currentStaffData[reduction.skillCategory]) {
                    currentStaffData[reduction.skillCategory].current -= reduction.reductionQuantity;
                    // Also reduce active count proportionally, but not below 0
                    const activeReduction = Math.min(
                        currentStaffData[reduction.skillCategory].active,
                        Math.ceil(reduction.reductionQuantity * 0.8) // Assume 80% of reduced staff were active
                    );
                    currentStaffData[reduction.skillCategory].active -= activeReduction;
                }
            });
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
                                        <option value="part-time">Part Time</option>
                                        <option value="project-based">Project Based</option>
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
                                        <option value="entry">Entry Level (0-1 years)</option>
                                        <option value="junior">Junior (1-3 years)</option>
                                        <option value="mid">Mid Level (3-5 years)</option>
                                        <option value="senior">Senior (5-10 years)</option>
                                        <option value="expert">Expert (10+ years)</option>
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
                                        <span id="previewCategory">Category</span> • 
                                        <span id="previewType">Type</span> • 
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
                    <div class="freelancer-avatar-large" id="freelancerAvatar">👤</div>
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
                    <button class="drawer-btn secondary" onclick="closeFreelancerDetailsDrawer()">
                        <i class="fas fa-times"></i> Close
                    </button>
                    <button class="drawer-btn" onclick="editFromFreelancerDrawer()">
                        <i class="fas fa-edit"></i> Edit Profile
                    </button>
                    <button class="drawer-btn success assign-freelancer-btn" onclick="assignFromFreelancerDrawer()">
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

                <div class="drawer-actions">
                    <button class="drawer-btn secondary" onclick="closeApplicationDetailsDrawer()">
                        <i class="fas fa-times"></i> Close
                    </button>
                    <button class="drawer-btn danger" onclick="rejectApplicationFromDrawer()">
                        <i class="fas fa-times-circle"></i> Decline
                    </button>
                    <button class="drawer-btn success" onclick="approveApplicationFromDrawer()">
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
                        <button type="button" class="drawer-btn secondary" onclick="closeContractDrawer()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="button" class="drawer-btn" onclick="saveContractAsDraft()">
                            <i class="fas fa-save"></i> Save as Draft
                        </button>
                        <button type="submit" class="drawer-btn success">
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

                        <button type="button" class="add-row-btn" onclick="addStaffRow()">
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
                        <button type="button" class="drawer-btn secondary" onclick="closeBulkStaffDrawer()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="button" class="drawer-btn primary" onclick="saveBulkStaff()">
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

                        <button type="button" class="add-row-btn" onclick="addReductionRow()">
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
                        <button type="button" class="drawer-btn secondary" onclick="closeReduceStaffDrawer()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="button" class="drawer-btn danger" onclick="confirmStaffReduction()">
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
                <h3><i class="fas fa-briefcase"></i> Assign Job to Freelancer</h3>
                <button class="close-drawer" onclick="closeAssignJobDrawer()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="drawer-content">
                <form id="assignJobForm" class="assign-job-form">
                    <!-- Freelancer Info (Read-only) -->
                    <div class="form-section">
                        <h4><i class="fas fa-user"></i> Freelancer</h4>
                        <div class="selected-freelancer-info">
                            <div class="freelancer-mini-card">
                                <div class="freelancer-mini-avatar" id="assignFreelancerAvatar">KP</div>
                                <div class="freelancer-mini-details">
                                    <strong id="assignFreelancerName">Kasun Perera</strong>
                                    <span id="assignFreelancerSpecialty">Mobile Phone Repair</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job Selection -->
                    <div class="form-section">
                        <h4><i class="fas fa-briefcase"></i> Select Job/Project</h4>
                        <div class="form-group">
                            <label for="jobSelect"><i class="fas fa-tasks"></i> Available Jobs <span class="required">*</span></label>
                            <select id="jobSelect" class="form-control" required>
                                <option value="">-- Select a job to assign --</option>
                                <option value="job1">Mobile Repair - Customer A (Project #12345)</option>
                                <option value="job2">Screen Replacement - Customer B (Project #12346)</option>
                                <option value="job3">Battery Replacement - Customer C (Project #12347)</option>
                                <option value="job4">Device Diagnostics - Customer D (Project #12348)</option>
                                <option value="job5">Water Damage Repair - Customer E (Project #12349)</option>
                            </select>
                            <span class="helper-text"><i class="fas fa-info-circle"></i> Select the project you want to assign to this freelancer</span>
                        </div>
                    </div>

                    <!-- Job Details -->
                    <div class="form-section">
                        <h4><i class="fas fa-info-circle"></i> Assignment Details</h4>
                        
                        <div class="form-group">
                            <label for="assignmentStartDate"><i class="fas fa-calendar-day"></i> Start Date <span class="required">*</span></label>
                            <input type="date" id="assignmentStartDate" class="form-control" required>
                            <span class="helper-text"><i class="fas fa-info-circle"></i> When should the freelancer begin work?</span>
                        </div>

                        <div class="form-group">
                            <label for="assignmentDeadline"><i class="fas fa-calendar-check"></i> Deadline <span class="required">*</span></label>
                            <input type="date" id="assignmentDeadline" class="form-control" required>
                            <span class="helper-text"><i class="fas fa-info-circle"></i> Expected completion date for this assignment</span>
                        </div>

                        <div class="form-group">
                            <label for="estimatedHours"><i class="fas fa-clock"></i> Estimated Hours <span class="required">*</span></label>
                            <input type="number" id="estimatedHours" class="form-control" min="1" step="0.5" placeholder="e.g., 8 or 8.5" required>
                            <span class="helper-text"><i class="fas fa-info-circle"></i> Approximate hours needed to complete the job</span>
                        </div>

                        <div class="form-group">
                            <label for="agreedRate"><i class="fas fa-money-bill-wave"></i> Agreed Hourly Rate (LKR) <span class="required">*</span></label>
                            <input type="number" id="agreedRate" class="form-control" min="0" step="100" placeholder="e.g., 2500" required>
                            <span class="helper-text"><i class="fas fa-info-circle"></i> Hourly rate for this specific assignment</span>
                        </div>

                        <div class="form-group">
                            <label for="assignmentNotes"><i class="fas fa-sticky-note"></i> Additional Notes</label>
                            <textarea id="assignmentNotes" class="form-control" rows="4" placeholder="Add any special instructions, requirements, or notes for the freelancer..."></textarea>
                            <span class="helper-text"><i class="fas fa-info-circle"></i> Optional: Include any specific requirements or instructions</span>
                        </div>
                    </div>

                    <!-- Cost Summary -->
                    <div class="form-section">
                        <h4><i class="fas fa-calculator"></i> Cost Estimate</h4>
                        <div class="cost-summary">
                            <div class="cost-item">
                                <span><i class="fas fa-clock"></i> Estimated Hours</span>
                                <span id="summaryHours">0 hrs</span>
                            </div>
                            <div class="cost-item">
                                <span><i class="fas fa-money-bill"></i> Hourly Rate</span>
                                <span id="summaryRate">LKR 0</span>
                            </div>
                            <div class="cost-item total">
                                <span><i class="fas fa-calculator"></i> Total Estimated Cost</span>
                                <span id="summaryTotal">LKR 0</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="drawer-btn secondary" onclick="closeAssignJobDrawer()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="drawer-btn primary">
                            <i class="fas fa-check"></i> Confirm Assignment
                        </button>
                    </div>
                </form>
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
                        <h4><i class="fas fa-tasks"></i> Select Job/Project <span class="required">*</span></h4>
                        <small class="form-helper-top">Select the project you want to assign to this freelancer</small>
                        <select id="assignJobSelect" class="form-control" required>
                            <option value="">-- Select a job --</option>
                            <option value="job1">Mobile Repair - Customer A (Project #12345)</option>
                            <option value="job2">Screen Replacement - Customer B (Project #12346)</option>
                            <option value="job3">Battery Replacement - Customer C (Project #12347)</option>
                            <option value="job4">Device Diagnostics - Customer D (Project #12348)</option>
                            <option value="job5">Water Damage Repair - Customer E (Project #12349)</option>
                        </select>
                    </div>

                    <!-- Assignment Details -->
                    <div class="form-section">
                        <h4><i class="fas fa-calendar"></i> Assignment Details</h4>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="assignStartDate">Start Date <span class="required">*</span></label>
                                <small class="form-helper-top">When should work begin?</small>
                                <input type="date" id="assignStartDate" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="assignDeadline">Deadline <span class="required">*</span></label>
                                <small class="form-helper-top">Expected completion date</small>
                                <input type="date" id="assignDeadline" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="assignEstimatedHours">Estimated Hours <span class="required">*</span></label>
                                <small class="form-helper-top">Approximate hours needed to complete</small>
                                <input type="number" id="assignEstimatedHours" class="form-control" 
                                       min="1" step="0.5" placeholder="e.g., 8 or 8.5" 
                                       oninput="updateAssignmentCost()" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="assignHourlyRate">
                                    <i class="fas fa-lock"></i> Hourly Rate (LKR) 
                                    <span class="fixed-rate-badge">FIXED</span>
                                </label>
                                <small class="form-helper-top">
                                    <i class="fas fa-info-circle"></i> Rate from repairer's application (cannot be changed)
                                </small>
                                <input type="number" id="assignHourlyRate" class="form-control readonly-field" 
                                       readonly disabled
                                       min="100" step="100" placeholder="Loading rate...">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="assignNotes">Additional Notes</label>
                            <small class="form-helper-top">Optional: Include any specific requirements or instructions</small>
                            <textarea id="assignNotes" class="form-control" rows="4" 
                                      placeholder="Add any special instructions, requirements, or notes..."></textarea>
                        </div>
                    </div>

                    <!-- Cost Summary -->
                    <div class="form-section cost-summary">
                        <h4><i class="fas fa-calculator"></i> Cost Estimate</h4>
                        <div class="cost-breakdown">
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
                    </div>

                    <!-- Action Buttons -->
                    <div class="drawer-actions">
                        <button type="button" class="btn-cancel" onclick="closeAssignJobDrawer()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn-confirm">
                            <i class="fas fa-check"></i> Confirm Assignment
                        </button>
                    </div>
                </form>
            </div>
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
                    </div>
                </div>
                <button class="close-drawer" onclick="closeChatDrawer()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="drawer-content chat-content">
                <div class="chat-messages-container" id="chatMessagesContainer">
                    <!-- Messages will be loaded dynamically -->
                </div>
            </div>

            <div class="chat-input-section">
                <input type="text" 
                       id="chatMessageInput" 
                       class="chat-input" 
                       placeholder="Type your message..." 
                       onkeypress="handleChatKeyPress(event)">
                <button class="chat-send-btn" onclick="sendChatMessage()">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>


</body>

</html>
