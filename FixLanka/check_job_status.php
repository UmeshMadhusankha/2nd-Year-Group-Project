<?php
require_once 'c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/config/database.php';
echo "DESCRIBE jobrequest\n";
$stmt = $pdo->query("DESCRIBE jobrequest");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    if ($row['Field'] == 'status') echo "status Type: {$row['Type']}\n";
}
echo "\nDESCRIBE directjobrequest\n";
$stmt = $pdo->query("DESCRIBE directjobrequest");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    if ($row['Field'] == 'status') echo "status Type: {$row['Type']}\n";
}
