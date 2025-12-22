<?php
/**
 * Homepage Model
 *
 * Handles homepage content database operations
 */

class Homepage extends BaseModel {
    protected $table = 'homepage_content';
    protected $fillable = [
        'section', 'title', 'content', 'image_path', 'link_url', 'link_text',
        'display_order', 'is_active', 'updated_by'
    ];

    /**
     * Get all homepage content
     */
    public function getAllContent() {
        return $this->db->select(
            "SELECT * FROM {$this->table} ORDER BY display_order ASC, section ASC"
        );
    }

    /**
     * Get content by section
     */
    public function getContentBySection($section) {
        return $this->db->select(
            "SELECT * FROM {$this->table} WHERE section = ? AND is_active = 1 ORDER BY display_order ASC",
            [$section]
        );
    }

    /**
     * Update homepage content
     */
    public function updateContent($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->update($id, $data);
    }

    /**
     * Create new homepage content
     */
    public function createContent($data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }

    /**
     * Delete homepage content
     */
    public function deleteContent($id) {
        return $this->delete($id);
    }

    /**
     * Get sections for dropdown
     */
    public function getSections() {
        return [
            'hero' => 'Hero Section',
            'about' => 'About Section',
            'courses' => 'Courses Section',
            'events' => 'Events Section',
            'gallery' => 'Gallery Section',
            'testimonials' => 'Testimonials Section'
        ];
    }

    /**
     * Reorder content
     */
    public function reorderContent($section, $orderData) {
        $this->db->beginTransaction();

        try {
            foreach ($orderData as $id => $order) {
                $this->db->update(
                    $this->table,
                    ['display_order' => $order],
                    'id = ?',
                    [$id]
                );
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
}
?>