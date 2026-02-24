<?php
// Test script to debug API responses for company ID 1
$company_id = 5;

// 1. Test Company Employees API
$ch = curl_init('http://localhost/2nd-Year-Group-Project/FixLanka/api/company-employees.php?action=stats&company_id=' . $company_id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$employees_response = curl_exec($ch);
curl_close($ch);

echo "=== Company Employees API ===\n";
echo "Response: " . substr($employees_response, 0, 500) . "...\n\n";

// 2. Test Freelancers API
$ch = curl_init('http://localhost/2nd-Year-Group-Project/FixLanka/api/freelancers.php?company_id=' . $company_id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$freelancers_response = curl_exec($ch);
curl_close($ch);

echo "=== Freelancers API ===\n";
echo "Response: " . substr($freelancers_response, 0, 500) . "...\n\n";

// 3. Test Applications API
$ch = curl_init('http://localhost/2nd-Year-Group-Project/FixLanka/api/repairer-applications.php?action=list&company_id=' . $company_id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$applications_response = curl_exec($ch);
curl_close($ch);

echo "=== Applications API ===\n";
echo "Response: " . substr($applications_response, 0, 500) . "...\n\n";

// 4. Test Job Postings API
$ch = curl_init('http://localhost/2nd-Year-Group-Project/FixLanka/api/job-postings.php?company_id=' . $company_id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$jobs_response = curl_exec($ch);
curl_close($ch);

echo "=== Job Postings API ===\n";
echo "Response: " . substr($jobs_response, 0, 500) . "...\n\n";
?>
