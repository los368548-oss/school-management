<?php
/**
 * Exam Model
 */

class Exam extends BaseModel {
    protected $table = 'exams';
    protected $fillable = ['name', 'type', 'class_id', 'start_date', 'end_date', 'created_by'];

    /**
     * Get exam with class information
     */
    public function findWithClass($id) {
        $result = $this->db->query("
            SELECT e.*, c.name as class_name, c.section as class_section,
                   u.username as created_by_name
            FROM {$this->table} e
            LEFT JOIN classes c ON e.class_id = c.id
            LEFT JOIN users u ON e.created_by = u.id
            WHERE e.{$this->primaryKey} = ?
        ")->bind(1, $id)->single();

        return $result ? $this->processResult($result) : null;
    }

    /**
     * Get all exams with class information
     */
    public function allWithClasses($orderBy = 'e.start_date DESC') {
        $results = $this->db->query("
            SELECT e.*, c.name as class_name, c.section as class_section,
                   u.username as created_by_name
            FROM {$this->table} e
            LEFT JOIN classes c ON e.class_id = c.id
            LEFT JOIN users u ON e.created_by = u.id
            ORDER BY {$orderBy}
        ")->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Get exams by class
     */
    public function getExamsByClass($classId) {
        $results = $this->db->query("
            SELECT e.*, u.username as created_by_name
            FROM {$this->table} e
            LEFT JOIN users u ON e.created_by = u.id
            WHERE e.class_id = ?
            ORDER BY e.start_date DESC
        ")->bind(1, $classId)->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Get student results for an exam
     */
    public function getStudentResults($studentId) {
        $results = $this->db->query("
            SELECT
                e.id as exam_id,
                e.name as exam_name,
                e.type as exam_type,
                e.start_date,
                e.end_date,
                GROUP_CONCAT(
                    CONCAT(s.name, ': ', COALESCE(er.marks_obtained, 0), '/', COALESCE(er.total_marks, 0))
                    ORDER BY s.name
                    SEPARATOR '; '
                ) as subject_marks,
                SUM(COALESCE(er.marks_obtained, 0)) as total_marks_obtained,
                SUM(COALESCE(er.total_marks, 0)) as total_marks,
                ROUND(
                    (SUM(COALESCE(er.marks_obtained, 0)) / SUM(COALESCE(er.total_marks, 0))) * 100,
                    2
                ) as percentage,
                COUNT(er.id) as subjects_count
            FROM {$this->table} e
            LEFT JOIN exam_results er ON e.id = er.exam_id AND er.student_id = ?
            LEFT JOIN subjects s ON er.subject_id = s.id
            GROUP BY e.id, e.name, e.type, e.start_date, e.end_date
            ORDER BY e.start_date DESC
        ")->bind(1, $studentId)->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Get exam results with student details
     */
    public function getExamResults($examId) {
        $exam = $this->findWithClass($examId);
        if (!$exam) return null;

        $results = $this->db->query("
            SELECT
                s.scholar_number,
                s.first_name,
                s.last_name,
                GROUP_CONCAT(
                    CONCAT(sub.name, ': ', COALESCE(er.marks_obtained, 0), '/', COALESCE(er.total_marks, 0))
                    ORDER BY sub.name
                    SEPARATOR '; '
                ) as subject_marks,
                SUM(COALESCE(er.marks_obtained, 0)) as total_marks_obtained,
                SUM(COALESCE(er.total_marks, 0)) as total_marks,
                ROUND(
                    (SUM(COALESCE(er.marks_obtained, 0)) / SUM(COALESCE(er.total_marks, 0))) * 100,
                    2
                ) as percentage
            FROM students s
            LEFT JOIN exam_results er ON s.id = er.student_id AND er.exam_id = ?
            LEFT JOIN subjects sub ON er.subject_id = sub.id
            WHERE s.class_id = ? AND s.is_active = 1
            GROUP BY s.id, s.scholar_number, s.first_name, s.last_name
            ORDER BY percentage DESC, s.first_name ASC, s.last_name ASC
        ")->bind(1, $examId)->bind(2, $exam['class_id'])->resultSet();

        return [
            'exam' => $exam,
            'results' => array_map([$this, 'processResult'], $results)
        ];
    }

    /**
     * Save exam results
     */
    public function saveExamResults($examId, $resultsData, $enteredBy) {
        $this->beginTransaction();

        try {
            foreach ($resultsData as $result) {
                // Check if result already exists
                $existing = $this->db->query("
                    SELECT id FROM exam_results
                    WHERE exam_id = ? AND student_id = ? AND subject_id = ?
                ")->bind(1, $examId)->bind(2, $result['student_id'])->bind(3, $result['subject_id'])->single();

                $resultData = [
                    'exam_id' => $examId,
                    'student_id' => $result['student_id'],
                    'subject_id' => $result['subject_id'],
                    'marks_obtained' => $result['marks_obtained'],
                    'total_marks' => $result['total_marks'],
                    'grade' => $this->calculateGrade($result['marks_obtained'], $result['total_marks']),
                    'entered_by' => $enteredBy
                ];

                if ($existing) {
                    $this->db->query("
                        UPDATE exam_results SET
                            marks_obtained = ?, total_marks = ?, grade = ?, entered_by = ?, updated_at = NOW()
                        WHERE id = ?
                    ")->bind(1, $result['marks_obtained'])->bind(2, $result['total_marks'])
                      ->bind(3, $resultData['grade'])->bind(4, $enteredBy)->bind(5, $existing['id'])->execute();
                } else {
                    $this->db->query("
                        INSERT INTO exam_results
                        (exam_id, student_id, subject_id, marks_obtained, total_marks, grade, entered_by, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                    ")->bind(1, $examId)->bind(2, $result['student_id'])->bind(3, $result['subject_id'])
                      ->bind(4, $result['marks_obtained'])->bind(5, $result['total_marks'])
                      ->bind(6, $resultData['grade'])->bind(7, $enteredBy)->execute();
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
     * Calculate grade based on percentage
     */
    private function calculateGrade($marks, $total) {
        if ($total == 0) return 'N/A';

        $percentage = ($marks / $total) * 100;

        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }

    /**
     * Get exam statistics
     */
    public function getExamStats($examId) {
        $result = $this->db->query("
            SELECT
                COUNT(DISTINCT er.student_id) as total_students,
                AVG((er.marks_obtained / er.total_marks) * 100) as average_percentage,
                MAX((er.marks_obtained / er.total_marks) * 100) as highest_percentage,
                MIN((er.marks_obtained / er.total_marks) * 100) as lowest_percentage,
                COUNT(er.id) as total_results
            FROM exam_results er
            WHERE er.exam_id = ?
        ")->bind(1, $examId)->single();

        return $result ?: [
            'total_students' => 0,
            'average_percentage' => 0,
            'highest_percentage' => 0,
            'lowest_percentage' => 0,
            'total_results' => 0
        ];
    }

    /**
     * Get upcoming exams
     */
    public function getUpcomingExams($limit = 10) {
        $today = date('Y-m-d');

        $results = $this->db->query("
            SELECT e.*, c.name as class_name, c.section as class_section
            FROM {$this->table} e
            LEFT JOIN classes c ON e.class_id = c.id
            WHERE e.start_date >= ?
            ORDER BY e.start_date ASC
            LIMIT ?
        ")->bind(1, $today)->bind(2, $limit)->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Check if exam name exists for class
     */
    public function examExists($name, $classId, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE name = ? AND class_id = ?";
        $params = [$name, $classId];

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