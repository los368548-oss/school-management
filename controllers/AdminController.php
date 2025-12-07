<?php
/**
 * Admin Controller
 */

class AdminController extends BaseController {
    private $userModel;
    private $studentModel;
    private $classModel;
    private $subjectModel;
    private $attendanceModel;
    private $examModel;
    private $feeModel;
    private $eventModel;
    private $galleryModel;
    private $reportModel;

    public function __construct() {
        parent::__construct();
        $this->requireAdmin();

        // Initialize models
        $this->userModel = new User();
        $this->studentModel = new Student();
        $this->classModel = new ClassModel();
        $this->subjectModel = new Subject();
        $this->attendanceModel = new Attendance();
        $this->examModel = new Exam();
        $this->feeModel = new Fee();
        $this->eventModel = new Event();
        $this->galleryModel = new Gallery();
        $this->reportModel = new Report();
    }

    /**
     * Admin Dashboard
     */
    public function dashboard() {
        // Get dashboard statistics
        $stats = $this->getDashboardStats();

        // Get recent activities
        $recentActivities = $this->getRecentActivities();

        // Quick actions
        $page_actions = [
            [
                'title' => 'Add Student',
                'url' => '/admin/students/create',
                'type' => 'primary',
                'icon' => 'user-plus'
            ],
            [
                'title' => 'Mark Attendance',
                'url' => '/admin/attendance',
                'type' => 'success',
                'icon' => 'calendar-check'
            ],
            [
                'title' => 'Create Event',
                'url' => '/admin/events/create',
                'type' => 'info',
                'icon' => 'calendar-plus'
            ]
        ];

        $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'page_title' => 'Dashboard',
            'stats' => $stats,
            'recent_activities' => $recentActivities,
            'page_actions' => $page_actions,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'active' => true]
            ]
        ]);
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats() {
        // Get real statistics from database
        $studentStats = $this->studentModel->getStudentStats();
        $totalClasses = $this->classModel->count();
        $totalTeachers = count($this->userModel->getUsersByRole(2)); // Assuming role_id 2 is teacher
        $eventStats = $this->eventModel->getEventStats();
        $galleryStats = $this->galleryModel->getGalleryStats();

        // Get today's attendance average
        $today = date('Y-m-d');
        $attendanceStats = $this->attendanceModel->getAttendanceStats(null, date('m'), date('Y'));
        $attendanceToday = $attendanceStats['total_records'] > 0 ? $attendanceStats['average_attendance'] : 0;

        // Get pending fees
        $overdueFees = $this->feeModel->getOverdueFees(0); // All pending fees
        $totalPendingFees = array_sum(array_column($overdueFees, 'pending_amount'));

        // Get recent exams count
        $upcomingExams = $this->examModel->getUpcomingExams(100);
        $recentExamsCount = count($upcomingExams);

        return [
            'total_students' => $studentStats['total_students'],
            'total_classes' => $totalClasses,
            'total_teachers' => $totalTeachers,
            'total_events' => $eventStats['total_events'],
            'attendance_today' => round($attendanceToday, 1),
            'fees_pending' => $totalPendingFees,
            'recent_exams' => $recentExamsCount,
            'total_gallery_images' => $galleryStats['total_images']
        ];
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities() {
        // Mock data - in production, this would come from audit_logs table
        return [
            [
                'action' => 'Student registered',
                'details' => 'New student John Doe registered',
                'timestamp' => date('Y-m-d H:i:s'),
                'user' => 'Admin'
            ],
            [
                'action' => 'Attendance marked',
                'details' => 'Class 10A attendance marked for today',
                'timestamp' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'user' => 'Teacher Smith'
            ],
            [
                'action' => 'Fee payment',
                'details' => 'Fee payment received from student Jane Doe',
                'timestamp' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'user' => 'Admin'
            ]
        ];
    }

    /**
     * Students management
     */
    public function students() {
        $page = $_GET['page'] ?? 1;
        $perPage = 25;
        $search = $_GET['search'] ?? '';
        $classId = $_GET['class_id'] ?? null;

        // Build conditions
        $conditions = ['is_active' => 1];
        if ($classId) {
            $conditions['class_id'] = $classId;
        }

        // Get students with pagination
        $students = $this->studentModel->paginate($page, $perPage, $conditions);
        $classes = $this->classModel->allWithTeachers();

        $page_actions = [
            [
                'title' => 'Add Student',
                'url' => '/admin/students/create',
                'type' => 'primary',
                'icon' => 'plus'
            ],
            [
                'title' => 'Import Students',
                'url' => '/admin/students/import',
                'type' => 'secondary',
                'icon' => 'upload'
            ]
        ];

        $this->view('admin/students/index', [
            'title' => 'Students Management',
            'page_title' => 'Students',
            'students' => $students,
            'classes' => $classes,
            'search' => $search,
            'selected_class' => $classId,
            'page_actions' => $page_actions,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Students', 'active' => true]
            ]
        ]);
    }

    /**
     * Create student form
     */
    public function createStudent() {
        $classes = $this->classModel->allWithTeachers();
        $csrf_token = $this->session->generateCsrfToken();

        $this->view('admin/students/create', [
            'title' => 'Add New Student',
            'page_title' => 'Add New Student',
            'classes' => $classes,
            'csrf_token' => $csrf_token,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Students', 'url' => '/admin/students'],
                ['title' => 'Add Student', 'active' => true]
            ]
        ]);
    }

    /**
     * Store new student
     */
    public function storeStudent() {
        $this->validateCsrf();

        $data = $this->getPostData();

        // Validate input
        $this->validator->setData($data);
        $this->validator->setRules([
            'scholar_number' => 'required|unique:students',
            'admission_number' => 'required|unique:students',
            'admission_date' => 'required|date',
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'class_id' => 'required|exists:classes,id',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'mobile_number' => 'max:15',
            'email' => 'email|max:100'
        ]);

        if (!$this->validator->validate()) {
            $this->session->setFlash('message', 'Please correct the errors below.');
            $this->session->setFlash('message_type', 'danger');
            $this->session->setFlash('errors', $this->validator->getErrors());
            $this->session->setFlash('old_input', $data);
            $this->redirect('/admin/students/create');
        }

        // Handle photo upload
        $photoPath = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photoPath = $this->handlePhotoUpload($_FILES['photo']);
        }

        // Prepare student data
        $studentData = [
            'scholar_number' => $data['scholar_number'],
            'admission_number' => $data['admission_number'],
            'admission_date' => $data['admission_date'],
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'class_id' => $data['class_id'],
            'section' => $data['section'] ?? 'A',
            'date_of_birth' => $data['date_of_birth'],
            'gender' => $data['gender'],
            'caste_category' => $data['caste_category'] ?? null,
            'nationality' => $data['nationality'] ?? 'Indian',
            'religion' => $data['religion'] ?? null,
            'blood_group' => $data['blood_group'] ?? null,
            'village_address' => $data['village_address'] ?? null,
            'permanent_address' => $data['permanent_address'] ?? null,
            'mobile_number' => $data['mobile_number'] ?? null,
            'email' => $data['email'] ?? null,
            'aadhar_number' => $data['aadhar_number'] ?? null,
            'samagra_number' => $data['samagra_number'] ?? null,
            'apaar_id' => $data['apaar_id'] ?? null,
            'pan_number' => $data['pan_number'] ?? null,
            'previous_school' => $data['previous_school'] ?? null,
            'medical_conditions' => $data['medical_conditions'] ?? null,
            'photo_path' => $photoPath,
            'father_name' => $data['father_name'] ?? null,
            'mother_name' => $data['mother_name'] ?? null,
            'guardian_name' => $data['guardian_name'] ?? null,
            'guardian_contact' => $data['guardian_contact'] ?? null
        ];

        if ($this->studentModel->create($studentData)) {
            $this->logAction('student_created', ['scholar_number' => $data['scholar_number']]);
            $this->session->setFlash('message', 'Student created successfully.');
            $this->session->setFlash('message_type', 'success');
            $this->redirect('/admin/students');
        } else {
            $this->session->setFlash('message', 'Failed to create student.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/students/create');
        }
    }

    /**
     * Show student details
     */
    public function showStudent($id) {
        $student = $this->studentModel->find($id);

        if (!$student) {
            $this->session->setFlash('message', 'Student not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/students');
        }

        $this->view('admin/students/show', [
            'title' => 'Student Details',
            'page_title' => 'Student Details',
            'student' => $student,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Students', 'url' => '/admin/students'],
                ['title' => $student['first_name'] . ' ' . $student['last_name'], 'active' => true]
            ]
        ]);
    }

    /**
     * Edit student form
     */
    public function editStudent($id) {
        $student = $this->studentModel->find($id);
        $classes = $this->classModel->allWithTeachers();
        $csrf_token = $this->session->generateCsrfToken();

        if (!$student) {
            $this->session->setFlash('message', 'Student not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/students');
        }

        $this->view('admin/students/edit', [
            'title' => 'Edit Student',
            'page_title' => 'Edit Student',
            'student' => $student,
            'classes' => $classes,
            'csrf_token' => $csrf_token,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Students', 'url' => '/admin/students'],
                ['title' => 'Edit Student', 'active' => true]
            ]
        ]);
    }

    /**
     * Update student
     */
    public function updateStudent($id) {
        $this->validateCsrf();

        $student = $this->studentModel->find($id);
        if (!$student) {
            $this->session->setFlash('message', 'Student not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/students');
        }

        $data = $this->getPostData();

        // Validate input
        $this->validator->setData($data);
        $this->validator->setRules([
            'scholar_number' => 'required|unique:students,scholar_number,' . $id,
            'admission_number' => 'required|unique:students,admission_number,' . $id,
            'admission_date' => 'required|date',
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'class_id' => 'required|exists:classes,id',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'mobile_number' => 'max:15',
            'email' => 'email|max:100'
        ]);

        if (!$this->validator->validate()) {
            $this->session->setFlash('message', 'Please correct the errors below.');
            $this->session->setFlash('message_type', 'danger');
            $this->session->setFlash('errors', $this->validator->getErrors());
            $this->redirect('/admin/students/' . $id . '/edit');
        }

        // Handle photo upload
        $photoPath = $student['photo_path'];
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $newPhotoPath = $this->handlePhotoUpload($_FILES['photo']);
            if ($newPhotoPath) {
                // Delete old photo if exists
                if ($photoPath && file_exists($photoPath)) {
                    unlink($photoPath);
                }
                $photoPath = $newPhotoPath;
            }
        }

        // Prepare update data
        $updateData = [
            'scholar_number' => $data['scholar_number'],
            'admission_number' => $data['admission_number'],
            'admission_date' => $data['admission_date'],
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'class_id' => $data['class_id'],
            'section' => $data['section'] ?? 'A',
            'date_of_birth' => $data['date_of_birth'],
            'gender' => $data['gender'],
            'caste_category' => $data['caste_category'] ?? null,
            'nationality' => $data['nationality'] ?? 'Indian',
            'religion' => $data['religion'] ?? null,
            'blood_group' => $data['blood_group'] ?? null,
            'village_address' => $data['village_address'] ?? null,
            'permanent_address' => $data['permanent_address'] ?? null,
            'mobile_number' => $data['mobile_number'] ?? null,
            'email' => $data['email'] ?? null,
            'aadhar_number' => $data['aadhar_number'] ?? null,
            'samagra_number' => $data['samagra_number'] ?? null,
            'apaar_id' => $data['apaar_id'] ?? null,
            'pan_number' => $data['pan_number'] ?? null,
            'previous_school' => $data['previous_school'] ?? null,
            'medical_conditions' => $data['medical_conditions'] ?? null,
            'photo_path' => $photoPath,
            'father_name' => $data['father_name'] ?? null,
            'mother_name' => $data['mother_name'] ?? null,
            'guardian_name' => $data['guardian_name'] ?? null,
            'guardian_contact' => $data['guardian_contact'] ?? null
        ];

        if ($this->studentModel->update($id, $updateData)) {
            $this->logAction('student_updated', ['student_id' => $id, 'scholar_number' => $data['scholar_number']]);
            $this->session->setFlash('message', 'Student updated successfully.');
            $this->session->setFlash('message_type', 'success');
            $this->redirect('/admin/students/' . $id);
        } else {
            $this->session->setFlash('message', 'Failed to update student.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/students/' . $id . '/edit');
        }
    }

    /**
     * Delete student
     */
    public function deleteStudent($id) {
        $student = $this->studentModel->find($id);
        if (!$student) {
            $this->json(['success' => false, 'message' => 'Student not found'], 404);
        }

        // Soft delete by setting is_active to false
        if ($this->studentModel->update($id, ['is_active' => false])) {
            $this->logAction('student_deleted', ['student_id' => $id, 'scholar_number' => $student['scholar_number']]);
            $this->json(['success' => true, 'message' => 'Student deleted successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to delete student'], 500);
        }
    }

    /**
     * Handle photo upload
     */
    private function handlePhotoUpload($file) {
        // Validate file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }

        if ($file['size'] > 2 * 1024 * 1024) { // 2MB limit
            return false;
        }

        // Create upload directory
        $uploadDir = 'uploads/students/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('student_') . '.' . $extension;
        $uploadPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $uploadPath;
        }

        return false;
    }

    /**
     * Classes management
     */
    public function classes() {
        $classes = $this->classModel->allWithTeachers();
        $subjects = $this->subjectModel->allWithClassCount();

        $page_actions = [
            [
                'title' => 'Add Class',
                'url' => '/admin/classes/create',
                'type' => 'primary',
                'icon' => 'plus'
            ],
            [
                'title' => 'Add Subject',
                'url' => '/admin/subjects/create',
                'type' => 'secondary',
                'icon' => 'book'
            ]
        ];

        $this->view('admin/classes/index', [
            'title' => 'Classes & Subjects Management',
            'page_title' => 'Classes & Subjects',
            'classes' => $classes,
            'subjects' => $subjects,
            'page_actions' => $page_actions,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Classes & Subjects', 'active' => true]
            ]
        ]);
    }

    /**
     * Attendance management
     */
    public function attendance() {
        $classes = $this->classModel->allWithTeachers();

        $page_actions = [
            [
                'title' => 'Mark Attendance',
                'url' => '/admin/attendance/mark',
                'type' => 'primary',
                'icon' => 'calendar-check'
            ],
            [
                'title' => 'View Report',
                'url' => '/admin/attendance/report',
                'type' => 'secondary',
                'icon' => 'chart-bar'
            ]
        ];

        $this->view('admin/attendance/index', [
            'title' => 'Attendance Management',
            'page_title' => 'Attendance',
            'classes' => $classes,
            'page_actions' => $page_actions,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Attendance', 'active' => true]
            ]
        ]);
    }

    /**
     * Mark attendance for a class
     */
    public function markAttendance() {
        $this->validateCsrf();

        $data = $this->getPostData();

        // Validate input
        $this->validator->setData($data);
        $this->validator->setRules([
            'class_id' => 'required|integer',
            'date' => 'required|date',
            'attendance' => 'required|array'
        ]);

        if (!$this->validator->validate()) {
            $this->session->setFlash('message', 'Please provide all required information.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/attendance');
        }

        $classId = $data['class_id'];
        $date = $data['date'];
        $attendanceData = [];

        // Prepare attendance data
        foreach ($data['attendance'] as $studentId => $status) {
            if (in_array($status, ['Present', 'Absent', 'Late'])) {
                $attendanceData[] = [
                    'student_id' => $studentId,
                    'class_id' => $classId,
                    'date' => $date,
                    'status' => $status,
                    'marked_by' => $this->getCurrentUserId()
                ];
            }
        }

        if (empty($attendanceData)) {
            $this->session->setFlash('message', 'No valid attendance data provided.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/attendance');
        }

        // Mark attendance
        $result = $this->attendanceModel->markAttendance($attendanceData);

        if ($result) {
            $this->logAction('attendance_marked', [
                'class_id' => $classId,
                'date' => $date,
                'students_count' => count($attendanceData)
            ]);
            $this->session->setFlash('message', "Attendance marked successfully for " . count($attendanceData) . " students.");
            $this->session->setFlash('message_type', 'success');
        } else {
            $this->session->setFlash('message', 'Failed to mark attendance.');
            $this->session->setFlash('message_type', 'danger');
        }

        $this->redirect('/admin/attendance');
    }

    /**
     * Get attendance data for AJAX
     */
    public function getAttendanceData() {
        $this->handleAjax(function() {
            $classId = $_GET['class_id'] ?? null;
            $date = $_GET['date'] ?? date('Y-m-d');

            if (!$classId) {
                throw new Exception('Class ID is required');
            }

            $students = $this->studentModel->getStudentsByClass($classId);
            $existingAttendance = $this->attendanceModel->getAttendanceByDateAndClass($date, $classId);

            // Merge student data with attendance data
            $attendanceMap = [];
            foreach ($existingAttendance as $att) {
                $attendanceMap[$att['student_id']] = $att['status'];
            }

            foreach ($students as &$student) {
                $student['attendance_status'] = $attendanceMap[$student['id']] ?? 'Present';
            }

            return [
                'students' => $students,
                'date' => $date,
                'is_marked' => $this->attendanceModel->isAttendanceMarked($classId, $date)
            ];
        });
    }

    /**
     * Attendance report
     */
    public function attendanceReport() {
        $classId = $_GET['class_id'] ?? null;
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');

        $classes = $this->classModel->allWithTeachers();
        $report = null;

        if ($classId) {
            $report = $this->attendanceModel->getMonthlyReport($classId, $month, $year);
            $class = $this->classModel->findWithTeacher($classId);
        }

        $this->view('admin/attendance/report', [
            'title' => 'Attendance Report',
            'page_title' => 'Attendance Report',
            'classes' => $classes,
            'report' => $report,
            'selected_class' => $class ?? null,
            'selected_month' => $month,
            'selected_year' => $year,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Attendance', 'url' => '/admin/attendance'],
                ['title' => 'Report', 'active' => true]
            ]
        ]);
    }

    /**
     * Exams management
     */
    public function exams() {
        $exams = $this->examModel->allWithClasses();
        $classes = $this->classModel->allWithTeachers();

        $page_actions = [
            [
                'title' => 'Create Exam',
                'url' => '/admin/exams/create',
                'type' => 'primary',
                'icon' => 'plus'
            ]
        ];

        $this->view('admin/exams/index', [
            'title' => 'Exams & Results Management',
            'page_title' => 'Exams & Results',
            'exams' => $exams,
            'classes' => $classes,
            'page_actions' => $page_actions,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Exams & Results', 'active' => true]
            ]
        ]);
    }

    /**
     * Create exam form
     */
    public function createExam() {
        $csrf_token = $this->session->generateCsrfToken();
        $classes = $this->classModel->allWithTeachers();

        $this->view('admin/exams/create', [
            'title' => 'Create New Exam',
            'page_title' => 'Create New Exam',
            'csrf_token' => $csrf_token,
            'classes' => $classes,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Exams & Results', 'url' => '/admin/exams'],
                ['title' => 'Create Exam', 'active' => true]
            ]
        ]);
    }

    /**
     * Store new exam
     */
    public function storeExam() {
        $this->validateCsrf();

        $data = $this->getPostData();

        // Validate input
        $this->validator->setData($data);
        $this->validator->setRules([
            'name' => 'required|max:100',
            'type' => 'required|in:Mid-term,Final,Custom',
            'class_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ]);

        if (!$this->validator->validate()) {
            $this->session->setFlash('message', 'Please correct the errors below.');
            $this->session->setFlash('message_type', 'danger');
            $this->session->setFlash('errors', $this->validator->getErrors());
            $this->session->setFlash('old_input', $data);
            $this->redirect('/admin/exams/create');
        }

        // Validate date range
        if (strtotime($data['start_date']) > strtotime($data['end_date'])) {
            $this->session->setFlash('message', 'End date must be after start date.');
            $this->session->setFlash('message_type', 'danger');
            $this->session->setFlash('old_input', $data);
            $this->redirect('/admin/exams/create');
        }

        // Prepare data for insertion
        $examData = $this->validator->getValidatedData();
        $examData['created_by'] = $this->getCurrentUserId();

        // Create exam
        $examId = $this->examModel->create($examData);

        if ($examId) {
            $this->logAction('exam_created', ['exam_id' => $examId]);
            $this->session->setFlash('message', 'Exam created successfully.');
            $this->session->setFlash('message_type', 'success');
            $this->redirect('/admin/exams');
        } else {
            $this->session->setFlash('message', 'Failed to create exam.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/exams/create');
        }
    }

    /**
     * Show exam details
     */
    public function showExam($id) {
        $exam = $this->examModel->findWithClass($id);

        if (!$exam) {
            $this->session->setFlash('message', 'Exam not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/exams');
        }

        $subjects = $this->examModel->getExamSubjects($id);
        $results = $this->examModel->getExamResults($id);
        $statistics = $this->examModel->getExamStatistics($id);
        $rankList = $this->examModel->generateRankList($id);

        $this->view('admin/exams/show', [
            'title' => 'Exam Details',
            'page_title' => 'Exam Details: ' . htmlspecialchars($exam['name']),
            'exam' => $exam,
            'subjects' => $subjects,
            'results' => $results,
            'statistics' => $statistics,
            'rank_list' => $rankList,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Exams & Results', 'url' => '/admin/exams'],
                ['title' => htmlspecialchars($exam['name']), 'active' => true]
            ]
        ]);
    }

    /**
     * Enter exam results
     */
    public function enterResults($examId) {
        $exam = $this->examModel->findWithClass($examId);

        if (!$exam) {
            $this->session->setFlash('message', 'Exam not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/exams');
        }

        $subjects = $this->examModel->getExamSubjects($examId);
        $students = $this->studentModel->getStudentsByClass($exam['class_id']);

        $csrf_token = $this->session->generateCsrfToken();

        $this->view('admin/exams/enter_results', [
            'title' => 'Enter Exam Results',
            'page_title' => 'Enter Results: ' . htmlspecialchars($exam['name']),
            'csrf_token' => $csrf_token,
            'exam' => $exam,
            'subjects' => $subjects,
            'students' => $students,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Exams & Results', 'url' => '/admin/exams'],
                ['title' => htmlspecialchars($exam['name']), 'url' => '/admin/exams/' . $examId],
                ['title' => 'Enter Results', 'active' => true]
            ]
        ]);
    }

    /**
     * Save exam results
     */
    public function saveResults() {
        $this->validateCsrf();

        $data = $this->getPostData();

        $this->validator->setData($data);
        $this->validator->setRules([
            'exam_id' => 'required|integer',
            'results' => 'required|array'
        ]);

        if (!$this->validator->validate()) {
            $this->json(['success' => false, 'message' => 'Invalid data provided'], 400);
        }

        $examId = $data['exam_id'];
        $results = $data['results'];

        // Validate exam exists
        $exam = $this->examModel->find($examId);
        if (!$exam) {
            $this->json(['success' => false, 'message' => 'Exam not found'], 404);
        }

        $this->db->beginTransaction();

        try {
            $saved = 0;
            foreach ($results as $studentId => $studentResults) {
                foreach ($studentResults as $subjectId => $result) {
                    $marks = isset($result['marks']) ? (float)$result['marks'] : null;
                    $totalMarks = isset($result['total_marks']) ? (float)$result['total_marks'] : null;
                    $grade = $result['grade'] ?? null;

                    if ($marks !== null && $totalMarks !== null) {
                        // Check if result already exists
                        $existing = $this->db->query("
                            SELECT id FROM exam_results
                            WHERE exam_id = ? AND student_id = ? AND subject_id = ?
                        ")->bind(1, $examId)->bind(2, $studentId)->bind(3, $subjectId)->single();

                        $resultData = [
                            'exam_id' => $examId,
                            'student_id' => $studentId,
                            'subject_id' => $subjectId,
                            'marks_obtained' => $marks,
                            'total_marks' => $totalMarks,
                            'grade' => $grade,
                            'entered_by' => $this->getCurrentUserId()
                        ];

                        if ($existing) {
                            $this->db->query("
                                UPDATE exam_results SET
                                marks_obtained = ?, total_marks = ?, grade = ?, entered_by = ?, updated_at = NOW()
                                WHERE id = ?
                            ")->bind(1, $marks)->bind(2, $totalMarks)->bind(3, $grade)->bind(4, $this->getCurrentUserId())->bind(5, $existing['id'])->execute();
                        } else {
                            $this->db->query("
                                INSERT INTO exam_results (exam_id, student_id, subject_id, marks_obtained, total_marks, grade, entered_by)
                                VALUES (?, ?, ?, ?, ?, ?, ?)
                            ")->bind(1, $examId)->bind(2, $studentId)->bind(3, $subjectId)->bind(4, $marks)->bind(5, $totalMarks)->bind(6, $grade)->bind(7, $this->getCurrentUserId())->execute();
                        }
                        $saved++;
                    }
                }
            }

            $this->db->commit();
            $this->logAction('exam_results_entered', ['exam_id' => $examId, 'results_count' => $saved]);
            $this->json(['success' => true, 'message' => "Results saved successfully for {$saved} entries"]);

        } catch (Exception $e) {
            $this->db->rollback();
            $this->json(['success' => false, 'message' => 'Failed to save results'], 500);
        }
    }

    /**
     * Fees management
     */
    public function fees() {
        $classes = $this->classModel->allWithTeachers();
        $pendingFees = $this->feeModel->getPendingFees(null, 10);

        $page_actions = [
            [
                'title' => 'Collect Fee',
                'url' => '/admin/fees/collect',
                'type' => 'primary',
                'icon' => 'money-bill-wave'
            ],
            [
                'title' => 'Set Fee Structure',
                'url' => '/admin/fees/structure',
                'type' => 'secondary',
                'icon' => 'cogs'
            ]
        ];

        $this->view('admin/fees/index', [
            'title' => 'Fees Management',
            'page_title' => 'Fees',
            'classes' => $classes,
            'pending_fees' => $pendingFees,
            'page_actions' => $page_actions,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Fees', 'active' => true]
            ]
        ]);
    }

    /**
     * Collect fee from student
     */
    public function collectFee() {
        $classes = $this->classModel->allWithTeachers();

        $csrf_token = $this->session->generateCsrfToken();

        $this->view('admin/fees/collect', [
            'title' => 'Collect Fee Payment',
            'page_title' => 'Collect Fee Payment',
            'csrf_token' => $csrf_token,
            'classes' => $classes,
            'receipt_number' => $this->feeModel->generateReceiptNumber(),
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Fees', 'url' => '/admin/fees'],
                ['title' => 'Collect Fee', 'active' => true]
            ]
        ]);
    }

    /**
     * Process fee collection
     */
    public function processFeeCollection() {
        $this->validateCsrf();

        $data = $this->getPostData();

        // Validate input
        $this->validator->setData($data);
        $this->validator->setRules([
            'student_id' => 'required|integer',
            'fee_id' => 'required|integer',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_mode' => 'required|in:Cash,Online,Cheque,UPI',
            'receipt_number' => 'required'
        ]);

        if (!$this->validator->validate()) {
            $this->session->setFlash('message', 'Please correct the errors below.');
            $this->session->setFlash('message_type', 'danger');
            $this->session->setFlash('errors', $this->validator->getErrors());
            $this->session->setFlash('old_input', $data);
            $this->redirect('/admin/fees/collect');
        }

        // Additional validation for cheque
        if ($data['payment_mode'] === 'Cheque' && empty($data['cheque_number'])) {
            $this->session->setFlash('message', 'Cheque number is required for cheque payments.');
            $this->session->setFlash('message_type', 'danger');
            $this->session->setFlash('old_input', $data);
            $this->redirect('/admin/fees/collect');
        }

        // Prepare payment data
        $paymentData = [
            'student_id' => $data['student_id'],
            'fee_id' => $data['fee_id'],
            'amount' => $data['amount'],
            'payment_date' => $data['payment_date'],
            'payment_mode' => $data['payment_mode'],
            'receipt_number' => $data['receipt_number'],
            'transaction_id' => $data['transaction_id'] ?? null,
            'cheque_number' => $data['cheque_number'] ?? null,
            'remarks' => $data['remarks'] ?? null,
            'received_by' => $this->getCurrentUserId()
        ];

        // Insert payment
        $paymentId = $this->db->query("
            INSERT INTO fee_payments (student_id, fee_id, amount, payment_date, payment_mode, receipt_number, transaction_id, cheque_number, remarks, received_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ")->bind(1, $paymentData['student_id'])->bind(2, $paymentData['fee_id'])->bind(3, $paymentData['amount'])->bind(4, $paymentData['payment_date'])->bind(5, $paymentData['payment_mode'])->bind(6, $paymentData['receipt_number'])->bind(7, $paymentData['transaction_id'])->bind(8, $paymentData['cheque_number'])->bind(9, $paymentData['remarks'])->bind(10, $paymentData['received_by'])->execute();

        if ($paymentId) {
            $this->logAction('fee_payment_collected', [
                'student_id' => $data['student_id'],
                'fee_id' => $data['fee_id'],
                'amount' => $data['amount'],
                'payment_id' => $paymentId
            ]);

            $this->session->setFlash('message', 'Fee payment collected successfully. Receipt: ' . $data['receipt_number']);
            $this->session->setFlash('message_type', 'success');
            $this->redirect('/admin/fees/receipt/' . $paymentId);
        } else {
            $this->session->setFlash('message', 'Failed to process fee payment.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/fees/collect');
        }
    }

    /**
     * Generate fee receipt
     */
    public function feeReceipt($paymentId) {
        $receipt = $this->feeModel->generateReceipt($paymentId);

        if (!$receipt) {
            $this->session->setFlash('message', 'Receipt not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/fees');
        }

        $this->view('admin/fees/receipt', [
            'title' => 'Fee Receipt',
            'receipt' => $receipt,
            'show_header' => false,
            'show_sidebar' => false,
            'show_footer' => false
        ]);
    }

    /**
     * Get student fee details via AJAX
     */
    public function getStudentFeeDetails() {
        $this->handleAjax(function() {
            $studentId = $_GET['student_id'] ?? null;

            if (!$studentId) {
                throw new Exception('Student ID is required');
            }

            $student = $this->studentModel->findWithClass($studentId);
            if (!$student) {
                throw new Exception('Student not found');
            }

            $feeStatus = $this->feeModel->getStudentFeeStatus($studentId);
            $fees = $this->feeModel->getFeesByClass($student['class_id']);
            $paymentHistory = $this->feeModel->getStudentPaymentHistory($studentId);

            return [
                'student' => $student,
                'fee_status' => $feeStatus,
                'fees' => $fees,
                'payment_history' => $paymentHistory
            ];
        });
    }

    /**
     * Fee reports
     */
    public function feeReport() {
        $classes = $this->classModel->allWithTeachers();
        $collectionSummary = $this->feeModel->getCollectionSummary();
        $defaulters = $this->feeModel->getDefaulters();

        $this->view('admin/fees/report', [
            'title' => 'Fee Reports',
            'page_title' => 'Fee Reports',
            'classes' => $classes,
            'collection_summary' => $collectionSummary,
            'defaulters' => $defaulters,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Fees', 'url' => '/admin/fees'],
                ['title' => 'Reports', 'active' => true]
            ]
        ]);
    }

    /**
     * Events management
     */
    public function events() {
        $events = $this->eventModel->allWithTeachers();
        $stats = $this->eventModel->getEventsCountByStatus();

        $page_actions = [
            [
                'title' => 'Create Event',
                'url' => '/admin/events/create',
                'type' => 'primary',
                'icon' => 'calendar-plus'
            ]
        ];

        $this->view('admin/events/index', [
            'title' => 'Events Management',
            'page_title' => 'Events',
            'events' => $events,
            'stats' => $stats,
            'page_actions' => $page_actions,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Events', 'active' => true]
            ]
        ]);
    }

    /**
     * Create event form
     */
    public function createEvent() {
        $csrf_token = $this->session->generateCsrfToken();

        $this->view('admin/events/create', [
            'title' => 'Create New Event',
            'page_title' => 'Create New Event',
            'csrf_token' => $csrf_token,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Events', 'url' => '/admin/events'],
                ['title' => 'Create Event', 'active' => true]
            ]
        ]);
    }

    /**
     * Store new event
     */
    public function storeEvent() {
        $this->validateCsrf();

        $data = $this->getPostData();

        // Validate input
        $this->validator->setData($data);
        $this->validator->setRules([
            'title' => 'required|max:200',
            'description' => 'max:1000',
            'event_date' => 'required|date',
            'location' => 'max:100'
        ]);

        if (!$this->validator->validate()) {
            $this->session->setFlash('message', 'Please correct the errors below.');
            $this->session->setFlash('message_type', 'danger');
            $this->session->setFlash('errors', $this->validator->getErrors());
            $this->session->setFlash('old_input', $data);
            $this->redirect('/admin/events/create');
        }

        // Handle file upload for event image
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->handleFileUpload($_FILES['image'], 'uploads/events/');
            if (!$imagePath) {
                $this->session->setFlash('message', 'Failed to upload event image.');
                $this->session->setFlash('message_type', 'danger');
                $this->redirect('/admin/events/create');
            }
        }

        // Prepare data for insertion
        $eventData = $this->validator->getValidatedData();
        $eventData['image_path'] = $imagePath;
        $eventData['created_by'] = $this->getCurrentUserId();
        $eventData['is_active'] = 1;

        // Create event
        $eventId = $this->eventModel->create($eventData);

        if ($eventId) {
            $this->logAction('event_created', ['event_id' => $eventId]);
            $this->session->setFlash('message', 'Event created successfully.');
            $this->session->setFlash('message_type', 'success');
            $this->redirect('/admin/events');
        } else {
            $this->session->setFlash('message', 'Failed to create event.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/events/create');
        }
    }

    /**
     * Show event details
     */
    public function showEvent($id) {
        $event = $this->eventModel->findWithDetails($id);

        if (!$event) {
            $this->session->setFlash('message', 'Event not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/events');
        }

        $this->view('admin/events/show', [
            'title' => 'Event Details',
            'page_title' => 'Event Details: ' . htmlspecialchars($event['title']),
            'event' => $event,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Events', 'url' => '/admin/events'],
                ['title' => htmlspecialchars($event['title']), 'active' => true]
            ]
        ]);
    }

    /**
     * Edit event form
     */
    public function editEvent($id) {
        $event = $this->eventModel->find($id);

        if (!$event) {
            $this->session->setFlash('message', 'Event not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/events');
        }

        $csrf_token = $this->session->generateCsrfToken();

        $this->view('admin/events/edit', [
            'title' => 'Edit Event',
            'page_title' => 'Edit Event',
            'event' => $event,
            'csrf_token' => $csrf_token,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Events', 'url' => '/admin/events'],
                ['title' => 'Edit', 'active' => true]
            ]
        ]);
    }

    /**
     * Update event
     */
    public function updateEvent($id) {
        $this->validateCsrf();

        $event = $this->eventModel->find($id);
        if (!$event) {
            $this->session->setFlash('message', 'Event not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/events');
        }

        $data = $this->getPostData();

        // Validate input
        $this->validator->setData($data);
        $this->validator->setRules([
            'title' => 'required|max:200',
            'description' => 'max:1000',
            'event_date' => 'required|date',
            'location' => 'max:100'
        ]);

        if (!$this->validator->validate()) {
            $this->session->setFlash('message', 'Please correct the errors below.');
            $this->session->setFlash('message_type', 'danger');
            $this->session->setFlash('errors', $this->validator->getErrors());
            $this->redirect('/admin/events/' . $id . '/edit');
        }

        // Handle file upload for event image
        $imagePath = $event['image_path'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $newImagePath = $this->handleFileUpload($_FILES['image'], 'uploads/events/');
            if ($newImagePath) {
                // Delete old image if exists
                if ($imagePath && file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $imagePath = $newImagePath;
            }
        }

        // Prepare data for update
        $eventData = $this->validator->getValidatedData();
        $eventData['image_path'] = $imagePath;

        // Update event
        if ($this->eventModel->update($id, $eventData)) {
            $this->logAction('event_updated', ['event_id' => $id]);
            $this->session->setFlash('message', 'Event updated successfully.');
            $this->session->setFlash('message_type', 'success');
            $this->redirect('/admin/events/' . $id);
        } else {
            $this->session->setFlash('message', 'Failed to update event.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/events/' . $id . '/edit');
        }
    }

    /**
     * Delete event
     */
    public function deleteEvent($id) {
        $event = $this->eventModel->find($id);
        if (!$event) {
            $this->json(['success' => false, 'message' => 'Event not found'], 404);
        }

        // Soft delete
        if ($this->eventModel->softDelete($id)) {
            $this->logAction('event_deleted', ['event_id' => $id]);
            $this->json(['success' => true, 'message' => 'Event deleted successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to delete event'], 500);
        }
    }

    /**
     * Gallery management
     */
    public function gallery() {
        $images = $this->galleryModel->allWithUploaders();
        $categories = $this->galleryModel->getCategories();
        $stats = $this->galleryModel->getGalleryStats();

        $page_actions = [
            [
                'title' => 'Upload Images',
                'url' => '/admin/gallery/upload',
                'type' => 'primary',
                'icon' => 'upload'
            ]
        ];

        $this->view('admin/gallery/index', [
            'title' => 'Gallery Management',
            'page_title' => 'Gallery',
            'images' => $images,
            'categories' => $categories,
            'stats' => $stats,
            'page_actions' => $page_actions,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Gallery', 'active' => true]
            ]
        ]);
    }

    /**
     * Upload gallery images
     */
    public function uploadGallery() {
        $csrf_token = $this->session->generateCsrfToken();
        $categories = $this->galleryModel->getCategories();

        $this->view('admin/gallery/upload', [
            'title' => 'Upload Gallery Images',
            'page_title' => 'Upload Images',
            'csrf_token' => $csrf_token,
            'categories' => $categories,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Gallery', 'url' => '/admin/gallery'],
                ['title' => 'Upload', 'active' => true]
            ]
        ]);
    }

    /**
     * Process gallery upload
     */
    public function processGalleryUpload() {
        $this->validateCsrf();

        if (empty($_FILES['images'])) {
            $this->session->setFlash('message', 'No files selected for upload.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/gallery/upload');
        }

        $category = $_POST['category'] ?? null;
        $files = $_FILES['images'];

        // Handle multiple file upload
        $uploadedFiles = [];
        if (is_array($files['name'])) {
            // Multiple files
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $uploadedFiles[] = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    ];
                }
            }
        } else {
            // Single file
            if ($files['error'] === UPLOAD_ERR_OK) {
                $uploadedFiles[] = $files;
            }
        }

        if (empty($uploadedFiles)) {
            $this->session->setFlash('message', 'No valid files to upload.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/gallery/upload');
        }

        // Validate file types and sizes
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        $validFiles = [];
        foreach ($uploadedFiles as $file) {
            if (!in_array($file['type'], $allowedTypes)) {
                continue; // Skip invalid file types
            }
            if ($file['size'] > $maxSize) {
                continue; // Skip files that are too large
            }
            $validFiles[] = $file;
        }

        if (empty($validFiles)) {
            $this->session->setFlash('message', 'No valid image files found. Please upload JPG, PNG, GIF, or WebP files under 5MB each.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/gallery/upload');
        }

        // Upload images
        $result = $this->galleryModel->bulkUpload($validFiles, $category, $this->getCurrentUserId());

        if ($result['uploaded'] > 0) {
            $this->logAction('gallery_images_uploaded', [
                'uploaded_count' => $result['uploaded'],
                'category' => $category
            ]);

            $message = "Successfully uploaded {$result['uploaded']} image(s).";
            if (!empty($result['errors'])) {
                $message .= " Some files failed to upload: " . implode(', ', $result['errors']);
            }

            $this->session->setFlash('message', $message);
            $this->session->setFlash('message_type', $result['uploaded'] > 0 ? 'success' : 'warning');
        } else {
            $this->session->setFlash('message', 'Failed to upload any images.');
            $this->session->setFlash('message_type', 'danger');
        }

        $this->redirect('/admin/gallery');
    }

    /**
     * Delete gallery image
     */
    public function deleteGallery($id) {
        $image = $this->galleryModel->find($id);
        if (!$image) {
            $this->json(['success' => false, 'message' => 'Image not found'], 404);
        }

        // Delete physical files
        if ($image['image_path'] && file_exists($image['image_path'])) {
            unlink($image['image_path']);
        }
        if (isset($image['thumbnail_path']) && $image['thumbnail_path'] && file_exists($image['thumbnail_path'])) {
            unlink($image['thumbnail_path']);
        }

        // Soft delete from database
        if ($this->galleryModel->softDelete($id)) {
            $this->logAction('gallery_image_deleted', ['image_id' => $id]);
            $this->json(['success' => true, 'message' => 'Image deleted successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to delete image'], 500);
        }
    }

    /**
     * Reports
     */
    public function reports() {
        $classes = $this->classModel->allWithTeachers();
        $exams = $this->examModel->allWithClasses();

        $this->view('admin/reports/index', [
            'title' => 'Reports & Analytics',
            'page_title' => 'Reports',
            'classes' => $classes,
            'exams' => $exams,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Reports', 'active' => true]
            ]
        ]);
    }

    /**
     * Generate student report
     */
    public function studentReport() {
        $filters = $_GET;
        $data = $this->reportModel->generateStudentReport($filters);

        $this->view('admin/reports/students', [
            'title' => 'Student Report',
            'page_title' => 'Student Report',
            'data' => $data,
            'filters' => $filters,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Reports', 'url' => '/admin/reports'],
                ['title' => 'Students', 'active' => true]
            ]
        ]);
    }

    /**
     * Generate attendance report
     */
    public function attendanceReport() {
        $classId = $_GET['class_id'] ?? null;
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');

        $data = null;
        $class = null;

        if ($classId) {
            $data = $this->reportModel->generateAttendanceReport($classId, $month, $year);
            $class = $this->classModel->findWithTeacher($classId);
        }

        $this->view('admin/reports/attendance', [
            'title' => 'Attendance Report',
            'page_title' => 'Attendance Report',
            'data' => $data,
            'class' => $class,
            'month' => $month,
            'year' => $year,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Reports', 'url' => '/admin/reports'],
                ['title' => 'Attendance', 'active' => true]
            ]
        ]);
    }

    /**
     * Generate fee report
     */
    public function feeReport() {
        $filters = $_GET;
        $data = $this->reportModel->generateFeeReport($filters);

        $this->view('admin/reports/fees', [
            'title' => 'Fee Collection Report',
            'page_title' => 'Fee Collection Report',
            'data' => $data,
            'filters' => $filters,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Reports', 'url' => '/admin/reports'],
                ['title' => 'Fees', 'active' => true]
            ]
        ]);
    }

    /**
     * Generate exam report
     */
    public function examReport($examId) {
        $report = $this->reportModel->generateExamReport($examId);

        if (!$report) {
            $this->session->setFlash('message', 'Exam not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/reports');
        }

        $this->view('admin/reports/exam', [
            'title' => 'Exam Results Report',
            'page_title' => 'Exam Results: ' . htmlspecialchars($report['exam']['name']),
            'report' => $report,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Reports', 'url' => '/admin/reports'],
                ['title' => 'Exam Results', 'active' => true]
            ]
        ]);
    }

    /**
     * Export report to CSV
     */
    public function exportCSV() {
        $type = $_GET['type'] ?? '';
        $filename = 'report_' . date('Y-m-d_H-i-s') . '.csv';

        switch ($type) {
            case 'students':
                $data = $this->reportModel->generateStudentReport($_GET);
                $this->reportModel->exportToCSV($data, 'students_' . $filename);
                break;

            case 'attendance':
                $classId = $_GET['class_id'] ?? null;
                $month = $_GET['month'] ?? date('m');
                $year = $_GET['year'] ?? date('Y');
                if ($classId) {
                    $data = $this->reportModel->generateAttendanceReport($classId, $month, $year);
                    $this->reportModel->exportToCSV($data, 'attendance_' . $filename);
                }
                break;

            case 'fees':
                $data = $this->reportModel->generateFeeReport($_GET);
                $this->reportModel->exportToCSV($data, 'fees_' . $filename);
                break;

            default:
                $this->json(['success' => false, 'message' => 'Invalid report type'], 400);
        }
    }

    /**
     * Export report to PDF
     */
    public function exportPDF() {
        $type = $_GET['type'] ?? '';
        $filename = 'report_' . date('Y-m-d_H-i-s') . '.pdf';
        $title = 'School Report';

        switch ($type) {
            case 'students':
                $data = $this->reportModel->generateStudentReport($_GET);
                $title = 'Student Report';
                break;

            case 'attendance':
                $classId = $_GET['class_id'] ?? null;
                $month = $_GET['month'] ?? date('m');
                $year = $_GET['year'] ?? date('Y');
                if ($classId) {
                    $data = $this->reportModel->generateAttendanceReport($classId, $month, $year);
                    $title = 'Attendance Report - ' . date('F Y', strtotime("$year-$month-01"));
                } else {
                    $data = [];
                }
                break;

            case 'fees':
                $data = $this->reportModel->generateFeeReport($_GET);
                $title = 'Fee Collection Report';
                break;

            default:
                $data = [];
        }

        $this->reportModel->generatePDF($data, $title, $filename);
    }

    /**
     * Settings
     */
    public function settings() {
        $csrf_token = $this->session->generateCsrfToken();

        $this->view('admin/settings/index', [
            'title' => 'System Settings',
            'page_title' => 'Settings',
            'csrf_token' => $csrf_token,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Settings', 'active' => true]
            ]
        ]);
    }

    /**
     * Update general settings
     */
    public function updateGeneralSettings() {
        $this->validateCsrf();

        $data = $this->getPostData();

        // Validate input
        $this->validator->setData($data);
        $this->validator->setRules([
            'app_name' => 'required|max:100',
            'timezone' => 'required',
            'language' => 'required|in:en,hi',
            'per_page' => 'required|integer|min:10|max:100'
        ]);

        if (!$this->validator->validate()) {
            $this->session->setFlash('message', 'Please correct the errors below.');
            $this->session->setFlash('message_type', 'danger');
            $this->session->setFlash('errors', $this->validator->getErrors());
            $this->redirect('/admin/settings');
        }

        // Update settings
        $settings = [
            'app_name' => $data['app_name'],
            'timezone' => $data['timezone'],
            'language' => $data['language'],
            'per_page' => $data['per_page']
        ];

        foreach ($settings as $key => $value) {
            $this->updateOrCreateSetting($key, $value);
        }

        $this->logAction('settings_updated', ['type' => 'general']);
        $this->session->setFlash('message', 'General settings updated successfully.');
        $this->session->setFlash('message_type', 'success');
        $this->redirect('/admin/settings');
    }

    /**
     * Update school information
     */
    public function updateSchoolSettings() {
        $this->validateCsrf();

        $data = $this->getPostData();

        // Validate input
        $this->validator->setData($data);
        $this->validator->setRules([
            'school_name' => 'required|max:100',
            'school_address' => 'max:255',
            'school_phone' => 'max:15',
            'school_email' => 'email|max:100',
            'school_website' => 'url|max:255',
            'academic_year' => 'required|max:20',
            'school_description' => 'max:1000'
        ]);

        if (!$this->validator->validate()) {
            $this->session->setFlash('message', 'Please correct the errors below.');
            $this->session->setFlash('message_type', 'danger');
            $this->session->setFlash('errors', $this->validator->getErrors());
            $this->redirect('/admin/settings');
        }

        // Update settings
        $settings = [
            'school_name' => $data['school_name'],
            'school_address' => $data['school_address'] ?? '',
            'school_phone' => $data['school_phone'] ?? '',
            'school_email' => $data['school_email'] ?? '',
            'school_website' => $data['school_website'] ?? '',
            'academic_year' => $data['academic_year'],
            'school_description' => $data['school_description'] ?? ''
        ];

        foreach ($settings as $key => $value) {
            $this->updateOrCreateSetting($key, $value);
        }

        $this->logAction('settings_updated', ['type' => 'school']);
        $this->session->setFlash('message', 'School information updated successfully.');
        $this->session->setFlash('message_type', 'success');
        $this->redirect('/admin/settings');
    }

    /**
     * Update security settings
     */
    public function updateSecuritySettings() {
        $this->validateCsrf();

        $data = $this->getPostData();

        // Validate input
        $this->validator->setData($data);
        $this->validator->setRules([
            'session_timeout' => 'required|integer|min:15|max:480',
            'password_min_length' => 'required|integer|min:6|max:32',
            'max_login_attempts' => 'required|integer|min:3|max:10',
            'lockout_duration' => 'required|integer|min:5|max:1440'
        ]);

        if (!$this->validator->validate()) {
            $this->session->setFlash('message', 'Please correct the errors below.');
            $this->session->setFlash('message_type', 'danger');
            $this->session->setFlash('errors', $this->validator->getErrors());
            $this->redirect('/admin/settings');
        }

        // Update settings
        $settings = [
            'session_timeout' => $data['session_timeout'],
            'password_min_length' => $data['password_min_length'],
            'max_login_attempts' => $data['max_login_attempts'],
            'lockout_duration' => $data['lockout_duration'],
            'enable_2fa' => isset($data['enable_2fa']) ? '1' : '0'
        ];

        foreach ($settings as $key => $value) {
            $this->updateOrCreateSetting($key, $value, 'boolean');
        }

        $this->logAction('settings_updated', ['type' => 'security']);
        $this->session->setFlash('message', 'Security settings updated successfully.');
        $this->session->setFlash('message_type', 'success');
        $this->redirect('/admin/settings');
    }

    /**
     * Create backup
     */
    public function createBackup() {
        $this->requireAdmin();

        try {
            $backupDir = 'backups/';
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $timestamp = date('Y-m-d_H-i-s');
            $backupFile = $backupDir . 'backup_' . $timestamp . '.sql';

            // Get database config
            $dbConfig = require 'config/database.php';

            // Create mysqldump command
            $command = sprintf(
                'mysqldump -h %s -u %s -p%s %s > %s',
                escapeshellarg($dbConfig['host']),
                escapeshellarg($dbConfig['username']),
                escapeshellarg($dbConfig['password']),
                escapeshellarg($dbConfig['database']),
                escapeshellarg($backupFile)
            );

            exec($command, $output, $returnCode);

            if ($returnCode === 0) {
                $this->logAction('backup_created', ['file' => $backupFile]);
                $this->json(['success' => true, 'message' => 'Backup created successfully', 'file' => $backupFile]);
            } else {
                $this->json(['success' => false, 'message' => 'Failed to create backup'], 500);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Backup creation failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Helper method to update or create setting
     */
    private function updateOrCreateSetting($key, $value, $type = 'string') {
        $existing = $this->db->query("SELECT id FROM settings WHERE setting_key = ?")->bind(1, $key)->single();

        if ($existing) {
            $this->db->query("
                UPDATE settings SET
                setting_value = ?, setting_type = ?, updated_by = ?, updated_at = NOW()
                WHERE setting_key = ?
            ")->bind(1, $value)->bind(2, $type)->bind(3, $this->getCurrentUserId())->bind(4, $key)->execute();
        } else {
            $this->db->query("
                INSERT INTO settings (setting_key, setting_value, setting_type, updated_by)
                VALUES (?, ?, ?, ?)
            ")->bind(1, $key)->bind(2, $value)->bind(3, $type)->bind(4, $this->getCurrentUserId())->execute();
        }
    }

    // Student CRUD Methods

    /**
     * Create student form
     */
    public function createStudent() {
        $csrf_token = $this->session->generateCsrfToken();

        $this->view('admin/students/create', [
            'title' => 'Add New Student',
            'page_title' => 'Add New Student',
            'csrf_token' => $csrf_token,
            'scholar_number' => $this->studentModel->generateScholarNumber(),
            'admission_number' => $this->studentModel->generateAdmissionNumber(),
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Students', 'url' => '/admin/students'],
                ['title' => 'Add New', 'active' => true]
            ]
        ]);
    }

    /**
     * Store new student
     */
    public function storeStudent() {
        $this->validateCsrf();

        $data = $this->getPostData();

        // Validate input
        $this->validator->setData($data);
        $this->validator->setRules([
            'scholar_number' => 'required|unique:students',
            'admission_number' => 'required|unique:students',
            'admission_date' => 'required|date',
            'first_name' => 'required|min:2|max:50',
            'last_name' => 'required|min:2|max:50',
            'class_id' => 'required|integer',
            'section' => 'required|max:10',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'father_name' => 'required|min:2|max:100',
            'mother_name' => 'required|min:2|max:100',
            'guardian_contact' => 'required|min:10|max:15',
            'village_address' => 'required|max:255',
            'permanent_address' => 'required|max:255'
        ]);

        if (!$this->validator->validate()) {
            $this->session->setFlash('message', 'Please correct the errors below.');
            $this->session->setFlash('message_type', 'danger');
            $this->session->setFlash('errors', $this->validator->getErrors());
            $this->session->setFlash('old_input', $data);
            $this->redirect('/admin/students/create');
        }

        // Handle file upload for photo
        $photoPath = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photoPath = $this->handleFileUpload($_FILES['photo'], 'uploads/students/');
            if (!$photoPath) {
                $this->session->setFlash('message', 'Failed to upload photo.');
                $this->session->setFlash('message_type', 'danger');
                $this->redirect('/admin/students/create');
            }
        }

        // Prepare data for insertion
        $studentData = $this->validator->getValidatedData();
        $studentData['photo_path'] = $photoPath;
        $studentData['is_active'] = 1;

        // Create student
        $studentId = $this->studentModel->create($studentData);

        if ($studentId) {
            $this->logAction('student_created', ['student_id' => $studentId]);
            $this->session->setFlash('message', 'Student created successfully.');
            $this->session->setFlash('message_type', 'success');
            $this->redirect('/admin/students');
        } else {
            $this->session->setFlash('message', 'Failed to create student.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/students/create');
        }
    }

    /**
     * Show student details
     */
    public function showStudent($id) {
        $student = $this->studentModel->findWithClass($id);

        if (!$student) {
            $this->session->setFlash('message', 'Student not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/students');
        }

        $this->view('admin/students/show', [
            'title' => 'Student Details',
            'page_title' => 'Student Details',
            'student' => $student,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Students', 'url' => '/admin/students'],
                ['title' => htmlspecialchars($student['first_name'] . ' ' . $student['last_name']), 'active' => true]
            ]
        ]);
    }

    /**
     * Edit student form
     */
    public function editStudent($id) {
        $student = $this->studentModel->find($id);

        if (!$student) {
            $this->session->setFlash('message', 'Student not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/students');
        }

        $csrf_token = $this->session->generateCsrfToken();

        $this->view('admin/students/edit', [
            'title' => 'Edit Student',
            'page_title' => 'Edit Student',
            'student' => $student,
            'csrf_token' => $csrf_token,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Students', 'url' => '/admin/students'],
                ['title' => 'Edit', 'active' => true]
            ]
        ]);
    }

    /**
     * Update student
     */
    public function updateStudent($id) {
        $this->validateCsrf();

        $student = $this->studentModel->find($id);
        if (!$student) {
            $this->session->setFlash('message', 'Student not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/students');
        }

        $data = $this->getPostData();

        // Validate input
        $this->validator->setData($data);
        $this->validator->setRules([
            'scholar_number' => 'required|unique:students,scholar_number,' . $id,
            'admission_number' => 'required|unique:students,admission_number,' . $id,
            'admission_date' => 'required|date',
            'first_name' => 'required|min:2|max:50',
            'last_name' => 'required|min:2|max:50',
            'class_id' => 'required|integer',
            'section' => 'required|max:10',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'father_name' => 'required|min:2|max:100',
            'mother_name' => 'required|min:2|max:100',
            'guardian_contact' => 'required|min:10|max:15',
            'village_address' => 'required|max:255',
            'permanent_address' => 'required|max:255'
        ]);

        if (!$this->validator->validate()) {
            $this->session->setFlash('message', 'Please correct the errors below.');
            $this->session->setFlash('message_type', 'danger');
            $this->session->setFlash('errors', $this->validator->getErrors());
            $this->redirect('/admin/students/' . $id . '/edit');
        }

        // Handle file upload for photo
        $photoPath = $student['photo_path'];
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $newPhotoPath = $this->handleFileUpload($_FILES['photo'], 'uploads/students/');
            if ($newPhotoPath) {
                // Delete old photo if exists
                if ($photoPath && file_exists($photoPath)) {
                    unlink($photoPath);
                }
                $photoPath = $newPhotoPath;
            }
        }

        // Prepare data for update
        $studentData = $this->validator->getValidatedData();
        $studentData['photo_path'] = $photoPath;

        // Update student
        if ($this->studentModel->update($id, $studentData)) {
            $this->logAction('student_updated', ['student_id' => $id]);
            $this->session->setFlash('message', 'Student updated successfully.');
            $this->session->setFlash('message_type', 'success');
            $this->redirect('/admin/students/' . $id);
        } else {
            $this->session->setFlash('message', 'Failed to update student.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/students/' . $id . '/edit');
        }
    }

    /**
     * Delete student
     */
    public function deleteStudent($id) {
        $student = $this->studentModel->find($id);
        if (!$student) {
            $this->json(['success' => false, 'message' => 'Student not found'], 404);
        }

        // Soft delete by setting is_active to false
        if ($this->studentModel->update($id, ['is_active' => 0])) {
            $this->logAction('student_deleted', ['student_id' => $id]);
            $this->json(['success' => true, 'message' => 'Student deleted successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to delete student'], 500);
        }
    }

    /**
     * Handle file upload
     */
    private function handleFileUpload($file, $uploadDir) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }

        if ($file['size'] > $maxSize) {
            return false;
        }

        // Create upload directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid() . '_' . basename($file['name']);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return $filePath;
        }

        return false;
    }

    // Class CRUD Methods

    /**
     * Create class form
     */
    public function createClass() {
        $csrf_token = $this->session->generateCsrfToken();
        $availableTeachers = $this->classModel->getAvailableTeachers();

        $this->view('admin/classes/create', [
            'title' => 'Add New Class',
            'page_title' => 'Add New Class',
            'csrf_token' => $csrf_token,
            'available_teachers' => $availableTeachers,
            'current_year' => date('Y') . '-' . (date('Y') + 1),
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Classes & Subjects', 'url' => '/admin/classes'],
                ['title' => 'Add New', 'active' => true]
            ]
        ]);
    }

    /**
     * Store new class
     */
    public function storeClass() {
        $this->validateCsrf();

        $data = $this->getPostData();

        // Validate input
        $this->validator->setData($data);
        $this->validator->setRules([
            'name' => 'required|max:50',
            'section' => 'required|max:10',
            'academic_year' => 'required',
            'class_teacher_id' => 'integer'
        ]);

        if (!$this->validator->validate()) {
            $this->session->setFlash('message', 'Please correct the errors below.');
            $this->session->setFlash('message_type', 'danger');
            $this->session->setFlash('errors', $this->validator->getErrors());
            $this->session->setFlash('old_input', $data);
            $this->redirect('/admin/classes/create');
        }

        // Check if class already exists
        if ($this->classModel->classExists($data['name'], $data['section'], $data['academic_year'])) {
            $this->session->setFlash('message', 'A class with this name and section already exists for the selected academic year.');
            $this->session->setFlash('message_type', 'danger');
            $this->session->setFlash('old_input', $data);
            $this->redirect('/admin/classes/create');
        }

        // Prepare data for insertion
        $classData = $this->validator->getValidatedData();

        // Create class
        $classId = $this->classModel->create($classData);

        if ($classId) {
            $this->logAction('class_created', ['class_id' => $classId]);
            $this->session->setFlash('message', 'Class created successfully.');
            $this->session->setFlash('message_type', 'success');
            $this->redirect('/admin/classes');
        } else {
            $this->session->setFlash('message', 'Failed to create class.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/classes/create');
        }
    }

    /**
     * Show class details
     */
    public function showClass($id) {
        $class = $this->classModel->findWithTeacher($id);

        if (!$class) {
            $this->session->setFlash('message', 'Class not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/classes');
        }

        $subjects = $this->classModel->getClassSubjects($id);
        $studentCount = $this->classModel->getStudentCount($id);

        $this->view('admin/classes/show', [
            'title' => 'Class Details',
            'page_title' => 'Class Details',
            'class' => $class,
            'subjects' => $subjects,
            'student_count' => $studentCount,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Classes & Subjects', 'url' => '/admin/classes'],
                ['title' => htmlspecialchars($class['name'] . ' ' . $class['section']), 'active' => true]
            ]
        ]);
    }

    /**
     * Edit class form
     */
    public function editClass($id) {
        $class = $this->classModel->find($id);

        if (!$class) {
            $this->session->setFlash('message', 'Class not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/classes');
        }

        $csrf_token = $this->session->generateCsrfToken();
        $availableTeachers = $this->classModel->getAvailableTeachers();

        $this->view('admin/classes/edit', [
            'title' => 'Edit Class',
            'page_title' => 'Edit Class',
            'class' => $class,
            'csrf_token' => $csrf_token,
            'available_teachers' => $availableTeachers,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Classes & Subjects', 'url' => '/admin/classes'],
                ['title' => 'Edit', 'active' => true]
            ]
        ]);
    }

    /**
     * Update class
     */
    public function updateClass($id) {
        $this->validateCsrf();

        $class = $this->classModel->find($id);
        if (!$class) {
            $this->session->setFlash('message', 'Class not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/classes');
        }

        $data = $this->getPostData();

        // Validate input
        $this->validator->setData($data);
        $this->validator->setRules([
            'name' => 'required|max:50',
            'section' => 'required|max:10',
            'academic_year' => 'required',
            'class_teacher_id' => 'integer'
        ]);

        if (!$this->validator->validate()) {
            $this->session->setFlash('message', 'Please correct the errors below.');
            $this->session->setFlash('message_type', 'danger');
            $this->session->setFlash('errors', $this->validator->getErrors());
            $this->redirect('/admin/classes/' . $id . '/edit');
        }

        // Check if class already exists (excluding current)
        if ($this->classModel->classExists($data['name'], $data['section'], $data['academic_year'], $id)) {
            $this->session->setFlash('message', 'A class with this name and section already exists for the selected academic year.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/classes/' . $id . '/edit');
        }

        // Prepare data for update
        $classData = $this->validator->getValidatedData();

        // Update class
        if ($this->classModel->update($id, $classData)) {
            $this->logAction('class_updated', ['class_id' => $id]);
            $this->session->setFlash('message', 'Class updated successfully.');
            $this->session->setFlash('message_type', 'success');
            $this->redirect('/admin/classes/' . $id);
        } else {
            $this->session->setFlash('message', 'Failed to update class.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/classes/' . $id . '/edit');
        }
    }

    /**
     * Delete class
     */
    public function deleteClass($id) {
        $class = $this->classModel->find($id);
        if (!$class) {
            $this->json(['success' => false, 'message' => 'Class not found'], 404);
        }

        // Check if class has students
        $studentCount = $this->classModel->getStudentCount($id);
        if ($studentCount > 0) {
            $this->json(['success' => false, 'message' => 'Cannot delete class with enrolled students'], 400);
        }

        // Delete class
        if ($this->classModel->delete($id)) {
            $this->logAction('class_deleted', ['class_id' => $id]);
            $this->json(['success' => true, 'message' => 'Class deleted successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to delete class'], 500);
        }
    }

    // Subject CRUD Methods

    /**
     * Create subject form
     */
    public function createSubject() {
        $csrf_token = $this->session->generateCsrfToken();

        $this->view('admin/subjects/create', [
            'title' => 'Add New Subject',
            'page_title' => 'Add New Subject',
            'csrf_token' => $csrf_token,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false,
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Classes & Subjects', 'url' => '/admin/classes'],
                ['title' => 'Add Subject', 'active' => true]
            ]
        ]);
    }

    /**
     * Store new subject
     */
    public function storeSubject() {
        $this->validateCsrf();

        $data = $this->getPostData();

        // Validate input
        $this->validator->setData($data);
        $this->validator->setRules([
            'name' => 'required|max:100',
            'code' => 'required|max:20|unique:subjects',
            'description' => 'max:255'
        ]);

        if (!$this->validator->validate()) {
            $this->session->setFlash('message', 'Please correct the errors below.');
            $this->session->setFlash('message_type', 'danger');
            $this->session->setFlash('errors', $this->validator->getErrors());
            $this->session->setFlash('old_input', $data);
            $this->redirect('/admin/subjects/create');
        }

        // Prepare data for insertion
        $subjectData = $this->validator->getValidatedData();

        // Create subject
        $subjectId = $this->subjectModel->create($subjectData);

        if ($subjectId) {
            $this->logAction('subject_created', ['subject_id' => $subjectId]);
            $this->session->setFlash('message', 'Subject created successfully.');
            $this->session->setFlash('message_type', 'success');
            $this->redirect('/admin/classes');
        } else {
            $this->session->setFlash('message', 'Failed to create subject.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/admin/subjects/create');
        }
    }

    /**
     * Assign subject to class
     */
    public function assignSubjectToClass() {
        $this->validateCsrf();

        $data = $this->getPostData();

        $this->validator->setData($data);
        $this->validator->setRules([
            'subject_id' => 'required|integer|exists:subjects,id',
            'class_id' => 'required|integer|exists:classes,id'
        ]);

        if (!$this->validator->validate()) {
            $this->json(['success' => false, 'message' => 'Invalid data provided'], 400);
        }

        $result = $this->subjectModel->assignToClass($data['subject_id'], $data['class_id']);

        if ($result) {
            $this->logAction('subject_assigned', ['subject_id' => $data['subject_id'], 'class_id' => $data['class_id']]);
            $this->json(['success' => true, 'message' => 'Subject assigned to class successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to assign subject'], 500);
        }
    }

    /**
     * Remove subject from class
     */
    public function removeSubjectFromClass() {
        $this->validateCsrf();

        $data = $this->getPostData();

        $this->validator->setData($data);
        $this->validator->setRules([
            'subject_id' => 'required|integer',
            'class_id' => 'required|integer'
        ]);

        if (!$this->validator->validate()) {
            $this->json(['success' => false, 'message' => 'Invalid data provided'], 400);
        }

        if ($this->subjectModel->removeFromClass($data['subject_id'], $data['class_id'])) {
            $this->logAction('subject_removed', ['subject_id' => $data['subject_id'], 'class_id' => $data['class_id']]);
            $this->json(['success' => true, 'message' => 'Subject removed from class successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to remove subject'], 500);
        }
    }
}
?>