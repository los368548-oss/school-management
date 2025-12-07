<?php
/**
 * Attendance Model
 */

class Attendance extends BaseModel {
    protected $table = 'attendance';
    protected $fillable = ['student_id', 'class_id', 'date', 'status', 'marked_by'];

    /**
     * Mark attendance for multiple students
     */
    public function markBulkAttendance($attendanceData, $markedBy) {
        $this->beginTransaction();

        try {
            foreach ($attendanceData as $data) {
                // Check if attendance already exists for this student/date
                $existing = $this->db->query("
                    SELECT id FROM {$this->table}
                    WHERE student_id = ? AND date = ?
                ")->bind(1, $data['student_id'])->bind(2, $data['date'])->single();

                if ($existing) {
                    // Update existing record
                    $this->update($existing['id'], [
                        'status' => $data['status'],
                        'marked_by' => $markedBy
                    ]);
                } else {
                    // Create new record
                    $this->create([
                        'student_id' => $data['student_id'],
                        'class_id' => $data['class_id'],
                        'date' => $data['date'],
                        'status' => $data['status'],
                        'marked_by' => $markedBy
                    ]);
                }
            }

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            return false;
        }
    }

    /**
     * Get attendance for a specific class and date
     */
    public function getClassAttendance($classId, $date) {
        $results = $this->db->query("
            SELECT a.*, s.first_name, s.last_name, s.scholar_number,
                   u.username as marked_by_name
            FROM {$this->table} a
            JOIN students s ON a.student_id = s.id
            LEFT JOIN users u ON a.marked_by = u.id
            WHERE a.class_id = ? AND a.date = ? AND s.is_active = 1
            ORDER BY s.first_name ASC, s.last_name ASC
        ")->bind(1, $classId)->bind(2, $date)->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Get student attendance calendar
     */
    public function getStudentAttendanceCalendar($studentId, $month, $year) {
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));

        $results = $this->db->query("
            SELECT date, status
            FROM {$this->table}
            WHERE student_id = ? AND date BETWEEN ? AND ?
            ORDER BY date ASC
        ")->bind(1, $studentId)->bind(2, $startDate)->bind(3, $endDate)->resultSet();

        // Create calendar array
        $calendar = [];
        $currentDate = strtotime($startDate);
        $endDateTime = strtotime($endDate);

        while ($currentDate <= $endDateTime) {
            $dateStr = date('Y-m-d', $currentDate);
            $dayOfWeek = date('w', $currentDate);

            // Skip weekends if needed (optional)
            // if ($dayOfWeek == 0 || $dayOfWeek == 6) {
            //     $currentDate = strtotime('+1 day', $currentDate);
            //     continue;
            // }

            $attendance = array_filter($results, function($att) use ($dateStr) {
                return $att['date'] === $dateStr;
            });

            $calendar[] = [
                'date' => $dateStr,
                'day' => date('d', $currentDate),
                'day_name' => date('D', $currentDate),
                'status' => !empty($attendance) ? array_values($attendance)[0]['status'] : null
            ];

            $currentDate = strtotime('+1 day', $currentDate);
        }

        return $calendar;
    }

    /**
     * Get attendance report for class
     */
    public function getClassAttendanceReport($classId, $startDate, $endDate) {
        $results = $this->db->query("
            SELECT
                s.id,
                s.scholar_number,
                s.first_name,
                s.last_name,
                COUNT(a.id) as total_days,
                SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN a.status = 'Late' THEN 1 ELSE 0 END) as late_days,
                ROUND(
                    (SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / COUNT(a.id)) * 100,
                    2
                ) as attendance_percentage
            FROM students s
            LEFT JOIN {$this->table} a ON s.id = a.student_id AND a.date BETWEEN ? AND ?
            WHERE s.class_id = ? AND s.is_active = 1
            GROUP BY s.id, s.scholar_number, s.first_name, s.last_name
            ORDER BY s.first_name ASC, s.last_name ASC
        ")->bind(1, $startDate)->bind(2, $endDate)->bind(3, $classId)->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Get attendance statistics
     */
    public function getAttendanceStats($classId = null, $month = null, $year = null) {
        $month = $month ?? date('m');
        $year = $year ?? date('Y');

        $whereClause = "MONTH(date) = ? AND YEAR(date) = ?";
        $params = [$month, $year];

        if ($classId) {
            $whereClause .= " AND class_id = ?";
            $params[] = $classId;
        }

        $result = $this->db->query("
            SELECT
                COUNT(*) as total_records,
                SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as total_present,
                SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as total_absent,
                SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) as total_late,
                COUNT(DISTINCT student_id) as unique_students,
                COUNT(DISTINCT date) as total_days
            FROM {$this->table}
            WHERE {$whereClause}
        ");

        foreach ($params as $index => $param) {
            $result->bind($index + 1, $param);
        }

        $stats = $result->single();

        if ($stats) {
            $stats['average_attendance'] = $stats['total_records'] > 0
                ? round(($stats['total_present'] / $stats['total_records']) * 100, 2)
                : 0;
        }

        return $stats ?: [
            'total_records' => 0,
            'total_present' => 0,
            'total_absent' => 0,
            'total_late' => 0,
            'unique_students' => 0,
            'total_days' => 0,
            'average_attendance' => 0
        ];
    }

    /**
     * Import attendance from CSV
     */
    public function importFromCSV($csvData, $markedBy) {
        $imported = 0;
        $errors = [];

        $this->beginTransaction();

        try {
            foreach ($csvData as $row) {
                // Validate required fields
                if (empty($row['student_id']) || empty($row['date']) || empty($row['status'])) {
                    $errors[] = 'Missing required fields in row';
                    continue;
                }

                // Check if student exists
                $student = $this->db->query("SELECT id, class_id FROM students WHERE id = ? AND is_active = 1")
                                   ->bind(1, $row['student_id'])->single();

                if (!$student) {
                    $errors[] = "Student ID {$row['student_id']} not found";
                    continue;
                }

                // Check if attendance already exists
                $existing = $this->db->query("
                    SELECT id FROM {$this->table}
                    WHERE student_id = ? AND date = ?
                ")->bind(1, $row['student_id'])->bind(2, $row['date'])->single();

                $attendanceData = [
                    'student_id' => $row['student_id'],
                    'class_id' => $student['class_id'],
                    'date' => $row['date'],
                    'status' => $row['status'],
                    'marked_by' => $markedBy
                ];

                if ($existing) {
                    $this->update($existing['id'], $attendanceData);
                } else {
                    $this->create($attendanceData);
                }

                $imported++;
            }

            $this->commit();
            return ['imported' => $imported, 'errors' => $errors];
        } catch (Exception $e) {
            $this->rollback();
            return ['imported' => 0, 'errors' => ['Transaction failed: ' . $e->getMessage()]];
        }
    }
}
?>