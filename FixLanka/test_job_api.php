<?php
/**
 * Test Database Connection and Job Requests API
 */

echo "<h1>Database Connection Test</h1>";

// Test 1: Include database config
echo "<h2>Test 1: Database Configuration</h2>";
try {
    require_once '../config/database.php';
    echo "✅ Database config loaded successfully<br>";
    
    if (isset($pdo)) {
        echo "✅ PDO object exists<br>";
        echo "PDO Connection: " . get_class($pdo) . "<br>";
    } else {
        echo "❌ PDO object not set<br>";
    }
} catch (Exception $e) {
    echo "❌ Error loading database config: " . $e->getMessage() . "<br>";
}

// Test 2: Test database connection
echo "<h2>Test 2: Database Connection</h2>";
try {
    $stmt = $pdo->query("SELECT DATABASE() as dbname");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Connected to database: " . $result['dbname'] . "<br>";
} catch (Exception $e) {
    echo "❌ Database connection error: " . $e->getMessage() . "<br>";
}

// Test 3: Check tables exist
echo "<h2>Test 3: Check Tables</h2>";
try {
    $tables = ['User', 'Category', 'JobRequest', 'Repairer', 'RepairerQuote'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Table '$table' exists<br>";
        } else {
            echo "❌ Table '$table' does NOT exist<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error checking tables: " . $e->getMessage() . "<br>";
}

// Test 4: Check JobRequest data
echo "<h2>Test 4: JobRequest Data</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM JobRequest");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total JobRequest records: " . $result['count'] . "<br>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM JobRequest WHERE status = 'pending'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Pending JobRequest records: " . $result['count'] . "<br>";
    
    if ($result['count'] > 0) {
        echo "<h3>Sample Pending Job:</h3>";
        $stmt = $pdo->query("SELECT * FROM JobRequest WHERE status = 'pending' LIMIT 1");
        $job = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<pre>" . print_r($job, true) . "</pre>";
    }
} catch (Exception $e) {
    echo "❌ Error checking JobRequest data: " . $e->getMessage() . "<br>";
}

// Test 5: Check User data
echo "<h2>Test 5: User Data</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM User");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total User records: " . $result['count'] . "<br>";
} catch (Exception $e) {
    echo "❌ Error checking User data: " . $e->getMessage() . "<br>";
}

// Test 6: Check Category data
echo "<h2>Test 6: Category Data</h2>";
try {
    $stmt = $pdo->query("SELECT * FROM Category");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Total Category records: " . count($categories) . "<br>";
    if (count($categories) > 0) {
        echo "Categories: ";
        foreach ($categories as $cat) {
            echo $cat['name'] . ", ";
        }
        echo "<br>";
    }
} catch (Exception $e) {
    echo "❌ Error checking Category data: " . $e->getMessage() . "<br>";
}

// Test 7: Test the API query
echo "<h2>Test 7: API Query Test</h2>";
try {
    $sql = "SELECT 
                jr.request_id,
                jr.user_id,
                jr.category_id,
                jr.title,
                jr.description,
                jr.status,
                jr.district,
                jr.address,
                jr.service_provider_type,
                jr.urgency,
                jr.finish_date,
                jr.dateCreated,
                jr.photos,
                c.name as category_name,
                u.f_name as customer_first_name,
                u.l_name as customer_last_name,
                TIMESTAMPDIFF(HOUR, jr.dateCreated, NOW()) as hours_ago
            FROM JobRequest jr
            LEFT JOIN Category c ON jr.category_id = c.category_id
            LEFT JOIN User u ON jr.user_id = u.user_id
            WHERE jr.status = 'pending'
            AND (jr.service_provider_type = 'individual' OR jr.service_provider_type = 'both')
            ORDER BY jr.dateCreated DESC";
    
    $stmt = $pdo->query($sql);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "✅ API query executed successfully<br>";
    echo "Jobs found: " . count($jobs) . "<br>";
    
    if (count($jobs) > 0) {
        echo "<h3>Sample Job Result:</h3>";
        echo "<pre>" . print_r($jobs[0], true) . "</pre>";
    }
} catch (Exception $e) {
    echo "❌ API query error: " . $e->getMessage() . "<br>";
    echo "Query: <pre>$sql</pre>";
}

// Test 8: Test API endpoint
echo "<h2>Test 8: API Endpoint Test</h2>";
echo "<a href='../api/job-requests.php' target='_blank'>Open API in new tab</a><br>";
echo "<a href='../api/job-requests.php?service_provider_type=individual' target='_blank'>Open API with filter in new tab</a><br>";

?>
