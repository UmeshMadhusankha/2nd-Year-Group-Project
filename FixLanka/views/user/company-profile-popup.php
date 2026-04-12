<!-- Company Profile Popup Modal -->
<div id="companyProfileModal" class="company-modal">
    <div class="company-modal-overlay" onclick="closeCompanyProfile()"></div>
    <div class="company-modal-content">
        <!-- Close Button -->
        <button class="company-modal-close" onclick="closeCompanyProfile()">
            <i class="fas fa-times"></i>
        </button>

        <!-- Profile Content -->
        <div class="profile-content">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-header-bg"></div>
                <div class="profile-header-content">
                    <div class="profile-logo">
                        <div id="profileLogo" class="logo-placeholder">CO</div>
                    </div>
                    <div class="profile-header-info">
                        <h2 id="profileName" class="profile-name">Loading...</h2>
                        <p id="profileType" class="profile-type">Service Company</p>
                        <div class="profile-rating">
                            <div class="stars" id="profileStars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span id="profileRatingText" class="rating-text">0.0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Body -->
            <div class="profile-body">
                <!-- Contact Information -->
                <div class="profile-section">
                    <h3 class="section-title">
                        <i class="fas fa-address-card"></i>
                        Contact Information
                    </h3>
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <span id="profilePhone">N/A</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <span id="profileEmail">N/A</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-globe"></i>
                            <span id="profileWebsite">N/A</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span id="profileAddress">N/A</span>
                        </div>
                    </div>
                </div>

                <!-- About Section -->
                <div class="profile-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        About Company
                    </h3>
                    <p id="profileAbout" class="profile-about">Loading profile information...</p>
                </div>

                <!-- Service Districts -->
                <div class="profile-section">
                    <h3 class="section-title">
                        <i class="fas fa-map"></i>
                        Service Districts
                    </h3>
                    <p id="profileDistricts" class="profile-districts">N/A</p>
                </div>

                <!-- Services Section -->
                <div class="profile-section" id="servicesSection" style="display:none;">
                    <h3 class="section-title">
                        <i class="fas fa-tools"></i>
                        Services Offered
                    </h3>
                    <div id="servicesList" class="services-list"></div>
                </div>

                <!-- Action Buttons -->
                <div class="profile-actions">
                    <button class="btn-primary" onclick="requestCompanyNewJobRequest()">
                        <i class="fas fa-file-invoice"></i>
                        Ask for a New Job
                    </button>
                    <button class="btn-secondary" type="button" onclick="sendCompanyListedJobRequest()">
                        <i class="fas fa-tools"></i>
                        Ask for an Existing Job
                    </button>
                    <button class="btn-outline" type="button" onclick="closeCompanyProfile()">
                        <i class="fas fa-times"></i>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
