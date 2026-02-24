<?php
/**
 * Payments Export API
 * Handles preview and download of payment reports
 */

// Start session for authentication
session_start();

// Include dependencies
require_once __DIR__ . '/../config/database.php';

// Set JSON headers for preview mode
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

try {
    // $pdo is provided by database.php
    
    // Get action type
    $action = isset($_GET['action']) ? $_GET['action'] : 'preview';
    
    // Get filters
    $type = isset($_GET['type']) ? $_GET['type'] : 'all';
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : '';
    $dateTo = isset($_GET['dateTo']) ? $_GET['dateTo'] : '';
    $format = isset($_GET['format']) ? $_GET['format'] : 'csv';
    
    // Build query
    $query = "SELECT 
        p.payment_id,
        p.amount,
        p.payment_method,
        p.status,
        p.invoice_number,
        p.created_at,
        p.description,
        p.payment_type
    FROM payment p
    WHERE 1=1";
    
    $params = [];
    
    // Apply type filter
    if ($type === 'income') {
        $query .= " AND p.payment_type = 'income'";
    } elseif ($type === 'expense') {
        $query .= " AND p.payment_type = 'expense'";
    }
    
    // Apply status filter
    if (!empty($status)) {
        $statusArray = explode(',', $status);
        $placeholders = [];
        foreach ($statusArray as $i => $s) {
            $placeholders[] = ":status$i";
            $params["status$i"] = trim($s);
        }
        $query .= " AND p.status IN (" . implode(',', $placeholders) . ")";
    }
    
    // Apply date filters
    if (!empty($dateFrom)) {
        $query .= " AND DATE(p.created_at) >= :dateFrom";
        $params['dateFrom'] = $dateFrom;
    }
    if (!empty($dateTo)) {
        $query .= " AND DATE(p.created_at) <= :dateTo";
        $params['dateTo'] = $dateTo;
    }
    
    // Add ordering
    $query .= " ORDER BY p.created_at DESC";
    
    // Execute query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $count = count($payments);
    
    if ($action === 'preview') {
        // Return JSON for preview
        header('Content-Type: application/json');
        
        // Calculate summary
        $totalAmount = 0;
        $incomeTotal = 0;
        $expenseTotal = 0;
        
        foreach ($payments as $payment) {
            $amount = floatval($payment['amount']);
            $totalAmount += $amount;
            if ($payment['payment_type'] === 'income') {
                $incomeTotal += $amount;
            } else {
                $expenseTotal += $amount;
            }
        }
        
        echo json_encode([
            'success' => true,
            'count' => $count,
            'summary' => [
                'total' => number_format($totalAmount, 2),
                'income' => number_format($incomeTotal, 2),
                'expense' => number_format($expenseTotal, 2)
            ],
            'sample' => array_slice($payments, 0, 5)
        ]);
        exit;
    }
    
    // Download mode - generate CSV
    $filename = 'payments_export_' . date('Y-m-d_His') . '.csv';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    $output = fopen('php://output', 'w');
    
    // Write CSV header
    fputcsv($output, [
        'Payment ID',
        'Amount',
        'Payment Method',
        'Status',
        'Invoice Number',
        'Type',
        'Description',
        'Date'
    ]);
    
    // Write data rows
    foreach ($payments as $payment) {
        fputcsv($output, [
            $payment['payment_id'],
            $payment['amount'],
            $payment['payment_method'] ?? 'N/A',
            $payment['status'],
            $payment['invoice_number'] ?? 'N/A',
            $payment['payment_type'] ?? 'N/A',
            $payment['description'] ?? '',
            $payment['created_at']
        ]);
    }
    
    fclose($output);
    exit;
    
} catch (Exception $e) {
    // Error response
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Export failed: ' . $e->getMessage()
    ]);
    exit;
}