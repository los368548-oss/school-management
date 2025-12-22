<?php
/**
 * API Controller
 *
 * Handles RESTful API endpoints for mobile and external integrations
 */

class ApiController extends BaseController {

    public function __construct() {
        parent::__construct();
        // API responses are JSON
        header('Content-Type: application/json');
    }

    // Authentication endpoints
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed'], 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $this->json(['error' => 'Invalid JSON input'], 400);
        }

        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->json(['error' => 'Username and password are required'], 400);
        }

        $user = $this->authenticateUser($username, $password);
        if (!$user) {
            $this->json(['error' => 'Invalid credentials'], 401);
        }

        // Generate API token (simplified - in production use JWT)
        $token = Security::generateRandomString(64);

        // Store token (simplified - in production use proper token storage)
        Session::set('api_token_' . $token, $user['id']);

        $this->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'name' => $user['name']
            ]
        ]);
    }

    // Student endpoints
    public function students() {
        $this->requireApiAuth();

        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                $this->getStudents();
                break;
            case 'POST':
                $this->createStudent();
                break;
            default:
                $this->json(['error' => 'Method not allowed'], 405);
        }
    }

    private function getStudents() {
        $studentModel = new Student();
        $students = $studentModel->getForCurrentYear();

        // Format for API response
        $formattedStudents = array_map(function($student) {
            return [
                'id' => $student->id,
                'scholar_number' => $student->scholar_number,
                'admission_number' => $student->admission_number,
                'name' => $student->first_name . ' ' . $student->last_name,
                'class' => $student->class_name . ' ' . $student->section,
                'roll_number' => $student->roll_number,
                'status' => $student->status
            ];
        }, $students);

        $this->json(['success' => true, 'students' => $formattedStudents]);
    }

    private function createStudent() {
        $this->requireApiAuth('admin');

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $this->json(['error' => 'Invalid JSON input'], 400);
        }

        // Validate required fields
        $requiredFields = [
            'scholar_number', 'admission_number', 'first_name', 'last_name',
            'date_of_birth', 'gender', 'class_id'
        ];

        foreach ($requiredFields as $field) {
            if (empty($input[$field])) {
                $this->json(['error' => "Field '{$field}' is required"], 400);
            }
        }

        $studentModel = new Student();

        try {
            $studentData = [
                'scholar_number' => $input['scholar_number'],
                'admission_number' => $input['admission_number'],
                'admission_date' => $input['admission_date'] ?? date('Y-m-d'),
                'first_name' => $input['first_name'],
                'middle_name' => $input['middle_name'] ?? null,
                'last_name' => $input['last_name'],
                'date_of_birth' => $input['date_of_birth'],
                'gender' => $input['gender'],
                'caste_category' => $input['caste_category'] ?? null,
                'nationality' => $input['nationality'] ?? 'Indian',
                'religion' => $input['religion'] ?? null,
                'blood_group' => $input['blood_group'] ?? null,
                'village' => $input['village'] ?? null,
                'address' => $input['address'] ?? null,
                'permanent_address' => $input['permanent_address'] ?? null,
                'mobile_number' => $input['mobile_number'] ?? null,
                'email' => $input['email'] ?? null,
                'aadhar_number' => $input['aadhar_number'] ?? null,
                'samagra_number' => $input['samagra_number'] ?? null,
                'aapaar_id' => $input['aapaar_id'] ?? null,
                'pan_number' => $input['pan_number'] ?? null,
                'previous_school' => $input['previous_school'] ?? null,
                'medical_conditions' => $input['medical_conditions'] ?? null,
                'father_name' => $input['father_name'] ?? null,
                'mother_name' => $input['mother_name'] ?? null,
                'guardian_name' => $input['guardian_name'] ?? null,
                'guardian_contact' => $input['guardian_contact'] ?? null,
                'class_id' => $input['class_id'],
                'roll_number' => $input['roll_number'] ?? null,
                'academic_year_id' => $this->getCurrentAcademicYear(),
                'status' => $input['status'] ?? 'active'
            ];

            $student = $studentModel->create($studentData);

            $this->logActivity('student_created_api', "Student created via API: {$studentData['scholar_number']}");

            $this->json([
                'success' => true,
                'student' => [
                    'id' => $student->id,
                    'scholar_number' => $student->scholar_number,
                    'admission_number' => $student->admission_number,
                    'name' => $student->first_name . ' ' . $student->last_name
                ]
            ], 201);

        } catch (Exception $e) {
            $this->json(['error' => 'Failed to create student: ' . $e->getMessage()], 500);
        }
    }

    // Fee endpoints
    public function fees() {
        $this->requireApiAuth();

        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                $this->getFees();
                break;
            case 'POST':
                $this->createFeePayment();
                break;
            default:
                $this->json(['error' => 'Method not allowed'], 405);
        }
    }

    private function getFees() {
        $academicYearId = $this->getCurrentAcademicYear();

        $fees = $this->db->select(
            "SELECT f.*, c.class_name, c.section
             FROM fees f
             LEFT JOIN classes c ON f.class_id = c.id
             WHERE f.academic_year_id = ?
             ORDER BY f.fee_name",
            [$academicYearId]
        );

        $this->json(['success' => true, 'fees' => $fees]);
    }

    private function createFeePayment() {
        $this->requireApiAuth('admin');

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $this->json(['error' => 'Invalid JSON input'], 400);
        }

        // Validate required fields
        $requiredFields = ['student_id', 'fee_id', 'amount_paid', 'payment_date'];

        foreach ($requiredFields as $field) {
            if (!isset($input[$field]) || $input[$field] === '') {
                $this->json(['error' => "Field '{$field}' is required"], 400);
            }
        }

        try {
            $paymentData = [
                'student_id' => $input['student_id'],
                'fee_id' => $input['fee_id'],
                'academic_year_id' => $this->getCurrentAcademicYear(),
                'amount_paid' => $input['amount_paid'],
                'payment_date' => $input['payment_date'],
                'payment_mode' => $input['payment_mode'] ?? 'cash',
                'transaction_id' => $input['transaction_id'] ?? null,
                'receipt_number' => $this->generateReceiptNumber(),
                'collected_by' => Session::get('user_id'),
                'remarks' => $input['remarks'] ?? null
            ];

            $paymentId = $this->db->insert('fee_payments', $paymentData);

            $this->logActivity('fee_payment_api', "Fee payment created via API: {$paymentData['receipt_number']}");

            $this->json([
                'success' => true,
                'payment' => [
                    'id' => $paymentId,
                    'receipt_number' => $paymentData['receipt_number'],
                    'amount_paid' => $paymentData['amount_paid']
                ]
            ], 201);

        } catch (Exception $e) {
            $this->json(['error' => 'Failed to create fee payment: ' . $e->getMessage()], 500);
        }
    }

    // Exam endpoints
    public function exams() {
        $this->requireApiAuth();

        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                $this->getExams();
                break;
            default:
                $this->json(['error' => 'Method not allowed'], 405);
        }
    }

    private function getExams() {
        $examModel = new Exam();
        $exams = $examModel->getForCurrentYear();

        $formattedExams = array_map(function($exam) {
            return [
                'id' => $exam['id'],
                'exam_name' => $exam['exam_name'],
                'exam_type' => $exam['exam_type'],
                'class' => $exam['class_name'] . ' ' . $exam['section'],
                'start_date' => $exam['start_date'],
                'end_date' => $exam['end_date'],
                'status' => $exam['status'],
                'subject_count' => $exam['subject_count']
            ];
        }, $exams);

        $this->json(['success' => true, 'exams' => $formattedExams]);
    }

    // Events endpoints
    public function events() {
        $this->requireApiAuth();

        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                $this->getEvents();
                break;
            case 'POST':
                $this->createEvent();
                break;
            default:
                $this->json(['error' => 'Method not allowed'], 405);
        }
    }

    private function getEvents() {
        $academicYearId = $this->getCurrentAcademicYear();

        $events = $this->db->select(
            "SELECT e.*, CONCAT(u.first_name, ' ', u.last_name) as created_by_name
             FROM events e
             LEFT JOIN user_profiles u ON e.created_by = u.user_id
             WHERE e.academic_year_id = ? OR e.academic_year_id IS NULL
             ORDER BY e.event_date DESC",
            [$academicYearId]
        );

        $this->json(['success' => true, 'events' => $events]);
    }

    private function createEvent() {
        $this->requireApiAuth('admin');

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $this->json(['error' => 'Invalid JSON input'], 400);
        }

        // Validate required fields
        $requiredFields = ['title', 'event_date'];

        foreach ($requiredFields as $field) {
            if (empty($input[$field])) {
                $this->json(['error' => "Field '{$field}' is required"], 400);
            }
        }

        try {
            $eventData = [
                'title' => $input['title'],
                'description' => $input['description'] ?? null,
                'event_date' => $input['event_date'],
                'event_time' => $input['event_time'] ?? null,
                'venue' => $input['venue'] ?? null,
                'organizer' => $input['organizer'] ?? null,
                'event_type' => $input['event_type'] ?? 'other',
                'status' => $input['status'] ?? 'upcoming',
                'is_public' => $input['is_public'] ?? 1,
                'created_by' => Session::get('user_id'),
                'academic_year_id' => $this->getCurrentAcademicYear()
            ];

            $eventId = $this->db->insert('events', $eventData);

            $this->logActivity('event_created_api', "Event created via API: {$eventData['title']}");

            $this->json([
                'success' => true,
                'event' => [
                    'id' => $eventId,
                    'title' => $eventData['title'],
                    'event_date' => $eventData['event_date']
                ]
            ], 201);

        } catch (Exception $e) {
            $this->json(['error' => 'Failed to create event: ' . $e->getMessage()], 500);
        }
    }

    // Gallery endpoints
    public function gallery() {
        $this->requireApiAuth();

        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                $this->getGallery();
                break;
            case 'POST':
                $this->uploadGallery();
                break;
            case 'DELETE':
                $this->deleteGallery();
                break;
            default:
                $this->json(['error' => 'Method not allowed'], 405);
        }
    }

    private function getGallery() {
        $academicYearId = $this->getCurrentAcademicYear();
        $eventId = $_GET['event_id'] ?? null;

        $query = "SELECT g.*, e.title as event_title,
                         CONCAT(u.first_name, ' ', u.last_name) as uploaded_by_name
                  FROM gallery g
                  LEFT JOIN events e ON g.event_id = e.id
                  LEFT JOIN user_profiles u ON g.uploaded_by = u.user_id
                  WHERE (g.academic_year_id = ? OR g.academic_year_id IS NULL)";

        $params = [$academicYearId];

        if ($eventId) {
            $query .= " AND g.event_id = ?";
            $params[] = $eventId;
        }

        $query .= " ORDER BY g.created_at DESC";

        $gallery = $this->db->select($query, $params);

        $this->json(['success' => true, 'gallery' => $gallery]);
    }

    private function uploadGallery() {
        $this->requireApiAuth('admin');

        // Handle file uploads
        if (empty($_FILES['images'])) {
            $this->json(['error' => 'No images uploaded'], 400);
        }

        $uploadedImages = [];
        $uploadDir = BASE_PATH . 'uploads/gallery/';

        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['images']['error'][$key] !== UPLOAD_ERR_OK) {
                continue;
            }

            $originalName = $_FILES['images']['name'][$key];
            $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            // Validate file type
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($fileExtension, $allowedTypes)) {
                continue;
            }

            // Generate unique filename
            $fileName = uniqid('gallery_') . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($tmpName, $filePath)) {
                $imageData = [
                    'title' => $_POST['title'][$key] ?? pathinfo($originalName, PATHINFO_FILENAME),
                    'image_path' => $fileName,
                    'category' => $_POST['category'] ?? 'general',
                    'event_id' => $_POST['event_id'] ?? null,
                    'is_featured' => $_POST['is_featured'] ?? 0,
                    'uploaded_by' => Session::get('user_id'),
                    'academic_year_id' => $this->getCurrentAcademicYear()
                ];

                $imageId = $this->db->insert('gallery', $imageData);
                $uploadedImages[] = [
                    'id' => $imageId,
                    'title' => $imageData['title'],
                    'filename' => $fileName
                ];
            }
        }

        if (empty($uploadedImages)) {
            $this->json(['error' => 'No images were uploaded successfully'], 400);
        }

        $this->logActivity('gallery_upload_api', count($uploadedImages) . ' images uploaded via API');

        $this->json([
            'success' => true,
            'uploaded' => $uploadedImages,
            'count' => count($uploadedImages)
        ], 201);
    }

    private function deleteGallery() {
        $this->requireApiAuth('admin');

        $imageId = $_GET['id'] ?? null;
        if (!$imageId) {
            $this->json(['error' => 'Image ID is required'], 400);
        }

        // Get image details
        $image = $this->db->selectOne("SELECT * FROM gallery WHERE id = ?", [$imageId]);
        if (!$image) {
            $this->json(['error' => 'Image not found'], 404);
        }

        // Delete file
        $filePath = BASE_PATH . 'uploads/gallery/' . $image['image_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete from database
        $this->db->delete('gallery', 'id = ?', [$imageId]);

        $this->logActivity('gallery_delete_api', "Image deleted via API: {$image['title']}");

        $this->json(['success' => true, 'message' => 'Image deleted successfully']);
    }

    // Reports endpoints
    public function reports() {
        $this->requireApiAuth();

        $type = $_GET['type'] ?? 'students';

        switch ($type) {
            case 'students':
                $this->getStudentReport();
                break;
            case 'attendance':
                $this->getAttendanceReport();
                break;
            case 'fees':
                $this->getFeeReport();
                break;
            default:
                $this->json(['error' => 'Invalid report type'], 400);
        }
    }

    private function getStudentReport() {
        $academicYearId = $this->getCurrentAcademicYear();

        $students = $this->db->select(
            "SELECT s.id, s.scholar_number, s.admission_number,
                    CONCAT(s.first_name, ' ', s.last_name) as name,
                    c.class_name, c.section, s.roll_number, s.status
             FROM students s
             JOIN classes c ON s.class_id = c.id
             WHERE s.academic_year_id = ?
             ORDER BY c.class_name, c.section, s.roll_number",
            [$academicYearId]
        );

        $this->json(['success' => true, 'report' => $students]);
    }

    private function getAttendanceReport() {
        $academicYearId = $this->getCurrentAcademicYear();
        $classId = $_GET['class_id'] ?? null;

        $query = "SELECT c.class_name, c.section,
                         COUNT(DISTINCT s.id) as total_students,
                         SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_count,
                         SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_count
                  FROM classes c
                  JOIN students s ON c.id = s.class_id
                  LEFT JOIN attendance a ON s.id = a.student_id
                  WHERE c.academic_year_id = ? AND s.academic_year_id = ?";

        $params = [$academicYearId, $academicYearId];

        if ($classId) {
            $query .= " AND c.id = ?";
            $params[] = $classId;
        }

        $query .= " GROUP BY c.id ORDER BY c.class_name, c.section";

        $report = $this->db->select($query, $params);

        $this->json(['success' => true, 'report' => $report]);
    }

    private function getFeeReport() {
        $academicYearId = $this->getCurrentAcademicYear();

        $report = $this->db->select(
            "SELECT c.class_name, c.section,
                    COUNT(DISTINCT s.id) as total_students,
                    SUM(f.amount) as total_fees,
                    SUM(COALESCE(fp.amount_paid, 0)) as collected_amount,
                    (SUM(f.amount) - SUM(COALESCE(fp.amount_paid, 0))) as pending_amount
             FROM classes c
             JOIN students s ON c.id = s.class_id
             LEFT JOIN fees f ON (f.class_id = c.id OR f.class_id IS NULL) AND f.academic_year_id = ?
             LEFT JOIN fee_payments fp ON s.id = fp.student_id AND fp.academic_year_id = ?
             WHERE c.academic_year_id = ? AND s.academic_year_id = ?
             GROUP BY c.id
             ORDER BY c.class_name, c.section",
            [$academicYearId, $academicYearId, $academicYearId, $academicYearId]
        );

        $this->json(['success' => true, 'report' => $report]);
    }

    // Helper methods
    private function requireApiAuth($role = null) {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? $headers['X-API-Token'] ?? null;

        if (!$token) {
            $this->json(['error' => 'API token required'], 401);
        }

        // Remove "Bearer " prefix if present
        $token = str_replace('Bearer ', '', $token);

        // Verify token (simplified)
        $userId = Session::get('api_token_' . $token);
        if (!$userId) {
            $this->json(['error' => 'Invalid API token'], 401);
        }

        // Get user details
        $user = $this->db->selectOne(
            "SELECT u.id, u.username, u.role, CONCAT(up.first_name, ' ', up.last_name) as name
             FROM users u
             LEFT JOIN user_profiles up ON u.id = up.user_id
             WHERE u.id = ? AND u.status = 'active'",
            [$userId]
        );

        if (!$user) {
            $this->json(['error' => 'User not found'], 401);
        }

        // Check role if specified
        if ($role && $user['role'] !== $role) {
            $this->json(['error' => 'Insufficient permissions'], 403);
        }

        // Set current user in session for logging
        Session::setUser($user);
    }

    private function authenticateUser($username, $password) {
        $user = $this->db->selectOne(
            "SELECT u.id, u.username, u.email, u.password, u.role, u.status,
                    CONCAT(up.first_name, ' ', up.last_name) as name
             FROM users u
             LEFT JOIN user_profiles up ON u.id = up.user_id
             WHERE (u.username = ? OR u.email = ?) AND u.status = 'active'",
            [$username, $username]
        );

        if ($user && Security::verifyPassword($password, $user['password'])) {
            // Update last login
            $this->db->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);
            return $user;
        }

        return false;
    }

    private function generateReceiptNumber() {
        $date = date('Ymd');
        $lastReceipt = $this->db->selectOne(
            "SELECT receipt_number FROM fee_payments
             WHERE receipt_number LIKE ?
             ORDER BY id DESC LIMIT 1",
            [$date . '%']
        );

        if ($lastReceipt) {
            $lastNumber = (int) substr($lastReceipt['receipt_number'], -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $date . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
?>