<?php
/**
 * Exam Controller
 *
 * Handles examination management operations
 */

class ExamController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole('admin');
    }

    public function exams() {
        $examModel = $this->loadModel('Admin/Exam/Exam');

        $exams = $examModel->getForCurrentYear();
        $upcomingExams = $examModel->getUpcomingExams();

        $data = [
            'exams' => $exams,
            'upcoming_exams' => $upcomingExams,
            'academic_year' => $this->getAcademicYearInfo($this->getCurrentAcademicYear()),
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/exams', $data);
    }

    public function createExam() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/exams');
        }

        $postData = $this->getPostData();

        // Validate input
        $validationRules = [
            'exam_name' => 'required|min:2|max:100',
            'exam_type' => 'required|in:unit_test,mid_term,final_exam,practical',
            'class_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'subjects' => 'required|array'
        ];

        $errors = Validator::validateData($postData, $validationRules);

        if (empty($errors)) {
            $examModel = $this->loadModel('Admin/Exam/Exam');

            $examData = [
                'exam_name' => $postData['exam_name'],
                'exam_type' => $postData['exam_type'],
                'class_id' => $postData['class_id'],
                'start_date' => $postData['start_date'],
                'end_date' => $postData['end_date'],
                'status' => 'upcoming'
            ];

            $subjectsData = [];
            foreach ($postData['subjects'] as $subject) {
                $subjectsData[] = [
                    'subject_id' => $subject['subject_id'],
                    'exam_date' => $subject['exam_date'],
                    'start_time' => $subject['start_time'],
                    'end_time' => $subject['end_time'],
                    'max_marks' => $subject['max_marks'] ?? 100,
                    'passing_marks' => $subject['passing_marks'] ?? 33
                ];
            }

            try {
                $examModel->createWithSubjects($examData, $subjectsData);
                $this->setFlash('success', 'Exam created successfully');
                $this->logActivity('exam_created', "Created exam: {$postData['exam_name']}");
            } catch (Exception $e) {
                $errors['db'] = 'Failed to create exam: ' . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode(', ', $errors));
        }

        $this->redirect('/admin/exams');
    }

    public function edit($examId) {
        $examModel = $this->loadModel('Admin/Exam/Exam');
        $exam = $examModel->getWithSubjects($examId);

        if (!$exam) {
            $this->setFlash('error', 'Exam not found');
            $this->redirect('/admin/exams');
        }

        $errors = [];
        $classes = $this->db->select("SELECT id, class_name, section FROM classes WHERE academic_year_id = ? AND status = 'active' ORDER BY class_name, section", [$this->getCurrentAcademicYear()]);
        $subjects = $this->db->select("SELECT id, subject_name, subject_code FROM subjects WHERE status = 'active' ORDER BY subject_name");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();

            // Validation rules
            $validationRules = [
                'exam_name' => 'required|min:2|max:100',
                'exam_type' => 'required|in:unit_test,mid_term,final_exam,practical',
                'class_id' => 'required|integer',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'status' => 'required|in:upcoming,ongoing,completed,cancelled'
            ];

            $errors = Validator::validateData($postData, $validationRules);

            if (empty($errors)) {
                $examData = [
                    'exam_name' => $postData['exam_name'],
                    'exam_type' => $postData['exam_type'],
                    'class_id' => $postData['class_id'],
                    'start_date' => $postData['start_date'],
                    'end_date' => $postData['end_date'],
                    'status' => $postData['status']
                ];

                if ($examModel->update($examId, $examData)) {
                    $this->setFlash('success', 'Exam updated successfully');
                    $this->logActivity('exam_updated', "Updated exam: {$postData['exam_name']}");
                    $this->redirect('/admin/exams');
                } else {
                    $errors['db'] = 'Failed to update exam';
                }
            }
        }

        $data = [
            'exam' => $exam,
            'classes' => $classes,
            'subjects' => $subjects,
            'errors' => $errors,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/exams/edit', $data);
    }

    public function delete($examId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method'], 400);
        }

        $examModel = $this->loadModel('Admin/Exam/Exam');
        $exam = $examModel->find($examId);

        if (!$exam) {
            $this->json(['error' => 'Exam not found'], 404);
        }

        if ($examModel->delete($examId)) {
            $this->logActivity('exam_deleted', "Deleted exam: {$exam['exam_name']}");
            $this->json(['success' => true, 'message' => 'Exam deleted successfully']);
        } else {
            $this->json(['error' => 'Failed to delete exam'], 500);
        }
    }

    public function enterResults($examId) {
        $examModel = $this->loadModel('Admin/Exam/Exam');
        $exam = $examModel->getWithSubjects($examId);

        if (!$exam) {
            $this->setFlash('error', 'Exam not found');
            $this->redirect('/admin/exams');
        }

        $students = $examModel->getExamStudents($examId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();

            if ($examModel->saveResults($examId, $postData['results'] ?? [])) {
                $this->setFlash('success', 'Exam results saved successfully');
                $this->logActivity('exam_results_saved', "Saved results for exam: {$exam->exam_name}");
                $this->redirect('/admin/exams');
            } else {
                $this->setFlash('error', 'Failed to save exam results');
            }
        }

        $data = [
            'exam' => $exam,
            'students' => $students,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/enter_results', $data);
    }

    public function saveResults() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method'], 400);
        }

        $postData = $this->getPostData();
        $examId = $postData['exam_id'] ?? null;

        if (!$examId) {
            $this->json(['error' => 'Exam ID is required'], 400);
        }

        $examModel = $this->loadModel('Admin/Exam/Exam');

        if ($examModel->saveResults($examId, $postData['results'] ?? [])) {
            $this->json(['success' => true, 'message' => 'Results saved successfully']);
        } else {
            $this->json(['error' => 'Failed to save results'], 500);
        }
    }

    public function generateAdmitCard($examId) {
        $examModel = $this->loadModel('Admin/Exam/Exam');
        $exam = $examModel->find($examId);

        if (!$exam) {
            $this->setFlash('error', 'Exam not found');
            $this->redirect('/admin/exams');
        }

        $students = $this->db->select(
            "SELECT s.* FROM students s
             WHERE s.class_id = ? AND s.academic_year_id = ?
             ORDER BY s.roll_number",
            [$exam['class_id'], $this->getCurrentAcademicYear()]
        );

        $data = [
            'exam' => $exam,
            'students' => $students,
            'academic_year' => $this->getAcademicYearInfo($this->getCurrentAcademicYear())
        ];

        $this->view('admin/exams/admit_card', $data);
    }

    public function generateMarksheet($examId) {
        $examModel = $this->loadModel('Admin/Exam/Exam');
        $exam = $examModel->find($examId);

        if (!$exam) {
            $this->setFlash('error', 'Exam not found');
            $this->redirect('/admin/exams');
        }

        $students = $this->db->select(
            "SELECT s.* FROM students s
             WHERE s.class_id = ? AND s.academic_year_id = ?
             ORDER BY s.roll_number",
            [$exam['class_id'], $this->getCurrentAcademicYear()]
        );

        $data = [
            'exam' => $exam,
            'students' => $students,
            'academic_year' => $this->getAcademicYearInfo($this->getCurrentAcademicYear())
        ];

        $this->view('admin/marksheet', $data);
    }

}
?>