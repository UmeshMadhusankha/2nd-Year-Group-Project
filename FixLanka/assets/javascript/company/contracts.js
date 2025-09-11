/* ================================================
   FixLanka Contracts Page JavaScript
   ================================================
   
   This file handles all interactive functionality
   for the contracts management page including:
   - Search and filtering
   - View switching (grid/list)
   - Modal dialogs
   - Status updates
   - Dynamic content loading
   ================================================ */

document.addEventListener('DOMContentLoaded', function() {
    initializeContractsPage();
});

function initializeContractsPage() {
    initializeSearch();
    initializeFilters();
    initializeViewSwitcher();
    initializeModals();
    initializeContractActions();
    initializeInfiniteScroll();
    initializeScrollToTop();
}

/* ===============================================
   SEARCH FUNCTIONALITY
   =============================================== */
function initializeSearch() {
    const searchInput = document.getElementById('contractSearch');
    
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            filterContracts(searchTerm);
        });
    }
}

function filterContracts(searchTerm) {
    const contractCards = document.querySelectorAll('.contract-card');
    let visibleCount = 0;
    
    contractCards.forEach(card => {
        const contractTitle = card.querySelector('.contract-info h3').textContent.toLowerCase();
        const clientName = card.querySelector('.client-name').textContent.toLowerCase();
        const isVisible = contractTitle.includes(searchTerm) || clientName.includes(searchTerm);
        
        if (isVisible) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    updateResultsCount(visibleCount);
}

function updateResultsCount(count) {
    const paginationInfo = document.querySelector('.pagination-info');
    if (paginationInfo) {
        const totalContracts = document.querySelectorAll('.contract-card').length;
        paginationInfo.textContent = `Showing ${count} of ${totalContracts} contracts`;
    }
}

/* ===============================================
   FILTER FUNCTIONALITY
   =============================================== */
function initializeFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const dateFilter = document.getElementById('dateFilter');
    const clearFilters = document.getElementById('clearFilters');
    
    if (statusFilter) {
        statusFilter.addEventListener('change', applyFilters);
    }
    
    if (typeFilter) {
        typeFilter.addEventListener('change', applyFilters);
    }
    
    if (dateFilter) {
        dateFilter.addEventListener('change', applyFilters);
    }
    
    if (clearFilters) {
        clearFilters.addEventListener('click', function() {
            document.getElementById('statusFilter').value = '';
            document.getElementById('typeFilter').value = '';
            document.getElementById('dateFilter').value = '';
            document.getElementById('contractSearch').value = '';
            applyFilters();
        });
    }
}

function applyFilters() {
    const statusFilter = document.getElementById('statusFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    const searchTerm = document.getElementById('contractSearch').value.toLowerCase();
    
    const contractCards = document.querySelectorAll('.contract-card');
    let visibleCount = 0;
    
    contractCards.forEach(card => {
        const cardStatus = card.getAttribute('data-status');
        const cardType = card.getAttribute('data-type');
        const contractTitle = card.querySelector('.contract-info h3').textContent.toLowerCase();
        const clientName = card.querySelector('.client-name').textContent.toLowerCase();
        
        const statusMatch = !statusFilter || cardStatus === statusFilter;
        const typeMatch = !typeFilter || cardType === typeFilter;
        const searchMatch = !searchTerm || contractTitle.includes(searchTerm) || clientName.includes(searchTerm);
        
        if (statusMatch && typeMatch && searchMatch) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    updateResultsCount(visibleCount);
}

/* ===============================================
   VIEW SWITCHER (GRID/LIST)
   =============================================== */
function initializeViewSwitcher() {
    const viewButtons = document.querySelectorAll('.view-btn');
    const contractsContainer = document.getElementById('contractsContainer');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const viewType = this.getAttribute('data-view');
            
            // Update active button
            viewButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Switch container class
            if (viewType === 'list') {
                contractsContainer.classList.add('list-view');
            } else {
                contractsContainer.classList.remove('list-view');
            }
            
            // Store preference
            localStorage.setItem('contractsViewPreference', viewType);
        });
    });
    
    // Load saved preference
    const savedView = localStorage.getItem('contractsViewPreference');
    if (savedView) {
        const targetButton = document.querySelector(`[data-view="${savedView}"]`);
        if (targetButton) {
            targetButton.click();
        }
    }
}

/* ===============================================
   MODAL FUNCTIONALITY
   =============================================== */
function initializeModals() {
    const modal = document.getElementById('contractModal');
    const closeModal = document.getElementById('closeModal');
    const modalCancel = document.getElementById('modalCancel');
    const modalClose = document.getElementById('modalClose');
    const modalEdit = document.getElementById('modalEdit');
    const modalDownload = document.getElementById('modalDownload');
    const modalPrint = document.getElementById('modalPrint');
    
    // Close modal handlers
    if (closeModal) {
        closeModal.addEventListener('click', closeContractModal);
    }
    
    if (modalCancel) {
        modalCancel.addEventListener('click', closeContractModal);
    }
    
    if (modalClose) {
        modalClose.addEventListener('click', closeContractModal);
    }
    
    // Action button handlers
    if (modalEdit) {
        modalEdit.addEventListener('click', function() {
            showNotification('Opening contract editor...', 'info');
            closeContractModal();
        });
    }
    
    if (modalDownload) {
        modalDownload.addEventListener('click', function() {
            showNotification('Downloading contract documents...', 'info');
        });
    }
    
    if (modalPrint) {
        modalPrint.addEventListener('click', function() {
            showNotification('Preparing contract for printing...', 'info');
            setTimeout(() => {
                window.print();
            }, 1000);
        });
    }
    
    // Close on overlay click
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeContractModal();
            }
        });
    }
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
            closeContractModal();
        }
    });
}

function openContractModal(contractData) {
    const modal = document.getElementById('contractModal');
    
    if (modal) {
        // Populate modal with contract data
        populateModalContent(contractData);
        
        // Show modal
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeContractModal() {
    const modal = document.getElementById('contractModal');
    
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function populateModalContent(contractData) {
    // Populate modal with comprehensive contract data
    document.getElementById('modalContractTitle').textContent = contractData.title;
    document.getElementById('modalContractId').textContent = contractData.id || 'CNT-2025-001';
    
    // Status badge with appropriate styling
    const statusBadge = document.getElementById('modalContractStatus');
    statusBadge.textContent = contractData.status || 'Active';
    statusBadge.className = `contract-status-badge ${contractData.status || 'active'}`;
    
    // Client Information
    document.getElementById('modalClientName').textContent = contractData.client || 'ABC Corporation';
    document.getElementById('modalContactPerson').textContent = contractData.contactPerson || 'John Doe';
    document.getElementById('modalClientEmail').textContent = contractData.email || 'john@abc.com';
    document.getElementById('modalClientPhone').textContent = contractData.phone || '+94 77 123 4567';
    
    // Project Details
    document.getElementById('modalDescription').textContent = contractData.description || 'This is a comprehensive project description that will provide detailed information about the scope of work, deliverables, and requirements.';
    document.getElementById('modalProjectType').textContent = contractData.type || 'Renovation';
    document.getElementById('modalLocation').textContent = contractData.location || 'Colombo, Sri Lanka';
    document.getElementById('modalStartDate').textContent = contractData.startDate || '2025-01-15';
    document.getElementById('modalEndDate').textContent = contractData.endDate || '2025-03-15';
    
    // Progress Information
    const progress = contractData.progress || 65;
    document.getElementById('modalProgressFill').style.width = progress + '%';
    document.getElementById('modalProgressText').textContent = progress + '% Complete';
    document.getElementById('modalProgressStage').textContent = contractData.stage || 'Design Phase';
    
    // Financial Details
    document.getElementById('modalContractValue').textContent = contractData.value || 'LKR 250,000';
    document.getElementById('modalPaidAmount').textContent = contractData.paidAmount || 'LKR 100,000';
    document.getElementById('modalRemainingAmount').textContent = contractData.remainingAmount || 'LKR 150,000';
    document.getElementById('modalPaymentTerms').textContent = contractData.paymentTerms || '30 Days';
    
    // Contract Information
    document.getElementById('modalContractType').textContent = contractData.contractType || 'Fixed Price';
    document.getElementById('modalDuration').textContent = contractData.duration || '60 Days';
    document.getElementById('modalPriority').textContent = contractData.priority || 'High';
    document.getElementById('modalTeam').textContent = contractData.team || 'Team Alpha';
    
    // Populate timeline if available
    if (contractData.timeline) {
        populateTimeline(contractData.timeline);
    }
    
    // Populate attachments if available
    if (contractData.attachments) {
        populateAttachments(contractData.attachments);
    }
}

function populateTimeline(timelineData) {
    const timelineContainer = document.getElementById('modalTimeline');
    timelineContainer.innerHTML = '';
    
    timelineData.forEach(item => {
        const timelineItem = document.createElement('div');
        timelineItem.className = 'timeline-item';
        timelineItem.innerHTML = `
            <div class="timeline-icon">
                <i class="fas fa-${item.icon}"></i>
            </div>
            <div class="timeline-content">
                <h4>${item.title}</h4>
                <p>${item.description}</p>
                <div class="timeline-date">${item.date}</div>
            </div>
        `;
        timelineContainer.appendChild(timelineItem);
    });
}

function populateAttachments(attachmentsData) {
    const attachmentsContainer = document.getElementById('modalAttachments');
    attachmentsContainer.innerHTML = '';
    
    attachmentsData.forEach(attachment => {
        const attachmentItem = document.createElement('div');
        attachmentItem.className = 'attachment-item';
        attachmentItem.innerHTML = `
            <div class="attachment-icon">
                <i class="fas fa-${attachment.icon}"></i>
            </div>
            <div class="attachment-info">
                <h5>${attachment.name}</h5>
                <p>${attachment.type} • ${attachment.size} • Last modified: ${attachment.date}</p>
            </div>
        `;
        attachmentItem.addEventListener('click', () => {
            showNotification(`Downloading ${attachment.name}...`, 'info');
        });
        attachmentsContainer.appendChild(attachmentItem);
    });
}

/* ===============================================
   CONTRACT ACTION HANDLERS
   =============================================== */
function initializeContractActions() {
    // Contract card click handler
    document.addEventListener('click', function(e) {
        // Check if click is on a contract card but not on an action button
        const contractCard = e.target.closest('.contract-card');
        const actionButton = e.target.closest('.action-btn');
        
        if (contractCard && !actionButton) {
            handleViewContract(contractCard);
            return;
        }
        
        // Handle individual action buttons
        if (e.target.closest('.action-btn.view')) {
            handleViewContract(e.target.closest('.contract-card'));
        }
        
        if (e.target.closest('.action-btn.edit')) {
            handleEditContract(e.target.closest('.contract-card'));
        }
        
        if (e.target.closest('.action-btn.download')) {
            handleDownloadContract(e.target.closest('.contract-card'));
        }
        
        if (e.target.closest('.action-btn.send')) {
            handleSendContract(e.target.closest('.contract-card'));
        }
        
        if (e.target.closest('.action-btn.invoice')) {
            handleGenerateInvoice(e.target.closest('.contract-card'));
        }
    });
    
    // Create new contract button
    const createBtn = document.getElementById('createContractBtn');
    if (createBtn) {
        createBtn.addEventListener('click', handleCreateContract);
    }
    
    // Export button
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', handleExportContracts);
    }
}

function handleViewContract(contractCard) {
    const contractTitle = contractCard.querySelector('.contract-info h3').textContent;
    const clientName = contractCard.querySelector('.client-name').textContent.replace(/^\s*\S+\s*/, ''); // Remove icon
    const statusElement = contractCard.querySelector('.contract-status');
    const status = statusElement ? statusElement.textContent.trim() : 'Active';
    const progressElement = contractCard.querySelector('.progress-fill');
    const progress = progressElement ? parseInt(progressElement.style.width) || 0 : 0;
    
    // Extract contract value if available
    const valueElement = contractCard.querySelector('.detail-row .detail-value');
    const value = valueElement ? valueElement.textContent : 'LKR 250,000';
    
    // Create comprehensive contract data object
    const contractData = {
        id: generateContractId(contractTitle),
        title: contractTitle,
        client: clientName,
        status: status.toLowerCase(),
        progress: progress,
        value: value,
        
        // Additional data that would typically come from a database
        contactPerson: getContactPerson(clientName),
        email: getClientEmail(clientName),
        phone: getClientPhone(clientName),
        description: getContractDescription(contractTitle),
        type: getContractType(contractCard),
        location: getProjectLocation(clientName),
        startDate: getStartDate(),
        endDate: getEndDate(),
        stage: getProgressStage(progress),
        paidAmount: getPaidAmount(value),
        remainingAmount: getRemainingAmount(value),
        paymentTerms: '30 Days',
        contractType: 'Fixed Price',
        duration: getDuration(),
        priority: getPriority(status),
        team: getAssignedTeam(contractTitle),
        timeline: getProjectTimeline(contractTitle),
        attachments: getContractAttachments(contractTitle)
    };
    
    openContractModal(contractData);
}

// Helper functions to generate realistic contract data
function generateContractId(title) {
    const hash = title.split('').reduce((a, b) => {
        a = ((a << 5) - a) + b.charCodeAt(0);
        return a & a;
    }, 0);
    return `CNT-2025-${Math.abs(hash).toString().slice(0, 3)}`;
}

function getContactPerson(clientName) {
    const contacts = {
        'Johnson Residence': 'Michael Johnson',
        'ABC Corporation': 'Sarah Wilson',
        'Downtown Mall': 'Robert Chen',
        'Modern Apartments': 'Lisa Anderson',
        'Tech Solutions Ltd': 'David Kumar',
        'Silva Family': 'Carlos Silva'
    };
    return contacts[clientName] || 'John Doe';
}

function getClientEmail(clientName) {
    const emails = {
        'Johnson Residence': 'michael@johnson-family.com',
        'ABC Corporation': 'sarah.wilson@abc-corp.com',
        'Downtown Mall': 'robert.chen@downtown-mall.lk',
        'Modern Apartments': 'lisa@modernapts.com',
        'Tech Solutions Ltd': 'david.kumar@techsolutions.lk',
        'Silva Family': 'carlos@silva-family.com'
    };
    return emails[clientName] || 'contact@client.com';
}

function getClientPhone(clientName) {
    const phones = ['+94 77 123 4567', '+94 76 987 6543', '+94 75 555 0123', '+94 78 456 7890'];
    return phones[Math.floor(Math.random() * phones.length)];
}

function getContractDescription(title) {
    const descriptions = {
        'Kitchen Renovation': 'Complete kitchen renovation including cabinet installation, countertop replacement, electrical work, and plumbing upgrades. Modern design with energy-efficient appliances.',
        'HVAC Maintenance Agreement': 'Comprehensive HVAC system maintenance contract including quarterly inspections, filter replacements, system cleaning, and emergency repair services.',
        'Plumbing System Overhaul': 'Complete plumbing system renovation including pipe replacement, fixture upgrades, water pressure optimization, and drainage system improvements.',
        'Electrical System Installation': 'New electrical system installation with modern wiring, circuit breaker upgrades, outlet installations, and safety compliance verification.',
        'Annual Maintenance Contract': 'Year-long maintenance agreement covering all building systems including HVAC, electrical, plumbing, and general facility maintenance services.'
    };
    return descriptions[title] || 'Comprehensive project covering all aspects of the contracted work with detailed specifications and quality assurance measures.';
}

function getContractType(contractCard) {
    const types = ['Renovation', 'Maintenance', 'Installation', 'Repair', 'Construction'];
    const dataType = contractCard.getAttribute('data-type');
    return dataType ? dataType.charAt(0).toUpperCase() + dataType.slice(1) : types[Math.floor(Math.random() * types.length)];
}

function getProjectLocation(clientName) {
    const locations = {
        'Johnson Residence': 'Nugegoda, Colombo',
        'ABC Corporation': 'Colombo 03, Sri Lanka',
        'Downtown Mall': 'Kandy, Sri Lanka',
        'Modern Apartments': 'Mount Lavinia, Colombo',
        'Tech Solutions Ltd': 'Maharagama, Colombo',
        'Silva Family': 'Galle, Sri Lanka'
    };
    return locations[clientName] || 'Colombo, Sri Lanka';
}

function getStartDate() {
    const dates = ['2025-01-15', '2025-02-01', '2025-01-10', '2025-02-15', '2025-01-20'];
    return dates[Math.floor(Math.random() * dates.length)];
}

function getEndDate() {
    const dates = ['2025-03-15', '2025-04-01', '2025-03-10', '2025-04-15', '2025-03-20'];
    return dates[Math.floor(Math.random() * dates.length)];
}

function getProgressStage(progress) {
    if (progress < 25) return 'Planning Phase';
    if (progress < 50) return 'Design Phase';
    if (progress < 75) return 'Implementation Phase';
    if (progress < 90) return 'Testing Phase';
    return 'Final Review';
}

function getPaidAmount(value) {
    const numValue = parseInt(value.replace(/[^\d]/g, ''));
    const paidPercentage = Math.floor(Math.random() * 70) + 20; // 20-90%
    const paidAmount = Math.floor(numValue * paidPercentage / 100);
    return `LKR ${paidAmount.toLocaleString()}`;
}

function getRemainingAmount(value) {
    const numValue = parseInt(value.replace(/[^\d]/g, ''));
    const paidAmount = parseInt(getPaidAmount(value).replace(/[^\d]/g, ''));
    const remaining = numValue - paidAmount;
    return `LKR ${remaining.toLocaleString()}`;
}

function getDuration() {
    const durations = ['30 Days', '45 Days', '60 Days', '90 Days', '120 Days'];
    return durations[Math.floor(Math.random() * durations.length)];
}

function getPriority(status) {
    const priorities = {
        'active': 'High',
        'pending': 'Medium',
        'completed': 'Low'
    };
    return priorities[status] || 'Medium';
}

function getAssignedTeam(title) {
    const teams = ['Team Alpha', 'Team Beta', 'Team Gamma', 'Team Delta', 'Team Sigma'];
    return teams[Math.floor(Math.random() * teams.length)];
}

function getProjectTimeline(title) {
    return [
        {
            icon: 'play',
            title: 'Project Started',
            description: 'Contract signed and project officially commenced',
            date: 'January 15, 2025'
        },
        {
            icon: 'cog',
            title: 'Planning Phase',
            description: 'Detailed planning and resource allocation completed',
            date: 'January 20, 2025'
        },
        {
            icon: 'tools',
            title: 'Implementation',
            description: 'Active work phase with regular progress updates',
            date: 'February 1, 2025'
        }
    ];
}

function getContractAttachments(title) {
    return [
        {
            name: 'Main Contract Agreement',
            type: 'PDF',
            size: '2.4 MB',
            date: 'Jan 15, 2025',
            icon: 'file-pdf'
        },
        {
            name: 'Project Blueprints',
            type: 'Images',
            size: '15.2 MB',
            date: 'Jan 10, 2025',
            icon: 'file-image'
        },
        {
            name: 'Technical Specifications',
            type: 'DOCX',
            size: '890 KB',
            date: 'Jan 12, 2025',
            icon: 'file-word'
        }
    ];
}

function handleEditContract(contractCard) {
    const contractTitle = contractCard.querySelector('.contract-info h3').textContent;
    
    showNotification(`Opening ${contractTitle} for editing...`, 'info');
    
    // Here you would typically redirect to an edit page or open an edit modal
    setTimeout(() => {
        showNotification('Edit functionality would be implemented here', 'success');
    }, 1000);
}

function handleDownloadContract(contractCard) {
    const contractTitle = contractCard.querySelector('.contract-info h3').textContent;
    
    showNotification(`Downloading ${contractTitle}...`, 'info');
    
    // Simulate download
    setTimeout(() => {
        showNotification('Contract downloaded successfully', 'success');
    }, 1500);
}

function handleSendContract(contractCard) {
    const contractTitle = contractCard.querySelector('.contract-info h3').textContent;
    const clientName = contractCard.querySelector('.client-name').textContent;
    
    showNotification(`Sending ${contractTitle} to ${clientName}...`, 'info');
    
    // Simulate sending
    setTimeout(() => {
        showNotification('Contract sent successfully', 'success');
    }, 2000);
}

function handleGenerateInvoice(contractCard) {
    const contractTitle = contractCard.querySelector('.contract-info h3').textContent;
    
    showNotification(`Generating invoice for ${contractTitle}...`, 'info');
    
    // Simulate invoice generation
    setTimeout(() => {
        showNotification('Invoice generated successfully', 'success');
    }, 1500);
}

function handleCreateContract() {
    showNotification('Opening new contract form...', 'info');
    
    // Here you would typically open a form modal or redirect to a form page
    setTimeout(() => {
        showNotification('New contract form would open here', 'success');
    }, 1000);
}

function handleExportContracts() {
    const visibleContracts = document.querySelectorAll('.contract-card[style*="block"], .contract-card:not([style*="none"])');
    
    showNotification(`Exporting ${visibleContracts.length} contracts...`, 'info');
    
    // Simulate export
    setTimeout(() => {
        showNotification('Contracts exported successfully', 'success');
    }, 2000);
}

/* ===============================================
   INFINITE SCROLL FUNCTIONALITY
   =============================================== */
let currentPage = 1;
let isLoading = false;
let hasMoreContracts = true;
const contractsPerPage = 6;

function initializeInfiniteScroll() {
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const scrollLoading = document.getElementById('scrollLoading');
    const contractsEnd = document.getElementById('contractsEnd');
    const mainContent = document.querySelector('.main-content');
    
    // Load More Button Click
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            loadMoreContracts();
        });
    }
    
    // Infinite Scroll Detection
    if (mainContent) {
        mainContent.addEventListener('scroll', function() {
            const { scrollTop, scrollHeight, clientHeight } = mainContent;
            
            if (scrollTop + clientHeight >= scrollHeight - 100 && !isLoading && hasMoreContracts) {
                loadMoreContracts(true); // true for auto-load (infinite scroll)
            }
        });
    }
}

function loadMoreContracts(autoLoad = false) {
    if (isLoading || !hasMoreContracts) return;
    
    isLoading = true;
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const scrollLoading = document.getElementById('scrollLoading');
    const contractsEnd = document.getElementById('contractsEnd');
    
    // Show loading state
    if (autoLoad) {
        scrollLoading.classList.add('visible');
    } else {
        loadMoreBtn.classList.add('loading');
        loadMoreBtn.innerHTML = '<i class="fas fa-spinner"></i> Loading...';
    }
    
    // Simulate API call delay
    setTimeout(() => {
        currentPage++;
        
        // Simulate loading more contracts (replace with actual API call)
        const newContracts = generateContractCards(contractsPerPage);
        
        if (newContracts.length > 0) {
            appendContractsToContainer(newContracts);
            
            // Check if we've reached the end (simulate with max 4 pages)
            if (currentPage >= 4) {
                hasMoreContracts = false;
                if (loadMoreBtn) loadMoreBtn.style.display = 'none';
                if (contractsEnd) contractsEnd.style.display = 'block';
            }
        } else {
            hasMoreContracts = false;
            if (loadMoreBtn) loadMoreBtn.style.display = 'none';
            if (contractsEnd) contractsEnd.style.display = 'block';
        }
        
        // Hide loading state
        isLoading = false;
        if (scrollLoading) scrollLoading.classList.remove('visible');
        if (loadMoreBtn) {
            loadMoreBtn.classList.remove('loading');
            loadMoreBtn.innerHTML = '<i class="fas fa-plus"></i> Load More Contracts';
        }
        
        // Animate new cards
        animateNewCards();
        
    }, 1500); // Simulate network delay
}

function generateContractCards(count) {
    // This is a simulation - replace with actual API data
    const contractTypes = ['maintenance', 'repair', 'installation', 'renovation'];
    const statuses = ['active', 'pending', 'completed'];
    const clients = [
        { name: 'Tech Solutions Ltd', icon: 'fas fa-industry' },
        { name: 'Modern Apartments', icon: 'fas fa-building' },
        { name: 'Green Valley Resort', icon: 'fas fa-hotel' },
        { name: 'City Mall', icon: 'fas fa-shopping-center' },
        { name: 'Johnson Residence', icon: 'fas fa-home' }
    ];
    
    const contracts = [];
    
    for (let i = 0; i < count; i++) {
        const contract = {
            id: `contract-${currentPage}-${i}`,
            title: `Contract ${currentPage * count + i + 1} - ${contractTypes[Math.floor(Math.random() * contractTypes.length)].charAt(0).toUpperCase() + contractTypes[Math.floor(Math.random() * contractTypes.length)].slice(1)}`,
            client: clients[Math.floor(Math.random() * clients.length)],
            status: statuses[Math.floor(Math.random() * statuses.length)],
            type: contractTypes[Math.floor(Math.random() * contractTypes.length)],
            value: Math.floor(Math.random() * 300000) + 50000,
            progress: Math.floor(Math.random() * 100)
        };
        contracts.push(contract);
    }
    
    return contracts;
}

function appendContractsToContainer(contracts) {
    const container = document.getElementById('contractsContainer');
    if (!container) return;
    
    contracts.forEach(contract => {
        const cardHTML = createContractCardHTML(contract);
        container.insertAdjacentHTML('beforeend', cardHTML);
    });
}

function createContractCardHTML(contract) {
    return `
        <div class="contract-card" data-status="${contract.status}" data-type="${contract.type}">
            <div class="contract-header">
                <div class="contract-info">
                    <h3>${contract.title}</h3>
                    <p class="client-name">
                        <i class="${contract.client.icon}"></i>
                        ${contract.client.name}
                    </p>
                </div>
                <div class="contract-status ${contract.status}">
                    <i class="fas fa-${getStatusIcon(contract.status)}"></i>
                    ${contract.status.charAt(0).toUpperCase() + contract.status.slice(1)}
                </div>
            </div>
            <div class="contract-details">
                <div class="detail-row">
                    <span class="detail-label">Contract Value:</span>
                    <span class="detail-value">LKR ${contract.value.toLocaleString()}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Progress:</span>
                    <div class="progress-container">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${contract.progress}%"></div>
                        </div>
                        <span class="progress-text">${contract.progress}%</span>
                    </div>
                </div>
            </div>
            <div class="contract-actions">
                <button class="action-btn view" title="View Details">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="action-btn edit" title="Edit Contract">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="action-btn download" title="Download Contract">
                    <i class="fas fa-download"></i>
                </button>
            </div>
        </div>
    `;
}

function getStatusIcon(status) {
    const icons = {
        active: 'play-circle',
        pending: 'clock',
        completed: 'check-circle'
    };
    return icons[status] || 'circle';
}

function animateNewCards() {
    const allCards = document.querySelectorAll('.contract-card');
    const newCards = Array.from(allCards).slice(-contractsPerPage);
    
    newCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

/* ===============================================
   SCROLL TO TOP FUNCTIONALITY
   =============================================== */
function initializeScrollToTop() {
    const scrollToTopBtn = document.getElementById('scrollToTop');
    const mainContent = document.querySelector('.main-content');
    
    if (!scrollToTopBtn || !mainContent) return;
    
    // Show/hide scroll to top button
    mainContent.addEventListener('scroll', function() {
        if (mainContent.scrollTop > 300) {
            scrollToTopBtn.classList.add('visible');
        } else {
            scrollToTopBtn.classList.remove('visible');
        }
    });
    
    // Scroll to top functionality
    scrollToTopBtn.addEventListener('click', function() {
        mainContent.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

/* ===============================================
   UTILITY FUNCTIONS
   =============================================== */
function showNotification(message, type = 'info') {
    // Create notification element if it doesn't exist
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
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        document.body.appendChild(notification);
    }
    
    // Set notification style based on type
    const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#0abab5'
    };
    
    notification.style.backgroundColor = colors[type] || colors.info;
    notification.textContent = message;
    
    // Show notification
    notification.style.transform = 'translateX(0)';
    
    // Hide after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
    }, 3000);
}

/* ===============================================
   CONTRACT CARD ANIMATIONS
   =============================================== */
function animateContractCards() {
    const contractCards = document.querySelectorAll('.contract-card');
    
    contractCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

/* ===============================================
   REAL-TIME UPDATES SIMULATION
   =============================================== */
function simulateRealTimeUpdates() {
    // Simulate periodic updates to contract data
    setInterval(() => {
        const activeContracts = document.querySelectorAll('[data-status="active"] .progress-fill');
        
        activeContracts.forEach(progressBar => {
            const currentWidth = parseInt(progressBar.style.width) || 0;
            const newWidth = Math.min(currentWidth + Math.random() * 2, 100);
            
            progressBar.style.width = `${newWidth}%`;
            
            const progressText = progressBar.parentElement.nextElementSibling;
            if (progressText) {
                progressText.textContent = `${Math.round(newWidth)}%`;
            }
        });
    }, 30000); // Update every 30 seconds
}

// Initialize real-time updates
setTimeout(simulateRealTimeUpdates, 5000);

// Initialize card animations on load
setTimeout(animateContractCards, 500);

/* ===============================================
   EXPORT FUNCTIONALITY
   =============================================== */
function initializeExportModal() {
    const exportBtn = document.getElementById('exportBtn');
    const exportModal = document.getElementById('exportModal');
    const exportModalClose = document.getElementById('exportModalClose');
    const exportCancel = document.getElementById('exportCancel');
    const exportDownload = document.getElementById('exportDownload');
    const exportPreview = document.getElementById('exportPreview');
    
    // Modal controls
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
    
    // Close modal when clicking overlay
    if (exportModal) {
        exportModal.addEventListener('click', (e) => {
            if (e.target === exportModal) {
                exportModal.classList.remove('active');
            }
        });
    }
    
    // Initialize export filters
    initializeExportFilters();
    initializeDatePresets();
    
    // Export actions
    if (exportPreview) {
        exportPreview.addEventListener('click', showExportPreview);
    }
    
    if (exportDownload) {
        exportDownload.addEventListener('click', performExport);
    }
}

function initializeExportFilters() {
    const allCheckbox = document.getElementById('exportAll');
    const statusCheckboxes = document.querySelectorAll('#exportActive, #exportPending, #exportCompleted, #exportCancelled, #exportExpired');
    const specialCheckboxes = document.querySelectorAll('#exportWithIssues, #exportHighValue, #exportRecentUpdates');
    
    // Handle "All Contracts" checkbox
    if (allCheckbox) {
        allCheckbox.addEventListener('change', function() {
            if (this.checked) {
                // Uncheck all specific filters
                [...statusCheckboxes, ...specialCheckboxes].forEach(cb => {
                    cb.checked = false;
                });
            }
            updateExportSummary();
        });
    }
    
    // Handle specific status checkboxes
    [...statusCheckboxes, ...specialCheckboxes].forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked && allCheckbox) {
                allCheckbox.checked = false;
            }
            updateExportSummary();
        });
    });
    
    // Handle date inputs
    const startDate = document.getElementById('exportStartDate');
    const endDate = document.getElementById('exportEndDate');
    
    if (startDate) {
        startDate.addEventListener('change', updateExportSummary);
    }
    
    if (endDate) {
        endDate.addEventListener('change', updateExportSummary);
    }
}

function initializeDatePresets() {
    const presetButtons = document.querySelectorAll('.preset-btn');
    const startDateInput = document.getElementById('exportStartDate');
    const endDateInput = document.getElementById('exportEndDate');
    
    presetButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            presetButtons.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const days = parseInt(this.dataset.preset);
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(endDate.getDate() - days);
            
            if (startDateInput) {
                startDateInput.value = startDate.toISOString().split('T')[0];
            }
            if (endDateInput) {
                endDateInput.value = endDate.toISOString().split('T')[0];
            }
            
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
    
    // Get selected status filters
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
    
    // Get selected special filters
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
    // Get all contract cards for estimation
    const allContracts = document.querySelectorAll('.contract-card');
    let estimatedCount = 0;
    
    if (filters.all) {
        estimatedCount = allContracts.length;
    } else {
        allContracts.forEach(card => {
            let matches = false;
            
            // Check status filters
            if (filters.statuses.length > 0) {
                const cardStatus = card.querySelector('.contract-status');
                if (cardStatus) {
                    const statusText = cardStatus.textContent.trim().toLowerCase();
                    matches = filters.statuses.some(status => 
                        statusText.includes(status.toLowerCase())
                    );
                }
            }
            
            // Check special filters
            if (filters.special.length > 0) {
                filters.special.forEach(special => {
                    if (special === 'With Issues') {
                        // Simulate contracts with issues (random for demo)
                        if (Math.random() > 0.7) matches = true;
                    } else if (special === 'High Value') {
                        // Simulate high value contracts
                        if (Math.random() > 0.6) matches = true;
                    } else if (special === 'Recently Updated') {
                        // Simulate recently updated contracts
                        if (Math.random() > 0.5) matches = true;
                    }
                });
            }
            
            if (matches) estimatedCount++;
        });
    }
    
    return estimatedCount;
}

function showExportPreview() {
    const filters = getSelectedFilters();
    
    // Create a simple preview table
    const previewWindow = window.open('', '_blank', 'width=800,height=600');
    previewWindow.document.write(`
        <html>
            <head>
                <title>Export Preview - FixLanka Contracts</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                    th { background-color: #0abab5; color: white; }
                    tr:nth-child(even) { background-color: #f9f9f9; }
                    .header { color: #0abab5; border-bottom: 2px solid #0abab5; padding-bottom: 10px; }
                    .filters { background: #f0f9ff; padding: 15px; border-radius: 8px; margin: 10px 0; }
                </style>
            </head>
            <body>
                <h1 class="header">FixLanka Contracts Export Preview</h1>
                <div class="filters">
                    <strong>Export Format:</strong> ${filters.format.toUpperCase()}<br>
                    <strong>Filters Applied:</strong> ${filters.all ? 'All Contracts' : [...filters.statuses, ...filters.special].join(', ') || 'None'}<br>
                    ${filters.dateRange.start ? `<strong>Date Range:</strong> ${filters.dateRange.start} to ${filters.dateRange.end || 'Present'}` : ''}
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Contract ID</th>
                            <th>Project Title</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Value</th>
                            <th>Progress</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${generatePreviewData(filters)}
                    </tbody>
                </table>
                <div style="margin-top: 20px; text-align: center;">
                    <button onclick="window.print()" style="background: #0abab5; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Print Preview</button>
                    <button onclick="window.close()" style="background: #6b7280; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-left: 10px;">Close</button>
                </div>
            </body>
        </html>
    `);
    previewWindow.document.close();
}

function generatePreviewData(filters) {
    // Generate sample data based on filters
    const sampleData = [
        { id: 'CNT-001', title: 'Smart Home Installation', client: 'John Smith', status: 'Active', value: '$2,500', progress: '75%', start: '2024-01-15', end: '2024-03-15' },
        { id: 'CNT-002', title: 'Office Network Setup', client: 'TechCorp Ltd', status: 'Pending', value: '$5,200', progress: '25%', start: '2024-02-01', end: '2024-04-01' },
        { id: 'CNT-003', title: 'Server Maintenance', client: 'DataFlow Inc', status: 'Completed', value: '$1,800', progress: '100%', start: '2023-12-01', end: '2024-01-01' },
        { id: 'CNT-004', title: 'Security System Upgrade', client: 'SafeGuard Co', status: 'Active', value: '$3,400', progress: '60%', start: '2024-01-20', end: '2024-03-20' },
        { id: 'CNT-005', title: 'Mobile App Development', client: 'StartupXYZ', status: 'Cancelled', value: '$8,000', progress: '30%', start: '2023-11-15', end: '2024-02-15' }
    ];
    
    let filteredData = sampleData;
    
    if (!filters.all) {
        filteredData = sampleData.filter(item => {
            if (filters.statuses.length > 0) {
                return filters.statuses.some(status => 
                    item.status.toLowerCase().includes(status.toLowerCase())
                );
            }
            return true;
        });
    }
    
    return filteredData.map(item => `
        <tr>
            <td>${item.id}</td>
            <td>${item.title}</td>
            <td>${item.client}</td>
            <td><span style="padding: 4px 8px; border-radius: 4px; background: ${getStatusColor(item.status)}; color: white; font-size: 12px;">${item.status}</span></td>
            <td>${item.value}</td>
            <td>${item.progress}</td>
            <td>${item.start}</td>
            <td>${item.end}</td>
        </tr>
    `).join('');
}

function getStatusColor(status) {
    const colors = {
        'Active': '#10b981',
        'Pending': '#f59e0b',
        'Completed': '#3b82f6',
        'Cancelled': '#ef4444',
        'Expired': '#6b7280'
    };
    return colors[status] || '#6b7280';
}

function performExport() {
    const filters = getSelectedFilters();
    const format = filters.format;
    
    // Show loading state
    const exportBtn = document.getElementById('exportDownload');
    const originalText = exportBtn.innerHTML;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
    exportBtn.disabled = true;
    
    // Simulate export process
    setTimeout(() => {
        // Generate filename
        const timestamp = new Date().toISOString().split('T')[0];
        const filterSuffix = filters.all ? 'all' : 'filtered';
        const filename = `fixlanka-contracts-${filterSuffix}-${timestamp}.${format}`;
        
        // Create and download file based on format
        if (format === 'excel') {
            downloadExcelFile(filename, filters);
        } else if (format === 'pdf') {
            downloadPDFFile(filename, filters);
        } else if (format === 'csv') {
            downloadCSVFile(filename, filters);
        }
        
        // Reset button
        exportBtn.innerHTML = originalText;
        exportBtn.disabled = false;
        
        // Close modal
        document.getElementById('exportModal').classList.remove('active');
        
        // Show success notification
        showNotification('Export completed successfully!', 'success');
    }, 2000);
}

function downloadExcelFile(filename, filters) {
    // Simulate Excel file download
    const data = generateExportData(filters);
    const csvContent = convertToCSV(data);
    downloadFile(csvContent, filename.replace('.excel', '.xlsx'), 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
}

function downloadPDFFile(filename, filters) {
    // Simulate PDF file download
    const data = generateExportData(filters);
    const pdfContent = generatePDFContent(data, filters);
    downloadFile(pdfContent, filename, 'application/pdf');
}

function downloadCSVFile(filename, filters) {
    const data = generateExportData(filters);
    const csvContent = convertToCSV(data);
    downloadFile(csvContent, filename, 'text/csv');
}

function generateExportData(filters) {
    // This would typically fetch real data from the server
    return [
        ['Contract ID', 'Project Title', 'Client Name', 'Status', 'Contract Value', 'Progress %', 'Start Date', 'End Date', 'Issues'],
        ['CNT-001', 'Smart Home Installation', 'John Smith', 'Active', '$2,500', '75', '2024-01-15', '2024-03-15', 'None'],
        ['CNT-002', 'Office Network Setup', 'TechCorp Ltd', 'Pending', '$5,200', '25', '2024-02-01', '2024-04-01', 'Awaiting Documents'],
        ['CNT-003', 'Server Maintenance', 'DataFlow Inc', 'Completed', '$1,800', '100', '2023-12-01', '2024-01-01', 'None'],
        ['CNT-004', 'Security System Upgrade', 'SafeGuard Co', 'Active', '$3,400', '60', '2024-01-20', '2024-03-20', 'Signature Required'],
        ['CNT-005', 'Mobile App Development', 'StartupXYZ', 'Cancelled', '$8,000', '30', '2023-11-15', '2024-02-15', 'Client Cancellation']
    ];
}

function convertToCSV(data) {
    return data.map(row => 
        row.map(cell => `"${cell}"`).join(',')
    ).join('\n');
}

function generatePDFContent(data, filters) {
    // This would generate actual PDF content in a real implementation
    return `PDF content for contracts export with filters: ${JSON.stringify(filters)}`;
}

function downloadFile(content, filename, mimeType) {
    const blob = new Blob([content], { type: mimeType });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
}

// Add export initialization to the main initialization function
document.addEventListener('DOMContentLoaded', function() {
    initializeContractsPage();
    initializeExportModal(); // Add this line
});
