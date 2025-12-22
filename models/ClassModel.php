<?php
/**
 * Class Model
 *
 * Handles class-related database operations
 */

class ClassModel extends BaseModel {
    protected $table = 'classes';
    protected $fillable = [
        'class_name', 'section', 'academic_year_id', 'class_teacher_id',
        'capacity', 'status'
    ];

    /**
     * Get classes for current academic year
     */
    public function getForCurrentYear() {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return [];
        }

        return $this->db->select(
            "SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) as teacher_name,
                    COUNT(s.id) as student_count
             FROM {$this->table} c
             LEFT JOIN user_profiles u ON c.class_teacher_id = u.user_id
             LEFT JOIN students s ON c.id = s.class_id AND s.academic_year_id = ?
             WHERE c.academic_year_id = ?
             GROUP BY c.id
             ORDER BY c.class_name, c.section",
            [$academicYearId, $academicYearId]
        );
    }

    /**
     * Get class with student count
     */
    public function getWithStudentCount($classId) {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return null;
        }

        return $this->db->selectOne(
            "SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) as teacher_name,
                    COUNT(s.id) as student_count
             FROM {$this->table} c
             LEFT JOIN user_profiles u ON c.class_teacher_id = u.user_id
             LEFT JOIN students s ON c.id = s.class_id AND s.academic_year_id = ?
             WHERE c.id = ? AND c.academic_year_id = ?
             GROUP BY c.id",
            [$academicYearId, $classId, $academicYearId]
        );
    }

    /**
     * Get available teachers (users with admin role can be class teachers)
     */
    public function getAvailableTeachers() {
        return $this->db->select(
            "SELECT u.id, CONCAT(up.first_name, ' ', up.last_name) as full_name
             FROM users u
             JOIN user_profiles up ON u.id = up.user_id
             WHERE u.role IN ('admin', 'teacher') AND u.status = 'active'
             ORDER BY up.first_name, up.last_name"
        );
    }

    /**
     * Check if class name/section combination already exists for current year
     */
    public function isClassExists($className, $section, $excludeId = null) {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return false;
        }

        $sql = "SELECT id FROM {$this->table} WHERE class_name = ? AND section = ? AND academic_year_id = ?";
        $params = [$className, $section, $academicYearId];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $result = $this->db->selectOne($sql, $params);
        return $result !== false;
    }

    /**
     * Get class capacity utilization
     */
    public function getCapacityUtilization() {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return [];
        }

        return $this->db->select(
            "SELECT c.class_name, c.section, c.capacity,
                    COUNT(s.id) as enrolled_students,
                    ROUND((COUNT(s.id) / c.capacity) * 100, 2) as utilization_percentage
             FROM {$this->table} c
             LEFT JOIN students s ON c.id = s.class_id AND s.academic_year_id = ?
             WHERE c.academic_year_id = ?
             GROUP BY c.id
             ORDER BY c.class_name, c.section",
            [$academicYearId, $academicYearId]
        );
    }
}
?>