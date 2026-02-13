<?php
/**
 * Invoice Controller
 * Phase 4 - Feature 5: Weekly Invoice Automation
 * 
 * Handles invoice generation and management for time & material contracts
 */

require_once __DIR__ . '/../config/database.php';

class InvoiceController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Generate weekly invoice for time & material contract
     */
    public function generateWeeklyInvoice($contract_id) {
        try {
            $this->pdo->beginTransaction();
            
            // Get contract
            $stmt = $this->pdo->prepare("SELECT * FROM contracts WHERE id = ? AND payment_type = 'time_material'");
            $stmt->execute([$contract_id]);
            $contract = $stmt->fetch();
            
            if (!$contract) {
                return ['success' => false, 'message' => 'Contract not found or not time & material'];
            }
            
            // Get approved time entries from last 7 days not yet invoiced
            $stmt = $this->pdo->prepare("
                SELECT * FROM contract_time_logs 
                WHERE contract_id = ? 
                AND status = 'approved'
                AND invoice_id IS NULL
                AND log_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                ORDER BY log_date ASC
            ");
            $stmt->execute([$contract_id]);
            $time_entries = $stmt->fetchAll();
            
            if (empty($time_entries)) {
                return ['success' => false, 'message' => 'No approved time entries to invoice'];
            }
            
            // Calculate totals
            $total_hours = 0;
            $total_amount = 0;
            foreach ($time_entries as $entry) {
                $total_hours += $entry['hours_worked'];
                $total_amount += ($entry['hours_worked'] * $entry['hourly_rate']);
            }
            
            // Generate invoice number
            $invoice_number = 'INV-' . $contract_id . '-' . date('Ymd') . '-' . substr(md5(uniqid()), 0, 4);
            
            // Create invoice
            $stmt = $this->pdo->prepare("
                INSERT INTO contract_invoices 
                (contract_id, invoice_number, invoice_date, total_hours, total_amount, status, created_at)
                VALUES (?, ?, NOW(), ?, ?, 'pending', NOW())
            ");
            $stmt->execute([$contract_id, $invoice_number, $total_hours, $total_amount]);
            $invoice_id = $this->pdo->lastInsertId();
            
            // Link time entries to invoice
            $entry_ids = array_column($time_entries, 'id');
            $placeholders = str_repeat('?,', count($entry_ids) - 1) . '?';
            $stmt = $this->pdo->prepare("UPDATE contract_time_logs SET invoice_id = ? WHERE id IN ($placeholders)");
            $stmt->execute(array_merge([$invoice_id], $entry_ids));
            
            // Add timeline event
            
            $stmt = $this->pdo->prepare("INSERT INTO contract_timeline (contract_id, event_type, event_title, event_description, actor_type, created_at) VALUES (?, 'invoice_generated', 'Invoice Generated', ?, 'system', NOW())"); $timelineDesc = "Weekly invoice generated: {$invoice_number} (Rs. " . number_format($total_amount, 2) . ")"; $stmt->execute([$contract_id, $timelineDesc]);
            
            // Notify customer
            $stmt = $this->pdo->prepare("INSERT INTO contract_notifications (contract_id, recipient_type, recipient_id, notification_type, title, message, created_at) SELECT ?, 'customer', customer_id, 'invoice_generated', 'New Invoice Ready', ?, NOW() FROM contract WHERE contract_id = ?"); $notificationMsg = "New invoice ready: {$invoice_number} for Rs. " . number_format($total_amount, 2); $stmt->execute([$contract_id, $notificationMsg, $contract_id]);
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Invoice generated',
                'invoice_id' => $invoice_id,
                'invoice_number' => $invoice_number,
                'total_amount' => $total_amount
            ];
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Generate weekly invoice error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    /**
     * Get invoices for contract
     */
    public function getInvoices($contract_id, $status = null) {
        try {
            $query = "SELECT * FROM contract_invoices WHERE contract_id = ?";
            $params = [$contract_id];
            
            if ($status) {
                $query .= " AND status = ?";
                $params[] = $status;
            }
            
            $query .= " ORDER BY invoice_date DESC";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get invoices error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get invoice details with line items
     */
    public function getInvoiceDetails($invoice_id) {
        try {
            // Get invoice
            $stmt = $this->pdo->prepare("SELECT * FROM contract_invoices WHERE id = ?");
            $stmt->execute([$invoice_id]);
            $invoice = $stmt->fetch();
            
            if (!$invoice) {
                return null;
            }
            
            // Get time entries (line items)
            $stmt = $this->pdo->prepare("
                SELECT * FROM contract_time_logs 
                WHERE invoice_id = ? 
                ORDER BY log_date ASC
            ");
            $stmt->execute([$invoice_id]);
            $invoice['line_items'] = $stmt->fetchAll();
            
            return $invoice;
        } catch (PDOException $e) {
            error_log("Get invoice details error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Approve invoice (customer)
     */
    public function approveInvoice($invoice_id, $customer_id) {
        try {
            $this->pdo->beginTransaction();
            
            // Verify customer owns contract
            $stmt = $this->pdo->prepare("
                SELECT i.*, c.customer_id 
                FROM contract_invoices i
                JOIN contracts c ON i.contract_id = c.id
                WHERE i.id = ? AND c.customer_id = ?
            ");
            $stmt->execute([$invoice_id, $customer_id]);
            $invoice = $stmt->fetch();
            
            if (!$invoice) {
                return ['success' => false, 'message' => 'Invoice not found'];
            }
            
            // Update invoice status
            $stmt = $this->pdo->prepare("
                UPDATE contract_invoices 
                SET status = 'approved', 
                    approved_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$invoice_id]);
            
            // Create payment request
            $stmt = $this->pdo->prepare("
                INSERT INTO contract_payments 
                (contract_id, amount, payment_type, payment_method, payment_status, description, created_at)
                VALUES (?, ?, 'weekly', 'time_material', 'pending', ?, NOW())
            ");
            $stmt->execute([
                $invoice['contract_id'],
                $invoice['total_amount'],
                "Weekly invoice payment: {$invoice['invoice_number']}"
            ]);
            
            // Add timeline event
            
            $stmt = $this->pdo->prepare("INSERT INTO contract_timeline (contract_id, event_type, event_title, event_description, actor_type, created_at) VALUES (?, 'invoice_approved', 'Invoice Approved', ?, 'customer', NOW())"); $timelineDesc = "Invoice approved: {$invoice['invoice_number']}"; $stmt->execute([$invoice['contract_id'], $timelineDesc]);
            
            // Notify company
            $stmt = $this->pdo->prepare("INSERT INTO contract_notifications (contract_id, recipient_type, recipient_id, notification_type, title, message, created_at) SELECT ?, 'company', company_id, 'invoice_approved', 'Invoice Approved', ?, NOW() FROM contract WHERE contract_id = ?"); $notificationMsg = "Invoice approved: {$invoice['invoice_number']}"; $stmt->execute([$invoice['contract_id'], $notificationMsg, $invoice['contract_id']]);
            
            $this->pdo->commit();
            return ['success' => true, 'message' => 'Invoice approved'];
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Approve invoice error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    /**
     * Reject invoice (customer)
     */
    public function rejectInvoice($invoice_id, $customer_id, $reason) {
        try {
            $this->pdo->beginTransaction();
            
            // Verify customer owns contract
            $stmt = $this->pdo->prepare("
                SELECT i.*, c.customer_id 
                FROM contract_invoices i
                JOIN contracts c ON i.contract_id = c.id
                WHERE i.id = ? AND c.customer_id = ?
            ");
            $stmt->execute([$invoice_id, $customer_id]);
            $invoice = $stmt->fetch();
            
            if (!$invoice) {
                return ['success' => false, 'message' => 'Invoice not found'];
            }
            
            // Update invoice status
            $stmt = $this->pdo->prepare("
                UPDATE contract_invoices 
                SET status = 'rejected', 
                    rejection_reason = ?, 
                    rejected_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$reason, $invoice_id]);
            
            // Add timeline event
            
            $stmt = $this->pdo->prepare("INSERT INTO contract_timeline (contract_id, event_type, event_title, event_description, actor_type, created_at) VALUES (?, 'invoice_rejected', 'Invoice Rejected', ?, 'customer', NOW())"); $timelineDesc = "Invoice rejected: {$invoice['invoice_number']} - {$reason}"; $stmt->execute([$invoice['contract_id'], $timelineDesc]);
            
            // Notify company
            $stmt = $this->pdo->prepare("INSERT INTO contract_notifications (contract_id, recipient_type, recipient_id, notification_type, title, message, priority, created_at) SELECT ?, 'company', company_id, 'invoice_rejected', 'Invoice Rejected', ?, 'high', NOW() FROM contract WHERE contract_id = ?"); $notificationMsg = "Invoice rejected: {$invoice['invoice_number']} - {$reason}"; $stmt->execute([$invoice['contract_id'], $notificationMsg, $invoice['contract_id']]);
            
            $this->pdo->commit();
            return ['success' => true, 'message' => 'Invoice rejected'];
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Reject invoice error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
}
