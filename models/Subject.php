<?php
/**
 * Subject Model
 */

class Subject extends BaseModel {
    protected $table = 'subjects';
    protected $fillable = ['name', 'code', 'description'];

    /**
     * Get subjects by class
     */
    public function getSubjectsByClass($classId) {
        $results = $this->db->query("
            SELECT s.*, cs.class_id
            FROM {$this->table} s
            JOIN class_subjects cs ON s.id = cs.subject_id
            WHERE cs.class_id = ?
            ORDER BY s.name ASC
        ")->bind(1, $classId)->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Assign subject to class
     */
    public function assignToClass($subjectId, $classId) {
        // Check if already assigned
        $existing = $this->db->query("
            SELECT id FROM class_subjects
            WHERE subject_id = ? AND class_id = ?
        ")->bind(1, $subjectId)->bind(2, $classId)->single();

        if ($existing) {
            return $existing['id']; // Already assigned
        }

        // Assign subject to class
        $result = $this->db->query("
            INSERT INTO class_subjects (subject_id, class_id, created_at)
            VALUES (?, ?, NOW())
        ")->bind(1, $subjectId)->bind(2, $classId)->execute();

        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Remove subject from class
     */
    public function removeFromClass($subjectId, $classId) {
        return $this->db->query("
            DELETE FROM class_subjects
            WHERE subject_id = ? AND class_id = ?
        ")->bind(1, $subjectId)->bind(2, $classId)->execute();
    }

    /**
     * Get subject statistics
     */
    public function getSubjectStats() {
        $result = $this->db->query("
            SELECT
                COUNT(DISTINCT s.id) as total_subjects,
                COUNT(DISTINCT cs.class_id) as classes_with_subjects,
                AVG(subject_count) as avg_subjects_per_class
            FROM {$this->table} s
            LEFT JOIN class_subjects cs ON s.id = cs.subject_id
            LEFT JOIN (
                SELECT class_id, COUNT(*) as subject_count
                FROM class_subjects
                GROUP BY class_id
            ) class_stats ON cs.class_id = class_stats.class_id
        ")->single();

        return $result ?: [
            'total_subjects' => 0,
            'classes_with_subjects' => 0,
            'avg_subjects_per_class' => 0
        ];
    }

    /**
     * Check if subject code exists
     */
    public function codeExists($code, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE code = ?";
        $params = [$code];

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

    /**
     * Get subjects not assigned to a class
     */
    public function getUnassignedSubjects($classId) {
        $results = $this->db->query("
            SELECT s.*
            FROM {$this->table} s
            WHERE s.id NOT IN (
                SELECT subject_id FROM class_subjects WHERE class_id = ?
            )
            ORDER BY s.name ASC
        ")->bind(1, $classId)->resultSet();

        return array_map([$this, 'processResult'], $results);
    }
}
?>