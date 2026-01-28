<!-- Repairer Profile Popup Modal -->
<div id="repairerProfileModal" class="repairer-modal">
    <div class="repairer-modal-overlay" onclick="closeRepairerProfile()"></div>
    <div class="repairer-modal-content">
        <!-- Close Button -->
        <button class="repairer-modal-close" onclick="closeRepairerProfile()">
            <i class="fas fa-times"></i>
        </button>

        <!-- Profile Content -->
        <div class="profile-content">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-header-bg"></div>
                <div class="profile-header-content">
                    <div class="profile-avatar">
                        <img id="profileImage" src="https://ui-avatars.com/api/?name=John+Doe&size=200&background=17a2b8&color=fff" alt="Profile">
                    </div>
                    <div class="profile-header-info">
                        <h2 id="profileName" class="profile-name">Loading...</h2>
                        <p id="profileCategory" class="profile-category">Service Professional</p>
                        <div class="profile-rating">
                            <div class="stars" id="profileStars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span id="profileRatingText" class="rating-text">4.5 (120 reviews)</span>
                        </div>
                        <div class="profile-stats">
                            <div class="stat-item">
                                <i class="fas fa-briefcase"></i>
                                <span id="completedJobs">0</span>
                                <span class="stat-label">Jobs Completed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Body -->
            <div class="profile-body">
                <!-- About Section -->
                <div class="profile-section">
                    <h3 class="section-title">
                        <i class="fas fa-user"></i>
                        About
                    </h3>
                    <p id="profileAbout" class="profile-about">Loading profile information...</p>
                </div>

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
                            <i class="fas fa-map-pin"></i>
                            <span id="profileDistricts">N/A</span>
                        </div>
                    </div>
                </div>

                <!-- Availability -->
                <div class="profile-section">
                    <h3 class="section-title">
                        <i class="fas fa-clock"></i>
                        Availability
                    </h3>
                    <div class="availability-badge" id="availabilityBadge">
                        <i class="fas fa-circle"></i>
                        <span id="availabilityText">Available</span>
                    </div>
                </div>

                <!-- Reviews Section -->
                <div class="profile-section">
                    <h3 class="section-title">
                        <i class="fas fa-star"></i>
                        Recent Reviews
                    </h3>
                    <div id="reviewsList" class="reviews-list">
                        <!-- Reviews will be loaded here -->
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="profile-actions">
                    <button class="btn-primary" onclick="sendRepairRequest('repairer')">
                        <i class="fas fa-tools"></i>
                        Send Repair Request
                    </button>
                    <button class="btn-secondary" onclick="contactRepairer()">
                        <i class="fas fa-comment"></i>
                        Send Message
                    </button>
                    <button class="btn-outline" onclick="requestQuote()">
                        <i class="fas fa-file-invoice"></i>
                        Request Quote
                    </button>
                    <button class="btn-outline" onclick="viewFullProfile()">
                        <i class="fas fa-external-link-alt"></i>
                        View Full Profile
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
