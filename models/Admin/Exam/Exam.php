<?php
/**
 * Exam Model
 *
 * Handles examination-related database operations
 */

class Exam extends BaseModel {
    protected $table = 'exams';
    protected $fillable = [
        'exam_name', 'exam_type', 'class_id', 'academic_year_id',
        'start_date', 'end_date', 'status', 'created_by'
    ];

    /**
     * Get exams for current academic year
     */
    public function getForCurrentYear($classId = null) {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return [];
        }

        $sql = "SELECT e.*, c.class_name, c.section,
                       CONCAT(u.first_name, ' ', u.last_name) as created_by_name
                FROM {$this->table} e
                JOIN classes c ON e.class_id = c.id
                LEFT JOIN user_profiles u ON e.created_by = u.user_id
                WHERE e.academic_year_id = ?";

        $params = [$academicYearId];

        if ($classId) {
            $sql .= " AND e.class_id = ?";
            $params[] = $classId;
        }

        $sql .= " ORDER BY e.start_date DESC";

        return $this->db->select($sql, $params);
    }

    /**
     * Create exam with subjects
     */
    public function createWithSubjects($examData, $subjectsData) {
        $this->db->beginTransaction();

        try {
            $examData['academic_year_id'] = $this->getCurrentAcademicYearId();
            $examData['created_by'] = Session::get('user_id');

            $exam = $this->create($examData);
            $examId = $exam->id;

            // Add subjects
            foreach ($subjectsData as $subject) {
                $this->db->insert('exam_subjects', [
                    'exam_id' => $examId,
                    'subject_id' => $subject['subject_id'],
                    'exam_date' => $subject['exam_date'],
                    'start_time' => $subject['start_time'],
                    'end_time' => $subject['end_time'],
                    'max_marks' => $subject['max_marks'] ?? 100,
                    'passing_marks' => $subject['passing_marks'] ?? 33
                ]);
            }

            $this->db->commit();
            return $exam;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Get exam with subjects
     */
    public function getWithSubjects($examId) {
        $exam = $this->find($examId);
        if (!$exam) {
            return null;
        }

        $subjects = $this->db->select(
            "SELECT es.*, s.subject_name, s.subject_code
             FROM exam_subjects es
             JOIN subjects s ON es.subject_id = s.id
             WHERE es.exam_id = ?
             ORDER BY es.exam_date, es.start_time",
            [$examId]
        );

        $exam->subjects = $subjects;
        return $exam;
    }

    /**
     * Get students for exam
     */
    public function getExamStudents($examId) {
        return $this->db->select(
            "SELECT s.*, er.marks_obtained, er.grade, er.remarks
             FROM students s
             LEFT JOIN exam_results er ON s.id = er.student_id AND er.exam_id = ?
             WHERE s.class_id = (SELECT class_id FROM exams WHERE id = ?) AND s.academic_year_id = ?
             ORDER BY s.roll_number",
            [$examId, $examId, $this->getCurrentAcademicYearId()]
        );
    }

    /**
     * Save exam results
     */
    public function saveResults($examId, $resultsData) {
        $this->db->beginTransaction();

        try {
            foreach ($resultsData as $studentId => $result) {
                // Check if result already exists
                $existing = $this->db->selectOne(
                    "SELECT id FROM exam_results WHERE exam_id = ? AND student_id = ? AND subject_id = ?",
                    [$examId, $studentId, $result['subject_id']]
                );

                $resultData = [
                    'exam_id' => $examId,
                    'student_id' => $studentId,
                    'subject_id' => $result['subject_id'],
                    'marks_obtained' => $result['marks_obtained'] ?? null,
                    'grade' => $result['grade'] ?? null,
                    'remarks' => $result['remarks'] ?? '',
                    'marked_by' => Session::get('user_id'),
                    'marked_at' => date('Y-m-d H:i:s')
                ];

                if ($existing) {
                    $this->db->update('exam_results', $resultData, 'id = ?', [$existing['id']]);
                } else {
                    $this->db->insert('exam_results', $resultData);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * Get exam results
     */
    public function getResults($examId) {
        return $this->db->select(
            "SELECT er.*, s.scholar_number, s.roll_number,
                    CONCAT(s.first_name, ' ', s.last_name) as student_name,
                    sub.subject_name, sub.subject_code,
                    es.max_marks, es.passing_marks
             FROM exam_results er
             JOIN students s ON er.student_id = s.id
             JOIN subjects sub ON er.subject_id = sub.id
             JOIN exam_subjects es ON er.exam_id = es.exam_id AND er.subject_id = es.subject_id
             WHERE er.exam_id = ?
             ORDER BY s.roll_number, sub.subject_name",
            [$examId]
        );
    }

    /**
     * Generate admit card data
     */
    public function generateAdmitCard($examId, $studentId) {
        $exam = $this->getWithSubjects($examId);
        if (!$exam) {
            return null;
        }

        $student = $this->db->selectOne(
            "SELECT s.*, c.class_name, c.section
             FROM students s
             JOIN classes c ON s.class_id = c.id
             WHERE s.id = ?",
            [$studentId]
        );

        if (!$student) {
            return null;
        }

        return [
            'exam' => $exam,
            'student' => $student,
            'subjects' => $exam->subjects
        ];
    }

    /**
     * Generate marksheet data
     */
    public function generateMarksheet($examId, $studentId) {
        $exam = $this->find($examId);
        if (!$exam) {
            return null;
        }

        $student = $this->db->selectOne(
            "SELECT s.*, c.class_name, c.section
             FROM students s
             JOIN classes c ON s.class_id = c.id
             WHERE s.id = ?",
            [$studentId]
        );

        if (!$student) {
            return null;
        }

        // Get all results for this exam and student
        $results = $this->db->select(
            "SELECT er.*, s.subject_name, s.subject_code,
                    es.max_marks, es.passing_marks
             FROM exam_results er
             JOIN subjects s ON er.subject_id = s.id
             JOIN exam_subjects es ON er.exam_id = es.exam_id AND er.subject_id = es.subject_id
             WHERE er.exam_id = ? AND er.student_id = ?
             ORDER BY s.subject_name",
            [$examId, $studentId]
        );

        // Calculate totals
        $totalMarks = 0;
        $totalMaxMarks = 0;
        $passedSubjects = 0;

        foreach ($results as $result) {
            $totalMarks += $result['marks_obtained'] ?? 0;
            $totalMaxMarks += $result['max_marks'];
            if (($result['marks_obtained'] ?? 0) >= $result['passing_marks']) {
                $passedSubjects++;
            }
        }

        $percentage = $totalMaxMarks > 0 ? round(($totalMarks / $totalMaxMarks) * 100, 2) : 0;

        // Determine grade
        $grade = 'F';
        if ($percentage >= 90) $grade = 'A+';
        elseif ($percentage >= 80) $grade = 'A';
        elseif ($percentage >= 70) $grade = 'B+';
        elseif ($percentage >= 60) $grade = 'B';
        elseif ($percentage >= 50) $grade = 'C';
        elseif ($percentage >= 40) $grade = 'D';

        return [
            'exam' => $exam,
            'student' => $student,
            'results' => $results,
            'total_marks' => $totalMarks,
            'total_max_marks' => $totalMaxMarks,
            'percentage' => $percentage,
            'grade' => $grade,
            'passed_subjects' => $passedSubjects,
            'total_subjects' => count($results)
        ];
    }

    /**
     * Get upcoming exams
     */
    public function getUpcomingExams() {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return [];
        }

        return $this->db->select(
            "SELECT e.*, c.class_name, c.section,
                    COUNT(DISTINCT es.subject_id) as subject_count
             FROM {$this->table} e
             JOIN classes c ON e.class_id = c.id
             LEFT JOIN exam_subjects es ON e.id = es.exam_id
             WHERE e.academic_year_id = ? AND e.start_date >= CURDATE() AND e.status = 'upcoming'
             GROUP BY e.id
             ORDER BY e.start_date ASC
             LIMIT 5",
            [$academicYearId]
        );
    }
}
?>