<?php
require_once __DIR__ . '/../../config/session.php';
requireRole('company');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center - FixLanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .help-container {
            max-width: 900px;
            margin: 0 auto;
            padding: var(--spacing-2xl);
        }

        .help-header {
            text-align: center;
            margin-bottom: var(--spacing-2xl);
            padding-bottom: var(--spacing-xl);
            border-bottom: 1px solid var(--border-color);
        }

        .help-header h1 {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: var(--spacing-sm);
        }

        .help-content {
            background: var(--bg-primary);
            border-radius: var(--border-radius-xl);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .help-section {
            padding: var(--spacing-2xl);
            border-bottom: 1px solid var(--border-color);
        }

        .help-section:last-child {
            border-bottom: none;
        }

        .help-section:target {
            animation: highlight 2s ease-out;
            background-color: rgba(10, 186, 181, 0.05);
        }

        @keyframes highlight {
            0% { background-color: rgba(10, 186, 181, 0.2); }
            100% { background-color: transparent; }
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-lg);
        }

        .section-icon {
            width: 48px;
            height: 48px;
            background: var(--bg-secondary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 1.5rem;
        }

        .section-header h2 {
            font-size: 1.8rem;
            margin: 0;
            color: var(--text-primary);
        }

        .help-text h3 {
            font-size: 1.2rem;
            color: var(--text-primary);
            margin: var(--spacing-lg) 0 var(--spacing-sm);
        }

        .help-text p {
            color: var(--text-secondary);
            margin-bottom: var(--spacing-md);
        }

        .help-text ul, .help-text ol {
            color: var(--text-secondary);
            margin-bottom: var(--spacing-md);
            padding-left: var(--spacing-xl);
        }

        .help-text li {
            margin-bottom: var(--spacing-xs);
        }

        .image-placeholder {
            background: var(--bg-secondary);
            border: 2px dashed var(--border-color);
            border-radius: var(--border-radius);
            padding: var(--spacing-2xl);
            text-align: center;
            color: var(--text-muted);
            margin: var(--spacing-lg) 0;
        }

        .header-nav {
            margin-bottom: var(--spacing-lg);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-xs);
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 600;
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--border-radius);
            transition: all 0.2s ease;
            background: rgba(255, 255, 255, 0.5);
        }

        .back-link:hover {
            color: var(--primary-color);
            background: white;
            box-shadow: var(--shadow-sm);
            transform: translateX(-2px);
        }
    </style>
</head>
<body>
    <div class="help-container">
        <div class="header-nav">
            <a href="dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
        <header class="help-header">
            <h1><i class="fas fa-life-ring"></i> Help Center</h1>
            <p>Guides and documentation for the FixLanka Company Dashboard</p>
        </header>

        <div class="help-content">
            <!-- Reset Password Section -->
            <section id="reset-password" class="help-section">
                <div class="section-header">
                    <div class="section-icon"><i class="fas fa-key"></i></div>
                    <h2>How to Reset Your Password</h2>
                </div>
                <div class="help-text">
                    <p>If you've forgotten your password or suspect it has been compromised, follow these steps to reset it:</p>
                    <ol>
                        <li>Go to the <strong>Settings</strong> page from the sidebar.</li>
                        <li>Navigate to the <strong>Security</strong> tab.</li>
                        <li>Click on the <strong>Change Password</strong> button.</li>
                        <li>Enter your current password strictly for verification.</li>
                        <li>Enter your new password and confirm it.</li>
                        <li>Click <strong>Update Password</strong>.</li>
                    </ol>
                    <div class="image-placeholder">
                        <i class="fas fa-image fa-2x"></i>
                        <p>Screenshot of Password Change Form</p>
                    </div>
                    <p><strong>Note:</strong> Passwords must be at least 8 characters long and include a mix of letters and numbers.</p>
                </div>
            </section>

            <!-- Getting Started Section -->
            <section id="getting-started" class="help-section">
                <div class="section-header">
                    <div class="section-icon"><i class="fas fa-video"></i></div>
                    <h2>Getting Started Tutorial</h2>
                </div>
                <div class="help-text">
                    <p>Welcome to FixLanka! This quick guide will help you navigate your new dashboard.</p>
                    <h3>1. The Dashboard</h3>
                    <p>Your main hub for overview statistics, recent activities, and quick actions.</p>
                    <h3>2. Managing Projects</h3>
                    <p>View all your ongoing and past projects in the <strong>Projects</strong> tab. You can filter by status and date.</p>
                    <h3>3. Requesting Repairs</h3>
                    <p>Need a fix? Go to <strong>Repair Requests</strong> to submit a new job request to our workforce.</p>
                    <div class="image-placeholder">
                        <i class="fas fa-play-circle fa-2x"></i>
                        <p>Video Tutorial Player (Coming Soon)</p>
                    </div>
                </div>
            </section>

            <!-- User Manual Section -->
            <section id="user-manual" class="help-section">
                <div class="section-header">
                    <div class="section-icon"><i class="fas fa-book"></i></div>
                    <h2>User Manual</h2>
                </div>
                <div class="help-text">
                    <p>A comprehensive guide to all features available in the company portal.</p>
                    <ul>
                        <li><strong>Workforce:</strong> Manage your assigned workers and view their performance.</li>
                        <li><strong>Payments:</strong> Track invoices, view transaction history, and manage payment methods.</li>
                        <li><strong>Contracts:</strong> View and download your service agreements.</li>
                        <li><strong>Support:</strong> Raise tickets for any issues you encounter with the platform.</li>
                    </ul>
                    <p>For more detailed documentation, please contact our support team.</p>
                </div>
            </section>
        </div>


    </div>
</body>
</html>
