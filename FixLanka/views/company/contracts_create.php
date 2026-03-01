<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

// Check if user is logged in and is a company
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Company') {
    header('Location: ../../views/auth/login.php');
    exit();
}

$company_id = $_SESSION['user_id'];

// Fetch company details
try {
    $stmt = $pdo->prepare("
        SELECT 
            c.name as company_name,
            c.business_registration_no as business_registration,
            c.address as company_address,
            c.contact_no as contact_number,
            c.email as company_email,
            u.f_name,
            u.l_name,
            u.email as user_email
        FROM Company c
        INNER JOIN User u ON c.user_id = u.user_id
        WHERE c.company_id = ?
    ");
    $stmt->execute([$company_id]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$company) {
        die("Company information not found.");
    }
} catch (PDOException $e) {
    die("Error fetching company details: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Contract - FixLanka</title>
    <link rel="stylesheet" href="../../assets/css/company/contracts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 600;
        }

        .header .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .header .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Progress Steps */
        .progress-container {
            background: #f8f9fa;
            padding: 30px 40px;
            border-bottom: 2px solid #e9ecef;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            max-width: 800px;
            margin: 0 auto;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 25px;
            left: 0;
            right: 0;
            height: 3px;
            background: #dee2e6;
            z-index: 0;
        }

        .progress-steps .progress-line {
            position: absolute;
            top: 25px;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            z-index: 1;
            transition: width 0.4s ease;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .step-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: white;
            border: 3px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #6c757d;
            transition: all 0.3s;
            margin-bottom: 10px;
        }

        .step.active .step-circle {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            transform: scale(1.1);
        }

        .step.completed .step-circle {
            border-color: #28a745;
            background: #28a745;
            color: white;
        }

        .step-label {
            font-size: 13px;
            color: #6c757d;
            font-weight: 500;
            text-align: center;
        }

        .step.active .step-label {
            color: #667eea;
            font-weight: 600;
        }

        /* Form Content */
        .form-content {
            padding: 40px;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .step-title {
            font-size: 24px;
            color: #2d3748;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .step-description {
            color: #718096;
            margin-bottom: 30px;
            font-size: 14px;
        }

        /* Section Styling */
        .contract-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            border-left: 4px solid #667eea;
        }

        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 15px;
        }

        .section-title {
            font-size: 18px;
            color: #2d3748;
            font-weight: 600;
        }

        /* Info Box */
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .info-box.warning {
            background: #fff3e0;
            border-left-color: #ff9800;
        }

        .info-box.success {
            background: #e8f5e9;
            border-left-color: #4caf50;
        }

        .info-box-title {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-box-content {
            font-size: 14px;
            color: #4a5568;
            line-height: 1.6;
        }

        .info-box-content ul {
            margin-left: 20px;
            margin-top: 8px;
        }

        /* Form Groups */
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group label .required {
            color: #e53e3e;
            margin-left: 4px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group input[readonly] {
            background-color: #f7fafc;
            cursor: not-allowed;
        }

        /* Milestone Table */
        .milestone-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .milestone-table th {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }

        .milestone-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .milestone-table tbody tr:last-child td {
            border-bottom: none;
        }

        .milestone-table tbody tr:hover {
            background: #f7fafc;
        }

        .milestone-table input {
            width: 100%;
            padding: 8px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
        }

        .milestone-table input:focus {
            outline: none;
            border-color: #667eea;
        }

        .milestone-table tfoot {
            background: #f8f9fa;
            font-weight: 600;
        }

        .milestone-table tfoot td {
            padding: 15px;
            border-top: 2px solid #667eea;
        }

        /* Template Buttons */
        .template-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .template-btn {
            padding: 8px 16px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .template-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        /* Navigation Buttons */
        .form-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #e2e8f0;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(72, 187, 120, 0.4);
        }

        /* Preview Styles */
        .preview-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-height: 600px;
            overflow-y: auto;
        }

        .preview-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
        }

        .preview-header h2 {
            color: #2d3748;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .preview-section {
            margin-bottom: 25px;
        }

        .preview-section h3 {
            color: #667eea;
            font-size: 18px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }

        .preview-field {
            margin-bottom: 12px;
            line-height: 1.6;
        }

        .preview-field strong {
            color: #2d3748;
            display: inline-block;
            min-width: 180px;
        }

        .preview-field span {
            color: #4a5568;
        }

        /* Loading Spinner */
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .progress-steps {
                flex-wrap: wrap;
                gap: 20px;
            }
            
            .form-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-file-contract"></i> Create New Contract</h1>
            <a href="contracts.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Contracts
            </a>
        </div>

        <!-- Progress Steps -->
        <div class="progress-container">
            <div class="progress-steps">
                <div class="progress-line" id="progressLine"></div>
                <div class="step active" data-step="1">
                    <div class="step-circle">1</div>
                    <div class="step-label">Select Project</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-circle">2</div>
                    <div class="step-label">Basic Details</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-circle">3</div>
                    <div class="step-label">Contract Terms</div>
                </div>
                <div class="step" data-step="4">
                    <div class="step-circle">4</div>
                    <div class="step-label">Review & Submit</div>
                </div>
            </div>
        </div>

        <!-- Form Content -->
        <form id="contractForm" class="form-content">
            <!-- STEP 1: Select Project & Quotation -->
            <div class="form-step active" data-step="1">
                <h2 class="step-title">Select Project & Quotation</h2>
                <p class="step-description">Choose an accepted quotation to create a contract</p>

                <div class="contract-section">
                    <div class="info-box">
                        <div class="info-box-title">
                            <i class="fas fa-info-circle"></i> About This Step
                        </div>
                        <div class="info-box-content">
                            Select from your accepted quotations. The contract will auto-fill with project and client details.
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="quotationSelect">Select Accepted Quotation <span class="required">*</span></label>
                        <select id="quotationSelect" name="quotation_id" required>
                            <option value="">-- Loading quotations... --</option>
                        </select>
                    </div>

                    <div id="quotationDetails" style="display: none;">
                        <div class="info-box success">
                            <div class="info-box-title">
                                <i class="fas fa-check-circle"></i> Quotation Selected
                            </div>
                            <div class="info-box-content">
                                <strong>Project:</strong> <span id="selectedProjectTitle"></span><br>
                                <strong>Client:</strong> <span id="selectedClientName"></span><br>
                                <strong>Amount:</strong> Rs. <span id="selectedAmount"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-navigation">
                    <div></div>
                    <button type="button" class="btn btn-primary" onclick="nextStep()">
                        Next <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 2: Basic Details (Parties & Project) -->
            <div class="form-step" data-step="2">
                <h2 class="step-title">Basic Contract Details</h2>
                <p class="step-description">Parties to the contract and project identification</p>

                <!-- Section 1: Parties to the Contract -->
                <div class="contract-section">
                    <div class="section-header">
                        <div class="section-number">1</div>
                        <div class="section-title">Parties to the Contract</div>
                    </div>

                    <div class="info-box">
                        <div class="info-box-title">
                            <i class="fas fa-users"></i> Legal Identity
                        </div>
                        <div class="info-box-content">
                            This section defines who is legally bound by this agreement. Details are auto-filled from your profiles.
                        </div>
                    </div>

                    <h4 style="margin: 20px 0 15px; color: #667eea; font-size: 16px;">
                        <i class="fas fa-building"></i> Contractor (Company)
                    </h4>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="companyName">Company Name <span class="required">*</span></label>
                            <input type="text" id="companyName" name="company_name" 
                                   value="<?php echo htmlspecialchars($company['company_name']); ?>" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="businessRegistration">Business Registration No. <span class="required">*</span></label>
                            <input type="text" id="businessRegistration" name="business_registration" 
                                   value="<?php echo htmlspecialchars($company['business_registration']); ?>" readonly required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="companyAddress">Registered Address <span class="required">*</span></label>
                            <input type="text" id="companyAddress" name="company_address" 
                                   value="<?php echo htmlspecialchars($company['company_address']); ?>" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="companyContact">Contact Number <span class="required">*</span></label>
                            <input type="tel" id="companyContact" name="company_contact" 
                                   value="<?php echo htmlspecialchars($company['contact_number']); ?>" readonly required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="companyEmail">Email Address <span class="required">*</span></label>
                        <input type="email" id="companyEmail" name="company_email" 
                               value="<?php echo htmlspecialchars($company['company_email']); ?>" readonly required>
                    </div>

                    <div class="form-group">
                        <label for="companyRepresentative">Authorized Representative <span class="required">*</span></label>
                        <input type="text" id="companyRepresentative" name="company_representative" 
                               value="<?php echo htmlspecialchars($company['f_name'] . ' ' . $company['l_name']); ?>" readonly required>
                    </div>

                    <h4 style="margin: 30px 0 15px; color: #667eea; font-size: 16px;">
                        <i class="fas fa-user"></i> Client
                    </h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="clientName">Client Name <span class="required">*</span></label>
                            <input type="text" id="clientName" name="client_name" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="clientNIC">NIC Number</label>
                            <input type="text" id="clientNIC" name="client_nic" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="clientAddress">Client Address <span class="required">*</span></label>
                        <input type="text" id="clientAddress" name="client_address" readonly required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="clientContact">Contact Number <span class="required">*</span></label>
                            <input type="tel" id="clientContact" name="client_contact" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="clientEmail">Email Address <span class="required">*</span></label>
                            <input type="email" id="clientEmail" name="client_email" readonly required>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Project Identification -->
                <div class="contract-section">
                    <div class="section-header">
                        <div class="section-number">2</div>
                        <div class="section-title">Project Identification & Overview</div>
                    </div>

                    <div class="info-box">
                        <div class="info-box-title">
                            <i class="fas fa-project-diagram"></i> Project Clarity
                        </div>
                        <div class="info-box-content">
                            Clearly defines which project this contract applies to and avoids ambiguity about the scope.
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="projectTitle">Project Title <span class="required">*</span></label>
                            <input type="text" id="projectTitle" name="project_title" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="projectReferenceID">Project Reference ID</label>
                            <input type="text" id="projectReferenceID" name="project_reference_id" readonly>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="projectType">Project Type <span class="required">*</span></label>
                            <input type="text" id="projectType" name="project_type" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="projectLocation">Project Location <span class="required">*</span></label>
                            <input type="text" id="projectLocation" name="project_location" readonly required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="projectDescription">Project Description <span class="required">*</span></label>
                        <textarea id="projectDescription" name="project_description" readonly required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="projectPurpose">Purpose of Work <span class="required">*</span></label>
                        <select id="projectPurpose" name="project_purpose" required>
                            <option value="">-- Select Purpose --</option>
                            <option value="Construction">New Construction</option>
                            <option value="Repair">Repair / Maintenance</option>
                            <option value="Renovation">Renovation / Remodeling</option>
                            <option value="Installation">Installation</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn btn-secondary" onclick="prevStep()">
                        <i class="fas fa-arrow-left"></i> Previous
                    </button>
                    <button type="button" class="btn btn-primary" onclick="nextStep()">
                        Next <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 3: Contract Terms (Sections 3-13) -->
            <div class="form-step" data-step="3">
                <h2 class="step-title">Contract Terms & Conditions</h2>
                <p class="step-description">Define scope, timeline, pricing, payment, and legal terms</p>

                <!-- Section 3: Scope of Work -->
                <div class="contract-section">
                    <div class="section-header">
                        <div class="section-number">3</div>
                        <div class="section-title">Scope of Work</div>
                    </div>

                    <div class="info-box">
                        <div class="info-box-title">
                            <i class="fas fa-tasks"></i> Work Definition
                        </div>
                        <div class="info-box-content">
                            Prevents disputes about what work is included or excluded. Be as specific as possible.
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="scopeDescription">Detailed Description of Work <span class="required">*</span></label>
                        <textarea id="scopeDescription" name="scope_description" rows="6" 
                                  placeholder="Describe in detail what work will be performed..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="scopeInclusions">Inclusions (What is Covered) <span class="required">*</span></label>
                        <textarea id="scopeInclusions" name="scope_inclusions" rows="4" 
                                  placeholder="List all items and services included in the scope..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="scopeExclusions">Exclusions (What is NOT Covered)</label>
                        <textarea id="scopeExclusions" name="scope_exclusions" rows="4" 
                                  placeholder="List all items and services NOT included..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="scopeStandards">Standards & Specifications</label>
                        <textarea id="scopeStandards" name="scope_standards" rows="3" 
                                  placeholder="Reference any standards, codes, or specifications to be followed..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="materialsResponsibility">Materials Responsibility <span class="required">*</span></label>
                        <select id="materialsResponsibility" name="materials_responsibility" required>
                            <option value="">-- Select Responsibility --</option>
                            <option value="Company">Company Provides All Materials</option>
                            <option value="Client">Client Provides All Materials</option>
                            <option value="Shared">Shared Responsibility</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quotationReference">Reference to Quotation / Documents</label>
                        <input type="text" id="quotationReference" name="quotation_reference" readonly>
                    </div>
                </div>

                <!-- Section 4: Contract Duration & Timeline -->
                <div class="contract-section">
                    <div class="section-header">
                        <div class="section-number">4</div>
                        <div class="section-title">Contract Duration & Timeline</div>
                    </div>

                    <div class="info-box">
                        <div class="info-box-title">
                            <i class="fas fa-calendar-alt"></i> Timeline Management
                        </div>
                        <div class="info-box-content">
                            Sets clear expectations for start, completion, and delivery dates.
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="startDate">Project Start Date <span class="required">*</span></label>
                            <input type="date" id="startDate" name="start_date" required>
                        </div>
                        <div class="form-group">
                            <label for="completionDate">Estimated Completion Date <span class="required">*</span></label>
                            <input type="date" id="completionDate" name="completion_date" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="workingDays">Working Days per Week</label>
                            <input type="number" id="workingDays" name="working_days" min="1" max="7" value="6">
                        </div>
                        <div class="form-group">
                            <label for="workingHours">Working Hours per Day</label>
                            <input type="text" id="workingHours" name="working_hours" placeholder="e.g., 8:00 AM - 5:00 PM">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="projectMilestones">Project Milestones & Phase Breakdown</label>
                        <textarea id="projectMilestones" name="project_milestones" rows="4" 
                                  placeholder="List key milestones and project phases..."></textarea>
                    </div>

                    <div class="info-box warning">
                        <div class="info-box-title">
                            <i class="fas fa-clock"></i> Timeline Extensions
                        </div>
                        <div class="info-box-content">
                            Timeline may be extended under these conditions:
                            <ul>
                                <li>Force majeure events (natural disasters, pandemics, etc.)</li>
                                <li>Client-caused delays (late payments, access restrictions, change requests)</li>
                                <li>Unforeseen site conditions requiring additional work</li>
                                <li>Material or labor shortages beyond contractor's control</li>
                                <li>All extensions require mutual written agreement</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Contract Price & Value -->
                <div class="contract-section">
                    <div class="section-header">
                        <div class="section-number">5</div>
                        <div class="section-title">Contract Price & Value</div>
                    </div>

                    <div class="info-box">
                        <div class="info-box-title">
                            <i class="fas fa-money-bill-wave"></i> Financial Terms
                        </div>
                        <div class="info-box-content">
                            Defines the total financial value of the agreement and pricing structure.
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="contractValue">Total Contract Amount (Rs.) <span class="required">*</span></label>
                            <input type="number" id="contractValue" name="contract_value" min="0" step="0.01" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="currency">Currency <span class="required">*</span></label>
                            <input type="text" id="currency" name="currency" value="LKR (Sri Lankan Rupees)" readonly required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pricingModel">Pricing Model <span class="required">*</span></label>
                        <select id="pricingModel" name="pricing_model" required>
                            <option value="">-- Select Pricing Model --</option>
                            <option value="Fixed">Fixed Price</option>
                            <option value="Milestone">Milestone-Based</option>
                            <option value="Time">Time & Materials</option>
                            <option value="Hybrid">Hybrid</option>
                        </select>
                    </div>

                    <div class="info-box success">
                        <div class="info-box-title">
                            <i class="fas fa-calculator"></i> Cost Breakdown
                        </div>
                        <div class="info-box-content">
                            <strong>Materials:</strong> Rs. <span id="materialsCost">0.00</span><br>
                            <strong>Labor:</strong> Rs. <span id="laborCost">0.00</span><br>
                            <strong>Equipment:</strong> Rs. <span id="equipmentCost">0.00</span><br>
                            <strong>Other Costs:</strong> Rs. <span id="otherCosts">0.00</span><br>
                            <strong>Taxes (if applicable):</strong> Rs. <span id="taxAmount">0.00</span><br>
                            <hr style="margin: 10px 0; border: none; border-top: 1px solid #c3e6cb;">
                            <strong>Total Contract Value:</strong> Rs. <span id="totalValue">0.00</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="taxInclusion">Tax Inclusion <span class="required">*</span></label>
                        <select id="taxInclusion" name="tax_inclusion" required>
                            <option value="Inclusive">Tax Inclusive</option>
                            <option value="Exclusive">Tax Exclusive</option>
                            <option value="NA">Not Applicable</option>
                        </select>
                    </div>
                </div>

                <!-- Section 6: Payment Terms & Schedule -->
                <div class="contract-section">
                    <div class="section-header">
                        <div class="section-number">6</div>
                        <div class="section-title">Payment Terms & Schedule</div>
                    </div>

                    <div class="info-box">
                        <div class="info-box-title">
                            <i class="fas fa-credit-card"></i> Payment Management
                        </div>
                        <div class="info-box-content">
                            Avoids payment disputes and protects cash flow for both parties.
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="paymentMethod">Payment Method <span class="required">*</span></label>
                        <select id="paymentMethod" name="payment_method" required>
                            <option value="">-- Select Payment Method --</option>
                            <option value="full_upfront">Full Payment Upfront (100%)</option>
                            <option value="50_50">50% Advance + 50% Completion</option>
                            <option value="30_70">30% Advance + 70% Completion</option>
                            <option value="milestone_based">Milestone-Based Payments</option>
                            <option value="completion">Full Payment on Completion</option>
                        </select>
                    </div>

                    <div id="milestoneTableContainer" style="display: none;">
                        <label style="font-weight: 600; color: #2d3748; margin-bottom: 10px; display: block;">
                            Payment Milestone Schedule <span class="required">*</span>
                        </label>
                        <table class="milestone-table" id="milestoneTable">
                            <thead>
                                <tr>
                                    <th>Milestone</th>
                                    <th>Description</th>
                                    <th>% of Total</th>
                                    <th>Amount (Rs.)</th>
                                    <th>Due Date</th>
                                </tr>
                            </thead>
                            <tbody id="milestoneTableBody">
                                <!-- Dynamically generated -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" style="text-align: right;"><strong>Total:</strong></td>
                                    <td><strong id="totalPercentage">0</strong>%</td>
                                    <td><strong>Rs. <span id="totalAmount">0.00</span></strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="form-row" style="margin-top: 20px;">
                        <div class="form-group">
                            <label for="paymentDueDays">Payment Due (Days after Invoice)</label>
                            <input type="number" id="paymentDueDays" name="payment_due_days" min="0" value="7">
                        </div>
                        <div class="form-group">
                            <label for="bankTransferMethod">Bank Transfer Method</label>
                            <select id="bankTransferMethod" name="bank_transfer_method">
                                <option value="Direct">Direct Bank Transfer</option>
                                <option value="Online">Online Payment Gateway</option>
                                <option value="Cash">Cash (for small amounts only)</option>
                                <option value="Check">Bank Check</option>
                            </select>
                        </div>
                    </div>

                    <div class="info-box warning">
                        <div class="info-box-title">
                            <i class="fas fa-exclamation-triangle"></i> Late Payment Consequences
                        </div>
                        <div class="info-box-content">
                            <strong>Important:</strong> Late or non-payment may result in:
                            <ul>
                                <li>Work suspension after 7 days of payment delay</li>
                                <li>Late payment penalty of 1.5% per month on outstanding amount</li>
                                <li>Termination of contract after 30 days of non-payment</li>
                                <li>Client remains liable for work completed plus penalties</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Section 7: Variations & Changes -->
                <div class="contract-section">
                    <div class="section-header">
                        <div class="section-number">7</div>
                        <div class="section-title">Variations & Changes</div>
                    </div>

                    <div class="info-box">
                        <div class="info-box-title">
                            <i class="fas fa-edit"></i> Change Control
                        </div>
                        <div class="info-box-content">
                            Controls scope creep and ensures all changes are documented and approved.
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="variationsClause">Variations & Change Orders Policy</label>
                        <textarea id="variationsClause" name="variations_clause" rows="6" readonly>Any changes to the original scope of work must be:
1. Requested in writing through the FixLanka platform
2. Reviewed and assessed for cost and timeline impact
3. Mutually approved by both parties before implementation
4. Documented with updated pricing and timeline
5. Logged in the system as a variation order

Unapproved changes will not be compensated. Both parties agree to negotiate in good faith for any necessary modifications.</textarea>
                    </div>

                    <div class="template-buttons">
                        <button type="button" class="template-btn" onclick="insertTemplate('variations', 'variationsClause')">
                            <i class="fas fa-file-alt"></i> Use Standard Template
                        </button>
                    </div>
                </div>

                <!-- Section 8: Communication & Negotiation -->
                <div class="contract-section">
                    <div class="section-header">
                        <div class="section-number">8</div>
                        <div class="section-title">Communication & Negotiation</div>
                    </div>

                    <div class="info-box">
                        <div class="info-box-title">
                            <i class="fas fa-comments"></i> Transparent Communication
                        </div>
                        <div class="info-box-content">
                            Ensures all communications are traceable and disputes are handled professionally.
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="communicationClause">Communication & Dispute Resolution</label>
                        <textarea id="communicationClause" name="communication_clause" rows="6" readonly>All official communications regarding this contract must be conducted through the FixLanka platform messaging system to ensure:
- Proper documentation and traceability
- Timestamp records of all communications
- Clear accountability for decisions

In case of disagreements, both parties agree to:
1. First attempt resolution through good-faith negotiation
2. Use the platform's mediation features if direct negotiation fails
3. Maintain professional and respectful communication at all times
4. Document all agreements reached during negotiation</textarea>
                    </div>

                    <div class="template-buttons">
                        <button type="button" class="template-btn" onclick="insertTemplate('communication', 'communicationClause')">
                            <i class="fas fa-file-alt"></i> Use Standard Template
                        </button>
                    </div>
                </div>

                <!-- Section 9: Project Delays & Responsibilities -->
                <div class="contract-section">
                    <div class="section-header">
                        <div class="section-number">9</div>
                        <div class="section-title">Project Delays & Responsibilities</div>
                    </div>

                    <div class="info-box">
                        <div class="info-box-title">
                            <i class="fas fa-hourglass-half"></i> Delay Management
                        </div>
                        <div class="info-box-content">
                            Clarifies accountability for delays and defines reasonable correction measures.
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="delaysClause">Delays & Responsibilities</label>
                        <textarea id="delaysClause" name="delays_clause" rows="8" readonly>CLIENT-CAUSED DELAYS:
- Delays in providing site access
- Late payment of milestone amounts
- Delayed approval of designs or materials
- Changes in project requirements
- Failure to provide necessary permissions

CONTRACTOR-CAUSED DELAYS:
- Failure to meet milestone deadlines without valid reason
- Poor resource management
- Non-compliance with quality standards

RESOLUTION:
Both parties agree to communicate delays promptly and work together on reasonable timeline adjustments. Extended delays may result in contract renegotiation or termination as per clause 10.</textarea>
                    </div>

                    <div class="template-buttons">
                        <button type="button" class="template-btn" onclick="insertTemplate('delays', 'delaysClause')">
                            <i class="fas fa-file-alt"></i> Use Standard Template
                        </button>
                    </div>
                </div>

                <!-- Section 10: Termination of Agreement -->
                <div class="contract-section">
                    <div class="section-header">
                        <div class="section-number">10</div>
                        <div class="section-title">Termination of Agreement</div>
                    </div>

                    <div class="info-box">
                        <div class="info-box-title">
                            <i class="fas fa-ban"></i> Exit Terms
                        </div>
                        <div class="info-box-content">
                            Provides a lawful exit path for both parties under specific conditions.
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="terminationClause">Termination Conditions</label>
                        <textarea id="terminationClause" name="termination_clause" rows="8" readonly>This contract may be terminated under the following conditions:

BY CLIENT:
- 7 days written notice if contractor fails to perform work
- Immediate termination for abandonment of work
- Material breach of contract terms

BY CONTRACTOR:
- Non-payment for more than 30 days after due date
- Client prevents access to site repeatedly
- Material breach by client

MUTUAL TERMINATION:
- Both parties may agree to terminate with written consent

Upon termination:
- Payment for work completed to date is required
- Materials purchased become client property upon payment
- All project documents and records are handed over
- Outstanding invoices must be settled within 14 days</textarea>
                    </div>

                    <div class="template-buttons">
                        <button type="button" class="template-btn" onclick="insertTemplate('termination', 'terminationClause')">
                            <i class="fas fa-file-alt"></i> Use Standard Template
                        </button>
                    </div>
                </div>

                <!-- Section 11: Force Majeure -->
                <div class="contract-section">
                    <div class="section-header">
                        <div class="section-number">11</div>
                        <div class="section-title">Force Majeure</div>
                    </div>

                    <div class="info-box">
                        <div class="info-box-title">
                            <i class="fas fa-shield-alt"></i> Uncontrollable Events
                        </div>
                        <div class="info-box-content">
                            Protects both parties from liability due to unforeseeable circumstances.
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="forceMajeureClause">Force Majeure</label>
                        <textarea id="forceMajeureClause" name="force_majeure_clause" rows="7" readonly>Neither party shall be liable for failure to perform obligations due to events beyond reasonable control, including:
- Natural disasters (floods, earthquakes, storms)
- Pandemics or epidemics
- Government actions or regulations
- War, terrorism, or civil unrest
- Strikes or labor disputes (external)
- Severe material shortages

The affected party must:
- Notify the other party within 7 days
- Provide reasonable evidence
- Take reasonable steps to minimize impact
- Resume performance as soon as possible

Timeline extensions will be granted for the duration of the force majeure event.</textarea>
                    </div>

                    <div class="template-buttons">
                        <button type="button" class="template-btn" onclick="insertTemplate('forceMajeure', 'forceMajeureClause')">
                            <i class="fas fa-file-alt"></i> Use Standard Template
                        </button>
                    </div>
                </div>

                <!-- Section 12: Liability Limitation -->
                <div class="contract-section">
                    <div class="section-header">
                        <div class="section-number">12</div>
                        <div class="section-title">Liability Limitation</div>
                    </div>

                    <div class="info-box">
                        <div class="info-box-title">
                            <i class="fas fa-balance-scale"></i> Risk Management
                        </div>
                        <div class="info-box-content">
                            Limits financial risk exposure while maintaining legal validity.
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="liabilityClause">Limitation of Liability</label>
                        <textarea id="liabilityClause" name="liability_clause" rows="7" readonly>EXCLUSIONS:
Neither party shall be liable for indirect, incidental, or consequential damages including:
- Loss of profits or business opportunities
- Loss of data or information
- Third-party claims (except as required by law)

MAXIMUM LIABILITY:
Total liability under this contract is limited to the total contract value stated in Section 5.

EXCEPTIONS:
This limitation does not apply to:
- Willful misconduct or gross negligence
- Death or personal injury
- Fraud or fraudulent misrepresentation
- Violations of applicable law

Both parties acknowledge this limitation is reasonable given the nature and value of this project.</textarea>
                    </div>

                    <div class="template-buttons">
                        <button type="button" class="template-btn" onclick="insertTemplate('liability', 'liabilityClause')">
                            <i class="fas fa-file-alt"></i> Use Standard Template
                        </button>
                    </div>
                </div>

                <!-- Section 13: Governing Law & Acceptance -->
                <div class="contract-section">
                    <div class="section-header">
                        <div class="section-number">13</div>
                        <div class="section-title">Governing Law & Acceptance</div>
                    </div>

                    <div class="info-box">
                        <div class="info-box-title">
                            <i class="fas fa-gavel"></i> Legal Framework
                        </div>
                        <div class="info-box-content">
                            Defines legal jurisdiction and confirms mutual agreement to all terms.
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="governingLawClause">Governing Law & Entire Agreement</label>
                        <textarea id="governingLawClause" name="governing_law_clause" rows="6" readonly>GOVERNING LAW:
This contract is governed by and construed in accordance with the laws of the Democratic Socialist Republic of Sri Lanka.

ENTIRE AGREEMENT:
This document constitutes the entire agreement between the parties and supersedes all prior negotiations, representations, or agreements.

AMENDMENTS:
Any modifications must be made in writing and signed by both parties through the FixLanka platform.

SEVERABILITY:
If any provision is found invalid, the remaining provisions continue in full effect.</textarea>
                    </div>

                    <div class="form-group">
                        <label for="acceptanceMethod">Acceptance Method <span class="required">*</span></label>
                        <select id="acceptanceMethod" name="acceptance_method" required>
                            <option value="Digital">Digital Signature (via Platform)</option>
                            <option value="Physical">Physical Signature</option>
                            <option value="Both">Both Digital and Physical</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="contractDate">Contract Date <span class="required">*</span></label>
                        <input type="date" id="contractDate" name="contract_date" required>
                    </div>

                    <div class="template-buttons">
                        <button type="button" class="template-btn" onclick="insertTemplate('governingLaw', 'governingLawClause')">
                            <i class="fas fa-file-alt"></i> Use Standard Template
                        </button>
                    </div>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn btn-secondary" onclick="prevStep()">
                        <i class="fas fa-arrow-left"></i> Previous
                    </button>
                    <button type="button" class="btn btn-primary" onclick="nextStep()">
                        Next <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 4: Review & Submit -->
            <div class="form-step" data-step="4">
                <h2 class="step-title">Review & Submit Contract</h2>
                <p class="step-description">Review all details before sending to client</p>

                <div class="preview-container" id="contractPreview">
                    <div class="preview-header">
                        <h2>SERVICE CONTRACT AGREEMENT</h2>
                        <p style="color: #718096; font-size: 14px;">FixLanka Digital Contract System</p>
                    </div>

                    <!-- Preview content will be dynamically generated -->
                    <div id="previewContent"></div>
                </div>

                <div class="info-box success" style="margin-top: 30px;">
                    <div class="info-box-title">
                        <i class="fas fa-check-circle"></i> Next Steps
                    </div>
                    <div class="info-box-content">
                        After submission:
                        <ol style="margin-left: 20px; margin-top: 8px;">
                            <li>Contract will be sent to the client for review</li>
                            <li>Client will receive email and platform notification</li>
                            <li>Client can accept, reject, or request modifications</li>
                            <li>Both parties can negotiate terms through the platform</li>
                            <li>Once accepted, contract becomes legally binding</li>
                        </ol>
                    </div>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn btn-secondary" onclick="prevStep()">
                        <i class="fas fa-arrow-left"></i> Previous
                    </button>
                    <button type="submit" class="btn btn-success" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> Submit Contract
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../assets/javascript/company/contracts.js"></script>
</body>
</html>
