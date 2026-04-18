/**
 * Reviews Export Modal Controller
 * Handles export functionality for review reports
 */

// API endpoint
const REVIEWS_EXPORT_API = '/2nd-Year-Group-Project/FixLanka/api/reviews-export.php';

// ============ Modal Functions ============

function exportReviews() {
    const modal = document.getElementById('reviewsExportModal');
    if (modal) {
        modal.classList.add('active');
        updateReviewsExportPreview();
    }
}

function closeReviewsExportModal() {
    const modal = document.getElementById('reviewsExportModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

// ============ Filter Collection ============

function collectReviewsExportFilters() {
    const filters = {};

    // Rating (radio buttons)
    const ratingRadio = document.querySelector('input[name="exportReviewRating"]:checked');
    filters.rating = ratingRadio ? ratingRadio.value : 'all';

    // Period (radio buttons)
    const periodRadio = document.querySelector('input[name="exportReviewPeriod"]:checked');
    filters.period = periodRadio ? periodRadio.value : 'all';

    // Export Format
    const formatRadio = document.querySelector('input[name="exportReviewFormat"]:checked');
    filters.format = formatRadio ? formatRadio.value : 'csv';

    return filters;
}

// ============ Preview & Export Functions ============

async function updateReviewsExportPreview() {
    const filters = collectReviewsExportFilters();
    const summaryBox = document.getElementById('reviewsExportSummaryBox');
    const recordsElement = document.getElementById('reviewsExportTotalRecords');
    const ratingElement = document.getElementById('reviewsExportAvgRating');

    if (summaryBox) {
        summaryBox.classList.add('loading');
    }

    try {
        const params = new URLSearchParams({
            action: 'preview',
            ...filters
        });

        const response = await fetch(`${REVIEWS_EXPORT_API}?${params.toString()}`);
        const data = await response.json();

        if (data.success && data.summary) {
            if (recordsElement) recordsElement.textContent = data.summary.total_records || 0;
            if (ratingElement) ratingElement.textContent = data.summary.avg_rating || '0.0';
        }
    } catch (error) {
        console.error('Preview error:', error);
    } finally {
        if (summaryBox) {
            summaryBox.classList.remove('loading');
        }
    }
}

async function downloadReviewsExport() {
    const filters = collectReviewsExportFilters();

    const params = new URLSearchParams({
        action: 'download',
        ...filters
    });

    const downloadUrl = `${REVIEWS_EXPORT_API}?${params.toString()}`;

    const link = document.createElement('a');
    link.href = downloadUrl;
    link.download = `reviews_export_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    setTimeout(closeReviewsExportModal, 500);
}

// ============ Initialization ============

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('reviewsExportModal');
    if (modal) {
        const filterInputs = modal.querySelectorAll('input');
        filterInputs.forEach(input => {
            input.addEventListener('change', updateReviewsExportPreview);
        });

        // Close on overlay click
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeReviewsExportModal();
        });
    }

    // Attach to export button
    const exportBtn = document.getElementById('exportReviewsBtn');
    if (exportBtn) {
        // Remove existing listeners if any by replacing the button or using a wrapper
        // Since we are adding it, we can just add the listener
        exportBtn.addEventListener('click', exportReviews);
    }
});

// Make globally available
window.exportReviews = exportReviews;
window.closeReviewsExportModal = closeReviewsExportModal;
window.downloadReviewsExport = downloadReviewsExport;
