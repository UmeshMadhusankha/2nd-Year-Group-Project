<?php
// Test script to check company data and API response
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Company Data Test</h2>";
echo "<hr>";

// Test 1: Check database connection
echo "<h3>1. Database Connection</h3>";
try {
    require_once __DIR__ . '/config/database.php';
    echo "✅ Connected to database<br><br>";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage();
    die();
}

// Test 2: Check Company table data
echo "<h3>2. Company Table Data</h3>";
try {
    $stmt = $pdo->query("SELECT * FROM Company LIMIT 5");
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total companies found: " . count($companies) . "<br><br>";
    
    if (count($companies) > 0) {
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>ID</th><th>Name</th><th>Email</th><th>Rating</th><th>Address</th></tr>";
        
        foreach ($companies as $company) {
            echo "<tr>";
            echo "<td>{$company['company_id']}</td>";
            echo "<td>{$company['name']}</td>";
            echo "<td>{$company['email']}</td>";
            echo "<td>{$company['rating']}</td>";
            echo "<td>" . substr($company['address'], 0, 50) . "...</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    } else {
        echo "<p style='color: red;'>❌ No companies in database!</p>";
        echo "<p>Run this SQL in phpMyAdmin:</p>";
        echo "<pre>INSERT INTO Company (name, registration_no, address, email, contact_no, rating) VALUES
('HomeFix Solutions Ltd', 'PV12345', 'No. 123, Galle Road, Colombo 03', 'info@homefixsolutions.lk', '0112345678', 4.8),
('ElectroTech Services', 'PV23456', 'No. 456, Duplication Road, Colombo 04', 'contact@electrotech.lk', '0112456789', 4.7),
('CleanPro Lanka', 'PV34567', 'No. 789, Baseline Road, Colombo 09', 'hello@cleanpro.lk', '0112567890', 4.9);</pre>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

// Test 3: Test CompanyModel
echo "<h3>3. CompanyModel Test</h3>";
try {
    require_once __DIR__ . '/models/CompanyModel.php';
    $companyModel = new Company($pdo);
    
    // Test getFeatured
    echo "<strong>Testing getFeatured():</strong><br>";
    $featured = $companyModel->getFeatured(5, 0);
    echo "Featured companies returned: " . count($featured) . "<br>";
    
    if (count($featured) > 0) {
        echo "✅ First company: " . $featured[0]['full_name'] . " (Rating: " . $featured[0]['ratings'] . ")<br>";
        echo "<pre>";
        print_r($featured[0]);
        echo "</pre>";
    } else {
        echo "⚠️ No featured companies returned<br>";
    }
    
    echo "<br><strong>Testing getAll():</strong><br>";
    $all = $companyModel->getAll([], 5, 0);
    echo "All companies returned: " . count($all) . "<br>";
    
    if (count($all) > 0) {
        echo "✅ Companies fetched successfully<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
}

// Test 4: Test API endpoint
echo "<h3>4. API Endpoint Test</h3>";

$testUrls = [
    'All providers' => 'http://localhost/2nd-Year-Group-Project/FixLanka/get-providers?limit=10',
    'Only companies' => 'http://localhost/2nd-Year-Group-Project/FixLanka/get-providers?provider_type=company&limit=10',
    'Featured providers' => 'http://localhost/2nd-Year-Group-Project/FixLanka/get-featured-providers?limit=10'
];

foreach ($testUrls as $name => $url) {
    echo "<strong>$name:</strong><br>";
    echo "URL: <a href='$url' target='_blank'>$url</a><br>";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            $providerCount = count($data['data']);
            echo "✅ Returned $providerCount providers<br>";
            
            // Count companies vs individuals
            $companies = 0;
            $individuals = 0;
            foreach ($data['data'] as $provider) {
                if (isset($provider['provider_type'])) {
                    if ($provider['provider_type'] === 'company') {
                        $companies++;
                    } else {
                        $individuals++;
                    }
                }
            }
            echo "Companies: $companies, Individuals: $individuals<br>";
            
            // Show first provider
            if ($providerCount > 0) {
                echo "<details><summary>First provider data</summary><pre>";
                print_r($data['data'][0]);
                echo "</pre></details>";
            }
        } else {
            echo "⚠️ API returned: " . ($data['message'] ?? 'Unknown error') . "<br>";
        }
    } else {
        echo "❌ HTTP Error $httpCode<br>";
        echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>";
    }
    echo "<br>";
}

echo "<hr>";
echo "<h3>Summary</h3>";
echo "If you see 0 companies in API responses but companies exist in the database, check:<br>";
echo "1. CompanyModel->getAll() method is working correctly<br>";
echo "2. provider_type field is set to 'company' in the query<br>";
echo "3. Check error logs in browser console and PHP error log<br>";
?>
