<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/_components/Common.php';
require_once __DIR__ . '/../../includes/admin-modarator/auth.php';
require_once __DIR__ . '/../../config/database.php';

requireRole('moderator', '/2nd-Year-Group-Project/FixLanka');

$basePath = '';
$currentPath = 'static-content';
$pageTitle = 'Static Content Management - FixLanka';
$pageDescription = 'Manage FAQs, Terms, Contact Us, About Us, How It Works, Services, Why Choose Us and Support content';

function dbColumnExists(PDO $pdo, string $table, string $column): bool
{
    try {
        $stmt = $pdo->prepare(
            "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table
               AND COLUMN_NAME = :column
             LIMIT 1"
        );
        $stmt->execute([':table' => $table, ':column' => $column]);
        return (bool)$stmt->fetchColumn();
    } catch (Throwable $e) {
        return false;
    }
}

function ensureStaticContentSchema(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS `staticcontent` (
            `content_id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(255) NOT NULL,
            `description` varchar(500) DEFAULT NULL,
            `body` text NOT NULL,
            `status` enum('Draft','Published') NOT NULL DEFAULT 'Published',
            `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            `content_type` enum('terms','privacy','faq','about','help','contact','how_it_works','services','why_choose','support','landing_hero') NOT NULL,
            PRIMARY KEY (`content_id`),
            KEY `idx_type` (`content_type`),
            KEY `idx_status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
    );

    if (!dbColumnExists($pdo, 'staticcontent', 'description')) {
        $pdo->exec("ALTER TABLE `staticcontent` ADD COLUMN `description` varchar(500) DEFAULT NULL AFTER `title`");
    }

    if (!dbColumnExists($pdo, 'staticcontent', 'status')) {
        $pdo->exec("ALTER TABLE `staticcontent` ADD COLUMN `status` enum('Draft','Published') NOT NULL DEFAULT 'Published' AFTER `body`");
    }

    try {
        $pdo->exec("ALTER TABLE `staticcontent` MODIFY COLUMN `content_type` enum('terms','privacy','faq','about','help','contact','how_it_works','services','why_choose','support','landing_hero') NOT NULL");
    } catch (Throwable $e) {
    }
}

function ensureManagedSections(PDO $pdo): void
{
    $defaults = [
        'landing_hero' => [
            'Landing Page Header',
            'Hero title and subtitle shown on the landing page.',
            '{"title":"Find Trusted Service Professionals Near You","subtitle":"Connect with verified local experts for all your home and business needs"}'
        ],
        'faq' => [
            'Frequently Asked Questions',
            'Common questions and answers for users and providers.',
            <<<HTML
<div class="faq-item">
    <button class="faq-question">
        <span class="question-text">How do I post a job request on Fix Lanka?</span>
        <i class="fas fa-chevron-down faq-icon"></i>
    </button>
    <div class="faq-answer">
        <p>Posting a job is simple! First, make sure you're logged into your account. Then, click on the "Post a Job" button on the landing page or navigate to the job posting section. Fill in the required details including the service category, description, location, preferred provider type, and urgency level. You can also upload photos if needed. Once submitted, your job request will be visible to service providers in your area.</p>
    </div>
</div>

<div class="faq-item">
    <button class="faq-question">
        <span class="question-text">How can I find trusted service providers?</span>
        <i class="fas fa-chevron-down faq-icon"></i>
    </button>
    <div class="faq-answer">
        <p>Fix Lanka features only verified and trusted service providers. You can browse through our featured providers on the landing page, or use the search filters to find professionals by service type, rating, and location. Each provider's profile displays their ratings, reviews, and service area to help you make an informed decision.</p>
    </div>
</div>

<div class="faq-item">
    <button class="faq-question">
        <span class="question-text">Can I edit or cancel my job request?</span>
        <i class="fas fa-chevron-down faq-icon"></i>
    </button>
    <div class="faq-answer">
        <p>Yes! You can edit or cancel your job request as long as it's still in "Pending" status. Simply go to "My Jobs" from the navigation menu, find your job request, and use the Edit or Delete buttons. Once a job is in progress or completed, it becomes read-only and cannot be modified or cancelled.</p>
    </div>
</div>

<div class="faq-item">
    <button class="faq-question">
        <span class="question-text">What types of services are available on Fix Lanka?</span>
        <i class="fas fa-chevron-down faq-icon"></i>
    </button>
    <div class="faq-answer">
        <p>Fix Lanka offers a wide range of services including Plumbing, Electrical work, HVAC services, Cleaning, Carpentry, Painting, and many more. Our platform connects you with both individual professionals and registered companies specializing in various home and business services across Sri Lanka.</p>
    </div>
</div>
HTML
        ],
        'help' => [
            'Help Center',
            'Help center content and troubleshooting guidance.',
            <<<HTML
<p>Find answers to frequently asked questions about Fix Lanka, job requests, providers, and platform usage.</p>
HTML
        ],
        'terms' => [
            'Terms of Service',
            'Legal terms and conditions for platform usage.',
            <<<HTML
<div class="terms-section">
    <h2 class="section-title">1. Acceptance of Terms</h2>
    <p>By accessing and using Fix Lanka's platform, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service. If you do not agree with any part of these terms, please do not use our services.</p>
</div>

<div class="terms-section">
    <h2 class="section-title">2. User Accounts</h2>
    <p>To access certain features of Fix Lanka, you must create an account. You are responsible for:</p>
    <ul>
        <li>Maintaining the confidentiality of your account credentials</li>
        <li>All activities that occur under your account</li>
        <li>Providing accurate and current information</li>
        <li>Notifying us immediately of any unauthorized access</li>
    </ul>
</div>

<div class="terms-section">
    <h2 class="section-title">3. Service Provider Responsibilities</h2>
    <p>Service providers using Fix Lanka agree to:</p>
    <ul>
        <li>Provide accurate information about their services and qualifications</li>
        <li>Maintain professional conduct with all customers</li>
        <li>Complete agreed-upon work in a timely and professional manner</li>
        <li>Comply with all applicable local laws and regulations</li>
        <li>Maintain necessary licenses and insurance as required by law</li>
    </ul>
</div>

<div class="terms-section">
    <h2 class="section-title">4. Customer Responsibilities</h2>
    <p>Customers using Fix Lanka agree to:</p>
    <ul>
        <li>Provide accurate job descriptions and requirements</li>
        <li>Treat service providers with respect and professionalism</li>
        <li>Provide safe access to work areas as needed</li>
        <li>Pay agreed-upon rates for completed services</li>
        <li>Provide honest and fair reviews of services received</li>
    </ul>
</div>

<div class="terms-section">
    <h2 class="section-title">5. Platform Usage</h2>
    <p>Users of Fix Lanka must not:</p>
    <ul>
        <li>Post false, misleading, or fraudulent information</li>
        <li>Harass, threaten, or abuse other users</li>
        <li>Violate any applicable laws or regulations</li>
        <li>Attempt to circumvent platform fees or policies</li>
        <li>Use the platform for any illegal or unauthorized purpose</li>
        <li>Interfere with the proper functioning of the platform</li>
    </ul>
</div>

<div class="terms-section">
    <h2 class="section-title">6. Payment and Fees</h2>
    <p>Fix Lanka may charge service fees for using the platform. All fees are non-refundable unless otherwise stated. Payment terms are agreed upon between customers and service providers directly.</p>
</div>

<div class="terms-section">
    <h2 class="section-title">7. Limitation of Liability</h2>
    <p>Fix Lanka acts as a platform connecting customers with service providers. We do not employ service providers and are not responsible for the quality, safety, or legality of services provided. Users agree to hold Fix Lanka harmless from any disputes arising from service arrangements.</p>
</div>

<div class="terms-section">
    <h2 class="section-title">8. Dispute Resolution</h2>
    <p>In the event of disputes between users, Fix Lanka may provide mediation services but is not obligated to do so. Users are encouraged to resolve disputes amicably and may seek legal remedies as appropriate.</p>
</div>

<div class="terms-section">
    <h2 class="section-title">10. Modifications to Terms</h2>
    <p>Fix Lanka reserves the right to modify these Terms of Service at any time. Users will be notified of significant changes via email or platform notifications. Continued use of the platform after changes constitutes acceptance of the modified terms.</p>
</div>

<div class="terms-section">
    <h2 class="section-title">11. Termination</h2>
    <p>Fix Lanka reserves the right to suspend or terminate accounts that violate these Terms of Service or engage in fraudulent, abusive, or illegal activities. Users may also close their accounts at any time.</p>
</div>

<div class="terms-section">
    <h2 class="section-title">12. Contact Information</h2>
    <p>For questions about these Terms of Service, please contact us:</p>
    <div class="contact-box">
        <p><i class="fas fa-envelope"></i> Email: support@fixlanka.lk</p>
        <p><i class="fas fa-phone"></i> Phone: +94 11 234 5678</p>
    </div>
</div>
HTML
        ],
        'privacy' => [
            'Privacy Policy',
            'How user data is collected and protected.',
            <<<HTML
<div class="terms-section">
    <h2 class="section-title">Privacy and Data Protection</h2>
    <p>Your privacy is important to us. We collect and use personal information in accordance with applicable data protection laws. By using Fix Lanka, you consent to our collection and use of personal information as described in this Privacy Policy.</p>
    <p>We may collect account details, contact information, service requests, and communication metadata to operate and improve the platform. We do not sell your personal data.</p>
    <p>If you have questions about how your data is handled, please contact our support team.</p>
</div>
HTML
        ],
        'about' => [
            'About Us',
            'Company story, mission, and platform purpose.',
            <<<HTML
<p>Fix Lanka was established to bridge the gap between customers and quality service providers in Sri Lanka. Our mission is to make finding trusted professionals simple, fast, and reliable.</p>
HTML
        ],
        'how_it_works' => [
            'How It Works',
            'Step-by-step explanation of platform workflow.',
            <<<HTML
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

<div class="steps-section">
    <h2 class="section-title">Getting Started is Easy</h2>
    <div class="steps-grid">
        <div class="step-card">
            <div class="step-number">1</div>
            <div class="step-icon"><i class="fas fa-user-plus"></i></div>
            <h3 class="step-title">Create Your Account</h3>
            <p class="step-description">Sign up for free in just a few minutes. Provide basic information to create your profile and start exploring our network of professional service providers.</p>
        </div>
        <div class="step-card">
            <div class="step-number">2</div>
            <div class="step-icon"><i class="fas fa-clipboard-list"></i></div>
            <h3 class="step-title">Post Your Job Request</h3>
            <p class="step-description">Describe your service needs in detail. Include the type of service, location, timeline, and any specific requirements. You can also upload photos to help service providers understand your needs better.</p>
        </div>
        <div class="step-card">
            <div class="step-number">3</div>
            <div class="step-icon"><i class="fas fa-search"></i></div>
            <h3 class="step-title">Browse &amp; Compare</h3>
            <p class="step-description">Receive quotes from multiple verified service providers. Review their profiles, ratings, past work, and customer reviews. Compare prices and service offerings to make an informed decision.</p>
        </div>
        <div class="step-card">
            <div class="step-number">4</div>
            <div class="step-icon"><i class="fas fa-handshake"></i></div>
            <h3 class="step-title">Choose Your Provider</h3>
            <p class="step-description">Select the service provider that best fits your needs and budget. Contact them directly through our platform to discuss details, schedule the service, and confirm the booking.</p>
        </div>
        <div class="step-card">
            <div class="step-number">5</div>
            <div class="step-icon"><i class="fas fa-tools"></i></div>
            <h3 class="step-title">Get Service Done</h3>
            <p class="step-description">The service provider completes the work according to your agreement. Track the progress through our platform and communicate directly with your provider for any updates or changes.</p>
        </div>
        <div class="step-card">
            <div class="step-number">6</div>
            <div class="step-icon"><i class="fas fa-star"></i></div>
            <h3 class="step-title">Rate &amp; Review</h3>
            <p class="step-description">Once the job is complete, rate your experience and leave a review. Your feedback helps maintain service quality and assists other customers in making informed decisions.</p>
        </div>
    </div>
</div>

<div class="providers-section">
    <div class="providers-content">
        <h2 class="providers-title"><i class="fas fa-briefcase"></i> Are You a Service Provider?</h2>
        <p class="providers-text">Join Fix Lanka's growing network of professional service providers. Reach thousands of potential customers, grow your business, and manage your bookings efficiently through our platform.</p>
        <div class="providers-benefits">
            <div class="benefit-item"><i class="fas fa-users"></i><span>Access to Customers</span></div>
            <div class="benefit-item"><i class="fas fa-calendar-check"></i><span>Manage Bookings</span></div>
            <div class="benefit-item"><i class="fas fa-chart-line"></i><span>Grow Your Business</span></div>
            <div class="benefit-item"><i class="fas fa-shield-alt"></i><span>Verified Badge</span></div>
        </div>
    </div>
</div>
HTML
        ],
        'services' => [
            'Services',
            'Overview of available service categories.',
            <<<HTML
<div class="service-card">
    <div class="service-icon plumbing"><i class="fas fa-faucet"></i></div>
    <h3 class="service-title">Plumbing</h3>
    <p class="service-description">Expert plumbing services including pipe repairs, leak fixes, drain cleaning, water heater installation, and bathroom/kitchen plumbing solutions.</p>
    <ul class="service-features">
        <li><i class="fas fa-check"></i> Emergency repairs</li>
        <li><i class="fas fa-check"></i> Pipe installation</li>
        <li><i class="fas fa-check"></i> Leak detection</li>
        <li><i class="fas fa-check"></i> Drain cleaning</li>
    </ul>
</div>

<div class="service-card">
    <div class="service-icon electrical"><i class="fas fa-bolt"></i></div>
    <h3 class="service-title">Electrical</h3>
    <p class="service-description">Professional electrical services including wiring, lighting installation, circuit repairs, electrical panel upgrades, and safety inspections.</p>
    <ul class="service-features">
        <li><i class="fas fa-check"></i> Wiring &amp; rewiring</li>
        <li><i class="fas fa-check"></i> Lighting installation</li>
        <li><i class="fas fa-check"></i> Panel upgrades</li>
        <li><i class="fas fa-check"></i> Safety inspections</li>
    </ul>
</div>

<div class="service-card">
    <div class="service-icon hvac"><i class="fas fa-wind"></i></div>
    <h3 class="service-title">HVAC</h3>
    <p class="service-description">Complete HVAC services including air conditioning installation, heating system repairs, ventilation solutions, and regular maintenance.</p>
    <ul class="service-features">
        <li><i class="fas fa-check"></i> AC installation</li>
        <li><i class="fas fa-check"></i> Heating repairs</li>
        <li><i class="fas fa-check"></i> Ventilation</li>
        <li><i class="fas fa-check"></i> Regular maintenance</li>
    </ul>
</div>

<div class="service-card">
    <div class="service-icon cleaning"><i class="fas fa-broom"></i></div>
    <h3 class="service-title">Cleaning</h3>
    <p class="service-description">Professional cleaning services for homes and offices including deep cleaning, regular maintenance, carpet cleaning, and specialized sanitization.</p>
    <ul class="service-features">
        <li><i class="fas fa-check"></i> Deep cleaning</li>
        <li><i class="fas fa-check"></i> Regular maintenance</li>
        <li><i class="fas fa-check"></i> Carpet cleaning</li>
        <li><i class="fas fa-check"></i> Sanitization</li>
    </ul>
</div>

<div class="service-card">
    <div class="service-icon carpentry"><i class="fas fa-hammer"></i></div>
    <h3 class="service-title">Carpentry</h3>
    <p class="service-description">Skilled carpentry services including custom furniture, cabinet installation, door and window repairs, and wooden structure construction.</p>
    <ul class="service-features">
        <li><i class="fas fa-check"></i> Custom furniture</li>
        <li><i class="fas fa-check"></i> Cabinet installation</li>
        <li><i class="fas fa-check"></i> Door &amp; window repairs</li>
        <li><i class="fas fa-check"></i> Wood structures</li>
    </ul>
</div>

<div class="service-card">
    <div class="service-icon painting"><i class="fas fa-paint-roller"></i></div>
    <h3 class="service-title">Painting</h3>
    <p class="service-description">Professional painting services for interior and exterior spaces including wall preparation, color consultation, and specialty finishes.</p>
    <ul class="service-features">
        <li><i class="fas fa-check"></i> Interior painting</li>
        <li><i class="fas fa-check"></i> Exterior painting</li>
        <li><i class="fas fa-check"></i> Color consultation</li>
        <li><i class="fas fa-check"></i> Specialty finishes</li>
    </ul>
</div>
HTML
        ],
        'why_choose' => [
            'Why Choose Fix Lanka?',
            'Trust and value proposition section.',
            <<<HTML
<div class="features-grid">
    <div class="feature-item">
        <div class="feature-icon"><i class="fas fa-user-shield"></i></div>
        <h4>Verified Professionals</h4>
        <p>All service providers are thoroughly vetted and verified for quality assurance</p>
    </div>
    <div class="feature-item">
        <div class="feature-icon"><i class="fas fa-clock"></i></div>
        <h4>24/7 Availability</h4>
        <p>Round-the-clock support and emergency services when you need them most</p>
    </div>
    <div class="feature-item">
        <div class="feature-icon"><i class="fas fa-star"></i></div>
        <h4>Quality Guaranteed</h4>
        <p>Satisfaction guaranteed with our quality assurance and service standards</p>
    </div>
    <div class="feature-item">
        <div class="feature-icon"><i class="fas fa-money-bill-wave"></i></div>
        <h4>Competitive Pricing</h4>
        <p>Transparent pricing with no hidden charges and competitive rates</p>
    </div>
</div>
HTML
        ],
        'contact' => [
            'Contact Us',
            'Support contact details and communication channels.',
            <<<HTML
<div class="info-card">
    <div class="info-icon"><i class="fas fa-envelope"></i></div>
    <h3>Email Us</h3>
    <p>support@fixlanka.lk</p>
    <small>We'll respond within 24 hours</small>
</div>

<div class="info-card">
    <div class="info-icon"><i class="fas fa-phone"></i></div>
    <h3>Call Us</h3>
    <p>+94 11 234 5678</p>
    <small>Mon-Fri, 9AM - 6PM</small>
</div>

<div class="info-card">
    <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
    <h3>Visit Us</h3>
    <p>Colombo, Sri Lanka</p>
    <small>Main Office</small>
</div>
HTML
        ],
        'support' => [
            'Support',
            'Support page guidance and help resources.',
            <<<HTML
<div class="quick-links-section">
    <h2 class="section-title">Quick Access</h2>
    <div class="quick-links-grid">
        <a href="/2nd-Year-Group-Project/FixLanka/views/user/help-center.php" class="quick-link-card">
            <div class="link-icon help"><i class="fas fa-question-circle"></i></div>
            <h3>Help Center</h3>
            <p>Browse frequently asked questions and find answers</p>
        </a>
        <a href="/2nd-Year-Group-Project/FixLanka/views/user/contact-us.php" class="quick-link-card">
            <div class="link-icon contact"><i class="fas fa-envelope"></i></div>
            <h3>Contact Us</h3>
            <p>Get in touch with our support team directly</p>
        </a>
        <a href="/2nd-Year-Group-Project/FixLanka/views/user/terms-of-service.php" class="quick-link-card">
            <div class="link-icon terms"><i class="fas fa-file-contract"></i></div>
            <h3>Terms of Service</h3>
            <p>Read our terms and conditions for using Fix Lanka</p>
        </a>
    </div>
</div>

<div class="contact-methods-section">
    <h2 class="section-title">Get in Touch</h2>
    <div class="contact-grid">
        <div class="contact-method">
            <div class="method-icon email"><i class="fas fa-envelope"></i></div>
            <h3>Email Support</h3>
            <p class="method-value">support@fixlanka.lk</p>
            <p class="method-description">Response within 24 hours</p>
        </div>
        <div class="contact-method">
            <div class="method-icon phone"><i class="fas fa-phone-alt"></i></div>
            <h3>Phone Support</h3>
            <p class="method-value">+94 11 234 5678</p>
            <p class="method-description">Mon - Fri, 9:00 AM - 6:00 PM</p>
        </div>
        <div class="contact-method">
            <div class="method-icon location"><i class="fas fa-map-marker-alt"></i></div>
            <h3>Office Location</h3>
            <p class="method-value">Colombo, Sri Lanka</p>
            <p class="method-description">Visit us for in-person support</p>
        </div>
    </div>
</div>

<div class="support-topics-section">
    <h2 class="section-title">Popular Topics</h2>
    <div class="topics-grid">
        <div class="topic-card">
            <div class="topic-icon"><i class="fas fa-user-circle"></i></div>
            <h4>Account Management</h4>
            <ul class="topic-list">
                <li>Creating an account</li>
                <li>Profile settings</li>
                <li>Password recovery</li>
                <li>Account verification</li>
            </ul>
        </div>
        <div class="topic-card">
            <div class="topic-icon"><i class="fas fa-tasks"></i></div>
            <h4>Job Requests</h4>
            <ul class="topic-list">
                <li>Posting a job</li>
                <li>Editing requests</li>
                <li>Cancellation policy</li>
                <li>Request tracking</li>
            </ul>
        </div>
        <div class="topic-card">
            <div class="topic-icon"><i class="fas fa-credit-card"></i></div>
            <h4>Payments &amp; Billing</h4>
            <ul class="topic-list">
                <li>Payment methods</li>
                <li>Pricing information</li>
                <li>Refund policy</li>
                <li>Invoice queries</li>
            </ul>
        </div>
        <div class="topic-card">
            <div class="topic-icon"><i class="fas fa-shield-alt"></i></div>
            <h4>Safety &amp; Security</h4>
            <ul class="topic-list">
                <li>Verified providers</li>
                <li>Data protection</li>
                <li>Reporting issues</li>
                <li>Trust &amp; safety</li>
            </ul>
        </div>
    </div>
</div>

<div class="emergency-banner">
    <div class="emergency-content">
        <div class="emergency-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="emergency-text">
            <h3>Need Urgent Help?</h3>
            <p>For emergency support or urgent issues, please call our hotline immediately</p>
        </div>
        <a href="tel:+94112345678" class="emergency-button"><i class="fas fa-phone"></i> Call Now</a>
    </div>
</div>
HTML
        ],
    ];

    $legacyBodies = [
        'landing_hero' => [],
        'faq' => ["Add and maintain frequently asked questions here."],
        'terms' => ["Define legal terms and usage conditions here."],
        'privacy' => ["Describe privacy and data handling policies here."],
        'about' => ["Share your company background and mission here."],
        'how_it_works' => ["Explain how users and providers use the platform."],
        'services' => ["Describe all service categories available in FixLanka."],
        'why_choose' => ["Highlight reasons to choose FixLanka."],
        'contact' => ["Maintain phone, email, and support contact details."],
        'support' => ["Maintain support process and guidance content."],
        'help' => ["Maintain help center introduction and guidance."],
    ];

    $selectStmt = $pdo->prepare("SELECT content_id, title, COALESCE(description,'') AS description, body FROM staticcontent WHERE content_type = :t LIMIT 1");
    $insertStmt = $pdo->prepare(
        "INSERT INTO staticcontent (title, description, body, status, content_type)
         VALUES (:title, :description, :body, 'Published', :t)"
    );
    $updateStmt = $pdo->prepare(
        "UPDATE staticcontent
         SET title = :title,
             description = :description,
             body = :body
         WHERE content_type = :t"
    );

    foreach ($defaults as $contentType => $data) {
        [$title, $description, $body] = $data;

        $selectStmt->execute([':t' => $contentType]);
        $existing = $selectStmt->fetch(PDO::FETCH_ASSOC);

        if (!$existing) {
            $insertStmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':body' => $body,
                ':t' => $contentType,
            ]);
            continue;
        }

        $existingBody = (string)($existing['body'] ?? '');
        $legacyList = $legacyBodies[$contentType] ?? [];

        if ($existingBody === '' || in_array($existingBody, $legacyList, true)) {
            $updateStmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':body' => $body,
                ':t' => $contentType,
            ]);
        }
    }
}

$sectionPageMap = [
    'landing_hero' => '/2nd-Year-Group-Project/FixLanka/views/user/landing.php#hero',
    'faq' => '/2nd-Year-Group-Project/FixLanka/help-center#faq',
    'terms' => '/2nd-Year-Group-Project/FixLanka/views/user/terms-of-service.php',
    'privacy' => '/2nd-Year-Group-Project/FixLanka/views/user/terms-of-service.php#privacy',
    'about' => '/2nd-Year-Group-Project/FixLanka/views/user/about-us.php',
    'how_it_works' => '/2nd-Year-Group-Project/FixLanka/views/user/how-it-works.php#how-it-works-content',
    'services' => '/2nd-Year-Group-Project/FixLanka/views/user/services.php#services',
    'why_choose' => '/2nd-Year-Group-Project/FixLanka/views/user/services.php#why-choose',
    'contact' => '/2nd-Year-Group-Project/FixLanka/views/user/contact-us.php#contact-info',
    'support' => '/2nd-Year-Group-Project/FixLanka/views/user/support.php#support-content',
    'help' => '/2nd-Year-Group-Project/FixLanka/help-center',
];

$successMessage = trim((string)($_GET['success'] ?? ''));
$errorMessage = trim((string)($_GET['error'] ?? ''));

try {
    ensureStaticContentSchema($pdo);
    ensureManagedSections($pdo);

    if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'POST') {
        $action = strtolower(trim((string)($_POST['action'] ?? '')));
        $contentId = (int)($_POST['content_id'] ?? 0);

        if ($contentId <= 0) {
            header('Location: /2nd-Year-Group-Project/FixLanka/moderator-static-content?error=' . urlencode('Invalid content selected.'));
            exit();
        }

        if ($action === 'update') {
            $title = trim((string)($_POST['title'] ?? ''));
            $description = trim((string)($_POST['description'] ?? ''));
            $body = trim((string)($_POST['body'] ?? ''));
            $status = trim((string)($_POST['status'] ?? 'Draft'));

            if ($title === '' || $body === '') {
                header('Location: /2nd-Year-Group-Project/FixLanka/moderator-static-content?error=' . urlencode('Title and content body are required.'));
                exit();
            }

            if (!in_array($status, ['Draft', 'Published'], true)) {
                $status = 'Draft';
            }

            $stmt = $pdo->prepare(
                "UPDATE `staticcontent`
                 SET `title` = :title,
                     `description` = :description,
                     `body` = :body,
                     `status` = :status,
                     `last_update` = CURRENT_TIMESTAMP
                 WHERE `content_id` = :content_id"
            );

            $stmt->execute([
                ':title' => $title,
                ':description' => ($description !== '' ? $description : null),
                ':body' => $body,
                ':status' => $status,
                ':content_id' => $contentId,
            ]);

            header('Location: /2nd-Year-Group-Project/FixLanka/moderator-static-content?success=' . urlencode('Content updated successfully.'));
            exit();
        }

        header('Location: /2nd-Year-Group-Project/FixLanka/moderator-static-content?error=' . urlencode('Unsupported action.'));
        exit();
    }

    $managedTypes = array_keys($sectionPageMap);
    $placeholders = implode(',', array_fill(0, count($managedTypes), '?'));

    $stmt = $pdo->prepare(
        "SELECT content_id, title, COALESCE(description, '') AS description, body,
                COALESCE(status, 'Draft') AS status, last_update, content_type
         FROM `staticcontent`
         WHERE `content_type` IN ({$placeholders})
         ORDER BY FIELD(content_type, 'faq','terms','privacy','about','how_it_works','services','why_choose','contact','support','help'), content_id ASC"
    );
    $stmt->execute($managedTypes);
    $contents = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (Throwable $e) {
    error_log('Moderator static content error: ' . $e->getMessage());
    $contents = [];
    if ($errorMessage === '') {
        $errorMessage = 'Failed to load static content data.';
    }
}

$totalSections = count($contents);
$publishedCount = count(array_filter($contents, static fn($item) => ($item['status'] ?? 'Draft') === 'Published'));
$draftCount = $totalSections - $publishedCount;
$lastUpdate = '';
foreach ($contents as $row) {
    $rowTime = (string)($row['last_update'] ?? '');
    if ($rowTime !== '' && ($lastUpdate === '' || strtotime($rowTime) > strtotime($lastUpdate))) {
        $lastUpdate = $rowTime;
    }
}

function staticContentShortTargetLabel(string $url): string
{
    $clean = trim($url);
    if ($clean === '') {
        return 'N/A';
    }

    $prefixes = [
        '/2nd-Year-Group-Project/FixLanka/views/user/',
        '/2nd-Year-Group-Project/FixLanka/',
    ];

    foreach ($prefixes as $prefix) {
        if (strpos($clean, $prefix) === 0) {
            $clean = substr($clean, strlen($prefix));
            break;
        }
    }

    return ltrim($clean, '/');
}

function staticContentPreviewText(string $contentType, string $body): string
{
    $trimmed = trim($body);
    if ($trimmed === '') {
        return 'No content body yet.';
    }

    if ($contentType === 'landing_hero') {
        $decoded = json_decode($trimmed, true);
        if (is_array($decoded)) {
            $title = trim((string)($decoded['title'] ?? ''));
            $subtitle = trim((string)($decoded['subtitle'] ?? ''));
            if ($title !== '' || $subtitle !== '') {
                return "Title: {$title}\nSubtitle: {$subtitle}";
            }
        }
    }

    $plain = trim(html_entity_decode(strip_tags($trimmed), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    if ($plain === '') {
        return 'Rich content body available. Open editor to review.';
    }

    if (mb_strlen($plain) > 240) {
        return mb_substr($plain, 0, 240) . '...';
    }

    return $plain;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath); ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/moderator/dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/moderator/static-content.css?v=<?php echo time(); ?>">
</head>
<body class="bg-foreground text-background">
<input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

<div class="dashboard-container">
    <?php renderModeratorSidebar($currentPath, $basePath); ?>

    <div class="dashboard-main">
        <?php renderPageHeader($basePath, 'Static Content Management', 'Manage FAQ, Terms, Privacy, About, Contact, Services and support sections'); ?>

        <main class="dashboard-content">
            <div class="space-y-6">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-foreground">Static Content Management</h2>
                    <p class="text-muted-foreground">Database-backed management for website static sections (edit + save only)</p>
                </div>

                <?php if ($successMessage !== ''): ?>
                    <div class="alert alert-success"><i data-lucide="check-circle"></i><?php echo htmlspecialchars($successMessage); ?></div>
                <?php endif; ?>

                <?php if ($errorMessage !== ''): ?>
                    <div class="alert alert-error"><i data-lucide="alert-triangle"></i><?php echo htmlspecialchars($errorMessage); ?></div>
                <?php endif; ?>

                <div class="grid gap-4 md-grid-cols-4">
                    <div class="bg-card rounded-lg border p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Total Sections</p>
                                <p class="text-2xl font-bold mt-2"><?php echo $totalSections; ?></p>
                                <p class="text-xs text-muted-foreground mt-1">Managed static sections</p>
                            </div>
                            <i data-lucide="file-text" class="h-8 w-8 text-blue-600"></i>
                        </div>
                    </div>
                    <div class="bg-card rounded-lg border p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Saved</p>
                                <p class="text-2xl font-bold mt-2"><?php echo $publishedCount; ?></p>
                                <p class="text-xs text-muted-foreground mt-1">Customer-visible content</p>
                            </div>
                            <i data-lucide="check-circle" class="h-8 w-8 text-green-600"></i>
                        </div>
                    </div>
                    <div class="bg-card rounded-lg border p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Drafts</p>
                                <p class="text-2xl font-bold mt-2"><?php echo $draftCount; ?></p>
                                <p class="text-xs text-muted-foreground mt-1">Draft sections</p>
                            </div>
                            <i data-lucide="edit" class="h-8 w-8 text-orange-600"></i>
                        </div>
                    </div>
                    <div class="bg-card rounded-lg border p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Last Updated</p>
                                <p class="text-lg font-bold mt-2"><?php echo $lastUpdate !== '' ? date('M d, Y', strtotime($lastUpdate)) : 'Never'; ?></p>
                                <p class="text-xs text-muted-foreground mt-1">Most recent content change</p>
                            </div>
                            <i data-lucide="clock" class="h-8 w-8 text-purple-600"></i>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border bg-card" style="margin-bottom: 4rem;">
                    <div class="p-6 border-b">
                        <h3 class="text-lg font-medium text-foreground">Content Sections</h3>
                        <p class="text-sm text-muted-foreground">Manage only existing core sections. No add/delete from UI.</p>
                    </div>

                    <?php
                        $initialPreviewUrl = '';
                        if (!empty($contents)) {
                            $firstType = (string)($contents[0]['content_type'] ?? '');
                            $initialPreviewUrl = (string)($sectionPageMap[$firstType] ?? '');
                        }
                    ?>

                    <div class="static-content-split">
                        <div class="static-content-list divide-y">
                        <?php if (empty($contents)): ?>
                            <div class="p-8 text-center">
                                <i data-lucide="inbox" class="h-16 w-16 text-muted-foreground mx-auto mb-4"></i>
                                <p class="text-muted-foreground">No static sections were found.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($contents as $item): ?>
                                <?php $isPublished = ($item['status'] ?? 'Draft') === 'Published'; ?>
                                <?php $targetUrl = (string)($sectionPageMap[$item['content_type']] ?? ''); ?>
                                <?php $targetLabel = staticContentShortTargetLabel($targetUrl); ?>
                                <?php $previewText = staticContentPreviewText((string)($item['content_type'] ?? ''), (string)($item['body'] ?? '')); ?>
                                <div class="p-6 flex items-start justify-between gap-3 static-content-row"
                                     data-content-type="<?php echo htmlspecialchars((string)$item['content_type']); ?>"
                                     data-preview-url="<?php echo htmlspecialchars($targetUrl); ?>"
                                     onclick="selectPreviewFromRow(this)">
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between gap-3 mb-3">
                                            <div>
                                                <h4 class="text-lg font-semibold text-foreground"><?php echo htmlspecialchars($item['title']); ?></h4>
                                                <p class="text-sm text-muted-foreground"><?php echo htmlspecialchars($item['description'] ?? ''); ?></p>
                                                <div class="static-target" style="margin-top: 6px;">
                                                    <span class="text-xs text-muted-foreground">Target page:</span>
                                                    <a class="static-target-link" href="<?php echo htmlspecialchars($targetUrl !== '' ? $targetUrl : '#'); ?>" target="_blank"><?php echo htmlspecialchars($targetLabel); ?></a>
                                                </div>
                                            </div>
                                            <span class="badge <?php echo $isPublished ? 'badge-default' : 'badge-secondary'; ?>"><?php echo $isPublished ? 'Saved' : 'Draft'; ?></span>
                                        </div>

                                        <div class="content-preview">
                                            <p><?php echo nl2br(htmlspecialchars($previewText)); ?></p>
                                        </div>
                                        <p class="text-xs text-muted-foreground">Last updated: <?php echo !empty($item['last_update']) ? date('Y-m-d H:i', strtotime((string)$item['last_update'])) : 'N/A'; ?></p>
                                    </div>

                                    <div class="content-actions flex gap-2">
                                        <button
                                            type="button"
                                            class="btn btn-secondary"
                                            onclick='event.stopPropagation(); openEditModal(<?php echo json_encode([
                                                "content_id" => (int)$item["content_id"],
                                                "title" => (string)$item["title"],
                                                "description" => (string)($item["description"] ?? ""),
                                                "body" => (string)$item["body"],
                                                "status" => (string)$item["status"],
                                                "content_type" => (string)$item["content_type"],
                                            ], JSON_HEX_QUOT | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE); ?>)'>
                                            <i data-lucide="edit" class="h-4 w-4"></i>
                                            Edit
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </div>

                        <aside class="static-content-preview">
                            <div class="static-content-preview-header">
                                <div class="preview-title">Page Preview</div>
                                <div class="preview-controls">
                                    <label for="previewMode" class="preview-label">View</label>
                                    <select id="previewMode" class="form-select" onchange="reloadPreview()">
                                        <option value="published">Customers (Saved)</option>
                                        <option value="draft">Moderator Draft</option>
                                    </select>
                                    <a id="openPreviewInNewTab" href="#" target="_blank" class="btn btn-secondary" style="text-decoration:none;">
                                        <i data-lucide="external-link" class="h-4 w-4"></i>
                                        Open
                                    </a>
                                </div>
                            </div>

                            <iframe
                                id="staticContentPreviewFrame"
                                class="static-content-preview-iframe"
                                src="<?php echo htmlspecialchars($initialPreviewUrl); ?>"
                                title="Static content preview"></iframe>
                        </aside>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<div id="editModal" class="modal-overlay">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i data-lucide="edit" class="h-5 w-5 mr-2"></i>
                    Edit Static Section
                </h3>
                <button type="button" onclick="closeEditModal()" class="modal-close">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <form method="POST" action="/2nd-Year-Group-Project/FixLanka/moderator-static-content">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="content_id" id="editContentId">

                    <div class="sc-form-section">
                        <div class="sc-form-section-title">Basics</div>

                        <div class="form-group">
                            <label class="form-label">Section Type</label>
                            <input type="text" id="editType" class="form-input" readonly>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" id="editTitle" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <input type="text" name="description" id="editDescription" class="form-input">
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Status</label>
                            <select name="status" id="editStatus" class="form-select">
                                <option value="Draft">Draft</option>
                                <option value="Published">Saved</option>
                            </select>
                        </div>
                    </div>

                    <div class="sc-form-section">
                        <div class="sc-form-section-title">Content</div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Body</label>

                            <div id="templateLandingHero" class="template-editor" style="display:none;">
                                <div class="template-editor-title">Landing Header</div>
                                <div class="template-grid">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label class="form-label">Hero Title</label>
                                        <input type="text" id="landingHeroTitle" class="form-input" placeholder="Enter hero title">
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label class="form-label">Hero Subtitle</label>
                                        <input type="text" id="landingHeroSubtitle" class="form-input" placeholder="Enter hero subtitle">
                                    </div>
                                </div>
                            </div>

                            <div id="templateFaq" class="template-editor" style="display:none;">
                                <div class="template-editor-title">FAQ Builder</div>
                                <div id="faqItems" class="template-faq-items"></div>
                                <button type="button" class="btn btn-secondary" onclick="addFaqItem()" style="margin-top: 10px;">
                                    <i data-lucide="plus" class="h-4 w-4"></i>
                                    Add Question
                                </button>
                            </div>

                            <div id="genericEditor" class="sc-generic-editor">
                                <div class="sc-editor-hint">
                                    Tip: Use <strong>=== SECTION TITLE ===</strong> for headings, <strong>-</strong> for bullet points, and blank lines for paragraphs.
                                </div>
                                <textarea name="body" id="editBody" rows="14" class="form-textarea sc-editor-textarea" required></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="sc-form-section">
                        <div class="sc-form-section-title">Preview</div>
                        <div id="editInlinePreview" class="sc-inline-preview"></div>
                        <iframe
                            id="editInlinePreviewFrame"
                            class="sc-inline-preview-iframe"
                            sandbox
                            title="Inline section preview"
                            style="display:none;"></iframe>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" class="h-4 w-4 mr-1"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function escapeHtml(text) {
        return String(text ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function parseFaqFromHtml(html) {
        try {
            const doc = new DOMParser().parseFromString(String(html || ''), 'text/html');
            const nodes = Array.from(doc.querySelectorAll('.faq-item'));
            return nodes
                .map((n) => {
                    const q = (n.querySelector('.question-text')?.textContent || '').trim();
                    const a = (n.querySelector('.faq-answer')?.textContent || '').trim();
                    return { q, a };
                })
                .filter((it) => it.q || it.a);
        } catch {
            return [];
        }
    }

    function parseFaqFromQaText(text) {
        const lines = String(text || '').split(/\r\n|\r|\n/);
        const items = [];

        let q = null;
        let aLines = [];
        const flush = () => {
            if (q === null) {
                aLines = [];
                return;
            }
            const a = aLines.join('\n').trim();
            items.push({ q: String(q).trim(), a });
            q = null;
            aLines = [];
        };

        for (const raw of lines) {
            const line = String(raw).trim();
            if (!line) {
                if (q !== null) aLines.push('');
                continue;
            }
            const qm = line.match(/^Q\s*:\s*(.+)$/i);
            if (qm) {
                flush();
                q = qm[1];
                continue;
            }
            const am = line.match(/^A\s*:\s*(.+)$/i);
            if (am) {
                if (q !== null) aLines.push(am[1]);
                continue;
            }
            if (q !== null) aLines.push(line);
        }
        flush();
        return items.filter((it) => it.q || it.a);
    }

    function buildFaqHtml(items) {
        return (items || [])
            .map((it) => {
                const q = escapeHtml(it.q || '');
                const a = escapeHtml(it.a || '').replace(/\n/g, '<br>');
                if (!q && !a) return '';
                return `\
<div class="faq-item">\
    <button class="faq-question">\
        <span class="question-text">${q}</span>\
        <i class="fas fa-chevron-down faq-icon"></i>\
    </button>\
    <div class="faq-answer">\
        <p>${a}</p>\
    </div>\
</div>`;
            })
            .filter(Boolean)
            .join("\n\n");
    }

    function showTemplateEditor(type) {
        const templateLandingHero = document.getElementById('templateLandingHero');
        const templateFaq = document.getElementById('templateFaq');
        const genericEditor = document.getElementById('genericEditor');

        templateLandingHero.style.display = 'none';
        templateFaq.style.display = 'none';
        genericEditor.style.display = 'block';

        if (type === 'landing_hero') {
            templateLandingHero.style.display = 'block';
            genericEditor.style.display = 'none';
        }

        if (type === 'faq') {
            templateFaq.style.display = 'block';
            genericEditor.style.display = 'none';
        }
    }

    function stripScripts(html) {
        return String(html ?? '').replace(/<\s*script\b[^>]*>[\s\S]*?<\s*\/\s*script\s*>/gi, '');
    }

    function looksLikeHtml(text) {
        return /<\s*(p|div|h1|h2|h3|h4|h5|h6|ul|ol|li|br|strong|em|b|i|a|table|thead|tbody|tr|td|th|section|article|header|footer|nav)\b/i.test(String(text || ''));
    }

    function renderPlainTextToStructuredHtml(text) {
        const lines = String(text || '').split(/\r\n|\r|\n/);
        let out = '<div class="static-content-body">';

        let sectionOpen = false;
        let para = [];
        let list = [];
        let listType = null; // 'ul' | 'ol' | null

        const openSectionIfNeeded = () => {
            if (!sectionOpen) {
                out += '<div class="static-content-section">';
                sectionOpen = true;
            }
        };

        const closeSection = () => {
            if (sectionOpen) {
                out += '</div>';
                sectionOpen = false;
            }
        };

        const flushParagraph = () => {
            const text = para.map((l) => String(l).trim()).filter(Boolean).join(' ').trim();
            if (!text) {
                para = [];
                return;
            }
            openSectionIfNeeded();
            out += '<p>' + escapeHtml(text) + '</p>';
            para = [];
        };

        const flushList = () => {
            if (!listType || !list.length) {
                list = [];
                listType = null;
                return;
            }
            openSectionIfNeeded();
            out += '<' + listType + '>';
            list.forEach((it) => {
                const v = String(it || '').trim();
                if (!v) return;
                out += '<li>' + escapeHtml(v) + '</li>';
            });
            out += '</' + listType + '>';
            list = [];
            listType = null;
        };

        const openNewSection = (title) => {
            flushParagraph();
            flushList();
            closeSection();
            sectionOpen = true;
            out += '<div class="static-content-section">';
            out += '<h2 class="section-title">' + escapeHtml(String(title || '').trim()) + '</h2>';
        };

        for (const raw of lines) {
            const line = String(raw ?? '').trim();
            if (!line) {
                flushParagraph();
                flushList();
                continue;
            }

            const heading = line.match(/^=+\s*(.+?)\s*=+$/);
            if (heading) {
                openNewSection(heading[1]);
                continue;
            }

            const bullet = line.match(/^[-*]\s+(.+)$/);
            if (bullet) {
                flushParagraph();
                if (listType && listType !== 'ul') flushList();
                listType = 'ul';
                list.push(bullet[1]);
                continue;
            }

            const numbered = line.match(/^\d+\.\s+(.+)$/);
            if (numbered) {
                flushParagraph();
                if (listType && listType !== 'ol') flushList();
                listType = 'ol';
                list.push(numbered[1]);
                continue;
            }

            flushList();
            para.push(line);
        }

        flushParagraph();
        flushList();
        closeSection();
        out += '</div>';
        return out;
    }

    function updateInlinePreview() {
        const type = String(document.getElementById('editType')?.value || '');
        const previewEl = document.getElementById('editInlinePreview');
        const previewFrame = document.getElementById('editInlinePreviewFrame');
        const textarea = document.getElementById('editBody');

        if (!previewEl || !previewFrame) return;

        previewEl.style.display = 'block';
        previewFrame.style.display = 'none';
        previewEl.innerHTML = '';

        if (type === 'landing_hero') {
            const title = (document.getElementById('landingHeroTitle')?.value || '').trim();
            const subtitle = (document.getElementById('landingHeroSubtitle')?.value || '').trim();
            previewEl.innerHTML = `
                <div class="sc-hero-preview">
                    <div class="sc-hero-title">${escapeHtml(title || 'Hero title')}</div>
                    <div class="sc-hero-subtitle">${escapeHtml(subtitle || 'Hero subtitle')}</div>
                </div>
            `;
            return;
        }

        if (type === 'faq') {
            const items = getFaqItemsFromEditor();
            const html = buildFaqHtml(items);
            previewEl.innerHTML = '<div class="sc-faq-preview">' + html + '</div>';
            return;
        }

        if (!textarea) return;
        const raw = String(textarea.value || '');
        const safe = stripScripts(raw);

        if (looksLikeHtml(safe)) {
            const srcdoc = `<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <style>
    body{font-family:-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Arial,sans-serif;background:#fff;margin:0;padding:14px;color:#0f172a;line-height:1.7}
    .static-content-body{color:#334155}
    .static-content-section{margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid #e2e8f0}
    .static-content-section:last-child{border-bottom:none;margin-bottom:0;padding-bottom:0}
    .section-title{color:#0f766e;font-size:18px;margin:0 0 10px 0;font-weight:800}
    p{margin:0 0 10px 0}
    ul,ol{margin:0 0 10px 20px}
  </style>
</head>
<body>${safe}</body>
</html>`;
            previewFrame.srcdoc = srcdoc;
            previewFrame.style.display = 'block';
            previewEl.style.display = 'none';
            return;
        }

        previewEl.innerHTML = renderPlainTextToStructuredHtml(safe);
    }

    function clearFaqItems() {
        const container = document.getElementById('faqItems');
        if (container) container.innerHTML = '';
    }

    function addFaqItem(initial = { q: '', a: '' }) {
        const container = document.getElementById('faqItems');
        if (!container) return;

        const idx = container.children.length;
        const item = document.createElement('div');
        item.className = 'template-faq-item';
        item.innerHTML = `
            <div class="template-faq-row">
                <label class="form-label" style="margin:0;">Question</label>
                <input type="text" class="form-input template-faq-q" value="${escapeHtml(initial.q)}" placeholder="Enter question">
            </div>
            <div class="template-faq-row">
                <label class="form-label" style="margin:0;">Answer</label>
                <textarea rows="3" class="form-textarea template-faq-a" placeholder="Enter answer">${escapeHtml(initial.a)}</textarea>
            </div>
            <div class="template-faq-actions">
                <button type="button" class="btn btn-secondary" onclick="removeFaqItem(this)">
                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                    Remove
                </button>
            </div>
        `;
        container.appendChild(item);
        lucide.createIcons();
        updateInlinePreview();
    }

    function removeFaqItem(btn) {
        const item = btn?.closest?.('.template-faq-item');
        if (item) item.remove();
        updateInlinePreview();
    }

    function getFaqItemsFromEditor() {
        const container = document.getElementById('faqItems');
        if (!container) return [];
        return Array.from(container.querySelectorAll('.template-faq-item')).map((el) => {
            const q = (el.querySelector('.template-faq-q')?.value || '').trim();
            const a = (el.querySelector('.template-faq-a')?.value || '').trim();
            return { q, a };
        }).filter((it) => it.q || it.a);
    }

    function buildPreviewUrl(rawUrl, mode) {
        const urlString = String(rawUrl || '');
        if (!urlString) return '';

        const hashIndex = urlString.indexOf('#');
        const base = hashIndex >= 0 ? urlString.slice(0, hashIndex) : urlString;
        const hash = hashIndex >= 0 ? urlString.slice(hashIndex) : '';

        const hasQuery = base.includes('?');
        const join = hasQuery ? '&' : '?';
        const qs = `sc_preview=1&sc_mode=${encodeURIComponent(mode || 'published')}`;
        return `${base}${join}${qs}${hash}`;
    }

    function setActiveRow(row) {
        document.querySelectorAll('.static-content-row.is-active').forEach((el) => el.classList.remove('is-active'));
        if (row) row.classList.add('is-active');
    }

    function selectPreviewFromRow(row) {
        if (!row) return;
        setActiveRow(row);

        const previewUrl = row.getAttribute('data-preview-url') || '';
        const mode = document.getElementById('previewMode')?.value || 'published';
        const finalUrl = buildPreviewUrl(previewUrl, mode);

        const frame = document.getElementById('staticContentPreviewFrame');
        if (frame && finalUrl) {
            frame.src = finalUrl;
        }

        const openLink = document.getElementById('openPreviewInNewTab');
        if (openLink) {
            openLink.href = finalUrl || '#';
        }
    }

    function reloadPreview() {
        const active = document.querySelector('.static-content-row.is-active') || document.querySelector('.static-content-row');
        if (active) {
            selectPreviewFromRow(active);
        }
    }

    function openEditModal(data) {
        const type = String(data.content_type || '');
        if (type) {
            const row = document.querySelector(`.static-content-row[data-content-type="${CSS.escape(type)}"]`);
            if (row) {
                selectPreviewFromRow(row);
            }
        }

        document.getElementById('editContentId').value = data.content_id || '';
        document.getElementById('editType').value = data.content_type || '';
        document.getElementById('editTitle').value = data.title || '';
        document.getElementById('editDescription').value = data.description || '';
        document.getElementById('editStatus').value = data.status || 'Draft';
        document.getElementById('editBody').value = data.body || '';

        showTemplateEditor(type);

        if (type === 'landing_hero') {
            let title = '';
            let subtitle = '';
            try {
                const decoded = JSON.parse(String(data.body || ''));
                if (decoded && typeof decoded === 'object') {
                    title = String(decoded.title || '');
                    subtitle = String(decoded.subtitle || '');
                }
            } catch {
            }
            document.getElementById('landingHeroTitle').value = title;
            document.getElementById('landingHeroSubtitle').value = subtitle;
        }

        if (type === 'faq') {
            clearFaqItems();
            const htmlItems = parseFaqFromHtml(String(data.body || ''));
            const items = htmlItems.length ? htmlItems : parseFaqFromQaText(String(data.body || ''));
            if (items.length) {
                items.forEach((it) => addFaqItem(it));
            } else {
                addFaqItem({ q: '', a: '' });
            }
        }

        updateInlinePreview();

        const modal = document.getElementById('editModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        lucide.createIcons();
    }

    document.getElementById('editBody')?.addEventListener('input', function () {
        updateInlinePreview();
    });

    document.getElementById('landingHeroTitle')?.addEventListener('input', function () {
        updateInlinePreview();
    });
    document.getElementById('landingHeroSubtitle')?.addEventListener('input', function () {
        updateInlinePreview();
    });
    document.getElementById('faqItems')?.addEventListener('input', function () {
        updateInlinePreview();
    });

    document.querySelector('#editModal form')?.addEventListener('submit', function (e) {
        const type = String(document.getElementById('editType')?.value || '');

        if (type === 'landing_hero') {
            const title = (document.getElementById('landingHeroTitle')?.value || '').trim();
            const subtitle = (document.getElementById('landingHeroSubtitle')?.value || '').trim();
            document.getElementById('editBody').value = JSON.stringify({ title, subtitle });
        }

        if (type === 'faq') {
            const items = getFaqItemsFromEditor();
            if (!items.length) {
                alert('Please add at least one FAQ item before saving.');
                e.preventDefault();
                return;
            }
            document.getElementById('editBody').value = buildFaqHtml(items);
        }
    });

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    document.getElementById('editModal').addEventListener('click', function (event) {
        if (event.target === this) {
            closeEditModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeEditModal();
        }
    });

    lucide.createIcons();

    window.addEventListener('DOMContentLoaded', () => {
        const firstRow = document.querySelector('.static-content-row');
        if (firstRow) {
            selectPreviewFromRow(firstRow);
        }
    });
</script>
<script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>
</body>
</html>

