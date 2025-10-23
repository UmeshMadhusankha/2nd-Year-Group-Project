<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\user\job_history.php

// This file is loaded by JobRequestController->index()
// $jobRequests variable is already set by the controller

if (!isset($jobRequests)) {
    $jobRequests = [];
}

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

// Count jobs by status
$pendingCount = 0;
$inProgressCount = 0;
$completedCount = 0;
$cancelledCount = 0;

foreach ($jobRequests as $job) {
    switch ($job['status']) {
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
                <div class="filter-tabs">
                    <button class="filter-tab active" data-status="all">
                        All Jobs <span class="tab-count"><?php echo count($jobRequests); ?></span>
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
                <a href="/2nd-Year-Group-Project/FixLanka/post-job" class="btn-primary">
                    <i class="fas fa-plus"></i> Post New Job
                </a>
            </div>

            <!-- Jobs Container -->
            <div class="jobs-container">
                <?php if (empty($jobRequests)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h2 class="empty-title">No Job Requests Yet</h2>
                        <p class="empty-description">You haven't posted any job requests. Start by posting your first job!</p>
                        <a href="/2nd-Year-Group-Project/FixLanka/post-job" class="post-job-btn">
                            <i class="fas fa-plus"></i> Post Your First Job
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($jobRequests as $job): ?>
                        <?php
                        $statusClass = strtolower($job['status']);
                        $isPending = $job['status'] === 'pending';
                        $statusLabel = ucfirst(str_replace('_', ' ', $job['status']));
                        ?>
                        <div class="job-card" data-status="<?php echo $job['status']; ?>">
                            <div class="job-card-header">
                                <div>
                                    <h3 class="job-title"><?php echo htmlspecialchars($job['category_name'] ?? 'Job Request'); ?></h3>
                                    <p class="job-date">
                                        <i class="fas fa-calendar-alt"></i> 
                                        Posted on <?php echo date('F j, Y \a\t g:i A', strtotime($job['dateCreated'])); ?>
                                    </p>
                                </div>
                                <div class="job-badges">
                                    <span class="job-badge badge-status status-<?php echo $statusClass; ?>">
                                        <?php echo $statusLabel; ?>
                                    </span>
                                    <span class="job-badge badge-urgency urgency-<?php echo $job['urgency']; ?>">
                                        <i class="fas fa-bolt"></i> <?php echo ucfirst($job['urgency']); ?>
                                    </span>
                                </div>
                            </div>

                            <p class="job-description"><?php echo htmlspecialchars($job['description']); ?></p>

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
                                        <span class="detail-value"><?php echo ucfirst($job['service_provider_type']); ?></span>
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
                                <?php if ($isPending): ?>
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
                        <label for="edit_category_id">Category <span class="required">*</span></label>
                        <select id="edit_category_id" name="category_id" required>
                            <option value="">Select a category</option>
                            <option value="1">Plumbing</option>
                            <option value="2">Electrical</option>
                            <option value="3">Cleaning</option>
                            <option value="4">HVAC</option>
                            <option value="5">Carpentry</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_description">Description <span class="required">*</span></label>
                        <textarea id="edit_description" name="description" rows="4" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_location">Location <span class="required">*</span></label>
                        <input type="text" id="edit_location" name="location" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_service_provider_type">Service Provider Type <span class="required">*</span></label>
                        <select id="edit_service_provider_type" name="service_provider_type" required>
                            <option value="">Select provider type</option>
                            <option value="individual">Individual</option>
                            <option value="company">Company</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_urgency">Urgency <span class="required">*</span></label>
                        <select id="edit_urgency" name="urgency" required>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_photos">Photo (Optional)</label>
                        <input type="file" id="edit_photos" name="photos" accept="image/*">
                        <small>Upload a new photo to replace the existing one</small>
                    </div>
                    
                    <div class="modal-actions">
                        <button type="button" class="action-btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                        <button type="submit" class="action-btn btn-primary">
                            <i class="fas fa-save"></i> Update Job
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    const jobs = <?php echo json_encode($jobRequests); ?>;
    
    // Filter jobs by status
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const status = this.dataset.status;
            
            // Update active tab
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Filter job cards
            document.querySelectorAll('.job-card').forEach(card => {
                if (status === 'all' || card.dataset.status === status) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
    
    // Open edit modal
    function openEditModal(requestId) {
        const job = jobs.find(j => j.request_id == requestId);
        if (job) {
            document.getElementById('edit_request_id').value = job.request_id;
            document.getElementById('edit_category_id').value = job.category_id;
            document.getElementById('edit_description').value = job.description;
            document.getElementById('edit_location').value = job.location;
            document.getElementById('edit_service_provider_type').value = job.service_provider_type;
            document.getElementById('edit_urgency').value = job.urgency;
            document.getElementById('editModal').classList.add('show');
        }
    }
    
    // Close edit modal
    function closeEditModal() {
        document.getElementById('editModal').classList.remove('show');
    }
    
    // Close modal on outside click
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        });
    }, 5000);
    </script>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/job-history.js"></script>
</body>
</html>