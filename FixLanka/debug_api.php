<?php
// Direct API test - simulates what the frontend does
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Direct API Test</h2>";
echo "<p>Testing the ProviderController API endpoints</p>";
echo "<hr>";

// Test 1: Get all providers
echo "<h3>Test 1: Get All Providers (no filter)</h3>";
$url = "http://localhost/2nd-Year-Group-Project/FixLanka/get-providers?limit=20";
testAPI($url);

// Test 2: Get only companies
echo "<h3>Test 2: Get Only Companies</h3>";
$url = "http://localhost/2nd-Year-Group-Project/FixLanka/get-providers?provider_type=company&limit=20";
testAPI($url);

// Test 3: Get only individuals
echo "<h3>Test 3: Get Only Individuals</h3>";
$url = "http://localhost/2nd-Year-Group-Project/FixLanka/get-providers?provider_type=individual&limit=20";
testAPI($url);

// Test 4: Get featured providers
echo "<h3>Test 4: Get Featured Providers</h3>";
$url = "http://localhost/2nd-Year-Group-Project/FixLanka/get-featured-providers?limit=20";
testAPI($url);

function testAPI($url) {
    echo "<p><strong>URL:</strong> <a href='$url' target='_blank'>$url</a></p>";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<p><strong>HTTP Status:</strong> $httpCode</p>";
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        
        if ($data && isset($data['success'])) {
            if ($data['success']) {
                $count = count($data['data']);
                echo "<p style='color: green;'>✅ <strong>Success!</strong> Returned $count providers</p>";
                
                // Count by type
                $companies = 0;
                $individuals = 0;
                
                foreach ($data['data'] as $provider) {
                    if (isset($provider['provider_type'])) {
                        if ($provider['provider_type'] === 'company') {
                            $companies++;
                        } elseif ($provider['provider_type'] === 'individual') {
                            $individuals++;
                        }
                    }
                }
                
                echo "<p><strong>Breakdown:</strong> Companies: $companies, Individuals: $individuals</p>";
                
                // Show first 3 providers
                if ($count > 0) {
                    echo "<details open><summary><strong>First 3 Providers:</strong></summary>";
                    echo "<table border='1' cellpadding='5' style='border-collapse: collapse; margin-top: 10px;'>";
                    echo "<tr style='background: #f0f0f0;'>";
                    echo "<th>Type</th><th>Name</th><th>Rating</th><th>Email</th></tr>";
                    
                    for ($i = 0; $i < min(3, $count); $i++) {
                        $p = $data['data'][$i];
                        $type = $p['provider_type'] ?? 'unknown';
                        $name = $p['full_name'] ?? $p['name'] ?? 'N/A';
                        $rating = $p['ratings'] ?? 'N/A';
                        $email = $p['email'] ?? 'N/A';
                        
                        $typeColor = $type === 'company' ? 'blue' : 'green';
                        
                        echo "<tr>";
                        echo "<td style='color: $typeColor; font-weight: bold;'>$type</td>";
                        echo "<td>$name</td>";
                        echo "<td>$rating</td>";
                        echo "<td>$email</td>";
                        echo "</tr>";
                    }
                    
                    echo "</table></details>";
                }
                
            } else {
                echo "<p style='color: red;'>❌ API returned error: " . ($data['message'] ?? 'Unknown error') . "</p>";
            }
        } else {
            echo "<p style='color: orange;'>⚠️ Invalid JSON response</p>";
            echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>";
        }
    } else {
        echo "<p style='color: red;'>❌ HTTP Error $httpCode</p>";
        echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>";
    }
    
    echo "<hr>";
}

echo "<h3>Summary</h3>";
echo "<p>If 'Get Only Companies' shows 0 companies but you know they exist in the database, check:</p>";
echo "<ul>";
echo "<li>ProviderController.php is properly getting the provider_type parameter</li>";
echo "<li>CompanyModel.php getAll() method is returning data</li>";
echo "<li>Check Apache error log: C:\\xampp\\apache\\logs\\error.log</li>";
echo "</ul>";
?>
