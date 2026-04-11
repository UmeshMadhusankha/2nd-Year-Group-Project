<?php
/**
 * Debug helper: Company job post schema + latest row snapshot.
 *
 * Usage:
 *   GET /2nd-Year-Group-Project/FixLanka/api/_dbcheck_job_postings.php
 *   GET /2nd-Year-Group-Project/FixLanka/api/_dbcheck_job_postings.php?posting_id=123
 */

header('Content-Type: application/json');

require_once '../config/database.php';

global $pdo;

function columnExists(PDO $pdo, string $table, string $column): bool {
    try {
        $stmt = $pdo->prepare('SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t AND COLUMN_NAME = :c LIMIT 1');
        $stmt->execute([':t' => $table, ':c' => $column]);
        return (bool)$stmt->fetchColumn();
    } catch (Throwable $e) {
        return false;
    }
}

function listColumns(PDO $pdo, string $table): array {
    try {
        $stmt = $pdo->prepare(
            'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS '
            . 'WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t '
            . 'ORDER BY ORDINAL_POSITION'
        );
        $stmt->execute([':t' => $table]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    } catch (Throwable $e) {
        return [];
    }
}

function firstExistingColumn(PDO $pdo, string $table, array $candidates): ?string {
    foreach ($candidates as $c) {
        if (columnExists($pdo, $table, (string)$c)) {
            return (string)$c;
        }
    }
    return null;
}

try {
    $postingId = isset($_GET['posting_id']) ? (int)$_GET['posting_id'] : 0;

    $table = 'companyjobpost';
    $columns = listColumns($pdo, $table);

    $hasDeadlineSnake = columnExists($pdo, $table, 'application_deadline');
    $hasDeadlineCamel = columnExists($pdo, $table, 'applicationDeadline');
    $hasPrioritySnake = columnExists($pdo, $table, 'priority_level');
    $hasPriorityCamel = columnExists($pdo, $table, 'priorityLevel');

    $deadlineCol = $hasDeadlineSnake ? 'application_deadline' : ($hasDeadlineCamel ? 'applicationDeadline' : null);
    $priorityCol = $hasPrioritySnake ? 'priority_level' : ($hasPriorityCamel ? 'priorityLevel' : null);

    $cols = ['posting_id', 'company_id', 'title', 'status'];
    $dateCol = firstExistingColumn($pdo, $table, [
        'created_at',
        'createdAt',
        'posted_date',
        'postedDate',
        'posting_date',
        'date_created',
        'dateCreated',
        'created_on',
        'createdOn',
        'updated_at',
        'updatedAt',
    ]);
    if ($dateCol) $cols[] = $dateCol;
    if ($deadlineCol) $cols[] = $deadlineCol;
    if ($priorityCol) $cols[] = $priorityCol;

    $sql = 'SELECT ' . implode(', ', array_map(fn($c) => '`' . $c . '`', $cols)) . ' FROM ' . $table;
    $params = [];
    if ($postingId > 0) {
        $sql .= ' WHERE posting_id = :pid';
        $params[':pid'] = $postingId;
    }
    $sql .= ' ORDER BY posting_id DESC LIMIT 1';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

    echo json_encode([
        'success' => true,
        'schema' => [
            'table' => $table,
            'columns' => $columns,
            'companyjobpost.application_deadline' => $hasDeadlineSnake,
            'companyjobpost.applicationDeadline' => $hasDeadlineCamel,
            'companyjobpost.priority_level' => $hasPrioritySnake,
            'companyjobpost.priorityLevel' => $hasPriorityCamel,
        ],
        'selected_columns' => $cols,
        'row' => $row,
    ], JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
    ]);
}
