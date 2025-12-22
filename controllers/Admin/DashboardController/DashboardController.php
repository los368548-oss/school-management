<?php
/**
 * Dashboard Controller
 *
 * Handles admin dashboard operations
 */

class DashboardController extends BaseController {

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