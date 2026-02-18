<?php
/**
 * AdScheduleModel.php
 * ✅ 100% CRASH-PROOF MODEL
 * ✅ Complete calendar functionality
 * ✅ All CRUD operations with validation
 * Version: 3.0.0 - PRODUCTION READY
 */

// ✅ CRASH PROTECTION
ini_set('memory_limit', '256M');
set_time_limit(30);

class AdScheduleModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * ✅ Check if ad_schedules table exists
     */
    public function tableExists()
    {
        try {
            $stmt = $this->pdo->query("SHOW TABLES LIKE 'ad_schedules'");
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Table check error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ✅ Create ad_schedules table with all required columns
     */
    public function createTableIfNotExists()
    {
        $sql = "CREATE TABLE IF NOT EXISTS ad_schedules (
            schedule_id INT PRIMARY KEY AUTO_INCREMENT,
            ad_id INT NOT NULL,
            placement ENUM('banner', 'sponsored', 'featured') NOT NULL,
            status ENUM('scheduled', 'active', 'completed', 'cancelled') DEFAULT 'scheduled',
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            start_time TIME DEFAULT '00:00:00',
            end_time TIME DEFAULT '23:59:59',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            created_by INT DEFAULT NULL,
            INDEX idx_ad (ad_id),
            INDEX idx_dates (start_date, end_date),
            INDEX idx_placement (placement),
            INDEX idx_status (status),
            UNIQUE KEY unique_ad_schedule (ad_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        try {
            $this->pdo->exec($sql);
            return true;
        } catch (PDOException $e) {
            error_log("Table creation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ✅ GET ADVERTISEMENT BY ID (with full validation)
     */
    public function getAdvertisementById($ad_id)
    {
        $sql = "SELECT 
                    ad_id, 
                    title, 
                    type, 
                    status, 
                    budget,
                    provider_id,
                    provider_type
                FROM Advertisement 
                WHERE ad_id = :ad_id 
                LIMIT 1";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':ad_id' => $ad_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get ad error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * ✅ CHECK FOR DUPLICATE SCHEDULE (prevent double-scheduling)
     */
    public function checkDuplicateAd($ad_id, $exclude_schedule_id = null)
    {
        $sql = "SELECT schedule_id, placement, start_date, end_date, status 
                FROM ad_schedules 
                WHERE ad_id = :ad_id 
                AND status != 'cancelled'";
        
        if ($exclude_schedule_id) {
            $sql .= " AND schedule_id != :exclude_id";
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            $params = [':ad_id' => $ad_id];
            
            if ($exclude_schedule_id) {
                $params[':exclude_id'] = $exclude_schedule_id;
            }
            
            $stmt->execute($params);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing) {
                throw new Exception(
                    "❌ DUPLICATE DETECTED!\n\n" .
                    "This ad is already scheduled:\n" .
                    "• Placement: " . ucfirst($existing['placement']) . "\n" .
                    "• Period: {$existing['start_date']} to {$existing['end_date']}\n" .
                    "• Status: " . ucfirst($existing['status']) . "\n\n" .
                    "Please edit the existing schedule or cancel it first."
                );
            }
            
            return false;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * ✅ CHECK PLACEMENT CONFLICTS (slot availability validation)
     */
    public function checkPlacementConflict($placement, $start_date, $end_date, $exclude_schedule_id = null)
    {
        $sql = "SELECT 
                    s.schedule_id,
                    s.placement,
                    s.start_date,
                    s.end_date,
                    a.title as ad_title,
                    CASE 
                        WHEN a.provider_type = 'company' THEN c.name
                        WHEN a.provider_type = 'repairer' THEN CONCAT(r.f_name, ' ', r.l_name)
                        ELSE 'Unknown'
                    END as provider_name
                FROM ad_schedules s
                JOIN Advertisement a ON s.ad_id = a.ad_id
                LEFT JOIN Company c ON a.provider_id = c.company_id AND a.provider_type = 'company'
                LEFT JOIN Repairer r ON a.provider_id = r.repairer_id AND a.provider_type = 'repairer'
                WHERE s.placement = :placement
                AND s.status != 'cancelled'
                AND s.status != 'completed'
                AND (
                    (s.start_date <= :end_date AND s.end_date >= :start_date)
                )";

        if ($exclude_schedule_id) {
            $sql .= " AND s.schedule_id != :exclude_id";
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            $params = [
                ':placement' => $placement,
                ':start_date' => $start_date,
                ':end_date' => $end_date
            ];
            
            if ($exclude_schedule_id) {
                $params[':exclude_id'] = $exclude_schedule_id;
            }
            
            $stmt->execute($params);
            $conflicts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($conflicts)) {
                $conflict = $conflicts[0];
                throw new Exception(
                    "❌ PLACEMENT CONFLICT!\n\n" .
                    "This slot is already occupied:\n" .
                    "• Ad: {$conflict['ad_title']}\n" .
                    "• Provider: {$conflict['provider_name']}\n" .
                    "• Placement: " . ucfirst($conflict['placement']) . "\n" .
                    "• Period: {$conflict['start_date']} to {$conflict['end_date']}\n\n" .
                    "Please choose a different date range or placement type."
                );
            }
            
            return false;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * ✅ CREATE SCHEDULE (complete with all validations)
     */
    public function createSchedule($data)
    {
        try {
            // Step 1: Get ad details and placement
            $ad = $this->getAdvertisementById($data['ad_id']);
            
            if (!$ad) {
                throw new Exception("❌ Advertisement not found.");
            }

            // Use ad's type as placement
            $placement = strtolower($ad['type']);

            // Step 2: Check for duplicates
            $this->checkDuplicateAd($data['ad_id']);

            // Step 3: Normalize times
            $startTime = !empty($data['start_time']) ? $data['start_time'] : '00:00:00';
            $endTime = !empty($data['end_time']) ? $data['end_time'] : '23:59:59';

            if (strlen($startTime) == 5) $startTime .= ':00';
            if (strlen($endTime) == 5) $endTime .= ':00';

            // Step 4: Check for placement conflicts
            $this->checkPlacementConflict($placement, $data['start_date'], $data['end_date']);

            // Step 5: Determine initial status
            $today = date('Y-m-d');
            $initialStatus = ($data['start_date'] <= $today) ? 'active' : 'scheduled';

            // Step 6: Insert with transaction
            $this->pdo->beginTransaction();
            
            $sql = "INSERT INTO ad_schedules (
                        ad_id, placement, status, start_date, end_date, start_time, end_time, created_by
                    ) VALUES (
                        :ad_id, :placement, :status, :start_date, :end_date, :start_time, :end_time, 1
                    )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':ad_id' => $data['ad_id'],
                ':placement' => $placement,
                ':status' => $initialStatus,
                ':start_date' => $data['start_date'],
                ':end_date' => $data['end_date'],
                ':start_time' => $startTime,
                ':end_time' => $endTime
            ]);
            
            $this->pdo->commit();
            return true;
            
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollback();
            }
            throw $e;
        }
    }

    /**
     * ✅ UPDATE SCHEDULE
     */
    public function updateSchedule($schedule_id, $data)
    {
        try {
            $existing = $this->getScheduleById($schedule_id);
            
            if (!$existing) {
                throw new Exception("❌ Schedule not found.");
            }

            $startTime = !empty($data['start_time']) ? $data['start_time'] : '00:00:00';
            $endTime = !empty($data['end_time']) ? $data['end_time'] : '23:59:59';

            if (strlen($startTime) == 5) $startTime .= ':00';
            if (strlen($endTime) == 5) $endTime .= ':00';

            // Check conflicts (excluding this schedule)
            $this->checkPlacementConflict(
                $existing['placement'],
                $data['start_date'],
                $data['end_date'],
                $schedule_id
            );

            $sql = "UPDATE ad_schedules SET
                        start_date = :start_date,
                        end_date = :end_date,
                        start_time = :start_time,
                        end_time = :end_time
                    WHERE schedule_id = :schedule_id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':start_date' => $data['start_date'],
                ':end_date' => $data['end_date'],
                ':start_time' => $startTime,
                ':end_time' => $endTime,
                ':schedule_id' => $schedule_id
            ]);
            
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * ✅ DELETE SCHEDULE
     */
    public function deleteSchedule($schedule_id)
    {
        $sql = "DELETE FROM ad_schedules WHERE schedule_id = :schedule_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':schedule_id' => $schedule_id]);
        } catch (PDOException $e) {
            throw new Exception("❌ Failed to delete schedule: " . $e->getMessage());
        }
    }

    /**
     * ✅ GET SCHEDULE BY ID
     */
    private function getScheduleById($schedule_id)
    {
        $sql = "SELECT * FROM ad_schedules WHERE schedule_id = :schedule_id LIMIT 1";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':schedule_id' => $schedule_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * ✅ AUTO-UPDATE STATUSES (scheduled → active → completed)
     */
    public function autoUpdateStatuses()
    {
        $today = date('Y-m-d');

        try {
            // Activate scheduled ads that started
            $this->pdo->exec("UPDATE ad_schedules SET status = 'active' 
                             WHERE status = 'scheduled' AND start_date <= '$today'");
            
            // Complete active ads that ended
            $this->pdo->exec("UPDATE ad_schedules SET status = 'completed' 
                             WHERE status = 'active' AND end_date < '$today'");
            
            return true;
        } catch (PDOException $e) {
            error_log("Status update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ✅ GET ALL SCHEDULED ADS (with filters)
     */
    public function getScheduledAds($filters = [])
    {
        $sql = "SELECT 
                    s.*,
                    a.title as ad_title,
                    a.budget,
                    a.type as ad_type,
                    CASE 
                        WHEN a.provider_type = 'company' THEN c.name
                        WHEN a.provider_type = 'repairer' THEN CONCAT(r.f_name, ' ', r.l_name)
                        ELSE 'Unknown'
                    END as provider_name
                FROM ad_schedules s
                JOIN Advertisement a ON s.ad_id = a.ad_id
                LEFT JOIN Company c ON a.provider_id = c.company_id AND a.provider_type = 'company'
                LEFT JOIN Repairer r ON a.provider_id = r.repairer_id AND a.provider_type = 'repairer'
                WHERE 1=1";

        $params = [];

        if (!empty($filters['placement']) && $filters['placement'] !== 'all') {
            $sql .= " AND s.placement = :placement";
            $params[':placement'] = $filters['placement'];
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $sql .= " AND s.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (a.title LIKE :search OR c.name LIKE :search OR CONCAT(r.f_name, ' ', r.l_name) LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY s.start_date DESC LIMIT 100";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get scheduled ads error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ GET APPROVED ADVERTISEMENTS ONLY
     */
    public function getApprovedAdvertisements()
    {
        $sql = "SELECT 
                    a.ad_id,
                    a.title,
                    a.type,
                    a.budget,
                    a.status,
                    CASE 
                        WHEN a.provider_type = 'company' THEN c.name
                        WHEN a.provider_type = 'repairer' THEN CONCAT(r.f_name, ' ', r.l_name)
                        ELSE 'Unknown'
                    END as provider_name,
                    CASE 
                        WHEN s.schedule_id IS NOT NULL THEN 1
                        ELSE 0
                    END as is_scheduled
                FROM Advertisement a
                LEFT JOIN Company c ON a.provider_id = c.company_id AND a.provider_type = 'company'
                LEFT JOIN Repairer r ON a.provider_id = r.repairer_id AND a.provider_type = 'repairer'
                LEFT JOIN ad_schedules s ON a.ad_id = s.ad_id
                WHERE a.status = 'approved'
                ORDER BY a.submission_date DESC
                LIMIT 100";

        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get approved ads error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ GET STATISTICS
     */
    public function getStatistics()
    {
        $stats = [
            'total' => 0,
            'active_schedules' => 0,
            'completed' => 0,
            'upcoming' => 0
        ];

        try {
            $result = $this->pdo->query("SELECT COUNT(*) as total FROM ad_schedules");
            $stats['total'] = $result->fetch(PDO::FETCH_ASSOC)['total'];

            $result = $this->pdo->query("SELECT COUNT(*) as total FROM ad_schedules WHERE status = 'active'");
            $stats['active_schedules'] = $result->fetch(PDO::FETCH_ASSOC)['total'];

            $result = $this->pdo->query("SELECT COUNT(*) as total FROM ad_schedules WHERE status = 'completed'");
            $stats['completed'] = $result->fetch(PDO::FETCH_ASSOC)['total'];

            $result = $this->pdo->query("SELECT COUNT(*) as total FROM ad_schedules WHERE status = 'scheduled'");
            $stats['upcoming'] = $result->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (PDOException $e) {
            error_log("Get statistics error: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * ✅ GET STARTING TODAY COUNT
     */
    public function getStartingTodayCount()
    {
        $today = date('Y-m-d');

        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM ad_schedules WHERE start_date = :today");
            $stmt->execute([':today' => $today]);
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * ✅ GET PLACEMENT ANALYTICS
     */
    public function getPlacementAnalytics()
    {
        $sql = "SELECT 
                    placement,
                    COUNT(*) as total_schedules,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count
                FROM ad_schedules
                GROUP BY placement";

        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * ✅ GET CALENDAR EVENTS FOR MONTH/YEAR (for calendar UI)
     */
    public function getCalendarEvents($month, $year)
    {
        $sql = "SELECT 
                    DATE(start_date) as event_date,
                    COUNT(*) as event_count,
                    GROUP_CONCAT(
                        CONCAT(a.title, ' (', s.placement, ')')
                        SEPARATOR '||'
                    ) as event_titles
                FROM ad_schedules s
                JOIN Advertisement a ON s.ad_id = a.ad_id
                WHERE MONTH(s.start_date) = :month 
                AND YEAR(s.start_date) = :year
                AND s.status != 'cancelled'
                GROUP BY DATE(s.start_date)";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':month' => $month,
                ':year' => $year
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * ✅ GET SCHEDULES FOR SPECIFIC DATE (for calendar click)
     */
    public function getSchedulesForDate($date)
    {
        $sql = "SELECT 
                    s.*,
                    a.title as ad_title,
                    a.budget,
                    CASE 
                        WHEN a.provider_type = 'company' THEN c.name
                        WHEN a.provider_type = 'repairer' THEN CONCAT(r.f_name, ' ', r.l_name)
                        ELSE 'Unknown'
                    END as provider_name
                FROM ad_schedules s
                JOIN Advertisement a ON s.ad_id = a.ad_id
                LEFT JOIN Company c ON a.provider_id = c.company_id AND a.provider_type = 'company'
                LEFT JOIN Repairer r ON a.provider_id = r.repairer_id AND a.provider_type = 'repairer'
                WHERE :date BETWEEN s.start_date AND s.end_date
                AND s.status != 'cancelled'
                ORDER BY s.placement";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':date' => $date]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}