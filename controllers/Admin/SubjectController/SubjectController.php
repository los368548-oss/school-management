<?php
/**
 * Subject Controller
 *
 * Handles subject management operations
 */

class SubjectController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole('admin');
    }

    public function subjects() {
        $subjectModel = new Subject();

        $subjects = $subjectModel->getActive();
        $statistics = $subjectModel->getStatistics();

        $data = [
            'subjects' => $subjects,
            'statistics' => $statistics,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/subjects', $data);
    }

    public function addSubject() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/subjects');
        }

        $postData = $this->getPostData();

        // Validate input
        $validationRules = [
            'subject_name' => 'required|min:2|max:100',
            'subject_code' => 'required|min:2|max:20|unique:subjects,subject_code',
            'description' => 'max:500'
        ];

        $errors = Validator::validateData($postData, $validationRules);

        if (empty($errors)) {
            $subjectModel = new Subject();

            $subjectData = [
                'subject_name' => $postData['subject_name'],
                'subject_code' => strtoupper($postData['subject_code']),
                'description' => $postData['description'] ?? null,
                'status' => 'active'
            ];

            if ($subjectModel->create($subjectData)) {
                $this->setFlash('success', 'Subject added successfully');
                $this->logActivity('subject_added', "Added subject: {$postData['subject_name']}");
            } else {
                $errors['db'] = 'Failed to add subject';
            }
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode(', ', $errors));
        }

        $this->redirect('/admin/subjects');
    }

    public function edit($subjectId) {
        $subjectModel = new Subject();
        $subject = $subjectModel->find($subjectId);

        if (!$subject) {
            $this->setFlash('error', 'Subject not found');
            $this->redirect('/admin/subjects');
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();

            // Validation rules
            $validationRules = [
                'subject_name' => 'required|min:2|max:100',
                'subject_code' => 'required|min:2|max:20|unique:subjects,subject_code,' . $subjectId,
                'description' => 'max:500',
                'status' => 'required|in:active,inactive'
            ];

            $errors = Validator::validateData($postData, $validationRules);

            if (empty($errors)) {
                $subjectData = [
                    'subject_name' => $postData['subject_name'],
                    'subject_code' => strtoupper($postData['subject_code']),
                    'description' => $postData['description'] ?? null,
                    'status' => $postData['status']
                ];

                if ($subjectModel->update($subjectId, $subjectData)) {
                    $this->setFlash('success', 'Subject updated successfully');
                    $this->logActivity('subject_updated', "Updated subject: {$postData['subject_name']}");
                    $this->redirect('/admin/subjects');
                } else {
                    $errors['db'] = 'Failed to update subject';
                }
            }
        }

        $data = [
            'subject' => $subject,
            'errors' => $errors,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/subjects/edit', $data);
    }

    public function delete($subjectId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method'], 400);
        }

        $subjectModel = new Subject();
        $subject = $subjectModel->find($subjectId);

        if (!$subject) {
            $this->json(['error' => 'Subject not found'], 404);
        }

        // Check if subject is assigned to any class
        $assigned = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM class_subjects WHERE subject_id = ?",
            [$subjectId]
        );

        if ($assigned['count'] > 0) {
            $this->json(['error' => 'Cannot delete subject that is assigned to classes'], 400);
        }

        if ($subjectModel->delete($subjectId)) {
            $this->logActivity('subject_deleted', "Deleted subject: {$subject['subject_name']}");
            $this->json(['success' => true, 'message' => 'Subject deleted successfully']);
        } else {
            $this->json(['error' => 'Failed to delete subject'], 500);
        }
    }

    public function assignSubjectToClass() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/subjects');
        }

        $postData = $this->getPostData();

        // Validate input
        $validationRules = [
            'class_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'teacher_id' => 'integer'
        ];

        $errors = Validator::validateData($postData, $validationRules);

        if (empty($errors)) {
            $subjectModel = new Subject();

            if ($subjectModel->assignToClass($postData['class_id'], $postData['subject_id'], $postData['teacher_id'] ?? null)) {
                $this->setFlash('success', 'Subject assigned to class successfully');
                $this->logActivity('subject_assigned', "Assigned subject to class");
            } else {
                $errors['db'] = 'Failed to assign subject to class';
            }
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode(', ', $errors));
        }

        $this->redirect('/admin/subjects');
    }

    public function getClassSubjects($classId) {
        $subjectModel = new Subject();
        $subjects = $subjectModel->getByClass($classId);

        $this->json($subjects);
    }

    public function removeSubjectFromClass($classId, $subjectId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method'], 400);
        }

        $subjectModel = new Subject();

        if ($subjectModel->removeFromClass($classId, $subjectId)) {
            $this->logActivity('subject_removed', "Removed subject from class");
            $this->json(['success' => true, 'message' => 'Subject removed from class successfully']);
        } else {
            $this->json(['error' => 'Failed to remove subject from class'], 500);
        }
    }
}
?>