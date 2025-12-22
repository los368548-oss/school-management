<?php
/**
 * Student Controller
 *
 * Handles student-specific operations and portal functionality
 */

class StudentController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole('student');
    }

    public function index() {
        $this->dashboard();
    }

    public function dashboard() {
        $user = Security::getCurrentUser();
        $studentModel = new Student();
        $examModel = new Exam();

        // Get student details
        $student = $studentModel->getWithDetails($user['id']);
        if (!$student) {
            $this->setFlash('error', 'Student profile not found');
            $this->redirect('/login');
        }

        // Get attendance summary
        $attendanceSummary = $studentModel->getAttendanceSummary($student->id);

        // Get recent exam results
        $recentResults = $this->db->select(
            "SELECT er.*, s.subject_name, e.exam_name,
                    es.max_marks, es.passing_marks
             FROM exam_results er
             JOIN subjects s ON er.subject_id = s.id
             JOIN exams e ON er.exam_id = e.id
             JOIN exam_subjects es ON er.exam_id = es.exam_id AND er.subject_id = es.subject_id
             WHERE er.student_id = ?
             ORDER BY er.marked_at DESC
             LIMIT 5",
            [$student->id]
        );

        // Get upcoming exams
        $upcomingExams = $examModel->getUpcomingExams();

        // Get fee status
        $feeStatus = $this->getStudentFeeStatus($student->id);

        $data = [
            'student' => $student,
            'attendance_summary' => $attendanceSummary,
            'recent_results' => $recentResults,
            'upcoming_exams' => $upcomingExams,
            'fee_status' => $feeStatus,
            'academic_year' => $this->getAcademicYearInfo($this->getCurrentAcademicYear())
        ];

        $this->view('student/dashboard', $data);
    }

    public function profile() {
        $user = Security::getCurrentUser();
        $studentModel = new Student();

        $student = $studentModel->getWithDetails($user['id']);
        if (!$student) {
            $this->setFlash('error', 'Student profile not found');
            $this->redirect('/student/dashboard');
        }

        $data = [
            'student' => $student,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('student/profile', $data);
    }

    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/student/profile');
        }

        $user = Security::getCurrentUser();
        $postData = $this->getPostData();

        // Basic validation
        $validationRules = [
            'mobile_number' => 'max:15',
            'email' => 'email|max:100'
        ];

        $errors = Validator::validateData($postData, $validationRules);

        if (!empty($errors)) {
            $this->setFlash('error', 'Validation failed: ' . implode(', ', array_map('implode', $errors)));
            $this->redirect('/student/profile');
        }

        try {
            // Update user profile
            $userData = [];
            if (!empty($postData['email'])) {
                $userData['email'] = $postData['email'];
            }

            if (!empty($userData)) {
                $this->db->update('users', $userData, 'id = ?', [$user['id']]);
            }

            // Update user profile details
            $profileData = array_intersect_key($postData, array_flip([
                'first_name', 'last_name', 'phone', 'address', 'date_of_birth', 'gender'
            ]));

            if (!empty($profileData)) {
                $this->db->update('user_profiles', $profileData, 'user_id = ?', [$user['id']]);
            }

            // Update student details
            $studentData = array_intersect_key($postData, array_flip([
                'mobile_number', 'email', 'address', 'permanent_address'
            ]));

            if (!empty($studentData)) {
                $studentModel = new Student();
                $student = $studentModel->where(['user_id' => $user['id']]);
                if (!empty($student)) {
                    $studentModel->update($student[0]->id, $studentData);
                }
            }

            $this->setFlash('success', 'Profile updated successfully');
            $this->logActivity('profile_updated', "Student profile updated");
        } catch (Exception $e) {
            $this->setFlash('error', 'Failed to update profile: ' . $e->getMessage());
        }

        $this->redirect('/student/profile');
    }

    public function attendance() {
        $user = Security::getCurrentUser();
        $studentModel = new Student();

        $student = $studentModel->where(['user_id' => $user['id']]);
        if (empty($student)) {
            $this->setFlash('error', 'Student profile not found');
            $this->redirect('/student/dashboard');
        }

        $student = $student[0];

        // Get attendance records
        $attendanceRecords = $this->db->select(
            "SELECT a.*, DATE_FORMAT(a.attendance_date, '%d/%m/%Y') as formatted_date,
                    c.class_name, c.section
             FROM attendance a
             JOIN classes c ON a.class_id = c.id
             WHERE a.student_id = ? AND a.academic_year_id = ?
             ORDER BY a.attendance_date DESC",
            [$student->id, $this->getCurrentAcademicYear()]
        );

        // Calculate attendance statistics
        $totalDays = count($attendanceRecords);
        $presentDays = count(array_filter($attendanceRecords, function($record) {
            return $record['status'] === 'present';
        }));
        $absentDays = count(array_filter($attendanceRecords, function($record) {
            return $record['status'] === 'absent';
        }));

        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;

        $data = [
            'student' => $student,
            'attendance_records' => $attendanceRecords,
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'attendance_percentage' => $attendancePercentage,
            'academic_year' => $this->getAcademicYearInfo($this->getCurrentAcademicYear())
        ];

        $this->view('student/attendance', $data);
    }

    public function results() {
        $user = Security::getCurrentUser();
        $studentModel = new Student();

        $student = $studentModel->where(['user_id' => $user['id']]);
        if (empty($student)) {
            $this->setFlash('error', 'Student profile not found');
            $this->redirect('/student/dashboard');
        }

        $student = $student[0];

        // Get exam results
        $examResults = $this->db->select(
            "SELECT e.exam_name, e.exam_type, s.subject_name, s.subject_code,
                    er.marks_obtained, er.grade, er.remarks,
                    es.max_marks, es.passing_marks,
                    DATE_FORMAT(er.marked_at, '%d/%m/%Y') as result_date
             FROM exam_results er
             JOIN exams e ON er.exam_id = e.id
             JOIN subjects s ON er.subject_id = s.id
             JOIN exam_subjects es ON er.exam_id = es.exam_id AND er.subject_id = es.subject_id
             WHERE er.student_id = ?
             ORDER BY e.start_date DESC, s.subject_name ASC",
            [$student->id]
        );

        // Group results by exam
        $groupedResults = [];
        foreach ($examResults as $result) {
            $examName = $result['exam_name'];
            if (!isset($groupedResults[$examName])) {
                $groupedResults[$examName] = [
                    'exam_type' => $result['exam_type'],
                    'result_date' => $result['result_date'],
                    'subjects' => []
                ];
            }
            $groupedResults[$examName]['subjects'][] = $result;
        }

        $data = [
            'student' => $student,
            'grouped_results' => $groupedResults,
            'academic_year' => $this->getAcademicYearInfo($this->getCurrentAcademicYear())
        ];

        $this->view('student/results', $data);
    }

    public function fees() {
        $user = Security::getCurrentUser();
        $studentModel = new Student();

        $student = $studentModel->where(['user_id' => $user['id']]);
        if (empty($student)) {
            $this->setFlash('error', 'Student profile not found');
            $this->redirect('/student/dashboard');
        }

        $student = $student[0];

        // Get fee payments
        $feePayments = $this->db->select(
            "SELECT fp.*, f.fee_name, f.fee_type,
                    DATE_FORMAT(fp.payment_date, '%d/%m/%Y') as formatted_date
             FROM fee_payments fp
             JOIN fees f ON fp.fee_id = f.id
             WHERE fp.student_id = ?
             ORDER BY fp.payment_date DESC",
            [$student->id]
        );

        // Get outstanding fees
        $outstandingFees = $this->getStudentOutstandingFees($student->id);

        $data = [
            'student' => $student,
            'fee_payments' => $feePayments,
            'outstanding_fees' => $outstandingFees,
            'academic_year' => $this->getAcademicYearInfo($this->getCurrentAcademicYear())
        ];

        $this->view('student/fees', $data);
    }

    private function getStudentFeeStatus($studentId) {
        $academicYearId = $this->getCurrentAcademicYear();

        $feeStatus = $this->db->selectOne(
            "SELECT
                SUM(f.amount) as total_fees,
                COALESCE(SUM(fp.amount_paid), 0) as paid_amount,
                (SUM(f.amount) - COALESCE(SUM(fp.amount_paid), 0)) as pending_amount
             FROM fees f
             LEFT JOIN fee_payments fp ON f.id = fp.fee_id AND fp.student_id = ?
             WHERE f.academic_year_id = ?",
            [$studentId, $academicYearId]
        );

        return $feeStatus ?: ['total_fees' => 0, 'paid_amount' => 0, 'pending_amount' => 0];
    }

    private function getStudentOutstandingFees($studentId) {
        $academicYearId = $this->getCurrentAcademicYear();

        return $this->db->select(
            "SELECT f.fee_name, f.fee_type, f.amount,
                    COALESCE(SUM(fp.amount_paid), 0) as paid_amount,
                    (f.amount - COALESCE(SUM(fp.amount_paid), 0)) as pending_amount,
                    DATE_FORMAT(f.due_date, '%d/%m/%Y') as due_date
             FROM fees f
             LEFT JOIN fee_payments fp ON f.id = fp.fee_id AND fp.student_id = ?
             WHERE f.academic_year_id = ?
             GROUP BY f.id
             HAVING pending_amount > 0
             ORDER BY f.due_date ASC",
            [$studentId, $academicYearId]
        );
    }
}
?>