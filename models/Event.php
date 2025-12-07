<?php
/**
 * Event Model
 */

class Event extends BaseModel {
    protected $table = 'events';
    protected $fillable = ['title', 'description', 'event_date', 'location', 'image_path', 'is_active', 'created_by'];

    /**
     * Get event with creator information
     */
    public function findWithCreator($id) {
        $result = $this->db->query("
            SELECT e.*, u.username as created_by_name, u.email as created_by_email
            FROM {$this->table} e
            LEFT JOIN users u ON e.created_by = u.id
            WHERE e.{$this->primaryKey} = ?
        ")->bind(1, $id)->single();

        return $result ? $this->processResult($result) : null;
    }

    /**
     * Get upcoming events
     */
    public function getUpcomingEvents($limit = 10) {
        $today = date('Y-m-d');

        $results = $this->db->query("
            SELECT e.*, u.username as created_by_name
            FROM {$this->table} e
            LEFT JOIN users u ON e.created_by = u.id
            WHERE e.event_date >= ? AND e.is_active = 1
            ORDER BY e.event_date ASC
            LIMIT ?
        ")->bind(1, $today)->bind(2, $limit)->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Get past events
     */
    public function getPastEvents($limit = 10) {
        $today = date('Y-m-d');

        $results = $this->db->query("
            SELECT e.*, u.username as created_by_name
            FROM {$this->table} e
            LEFT JOIN users u ON e.created_by = u.id
            WHERE e.event_date < ? AND e.is_active = 1
            ORDER BY e.event_date DESC
            LIMIT ?
        ")->bind(1, $today)->bind(2, $limit)->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Get events by month
     */
    public function getEventsByMonth($year, $month) {
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));

        $results = $this->db->query("
            SELECT e.*, u.username as created_by_name
            FROM {$this->table} e
            LEFT JOIN users u ON e.created_by = u.id
            WHERE e.event_date BETWEEN ? AND ? AND e.is_active = 1
            ORDER BY e.event_date ASC
        ")->bind(1, $startDate)->bind(2, $endDate)->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Search events
     */
    public function search($query, $limit = 50) {
        $searchTerm = '%' . $query . '%';

        $results = $this->db->query("
            SELECT e.*, u.username as created_by_name
            FROM {$this->table} e
            LEFT JOIN users u ON e.created_by = u.id
            WHERE (e.title LIKE ? OR e.description LIKE ? OR e.location LIKE ?) AND e.is_active = 1
            ORDER BY e.event_date DESC
            LIMIT ?
        ")->bind(1, $searchTerm)->bind(2, $searchTerm)->bind(3, $searchTerm)->bind(4, $limit)->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Get event statistics
     */
    public function getEventStats() {
        $result = $this->db->query("
            SELECT
                COUNT(*) as total_events,
                COUNT(CASE WHEN event_date >= CURDATE() THEN 1 END) as upcoming_events,
                COUNT(CASE WHEN event_date < CURDATE() THEN 1 END) as past_events,
                COUNT(DISTINCT YEAR(event_date)) as years_with_events
            FROM {$this->table}
            WHERE is_active = 1
        ")->single();

        return $result ?: [
            'total_events' => 0,
            'upcoming_events' => 0,
            'past_events' => 0,
            'years_with_events' => 0
        ];
    }

    /**
     * Check if event title exists on same date
     */
    public function eventExists($title, $eventDate, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE title = ? AND event_date = ?";
        $params = [$title, $eventDate];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $result = $this->db->query($sql);
        foreach ($params as $index => $param) {
            $result->bind($index + 1, $param);
        }

        $count = $result->single()['count'];
        return $count > 0;
    }
}
?>