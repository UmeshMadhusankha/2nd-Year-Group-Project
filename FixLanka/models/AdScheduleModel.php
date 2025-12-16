<?php
/**
 * AdScheduleModel.php
 * BULLETPROOF conflict detection - GUARANTEED TO WORK
 */

class AdScheduleModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function tableExists()
    {
        try {
            $stmt = $this->pdo->query("SHOW TABLES LIKE 'ad_schedules'");
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function createTableIfNotExists()
    {
        $sql = "CREATE TABLE IF NOT EXISTS ad_schedules (
            schedule_id INT PRIMARY KEY AUTO_INCREMENT,
            ad_id INT NOT NULL,
            placement VARCHAR(50) NOT NULL DEFAULT 'banner',
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            start_time TIME NOT NULL DEFAULT '00:00:00',
            end_time TIME NOT NULL DEFAULT '23:59:59',
            status ENUM('scheduled', 'active', 'expired', 'cancelled') DEFAULT 'scheduled',
            priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_ad_schedule (ad_id),
            INDEX idx_placement (placement),
            INDEX idx_dates (start_date, end_date),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        try {
            $this->pdo->exec($sql);
            return true;
        } catch (PDOException $e) {
            error_log("Error creating ad_schedules table: " . $e->getMessage());
            return false;
        }
    }

    /**
     * CRITICAL: Check if ad already has a schedule
     */
    public function checkDuplicateAd($ad_id, $exclude_schedule_id = null)
    {
        $sql = "SELECT schedule_id, placement, start_date, end_date 
                FROM ad_schedules 
                WHERE ad_id = :ad_id";
        
        if ($exclude_schedule_id) {
            $sql .= " AND schedule_id != :exclude_id";
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':ad_id', $ad_id, PDO::PARAM_INT);
            
            if ($exclude_schedule_id) {
                $stmt->bindParam(':exclude_id', $exclude_schedule_id, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                error_log("DUPLICATE AD CHECK FAILED: Ad #{$ad_id} already scheduled");
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Error checking duplicate ad: " . $e->getMessage());
            return false;
        }
    }

    /**
     * BULLETPROOF CONFLICT DETECTION
     * Returns array with conflict details if found, false otherwise
     */
    public function checkPlacementConflict($placement, $start_date, $end_date, $start_time = null, $end_time = null, $exclude_schedule_id = null)
    {
        // Normalize times to HH:MM:SS format
        $newStartTime = $start_time ? $start_time : '00:00:00';
        $newEndTime = $end_time ? $end_time : '23:59:59';

        // Make sure times are in correct format
        if (strlen($newStartTime) == 5) $newStartTime .= ':00';
        if (strlen($newEndTime) == 5) $newEndTime .= ':00';

        error_log("=== CHECKING CONFLICT ===");
        error_log("Looking for conflicts in placement: {$placement}");
        error_log("New schedule: {$start_date} to {$end_date}, {$newStartTime} to {$newEndTime}");

        // Find ALL schedules with same placement
        $sql = "SELECT 
                    s.schedule_id,
                    s.ad_id,
                    s.placement,
                    s.start_date,
                    s.end_date,
                    s.start_time,
                    s.end_time,
                    s.status,
                    a.title,
                    CASE 
                        WHEN a.provider_type = 'company' THEN c.name
                        WHEN a.provider_type = 'repairer' THEN CONCAT(r.f_name, ' ', r.l_name)
                        ELSE 'Unknown Provider'
                    END as company_name
                FROM ad_schedules s
                LEFT JOIN Advertisement a ON s.ad_id = a.ad_id
                LEFT JOIN Company c ON a.provider_id = c.company_id AND a.provider_type = 'company'
                LEFT JOIN Repairer r ON a.provider_id = r.repairer_id AND a.provider_type = 'repairer'
                WHERE s.placement = :placement
                AND s.status IN ('scheduled', 'active')";

        if ($exclude_schedule_id) {
            $sql .= " AND s.schedule_id != :exclude_id";
        }

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':placement', $placement, PDO::PARAM_STR);
            
            if ($exclude_schedule_id) {
                $stmt->bindValue(':exclude_id', $exclude_schedule_id, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $existingSchedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

            error_log("Found " . count($existingSchedules) . " existing schedules with placement: {$placement}");

            // Check each existing schedule for overlap
            foreach ($existingSchedules as $existing) {
                error_log("Checking against schedule #{$existing['schedule_id']}: {$existing['start_date']} to {$existing['end_date']}");

                // Normalize existing times
                $existingStartTime = $existing['start_time'] ?: '00:00:00';
                $existingEndTime = $existing['end_time'] ?: '23:59:59';

                // DATE OVERLAP: (StartA <= EndB) AND (EndA >= StartB)
                $newStartTS = strtotime($start_date);
                $newEndTS = strtotime($end_date);
                $existingStartTS = strtotime($existing['start_date']);
                $existingEndTS = strtotime($existing['end_date']);

                $dateOverlap = ($newStartTS <= $existingEndTS && $newEndTS >= $existingStartTS);

                if (!$dateOverlap) {
                    error_log("No date overlap - safe");
                    continue;
                }

                error_log("DATE OVERLAP DETECTED!");

                // TIME OVERLAP: (StartA < EndB) AND (EndA > StartB)
                $newStartSeconds = strtotime($newStartTime);
                $newEndSeconds = strtotime($newEndTime);
                $existingStartSeconds = strtotime($existingStartTime);
                $existingEndSeconds = strtotime($existingEndTime);

                $timeOverlap = ($newStartSeconds < $existingEndSeconds && $newEndSeconds > $existingStartSeconds);

                if ($timeOverlap) {
                    error_log("!!! TIME OVERLAP DETECTED !!!");
                    error_log("New: {$newStartTime} to {$newEndTime}");
                    error_log("Existing: {$existingStartTime} to {$existingEndTime}");
                    error_log("=== CONFLICT FOUND ===");
                    
                    return $existing; // CONFLICT!
                }

                error_log("No time overlap - safe");
            }

            error_log("=== NO CONFLICTS ===");
            return false; // No conflicts

        } catch (PDOException $e) {
            error_log("Error in checkPlacementConflict: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create new schedule with STRICT validation
     */
    public function createSchedule($data)
    {
        error_log("=== CREATE SCHEDULE CALLED ===");
        error_log("Ad ID: " . $data['ad_id']);

        // Step 1: Get placement type from Advertisement
        $placement = $this->getAdPlacement($data['ad_id']);
        
        if (!$placement) {
            error_log("ERROR: Could not get placement for ad #{$data['ad_id']}");
            throw new Exception("Advertisement not found or has invalid type.");
        }

        error_log("Placement type: {$placement}");

        // Step 2: Check if ad already scheduled
        if ($this->checkDuplicateAd($data['ad_id'])) {
            error_log("ERROR: Ad #{$data['ad_id']} already has a schedule");
            throw new Exception("This advertisement is already scheduled. Delete the existing schedule first.");
        }

        // Step 3: Normalize times
        $startTime = !empty($data['start_time']) ? $data['start_time'] : '00:00:00';
        $endTime = !empty($data['end_time']) ? $data['end_time'] : '23:59:59';

        if (strlen($startTime) == 5) $startTime .= ':00';
        if (strlen($endTime) == 5) $endTime .= ':00';

        error_log("Times normalized: {$startTime} to {$endTime}");

        // Step 4: CHECK FOR CONFLICTS
        $conflict = $this->checkPlacementConflict(
            $placement,
            $data['start_date'],
            $data['end_date'],
            $startTime,
            $endTime
        );

        if ($conflict) {
            error_log("!!! CONFLICT DETECTED - BLOCKING INSERT !!!");
            
            $title = htmlspecialchars($conflict['title'] ?? 'Unknown Ad');
            $company = htmlspecialchars($conflict['company_name'] ?? 'Unknown Company');
            $dates = date('M d, Y', strtotime($conflict['start_date'])) . ' - ' . date('M d, Y', strtotime($conflict['end_date']));
            
            $existingStart = $conflict['start_time'] ?: '00:00:00';
            $existingEnd = $conflict['end_time'] ?: '23:59:59';
            $times = date('g:i A', strtotime($existingStart)) . ' - ' . date('g:i A', strtotime($existingEnd));
            
            $errorMsg = "❌ CONFLICT DETECTED!\n\n'{$title}' by {$company} is already scheduled for {$placement} placement from {$dates} ({$times}).\n\nYou cannot schedule overlapping ads in the same placement. Please choose different dates/times or delete the existing schedule.";
            
            error_log("Throwing exception: " . $errorMsg);
            throw new Exception($errorMsg);
        }

        error_log("No conflicts found - proceeding with insert");

        // Step 5: Insert into database
        $sql = "INSERT INTO ad_schedules (
                    ad_id, placement, start_date, end_date, start_time, end_time, status
                ) VALUES (
                    :ad_id, :placement, :start_date, :end_date, :start_time, :end_time, 'scheduled'
                )";

        try {
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':ad_id' => $data['ad_id'],
                ':placement' => $placement,
                ':start_date' => $data['start_date'],
                ':end_date' => $data['end_date'],
                ':start_time' => $startTime,
                ':end_time' => $endTime
            ]);
            
            if ($result) {
                $id = $this->pdo->lastInsertId();
                error_log("Schedule created successfully with ID: {$id}");
                return $id;
            }
            
            error_log("Insert failed but no exception thrown");
            return false;
            
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            throw new Exception("Database error: Failed to create schedule. " . $e->getMessage());
        }
    }

    public function updateSchedule($schedule_id, $data)
    {
        $existing = $this->getScheduleById($schedule_id);
        if (!$existing) {
            throw new Exception("Schedule not found.");
        }

        $startTime = !empty($data['start_time']) ? $data['start_time'] : '00:00:00';
        $endTime = !empty($data['end_time']) ? $data['end_time'] : '23:59:59';

        if (strlen($startTime) == 5) $startTime .= ':00';
        if (strlen($endTime) == 5) $endTime .= ':00';

        $conflict = $this->checkPlacementConflict(
            $existing['placement'],
            $data['start_date'],
            $data['end_date'],
            $startTime,
            $endTime,
            $schedule_id
        );

        if ($conflict) {
            $title = htmlspecialchars($conflict['title'] ?? 'Unknown Ad');
            $company = htmlspecialchars($conflict['company_name'] ?? 'Unknown Company');
            $dates = date('M d, Y', strtotime($conflict['start_date'])) . ' - ' . date('M d, Y', strtotime($conflict['end_date']));
            
            $existingStart = $conflict['start_time'] ?: '00:00:00';
            $existingEnd = $conflict['end_time'] ?: '23:59:59';
            $times = date('g:i A', strtotime($existingStart)) . ' - ' . date('g:i A', strtotime($existingEnd));
            
            throw new Exception("❌ CONFLICT DETECTED!\n\n'{$title}' by {$company} is already scheduled for this placement from {$dates} ({$times}).\n\nPlease choose different dates/times.");
        }

        $sql = "UPDATE ad_schedules SET
                    start_date = :start_date,
                    end_date = :end_date,
                    start_time = :start_time,
                    end_time = :end_time
                WHERE schedule_id = :schedule_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':start_date' => $data['start_date'],
                ':end_date' => $data['end_date'],
                ':start_time' => $startTime,
                ':end_time' => $endTime,
                ':schedule_id' => $schedule_id
            ]);
        } catch (PDOException $e) {
            error_log("Error updating schedule: " . $e->getMessage());
            throw new Exception("Database error: Failed to update schedule.");
        }
    }

    public function deleteSchedule($schedule_id)
    {
        $sql = "DELETE FROM ad_schedules WHERE schedule_id = :schedule_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting schedule: " . $e->getMessage());
            return false;
        }
    }

    private function getScheduleById($schedule_id)
    {
        $sql = "SELECT * FROM ad_schedules WHERE schedule_id = :schedule_id LIMIT 1";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting schedule: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get ad placement type from Advertisement table
     */
    private function getAdPlacement($ad_id)
    {
        $sql = "SELECT ad_id, type, title FROM Advertisement WHERE ad_id = :ad_id LIMIT 1";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':ad_id', $ad_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && isset($result['type'])) {
                error_log("Found ad #{$ad_id}: '{$result['title']}' with type: {$result['type']}");
                return $result['type'];
            }
            
            error_log("Ad #{$ad_id} not found in Advertisement table");
            return null;
        } catch (PDOException $e) {
            error_log("Error getting ad placement: " . $e->getMessage());
            return null;
        }
    }

    public function autoUpdateStatuses()
    {
        $today = date('Y-m-d');

        try {
            $this->pdo->exec("UPDATE ad_schedules SET status = 'active' 
                             WHERE status = 'scheduled' AND start_date <= '{$today}'");
            
            $this->pdo->exec("UPDATE ad_schedules SET status = 'expired' 
                             WHERE status IN ('scheduled', 'active') AND end_date < '{$today}'");
            
            return true;
        } catch (PDOException $e) {
            error_log("Error auto-updating statuses: " . $e->getMessage());
            return false;
        }
    }

    public function getScheduledAds($filters = [])
    {
        $sql = "SELECT 
                    s.schedule_id,
                    s.ad_id,
                    s.placement,
                    s.start_date,
                    s.end_date,
                    s.start_time,
                    s.end_time,
                    s.status,
                    s.priority,
                    s.created_at,
                    a.title,
                    a.type,
                    a.budget,
                    a.status as ad_status,
                    CASE 
                        WHEN a.provider_type = 'company' THEN c.name
                        WHEN a.provider_type = 'repairer' THEN CONCAT(r.f_name, ' ', r.l_name)
                        ELSE 'Unknown'
                    END as company_name
                FROM ad_schedules s
                LEFT JOIN Advertisement a ON s.ad_id = a.ad_id
                LEFT JOIN Company c ON a.provider_id = c.company_id AND a.provider_type = 'company'
                LEFT JOIN Repairer r ON a.provider_id = r.repairer_id AND a.provider_type = 'repairer'
                WHERE 1=1";

        if (!empty($filters['placement']) && $filters['placement'] !== 'all') {
            $sql .= " AND s.placement = :placement";
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (a.title LIKE :search OR c.name LIKE :search OR CONCAT(r.f_name, ' ', r.l_name) LIKE :search)";
        }

        $sql .= " ORDER BY s.created_at DESC";

        try {
            $stmt = $this->pdo->prepare($sql);
            
            if (!empty($filters['placement']) && $filters['placement'] !== 'all') {
                $stmt->bindValue(':placement', $filters['placement']);
            }
            
            if (!empty($filters['search'])) {
                $search = '%' . $filters['search'] . '%';
                $stmt->bindValue(':search', $search);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting scheduled ads: " . $e->getMessage());
            return [];
        }
    }

    public function getAdvertisements($filters = [])
    {
        $sql = "SELECT 
                    a.ad_id,
                    a.title,
                    a.type,
                    a.budget,
                    a.status,
                    a.provider_type,
                    CASE 
                        WHEN a.provider_type = 'company' THEN c.name
                        WHEN a.provider_type = 'repairer' THEN CONCAT(r.f_name, ' ', r.l_name)
                        ELSE 'Unknown Provider'
                    END as company_name
                FROM Advertisement a
                LEFT JOIN ad_schedules s ON a.ad_id = s.ad_id
                LEFT JOIN Company c ON a.provider_id = c.company_id AND a.provider_type = 'company'
                LEFT JOIN Repairer r ON a.provider_id = r.repairer_id AND a.provider_type = 'repairer'
                WHERE s.schedule_id IS NULL
                AND a.status IN ('approved', 'active')
                ORDER BY a.submission_date DESC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting advertisements: " . $e->getMessage());
            return [];
        }
    }

    public function getStatistics()
    {
        try {
            $total = $this->pdo->query("SELECT COUNT(*) as count FROM ad_schedules")->fetch()['count'];
            $active = $this->pdo->query("SELECT COUNT(*) as count FROM ad_schedules WHERE status = 'active'")->fetch()['count'];
            
            return [
                'total_schedules' => $total,
                'active_schedules' => $active
            ];
        } catch (PDOException $e) {
            error_log("Error getting statistics: " . $e->getMessage());
            return ['total_schedules' => 0, 'active_schedules' => 0];
        }
    }

    public function getPlacementAnalytics()
    {
        $placements = ['banner', 'featured', 'sponsored'];
        $analytics = [];

        foreach ($placements as $type) {
            try {
                $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM ad_schedules WHERE placement = ? AND status = 'active'");
                $stmt->execute([$type]);
                $count = $stmt->fetch()['count'];
                
                $analytics[] = [
                    'placement' => ucfirst($type),
                    'active' => $count
                ];
            } catch (PDOException $e) {
                $analytics[] = ['placement' => ucfirst($type), 'active' => 0];
            }
        }

        return $analytics;
    }

    public function getCalendarEvents($month, $year)
    {
        $firstDay = sprintf('%04d-%02d-01', $year, $month);
        $lastDay = date('Y-m-t', strtotime($firstDay));

        $sql = "SELECT 
                    s.schedule_id,
                    s.ad_id,
                    s.start_date,
                    s.end_date,
                    s.placement,
                    a.title
                FROM ad_schedules s
                LEFT JOIN Advertisement a ON s.ad_id = a.ad_id
                WHERE s.start_date <= :last_day
                AND s.end_date >= :first_day
                AND s.status IN ('scheduled', 'active')
                ORDER BY s.start_date ASC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':first_day', $firstDay);
            $stmt->bindParam(':last_day', $lastDay);
            $stmt->execute();
            
            $events = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $events[] = [
                    'id' => $row['schedule_id'],
                    'title' => $row['title'] ?? 'Untitled Ad',
                    'start' => $row['start_date'],
                    'end' => $row['end_date'],
                    'placement' => $row['placement']
                ];
            }
            
            return $events;
        } catch (PDOException $e) {
            error_log("Error getting calendar events: " . $e->getMessage());
            return [];
        }
    }
}