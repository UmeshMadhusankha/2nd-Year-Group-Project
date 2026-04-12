<?php
require_once __DIR__ . '/../../config/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How It Works - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/how-it-works.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="how-it-works-container">
        <!-- Header with Home Button -->
        <div class="how-it-works-header">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-info-circle"></i> How It Works
                </h1>
                <p class="page-subtitle">Simple steps to get professional service</p>
            </div>
            <a href="/2nd-Year-Group-Project/FixLanka/views/user/landing.php" class="btn-home">
                <i class="fas fa-home"></i> Home
            </a>
        </div>

        <!-- About Fix Lanka Section -->
        <div class="about-section">
            <div class="about-content">
                <h2 class="about-title">About Fix Lanka</h2>
                <p class="about-text">
                    <strong>Fix Lanka</strong> is your trusted platform connecting customers with verified, 
                    professional service providers across Sri Lanka. We understand that finding reliable 
                    professionals for home and business needs can be challenging and time-consuming.
                </p>
                <p class="about-text">
                    Our mission is to simplify this process by creating a seamless marketplace where 
                    quality service providers and customers meet. Whether you need a plumber, electrician, 
                    cleaner, carpenter, or any other professional service, Fix Lanka makes it easy to 
                    find, compare, and hire the right expert for your needs.
                </p>
                <p class="about-text">
                    We carefully verify all service providers on our platform, ensuring they meet our 
                    high standards for professionalism, reliability, and quality. With transparent pricing, 
                    real customer reviews, and 24/7 support, Fix Lanka is committed to delivering 
                    exceptional service experiences every time.
                </p>
            </div>
        </div>

        <!-- Steps Section -->
        <div class="steps-section">
            <h2 class="section-title">Getting Started is Easy</h2>
            <div class="steps-grid">
                <!-- Step 1 -->
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="step-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3 class="step-title">Create Your Account</h3>
                    <p class="step-description">
                        Sign up for free in just a few minutes. Provide basic information to create 
                        your profile and start exploring our network of professional service providers.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="step-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3 class="step-title">Post Your Job Request</h3>
                    <p class="step-description">
                        Describe your service needs in detail. Include the type of service, location, 
                        timeline, and any specific requirements. You can also upload photos to help 
                        service providers understand your needs better.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="step-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="step-title">Browse & Compare</h3>
                    <p class="step-description">
                        Receive quotes from multiple verified service providers. Review their profiles, 
                        ratings, past work, and customer reviews. Compare prices and service offerings 
                        to make an informed decision.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="step-card">
                    <div class="step-number">4</div>
                    <div class="step-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3 class="step-title">Choose Your Provider</h3>
                    <p class="step-description">
                        Select the service provider that best fits your needs and budget. Contact them 
                        directly through our platform to discuss details, schedule the service, and 
                        confirm the booking.
                    </p>
                </div>

                <!-- Step 5 -->
                <div class="step-card">
                    <div class="step-number">5</div>
                    <div class="step-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3 class="step-title">Get Service Done</h3>
                    <p class="step-description">
                        The service provider completes the work according to your agreement. Track 
                        the progress through our platform and communicate directly with your provider 
                        for any updates or changes.
                    </p>
                </div>

                <!-- Step 6 -->
                <div class="step-card">
                    <div class="step-number">6</div>
                    <div class="step-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="step-title">Rate & Review</h3>
                    <p class="step-description">
                        Once the job is complete, rate your experience and leave a review. Your 
                        feedback helps maintain service quality and assists other customers in 
                        making informed decisions.
                    </p>
                </div>
            </div>
        </div>

        <!-- For Service Providers Section -->
        <div class="providers-section">
            <div class="providers-content">
                <h2 class="providers-title">
                    <i class="fas fa-briefcase"></i> Are You a Service Provider?
                </h2>
                <p class="providers-text">
                    Join Fix Lanka's growing network of professional service providers. Reach thousands 
                    of potential customers, grow your business, and manage your bookings efficiently 
                    through our platform.
                </p>
                <div class="providers-benefits">
                    <div class="benefit-item">
                        <i class="fas fa-users"></i>
                        <span>Access to Customers</span>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-calendar-check"></i>
                        <span>Manage Bookings</span>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Grow Your Business</span>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Verified Badge</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/how-it-works.js"></script>
</body>
</html>
