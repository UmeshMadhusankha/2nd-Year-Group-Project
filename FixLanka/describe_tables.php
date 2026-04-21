<?php
require_once 'c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/config/database.php';

function describe($tableName, $pdo) {
    echo "--- $tableName ---\n";
    $stmt = $pdo->query("DESCRIBE $tableName");
    foreach ($stmt->fetchAll() as $row) {
        printf("%-20s %-20s %-10s %-5s %-10s %-10s\n", $row['Field'], $row['Type'], $row['Null'], $row['Key'], $row['Default'], $row['Extra']);
    }
}

describe('Contract', $pdo);
describe('CompanyQuotation', $pdo);
describe('JobRequest', $pdo);
