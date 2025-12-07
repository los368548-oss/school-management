<?php
/**
 * Student Controller
 */

class StudentController extends BaseController {
    private $studentModel;
    private $attendanceModel;
    private $examModel;
    private $feeModel;
    private $eventModel;

    public function __construct() {
        parent::__construct();
        $this->requireStudent();

        // Initialize models
        $this->studentModel = new Student();
        $this->attendanceModel = new Attendance();
        $this->examModel = new Exam();
        $this->feeModel = new Fee();
        $this->eventModel = new Event();
    }

    /**
     * Student Dashboard
     */
    public function dashboard() {
        $studentId = $this->getCurrentUserId();
        $student = $this->studentModel->findByUserId($studentId);

        if (!$student) {
            $this->session->setFlash('message', 'Student profile not found. Please contact administrator.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/logout');
        }

        // Get dashboard data
        $attendanceStats = $this->getAttendanceStats($student['id']);
        $recentResults = $this->examModel->getStudentResults($student['id']);
        $feeStatus = $this->feeModel->getStudentFeeStatus($student['id']);
        $upcomingEvents = $this->eventModel->getUpcomingEvents(5);
        $recentAttendance = $this->getRecentAttendance($student['id']);

        $this->view('student/dashboard', [
            'title' => 'Student Dashboard',
            'page_title' => 'My Dashboard',
            'student' => $student,
            'attendance_stats' => $attendanceStats,
            'recent_results' => array_slice($recentResults, 0, 5),
            'fee_status' => $feeStatus,
            'upcoming_events' => $upcomingEvents,
            'recent_attendance' => $recentAttendance,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false
        ]);
    }

    /**
     * Student Profile
     */
    public function profile() {
        $studentId = $this->getCurrentUserId();
        $student = $this->studentModel->findByUserId($studentId);

        if (!$student) {
            $this->session->setFlash('message', 'Student profile not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/student/dashboard');
        }

        $csrf_token = $this->session->generateCsrfToken();

        $this->view('student/profile', [
            'title' => 'My Profile',
            'page_title' => 'My Profile',
            'student' => $student,
            'csrf_token' => $csrf_token,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false
        ]);
    }

    /**
     * Update Student Profile
     */
    public function updateProfile() {
        $this->validateCsrf();

        $studentId = $this->getCurrentUserId();
        $student = $this->studentModel->findByUserId($studentId);

        if (!$student) {
            $this->session->setFlash('message', 'Student profile not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/student/profile');
        }

        $data = $this->getPostData();

        // Validate input
        $this->validator->setData($data);
        $this->validator->setRules([
            'mobile_number' => 'max:15',
            'email' => 'email|max:100',
            'village_address' => 'max:500',
            'guardian_contact' => 'max:15'
        ]);

        if (!$this->validator->validate()) {
            $this->session->setFlash('message', 'Please correct the errors below.');
            $this->session->setFlash('message_type', 'danger');
            $this->session->setFlash('errors', $this->validator->getErrors());
            $this->session->setFlash('old_input', $data);
            $this->redirect('/student/profile');
        }

        // Update allowed fields
        $updateData = [
            'mobile_number' => $data['mobile_number'] ?? $student['mobile_number'],
            'email' => $data['email'] ?? $student['email'],
            'village_address' => $data['village_address'] ?? $student['village_address'],
            'guardian_contact' => $data['guardian_contact'] ?? $student['guardian_contact']
        ];

        if ($this->studentModel->update($student['id'], $updateData)) {
            $this->logAction('profile_updated', ['student_id' => $student['id']]);
            $this->session->setFlash('message', 'Profile updated successfully.');
            $this->session->setFlash('message_type', 'success');
        } else {
            $this->session->setFlash('message', 'Failed to update profile.');
            $this->session->setFlash('message_type', 'danger');
        }

        $this->redirect('/student/profile');
    }

    /**
     * Student Attendance
     */
    public function attendance() {
        $studentId = $this->getCurrentUserId();
        $student = $this->studentModel->findByUserId($studentId);

        if (!$student) {
            $this->session->setFlash('message', 'Student profile not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/student/dashboard');
        }

        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');

        $attendanceCalendar = $this->attendanceModel->getStudentAttendanceCalendar($student['id'], $month, $year);
        $attendanceStats = $this->getAttendanceStats($student['id']);

        $this->view('student/attendance', [
            'title' => 'My Attendance',
            'page_title' => 'My Attendance',
            'student' => $student,
            'attendance_calendar' => $attendanceCalendar,
            'attendance_stats' => $attendanceStats,
            'month' => $month,
            'year' => $year,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false
        ]);
    }

    /**
     * Student Results
     */
    public function results() {
        $studentId = $this->getCurrentUserId();
        $student = $this->studentModel->findByUserId($studentId);

        if (!$student) {
            $this->session->setFlash('message', 'Student profile not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/student/dashboard');
        }

        $examResults = $this->examModel->getStudentResults($student['id']);

        $this->view('student/results', [
            'title' => 'My Results',
            'page_title' => 'My Exam Results',
            'student' => $student,
            'exam_results' => $examResults,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false
        ]);
    }

    /**
     * Student Fees
     */
    public function fees() {
        $studentId = $this->getCurrentUserId();
        $student = $this->studentModel->findByUserId($studentId);

        if (!$student) {
            $this->session->setFlash('message', 'Student profile not found.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/student/dashboard');
        }

        $feeStatus = $this->feeModel->getStudentFeeStatus($student['id']);
        $paymentHistory = $this->feeModel->getStudentPaymentHistory($student['id']);

        $this->view('student/fees', [
            'title' => 'My Fees',
            'page_title' => 'My Fee Information',
            'student' => $student,
            'fee_status' => $feeStatus,
            'payment_history' => $paymentHistory,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false
        ]);
    }

    /**
     * Student Events
     */
    public function events() {
        $upcomingEvents = $this->eventModel->getUpcomingEvents(20);

        $this->view('student/events', [
            'title' => 'School Events',
            'page_title' => 'School Events & Announcements',
            'upcoming_events' => $upcomingEvents,
            'show_header' => true,
            'show_sidebar' => true,
            'show_footer' => false
        ]);
    }

    /**
     * Get attendance statistics for student
     */
    private function getAttendanceStats($studentId) {
        // Get current month attendance
        $currentMonth = date('m');
        $currentYear = date('Y');

        $monthlyAttendance = $this->db->query("
            SELECT
                COUNT(*) as total_days,
                SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) as late_days
            FROM attendance
            WHERE student_id = ? AND MONTH(date) = ? AND YEAR(date) = ?
        ")->bind(1, $studentId)->bind(2, $currentMonth)->bind(3, $currentYear)->single();

        if ($monthlyAttendance) {
            $monthlyAttendance['percentage'] = $monthlyAttendance['total_days'] > 0
                ? round(($monthlyAttendance['present_days'] / $monthlyAttendance['total_days']) * 100, 2)
                : 0;
        } else {
            $monthlyAttendance = [
                'total_days' => 0,
                'present_days' => 0,
                'absent_days' => 0,
                'late_days' => 0,
                'percentage' => 0
            ];
        }

        return $monthlyAttendance;
    }

    /**
     * Get recent attendance records
     */
    private function getRecentAttendance($studentId, $limit = 10) {
        return $this->db->query("
            SELECT date, status
            FROM attendance
            WHERE student_id = ?
            ORDER BY date DESC
            LIMIT ?
        ")->bind(1, $studentId)->bind(2, $limit)->resultSet();
    }
}
?>