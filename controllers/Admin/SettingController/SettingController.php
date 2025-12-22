<?php
/**
 * Setting Controller
 *
 * Handles settings management operations
 */

class SettingController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole('admin');
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

    private function getAcademicYearInfo($academicYearId) {
        return $this->db->selectOne("SELECT * FROM academic_years WHERE id = ?", [$academicYearId]);
    }
}
?>