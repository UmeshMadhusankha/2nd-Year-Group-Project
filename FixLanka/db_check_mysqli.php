<?php
$conn = new mysqli("127.0.0.1", "root", "", "fix_lanka");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tables = ['contract_milestone', 'milestone', 'contract_timeline', 'phases'];
foreach ($tables as $table) {
    echo "--- Table: $table ---\n";
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        $data = $conn->query("SELECT * FROM $table ORDER BY created_at DESC LIMIT 5");
        if ($data) {
            $rows = [];
            while($row = $data->fetch_assoc()) {
                $rows[] = $row;
            }
            echo json_encode($rows, JSON_PRETTY_PRINT) . "\n\n";
        } else {
            echo "Error running query: " . $conn->error . "\n\n";
        }
    } else {
        echo "Table does not exist\n\n";
    }
}
$conn->close();
