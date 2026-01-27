<?php
/**
 * Direct test of search functionality
 * Visit: http://localhost/2nd-Year-Group-Project/FixLanka/test_search_direct.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

echo "<h1>Search Test</h1>";

// Check session
echo "<h2>Session Info:</h2>";
echo "<pre>";
echo "user_id: " . ($_SESSION['user_id'] ?? 'NOT SET') . "\n";
echo "user_role: " . ($_SESSION['user_role'] ?? 'NOT SET') . "\n";
echo "Full session: " . print_r($_SESSION, true);
echo "</pre>";

// Test database connection
echo "<h2>Database Connection Test:</h2>";
try {
    require_once __DIR__ . '/config/databse.php';
    echo "✅ Database connected successfully<br>";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM project");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Total projects in database: " . $result['count'] . "<br>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM jobrequest");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Total requests in database: " . $result['count'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test search with different categories
echo "<h2>Search Test Results:</h2>";

$testCases = [
    ['query' => 'kit', 'category' => 'all'],
    ['query' => 'kit', 'category' => 'projects'],
    ['query' => 'kit', 'category' => 'requests'],
];

foreach ($testCases as $test) {
    $query = $test['query'];
    $category = $test['category'];
    
    echo "<h3>Testing: query='$query', category='$category'</h3>";
    
    // Simulate the API call
    $url = "http://localhost/2nd-Year-Group-Project/FixLanka/api/global-search.php?q=" . urlencode($query) . "&category=" . urlencode($category);
    
    $ch = curl_init($url);
    curl_setopt($ch, $ch_CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id()); // Pass session
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP Code: $httpCode<br>";
    echo "Response:<br>";
    echo "<pre>";
    $data = json_decode($response, true);
    print_r($data);
    echo "</pre>";
    echo "<hr>";
}

// Check error log
echo "<h2>Recent PHP Error Log:</h2>";
$errorLog = 'C:\xampp\apache\logs\error.log';
if (file_exists($errorLog)) {
    $lines = file($errorLog);
    $recent = array_slice($lines, -20); // Last 20 lines
    echo "<pre>";
    echo implode("", $recent);
    echo "</pre>";
} else {
    echo "Error log not found at: $errorLog";
}
