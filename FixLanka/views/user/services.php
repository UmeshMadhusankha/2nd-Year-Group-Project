<?php
require_once __DIR__ . '/../../config/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/services.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="services-container">
        <!-- Header with Home Button -->
        <div class="services-header">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-tools"></i> Our Services
                </h1>
                <p class="page-subtitle">Professional services for all your needs</p>
            </div>
            <a href="/2nd-Year-Group-Project/FixLanka/views/user/landing.php" class="btn-home">
                <i class="fas fa-home"></i> Home
            </a>
        </div>

        <!-- Services Grid -->
        <div class="services-grid">
            <!-- Plumbing -->
            <div class="service-card">
                <div class="service-icon plumbing">
                    <i class="fas fa-faucet"></i>
                </div>
                <h3 class="service-title">Plumbing</h3>
                <p class="service-description">
                    Expert plumbing services including pipe repairs, leak fixes, drain cleaning, 
                    water heater installation, and bathroom/kitchen plumbing solutions.
                </p>
                <ul class="service-features">
                    <li><i class="fas fa-check"></i> Emergency repairs</li>
                    <li><i class="fas fa-check"></i> Pipe installation</li>
                    <li><i class="fas fa-check"></i> Leak detection</li>
                    <li><i class="fas fa-check"></i> Drain cleaning</li>
                </ul>
            </div>

            <!-- Electrical -->
            <div class="service-card">
                <div class="service-icon electrical">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3 class="service-title">Electrical</h3>
                <p class="service-description">
                    Professional electrical services including wiring, lighting installation, 
                    circuit repairs, electrical panel upgrades, and safety inspections.
                </p>
                <ul class="service-features">
                    <li><i class="fas fa-check"></i> Wiring & rewiring</li>
                    <li><i class="fas fa-check"></i> Lighting installation</li>
                    <li><i class="fas fa-check"></i> Panel upgrades</li>
                    <li><i class="fas fa-check"></i> Safety inspections</li>
                </ul>
            </div>

            <!-- HVAC -->
            <div class="service-card">
                <div class="service-icon hvac">
                    <i class="fas fa-wind"></i>
                </div>
                <h3 class="service-title">HVAC</h3>
                <p class="service-description">
                    Complete HVAC services including air conditioning installation, heating system 
                    repairs, ventilation solutions, and regular maintenance.
                </p>
                <ul class="service-features">
                    <li><i class="fas fa-check"></i> AC installation</li>
                    <li><i class="fas fa-check"></i> Heating repairs</li>
                    <li><i class="fas fa-check"></i> Ventilation</li>
                    <li><i class="fas fa-check"></i> Regular maintenance</li>
                </ul>
            </div>

            <!-- Cleaning -->
            <div class="service-card">
                <div class="service-icon cleaning">
                    <i class="fas fa-broom"></i>
                </div>
                <h3 class="service-title">Cleaning</h3>
                <p class="service-description">
                    Professional cleaning services for homes and offices including deep cleaning, 
                    regular maintenance, carpet cleaning, and specialized sanitization.
                </p>
                <ul class="service-features">
                    <li><i class="fas fa-check"></i> Deep cleaning</li>
                    <li><i class="fas fa-check"></i> Regular maintenance</li>
                    <li><i class="fas fa-check"></i> Carpet cleaning</li>
                    <li><i class="fas fa-check"></i> Sanitization</li>
                </ul>
            </div>

            <!-- Carpentry -->
            <div class="service-card">
                <div class="service-icon carpentry">
                    <i class="fas fa-hammer"></i>
                </div>
                <h3 class="service-title">Carpentry</h3>
                <p class="service-description">
                    Skilled carpentry services including custom furniture, cabinet installation, 
                    door and window repairs, and wooden structure construction.
                </p>
                <ul class="service-features">
                    <li><i class="fas fa-check"></i> Custom furniture</li>
                    <li><i class="fas fa-check"></i> Cabinet installation</li>
                    <li><i class="fas fa-check"></i> Door & window repairs</li>
                    <li><i class="fas fa-check"></i> Wood structures</li>
                </ul>
            </div>

            <!-- Painting -->
            <div class="service-card">
                <div class="service-icon painting">
                    <i class="fas fa-paint-roller"></i>
                </div>
                <h3 class="service-title">Painting</h3>
                <p class="service-description">
                    Professional painting services for interior and exterior spaces including 
                    wall preparation, color consultation, and specialty finishes.
                </p>
                <ul class="service-features">
                    <li><i class="fas fa-check"></i> Interior painting</li>
                    <li><i class="fas fa-check"></i> Exterior painting</li>
                    <li><i class="fas fa-check"></i> Color consultation</li>
                    <li><i class="fas fa-check"></i> Specialty finishes</li>
                </ul>
            </div>
        </div>

        <!-- Why Choose Us Section -->
        <div class="why-choose-section">
            <h2 class="section-title">Why Choose Fix Lanka?</h2>
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h4>Verified Professionals</h4>
                    <p>All service providers are thoroughly vetted and verified for quality assurance</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h4>24/7 Availability</h4>
                    <p>Round-the-clock support and emergency services when you need them most</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h4>Quality Guaranteed</h4>
                    <p>Satisfaction guaranteed with our quality assurance and service standards</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h4>Competitive Pricing</h4>
                    <p>Transparent pricing with no hidden charges and competitive rates</p>
                </div>
            </div>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/services.js"></script>
</body>
</html>
