<!-- Listed Job Request Selection Modal -->
<div class="listed-job-modal-overlay" id="listedJobRequestModal" aria-hidden="true">
    <div class="listed-job-modal-container" role="dialog" aria-modal="true" aria-labelledby="listedJobRequestTitle">
        <div class="listed-job-modal-header">
            <h3 id="listedJobRequestTitle">Select a Listed Job</h3>
            <p class="listed-job-modal-subtitle">
                Choose one of your pending jobs for
                <strong id="listedJobRequestProviderTypeLabel">this provider</strong>.
            </p>
            <button type="button" class="listed-job-close" id="listedJobRequestCloseBtn" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="listed-job-modal-body">
            <div class="listed-job-error" id="listedJobRequestError" style="display:none;"></div>
            <div class="listed-job-list" id="listedJobRequestList">
                <p class="listed-job-placeholder">Loading your pending jobs...</p>
            </div>
        </div>

        <div class="listed-job-modal-actions">
            <button type="button" class="btn-outline" id="listedJobRequestCancelBtn">Cancel</button>
            <button type="button" class="btn-primary" id="listedJobRequestSubmitBtn" disabled>Request</button>
        </div>
    </div>
</div>
