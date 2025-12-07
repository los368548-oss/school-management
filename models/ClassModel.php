<?php
/**
 * Class Model
 */

class ClassModel extends BaseModel {
    protected $table = 'classes';
    protected $fillable = ['name', 'section', 'academic_year', 'class_teacher_id'];

    /**
     * Get class with teacher information
     */
    public function findWithTeacher($id) {
        $result = $this->db->query("
            SELECT c.*, u.username as teacher_name, u.email as teacher_email
            FROM {$this->table} c
            LEFT JOIN users u ON c.class_teacher_id = u.id
            WHERE c.{$this->primaryKey} = ?
        ")->bind(1, $id)->single();

        return $result ? $this->processResult($result) : null;
    }

    /**
     * Get all classes with teacher information
     */
    public function allWithTeachers($orderBy = 'c.name ASC, c.section ASC') {
        $results = $this->db->query("
            SELECT c.*, u.username as teacher_name, u.email as teacher_email
            FROM {$this->table} c
            LEFT JOIN users u ON c.class_teacher_id = u.id
            ORDER BY {$orderBy}
        ")->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Get classes by academic year
     */
    public function getClassesByYear($year) {
        $results = $this->db->query("
            SELECT c.*, u.username as teacher_name
            FROM {$this->table} c
            LEFT JOIN users u ON c.class_teacher_id = u.id
            WHERE c.academic_year = ?
            ORDER BY c.name ASC, c.section ASC
        ")->bind(1, $year)->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Get class statistics
     */
    public function getClassStats($classId) {
        $result = $this->db->query("
            SELECT
                c.name, c.section,
                COUNT(DISTINCT s.id) as total_students,
                COUNT(DISTINCT sub.id) as total_subjects,
                AVG(att.attendance_percentage) as avg_attendance
            FROM {$this->table} c
            LEFT JOIN students s ON c.id = s.class_id AND s.is_active = 1
            LEFT JOIN class_subjects cs ON c.id = cs.class_id
            LEFT JOIN subjects sub ON cs.subject_id = sub.id
            LEFT JOIN (
                SELECT student_id, ROUND((SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as attendance_percentage
                FROM attendance
                WHERE MONTH(date) = MONTH(CURRENT_DATE) AND YEAR(date) = YEAR(CURRENT_DATE)
                GROUP BY student_id
            ) att ON s.id = att.student_id
            WHERE c.id = ?
            GROUP BY c.id, c.name, c.section
        ")->bind(1, $classId)->single();

        return $result ?: [
            'name' => '',
            'section' => '',
            'total_students' => 0,
            'total_subjects' => 0,
            'avg_attendance' => 0
        ];
    }

    /**
     * Assign teacher to class
     */
    public function assignTeacher($classId, $teacherId) {
        return $this->update($classId, ['class_teacher_id' => $teacherId]);
    }

    /**
     * Get available teachers (users with teacher role)
     */
    public function getAvailableTeachers() {
        $results = $this->db->query("
            SELECT u.id, u.username, u.email, r.name as role_name
            FROM users u
            JOIN user_roles r ON u.role_id = r.id
            WHERE u.is_active = 1 AND r.name IN ('Admin', 'Teacher')
            ORDER BY u.username ASC
        ")->resultSet();

        return $results;
    }

    /**
     * Check if class name/section combination exists for academic year
     */
    public function classExists($name, $section, $academicYear, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE name = ? AND section = ? AND academic_year = ?";
        $params = [$name, $section, $academicYear];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $result = $this->db->query($sql);
        foreach ($params as $index => $param) {
            $result->bind($index + 1, $param);
        }

        $count = $result->single()['count'];
        return $count > 0;
    }
}
?>