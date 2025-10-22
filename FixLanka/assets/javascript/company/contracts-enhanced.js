// ===================================
// CONTRACTS PAGE - COMPLETE IMPLEMENTATION
// ===================================

// Global State Management
let contractsData = [];
let currentContract = null;
let currentStep = 1;
let editingContract = null;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeContractsPage();
    loadContractsData();
});

function initializeContractsPage() {
    initializeFilters();
    initializeViewSwitcher();
    initializeContractActions();
    initializeModals();
    initializeNewContractForm();
    initializeSendContractModal();
    initializeDeleteModal();
    initializeScrollToTop();
    initializeExportModal();
}

// ===================================
// DATA MANAGEMENT
// ===================================

function loadContractsData() {
    // In production, this would fetch from API
    // For now, read from existing cards
    const contractCards = document.querySelectorAll('.contract-card');
    contractsData = Array.from(contractCards).map((card, index) => {
        return extractContractData(card, index + 1);
    });
}

function extractContractData(card, id) {
    const title = card.querySelector('.contract-info h3')?.textContent || '';
    const contractId = card.querySelector('.contract-id')?.textContent || `CNT-2025-${String(id).padStart(3, '0')}`;
    const clientName = card.querySelector('.client-details h4')?.textContent || '';
    const status = card.querySelector('.contract-status')?.textContent.trim().toLowerCase() || 'active';
    const value = card.querySelector('.contract-value')?.textContent || 'LKR 0';
    const progressElement = card.querySelector('.progress-fill');
    const progress = progressElement ? parseInt(progressElement.style.width) || 0 : 0;
    
    return {
        id,
        contractId,
        title,
        clientName,
        status,
        value,
        progress,
        type: card.getAttribute('data-type') || 'general',
        startDate: '2025-01-15',
        endDate: '2025-03-15',
        description: card.querySelector('.contract-description p')?.textContent || ''
    };
}

// ===================================
// FILTER SYSTEM
// ===================================

function initializeFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const dateFilter = document.getElementById('dateFilter');
    
    if (statusFilter) statusFilter.addEventListener('change', applyFilters);
    if (typeFilter) typeFilter.addEventListener('change', applyFilters);
    if (dateFilter) dateFilter.addEventListener('change', applyFilters);
}

function applyFilters() {
    const statusFilter = document.getElementById('statusFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    
    const contractCards = document.querySelectorAll('.contract-card');
    let visibleCount = 0;
    
    contractCards.forEach(card => {
        const cardStatus = card.getAttribute('data-status');
        const cardType = card.getAttribute('data-type');
        
        const statusMatch = !statusFilter || cardStatus === statusFilter;
        const typeMatch = !typeFilter || cardType === typeFilter;
        
        if (statusMatch && typeMatch) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    updateResultsCount(visibleCount);
}

function updateResultsCount(count) {
    const totalContracts = document.querySelectorAll('.contract-card').length;
    console.log(`Showing ${count} of ${totalContracts} contracts`);
}

// ===================================
// VIEW SWITCHER
// ===================================

function initializeViewSwitcher() {
    const viewButtons = document.querySelectorAll('.view-btn');
    const contractsContainer = document.getElementById('contractsContainer');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const viewType = this.getAttribute('data-view');
            
            viewButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            if (viewType === 'list') {
                contractsContainer.classList.add('list-view');
            } else {
                contractsContainer.classList.remove('list-view');
            }
            
            localStorage.setItem('contractsViewPreference', viewType);
        });
    });
    
    // Load saved preference
    const savedView = localStorage.getItem('contractsViewPreference');
    if (savedView) {
        const targetButton = document.querySelector(`[data-view="${savedView}"]`);
        if (targetButton) targetButton.click();
    }
}

// ===================================
// CONTRACT ACTIONS
// ===================================

function initializeContractActions() {
    document.addEventListener('click', function(e) {
        const contractCard = e.target.closest('.contract-card');
        if (!contractCard) return;
        
        const btn = e.target.closest('.action-btn');
        
        if (!btn) {
            // Click on card itself - view details
            handleViewContract(contractCard);
            return;
        }
        
        // Handle button clicks
        const title = btn.getAttribute('title')?.toLowerCase() || '';
        
        if (title.includes('view')) {
            handleViewContract(contractCard);
        } else if (title.includes('edit')) {
            handleEditContract(contractCard);
        } else if (title.includes('download')) {
            handleDownloadContract(contractCard);
        } else if (title.includes('send')) {
            handleSendContract(contractCard);
        } else if (title.includes('invoice')) {
            handleGenerateInvoice(contractCard);
        }
    });
    
    // New contract button
    const newContractBtn = document.getElementById('newContractBtn');
    if (newContractBtn) {
        newContractBtn.addEventListener('click', openNewContractModal);
    }
}

function handleViewContract(contractCard) {
    const contractData = extractDetailedContractData(contractCard);
    openContractDetailsModal(contractData);
}

function extractDetailedContractData(card) {
    const title = card.querySelector('.contract-info h3')?.textContent || '';
    const contractId = card.querySelector('.contract-id')?.textContent || '';
    const clientName = card.querySelector('.client-details h4')?.textContent || '';
    const statusElement = card.querySelector('.contract-status');
    const status = statusElement ? statusElement.textContent.trim() : 'Active';
    const value = card.querySelector('.contract-value')?.textContent || 'LKR 0';
    const progressElement = card.querySelector('.progress-fill');
    const progress = progressElement ? parseInt(progressElement.style.width) || 0 : 0;
    const description = card.querySelector('.contract-description p')?.textContent || '';
    
    return {
        id: contractId,
        title,
        client: clientName,
        status: status.toLowerCase(),
        progress,
        value,
        description,
        contactPerson: 'John Doe',
        email: 'john@example.com',
        phone: '+94 77 123 4567',
        type: card.getAttribute('data-type') || 'General',
        location: 'Colombo, Sri Lanka',
        startDate: '2025-01-15',
        endDate: '2025-03-15',
        stage: getProgressStage(progress),
        paidAmount: calculatePaidAmount(value, progress),
        remainingAmount: calculateRemainingAmount(value, progress),
        paymentTerms: '30 Days',
        contractType: 'Fixed Price',
        duration: '60 Days',
        priority: 'High',
        team: 'Team Alpha'
    };
}

function getProgressStage(progress) {
    if (progress < 25) return 'Planning Phase';
    if (progress < 50) return 'Design Phase';
    if (progress < 75) return 'Implementation Phase';
    if (progress < 90) return 'Testing Phase';
    return 'Final Review';
}

function calculatePaidAmount(value, progress) {
    const numValue = parseInt(value.replace(/[^\d]/g, ''));
    const paidAmount = Math.floor(numValue * progress / 100);
    return `LKR ${paidAmount.toLocaleString()}`;
}

function calculateRemainingAmount(value, progress) {
    const numValue = parseInt(value.replace(/[^\d]/g, ''));
    const remaining = numValue - Math.floor(numValue * progress / 100);
    return `LKR ${remaining.toLocaleString()}`;
}

function handleEditContract(contractCard) {
    const contractData = extractDetailedContractData(contractCard);
    openEditContractModal(contractData);
}

function handleDownloadContract(contractCard) {
    const title = contractCard.querySelector('.contract-info h3')?.textContent || 'Contract';
    showNotification(`Downloading ${title}...`, 'info');
    
    // Simulate download
    setTimeout(() => {
        const contractId = contractCard.querySelector('.contract-id')?.textContent || 'CNT-001';
        downloadContractPDF(contractId, title);
        showNotification('Contract downloaded successfully!', 'success');
    }, 1500);
}

function downloadContractPDF(contractId, title) {
    // Create a simple text file as placeholder
    const content = `CONTRACT AGREEMENT\n\n${contractId}\n${title}\n\nFixLanka Services\nwww.fixlanka.com\n\nThis is a placeholder contract document.`;
    const blob = new Blob([content], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${contractId.replace(/[^a-zA-Z0-9]/g, '_')}_contract.txt`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
}

function handleSendContract(contractCard) {
    currentContract = extractDetailedContractData(contractCard);
    openSendContractModal(currentContract);
}

function handleGenerateInvoice(contractCard) {
    const title = contractCard.querySelector('.contract-info h3')?.textContent || 'Contract';
    showNotification(`Generating invoice for ${title}...`, 'info');
    
    setTimeout(() => {
        showNotification('Invoice generated successfully!', 'success');
    }, 1500);
}

// ===================================
// CONTRACT DETAILS MODAL
// ===================================

function initializeModals() {
    const modal = document.getElementById('contractModal');
    const closeBtn = document.getElementById('closeModal');
    const modalClose = document.getElementById('modalClose');
    const modalEdit = document.getElementById('modalEdit');
    const modalDownload = document.getElementById('modalDownload');
    const modalPrint = document.getElementById('modalPrint');
    
    if (closeBtn) closeBtn.addEventListener('click', closeContractDetailsModal);
    if (modalClose) modalClose.addEventListener('click', closeContractDetailsModal);
    
    if (modalEdit) {
        modalEdit.addEventListener('click', function() {
            closeContractDetailsModal();
            if (currentContract) {
                openEditContractModal(currentContract);
            }
        });
    }
    
    if (modalDownload) {
        modalDownload.addEventListener('click', function() {
            if (currentContract) {
                downloadContractPDF(currentContract.id, currentContract.title);
                showNotification('Contract downloaded successfully!', 'success');
            }
        });
    }
    
    if (modalPrint) {
        modalPrint.addEventListener('click', function() {
            showNotification('Preparing contract for printing...', 'info');
            setTimeout(() => window.print(), 1000);
        });
    }
    
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeContractDetailsModal();
        });
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
            closeContractDetailsModal();
        }
    });
}

function openContractDetailsModal(contractData) {
    currentContract = contractData;
    const modal = document.getElementById('contractModal');
    
    if (modal) {
        populateModalContent(contractData);
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeContractDetailsModal() {
    const modal = document.getElementById('contractModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function populateModalContent(data) {
    // Header
    document.getElementById('modalContractTitle').textContent = data.title;
    document.getElementById('modalContractId').textContent = data.id;
    
    const statusBadge = document.getElementById('modalContractStatus');
    statusBadge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
    statusBadge.className = `contract-status-badge ${data.status}`;
    
    // Client Info
    document.getElementById('modalClientName').textContent = data.client;
    document.getElementById('modalContactPerson').textContent = data.contactPerson;
    document.getElementById('modalClientEmail').textContent = data.email;
    document.getElementById('modalClientPhone').textContent = data.phone;
    
    // Project Details
    document.getElementById('modalDescription').textContent = data.description;
    document.getElementById('modalProjectType').textContent = data.type;
    document.getElementById('modalLocation').textContent = data.location;
    document.getElementById('modalStartDate').textContent = data.startDate;
    document.getElementById('modalEndDate').textContent = data.endDate;
    
    // Progress
    document.getElementById('modalProgressFill').style.width = data.progress + '%';
    document.getElementById('modalProgressText').textContent = data.progress + '% Complete';
    document.getElementById('modalProgressStage').textContent = data.stage;
    
    // Financial
    document.getElementById('modalContractValue').textContent = data.value;
    document.getElementById('modalPaidAmount').textContent = data.paidAmount;
    document.getElementById('modalRemainingAmount').textContent = data.remainingAmount;
    document.getElementById('modalPaymentTerms').textContent = data.paymentTerms;
    
    // Contract Info
    document.getElementById('modalContractType').textContent = data.contractType;
    document.getElementById('modalDuration').textContent = data.duration;
    document.getElementById('modalPriority').textContent = data.priority;
    document.getElementById('modalTeam').textContent = data.team;
}

// ===================================
// NEW/EDIT CONTRACT FORM
// ===================================

function initializeNewContractForm() {
    const modal = document.getElementById('newContractModal');
    const closeBtn = document.getElementById('newContractClose');
    const cancelBtn = document.getElementById('formCancelBtn');
    const nextBtn = document.getElementById('formNextBtn');
    const prevBtn = document.getElementById('formPrevBtn');
    const submitBtn = document.getElementById('formSubmitBtn');
    
    if (closeBtn) closeBtn.addEventListener('click', closeNewContractModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeNewContractModal);
    if (nextBtn) nextBtn.addEventListener('click', nextFormStep);
    if (prevBtn) prevBtn.addEventListener('click', prevFormStep);
    if (submitBtn) submitBtn.addEventListener('click', submitContractForm);
    
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeNewContractModal();
        });
    }
}

function openNewContractModal() {
    editingContract = null;
    currentStep = 1;
    const modal = document.getElementById('newContractModal');
    document.getElementById('formModalTitle').textContent = 'Create New Contract';
    document.getElementById('formSubmitBtn').innerHTML = '<i class="fas fa-check"></i> Create Contract';
    
    // Reset form
    document.getElementById('contractForm').reset();
    showFormStep(1);
    
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function openEditContractModal(contractData) {
    editingContract = contractData;
    currentStep = 1;
    const modal = document.getElementById('newContractModal');
    document.getElementById('formModalTitle').textContent = 'Edit Contract';
    document.getElementById('formSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Update Contract';
    
    // Populate form with existing data
    populateFormWithContract(contractData);
    showFormStep(1);
    
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeNewContractModal() {
    const modal = document.getElementById('newContractModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        editingContract = null;
        currentStep = 1;
    }
}

function nextFormStep() {
    if (validateFormStep(currentStep)) {
        if (currentStep === 3) {
            // Before going to review, populate review data
            populateReviewStep();
        }
        currentStep++;
        showFormStep(currentStep);
    }
}

function prevFormStep() {
    currentStep--;
    showFormStep(currentStep);
}

function showFormStep(step) {
    // Update indicators
    document.querySelectorAll('.form-step-indicator').forEach((indicator, index) => {
        if (index + 1 < step) {
            indicator.classList.add('completed');
            indicator.classList.remove('active');
        } else if (index + 1 === step) {
            indicator.classList.add('active');
            indicator.classList.remove('completed');
        } else {
            indicator.classList.remove('active', 'completed');
        }
    });
    
    // Update content
    document.querySelectorAll('.form-step-content').forEach((content, index) => {
        if (index + 1 === step) {
            content.classList.add('active');
        } else {
            content.classList.remove('active');
        }
    });
    
    // Update buttons
    const prevBtn = document.getElementById('formPrevBtn');
    const nextBtn = document.getElementById('formNextBtn');
    const submitBtn = document.getElementById('formSubmitBtn');
    
    if (step === 1) {
        prevBtn.style.display = 'none';
    } else {
        prevBtn.style.display = 'inline-flex';
    }
    
    if (step === 4) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'inline-flex';
    } else {
        nextBtn.style.display = 'inline-flex';
        submitBtn.style.display = 'none';
    }
}

function validateFormStep(step) {
    const stepContent = document.querySelector(`.form-step-content[data-step="${step}"]`);
    const requiredFields = stepContent.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('error');
            isValid = false;
        } else {
            field.classList.remove('error');
        }
    });
    
    if (!isValid) {
        showNotification('Please fill in all required fields', 'error');
    }
    
    return isValid;
}

function populateFormWithContract(data) {
    // Client Info
    document.getElementById('clientName').value = data.client;
    document.getElementById('contactPerson').value = data.contactPerson;
    document.getElementById('clientEmail').value = data.email;
    document.getElementById('clientPhone').value = data.phone;
    
    // Project Details
    document.getElementById('projectTitle').value = data.title;
    document.getElementById('projectLocation').value = data.location;
    document.getElementById('startDate').value = data.startDate;
    document.getElementById('endDate').value = data.endDate;
    document.getElementById('projectDescription').value = data.description;
    
    // Financial
    const numValue = parseInt(data.value.replace(/[^\d]/g, ''));
    document.getElementById('contractValue').value = numValue;
}

function populateReviewStep() {
    // Client Info
    document.getElementById('reviewClientName').textContent = document.getElementById('clientName').value;
    document.getElementById('reviewClientType').textContent = document.getElementById('clientType').value;
    document.getElementById('reviewContactPerson').textContent = document.getElementById('contactPerson').value;
    document.getElementById('reviewClientEmail').textContent = document.getElementById('clientEmail').value;
    document.getElementById('reviewClientPhone').textContent = document.getElementById('clientPhone').value;
    document.getElementById('reviewClientAddress').textContent = document.getElementById('clientAddress').value || 'N/A';
    
    // Project Details
    document.getElementById('reviewProjectTitle').textContent = document.getElementById('projectTitle').value;
    document.getElementById('reviewProjectType').textContent = document.getElementById('projectType').value;
    document.getElementById('reviewProjectLocation').textContent = document.getElementById('projectLocation').value;
    document.getElementById('reviewStartDate').textContent = document.getElementById('startDate').value;
    document.getElementById('reviewEndDate').textContent = document.getElementById('endDate').value;
    document.getElementById('reviewPriority').textContent = document.getElementById('priority').value;
    document.getElementById('reviewDescription').textContent = document.getElementById('projectDescription').value;
    
    // Financial
    const value = document.getElementById('contractValue').value;
    document.getElementById('reviewContractValue').textContent = `LKR ${parseInt(value).toLocaleString()}`;
    document.getElementById('reviewContractType').textContent = document.getElementById('contractType').value;
    document.getElementById('reviewPaymentTerms').textContent = document.getElementById('paymentTerms').value;
    const advance = document.getElementById('advancePayment').value;
    document.getElementById('reviewAdvancePayment').textContent = advance ? `LKR ${parseInt(advance).toLocaleString()}` : 'N/A';
}

function submitContractForm() {
    const form = document.getElementById('contractForm');
    const formData = new FormData(form);
    
    const contractData = {};
    formData.forEach((value, key) => {
        contractData[key] = value;
    });
    
    const submitBtn = document.getElementById('formSubmitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    
    // Simulate API call
    setTimeout(() => {
        if (editingContract) {
            showNotification('Contract updated successfully!', 'success');
        } else {
            addNewContractCard(contractData);
            showNotification('Contract created successfully!', 'success');
        }
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        closeNewContractModal();
    }, 2000);
}

function addNewContractCard(data) {
    const container = document.getElementById('contractsContainer');
    const cardHTML = createContractCardHTML(data);
    container.insertAdjacentHTML('afterbegin', cardHTML);
    
    // Animate the new card
    const newCard = container.querySelector('.contract-card');
    newCard.style.opacity = '0';
    newCard.style.transform = 'scale(0.9)';
    setTimeout(() => {
        newCard.style.transition = 'all 0.3s ease';
        newCard.style.opacity = '1';
        newCard.style.transform = 'scale(1)';
    }, 100);
}

function createContractCardHTML(data) {
    const contractNumber = document.querySelectorAll('.contract-card').length + 1;
    const status = 'pending';
    
    return `
        <div class="contract-card" data-status="${status}" data-type="${data.projectType}">
            <div class="contract-header">
                <div class="contract-info">
                    <h3>${data.projectTitle}</h3>
                    <p class="contract-id">Contract #CNT-2025-${String(contractNumber).padStart(3, '0')}</p>
                </div>
                <div class="contract-status ${status}">
                    <i class="fas fa-clock"></i>
                    Pending
                </div>
            </div>
            <div class="client-info">
                <div class="client-avatar">${data.clientName.substring(0, 2).toUpperCase()}</div>
                <div class="client-details">
                    <h4>${data.clientName}</h4>
                    <p>${data.clientType} Client</p>
                    <span class="contract-value">LKR ${parseInt(data.contractValue).toLocaleString()}</span>
                </div>
            </div>
            <div class="contract-details">
                <div class="detail-row">
                    <i class="fas fa-calendar-alt detail-icon"></i>
                    <span class="detail-label">Start Date:</span>
                    <span class="detail-value">${formatDate(data.startDate)}</span>
                </div>
                <div class="detail-row">
                    <i class="fas fa-calendar-check detail-icon"></i>
                    <span class="detail-label">End Date:</span>
                    <span class="detail-value">${formatDate(data.endDate)}</span>
                </div>
                <div class="detail-row">
                    <i class="fas fa-chart-line detail-icon"></i>
                    <span class="detail-label">Progress:</span>
                    <div class="progress-container">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 0%"></div>
                        </div>
                        <span class="progress-text">0%</span>
                    </div>
                </div>
            </div>
            <div class="contract-description">
                <p>${data.projectDescription}</p>
            </div>
            <div class="card-actions">
                <button class="action-btn primary" title="View Details">
                    <i class="fas fa-eye"></i>
                    View Details
                </button>
                <button class="action-btn secondary" title="Edit Contract">
                    <i class="fas fa-edit"></i>
                    Edit
                </button>
                <button class="action-btn secondary" title="Send for Signature">
                    <i class="fas fa-paper-plane"></i>
                    Send
                </button>
            </div>
        </div>
    `;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

// ===================================
// SEND CONTRACT MODAL
// ===================================

function initializeSendContractModal() {
    const modal = document.getElementById('sendContractModal');
    const closeBtn = document.getElementById('sendModalClose');
    const cancelBtn = document.getElementById('sendCancelBtn');
    const submitBtn = document.getElementById('sendSubmitBtn');
    
    if (closeBtn) closeBtn.addEventListener('click', closeSendContractModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeSendContractModal);
    if (submitBtn) submitBtn.addEventListener('click', submitSendContract);
    
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeSendContractModal();
        });
    }
}

function openSendContractModal(contractData) {
    const modal = document.getElementById('sendContractModal');
    
    // Pre-fill email if available
    if (contractData && contractData.email) {
        document.getElementById('sendToEmail').value = contractData.email;
    }
    
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeSendContractModal() {
    const modal = document.getElementById('sendContractModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        document.getElementById('sendContractForm').reset();
    }
}

function submitSendContract() {
    const email = document.getElementById('sendToEmail').value;
    const subject = document.getElementById('sendSubject').value;
    const message = document.getElementById('sendMessage').value;
    
    if (!email || !subject || !message) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }
    
    const submitBtn = document.getElementById('sendSubmitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    
    // Simulate sending
    setTimeout(() => {
        showNotification(`Contract sent successfully to ${email}!`, 'success');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        closeSendContractModal();
    }, 2000);
}

// ===================================
// DELETE CONTRACT MODAL
// ===================================

function initializeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    const closeBtn = document.getElementById('deleteModalClose');
    const cancelBtn = document.getElementById('deleteCancelBtn');
    const confirmBtn = document.getElementById('deleteConfirmBtn');
    
    if (closeBtn) closeBtn.addEventListener('click', closeDeleteModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeDeleteModal);
    if (confirmBtn) confirmBtn.addEventListener('click', confirmDeleteContract);
    
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeDeleteModal();
        });
    }
}

let contractToDelete = null;

function openDeleteModal(contractCard) {
    contractToDelete = contractCard;
    const title = contractCard.querySelector('.contract-info h3')?.textContent || 'Contract';
    const modal = document.getElementById('deleteModal');
    document.getElementById('deleteContractInfo').textContent = title;
    
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        contractToDelete = null;
    }
}

function confirmDeleteContract() {
    if (!contractToDelete) return;
    
    const confirmBtn = document.getElementById('deleteConfirmBtn');
    const originalText = confirmBtn.innerHTML;
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
    
    setTimeout(() => {
        contractToDelete.style.transition = 'all 0.3s ease';
        contractToDelete.style.opacity = '0';
        contractToDelete.style.transform = 'scale(0.9)';
        
        setTimeout(() => {
            contractToDelete.remove();
            showNotification('Contract deleted successfully', 'success');
            closeDeleteModal();
        }, 300);
        
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = originalText;
    }, 1500);
}

// ===================================
// EXPORT MODAL (Enhanced)
// ===================================

function initializeExportModal() {
    const exportBtn = document.getElementById('exportBtn');
    const exportModal = document.getElementById('exportModal');
    const exportModalClose = document.getElementById('exportModalClose');
    const exportCancel = document.getElementById('exportCancel');
    const exportDownload = document.getElementById('exportDownload');
    const exportPreview = document.getElementById('exportPreview');
    
    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            exportModal.classList.add('active');
            updateExportSummary();
        });
    }
    
    if (exportModalClose) {
        exportModalClose.addEventListener('click', () => {
            exportModal.classList.remove('active');
        });
    }
    
    if (exportCancel) {
        exportCancel.addEventListener('click', () => {
            exportModal.classList.remove('active');
        });
    }
    
    if (exportModal) {
        exportModal.addEventListener('click', (e) => {
            if (e.target === exportModal) {
                exportModal.classList.remove('active');
            }
        });
    }
    
    initializeExportFilters();
    initializeDatePresets();
    if (exportPreview) exportPreview.addEventListener('click', showExportPreview);
    if (exportDownload) exportDownload.addEventListener('click', performExport);
}

function initializeExportFilters() {
    const allCheckbox = document.getElementById('exportAll');
    const statusCheckboxes = document.querySelectorAll('#exportActive, #exportPending, #exportCompleted, #exportCancelled, #exportExpired');
    const specialCheckboxes = document.querySelectorAll('#exportWithIssues, #exportHighValue, #exportRecentUpdates');
    
    if (allCheckbox) {
        allCheckbox.addEventListener('change', function() {
            if (this.checked) {
                [...statusCheckboxes, ...specialCheckboxes].forEach(cb => cb.checked = false);
            }
            updateExportSummary();
        });
    }
    
    [...statusCheckboxes, ...specialCheckboxes].forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked && allCheckbox) allCheckbox.checked = false;
            updateExportSummary();
        });
    });
    
    const startDate = document.getElementById('exportStartDate');
    const endDate = document.getElementById('exportEndDate');
    
    if (startDate) startDate.addEventListener('change', updateExportSummary);
    if (endDate) endDate.addEventListener('change', updateExportSummary);
}

function initializeDatePresets() {
    const presetButtons = document.querySelectorAll('.preset-btn');
    const startDateInput = document.getElementById('exportStartDate');
    const endDateInput = document.getElementById('exportEndDate');
    
    presetButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            presetButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const days = parseInt(this.dataset.preset);
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(endDate.getDate() - days);
            
            if (startDateInput) startDateInput.value = startDate.toISOString().split('T')[0];
            if (endDateInput) endDateInput.value = endDate.toISOString().split('T')[0];
            
            updateExportSummary();
        });
    });
}

function updateExportSummary() {
    const selectedFilters = getSelectedFilters();
    const estimatedCount = calculateEstimatedCount(selectedFilters);
    
    const summaryText = document.getElementById('exportSummaryText');
    const countElement = document.getElementById('estimatedCount');
    
    if (summaryText && countElement) {
        if (selectedFilters.all) {
            summaryText.textContent = 'Ready to export all contracts';
        } else if (selectedFilters.statuses.length > 0 || selectedFilters.special.length > 0) {
            const filterNames = [...selectedFilters.statuses, ...selectedFilters.special];
            summaryText.textContent = `Ready to export: ${filterNames.join(', ')}`;
        } else {
            summaryText.textContent = 'Select filters to export specific contracts';
        }
        
        countElement.textContent = `Estimated: ${estimatedCount} contracts`;
    }
}

function getSelectedFilters() {
    const filters = {
        all: document.getElementById('exportAll')?.checked || false,
        statuses: [],
        special: [],
        dateRange: {
            start: document.getElementById('exportStartDate')?.value || null,
            end: document.getElementById('exportEndDate')?.value || null
        },
        format: document.querySelector('input[name="exportFormat"]:checked')?.value || 'excel'
    };
    
    const statusMap = {
        'exportActive': 'Active',
        'exportPending': 'Pending',
        'exportCompleted': 'Completed',
        'exportCancelled': 'Cancelled',
        'exportExpired': 'Expired'
    };
    
    Object.keys(statusMap).forEach(id => {
        if (document.getElementById(id)?.checked) {
            filters.statuses.push(statusMap[id]);
        }
    });
    
    const specialMap = {
        'exportWithIssues': 'With Issues',
        'exportHighValue': 'High Value',
        'exportRecentUpdates': 'Recently Updated'
    };
    
    Object.keys(specialMap).forEach(id => {
        if (document.getElementById(id)?.checked) {
            filters.special.push(specialMap[id]);
        }
    });
    
    return filters;
}

function calculateEstimatedCount(filters) {
    const allContracts = document.querySelectorAll('.contract-card:not([style*="display: none"])');
    if (filters.all) return allContracts.length;
    
    let count = 0;
    allContracts.forEach(card => {
        const cardStatus = card.querySelector('.contract-status')?.textContent.trim();
        if (filters.statuses.length > 0 && filters.statuses.some(s => cardStatus?.includes(s))) {
            count++;
        }
    });
    
    return count || allContracts.length;
}

function showExportPreview() {
    const filters = getSelectedFilters();
    showNotification('Opening export preview...', 'info');
    
    setTimeout(() => {
        showNotification('Preview feature would open in a new window', 'success');
    }, 1000);
}

function performExport() {
    const filters = getSelectedFilters();
    const format = filters.format;
    const exportBtn = document.getElementById('exportDownload');
    const originalText = exportBtn.innerHTML;
    exportBtn.disabled = true;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
    
    setTimeout(() => {
        const timestamp = new Date().toISOString().split('T')[0];
        const filterSuffix = filters.all ? 'all' : 'filtered';
        const filename = `fixlanka-contracts-${filterSuffix}-${timestamp}`;
        
        if (format === 'excel' || format === 'csv') {
            downloadCSVFile(filename, filters);
        } else if (format === 'pdf') {
            downloadPDFFile(filename, filters);
        }
        
        exportBtn.disabled = false;
        exportBtn.innerHTML = originalText;
        document.getElementById('exportModal').classList.remove('active');
        showNotification('Export completed successfully!', 'success');
    }, 2000);
}

function downloadCSVFile(filename, filters) {
    const data = generateExportData(filters);
    const csvContent = convertToCSV(data);
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${filename}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
}

function downloadPDFFile(filename, filters) {
    const data = generateExportData(filters);
    const textContent = data.map(row => row.join(' | ')).join('\n');
    const blob = new Blob([textContent], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${filename}.txt`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
}

function generateExportData(filters) {
    const headers = ['Contract ID', 'Title', 'Client', 'Status', 'Value', 'Progress', 'Start Date', 'End Date'];
    const rows = [headers];
    
    const contracts = document.querySelectorAll('.contract-card:not([style*="display: none"])');
    contracts.forEach(card => {
        const data = extractContractData(card, rows.length);
        rows.push([
            data.contractId,
            data.title,
            data.clientName,
            data.status,
            data.value,
            `${data.progress}%`,
            data.startDate,
            data.endDate
        ]);
    });
    
    return rows;
}

function convertToCSV(data) {
    return data.map(row => 
        row.map(cell => `"${cell}"`).join(',')
    ).join('\n');
}

// ===================================
// SCROLL TO TOP
// ===================================

function initializeScrollToTop() {
    const scrollToTopBtn = document.getElementById('scrollToTop');
    const mainContent = document.querySelector('.main-content');
    
    if (!scrollToTopBtn || !mainContent) return;
    
    mainContent.addEventListener('scroll', function() {
        if (mainContent.scrollTop > 300) {
            scrollToTopBtn.classList.add('visible');
        } else {
            scrollToTopBtn.classList.remove('visible');
        }
    });
    
    scrollToTopBtn.addEventListener('click', function() {
        mainContent.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// ===================================
// NOTIFICATION SYSTEM
// ===================================

function showNotification(message, type = 'info') {
    let notification = document.getElementById('notification');
    
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 10000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        `;
        document.body.appendChild(notification);
    }
    
    const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#0abab5'
    };
    
    notification.style.backgroundColor = colors[type] || colors.info;
    notification.textContent = message;
    notification.style.transform = 'translateX(0)';
    
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
    }, 3000);
}

// ===================================
// CONTRACT NEGOTIATION & CHAT SYSTEM
// ===================================

let currentNegotiationContract = null;

// Initialize negotiation/chat features when DOM loads
(function() {
    const originalInit = initializeContractsPage;
    initializeContractsPage = function() {
        originalInit();
        initializeNegotiationSystem();
    };
})();

function initializeNegotiationSystem() {
    initializeNegotiationModal();
    initializeStatusUpdateModal();
    initializeRejectionModal();
    initializeChatInput();
}

// ===================================
// NEGOTIATION MODAL
// ===================================

function initializeNegotiationModal() {
    const modal = document.getElementById('negotiationModal');
    const closeBtn = document.getElementById('negotiationModalClose');
    const closeNegotiationBtn = document.getElementById('closeNegotiationBtn');
    
    // Quick action buttons
    const reviseContractBtn = document.getElementById('reviseContractBtn');
    const viewOriginalBtn = document.getElementById('viewOriginalBtn');
    const sendRevisedBtn = document.getElementById('sendRevisedBtn');
    const withdrawContractBtn = document.getElementById('withdrawContractBtn');
    
    if (closeBtn) closeBtn.addEventListener('click', closeNegotiationModal);
    if (closeNegotiationBtn) closeNegotiationBtn.addEventListener('click', closeNegotiationModal);
    
    // Edit contract button in banner
    const editContractFromBanner = document.getElementById('editContractFromBanner');
    if (editContractFromBanner) {
        editContractFromBanner.addEventListener('click', function() {
            if (currentNegotiationContract) {
                closeNegotiationModal();
                setTimeout(() => {
                    handleEditContract(currentNegotiationContract);
                }, 300);
                showNotification('Opening contract editor...', 'info');
            }
        });
    }
    
    if (reviseContractBtn) {
        reviseContractBtn.addEventListener('click', function() {
            if (currentNegotiationContract) {
                closeNegotiationModal();
                setTimeout(() => {
                    handleEditContract(currentNegotiationContract);
                }, 300);
                showNotification('Opening contract editor to make revisions...', 'info');
            }
        });
    }
    
    if (viewOriginalBtn) {
        viewOriginalBtn.addEventListener('click', function() {
            if (currentNegotiationContract) {
                showNotification('Opening original contract...', 'info');
                setTimeout(() => {
                    handleViewContract(currentNegotiationContract);
                }, 500);
            }
        });
    }
    
    if (sendRevisedBtn) {
        sendRevisedBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to send the revised contract to the customer?')) {
                sendRevisedContract();
            }
        });
    }
    
    if (withdrawContractBtn) {
        withdrawContractBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to withdraw this contract? This action cannot be undone.')) {
                withdrawContract();
            }
        });
    }
    
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeNegotiationModal();
        });
    }
}

function openNegotiationModal(contractData) {
    currentNegotiationContract = contractData;
    const modal = document.getElementById('negotiationModal');
    
    // Update modal header
    document.getElementById('chatContractTitle').textContent = contractData.title || 'Contract';
    
    // Update status badge
    const statusBadge = document.getElementById('chatContractStatus');
    updateStatusBadge(statusBadge, contractData.status || 'rejected');
    
    // Show/hide rejection banner based on status
    const rejectionBanner = document.getElementById('rejectionBanner');
    if (contractData.status === 'rejected' || contractData.status === 'negotiating') {
        rejectionBanner.style.display = 'flex';
        document.getElementById('rejectionReason').textContent = 
            contractData.rejectionReason || 'Reason: Budget concerns and timeline too tight';
        document.getElementById('rejectionDate').textContent = 
            contractData.rejectionDate || 'Rejected on: October 20, 2025 at 2:45 PM';
    } else {
        rejectionBanner.style.display = 'none';
    }
    
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Auto-focus chat input for better UX
        setTimeout(() => {
            const chatMessages = document.getElementById('chatMessages');
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
            
            const chatInput = document.getElementById('chatInput');
            if (chatInput) {
                chatInput.focus();
            }
        }, 300);
        
        // Add escape key listener
        document.addEventListener('keydown', handleEscapeKey);
    }
}

function handleEscapeKey(e) {
    if (e.key === 'Escape') {
        closeNegotiationModal();
    }
}

function closeNegotiationModal() {
    const modal = document.getElementById('negotiationModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        currentNegotiationContract = null;
        
        // Remove escape key listener
        document.removeEventListener('keydown', handleEscapeKey);
    }
}

function updateStatusBadge(badge, status) {
    badge.className = 'contract-status-badge ' + status;
    const statusInfo = {
        'rejected': { icon: 'times-circle', text: 'Rejected by Customer' },
        'negotiating': { icon: 'comments', text: 'Negotiating' },
        'under-review': { icon: 'eye', text: 'Under Review' },
        'sent': { icon: 'paper-plane', text: 'Sent to Customer' },
        'accepted': { icon: 'check-circle', text: 'Accepted' },
        'active': { icon: 'play-circle', text: 'Active' },
        'draft': { icon: 'file-alt', text: 'Draft' }
    };
    const info = statusInfo[status] || statusInfo['draft'];
    badge.innerHTML = `<i class="fas fa-${info.icon}"></i> ${info.text}`;
}

// ===================================
// CHAT INPUT FUNCTIONALITY
// ===================================

function initializeChatInput() {
    const chatInput = document.getElementById('chatInput');
    const sendMessageBtn = document.getElementById('sendMessageBtn');
    const attachFileBtn = document.getElementById('attachFileBtn');
    
    if (chatInput) {
        chatInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
        
        chatInput.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'Enter') sendChatMessage();
        });
    }
    
    if (sendMessageBtn) sendMessageBtn.addEventListener('click', sendChatMessage);
    if (attachFileBtn) {
        attachFileBtn.addEventListener('click', function() {
            showNotification('File attachment feature coming soon', 'info');
        });
    }
}

function sendChatMessage() {
    const chatInput = document.getElementById('chatInput');
    const sendBtn = document.getElementById('sendMessageBtn');
    const message = chatInput.value.trim();
    
    if (!message) {
        showNotification('Please type a message first', 'warning');
        chatInput.focus();
        return;
    }
    
    // Disable send button temporarily
    sendBtn.disabled = true;
    const originalContent = sendBtn.innerHTML;
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    // Add message with slight delay for better UX
    setTimeout(() => {
        addMessageToChat('company', message);
        chatInput.value = '';
        chatInput.style.height = 'auto';
        
        // Re-enable button
        sendBtn.disabled = false;
        sendBtn.innerHTML = originalContent;
        
        // Focus back to input
        chatInput.focus();
        
        showTypingIndicator();
        setTimeout(() => {
            hideTypingIndicator();
            simulateCustomerResponse();
        }, 2000 + Math.random() * 2000);
        
        showNotification('Message sent successfully', 'success');
    }, 300);
}

function addMessageToChat(sender, message) {
    const chatMessages = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    
    if (sender === 'company') {
        messageDiv.className = 'chat-message company-message';
        messageDiv.innerHTML = `
            <div class="message-avatar company">FL</div>
            <div class="message-content">
                <div class="message-header">
                    <span class="message-sender">FixLanka Team</span>
                    <span class="message-role">Company</span>
                </div>
                <p>${escapeHtml(message)}</p>
                <span class="message-time">${getCurrentTime()}</span>
            </div>
        `;
    } else {
        messageDiv.className = 'chat-message customer-message';
        messageDiv.innerHTML = `
            <div class="message-avatar">JD</div>
            <div class="message-content">
                <div class="message-header">
                    <span class="message-sender">John Doe</span>
                    <span class="message-role">Customer</span>
                </div>
                <p>${escapeHtml(message)}</p>
                <span class="message-time">${getCurrentTime()}</span>
            </div>
        `;
    }
    
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function showTypingIndicator() {
    const indicator = document.getElementById('typingIndicator');
    if (indicator) {
        indicator.style.display = 'flex';
        document.getElementById('chatMessages').scrollTop = document.getElementById('chatMessages').scrollHeight;
    }
}

function hideTypingIndicator() {
    const indicator = document.getElementById('typingIndicator');
    if (indicator) indicator.style.display = 'none';
}

function simulateCustomerResponse() {
    const responses = [
        "Thank you for your response. Let me discuss this with my team.",
        "That sounds reasonable. Can you send me the updated contract?",
        "I appreciate your flexibility. When can we finalize this?",
        "Could you provide more details about that?",
        "That works for us. Let's proceed with the revision."
    ];
    addMessageToChat('customer', responses[Math.floor(Math.random() * responses.length)]);
}

function getCurrentTime() {
    return new Date().toLocaleDateString('en-US', { 
        month: 'long', day: 'numeric', year: 'numeric',
        hour: 'numeric', minute: '2-digit', hour12: true
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ===================================
// STATUS UPDATE MODAL
// ===================================

function initializeStatusUpdateModal() {
    const modal = document.getElementById('statusUpdateModal');
    const closeBtn = document.getElementById('statusUpdateClose');
    const cancelBtn = document.getElementById('statusUpdateCancel');
    const submitBtn = document.getElementById('statusUpdateSubmit');
    
    if (closeBtn) closeBtn.addEventListener('click', closeStatusUpdateModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeStatusUpdateModal);
    if (submitBtn) submitBtn.addEventListener('click', submitStatusUpdate);
    
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeStatusUpdateModal();
        });
    }
}

function openStatusUpdateModal(contractData) {
    const modal = document.getElementById('statusUpdateModal');
    const currentBadge = document.getElementById('currentStatusBadge');
    currentBadge.textContent = (contractData.status || 'Pending').charAt(0).toUpperCase() + (contractData.status || 'Pending').slice(1);
    currentBadge.className = 'status-badge ' + (contractData.status || 'pending');
    currentNegotiationContract = contractData;
    
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeStatusUpdateModal() {
    const modal = document.getElementById('statusUpdateModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        document.getElementById('statusUpdateForm').reset();
    }
}

function submitStatusUpdate() {
    const newStatus = document.getElementById('newStatus').value;
    if (!newStatus) {
        showNotification('Please select a new status', 'error');
        return;
    }
    
    const submitBtn = document.getElementById('statusUpdateSubmit');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    
    setTimeout(() => {
        showNotification('Contract status updated successfully!', 'success');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        closeStatusUpdateModal();
        
        if (currentNegotiationContract) {
            updateContractCardStatus(currentNegotiationContract.id, newStatus);
        }
    }, 1500);
}

function updateContractCardStatus(contractId, newStatus) {
    const cards = document.querySelectorAll('.contract-card');
    cards.forEach(card => {
        const cardId = card.querySelector('.contract-id')?.textContent;
        if (cardId === contractId) {
            const statusBadge = card.querySelector('.contract-status');
            if (statusBadge) {
                updateStatusBadge(statusBadge, newStatus);
                card.setAttribute('data-status', newStatus);
            }
        }
    });
}

// ===================================
// REJECTION MODAL
// ===================================

function initializeRejectionModal() {
    const modal = document.getElementById('rejectionModal');
    const closeBtn = document.getElementById('rejectionModalClose');
    const cancelBtn = document.getElementById('rejectionCancel');
    const submitBtn = document.getElementById('rejectionSubmit');
    
    if (closeBtn) closeBtn.addEventListener('click', closeRejectionModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeRejectionModal);
    if (submitBtn) submitBtn.addEventListener('click', submitRejection);
    
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeRejectionModal();
        });
    }
}

function closeRejectionModal() {
    const modal = document.getElementById('rejectionModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        document.getElementById('rejectionForm').reset();
    }
}

function submitRejection() {
    const form = document.getElementById('rejectionForm');
    const reason = form.querySelector('input[name="rejectionReason"]:checked')?.value;
    const details = document.getElementById('rejectionDetails').value;
    const openNegotiation = document.getElementById('openNegotiation').checked;
    
    if (!reason || !details.trim()) {
        showNotification('Please select a reason and provide details', 'error');
        return;
    }
    
    const submitBtn = document.getElementById('rejectionSubmit');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    
    setTimeout(() => {
        const statusToSet = openNegotiation ? 'negotiating' : 'rejected';
        showNotification('Contract rejection submitted', 'success');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        closeRejectionModal();
        
        if (currentNegotiationContract) {
            updateContractCardStatus(currentNegotiationContract.id, statusToSet);
            if (openNegotiation) {
                setTimeout(() => {
                    openNegotiationModal({
                        ...currentNegotiationContract,
                        status: 'negotiating',
                        rejectionReason: 'Reason: ' + reason,
                        rejectionDate: getCurrentTime()
                    });
                }, 500);
            }
        }
    }, 2000);
}

// ===================================
// CONTRACT ACTION HELPERS
// ===================================

function sendRevisedContract() {
    const submitBtn = document.getElementById('sendRevisedBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    
    setTimeout(() => {
        showNotification('Revised contract sent to customer successfully!', 'success');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        
        if (currentNegotiationContract) {
            updateContractCardStatus(currentNegotiationContract.id, 'sent');
        }
        
        const chatMessages = document.getElementById('chatMessages');
        const systemMessage = document.createElement('div');
        systemMessage.className = 'chat-message system-message';
        systemMessage.innerHTML = `
            <div class="message-icon">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div class="message-content">
                <p><strong>Revised Contract Sent</strong></p>
                <p>You sent the updated contract to the customer for review.</p>
                <span class="message-time">${getCurrentTime()}</span>
            </div>
        `;
        chatMessages.appendChild(systemMessage);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }, 1500);
}

function withdrawContract() {
    if (!currentNegotiationContract) return;
    setTimeout(() => {
        showNotification('Contract withdrawn successfully', 'success');
        closeNegotiationModal();
        updateContractCardStatus(currentNegotiationContract.id, 'withdrawn');
    }, 1000);
}

// Enable opening chat for rejected/negotiating contracts
window.openNegotiationModal = openNegotiationModal;
window.openStatusUpdateModal = openStatusUpdateModal;
