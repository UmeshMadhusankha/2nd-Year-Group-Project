<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Milestone Plan - FixLanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/contracts.css">
    <style>
        /* Customer Milestone Plan Review Styles */
        .plan-review-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .page-header {
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #3498db;
        }

        .page-header h1 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }

        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .contract-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .summary-item {
            display: flex;
            flex-direction: column;
        }

        .summary-label {
            font-size: 13px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
        }

        .summary-value.large {
            font-size: 24px;
            color: #3498db;
        }

        /* Timeline Visualization */
        .timeline-container {
            margin: 30px 0;
        }

        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .timeline-header h2 {
            margin: 0;
            color: #2c3e50;
        }

        .view-toggle {
            display: flex;
            gap: 10px;
        }

        .view-toggle button {
            padding: 8px 16px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .view-toggle button.active {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }

        /* Timeline View */
        .timeline-view {
            position: relative;
            padding-left: 40px;
        }

        .timeline-item {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
            position: relative;
        }

        .timeline-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: -16px;
            top: 60px;
            width: 2px;
            height: calc(100% - 40px);
            background: linear-gradient(to bottom, #3498db, #e0e0e0);
        }

        .timeline-marker {
            position: absolute;
            left: -40px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
        }

        .timeline-content {
            flex: 1;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }

        .milestone-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .milestone-title {
            flex: 1;
        }

        .milestone-title h3 {
            margin: 0 0 5px 0;
            color: #2c3e50;
        }

        .milestone-subtitle {
            font-size: 13px;
            color: #7f8c8d;
        }

        .milestone-payment {
            text-align: right;
        }

        .payment-percentage {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .payment-amount {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }

        .milestone-description {
            margin: 15px 0;
            color: #555;
            line-height: 1.6;
        }

        .milestone-dates {
            display: flex;
            gap: 20px;
            margin: 15px 0;
            padding: 12px;
            background: white;
            border-radius: 6px;
        }

        .date-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .date-item span:first-child {
            font-weight: 600;
            color: #7f8c8d;
        }

        .deliverables-list {
            margin-top: 15px;
        }

        .deliverables-list h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #2c3e50;
        }

        .deliverables-list ul {
            margin: 0;
            padding-left: 20px;
        }

        .deliverables-list li {
            margin-bottom: 8px;
            color: #555;
        }

        .deliverables-list li::marker {
            color: #3498db;
        }

        .dependency-note {
            background: #fff3cd;
            padding: 10px;
            border-radius: 6px;
            font-size: 13px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Grid View */
        .grid-view {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            display: none;
        }

        .grid-view.active {
            display: grid;
        }

        .milestone-card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.3s;
        }

        .milestone-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-color: #3498db;
        }

        /* Payment Breakdown */
        .payment-breakdown {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin: 30px 0;
        }

        .payment-breakdown h2 {
            margin: 0 0 20px 0;
            color: white;
        }

        .breakdown-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .breakdown-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 6px;
            backdrop-filter: blur(10px);
        }

        .breakdown-label {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .breakdown-value {
            font-size: 22px;
            font-weight: bold;
        }

        /* Feedback Section */
        .feedback-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }

        .feedback-section h3 {
            margin-top: 0;
            color: #2c3e50;
        }

        .feedback-textarea {
            width: 100%;
            min-height: 120px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: inherit;
            font-size: 14px;
            resize: vertical;
        }

        .feedback-textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
        }

        .btn {
            padding: 14px 32px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-approve {
            background: #27ae60;
            color: white;
        }

        .btn-approve:hover {
            background: #229954;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
        }

        .btn-changes {
            background: #f39c12;
            color: white;
        }

        .btn-changes:hover {
            background: #e67e22;
            transform: translateY(-2px);
        }

        .btn-reject {
            background: #e74c3c;
            color: white;
        }

        .btn-reject:hover {
            background: #c0392b;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            max-width: 500px;
            width: 90%;
        }

        .modal-content h3 {
            margin-top: 0;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            display: none;
            min-width: 300px;
            animation: slideIn 0.3s ease-out;
        }

        .toast.show {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .toast.success {
            border-left: 4px solid #27ae60;
        }

        .toast.error {
            border-left: 4px solid #e74c3c;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .timeline-view {
                padding-left: 30px;
            }

            .timeline-marker {
                left: -30px;
                width: 40px;
                height: 40px;
                font-size: 16px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="plan-review-container">
        <!-- Header -->
        <div class="page-header">
            <h1>
                <span>📋</span>
                Review Milestone Plan
                <span class="status-badge pending" id="planStatus">Pending Your Approval</span>
            </h1>
            <p>Review the proposed milestone plan and payment schedule for this contract.</p>
        </div>

        <!-- Contract Summary -->
        <div class="contract-summary">
            <div class="summary-item">
                <span class="summary-label">Contract ID</span>
                <span class="summary-value" id="contractId">Loading...</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Budget</span>
                <span class="summary-value large" id="totalBudget">Loading...</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Milestones</span>
                <span class="summary-value" id="totalMilestones">Loading...</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Contract Duration</span>
                <span class="summary-value" id="duration">Loading...</span>
            </div>
        </div>

        <!-- Payment Breakdown -->
        <div class="payment-breakdown">
            <h2>💰 Payment Breakdown</h2>
            <div class="breakdown-grid" id="paymentBreakdown">
                <!-- Will be populated dynamically -->
            </div>
        </div>

        <!-- Timeline/Grid Toggle -->
        <div class="timeline-container">
            <div class="timeline-header">
                <h2>Milestone Details</h2>
                <div class="view-toggle">
                    <button class="active" onclick="switchView('timeline')">📅 Timeline View</button>
                    <button onclick="switchView('grid')">📊 Grid View</button>
                </div>
            </div>

            <!-- Timeline View -->
            <div class="timeline-view active" id="timelineView">
                <!-- Will be populated dynamically -->
            </div>

            <!-- Grid View -->
            <div class="grid-view" id="gridView">
                <!-- Will be populated dynamically -->
            </div>
        </div>

        <!-- Feedback Section -->
        <div class="feedback-section">
            <h3>💬 Comments or Questions (Optional)</h3>
            <textarea id="feedbackText" class="feedback-textarea" 
                      placeholder="Add any comments, questions, or concerns about this milestone plan..."></textarea>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button class="btn btn-changes" onclick="showChangesModal()">
                📝 Request Changes
            </button>
            <button class="btn btn-approve" onclick="showApproveModal()">
                ✅ Approve Plan
            </button>
        </div>
    </div>

    <!-- Approve Confirmation Modal -->
    <div class="modal" id="approveModal">
        <div class="modal-content">
            <h3>✅ Approve Milestone Plan</h3>
            <p>By approving this plan, you agree to:</p>
            <ul>
                <li>The proposed milestone breakdown</li>
                <li>The payment schedule</li>
                <li>The timeline for each milestone</li>
            </ul>
            <p><strong>The first milestone will become active immediately.</strong></p>
            <div class="modal-buttons">
                <button class="btn" style="background: #95a5a6;" onclick="closeModal('approveModal')">Cancel</button>
                <button class="btn btn-approve" onclick="approvePlan()">Confirm Approval</button>
            </div>
        </div>
    </div>

    <!-- Request Changes Modal -->
    <div class="modal" id="changesModal">
        <div class="modal-content">
            <h3>📝 Request Changes</h3>
            <p>Please explain what changes you'd like to see in the milestone plan:</p>
            <textarea id="changesReason" class="feedback-textarea" 
                      placeholder="E.g., I'd like more milestones, different payment distribution, etc."
                      required></textarea>
            <div class="modal-buttons">
                <button class="btn" style="background: #95a5a6;" onclick="closeModal('changesModal')">Cancel</button>
                <button class="btn btn-changes" onclick="requestChanges()">Submit Request</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <span id="toastIcon"></span>
        <div>
            <div id="toastTitle" style="font-weight: bold; margin-bottom: 4px;"></div>
            <div id="toastMessage" style="font-size: 14px; color: #666;"></div>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/customer/milestone-plan-review.js"></script>
</body>
</html>
