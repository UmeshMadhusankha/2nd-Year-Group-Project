/**
 * Invoice Management System
 * Create, view, and manage invoices for contracts
 * 
 * @package FixLanka
 * @version 1.0.0
 */

/**
 * Initialize invoice management for a contract
 * @param {string} contractId - Contract ID
 */
async function initializeInvoices(contractId) {
    try {
        const response = await fetch('api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'get_invoices',
                contract_id: contractId
            })
        });

        const result = await response.json();
        
        if (result.success) {
            displayInvoices(result.invoices, contractId);
        } else {
            throw new Error(result.message || 'Failed to load invoices');
        }
    } catch (error) {
        console.error('Invoice load error:', error);
        showToast('error', 'Load Failed', error.message);
    }
}

/**
 * Display invoice list
 * @param {Array} invoices - Array of invoice objects
 * @param {string} contractId - Contract ID
 */
function displayInvoices(invoices, contractId) {
    const container = document.getElementById('invoicesContainer');
    if (!container) return;

    if (!invoices || invoices.length === 0) {
        container.innerHTML = `
            <div class="invoice-empty">
                <i class="fas fa-file-invoice"></i>
                <p>No invoices yet</p>
            </div>
        `;
        return;
    }

    container.innerHTML = `
        <div class="invoices-list">
            ${invoices.map(invoice => renderInvoiceCard(invoice)).join('')}
        </div>
    `;
}

/**
 * Render individual invoice card
 * @param {Object} invoice - Invoice object
 * @returns {string} HTML string
 */
function renderInvoiceCard(invoice) {
    const dueDate = new Date(invoice.due_date);
    const now = new Date();
    const daysUntilDue = Math.ceil((dueDate - now) / (1000 * 60 * 60 * 24));
    const isOverdue = daysUntilDue < 0;
    const isDueSoon = daysUntilDue >= 0 && daysUntilDue <= 7;

    return `
        <div class="invoice-card ${invoice.status}">
            <div class="invoice-header">
                <div class="invoice-number">
                    <i class="fas fa-file-invoice"></i>
                    <span>Invoice #${invoice.invoice_number}</span>
                </div>
                <div class="invoice-status-badge ${invoice.status}">
                    ${getInvoiceStatusBadge(invoice.status)}
                </div>
            </div>

            <div class="invoice-details">
                <div class="invoice-amount">
                    ${formatCurrency(invoice.amount)}
                </div>
                
                <div class="invoice-info-grid">
                    <div class="info-item">
                        <i class="fas fa-calendar-alt"></i>
                        <div>
                            <div class="info-label">Issue Date</div>
                            <div class="info-value">${formatDate(invoice.issue_date)}</div>
                        </div>
                    </div>
                    
                    <div class="info-item ${isOverdue ? 'overdue' : isDueSoon ? 'due-soon' : ''}">
                        <i class="fas fa-calendar-check"></i>
                        <div>
                            <div class="info-label">Due Date</div>
                            <div class="info-value">${formatDate(invoice.due_date)}</div>
                            ${isOverdue ? `<div class="due-warning">Overdue by ${Math.abs(daysUntilDue)} days</div>` : ''}
                            ${isDueSoon && !isOverdue ? `<div class="due-warning">Due in ${daysUntilDue} days</div>` : ''}
                        </div>
                    </div>
                    
                    ${invoice.paid_date ? `
                        <div class="info-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <div class="info-label">Paid On</div>
                                <div class="info-value">${formatDate(invoice.paid_date)}</div>
                            </div>
                        </div>
                    ` : ''}
                </div>

                ${invoice.description ? `
                    <div class="invoice-description">
                        <i class="fas fa-align-left"></i>
                        ${invoice.description}
                    </div>
                ` : ''}
            </div>

            <div class="invoice-actions">
                <button onclick="viewInvoice(${invoice.id})" class="btn-invoice view">
                    <i class="fas fa-eye"></i> View Details
                </button>
                <button onclick="downloadInvoicePDF(${invoice.id})" class="btn-invoice download">
                    <i class="fas fa-download"></i> Download PDF
                </button>
                ${invoice.status === 'pending' && invoice.can_mark_paid ? `
                    <button onclick="markInvoicePaid(${invoice.id})" class="btn-invoice paid">
                        <i class="fas fa-check"></i> Mark as Paid
                    </button>
                ` : ''}
            </div>
        </div>
    `;
}

/**
 * Get invoice status badge HTML
 * @param {string} status - Invoice status
 * @returns {string} Badge HTML
 */
function getInvoiceStatusBadge(status) {
    const badges = {
        'pending': '<i class="fas fa-clock"></i> Pending',
        'paid': '<i class="fas fa-check-circle"></i> Paid',
        'overdue': '<i class="fas fa-exclamation-triangle"></i> Overdue',
        'cancelled': '<i class="fas fa-times-circle"></i> Cancelled'
    };
    return badges[status] || status;
}

/**
 * View invoice details in modal
 * @param {number} invoiceId - Invoice ID
 */
async function viewInvoice(invoiceId) {
    try {
        const response = await fetch('api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'get_invoice_details',
                invoice_id: invoiceId
            })
        });

        const result = await response.json();
        
        if (result.success) {
            showInvoiceModal(result.invoice);
        } else {
            throw new Error(result.message || 'Failed to load invoice details');
        }
    } catch (error) {
        console.error('Invoice view error:', error);
        showToast('error', 'Load Failed', error.message);
    }
}

/**
 * Show invoice modal with full details
 * @param {Object} invoice - Invoice object
 */
function showInvoiceModal(invoice) {
    const modal = document.createElement('div');
    modal.className = 'invoice-modal-overlay';
    modal.innerHTML = `
        <div class="invoice-modal">
            <div class="invoice-modal-header">
                <h3><i class="fas fa-file-invoice-dollar"></i> Invoice #${invoice.invoice_number}</h3>
                <button class="close-modal" onclick="this.closest('.invoice-modal-overlay').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="invoice-modal-body">
                <div class="invoice-detail-section">
                    <h4>Invoice Information</h4>
                    <div class="detail-grid">
                        <div><strong>Invoice Number:</strong> #${invoice.invoice_number}</div>
                        <div><strong>Status:</strong> ${getInvoiceStatusBadge(invoice.status)}</div>
                        <div><strong>Issue Date:</strong> ${formatDate(invoice.issue_date)}</div>
                        <div><strong>Due Date:</strong> ${formatDate(invoice.due_date)}</div>
                        ${invoice.paid_date ? `<div><strong>Paid Date:</strong> ${formatDate(invoice.paid_date)}</div>` : ''}
                    </div>
                </div>

                <div class="invoice-detail-section">
                    <h4>Contract Information</h4>
                    <div class="detail-grid">
                        <div><strong>Contract:</strong> ${invoice.contract_title}</div>
                        <div><strong>Company:</strong> ${invoice.company_name}</div>
                        <div><strong>Customer:</strong> ${invoice.customer_name}</div>
                    </div>
                </div>

                ${invoice.line_items && invoice.line_items.length > 0 ? `
                    <div class="invoice-detail-section">
                        <h4>Line Items</h4>
                        <table class="invoice-items-table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${invoice.line_items.map(item => `
                                    <tr>
                                        <td>${item.description}</td>
                                        <td>${item.quantity}</td>
                                        <td>${formatCurrency(item.unit_price)}</td>
                                        <td>${formatCurrency(item.total)}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                            <tfoot>
                                <tr class="total-row">
                                    <td colspan="3"><strong>Total Amount</strong></td>
                                    <td><strong>${formatCurrency(invoice.amount)}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                ` : `
                    <div class="invoice-detail-section">
                        <h4>Amount</h4>
                        <div class="invoice-total-amount">${formatCurrency(invoice.amount)}</div>
                    </div>
                `}

                ${invoice.notes ? `
                    <div class="invoice-detail-section">
                        <h4>Notes</h4>
                        <p>${invoice.notes}</p>
                    </div>
                ` : ''}
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

/**
 * Download invoice as PDF
 * @param {number} invoiceId - Invoice ID
 */
async function downloadInvoicePDF(invoiceId) {
    try {
        showToast('success', 'Downloading...', 'Generating PDF file');
        
        window.location.href = `api/contracts.php?action=download_invoice_pdf&invoice_id=${invoiceId}`;
        
    } catch (error) {
        console.error('PDF download error:', error);
        showToast('error', 'Download Failed', error.message);
    }
}

/**
 * Mark invoice as paid
 * @param {number} invoiceId - Invoice ID
 */
async function markInvoicePaid(invoiceId) {
    if (!confirm('Are you sure you want to mark this invoice as paid?')) {
        return;
    }

    try {
        const response = await fetch('api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'mark_invoice_paid',
                invoice_id: invoiceId
            })
        });

        const result = await response.json();
        
        if (result.success) {
            showToast('success', 'Updated!', 'Invoice has been marked as paid');
            setTimeout(() => location.reload(), 2000);
        } else {
            throw new Error(result.message || 'Failed to update invoice');
        }
    } catch (error) {
        console.error('Update error:', error);
        showToast('error', 'Update Failed', error.message);
    }
}

/**
 * Format currency
 * @param {number} amount - Amount to format
 * @returns {string} Formatted currency
 */
function formatCurrency(amount) {
    return 'Rs. ' + parseFloat(amount).toLocaleString('en-LK', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

/**
 * Format date
 * @param {string} dateString - Date string
 * @returns {string} Formatted date
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

/**
 * Show toast notification
 * @param {string} type - Toast type
 * @param {string} title - Toast title
 * @param {string} message - Toast message
 */
function showToast(type, title, message) {
    const toast = document.createElement('div');
    toast.className = `invoice-toast ${type} show`;
    toast.innerHTML = `
        <div class="toast-icon">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        </div>
        <div class="toast-content">
            <h4>${title}</h4>
            <p>${message}</p>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// Export functions for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initializeInvoices,
        viewInvoice,
        downloadInvoicePDF,
        markInvoicePaid
    };
}
