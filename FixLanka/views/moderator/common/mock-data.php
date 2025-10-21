<?php
// Enhanced mock data for notifications

// Enhanced mock data with additional fields
function getEnhancedMockNotifications()
{

    $mockNotifications = [
        [
            'message' => 'System maintenance scheduled for tonight at 11 PM. Services will be temporarily unavailable.',
            'recipients' => 'All Users',
            'priority' => 'high',
            'status' => 'Sent'
        ],
        [
            'message' => 'New feature: Advanced search filters now available in your dashboard.',
            'recipients' => 'Service Providers',
            'priority' => 'medium',
            'status' => 'Sent'
        ],
        [
            'message' => 'Welcome to FixLanka! Complete your profile to get started.',
            'recipients' => 'New Users',
            'priority' => 'low',
            'status' => 'Draft'
        ],
        [
            'message' => 'Payment reminder: Your subscription expires in 3 days.',
            'recipients' => 'Premium Users',
            'priority' => 'high',
            'status' => 'Sent'
        ],
        [
            'message' => 'Weekly digest: 15 new service requests in your area.',
            'recipients' => 'Service Providers',
            'priority' => 'medium',
            'status' => 'Sent'
        ],
        [
            'message' => 'Security alert: New login detected from unknown device.',
            'recipients' => 'All Users',
            'priority' => 'high',
            'status' => 'Draft'
        ]
    ];

    if (!is_array($mockNotifications)) {
        error_log('[DEBUG] $mockNotifications is not an array, returning empty array');
        return []; // safe fallback
    }

    $result = array_map(function ($notification, $index) {
        $enhanced = array_merge($notification, [
            'id' => $index + 1,
            'sentAt' => $notification['status'] === 'Sent' ? date('Y-m-d H:i', strtotime('-' . rand(1, 72) . ' hours')) : null,
            'createdAt' => date('Y-m-d H:i', strtotime('-' . rand(1, 168) . ' hours')),
            'deliveryRate' => $notification['status'] === 'Sent' ? rand(85, 99) . '%' : null,
            'openRate' => $notification['status'] === 'Sent' ? rand(15, 45) . '%' : null,
            'category' => ['System', 'Marketing', 'Alert', 'Update'][rand(0, 3)],
            'readCount' => $notification['status'] === 'Sent' ? rand(50, 500) : 0,
            'totalRecipients' => rand(100, 1000)
        ]);
        return $enhanced;
    }, $mockNotifications, array_keys($mockNotifications));

    return $result;
}



// Mock notification templates

function getNotificationTemplates()
{
    $notificationTemplates = [
        [
            'title' => 'Service Update',
            'message' => 'Important updates to our service offerings. Check out the new features available in your dashboard.',
            'priority' => 'medium',
            'category' => 'Update'
        ],
        [
            'title' => 'Maintenance Notice',
            'message' => 'Scheduled maintenance on [DATE] from [TIME] to [TIME]. Services may be temporarily unavailable.',
            'priority' => 'high',
            'category' => 'System'
        ],
        [
            'title' => 'New Feature',
            'message' => 'We\'ve added a new feature to improve your experience. Learn more about [FEATURE_NAME] in your account.',
            'priority' => 'medium',
            'category' => 'Update'
        ],
        [
            'title' => 'Payment Reminder',
            'message' => 'Your payment is due soon. Please update your payment method to continue using our services.',
            'priority' => 'high',
            'category' => 'Alert'
        ],
        [
            'title' => 'Welcome Message',
            'message' => 'Welcome to FixLanka! Complete your profile to start connecting with service providers.',
            'priority' => 'low',
            'category' => 'Marketing'
        ],
        [
            'title' => 'Security Alert',
            'message' => 'We\'ve detected unusual activity on your account. Please verify your security settings.',
            'priority' => 'high',
            'category' => 'Alert'
        ]
    ];
    return $notificationTemplates;
}
