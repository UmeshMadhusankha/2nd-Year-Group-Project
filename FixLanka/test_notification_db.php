<?php
// Test script to verify Notification table exists and is accessible

require_once __DIR__ . '/config/databse.php';

echo "=== Notification Database Test ===\n\n";

try {
    // Test 1: Check if table exists
    echo "1. Checking if Notification table exists...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'Notification'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "   ✓ Notification table EXISTS\n\n";
        
        // Test 2: Check table structure
        echo "2. Table structure:\n";
        $stmt = $pdo->query("DESCRIBE Notification");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            echo "   - {$col['Field']} ({$col['Type']}) {$col['Null']} {$col['Key']}\n";
        }
        echo "\n";
        
        // Test 3: Count existing notifications
        echo "3. Checking existing data...\n";
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Notification");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "   ✓ Found $count notification(s) in database\n\n";
        
        // Test 4: Fetch recent notifications
        echo "4. Fetching recent notifications:\n";
        $stmt = $pdo->query("SELECT * FROM Notification ORDER BY send_date DESC LIMIT 5");
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($notifications)) {
            echo "   - No notifications found (table is empty)\n\n";
        } else {
            foreach ($notifications as $notif) {
                echo "   - ID: {$notif['notification_id']}, Title: {$notif['title']}, Status: {$notif['status']}\n";
            }
            echo "\n";
        }
        
        // Test 5: Insert test notification
        echo "5. Testing INSERT operation...\n";
        $stmt = $pdo->prepare("INSERT INTO Notification (title, message, recipient_type, status) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute(['Test Title', 'Test Message', 'all', 'sent']);
        
        if ($result) {
            $lastId = $pdo->lastInsertId();
            echo "   ✓ Test notification inserted successfully! ID: $lastId\n";
            
            // Clean up test data
            $stmt = $pdo->prepare("DELETE FROM Notification WHERE notification_id = ?");
            $stmt->execute([$lastId]);
            echo "   ✓ Test notification cleaned up\n\n";
        } else {
            echo "   ✗ Failed to insert test notification\n\n";
        }
        
        echo "=== All Tests Passed! ===\n";
        echo "Your database is ready for the CRUD system.\n";
        
    } else {
        echo "   ✗ Notification table DOES NOT EXIST!\n\n";
        echo "Please run the create_database.sql script to create the table.\n";
        echo "You can find it at: create_database.sql\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Database Error: " . $e->getMessage() . "\n";
    echo "\nPlease check:\n";
    echo "1. XAMPP MySQL is running\n";
    echo "2. Database 'fix_lanka' exists\n";
    echo "3. Database credentials in config/databse.php are correct\n";
}
