<?php
/**
 * API Controller
 * Handles API endpoints
 */

class ApiController extends BaseController {
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats() {
        $this->requireAdmin();

        $this->handleAjax(function() {
            $studentModel = new Student();
            $classModel = new ClassModel();
            $eventModel = new Event();
            $feeModel = new Fee();
            $attendanceModel = new Attendance();

            // Get real statistics
            $studentStats = $studentModel->getStudentStats();
            $totalClasses = $classModel->count();
            $totalTeachers = count($this->db->query("SELECT id FROM users WHERE role_id = 2")->resultSet()); // Assuming role_id 2 is teacher
            $totalEvents = $eventModel->count();

            // Today's attendance average
            $today = date('Y-m-d');
            $todayAttendance = $this->db->query("
                SELECT AVG(attendance_percentage) as avg_attendance
                FROM (
                    SELECT (SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) / COUNT(*)) * 100 as attendance_percentage
                    FROM attendance
                    WHERE date = ?
                    GROUP BY class_id
                ) as class_attendance
            ")->bind(1, $today)->single();

            $attendanceToday = $todayAttendance ? round($todayAttendance['avg_attendance'], 1) : 0;

            // Pending fees
            $pendingFees = $feeModel->getOverdueFees(0);
            $totalPendingFees = array_sum(array_column($pendingFees, 'pending_amount'));

            return [
                'students' => $studentStats['total_students'],
                'classes' => $totalClasses,
                'teachers' => $totalTeachers,
                'events' => $totalEvents,
                'attendance_today' => $attendanceToday,
                'fees_pending' => $totalPendingFees
            ];
        });
    }

    /**
     * Get attendance data
     */
    public function getAttendance() {
        $this->requireAdmin();

        $this->handleAjax(function() {
            $date = $_GET['date'] ?? date('Y-m-d');
            $class_id = $_GET['class_id'] ?? null;

            if (!$class_id) {
                throw new Exception('Class ID is required');
            }

            $attendanceModel = new Attendance();
            $studentModel = new Student();

            $students = $studentModel->getStudentsByClass($class_id);
            $existingAttendance = $attendanceModel->getClassAttendance($class_id, $date);

            // Create attendance map
            $attendanceMap = [];
            foreach ($existingAttendance as $att) {
                $attendanceMap[$att['student_id']] = $att;
            }

            // Merge with students
            $attendanceRecords = [];
            $presentCount = 0;
            $absentCount = 0;
            $lateCount = 0;

            foreach ($students as $student) {
                $attendance = $attendanceMap[$student['id']] ?? null;
                $status = $attendance ? $attendance['status'] : 'Present';

                $attendanceRecords[] = [
                    'student_id' => $student['id'],
                    'scholar_number' => $student['scholar_number'],
                    'student_name' => $student['first_name'] . ' ' . $student['last_name'],
                    'status' => $status,
                    'marked_time' => $attendance ? $attendance['created_at'] : null
                ];

                switch ($status) {
                    case 'Present': $presentCount++; break;
                    case 'Absent': $absentCount++; break;
                    case 'Late': $lateCount++; break;
                }
            }

            return [
                'date' => $date,
                'class_id' => $class_id,
                'total_students' => count($students),
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'late_count' => $lateCount,
                'attendance_records' => $attendanceRecords
            ];
        });
    }

    /**
     * Mark attendance
     */
    public function markAttendance() {
        $this->requireAdmin();
        $this->validateCsrf();

        $this->handleAjax(function() {
            $data = $this->getPostData();

            // Validate required fields
            if (!isset($data['student_id']) || !isset($data['date']) || !isset($data['status'])) {
                throw new Exception('Missing required fields');
            }

            if (!in_array($data['status'], ['Present', 'Absent', 'Late'])) {
                throw new Exception('Invalid attendance status');
            }

            $attendanceModel = new Attendance();

            // Check if attendance already exists
            $existing = $this->db->query("
                SELECT id FROM attendance
                WHERE student_id = ? AND date = ?
            ")->bind(1, $data['student_id'])->bind(2, $data['date'])->single();

            if ($existing) {
                // Update existing
                $attendanceModel->update($existing['id'], [
                    'status' => $data['status'],
                    'marked_by' => $this->getCurrentUserId()
                ]);
            } else {
                // Get student class
                $student = $this->db->query("SELECT class_id FROM students WHERE id = ?")
                                  ->bind(1, $data['student_id'])->single();

                if (!$student) {
                    throw new Exception('Student not found');
                }

                // Create new
                $attendanceModel->create([
                    'student_id' => $data['student_id'],
                    'class_id' => $student['class_id'],
                    'date' => $data['date'],
                    'status' => $data['status'],
                    'marked_by' => $this->getCurrentUserId()
                ]);
            }

            return [
                'student_id' => $data['student_id'],
                'date' => $data['date'],
                'status' => $data['status'],
                'marked_by' => $this->getCurrentUserId(),
                'timestamp' => date('Y-m-d H:i:s')
            ];
        });
    }

    /**
     * Get fees data
     */
    public function getFees() {
        $this->handleAjax(function() {
            $student_id = $_GET['student_id'] ?? null;

            if (!$student_id) {
                throw new Exception('Student ID is required');
            }

            $feeModel = new Fee();

            // Get fee status
            $feeStatus = $feeModel->getStudentFeeStatus($student_id);

            // Get payment history
            $paymentHistory = $this->db->query("
                SELECT fp.*, f.fee_type, f.academic_year, u.username as received_by_name
                FROM fee_payments fp
                JOIN fees f ON fp.fee_id = f.id
                LEFT JOIN users u ON fp.received_by = u.id
                WHERE fp.student_id = ?
                ORDER BY fp.payment_date DESC
            ")->bind(1, $student_id)->resultSet();

            // Get upcoming dues
            $upcomingDues = $this->db->query("
                SELECT f.*, DATEDIFF(f.due_date, CURDATE()) as days_until_due
                FROM fees f
                JOIN students s ON f.class_id = s.class_id
                LEFT JOIN fee_payments fp ON f.id = fp.fee_id AND s.id = fp.student_id
                WHERE s.id = ? AND f.due_date >= CURDATE()
                GROUP BY f.id
                HAVING SUM(COALESCE(fp.amount, 0)) < f.amount
                ORDER BY f.due_date ASC
            ")->bind(1, $student_id)->resultSet();

            return [
                'student_id' => $student_id,
                'total_fees' => $feeStatus['total_fees'],
                'paid_amount' => $feeStatus['paid_amount'],
                'pending_amount' => $feeStatus['pending_amount'],
                'payment_history' => $paymentHistory,
                'upcoming_dues' => $upcomingDues
            ];
        });
    }

    /**
     * Get students list
     */
    public function getStudents() {
        $this->requireAdmin();

        $this->handleAjax(function() {
            $page = (int)($_GET['page'] ?? 1);
            $per_page = (int)($_GET['per_page'] ?? 25);
            $search = $_GET['search'] ?? '';
            $class_id = $_GET['class_id'] ?? null;

            $studentModel = new Student();

            // Build conditions
            $conditions = ['is_active' => 1];
            if ($class_id) {
                $conditions['class_id'] = $class_id;
            }

            // Get paginated results
            $result = $studentModel->paginate($page, $per_page, $conditions, $search);

            return [
                'data' => $result['data'],
                'total' => $result['total'],
                'per_page' => $per_page,
                'current_page' => $page,
                'last_page' => $result['last_page'],
                'from' => $result['from'],
                'to' => $result['to']
            ];
        });
    }
}
?>