<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include components
require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/_components/Common.php';
require_once __DIR__ . '/../../includes/admin-modarator/auth.php';
require_once __DIR__ . '/../../includes/admin-modarator/mock-data.php';

$basePath = '';
$currentPath = 'alerts';
$message = '';

$recentAlerts = [
    [
        'id' => 1,
        'message' => 'System maintenance scheduled for August 5th, 10 PM – 12 AM',
        'recipients' => 'All Users',
        'priority' => 'high',
        'sentAt' => '2024-07-25 14:30',
        'status' => 'Sent'
    ],
    [
        'id' => 2,
        'message' => 'New feature: Real-time chat with service providers now available',
        'recipients' => 'Customers',
        'priority' => 'medium',
        'sentAt' => '2024-07-24 09:15',
        'status' => 'Sent'
    ],
    [
        'id' => 3,
        'message' => 'Commission rate update effective from next month',
        'recipients' => 'Service Providers',
        'priority' => 'high',
        'sentAt' => '2024-07-23 16:45',
        'status' => 'Sent'
    ]
];

// Get page title and description from variables or use defaults
$pageTitle = $title ?? 'Advanced PHP Router';
$pageDescription = $description ?? 'A Next.js-inspired PHP routing system with advanced features';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-background text-foreground">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderAdminSidebar($currentPath, $basePath); ?>
        <div class="dashboard-main">
            <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/alerts.css">

            <?php renderPageHeader($basePath, 'Send Alerts', 'Broadcast important messages to users across the platform'); ?>

            <main style="margin-top: 5rem;" class="dashboard-content">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight text-foreground">Send Alerts</h2>
                        <p class="text-muted-foreground">Broadcast important messages to users across the platform</p>
                    </div>

                    <!-- Success Message Container -->
                    <div id="successMessage" style="display: none;" class="bg-fixlanka-highlight/10 border border-fixlanka-highlight/20 text-fixlanka-primary px-4 py-3 rounded">
                        <span id="successMessageText"></span>
                    </div>

                    <div class="alerts-compose-grid">
                        <div class="alerts-compose-card">
                            <div class="alerts-compose-header">
                                <h3 class="text-lg font-medium w-full text-card-foreground flex items-center gap-2">
                                    <i data-lucide="send" class="h-5 w-5"></i>
                                    Compose Alert
                                </h3>
                                <p class="text-sm text-muted-foreground w-full">Send notifications to specific user groups</p>

                                <form method="POST" class="alerts-compose-form w-full" onsubmit="return handleFormSubmit(event)">
                                    <input type="hidden" name="action" value="send_alert">
                                    <input type="hidden" id="editingAlertId" name="editing_alert_id" value="">

                                    <div class="alerts-form-group">
                                        <label class="alerts-form-label">Recipients</label>
                                        <select name="recipients" id="recipientsSelect">
                                            <option value="all">All Users (150)</option>
                                            <option value="providers">Service Providers (45)</option>
                                            <option value="companies">Companies (25)</option>
                                            <option value="customers">Customers (80)</option>
                                        </select>
                                    </div>

                                    <div class="alerts-form-group">
                                        <label class="alerts-form-label">Priority</label>
                                        <select name="priority" id="prioritySelect">
                                            <option value="low">Low Priority</option>
                                            <option value="medium" selected>Medium Priority</option>
                                            <option value="high">High Priority</option>
                                        </select>
                                    </div>

                                    <div class="alerts-form-group">
                                        <label class="alerts-form-label">Message</label>
                                        <textarea
                                            name="message"
                                            id="messageTextarea"
                                            rows="5"
                                            placeholder="Enter your alert message here..."
                                            required></textarea>
                                    </div>

                                    <div style="display: flex; gap: 0.75rem;">
                                        <button type="submit" class="alerts-send-btn" id="submitButton">
                                            <i data-lucide="send" class="mr-2 h-4 w-4"></i>
                                            <span id="submitButtonText">Send Alert</span>
                                        </button>
                                        <button type="button" onclick="cancelEdit()" class="alerts-cancel-btn" id="cancelButton" style="display: none;">
                                            <i data-lucide="x" class="mr-2 h-4 w-4"></i>
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="alerts-compose-card">
                            <div class="alerts-compose-header">
                                <h3 class="text-lg font-medium text-card-foreground flex items-center gap-2">
                                    <i data-lucide="alert-triangle" class="h-5 w-5"></i>
                                    Recent Alerts
                                </h3>
                                <p class="text-sm text-muted-foreground">Previously sent notifications and their status</p>

                                <div class="alerts-recent-list" id="recentAlertsList">
                                    <?php
                                    $priorityVariants = [
                                        'low' => 'secondary',
                                        'medium' => 'outline',
                                        'high' => 'destructive'
                                    ];

                                    foreach ($recentAlerts as $alert): ?>
                                        <div class="alerts-recent-item" id="alert-<?php echo $alert['id']; ?>" data-alert-id="<?php echo $alert['id']; ?>">
                                            <div class="alerts-recent-header">
                                                <p class="alerts-recent-message"><?php echo htmlspecialchars($alert['message']); ?></p>
                                                <?php renderBadge(ucfirst($alert['priority']), $priorityVariants[$alert['priority']]); ?>
                                            </div>
                                            <div class="alerts-recent-meta">
                                                <span>To: <?php echo $alert['recipients']; ?></span>
                                                <span><?php echo $alert['sentAt']; ?></span>
                                            </div>
                                            <div class="alerts-recent-actions">
                                                <div class="alerts-recent-footer">
                                                    <?php renderBadge($alert['status'], 'outline'); ?>
                                                </div>
                                                <div class="alerts-action-buttons">
                                                    <button onclick="editAlert(<?php echo $alert['id']; ?>)" class="alerts-edit-btn">
                                                        <i data-lucide="edit-2"></i>
                                                        Edit
                                                    </button>
                                                    <button onclick="deleteAlert(<?php echo $alert['id']; ?>)" class="alerts-delete-btn">
                                                        <i data-lucide="trash-2"></i>
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-card-foreground">Alert Templates</h3>
                            <p class="text-sm text-muted-foreground">Quick templates for common alert messages</p>

                            <div class="alerts-templates-grid">
                                <?php
                                $templates = [
                                    [
                                        'title' => 'Maintenance Notice',
                                        'message' => 'System maintenance scheduled for [DATE] from [TIME] to [TIME]. Services may be temporarily unavailable.',
                                        'priority' => 'high'
                                    ],
                                    [
                                        'title' => 'New Feature Announcement',
                                        'message' => 'We\'re excited to announce a new feature: [FEATURE_NAME]. Check it out in your dashboard!',
                                        'priority' => 'medium'
                                    ],
                                    [
                                        'title' => 'Policy Update',
                                        'message' => 'Important updates to our terms of service. Please review the changes in your account settings.',
                                        'priority' => 'medium'
                                    ],
                                    [
                                        'title' => 'Security Alert',
                                        'message' => 'We\'ve detected unusual activity. Please verify your account security settings immediately.',
                                        'priority' => 'high'
                                    ],
                                    [
                                        'title' => 'Payment Reminder',
                                        'message' => 'Your subscription payment is due soon. Update your payment method to avoid service interruption.',
                                        'priority' => 'medium'
                                    ],
                                    [
                                        'title' => 'Welcome Message',
                                        'message' => 'Welcome to FixLanka! Get started by completing your profile and exploring our services.',
                                        'priority' => 'low'
                                    ]
                                ];

                                foreach ($templates as $template):
                                ?>
                                    <div class="alerts-template-card">
                                        <div class="alerts-template-header">
                                            <h4 class="alerts-template-title"><?php echo $template['title']; ?></h4>
                                            <?php renderBadge(ucfirst($template['priority']), $priorityVariants[$template['priority']]); ?>
                                        </div>
                                        <p class="alerts-template-message"><?php echo $template['message']; ?></p>
                                        <button onclick="useTemplate('<?php echo addslashes($template['message']); ?>', '<?php echo $template['priority']; ?>')" class="alerts-template-btn">
                                            Use Template
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <script>
                lucide.createIcons();

                // Alert data for JavaScript
                let alertsData = <?php echo json_encode($recentAlerts); ?>;

                // Use Template Function - Enhanced
                function useTemplate(message, priority) {
                    document.getElementById('messageTextarea').value = message;
                    document.getElementById('prioritySelect').value = priority;
                    
                    // Scroll to form
                    document.querySelector('.alerts-compose-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
                    
                    // Focus on message field
                    setTimeout(() => {
                        document.getElementById('messageTextarea').focus();
                    }, 500);
                }

                // Edit Alert Function - FIXED
                function editAlert(alertId) {
                    // Find the alert data
                    const alert = alertsData.find(a => a.id === alertId);
                    
                    if (alert) {
                        // Map recipients to select value
                        let recipientsValue = 'all';
                        if (alert.recipients === 'Service Providers') {
                            recipientsValue = 'providers';
                        } else if (alert.recipients === 'Companies') {
                            recipientsValue = 'companies';
                        } else if (alert.recipients === 'Customers') {
                            recipientsValue = 'customers';
                        }
                        
                        // Populate the form with alert data
                        document.getElementById('recipientsSelect').value = recipientsValue;
                        document.getElementById('prioritySelect').value = alert.priority;
                        document.getElementById('messageTextarea').value = alert.message;
                        document.getElementById('editingAlertId').value = alertId;
                        
                        // Change button text to "Update Alert"
                        document.getElementById('submitButtonText').textContent = 'Update Alert';
                        document.getElementById('submitButton').innerHTML = '<i data-lucide="save" class="mr-2 h-4 w-4"></i><span>Update Alert</span>';
                        
                        // Show cancel button
                        document.getElementById('cancelButton').style.display = 'flex';
                        
                        // Reinitialize icons
                        lucide.createIcons();
                        
                        // Scroll to form
                        document.querySelector('.alerts-compose-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
                        
                        // Focus on message field
                        setTimeout(() => {
                            document.getElementById('messageTextarea').focus();
                            document.getElementById('messageTextarea').setSelectionRange(0, 0);
                        }, 500);
                        
                        // Show notification
                        showSuccessMessage('Alert loaded for editing. Make your changes and click "Update Alert".');
                    }
                }

                // Cancel Edit Function
                function cancelEdit() {
                    // Clear form
                    document.getElementById('recipientsSelect').value = 'all';
                    document.getElementById('prioritySelect').value = 'medium';
                    document.getElementById('messageTextarea').value = '';
                    document.getElementById('editingAlertId').value = '';
                    
                    // Reset button text
                    document.getElementById('submitButtonText').textContent = 'Send Alert';
                    document.getElementById('submitButton').innerHTML = '<i data-lucide="send" class="mr-2 h-4 w-4"></i><span>Send Alert</span>';
                    
                    // Hide cancel button
                    document.getElementById('cancelButton').style.display = 'none';
                    
                    // Reinitialize icons
                    lucide.createIcons();
                    
                    // Hide success message
                    document.getElementById('successMessage').style.display = 'none';
                }

                // Delete Alert Function - COMPLETELY FIXED
                function deleteAlert(alertId) {
                    if (confirm('Are you sure you want to delete this alert? This action cannot be undone.')) {
                        // Find the alert element
                        const alertElement = document.getElementById('alert-' + alertId);
                        
                        if (alertElement) {
                            // Add fade out animation
                            alertElement.style.transition = 'all 0.3s ease';
                            alertElement.style.opacity = '0';
                            alertElement.style.transform = 'translateX(-20px)';
                            
                            // Remove from DOM after animation
                            setTimeout(() => {
                                alertElement.remove();
                                
                                // Remove from data array
                                alertsData = alertsData.filter(a => a.id !== alertId);
                                
                                // Show success message
                                showSuccessMessage('Alert deleted successfully!');
                                
                                // Check if no alerts left
                                if (alertsData.length === 0) {
                                    document.getElementById('recentAlertsList').innerHTML = `
                                        <div style="text-align: center; padding: 3rem; color: #64748b;">
                                            <i data-lucide="inbox" style="width: 48px; height: 48px; margin: 0 auto 1rem; opacity: 0.5;"></i>
                                            <p style="font-size: 0.95rem; font-weight: 500;">No recent alerts</p>
                                            <p style="font-size: 0.875rem; margin-top: 0.5rem;">Send your first alert using the form on the left</p>
                                        </div>
                                    `;
                                    lucide.createIcons();
                                }
                            }, 300);
                        }
                    }
                }

                // Handle Form Submit
                function handleFormSubmit(event) {
                    event.preventDefault();
                    
                    const editingId = document.getElementById('editingAlertId').value;
                    const message = document.getElementById('messageTextarea').value;
                    const priority = document.getElementById('prioritySelect').value;
                    const recipientsValue = document.getElementById('recipientsSelect').value;
                    
                    // Map recipients value to display text
                    const recipientsMap = {
                        'all': 'All Users',
                        'providers': 'Service Providers',
                        'companies': 'Companies',
                        'customers': 'Customers'
                    };
                    
                    if (editingId) {
                        // Update existing alert
                        const alert = alertsData.find(a => a.id == editingId);
                        if (alert) {
                            alert.message = message;
                            alert.priority = priority;
                            alert.recipients = recipientsMap[recipientsValue];
                            
                            // Update DOM
                            const alertElement = document.getElementById('alert-' + editingId);
                            if (alertElement) {
                                alertElement.querySelector('.alerts-recent-message').textContent = message;
                                // Update priority badge (you'd need to regenerate the badge HTML)
                            }
                            
                            showSuccessMessage('Alert updated successfully!');
                        }
                    } else {
                        // Send new alert
                        showSuccessMessage('Alert sent successfully!');
                    }
                    
                    // Reset form
                    cancelEdit();
                    
                    return false;
                }

                // Show Success Message
                function showSuccessMessage(message) {
                    const successDiv = document.getElementById('successMessage');
                    const successText = document.getElementById('successMessageText');
                    
                    successText.textContent = message;
                    successDiv.style.display = 'block';
                    
                    // Scroll to top to see message
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    
                    // Hide after 5 seconds
                    setTimeout(() => {
                        successDiv.style.transition = 'opacity 0.3s ease';
                        successDiv.style.opacity = '0';
                        setTimeout(() => {
                            successDiv.style.display = 'none';
                            successDiv.style.opacity = '1';
                        }, 300);
                    }, 5000);
                }

                // Reinitialize Lucide icons
                setTimeout(() => {
                    lucide.createIcons();
                }, 100);
            </script>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>
</body>

</html>