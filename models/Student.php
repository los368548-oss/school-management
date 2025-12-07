<?php
/**
 * Student Model
 */

class Student extends BaseModel {
    protected $table = 'students';
    protected $fillable = [
        'scholar_number', 'admission_number', 'admission_date', 'first_name', 'middle_name', 'last_name',
        'class_id', 'section', 'date_of_birth', 'gender', 'caste_category', 'nationality', 'religion',
        'blood_group', 'village_address', 'permanent_address', 'mobile_number', 'email', 'aadhar_number',
        'samagra_number', 'apaar_id', 'pan_number', 'previous_school', 'medical_conditions',
        'photo_path', 'father_name', 'mother_name', 'guardian_name', 'guardian_contact', 'is_active'
    ];

    /**
     * Find student by user ID (for logged in students)
     */
    public function findByUserId($userId) {
        // This assumes there's a way to link users to students
        // In a real implementation, you might have a user_student mapping table
        // For now, we'll assume the user ID corresponds to a student record
        // This would need to be adjusted based on your actual schema

        $result = $this->db->query("
            SELECT s.*, c.name as class_name, c.section as class_section,
                   u.username as user_username
            FROM {$this->table} s
            LEFT JOIN classes c ON s.class_id = c.id
            LEFT JOIN users u ON u.id = ?
            WHERE s.id = ? AND s.is_active = 1
        ")->bind(1, $userId)->bind(2, $userId)->single();

        return $result ? $this->processResult($result) : null;
    }

    /**
     * Get students by class
     */
    public function getStudentsByClass($classId) {
        $results = $this->db->query("
            SELECT s.*, c.name as class_name, c.section as class_section
            FROM {$this->table} s
            LEFT JOIN classes c ON s.class_id = c.id
            WHERE s.class_id = ? AND s.is_active = 1
            ORDER BY s.first_name ASC, s.last_name ASC
        ")->bind(1, $classId)->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Search students
     */
    public function search($query, $classId = null, $limit = 50) {
        $searchTerm = '%' . $query . '%';
        $whereClause = '(s.first_name LIKE ? OR s.last_name LIKE ? OR s.scholar_number LIKE ?)';
        $params = [$searchTerm, $searchTerm, $searchTerm];

        if ($classId) {
            $whereClause .= ' AND s.class_id = ?';
            $params[] = $classId;
        }

        $sql = "
            SELECT s.*, c.name as class_name, c.section as class_section
            FROM {$this->table} s
            LEFT JOIN classes c ON s.class_id = c.id
            WHERE {$whereClause} AND s.is_active = 1
            ORDER BY s.first_name ASC, s.last_name ASC
            LIMIT ?
        ";

        $stmt = $this->db->query($sql);
        foreach ($params as $index => $param) {
            $stmt->bind($index + 1, $param);
        }
        $stmt->bind(count($params) + 1, $limit);

        $results = $stmt->resultSet();
        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Get student statistics
     */
    public function getStudentStats() {
        $result = $this->db->query("
            SELECT
                COUNT(*) as total_students,
                SUM(CASE WHEN gender = 'Male' THEN 1 ELSE 0 END) as male_students,
                SUM(CASE WHEN gender = 'Female' THEN 1 ELSE 0 END) as female_students,
                COUNT(DISTINCT class_id) as total_classes
            FROM {$this->table}
            WHERE is_active = 1
        ")->single();

        return $result ?: [
            'total_students' => 0,
            'male_students' => 0,
            'female_students' => 0,
            'total_classes' => 0
        ];
    }

    /**
     * Get students with pending fees
     */
    public function getStudentsWithPendingFees($threshold = 0) {
        $results = $this->db->query("
            SELECT s.*,
                   COALESCE(SUM(f.amount), 0) as total_fees,
                   COALESCE(SUM(fp.amount), 0) as paid_amount,
                   (COALESCE(SUM(f.amount), 0) - COALESCE(SUM(fp.amount), 0)) as pending_amount
            FROM {$this->table} s
            LEFT JOIN fees f ON s.class_id = f.class_id
            LEFT JOIN fee_payments fp ON s.id = fp.student_id AND fp.fee_id = f.id
            WHERE s.is_active = 1
            GROUP BY s.id
            HAVING pending_amount > ?
            ORDER BY pending_amount DESC
        ")->bind(1, $threshold)->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Get student attendance summary
     */
    public function getStudentAttendanceSummary($studentId, $month = null, $year = null) {
        $month = $month ?? date('m');
        $year = $year ?? date('Y');

        $result = $this->db->query("
            SELECT
                COUNT(*) as total_days,
                SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) as late_days,
                ROUND(
                    (SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) / COUNT(*)) * 100,
                    2
                ) as attendance_percentage
            FROM attendance
            WHERE student_id = ? AND MONTH(date) = ? AND YEAR(date) = ?
        ")->bind(1, $studentId)->bind(2, $month)->bind(3, $year)->single();

        return $result ?: [
            'total_days' => 0,
            'present_days' => 0,
            'absent_days' => 0,
            'late_days' => 0,
            'attendance_percentage' => 0
        ];
    }

    /**
     * Get student exam performance
     */
    public function getStudentExamPerformance($studentId) {
        $results = $this->db->query("
            SELECT
                e.name as exam_name,
                e.type as exam_type,
                AVG((er.marks_obtained / er.total_marks) * 100) as average_percentage,
                SUM(er.marks_obtained) as total_marks_obtained,
                SUM(er.total_marks) as total_marks_possible,
                COUNT(er.id) as subjects_count
            FROM exam_results er
            JOIN exams e ON er.exam_id = e.id
            WHERE er.student_id = ?
            GROUP BY er.exam_id, e.name, e.type
            ORDER BY e.start_date DESC
        ")->bind(1, $studentId)->resultSet();

        return $results;
    }

    /**
     * Update student photo
     */
    public function updatePhoto($studentId, $photoPath) {
        return $this->update($studentId, ['photo_path' => $photoPath]);
    }

    /**
     * Promote student to next class
     */
    public function promoteStudent($studentId, $newClassId, $newSection = null) {
        $updateData = ['class_id' => $newClassId];
        if ($newSection) {
            $updateData['section'] = $newSection;
        }

        return $this->update($studentId, $updateData);
    }

    /**
     * Get students by admission year
     */
    public function getStudentsByAdmissionYear($year) {
        $results = $this->db->query("
            SELECT s.*, c.name as class_name, c.section as class_section
            FROM {$this->table} s
            LEFT JOIN classes c ON s.class_id = c.id
            WHERE YEAR(s.admission_date) = ? AND s.is_active = 1
            ORDER BY s.admission_date ASC
        ")->bind(1, $year)->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Get student full profile with all related data
     */
    public function getStudentFullProfile($studentId) {
        $student = $this->find($studentId);
        if (!$student) return null;

        // Get attendance stats
        $attendanceStats = $this->getStudentAttendanceSummary($studentId);

        // Get exam performance
        $examPerformance = $this->getStudentExamPerformance($studentId);

        // Get fee status
        $feeModel = new Fee();
        $feeStatus = $feeModel->getStudentFeeStatus($studentId);

        return [
            'student' => $student,
            'attendance_stats' => $attendanceStats,
            'exam_performance' => $examPerformance,
            'fee_status' => $feeStatus
        ];
    }
}
?>