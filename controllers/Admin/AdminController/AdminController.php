<?php
/**
 * Admin Controller
 *
 * Handles admin-specific operations
 */

class AdminController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole('admin');
    }

    public function index() {
        $this->dashboard();
    }

    public function dashboard() {
        // Check if academic year is set
        $academicYearId = $this->getCurrentAcademicYear();
        if (!$academicYearId) {
            $this->redirect('/admin/select-academic-year');
        }

        // Get dashboard statistics
        $stats = $this->getDashboardStats();

        $data = [
            'stats' => $stats,
            'academic_year' => $this->getAcademicYearInfo($academicYearId),
            'recent_activities' => $this->getRecentActivities(),
            'upcoming_events' => $this->getUpcomingEvents()
        ];

        $this->view('admin/dashboard', $data);
    }

    public function selectAcademicYear() {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();

            if (isset($postData['academic_year_id']) && !empty($postData['academic_year_id'])) {
                $academicYearId = (int)$postData['academic_year_id'];

                // Verify the academic year exists
                $year = $this->db->selectOne("SELECT id, year_name FROM academic_years WHERE id = ?", [$academicYearId]);

                if ($year) {
                    Session::setAcademicYear($academicYearId);
                    $this->logActivity('academic_year_selected', "Selected academic year: {$year['year_name']}");
                    $this->redirect('/admin/dashboard');
                } else {
                    $errors['academic_year'] = 'Invalid academic year selected';
                }
            } else {
                $errors['academic_year'] = 'Please select an academic year';
            }
        }

        // Get available academic years
        $academicYears = $this->db->select("SELECT id, year_name, is_active FROM academic_years ORDER BY start_date DESC");

        $data = [
            'academic_years' => $academicYears,
            'errors' => $errors,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/select_academic_year', $data);
    }

    public function students() {
        $studentModel = new Student();

        // Get students for current academic year
        $students = $studentModel->getForCurrentYear();

        $data = [
            'students' => $students,
            'academic_year' => $this->getAcademicYearInfo($this->getCurrentAcademicYear())
        ];

        $this->view('admin/students', $data);
    }

    public function classes() {
        $classModel = new ClassModel();
        $subjectModel = new Subject();

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
            $classModel = new ClassModel();

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

    public function subjects() {
        $subjectModel = new Subject();

        $subjects = $subjectModel->all();
        $subjectStats = $subjectModel->getStatistics();

        $data = [
            'subjects' => $subjects,
            'subject_stats' => $subjectStats,
            'academic_year' => $this->getAcademicYearInfo($this->getCurrentAcademicYear()),
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
            'subject_code' => 'required|min:2|max:20|alpha_num'
        ];

        $errors = Validator::validateData($postData, $validationRules);

        if (empty($errors)) {
            $subjectModel = new Subject();

            // Check if subject code already exists
            if ($subjectModel->isCodeExists($postData['subject_code'])) {
                $errors['code'] = 'Subject code already exists';
            } else {
                $subjectData = [
                    'subject_name' => $postData['subject_name'],
                    'subject_code' => strtoupper($postData['subject_code']),
                    'description' => $postData['description'] ?? '',
                    'status' => 'active'
                ];

                if ($subjectModel->create($subjectData)) {
                    $this->setFlash('success', 'Subject added successfully');
                    $this->logActivity('subject_added', "Added subject: {$postData['subject_name']} ({$postData['subject_code']})");
                } else {
                    $errors['db'] = 'Failed to add subject';
                }
            }
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode(', ', $errors));
        }

        $this->redirect('/admin/subjects');
    }

    public function assignSubjectToClass() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method'], 400);
        }

        $postData = $this->getPostData();

        if (empty($postData['class_id']) || empty($postData['subject_id'])) {
            $this->json(['error' => 'Class ID and Subject ID are required'], 400);
        }

        $subjectModel = new Subject();
        $result = $subjectModel->assignToClass(
            $postData['class_id'],
            $postData['subject_id'],
            !empty($postData['teacher_id']) ? $postData['teacher_id'] : null
        );

        if ($result) {
            $this->logActivity('subject_assigned', "Assigned subject {$postData['subject_id']} to class {$postData['class_id']}");
            $this->json(['success' => true, 'message' => 'Subject assigned successfully']);
        } else {
            $this->json(['error' => 'Failed to assign subject'], 500);
        }
    }

    public function attendance() {
        $attendanceModel = new Attendance();
        $classModel = new ClassModel();

        // Get attendance summary for today
        $attendanceStats = $attendanceModel->getDashboardStats();

        // Get class-wise summary
        $classSummary = $this->db->select(
            "SELECT c.class_name, c.section,
                    COUNT(DISTINCT s.id) as total_students,
                    SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_count,
                    SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_count
             FROM classes c
             JOIN students s ON c.id = s.class_id
             LEFT JOIN attendance a ON s.id = a.student_id AND a.attendance_date = CURDATE()
             WHERE c.academic_year_id = ? AND s.academic_year_id = ?
             GROUP BY c.id
             ORDER BY c.class_name, c.section",
            [$this->getCurrentAcademicYear(), $this->getCurrentAcademicYear()]
        );

        $data = [
            'attendance_stats' => $attendanceStats,
            'class_summary' => $classSummary,
            'academic_year' => $this->getAcademicYearInfo($this->getCurrentAcademicYear()),
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/attendance', $data);
    }

    public function markAttendance() {
        $classId = $_GET['class_id'] ?? null;
        $date = $_GET['date'] ?? date('Y-m-d');

        if (!$classId) {
            $this->redirect('/admin/attendance');
        }

        $attendanceModel = new Attendance();
        $studentModel = new Student();

        // Get class info
        $classModel = new ClassModel();
        $class = $classModel->getWithStudentCount($classId);

        if (!$class) {
            $this->setFlash('error', 'Class not found');
            $this->redirect('/admin/attendance');
        }

        // Get students in class
        $students = $studentModel->getByClass($classId);

        // Get existing attendance for this date
        $existingAttendance = $attendanceModel->getClassAttendance($classId, $date);

        // Create attendance map for easy lookup
        $attendanceMap = [];
        foreach ($existingAttendance as $att) {
            $attendanceMap[$att['student_id']] = $att['status'];
        }

        $data = [
            'class' => $class,
            'students' => $students,
            'attendance_date' => $date,
            'attendance_map' => $attendanceMap,
            'academic_year' => $this->getAcademicYearInfo($this->getCurrentAcademicYear()),
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/mark_attendance', $data);
    }

    public function saveAttendance() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/attendance');
        }

        $postData = $this->getPostData();

        $classId = $postData['class_id'] ?? null;
        $attendanceDate = $postData['attendance_date'] ?? null;
        $attendanceData = $postData['attendance'] ?? [];

        if (!$classId || !$attendanceDate) {
            $this->setFlash('error', 'Invalid request data');
            $this->redirect('/admin/attendance');
        }

        $attendanceModel = new Attendance();
        $result = $attendanceModel->markClassAttendance($classId, $attendanceDate, $attendanceData, Session::get('user_id'));

        if ($result) {
            $this->setFlash('success', 'Attendance marked successfully');
            $this->logActivity('attendance_marked', "Marked attendance for class {$classId} on {$attendanceDate}");
        } else {
            $this->setFlash('error', 'Failed to save attendance');
        }

        $this->redirect('/admin/attendance');
    }

    public function attendanceReport() {
        $classId = $_GET['class_id'] ?? null;
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');

        if (!$classId) {
            $this->redirect('/admin/attendance');
        }

        $attendanceModel = new Attendance();
        $classModel = new ClassModel();

        $class = $classModel->find($classId);
        $report = $attendanceModel->getClassReport($classId, $startDate, $endDate);

        $data = [
            'class' => $class,
            'report' => $report,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'academic_year' => $this->getAcademicYearInfo($this->getCurrentAcademicYear())
        ];

        $this->view('admin/attendance_report', $data);
    }

    public function exams() {
        $examModel = new Exam();

        $exams = $examModel->getForCurrentYear();

        $data = [
            'exams' => $exams,
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

        // Basic validation
        if (empty($postData['exam_name']) || empty($postData['class_id']) ||
            empty($postData['start_date']) || empty($postData['subjects'])) {
            $this->setFlash('error', 'All required fields must be filled');
            $this->redirect('/admin/exams');
        }

        $examModel = new Exam();

        try {
            $examData = [
                'exam_name' => $postData['exam_name'],
                'exam_type' => $postData['exam_type'] ?? 'custom',
                'class_id' => $postData['class_id'],
                'start_date' => $postData['start_date'],
                'end_date' => $postData['end_date'] ?? $postData['start_date'],
                'status' => 'upcoming'
            ];

            $exam = $examModel->createWithSubjects($examData, $postData['subjects']);

            $this->setFlash('success', 'Exam created successfully');
            $this->logActivity('exam_created', "Created exam: {$postData['exam_name']}");
        } catch (Exception $e) {
            $this->setFlash('error', 'Failed to create exam: ' . $e->getMessage());
        }

        $this->redirect('/admin/exams');
    }

    public function enterResults() {
        $examId = $_GET['exam_id'] ?? null;

        if (!$examId) {
            $this->redirect('/admin/exams');
        }

        $examModel = new Exam();
        $exam = $examModel->getWithSubjects($examId);
        $students = $examModel->getExamStudents($examId);

        if (!$exam) {
            $this->setFlash('error', 'Exam not found');
            $this->redirect('/admin/exams');
        }

        $data = [
            'exam' => $exam,
            'students' => $students,
            'academic_year' => $this->getAcademicYearInfo($this->getCurrentAcademicYear()),
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/enter_results', $data);
    }

    public function saveResults() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/exams');
        }

        $postData = $this->getPostData();
        $examId = $postData['exam_id'] ?? null;

        if (!$examId) {
            $this->setFlash('error', 'Invalid request');
            $this->redirect('/admin/exams');
        }

        $examModel = new Exam();
        $resultsData = [];

        // Process results data
        foreach ($postData as $key => $value) {
            if (strpos($key, 'marks_') === 0) {
                $parts = explode('_', $key);
                if (count($parts) === 3) {
                    $studentId = $parts[1];
                    $subjectId = $parts[2];

                    if (!isset($resultsData[$studentId])) {
                        $resultsData[$studentId] = [];
                    }

                    $resultsData[$studentId][] = [
                        'subject_id' => $subjectId,
                        'marks_obtained' => $value,
                        'grade' => $postData["grade_{$studentId}_{$subjectId}"] ?? null,
                        'remarks' => $postData["remarks_{$studentId}_{$subjectId}"] ?? ''
                    ];
                }
            }
        }

        if ($examModel->saveResults($examId, $resultsData)) {
            $this->setFlash('success', 'Results saved successfully');
            $this->logActivity('results_saved', "Saved results for exam ID: {$examId}");
        } else {
            $this->setFlash('error', 'Failed to save results');
        }

        $this->redirect('/admin/enter-results?exam_id=' . $examId);
    }

    public function generateAdmitCard() {
        $examId = $_GET['exam_id'] ?? null;
        $studentId = $_GET['student_id'] ?? null;

        if (!$examId || !$studentId) {
            $this->setFlash('error', 'Invalid request');
            $this->redirect('/admin/exams');
        }

        $examModel = new Exam();
        $data = $examModel->generateAdmitCard($examId, $studentId);

        if (!$data) {
            $this->setFlash('error', 'Admit card data not found');
            $this->redirect('/admin/exams');
        }

        $this->view('admin/admit_card', $data);
    }

    public function generateMarksheet() {
        $examId = $_GET['exam_id'] ?? null;
        $studentId = $_GET['student_id'] ?? null;

        if (!$examId || !$studentId) {
            $this->setFlash('error', 'Invalid request');
            $this->redirect('/admin/exams');
        }

        $examModel = new Exam();
        $data = $examModel->generateMarksheet($examId, $studentId);

        if (!$data) {
            $this->setFlash('error', 'Marksheet data not found');
            $this->redirect('/admin/exams');
        }

        $this->view('admin/marksheet', $data);
    }

    public function fees() {
        $feeModel = new Fee();

        // Get fee collection summary
        $feeSummary = $feeModel->getCollectionSummary();

        $data = [
            'fee_summary' => $feeSummary,
            'academic_year' => $this->getAcademicYearInfo($this->getCurrentAcademicYear())
        ];

        $this->view('admin/fees', $data);
    }

    public function events() {
        $academicYearId = $this->getCurrentAcademicYear();

        $events = $this->db->select(
            "SELECT e.*, CONCAT(u.first_name, ' ', u.last_name) as created_by_name
             FROM events e
             LEFT JOIN user_profiles u ON e.created_by = u.user_id
             WHERE e.academic_year_id = ? OR e.academic_year_id IS NULL
             ORDER BY e.event_date DESC",
            [$academicYearId]
        );

        $data = [
            'events' => $events,
            'academic_year' => $this->getAcademicYearInfo($academicYearId)
        ];

        $this->view('admin/events', $data);
    }

    public function gallery() {
        $academicYearId = $this->getCurrentAcademicYear();

        $gallery = $this->db->select(
            "SELECT g.*, e.title as event_title,
                    CONCAT(u.first_name, ' ', u.last_name) as uploaded_by_name
             FROM gallery g
             LEFT JOIN events e ON g.event_id = e.id
             LEFT JOIN user_profiles u ON g.uploaded_by = u.user_id
             WHERE g.academic_year_id = ? OR g.academic_year_id IS NULL
             ORDER BY g.created_at DESC",
            [$academicYearId]
        );

        $data = [
            'gallery' => $gallery,
            'academic_year' => $this->getAcademicYearInfo($academicYearId)
        ];

        $this->view('admin/gallery', $data);
    }

    public function reports() {
        $academicYearId = $this->getCurrentAcademicYear();

        $data = [
            'academic_year' => $this->getAcademicYearInfo($academicYearId),
            'report_types' => [
                'students' => 'Student Report',
                'attendance' => 'Attendance Report',
                'fees' => 'Fee Collection Report',
                'exams' => 'Examination Report'
            ]
        ];

        $this->view('admin/reports', $data);
    }

    public function settings() {
        $academicYearId = $this->getCurrentAcademicYear();

        // Get current settings
        $settings = $this->db->select("SELECT * FROM settings WHERE is_public = 0 ORDER BY setting_group, setting_key");

        $data = [
            'settings' => $settings,
            'academic_year' => $this->getAcademicYearInfo($academicYearId)
        ];

        $this->view('admin/settings', $data);
    }

    private function getDashboardStats() {
        $academicYearId = $this->getCurrentAcademicYear();

        return [
            'total_students' => $this->db->selectOne("SELECT COUNT(*) as count FROM students WHERE academic_year_id = ?", [$academicYearId])['count'],
            'total_classes' => $this->db->selectOne("SELECT COUNT(*) as count FROM classes WHERE academic_year_id = ?", [$academicYearId])['count'],
            'total_exams' => $this->db->selectOne("SELECT COUNT(*) as count FROM exams WHERE academic_year_id = ?", [$academicYearId])['count'],
            'total_events' => $this->db->selectOne("SELECT COUNT(*) as count FROM events WHERE academic_year_id = ? OR academic_year_id IS NULL", [$academicYearId])['count'],
            'pending_fees' => $this->db->selectOne("
                SELECT SUM(f.amount - COALESCE(fp.amount_paid, 0)) as pending
                FROM fees f
                LEFT JOIN fee_payments fp ON f.id = fp.fee_id
                WHERE f.academic_year_id = ?
            ", [$academicYearId])['pending'] ?? 0
        ];
    }

    private function getAcademicYearInfo($academicYearId) {
        return $this->db->selectOne("SELECT * FROM academic_years WHERE id = ?", [$academicYearId]);
    }

    private function getRecentActivities() {
        return $this->db->select(
            "SELECT al.*, CONCAT(u.first_name, ' ', u.last_name) as user_name
             FROM audit_logs al
             LEFT JOIN user_profiles u ON al.user_id = u.user_id
             ORDER BY al.created_at DESC
             LIMIT 10"
        );
    }

    private function getUpcomingEvents() {
        $academicYearId = $this->getCurrentAcademicYear();

        return $this->db->select(
            "SELECT * FROM events
             WHERE (academic_year_id = ? OR academic_year_id IS NULL)
             AND event_date >= CURDATE()
             ORDER BY event_date ASC
             LIMIT 5",
            [$academicYearId]
        );
    }
}
?>