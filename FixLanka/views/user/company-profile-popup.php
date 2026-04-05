<!-- Company Profile Popup Modal -->
<div class="company-modal-overlay" id="companyModal">
    <div class="company-modal-container">
        <div class="company-modal-header">
            <div class="company-modal-logo" id="companyModalLogo">CO</div>
            <div class="company-modal-title-section">
                <h2 class="company-modal-title" id="companyModalTitle">Company</h2>
                <p class="company-modal-type" id="companyModalType">Service Company</p>
            </div>
            <button class="company-modal-close" type="button" onclick="closeCompanyModal()" aria-label="Close company profile">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="company-modal-content">
            <div class="company-modal-rating">
                <div class="stars" id="companyModalStars"></div>
                <span class="rating-text" id="companyModalRatingText">0.0 / 5.0</span>
            </div>

            <div class="company-modal-info-grid">
                <div class="company-modal-info-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <span class="info-label">Contact</span>
                        <span class="info-value" id="companyModalContact">N/A</span>
                    </div>
                </div>
                <div class="company-modal-info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <span class="info-label">Email</span>
                        <span class="info-value" id="companyModalEmail">N/A</span>
                    </div>
                </div>
                <div class="company-modal-info-item">
                    <i class="fas fa-globe"></i>
                    <div>
                        <span class="info-label">Website</span>
                        <span class="info-value" id="companyModalWebsite">N/A</span>
                    </div>
                </div>
                <div class="company-modal-info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <span class="info-label">Service Areas</span>
                        <span class="info-value" id="companyModalDistricts">N/A</span>
                    </div>
                </div>
            </div>

            <div class="company-modal-section">
                <h3 class="modal-section-title"><i class="fas fa-location-dot"></i> Address</h3>
                <p class="company-modal-description" id="companyModalAddress">N/A</p>
            </div>

            <div class="company-modal-section">
                <h3 class="modal-section-title"><i class="fas fa-info-circle"></i> About Company</h3>
                <p class="company-modal-description" id="companyModalDescription">No description available.</p>
            </div>

            <div class="company-modal-section" id="companyModalServicesSection" style="display:none;">
                <h3 class="modal-section-title"><i class="fas fa-tools"></i> Services Offered</h3>
                <div class="company-modal-services" id="companyModalServices"></div>
            </div>

            <div class="company-modal-actions">
                <button class="btn-primary" type="button" onclick="requestCompanyQuoteFromModal()">
                    <i class="fas fa-file-invoice"></i>
                    Request for a New Job
                </button>
                <button class="btn-secondary" type="button" onclick="sendCompanyRepairRequest()">
                    <i class="fas fa-tools"></i>
                    Request for a Listed Job
                </button>
                <button class="btn-outline" type="button" onclick="closeCompanyModal()">
                    <i class="fas fa-times"></i>
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
