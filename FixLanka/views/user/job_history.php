<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\user\job_history.php

// This file is loaded by JobRequestController->index()
// $jobRequests variable is already set by the controller

if (!isset($jobRequests)) {
    $jobRequests = [];
}

if (!isset($directJobRequests)) {
    $directJobRequests = [];
}

$allJobRequests = [];

foreach ($jobRequests as $job) {
    $job['request_type'] = 'regular';
    $job['posted_date'] = $job['dateCreated'] ?? ($job['date_created'] ?? null);
    $allJobRequests[] = $job;
}

foreach ($directJobRequests as $job) {
    $job['request_type'] = 'direct';
    $job['service_provider_type'] = $job['service_provider_type'] ?? ($job['provider_type'] ?? 'individual');
    $job['posted_date'] = $job['date_created'] ?? ($job['dateCreated'] ?? null);
    $allJobRequests[] = $job;
}

usort($allJobRequests, function ($a, $b) {
    $dateA = strtotime($a['posted_date'] ?? '1970-01-01 00:00:00');
    $dateB = strtotime($b['posted_date'] ?? '1970-01-01 00:00:00');
    return $dateB <=> $dateA;
});

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

// Count jobs by status
$pendingCount = 0;
$inProgressCount = 0;
$completedCount = 0;
$cancelledCount = 0;

foreach ($allJobRequests as $job) {
    $normalizedStatus = strtolower(trim((string)($job['status'] ?? '')));
    switch ($normalizedStatus) {
        case 'pending':
            $pendingCount++;
            break;
        case 'in_progress':
            $inProgressCount++;
            break;
        case 'completed':
            $completedCount++;
            break;
        case 'cancelled':
            $cancelledCount++;
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job History - Fix Lanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/navbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/direct-job-request-popup.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/job-history.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <main class="main-content">
        <div class="page-container">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">My Job Requests</h1>
                    <p class="page-subtitle">Track and manage all your job requests ordered by date</p>
                </div>
                <a href="/2nd-Year-Group-Project/FixLanka/views/user/landing.php" class="btn-home">
                    <i class="fas fa-home"></i> Home
                </a>
            </div>

            <!-- Success/Error Messages -->
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Filter Tabs -->
            <div class="filter-controls">
                <div class="history-controls-left">
                    <div class="history-view-toggle">
                        <button class="history-view-btn active" id="jobsPostedBtn" type="button">Jobs Posted</button>
                        <button class="history-view-btn" id="quotesReceivedBtn" type="button">
                            Quotes Received
                            <span class="quotes-pill" id="quotesReceivedPill" style="display:none;">0</span>
                        </button>
                    </div>

                    <div class="filter-tabs" id="jobsFilterTabs">
                        <button class="filter-tab active" data-status="all">
                            All Jobs <span class="tab-count"><?php echo count($allJobRequests); ?></span>
                        </button>
                        <button class="filter-tab" data-status="pending">
                            Pending <span class="tab-count"><?php echo $pendingCount; ?></span>
                        </button>
                        <button class="filter-tab" data-status="in_progress">
                            In Progress <span class="tab-count"><?php echo $inProgressCount; ?></span>
                        </button>
                        <button class="filter-tab" data-status="completed">
                            Completed <span class="tab-count"><?php echo $completedCount; ?></span>
                        </button>
                        <button class="filter-tab" data-status="cancelled">
                            Cancelled <span class="tab-count"><?php echo $cancelledCount; ?></span>
                        </button>
                    </div>
                </div>
                <a href="/2nd-Year-Group-Project/FixLanka/post-job" class="btn-primary">
                    <i class="fas fa-plus"></i> Post New Job
                </a>
            </div>

            <!-- Jobs Container -->
            <div class="jobs-container">
                <?php if (empty($allJobRequests)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h2 class="empty-title">No Job Requests Yet</h2>
                        <p class="empty-description">You haven't posted any standard or direct job requests yet. Start by posting your first job!</p>
                        <a href="/2nd-Year-Group-Project/FixLanka/post-job" class="post-job-btn">
                            <i class="fas fa-plus"></i> Post Your First Job
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($allJobRequests as $job): ?>
                        <?php
                        $statusValue = strtolower(trim((string)($job['status'] ?? '')));
                        $statusClass = $statusValue !== '' ? $statusValue : 'unknown';
                        $isDirectRequest = ($job['request_type'] ?? 'regular') === 'direct';
                        $isPendingRegular = $statusValue === 'pending' && !$isDirectRequest;
                        $isPendingDirect = $statusValue === 'pending' && $isDirectRequest;
                        $showCollaborationButton = in_array($statusValue, ['in_progress', 'completed'], true);
                        $showInProgressDetails = $statusValue === 'in_progress';
                        $statusLabel = ucfirst(str_replace('_', ' ', $statusClass));
                        $providerType = str_replace(',', ', ', (string)($job['service_provider_type'] ?? 'individual'));
                        $providerType = ucwords(str_replace('_', ' ', $providerType));
                        $postedDate = $job['posted_date'] ?? null;
                        $title = $job['title'] ?? ($job['category_name'] ?? 'Job Request');
                        $providerName = trim((string)($job['provider_name'] ?? ''));
                        $providerTypeKey = strtolower((string)($job['service_provider_type'] ?? 'individual'));
                        if ($providerTypeKey === 'company') {
                            $providerTypeLabel = 'company';
                        } else {
                            $providerTypeLabel = 'repairer';
                        }
                        ?>
                            <div class="job-card <?php echo $isDirectRequest ? 'direct-job-card' : ''; ?>"
                                data-status="<?php echo htmlspecialchars($statusClass); ?>"
                                data-request-type="<?php echo htmlspecialchars($job['request_type']); ?>"
                                data-request-id="<?php echo (int)$job['request_id']; ?>"
                                data-provider-id="<?php echo (int)($job['provider_id'] ?? 0); ?>"
                                data-provider-type="<?php echo htmlspecialchars($job['provider_type'] ?? ($job['service_provider_type'] ?? '')); ?>"
                                data-category-id="<?php echo (int)($job['category_id'] ?? 0); ?>">
                            <div class="job-card-header">
                                <div>
                                    <h3 class="job-title"><?php echo htmlspecialchars($title); ?></h3>
                                    <p class="job-date">
                                        <i class="fas fa-calendar-alt"></i> 
                                        Posted on <?php echo $postedDate ? date('F j, Y \a\t g:i A', strtotime($postedDate)) : 'N/A'; ?>
                                    </p>
                                </div>
                                <div class="job-badges">
                                    <?php if ($isDirectRequest): ?>
                                        <span class="job-badge badge-direct-request">
                                            <i class="fas fa-location-arrow"></i> Direct Request
                                        </span>
                                        <span class="job-badge badge-status status-<?php echo $statusClass; ?>">
                                        <?php echo $statusLabel; ?>
                                    </span>
                                    <?php else: ?>
                                        <span class="job-badge badge-status status-<?php echo $statusClass; ?>">
                                            <?php echo $statusLabel; ?>
                                        </span>
                                        <span class="job-badge badge-urgency urgency-<?php echo $job['urgency']; ?>">
                                            <i class="fas fa-bolt"></i> <?php echo ucfirst($job['urgency']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <p class="job-description"><?php echo htmlspecialchars($job['description']); ?></p>

                            <?php if ($isDirectRequest): ?>
                                <p class="direct-job-note" style="color: red; font-weight: 600; font-size: var(--font-size-lg);">
                                    This is a direct job request made to <?php echo htmlspecialchars($providerName !== '' ? $providerName : 'the selected provider'); ?>.
                                </p>
                            <?php endif; ?>

                            <div class="job-details-grid">
                                <div class="job-detail">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <div>
                                        <span class="detail-label">Address</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($job['address']); ?></span>
                                    </div>
                                </div>
                                <div class="job-detail">
                                    <i class="fas fa-user-tie"></i>
                                    <div>
                                        <span class="detail-label">Provider Type</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($providerType); ?></span>
                                    </div>
                                </div>
                                <div class="job-detail">
                                    <i class="fas fa-tag"></i>
                                    <div>
                                        <span class="detail-label">Category</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($job['category_name']); ?></span>
                                    </div>
                                </div>
                                <?php if ($job['photos']): ?>
                                <div class="job-detail">
                                    <i class="fas fa-image"></i>
                                    <div>
                                        <span class="detail-label">Photo</span>
                                        <a href="/2nd-Year-Group-Project/FixLanka/<?php echo htmlspecialchars($job['photos']); ?>" 
                                           target="_blank" class="detail-value">View Photo</a>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="job-actions">
                                <?php if ($showInProgressDetails): ?>
                                    <button type="button"
                                            class="action-btn btn-view-quotes"
                                            onclick="openInProgressJobFullDetails(<?php echo (int)$job['request_id']; ?>, '<?php echo $isDirectRequest ? 'direct' : 'regular'; ?>')">
                                        <i class="fas fa-eye"></i> View Full Details
                                    </button>
                                <?php endif; ?>

                                <?php if (!$showCollaborationButton): ?>
                                    <button type="button"
                                            class="action-btn btn-view-quotes"
                                            onclick="openJobQuotesModal(<?php echo (int)$job['request_id']; ?>, '<?php echo $isDirectRequest ? 'direct' : 'regular'; ?>')">
                                        <i class="fas fa-file-invoice-dollar"></i> View Received Quotations
                                    </button>
                                <?php else: ?>
                                    <button type="button"
                                            class="action-btn btn-collaboration"
                                            onclick="openJobCollaborationModal(<?php echo (int)$job['request_id']; ?>, '<?php echo $isDirectRequest ? 'direct' : 'regular'; ?>')">
                                        <i class="fas fa-comments"></i> Job Collaboration
                                    </button>
                                <?php endif; ?>

                                <?php if ($isPendingRegular): ?>
                                    <!-- Edit and Delete buttons only for pending jobs -->
                                    <button onclick="openEditModal(<?php echo $job['request_id']; ?>)" class="action-btn btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="/2nd-Year-Group-Project/FixLanka/delete-job" method="POST" style="display:inline;" 
                                          onsubmit="return confirm('Are you sure you want to delete this job request?');">
                                        <input type="hidden" name="request_id" value="<?php echo $job['request_id']; ?>">
                                        <button type="submit" class="action-btn btn-delete">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                <?php elseif ($isPendingDirect): ?>
                                    <button type="button" class="action-btn btn-edit" onclick="openDirectEditModal(<?php echo (int)$job['request_id']; ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="action-btn btn-delete" onclick="deleteDirectRequest(<?php echo (int)$job['request_id']; ?>)">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                <?php elseif ($showCollaborationButton): ?>
                                    <span class="read-only-badge direct-read-only-badge">
                                        <i class="fas fa-route"></i> Workflow active
                                    </span>
                                <?php elseif ($isDirectRequest): ?>
                                    <span class="read-only-badge direct-read-only-badge">
                                        <i class="fas fa-paper-plane"></i> Direct job request submitted
                                    </span>
                                <?php else: ?>
                                    <!-- Read-only indicator for non-pending jobs -->
                                    <span class="read-only-badge">
                                        <i class="fas fa-lock"></i> Read Only
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="quotes-received-section" id="quotesReceivedSection" style="display: none;">
                <div class="quotes-received-header">
                    <h2 class="quotes-received-title">Received Quotes For Your Jobs</h2>
                    <p class="quotes-received-subtitle">Quotes from individual repairers and companies for jobs you published.</p>
                </div>
                <div class="quotes-filter-tabs" id="quotesFilterTabs">
                    <button class="quotes-filter-tab active" type="button" data-quote-status="pending">Pending</button>
                    <button class="quotes-filter-tab" type="button" data-quote-status="accepted">Accepted</button>
                    <button class="quotes-filter-tab" type="button" data-quote-status="rejected">Rejected</button>
                    <button class="quotes-filter-tab" type="button" data-quote-status="completed">Completed</button>
                </div>
                <div class="quotes-received-list" id="quotesReceivedList">
                    <div class="quote-empty-state">Loading quotes...</div>
                </div>
            </div>
        </div>
    </main>

    <!-- Edit Modal -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="modal-title">Edit Job Request</h2>
                <button class="modal-close" onclick="closeEditModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <form id="editForm" action="/2nd-Year-Group-Project/FixLanka/update-job" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="edit_request_id" name="request_id">
                    
                    <div class="form-group">
                        <label for="edit_title">Job Title <span class="required">*</span></label>
                        <input type="text" id="edit_title" name="title" required placeholder="e.g., Kitchen Sink Repair, AC Installation">
                    </div>

                    <div class="form-group">
                        <label for="edit_category_id">Category <span class="required">*</span></label>
                        <select id="edit_category_id" name="category_id" required>
                            <option value="">Select a category</option>
                            <option value="1">Plumbing</option>
                            <option value="2">Electrical</option>
                            <option value="3">HVAC</option>
                            <option value="4">Cleaning</option>
                            <option value="5">Carpentry</option>
                            <option value="6">Painting</option>
                            <option value="7">Appliance Repair</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_description">Description <span class="required">*</span></label>
                        <textarea id="edit_description" name="description" rows="5" required placeholder="Describe your job in detail..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="edit_district">District <span class="required">*</span></label>
                        <select id="edit_district" name="district" required>
                            <option value="">Select your district</option>
                            <option value="Colombo">Colombo</option>
                            <option value="Gampaha">Gampaha</option>
                            <option value="Kalutara">Kalutara</option>
                            <option value="Kandy">Kandy</option>
                            <option value="Matale">Matale</option>
                            <option value="Nuwara Eliya">Nuwara Eliya</option>
                            <option value="Galle">Galle</option>
                            <option value="Matara">Matara</option>
                            <option value="Hambantota">Hambantota</option>
                            <option value="Jaffna">Jaffna</option>
                            <option value="Kilinochchi">Kilinochchi</option>
                            <option value="Mannar">Mannar</option>
                            <option value="Vavuniya">Vavuniya</option>
                            <option value="Mullaitivu">Mullaitivu</option>
                            <option value="Batticaloa">Batticaloa</option>
                            <option value="Ampara">Ampara</option>
                            <option value="Trincomalee">Trincomalee</option>
                            <option value="Kurunegala">Kurunegala</option>
                            <option value="Puttalam">Puttalam</option>
                            <option value="Anuradhapura">Anuradhapura</option>
                            <option value="Polonnaruwa">Polonnaruwa</option>
                            <option value="Badulla">Badulla</option>
                            <option value="Monaragala">Monaragala</option>
                            <option value="Ratnapura">Ratnapura</option>
                            <option value="Kegalle">Kegalle</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_address">Address <span class="required">*</span></label>
                        <input type="text" id="edit_address" name="address" required placeholder="Enter your full address (street, area)">
                    </div>

                    <div class="form-group">
                        <label>Service Provider Type <span class="required">*</span></label>
                        <div class="checkbox-group">
                            <div class="checkbox-item">
                                <input type="checkbox" id="edit_provider_individual" name="provider_type[]" value="individual">
                                <label for="edit_provider_individual" class="checkbox-label">
                                    <i class="fas fa-user"></i>
                                    Individual Repairer
                                </label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="edit_provider_company" name="provider_type[]" value="company">
                                <label for="edit_provider_company" class="checkbox-label">
                                    <i class="fas fa-building"></i>
                                    Company
                                </label>
                            </div>
                        </div>
                        <small class="hint-text">Select at least one service provider type</small>
                        <span class="error-message" id="edit-provider-error"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_urgency">Urgency Level <span class="required">*</span></label>
                        <select id="edit_urgency" name="urgency" required>
                            <option value="medium">Medium</option>
                            <option value="urgent">Urgent <span class="pro-badge">PRO</span></option>
                        </select>
                        <div class="urgency-info">
                            <i class="fas fa-info-circle"></i>
                            <span>Urgent priority is a PRO feature - Your job will be highlighted to service providers</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_finish_date">Expected Finish Date <span class="required">*</span></label>
                        <input type="date" id="edit_finish_date" name="finish_date" required>
                        <small class="hint-text">When do you need this work completed?</small>
                    </div>

                    <div class="form-group">
                        <label for="edit_photos">Photos (Optional)</label>
                        <div class="file-upload-wrapper">
                            <input type="file" id="edit_photos" name="photos" accept="image/*" onchange="updateEditFileName(this)">
                            <label for="edit_photos" class="file-upload-label">
                                <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                <span class="upload-text">Choose File</span>
                            </label>
                        </div>
                        <small class="hint-text">Upload a new photo to replace the existing one</small>
                        <div id="edit-file-name-display" class="file-preview"></div>
                    </div>
                    
                    <div class="modal-actions">
                        <button type="button" class="action-btn btn-secondary" onclick="closeEditModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="action-btn btn-primary">
                            <i class="fas fa-save"></i> Update Job
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="jobQuotesModal" class="modal-overlay">
        <div class="modal-container quotes-request-modal-container">
            <div class="modal-header">
                <h2 class="modal-title">Received Quotations</h2>
                <button class="modal-close" type="button" onclick="closeJobQuotesModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <div class="quotes-request-list" id="jobQuotesList">
                    <div class="quote-empty-state">Loading quotes...</div>
                </div>
            </div>
        </div>
    </div>

    <div id="quoteDetailsModal" class="modal-overlay">
        <div class="modal-container quote-details-modal-container">
            <div class="modal-header">
                <h2 class="modal-title">Quotation Details</h2>
                <button class="modal-close" type="button" onclick="closeQuoteDetailsModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content" id="quoteDetailsContent">
                <div class="quote-empty-state">Select a quote to view details.</div>
            </div>
            <div class="modal-actions quote-details-actions">
                <button type="button" class="action-btn btn-secondary" onclick="closeQuoteDetailsModal()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="action-btn btn-reject-sm" id="quoteRejectBtn">
                    <i class="fas fa-xmark"></i> Reject
                </button>
                <a href="#" class="action-btn btn-negotiate-sm" id="quoteNegotiateBtn">
                    <i class="fas fa-message"></i> Negotiate
                </a>
                <button type="button" class="action-btn btn-success-sm" id="quoteAcceptBtn">
                    <i class="fas fa-check"></i> Accept
                </button>
            </div>
        </div>
    </div>

    <div id="quoteNegotiationModal" class="modal-overlay">
        <div class="modal-container quote-negotiation-modal-container">
            <div class="modal-header">
                <h2 class="modal-title">Price Negotiation</h2>
                <button class="modal-close" type="button" onclick="closeQuoteNegotiationModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <div class="quote-negotiation-summary" id="quoteNegotiationSummary">
                    <div class="quote-empty-state">Select a pending quote to negotiate.</div>
                </div>

                <form id="quoteNegotiationForm" class="quote-negotiation-form">
                    <div class="form-group">
                        <label for="negotiationProposedPrice">Your Negotiated Price (LKR) <span class="required">*</span></label>
                        <input type="number" id="negotiationProposedPrice" min="1" step="0.01" required placeholder="Enter your proposed amount">
                    </div>
                    <div class="form-group">
                        <label for="negotiationMessage">Message (Optional)</label>
                        <textarea id="negotiationMessage" rows="3" placeholder="Add a short reason for your proposal"></textarea>
                    </div>
                    <div class="modal-actions quote-negotiation-actions">
                        <button type="button" class="action-btn btn-secondary" onclick="closeQuoteNegotiationModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="action-btn btn-primary" id="negotiationSendBtn">
                            <i class="fas fa-paper-plane"></i> Send Proposal
                        </button>
                    </div>
                </form>

                <section class="quote-negotiation-history-wrap">
                    <h3>Negotiation History</h3>
                    <div class="quote-negotiation-history" id="quoteNegotiationHistory">
                        <div class="quote-empty-state">No negotiations yet.</div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <div id="latestQuoteNegotiationModal" class="modal-overlay">
        <div class="modal-container latest-quote-negotiation-modal-container">
            <div class="modal-header">
                <h2 class="modal-title">Latest Negotiation</h2>
                <button class="modal-close" type="button" onclick="closeLatestQuoteNegotiationModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content" id="latestQuoteNegotiationContent">
                <div class="quote-empty-state">Select a quote to view its latest negotiation.</div>
            </div>
            <div class="modal-actions quote-negotiation-actions">
                <button type="button" class="action-btn btn-success-sm" id="latestNegotiationAcceptBtn" style="display:none;">
                    <i class="fas fa-check"></i> Accept
                </button>
                <button type="button" class="action-btn btn-reject-sm" id="latestNegotiationRejectBtn" style="display:none;">
                    <i class="fas fa-xmark"></i> Reject
                </button>
                <button type="button" class="action-btn btn-secondary" onclick="closeLatestQuoteNegotiationModal()">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>

    <div id="jobCollaborationModal" class="modal-overlay">
        <div class="modal-container collaboration-modal-container">
            <div class="modal-header">
                <h2 class="modal-title">Job Collaboration</h2>
                <button class="modal-close" type="button" onclick="closeJobCollaborationModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <div class="collaboration-checks">
                    <div class="collaboration-check-item" id="collabUserCompleted">User completion: pending</div>
                    <div class="collaboration-check-item" id="collabProviderCompleted">Provider completion: pending</div>
                    <div class="collaboration-check-item" id="collabUserPaid">User payment: pending</div>
                    <div class="collaboration-check-item" id="collabProviderPaid">Provider payment: pending</div>
                </div>

                <div class="collaboration-actions-grid">
                    <section class="collaboration-panel">
                        <h3>Progress Confirmation</h3>
                        <div class="collaboration-inline-actions collaboration-progress-actions">
                            <button type="button" class="action-btn btn-primary" id="collabMarkCompletedBtn">
                                <i class="fas fa-flag-checkered"></i> Mark Job Completed
                            </button>
                            <button type="button" class="action-btn btn-secondary" id="collabResetCompletedBtn" style="display:none;">
                                <i class="fas fa-rotate-left"></i> Redo
                            </button>
                        </div>
                        <div class="collaboration-inline-actions collaboration-progress-actions">
                            <button type="button" class="action-btn btn-primary" id="collabConfirmPaymentBtn">
                                <i class="fas fa-wallet"></i> Confirm Payment Done
                            </button>
                            <button type="button" class="action-btn btn-secondary" id="collabResetPaymentBtn" style="display:none;">
                                <i class="fas fa-rotate-left"></i> Redo
                            </button>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <div id="collaborationReviewModal" class="modal-overlay">
        <div class="modal-container collaboration-modal-container">
            <div class="modal-header">
                <h2 class="modal-title">Give a Review</h2>
                <button class="modal-close" type="button" onclick="closeCollaborationReviewModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <section class="collaboration-panel collaboration-review-panel is-locked" id="collabRatingPanel">
                    <h3>Review</h3>
                    <p class="collaboration-hint collab-review-lock-text" id="collabReviewLockText">Job and payment must be completed.</p>
                    <select id="collabRatingValue">
                        <option value="">Select rating</option>
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Good</option>
                        <option value="3">3 - Average</option>
                        <option value="2">2 - Poor</option>
                        <option value="1">1 - Bad</option>
                    </select>
                    <textarea id="collabRatingComment" rows="2" placeholder="Optional review comment"></textarea>
                    <button type="button" class="action-btn btn-secondary" id="collabEditRatingBtn" style="display:none;">
                        <i class="fas fa-pen"></i> Edit Review
                    </button>
                    <button type="button" class="action-btn btn-success-sm" id="collabSubmitRatingBtn">
                        <i class="fas fa-star"></i> Submit Rating
                    </button>
                </section>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/direct-job-request-popup.php'; ?>

    <script>
    const regularJobs = <?php echo json_encode($jobRequests); ?>;
    const directJobs = <?php echo json_encode($directJobRequests); ?>;
    const jobs = regularJobs;
    const directJobsById = new Map((Array.isArray(directJobs) ? directJobs : []).map((job) => [Number(job.request_id), job]));

    const USER_QUOTES_API = '/2nd-Year-Group-Project/FixLanka/api/user-quotes.php';
    const DIRECT_JOB_API = '/2nd-Year-Group-Project/FixLanka/api/user/direct-job-requests.php';
    const JOB_COLLAB_API = '/2nd-Year-Group-Project/FixLanka/api/job-collaboration.php';
    const QUOTE_NEGOTIATION_API = '/2nd-Year-Group-Project/FixLanka/api/quote-negotiations.php';

    const jobsPostedBtn = document.getElementById('jobsPostedBtn');
    const quotesReceivedBtn = document.getElementById('quotesReceivedBtn');
    const jobsFilterTabs = document.getElementById('jobsFilterTabs');
    const jobsContainerEl = document.querySelector('.jobs-container');
    const quotesReceivedSection = document.getElementById('quotesReceivedSection');
    const quotesReceivedList = document.getElementById('quotesReceivedList');
    const quotesReceivedPill = document.getElementById('quotesReceivedPill');
    const quotesFilterTabs = document.querySelectorAll('.quotes-filter-tab');
    const jobQuotesModal = document.getElementById('jobQuotesModal');
    const jobQuotesList = document.getElementById('jobQuotesList');
    const jobQuotesSubtitle = document.getElementById('jobQuotesSubtitle');
    const quoteDetailsModal = document.getElementById('quoteDetailsModal');
    const quoteDetailsContent = document.getElementById('quoteDetailsContent');
    const quoteAcceptBtn = document.getElementById('quoteAcceptBtn');
    const quoteRejectBtn = document.getElementById('quoteRejectBtn');
    const quoteNegotiateBtn = document.getElementById('quoteNegotiateBtn');
    const quoteNegotiationModal = document.getElementById('quoteNegotiationModal');
    const quoteNegotiationSummary = document.getElementById('quoteNegotiationSummary');
    const quoteNegotiationHistory = document.getElementById('quoteNegotiationHistory');
    const quoteNegotiationForm = document.getElementById('quoteNegotiationForm');
    const negotiationProposedPrice = document.getElementById('negotiationProposedPrice');
    const negotiationMessage = document.getElementById('negotiationMessage');
    const negotiationSendBtn = document.getElementById('negotiationSendBtn');
    const latestQuoteNegotiationModal = document.getElementById('latestQuoteNegotiationModal');
    const latestQuoteNegotiationContent = document.getElementById('latestQuoteNegotiationContent');
    const latestNegotiationAcceptBtn = document.getElementById('latestNegotiationAcceptBtn');
    const latestNegotiationRejectBtn = document.getElementById('latestNegotiationRejectBtn');
    const jobCollaborationModal = document.getElementById('jobCollaborationModal');
    const collaborationReviewModal = document.getElementById('collaborationReviewModal');
    const collabCurrentPhase = document.getElementById('collabCurrentPhase');
    const collabAgreedPrice = document.getElementById('collabAgreedPrice');
    const collabPendingPrice = document.getElementById('collabPendingPrice');
    const collabUserCompleted = document.getElementById('collabUserCompleted');
    const collabProviderCompleted = document.getElementById('collabProviderCompleted');
    const collabUserPaid = document.getElementById('collabUserPaid');
    const collabProviderPaid = document.getElementById('collabProviderPaid');
    const collabEventsList = document.getElementById('collabEventsList');
    const collabProposedPriceInput = document.getElementById('collabProposedPriceInput');
    const collabPriceNoteInput = document.getElementById('collabPriceNoteInput');
    const collabProposePriceBtn = document.getElementById('collabProposePriceBtn');
    const collabPendingProposalText = document.getElementById('collabPendingProposalText');
    const collabAcceptPriceBtn = document.getElementById('collabAcceptPriceBtn');
    const collabRejectPriceBtn = document.getElementById('collabRejectPriceBtn');
    const collabMarkCompletedBtn = document.getElementById('collabMarkCompletedBtn');
    const collabConfirmPaymentBtn = document.getElementById('collabConfirmPaymentBtn');
    const collabResetCompletedBtn = document.getElementById('collabResetCompletedBtn');
    const collabResetPaymentBtn = document.getElementById('collabResetPaymentBtn');
    const collabNoteInput = document.getElementById('collabNoteInput');
    const collabSendNoteBtn = document.getElementById('collabSendNoteBtn');
    const collabRatingPanel = document.getElementById('collabRatingPanel');
    const collabReviewLockText = document.getElementById('collabReviewLockText');
    const collabRatingValue = document.getElementById('collabRatingValue');
    const collabRatingComment = document.getElementById('collabRatingComment');
    const collabEditRatingBtn = document.getElementById('collabEditRatingBtn');
    const collabSubmitRatingBtn = document.getElementById('collabSubmitRatingBtn');
    const JOB_HISTORY_VIEW_KEY = 'jobHistory.activeView';

    const directJobRequestModal = document.getElementById('directJobRequestModal');
    const directJobRequestCloseBtn = document.getElementById('directJobRequestCloseBtn');
    const directJobRequestCancelBtn = document.getElementById('directJobRequestCancelBtn');
    const directJobRequestForm = document.getElementById('directJobRequestForm');
    const directJobRequestError = document.getElementById('directJobRequestError');
    const directJobRequestSuccess = document.getElementById('directJobRequestSuccess');
    const directJobRequestProviderLabel = document.getElementById('directJobRequestProviderLabel');
    const directJobProviderId = document.getElementById('directJobProviderId');
    const directJobProviderType = document.getElementById('directJobProviderType');
    const directJobCategory = document.getElementById('directJobCategory');
    const directJobCategoryDisplay = document.getElementById('directJobCategoryDisplay');
    const directJobCategoryList = document.getElementById('directJobCategoryList');
    const directJobAddress = document.getElementById('directJobAddress');
    const directJobDistrict = document.getElementById('directJobDistrict');
    const directJobFinishDate = document.getElementById('directJobFinishDate');
    const directJobPhotos = document.getElementById('directJobPhotos');
    const directJobPhotoPreview = document.getElementById('directJobPhotoPreview');
    const directJobRequestSubmitBtn = document.getElementById('directJobRequestSubmitBtn');

    let currentQuoteStatusFilter = 'pending';
    let currentRequestQuotesContext = { requestId: null, requestType: null };
    let currentlyOpenedQuote = null;
    let currentNegotiationQuote = null;
    let currentLatestNegotiationQuote = null;
    let currentLatestNegotiationItem = null;
    let currentCollaboration = null;
    let currentCollabContext = { requestId: null, requestType: null };
    let collabRatingEditMode = false;
    let directEditRequestId = null;

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function formatMoney(value) {
        const n = Number(value);
        if (!Number.isFinite(n)) return 'LKR 0';
        return `LKR ${n.toLocaleString(undefined, { maximumFractionDigits: 2 })}`;
    }

    function formatDateOnly(value) {
        if (!value) return 'N/A';
        const d = new Date(value);
        if (Number.isNaN(d.getTime())) return 'N/A';
        return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function formatDateTime(value) {
        if (!value) return 'N/A';
        const d = new Date(value);
        if (Number.isNaN(d.getTime())) return 'N/A';
        return d.toLocaleString(undefined, { year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
    }

    function readableStatus(status) {
        const normalized = String(status || 'pending').toLowerCase();
        if (normalized === 'successful') {
            return 'completed';
        }
        return normalized.replaceAll('_', ' ');
    }

    function normalizeProviderType(providerType) {
        const normalized = String(providerType || '').toLowerCase().trim();
        return normalized === 'company' ? 'company' : 'individual';
    }

    async function fetchJson(url, options) {
        const response = await fetch(url, { credentials: 'same-origin', ...(options || {}) });
        const text = await response.text();
        let result;
        try {
            result = JSON.parse(text);
        } catch (error) {
            console.error('[QuotesAPI] Invalid JSON response', {
                url,
                status: response.status,
                preview: String(text || '').slice(0, 500)
            });
            throw new Error('Invalid JSON response');
        }

        if (!response.ok || result?.success === false) {
            console.error('[QuotesAPI] Request failed', {
                url,
                status: response.status,
                result
            });
            throw new Error(result?.message || `Request failed (${response.status})`);
        }

        return result;
    }

    function updateEditFileName(input) {
        const fileDisplay = document.getElementById('edit-file-name-display');
        if (!fileDisplay) return;
        if (input.files && input.files[0]) {
            fileDisplay.innerHTML = `<div class="file-preview-item">${escapeHtml(input.files[0].name)}</div>`;
        } else {
            fileDisplay.innerHTML = '';
        }
    }

    function setQuotesPill(count) {
        if (!quotesReceivedPill) return;
        if (count > 0) {
            quotesReceivedPill.textContent = String(count);
            quotesReceivedPill.style.display = 'inline-flex';
        } else {
            quotesReceivedPill.style.display = 'none';
        }
    }

    function formatStatusLabel(status) {
        return readableStatus(status).replace(/\b\w/g, (char) => char.toUpperCase());
    }

    function quoteDisplayStatus(quote) {
        const rawQuoteStatus = String(quote?.status || 'pending').toLowerCase();
        const requestStatus = String(quote?.request_status || quote?.job_status || '').toLowerCase();

        if (requestStatus === 'completed' && (rawQuoteStatus === 'accepted' || rawQuoteStatus === 'successful')) {
            return 'completed';
        }

        return rawQuoteStatus;
    }

    function showActionToast(message) {
        let toast = document.getElementById('jobHistoryActionToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'jobHistoryActionToast';
            toast.className = 'job-history-toast';
            document.body.appendChild(toast);
        }

        toast.textContent = String(message || 'Action completed successfully.');
        toast.classList.add('show');

        window.clearTimeout(window.__jobHistoryToastTimer);
        window.__jobHistoryToastTimer = window.setTimeout(() => {
            toast.classList.remove('show');
        }, 2200);
    }

    function refreshJobTabCounts() {
        const cards = Array.from(document.querySelectorAll('.job-card'));
        const counts = {
            all: cards.length,
            pending: 0,
            in_progress: 0,
            completed: 0,
            cancelled: 0,
        };

        cards.forEach((card) => {
            const status = String(card.dataset.status || '').toLowerCase();
            if (Object.prototype.hasOwnProperty.call(counts, status)) {
                counts[status] += 1;
            }
        });

        document.querySelectorAll('.filter-tab').forEach((tab) => {
            const status = String(tab.dataset.status || 'all');
            const countNode = tab.querySelector('.tab-count');
            if (!countNode || !Object.prototype.hasOwnProperty.call(counts, status)) return;
            countNode.textContent = String(counts[status]);
        });
    }

    function applyActiveJobFilter() {
        const activeTab = document.querySelector('.filter-tab.active');
        const activeStatus = activeTab ? String(activeTab.dataset.status || 'all') : 'all';

        document.querySelectorAll('.job-card').forEach((card) => {
            card.style.display = activeStatus === 'all' || card.dataset.status === activeStatus ? 'block' : 'none';
        });
    }

    function collaborationActionButtonHtml(requestId, requestType) {
        const safeType = String(requestType || 'regular').toLowerCase() === 'direct' ? 'direct' : 'regular';
        return `
            <button type="button" class="action-btn btn-collaboration" onclick="openJobCollaborationModal(${Number(requestId)}, '${safeType}')">
                <i class="fas fa-comments"></i> Job Collaboration
            </button>
        `;
    }

    function updateJobCardStatus(requestId, requestType, nextStatus) {
        const numericRequestId = Number(requestId);
        const normalizedRequestType = String(requestType || 'regular').toLowerCase() === 'direct' ? 'direct' : 'regular';
        const normalizedStatus = String(nextStatus || '').toLowerCase().trim();
        if (!Number.isFinite(numericRequestId) || numericRequestId <= 0 || normalizedStatus === '') return;

        const card = document.querySelector(`.job-card[data-request-id="${numericRequestId}"][data-request-type="${normalizedRequestType}"]`);
        if (!card) return;

        card.dataset.status = normalizedStatus;

        const statusBadge = card.querySelector('.badge-status');
        if (statusBadge) {
            Array.from(statusBadge.classList).forEach((className) => {
                if (className.startsWith('status-')) {
                    statusBadge.classList.remove(className);
                }
            });
            statusBadge.classList.add(`status-${normalizedStatus}`);
            statusBadge.textContent = formatStatusLabel(normalizedStatus);
        }

        if (normalizedStatus === 'in_progress' || normalizedStatus === 'completed') {
            const actionsContainer = card.querySelector('.job-actions');
            if (actionsContainer) {
                const collaborationHtml = `
                    <button type="button" class="action-btn btn-view-quotes" onclick="openInProgressJobFullDetails(${numericRequestId}, '${normalizedRequestType}')">
                        <i class="fas fa-eye"></i> View Full Details
                    </button>
                `;
                const workflowBadgeHtml = '<span class="read-only-badge direct-read-only-badge"><i class="fas fa-route"></i> Workflow active</span>';
                actionsContainer.innerHTML = `${collaborationHtml}${workflowBadgeHtml}`;
            }
        }

        refreshJobTabCounts();
        applyActiveJobFilter();
    }

    function quoteDetailsHtml(quote) {
        const displayStatus = quoteDisplayStatus(quote);
        const companyFields = quote.source === 'company'
            ? `
                <div class="quote-meta-item"><strong>Labor:</strong> ${escapeHtml(formatMoney(quote.labor_cost))}</div>
                <div class="quote-meta-item"><strong>Materials:</strong> ${escapeHtml(formatMoney(quote.material_cost))}</div>
                <div class="quote-meta-item"><strong>Transport:</strong> ${escapeHtml(formatMoney(quote.transport_cost))}</div>
                <div class="quote-meta-item"><strong>Other Charges:</strong> ${escapeHtml(formatMoney(quote.other_charges))}</div>
                <div class="quote-meta-item"><strong>Start Date:</strong> ${escapeHtml(formatDateOnly(quote.company_start_date))}</div>
                <div class="quote-meta-item"><strong>Completion Date:</strong> ${escapeHtml(formatDateOnly(quote.company_completion_date))}</div>
                <div class="quote-meta-item"><strong>Payment Method:</strong> ${escapeHtml(quote.payment_method || 'N/A')}</div>
            `
            : `
                <div class="quote-meta-item"><strong>Estimated Days:</strong> ${escapeHtml(quote.estimated_days ?? 'N/A')}</div>
                <div class="quote-meta-item"><strong>Warranty:</strong> ${escapeHtml(quote.warranty_period ?? 'N/A')}</div>
                <div class="quote-meta-item"><strong>Valid Until:</strong> ${escapeHtml(formatDateOnly(quote.valid_until))}</div>
                <div class="quote-meta-item"><strong>Materials Included:</strong> ${quote.materials_included == 1 ? 'Yes' : 'No'}</div>
            `;

        return `
            <div class="quote-details-head">
                <h3 class="quote-job-title">${escapeHtml(quote.job_title || 'Quotation')}</h3>
                <span class="quote-status-pill quote-status-${escapeHtml(displayStatus)}">${escapeHtml(readableStatus(displayStatus))}</span>
            </div>
            <div class="quote-provider">
                <img src="${escapeHtml(quote.provider_avatar || 'https://via.placeholder.com/48')}" alt="Provider" class="provider-avatar">
                <div class="provider-info">
                    <span class="provider-name">${escapeHtml(quote.provider_name || (quote.source === 'company' ? 'Company' : 'Repairer'))}</span>
                    <span class="provider-type">${escapeHtml(quote.provider_type || (quote.source === 'company' ? 'Company' : 'Individual'))}</span>
                </div>
                <div class="quote-price">${escapeHtml(formatMoney(quote.amount))}</div>
            </div>
            <div class="quote-meta-grid">
                <div class="quote-meta-item"><strong>Request Type:</strong> ${escapeHtml(quote.request_type || 'regular')}</div>
                <div class="quote-meta-item"><strong>Category:</strong> ${escapeHtml(quote.category_name || 'N/A')}</div>
                <div class="quote-meta-item"><strong>Quote Sent:</strong> ${escapeHtml(formatDateTime(quote.created_at))}</div>
                <div class="quote-meta-item"><strong>Job Posted:</strong> ${escapeHtml(formatDateTime(quote.job_posted_at))}</div>
                ${companyFields}
            </div>
            <p class="quote-message">${escapeHtml(quote.quote_message || quote.message || 'No additional notes.')}</p>
            `;
    }

    function completedSummaryHtml(summary) {
        const review = summary && summary.review ? summary.review : null;
        const reviewText = review && review.comment ? review.comment : 'No written review found.';
        const reviewRating = review && Number.isFinite(Number(review.rating)) ? `${Number(review.rating)}/5` : 'Not available';
        const reviewDate = review && review.created_at ? formatDateTime(review.created_at) : 'N/A';

        return `
            <div class="quote-details-head">
                <h3 class="quote-job-title">Completed Quote Summary</h3>
                <span class="quote-status-pill quote-status-completed">completed</span>
            </div>
            <div class="quote-meta-grid">
                <div class="quote-meta-item"><strong>Provider:</strong> ${escapeHtml(summary?.provider_name || 'Provider')}</div>
                <div class="quote-meta-item"><strong>Price:</strong> ${escapeHtml(formatMoney(summary?.price || 0))}</div>
                <div class="quote-meta-item"><strong>Your Rating:</strong> ${escapeHtml(reviewRating)}</div>
                <div class="quote-meta-item"><strong>Reviewed At:</strong> ${escapeHtml(reviewDate)}</div>
            </div>
            <p class="quote-message">${escapeHtml(reviewText)}</p>
        `;
    }

    function quoteCardActionsHtml(quote, compact, quotePayload, displayStatus) {
        const status = String(displayStatus || 'pending').toLowerCase();
        const source = String(quote.source || '').toLowerCase();
        const hasCompanyContract = Number(quote.has_contract || 0) === 1 || Number(quote.contract_id || 0) > 0;

        if (!compact && source === 'company' && status === 'accepted') {
            return `
                <button
                    type="button"
                    class="action-btn ${hasCompanyContract ? 'btn-success-sm' : 'btn-secondary'}"
                    ${hasCompanyContract ? '' : 'disabled'}
                    onclick='event.stopPropagation();${hasCompanyContract ? "window.location.href=\"/2nd-Year-Group-Project/FixLanka/my-contracts\"" : "return false"}'>
                    <i class="fas fa-file-contract"></i> Company Contract
                </button>
                <button
                    type="button"
                    class="action-btn btn-reject-sm"
                    onclick='event.stopPropagation();resetQuoteToPending(${quotePayload}, "accepted")'>
                    <i class="fas fa-rotate-left"></i> Cancel Acceptance
                </button>
            `;
        }

        if (status === 'accepted') {
            return `
                <button
                    type="button"
                    class="action-btn btn-collaboration"
                    id="quoteNegotiationButton"
                    onclick='event.stopPropagation();openJobCollaborationModal(${Number(quote.request_id)}, "${escapeHtml(String(quote.request_type || 'regular').toLowerCase() === 'direct' ? 'direct' : 'regular')}")'>
                    <i class="fas fa-comments"></i> Job Collaboration
                </button>
                <button
                    type="button"
                    class="action-btn btn-reject-sm"
                    onclick='event.stopPropagation();resetQuoteToPending(${quotePayload}, "accepted")'>
                    <i class="fas fa-rotate-left"></i> Cancel Acceptance
                </button>
            `;
        }

        if (status === 'rejected') {
            return `
                <button
                    type="button"
                    class="action-btn btn-secondary"
                    onclick='event.stopPropagation();resetQuoteToPending(${quotePayload}, "rejected")'>
                    <i class="fas fa-rotate-left"></i> Cancel Rejection
                </button>
            `;
        }

        if (status === 'completed') {
            return `
                <button
                    type="button"
                    class="action-btn btn-view-quotes"
                    onclick='event.stopPropagation();openCompletedQuoteSummary(${quotePayload})'>
                    <i class="fas fa-file-lines"></i> Summary
                </button>
                <button
                    type="button"
                    class="action-btn btn-success-sm"
                    onclick='event.stopPropagation();openCollaborationReviewModal(${Number(quote.request_id)}, "${escapeHtml(String(quote.request_type || 'regular').toLowerCase() === 'direct' ? 'direct' : 'regular')}")'>
                    <i class="fas fa-star"></i> Give a Review
                </button>
            `;
        }

        return `
            <button type="button" class="action-btn btn-view-quotes" onclick='event.stopPropagation();openQuoteDetails(${quotePayload})'>
                <i class="fas fa-eye"></i> View Full Details
            </button>
            ${compact ? '' : `
            <button type="button" class="action-btn btn-negotiate-sm" onclick='event.stopPropagation();openLatestQuoteNegotiationModal(${quotePayload})'>
                <i class="fas fa-clock-rotate-left"></i> Latest Negotiation
            </button>
            `}
        `;
    }

    function renderQuoteCard(quote, variant) {
        const compact = variant === 'request';
        const displayStatus = quoteDisplayStatus(quote);
        const quotePayload = JSON.stringify({ source: quote.source, quote_id: quote.quote_id, request_type: quote.request_type });
        const normalizedDisplayStatus = String(displayStatus || 'pending').toLowerCase();
        const canOpenCard = normalizedDisplayStatus === 'pending';
        const cardRole = canOpenCard ? 'button' : 'article';
        const cardTabIndex = canOpenCard ? '0' : '-1';
        const clickHandler = canOpenCard
            ? ` onclick='openQuoteDetails(${quotePayload})' onkeydown='if(event.key === "Enter" || event.key === " "){event.preventDefault();openQuoteDetails(${quotePayload});}'`
            : '';
        const extraInfo = compact
            ? `<p class="quote-job-meta">${escapeHtml(quote.provider_type || quote.source)} | ${escapeHtml(formatMoney(quote.amount))}</p>`
            : `<p class="quote-job-meta">${escapeHtml(quote.category_name || 'N/A')} | Sent ${escapeHtml(formatDateTime(quote.created_at))}</p>`;

        return `
            <article class="quote-item quote-item-${compact ? 'compact' : 'full'}" role="${cardRole}" tabindex="${cardTabIndex}"${clickHandler}>
                <div class="quote-card-top">
                    <div>
                        <h3 class="quote-job-title">${escapeHtml(quote.provider_name || (quote.source === 'company' ? 'Company' : 'Repairer'))}</h3>
                        ${extraInfo}
                    </div>
                    <span class="quote-status-pill quote-status-${escapeHtml(displayStatus)}">${escapeHtml(readableStatus(displayStatus))}</span>
                </div>
                <div class="quote-meta-grid ${compact ? 'quote-meta-grid-compact' : ''}">
                    <div class="quote-meta-item"><strong>Service Provider Type:</strong> ${escapeHtml(quote.provider_type || 'N/A')}</div>
                    <div class="quote-meta-item"><strong>Total:</strong> ${escapeHtml(formatMoney(quote.amount))}</div>
                    <div class="quote-meta-item"><strong>Request ID:</strong> #${escapeHtml(quote.request_id)}</div>
                    <div class="quote-meta-item"><strong>Quote ID:</strong> #${escapeHtml(quote.quote_id)}</div>
                </div>
                <div class="quote-actions quote-actions-inline">
                    ${quoteCardActionsHtml(quote, compact, quotePayload, normalizedDisplayStatus)}
                </div>
            </article>
        `;
    }

    async function resetQuoteToPending(payload, fromStatus) {
        const quote = findQuote(payload);
        if (!quote) {
            alert('Quote not found. Please refresh and try again.');
            return;
        }

        const label = String(fromStatus || '').toLowerCase() === 'accepted' ? 'acceptance' : 'rejection';
        if (!confirm(`Cancel this ${label} and move the quote back to pending?`)) {
            return;
        }

        try {
            await fetchJson(`${USER_QUOTES_API}?action=reset_to_pending`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    source: String(quote.source || ''),
                    quote_id: Number(quote.quote_id),
                    request_type: String(quote.request_type || 'regular')
                })
            });

            showActionToast('Quote moved back to pending.');
            await loadQuotesReceived();
        } catch (error) {
            alert(error && error.message ? error.message : 'Failed to move quote back to pending.');
        }
    }

    async function openCompletedQuoteSummary(payload) {
        const quote = findQuote(payload);
        if (!quote || !quoteDetailsModal || !quoteDetailsContent) {
            alert('Unable to open quote summary.');
            return;
        }

        quoteDetailsContent.innerHTML = '<div class="quote-empty-state">Loading completed summary...</div>';
        quoteDetailsModal.classList.add('show');
        document.body.style.overflow = 'hidden';

        if (quoteAcceptBtn) quoteAcceptBtn.style.display = 'none';
        if (quoteRejectBtn) quoteRejectBtn.style.display = 'none';
        if (quoteNegotiateBtn) quoteNegotiateBtn.style.display = 'none';

        try {
            const result = await fetchJson(`${USER_QUOTES_API}?action=completion_summary&source=${encodeURIComponent(String(quote.source || ''))}&quote_id=${encodeURIComponent(String(quote.quote_id))}&request_type=${encodeURIComponent(String(quote.request_type || 'regular'))}`);
            quoteDetailsContent.innerHTML = completedSummaryHtml(result.summary || null);
        } catch (error) {
            quoteDetailsContent.innerHTML = `<div class="quote-empty-state">${escapeHtml(error && error.message ? error.message : 'Failed to load summary.')}</div>`;
        }
    }

    function resolveNegotiationParty(role, id, quote) {
        const normalizedRole = String(role || '').toLowerCase();
        const numericId = Number(id || 0);

        if (normalizedRole === 'user') {
            return 'You';
        }

        const providerRole = String(quote?.source || '').toLowerCase() === 'company' ? 'company' : 'repairer';
        const providerId = Number(quote?.provider_id || 0);
        const providerName = String(quote?.provider_name || '').trim();
        if (normalizedRole === providerRole && numericId > 0 && providerId > 0 && numericId === providerId && providerName !== '') {
            return providerName;
        }

        const prettyRole = normalizedRole ? normalizedRole.charAt(0).toUpperCase() + normalizedRole.slice(1) : 'Participant';
        return numericId > 0 ? `${prettyRole} #${numericId}` : prettyRole;
    }

    function renderLatestNegotiationWindow(item, quote) {
        if (!latestQuoteNegotiationContent) return;
        currentLatestNegotiationItem = item || null;

        const canRespond = !!item
            && String(item.receiver_role || '').toLowerCase() === 'user'
            && ['pending', 'countered'].includes(String(item.status || '').toLowerCase());
        const canAccept = canRespond && String(item.sender_role || '').toLowerCase() === 'repairer';

        if (latestNegotiationAcceptBtn) {
            latestNegotiationAcceptBtn.style.display = canAccept ? '' : 'none';
            latestNegotiationAcceptBtn.disabled = !canAccept;
        }
        if (latestNegotiationRejectBtn) {
            latestNegotiationRejectBtn.style.display = canRespond ? '' : 'none';
            latestNegotiationRejectBtn.disabled = !canRespond;
        }

        if (!item) {
            latestQuoteNegotiationContent.innerHTML = '<div class="quote-empty-state">No negotiations found for this quote yet.</div>';
            return;
        }

        const fromParty = resolveNegotiationParty(item.sender_role, item.sender_id, quote);
        const toParty = resolveNegotiationParty(item.receiver_role, item.receiver_id, quote);
        const directionLabel = `${fromParty} to ${toParty}`;
        const statusClass = String(item.status || 'pending').toLowerCase();

        latestQuoteNegotiationContent.innerHTML = `
            <div class="quote-negotiation-summary">
                <div class="quote-negotiation-summary-item">
                    <span class="quote-negotiation-label">Job</span>
                    <span class="quote-negotiation-value">${escapeHtml(quote?.job_title || item.job_title || 'N/A')}</span>
                </div>
                <div class="quote-negotiation-summary-item">
                    <span class="quote-negotiation-label">Direction</span>
                    <span class="quote-negotiation-value">${escapeHtml(directionLabel)}</span>
                </div>
                <div class="quote-negotiation-summary-item">
                    <span class="quote-negotiation-label">Listed Price</span>
                    <span class="quote-negotiation-value">${escapeHtml(formatMoney(item.listed_price || 0))}</span>
                </div>
                <div class="quote-negotiation-summary-item">
                    <span class="quote-negotiation-label">Proposed Price</span>
                    <span class="quote-negotiation-value">${escapeHtml(formatMoney(item.proposed_price || 0))}</span>
                </div>
                <div class="quote-negotiation-summary-item">
                    <span class="quote-negotiation-label">Status</span>
                    <span class="quote-status-pill quote-status-${escapeHtml(statusClass)}">${escapeHtml(readableStatus(item.status || 'pending'))}</span>
                </div>
                <div class="quote-negotiation-summary-item">
                    <span class="quote-negotiation-label">Sent At</span>
                    <span class="quote-negotiation-value">${escapeHtml(formatDateTime(item.created_at))}</span>
                </div>
                <div class="quote-negotiation-summary-item quote-negotiation-summary-full">
                    <span class="quote-negotiation-label">Message</span>
                    <p class="quote-negotiation-description">${escapeHtml(item.message || 'No message was added for this negotiation.')}</p>
                </div>
            </div>
        `;
    }

    async function openLatestQuoteNegotiationModal(payload) {
        const quote = findQuote(payload);
        if (!quote || !latestQuoteNegotiationModal || !latestQuoteNegotiationContent) {
            alert('Unable to open latest negotiation details.');
            return;
        }

        currentLatestNegotiationQuote = quote;
        latestQuoteNegotiationContent.innerHTML = '<div class="quote-empty-state">Loading latest negotiation...</div>';

        latestQuoteNegotiationModal.classList.add('show');
        document.body.style.overflow = 'hidden';

        try {
            const latest = await fetchLatestNegotiationForQuote(quote);
            renderLatestNegotiationWindow(latest, quote);
        } catch (error) {
            const message = error && error.message ? error.message : 'Failed to load latest negotiation.';
            currentLatestNegotiationItem = null;
            if (latestNegotiationAcceptBtn) latestNegotiationAcceptBtn.style.display = 'none';
            if (latestNegotiationRejectBtn) latestNegotiationRejectBtn.style.display = 'none';
            latestQuoteNegotiationContent.innerHTML = `<div class="quote-empty-state">${escapeHtml(message)}</div>`;
        }
    }

    async function respondToLatestNegotiation(decision) {
        if (!currentLatestNegotiationItem || !currentLatestNegotiationQuote) {
            alert('Negotiation could not be identified. Please reopen this window.');
            return;
        }

        const normalizedDecision = String(decision || '').toLowerCase();
        if (!['accept', 'reject'].includes(normalizedDecision)) {
            return;
        }

        if (normalizedDecision === 'reject' && !confirm('This negotiation will be deleted. Continue?')) {
            return;
        }

        try {
            await fetchJson(`${QUOTE_NEGOTIATION_API}?action=respond`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    negotiation_id: Number(currentLatestNegotiationItem.negotiation_id),
                    decision: normalizedDecision,
                })
            });

            showActionToast(normalizedDecision === 'accept'
                ? 'Negotiation accepted successfully.'
                : 'Negotiation rejected successfully.');

            if (normalizedDecision === 'accept') {
                try {
                    const refreshedQuotes = await loadQuotesForRequest(currentLatestNegotiationQuote.request_id, currentLatestNegotiationQuote.request_type);
                    const refreshedQuote = refreshedQuotes.find((quote) =>
                        Number(quote.quote_id) === Number(currentLatestNegotiationQuote.quote_id) &&
                        String(quote.source || '') === String(currentLatestNegotiationQuote.source || '')
                    ) || null;

                    if (String(currentLatestNegotiationQuote.request_type || 'regular').toLowerCase() === 'direct') {
                        window.__lastRequestQuotes = refreshedQuotes;
                    } else {
                        window.__lastQuotes = refreshedQuotes;
                    }

                    if (refreshedQuote && currentlyOpenedQuote) {
                        currentlyOpenedQuote = refreshedQuote;
                        quoteDetailsContent.innerHTML = quoteDetailsHtml(refreshedQuote);
                        setQuoteActionButtons(refreshedQuote);
                        syncQuoteNegotiateButtonWithLatest(refreshedQuote);
                    }

                    if (typeof updateJobCardStatus === 'function' && refreshedQuote) {
                        updateJobCardStatus(
                            refreshedQuote.request_id,
                            refreshedQuote.request_type || currentLatestNegotiationQuote.request_type,
                            refreshedQuote.request_status || refreshedQuote.job_status || 'active'
                        );
                    }

                    await loadQuotesReceived();
                } catch (refreshError) {
                    console.error('Failed to refresh quote state after negotiation accept:', refreshError);
                }
            }

            const latest = await fetchLatestNegotiationForQuote(currentLatestNegotiationQuote);
            renderLatestNegotiationWindow(latest, currentLatestNegotiationQuote);
            if (currentlyOpenedQuote) {
                setQuoteActionButtons(currentlyOpenedQuote);
                syncQuoteNegotiateButtonWithLatest(currentlyOpenedQuote);
            }
        } catch (error) {
            alert(error && error.message ? error.message : 'Failed to update negotiation.');
        }
    }

    function closeLatestQuoteNegotiationModal() {
        if (!latestQuoteNegotiationModal) return;
        latestQuoteNegotiationModal.classList.remove('show');
        document.body.style.overflow = '';
        currentLatestNegotiationQuote = null;
        currentLatestNegotiationItem = null;
        if (latestNegotiationAcceptBtn) latestNegotiationAcceptBtn.style.display = 'none';
        if (latestNegotiationRejectBtn) latestNegotiationRejectBtn.style.display = 'none';
    }

    async function loadQuotesForRequest(requestId, requestType) {
        const normalizedRequestType = String(requestType || '').toLowerCase() === 'direct' ? 'direct' : 'regular';
        const url = `${USER_QUOTES_API}?action=list&limit=50&offset=0&request_id=${encodeURIComponent(String(requestId))}&request_type=${encodeURIComponent(normalizedRequestType)}`;
        const result = await fetchJson(url);
        return Array.isArray(result.quotes) ? result.quotes : [];
    }

    async function loadQuotesReceived() {
        if (!quotesReceivedList) return;
        quotesReceivedList.innerHTML = '<div class="quote-empty-state">Loading quotes...</div>';

        try {
            const result = await fetchJson(`${USER_QUOTES_API}?action=list&limit=100&offset=0&status=${encodeURIComponent(currentQuoteStatusFilter)}`);
            const quotes = Array.isArray(result.quotes) ? result.quotes : [];
            setQuotesPill(parseInt(result.pending_count, 10) || 0);

            if (!quotes.length) {
                quotesReceivedList.innerHTML = '<div class="quote-empty-state">No quotes received.</div>';
                return;
            }

            quotesReceivedList.innerHTML = quotes.map((quote) => renderQuoteCard(quote, 'section')).join('');
            window.__lastQuotes = quotes;
        } catch (error) {
            console.error('[QuotesReceived] Failed to load list', {
                statusFilter: currentQuoteStatusFilter,
                error: error && error.message ? error.message : error
            });
            quotesReceivedList.innerHTML = '<div class="quote-empty-state">Failed to load quotes.</div>';
            setQuotesPill(0);
        }
    }

    async function openJobQuotesModal(requestId, requestType) {
        if (!jobQuotesModal || !jobQuotesList) return;

        const normalizedRequestType = String(requestType || '').toLowerCase() === 'direct' ? 'direct' : 'regular';
        currentRequestQuotesContext = { requestId: Number(requestId), requestType: normalizedRequestType };
        jobQuotesList.innerHTML = '<div class="quote-empty-state">Loading quotes...</div>';

        jobQuotesModal.classList.add('show');
        document.body.style.overflow = 'hidden';

        try {
            const quotes = await loadQuotesForRequest(requestId, normalizedRequestType);
            if (!quotes.length) {
                jobQuotesList.innerHTML = '<div class="quote-empty-state">No quotes received</div>';
                return;
            }

            jobQuotesList.innerHTML = quotes.map((quote) => renderQuoteCard(quote, 'request')).join('');
            window.__lastRequestQuotes = quotes;
        } catch (error) {
            console.error('[JobQuotesModal] Failed to load request quotes', {
                requestId,
                requestType: normalizedRequestType,
                error: error && error.message ? error.message : error
            });
            const message = error && error.message ? error.message : 'Failed to load request quotations.';
            jobQuotesList.innerHTML = `<div class="quote-empty-state">${escapeHtml(message)}</div>`;
        }
    }

    function closeJobQuotesModal() {
        if (!jobQuotesModal) return;
        jobQuotesModal.classList.remove('show');
        document.body.style.overflow = '';
    }

    function findQuote(payload) {
        const allQuotes = [
            ...(Array.isArray(window.__lastQuotes) ? window.__lastQuotes : []),
            ...(Array.isArray(window.__lastRequestQuotes) ? window.__lastRequestQuotes : [])
        ];

        return allQuotes.find((quote) =>
            String(quote.source) === String(payload.source) &&
            Number(quote.quote_id) === Number(payload.quote_id) &&
            String(quote.request_type || 'regular') === String(payload.request_type || 'regular')
        ) || null;
    }

    async function fetchLatestNegotiationForQuote(quote) {
        const url = `${QUOTE_NEGOTIATION_API}?action=list&quote_id=${encodeURIComponent(String(quote.quote_id))}&source=${encodeURIComponent(String(quote.source || ''))}&request_type=${encodeURIComponent(String(quote.request_type || 'regular'))}&limit=1`;
        const result = await fetchJson(url);
        return Array.isArray(result.items) && result.items.length ? result.items[0] : null;
    }

    function isOpenUserSentNegotiation(item) {
        if (!item) return false;
        const status = String(item.status || '').toLowerCase();
        const senderRole = String(item.sender_role || '').toLowerCase();
        return senderRole === 'user' && (status === 'pending' || status === 'countered');
    }

    function setQuoteActionButtons(quote) {
        if (!quoteAcceptBtn || !quoteRejectBtn || !quoteNegotiateBtn) return;

        quoteAcceptBtn.style.display = '';
        quoteRejectBtn.style.display = '';
        quoteNegotiateBtn.style.display = '';

        const quoteStatus = String(quote.status || '').toLowerCase();
        const quoteSource = String(quote.source || '').toLowerCase();
        const canRespond = quoteStatus === 'pending';
        quoteAcceptBtn.disabled = !canRespond;
        quoteRejectBtn.disabled = !canRespond;

        if (quoteStatus === 'pending' && quoteSource === 'company') {
            quoteNegotiateBtn.style.display = 'none';
            return;
        }

        const requestStatus = String(quote.request_status || quote.job_status || '').toLowerCase();
        const isInProgress = requestStatus === 'in_progress';
        const isQuoteAccepted = quoteStatus === 'accepted' || quoteStatus === 'successful';
        const showCollaboration = isQuoteAccepted || (isInProgress && quoteStatus !== 'pending');
        const providerId = Number(quote.provider_id);
        const canNegotiate = Number.isFinite(providerId) && providerId > 0;
        if (showCollaboration) {
            quoteNegotiateBtn.classList.remove('is-disabled');
            quoteNegotiateBtn.classList.add('btn-collaboration');
            quoteNegotiateBtn.innerHTML = '<i class="fas fa-comments"></i> Job Collaboration';
            quoteNegotiateBtn.href = '#';
            quoteNegotiateBtn.onclick = function(event) {
                event.preventDefault();
                openJobCollaborationModal(quote.request_id, quote.request_type || 'regular');
                return false;
            };
            return;
        }

        quoteNegotiateBtn.classList.remove('btn-collaboration');
        if (canNegotiate && canRespond) {
            quoteNegotiateBtn.classList.remove('is-disabled');
            quoteNegotiateBtn.innerHTML = '<i class="fas fa-message"></i> Negotiate';
            quoteNegotiateBtn.href = '#';
            quoteNegotiateBtn.onclick = function(event) {
                event.preventDefault();
                openQuoteNegotiationModal(quote);
                return false;
            };
        } else {
            quoteNegotiateBtn.classList.add('is-disabled');
            quoteNegotiateBtn.innerHTML = '<i class="fas fa-message"></i> Negotiate';
            quoteNegotiateBtn.href = '#';
            quoteNegotiateBtn.onclick = function() { return false; };
        }
    }

    async function syncQuoteNegotiateButtonWithLatest(quote) {
        if (!quoteNegotiateBtn) return;

        const requestStatus = String(quote.request_status || quote.job_status || '').toLowerCase();
        const isInProgress = requestStatus === 'in_progress';
        const canRespond = String(quote.status || '').toLowerCase() === 'pending';
        if (isInProgress || !canRespond) {
            return;
        }

        try {
            const latest = await fetchLatestNegotiationForQuote(quote);

            const sameQuoteStillOpen = currentlyOpenedQuote
                && Number(currentlyOpenedQuote.quote_id) === Number(quote.quote_id)
                && String(currentlyOpenedQuote.source || '') === String(quote.source || '')
                && String(currentlyOpenedQuote.request_type || 'regular') === String(quote.request_type || 'regular');
            if (!sameQuoteStillOpen) {
                return;
            }

            if (isOpenUserSentNegotiation(latest)) {
                quoteNegotiateBtn.classList.add('is-disabled');
                quoteNegotiateBtn.innerHTML = '<i class="fas fa-hourglass-half"></i> Negotiation Pending';
                quoteNegotiateBtn.href = '#';
                quoteNegotiateBtn.onclick = function(event) {
                    event.preventDefault();
                    return false;
                };
            }
        } catch (error) {
            // Keep default state when latest-negotiation lookup fails.
        }
    }

    function renderQuoteNegotiationSummary(quote) {
        if (!quoteNegotiationSummary) return;

        quoteNegotiationSummary.innerHTML = `
            <div class="quote-negotiation-summary-item">
                <span class="quote-negotiation-label">Job</span>
                <span class="quote-negotiation-value">${escapeHtml(quote.job_title || 'N/A')}</span>
            </div>
            <div class="quote-negotiation-summary-item">
                <span class="quote-negotiation-label">Listed Price</span>
                <span class="quote-negotiation-value">${escapeHtml(formatMoney(quote.amount))}</span>
            </div>
            <div class="quote-negotiation-summary-item quote-negotiation-summary-full">
                <span class="quote-negotiation-label">Job Description</span>
                <p class="quote-negotiation-description">${escapeHtml(quote.description || 'No description available.')}</p>
            </div>
        `;
    }

    function renderNegotiationHistory(items) {
        if (!quoteNegotiationHistory) return;
        if (!Array.isArray(items) || !items.length) {
            quoteNegotiationHistory.innerHTML = '<div class="quote-empty-state">No negotiations yet.</div>';
            return;
        }

        quoteNegotiationHistory.innerHTML = items.map((item) => {
            const senderRole = readableStatus(item.sender_role || 'participant');
            const receiverRole = readableStatus(item.receiver_role || 'participant');
            const status = readableStatus(item.status || 'pending');
            const listed = formatMoney(item.listed_price || 0);
            const proposed = formatMoney(item.proposed_price || 0);

            return `
                <article class="quote-negotiation-item">
                    <div class="quote-negotiation-item-head">
                        <strong>${escapeHtml(senderRole)} to ${escapeHtml(receiverRole)}</strong>
                        <span>${escapeHtml(formatDateTime(item.created_at))}</span>
                    </div>
                    <div class="quote-negotiation-item-meta">
                        <span>Listed: ${escapeHtml(listed)}</span>
                        <span>Proposed: ${escapeHtml(proposed)}</span>
                        <span class="quote-status-pill quote-status-${escapeHtml(String(item.status || 'pending').toLowerCase())}">${escapeHtml(status)}</span>
                    </div>
                    ${item.message ? `<p class="quote-negotiation-item-message">${escapeHtml(item.message)}</p>` : ''}
                </article>
            `;
        }).join('');
    }

    async function loadNegotiationHistory(quote) {
        const url = `${QUOTE_NEGOTIATION_API}?action=list&quote_id=${encodeURIComponent(String(quote.quote_id))}&source=${encodeURIComponent(String(quote.source || ''))}&request_type=${encodeURIComponent(String(quote.request_type || 'regular'))}&limit=20`;
        const result = await fetchJson(url);
        renderNegotiationHistory(Array.isArray(result.items) ? result.items : []);
    }

    async function openQuoteNegotiationModal(quote) {
        if (!quoteNegotiationModal) return;

        try {
            const latest = await fetchLatestNegotiationForQuote(quote);
            if (isOpenUserSentNegotiation(latest)) {
                alert('You already have a pending negotiation for this quote.');
                return;
            }
        } catch (error) {
            // If lookup fails, backend validation will still prevent invalid duplicates.
        }

        currentNegotiationQuote = quote;
        renderQuoteNegotiationSummary(quote);
        if (negotiationProposedPrice) {
            negotiationProposedPrice.value = Number.isFinite(Number(quote.amount)) ? String(Number(quote.amount)) : '';
        }
        if (negotiationMessage) {
            negotiationMessage.value = '';
        }

        if (quoteDetailsModal) {
            quoteDetailsModal.classList.remove('show');
        }

        if (quoteNegotiationHistory) {
            quoteNegotiationHistory.innerHTML = '<div class="quote-empty-state">Loading negotiation history...</div>';
        }

        quoteNegotiationModal.classList.add('show');
        document.body.style.overflow = 'hidden';

        try {
            await loadNegotiationHistory(quote);
        } catch (error) {
            const message = error && error.message ? error.message : 'Failed to load negotiation history.';
            if (quoteNegotiationHistory) {
                quoteNegotiationHistory.innerHTML = `<div class="quote-empty-state">${escapeHtml(message)}</div>`;
            }
        }
    }

    function closeQuoteNegotiationModal() {
        if (!quoteNegotiationModal) return;
        quoteNegotiationModal.classList.remove('show');
        document.body.style.overflow = '';
        currentNegotiationQuote = null;
    }

    async function submitQuoteNegotiation(event) {
        event.preventDefault();
        if (!currentNegotiationQuote) return;

        const proposedPrice = Number(negotiationProposedPrice ? negotiationProposedPrice.value : 0);
        if (!Number.isFinite(proposedPrice) || proposedPrice <= 0) {
            alert('Please enter a valid negotiation price.');
            return;
        }

        const message = negotiationMessage ? negotiationMessage.value.trim() : '';

        if (negotiationSendBtn) {
            negotiationSendBtn.disabled = true;
        }

        try {
            await fetchJson(`${QUOTE_NEGOTIATION_API}?action=create`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    quote_id: Number(currentNegotiationQuote.quote_id),
                    source: String(currentNegotiationQuote.source || ''),
                    request_type: String(currentNegotiationQuote.request_type || 'regular'),
                    proposed_price: proposedPrice,
                    message: message || null,
                })
            });

            showActionToast('Negotiation proposal sent successfully.');
            closeQuoteNegotiationModal();
        } catch (error) {
            alert(error && error.message ? error.message : 'Failed to send negotiation proposal.');
        } finally {
            if (negotiationSendBtn) {
                negotiationSendBtn.disabled = false;
            }
        }
    }

    function openQuoteDetails(payload) {
        const quote = findQuote(payload);
        if (!quote || !quoteDetailsModal || !quoteDetailsContent) {
            alert('Unable to open quote details.');
            return;
        }

        currentlyOpenedQuote = quote;
        quoteDetailsContent.innerHTML = quoteDetailsHtml(quote);
        setQuoteActionButtons(quote);
        syncQuoteNegotiateButtonWithLatest(quote);

        quoteDetailsModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    async function openInProgressJobFullDetails(requestId, requestType) {
        const normalizedRequestType = String(requestType || '').toLowerCase() === 'direct' ? 'direct' : 'regular';
        const numericRequestId = Number(requestId);
        if (!Number.isFinite(numericRequestId) || numericRequestId <= 0) return;

        try {
            const collaborationResult = await fetchJson(`${JOB_COLLAB_API}?action=get&request_id=${encodeURIComponent(String(numericRequestId))}&request_type=${encodeURIComponent(normalizedRequestType)}`);
            const collaboration = collaborationResult.collaboration || null;
            if (!collaboration) {
                throw new Error('No collaboration found for this job.');
            }

            const quoteList = await loadQuotesForRequest(numericRequestId, normalizedRequestType);
            const matchedQuote = quoteList.find((quote) =>
                Number(quote.quote_id) === Number(collaboration.quote_id) &&
                String(quote.source) === String(collaboration.quote_source)
            );

            if (!matchedQuote) {
                throw new Error('Accepted quote details could not be loaded.');
            }

            const enrichedQuote = {
                ...matchedQuote,
                request_status: 'in_progress',
                job_status: 'in_progress'
            };
            window.__lastRequestQuotes = [enrichedQuote];
            openQuoteDetails({
                source: enrichedQuote.source,
                quote_id: enrichedQuote.quote_id,
                request_type: enrichedQuote.request_type || normalizedRequestType,
            });
        } catch (error) {
            alert(error && error.message ? error.message : 'Failed to open full details.');
        }
    }

    function closeQuoteDetailsModal() {
        if (!quoteDetailsModal) return;
        quoteDetailsModal.classList.remove('show');
        document.body.style.overflow = '';
        currentlyOpenedQuote = null;
    }

    async function handleQuoteAction(decision, source, quoteId, requestType) {
        const actionLabel = decision === 'accepted' ? 'accept' : 'reject';
        if (!confirm(`Are you sure you want to ${actionLabel} this quotation?`)) {
            return;
        }

        await fetchJson(`${USER_QUOTES_API}?action=respond`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ source, quote_id: Number(quoteId), decision, request_type: requestType || null })
        });

        if (decision === 'accepted' && currentlyOpenedQuote) {
            updateJobCardStatus(currentlyOpenedQuote.request_id, currentlyOpenedQuote.request_type, 'in_progress');
        }

        showActionToast(decision === 'accepted' ? 'Quotation accepted successfully.' : 'Quotation rejected successfully.');

        if (currentRequestQuotesContext.requestId) {
            await openJobQuotesModal(currentRequestQuotesContext.requestId, currentRequestQuotesContext.requestType);
        }
        await loadQuotesReceived();
        closeQuoteDetailsModal();
    }

    function readablePhase(phase) {
        const normalized = String(phase || '').toLowerCase();
        if (!normalized) return 'Unknown';
        return normalized.replaceAll('_', ' ').replace(/\b\w/g, (c) => c.toUpperCase());
    }

    function setCollabCheckState(node, label, value) {
        if (!node) return;
        const done = !!value;
        node.classList.toggle('done', done);
        node.textContent = `${label}: ${done ? 'done' : 'pending'}`;
    }

    function eventTitle(eventType) {
        const map = {
            note: 'Note',
            price_proposed: 'Price Proposed',
            price_accepted: 'Price Accepted',
            price_rejected: 'Price Rejected',
            completed_marked: 'Completion Marked',
            completed_reset: 'Completion Reset',
            payment_confirmed: 'Payment Confirmed',
            payment_reset: 'Payment Reset',
            rating_submitted: 'Rating Submitted',
            phase_changed: 'Phase Updated',
            system: 'System',
        };
        return map[String(eventType || '').toLowerCase()] || readableStatus(eventType || 'event');
    }

    function renderCollaborationEvents(events) {
        if (!collabEventsList) return;
        if (!Array.isArray(events) || !events.length) {
            collabEventsList.innerHTML = '<div class="quote-empty-state">No timeline entries yet.</div>';
            return;
        }

        collabEventsList.innerHTML = events.map((event) => {
            const amount = Number(event.amount);
            const amountHtml = Number.isFinite(amount) && amount > 0
                ? `<span class="collab-event-amount">${escapeHtml(formatMoney(amount))}</span>`
                : '';
            return `
                <article class="collab-event-item">
                    <div class="collab-event-head">
                        <strong>${escapeHtml(eventTitle(event.event_type))}</strong>
                        <span>${escapeHtml(formatDateTime(event.created_at))}</span>
                    </div>
                    <div class="collab-event-meta">By ${escapeHtml(readableStatus(event.actor_role || 'system'))}</div>
                    ${event.message ? `<p class="collab-event-message">${escapeHtml(event.message)}</p>` : ''}
                    ${amountHtml}
                </article>
            `;
        }).join('');
    }

    function renderCollaboration(collaboration) {
        currentCollaboration = collaboration || null;
        if (!currentCollaboration) return;

        if (collabCurrentPhase) {
            collabCurrentPhase.textContent = readablePhase(currentCollaboration.current_phase);
        }
        if (collabAgreedPrice) {
            collabAgreedPrice.textContent = formatMoney(currentCollaboration.agreed_price || currentCollaboration.base_price || 0);
        }
        if (collabPendingPrice) {
            if (currentCollaboration.pending_price !== null && currentCollaboration.pending_price !== undefined) {
                collabPendingPrice.textContent = formatMoney(currentCollaboration.pending_price);
            } else {
                collabPendingPrice.textContent = 'None';
            }
        }

        setCollabCheckState(collabUserCompleted, 'User completion', currentCollaboration.user_completed_at);
        setCollabCheckState(collabProviderCompleted, 'Provider completion', currentCollaboration.provider_completed_at);
        setCollabCheckState(collabUserPaid, 'User payment', currentCollaboration.user_payment_confirmed_at);
        setCollabCheckState(collabProviderPaid, 'Provider payment', currentCollaboration.provider_payment_confirmed_at);

        if (collabPendingProposalText) {
            if (currentCollaboration.pending_price !== null && currentCollaboration.pending_price !== undefined) {
                const by = readableStatus(currentCollaboration.pending_price_actor_role || 'participant');
                collabPendingProposalText.textContent = `${by} proposed ${formatMoney(currentCollaboration.pending_price)}.`;
            } else {
                collabPendingProposalText.textContent = 'No pending proposal.';
            }
        }

        const pendingByCurrentUser = String(currentCollaboration.pending_price_actor_role || '').toLowerCase() === 'user';
        const hasPendingPrice = currentCollaboration.pending_price !== null && currentCollaboration.pending_price !== undefined;
        if (collabAcceptPriceBtn) collabAcceptPriceBtn.disabled = !hasPendingPrice || pendingByCurrentUser;
        if (collabRejectPriceBtn) collabRejectPriceBtn.disabled = !hasPendingPrice || pendingByCurrentUser;

        const userCompleted = !!currentCollaboration.user_completed_at;
        if (collabMarkCompletedBtn) {
            collabMarkCompletedBtn.disabled = String(currentCollaboration.current_phase || '').toLowerCase() === 'completed';
            if (userCompleted) {
                collabMarkCompletedBtn.classList.add('is-confirmed');
                collabMarkCompletedBtn.innerHTML = '<i class="fas fa-check-circle"></i> Marked as Job Completed';
            } else {
                collabMarkCompletedBtn.classList.remove('is-confirmed');
                collabMarkCompletedBtn.innerHTML = '<i class="fas fa-flag-checkered"></i> Mark Job Completed';
            }
        }

        if (collabResetCompletedBtn) {
            collabResetCompletedBtn.style.display = userCompleted ? '' : 'none';
            collabResetCompletedBtn.disabled = !userCompleted;
        }

        const userPaid = !!currentCollaboration.user_payment_confirmed_at;
        if (collabConfirmPaymentBtn) {
            collabConfirmPaymentBtn.disabled = false;
            if (userPaid) {
                collabConfirmPaymentBtn.classList.add('is-confirmed');
                collabConfirmPaymentBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirmed Payment Done';
            } else {
                collabConfirmPaymentBtn.classList.remove('is-confirmed');
                collabConfirmPaymentBtn.innerHTML = '<i class="fas fa-wallet"></i> Confirm Payment Done';
            }
        }

        if (collabResetPaymentBtn) {
            collabResetPaymentBtn.style.display = userPaid ? '' : 'none';
            collabResetPaymentBtn.disabled = !userPaid;
        }

        const canReviewPhase = !!currentCollaboration.user_payment_confirmed_at
            && !!currentCollaboration.provider_payment_confirmed_at;
        const alreadyRated = !!currentCollaboration.user_rated_at;
        const canRate = canReviewPhase && (!alreadyRated || collabRatingEditMode);

        if (collabRatingValue) {
            const existingRating = Number(currentCollaboration.user_rating || 0);
            if (existingRating >= 1 && existingRating <= 5) {
                collabRatingValue.value = String(existingRating);
            }
        }
        if (collabRatingComment) {
            collabRatingComment.value = String(currentCollaboration.user_rating_comment || '');
        }

        if (collabRatingPanel) {
            collabRatingPanel.classList.toggle('is-locked', !canRate);
            collabRatingPanel.style.display = 'block';
        }

        if (collabReviewLockText) {
            if (alreadyRated && !collabRatingEditMode) {
                collabReviewLockText.textContent = 'Review already submitted. You can edit it.';
            } else if (!canRate) {
                collabReviewLockText.textContent = 'Job and payment must be completed.';
            } else if (alreadyRated && collabRatingEditMode) {
                collabReviewLockText.textContent = 'Update your submitted review.';
            } else {
                collabReviewLockText.textContent = 'You can now submit your review.';
            }
        }

        if (collabRatingValue) {
            collabRatingValue.disabled = !canRate;
        }
        if (collabRatingComment) {
            collabRatingComment.disabled = !canRate;
        }
        if (collabEditRatingBtn) {
            collabEditRatingBtn.style.display = alreadyRated && canReviewPhase && !collabRatingEditMode ? '' : 'none';
            collabEditRatingBtn.disabled = !canReviewPhase;
        }
        if (collabSubmitRatingBtn) {
            collabSubmitRatingBtn.disabled = !canRate;
            collabSubmitRatingBtn.style.display = canReviewPhase ? '' : 'none';
            collabSubmitRatingBtn.innerHTML = alreadyRated
                ? '<i class="fas fa-save"></i> Update Review'
                : '<i class="fas fa-star"></i> Submit Rating';
        }

        renderCollaborationEvents(currentCollaboration.events || []);
    }

    async function refreshCollaboration() {
        if (!currentCollabContext.requestId) return;
        const url = `${JOB_COLLAB_API}?action=get&request_id=${encodeURIComponent(String(currentCollabContext.requestId))}&request_type=${encodeURIComponent(String(currentCollabContext.requestType || 'regular'))}`;
        const result = await fetchJson(url);
        renderCollaboration(result.collaboration || null);
    }

    async function callCollaborationAction(action, payload, successMessage) {
        if (!currentCollaboration) return;

        const result = await fetchJson(`${JOB_COLLAB_API}?action=${encodeURIComponent(action)}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                collaboration_id: Number(currentCollaboration.collaboration_id),
                ...(payload || {})
            })
        });

        renderCollaboration(result.collaboration || null);
        showActionToast(successMessage || 'Updated successfully.');
    }

    async function safelyRunCollaborationAction(task) {
        try {
            await task();
        } catch (error) {
            alert(error && error.message ? error.message : 'Failed to process collaboration action.');
        }
    }

    async function openJobCollaborationModal(requestId, requestType) {
        if (!jobCollaborationModal) return;

        currentCollabContext = {
            requestId: Number(requestId),
            requestType: String(requestType || 'regular').toLowerCase() === 'direct' ? 'direct' : 'regular'
        };

        if (collabEventsList) {
            collabEventsList.innerHTML = '<div class="quote-empty-state">Loading timeline...</div>';
        }

        jobCollaborationModal.classList.add('show');
        document.body.style.overflow = 'hidden';

        try {
            await refreshCollaboration();
        } catch (error) {
            const message = error && error.message ? error.message : 'Failed to load collaboration';
            if (collabEventsList) {
                collabEventsList.innerHTML = `<div class="quote-empty-state">${escapeHtml(message)}</div>`;
            }
        }
    }

    async function openCollaborationReviewModal(requestId, requestType) {
        if (!collaborationReviewModal) return;

        collabRatingEditMode = false;

        currentCollabContext = {
            requestId: Number(requestId),
            requestType: String(requestType || 'regular').toLowerCase() === 'direct' ? 'direct' : 'regular'
        };

        collaborationReviewModal.classList.add('show');
        document.body.style.overflow = 'hidden';

        try {
            await refreshCollaboration();
        } catch (error) {
            const message = error && error.message ? error.message : 'Failed to load collaboration review details.';
            if (collabReviewLockText) {
                collabReviewLockText.textContent = message;
            }
        }
    }

    function closeCollaborationReviewModal() {
        if (!collaborationReviewModal) return;
        collaborationReviewModal.classList.remove('show');
        document.body.style.overflow = '';
        collabRatingEditMode = false;
    }

    function closeJobCollaborationModal() {
        if (!jobCollaborationModal) return;
        jobCollaborationModal.classList.remove('show');
        document.body.style.overflow = '';
        currentCollaboration = null;
        currentCollabContext = { requestId: null, requestType: null };
    }

    function switchMainView(view) {
        const showJobs = view === 'jobs';
        jobsPostedBtn.classList.toggle('active', showJobs);
        quotesReceivedBtn.classList.toggle('active', !showJobs);

        if (jobsFilterTabs) jobsFilterTabs.style.display = showJobs ? 'flex' : 'none';
        if (jobsContainerEl) jobsContainerEl.style.display = showJobs ? '' : 'none';
        if (quotesReceivedSection) quotesReceivedSection.style.display = showJobs ? 'none' : 'block';

        try {
            sessionStorage.setItem(JOB_HISTORY_VIEW_KEY, showJobs ? 'jobs' : 'quotes');
        } catch (e) {
            // Ignore storage failures.
        }

        const url = new URL(window.location.href);
        if (showJobs) {
            url.searchParams.delete('view');
        } else {
            url.searchParams.set('view', 'quotes');
            loadQuotesReceived();
        }
        window.history.replaceState({}, '', url.toString());
    }

    function renderDirectCategoryOptions(categories, selectedCategoryId) {
        if (!directJobCategory || !directJobCategoryDisplay || !directJobCategoryList) return;

        const valid = Array.isArray(categories) ? categories : [];
        if (!valid.length) {
            directJobCategory.value = String(selectedCategoryId || '');
            directJobCategoryDisplay.value = '';
            directJobCategoryList.innerHTML = '';
            directJobCategoryList.style.display = 'none';
            return;
        }

        const selected = valid.find((item) => Number(item.category_id) === Number(selectedCategoryId)) || valid[0];
        directJobCategory.value = String(selected.category_id);
        directJobCategoryDisplay.value = String(selected.name || '');

        if (valid.length <= 1) {
            directJobCategoryList.innerHTML = '';
            directJobCategoryList.style.display = 'none';
            return;
        }

        directJobCategoryList.style.display = 'grid';
        directJobCategoryList.innerHTML = valid.map((item) => {
            const checked = Number(item.category_id) === Number(selected.category_id) ? 'checked' : '';
            return `
                <label class="direct-job-category-option">
                    <input type="radio" name="directJobCategoryChoice" value="${Number(item.category_id)}" ${checked}>
                    <span>${escapeHtml(item.name || '')}</span>
                </label>
            `;
        }).join('');

        const radios = directJobCategoryList.querySelectorAll('input[name="directJobCategoryChoice"]');
        radios.forEach((radio) => {
            radio.addEventListener('change', function() {
                if (!this.checked) return;
                const selectedItem = valid.find((item) => Number(item.category_id) === Number(this.value));
                if (!selectedItem) return;
                directJobCategory.value = String(selectedItem.category_id);
                directJobCategoryDisplay.value = String(selectedItem.name || '');
            });
        });
    }

    async function openDirectEditModal(requestId) {
        const numericId = Number(requestId);
        if (!Number.isFinite(numericId) || numericId <= 0 || !directJobRequestModal || !directJobRequestForm) return;

        try {
            const directResult = await fetchJson(`${DIRECT_JOB_API}?action=get&request_id=${encodeURIComponent(String(numericId))}`);
            const directRequest = directResult.data || directResult;

            const providerId = Number(directRequest.provider_id || 0);
            const providerType = normalizeProviderType(directRequest.provider_type || 'individual');
            const categoriesResult = await fetchJson(`${DIRECT_JOB_API}?action=provider-categories&provider_id=${encodeURIComponent(String(providerId))}&provider_type=${encodeURIComponent(providerType)}`);
            const providerCategories = categoriesResult.data || [];

            directEditRequestId = numericId;
            directJobRequestForm.reset();

            directJobProviderId.value = String(providerId);
            directJobProviderType.value = providerType;
            directJobRequestProviderLabel.textContent = providerType === 'company' ? 'this company' : 'this repairer';
            document.getElementById('directJobTitle').value = String(directRequest.title || '');
            document.getElementById('directJobDescription').value = String(directRequest.description || '');
            directJobDistrict.value = String(directRequest.district || '');
            directJobAddress.value = String(directRequest.address || '');
            directJobFinishDate.value = String(directRequest.finish_date || '');

            renderDirectCategoryOptions(providerCategories, Number(directRequest.category_id || 0));

            if (directJobPhotoPreview) {
                directJobPhotoPreview.textContent = directRequest.photos ? `Current: ${String(directRequest.photos).split('/').pop()}` : '';
            }

            if (directJobRequestError) {
                directJobRequestError.style.display = 'none';
                directJobRequestError.textContent = '';
            }
            if (directJobRequestSuccess) {
                directJobRequestSuccess.style.display = 'none';
                directJobRequestSuccess.textContent = '';
            }
            if (directJobRequestSubmitBtn) {
                directJobRequestSubmitBtn.textContent = 'Update Request';
                directJobRequestSubmitBtn.disabled = false;
            }

            directJobRequestModal.classList.add('show');
            directJobRequestModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        } catch (error) {
            alert(error.message || 'Failed to load direct request for editing.');
        }
    }

    async function deleteDirectRequest(requestId) {
        const numericId = Number(requestId);
        if (!Number.isFinite(numericId) || numericId <= 0) return;
        if (!confirm('Are you sure you want to delete this direct job request?')) return;

        try {
            const formData = new FormData();
            formData.set('request_id', String(numericId));
            await fetchJson(`${DIRECT_JOB_API}?action=delete`, {
                method: 'POST',
                body: formData
            });
            window.location.reload();
        } catch (error) {
            alert(error.message || 'Failed to delete direct request.');
        }
    }

    function closeDirectJobRequestModal() {
        if (!directJobRequestModal || !directJobRequestForm) return;
        directJobRequestModal.classList.remove('show');
        directJobRequestModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        directJobRequestForm.reset();
        directEditRequestId = null;
        if (directJobCategoryList) {
            directJobCategoryList.innerHTML = '';
            directJobCategoryList.style.display = 'none';
        }
        if (directJobPhotoPreview) {
            directJobPhotoPreview.textContent = '';
        }
        if (directJobRequestSubmitBtn) {
            directJobRequestSubmitBtn.textContent = 'Request';
            directJobRequestSubmitBtn.disabled = false;
        }
        if (directJobRequestError) {
            directJobRequestError.style.display = 'none';
            directJobRequestError.textContent = '';
        }
        if (directJobRequestSuccess) {
            directJobRequestSuccess.style.display = 'none';
            directJobRequestSuccess.textContent = '';
        }
    }

    async function submitDirectJobEdit(event) {
        event.preventDefault();
        if (!directEditRequestId) return;

        const formData = new FormData(directJobRequestForm);
        formData.set('request_id', String(directEditRequestId));
        formData.set('provider_id', String(directJobProviderId.value || ''));
        formData.set('provider_type', String(directJobProviderType.value || 'individual'));
        formData.set('category_id', String(directJobCategory.value || ''));

        if (directJobRequestSubmitBtn) {
            directJobRequestSubmitBtn.disabled = true;
            directJobRequestSubmitBtn.textContent = 'Updating...';
        }

        try {
            const result = await fetchJson(`${DIRECT_JOB_API}?action=update`, {
                method: 'POST',
                body: formData
            });

            if (directJobRequestSuccess) {
                directJobRequestSuccess.style.display = 'block';
                directJobRequestSuccess.textContent = result.message || 'Direct request updated successfully.';
            }
            setTimeout(() => window.location.reload(), 400);
        } catch (error) {
            if (directJobRequestError) {
                directJobRequestError.style.display = 'block';
                directJobRequestError.textContent = error.message || 'Failed to update direct request.';
            }
            if (directJobRequestSubmitBtn) {
                directJobRequestSubmitBtn.disabled = false;
                directJobRequestSubmitBtn.textContent = 'Update Request';
            }
        }
    }

    function openEditModal(requestId) {
        const job = jobs.find((item) => Number(item.request_id) === Number(requestId));
        if (!job) return;

        document.getElementById('edit_request_id').value = job.request_id;
        document.getElementById('edit_title').value = job.title || '';
        document.getElementById('edit_category_id').value = job.category_id;
        document.getElementById('edit_description').value = job.description || '';
        document.getElementById('edit_district').value = job.district || '';
        document.getElementById('edit_address').value = job.address || '';
        document.getElementById('edit_urgency').value = job.urgency || 'medium';
        document.getElementById('edit_finish_date').value = job.finish_date || '';

        const providerType = String(job.service_provider_type || '');
        document.getElementById('edit_provider_individual').checked = providerType.includes('individual');
        document.getElementById('edit_provider_company').checked = providerType.includes('company');
        document.getElementById('edit-file-name-display').innerHTML = '';
        document.getElementById('editModal').classList.add('show');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.remove('show');
    }

    window.openEditModal = openEditModal;
    window.closeEditModal = closeEditModal;
    window.openJobQuotesModal = openJobQuotesModal;
    window.closeJobQuotesModal = closeJobQuotesModal;
    window.openQuoteDetails = openQuoteDetails;
    window.resetQuoteToPending = resetQuoteToPending;
    window.openCompletedQuoteSummary = openCompletedQuoteSummary;
    window.closeQuoteDetailsModal = closeQuoteDetailsModal;
    window.closeQuoteNegotiationModal = closeQuoteNegotiationModal;
    window.openLatestQuoteNegotiationModal = openLatestQuoteNegotiationModal;
    window.closeLatestQuoteNegotiationModal = closeLatestQuoteNegotiationModal;
    window.openJobCollaborationModal = openJobCollaborationModal;
    window.closeJobCollaborationModal = closeJobCollaborationModal;
    window.openCollaborationReviewModal = openCollaborationReviewModal;
    window.closeCollaborationReviewModal = closeCollaborationReviewModal;
    window.openDirectEditModal = openDirectEditModal;
    window.deleteDirectRequest = deleteDirectRequest;

    document.addEventListener('DOMContentLoaded', function() {
        const finishDateInput = document.getElementById('edit_finish_date');
        if (finishDateInput) {
            finishDateInput.setAttribute('min', new Date().toISOString().split('T')[0]);
        }

        document.querySelectorAll('.filter-tab').forEach((tab) => {
            tab.addEventListener('click', function() {
                const status = this.dataset.status;
                document.querySelectorAll('.filter-tab').forEach((t) => t.classList.remove('active'));
                this.classList.add('active');
                document.querySelectorAll('.job-card').forEach((card) => {
                    card.style.display = status === 'all' || card.dataset.status === status ? 'block' : 'none';
                });
            });
        });

        const providerCheckboxes = document.querySelectorAll('#editForm input[name="provider_type[]"]');
        const providerError = document.getElementById('edit-provider-error');
        const editForm = document.getElementById('editForm');
        if (editForm) {
            editForm.addEventListener('submit', function(event) {
                const checked = Array.from(providerCheckboxes).some((checkbox) => checkbox.checked);
                if (!checked) {
                    event.preventDefault();
                    providerError.textContent = 'Please select at least one service provider type';
                } else {
                    providerError.textContent = '';
                }
            });
        }

        if (jobsPostedBtn && quotesReceivedBtn) {
            jobsPostedBtn.addEventListener('click', function() { switchMainView('jobs'); });
            quotesReceivedBtn.addEventListener('click', function() { switchMainView('quotes'); });
        }

        if (quotesFilterTabs && quotesFilterTabs.length) {
            quotesFilterTabs.forEach((tab) => {
                tab.addEventListener('click', function() {
                    const next = this.dataset.quoteStatus;
                    if (!next || next === currentQuoteStatusFilter) return;
                    currentQuoteStatusFilter = next;
                    quotesFilterTabs.forEach((item) => item.classList.remove('active'));
                    this.classList.add('active');
                    if (quotesReceivedSection && quotesReceivedSection.style.display !== 'none') {
                        loadQuotesReceived();
                    }
                });
            });
        }

        if (jobQuotesModal) {
            jobQuotesModal.addEventListener('click', function(event) {
                if (event.target === jobQuotesModal) {
                    closeJobQuotesModal();
                }
            });
        }

        if (quoteDetailsModal) {
            quoteDetailsModal.addEventListener('click', function(event) {
                if (event.target === quoteDetailsModal) {
                    closeQuoteDetailsModal();
                }
            });
        }

        if (quoteNegotiationModal) {
            quoteNegotiationModal.addEventListener('click', function(event) {
                if (event.target === quoteNegotiationModal) {
                    closeQuoteNegotiationModal();
                }
            });
        }

        if (latestQuoteNegotiationModal) {
            latestQuoteNegotiationModal.addEventListener('click', function(event) {
                if (event.target === latestQuoteNegotiationModal) {
                    closeLatestQuoteNegotiationModal();
                }
            });
        }

        if (jobCollaborationModal) {
            jobCollaborationModal.addEventListener('click', function(event) {
                if (event.target === jobCollaborationModal) {
                    closeJobCollaborationModal();
                }
            });
        }

        if (collaborationReviewModal) {
            collaborationReviewModal.addEventListener('click', function(event) {
                if (event.target === collaborationReviewModal) {
                    closeCollaborationReviewModal();
                }
            });
        }

        document.getElementById('editModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeEditModal();
            }
        });

        if (directJobRequestCloseBtn) {
            directJobRequestCloseBtn.addEventListener('click', closeDirectJobRequestModal);
        }
        if (directJobRequestCancelBtn) {
            directJobRequestCancelBtn.addEventListener('click', closeDirectJobRequestModal);
        }
        if (directJobRequestModal) {
            directJobRequestModal.addEventListener('click', function(event) {
                if (event.target === directJobRequestModal) {
                    closeDirectJobRequestModal();
                }
            });
        }
        if (directJobPhotos && directJobPhotoPreview) {
            directJobPhotos.addEventListener('change', function() {
                const file = directJobPhotos.files && directJobPhotos.files[0];
                if (file) {
                    directJobPhotoPreview.textContent = `Selected: ${file.name}`;
                }
            });
        }
        if (directJobRequestForm) {
            directJobRequestForm.addEventListener('submit', submitDirectJobEdit);
        }

        if (quoteAcceptBtn) {
            quoteAcceptBtn.addEventListener('click', function() {
                if (!currentlyOpenedQuote) return;
                handleQuoteAction('accepted', currentlyOpenedQuote.source, currentlyOpenedQuote.quote_id, currentlyOpenedQuote.request_type);
            });
        }
        if (quoteRejectBtn) {
            quoteRejectBtn.addEventListener('click', function() {
                if (!currentlyOpenedQuote) return;
                handleQuoteAction('rejected', currentlyOpenedQuote.source, currentlyOpenedQuote.quote_id, currentlyOpenedQuote.request_type);
            });
        }
        if (quoteNegotiationForm) {
            quoteNegotiationForm.addEventListener('submit', submitQuoteNegotiation);
        }
        if (latestNegotiationAcceptBtn) {
            latestNegotiationAcceptBtn.addEventListener('click', async function() {
                await respondToLatestNegotiation('accept');
            });
        }
        if (latestNegotiationRejectBtn) {
            latestNegotiationRejectBtn.addEventListener('click', async function() {
                await respondToLatestNegotiation('reject');
            });
        }

        if (collabProposePriceBtn) {
            collabProposePriceBtn.addEventListener('click', async function() {
                if (!currentCollaboration) return;
                const proposed = Number(collabProposedPriceInput ? collabProposedPriceInput.value : 0);
                if (!Number.isFinite(proposed) || proposed <= 0) {
                    alert('Enter a valid proposed price.');
                    return;
                }
                const note = collabPriceNoteInput ? collabPriceNoteInput.value.trim() : '';
                await safelyRunCollaborationAction(async function() {
                    await callCollaborationAction('propose_price', {
                        proposed_price: proposed,
                        message: note || null,
                    }, 'Price proposal sent.');
                });
            });
        }

        if (collabAcceptPriceBtn) {
            collabAcceptPriceBtn.addEventListener('click', async function() {
                if (!currentCollaboration) return;
                await safelyRunCollaborationAction(async function() {
                    await callCollaborationAction('respond_price', {
                        decision: 'accept',
                    }, 'Price proposal accepted.');
                });
            });
        }

        if (collabRejectPriceBtn) {
            collabRejectPriceBtn.addEventListener('click', async function() {
                if (!currentCollaboration) return;
                await safelyRunCollaborationAction(async function() {
                    await callCollaborationAction('respond_price', {
                        decision: 'reject',
                    }, 'Price proposal rejected.');
                });
            });
        }

        if (collabMarkCompletedBtn) {
            collabMarkCompletedBtn.addEventListener('click', async function() {
                if (!currentCollaboration) return;
                if (currentCollaboration.user_completed_at) {
                    return;
                }
                if (!confirm('Whether the job is completed or not, are you sure?')) {
                    return;
                }
                await safelyRunCollaborationAction(async function() {
                    await callCollaborationAction('mark_completed', {}, 'Marked as completed from your side.');
                });
            });
        }

        if (collabResetCompletedBtn) {
            collabResetCompletedBtn.addEventListener('click', async function() {
                if (!currentCollaboration || !currentCollaboration.user_completed_at) return;
                if (!confirm('Reset your job completion mark?')) {
                    return;
                }
                await safelyRunCollaborationAction(async function() {
                    await callCollaborationAction('reset_completed', {}, 'Completion mark reset from your side.');
                });
            });
        }

        if (collabConfirmPaymentBtn) {
            collabConfirmPaymentBtn.addEventListener('click', async function() {
                if (!currentCollaboration) return;
                if (currentCollaboration.user_payment_confirmed_at) {
                    alert('Payment is already confirmed from your side. Use Redo to reset it.');
                    return;
                }
                if (!currentCollaboration.user_completed_at) {
                    alert('Please mark the job as completed from your side first.');
                    return;
                }
                if (!currentCollaboration.provider_completed_at) {
                    alert('Please wait until the repairer marks the job as completed.');
                    return;
                }
                if (!confirm('Confirm that payment is done. Are you sure?')) {
                    return;
                }
                await safelyRunCollaborationAction(async function() {
                    await callCollaborationAction('confirm_payment', {}, 'Payment marked as done from your side.');
                });
            });
        }

        if (collabResetPaymentBtn) {
            collabResetPaymentBtn.addEventListener('click', async function() {
                if (!currentCollaboration || !currentCollaboration.user_payment_confirmed_at) return;
                if (!confirm('Reset your payment confirmation?')) {
                    return;
                }
                await safelyRunCollaborationAction(async function() {
                    await callCollaborationAction('reset_payment', {}, 'Payment confirmation reset from your side.');
                });
            });
        }

        if (collabSendNoteBtn) {
            collabSendNoteBtn.addEventListener('click', async function() {
                if (!currentCollaboration) return;
                const message = collabNoteInput ? collabNoteInput.value.trim() : '';
                if (!message) {
                    alert('Please type a note first.');
                    return;
                }
                await safelyRunCollaborationAction(async function() {
                    await callCollaborationAction('post_note', { message }, 'Note added to timeline.');
                    if (collabNoteInput) collabNoteInput.value = '';
                });
            });
        }

        if (collabSubmitRatingBtn) {
            collabSubmitRatingBtn.addEventListener('click', async function() {
                if (!currentCollaboration) return;
                const rating = Number(collabRatingValue ? collabRatingValue.value : 0);
                if (!Number.isFinite(rating) || rating < 1 || rating > 5) {
                    alert('Please select a rating between 1 and 5.');
                    return;
                }
                const comment = collabRatingComment ? collabRatingComment.value.trim() : '';
                await safelyRunCollaborationAction(async function() {
                    await callCollaborationAction('submit_rating', {
                        rating,
                        comment: comment || null,
                    }, 'Review saved successfully.');
                    collabRatingEditMode = false;
                });
            });
        }

        if (collabEditRatingBtn) {
            collabEditRatingBtn.addEventListener('click', function() {
                if (!currentCollaboration) return;
                collabRatingEditMode = true;
                renderCollaboration(currentCollaboration);
            });
        }

        const initialViewFromUrl = new URLSearchParams(window.location.search).get('view');
        let initialView = initialViewFromUrl;
        if (!initialView) {
            try {
                initialView = sessionStorage.getItem(JOB_HISTORY_VIEW_KEY) || 'jobs';
            } catch (e) {
                initialView = 'jobs';
            }
        }
        switchMainView(initialView === 'quotes' ? 'quotes' : 'jobs');

        setTimeout(function() {
            document.querySelectorAll('.alert').forEach((alertElement) => {
                alertElement.style.opacity = '0';
                setTimeout(() => alertElement.remove(), 300);
            });
        }, 5000);
    });
    </script>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/job-history.js"></script>
</body>
</html>