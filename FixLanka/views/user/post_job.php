<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\user\post_job.php
require_once __DIR__ . '/../../config/session.php';

if (!isLoggedIn()) {
    header('Location: /2nd-Year-Group-Project/FixLanka/login');
    exit;
}

$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Job - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/post_job.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <main class="main-content">
        <div class="form-container">
            <div class="form-card">
                <div class="form-header">
                    <h1>Post a New Job</h1>
                    <p>Fill in the details below to post your repair job</p>
                    <a href="/2nd-Year-Group-Project/FixLanka/views/user/landing.php" class="btn-home">
                        <i class="fas fa-home"></i> Home
                    </a>
                </div>

                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form action="/2nd-Year-Group-Project/FixLanka/create-job" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="category_id">Category <span class="required">*</span></label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Select a category</option>
                            <option value="1">Plumbing</option>
                            <option value="2">Electrical</option>
                            <option value="3">Cleaning</option>
                            <option value="4">HVAC</option>
                            <option value="5">Carpentry</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Description <span class="required">*</span></label>
                        <textarea id="description" name="description" rows="5" required placeholder="Describe your job in detail..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="location">Location <span class="required">*</span></label>
                        <input type="text" id="location" name="location" required placeholder="Enter location">
                    </div>

                    <div class="form-group">
                        <label for="service_provider_type">Service Provider Type <span class="required">*</span></label>
                        <select id="service_provider_type" name="service_provider_type" required>
                            <option value="">Select provider type</option>
                            <option value="individual">Individual</option>
                            <option value="company">Company</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="urgency">Urgency Level <span class="required">*</span></label>
                        <select id="urgency" name="urgency" required>
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="photos">Photos (Optional)</label>
                        <div class="file-upload-wrapper">
                            <input type="file" id="photos" name="photos" accept="image/*" onchange="updateFileName(this)">
                            <label for="photos" class="file-upload-label">
                                <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                <span class="upload-text">Choose File</span>
                            </label>
                        </div>
                        <small class="hint-text">Upload a photo related to your job request</small>
                        <div id="file-name-display" class="file-preview"></div>
                    </div>

                    <button type="submit" class="submit-btn">Post Job</button>
                </form>
            </div>
        </div>
    </main>

    <script>
    function updateFileName(input) {
        const fileDisplay = document.getElementById('file-name-display');
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            fileDisplay.innerHTML = `<div class="file-preview-item">${fileName}</div>`;
        } else {
            fileDisplay.innerHTML = '';
        }
    }
    </script>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/post_job.js"></script>
</body>
</html>