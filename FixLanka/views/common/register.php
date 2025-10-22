<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FixLanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/common/global.css">
    <link rel="stylesheet" href="../../assets/css/common/variables.css">
    <link rel="stylesheet" href="../../assets/css/common/register.css">
</head>
<body>
    <div class="register-container">
        <!-- Register Card -->
        <div class="register-card">
            <!-- Logo and Header -->
            <div class="register-header">
                <div class="logo">
                    <img src="../../assets/images/fixlanka.png" alt="FixLanka Logo">
                </div>
                <h1 class="register-title">Join FixLanka</h1>
                <p class="register-subtitle">Choose your role and register to get started.</p>
            </div>

            <!-- Role Selection -->
            <div class="role-selection">
                <button class="role-btn active" data-role="user">
                    <i class="fas fa-user"></i>
                    <span>User</span>
                    <small>Request repair services</small>
                </button>
                <button class="role-btn" data-role="repairer">
                    <i class="fas fa-wrench"></i>
                    <span>Repairer</span>
                    <small>Provide repair services</small>
                </button>
                <button class="role-btn" data-role="company">
                    <i class="fas fa-building"></i>
                    <span>Company</span>
                    <small>Manage business operations</small>
                </button>
            </div>

            <!-- Registration Forms Container -->
            <div class="forms-container">
                <!-- User Registration Form -->
                <form class="register-form active" id="user-form" data-form="user">
                    <div class="form-header">
                        <h2>User Registration</h2>
                        <p>Create your account to request repair services</p>
                    </div>

                    <div class="form-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="user-first-name">
                                    <i class="fas fa-user"></i>
                                    First Name
                                </label>
                                <input type="text" id="user-first-name" name="first_name" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label for="user-last-name">
                                    <i class="fas fa-user"></i>
                                    Last Name
                                </label>
                                <input type="text" id="user-last-name" name="last_name" class="form-input" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="user-email">
                                <i class="fas fa-envelope"></i>
                                Email Address
                            </label>
                            <input type="email" id="user-email" name="email" class="form-input" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="user-password">
                                    <i class="fas fa-lock"></i>
                                    Password
                                </label>
                                <input type="password" id="user-password" name="password" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label for="user-confirm-password">
                                    <i class="fas fa-lock"></i>
                                    Confirm Password
                                </label>
                                <input type="password" id="user-confirm-password" name="confirm_password" class="form-input" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="user-address">
                                <i class="fas fa-map-marker-alt"></i>
                                Address
                            </label>
                            <textarea id="user-address" name="address" class="form-input" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="user-phone">
                                <i class="fas fa-phone"></i>
                                Contact Number <span class="optional">(Optional)</span>
                            </label>
                            <input type="tel" id="user-phone" name="phone" class="form-input">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i>
                            Register as User
                        </button>
                    </div>

                    <div class="form-footer">
                        <p>Already have an account? <a href="login.php">Login</a></p>
                    </div>
                </form>

                <!-- Repairer Registration Form -->
                <form class="register-form" id="repairer-form" data-form="repairer">
                    <div class="form-header">
                        <h2>Repairer Registration</h2>
                        <p>Register as a service provider and start earning</p>
                    </div>

                    <div class="form-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="repairer-first-name">
                                    <i class="fas fa-user"></i>
                                    First Name
                                </label>
                                <input type="text" id="repairer-first-name" name="first_name" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label for="repairer-last-name">
                                    <i class="fas fa-user"></i>
                                    Last Name
                                </label>
                                <input type="text" id="repairer-last-name" name="last_name" class="form-input" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="repairer-email">
                                <i class="fas fa-envelope"></i>
                                Email Address
                            </label>
                            <input type="email" id="repairer-email" name="email" class="form-input" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="repairer-password">
                                    <i class="fas fa-lock"></i>
                                    Password
                                </label>
                                <input type="password" id="repairer-password" name="password" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label for="repairer-confirm-password">
                                    <i class="fas fa-lock"></i>
                                    Confirm Password
                                </label>
                                <input type="password" id="repairer-confirm-password" name="confirm_password" class="form-input" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="repairer-phone">
                                <i class="fas fa-phone"></i>
                                Phone Number
                            </label>
                            <input type="tel" id="repairer-phone" name="phone" class="form-input" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="repairer-category">
                                    <i class="fas fa-tools"></i>
                                    Service Category
                                </label>
                                <select id="repairer-category" name="service_category" class="form-input" required>
                                    <option value="">Select a category</option>
                                    <option value="electrician">Electrician</option>
                                    <option value="plumber">Plumber</option>
                                    <option value="carpenter">Carpenter</option>
                                    <option value="technician">Technician</option>
                                    <option value="painter">Painter</option>
                                    <option value="hvac">HVAC Specialist</option>
                                    <option value="appliance">Appliance Repair</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="repairer-pricing">
                                    <i class="fas fa-dollar-sign"></i>
                                    Pricing (LKR per hour)
                                </label>
                                <input type="number" id="repairer-pricing" name="pricing" class="form-input" placeholder="e.g., 2000" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>
                                <i class="fas fa-map-marked-alt"></i>
                                Service Areas (Select one or more districts)
                            </label>
                            <div class="checkbox-grid" id="repairer-service-areas">
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Ampara">
                                    <span>Ampara</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Anuradhapura">
                                    <span>Anuradhapura</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Badulla">
                                    <span>Badulla</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Batticaloa">
                                    <span>Batticaloa</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Colombo">
                                    <span>Colombo</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Galle">
                                    <span>Galle</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Gampaha">
                                    <span>Gampaha</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Hambantota">
                                    <span>Hambantota</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Jaffna">
                                    <span>Jaffna</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Kalutara">
                                    <span>Kalutara</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Kandy">
                                    <span>Kandy</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Kegalle">
                                    <span>Kegalle</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Kilinochchi">
                                    <span>Kilinochchi</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Kurunegala">
                                    <span>Kurunegala</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Mannar">
                                    <span>Mannar</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Matale">
                                    <span>Matale</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Matara">
                                    <span>Matara</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Monaragala">
                                    <span>Monaragala</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Mullaitivu">
                                    <span>Mullaitivu</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Nuwara Eliya">
                                    <span>Nuwara Eliya</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Polonnaruwa">
                                    <span>Polonnaruwa</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Puttalam">
                                    <span>Puttalam</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Ratnapura">
                                    <span>Ratnapura</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Trincomalee">
                                    <span>Trincomalee</span>
                                </label>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="service_areas[]" value="Vavuniya">
                                    <span>Vavuniya</span>
                                </label>
                            </div>
                            <small class="form-hint">Select all districts where you provide services</small>
                        </div>

                        <div class="form-group">
                            <label for="repairer-about">
                                <i class="fas fa-info-circle"></i>
                                About / Experience Summary
                            </label>
                            <textarea id="repairer-about" name="about" class="form-input" rows="4" placeholder="Tell us about your experience and expertise..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="repairer-profile-pic">
                                <i class="fas fa-camera"></i>
                                Profile Picture <span class="optional">(Optional)</span>
                            </label>
                            <input type="file" id="repairer-profile-pic" name="profile_picture" class="form-input-file" accept="image/*">
                            <div class="file-upload-wrapper">
                                <label for="repairer-profile-pic" class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Choose file or drag here</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i>
                            Register as Repairer
                        </button>
                    </div>

                    <div class="form-footer">
                        <p>Already have an account? <a href="login.php">Login</a></p>
                    </div>
                </form>

                <!-- Company Registration Form -->
                <form class="register-form" id="company-form" data-form="company">
                    <div class="form-header">
                        <h2>Company Registration</h2>
                        <p>Register your company and manage business operations</p>
                    </div>

                    <div class="form-body">
                        <div class="form-group">
                            <label for="company-name">
                                <i class="fas fa-building"></i>
                                Company Name
                            </label>
                            <input type="text" id="company-name" name="company_name" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label for="company-reg-number">
                                <i class="fas fa-id-card"></i>
                                Company Registration Number
                            </label>
                            <input type="text" id="company-reg-number" name="registration_number" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label for="company-email">
                                <i class="fas fa-envelope"></i>
                                Email Address
                            </label>
                            <input type="email" id="company-email" name="email" class="form-input" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="company-password">
                                    <i class="fas fa-lock"></i>
                                    Password
                                </label>
                                <input type="password" id="company-password" name="password" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label for="company-confirm-password">
                                    <i class="fas fa-lock"></i>
                                    Confirm Password
                                </label>
                                <input type="password" id="company-confirm-password" name="confirm_password" class="form-input" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="company-address">
                                <i class="fas fa-map-marker-alt"></i>
                                Address
                            </label>
                            <textarea id="company-address" name="address" class="form-input" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="company-phone">
                                <i class="fas fa-phone"></i>
                                Contact Number
                            </label>
                            <input type="tel" id="company-phone" name="phone" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label for="company-description">
                                <i class="fas fa-align-left"></i>
                                Description / About Company
                            </label>
                            <textarea id="company-description" name="description" class="form-input" rows="4" placeholder="Tell us about your company..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="company-logo">
                                <i class="fas fa-image"></i>
                                Company Logo <span class="optional">(Optional)</span>
                            </label>
                            <input type="file" id="company-logo" name="company_logo" class="form-input-file" accept="image/*">
                            <div class="file-upload-wrapper">
                                <label for="company-logo" class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Choose file or drag here</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-building"></i>
                            Register Company
                        </button>
                    </div>

                    <div class="form-footer">
                        <p>Already have an account? <a href="login.php">Login</a></p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Decorative Background -->
        <div class="background-decoration">
            <div class="decoration-circle circle-1"></div>
            <div class="decoration-circle circle-2"></div>
            <div class="decoration-circle circle-3"></div>
        </div>
    </div>

    <script src="../../assets/javascript/common/register.js"></script>
</body>
</html>
