<?php
/**
 * Subject Model
 *
 * Handles subject-related database operations
 */

class Subject extends BaseModel {
    protected $table = 'subjects';
    protected $fillable = [
        'subject_name', 'subject_code', 'description', 'status'
    ];

    /**
     * Get all active subjects
     */
    public function getActive() {
        return $this->where(['status' => 'active']);
    }

    /**
     * Get subjects assigned to a class
     */
    public function getByClass($classId) {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return [];
        }

        return $this->db->select(
            "SELECT s.*, cs.teacher_id,
                    CONCAT(u.first_name, ' ', u.last_name) as teacher_name
             FROM {$this->table} s
             JOIN class_subjects cs ON s.id = cs.subject_id
             LEFT JOIN user_profiles u ON cs.teacher_id = u.user_id
             WHERE cs.class_id = ? AND cs.academic_year_id = ? AND s.status = 'active'
             ORDER BY s.subject_name",
            [$classId, $academicYearId]
        );
    }

    /**
     * Assign subject to class
     */
    public function assignToClass($classId, $subjectId, $teacherId = null) {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return false;
        }

        // Check if already assigned
        $existing = $this->db->selectOne(
            "SELECT id FROM class_subjects
             WHERE class_id = ? AND subject_id = ? AND academic_year_id = ?",
            [$classId, $subjectId, $academicYearId]
        );

        if ($existing) {
            // Update teacher
            return $this->db->update('class_subjects',
                ['teacher_id' => $teacherId],
                'id = ?',
                [$existing['id']]
            );
        } else {
            // Create new assignment
            return $this->db->insert('class_subjects', [
                'class_id' => $classId,
                'subject_id' => $subjectId,
                'teacher_id' => $teacherId,
                'academic_year_id' => $academicYearId
            ]);
        }
    }

    /**
     * Remove subject from class
     */
    public function removeFromClass($classId, $subjectId) {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return false;
        }

        return $this->db->delete('class_subjects',
            'class_id = ? AND subject_id = ? AND academic_year_id = ?',
            [$classId, $subjectId, $academicYearId]
        );
    }

    /**
     * Get subject statistics
     */
    public function getStatistics() {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return [];
        }

        return $this->db->select(
            "SELECT s.subject_name, s.subject_code,
                    COUNT(DISTINCT cs.class_id) as classes_assigned,
                    COUNT(DISTINCT CASE WHEN cs.teacher_id IS NOT NULL THEN cs.class_id END) as classes_with_teacher
             FROM {$this->table} s
             LEFT JOIN class_subjects cs ON s.id = cs.subject_id AND cs.academic_year_id = ?
             WHERE s.status = 'active'
             GROUP BY s.id
             ORDER BY s.subject_name",
            [$academicYearId]
        );
    }

    /**
     * Check if subject code already exists
     */
    public function isCodeExists($code, $excludeId = null) {
        $sql = "SELECT id FROM {$this->table} WHERE subject_code = ?";
        $params = [$code];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $result = $this->db->selectOne($sql, $params);
        return $result !== false;
    }

    /**
     * Get subjects taught by a teacher
     */
    public function getByTeacher($teacherId) {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return [];
        }

        return $this->db->select(
            "SELECT DISTINCT s.*, c.class_name, c.section
             FROM {$this->table} s
             JOIN class_subjects cs ON s.id = cs.subject_id
             JOIN classes c ON cs.class_id = c.id
             WHERE cs.teacher_id = ? AND cs.academic_year_id = ? AND s.status = 'active'
             ORDER BY c.class_name, c.section, s.subject_name",
            [$teacherId, $academicYearId]
        );
    }
}
?>