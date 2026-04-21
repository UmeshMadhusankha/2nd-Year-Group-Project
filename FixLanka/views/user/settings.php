<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: /2nd-Year-Group-Project/FixLanka/login');
    exit;
}

$userData = getUserData();
$userId = (int)($userData['id'] ?? 0);

$dbUser = null;
try {
    global $pdo;
    $stmt = $pdo->prepare('SELECT f_name, l_name, email, address, district FROM User WHERE user_id = ? LIMIT 1');
    $stmt->execute([$userId]);
    $dbUser = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
} catch (Throwable $e) {
    $dbUser = null;
}

$nameParts = preg_split('/\s+/', trim((string)($userData['name'] ?? '')));
$fallbackFirst = $nameParts[0] ?? '';
$fallbackLast = isset($nameParts[1]) ? implode(' ', array_slice($nameParts, 1)) : '';

$firstName = trim((string)($dbUser['f_name'] ?? $fallbackFirst));
$lastName = trim((string)($dbUser['l_name'] ?? $fallbackLast));
$email = trim((string)($dbUser['email'] ?? ($userData['email'] ?? '')));
$address = trim((string)($dbUser['address'] ?? ''));
$district = trim((string)($dbUser['district'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - FixLanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/navbar.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
        }

        .settings-container {
            max-width: 1000px;
            margin: 100px auto 40px;
            padding: 0 20px;
        }

        .settings-header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .settings-header h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 10px;
        }

        .settings-header p {
            color: #666;
            font-size: 16px;
        }

        .settings-tabs {
            display: none;
        }

        .status-message {
            margin-top: 18px;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 14px;
            display: none;
        }

        .status-message.success {
            display: block;
            color: #155724;
            background: #d4edda;
            border: 1px solid #c3e6cb;
        }

        .status-message.error {
            display: block;
            color: #721c24;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
        }

        .support-note {
            margin-top: 28px;
            padding: 16px;
            background: white;
            border-radius: 10px;
            border-left: 4px solid #667eea;
            color: #495057;
        }

        .settings-tab {
            display: none;
        }

        .settings-tab:hover {
            background: transparent;
        }

        .settings-tab.active {
            background: transparent;
            color: inherit;
        }

        .settings-panel {
            display: none;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .settings-panel.active {
            display: block;
        }

        .settings-section {
            margin-bottom: 40px;
        }

        .settings-section:last-child {
            margin-bottom: 0;
        }

        .settings-section h3 {
            font-size: 20px;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        textarea.form-input {
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }

        .setting-option {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .option-info label {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .option-info p {
            font-size: 13px;
            color: #666;
            margin: 0;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 26px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        .toggle-switch input:checked + .toggle-slider {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .toggle-switch input:checked + .toggle-slider:before {
            transform: translateX(24px);
        }

        .settings-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #e9ecef;
            color: #666;
        }

        .btn-secondary:hover {
            background: #dee2e6;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .danger-zone {
            border: 2px solid #dc3545;
            border-radius: 8px;
            padding: 20px;
            background: #fff5f5;
        }

        .danger-zone h3 {
            color: #dc3545;
        }

        .success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .error-message {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .settings-container {
                margin-top: 80px;
            }

            .settings-tabs {
                flex-wrap: wrap;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .settings-actions {
                flex-direction: column-reverse;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="settings-container">
        <div class="settings-header">
            <h1>Settings</h1>
            <p>Manage your account details</p>
        </div>

        <div class="settings-panel active" id="account-panel">
            <div class="settings-section">
                <h3>Profile Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" class="form-input" value="<?php echo htmlspecialchars($firstName); ?>" placeholder="Enter first name">
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" class="form-input" value="<?php echo htmlspecialchars($lastName); ?>" placeholder="Enter last name">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" class="form-input" value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter email">
                    </div>
                    <div class="form-group">
                        <label for="district">District</label>
                        <input type="text" id="district" class="form-input" value="<?php echo htmlspecialchars($district); ?>" placeholder="Enter district">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" class="form-input" placeholder="Enter your address"><?php echo htmlspecialchars($address); ?></textarea>
                </div>
            </div>

            <div class="support-note">
                <strong>Available here:</strong> profile details update only.
            </div>

            <div id="settingsStatus" class="status-message" role="status" aria-live="polite"></div>

            <div class="settings-actions">
                <button class="btn btn-secondary" id="resetBtn" type="button">Reset</button>
                <button class="btn btn-primary" id="saveBtn" type="button">Save Changes</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const UPDATE_USER_API = '/2nd-Year-Group-Project/FixLanka/api/user/updateUser.php';
            const saveBtn = document.getElementById('saveBtn');
            const resetBtn = document.getElementById('resetBtn');
            const statusEl = document.getElementById('settingsStatus');

            const firstNameEl = document.getElementById('firstName');
            const lastNameEl = document.getElementById('lastName');
            const emailEl = document.getElementById('email');
            const districtEl = document.getElementById('district');
            const addressEl = document.getElementById('address');

            const initialValues = {
                firstName: firstNameEl ? firstNameEl.value : '',
                lastName: lastNameEl ? lastNameEl.value : '',
                email: emailEl ? emailEl.value : '',
                district: districtEl ? districtEl.value : '',
                address: addressEl ? addressEl.value : ''
            };

            function setStatus(message, type) {
                if (!statusEl) return;
                statusEl.textContent = message;
                statusEl.className = 'status-message ' + type;
            }

            function resetForm() {
                if (firstNameEl) firstNameEl.value = initialValues.firstName;
                if (lastNameEl) lastNameEl.value = initialValues.lastName;
                if (emailEl) emailEl.value = initialValues.email;
                if (districtEl) districtEl.value = initialValues.district;
                if (addressEl) addressEl.value = initialValues.address;
                if (statusEl) {
                    statusEl.textContent = '';
                    statusEl.className = 'status-message';
                }
            }

            async function saveProfile() {
                const firstName = (firstNameEl ? firstNameEl.value : '').trim();
                const lastName = (lastNameEl ? lastNameEl.value : '').trim();
                const email = (emailEl ? emailEl.value : '').trim();
                const district = (districtEl ? districtEl.value : '').trim();
                const address = (addressEl ? addressEl.value : '').trim();

                if (!firstName || !lastName || !email || !address) {
                    setStatus('First name, last name, email and address are required.', 'error');
                    return;
                }

                const previousLabel = saveBtn ? saveBtn.textContent : '';
                if (saveBtn) {
                    saveBtn.disabled = true;
                    saveBtn.textContent = 'Saving...';
                }

                try {
                    const response = await fetch(UPDATE_USER_API, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ firstName, lastName, email, district, address })
                    });

                    const payload = await response.json();
                    if (!response.ok || !payload || payload.success === false) {
                        throw new Error((payload && payload.message) ? payload.message : 'Failed to save changes');
                    }

                    initialValues.firstName = firstName;
                    initialValues.lastName = lastName;
                    initialValues.email = email;
                    initialValues.district = district;
                    initialValues.address = address;
                    setStatus('Profile updated successfully.', 'success');
                } catch (error) {
                    setStatus(error.message || 'Failed to save changes.', 'error');
                } finally {
                    if (saveBtn) {
                        saveBtn.disabled = false;
                        saveBtn.textContent = previousLabel || 'Save Changes';
                    }
                }
            }

            if (saveBtn) {
                saveBtn.addEventListener('click', saveProfile);
            }
            if (resetBtn) {
                resetBtn.addEventListener('click', resetForm);
            }
            [firstNameEl, lastNameEl, emailEl, districtEl, addressEl].forEach(function(field) {
                if (!field) return;
                field.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter' && field.tagName !== 'TEXTAREA') {
                        event.preventDefault();
                        saveProfile();
                    }
                });
            });
        });
    </script>
</body>
</html>
