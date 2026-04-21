<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/static_content.php';

$howItWorksContent = fixlanka_static_content_for_page($pdo, 'how_it_works');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How It Works - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/how-it-works.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/static-content.css">
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

        <div id="how-it-works-content">
            <?php if (!empty($howItWorksContent['body'])): ?>
                <?php echo fixlanka_static_content_body_to_html((string)$howItWorksContent['body']); ?>
            <?php endif; ?>
        </div>

        <section class="guide-section" aria-labelledby="user-guide-title">
            <div class="guide-header">
                <h2 id="user-guide-title"><i class="fas fa-route"></i> User Guide: End-to-End Job Flow</h2>
                <p>Follow this workflow to track requests, negotiate, complete work, and confirm payments without missing steps.</p>
            </div>

            <div class="guide-steps">
                <article class="guide-step-card">
                    <div class="guide-step-number">1</div>
                    <h3>Create a Job Request</h3>
                    <p>Start by posting a request with your issue details, budget, and location.</p>
                    <a class="guide-link" href="/2nd-Year-Group-Project/FixLanka/post-job">Go to Post Job</a>
                </article>

                <article class="guide-step-card">
                    <div class="guide-step-number">2</div>
                    <h3>Track Pending Requests</h3>
                    <p>New requests appear in My Jobs under <strong>Pending</strong> until a provider responds or the request progresses.</p>
                    <a class="guide-link" href="/2nd-Year-Group-Project/FixLanka/job-history">Open My Jobs</a>
                </article>

                <article class="guide-step-card">
                    <div class="guide-step-number">3</div>
                    <h3>Review Quotes and Negotiate</h3>
                    <p>In My Jobs, open the quotes area and use <strong>Negotiate</strong> if you need price changes before accepting.</p>
                    <a class="guide-link" href="/2nd-Year-Group-Project/FixLanka/job-history">Open Quotes in My Jobs</a>
                </article>

                <article class="guide-step-card">
                    <div class="guide-step-number">4</div>
                    <h3>Move to Contract Stage</h3>
                    <p>After quote acceptance, the job appears in your contracts area where project progress and milestones are managed.</p>
                    <a class="guide-link" href="/2nd-Year-Group-Project/FixLanka/my-contracts">Open My Contracts</a>
                </article>

                <article class="guide-step-card">
                    <div class="guide-step-number">5</div>
                    <h3>Review Completion Proof</h3>
                    <p>For milestone-based jobs, use <strong>Review Now</strong> in contracts when providers submit proof for your approval.</p>
                    <a class="guide-link" href="/2nd-Year-Group-Project/FixLanka/my-contracts">Review Milestones</a>
                </article>

                <article class="guide-step-card">
                    <div class="guide-step-number">6</div>
                    <h3>Mark Completion and Payment</h3>
                    <p>In My Jobs collaboration actions, use <strong>Mark Job Completed</strong> and then <strong>Confirm Payment Done</strong>. Completed work then shows under <strong>Completed</strong>.</p>
                    <a class="guide-link" href="/2nd-Year-Group-Project/FixLanka/job-history">Complete and Confirm Payment</a>
                </article>
            </div>

            <div class="guide-notes">
                <h3><i class="fas fa-lightbulb"></i> Quick Tips</h3>
                <ul>
                    <li>Use Pending in My Jobs to catch new requests quickly.</li>
                    <li>Keep negotiation messages short and clear for faster agreement.</li>
                    <li>Check My Contracts regularly for submitted milestone proof.</li>
                    <li>Only confirm payment after work is verified.</li>
                </ul>
            </div>
        </section>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/how-it-works.js"></script>
</body>
</html>
