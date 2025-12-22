<?php
/**
 * Gallery Controller
 *
 * Handles gallery management operations
 */

class GalleryController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole('admin');
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

    private function getAcademicYearInfo($academicYearId) {
        return $this->db->selectOne("SELECT * FROM academic_years WHERE id = ?", [$academicYearId]);
    }
}
?>