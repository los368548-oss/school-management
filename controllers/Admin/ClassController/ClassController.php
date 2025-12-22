<?php
/**
 * Class Controller
 *
 * Handles class management operations
 */

class ClassController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole('admin');
    }

    public function classes() {
        $classModel = $this->loadModel('Admin/ClassModel/ClassModel');
        $subjectModel = $this->loadModel('Admin/Subject/Subject');

        $classes = $classModel->getForCurrentYear();
        $subjects = $subjectModel->getActive();
        $teachers = $classModel->getAvailableTeachers();

        $data = [
            'classes' => $classes,
            'subjects' => $subjects,
            'teachers' => $teachers,
            'academic_year' => $this->getAcademicYearInfo($this->getCurrentAcademicYear()),
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/classes', $data);
    }

    public function addClass() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/classes');
        }

        $postData = $this->getPostData();

        // Validate input
        $validationRules = [
            'class_name' => 'required',
            'section' => 'required',
            'capacity' => 'required|integer|min:1|max:100'
        ];

        $errors = Validator::validateData($postData, $validationRules);

        if (empty($errors)) {
            $classModel = $this->loadModel('Admin/ClassModel/ClassModel');

            // Check if class already exists
            if ($classModel->isClassExists($postData['class_name'], $postData['section'])) {
                $errors['class'] = 'Class with this name and section already exists for current academic year';
            } else {
                $classData = [
                    'class_name' => $postData['class_name'],
                    'section' => $postData['section'],
                    'capacity' => $postData['capacity'],
                    'class_teacher_id' => !empty($postData['class_teacher_id']) ? $postData['class_teacher_id'] : null,
                    'academic_year_id' => $this->getCurrentAcademicYear(),
                    'status' => 'active'
                ];

                if ($classModel->create($classData)) {
                    $this->setFlash('success', 'Class added successfully');
                    $this->logActivity('class_added', "Added class: {$postData['class_name']} {$postData['section']}");
                } else {
                    $errors['db'] = 'Failed to add class';
                }
            }
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode(', ', $errors));
        }

        $this->redirect('/admin/classes');
    }

    public function edit($classId) {
        $classModel = $this->loadModel('Admin/ClassModel/ClassModel');
        $class = $classModel->find($classId);

        if (!$class) {
            $this->setFlash('error', 'Class not found');
            $this->redirect('/admin/classes');
        }

        $errors = [];
        $teachers = $classModel->getAvailableTeachers();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();

            // Validation rules
            $validationRules = [
                'class_name' => 'required',
                'section' => 'required',
                'capacity' => 'required|integer|min:1|max:100',
                'status' => 'required|in:active,inactive'
            ];

            $errors = Validator::validateData($postData, $validationRules);

            if (empty($errors)) {
                // Check if class already exists (excluding current class)
                if ($classModel->isClassExists($postData['class_name'], $postData['section'], $classId)) {
                    $errors['class'] = 'Class with this name and section already exists for current academic year';
                } else {
                    $classData = [
                        'class_name' => $postData['class_name'],
                        'section' => $postData['section'],
                        'capacity' => $postData['capacity'],
                        'class_teacher_id' => !empty($postData['class_teacher_id']) ? $postData['class_teacher_id'] : null,
                        'status' => $postData['status']
                    ];

                    if ($classModel->update($classId, $classData)) {
                        $this->setFlash('success', 'Class updated successfully');
                        $this->logActivity('class_updated', "Updated class: {$postData['class_name']} {$postData['section']}");
                        $this->redirect('/admin/classes');
                    } else {
                        $errors['db'] = 'Failed to update class';
                    }
                }
            }
        }

        $data = [
            'class' => $class,
            'teachers' => $teachers,
            'errors' => $errors,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/classes/edit', $data);
    }

    public function delete($classId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method'], 400);
        }

        $classModel = $this->loadModel('Admin/ClassModel/ClassModel');
        $class = $classModel->find($classId);

        if (!$class) {
            $this->json(['error' => 'Class not found'], 404);
        }

        // Check if class has students
        $studentCount = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM students WHERE class_id = ? AND academic_year_id = ?",
            [$classId, $this->getCurrentAcademicYear()]
        );

        if ($studentCount['count'] > 0) {
            $this->json(['error' => 'Cannot delete class that has students assigned'], 400);
        }

        if ($classModel->delete($classId)) {
            $this->logActivity('class_deleted', "Deleted class: {$class['class_name']} {$class['section']}");
            $this->json(['success' => true, 'message' => 'Class deleted successfully']);
        } else {
            $this->json(['error' => 'Failed to delete class'], 500);
        }
    }

}
?>