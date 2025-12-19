<?php
/**
 * Test script to check provider data and API functionality
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing Provider System</h2>";
echo "<hr>";

// Test 1: Database Connection
echo "<h3>1. Testing Database Connection</h3>";
try {
    require_once __DIR__ . '/config/database.php';
    echo "✅ Database connection successful!<br>";
    echo "Connected to database: fix_lanka<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    die();
}

// Test 2: Check if Repairer table has data
echo "<h3>2. Checking Repairer Table</h3>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Repairer");
    $result = $stmt->fetch();
    $repairerCount = $result['count'];
    echo "Total Repairers: $repairerCount<br>";
    
    if ($repairerCount > 0) {
        echo "✅ Repairer data exists<br>";
        
        // Show sample repairer
        $stmt = $pdo->query("SELECT repairer_id, f_name, l_name, email, ratings, availability FROM Repairer LIMIT 3");
        $repairers = $stmt->fetchAll();
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Rating</th><th>Availability</th></tr>";
        foreach ($repairers as $r) {
            echo "<tr>";
            echo "<td>{$r['repairer_id']}</td>";
            echo "<td>{$r['f_name']} {$r['l_name']}</td>";
            echo "<td>{$r['email']}</td>";
            echo "<td>{$r['ratings']}</td>";
            echo "<td>{$r['availability']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "⚠️ No repairer data found in database<br>";
    }
} catch (Exception $e) {
    echo "❌ Error checking Repairer table: " . $e->getMessage() . "<br>";
}

// Test 3: Check if Company table has data
echo "<h3>3. Checking Company Table</h3>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Company");
    $result = $stmt->fetch();
    $companyCount = $result['count'];
    echo "Total Companies: $companyCount<br>";
    
    if ($companyCount > 0) {
        echo "✅ Company data exists<br>";
        
        // Show sample company
        $stmt = $pdo->query("SELECT company_id, company_name, email, ratings, available FROM Company LIMIT 3");
        $companies = $stmt->fetchAll();
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Rating</th><th>Available</th></tr>";
        foreach ($companies as $c) {
            echo "<tr>";
            echo "<td>{$c['company_id']}</td>";
            echo "<td>{$c['company_name']}</td>";
            echo "<td>{$c['email']}</td>";
            echo "<td>{$c['ratings']}</td>";
            echo "<td>{$c['available']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "⚠️ No company data found in database<br>";
    }
} catch (Exception $e) {
    echo "❌ Error checking Company table: " . $e->getMessage() . "<br>";
}

// Test 4: Test RepairerModel
echo "<h3>4. Testing RepairerModel</h3>";
try {
    require_once __DIR__ . '/models/RepairerModel.php';
    $repairerModel = new Repairer($pdo);
    
    $featured = $repairerModel->getFeatured(3, 0);
    echo "Featured repairers returned: " . count($featured) . "<br>";
    
    if (count($featured) > 0) {
        echo "✅ RepairerModel->getFeatured() working<br>";
        echo "<pre>";
        print_r($featured[0]);
        echo "</pre>";
    } else {
        echo "⚠️ No featured repairers returned<br>";
    }
} catch (Exception $e) {
    echo "❌ Error testing RepairerModel: " . $e->getMessage() . "<br>";
}

// Test 5: Test CompanyModel
echo "<h3>5. Testing CompanyModel</h3>";
try {
    require_once __DIR__ . '/models/CompanyModel.php';
    $companyModel = new Company($pdo);
    
    $featured = $companyModel->getFeatured(3, 0);
    echo "Featured companies returned: " . count($featured) . "<br>";
    
    if (count($featured) > 0) {
        echo "✅ CompanyModel->getFeatured() working<br>";
        echo "<pre>";
        print_r($featured[0]);
        echo "</pre>";
    } else {
        echo "⚠️ No featured companies returned<br>";
    }
} catch (Exception $e) {
    echo "❌ Error testing CompanyModel: " . $e->getMessage() . "<br>";
}

// Test 6: Test API Endpoints
echo "<h3>6. Testing API Endpoints</h3>";
$baseUrl = "http://localhost/2nd-Year-Group-Project/FixLanka";
$endpoints = [
    'get-featured-providers' => "$baseUrl/get-featured-providers",
    'get-providers' => "$baseUrl/get-providers?limit=5"
];

foreach ($endpoints as $name => $url) {
    echo "<strong>Testing: $name</strong><br>";
    echo "URL: <a href='$url' target='_blank'>$url</a><br>";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP Code: $httpCode<br>";
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if ($data && isset($data['success'])) {
            if ($data['success']) {
                echo "✅ API working! Returned " . count($data['data']) . " providers<br>";
            } else {
                echo "⚠️ API returned error: " . ($data['message'] ?? 'Unknown error') . "<br>";
            }
        } else {
            echo "⚠️ Invalid JSON response<br>";
            echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>";
        }
    } else {
        echo "❌ API request failed (HTTP $httpCode)<br>";
        echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>";
    }
    echo "<br>";
}

echo "<hr>";
echo "<h3>Summary</h3>";
echo "Total Repairers in DB: $repairerCount<br>";
echo "Total Companies in DB: $companyCount<br>";

if ($repairerCount == 0 && $companyCount == 0) {
    echo "<p style='color: red; font-weight: bold;'>⚠️ NO DATA FOUND! You need to insert test data into the database.</p>";
    echo "<p>Run the sample_data.sql file or create test accounts for repairers and companies.</p>";
}
?>
