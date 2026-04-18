/**
 * Workforce Export Modal Controller
 * Handles export functionality for workforce reports
 */

// API endpoint
const WORKFORCE_EXPORT_API = '/2nd-Year-Group-Project/FixLanka/api/workforce-export.php';

// ============ Modal Functions ============

function exportWorkforce() {
    const modal = document.getElementById('workforceExportModal');
    if (modal) {
        modal.classList.add('active');
        updateWorkforceExportPreview();
    }
}

function closeWorkforceExportModal() {
    const modal = document.getElementById('workforceExportModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

// ============ Filter Collection ============

function collectWorkforceExportFilters() {
    const filters = {};

    // Workforce Type
    const typeRadio = document.querySelector('input[name="workforceType"]:checked');
    filters.type = typeRadio ? typeRadio.value : 'all';

    // Status (all/active/inactive)
    const statusRadio = document.querySelector('input[name="workforceStatus"]:checked');
    filters.status = statusRadio ? statusRadio.value : 'active';

    // Export Format
    const formatRadio = document.querySelector('input[name="workforceFormat"]:checked');
    filters.format = formatRadio ? formatRadio.value : 'csv';

    return filters;
}

// ============ Preview & Export Functions ============

async function updateWorkforceExportPreview() {
    const filters = collectWorkforceExportFilters();
    const summaryBox = document.getElementById('workforceExportSummaryBox');
    const recordsElement = document.getElementById('workforceExportTotalRecords');
    const ratingElement = document.getElementById('workforceExportAvgRating');
    const specialtyElement = document.getElementById('workforceExportSpecialties');

    if (summaryBox) {
        summaryBox.classList.add('loading');
    }

    try {
        const params = new URLSearchParams({
            action: 'preview',
            ...filters
        });

        const response = await fetch(`${WORKFORCE_EXPORT_API}?${params.toString()}`);
        const data = await response.json();

        if (data.success && data.summary) {
            if (recordsElement) recordsElement.textContent = data.summary.total_records || 0;
            if (ratingElement) ratingElement.textContent = data.summary.avg_rating || '0.0';
            if (specialtyElement) specialtyElement.textContent = data.summary.specialties_count || 0;
        }
    } catch (error) {
        console.error('Preview error:', error);
    } finally {
        if (summaryBox) {
            summaryBox.classList.remove('loading');
        }
    }
}

async function downloadWorkforceExport() {
    const filters = collectWorkforceExportFilters();

    const params = new URLSearchParams({
        action: 'download',
        ...filters
    });

    const downloadUrl = `${WORKFORCE_EXPORT_API}?${params.toString()}`;

    const link = document.createElement('a');
    link.href = downloadUrl;
    link.download = `workforce_export_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    setTimeout(closeWorkforceExportModal, 500);
}

// ============ Initialization ============

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('workforceExportModal');
    if (modal) {
        const filterInputs = modal.querySelectorAll('input');
        filterInputs.forEach(input => {
            input.addEventListener('change', updateWorkforceExportPreview);
        });

        // Close on overlay click
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeWorkforceExportModal();
        });
    }

    // Attach to export button if present
    const exportBtn = document.getElementById('exportWorkforceBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', exportWorkforce);
    }
});

// Make globally available
window.exportWorkforce = exportWorkforce;
window.closeWorkforceExportModal = closeWorkforceExportModal;
window.downloadWorkforceExport = downloadWorkforceExport;
