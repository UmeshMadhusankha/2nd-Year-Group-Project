// FixLanka Projects Page JavaScript
document.addEventListener('DOMContentLoaded', function () {

    // View toggle is handled by projects-db.js (initializeViewToggle)

    // Function to attach drawer events to project elements
    function attachDrawerEvents() {
        const projectElements = document.querySelectorAll('.project-row, .project-card');

        // Remove existing event listeners by cloning and replacing elements
        projectElements.forEach(element => {
            const newElement = element.cloneNode(true);
            element.parentNode.replaceChild(newElement, element);
        });

        // Reattach events to new elements
        const newProjectElements = document.querySelectorAll('.project-row, .project-card');
        newProjectElements.forEach(element => {
            const viewBtn = element.querySelector('.action-btn-sm.primary');
            const editBtn = element.querySelector('.action-btn-sm.secondary');

            if (viewBtn) {
                viewBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    openDrawer(element.dataset.project);
                });
            }

            if (editBtn) {
                editBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    // Only open edit modal, don't open drawer
                    enableEditMode();
                });
            }

            // Also open on element click
            element.addEventListener('click', function () {
                openDrawer(this.dataset.project);
            });
        });
    }

    // Phase Tab Navigation
    const phaseTabs = document.querySelectorAll('.phase-tab');

    phaseTabs.forEach(tab => {
        tab.addEventListener('click', function () {
            // Update active tab
            phaseTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Filter projects based on phase (if needed)
            const phase = this.dataset.phase;
            filterByPhase(phase);
        });
    });

    // Project Detail Drawer
    const drawerOverlay = document.getElementById('projectDrawer');
    const closeDrawer = document.querySelector('.close-drawer');

    // Initial attachment of drawer events
    attachDrawerEvents();

    // Close drawer
    closeDrawer?.addEventListener('click', function () {
        drawerOverlay.classList.remove('active');
    });

    drawerOverlay?.addEventListener('click', function (e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });

    // Drawer Tab Navigation
    const drawerTabs = document.querySelectorAll('.drawer-tab');
    const drawerContents = document.querySelectorAll('.drawer-tab-content');

    drawerTabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const targetTab = this.dataset.tab;

            // Update active tab
            drawerTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Switch content
            drawerContents.forEach(content => {
                content.classList.remove('active');
            });

            const targetContent = document.getElementById(targetTab);
            targetContent?.classList.add('active');
        });
    });

    // Search Functionality
    const searchInput = document.querySelector('.global-search');

    searchInput?.addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();
        filterProjects(searchTerm);
    });

    // Status Filter Functionality
    const filterSelect = document.querySelector('.filter-select');

    filterSelect?.addEventListener('change', function () {
        const selectedStatus = this.value;
        filterByStatus(selectedStatus);
    });

    // Filter Functions
    function filterByPhase(phase) {
        const projects = document.querySelectorAll('.project-row, .project-card');

        projects.forEach(project => {
            // Show/hide based on phase (implement your filtering logic)
            project.style.display = 'block';
        });
    }

    function filterByStatus(status) {
        const projects = document.querySelectorAll('.project-row, .project-card');

        projects.forEach(project => {
            if (status === 'all') {
                project.style.display = '';
            } else {
                const projectStatus = project.querySelector('td:nth-child(7) .status-badge')?.textContent.trim().toLowerCase() ||
                    project.querySelector('.card-status .status-badge')?.textContent.trim().toLowerCase() || '';

                if (projectStatus.includes(status.toLowerCase()) || status === 'all') {
                    project.style.display = '';
                } else {
                    project.style.display = 'none';
                }
            }
        });
    }

    function filterProjects(searchTerm) {
        const projects = document.querySelectorAll('.project-row, .project-card');

        projects.forEach(project => {
            const title = project.querySelector('h4')?.textContent.toLowerCase() || '';
            const customer = project.querySelector('.customer-name')?.textContent.toLowerCase() || '';

            if (title.includes(searchTerm) || customer.includes(searchTerm)) {
                project.style.display = 'block';
            } else {
                project.style.display = 'none';
            }
        });
    }

    function openDrawer(projectId) {
        // Load project data (implement your data loading logic)
        drawerOverlay.classList.add('active');
    }

    // Function to open drawer with specific tab
    function openDrawerWithTab(projectId, tabName) {
        // Open the drawer first
        openDrawer(projectId);

        // Wait a bit for drawer to open, then switch to the specified tab
        setTimeout(() => {
            // Find and click the tab
            const targetTab = document.querySelector(`.drawer-tab[data-tab="${tabName}"]`);
            if (targetTab) {
                // Remove active from all tabs
                drawerTabs.forEach(t => t.classList.remove('active'));
                targetTab.classList.add('active');

                // Switch content
                drawerContents.forEach(content => {
                    content.classList.remove('active');
                });

                const targetContent = document.getElementById(tabName);
                if (targetContent) {
                    targetContent.classList.add('active');
                }
            }
        }, 100);
    }

    // Make function globally accessible
    window.openDrawerWithTab = openDrawerWithTab;

    // Message Send Functionality
    const sendBtn = document.querySelector('.send-btn');
    const messageInput = document.querySelector('.message-input input');

    sendBtn?.addEventListener('click', function () {
        const message = messageInput.value.trim();
        if (message) {
            addMessage(message, 'company');
            messageInput.value = '';
        }
    });

    messageInput?.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            sendBtn.click();
        }
    });

    function addMessage(text, sender) {
        const messagesContainer = document.querySelector('.chat-messages');
        if (!messagesContainer) return;

        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;

        const now = new Date();
        const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        messageDiv.innerHTML = `
            <div class="message-avatar">${sender === 'company' ? 'FL' : 'CU'}</div>
            <div class="message-content">
                <div class="message-header">
                    <span class="sender">${sender === 'company' ? 'FixLanka Team' : 'Customer'}</span>
                    <span class="time">Today, ${timeStr}</span>
                </div>
                <div class="message-text">${text}</div>
            </div>
        `;

        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Editable Content (Milestones)
    const editableElements = document.querySelectorAll('.editable');

    editableElements.forEach(element => {
        element.addEventListener('blur', function () {
            // Save changes (implement your save logic)

        });

        element.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                this.blur();
            }
        });
    });

    // File Upload Functionality
    const uploadBtn = document.querySelector('.upload-btn');

    uploadBtn?.addEventListener('click', function () {
        // Create and trigger file input
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.multiple = true;
        fileInput.accept = '.pdf,.doc,.docx,.jpg,.jpeg,.png,.zip';

        fileInput.addEventListener('change', function (e) {
            const files = Array.from(e.target.files);
            files.forEach(file => {
                addFileToList(file);
            });
        });

        fileInput.click();
    });

    function addFileToList(file) {
        const filesList = document.querySelector('.files-list');
        if (!filesList) return;

        const fileDiv = document.createElement('div');
        fileDiv.className = 'file-item';

        const iconClass = getFileIcon(file.name);
        const fileSize = formatFileSize(file.size);
        const uploadDate = new Date().toLocaleDateString();

        fileDiv.innerHTML = `
            <i class="${iconClass} file-icon"></i>
            <div class="file-info">
                <div class="file-name">${file.name}</div>
                <div class="file-meta">${fileSize} &bull; Uploaded ${uploadDate}</div>
            </div>
            <button class="download-btn"><i class="fas fa-download"></i></button>
        `;

        filesList.appendChild(fileDiv);
    }

    function getFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const iconMap = {
            'pdf': 'fas fa-file-pdf',
            'doc': 'fas fa-file-word',
            'docx': 'fas fa-file-word',
            'jpg': 'fas fa-image',
            'jpeg': 'fas fa-image',
            'png': 'fas fa-image',
            'zip': 'fas fa-file-archive'
        };
        return iconMap[ext] || 'fas fa-file';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Filter Controls
    const filterSelects = document.querySelectorAll('.filter-select');
    const filterDates = document.querySelectorAll('.filter-date');
    const budgetInputs = document.querySelectorAll('.budget-input');

    // Add change listeners for filters
    [...filterSelects, ...filterDates, ...budgetInputs].forEach(input => {
        input.addEventListener('change', applyFilters);
    });

    function applyFilters() {
        // Implement your filtering logic here

    }

    // Initialize smooth transitions
    document.body.style.setProperty('--transition-speed', '0.3s');

    // Date Range Picker Functionality
    const dateRangeBtn = document.getElementById('dateRangeBtn');
    const dateRangeModal = document.getElementById('dateRangeModal');
    const closeDateModal = document.querySelector('.close-date-modal');
    const dateRangeOverlay = document.querySelector('.date-range-overlay');
    const quickSelectBtns = document.querySelectorAll('.quick-select-btn');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const rangeText = document.querySelector('.range-text');
    const applyBtn = document.getElementById('applyDateRange');
    const clearBtn = document.getElementById('clearDateRange');
    const cancelBtn = document.getElementById('cancelDateRange');

    let selectedStartDate = null;
    let selectedEndDate = null;
    let selectedQuickRange = null;

    // Open date range modal
    dateRangeBtn?.addEventListener('click', function () {
        dateRangeModal.classList.add('active');
        dateRangeBtn.classList.add('active');
    });

    // Close modal functions
    function closeDateRangeModal() {
        dateRangeModal.classList.remove('active');
        dateRangeBtn.classList.remove('active');
    }

    closeDateModal?.addEventListener('click', closeDateRangeModal);
    dateRangeOverlay?.addEventListener('click', closeDateRangeModal);
    cancelBtn?.addEventListener('click', closeDateRangeModal);

    // Quick select functionality
    quickSelectBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            // Remove selected class from all buttons
            quickSelectBtns.forEach(b => b.classList.remove('selected'));
            // Add selected class to clicked button
            this.classList.add('selected');

            const range = this.dataset.range;
            selectedQuickRange = range;

            const dates = getQuickSelectDates(range);
            selectedStartDate = dates.start;
            selectedEndDate = dates.end;

            // Update custom date inputs
            startDateInput.value = formatDateForInput(selectedStartDate);
            endDateInput.value = formatDateForInput(selectedEndDate);

            // Update preview
            updateRangePreview();
        });
    });

    // Custom date input handlers
    startDateInput?.addEventListener('change', function () {
        selectedStartDate = new Date(this.value);
        selectedQuickRange = null;
        quickSelectBtns.forEach(btn => btn.classList.remove('selected'));
        updateRangePreview();
    });

    endDateInput?.addEventListener('change', function () {
        selectedEndDate = new Date(this.value);
        selectedQuickRange = null;
        quickSelectBtns.forEach(btn => btn.classList.remove('selected'));
        updateRangePreview();
    });

    // Apply date range
    applyBtn?.addEventListener('click', function () {
        if (selectedStartDate && selectedEndDate) {
            const startStr = formatDateDisplay(selectedStartDate);
            const endStr = formatDateDisplay(selectedEndDate);

            // Update button text
            const dateText = dateRangeBtn.querySelector('.date-range-text');
            if (selectedQuickRange) {
                dateText.textContent = getQuickRangeLabel(selectedQuickRange);
            } else {
                dateText.textContent = `${startStr} - ${endStr}`;
            }

            // Apply filters (implement your filtering logic)
            applyDateFilter(selectedStartDate, selectedEndDate);

            closeDateRangeModal();
        }
    });

    // Clear date range
    clearBtn?.addEventListener('click', function () {
        selectedStartDate = null;
        selectedEndDate = null;
        selectedQuickRange = null;

        // Clear inputs
        startDateInput.value = '';
        endDateInput.value = '';

        // Clear selections
        quickSelectBtns.forEach(btn => btn.classList.remove('selected'));

        // Reset button text
        const dateText = dateRangeBtn.querySelector('.date-range-text');
        dateText.textContent = 'Select Date Range';

        // Clear filters
        clearDateFilter();

        updateRangePreview();
    });

    // Helper functions for date range picker
    function getQuickSelectDates(range) {
        const today = new Date();
        const start = new Date();
        const end = new Date();

        switch (range) {
            case 'today':
                start.setHours(0, 0, 0, 0);
                end.setHours(23, 59, 59, 999);
                break;
            case 'yesterday':
                start.setDate(today.getDate() - 1);
                start.setHours(0, 0, 0, 0);
                end.setDate(today.getDate() - 1);
                end.setHours(23, 59, 59, 999);
                break;
            case 'this-week':
                const startOfWeek = today.getDate() - today.getDay();
                start.setDate(startOfWeek);
                start.setHours(0, 0, 0, 0);
                end.setDate(startOfWeek + 6);
                end.setHours(23, 59, 59, 999);
                break;
            case 'last-week':
                const lastWeekStart = today.getDate() - today.getDay() - 7;
                start.setDate(lastWeekStart);
                start.setHours(0, 0, 0, 0);
                end.setDate(lastWeekStart + 6);
                end.setHours(23, 59, 59, 999);
                break;
            case 'this-month':
                start.setDate(1);
                start.setHours(0, 0, 0, 0);
                end.setMonth(today.getMonth() + 1, 0);
                end.setHours(23, 59, 59, 999);
                break;
            case 'last-month':
                start.setMonth(today.getMonth() - 1, 1);
                start.setHours(0, 0, 0, 0);
                end.setMonth(today.getMonth(), 0);
                end.setHours(23, 59, 59, 999);
                break;
            case 'this-quarter':
                const quarterStart = Math.floor(today.getMonth() / 3) * 3;
                start.setMonth(quarterStart, 1);
                start.setHours(0, 0, 0, 0);
                end.setMonth(quarterStart + 3, 0);
                end.setHours(23, 59, 59, 999);
                break;
            case 'this-year':
                start.setMonth(0, 1);
                start.setHours(0, 0, 0, 0);
                end.setMonth(11, 31);
                end.setHours(23, 59, 59, 999);
                break;
            case 'last-30-days':
                start.setDate(today.getDate() - 30);
                start.setHours(0, 0, 0, 0);
                end.setHours(23, 59, 59, 999);
                break;
            case 'last-90-days':
                start.setDate(today.getDate() - 90);
                start.setHours(0, 0, 0, 0);
                end.setHours(23, 59, 59, 999);
                break;
        }

        return { start, end };
    }

    function getQuickRangeLabel(range) {
        const labels = {
            'today': 'Today',
            'yesterday': 'Yesterday',
            'this-week': 'This Week',
            'last-week': 'Last Week',
            'this-month': 'This Month',
            'last-month': 'Last Month',
            'this-quarter': 'This Quarter',
            'this-year': 'This Year',
            'last-30-days': 'Last 30 Days',
            'last-90-days': 'Last 90 Days'
        };
        return labels[range] || 'Custom Range';
    }

    function formatDateForInput(date) {
        if (!date) return '';
        return date.toISOString().split('T')[0];
    }

    function formatDateDisplay(date) {
        if (!date) return '';
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    function updateRangePreview() {
        if (selectedStartDate && selectedEndDate) {
            const startStr = formatDateDisplay(selectedStartDate);
            const endStr = formatDateDisplay(selectedEndDate);

            if (selectedQuickRange) {
                rangeText.textContent = `${getQuickRangeLabel(selectedQuickRange)} (${startStr} - ${endStr})`;
            } else {
                rangeText.textContent = `${startStr} - ${endStr}`;
            }
        } else if (selectedStartDate) {
            rangeText.textContent = `From ${formatDateDisplay(selectedStartDate)}`;
        } else if (selectedEndDate) {
            rangeText.textContent = `Until ${formatDateDisplay(selectedEndDate)}`;
        } else {
            rangeText.textContent = 'No date range selected';
        }
    }

    function applyDateFilter(startDate, endDate) {
        // Implement your date filtering logic here

        // Example: Filter projects based on date range
        const projects = document.querySelectorAll('.project-row, .project-card');
        projects.forEach(project => {
            // You would implement actual date comparison logic here
            // For now, just showing all projects
            project.style.display = 'block';
        });
    }

    function clearDateFilter() {
        // Clear date filters and show all projects

        const projects = document.querySelectorAll('.project-row, .project-card');
        projects.forEach(project => {
            project.style.display = 'block';
        });
    }

    // ESC key to close modal
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && dateRangeModal.classList.contains('active')) {
            closeDateRangeModal();
        }
    });

    // ============================================
    // Edit Mode Functions - Simple Form Interface
    // ============================================

    function enableEditMode() {
        openEditModal();
    }

    function openEditModal() {
        const projectData = getCurrentProjectData();

        const modal = document.createElement('div');
        modal.id = 'projectEditModal';
        modal.className = 'modal-overlay active';
        modal.innerHTML = `
            <div class="modal-container edit-modal">
                <div class="modal-header">
                    <h3><i class="fas fa-edit"></i> Update Project Status</h3>
                    <button class="close-modal" onclick="closeEditModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="projectEditForm">
                        
                        <div class="info-notice">
                            <i class="fas fa-info-circle"></i>
                            <span>Progress auto-calculates from milestone completion. Requires verification to mark complete.</span>
                        </div>

                        <!-- Auto-calculated Progress Display -->
                        <div class="progress-display">
                            <div class="progress-header">
                                <label><i class="fas fa-chart-line"></i> Overall Progress</label>
                                <span class="progress-value" id="calculatedProgress">0%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" id="progressFill" style="width: 0%"></div>
                            </div>
                            <p class="helper-text">Auto-calculated based on completed milestones</p>
                        </div>

                        <!-- Project Status -->
                        <div class="form-group">
                            <label><i class="fas fa-flag"></i> Project Status</label>
                            <select name="status" required>
                                <option value="ongoing">Ongoing</option>
                                <option value="delayed">Delayed</option>
                                <option value="completed">Completed</option>
                                <option value="on-hold">On Hold</option>
                            </select>
                        </div>

                        <!-- Milestone Status Updates with Verification -->
                        <div class="form-divider">
                            <h4><i class="fas fa-tasks"></i> Milestone & Payment Tracking</h4>
                            <p class="helper-text">âš ï¸ Customer must verify completion before payment release</p>
                        </div>

                        ${projectData.milestones.map((milestone, index) => `
                            <div class="milestone-status-item" data-milestone="${index}">
                                <div class="milestone-header-row">
                                    <div class="milestone-info">
                                        <div class="milestone-title-row">
                                            <strong>${milestone.title}</strong>
                                            <span class="milestone-payment-badge">
                                                <i class="fas fa-money-bill-wave"></i>
                                                LKR ${milestone.amount}
                                            </span>
                                        </div>
                                        <div class="milestone-payment-status">
                                            ${getMilestonePaymentStatus(milestone)}
                                        </div>
                                    </div>
                                    <div class="form-group milestone-status-select">
                                        <select name="milestone_${index}_status" onchange="handleMilestoneStatusChange(${index}, this.value)">
                                            <option value="pending">â³ Not Started</option>
                                            <option value="active">ðŸ”„ In Progress</option>
                                            <option value="review">ðŸ“‹ Pending Customer Review</option>
                                            <option value="completed" disabled>âœ… Verified & Complete</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Verification Section (shows when review is selected) -->
                                <div class="verification-section" id="verification_${index}" style="display: none;">
                                    <div class="warning-box customer-verify">
                                        <i class="fas fa-user-check"></i>
                                        <div>
                                            <strong>Customer Verification Required</strong>
                                            <p>Customer will be notified to verify completion. Payment will be released after approval.</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Completion Evidence/Documentation (Required)</label>
                                        <textarea name="milestone_${index}_evidence" rows="3" placeholder="Describe completed work:
&bull; What was done?
&bull; Any photos/documentation?
&bull; Ready for customer inspection?" required></textarea>
                                    </div>
                                    <div class="notification-preview">
                                        <i class="fas fa-bell"></i>
                                        <div>
                                            <strong>Customer will receive:</strong>
                                            <ul>
                                                <li>Email & SMS notification</li>
                                                <li>Evidence/photos you provided</li>
                                                <li>Option to approve or request changes</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Completion Timeline (readonly info) -->
                                <div class="milestone-timeline-info" style="display: none;" id="timeline_${index}">
                                    <div class="timeline-step">
                                        <i class="fas fa-check-circle text-success"></i>
                                        <span>Work completed - Awaiting verification</span>
                                    </div>
                                    <div class="timeline-step pending">
                                        <i class="fas fa-clock text-muted"></i>
                                        <span>Customer review pending</span>
                                    </div>
                                    <div class="timeline-step pending">
                                        <i class="fas fa-money-bill-wave text-muted"></i>
                                        <span>Payment release (auto after approval)</span>
                                    </div>
                                </div>
                            </div>
                        `).join('')}

                        <!-- Internal Notes -->
                        <div class="form-divider">
                            <h4><i class="fas fa-sticky-note"></i> Progress Update Notes</h4>
                        </div>

                        <div class="form-group">
                            <label>What was done today? (Optional)</label>
                            <textarea name="notes" rows="3" placeholder="Describe today's progress, issues encountered, or next steps..."></textarea>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn-save" onclick="saveProjectChanges()">
                        <i class="fas fa-check"></i> Update Status
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Close modal when clicking on overlay background
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeEditModal();
            }
        });
    }

    function getCurrentProjectData() {
        const description = document.querySelector('.project-description p')?.textContent || '';

        const milestones = [];
        document.querySelectorAll('.milestone-item').forEach((item, index) => {
            const status = item.classList.contains('completed') ? 'completed' :
                item.classList.contains('active') ? 'active' : 'pending';

            milestones.push({
                title: item.querySelector('h5')?.textContent || '',
                description: item.querySelector('p')?.textContent || '',
                amount: item.querySelector('.payment-amount')?.textContent.replace(/[^\d]/g, '') || '37500',
                date: '2025-09-05',
                status: status,
                paymentStatus: status === 'completed' ? 'paid' : 'pending',
                verifiedByCustomer: status === 'completed'
            });
        });

        return {
            description: description,
            milestones: milestones
        };
    }

    function getMilestonePaymentStatus(milestone) {
        if (milestone.status === 'completed' && milestone.paymentStatus === 'paid') {
            return `<span class="payment-status paid"><i class="fas fa-check-circle"></i> Payment Released</span>`;
        } else if (milestone.status === 'completed' && !milestone.verifiedByCustomer) {
            return `<span class="payment-status pending-review"><i class="fas fa-clock"></i> Awaiting Customer Approval</span>`;
        } else if (milestone.status === 'active') {
            return `<span class="payment-status in-progress"><i class="fas fa-hourglass-half"></i> Work In Progress</span>`;
        } else {
            return `<span class="payment-status unpaid"><i class="fas fa-circle"></i> Payment Pending</span>`;
        }
    }

    function closeEditModal() {
        const modal = document.getElementById('projectEditModal');
        if (modal) {
            modal.classList.remove('active');
            setTimeout(() => modal.remove(), 300);
        }
    }

    function handleMilestoneStatusChange(index, status) {
        const verificationSection = document.getElementById(`verification_${index}`);
        const timelineInfo = document.getElementById(`timeline_${index}`);

        if (status === 'review') {
            // Show verification requirements
            verificationSection.style.display = 'block';
            timelineInfo.style.display = 'block';
            verificationSection.querySelector('textarea').required = true;

            // Update timeline to show current step
            const timelineSteps = timelineInfo.querySelectorAll('.timeline-step');
            timelineSteps[0].classList.remove('pending');
            timelineSteps[1].classList.add('active');
            timelineSteps[2].classList.add('pending');
        } else {
            // Hide verification section
            verificationSection.style.display = 'none';
            timelineInfo.style.display = 'none';
            verificationSection.querySelector('textarea').required = false;
        }

        // Recalculate progress and auto-update project status
        calculateProgressAndUpdateStatus();
    }

    function calculateProgress() {
        const milestoneSelects = document.querySelectorAll('[name^="milestone_"][name$="_status"]');
        let totalMilestones = milestoneSelects.length;
        let completedMilestones = 0;

        milestoneSelects.forEach(select => {
            if (select.value === 'completed') {
                completedMilestones++;
            }
        });

        const progress = Math.round((completedMilestones / totalMilestones) * 100);

        // Update display
        document.getElementById('calculatedProgress').textContent = progress + '%';
        document.getElementById('progressFill').style.width = progress + '%';

        return progress;
    }

    function calculateProgressAndUpdateStatus() {
        const progress = calculateProgress();
        const statusSelect = document.querySelector('[name="status"]');

        const milestoneSelects = document.querySelectorAll('[name^="milestone_"][name$="_status"]');
        let hasDelayed = false;
        let allCompleted = true;

        milestoneSelects.forEach(select => {
            if (select.value === 'pending') {
                allCompleted = false;
            }
            // Check if deadline passed for pending/active milestones
            // You would implement actual deadline checking here
        });

        // Auto-update project status based on milestones
        if (progress === 100 && allCompleted) {
            statusSelect.value = 'completed';
            showStatusUpdateNotice('Project status auto-updated to Completed (all milestones done)');
        } else if (hasDelayed) {
            statusSelect.value = 'delayed';
            showStatusUpdateNotice('Project status auto-updated to Delayed (milestone past due)');
        } else if (progress > 0) {
            statusSelect.value = 'ongoing';
        }

        // Disable manual override if all milestones are complete
        if (allCompleted) {
            statusSelect.disabled = true;
        } else {
            statusSelect.disabled = false;
        }
    }

    function showStatusUpdateNotice(message) {
        const existingNotice = document.querySelector('.auto-status-notice');
        if (existingNotice) {
            existingNotice.remove();
        }

        const notice = document.createElement('div');
        notice.className = 'auto-status-notice';
        notice.innerHTML = `
            <i class="fas fa-info-circle"></i>
            <span>${message}</span>
        `;
        notice.style.cssText = `
            background: linear-gradient(135deg, rgba(10, 186, 181, 0.1), rgba(10, 186, 181, 0.05));
            border-left: 4px solid var(--primary-color);
            padding: 12px 16px;
            margin-bottom: 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-primary);
            font-size: 14px;
            animation: slideInDown 0.3s ease;
        `;

        const statusGroup = document.querySelector('[name="status"]').closest('.form-group');
        statusGroup.parentNode.insertBefore(notice, statusGroup);
    }

    function saveProjectChanges() {
        const form = document.getElementById('projectEditForm');

        // Validate form first
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const calculatedProgress = calculateProgress();

        const projectData = {
            status: formData.get('status'),
            progress: calculatedProgress, // Auto-calculated, not from user input
            notes: formData.get('notes'),
            milestones: [],
            timestamp: new Date().toISOString(),
            updatedBy: 'Current User', // TODO: Get from auth system
            customerNotifications: []
        };

        // Collect milestone status updates with verification
        const milestoneItems = document.querySelectorAll('.milestone-status-item');
        milestoneItems.forEach((item, index) => {
            const status = formData.get(`milestone_${index}_status`);
            const milestoneData = {
                index: index,
                status: status
            };

            // If submitted for review, prepare customer notification
            if (status === 'review') {
                milestoneData.evidence = formData.get(`milestone_${index}_evidence`);
                milestoneData.submittedForReview = new Date().toISOString();
                milestoneData.requiresCustomerApproval = true;

                // Add to notifications queue
                projectData.customerNotifications.push({
                    type: 'milestone_review',
                    milestoneIndex: index,
                    milestoneTitle: item.querySelector('.milestone-title-row strong').textContent,
                    evidence: milestoneData.evidence,
                    notificationChannels: ['email', 'sms', 'in-app']
                });
            }

            // If already completed (customer verified), include verification details
            if (status === 'completed') {
                milestoneData.verifiedByCustomer = true;
                milestoneData.completedAt = new Date().toISOString();
                milestoneData.paymentStatus = 'released';
            }

            projectData.milestones.push(milestoneData);
        });


        // Show what will happen
        let notificationMessage = `âœ… Project status updated successfully!\n\n`;
        notificationMessage += `ðŸ“Š Progress: ${calculatedProgress}%\n`;
        notificationMessage += `ðŸ“‹ Status: ${projectData.status}\n\n`;

        if (projectData.customerNotifications.length > 0) {
            notificationMessage += `ðŸ”” Customer Notifications:\n`;
            projectData.customerNotifications.forEach(notif => {
                notificationMessage += `  &bull; ${notif.milestoneTitle} - Pending customer approval\n`;
            });
            notificationMessage += `\nðŸ“§ Customer will be notified via Email & SMS\n`;
            notificationMessage += `ðŸ’° Payment will auto-release upon approval\n`;
        }

        // TODO: Send to backend API
        // const response = await fetch('/api/projects/update-status', { 
        //     method: 'POST', 
        //     body: JSON.stringify(projectData),
        //     headers: { 'Content-Type': 'application/json' }
        // });

        // Backend should:
        // 1. Update project status
        // 2. Send customer notifications
        // 3. Create approval requests for customer
        // 4. Log all changes for audit trail
        // 5. Set up payment release triggers for approved milestones

        showEnhancedSuccessMessage(notificationMessage, projectData);
        closeEditModal();

        // Reload to show updated status
        // setTimeout(() => location.reload(), 2000);
    }

    function showEnhancedSuccessMessage(message, projectData) {
        const modal = document.createElement('div');
        modal.className = 'success-modal-overlay active';
        modal.innerHTML = `
            <div class="success-modal">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>Update Successful!</h3>
                <div class="success-details">
                    <div class="detail-row">
                        <span class="label">Progress:</span>
                        <span class="value">${projectData.progress}%</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Status:</span>
                        <span class="value status-${projectData.status}">${projectData.status}</span>
                    </div>
                    ${projectData.customerNotifications.length > 0 ? `
                        <div class="notifications-section">
                            <h4><i class="fas fa-bell"></i> Customer Notifications Sent</h4>
                            ${projectData.customerNotifications.map(notif => `
                                <div class="notification-item">
                                    <i class="fas fa-check"></i>
                                    <span>${notif.milestoneTitle} - Pending customer review</span>
                                </div>
                            `).join('')}
                            <p class="notification-note">
                                <i class="fas fa-info-circle"></i>
                                Payment will auto-release upon customer approval
                            </p>
                        </div>
                    ` : ''}
                </div>
                <button class="btn-primary" onclick="closeSuccessModal()">
                    <i class="fas fa-check"></i> Got it
                </button>
            </div>
        `;
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10002;
            animation: fadeIn 0.3s ease;
        `;

        document.body.appendChild(modal);

        window.closeSuccessModal = function () {
            modal.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => modal.remove(), 300);
        };
    }

    window.enableEditMode = enableEditMode;
    window.closeEditModal = closeEditModal;
    window.saveProjectChanges = saveProjectChanges;
    window.handleMilestoneStatusChange = handleMilestoneStatusChange;

    // ================================================
    // EXPORT FUNCTIONALITY
    // ================================================

    function openExportModal() {
        const modal = document.getElementById('exportModal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeExportModal() {
        const modal = document.getElementById('exportModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function executeExport() {
        const format = document.querySelector('input[name="exportFormat"]:checked')?.value || 'csv';
        const dataRange = document.querySelector('input[name="dataRange"]:checked')?.value || 'all';
        const selectedColumns = Array.from(document.querySelectorAll('input[name="column"]:checked'))
            .map(cb => cb.value);

        if (selectedColumns.length === 0) {
            alert('Please select at least one column to export.');
            return;
        }

        // Get project data
        const projectData = getProjectData(dataRange);

        // Export based on format
        switch (format) {
            case 'csv':
                exportToCSV(projectData, selectedColumns);
                break;
            case 'excel':
                exportToExcel(projectData, selectedColumns);
                break;
            case 'pdf':
                exportToPDF(projectData, selectedColumns);
                break;
        }

        closeExportModal();
    }

    function getProjectData(dataRange) {
        const projects = [];

        // Determine which rows to export
        let rows;
        if (dataRange === 'visible') {
            // Get visible rows in current view
            const activeView = document.querySelector('.table-view.active') ? 'table' : 'cards';
            if (activeView === 'table') {
                rows = document.querySelectorAll('.table-view .project-row');
            } else {
                rows = document.querySelectorAll('.card-view .project-card');
            }
        } else {
            // Get all rows (from table view)
            rows = document.querySelectorAll('.table-view .project-row');
        }

        rows.forEach(row => {
            const project = {
                title: row.querySelector('.project-title h4')?.textContent.trim() || '',
                id: row.querySelector('.project-id')?.textContent.trim() || '',
                customer: row.querySelector('.customer-name')?.textContent.trim() || '',
                customerContact: row.querySelector('.customer-contact')?.textContent.trim() || '',
                startDate: row.querySelector('.start-date')?.textContent.replace('Started:', '').trim() || '',
                deadline: row.querySelector('.deadline')?.textContent.replace('Due:', '').trim() || '',
                contract: row.querySelector('.status-badge')?.textContent.trim() || '',
                progress: row.querySelector('.progress-text')?.textContent.trim() || '',
                totalBudget: row.querySelector('.total-budget')?.textContent.trim() || '',
                spentBudget: row.querySelector('.spent-budget')?.textContent.replace('Spent:', '').trim() || '',
                status: row.querySelector('td:nth-child(7) .status-badge')?.textContent.trim() || ''
            };
            projects.push(project);
        });

        return projects;
    }

    function exportToCSV(projectData, selectedColumns) {
        const columnMapping = {
            title: 'Project Title',
            customer: 'Customer',
            timeline: 'Timeline',
            contract: 'Contract Status',
            progress: 'Progress',
            budget: 'Budget',
            status: 'Status'
        };

        // Build CSV headers
        const headers = [];
        const dataKeys = [];

        selectedColumns.forEach(col => {
            switch (col) {
                case 'title':
                    headers.push('Project Title', 'Project ID');
                    dataKeys.push('title', 'id');
                    break;
                case 'customer':
                    headers.push('Customer Name', 'Customer Contact');
                    dataKeys.push('customer', 'customerContact');
                    break;
                case 'timeline':
                    headers.push('Start Date', 'Deadline');
                    dataKeys.push('startDate', 'deadline');
                    break;
                case 'contract':
                    headers.push('Contract Status');
                    dataKeys.push('contract');
                    break;
                case 'progress':
                    headers.push('Progress');
                    dataKeys.push('progress');
                    break;
                case 'budget':
                    headers.push('Total Budget', 'Spent Budget');
                    dataKeys.push('totalBudget', 'spentBudget');
                    break;
                case 'status':
                    headers.push('Status');
                    dataKeys.push('status');
                    break;
            }
        });

        // Build CSV content
        let csvContent = headers.join(',') + '\n';

        projectData.forEach(project => {
            const row = dataKeys.map(key => {
                const value = project[key] || '';
                // Escape quotes and wrap in quotes if contains comma
                return value.includes(',') || value.includes('"')
                    ? `"${value.replace(/"/g, '""')}"`
                    : value;
            });
            csvContent += row.join(',') + '\n';
        });

        // Download CSV
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);

        const date = new Date().toISOString().split('T')[0];
        link.setAttribute('href', url);
        link.setAttribute('download', `FixLanka_Projects_${date}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        showExportSuccessMessage('CSV file downloaded successfully!');
    }

    function exportToExcel(projectData, selectedColumns) {
        // For Excel export, we'll create an HTML table and convert it
        // This is a simple approach that works in most browsers

        const columnMapping = {
            title: ['Project Title', 'Project ID'],
            customer: ['Customer Name', 'Customer Contact'],
            timeline: ['Start Date', 'Deadline'],
            contract: ['Contract Status'],
            progress: ['Progress'],
            budget: ['Total Budget', 'Spent Budget'],
            status: ['Status']
        };

        let tableHTML = '<table border="1"><thead><tr>';

        // Build headers
        const dataKeys = [];
        selectedColumns.forEach(col => {
            const headers = columnMapping[col] || [col];
            headers.forEach(header => {
                tableHTML += `<th>${header}</th>`;
            });

            // Map to data keys
            switch (col) {
                case 'title':
                    dataKeys.push('title', 'id');
                    break;
                case 'customer':
                    dataKeys.push('customer', 'customerContact');
                    break;
                case 'timeline':
                    dataKeys.push('startDate', 'deadline');
                    break;
                case 'contract':
                    dataKeys.push('contract');
                    break;
                case 'progress':
                    dataKeys.push('progress');
                    break;
                case 'budget':
                    dataKeys.push('totalBudget', 'spentBudget');
                    break;
                case 'status':
                    dataKeys.push('status');
                    break;
            }
        });

        tableHTML += '</tr></thead><tbody>';

        // Build rows
        projectData.forEach(project => {
            tableHTML += '<tr>';
            dataKeys.forEach(key => {
                tableHTML += `<td>${project[key] || ''}</td>`;
            });
            tableHTML += '</tr>';
        });

        tableHTML += '</tbody></table>';

        // Create blob and download
        const blob = new Blob([tableHTML], { type: 'application/vnd.ms-excel' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);

        const date = new Date().toISOString().split('T')[0];
        link.setAttribute('href', url);
        link.setAttribute('download', `FixLanka_Projects_${date}.xls`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        showExportSuccessMessage('Excel file downloaded successfully!');
    }

    function exportToPDF(projectData, selectedColumns) {
        // For PDF, we'll create a printable HTML page
        const printWindow = window.open('', '_blank');

        const columnMapping = {
            title: ['Project Title', 'Project ID'],
            customer: ['Customer Name', 'Contact'],
            timeline: ['Start Date', 'Deadline'],
            contract: ['Contract Status'],
            progress: ['Progress'],
            budget: ['Total Budget', 'Spent'],
            status: ['Status']
        };

        let tableHTML = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>FixLanka Projects Report</title>
                <style>
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }
                    body {
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                        padding: 20px;
                        color: #333;
                    }
                    .header {
                        text-align: center;
                        margin-bottom: 30px;
                        padding-bottom: 20px;
                        border-bottom: 3px solid #0abab5;
                    }
                    .header h1 {
                        color: #0abab5;
                        font-size: 28px;
                        margin-bottom: 10px;
                    }
                    .header p {
                        color: #666;
                        font-size: 14px;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 20px;
                        font-size: 12px;
                    }
                    th {
                        background: #0abab5;
                        color: white;
                        padding: 12px 8px;
                        text-align: left;
                        font-weight: 600;
                        border: 1px solid #08908c;
                    }
                    td {
                        padding: 10px 8px;
                        border: 1px solid #ddd;
                    }
                    tr:nth-child(even) {
                        background: #f9f9f9;
                    }
                    tr:hover {
                        background: #f0f9f9;
                    }
                    .footer {
                        margin-top: 30px;
                        text-align: center;
                        font-size: 12px;
                        color: #666;
                        padding-top: 20px;
                        border-top: 1px solid #ddd;
                    }
                    @media print {
                        body {
                            padding: 0;
                        }
                        .no-print {
                            display: none;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>ðŸ”§ FixLanka Projects Report</h1>
                    <p>Generated on ${new Date().toLocaleDateString()} at ${new Date().toLocaleTimeString()}</p>
                    <p>Total Projects: ${projectData.length}</p>
                </div>
                
                <table>
                    <thead>
                        <tr>
        `;

        const dataKeys = [];
        selectedColumns.forEach(col => {
            const headers = columnMapping[col] || [col];
            headers.forEach(header => {
                tableHTML += `<th>${header}</th>`;
            });

            switch (col) {
                case 'title':
                    dataKeys.push('title', 'id');
                    break;
                case 'customer':
                    dataKeys.push('customer', 'customerContact');
                    break;
                case 'timeline':
                    dataKeys.push('startDate', 'deadline');
                    break;
                case 'contract':
                    dataKeys.push('contract');
                    break;
                case 'progress':
                    dataKeys.push('progress');
                    break;
                case 'budget':
                    dataKeys.push('totalBudget', 'spentBudget');
                    break;
                case 'status':
                    dataKeys.push('status');
                    break;
            }
        });

        tableHTML += '</tr></thead><tbody>';

        projectData.forEach(project => {
            tableHTML += '<tr>';
            dataKeys.forEach(key => {
                tableHTML += `<td>${project[key] || '-'}</td>`;
            });
            tableHTML += '</tr>';
        });

        tableHTML += `
                    </tbody>
                </table>
                
                <div class="footer">
                    <p>Â© ${new Date().getFullYear()} FixLanka - Project Management System</p>
                    <p>This is an automated report. For queries, contact your administrator.</p>
                </div>
                
                <script>
                    window.onload = function() {
                        window.print();
                    }
                </script>
            </body>
            </html>
        `;

        printWindow.document.write(tableHTML);
        printWindow.document.close();

        showExportSuccessMessage('PDF generation initiated. Please use your browser\'s print dialog.');
    }

    function showExportSuccessMessage(message) {
        const notification = document.createElement('div');
        notification.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
        `;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
            z-index: 10001;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            animation: slideInRight 0.3s ease;
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Close export modal on clicking overlay
    document.addEventListener('click', function (e) {
        const exportModal = document.getElementById('exportModal');
        if (e.target === exportModal) {
            closeExportModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const exportModal = document.getElementById('exportModal');
            if (exportModal && exportModal.style.display === 'flex') {
                closeExportModal();
            }
        }
    });

    // Make functions globally available
    window.openExportModal = openExportModal;
    window.closeExportModal = closeExportModal;
    window.executeExport = executeExport;

});
