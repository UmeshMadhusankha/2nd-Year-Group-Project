/**
 * Payments Export Modal Controller
 * Handles export functionality for payment reports
 */

// API endpoint
const PAYMENTS_EXPORT_API = '/2nd-Year-Group-Project/FixLanka/api/payments-export.php';

// ============ Modal Functions ============

function exportReport() {
    console.log('exportReport() called'); // Debug log
    const modal = document.getElementById('exportModal');
    console.log('Modal element:', modal); // Debug log
    if (modal) {
        modal.classList.add('active');
        console.log('Added active class'); // Debug log
        updateExportPreview(); // Update preview on open
    } else {
        console.error('Export modal not found! Check if #exportModal exists in the HTML.');
    }
}

function openPaymentsExportModal() {
    exportReport();
}

function closeExportModal() {
    const modal = document.getElementById('exportModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

// Close modal when clicking outside
document.addEventListener('click', function (e) {
    const modal = document.getElementById('exportModal');
    if (modal && e.target === modal) {
        closeExportModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeExportModal();
    }
});

// ============ Date Preset Functions ============

function setDatePreset(days) {
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - days);

    document.getElementById('exportStartDate').value = formatDate(startDate);
    document.getElementById('exportEndDate').value = formatDate(endDate);

    // Update active state on preset buttons
    document.querySelectorAll('.export-date-preset-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');

    updateExportPreview();
}

function formatDate(date) {
    return date.toISOString().split('T')[0];
}

// ============ Filter Collection ============

function collectExportFilters() {
    const filters = {};

    // Payment Type (radio buttons - single selection)
    const paymentTypeRadio = document.querySelector('input[name="paymentType"]:checked');
    filters.type = paymentTypeRadio ? paymentTypeRadio.value : 'all';

    // Status filters (checkboxes - multiple selection)
    const statusFilters = [];
    if (document.getElementById('exportCompleted')?.checked) statusFilters.push('completed');
    if (document.getElementById('exportPending')?.checked) statusFilters.push('pending');
    if (document.getElementById('exportFailed')?.checked) statusFilters.push('failed');
    if (statusFilters.length > 0) {
        filters.status = statusFilters;
    }

    // Date Range
    const dateFrom = document.getElementById('exportStartDate');
    const dateTo = document.getElementById('exportEndDate');
    if (dateFrom && dateFrom.value) {
        filters.dateFrom = dateFrom.value;
    }
    if (dateTo && dateTo.value) {
        filters.dateTo = dateTo.value;
    }

    // Export Format
    const formatRadio = document.querySelector('input[name="exportFormat"]:checked');
    if (formatRadio) {
        filters.format = formatRadio.value;
    } else {
        filters.format = 'csv'; // Default
    }

    return filters;
}

// ============ Preview & Export Functions ============

async function updateExportPreview() {
    const filters = collectExportFilters();
    const summaryBox = document.getElementById('exportSummaryBox');
    const recordsElement = document.getElementById('exportTotalRecords');
    const amountElement = document.getElementById('exportTotalAmount');
    const daysElement = document.getElementById('exportDateRange');

    if (summaryBox) {
        summaryBox.classList.add('loading');
    }

    try {
        const params = new URLSearchParams({
            action: 'preview',
            ...filters,
            status: filters.status ? filters.status.join(',') : ''
        });

        const response = await fetch(`${PAYMENTS_EXPORT_API}?${params.toString()}`);
        const data = await response.json();

        if (data.success) {
            if (recordsElement) recordsElement.textContent = data.count || 0;
            if (amountElement) amountElement.textContent = data.total || '0';
            if (daysElement) {
                // Calculate days in range
                const from = document.getElementById('exportStartDate')?.value;
                const to = document.getElementById('exportEndDate')?.value;
                if (from && to) {
                    const diff = Math.ceil((new Date(to) - new Date(from)) / (1000 * 60 * 60 * 24));
                    daysElement.textContent = diff;
                } else {
                    daysElement.textContent = 'All';
                }
            }
        }
    } catch (error) {
        console.error('Preview error:', error);
        if (recordsElement) recordsElement.textContent = '--';
    } finally {
        if (summaryBox) {
            summaryBox.classList.remove('loading');
        }
    }
}

async function previewPaymentsExport() {
    const filters = collectExportFilters();

    try {
        const params = new URLSearchParams({
            action: 'preview',
            ...filters,
            status: filters.status ? filters.status.join(',') : ''
        });

        const response = await fetch(`${PAYMENTS_EXPORT_API}?${params.toString()}`);
        const data = await response.json();

        if (data.success) {
            alert(`Preview:\n\nTotal Records: ${data.count}\nFormat: ${filters.format || 'CSV'}\n\nClick "Export Now" to download.`);
        } else {
            alert('Preview failed: ' + (data.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Preview error:', error);
        alert('Failed to generate preview');
    }
}

async function downloadPaymentsExport() {
    const filters = collectExportFilters();

    // Build download URL
    const params = new URLSearchParams({
        action: 'download',
        ...filters,
        status: filters.status ? filters.status.join(',') : ''
    });

    // Trigger download
    const downloadUrl = `${PAYMENTS_EXPORT_API}?${params.toString()}`;

    // Create temporary link and click it
    const link = document.createElement('a');
    link.href = downloadUrl;
    link.download = `payments_export_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Close modal after download starts
    setTimeout(closeExportModal, 500);
}

// ============ Event Listeners ============

document.addEventListener('DOMContentLoaded', function () {
    // Add change listeners to update preview
    const filterInputs = document.querySelectorAll('#exportModal input');
    filterInputs.forEach(input => {
        input.addEventListener('change', updateExportPreview);
    });
});

// Make functions globally available
window.exportReport = exportReport;
window.openPaymentsExportModal = openPaymentsExportModal;
window.closeExportModal = closeExportModal;
window.setDatePreset = setDatePreset;
window.previewPaymentsExport = previewPaymentsExport;
window.downloadPaymentsExport = downloadPaymentsExport;