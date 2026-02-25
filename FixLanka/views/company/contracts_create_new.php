<?php
/**
 * Contract Creation Form - UX Optimized Multi-Step Design
 * 
 * This form follows excellent UX principles:
 * - Auto-fill > Manual entry
 * - Read-only where possible
 * - Progressive disclosure
 * - Clear status at all times
 * - Draft-friendly
 * 
 * Steps: Parties → Project → Scope → Timeline → Payments → Review & Send
 * 
 * @package FixLanka\Views\Company
 * @version 3.0.0 - Complete UX Redesign
 */

session_start();
require_once __DIR__ . '/../../config/database.php';

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Company') {
    header('Location: ../../views/auth/login.php');
    exit();
}

$company_id = $_SESSION['user_id'];

// Fetch company details (auto-fill)
try {
    $stmt = $pdo->prepare("
        SELECT 
            c.company_id,
            c.name as company_name,
            c.business_registration_no,
            c.address as company_address,
            c.contact_no as company_contact,
            c.email as company_email,
            CONCAT(u.f_name, ' ', u.l_name) as company_representative
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
    die("Error: " . $e->getMessage());
}

// Get accepted quotations for auto-fill
$quotation_id = isset($_GET['quotation_id']) ? intval($_GET['quotation_id']) : null;
$quotation_data = null;

if ($quotation_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                q.*,
                c.name as client_name,
                c.address as client_address,
                c.contact_no as client_contact,
                c.email as client_email,
                CONCAT(u.f_name, ' ', u.l_name) as client_representative
            FROM Quotation q
            INNER JOIN Client c ON q.client_id = c.client_id
            INNER JOIN User u ON c.user_id = u.user_id
            WHERE q.quotation_id = ? AND q.company_id = ? AND q.status = 'Accepted'
        ");
        $stmt->execute([$quotation_id, $company_id]);
        $quotation_data = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

// Fetch all clients with accepted quotations
try {
    $stmt = $pdo->prepare("
        SELECT DISTINCT
            c.client_id,
            c.name as client_name,
            c.address as client_address,
            c.contact_no as client_contact,
            c.email as client_email,
            CONCAT(u.f_name, ' ', u.l_name) as client_representative
        FROM Client c
        INNER JOIN User u ON c.user_id = u.user_id
        INNER JOIN Quotation q ON c.client_id = q.client_id
        WHERE q.company_id = ? AND q.status = 'Accepted'
        ORDER BY c.name ASC
    ");
    $stmt->execute([$company_id]);
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $clients = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Contract - FixLanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: var(--gray-50);
            color: var(--gray-900);
            line-height: 1.6;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 24px;
        }

        /* Header */
        .header {
            background: white;
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 8px;
        }

        .header p {
            color: var(--gray-600);
            font-size: 15px;
        }

        /* Progress Indicator */
        .progress-container {
            background: white;
            padding: 32px 24px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 16px;
        }

        .progress-line {
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--gray-200);
            z-index: 0;
        }

        .progress-line-fill {
            height: 100%;
            background: var(--primary);
            transition: width 0.3s ease;
        }

        .step {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 2px solid var(--gray-300);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: var(--gray-600);
            transition: all 0.3s ease;
            margin-bottom: 8px;
        }

        .step.active .step-circle {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            transform: scale(1.1);
        }

        .step.completed .step-circle {
            background: var(--success);
            border-color: var(--success);
            color: white;
        }

        .step-label {
            font-size: 13px;
            color: var(--gray-600);
            text-align: center;
            font-weight: 500;
        }

        .step.active .step-label {
            color: var(--primary);
            font-weight: 600;
        }

        /* Form Container */
        .form-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .form-step {
            display: none;
            padding: 32px;
        }

        .form-step.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Section Styles */
        .section {
            margin-bottom: 32px;
        }

        .section:last-child {
            margin-bottom: 0;
        }

        .section-header {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--primary);
        }

        .section-subtitle {
            font-size: 14px;
            color: var(--gray-600);
        }

        /* Card Styles */
        .card {
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
        }

        .card.readonly {
            background: var(--gray-100);
            border-style: dashed;
        }

        .card-header {
            font-size: 15px;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .readonly-badge {
            font-size: 12px;
            background: var(--gray-300);
            color: var(--gray-700);
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 500;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .card-grid.single {
            grid-template-columns: 1fr;
        }

        /* Form Fields */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 6px;
        }

        .required {
            color: var(--danger);
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            font-size: 15px;
            border: 1px solid var(--gray-300);
            border-radius: 6px;
            transition: all 0.2s;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-control:disabled,
        .form-control:read-only {
            background: var(--gray-100);
            color: var(--gray-600);
            cursor: not-allowed;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        select.form-control {
            cursor: pointer;
        }

        .form-hint {
            font-size: 13px;
            color: var(--gray-600);
            margin-top: 4px;
        }

        /* Info Box */
        .info-box {
            background: #eff6ff;
            border-left: 4px solid var(--primary);
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            gap: 12px;
        }

        .info-box i {
            color: var(--primary);
            margin-top: 2px;
        }

        .info-box.warning {
            background: #fef3c7;
            border-left-color: var(--warning);
        }

        .info-box.warning i {
            color: var(--warning);
        }

        /* Table Styles */
        .table-container {
            overflow-x: auto;
            margin-top: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: var(--gray-50);
        }

        th {
            padding: 12px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-700);
            border-bottom: 2px solid var(--gray-200);
        }

        td {
            padding: 12px;
            border-bottom: 1px solid var(--gray-200);
        }

        tbody tr:hover {
            background: var(--gray-50);
        }

        /* Milestone Builder */
        .milestone-item {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
        }

        .milestone-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--gray-200);
        }

        .milestone-number {
            font-weight: 600;
            color: var(--primary);
        }

        .btn-remove {
            background: none;
            border: none;
            color: var(--danger);
            cursor: pointer;
            font-size: 14px;
            padding: 4px 8px;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .btn-remove:hover {
            background: #fee2e2;
        }

        .btn-add {
            background: white;
            border: 2px dashed var(--gray-300);
            color: var(--primary);
            padding: 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            width: 100%;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-add:hover {
            border-color: var(--primary);
            background: #eff6ff;
        }

        /* Payment Summary */
        .payment-summary {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 15px;
        }

        .summary-row.total {
            border-top: 2px solid #86efac;
            padding-top: 12px;
            margin-top: 8px;
            font-weight: 700;
            font-size: 18px;
            color: var(--success);
        }

        /* Review Section */
        .review-section {
            background: var(--gray-50);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .review-section h4 {
            font-size: 16px;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--gray-200);
        }

        .review-item {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid var(--gray-200);
        }

        .review-item:last-child {
            border-bottom: none;
        }

        .review-label {
            font-weight: 500;
            color: var(--gray-700);
            min-width: 180px;
        }

        .review-value {
            color: var(--gray-900);
            flex: 1;
        }

        /* Action Buttons */
        .form-actions {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding-top: 24px;
            border-top: 1px solid var(--gray-200);
            margin-top: 32px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-secondary {
            background: white;
            border: 1px solid var(--gray-300);
            color: var(--gray-700);
        }

        .btn-secondary:hover {
            background: var(--gray-50);
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success:hover {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Checkbox Styles */
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px;
            background: var(--gray-50);
            border-radius: 6px;
            margin-bottom: 12px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .checkbox-group label {
            cursor: pointer;
            font-size: 14px;
            color: var(--gray-700);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 16px;
            }

            .card-grid {
                grid-template-columns: 1fr;
            }

            .progress-steps {
                overflow-x: auto;
            }

            .step-label {
                font-size: 11px;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Loading Spinner */
        .loading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .loading.active {
            display: flex;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<div class="loading" id="loading">
    <div class="spinner"></div>
</div>

<div class="container">
    <!-- Header -->
    <div class="header">
        <h1><i class="fas fa-file-contract"></i> Create Contract</h1>
        <p>Follow the guided steps to create a professional construction contract</p>
    </div>

    <!-- Progress Indicator -->
    <div class="progress-container">
        <div class="progress-steps">
            <div class="progress-line">
                <div class="progress-line-fill" id="progressFill" style="width: 0%"></div>
            </div>
            <div class="step active" data-step="1">
                <div class="step-circle">1</div>
                <div class="step-label">Parties</div>
            </div>
            <div class="step" data-step="2">
                <div class="step-circle">2</div>
                <div class="step-label">Project</div>
            </div>
            <div class="step" data-step="3">
                <div class="step-circle">3</div>
                <div class="step-label">Scope</div>
            </div>
            <div class="step" data-step="4">
                <div class="step-circle">4</div>
                <div class="step-label">Timeline</div>
            </div>
            <div class="step" data-step="5">
                <div class="step-circle">5</div>
                <div class="step-label">Payments</div>
            </div>
            <div class="step" data-step="6">
                <div class="step-circle">6</div>
                <div class="step-label">Review</div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form id="contractForm" method="POST" action="../../api/contracts/create.php">
        <div class="form-container">
            
            <!-- STEP 1: PARTIES -->
            <div class="form-step active" data-step="1">
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-users"></i>
                            Parties to the Contract
                        </h2>
                        <p class="section-subtitle">Contract parties information (Auto-filled from profiles)</p>
                    </div>

                    <div class="info-box">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Auto-filled data:</strong> This information is loaded from your company and client profiles. 
                            It's read-only to prevent legal errors. Update profiles if changes are needed.
                        </div>
                    </div>

                    <!-- Company Card (Read-only) -->
                    <div class="card readonly">
                        <div class="card-header">
                            <span><i class="fas fa-building"></i> Your Company (Contractor)</span>
                            <span class="readonly-badge">Read-only</span>
                        </div>
                        <div class="card-grid">
                            <div>
                                <div class="form-group">
                                    <label class="form-label">Company Name</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($company['company_name']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Business Registration No.</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($company['business_registration_no'] ?? 'N/A'); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Contact Number</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($company['company_contact']); ?>" readonly>
                                </div>
                            </div>
                            <div>
                                <div class="form-group">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" readonly style="min-height: 80px;"><?php echo htmlspecialchars($company['company_address']); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($company['company_email']); ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Client Selection -->
                    <div class="card">
                        <div class="card-header">
                            <span><i class="fas fa-user"></i> Client (Customer)</span>
                        </div>
                        
                        <?php if (count($clients) > 0): ?>
                        <div class="form-group">
                            <label class="form-label">Select Client <span class="required">*</span></label>
                            <select id="clientSelect" name="client_id" class="form-control" required>
                                <option value="">-- Select a client --</option>
                                <?php foreach ($clients as $client): ?>
                                <option value="<?php echo $client['client_id']; ?>"
                                        data-name="<?php echo htmlspecialchars($client['client_name']); ?>"
                                        data-address="<?php echo htmlspecialchars($client['client_address']); ?>"
                                        data-contact="<?php echo htmlspecialchars($client['client_contact']); ?>"
                                        data-email="<?php echo htmlspecialchars($client['client_email']); ?>"
                                        data-rep="<?php echo htmlspecialchars($client['client_representative']); ?>"
                                        <?php echo ($quotation_data && $quotation_data['client_id'] == $client['client_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($client['client_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-hint">Select the client for this contract</div>
                        </div>

                        <div id="clientDetails" style="display: none;">
                            <div class="card-grid">
                                <div>
                                    <div class="form-group">
                                        <label class="form-label">Client Name</label>
                                        <input type="text" id="clientName" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Contact Number</label>
                                        <input type="text" id="clientContact" class="form-control" readonly>
                                    </div>
                                </div>
                                <div>
                                    <div class="form-group">
                                        <label class="form-label">Address</label>
                                        <textarea id="clientAddress" class="form-control" readonly style="min-height: 80px;"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Email</label>
                                        <input type="email" id="clientEmail" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="info-box warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>No clients available:</strong> You need to have at least one accepted quotation before creating a contract.
                                <a href="quotations.php">Create a quotation first →</a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-actions">
                    <div></div>
                    <button type="button" class="btn btn-primary" onclick="nextStep(2)" <?php echo count($clients) == 0 ? 'disabled' : ''; ?>>
                        Next: Project Details <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 2: PROJECT -->
            <div class="form-step" data-step="2">
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-project-diagram"></i>
                            Project Overview
                        </h2>
                        <p class="section-subtitle">Define the construction project details</p>
                    </div>

                    <div class="info-box">
                        <i class="fas fa-lightbulb"></i>
                        <div>
                            <strong>Auto-fill tip:</strong> If you're creating this contract from an accepted quotation, 
                            most fields will be pre-filled. You can still edit them if needed.
                        </div>
                    </div>

                    <!-- Auto-fill from Quotation -->
                    <?php if (count($clients) > 0): ?>
                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-magic"></i> Auto-fill from Quotation (Optional)</label>
                        <select id="quotationSelect" class="form-control">
                            <option value="">-- Select a quotation to auto-fill --</option>
                            <?php
                            $stmt = $pdo->prepare("
                                SELECT q.quotation_id, q.project_name, q.project_description, q.total_amount, c.name as client_name
                                FROM Quotation q
                                INNER JOIN Client c ON q.client_id = c.client_id
                                WHERE q.company_id = ? AND q.status = 'Accepted'
                                ORDER BY q.created_at DESC
                            ");
                            $stmt->execute([$company_id]);
                            $quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($quotations as $q):
                            ?>
                            <option value="<?php echo $q['quotation_id']; ?>"
                                    data-project-name="<?php echo htmlspecialchars($q['project_name']); ?>"
                                    data-description="<?php echo htmlspecialchars($q['project_description']); ?>"
                                    data-amount="<?php echo $q['total_amount']; ?>">
                                <?php echo htmlspecialchars($q['project_name']); ?> - <?php echo htmlspecialchars($q['client_name']); ?> (LKR <?php echo number_format($q['total_amount'], 2); ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-hint">Select to automatically fill project details</div>
                    </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-grid">
                            <div class="form-group">
                                <label class="form-label">Project Name <span class="required">*</span></label>
                                <input type="text" id="projectName" name="project_name" class="form-control" 
                                       placeholder="e.g., Two-Story Residential Building"
                                       value="<?php echo $quotation_data ? htmlspecialchars($quotation_data['project_name']) : ''; ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Project ID</label>
                                <input type="text" class="form-control" value="Auto-generated" readonly>
                                <div class="form-hint">System will generate unique ID</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Site Address <span class="required">*</span></label>
                            <textarea id="siteAddress" name="site_address" class="form-control" 
                                      placeholder="Complete address where construction will take place" required><?php echo $quotation_data ? htmlspecialchars($quotation_data['site_address'] ?? '') : ''; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Project Description <span class="required">*</span></label>
                            <textarea id="projectDescription" name="project_description" class="form-control" 
                                      style="min-height: 120px;" 
                                      placeholder="Detailed description of the construction project..." required><?php echo $quotation_data ? htmlspecialchars($quotation_data['project_description']) : ''; ?></textarea>
                            <div class="form-hint">Be specific about what will be built</div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="prevStep(1)">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                    <button type="button" class="btn btn-primary" onclick="nextStep(3)">
                        Next: Scope of Work <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 3: SCOPE -->
            <div class="form-step" data-step="3">
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-tasks"></i>
                            Scope of Work
                        </h2>
                        <p class="section-subtitle">Define what work is included and excluded</p>
                    </div>

                    <div class="info-box warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Important:</strong> This is the most critical section. Clear scope definition prevents disputes.
                            Be specific about what's included and what's not.
                        </div>
                    </div>

                    <div class="card">
                        <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: var(--success);">
                            <i class="fas fa-check-circle"></i> Work Included
                        </h4>
                        <div class="form-group">
                            <label class="form-label">Detailed Scope of Work <span class="required">*</span></label>
                            <textarea id="scopeIncluded" name="scope_included" class="form-control" 
                                      style="min-height: 150px;" 
                                      placeholder="List all work that will be performed:&#10;- Foundation work&#10;- Wall construction&#10;- Roofing&#10;- Electrical wiring&#10;- Plumbing installations&#10;etc." required></textarea>
                            <div class="form-hint">Be as detailed as possible</div>
                        </div>

                        <h4 style="font-size: 16px; font-weight: 600; margin: 24px 0 16px 0; color: var(--danger);">
                            <i class="fas fa-times-circle"></i> Work Excluded
                        </h4>
                        <div class="form-group">
                            <label class="form-label">Exclusions (Optional but Recommended)</label>
                            <textarea id="scopeExcluded" name="scope_excluded" class="form-control" 
                                      style="min-height: 100px;" 
                                      placeholder="List work that is NOT included:&#10;- Interior decoration&#10;- Furniture&#10;- Landscaping&#10;etc."></textarea>
                            <div class="form-hint">Clarify what you won't be responsible for</div>
                        </div>

                        <h4 style="font-size: 16px; font-weight: 600; margin: 24px 0 16px 0; color: var(--primary);">
                            <i class="fas fa-toolbox"></i> Materials & Responsibilities
                        </h4>
                        
                        <div class="form-group">
                            <label class="form-label">Who provides materials? <span class="required">*</span></label>
                            <select id="materialsProvider" name="materials_provider" class="form-control" required>
                                <option value="">-- Select --</option>
                                <option value="contractor">Contractor provides all materials</option>
                                <option value="client">Client provides materials</option>
                                <option value="shared">Shared responsibility (specify below)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Materials Details</label>
                            <textarea id="materialsDetails" name="materials_details" class="form-control" 
                                      placeholder="Specify which materials will be provided and by whom..."></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Quality Standards <span class="required">*</span></label>
                            <textarea id="qualityStandards" name="quality_standards" class="form-control" 
                                      placeholder="e.g., All materials must meet SLS (Sri Lankan Standards). Work must comply with local building codes." required></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="prevStep(2)">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                    <button type="button" class="btn btn-primary" onclick="nextStep(4)">
                        Next: Timeline & Milestones <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 4: TIMELINE -->
            <div class="form-step" data-step="4">
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-calendar-alt"></i>
                            Project Duration & Milestones
                        </h2>
                        <p class="section-subtitle">Define project timeline and key milestones</p>
                    </div>

                    <div class="card">
                        <div class="card-grid">
                            <div class="form-group">
                                <label class="form-label">Start Date <span class="required">*</span></label>
                                <input type="date" id="startDate" name="start_date" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Completion Date <span class="required">*</span></label>
                                <input type="date" id="completionDate" name="completion_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Project Duration</label>
                            <input type="text" id="duration" class="form-control" readonly placeholder="Will be calculated">
                            <div class="form-hint">Calculated from start and completion dates</div>
                        </div>
                    </div>

                    <div class="card">
                        <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">
                            <i class="fas fa-flag-checkered"></i> Project Milestones
                        </h4>
                        <div class="info-box">
                            <i class="fas fa-info-circle"></i>
                            <div>Milestones help track progress and can be linked to payment schedules. Add key project phases below.</div>
                        </div>

                        <div id="milestonesContainer">
                            <!-- Milestones will be added here -->
                        </div>

                        <button type="button" class="btn-add" onclick="addMilestone()">
                            <i class="fas fa-plus"></i> Add Milestone
                        </button>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="prevStep(3)">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                    <button type="button" class="btn btn-primary" onclick="nextStep(5)">
                        Next: Payments <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 5: PAYMENTS -->
            <div class="form-step" data-step="5">
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-money-bill-wave"></i>
                            Pricing & Payment Terms
                        </h2>
                        <p class="section-subtitle">Define contract value and payment schedule</p>
                    </div>

                    <div class="card">
                        <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">
                            <i class="fas fa-calculator"></i> Contract Value
                        </h4>

                        <div class="card-grid">
                            <div class="form-group">
                                <label class="form-label">Total Contract Value (LKR) <span class="required">*</span></label>
                                <input type="number" id="contractValue" name="contract_value" class="form-control" 
                                       min="0" step="0.01" placeholder="0.00" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Pricing Type <span class="required">*</span></label>
                                <select id="pricingType" name="pricing_type" class="form-control" required>
                                    <option value="fixed">Fixed Price</option>
                                    <option value="time_material">Time & Material</option>
                                    <option value="unit_price">Unit Price</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">
                            <i class="fas fa-credit-card"></i> Payment Schedule
                        </h4>

                        <div class="card-grid">
                            <div class="form-group">
                                <label class="form-label">Advance Payment (%) <span class="required">*</span></label>
                                <input type="number" id="advancePayment" name="advance_payment" class="form-control" 
                                       min="0" max="100" step="5" placeholder="e.g., 30" required>
                                <div class="form-hint">Typical: 20-40%</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Payment Method <span class="required">*</span></label>
                                <select name="payment_method" class="form-control" required>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="cash">Cash</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Payment Terms <span class="required">*</span></label>
                            <textarea name="payment_terms" class="form-control" 
                                      placeholder="e.g., Payment due within 7 days of invoice. Late payments may incur penalty." required></textarea>
                        </div>

                        <!-- Payment Summary -->
                        <div class="payment-summary" id="paymentSummary" style="display: none;">
                            <h5 style="font-weight: 600; margin-bottom: 12px;">Payment Breakdown</h5>
                            <div class="summary-row">
                                <span>Advance Payment:</span>
                                <span id="advanceAmount">LKR 0.00</span>
                            </div>
                            <div class="summary-row">
                                <span>Remaining Amount:</span>
                                <span id="remainingAmount">LKR 0.00</span>
                            </div>
                            <div class="summary-row total">
                                <span>Total Contract Value:</span>
                                <span id="totalAmount">LKR 0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">
                            <i class="fas fa-shield-alt"></i> Additional Terms
                        </h4>

                        <div class="form-group">
                            <label class="form-label">Warranty Period</label>
                            <input type="text" name="warranty_period" class="form-control" 
                                   placeholder="e.g., 12 months from completion">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Delay Penalty (Optional)</label>
                            <textarea name="delay_penalty" class="form-control" 
                                      placeholder="e.g., LKR 5,000 per day after grace period of 7 days"></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="prevStep(4)">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                    <button type="button" class="btn btn-primary" onclick="nextStep(6)">
                        Next: Review & Finalize <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- STEP 6: REVIEW -->
            <div class="form-step" data-step="6">
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-clipboard-check"></i>
                            Review & Finalize
                        </h2>
                        <p class="section-subtitle">Review all contract details before sending</p>
                    </div>

                    <div class="info-box">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Important:</strong> Review all details carefully. Once sent to the client, 
                            you can only edit the contract if it's rejected or still in draft status.
                        </div>
                    </div>

                    <!-- Contract Summary -->
                    <div id="contractReview">
                        <!-- Will be populated by JavaScript -->
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="card">
                        <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">
                            <i class="fas fa-file-alt"></i> Additional Contract Terms
                        </h4>

                        <div class="checkbox-group">
                            <input type="checkbox" id="variationsClause" name="variations_clause" value="1" checked disabled>
                            <label for="variationsClause">All variations and changes must be approved through the FixLanka platform</label>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="communicationClause" name="communication_clause" value="1" checked disabled>
                            <label for="communicationClause">Official communication will be conducted via FixLanka platform messaging</label>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="disputeClause" name="dispute_clause" value="1" checked disabled>
                            <label for="disputeClause">Disputes will be resolved through negotiation first, followed by mediation if needed</label>
                        </div>

                        <div class="form-group" style="margin-top: 20px;">
                            <label class="form-label">Additional Notes (Optional)</label>
                            <textarea name="additional_notes" class="form-control" 
                                      placeholder="Any additional terms, conditions, or notes..."></textarea>
                        </div>
                    </div>

                    <!-- Confirmation -->
                    <div class="card" style="border: 2px solid var(--primary);">
                        <div class="checkbox-group" style="background: white;">
                            <input type="checkbox" id="confirmAccurate" required>
                            <label for="confirmAccurate"><strong>I confirm that all information provided is accurate and complete</strong></label>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="prevStep(5)">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                    <div style="display: flex; gap: 12px;">
                        <button type="button" class="btn btn-secondary" onclick="saveDraft()">
                            <i class="fas fa-save"></i> Save as Draft
                        </button>
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <i class="fas fa-paper-plane"></i> Send to Client
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
let currentStep = 1;
const totalSteps = 6;
let milestoneCount = 0;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Add first milestone by default
    addMilestone();
    
    // Client selection handler
    const clientSelect = document.getElementById('clientSelect');
    if (clientSelect) {
        clientSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (selected.value) {
                document.getElementById('clientName').value = selected.dataset.name || '';
                document.getElementById('clientAddress').value = selected.dataset.address || '';
                document.getElementById('clientContact').value = selected.dataset.contact || '';
                document.getElementById('clientEmail').value = selected.dataset.email || '';
                document.getElementById('clientDetails').style.display = 'block';
            } else {
                document.getElementById('clientDetails').style.display = 'none';
            }
        });
        
        // Trigger if pre-selected
        if (clientSelect.value) {
            clientSelect.dispatchEvent(new Event('change'));
        }
    }
    
    // Quotation auto-fill
    const quotationSelect = document.getElementById('quotationSelect');
    if (quotationSelect) {
        quotationSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (selected.value) {
                document.getElementById('projectName').value = selected.dataset.projectName || '';
                document.getElementById('projectDescription').value = selected.dataset.description || '';
                document.getElementById('contractValue').value = selected.dataset.amount || '';
                calculatePayment();
            }
        });
    }
    
    // Payment calculator
    const contractValue = document.getElementById('contractValue');
    const advancePayment = document.getElementById('advancePayment');
    
    if (contractValue) contractValue.addEventListener('input', calculatePayment);
    if (advancePayment) advancePayment.addEventListener('input', calculatePayment);
    
    // Date calculator
    const startDate = document.getElementById('startDate');
    const completionDate = document.getElementById('completionDate');
    
    if (startDate) startDate.addEventListener('change', calculateDuration);
    if (completionDate) completionDate.addEventListener('change', calculateDuration);
});

// Step Navigation
function nextStep(step) {
    if (validateStep(currentStep)) {
        // Update progress
        document.querySelector(`.step[data-step="${currentStep}"]`).classList.remove('active');
        document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('completed');
        document.querySelector(`.step[data-step="${step}"]`).classList.add('active');
        
        // Update progress bar
        const progress = ((step - 1) / (totalSteps - 1)) * 100;
        document.getElementById('progressFill').style.width = progress + '%';
        
        // Hide current, show next
        document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.remove('active');
        document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');
        
        currentStep = step;
        
        // Populate review if on last step
        if (step === 6) {
            populateReview();
        }
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function prevStep(step) {
    // Update progress
    document.querySelector(`.step[data-step="${currentStep}"]`).classList.remove('active');
    document.querySelector(`.step[data-step="${step}"]`).classList.remove('completed');
    document.querySelector(`.step[data-step="${step}"]`).classList.add('active');
    
    // Update progress bar
    const progress = ((step - 1) / (totalSteps - 1)) * 100;
    document.getElementById('progressFill').style.width = progress + '%';
    
    // Hide current, show previous
    document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.remove('active');
    document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');
    
    currentStep = step;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Validation
function validateStep(step) {
    const currentFormStep = document.querySelector(`.form-step[data-step="${step}"]`);
    const required = currentFormStep.querySelectorAll('[required]');
    let valid = true;
    
    required.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = 'var(--danger)';
            valid = false;
            
            field.addEventListener('input', function() {
                this.style.borderColor = 'var(--gray-300)';
            }, { once: true });
        }
    });
    
    if (!valid) {
        alert('Please fill in all required fields before proceeding.');
    }
    
    return valid;
}

// Milestone Management
function addMilestone() {
    milestoneCount++;
    const container = document.getElementById('milestonesContainer');
    
    const html = `
        <div class="milestone-item" id="milestone${milestoneCount}">
            <div class="milestone-header">
                <span class="milestone-number">Milestone ${milestoneCount}</span>
                ${milestoneCount > 1 ? `<button type="button" class="btn-remove" onclick="removeMilestone(${milestoneCount})">
                    <i class="fas fa-trash"></i> Remove
                </button>` : ''}
            </div>
            <div class="card-grid">
                <div class="form-group">
                    <label class="form-label">Milestone Name <span class="required">*</span></label>
                    <input type="text" name="milestone_name[]" class="form-control" 
                           placeholder="e.g., Foundation Complete" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Target Date <span class="required">*</span></label>
                    <input type="date" name="milestone_date[]" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="milestone_description[]" class="form-control" rows="2" 
                          placeholder="Brief description of this milestone..."></textarea>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', html);
}

function removeMilestone(id) {
    const milestone = document.getElementById('milestone' + id);
    if (milestone) {
        milestone.remove();
    }
}

// Calculators
function calculatePayment() {
    const value = parseFloat(document.getElementById('contractValue').value) || 0;
    const advance = parseFloat(document.getElementById('advancePayment').value) || 0;
    
    if (value > 0 && advance > 0) {
        const advanceAmount = (value * advance) / 100;
        const remaining = value - advanceAmount;
        
        document.getElementById('advanceAmount').textContent = 'LKR ' + advanceAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('remainingAmount').textContent = 'LKR ' + remaining.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('totalAmount').textContent = 'LKR ' + value.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('paymentSummary').style.display = 'block';
    } else {
        document.getElementById('paymentSummary').style.display = 'none';
    }
}

function calculateDuration() {
    const start = new Date(document.getElementById('startDate').value);
    const end = new Date(document.getElementById('completionDate').value);
    
    if (start && end && end > start) {
        const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
        const months = Math.floor(days / 30);
        const remainingDays = days % 30;
        
        let duration = '';
        if (months > 0) duration += months + ' month' + (months > 1 ? 's' : '');
        if (remainingDays > 0) duration += (months > 0 ? ' and ' : '') + remainingDays + ' day' + (remainingDays > 1 ? 's' : '');
        
        document.getElementById('duration').value = duration + ' (' + days + ' days)';
    }
}

// Review Population
function populateReview() {
    const sections = [
        {
            title: 'Parties',
            items: [
                { label: 'Client', value: document.getElementById('clientName')?.value || 'Not selected' }
            ]
        },
        {
            title: 'Project',
            items: [
                { label: 'Project Name', value: document.getElementById('projectName')?.value },
                { label: 'Site Address', value: document.getElementById('siteAddress')?.value },
                { label: 'Start Date', value: document.getElementById('startDate')?.value },
                { label: 'Completion Date', value: document.getElementById('completionDate')?.value }
            ]
        },
        {
            title: 'Financial',
            items: [
                { label: 'Contract Value', value: 'LKR ' + (parseFloat(document.getElementById('contractValue')?.value) || 0).toLocaleString() },
                { label: 'Advance Payment', value: (document.getElementById('advancePayment')?.value || '0') + '%' }
            ]
        }
    ];
    
    let html = '';
    sections.forEach(section => {
        html += `<div class="review-section">
            <h4>${section.title}</h4>`;
        section.items.forEach(item => {
            html += `<div class="review-item">
                <div class="review-label">${item.label}:</div>
                <div class="review-value">${item.value || 'Not specified'}</div>
            </div>`;
        });
        html += `</div>`;
    });
    
    document.getElementById('contractReview').innerHTML = html;
}

// Form Submission
document.getElementById('contractForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!document.getElementById('confirmAccurate').checked) {
        alert('Please confirm that all information is accurate before submitting.');
        return;
    }
    
    document.getElementById('loading').classList.add('active');
    document.getElementById('submitBtn').disabled = true;
    
    // Submit form
    this.submit();
});

// Save Draft
function saveDraft() {
    const form = document.getElementById('contractForm');
    const formData = new FormData(form);
    formData.append('save_draft', '1');
    
    document.getElementById('loading').classList.add('active');
    
    fetch('../../api/contracts/create.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loading').classList.remove('active');
        if (data.success) {
            alert('Contract saved as draft successfully!');
            window.location.href = 'contracts.php';
        } else {
            alert('Error: ' + (data.message || 'Failed to save draft'));
        }
    })
    .catch(error => {
        document.getElementById('loading').classList.remove('active');
        alert('Error: ' + error.message);
    });
}
</script>

</body>
</html>
