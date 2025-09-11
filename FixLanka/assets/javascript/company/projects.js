// FixLanka Projects Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    
    // View Toggle (Table/Card)
    const viewToggles = document.querySelectorAll('.view-toggle');
    const tableView = document.querySelector('.table-view');
    const cardView = document.querySelector('.card-view');
    
    viewToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const view = this.dataset.view;
            
            // Update toggle states
            viewToggles.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Switch views
            if (view === 'table') {
                tableView.classList.add('active');
                cardView.classList.remove('active');
            } else {
                cardView.classList.add('active');
                tableView.classList.remove('active');
            }
            
            // Reattach drawer events to new visible elements
            attachDrawerEvents();
        });
    });
    
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
            if (viewBtn) {
                viewBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    openDrawer(element.dataset.project);
                });
            }
            
            // Also open on element click
            element.addEventListener('click', function() {
                openDrawer(this.dataset.project);
            });
        });
    }
    
    // Phase Tab Navigation
    const phaseTabs = document.querySelectorAll('.phase-tab');
    
    phaseTabs.forEach(tab => {
        tab.addEventListener('click', function() {
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
    closeDrawer?.addEventListener('click', function() {
        drawerOverlay.classList.remove('active');
    });
    
    drawerOverlay?.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
    
    // Drawer Tab Navigation
    const drawerTabs = document.querySelectorAll('.drawer-tab');
    const drawerContents = document.querySelectorAll('.drawer-tab-content');
    
    drawerTabs.forEach(tab => {
        tab.addEventListener('click', function() {
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
    
    searchInput?.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        filterProjects(searchTerm);
    });
    
    // Filter Functions
    function filterByPhase(phase) {
        const projects = document.querySelectorAll('.project-row, .project-card');
        
        projects.forEach(project => {
            // Show/hide based on phase (implement your filtering logic)
            project.style.display = 'block';
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
    
    // Message Send Functionality
    const sendBtn = document.querySelector('.send-btn');
    const messageInput = document.querySelector('.message-input input');
    
    sendBtn?.addEventListener('click', function() {
        const message = messageInput.value.trim();
        if (message) {
            addMessage(message, 'company');
            messageInput.value = '';
        }
    });
    
    messageInput?.addEventListener('keypress', function(e) {
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
        const timeStr = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        
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
        element.addEventListener('blur', function() {
            // Save changes (implement your save logic)
            console.log('Saving:', this.textContent);
        });
        
        element.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.blur();
            }
        });
    });
    
    // File Upload Functionality
    const uploadBtn = document.querySelector('.upload-btn');
    
    uploadBtn?.addEventListener('click', function() {
        // Create and trigger file input
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.multiple = true;
        fileInput.accept = '.pdf,.doc,.docx,.jpg,.jpeg,.png,.zip';
        
        fileInput.addEventListener('change', function(e) {
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
                <div class="file-meta">${fileSize} • Uploaded ${uploadDate}</div>
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
        console.log('Applying filters...');
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
    dateRangeBtn?.addEventListener('click', function() {
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
        btn.addEventListener('click', function() {
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
    startDateInput?.addEventListener('change', function() {
        selectedStartDate = new Date(this.value);
        selectedQuickRange = null;
        quickSelectBtns.forEach(btn => btn.classList.remove('selected'));
        updateRangePreview();
    });
    
    endDateInput?.addEventListener('change', function() {
        selectedEndDate = new Date(this.value);
        selectedQuickRange = null;
        quickSelectBtns.forEach(btn => btn.classList.remove('selected'));
        updateRangePreview();
    });
    
    // Apply date range
    applyBtn?.addEventListener('click', function() {
        if (selectedStartDate && selectedEndDate) {
            const startStr = formatDateDisplay(selectedStartDate);
            const endStr = formatDateDisplay(selectedEndDate);
            
            // Update button text
            const dateText = dateRangeBtn.querySelector('.date-text');
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
    clearBtn?.addEventListener('click', function() {
        selectedStartDate = null;
        selectedEndDate = null;
        selectedQuickRange = null;
        
        // Clear inputs
        startDateInput.value = '';
        endDateInput.value = '';
        
        // Clear selections
        quickSelectBtns.forEach(btn => btn.classList.remove('selected'));
        
        // Reset button text
        const dateText = dateRangeBtn.querySelector('.date-text');
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
        console.log('Applying date filter:', startDate, 'to', endDate);
        
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
        console.log('Clearing date filter');
        
        const projects = document.querySelectorAll('.project-row, .project-card');
        projects.forEach(project => {
            project.style.display = 'block';
        });
    }
    
    // ESC key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && dateRangeModal.classList.contains('active')) {
            closeDateRangeModal();
        }
    });
    
});
