<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db = new mysqli("localhost", "root", "", "fix_lanka");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$result = $db->query("SELECT * FROM StaticContent");

if (!$result) {
    die("Query failed: " . $db->error);
}

echo "<!DOCTYPE html><html><head><title>Test</title></head><body>";
echo "<h1>Static Content Test</h1>";
echo "<p>Found " . $result->num_rows . " rows</p>";

if ($result->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Title</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['content_id'] . "</td><td>" . $row['title'] . "</td><td>" . $row['status'] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No data found. Run the SQL in phpMyAdmin.</p>";
}

echo "</body></html>";
?>