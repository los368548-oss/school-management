<?php
/**
 * Attendance Model
 *
 * Handles attendance-related database operations
 */

class Attendance extends BaseModel {
    protected $table = 'attendance';
    protected $fillable = [
        'student_id', 'class_id', 'academic_year_id', 'attendance_date',
        'status', 'marked_by', 'remarks'
    ];

    /**
     * Mark attendance for a class
     */
    public function markClassAttendance($classId, $attendanceDate, $attendanceData, $markedBy) {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return false;
        }

        $this->db->beginTransaction();

        try {
            // Delete existing attendance for this date and class
            $this->db->delete($this->table,
                'class_id = ? AND attendance_date = ? AND academic_year_id = ?',
                [$classId, $attendanceDate, $academicYearId]
            );

            // Insert new attendance records
            foreach ($attendanceData as $studentId => $status) {
                $this->create([
                    'student_id' => $studentId,
                    'class_id' => $classId,
                    'academic_year_id' => $academicYearId,
                    'attendance_date' => $attendanceDate,
                    'status' => $status,
                    'marked_by' => $markedBy
                ]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * Get attendance for a specific date and class
     */
    public function getClassAttendance($classId, $date) {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return [];
        }

        return $this->db->select(
            "SELECT a.*, s.scholar_number,
                    CONCAT(s.first_name, ' ', s.last_name) as student_name
             FROM {$this->table} a
             JOIN students s ON a.student_id = s.id
             WHERE a.class_id = ? AND a.attendance_date = ? AND a.academic_year_id = ?
             ORDER BY s.roll_number",
            [$classId, $date, $academicYearId]
        );
    }

    /**
     * Get attendance summary for a student
     */
    public function getStudentAttendance($studentId, $startDate = null, $endDate = null) {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return [];
        }

        $sql = "SELECT attendance_date, status, remarks
                FROM {$this->table}
                WHERE student_id = ? AND academic_year_id = ?";

        $params = [$studentId, $academicYearId];

        if ($startDate && $endDate) {
            $sql .= " AND attendance_date BETWEEN ? AND ?";
            $params[] = $startDate;
            $params[] = $endDate;
        }

        $sql .= " ORDER BY attendance_date DESC";

        return $this->db->select($sql, $params);
    }

    /**
     * Get attendance report for a class
     */
    public function getClassReport($classId, $startDate, $endDate) {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return [];
        }

        return $this->db->select(
            "SELECT s.scholar_number, CONCAT(s.first_name, ' ', s.last_name) as student_name,
                    COUNT(*) as total_days,
                    SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_days,
                    SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_days,
                    SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late_days,
                    ROUND((SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as attendance_percentage
             FROM students s
             LEFT JOIN {$this->table} a ON s.id = a.student_id AND a.attendance_date BETWEEN ? AND ?
             WHERE s.class_id = ? AND s.academic_year_id = ?
             GROUP BY s.id
             ORDER BY s.roll_number",
            [$startDate, $endDate, $classId, $academicYearId]
        );
    }

    /**
     * Get monthly attendance summary
     */
    public function getMonthlySummary($year, $month) {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return [];
        }

        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));

        return $this->db->select(
            "SELECT c.class_name, c.section,
                    COUNT(DISTINCT s.id) as total_students,
                    COUNT(a.id) as total_attendance_records,
                    SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as total_present,
                    SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as total_absent,
                    ROUND(AVG(CASE WHEN a.status = 'present' THEN 100 ELSE 0 END), 2) as avg_attendance
             FROM classes c
             JOIN students s ON c.id = s.class_id
             LEFT JOIN {$this->table} a ON s.id = a.student_id AND a.attendance_date BETWEEN ? AND ?
             WHERE c.academic_year_id = ? AND s.academic_year_id = ?
             GROUP BY c.id
             ORDER BY c.class_name, c.section",
            [$startDate, $endDate, $academicYearId, $academicYearId]
        );
    }

    /**
     * Check if attendance is already marked for a date and class
     */
    public function isAttendanceMarked($classId, $date) {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return false;
        }

        $count = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM {$this->table}
             WHERE class_id = ? AND attendance_date = ? AND academic_year_id = ?",
            [$classId, $date, $academicYearId]
        );

        return $count['count'] > 0;
    }

    /**
     * Get attendance statistics for dashboard
     */
    public function getDashboardStats() {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return ['total_students' => 0, 'present_today' => 0, 'absent_today' => 0];
        }

        $today = date('Y-m-d');

        $stats = $this->db->selectOne(
            "SELECT
                COUNT(DISTINCT s.id) as total_students,
                SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_today,
                SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_today
             FROM students s
             LEFT JOIN {$this->table} a ON s.id = a.student_id AND a.attendance_date = ?
             WHERE s.academic_year_id = ?",
            [$today, $academicYearId]
        );

        return $stats ?: ['total_students' => 0, 'present_today' => 0, 'absent_today' => 0];
    }
}
?>