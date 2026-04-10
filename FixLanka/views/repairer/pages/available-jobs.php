<?php
require_once __DIR__ . '/../../../config/session.php';
require_once __DIR__ . '/../../../config/database.php';

// Ensure repairer is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /2nd-Year-Group-Project/FixLanka/views/auth/login.php');
    exit;
}

$currentRepairerId = (int)$_SESSION['user_id'];

// Load categories from database for filter dropdown
$categories = [];
try {
    $catStmt = $pdo->query("SELECT category_id, name FROM category ORDER BY name ASC");
    $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error loading categories: " . $e->getMessage());
}

// Load districts from existing job requests for filter dropdown
$districts = [];
try {
    $distStmt = $pdo->query("SELECT DISTINCT district FROM jobrequest WHERE status = 'pending' AND district IS NOT NULL AND district != '' ORDER BY district ASC");
    $districts = $distStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    error_log("Error loading districts: " . $e->getMessage());
}

// Page configuration
$currentPage = 'available-jobs';
$pageTitle = 'Available Jobs';
$pageSubtitle = 'Find new repair requests in your area';
$searchPlaceholder = 'Search jobs, customers, locations...';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Jobs - FixLanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/global.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/repairer-pages.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/available-jobs.css">
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
                                <h1 class="page-header-title">Available Jobs</h1>
                                <p class="page-header-subtitle">Browse and apply for repair jobs in your area</p>
                            </div>
                            <div class="page-header-stats">
                                <div class="header-stat">
                                    <span class="header-stat-number" id="new-jobs-count">0</span>
                                    <span class="header-stat-label">New Jobs</span>
                                </div>
                                <div class="header-stat">
                                    <span class="header-stat-number" id="total-jobs-count">0</span>
                                    <span class="header-stat-label">Total Available</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Filters Section -->
                    <section class="filters-section">
                        <div class="filters-container">
                            <div class="filter-group">
                                <label for="category-filter" class="filter-label">Category</label>
                                <select id="category-filter" class="filter-select">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat['name']); ?>">
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="location-filter" class="filter-label">Location</label>
                                <select id="location-filter" class="filter-select">
                                    <option value="">All Districts</option>
                                    <?php foreach ($districts as $district): ?>
                                        <option value="<?php echo htmlspecialchars($district); ?>">
                                            <?php echo htmlspecialchars(ucfirst($district)); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="sort-filter" class="filter-label">Sort by</label>
                                <select id="sort-filter" class="filter-select">
                                    <option value="newest">Newest First</option>
                                    <option value="oldest">Oldest First</option>
                                    <option value="urgency">Urgency</option>
                                    <option value="deadline">Deadline</option>
                                </select>
                            </div>

                            <div class="filter-actions">
                                <button class="btn-filter btn-primary">
                                    <i class="fas fa-filter"></i>
                                    Apply Filters
                                </button>
                                <button class="btn-filter btn-secondary">
                                    <i class="fas fa-undo"></i>
                                    Reset
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Tabs Navigation -->
                    <section class="tabs-section">
                        <div class="tabs-container">
                            <button class="tab-button active" data-tab="available-jobs">
                                <i class="fas fa-briefcase"></i>
                                Available Jobs
                                <span class="tab-badge" id="available-jobs-badge">0</span>
                            </button>
                            <button class="tab-button" data-tab="submitted-quotes">
                                <i class="fas fa-file-invoice"></i>
                                My Quotations
                                <span class="tab-badge" id="quotes-count-badge">0</span>
                            </button>
                        </div>
                    </section>

                    <!-- Tab Content: Available Jobs -->
                    <div class="tab-content active" id="available-jobs-tab">
                        <!-- Jobs Section -->
                        <section class="jobs-section">
                            <div class="section-header">
                                <h2 class="section-title">Available Jobs</h2>
                                <span class="section-subtitle" id="jobs-count">Loading...</span>
                            </div>

                            <div class="jobs-grid" id="jobs-grid-container">
                                <!-- Jobs will be loaded dynamically via JavaScript -->
                                <div class="loading-state">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <p>Loading available jobs...</p>
                                </div>
                            </div>
                        </section>
                    </div>
                    <!-- End Available Jobs Tab -->

                    <!-- Tab Content: Submitted Quotations -->
                    <div class="tab-content" id="submitted-quotes-tab">
                        <section class="submitted-quotes-section">
                            <div class="section-header">
                                <h2 class="section-title">
                                    <i class="fas fa-file-invoice"></i>
                                    My Submitted Quotations
                                </h2>
                                <span class="section-subtitle" id="quotes-count">Loading...</span>
                            </div>

                            <div class="quotes-container" id="submitted-quotes-container">
                                <!-- Quotations will be loaded dynamically -->
                                <div class="loading-state">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <p>Loading your quotations...</p>
                                </div>
                            </div>
                        </section>
                    </div>
                    <!-- End Submitted Quotations Tab -->
                </div>
            </main>
        </div>
    </div>

    <!-- Job Details Drawer -->
    <div class="drawer" id="jobDetailsDrawer">
        <div class="drawer-overlay" onclick="closeJobDetails()"></div>
        <div class="drawer-content">
            <div class="drawer-header">
                <h3><i class="fas fa-briefcase"></i> Job Details</h3>
                <button class="drawer-close" onclick="closeJobDetails()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="drawer-body">
                <!-- Job Header -->
                <div class="job-detail-header">
                    <div class="job-detail-category" id="detailCategory">
                        <i class="fas fa-wrench"></i>
                        <span>Loading...</span>
                    </div>
                    <div class="job-detail-urgency" id="detailUrgency">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Loading...</span>
                    </div>
                </div>

                <h2 class="job-detail-title" id="detailTitle">Loading...</h2>

                <!-- Customer Information -->
                <div class="detail-section">
                    <h4><i class="fas fa-user"></i> Customer Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Name</span>
                            <span class="detail-value" id="detailCustomerName">-</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Posted</span>
                            <span class="detail-value" id="detailPosted">-</span>
                        </div>
                    </div>
                </div>

                <!-- Location Information -->
                <div class="detail-section">
                    <h4><i class="fas fa-map-marker-alt"></i> Location</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">District</span>
                            <span class="detail-value" id="detailDistrict">-</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Full Address</span>
                            <span class="detail-value" id="detailAddress">-</span>
                        </div>
                    </div>
                </div>

                <!-- Schedule Information -->
                <div class="detail-section">
                    <h4><i class="fas fa-calendar"></i> Schedule</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Finish By Date</span>
                            <span class="detail-value" id="detailSchedule">-</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Service Provider Type</span>
                            <span class="detail-value" id="detailProviderType">-</span>
                        </div>
                    </div>
                </div>

                <!-- Job Description -->
                <div class="detail-section">
                    <h4><i class="fas fa-file-alt"></i> Job Description</h4>
                    <p class="detail-description" id="detailDescription">-</p>
                </div>

                <!-- Attachments -->
                <div class="detail-section">
                    <h4><i class="fas fa-paperclip"></i> Attachments</h4>
                    <div class="attachments-grid" id="detailAttachments">
                        <p class="text-muted">No attachments</p>
                    </div>
                </div>
            </div>
            <div class="drawer-footer">
                <button class="btn btn-secondary" onclick="closeJobDetails()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button class="btn btn-primary" id="drawerSubmitQuoteBtn" onclick="submitQuoteFromDetails()">
                    <i class="fas fa-file-invoice-dollar"></i> Submit Quote
                </button>
            </div>
        </div>
    </div>

    <!-- Quote Submission Modal -->
    <div class="modal-overlay" id="quoteModal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-file-invoice-dollar"></i> Submit Quotation</h3>
                <button class="modal-close" onclick="closeQuoteModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="quoteForm" onsubmit="handleQuoteSubmit(event)">
                    <input type="hidden" id="quoteJobId" name="request_id">
                    <h4 id="quoteJobTitle" style="margin-bottom: 16px; color: var(--text-primary, #1a1a2e);"></h4>
                    
                    <div class="form-group">
                        <label for="quoteAmount"><i class="fas fa-rupee-sign"></i> Quote Amount (LKR) *</label>
                        <input type="number" id="quoteAmount" name="quoteAmount" min="1" step="0.01" required placeholder="Enter your quote amount">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="estimatedDays"><i class="fas fa-clock"></i> Estimated Days *</label>
                            <input type="number" id="estimatedDays" name="estimatedDays" min="1" required placeholder="Days to complete">
                        </div>
                        <div class="form-group">
                            <label for="warrantyPeriod"><i class="fas fa-shield-alt"></i> Warranty (Months)</label>
                            <input type="number" id="warrantyPeriod" name="warrantyPeriod" min="0" value="0" placeholder="Warranty months">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="validUntil"><i class="fas fa-calendar-check"></i> Quote Valid Until *</label>
                        <input type="date" id="validUntil" name="validUntil" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-box"></i> Materials Included?</label>
                        <div class="radio-group">
                            <label class="radio-label"><input type="radio" name="materialsIncluded" value="1" checked> Yes</label>
                            <label class="radio-label"><input type="radio" name="materialsIncluded" value="0"> No</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="quoteMessage"><i class="fas fa-comment"></i> Message / Notes</label>
                        <textarea id="quoteMessage" name="message" rows="3" placeholder="Describe your approach, materials needed, etc."></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeQuoteModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitQuoteBtn">
                            <i class="fas fa-paper-plane"></i> Submit Quote
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Quote Modal -->
    <div class="modal-overlay" id="editQuoteModal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Edit Quotation</h3>
                <button class="modal-close" onclick="closeEditQuoteModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="editQuoteForm" onsubmit="handleQuoteUpdate(event)">
                    <input type="hidden" id="editQuoteId" name="quote_id">
                    <h4 id="editQuoteJobTitle" style="margin-bottom: 16px; color: var(--text-primary, #1a1a2e);"></h4>
                    
                    <div class="form-group">
                        <label for="editQuoteAmount"><i class="fas fa-rupee-sign"></i> Quote Amount (LKR) *</label>
                        <input type="number" id="editQuoteAmount" name="quoteAmount" min="1" step="0.01" required placeholder="Enter your quote amount">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editEstimatedDays"><i class="fas fa-clock"></i> Estimated Days *</label>
                            <input type="number" id="editEstimatedDays" name="estimatedDays" min="1" required placeholder="Days to complete">
                        </div>
                        <div class="form-group">
                            <label for="editWarrantyPeriod"><i class="fas fa-shield-alt"></i> Warranty (Months)</label>
                            <input type="number" id="editWarrantyPeriod" name="warrantyPeriod" min="0" value="0" placeholder="Warranty months">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="editValidUntil"><i class="fas fa-calendar-check"></i> Quote Valid Until *</label>
                        <input type="date" id="editValidUntil" name="validUntil" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-box"></i> Materials Included?</label>
                        <div class="radio-group">
                            <label class="radio-label"><input type="radio" name="editMaterialsIncluded" value="1"> Yes</label>
                            <label class="radio-label"><input type="radio" name="editMaterialsIncluded" value="0"> No</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="editQuoteMessage"><i class="fas fa-comment"></i> Message / Notes</label>
                        <textarea id="editQuoteMessage" name="message" rows="3" placeholder="Describe your approach, materials needed, etc."></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeEditQuoteModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="updateQuoteBtn">
                            <i class="fas fa-save"></i> Update Quote
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Inject repairer ID from PHP session into JS scope -->
    <script>
        window.CURRENT_REPAIRER_ID = <?php echo (int)$currentRepairerId; ?>;
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/common/common.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/available-jobs.js"></script>
</body>
</html>

