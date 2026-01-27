
document.addEventListener('DOMContentLoaded', function () {
    console.log('Support page initialized');
    fetchTickets(); // Run this first to ensure data loads
    try {
        initializeTicketForm();
    } catch (e) {
        console.error('Error initializing ticket form:', e);
    }
    try {
        initializeFilters();
    } catch (e) {
        console.error('Error initializing filters:', e);
    }
});

function initializeCategoryFilters() {
    const cards = document.querySelectorAll('.category-card');

    cards.forEach(card => {
        card.addEventListener('click', () => {
            // Remove active class from all
            cards.forEach(c => c.classList.remove('active'));

            // Add to clicked
            card.classList.add('active');

            // Fetch tickets
            const category = card.dataset.category;
            const filters = category ? { category: category } : {};
            fetchTickets(filters);
        });
    });
}

// Fetch tickets from API
async function fetchTickets(filters = {}) {
    const listContainer = document.getElementById('recentTicketsList');

    try {
        let url = '../../api/company-support.php';
        const params = new URLSearchParams(filters);
        if (Object.keys(filters).length > 0) url += `?${params.toString()}`;

        const response = await fetch(url);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Response is not JSON:', text);
            throw new Error('Invalid server response: ' + text.substring(0, 100));
        }

        if (data.success) {
            renderTickets(data.tickets);
            // updateStats(data.stats); // TODO: Implement stats update if UI elements exist
        } else {
            console.error('Failed to load tickets:', data.message);
            listContainer.innerHTML = `<div class="error-message">Failed to load tickets: ${data.message}</div>`;
        }
    } catch (error) {
        console.error('Error fetching tickets:', error);
        listContainer.innerHTML = `<div class="error-message">Error loading tickets: ${error.message}</div>`;
    }
}

function renderTickets(tickets) {
    const listContainer = document.getElementById('recentTicketsList');

    if (!tickets || tickets.length === 0) {
        listContainer.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-ticket-alt"></i>
                <h3>No Tickets Found</h3>
                <p>You don't have any support tickets matching your criteria.</p>
            </div>
        `;
        return;
    }

    listContainer.innerHTML = tickets.map(ticket => `
        <div class="ticket-item ${getPriorityClass(ticket.priority)}">
            <div class="ticket-id">#SP-${String(ticket.ticket_id).padStart(3, '0')}</div>
            <div class="ticket-details">
                <h4>${escapeHtml(ticket.title)}</h4>
                <p>${escapeHtml(ticket.description)}</p>
                <span class="ticket-meta">${formatDate(ticket.created_at)}</span>
            </div>
            <div class="ticket-priority ${ticket.priority.toLowerCase()}">${capitalize(ticket.priority)}</div>
            <div class="ticket-status ${ticket.status.toLowerCase()}">${capitalize(ticket.status)}</div>
        </div>
    `).join('');
}

function getPriorityClass(priority) {
    return `${priority.toLowerCase()}-priority`;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000); // seconds

    if (diff < 60) return 'Just now';
    if (diff < 3600) return `${Math.floor(diff / 60)} minutes ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} hours ago`;
    if (diff < 2592000) return `${Math.floor(diff / 86400)} days ago`;

    return date.toLocaleDateString();
}

function capitalize(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1).replace('_', ' ');
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}


// New Ticket Form Functionality
function initializeTicketForm() {
    const modal = document.getElementById('ticketModal');
    const newTicketBtn = document.getElementById('newTicketBtn');
    const closeModal = document.getElementById('closeModal');
    const overlay = modal.querySelector('.modal-overlay');
    const form = document.getElementById('ticketForm');
    const steps = document.querySelectorAll('.form-step');
    const progressSteps = document.querySelectorAll('.step');
    const progressFill = document.querySelector('.progress-fill');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const successState = document.getElementById('successState');

    let currentStep = 1;
    const totalSteps = 3;

    // Modal controls
    if (newTicketBtn) newTicketBtn.addEventListener('click', () => openModal());
    if (closeModal) closeModal.addEventListener('click', () => closeModalFunc());
    if (overlay) overlay.addEventListener('click', () => closeModalFunc());

    // Form navigation
    if (nextBtn) nextBtn.addEventListener('click', () => nextStep());
    if (prevBtn) prevBtn.addEventListener('click', () => prevStep());
    if (submitBtn) submitBtn.addEventListener('click', (e) => submitForm(e));

    // Edit step buttons
    document.querySelectorAll('.edit-step').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const step = parseInt(e.target.closest('.edit-step').dataset.step);
            goToStep(step);
        });
    });

    // Character counters
    initializeCharCounters();

    // File upload
    initializeFileUpload();

    // Form validation
    initializeValidation();

    // Success state actions
    const createAnotherBtn = document.getElementById('createAnotherBtn');
    if (createAnotherBtn) {
        createAnotherBtn.addEventListener('click', () => {
            resetForm();
            successState.style.display = 'none';
            form.style.display = 'block';
            goToStep(1);
        });
    }

    const closeSuccessBtn = document.getElementById('closeSuccessBtn');
    if (closeSuccessBtn) closeSuccessBtn.addEventListener('click', () => {
        closeModalFunc();
        fetchTickets(); // Refresh list
    });

    const viewTicketBtn = document.getElementById('viewTicketBtn');
    if (viewTicketBtn) viewTicketBtn.addEventListener('click', () => {
        // Simulate viewing ticket
        alert('Redirecting to ticket view...'); // TODO: Implement view ticket
        closeModalFunc();
        fetchTickets();
    });

    function openModal() {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        resetForm();
    }

    function closeModalFunc() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        setTimeout(() => {
            resetForm();
        }, 300);
    }

    function nextStep() {
        if (validateCurrentStep()) {
            if (currentStep < totalSteps) {
                currentStep++;
                updateStep();
            }
        }
    }

    function prevStep() {
        if (currentStep > 1) {
            currentStep--;
            updateStep();
        }
    }

    function goToStep(step) {
        currentStep = step;
        updateStep();
    }

    function updateStep() {
        // Update form steps
        steps.forEach((step, index) => {
            step.classList.toggle('active', index + 1 === currentStep);
        });

        // Update progress steps
        progressSteps.forEach((step, index) => {
            const stepNum = index + 1;
            step.classList.toggle('active', stepNum === currentStep);
            step.classList.toggle('completed', stepNum < currentStep);
        });

        // Update progress bar
        const progressPercent = (currentStep / totalSteps) * 100;
        progressFill.style.width = progressPercent + '%';

        // Update navigation buttons
        prevBtn.style.display = currentStep > 1 ? 'flex' : 'none';
        nextBtn.style.display = currentStep < totalSteps ? 'flex' : 'none';
        submitBtn.style.display = currentStep === totalSteps ? 'flex' : 'none';

        // Update review content if on step 3
        if (currentStep === 3) {
            updateReviewContent();
        }
    }

    function validateCurrentStep() {
        const currentStepElement = document.querySelector(`.form-step[data-step="${currentStep}"]`);
        const requiredFields = currentStepElement.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    function validateField(field) {
        const value = field.value.trim();
        const fieldGroup = field.closest('.form-group');
        const errorElement = fieldGroup.querySelector('.field-error');
        let isValid = true;
        let errorMessage = '';

        // Check if required field is empty
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'This field is required';
        }

        // Field-specific validation
        switch (field.id) {
            case 'ticketTitle':
                if (value && value.length < 10) {
                    isValid = false;
                    errorMessage = 'Title must be at least 10 characters long';
                }
                break;
            case 'ticketDescription':
                if (value && value.length < 20) {
                    isValid = false;
                    errorMessage = 'Description must be at least 20 characters long';
                }
                break;
            case 'agreement':
                if (!field.checked) {
                    isValid = false;
                    errorMessage = 'You must agree to the terms and conditions';
                }
                break;
        }

        // Update field appearance
        if (field.classList) field.classList.toggle('error', !isValid);

        // Show/hide error message
        if (errorElement) {
            if (errorMessage) {
                errorElement.textContent = errorMessage;
                errorElement.classList.add('show');
            } else {
                errorElement.classList.remove('show');
            }
        }

        return isValid;
    }

    function initializeCharCounters() {
        const fieldsWithCounters = ['ticketTitle', 'ticketDescription', 'ticketSteps'];

        fieldsWithCounters.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (!field) return;

            const counter = field.closest('.form-group').querySelector('.char-counter');
            if (!counter) return;

            const currentSpan = counter.querySelector('.current');
            const max = parseInt(counter.querySelector('.max').textContent);

            field.addEventListener('input', () => {
                const current = field.value.length;
                currentSpan.textContent = current;

                // Update counter color based on usage
                counter.classList.remove('warning', 'danger');
                if (current > max * 0.9) {
                    counter.classList.add('danger');
                } else if (current > max * 0.7) {
                    counter.classList.add('warning');
                }
            });
        });
    }

    function initializeFileUpload() {
        const uploadArea = document.getElementById('fileUploadArea');
        const fileInput = document.getElementById('ticketAttachments');
        const uploadedFiles = uploadArea?.querySelector('.uploaded-files');
        let files = [];

        if (!uploadArea || !fileInput) return;

        // Click to browse
        uploadArea.addEventListener('click', (e) => {
            if (e.target.classList.contains('upload-link') || e.target.closest('.upload-placeholder')) {
                fileInput.click();
            }
        });

        // File input change
        fileInput.addEventListener('change', (e) => {
            handleFiles(Array.from(e.target.files));
        });

        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            if (!uploadArea.contains(e.relatedTarget)) {
                uploadArea.classList.remove('dragover');
            }
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            handleFiles(Array.from(e.dataTransfer.files));
        });

        function handleFiles(newFiles) {
            newFiles.forEach(file => {
                if (file.size > 5 * 1024 * 1024) { // 5MB limit
                    alert(`${file.name} is too large. Maximum file size is 5MB.`);
                    return;
                }

                if (!isValidFileType(file)) {
                    alert(`${file.name} is not a supported file type.`);
                    return;
                }

                files.push(file);
                addFileToDisplay(file);
            });
        }

        function isValidFileType(file) {
            const allowedTypes = [
                'image/jpeg', 'image/jpg', 'image/png', 'image/gif',
                'application/pdf', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'text/plain'
            ];
            return allowedTypes.includes(file.type);
        }

        function addFileToDisplay(file) {
            const fileElement = document.createElement('div');
            fileElement.className = 'uploaded-file';
            fileElement.innerHTML = `
                <i class="fas fa-file-alt file-icon"></i>
                <div class="file-info">
                    <div class="file-name">${file.name}</div>
                    <div class="file-size">${formatFileSize(file.size)}</div>
                </div>
                <button type="button" class="remove-file" data-filename="${file.name}">
                    <i class="fas fa-times"></i>
                </button>
            `;

            fileElement.querySelector('.remove-file').addEventListener('click', () => {
                files = files.filter(f => f.name !== file.name);
                fileElement.remove();
            });

            uploadedFiles.appendChild(fileElement);
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    }

    function initializeValidation() {
        // Real-time validation for required fields
        if (!form) return;
        form.querySelectorAll('[required]').forEach(field => {
            field.addEventListener('blur', () => validateField(field));
            field.addEventListener('input', () => {
                if (field.classList.contains('error')) {
                    validateField(field);
                }
            });
        });
    }

    function updateReviewContent() {
        const formData = new FormData(form);

        // Basic information review
        const basicReview = document.getElementById('reviewBasic');
        basicReview.innerHTML = `
            <div class="review-item">
                <span class="review-label">Title:</span>
                <span class="review-value">${formData.get('title') || 'Not specified'}</span>
            </div>
            <div class="review-item">
                <span class="review-label">Category:</span>
                <span class="review-value">${getCategoryLabel(formData.get('category'))}</span>
            </div>
            <div class="review-item">
                <span class="review-label">Priority:</span>
                <span class="review-value">
                    <span class="priority-badge ${formData.get('priority')}">${getPriorityLabel(formData.get('priority'))}</span>
                </span>
            </div>
            <div class="review-item">
                <span class="review-label">Project:</span>
                <span class="review-value">${getProjectLabel(formData.get('project'))}</span>
            </div>
        `;

        // Details review
        const detailsReview = document.getElementById('reviewDetails');
        const urgency = formData.get('urgency');
        const affected = formData.getAll('affected[]');

        detailsReview.innerHTML = `
            <div class="review-item">
                <span class="review-label">Description:</span>
                <span class="review-value">${truncateText(formData.get('description') || 'Not provided', 100)}</span>
            </div>
            <div class="review-item">
                <span class="review-label">Urgency:</span>
                <span class="review-value">${getUrgencyLabel(urgency)}</span>
            </div>
            <div class="review-item">
                <span class="review-label">Affected:</span>
                <span class="review-value">${affected.map(a => getAffectedLabel(a)).join(', ')}</span>
            </div>
            <div class="review-item">
                <span class="review-label">Attachments:</span>
                <span class="review-value">${document.querySelectorAll('.uploaded-file').length} file(s)</span>
            </div>
        `;
    }

    function getCategoryLabel(value) {
        const labels = {
            'payment': 'Payment Issues',
            'technical': 'Technical Issues',
            'account': 'Account Issues',
            'feature': 'Feature Request',
            'billing': 'Billing Inquiry',
            'other': 'Other'
        };
        return labels[value] || 'Not specified';
    }

    function getPriorityLabel(value) {
        const labels = {
            'low': 'Low',
            'medium': 'Medium',
            'high': 'High',
            'urgent': 'Urgent'
        };
        return labels[value] || 'Not specified';
    }

    function getProjectLabel(value) {
        // In a real app, this should map IDs to names, maybe from a dropdown data attribute
        return value || 'None selected';
    }

    function getUrgencyLabel(value) {
        const labels = {
            'can-wait': 'Can wait (48h response)',
            'soon': 'Need help soon (24h response)',
            'asap': 'ASAP (4h response)'
        };
        return labels[value] || 'Not specified';
    }

    function getAffectedLabel(value) {
        const labels = {
            'just-me': 'Just me',
            'my-team': 'My team',
            'customers': 'Our customers'
        };
        return labels[value] || value;
    }

    function truncateText(text, maxLength) {
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    }

    async function submitForm(e) {
        e.preventDefault();

        if (!validateCurrentStep()) {
            return;
        }

        // Prepare data for API
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        data.affected = formData.getAll('affected[]');

        // Show loading state...
        const submitBtn = document.getElementById('submitBtn');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
        submitBtn.disabled = true;

        try {
            const response = await fetch('../../api/company-support.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                // Hide form and show success state
                form.style.display = 'none';
                successState.style.display = 'block';

                document.getElementById('ticketId').textContent = `#SP-${String(result.ticket_id).padStart(3, '0')}`;

                // Refresh list in background
                fetchTickets();
            } else {
                alert('Error creating ticket: ' + result.message);
            }

        } catch (error) {
            console.error('Error submitting ticket:', error);
            alert('An error occurred while creating the ticket.');
        } finally {
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        }
    }

    function resetForm() {
        form.reset();
        currentStep = 1;
        updateStep();

        // Clear uploaded files
        const uploadedFiles = document.querySelector('.uploaded-files');
        if (uploadedFiles) uploadedFiles.innerHTML = '';

        // Clear error states
        form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
        form.querySelectorAll('.field-error.show').forEach(el => el.classList.remove('show'));

        // Reset character counters
        form.querySelectorAll('.char-counter .current').forEach(el => el.textContent = '0');

        // Hide success state and show form
        successState.style.display = 'none';
        form.style.display = 'block';
    }

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (modal && modal.classList.contains('active')) {
            if (e.key === 'Escape') {
                closeModalFunc();
            }
        }
    });
}
