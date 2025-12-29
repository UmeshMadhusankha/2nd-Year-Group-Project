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
        $dateFilter = getDateFilter($period);
        
        // Fetch income from milestone payments
        $incomeQuery = "SELECT 
                            mp.payment_id as id,
                            mp.payment_date as date,
                            p.title as project_name,
                            p.project_id,
                            u.f_name as client_first_name,
                            u.l_name as client_last_name,
                            mp.amount,
                            mp.status,
                            mp.method as payment_method,
                            m.description as milestone_description
                        FROM MilestonePayment mp
                        JOIN Milestone m ON mp.milestone_id = m.milestone_id
                        JOIN Contract c ON m.contract_id = c.contract_id
                        JOIN Project p ON c.project_id = p.project_id
                        JOIN User u ON p.customer_id = u.user_id
                        WHERE p.company_id = :company_id
                        {$dateFilter}
                        ORDER BY mp.payment_date DESC";
        
        $stmt = $pdo->prepare($incomeQuery);
        $stmt->bindParam(':company_id', $companyId, PDO::PARAM_INT);
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
        
        // Fetch expenses
        $expenseQuery = "SELECT 
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
                        {$dateFilter}
                        ORDER BY e.expense_date DESC";
        
        $stmt = $pdo->prepare($expenseQuery);
        $stmt->bindParam(':company_id', $companyId, PDO::PARAM_INT);
        $stmt->execute();
        $expenseData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format expense data
        $expenses = array_map(function($expense) {
            return [
                'id' => 'EXP-' . str_pad($expense['id'], 6, '0', STR_PAD_LEFT),
                'date' => $expense['date'],
                'project_id' => $expense['project_id'],
                'project_name' => $expense['project_name'],
                'category' => $expense['category'],
                'description' => $expense['description'],
                'amount' => floatval($expense['amount'])
            ];
        }, $expenseData);
        
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
        
        $query = "DELETE FROM CompanyExpense WHERE expense_id = :expense_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':expense_id' => $expenseId]);
        
        sendSuccessResponse(['message' => 'Expense deleted successfully']);
        
    } catch (PDOException $e) {
        error_log("Database error deleting expense: " . $e->getMessage());
        sendErrorResponse('Failed to delete expense', 500);
    }
}

/**
 * Helper: Get date filter SQL based on period
 */
function getDateFilter($period) {
    switch ($period) {
        case 'today':
            return "AND DATE(mp.payment_date) = CURDATE()";
        case 'week':
            return "AND mp.payment_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        case 'month':
            return "AND MONTH(mp.payment_date) = MONTH(CURDATE()) AND YEAR(mp.payment_date) = YEAR(CURDATE())";
        case 'quarter':
            return "AND QUARTER(mp.payment_date) = QUARTER(CURDATE()) AND YEAR(mp.payment_date) = YEAR(CURDATE())";
        case 'year':
            return "AND YEAR(mp.payment_date) = YEAR(CURDATE())";
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
