<?php
// Mock Data for Admin and Moderator Dashboards

// Analytics Data
$mockAnalytics = [
    'totalUsers' => 15234,
    'serviceProviders' => 1247,
    'activeAds' => 89,
    'userEngagement' => 78,
    'revenueGrowth' => 15.2,
    'newUsersThisMonth' => 1842,
    'activeProvidersThisMonth' => 156
];

// Financial Data
$mockFinancials = [
    'monthlyRevenue' => 4567000, // LKR 4.5M
    'pendingWithdrawals' => 234500, // LKR 234.5K
    'completedTransactions' => 8945,
    'averageTransactionValue' => 5200,
    'totalCommission' => 456700,
    'totalCommissions' => 456700, // Alias for compatibility
    'activeSubscriptions' => 142,
    'recentTransactions' => [
        [
            'id' => 'TXN-2024-10-001',
            'type' => 'Commission',
            'amount' => 15000,
            'date' => '2024-10-22',
            'status' => 'Completed'
        ],
        [
            'id' => 'TXN-2024-10-002',
            'type' => 'Ad Payment',
            'amount' => 50000,
            'date' => '2024-10-21',
            'status' => 'Completed'
        ],
        [
            'id' => 'TXN-2024-10-003',
            'type' => 'Withdrawal',
            'amount' => 25000,
            'date' => '2024-10-21',
            'status' => 'Pending'
        ],
        [
            'id' => 'TXN-2024-10-004',
            'type' => 'Subscription',
            'amount' => 5000,
            'date' => '2024-10-20',
            'status' => 'Completed'
        ],
        [
            'id' => 'TXN-2024-10-005',
            'type' => 'Commission',
            'amount' => 18500,
            'date' => '2024-10-20',
            'status' => 'Completed'
        ],
        [
            'id' => 'TXN-2024-10-006',
            'type' => 'Withdrawal',
            'amount' => 30000,
            'date' => '2024-10-19',
            'status' => 'Failed'
        ],
        [
            'id' => 'TXN-2024-10-007',
            'type' => 'Ad Payment',
            'amount' => 75000,
            'date' => '2024-10-19',
            'status' => 'Completed'
        ],
        [
            'id' => 'TXN-2024-10-008',
            'type' => 'Commission',
            'amount' => 12000,
            'date' => '2024-10-18',
            'status' => 'Completed'
        ]
    ]
];

// Users Data
$mockUsers = [
    [
        'id' => 1,
        'name' => 'John Silva',
        'email' => 'john.silva@email.com',
        'type' => 'Service Provider',
        'role' => 'repairer',
        'status' => 'active',
        'joined' => '2024-01-15',
        'phone' => '+94 77 123 4567',
        'jobs' => 45,
        'rating' => 4.8
    ],
    [
        'id' => 2,
        'name' => 'Sarah Fernando',
        'email' => 'sarah.fernando@email.com',
        'type' => 'Company',
        'role' => 'company',
        'status' => 'active',
        'joined' => '2024-02-20',
        'phone' => '+94 11 234 5678',
        'jobs' => 23,
        'rating' => 4.6
    ],
    [
        'id' => 3,
        'name' => 'Michael Perera',
        'email' => 'michael.perera@email.com',
        'type' => 'Service Provider',
        'role' => 'repairer',
        'status' => 'suspended',
        'joined' => '2024-03-10',
        'phone' => '+94 71 345 6789',
        'jobs' => 12,
        'rating' => 3.2
    ],
    [
        'id' => 4,
        'name' => 'Emily Rodrigo',
        'email' => 'emily.rodrigo@email.com',
        'type' => 'Moderator',
        'role' => 'moderator',
        'status' => 'active',
        'joined' => '2024-01-05',
        'phone' => '+94 77 456 7890',
        'jobs' => 0,
        'rating' => null
    ],
    [
        'id' => 5,
        'name' => 'David Jayawardena',
        'email' => 'david.j@email.com',
        'type' => 'Service Provider',
        'role' => 'repairer',
        'status' => 'active',
        'joined' => '2024-04-12',
        'phone' => '+94 76 567 8901',
        'jobs' => 67,
        'rating' => 4.9
    ],
    [
        'id' => 6,
        'name' => 'Kasun Perera',
        'email' => 'kasun.perera@email.com',
        'type' => 'User',
        'role' => 'user',
        'status' => 'banned',
        'joined' => '2024-05-20',
        'phone' => '+94 77 678 9012',
        'jobs' => 5,
        'rating' => 2.1
    ],
    [
        'id' => 7,
        'name' => 'Nimal Silva',
        'email' => 'nimal.silva@email.com',
        'type' => 'Service Provider',
        'role' => 'repairer',
        'status' => 'banned',
        'joined' => '2024-06-15',
        'phone' => '+94 71 789 0123',
        'jobs' => 8,
        'rating' => 1.8
    ],
    [
        'id' => 8,
        'name' => 'Chamari Perera',
        'email' => 'chamari.perera@email.com',
        'type' => 'Company',
        'role' => 'company',
        'status' => 'suspended',
        'joined' => '2024-07-10',
        'phone' => '+94 11 890 1234',
        'jobs' => 15,
        'rating' => 3.5
    ],
    [
        'id' => 9,
        'name' => 'Ruwan Fernando',
        'email' => 'ruwan.fernando@email.com',
        'type' => 'User',
        'role' => 'user',
        'status' => 'active',
        'joined' => '2024-08-05',
        'phone' => '+94 77 901 2345',
        'jobs' => 12,
        'rating' => 4.2
    ],
    [
        'id' => 10,
        'name' => 'Dilani Jayawardena',
        'email' => 'dilani.j@email.com',
        'type' => 'Service Provider',
        'role' => 'repairer',
        'status' => 'suspended',
        'joined' => '2024-09-01',
        'phone' => '+94 76 012 3456',
        'jobs' => 3,
        'rating' => 2.8
    ],
    [
        'id' => 11,
        'name' => 'Saman Kumara',
        'email' => 'saman.kumara@email.com',
        'type' => 'User',
        'role' => 'user',
        'status' => 'banned',
        'joined' => '2024-09-15',
        'phone' => '+94 71 123 4567',
        'jobs' => 2,
        'rating' => 1.5
    ],
    [
        'id' => 12,
        'name' => 'Anura Dissanayake',
        'email' => 'anura.d@email.com',
        'type' => 'Service Provider',
        'role' => 'repairer',
        'status' => 'active',
        'joined' => '2024-10-01',
        'phone' => '+94 77 234 5678',
        'jobs' => 25,
        'rating' => 4.7
    ],
    [
        'id' => 13,
        'name' => 'Malini Wijesinghe',
        'email' => 'malini.w@email.com',
        'type' => 'Company',
        'role' => 'company',
        'status' => 'active',
        'joined' => '2024-10-10',
        'phone' => '+94 11 345 6789',
        'jobs' => 18,
        'rating' => 4.4
    ],
    [
        'id' => 14,
        'name' => 'Pradeep Ranasinghe',
        'email' => 'pradeep.r@email.com',
        'type' => 'User',
        'role' => 'user',
        'status' => 'suspended',
        'joined' => '2024-10-15',
        'phone' => '+94 76 456 7890',
        'jobs' => 4,
        'rating' => 2.3
    ],
    [
        'id' => 15,
        'name' => 'Gayan Amarasinghe',
        'email' => 'gayan.a@email.com',
        'type' => 'Service Provider',
        'role' => 'repairer',
        'status' => 'banned',
        'joined' => '2024-10-18',
        'phone' => '+94 71 567 8901',
        'jobs' => 1,
        'rating' => 1.2
    ]
];

// Ads Data
$mockAds = [
    [
        'id' => 1,
        'title' => 'Professional Plumbing Services',
        'company' => 'Silva Plumbing Co.',
        'type' => 'Banner',
        'status' => 'Pending',
        'submitted' => '2024-10-15',
        'submittedDate' => '2024-10-15',
        'created_at' => '2024-10-15',
        'budget' => 50000,
        'duration' => '30 days',
        'impressions' => 0,
        'description' => 'Professional plumbing services for residential and commercial properties. Available 24/7 for emergencies.',
        'rejection_reason' => null
    ],
    [
        'id' => 2,
        'title' => 'Expert Electrical Work',
        'company' => 'Fernando Electrics',
        'type' => 'Sponsored',
        'status' => 'Approved',
        'submitted' => '2024-10-10',
        'submittedDate' => '2024-10-10',
        'created_at' => '2024-10-10',
        'budget' => 75000,
        'duration' => '60 days',
        'impressions' => 12453,
        'description' => 'Licensed electricians providing installation, repair, and maintenance services for homes and businesses.',
        'rejection_reason' => null
    ],
    [
        'id' => 3,
        'title' => 'Home Renovation Specialists',
        'company' => 'BuildRight Construction',
        'type' => 'Featured',
        'status' => 'Rejected',
        'submitted' => '2024-10-08',
        'submittedDate' => '2024-10-08',
        'created_at' => '2024-10-08',
        'budget' => 100000,
        'duration' => '90 days',
        'impressions' => 0,
        'description' => 'Complete home renovation services including kitchen, bathroom, and full house remodeling.',
        'rejection_reason' => 'Submitted images do not meet quality standards. Please provide high-resolution images and proper licensing documentation.'
    ],
    [
        'id' => 4,
        'title' => 'Carpentry & Woodwork',
        'company' => 'Woodcraft Masters',
        'type' => 'Banner',
        'status' => 'Active',
        'submitted' => '2024-09-20',
        'submittedDate' => '2024-09-20',
        'created_at' => '2024-09-20',
        'budget' => 60000,
        'duration' => '45 days',
        'impressions' => 23567,
        'description' => 'Custom carpentry and woodwork services. Specializing in furniture, cabinets, and decorative pieces.',
        'rejection_reason' => null
    ],
    [
        'id' => 5,
        'title' => 'AC Repair & Maintenance',
        'company' => 'CoolBreeze Services',
        'type' => 'Sponsored',
        'status' => 'Pending',
        'submitted' => '2024-10-18',
        'submittedDate' => '2024-10-18',
        'created_at' => '2024-10-18',
        'budget' => 45000,
        'duration' => '30 days',
        'impressions' => 0,
        'description' => 'Air conditioning repair, installation, and maintenance services for all brands.',
        'rejection_reason' => null
    ],
    [
        'id' => 6,
        'title' => 'Painting & Decorating',
        'company' => 'Color Experts Ltd',
        'type' => 'Featured',
        'status' => 'Pending',
        'submitted' => '2024-10-20',
        'submittedDate' => '2024-10-20',
        'created_at' => '2024-10-20',
        'budget' => 35000,
        'duration' => '30 days',
        'impressions' => 0,
        'description' => 'Interior and exterior painting services with premium quality paints and professional finishes.',
        'rejection_reason' => null
    ],
    [
        'id' => 7,
        'title' => 'Landscaping Services',
        'company' => 'Green Gardens',
        'type' => 'Banner',
        'status' => 'Approved',
        'submitted' => '2024-10-12',
        'submittedDate' => '2024-10-12',
        'created_at' => '2024-10-12',
        'budget' => 55000,
        'duration' => '60 days',
        'impressions' => 8932,
        'description' => 'Professional landscaping and garden maintenance services for residential and commercial properties.',
        'rejection_reason' => null
    ],
    [
        'id' => 8,
        'title' => 'Roofing Solutions',
        'company' => 'RoofTech Services',
        'type' => 'Sponsored',
        'status' => 'Active',
        'submitted' => '2024-10-05',
        'submittedDate' => '2024-10-05',
        'created_at' => '2024-10-05',
        'budget' => 80000,
        'duration' => '90 days',
        'impressions' => 15678,
        'description' => 'Complete roofing solutions including repairs, replacements, and waterproofing services.',
        'rejection_reason' => null
    ],
    [
        'id' => 9,
        'title' => 'Pest Control Services',
        'company' => 'BugBusters',
        'type' => 'Featured',
        'status' => 'Rejected',
        'submitted' => '2024-10-14',
        'submittedDate' => '2024-10-14',
        'created_at' => '2024-10-14',
        'budget' => 28000,
        'duration' => '30 days',
        'impressions' => 0,
        'description' => 'Eco-friendly pest control and termite treatment services for homes and offices.',
        'rejection_reason' => 'Advertisement content contains unverified claims about product effectiveness. Please provide supporting documentation.'
    ],
    [
        'id' => 10,
        'title' => 'Security Systems Installation',
        'company' => 'SecureHome Technologies',
        'type' => 'Banner',
        'status' => 'Pending',
        'submitted' => '2024-10-21',
        'submittedDate' => '2024-10-21',
        'created_at' => '2024-10-21',
        'budget' => 95000,
        'duration' => '60 days',
        'impressions' => 0,
        'description' => 'Smart home security systems with CCTV cameras, alarms, and remote monitoring capabilities.',
        'rejection_reason' => null
    ],
    [
        'id' => 11,
        'title' => 'Pool Maintenance',
        'company' => 'Crystal Pool Care',
        'type' => 'Sponsored',
        'status' => 'Expired',
        'submitted' => '2024-08-15',
        'submittedDate' => '2024-08-15',
        'created_at' => '2024-08-15',
        'budget' => 42000,
        'duration' => '60 days',
        'impressions' => 11234,
        'description' => 'Swimming pool cleaning, maintenance, and chemical treatment services.',
        'rejection_reason' => null
    ],
    [
        'id' => 12,
        'title' => 'Solar Panel Installation',
        'company' => 'GreenEnergy Solutions',
        'type' => 'Featured',
        'status' => 'Active',
        'submitted' => '2024-09-28',
        'submittedDate' => '2024-09-28',
        'created_at' => '2024-09-28',
        'budget' => 120000,
        'duration' => '90 days',
        'impressions' => 19845,
        'description' => 'Professional solar panel installation with government subsidies and financing options available.',
        'rejection_reason' => null
    ]
];

// Issues/Reports Data
$mockIssues = [
    [
        'id' => 1,
        'title' => 'Payment not received',
        'reporter' => 'John Silva',
        'category' => 'Payment',
        'status' => 'Open',
        'priority' => 'High',
        'created' => '2024-10-20',
        'description' => 'Completed job but payment is still pending after 7 days.'
    ],
    [
        'id' => 2,
        'title' => 'Inappropriate ad content',
        'reporter' => 'Sarah Fernando',
        'category' => 'Content',
        'status' => 'In Progress',
        'priority' => 'Medium',
        'created' => '2024-10-19',
        'description' => 'Advertisement contains misleading information.'
    ],
    [
        'id' => 3,
        'title' => 'User harassment',
        'reporter' => 'Emily Rodrigo',
        'category' => 'Behavior',
        'status' => 'Resolved',
        'priority' => 'High',
        'created' => '2024-10-15',
        'description' => 'User received threatening messages from another user.'
    ],
    [
        'id' => 4,
        'title' => 'App crashing on mobile',
        'reporter' => 'David Jayawardena',
        'category' => 'Technical',
        'status' => 'Open',
        'priority' => 'Low',
        'created' => '2024-10-21',
        'description' => 'Mobile app crashes when trying to upload images.'
    ]
];

// Notifications Data
$mockNotifications = [
    [
        'id' => 1,
        'type' => 'new_user',
        'title' => 'New User Registration',
        'message' => 'Kasun Perera registered as a service provider',
        'time' => '2 minutes ago',
        'read' => false
    ],
    [
        'id' => 2,
        'type' => 'ad_pending',
        'title' => 'Ad Awaiting Review',
        'message' => 'New advertisement submission from ABC Company',
        'time' => '15 minutes ago',
        'read' => false
    ],
    [
        'id' => 3,
        'type' => 'issue_reported',
        'title' => 'Issue Reported',
        'message' => 'Payment dispute reported by John Silva',
        'time' => '1 hour ago',
        'read' => true
    ],
    [
        'id' => 4,
        'type' => 'payment_completed',
        'title' => 'Payment Processed',
        'message' => 'LKR 15,000 payment successfully processed',
        'time' => '2 hours ago',
        'read' => true
    ]
];

// System Alerts
$mockAlerts = [
    [
        'id' => 1,
        'type' => 'system',
        'title' => 'System Maintenance Scheduled',
        'message' => 'Scheduled maintenance on Oct 25, 2024 from 2:00 AM to 4:00 AM',
        'severity' => 'info',
        'created' => '2024-10-22'
    ],
    [
        'id' => 2,
        'type' => 'security',
        'title' => 'Suspicious Login Attempt',
        'message' => 'Multiple failed login attempts detected from IP 192.168.1.100',
        'severity' => 'warning',
        'created' => '2024-10-21'
    ],
    [
        'id' => 3,
        'type' => 'payment',
        'title' => 'Payment Gateway Issue',
        'message' => 'Payment gateway experiencing intermittent connectivity issues',
        'severity' => 'critical',
        'created' => '2024-10-20'
    ]
];

// Recent Activity
$mockActivity = [
    ['action' => 'New service provider registered', 'time' => '2 minutes ago', 'type' => 'user'],
    ['action' => 'Advertisement approved', 'time' => '15 minutes ago', 'type' => 'ad'],
    ['action' => 'Issue reported and resolved', 'time' => '1 hour ago', 'type' => 'issue'],
    ['action' => 'Financial report generated', 'time' => '2 hours ago', 'type' => 'finance'],
    ['action' => 'User account suspended', 'time' => '3 hours ago', 'type' => 'moderation']
];

// Ad Reports Data - ADVERTISEMENT REPORTS ONLY
$mockAdReports = [
    [
        'id' => 1,
        'user_name' => 'John Silva',
        'user_email' => 'john.silva@email.com',
        'ad_id' => 2,
        'ad_title' => 'Expert Electrical Work',
        'company_name' => 'Fernando Electrics',
        'issue_type' => 'Misleading Information',
        'category' => 'advertisement',
        'description' => 'This advertisement contains misleading information about certifications and qualifications.',
        'priority' => 'High',
        'status' => 'Pending',
        'created_date' => '2024-10-22',
        'evidence' => 'Screenshots attached showing false claims',
        'reported_entity_type' => 'advertisement',
        'reported_entity_id' => 2,
        'moderator_notes' => ''
    ],
    [
        'id' => 2,
        'user_name' => 'Sarah Fernando',
        'user_email' => 'sarah.fernando@email.com',
        'ad_id' => 5,
        'ad_title' => 'AC Repair & Maintenance',
        'company_name' => 'CoolBreeze Services',
        'issue_type' => 'False Pricing',
        'category' => 'advertisement',
        'description' => 'Advertisement displays different prices than what is actually charged to customers.',
        'priority' => 'High',
        'status' => 'Investigating',
        'created_date' => '2024-10-21',
        'moderator_notes' => 'Contacted company for pricing clarification',
        'reported_entity_type' => 'advertisement',
        'reported_entity_id' => 5
    ],
    [
        'id' => 3,
        'user_name' => 'Michael Perera',
        'user_email' => 'michael.perera@email.com',
        'ad_id' => 1,
        'ad_title' => 'Professional Plumbing Services',
        'company_name' => 'Silva Plumbing Co.',
        'issue_type' => 'Inappropriate Content',
        'category' => 'advertisement',
        'description' => 'Advertisement contains inappropriate images and unprofessional language.',
        'priority' => 'High',
        'status' => 'Escalated',
        'created_date' => '2024-10-20',
        'moderator_notes' => 'Escalated to admin - requires immediate action',
        'reported_entity_type' => 'advertisement',
        'reported_entity_id' => 1
    ],
    [
        'id' => 4,
        'user_name' => 'Emily Rodrigo',
        'user_email' => 'emily.rodrigo@email.com',
        'ad_id' => 3,
        'ad_title' => 'Home Renovation Specialists',
        'company_name' => 'BuildRight Construction',
        'issue_type' => 'Duplicate Listing',
        'category' => 'advertisement',
        'description' => 'Same advertisement posted multiple times with different prices and details.',
        'priority' => 'Medium',
        'status' => 'Resolved',
        'created_date' => '2024-10-18',
        'moderator_notes' => 'Removed duplicate advertisements. Warning issued to company.',
        'resolved_at' => '2024-10-19',
        'resolver_name' => 'Moderator Team'
    ],
    [
        'id' => 5,
        'user_name' => 'David Jayawardena',
        'user_email' => 'david.j@email.com',
        'ad_id' => 4,
        'ad_title' => 'Carpentry & Woodwork',
        'company_name' => 'Woodcraft Masters',
        'issue_type' => 'Spam Content',
        'category' => 'advertisement',
        'description' => 'Advertisement contains spam links and irrelevant promotional content not related to services.',
        'priority' => 'Medium',
        'status' => 'Pending',
        'created_date' => '2024-10-22',
        'reported_entity_type' => 'advertisement',
        'reported_entity_id' => 4,
        'moderator_notes' => ''
    ],
    [
        'id' => 6,
        'user_name' => 'Kasun Perera',
        'user_email' => 'kasun.perera@email.com',
        'ad_id' => 6,
        'ad_title' => 'Painting & Decorating',
        'company_name' => 'Color Experts Ltd',
        'issue_type' => 'Expired Advertisement',
        'category' => 'advertisement',
        'description' => 'Advertisement has expired but still appears as active in search results.',
        'priority' => 'Low',
        'status' => 'Investigating',
        'created_date' => '2024-10-21',
        'reported_entity_type' => 'advertisement',
        'reported_entity_id' => 6,
        'moderator_notes' => 'Checking system records'
    ],
    [
        'id' => 7,
        'user_name' => 'Nimal Silva',
        'user_email' => 'nimal.silva@email.com',
        'ad_id' => 7,
        'ad_title' => 'Landscaping Services',
        'company_name' => 'Green Gardens',
        'issue_type' => 'Policy Violation',
        'category' => 'advertisement',
        'description' => 'Advertisement violates platform policies by requesting payment outside the system.',
        'priority' => 'High',
        'status' => 'Escalated',
        'created_date' => '2024-10-22',
        'reported_entity_type' => 'advertisement',
        'reported_entity_id' => 7,
        'moderator_notes' => 'Company contacted - awaiting response'
    ],
    [
        'id' => 8,
        'user_name' => 'Chamari Perera',
        'user_email' => 'chamari.perera@email.com',
        'ad_id' => 8,
        'ad_title' => 'Roofing Solutions',
        'company_name' => 'RoofTech Services',
        'issue_type' => 'Unverified Claims',
        'category' => 'advertisement',
        'description' => 'Advertisement makes unverified claims about warranty and certification.',
        'priority' => 'Medium',
        'status' => 'Investigating',
        'created_date' => '2024-10-19',
        'moderator_notes' => 'Requested documentation from company',
        'reported_entity_type' => 'advertisement',
        'reported_entity_id' => 8
    ],
    [
        'id' => 9,
        'user_name' => 'Ruwan Fernando',
        'user_email' => 'ruwan.fernando@email.com',
        'ad_id' => 9,
        'ad_title' => 'Pest Control Services',
        'company_name' => 'BugBusters',
        'issue_type' => 'Misleading Information',
        'category' => 'advertisement',
        'description' => 'Advertisement claims 100% effectiveness with no scientific backing or proof.',
        'priority' => 'Medium',
        'status' => 'Pending',
        'created_date' => '2024-10-21',
        'reported_entity_type' => 'advertisement',
        'reported_entity_id' => 9,
        'moderator_notes' => ''
    ],
    [
        'id' => 10,
        'user_name' => 'Dilani Jayawardena',
        'user_email' => 'dilani.j@email.com',
        'ad_id' => 10,
        'ad_title' => 'Security Systems Installation',
        'company_name' => 'SecureHome Technologies',
        'issue_type' => 'Inappropriate Images',
        'category' => 'advertisement',
        'description' => 'Advertisement contains low-quality and unprofessional images.',
        'priority' => 'Low',
        'status' => 'Resolved',
        'created_date' => '2024-10-18',
        'moderator_notes' => 'Company updated images. Issue resolved.',
        'resolved_at' => '2024-10-20',
        'resolver_name' => 'Moderator Team'
    ]
];

/**
 * Get advertisement reports ONLY
 * Filters out all non-advertisement related reports
 */
function getAdReports() {
    global $mockAdReports;

    if (!is_array($mockAdReports)) {
        $mockAdReports = [];
    }

    // Filter to ensure ONLY advertisement category reports.
    // Some mock rows may not include a `category`; default those to `advertisement`.
    return array_values(array_filter($mockAdReports, function ($report) {
        if (!is_array($report)) {
            return false;
        }

        $category = $report['category'] ?? 'advertisement';
        return $category === 'advertisement';
    }));
}
        