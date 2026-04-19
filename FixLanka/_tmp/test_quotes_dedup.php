<?php
// Test script to verify quotes deduplication fix
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/UserQuotesModel.php';

// Start session if needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "=== Quotes Deduplication Fix Test ===\n\n";

try {
    $model = new UserQuotesModel($pdo);
    
    // Test with a sample user - get pending quotes
    $testUserId = 1; // Adjust based on your test data
    echo "Fetching pending quotes for user $testUserId...\n\n";
    
    $quotes = $model->getUserQuotes($testUserId, 50, 0, 'pending');
    $pendingCount = $model->getUserPendingCount($testUserId);
    
    echo "Results:\n";
    echo "- Total quotes returned: " . count($quotes) . "\n";
    echo "- Pending count indicator: $pendingCount\n\n";
    
    // Check for duplicates
    $quoteIds = [];
    $duplicates = [];
    foreach ($quotes as $quote) {
        $key = $quote['source'] . '_' . $quote['quote_id'];
        if (isset($quoteIds[$key])) {
            $duplicates[$key] = ($duplicates[$key] ?? 1) + 1;
        } else {
            $quoteIds[$key] = 1;
        }
    }
    
    if (count($duplicates) > 0) {
        echo "FOUND DUPLICATES:\n";
        foreach ($duplicates as $key => $count) {
            echo "  - $key: appears $count times\n";
        }
    } else {
        echo "✓ NO DUPLICATES FOUND - Fix is working!\n";
    }
    
    echo "\nSample quotes (first 5):\n";
    foreach (array_slice($quotes, 0, 5) as $quote) {
        echo "  - [" . $quote['source'] . "] Quote #" . $quote['quote_id'] . 
             " for Request #" . $quote['request_id'] . 
             " - Status: " . $quote['status'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
echo "\n=== Test Complete ===\n";
