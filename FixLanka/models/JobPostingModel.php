<?php
/**
 * Job Posting Model
 * Handles all database operations for Company Job Postings
 */

class JobPostingModel {
    private $pdo;
    private $columnExistsCache = [];
    // IMPORTANT: The actual table in your DB is `companyjobpost` (no underscore).
    // Do not create a new table name; always use the existing one.
    private $table = 'companyjobpost';
    private ?bool $tableReady = null;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Ensures the base table exists (and auto-migrates legacy table name when possible).
     * This is safe to call from API endpoints before running custom SQL.
     */
    public function ensureTable(): void {
        $this->ensureBaseTable();
    }

    public function getTableName(): string {
        return $this->table;
    }

    private function tableExists(string $table): bool {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT 1 FROM INFORMATION_SCHEMA.TABLES '
                . 'WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table LIMIT 1'
            );
            $stmt->execute([':table' => $table]);
            return (bool)$stmt->fetchColumn();
        } catch (Throwable $e) {
            return false;
        }
    }

    private function ensureBaseTable(): void {
        if ($this->tableReady === true) {
            return;
        }

        if ($this->tableExists($this->table)) {
            $this->tableReady = true;
            return;
        }

        // If the underscore version exists (created by earlier schema), prefer renaming it
        // to the correct existing name the app should use.
        if ($this->tableExists('company_jobpost') && !$this->tableExists('companyjobpost')) {
            try {
                $this->pdo->exec('RENAME TABLE `company_jobpost` TO `companyjobpost`');
                $this->tableReady = true;
                $this->columnExistsCache = [];
                return;
            } catch (Throwable $e) {
                // If rename fails but the table exists, fall back to using it.
                $this->table = 'company_jobpost';
                $this->tableReady = true;
                $this->columnExistsCache = [];
                return;
            }
        }

        // Auto-migrate old DBs: rename legacy table -> `companyjobpost`.
        // Some DBs used `job_postings` while others used `job_posting`.
        $legacyTable = null;
        if ($this->tableExists('job_postings')) {
            $legacyTable = 'job_postings';
        } elseif ($this->tableExists('job_posting')) {
            $legacyTable = 'job_posting';
        }

        if ($legacyTable) {
            try {
                $this->pdo->exec('RENAME TABLE `' . $legacyTable . '` TO `' . $this->table . '`');
                $this->tableReady = true;
                $this->columnExistsCache = [];
                return;
            } catch (Throwable $e) {
                // Fall through.
            }
        }

        $this->tableReady = false;
        throw new Exception(
            "Required table '{$this->table}' is missing in your database. Create it (using your existing schema) "
            . "or rename legacy job_postings/job_posting to {$this->table}."
        );
    }

    private function columnExists(string $table, string $column): bool {
        $key = strtolower($table . '.' . $column);
        if (array_key_exists($key, $this->columnExistsCache)) {
            return (bool)$this->columnExistsCache[$key];
        }

        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS '
            . 'WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column'
        );
        $stmt->execute([':table' => $table, ':column' => $column]);
        $exists = ((int)$stmt->fetchColumn()) > 0;
        $this->columnExistsCache[$key] = $exists;
        return $exists;
    }

    private function firstExistingColumn(string $table, array $candidates): ?string {
        foreach ($candidates as $col) {
            if ($this->columnExists($table, (string)$col)) {
                return (string)$col;
            }
        }
        return null;
    }

    private function preferredSortColumn(): string {
        // Some DB variants don't have `created_at`. Fall back safely.
        $col = $this->firstExistingColumn($this->table, [
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

        return $col ?: 'posting_id';
    }

    private function ensureCreatedAtInRow(array $row): array {
        if (array_key_exists('created_at', $row) && $row['created_at'] !== null && $row['created_at'] !== '') {
            return $row;
        }

        $fallbackCol = $this->firstExistingColumn($this->table, [
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

        if ($fallbackCol && array_key_exists($fallbackCol, $row)) {
            $row['created_at'] = $row[$fallbackCol];
        }

        return $row;
    }

    private function getOptionalValue(array $data, array $keys, $default = null) {
        foreach ($keys as $k) {
            if (array_key_exists($k, $data)) {
                return $data[$k];
            }
        }
        return $default;
    }

    private function normalizeDateOrNull($value): ?string {
        if ($value === null) {
            return null;
        }
        $s = trim((string)$value);
        if ($s === '' || $s === '0000-00-00') {
            return null;
        }
        // Expecting YYYY-MM-DD from <input type="date">.
        return $s;
    }

    private function ensureOptionalColumnsForCreateOrUpdate(array $data): void {
        // Only attempt schema changes when the caller actually sends these fields.
        // If the DB user lacks ALTER privileges, we fail silently.

        $needsPriority = array_key_exists('priority_level', $data) || array_key_exists('priorityLevel', $data);
        $needsDeadline = array_key_exists('application_deadline', $data) || array_key_exists('applicationDeadline', $data);
        $needsSkills = array_key_exists('required_skills', $data) || array_key_exists('requiredSkills', $data);
        $needsLocationReq = array_key_exists('location_requirements', $data) || array_key_exists('locationRequirements', $data);

        $didAlter = false;

        try {
            if ($needsPriority && !$this->firstExistingColumn($this->table, ['priority_level', 'priorityLevel'])) {
                $this->pdo->exec("ALTER TABLE {$this->table} ADD COLUMN priority_level ENUM('low','medium','high','urgent') NOT NULL DEFAULT 'medium'");
                $didAlter = true;
            }

            if ($needsDeadline && !$this->firstExistingColumn($this->table, ['application_deadline', 'applicationDeadline'])) {
                $this->pdo->exec("ALTER TABLE {$this->table} ADD COLUMN application_deadline DATE NULL");
                $didAlter = true;
            }

            if ($needsSkills && !$this->firstExistingColumn($this->table, ['required_skills', 'requiredSkills'])) {
                $this->pdo->exec("ALTER TABLE {$this->table} ADD COLUMN required_skills TEXT NULL");
                $didAlter = true;
            }

            if ($needsLocationReq && !$this->firstExistingColumn($this->table, ['location_requirements', 'locationRequirements'])) {
                $this->pdo->exec("ALTER TABLE {$this->table} ADD COLUMN location_requirements TEXT NULL");
                $didAlter = true;
            }
        } catch (Throwable $e) {
            // Ignore: dev/hosting DB user may not have privileges.
        }

        if ($didAlter) {
            // Schema changed; clear cached column results.
            $this->columnExistsCache = [];
        }
    }
    
    /**
     * Create a new job posting
     */
    public function create($data) {
        try {
            $this->ensureBaseTable();
            $this->ensureOptionalColumnsForCreateOrUpdate(is_array($data) ? $data : []);

            $columns = [
                'company_id', 'title', 'category', 'employment_type',
                'description', 'min_experience', 'min_budget', 'max_budget',
                'location', 'status'
            ];

            $params = [
                ':company_id' => $data['company_id'],
                ':title' => $data['title'],
                ':category' => $data['category'],
                ':employment_type' => $data['employment_type'],
                ':description' => $data['description'],
                ':min_experience' => $data['min_experience'],
                ':min_budget' => $data['min_budget'],
                ':max_budget' => $data['max_budget'],
                ':location' => $data['location'],
                ':status' => $data['status'] ?? 'draft',
            ];

            // Optional fields (schema-drift safe; supports camelCase columns too)
            $priorityCol = $this->firstExistingColumn($this->table, ['priority_level', 'priorityLevel']);
            if ($priorityCol) {
                $columns[] = $priorityCol;
                $params[':' . $priorityCol] = $this->getOptionalValue($data, ['priority_level', 'priorityLevel'], 'medium') ?? 'medium';
            }

            $deadlineCol = $this->firstExistingColumn($this->table, ['application_deadline', 'applicationDeadline']);
            if ($deadlineCol) {
                $columns[] = $deadlineCol;
                $rawDeadline = $this->getOptionalValue($data, ['application_deadline', 'applicationDeadline'], null);
                $params[':' . $deadlineCol] = $this->normalizeDateOrNull($rawDeadline);
            }

            $skillsCol = $this->firstExistingColumn($this->table, ['required_skills', 'requiredSkills']);
            if ($skillsCol) {
                $columns[] = $skillsCol;
                $params[':' . $skillsCol] = $this->getOptionalValue($data, ['required_skills', 'requiredSkills'], null);
            }

            $locationReqCol = $this->firstExistingColumn($this->table, ['location_requirements', 'locationRequirements']);
            if ($locationReqCol) {
                $columns[] = $locationReqCol;
                $params[':' . $locationReqCol] = $this->getOptionalValue($data, ['location_requirements', 'locationRequirements'], null);
            }

            $placeholders = array_map(fn($c) => ':' . $c, $columns);
            $sql = 'INSERT INTO ' . $this->table . ' (' . implode(', ', $columns) . ') '
                . 'VALUES (' . implode(', ', $placeholders) . ')';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("JobPostingModel::create error: " . $e->getMessage());
            throw new Exception("Failed to create job posting");
        }
    }
    
    /**
     * Get all job postings for a company
     */
    public function getByCompany($companyId, $status = null) {
        try {
            $this->ensureBaseTable();
            $sql = "SELECT * FROM {$this->table} WHERE company_id = :company_id";
            $params = [':company_id' => $companyId];
            
            if ($status) {
                $sql .= " AND status = :status";
                $params[':status'] = $status;
            }
            
            $orderCol = $this->preferredSortColumn();
            $sql .= " ORDER BY `{$orderCol}` DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as &$r) {
                if (is_array($r)) {
                    $r = $this->ensureCreatedAtInRow($r);
                }
            }
            return $rows;
        } catch (PDOException $e) {
            error_log("JobPostingModel::getByCompany error: " . $e->getMessage());
            throw new Exception("Failed to fetch job postings");
        }
    }
    
    /**
     * Get a single job posting by ID
     */
    public function getById($postingId) {
        try {
            $this->ensureBaseTable();
            $sql = "SELECT * FROM {$this->table} WHERE posting_id = :posting_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':posting_id' => $postingId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (is_array($row)) {
                $row = $this->ensureCreatedAtInRow($row);
            }
            return $row;
        } catch (PDOException $e) {
            error_log("JobPostingModel::getById error: " . $e->getMessage());
            throw new Exception("Failed to fetch job posting");
        }
    }
    
    /**
     * Update a job posting
     */
    public function update($postingId, $data) {
        try {
            $this->ensureBaseTable();
            $this->ensureOptionalColumnsForCreateOrUpdate(is_array($data) ? $data : []);

            $fields = [
                '`title` = :title',
                '`category` = :category',
                '`employment_type` = :employment_type',
                '`description` = :description',
                '`min_experience` = :min_experience',
                '`min_budget` = :min_budget',
                '`max_budget` = :max_budget',
                '`location` = :location'
            ];

            $params = [
                ':posting_id' => $postingId,
                ':title' => $data['title'],
                ':category' => $data['category'],
                ':employment_type' => $data['employment_type'],
                ':description' => $data['description'],
                ':min_experience' => $data['min_experience'],
                ':min_budget' => $data['min_budget'],
                ':max_budget' => $data['max_budget'],
                ':location' => $data['location'],
            ];

            $priorityCol = $this->firstExistingColumn($this->table, ['priority_level', 'priorityLevel']);
            if ($priorityCol) {
                $fields[] = '`' . $priorityCol . '` = :' . $priorityCol;
                $params[':' . $priorityCol] = $this->getOptionalValue($data, ['priority_level', 'priorityLevel'], 'medium') ?? 'medium';
            }

            $deadlineCol = $this->firstExistingColumn($this->table, ['application_deadline', 'applicationDeadline']);
            if ($deadlineCol) {
                $fields[] = '`' . $deadlineCol . '` = :' . $deadlineCol;
                $rawDeadline = $this->getOptionalValue($data, ['application_deadline', 'applicationDeadline'], null);
                $params[':' . $deadlineCol] = $this->normalizeDateOrNull($rawDeadline);
            }

            $skillsCol = $this->firstExistingColumn($this->table, ['required_skills', 'requiredSkills']);
            if ($skillsCol) {
                $fields[] = '`' . $skillsCol . '` = :' . $skillsCol;
                $params[':' . $skillsCol] = $this->getOptionalValue($data, ['required_skills', 'requiredSkills'], null);
            }

            $locationReqCol = $this->firstExistingColumn($this->table, ['location_requirements', 'locationRequirements']);
            if ($locationReqCol) {
                $fields[] = '`' . $locationReqCol . '` = :' . $locationReqCol;
                $params[':' . $locationReqCol] = $this->getOptionalValue($data, ['location_requirements', 'locationRequirements'], null);
            }

            $sql = 'UPDATE ' . $this->table . ' SET ' . implode(",\n                ", $fields) . ' WHERE posting_id = :posting_id';

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("JobPostingModel::update error: " . $e->getMessage());
            throw new Exception("Failed to update job posting");
        }
    }
    
    /**
     * Update job posting status
     */
    public function updateStatus($postingId, $status) {
        try {
            $this->ensureBaseTable();
            $sql = "UPDATE {$this->table} SET status = :status";
            
            // If closing the job, you could potentially set closed values if they existed in schema
            // if ($status === 'closed' || $status === 'filled') {
            //     $sql .= ", closed_date = NOW()";
            // }
            
            $sql .= " WHERE posting_id = :posting_id";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':posting_id' => $postingId,
                ':status' => $status
            ]);
        } catch (PDOException $e) {
            error_log("JobPostingModel::updateStatus error: " . $e->getMessage());
            throw new Exception("Failed to update job status");
        }
    }
    
    /**
     * Delete a job posting
     */
    public function delete($postingId) {
        try {
            $this->ensureBaseTable();
            $sql = "DELETE FROM {$this->table} WHERE posting_id = :posting_id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':posting_id' => $postingId]);
        } catch (PDOException $e) {
            error_log("JobPostingModel::delete error: " . $e->getMessage());
            throw new Exception("Failed to delete job posting");
        }
    }
    
    /**
     * Get job posting statistics for a company
     */
    public function getStatistics($companyId) {
        try {
            $this->ensureBaseTable();
            $sql = "SELECT 
                COUNT(*) as total_postings,
                SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_postings,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft_postings,
                SUM(CASE WHEN status = 'filled' THEN 1 ELSE 0 END) as filled_postings,
                SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed_postings
                FROM {$this->table} 
                WHERE company_id = :company_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':company_id' => $companyId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("JobPostingModel::getStatistics error: " . $e->getMessage());
            throw new Exception("Failed to fetch statistics");
        }
    }
    
    /**
     * Get application count for a job posting
     */
    public function getApplicationCount($postingId) {
        try {
            $sql = "SELECT COUNT(*) as count FROM repairer_applications WHERE job_posting_id = :posting_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':posting_id' => $postingId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            error_log("JobPostingModel::getApplicationCount error: " . $e->getMessage());
            return 0;
        }
    }
}
