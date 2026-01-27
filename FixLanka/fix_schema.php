<?php
require_once 'config/database.php';

try {
    global $pdo;
    
    // SQL to create SupportTicket table
    $sql1 = "CREATE TABLE IF NOT EXISTS SupportTicket (
        ticket_id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        user_type ENUM('user', 'repairer', 'company') NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        category ENUM('payment', 'technical', 'account', 'feature', 'billing', 'other') NOT NULL,
        priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
        status ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
        urgency ENUM('can-wait', 'soon', 'asap') DEFAULT 'soon',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        project_id INT,
        attachment VARCHAR(500),
        FOREIGN KEY (project_id) REFERENCES Project(project_id) ON DELETE SET NULL,
        INDEX idx_user (user_id, user_type),
        INDEX idx_status (status),
        INDEX idx_project (project_id)
    )";

    // SQL to create TicketMessage table
    $sql2 = "CREATE TABLE IF NOT EXISTS TicketMessage (
        message_id INT PRIMARY KEY AUTO_INCREMENT,
        ticket_id INT NOT NULL,
        sender_id INT NOT NULL,
        sender_type ENUM('user', 'repairer', 'company', 'admin', 'moderator') NOT NULL,
        message TEXT NOT NULL,
        attachment VARCHAR(500),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (ticket_id) REFERENCES SupportTicket(ticket_id) ON DELETE CASCADE,
        INDEX idx_ticket (ticket_id)
    )";

    echo "Creating SupportTicket table...\n";
    $pdo->exec($sql1);
    echo "SupportTicket table created or already exists.\n";

    echo "Creating TicketMessage table...\n";
    $pdo->exec($sql2);
    echo "TicketMessage table created or already exists.\n";
    
    echo "Database schema updated successfully!";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
