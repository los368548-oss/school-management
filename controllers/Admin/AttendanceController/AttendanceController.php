<?php
/**
 * Attendance Controller
 *
 * Handles attendance management operations
 */

class AttendanceController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole('admin');
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

    private function getAcademicYearInfo($academicYearId) {
        return $this->db->selectOne("SELECT * FROM academic_years WHERE id = ?", [$academicYearId]);
    }
}
?>