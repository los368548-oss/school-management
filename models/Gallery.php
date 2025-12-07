<?php
/**
 * Gallery Model
 */

class Gallery extends BaseModel {
    protected $table = 'gallery';
    protected $fillable = ['title', 'image_path', 'category', 'description', 'is_active', 'uploaded_by'];

    /**
     * Get image with uploader information
     */
    public function findWithUploader($id) {
        $result = $this->db->query("
            SELECT g.*, u.username as uploaded_by_name, u.email as uploaded_by_email
            FROM {$this->table} g
            LEFT JOIN users u ON g.uploaded_by = u.id
            WHERE g.{$this->primaryKey} = ?
        ")->bind(1, $id)->single();

        return $result ? $this->processResult($result) : null;
    }

    /**
     * Get all images with uploader information
     */
    public function allWithUploaders($orderBy = 'g.upload_date DESC') {
        $results = $this->db->query("
            SELECT g.*, u.username as uploaded_by_name
            FROM {$this->table} g
            LEFT JOIN users u ON g.uploaded_by = u.id
            ORDER BY {$orderBy}
        ")->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Get images by category
     */
    public function getImagesByCategory($category = null, $limit = null) {
        $whereClause = 'g.is_active = 1';
        $params = [];

        if ($category) {
            $whereClause .= ' AND g.category = ?';
            $params[] = $category;
        }

        $limitClause = $limit ? "LIMIT {$limit}" : '';

        $sql = "
            SELECT g.*, u.username as uploaded_by_name
            FROM {$this->table} g
            LEFT JOIN users u ON g.uploaded_by = u.id
            WHERE {$whereClause}
            ORDER BY g.upload_date DESC
            {$limitClause}
        ";

        $stmt = $this->db->query($sql);
        foreach ($params as $index => $param) {
            $stmt->bind($index + 1, $param);
        }

        $results = $stmt->resultSet();
        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Get all categories
     */
    public function getCategories() {
        $results = $this->db->query("
            SELECT
                category,
                COUNT(*) as image_count,
                MAX(upload_date) as last_upload
            FROM {$this->table}
            WHERE category IS NOT NULL AND category != '' AND is_active = 1
            GROUP BY category
            ORDER BY category ASC
        ")->resultSet();

        return $results;
    }

    /**
     * Search gallery images
     */
    public function search($query, $category = null, $limit = 50) {
        $searchTerm = '%' . $query . '%';
        $whereClause = '(g.title LIKE ? OR g.description LIKE ? OR g.category LIKE ?) AND g.is_active = 1';
        $params = [$searchTerm, $searchTerm, $searchTerm];

        if ($category) {
            $whereClause .= ' AND g.category = ?';
            $params[] = $category;
        }

        $sql = "
            SELECT g.*, u.username as uploaded_by_name
            FROM {$this->table} g
            LEFT JOIN users u ON g.uploaded_by = u.id
            WHERE {$whereClause}
            ORDER BY g.upload_date DESC
            LIMIT ?
        ";

        $stmt = $this->db->query($sql);
        foreach ($params as $index => $param) {
            $stmt->bind($index + 1, $param);
        }
        $stmt->bind(count($params) + 1, $limit);

        $results = $stmt->resultSet();
        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Get gallery statistics
     */
    public function getGalleryStats() {
        $result = $this->db->query("
            SELECT
                COUNT(*) as total_images,
                COUNT(DISTINCT category) as total_categories,
                COUNT(CASE WHEN upload_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as recent_uploads,
                MAX(upload_date) as last_upload
            FROM {$this->table}
            WHERE is_active = 1
        ")->single();

        return $result ?: [
            'total_images' => 0,
            'total_categories' => 0,
            'recent_uploads' => 0,
            'last_upload' => null
        ];
    }

    /**
     * Upload multiple images
     */
    public function bulkUpload($files, $uploadedBy, $category = null, $titles = []) {
        $uploaded = [];
        $errors = [];

        foreach ($files as $index => $file) {
            try {
                // Validate file
                if (!$this->validateImageFile($file)) {
                    $errors[] = "Invalid file: {$file['name']}";
                    continue;
                }

                // Generate unique filename
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid('gallery_') . '.' . $extension;
                $uploadPath = 'uploads/gallery/' . $filename;

                // Create upload directory if it doesn't exist
                $uploadDir = dirname($uploadPath);
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Move uploaded file
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    // Create thumbnail (simplified - in production use proper image library)
                    $this->createThumbnail($uploadPath);

                    // Save to database
                    $title = isset($titles[$index]) && !empty($titles[$index])
                        ? $titles[$index]
                        : pathinfo($file['name'], PATHINFO_FILENAME);

                    $imageData = [
                        'title' => $title,
                        'image_path' => $uploadPath,
                        'category' => $category,
                        'uploaded_by' => $uploadedBy
                    ];

                    $imageId = $this->create($imageData);
                    $uploaded[] = $imageId;
                } else {
                    $errors[] = "Failed to upload: {$file['name']}";
                }
            } catch (Exception $e) {
                $errors[] = "Error uploading {$file['name']}: " . $e->getMessage();
            }
        }

        return ['uploaded' => $uploaded, 'errors' => $errors];
    }

    /**
     * Validate image file
     */
    private function validateImageFile($file) {
        // Check if file is uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }

        // Check file size (5MB limit)
        if ($file['size'] > 5 * 1024 * 1024) {
            return false;
        }

        // Check file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }

        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $allowedExtensions)) {
            return false;
        }

        return true;
    }

    /**
     * Create thumbnail (simplified version)
     */
    private function createThumbnail($imagePath) {
        // In production, use proper image processing library like GD or Imagick
        // This is a placeholder for thumbnail creation
        return true;
    }

    /**
     * Delete image file when record is deleted
     */
    public function delete($id) {
        $image = $this->find($id);
        if ($image && file_exists($image['image_path'])) {
            unlink($image['image_path']);
        }

        return parent::delete($id);
    }
}
?>