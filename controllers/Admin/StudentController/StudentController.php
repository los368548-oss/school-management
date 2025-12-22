<?php
/**
 * Student Controller
 *
 * Handles student management operations
 */

class StudentController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole('admin');
    }

    public function students() {
        $studentModel = $this->loadModel('Student/Student/Student');

        // Get students for current academic year
        $students = $studentModel->getForCurrentYear();

        $data = [
            'students' => $students,
            'academic_year' => $this->getAcademicYearInfo($this->getCurrentAcademicYear())
        ];

        $this->view('admin/students', $data);
    }

    public function add() {
        $errors = [];
        $classes = $this->db->select("SELECT id, class_name, section FROM classes WHERE academic_year_id = ? AND status = 'active' ORDER BY class_name, section", [$this->getCurrentAcademicYear()]);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();

            // Validation rules
            $validationRules = [
                'first_name' => 'required|min:2|max:50',
                'last_name' => 'required|min:2|max:50',
                'roll_number' => 'required|unique:students,roll_number',
                'date_of_birth' => 'required|date',
                'gender' => 'required|in:male,female,other',
                'class_id' => 'required|integer',
                'admission_date' => 'required|date'
            ];

            $errors = Validator::validateData($postData, $validationRules);

            if (empty($errors)) {
                $studentModel = $this->loadModel('Student/Student/Student');

                // Handle file upload
                $photoPath = null;
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = 'uploads/students/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $fileName = uniqid() . '_' . basename($_FILES['photo']['name']);
                    $photoPath = $uploadDir . $fileName;
                    move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);
                }

                $studentData = [
                    'first_name' => $postData['first_name'],
                    'last_name' => $postData['last_name'],
                    'roll_number' => $postData['roll_number'],
                    'admission_number' => $postData['admission_number'] ?? null,
                    'date_of_birth' => $postData['date_of_birth'],
                    'gender' => $postData['gender'],
                    'class_id' => $postData['class_id'],
                    'admission_date' => $postData['admission_date'],
                    'mobile_number' => $postData['mobile_number'] ?? null,
                    'email' => $postData['email'] ?? null,
                    'address' => $postData['address'] ?? null,
                    'father_name' => $postData['father_name'] ?? null,
                    'mother_name' => $postData['mother_name'] ?? null,
                    'guardian_contact' => $postData['guardian_contact'] ?? null,
                    'photo_path' => $photoPath,
                    'academic_year_id' => $this->getCurrentAcademicYear(),
                    'status' => 'active'
                ];

                if ($studentModel->create($studentData)) {
                    $this->setFlash('success', 'Student added successfully');
                    $this->logActivity('student_added', "Added student: {$postData['first_name']} {$postData['last_name']}");
                    $this->redirect('/admin/students');
                } else {
                    $errors['db'] = 'Failed to add student';
                }
            }
        }

        $data = [
            'classes' => $classes,
            'errors' => $errors,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/students/add', $data);
    }

    public function edit($studentId) {
        $studentModel = $this->loadModel('Student/Student/Student');
        $student = $studentModel->find($studentId);

        if (!$student) {
            $this->setFlash('error', 'Student not found');
            $this->redirect('/admin/students');
        }

        $errors = [];
        $classes = $this->db->select("SELECT id, class_name, section FROM classes WHERE academic_year_id = ? AND status = 'active' ORDER BY class_name, section", [$this->getCurrentAcademicYear()]);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();

            // Validation rules
            $validationRules = [
                'first_name' => 'required|min:2|max:50',
                'last_name' => 'required|min:2|max:50',
                'roll_number' => 'required|unique:students,roll_number,' . $studentId,
                'date_of_birth' => 'required|date',
                'gender' => 'required|in:male,female,other',
                'class_id' => 'required|integer',
                'admission_date' => 'required|date'
            ];

            $errors = Validator::validateData($postData, $validationRules);

            if (empty($errors)) {
                // Handle file upload
                $photoPath = $student['photo_path'];
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = 'uploads/students/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $fileName = uniqid() . '_' . basename($_FILES['photo']['name']);
                    $photoPath = $uploadDir . $fileName;
                    move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);

                    // Delete old photo if exists
                    if ($student['photo_path'] && file_exists($student['photo_path'])) {
                        unlink($student['photo_path']);
                    }
                }

                $studentData = [
                    'first_name' => $postData['first_name'],
                    'last_name' => $postData['last_name'],
                    'roll_number' => $postData['roll_number'],
                    'admission_number' => $postData['admission_number'] ?? null,
                    'date_of_birth' => $postData['date_of_birth'],
                    'gender' => $postData['gender'],
                    'class_id' => $postData['class_id'],
                    'admission_date' => $postData['admission_date'],
                    'mobile_number' => $postData['mobile_number'] ?? null,
                    'email' => $postData['email'] ?? null,
                    'address' => $postData['address'] ?? null,
                    'father_name' => $postData['father_name'] ?? null,
                    'mother_name' => $postData['mother_name'] ?? null,
                    'guardian_contact' => $postData['guardian_contact'] ?? null,
                    'photo_path' => $photoPath,
                    'status' => $postData['status'] ?? 'active'
                ];

                if ($studentModel->update($studentId, $studentData)) {
                    $this->setFlash('success', 'Student updated successfully');
                    $this->logActivity('student_updated', "Updated student: {$postData['first_name']} {$postData['last_name']}");
                    $this->redirect('/admin/students');
                } else {
                    $errors['db'] = 'Failed to update student';
                }
            }
        }

        $data = [
            'student' => $student,
            'classes' => $classes,
            'errors' => $errors,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/students/edit', $data);
    }

    public function view($studentId) {
        $studentModel = $this->loadModel('Student/Student/Student');
        $student = $studentModel->find($studentId);

        if (!$student) {
            $this->setFlash('error', 'Student not found');
            $this->redirect('/admin/students');
        }

        // Get additional student information
        $class = $this->db->selectOne("SELECT * FROM classes WHERE id = ?", [$student['class_id']]);
        $attendance = $this->db->selectOne("
            SELECT COUNT(*) as total_days,
                   SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days
            FROM attendance
            WHERE student_id = ? AND attendance_date >= ?
        ", [$studentId, date('Y-m-d', strtotime('-30 days'))]);

        $fees = $this->db->select("
            SELECT f.*, fp.amount_paid, fp.payment_date
            FROM fees f
            LEFT JOIN fee_payments fp ON f.id = fp.fee_id AND fp.student_id = ?
            WHERE f.student_id = ?
            ORDER BY f.due_date DESC
        ", [$studentId, $studentId]);

        $data = [
            'student' => $student,
            'class' => $class,
            'attendance' => $attendance,
            'fees' => $fees,
            'academic_year' => $this->getAcademicYearInfo($this->getCurrentAcademicYear())
        ];

        $this->view('admin/students/view', $data);
    }

    public function delete($studentId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method'], 400);
        }

        $studentModel = $this->loadModel('Student/Student/Student');
        $student = $studentModel->find($studentId);

        if (!$student) {
            $this->json(['error' => 'Student not found'], 404);
        }

        if ($studentModel->delete($studentId)) {
            // Delete photo file if exists
            if ($student['photo_path'] && file_exists($student['photo_path'])) {
                unlink($student['photo_path']);
            }

            $this->logActivity('student_deleted', "Deleted student: {$student['first_name']} {$student['last_name']}");
            $this->json(['success' => true, 'message' => 'Student deleted successfully']);
        } else {
            $this->json(['error' => 'Failed to delete student'], 500);
        }
    }

    public function export() {
        $studentModel = $this->loadModel('Student/Student/Student');
        $students = $studentModel->getForCurrentYear();

        // Generate CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="students_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // CSV headers
        fputcsv($output, [
            'Roll Number', 'Admission Number', 'First Name', 'Last Name',
            'Class', 'Section', 'Date of Birth', 'Gender', 'Mobile', 'Email',
            'Father Name', 'Mother Name', 'Address', 'Admission Date', 'Status'
        ]);

        // CSV data
        foreach ($students as $student) {
            fputcsv($output, [
                $student['roll_number'],
                $student['admission_number'],
                $student['first_name'],
                $student['last_name'],
                $student['class_name'],
                $student['section'],
                $student['date_of_birth'],
                $student['gender'],
                $student['mobile_number'],
                $student['email'],
                $student['father_name'],
                $student['mother_name'],
                $student['address'],
                $student['admission_date'],
                $student['status']
            ]);
        }

        fclose($output);
        exit;
    }

}
?>