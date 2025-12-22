<?php
/**
 * Student Model
 *
 * Handles student-related database operations
 */

class Student extends BaseModel {
    protected $table = 'students';
    protected $fillable = [
        'user_id', 'scholar_number', 'admission_number', 'admission_date',
        'first_name', 'middle_name', 'last_name', 'date_of_birth', 'gender',
        'caste_category', 'nationality', 'religion', 'blood_group',
        'village', 'address', 'permanent_address', 'mobile_number', 'email',
        'aadhar_number', 'samagra_number', 'aapaar_id', 'pan_number',
        'previous_school', 'medical_conditions', 'father_name', 'mother_name',
        'guardian_name', 'guardian_contact', 'class_id', 'roll_number',
        'academic_year_id', 'status', 'profile_image'
    ];

    /**
     * Get students for current academic year
     */
    public function getForCurrentYear() {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return [];
        }

        return $this->where(['academic_year_id' => $academicYearId]);
    }

    /**
     * Get students by class for current academic year
     */
    public function getByClass($classId) {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return [];
        }

        return $this->where([
            'class_id' => $classId,
            'academic_year_id' => $academicYearId
        ]);
    }

    /**
     * Get student with class and academic year info
     */
    public function getWithDetails($studentId) {
        $result = $this->db->selectOne(
            "SELECT s.*, c.class_name, c.section, ay.year_name,
                    CONCAT(s.first_name, ' ', s.last_name) as full_name
             FROM {$this->table} s
             JOIN classes c ON s.class_id = c.id
             JOIN academic_years ay ON s.academic_year_id = ay.id
             WHERE s.id = ?",
            [$studentId]
        );
        return $result ? $this->createInstance($result) : null;
    }

    /**
     * Search students by name, scholar number, or admission number
     */
    public function search($query, $classId = null) {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return [];
        }

        $sql = "SELECT s.*, c.class_name, c.section,
                       CONCAT(s.first_name, ' ', s.last_name) as full_name
                FROM {$this->table} s
                JOIN classes c ON s.class_id = c.id
                WHERE s.academic_year_id = ? AND (
                    s.first_name LIKE ? OR
                    s.last_name LIKE ? OR
                    s.scholar_number LIKE ? OR
                    s.admission_number LIKE ?
                )";

        $params = [$academicYearId, "%{$query}%", "%{$query}%", "%{$query}%", "%{$query}%"];

        if ($classId) {
            $sql .= " AND s.class_id = ?";
            $params[] = $classId;
        }

        $sql .= " ORDER BY c.class_name, c.section, s.roll_number";

        $results = $this->db->select($sql, $params);
        return array_map([$this, 'createInstance'], $results);
    }

    /**
     * Get student count by class
     */
    public function getCountByClass() {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return [];
        }

        return $this->db->select(
            "SELECT c.class_name, c.section, COUNT(s.id) as student_count
             FROM classes c
             LEFT JOIN {$this->table} s ON c.id = s.class_id AND s.academic_year_id = ?
             WHERE c.academic_year_id = ?
             GROUP BY c.id
             ORDER BY c.class_name, c.section",
            [$academicYearId, $academicYearId]
        );
    }

    /**
     * Promote students to next class
     */
    public function promoteStudents($fromClassId, $toClassId) {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return false;
        }

        // Get next academic year
        $nextYear = $this->db->selectOne(
            "SELECT id FROM academic_years
             WHERE start_date > (SELECT end_date FROM academic_years WHERE id = ?)
             ORDER BY start_date ASC LIMIT 1",
            [$academicYearId]
        );

        if (!$nextYear) {
            return false;
        }

        // Update students
        return $this->db->update(
            $this->table,
            [
                'class_id' => $toClassId,
                'academic_year_id' => $nextYear['id'],
                'roll_number' => null // Reset roll numbers
            ],
            'class_id = ? AND academic_year_id = ?',
            [$fromClassId, $academicYearId]
        );
    }

    /**
     * Generate next scholar number
     */
    public function generateScholarNumber() {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return null;
        }

        $year = $this->db->selectOne("SELECT year_name FROM academic_years WHERE id = ?", [$academicYearId]);
        if (!$year) {
            return null;
        }

        // Get the last scholar number for this year
        $lastStudent = $this->db->selectOne(
            "SELECT scholar_number FROM {$this->table}
             WHERE academic_year_id = ? AND scholar_number LIKE ?
             ORDER BY CAST(SUBSTRING(scholar_number, -4) AS UNSIGNED) DESC LIMIT 1",
            [$academicYearId, substr($year['year_name'], 0, 4) . '%']
        );

        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent['scholar_number'], -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return substr($year['year_name'], 0, 4) . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get attendance summary for student
     */
    public function getAttendanceSummary($studentId) {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return null;
        }

        return $this->db->selectOne(
            "SELECT
                COUNT(*) as total_days,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_days,
                ROUND((SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as attendance_percentage
             FROM attendance
             WHERE student_id = ? AND academic_year_id = ?",
            [$studentId, $academicYearId]
        );
    }
}
?>