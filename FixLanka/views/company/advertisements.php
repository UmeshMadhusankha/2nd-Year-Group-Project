<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advertisement Management - FixLanka Company Dashboard</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/progress-bars.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/buttons.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/dashboard.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/advertisements.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <input type="checkbox" id="sidebar-toggle">

    <div class="dashboard-container">
        <!-- Sidebar Component -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header Component -->
            <?php include 'topbar.php'; ?>



            <!-- KPI Cards Section -->
            <section class="kpi-section">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="header-left">
                        <h1><i class="fas fa-bullhorn"></i> Advertisement Management</h1>
                        <p class="subtitle">Create, manage, and track your advertising campaigns</p>
                    </div>
                    <div class="header-right">
                        <button class="action-btn primary" id="createAdBtn">
                            <i class="fas fa-plus"></i> Create New Advertisement
                        </button>
                    </div>
                </div>

                <!-- Filters & Controls -->
                <div class="controls-section">
                    <!-- Search and Sort Bar -->
                    <div class="search-sort-bar">
                        <div class="search-wrapper">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="adSearchInput" class="search-input" placeholder="Search advertisements by title, description, or category...">
                            <button class="clear-search" id="clearSearch" style="display: none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="sort-controls">
                            <select id="sortBy" class="sort-select">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="views">Most Views</option>
                                <option value="clicks">Most Clicks</option>
                                <option value="ending-soon">Ending Soon</option>
                            </select>
                            <select id="categoryFilter" class="sort-select">
                                <option value="all">All Categories</option>
                                <option value="plumbing">Plumbing</option>
                                <option value="electrical">Electrical</option>
                                <option value="carpentry">Carpentry</option>
                                <option value="painting">Painting</option>
                                <option value="hvac">HVAC</option>
                                <option value="general">General Repairs</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="filter-tabs">
                        <button class="filter-tab active" data-filter="all">
                            <i class="fas fa-th-large"></i> 
                            <span>All Ads</span>
                            <span class="filter-count">6</span>
                        </button>
                        <button class="filter-tab" data-filter="active">
                            <i class="fas fa-play-circle"></i> 
                            <span>Active</span>
                            <span class="filter-count">2</span>
                        </button>
                        <button class="filter-tab" data-filter="pending">
                            <i class="fas fa-clock"></i> 
                            <span>Pending</span>
                            <span class="filter-count">1</span>
                        </button>
                        <button class="filter-tab" data-filter="scheduled">
                            <i class="fas fa-calendar-alt"></i> 
                            <span>Scheduled</span>
                            <span class="filter-count">1</span>
                        </button>
                        <button class="filter-tab" data-filter="paused">
                            <i class="fas fa-pause-circle"></i> 
                            <span>Paused</span>
                            <span class="filter-count">1</span>
                        </button>
                        <button class="filter-tab" data-filter="expired">
                            <i class="fas fa-calendar-times"></i> 
                            <span>Expired</span>
                            <span class="filter-count">1</span>
                        </button>
                    </div>

                    <!-- Results Summary -->
                    <div class="results-summary">
                        <span class="results-text">Showing <strong id="resultCount">6</strong> advertisements</span>
                        <div class="view-toggle">
                            <button class="view-btn active" data-view="grid" title="Grid View">
                                <i class="fas fa-th-large"></i>
                            </button>
                            <button class="view-btn" data-view="list" title="List View">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Advertisements List -->
                <div class="ads-section">
                    <div class="ads-container">
                        <!-- Active Ad Example -->
                        <div class="ad-card" data-status="active" data-id="ad001" data-category="general" data-created="2025-10-15" data-end-date="2025-11-15">
                            <div class="ad-media">
                                <div class="ad-banner summer-banner">
                                    <div class="banner-content">
                                        <i class="fas fa-sun"></i>
                                        <h2>Summer Special</h2>
                                        <p>20% OFF</p>
                                    </div>
                                </div>
                                <span class="media-type"><i class="fas fa-image"></i> Banner</span>
                            </div>
                            <div class="ad-content">
                                <div class="ad-header">
                                    <h3 class="ad-title">Summer Special - 20% Off All Repairs</h3>
                                    <span class="status-badge active">
                                        <i class="fas fa-circle"></i> Active
                                    </span>
                                </div>
                                <p class="ad-description">Get 20% discount on all home repair services this summer. Limited time offer for new customers.</p>
                                <div class="ad-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>Start: Oct 15, 2025</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-calendar-check"></i>
                                        <span>End: Nov 15, 2025</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-eye"></i>
                                        <span>4,523 views</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-mouse-pointer"></i>
                                        <span>234 clicks</span>
                                    </div>
                                </div>
                            </div>
                            <div class="ad-actions">
                                <button class="action-btn-ad view" onclick="viewAdReport('ad001')">
                                    <i class="fas fa-chart-line"></i> Analytics
                                </button>
                                <button class="action-btn-ad edit" onclick="editAd('ad001')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="action-btn-ad pause" onclick="pauseAd('ad001')">
                                    <i class="fas fa-pause"></i> Pause
                                </button>
                                <button class="action-btn-ad delete" onclick="deleteAd('ad001')">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </div>
                        </div>

                        <!-- Pending Approval Ad Example -->
                        <div class="ad-card" data-status="pending" data-id="ad002" data-category="plumbing" data-created="2025-10-20">
                            <div class="ad-media">
                                <div class="ad-banner plumbing-banner">
                                    <div class="banner-content">
                                        <i class="fas fa-wrench"></i>
                                        <h2>Plumbing Services</h2>
                                        <p>24/7 Available</p>
                                    </div>
                                </div>
                                <span class="media-type"><i class="fas fa-video"></i> Video</span>
                            </div>
                            <div class="ad-content">
                                <div class="ad-header">
                                    <h3 class="ad-title">Professional Plumbing Services</h3>
                                    <span class="status-badge pending">
                                        <i class="fas fa-clock"></i> Pending Approval
                                    </span>
                                </div>
                                <p class="ad-description">Expert plumbing solutions for residential and commercial properties. 24/7 emergency service available.</p>
                                <div class="ad-meta">
                                    <div class="meta-item ad-category">
                                        <i class="fas fa-tag"></i>
                                        <span>Plumbing</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-calendar-plus"></i>
                                        <span>Submitted: Oct 20, 2025</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-hourglass-half"></i>
                                        <span>Waiting for moderator review</span>
                                    </div>
                                </div>
                            </div>
                            <div class="ad-actions">
                                <button class="action-btn-ad view" onclick="viewAdDetails('ad002')">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="action-btn-ad edit" onclick="editAd('ad002')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="action-btn-ad warning" onclick="cancelAd('ad002')">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </div>

                        <!-- Scheduled Ad Example -->
                        <div class="ad-card" data-status="scheduled" data-id="ad003" data-category="general" data-created="2025-10-18" data-end-date="2025-12-01">
                            <div class="ad-media">
                                <div class="ad-banner blackfriday-banner">
                                    <div class="banner-content">
                                        <i class="fas fa-tags"></i>
                                        <h2>Black Friday</h2>
                                        <p>50% OFF</p>
                                    </div>
                                </div>
                                <span class="media-type"><i class="fas fa-image"></i> Banner</span>
                            </div>
                            <div class="ad-content">
                                <div class="ad-header">
                                    <h3 class="ad-title">Black Friday Mega Sale - 50% Off</h3>
                                    <span class="status-badge scheduled">
                                        <i class="fas fa-calendar-alt"></i> Scheduled
                                    </span>
                                </div>
                                <p class="ad-description">Massive discounts on all services during Black Friday week. Don't miss out!</p>
                                <div class="ad-meta">
                                    <div class="meta-item ad-category">
                                        <i class="fas fa-tag"></i>
                                        <span>General</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>Starts: Nov 24, 2025</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-calendar-check"></i>
                                        <span>Ends: Nov 30, 2025</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Approved by moderator</span>
                                    </div>
                                </div>
                            </div>
                            <div class="ad-actions">
                                <button class="action-btn-ad view" onclick="viewAdDetails('ad003')">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="action-btn-ad edit" onclick="editAd('ad003')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="action-btn-ad success" onclick="startAdNow('ad003')">
                                    <i class="fas fa-play"></i> Start Now
                                </button>
                                <button class="action-btn-ad warning" onclick="cancelAd('ad003')">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </div>

                        <!-- Paused Ad Example -->
                        <div class="ad-card" data-status="paused" data-id="ad004" data-category="electrical" data-created="2025-09-15" data-end-date="2025-11-30">
                            <div class="ad-media">
                                <div class="ad-banner electrical-banner">
                                    <div class="banner-content">
                                        <i class="fas fa-bolt"></i>
                                        <h2>Electrical Services</h2>
                                        <p>Expert Solutions</p>
                                    </div>
                                </div>
                                <span class="media-type"><i class="fas fa-image"></i> Banner</span>
                            </div>
                            <div class="ad-content">
                                <div class="ad-header">
                                    <h3 class="ad-title">Professional Electrical Services</h3>
                                    <span class="status-badge paused">
                                        <i class="fas fa-pause-circle"></i> Paused
                                    </span>
                                </div>
                                <p class="ad-description">Certified electricians for all your electrical needs. Safety first, quality always.</p>
                                <div class="ad-meta">
                                    <div class="meta-item ad-category">
                                        <i class="fas fa-tag"></i>
                                        <span>Electrical</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>Paused on: Oct 18, 2025</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-eye"></i>
                                        <span class="stat-value">1,234</span> <span>views</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-mouse-pointer"></i>
                                        <span class="stat-value">89</span> <span>clicks</span>
                                    </div>
                                </div>
                            </div>
                            <div class="ad-actions">
                                <button class="action-btn-ad success" onclick="resumeAd('ad004')">
                                    <i class="fas fa-play"></i> Resume
                                </button>
                                <button class="action-btn-ad view" onclick="viewAdReport('ad004')">
                                    <i class="fas fa-chart-line"></i> Analytics
                                </button>
                                <button class="action-btn-ad edit" onclick="editAd('ad004')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="action-btn-ad delete" onclick="deleteAd('ad004')">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </div>
                        </div>

                        <!-- Expired Ad Example -->
                        <div class="ad-card" data-status="expired" data-id="ad005" data-category="general" data-created="2025-08-01" data-end-date="2025-10-01">
                            <div class="ad-media">
                                <div class="ad-banner winter-banner">
                                    <div class="banner-content">
                                        <i class="fas fa-snowflake"></i>
                                        <h2>Winter Sale</h2>
                                        <p>15% OFF</p>
                                    </div>
                                </div>
                                <span class="media-type"><i class="fas fa-image"></i> Banner</span>
                            </div>
                            <div class="ad-content">
                                <div class="ad-header">
                                    <h3 class="ad-title">Winter Maintenance Special</h3>
                                    <span class="status-badge expired">
                                        <i class="fas fa-calendar-times"></i> Expired
                                    </span>
                                </div>
                                <p class="ad-description">Winter home maintenance packages at discounted rates. Keep your home cozy this winter.</p>
                                <div class="ad-meta">
                                    <div class="meta-item ad-category">
                                        <i class="fas fa-tag"></i>
                                        <span>General</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-calendar-times"></i>
                                        <span>Ended: Sep 30, 2025</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-eye"></i>
                                        <span class="stat-value">8,945</span> <span>views</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-mouse-pointer"></i>
                                        <span class="stat-value">456</span> <span>clicks</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-chart-line"></i>
                                        <span>5.1% CTR</span>
                                    </div>
                                </div>
                            </div>
                            <div class="ad-actions">
                                <button class="action-btn-ad view" onclick="viewAdReport('ad005')">
                                    <i class="fas fa-chart-bar"></i> Report
                                </button>
                                <button class="action-btn-ad success" onclick="renewAd('ad005')">
                                    <i class="fas fa-redo"></i> Renew
                                </button>
                                <button class="action-btn-ad edit" onclick="duplicateAd('ad005')">
                                    <i class="fas fa-copy"></i> Duplicate
                                </button>
                                <button class="action-btn-ad warning" onclick="archiveAd('ad005')">
                                    <i class="fas fa-archive"></i> Archive
                                </button>
                            </div>
                        </div>

                        <!-- Active Ad Example 2 -->
                        <div class="ad-card" data-status="active" data-id="ad006" data-category="painting" data-created="2025-10-10" data-end-date="2025-11-25">
                            <div class="ad-media">
                                <div class="ad-banner painting-banner">
                                    <div class="banner-content">
                                        <i class="fas fa-paint-roller"></i>
                                        <h2>Painting Services</h2>
                                        <p>Transform Your Space</p>
                                    </div>
                                </div>
                                <span class="media-type"><i class="fas fa-image"></i> Banner</span>
                            </div>
                            <div class="ad-content">
                                <div class="ad-header">
                                    <h3 class="ad-title">Professional Painting & Finishing</h3>
                                    <span class="status-badge active">
                                        <i class="fas fa-circle"></i> Active
                                    </span>
                                </div>
                                <p class="ad-description">Expert painting services for interior and exterior. Quality finish guaranteed.</p>
                                <div class="ad-meta">
                                    <div class="meta-item ad-category">
                                        <i class="fas fa-tag"></i>
                                        <span>Painting</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>Start: Oct 10, 2025</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-calendar-check"></i>
                                        <span>End: Nov 30, 2025</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-eye"></i>
                                        <span class="stat-value">2,789</span> <span>views</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-mouse-pointer"></i>
                                        <span class="stat-value">167</span> <span>clicks</span>
                                    </div>
                                </div>
                            </div>
                            <div class="ad-actions">
                                <button class="action-btn-ad view" onclick="viewAdReport('ad006')">
                                    <i class="fas fa-chart-line"></i> Analytics
                                </button>
                                <button class="action-btn-ad edit" onclick="editAd('ad006')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="action-btn-ad pause" onclick="pauseAd('ad006')">
                                    <i class="fas fa-pause"></i> Pause
                                </button>
                                <button class="action-btn-ad delete" onclick="deleteAd('ad006')">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State (Hidden by default, shown when no results) -->
                    <div class="empty-state" style="display: none;">
                        <div class="empty-state" id="emptyState" style="display: none;">
                            <div class="empty-icon">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <h3>No Advertisements Found</h3>
                            <p>Try adjusting your filters or create a new advertisement to get started.</p>
                            <button class="action-btn primary" onclick="document.getElementById('createAdBtn').click()">
                                <i class="fas fa-plus"></i> Create Advertisement
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Create/Edit Advertisement Modal -->
    <div class="modal-overlay" id="adModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2 id="modalTitle"><i class="fas fa-plus-circle"></i> Create New Advertisement</h2>
                <button class="modal-close" id="closeModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-content">
                <form id="adForm">
                    <!-- Step 1: Ad Type Selection -->
                    <div class="form-step active" id="step1">
                        <h3 class="step-title">Step 1: Select Advertisement Type</h3>
                        <div class="ad-type-selection">
                            <label class="ad-type-card">
                                <input type="radio" name="adType" value="banner" checked>
                                <div class="type-card-content">
                                    <i class="fas fa-image"></i>
                                    <h4>Banner Ad</h4>
                                    <p>Static image advertisement (JPG, PNG, GIF)</p>
                                    <span class="recommended">Recommended</span>
                                </div>
                            </label>
                            <label class="ad-type-card">
                                <input type="radio" name="adType" value="video">
                                <div class="type-card-content">
                                    <i class="fas fa-video"></i>
                                    <h4>Video Ad</h4>
                                    <p>Video advertisement (MP4, WebM)</p>
                                    <span class="premium">Premium</span>
                                </div>
                            </label>
                            <label class="ad-type-card">
                                <input type="radio" name="adType" value="carousel">
                                <div class="type-card-content">
                                    <i class="fas fa-images"></i>
                                    <h4>Carousel Ad</h4>
                                    <p>Multiple images in slideshow</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Step 2: Upload Media -->
                    <div class="form-step" id="step2">
                        <h3 class="step-title">Step 2: Upload Media</h3>

                        <div class="upload-section">
                            <div class="upload-area" id="uploadArea">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <h4>Drag & Drop your files here</h4>
                                <p>or click to browse</p>
                                <input type="file" id="mediaFile" accept="image/*,video/*" hidden>
                                <div class="upload-specs">
                                    <p><strong>Banner:</strong> Recommended size 1200x628px, Max 5MB</p>
                                    <p><strong>Video:</strong> Max 30 seconds, Max 50MB, Format: MP4/WebM</p>
                                </div>
                            </div>
                            <div class="upload-preview" id="uploadPreview" style="display: none;">
                                <img id="previewImage" src="" alt="Preview">
                                <video id="previewVideo" controls style="display: none;"></video>
                                <button type="button" class="btn-remove" id="removeMedia">
                                    <i class="fas fa-times"></i> Remove
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="adTitle">Advertisement Title *</label>
                            <input type="text" id="adTitle" placeholder="e.g., Summer Special - 20% Off All Repairs" required>
                        </div>

                        <div class="form-group">
                            <label for="adDescription">Description *</label>
                            <textarea id="adDescription" rows="4" placeholder="Provide a compelling description of your advertisement..." required></textarea>
                            <small>Describe what makes this offer special and why customers should choose your services</small>
                        </div>
                    </div>

                    <!-- Step 3: Target & Details -->
                    <div class="form-step" id="step3">
                        <h3 class="step-title">Step 3: Advertisement Details</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="adCategory">Service Category *</label>
                                <select id="adCategory" required>
                                    <option value="">Select Category</option>
                                    <option value="plumbing">Plumbing Services</option>
                                    <option value="electrical">Electrical Services</option>
                                    <option value="carpentry">Carpentry</option>
                                    <option value="painting">Painting</option>
                                    <option value="hvac">HVAC Services</option>
                                    <option value="roofing">Roofing</option>
                                    <option value="general">General Repairs</option>
                                    <option value="all">All Services</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="targetAudience">Target Audience</label>
                                <select id="targetAudience">
                                    <option value="all">All Users</option>
                                    <option value="homeowners">Homeowners</option>
                                    <option value="businesses">Businesses</option>
                                    <option value="contractors">Contractors</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="linkUrl">Call-to-Action Link (Optional)</label>
                            <input type="url" id="linkUrl" placeholder="https://yourwebsite.com/offer">
                            <small>Where should users go when they click on your ad?</small>
                        </div>

                        <div class="form-group">
                            <label for="linkText">Call-to-Action Button Text</label>
                            <input type="text" id="linkText" placeholder="e.g., Learn More, Get Quote, Book Now">
                        </div>
                    </div>

                    <!-- Step 4: Schedule -->
                    <div class="form-step" id="step4">
                        <h3 class="step-title">Step 4: Schedule Your Advertisement</h3>

                        <div class="schedule-options">
                            <label class="schedule-option">
                                <input type="radio" name="scheduleType" value="immediate" checked>
                                <div class="option-content">
                                    <i class="fas fa-bolt"></i>
                                    <div>
                                        <h4>Publish Immediately</h4>
                                        <p>Start showing after moderator approval</p>
                                    </div>
                                </div>
                            </label>
                            <label class="schedule-option">
                                <input type="radio" name="scheduleType" value="scheduled">
                                <div class="option-content">
                                    <i class="fas fa-calendar-alt"></i>
                                    <div>
                                        <h4>Schedule for Later</h4>
                                        <p>Set specific start and end dates</p>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="schedule-dates" id="scheduleDates" style="display: none;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="startDate">Start Date & Time *</label>
                                    <input type="datetime-local" id="startDate">
                                </div>
                                <div class="form-group">
                                    <label for="endDate">End Date & Time *</label>
                                    <input type="datetime-local" id="endDate">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="duration">Campaign Duration</label>
                            <select id="duration">
                                <option value="7">1 Week</option>
                                <option value="14">2 Weeks</option>
                                <option value="30">1 Month</option>
                                <option value="60">2 Months</option>
                                <option value="90">3 Months</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>

                        <div class="info-box">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>Ad Review Process:</strong>
                                <p>Your advertisement will be reviewed by our moderation team within 24-48 hours. You'll receive a notification once it's approved or if changes are needed.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Budget & Payment -->
                    <div class="form-step" id="step5">
                        <h3 class="step-title">Step 5: Budget & Payment</h3>

                        <div class="pricing-info">
                            <div class="price-card">
                                <div class="price-header">
                                    <i class="fas fa-tag"></i>
                                    <h4>Advertisement Pricing</h4>
                                </div>
                                <div class="price-breakdown">
                                    <div class="price-item">
                                        <span>Base Rate (per day)</span>
                                        <span class="price">LKR 500</span>
                                    </div>
                                    <div class="price-item">
                                        <span>Video Ad Premium</span>
                                        <span class="price">+ LKR 300/day</span>
                                    </div>
                                    <div class="price-item">
                                        <span>Priority Placement</span>
                                        <span class="price">+ LKR 200/day</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="priorityPlacement">
                                Priority Placement (+LKR 200/day)
                            </label>
                            <small>Your ad will appear at the top of the page for maximum visibility</small>
                        </div>

                        <div class="cost-summary">
                            <div class="summary-row">
                                <span>Campaign Duration:</span>
                                <span id="summaryDuration">30 days</span>
                            </div>
                            <div class="summary-row">
                                <span>Base Cost:</span>
                                <span id="summaryBase">LKR 15,000</span>
                            </div>
                            <div class="summary-row" id="videoCostRow" style="display: none;">
                                <span>Video Premium:</span>
                                <span id="summaryVideo">LKR 9,000</span>
                            </div>
                            <div class="summary-row" id="priorityCostRow" style="display: none;">
                                <span>Priority Placement:</span>
                                <span id="summaryPriority">LKR 6,000</span>
                            </div>
                            <div class="summary-divider"></div>
                            <div class="summary-row total">
                                <span>Total Cost:</span>
                                <span id="summaryTotal">LKR 15,000</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="paymentMethod">Payment Method</label>
                            <select id="paymentMethod" required>
                                <option value="">Select Payment Method</option>
                                <option value="card">Credit/Debit Card</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="wallet">Digital Wallet</option>
                            </select>
                        </div>

                        <div class="terms-checkbox">
                            <label>
                                <input type="checkbox" id="termsAgree" required>
                                I agree to the <a href="#" target="_blank">Advertising Terms & Conditions</a>
                            </label>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="modal-footer">
                        <div class="step-indicator">
                            <span class="step-dot active"></span>
                            <span class="step-dot"></span>
                            <span class="step-dot"></span>
                            <span class="step-dot"></span>
                            <span class="step-dot"></span>
                        </div>
                        <div class="modal-actions">
                            <button type="button" class="btn-secondary" id="prevStep" style="display: none;">
                                <i class="fas fa-arrow-left"></i> Previous
                            </button>
                            <button type="button" class="btn-secondary" id="saveDraft">
                                <i class="fas fa-save"></i> Save as Draft
                            </button>
                            <button type="button" class="btn-primary" id="nextStep">
                                Next <i class="fas fa-arrow-right"></i>
                            </button>
                            <button type="submit" class="btn-primary" id="submitAd" style="display: none;">
                                <i class="fas fa-paper-plane"></i> Submit for Approval
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- link the sidebar.js -->
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/sidebar.js"></script>

    <script>
        // Load components when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            loadComponent('sidebar-container', '/2nd-Year-Group-Project/FixLanka/views/company/sidebar.php');
            loadComponent('header-container', '/2nd-Year-Group-Project/FixLanka/views/company/topbar.php');
        });

        // Function to load HTML components
        function loadComponent(containerId, componentFile) {
            fetch(componentFile)
                .then(response => response.text())
                .then(html => {
                    document.getElementById(containerId).innerHTML = html;
                })
                .catch(error => {
                    console.error('Error loading component:', error);
                });
        }

        // Filter tabs functionality
        const filterTabs = document.querySelectorAll('.filter-tab');
        const adCards = document.querySelectorAll('.ad-card');

        filterTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const filter = tab.dataset.filter;

                filterTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                adCards.forEach(card => {
                    if (filter === 'all' || card.dataset.status === filter) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Modal functionality
        const createAdBtn = document.getElementById('createAdBtn');
        const adModal = document.getElementById('adModal');
        const closeModal = document.getElementById('closeModal');

        createAdBtn.addEventListener('click', () => {
            adModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });

        closeModal.addEventListener('click', () => {
            adModal.classList.remove('active');
            document.body.style.overflow = 'auto';
        });

        adModal.addEventListener('click', (e) => {
            if (e.target === adModal) {
                adModal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });

        // Multi-step form navigation
        let currentStep = 1;
        const totalSteps = 5;
        const formSteps = document.querySelectorAll('.form-step');
        const stepDots = document.querySelectorAll('.step-dot');
        const prevBtn = document.getElementById('prevStep');
        const nextBtn = document.getElementById('nextStep');
        const submitBtn = document.getElementById('submitAd');

        function showStep(step) {
            formSteps.forEach((s, i) => {
                s.classList.toggle('active', i === step - 1);
            });

            stepDots.forEach((dot, i) => {
                dot.classList.toggle('active', i < step);
            });

            prevBtn.style.display = step > 1 ? 'block' : 'none';
            nextBtn.style.display = step < totalSteps ? 'block' : 'none';
            submitBtn.style.display = step === totalSteps ? 'block' : 'none';
        }

        nextBtn.addEventListener('click', () => {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });

        // File upload
        const uploadArea = document.getElementById('uploadArea');
        const mediaFile = document.getElementById('mediaFile');
        const uploadPreview = document.getElementById('uploadPreview');
        const previewImage = document.getElementById('previewImage');
        const previewVideo = document.getElementById('previewVideo');
        const removeMedia = document.getElementById('removeMedia');

        uploadArea.addEventListener('click', () => {
            mediaFile.click();
        });

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = 'var(--primary-color)';
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.style.borderColor = '#ddd';
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = '#ddd';
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileUpload(files[0]);
            }
        });

        mediaFile.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileUpload(e.target.files[0]);
            }
        });

        function handleFileUpload(file) {
            const reader = new FileReader();

            reader.onload = (e) => {
                uploadArea.style.display = 'none';
                uploadPreview.style.display = 'block';

                if (file.type.startsWith('image/')) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                    previewVideo.style.display = 'none';
                } else if (file.type.startsWith('video/')) {
                    previewVideo.src = e.target.result;
                    previewVideo.style.display = 'block';
                    previewImage.style.display = 'none';
                }
            };

            reader.readAsDataURL(file);
        }

        removeMedia.addEventListener('click', () => {
            mediaFile.value = '';
            uploadArea.style.display = 'flex';
            uploadPreview.style.display = 'none';
            previewImage.src = '';
            previewVideo.src = '';
        });

        // Schedule type toggle
        const scheduleTypeRadios = document.querySelectorAll('input[name="scheduleType"]');
        const scheduleDates = document.getElementById('scheduleDates');

        scheduleTypeRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                scheduleDates.style.display = radio.value === 'scheduled' ? 'block' : 'none';
            });
        });

        // Cost calculation
        function updateCostSummary() {
            const adType = document.querySelector('input[name="adType"]:checked').value;
            const priority = document.getElementById('priorityPlacement').checked;
            const duration = parseInt(document.getElementById('duration').value) || 30;

            let baseRate = 500;
            let videoRate = adType === 'video' ? 300 : 0;
            let priorityRate = priority ? 200 : 0;

            let baseCost = baseRate * duration;
            let videoCost = videoRate * duration;
            let priorityCost = priorityRate * duration;
            let total = baseCost + videoCost + priorityCost;

            document.getElementById('summaryDuration').textContent = duration + ' days';
            document.getElementById('summaryBase').textContent = 'LKR ' + baseCost.toLocaleString();
            document.getElementById('summaryVideo').textContent = 'LKR ' + videoCost.toLocaleString();
            document.getElementById('summaryPriority').textContent = 'LKR ' + priorityCost.toLocaleString();
            document.getElementById('summaryTotal').textContent = 'LKR ' + total.toLocaleString();

            document.getElementById('videoCostRow').style.display = videoCost > 0 ? 'flex' : 'none';
            document.getElementById('priorityCostRow').style.display = priorityCost > 0 ? 'flex' : 'none';
        }

        document.querySelectorAll('input[name="adType"]').forEach(radio => {
            radio.addEventListener('change', updateCostSummary);
        });

        document.getElementById('priorityPlacement').addEventListener('change', updateCostSummary);
        document.getElementById('duration').addEventListener('change', updateCostSummary);

        // Form submission
        document.getElementById('adForm').addEventListener('submit', (e) => {
            e.preventDefault();

            // Collect form data
            const formData = {
                type: document.querySelector('input[name="adType"]:checked').value,
                title: document.getElementById('adTitle').value,
                description: document.getElementById('adDescription').value,
                category: document.getElementById('adCategory').value,
                targetAudience: document.getElementById('targetAudience').value,
                linkUrl: document.getElementById('linkUrl').value,
                linkText: document.getElementById('linkText').value,
                scheduleType: document.querySelector('input[name="scheduleType"]:checked').value,
                startDate: document.getElementById('startDate').value,
                endDate: document.getElementById('endDate').value,
                duration: document.getElementById('duration').value,
                priorityPlacement: document.getElementById('priorityPlacement').checked,
                paymentMethod: document.getElementById('paymentMethod').value
            };

            console.log('Advertisement Data:', formData);

            // Show success message
            alert('Advertisement submitted successfully! It will be reviewed by our moderation team within 24-48 hours.');

            // Close modal and reset form
            adModal.classList.remove('active');
            document.body.style.overflow = 'auto';
            currentStep = 1;
            showStep(currentStep);
            document.getElementById('adForm').reset();
            removeMedia.click();
        });

        // Save as draft
        document.getElementById('saveDraft').addEventListener('click', () => {
            alert('Advertisement saved as draft successfully!');
        });

        // Highlight active sidebar link
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = 'advertisements.php';
            const navLinks = document.querySelectorAll('.sidebar .nav-link');

            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                const navItem = link.parentElement;

                // Remove active class from all items
                navItem.classList.remove('active');

                // Add active class to current page
                if (href === currentPage) {
                    navItem.classList.add('active');
                }
            });
        });
    </script>

    <!-- Advertisement Management JavaScript -->
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/advertisements.js"></script>
</body>

</html>



