<?php
/**
 * Company Payments API
 * Handles fetching income (from projects/milestones) and expenses for companies
 * 
 * @author FixLanka Team
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once 'helpers.php';

header('Content-Type: application/json');

$requestMethod = $_SERVER['REQUEST_METHOD'];

// Route requests
switch ($requestMethod) {
    case 'GET':
        handleGetPayments();
        break;
    case 'POST':
        handleCreateExpense();
        break;
    case 'PUT':
        handleUpdateExpense();
        break;
    case 'DELETE':
        handleDeleteExpense();
        break;
    default:
        sendErrorResponse('Method not allowed', 405);
}

/**
 * GET - Fetch all payments (income from milestones + expenses) for company
 */
function handleGetPayments() {
    global $pdo;
    
    try {
        requireAuth();
        
        $userId = $_SESSION['user_id'];
        $companyId = $_SESSION['company_id'] ?? null;
        
        if (!$companyId) {
            $companyData = getCompanyByUserId($pdo, $userId);
            if (!$companyData) {
                sendSuccessResponse([
                    'summary' => getEmptySummary(),
                    'income' => [],
                    'expenses' => [],
                    'projects' => []
                ]);
                return;
            }
            $companyId = $companyData['company_id'];
        }
        
        // Get period filter
        $period = $_GET['period'] ?? 'month';
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        $dateFilter = getDateFilter($period, $startDate, $endDate);
        
        // Fetch income from both direct milestone payments and released escrow/contract payments
        $incomeQuery = "SELECT * FROM (
                            SELECT 
                                mp.payment_id as id,
                                COALESCE(mp.paid_at, mp.payment_date) as date,
                                p.title as project_name,
                                p.project_id,
                                u.f_name as client_first_name,
                                u.l_name as client_last_name,
                                mp.amount,
                                mp.status,
                                mp.method as payment_method,
                                cm.title as milestone_description
                            FROM milestonepayment mp
                            JOIN contract_milestone cm ON mp.milestone_id = cm.milestone_id
                            JOIN contract c ON cm.contract_id = c.contract_id
                            JOIN project p ON c.project_id = p.project_id
                            JOIN user u ON p.customer_id = u.user_id
                            WHERE p.company_id = :company_id
                            
                            UNION ALL
                            
                            SELECT 
                                cph.payment_id as id,
                                COALESCE(cph.completed_at, cph.created_at) as date,
                                p.title as project_name,
                                p.project_id,
                                u.f_name as client_first_name,
                                u.l_name as client_last_name,
                                cph.amount,
                                cph.status,
                                cph.payment_method as payment_method,
                                CONCAT(REPLACE(cph.payment_type, '_', ' '), ': ', COALESCE(cm.title, 'General payment')) as milestone_description
                            FROM contract_payment_history cph
                            JOIN contract c ON cph.contract_id = c.contract_id
                            JOIN project p ON c.project_id = p.project_id
                            JOIN user u ON p.customer_id = u.user_id
                            LEFT JOIN contract_milestone cm ON cph.milestone_id = cm.milestone_id
                            WHERE p.company_id = :company_id2
                            AND cph.status = 'completed'
                            AND cph.payment_type IN ('milestone_release', 'upfront_payment', 'bonus')
                        ) AS combined_income
                        WHERE 1=1
                        {$dateFilter}
                        ORDER BY date DESC";
        
        $stmt = $pdo->prepare($incomeQuery);
        $stmt->bindParam(':company_id', $companyId, PDO::PARAM_INT);
        $stmt->bindParam(':company_id2', $companyId, PDO::PARAM_INT);
        $stmt->execute();
        $incomePayments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format income data
        $income = array_map(function($payment) {
            return [
                'id' => 'PAY-' . str_pad($payment['id'], 6, '0', STR_PAD_LEFT),
                'date' => $payment['date'],
                'project_name' => $payment['project_name'],
                'project_id' => $payment['project_id'],
                'client_name' => $payment['client_first_name'] . ' ' . $payment['client_last_name'],
                'amount' => floatval($payment['amount']),
                'status' => $payment['status'],
                'payment_method' => $payment['payment_method'],
                'description' => $payment['milestone_description']
            ];
        }, $incomePayments);
        
        // Fetch expenses from multiple sources
        $expenseData = [];
        
        // 1. Manual Expenses (CompanyExpense table) - may not exist
        try {
            $manualExpenseQuery = "SELECT * FROM (
                                    SELECT 
                                        e.expense_id as id,
                                        e.expense_date as date,
                                        e.project_id,
                                        COALESCE(p.title, 'General Expense') as project_name,
                                        e.category,
                                        e.description,
                                        e.amount
                                    FROM CompanyExpense e
                                    LEFT JOIN Project p ON e.project_id = p.project_id
                                    WHERE e.company_id = :company_id
                                ) AS me
                                WHERE 1=1 {$dateFilter}
                                ORDER BY date DESC";
            
            $stmt = $pdo->prepare($manualExpenseQuery);
            $stmt->bindParam(':company_id', $companyId, PDO::PARAM_INT);
            $stmt->execute();
            $manualExpenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($manualExpenses as $me) {
                $expenseData[] = [
                    'id' => 'EXP-' . str_pad($me['id'], 6, '0', STR_PAD_LEFT),
                    'date' => $me['date'],
                    'project_id' => $me['project_id'],
                    'project_name' => $me['project_name'],
                    'category' => $me['category'],
                    'description' => $me['description'],
                    'amount' => floatval($me['amount'])
                ];
            }
        } catch (PDOException $e) {
            if ($e->getCode() != '42S02') {
                error_log("Database error fetching manual expenses: " . $e->getMessage());
            }
        }

        // 2. Advertisement Expenses (from advertisement table)
        try {
            // Treat advertisements with committed budget as expenses
            $adExpenseQuery = "SELECT * FROM (
                                    SELECT 
                                        ad_id as id,
                                        submission_date as date,
                                        title as ad_title,
                                        type as ad_type,
                                        budget as amount,
                                        status
                                    FROM advertisement
                                    WHERE provider_id = :company_id 
                                      AND provider_type = 'company'
                                      AND status IN ('approved', 'active', 'scheduled', 'expired', 'paused', 'suspended')
                                ) AS ads
                                WHERE 1=1 {$dateFilter}";
            
            $stmt = $pdo->prepare($adExpenseQuery);
            $stmt->bindParam(':company_id', $companyId, PDO::PARAM_INT);
            $stmt->execute();
            $adExpenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($adExpenses as $ae) {
                $expenseData[] = [
                    'id' => 'AD-' . str_pad($ae['id'], 6, '0', STR_PAD_LEFT),
                    'date' => $ae['date'],
                    'project_id' => null,
                    'project_name' => 'Marketing',
                    'category' => 'other',
                    'description' => "Advertisement: " . $ae['ad_title'] . " (" . ucfirst($ae['ad_type']) . ")",
                    'amount' => floatval($ae['amount'])
                ];
            }
        } catch (PDOException $e) {
            error_log("Database error fetching ad expenses: " . $e->getMessage());
        }

        // 3. Billing History (from billinghistory table)
        try {
            $billingQuery = "SELECT * FROM (
                                SELECT 
                                    invoice_id as id,
                                    date,
                                    amount,
                                    status
                                FROM billinghistory
                                WHERE company_id = :company_id
                                  AND status = 'paid'
                            ) AS bh
                            WHERE 1=1 {$dateFilter}";
            
            $stmt = $pdo->prepare($billingQuery);
            $stmt->bindParam(':company_id', $companyId, PDO::PARAM_INT);
            $stmt->execute();
            $billingData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($billingData as $bd) {
                $expenseData[] = [
                    'id' => 'INV-' . str_pad($bd['id'], 6, '0', STR_PAD_LEFT),
                    'date' => $bd['date'],
                    'project_id' => null,
                    'project_name' => 'Service Fees',
                    'category' => 'other',
                    'description' => "Billing Invoice #" . $bd['id'],
                    'amount' => floatval($bd['amount'])
                ];
            }
        } catch (PDOException $e) {
            error_log("Database error fetching billing history: " . $e->getMessage());
        }

        // Sort combined expenses by date descending
        usort($expenseData, function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });
        
        $expenses = $expenseData;
        
        // Fetch company projects for dropdowns
        $projectsQuery = "SELECT project_id, title FROM Project WHERE company_id = :company_id ORDER BY title";
        $stmt = $pdo->prepare($projectsQuery);
        $stmt->bindParam(':company_id', $companyId, PDO::PARAM_INT);
        $stmt->execute();
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate summary
        $totalIncome = array_reduce($income, function($sum, $p) {
            return $sum + ($p['status'] === 'completed' ? $p['amount'] : 0);
        }, 0);
        
        $totalExpenses = array_reduce($expenses, function($sum, $e) {
            return $sum + $e['amount'];
        }, 0);
        
        $pendingPayments = array_reduce($income, function($sum, $p) {
            return $sum + ($p['status'] === 'pending' ? $p['amount'] : 0);
        }, 0);
        
        $completedIncomeCount = count(array_filter($income, fn($p) => $p['status'] === 'completed'));
        $netProfit = $totalIncome - $totalExpenses;
        $avgPayment = $completedIncomeCount > 0 ? round($totalIncome / $completedIncomeCount, 2) : 0;
        $profitMargin = $totalIncome > 0 ? round(($netProfit / $totalIncome) * 100) : 0;
        $expenseRatio = $totalIncome > 0 ? round(($totalExpenses / $totalIncome) * 100) : 0;
        
        $summary = [
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'pending_payments' => $pendingPayments,
            'total_transactions' => count($income) + count($expenses),
            'avg_payment' => $avgPayment,
            'profit_margin' => $profitMargin,
            'expense_ratio' => $expenseRatio,
            'pending_count' => count(array_filter($income, fn($p) => $p['status'] === 'pending'))
        ];
        
        sendSuccessResponse([
            'summary' => $summary,
            'income' => $income,
            'expenses' => $expenses,
            'projects' => $projects
        ]);
        
    } catch (PDOException $e) {
        error_log("Database error in payments API: " . $e->getMessage());
        sendErrorResponse('Database error occurred', 500);
    } catch (Exception $e) {
        error_log("Error in payments API: " . $e->getMessage());
        sendErrorResponse('An error occurred', 500);
    }
}

/**
 * POST - Create new expense
 */
function handleCreateExpense() {
    global $pdo;
    
    try {
        requireAuth();
        
        $userId = $_SESSION['user_id'];
        $companyId = $_SESSION['company_id'] ?? null;
        
        if (!$companyId) {
            $companyData = getCompanyByUserId($pdo, $userId);
            if (!$companyData) {
                sendErrorResponse('Company not found', 404);
                return;
            }
            $companyId = $companyData['company_id'];
        }
        
        // Get POST data
        $data = json_decode(file_get_contents('php://input'), true);
        
        $projectId = !empty($data['project_id']) ? $data['project_id'] : null;
        $category = $data['category'] ?? '';
        $amount = floatval($data['amount'] ?? 0);
        $description = $data['description'] ?? '';
        $expenseDate = $data['expense_date'] ?? date('Y-m-d');
        
        // Validate
        if (empty($category) || $amount <= 0 || empty($description)) {
            sendErrorResponse('Category, amount, and description are required', 400);
            return;
        }
        
        $validCategories = ['materials', 'labor', 'transport', 'equipment', 'permits', 'other'];
        if (!in_array($category, $validCategories)) {
            sendErrorResponse('Invalid category', 400);
            return;
        }
        
        // Insert expense
        try {
            $query = "INSERT INTO CompanyExpense (company_id, project_id, category, amount, description, expense_date)
                      VALUES (:company_id, :project_id, :category, :amount, :description, :expense_date)";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':company_id' => $companyId,
                ':project_id' => $projectId,
                ':category' => $category,
                ':amount' => $amount,
                ':description' => $description,
                ':expense_date' => $expenseDate
            ]);
            
            $expenseId = $pdo->lastInsertId();
            
            sendSuccessResponse([
                'message' => 'Expense created successfully',
                'expense_id' => $expenseId
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() == '42S02') {
                sendErrorResponse('Expense tracking is not configured in the database (CompanyExpense table missing)', 400);
                return;
            }
            throw $e;
        }
        
    } catch (PDOException $e) {
        error_log("Database error creating expense: " . $e->getMessage());
        sendErrorResponse('Failed to create expense', 500);
    }
}

/**
 * PUT - Update expense
 */
function handleUpdateExpense() {
    global $pdo;
    
    try {
        requireAuth();
        
        $data = json_decode(file_get_contents('php://input'), true);
        $expenseId = $data['expense_id'] ?? null;
        
        if (!$expenseId) {
            sendErrorResponse('Expense ID required', 400);
            return;
        }
        
        // Extract numeric ID from formatted ID
        if (strpos($expenseId, 'EXP-') === 0) {
            $expenseId = intval(ltrim(substr($expenseId, 4), '0'));
        }
        
        $projectId = !empty($data['project_id']) ? $data['project_id'] : null;
        $category = $data['category'] ?? '';
        $amount = floatval($data['amount'] ?? 0);
        $description = $data['description'] ?? '';
        $expenseDate = $data['expense_date'] ?? date('Y-m-d');
        
        // Validate
        if (empty($category) || $amount <= 0 || empty($description)) {
            sendErrorResponse('Category, amount, and description are required', 400);
            return;
        }
        
        try {
            $query = "UPDATE CompanyExpense 
                      SET project_id = :project_id, category = :category, amount = :amount, 
                          description = :description, expense_date = :expense_date
                      WHERE expense_id = :expense_id";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':expense_id' => $expenseId,
                ':project_id' => $projectId,
                ':category' => $category,
                ':amount' => $amount,
                ':description' => $description,
                ':expense_date' => $expenseDate
            ]);
            
            sendSuccessResponse(['message' => 'Expense updated successfully']);
        } catch (PDOException $e) {
            if ($e->getCode() == '42S02') {
                sendErrorResponse('Expense tracking is not configured in the database', 400);
                return;
            }
            throw $e;
        }
        
    } catch (PDOException $e) {
        error_log("Database error updating expense: " . $e->getMessage());
        sendErrorResponse('Failed to update expense', 500);
    }
}

/**
 * DELETE - Delete expense
 */
function handleDeleteExpense() {
    global $pdo;
    
    try {
        requireAuth();
        
        $data = json_decode(file_get_contents('php://input'), true);
        $expenseId = $data['expense_id'] ?? null;
        
        if (!$expenseId) {
            sendErrorResponse('Expense ID required', 400);
            return;
        }
        
        // Extract numeric ID
        if (strpos($expenseId, 'EXP-') === 0) {
            $expenseId = intval(ltrim(substr($expenseId, 4), '0'));
        }
        
        try {
            $query = "DELETE FROM CompanyExpense WHERE expense_id = :expense_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':expense_id' => $expenseId]);
            
            sendSuccessResponse(['message' => 'Expense deleted successfully']);
        } catch (PDOException $e) {
            if ($e->getCode() == '42S02') {
                sendErrorResponse('Expense tracking is not configured in the database', 400);
                return;
            }
            throw $e;
        }
        
    } catch (PDOException $e) {
        error_log("Database error deleting expense: " . $e->getMessage());
        sendErrorResponse('Failed to delete expense', 500);
    }
}

/**
 * Helper: Get date filter SQL based on period
 */
function getDateFilter($period, $startDate = null, $endDate = null) {
    switch ($period) {
        case 'today':
            return "AND DATE(date) = CURDATE()";
        case 'week':
            return "AND date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        case 'month':
            return "AND MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())";
        case 'quarter':
            return "AND QUARTER(date) = QUARTER(CURDATE()) AND YEAR(date) = YEAR(CURDATE())";
        case 'year':
            return "AND YEAR(date) = YEAR(CURDATE())";
        case 'custom':
            if ($startDate && $endDate) {
                return "AND DATE(date) BETWEEN '$startDate' AND '$endDate'";
            }
            return "";
        default:
            return "";
    }
}

/**
 * Helper: Get empty summary
 */
function getEmptySummary() {
    return [
        'total_income' => 0,
        'total_expenses' => 0,
        'net_profit' => 0,
        'pending_payments' => 0,
        'total_transactions' => 0,
        'avg_payment' => 0,
        'profit_margin' => 0,
        'expense_ratio' => 0,
        'pending_count' => 0
    ];
}
