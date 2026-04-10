<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\auth\signup.php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /2nd-Year-Group-Project/FixLanka/dashboard');
    exit;
}

// Get error message if exists
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

// Load service categories (for Repairer signup)
$serviceCategories = [];
try {
    if (isset($pdo)) {
        $stmt = $pdo->query('SELECT category_id, name FROM category ORDER BY name');
        $serviceCategories = $stmt->fetchAll();
    }
} catch (Exception $e) {
    // Non-fatal: fallback to empty list; backend still accepts custom category.
    error_log('Failed to load categories for signup: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/buttons.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/auth/signup.css">
</head>
<body>
    <div class="signup-container">
        <div class="signup-card">
            <div class="signup-header">
                <h1>Create Account</h1>
                <p>Join Fix Lanka today</p>
            </div>
            
            <!-- Role Selection Buttons -->
            <div class="view-toggle-group role-buttons">
                <button type="button" class="view-toggle role-btn active" data-role="user">User</button>
                <button type="button" class="view-toggle role-btn" data-role="repairer">Repairer</button>
                <button type="button" class="view-toggle role-btn" data-role="company">Company</button>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <!-- USER REGISTRATION FORM -->
            <form action="/2nd-Year-Group-Project/FixLanka/register" method="POST" class="signup-form active" id="userForm" data-role="user" autocomplete="off">
                <input type="hidden" name="user_type" value="user">
                <div class="form-row">
                    <div class="form-group">
                        <label for="f_name">First Name *</label>
                        <input type="text" id="f_name" name="f_name" required placeholder="Enter first name" autocomplete="off" value="<?php echo htmlspecialchars($_POST['f_name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="l_name">Last Name *</label>
                        <input type="text" id="l_name" name="l_name" required placeholder="Enter last name" autocomplete="off" value="<?php echo htmlspecialchars($_POST['l_name'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email" autocomplete="off" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" placeholder="Enter your address (optional)" autocomplete="off" value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" required placeholder="Create a password" minlength="6" autocomplete="new-password">
                    <small class="password-hint">At least 6 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password" autocomplete="new-password">
                </div>
                
                <div class="terms-checkbox">
                    <label class="checkbox-label">
                        <input type="checkbox" name="terms" required>
                        I agree to the <a href="/terms" target="_blank">Terms & Conditions</a>
                    </label>
                </div>
                
                <button type="submit" class="action-btn primary large signup-submit-btn">Create User Account</button>
            </form>
            
            <!-- REPAIRER REGISTRATION FORM -->
            <form action="/2nd-Year-Group-Project/FixLanka/register" method="POST" class="signup-form" id="repairerForm" data-role="repairer" enctype="multipart/form-data" autocomplete="off">
                <input type="hidden" name="user_type" value="repairer">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="repairer_f_name">First Name *</label>
                        <input type="text" id="repairer_f_name" name="f_name" required placeholder="Enter first name">
                    </div>
                    
                    <div class="form-group">
                        <label for="repairer_l_name">Last Name *</label>
                        <input type="text" id="repairer_l_name" name="l_name" required placeholder="Enter last name">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="repairer_email">Email Address *</label>
                    <input type="email" id="repairer_email" name="email" required placeholder="Enter your email">
                </div>
                
                <div class="form-group">
                    <label for="repairer_phone">Phone Number *</label>
                    <input type="tel" id="repairer_phone" name="phoneNumber" required placeholder="Enter phone number">
                </div>
                
                <div class="form-group">
                    <label for="repairer_password">Password *</label>
                    <input type="password" id="repairer_password" name="password" required placeholder="Create a password" minlength="6">
                    <small class="password-hint">At least 6 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="repairer_confirm_password">Confirm Password *</label>
                    <input type="password" id="repairer_confirm_password" name="confirm_password" required placeholder="Confirm your password">
                </div>
                
                <div class="form-group">
                    <label for="category_id">Service Category *</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Select a category</option>
                        <?php foreach ($serviceCategories as $cat): ?>
                            <option value="<?php echo htmlspecialchars((string)$cat['category_id']); ?>">
                                <?php echo htmlspecialchars((string)$cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group" id="category_other_group" style="display:none;">
                    <label for="category_custom">Other Category *</label>
                    <input type="text" id="category_custom" name="category_custom" placeholder="Type your service category">
                    <small class="password-hint">If it doesn't exist, we'll add it as a new category</small>
                </div>

                <div class="form-group">
                    <label for="experience_initial_years">Initial Experience (Years) *</label>
                    <input type="number" id="experience_initial_years" name="experience_initial_years" required min="0" max="50" step="1" placeholder="e.g. 1">
                    <small class="password-hint">Enter your existing experience before using FixLanka</small>
                </div>
                
                <div class="form-group">
                    <label>Service Districts * (Select at least one)</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="districts[]" value="Colombo"> Colombo</label>
                        <label><input type="checkbox" name="districts[]" value="Gampaha"> Gampaha</label>
                        <label><input type="checkbox" name="districts[]" value="Kalutara"> Kalutara</label>
                        <label><input type="checkbox" name="districts[]" value="Kandy"> Kandy</label>
                        <label><input type="checkbox" name="districts[]" value="Matale"> Matale</label>
                        <label><input type="checkbox" name="districts[]" value="Nuwara Eliya"> Nuwara Eliya</label>
                        <label><input type="checkbox" name="districts[]" value="Galle"> Galle</label>
                        <label><input type="checkbox" name="districts[]" value="Matara"> Matara</label>
                        <label><input type="checkbox" name="districts[]" value="Hambantota"> Hambantota</label>
                        <label><input type="checkbox" name="districts[]" value="Jaffna"> Jaffna</label>
                        <label><input type="checkbox" name="districts[]" value="Kilinochchi"> Kilinochchi</label>
                        <label><input type="checkbox" name="districts[]" value="Mannar"> Mannar</label>
                        <label><input type="checkbox" name="districts[]" value="Vavuniya"> Vavuniya</label>
                        <label><input type="checkbox" name="districts[]" value="Mullaitivu"> Mullaitivu</label>
                        <label><input type="checkbox" name="districts[]" value="Batticaloa"> Batticaloa</label>
                        <label><input type="checkbox" name="districts[]" value="Ampara"> Ampara</label>
                        <label><input type="checkbox" name="districts[]" value="Trincomalee"> Trincomalee</label>
                        <label><input type="checkbox" name="districts[]" value="Kurunegala"> Kurunegala</label>
                        <label><input type="checkbox" name="districts[]" value="Puttalam"> Puttalam</label>
                        <label><input type="checkbox" name="districts[]" value="Anuradhapura"> Anuradhapura</label>
                        <label><input type="checkbox" name="districts[]" value="Polonnaruwa"> Polonnaruwa</label>
                        <label><input type="checkbox" name="districts[]" value="Badulla"> Badulla</label>
                        <label><input type="checkbox" name="districts[]" value="Monaragala"> Monaragala</label>
                        <label><input type="checkbox" name="districts[]" value="Ratnapura"> Ratnapura</label>
                        <label><input type="checkbox" name="districts[]" value="Kegalle"> Kegalle</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="about">About / Experience *</label>
                    <textarea id="about" name="about" required placeholder="Tell us about your experience and skills" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="profile_picture">Profile Picture (Optional)</label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                    <small class="password-hint">Max 5MB - JPG, PNG, GIF</small>
                </div>
                
                <div class="terms-checkbox">
                    <label class="checkbox-label">
                        <input type="checkbox" name="terms" required>
                        I agree to the <a href="/terms" target="_blank">Terms & Conditions</a>
                    </label>
                </div>
                
                <button type="submit" class="action-btn primary large signup-submit-btn">Create Repairer Account</button>
            </form>
            
            <!-- COMPANY REGISTRATION FORM -->
            <form action="/2nd-Year-Group-Project/FixLanka/register" method="POST" class="signup-form" id="companyForm" data-role="company" autocomplete="off">
                <input type="hidden" name="user_type" value="company">
                
                <div class="form-group">
                    <label for="company_name">Company Name *</label>
                    <input type="text" id="company_name" name="name" required placeholder="Enter company name">
                </div>
                
                <div class="form-group">
                    <label>Business Type * (Select at least one)</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="business_type[]" value="Plumbing"> Plumbing</label>
                        <label><input type="checkbox" name="business_type[]" value="Electrical"> Electrical</label>
                        <label><input type="checkbox" name="business_type[]" value="HVAC"> HVAC</label>
                        <label><input type="checkbox" name="business_type[]" value="Cleaning"> Cleaning</label>
                        <label><input type="checkbox" name="business_type[]" value="Carpentry"> Carpentry</label>
                        <label><input type="checkbox" name="business_type[]" value="Painting"> Painting</label>
                        <label><input type="checkbox" name="business_type[]" value="Appliance Repair"> Appliance Repair</label>
                        <label><input type="checkbox" name="business_type[]" value="Roofing"> Roofing</label>
                        <label><input type="checkbox" name="business_type[]" value="Landscaping"> Landscaping</label>
                        <label><input type="checkbox" name="business_type[]" value="Pest Control"> Pest Control</label>
                        <label><input type="checkbox" name="business_type[]" value="Home Security"> Home Security</label>
                        <label><input type="checkbox" name="business_type[]" value="Interior Design"> Interior Design</label>
                        <label><input type="checkbox" name="business_type[]" value="Flooring"> Flooring</label>
                        <label><input type="checkbox" name="business_type[]" value="Masonry"> Masonry</label>
                        <label><input type="checkbox" name="business_type[]" value="Welding"> Welding</label>
                        <label><input type="checkbox" name="business_type[]" value="Construction"> Construction</label>
                        <label><input type="checkbox" id="company_business_type_other" name="business_type[]" value="Other"> Other</label>
                    </div>
                </div>

                <div class="form-group" id="company_business_type_other_group" style="display:none;">
                    <label for="company_business_type_other_text">Other Business Type *</label>
                    <input type="text" id="company_business_type_other_text" name="business_type_other" placeholder="Type your business type">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="registration_no">Registration Number *</label>
                        <input type="text" id="registration_no" name="registration_no" required placeholder="Company reg. number">
                    </div>
                    
                    <div class="form-group">
                        <label for="tax_id">Tax ID / VAT Number</label>
                        <input type="text" id="tax_id" name="tax_id" placeholder="Tax ID (optional)">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="company_email">Email Address *</label>
                    <input type="email" id="company_email" name="email" required placeholder="Company email">
                </div>
                
                <div class="form-group">
                    <label for="website">Website</label>
                    <input type="url" id="website" name="website" placeholder="https://yourcompany.com">
                </div>
                
                <div class="form-group">
                    <label for="company_address">Address *</label>
                    <textarea id="company_address" name="address" required placeholder="Company address" rows="2"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="contact_no">Contact Number *</label>
                    <input type="tel" id="contact_no" name="contact_no" required placeholder="Contact number">
                </div>
                
                <div class="form-group">
                    <label>Service Districts * (Select at least one)</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="districts[]" value="Colombo"> Colombo</label>
                        <label><input type="checkbox" name="districts[]" value="Gampaha"> Gampaha</label>
                        <label><input type="checkbox" name="districts[]" value="Kalutara"> Kalutara</label>
                        <label><input type="checkbox" name="districts[]" value="Kandy"> Kandy</label>
                        <label><input type="checkbox" name="districts[]" value="Matale"> Matale</label>
                        <label><input type="checkbox" name="districts[]" value="Nuwara Eliya"> Nuwara Eliya</label>
                        <label><input type="checkbox" name="districts[]" value="Galle"> Galle</label>
                        <label><input type="checkbox" name="districts[]" value="Matara"> Matara</label>
                        <label><input type="checkbox" name="districts[]" value="Hambantota"> Hambantota</label>
                        <label><input type="checkbox" name="districts[]" value="Jaffna"> Jaffna</label>
                        <label><input type="checkbox" name="districts[]" value="Kilinochchi"> Kilinochchi</label>
                        <label><input type="checkbox" name="districts[]" value="Mannar"> Mannar</label>
                        <label><input type="checkbox" name="districts[]" value="Vavuniya"> Vavuniya</label>
                        <label><input type="checkbox" name="districts[]" value="Mullaitivu"> Mullaitivu</label>
                        <label><input type="checkbox" name="districts[]" value="Batticaloa"> Batticaloa</label>
                        <label><input type="checkbox" name="districts[]" value="Ampara"> Ampara</label>
                        <label><input type="checkbox" name="districts[]" value="Trincomalee"> Trincomalee</label>
                        <label><input type="checkbox" name="districts[]" value="Kurunegala"> Kurunegala</label>
                        <label><input type="checkbox" name="districts[]" value="Puttalam"> Puttalam</label>
                        <label><input type="checkbox" name="districts[]" value="Anuradhapura"> Anuradhapura</label>
                        <label><input type="checkbox" name="districts[]" value="Polonnaruwa"> Polonnaruwa</label>
                        <label><input type="checkbox" name="districts[]" value="Badulla"> Badulla</label>
                        <label><input type="checkbox" name="districts[]" value="Monaragala"> Monaragala</label>
                        <label><input type="checkbox" name="districts[]" value="Ratnapura"> Ratnapura</label>
                        <label><input type="checkbox" name="districts[]" value="Kegalle"> Kegalle</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="company_password">Password *</label>
                    <input type="password" id="company_password" name="password" required placeholder="Create a password" minlength="6">
                    <small class="password-hint">At least 6 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="company_confirm_password">Confirm Password *</label>
                    <input type="password" id="company_confirm_password" name="confirm_password" required placeholder="Confirm your password">
                </div>
                
                <div class="form-group">
                    <label for="description">Company Description *</label>
                    <textarea id="description" name="description" required placeholder="Describe your company and services" rows="4"></textarea>
                </div>
                
                <div class="terms-checkbox">
                    <label class="checkbox-label">
                        <input type="checkbox" name="terms" required>
                        I agree to the <a href="/terms" target="_blank">Terms & Conditions</a>
                    </label>
                </div>
                
                <button type="submit" class="action-btn primary large signup-submit-btn">Create Company Account</button>
            </form>
            
            <div class="login-link">
                Already have an account? <a href="/2nd-Year-Group-Project/FixLanka/login">Login here</a>
            </div>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/auth/signup.js"></script>
</body>
</html>