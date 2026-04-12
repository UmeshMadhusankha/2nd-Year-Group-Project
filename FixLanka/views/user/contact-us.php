<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\user\contact-us.php
require_once __DIR__ . '/../../config/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/contact-us.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="contact-container">
        <!-- Header with Home Button -->
        <div class="contact-header">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-envelope"></i>
                    Contact Us
                </h1>
                <p class="page-subtitle">We'd love to hear from you. Send us a message!</p>
            </div>
            <a href="/2nd-Year-Group-Project/FixLanka/views/user/landing.php" class="btn-home">
                <i class="fas fa-home"></i> Home
            </a>
        </div>

        <div class="contact-content">
            <!-- Contact Info Cards -->
            <div class="contact-info-section">
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Email Us</h3>
                    <p>support@fixlanka.lk</p>
                    <small>We'll respond within 24 hours</small>
                </div>

                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h3>Call Us</h3>
                    <p>+94 11 234 5678</p>
                    <small>Mon-Fri, 9AM - 6PM</small>
                </div>

                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Visit Us</h3>
                    <p>Colombo, Sri Lanka</p>
                    <small>Main Office</small>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-section">
                <div class="form-card">
                    <h2 class="form-title">Send us a Message</h2>
                    <p class="form-description">Fill out the form below and we'll get back to you as soon as possible.</p>

                    <form class="contact-form" id="contactForm">
                        <div class="form-group">
                            <label for="name">Your Name <span class="required">*</span></label>
                            <input type="text" id="name" name="name" required placeholder="Enter your full name">
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address <span class="required">*</span></label>
                            <input type="email" id="email" name="email" required placeholder="your.email@example.com">
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject <span class="required">*</span></label>
                            <select id="subject" name="subject" required>
                                <option value="">Select a subject</option>
                                <option value="general">General Inquiry</option>
                                <option value="support">Technical Support</option>
                                <option value="billing">Billing Question</option>
                                <option value="feedback">Feedback</option>
                                <option value="complaint">Complaint</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="message">Message <span class="required">*</span></label>
                            <textarea id="message" name="message" rows="6" required placeholder="Write your message here..."></textarea>
                            <small class="char-counter">0 / 500 characters</small>
                        </div>

                        <button type="submit" class="submit-btn">
                            <i class="fas fa-paper-plane"></i>
                            Send Message
                        </button>

                        <!-- Success Message (hidden by default) -->
                        <div class="success-message" id="successMessage">
                            <i class="fas fa-check-circle"></i>
                            <p>Thank you! Your message has been sent successfully. We'll get back to you soon.</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/contact-us.js"></script>
</body>
</html>
