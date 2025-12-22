<?php
/**
 * Base Model Class
 *
 * All models should extend this class
 */

class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    protected $timestamps = true;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Find record by ID
     */
    public function find($id) {
        $result = $this->db->selectOne("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?", [$id]);
        return $result ? $this->createInstance($result) : null;
    }

    /**
     * Find records by conditions
     */
    public function where($conditions, $params = []) {
        $whereClause = $this->buildWhereClause($conditions);
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause}";
        $results = $this->db->select($sql, $params);
        return array_map([$this, 'createInstance'], $results);
    }

    /**
     * Get all records
     */
    public function all() {
        $results = $this->db->select("SELECT * FROM {$this->table}");
        return array_map([$this, 'createInstance'], $results);
    }

    /**
     * Create new record
     */
    public function create($data) {
        $data = $this->filterFillable($data);

        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $id = $this->db->insert($this->table, $data);
        return $this->find($id);
    }

    /**
     * Update record
     */
    public function update($id, $data) {
        $data = $this->filterFillable($data);

        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $updated = $this->db->update($this->table, $data, "{$this->primaryKey} = ?", [$id]);
        return $updated > 0 ? $this->find($id) : false;
    }

    /**
     * Delete record
     */
    public function delete($id) {
        return $this->db->delete($this->table, "{$this->primaryKey} = ?", [$id]);
    }

    /**
     * Get records with pagination
     */
    public function paginate($page = 1, $perPage = 25, $conditions = [], $params = []) {
        $offset = ($page - 1) * $perPage;

        $whereClause = empty($conditions) ? '1=1' : $this->buildWhereClause($conditions);
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause} LIMIT {$perPage} OFFSET {$offset}";
        $results = $this->db->select($sql, $params);

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$whereClause}";
        $total = $this->db->selectOne($countSql, $params)['total'];

        return [
            'data' => array_map([$this, 'createInstance'], $results),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }

    /**
     * Filter data to only fillable fields
     */
    protected function filterFillable($data) {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Build WHERE clause from conditions array
     */
    protected function buildWhereClause($conditions) {
        $clauses = [];
        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $clauses[] = "{$field} IN (" . str_repeat('?,', count($value) - 1) . "?)";
            } else {
                $clauses[] = "{$field} = ?";
            }
        }
        return implode(' AND ', $clauses);
    }

    /**
     * Create model instance from data
     */
    protected function createInstance($data) {
        $instance = new static();
        foreach ($data as $key => $value) {
            if (!in_array($key, $this->hidden)) {
                $instance->$key = $value;
            }
        }
        return $instance;
    }

    /**
     * Get current academic year ID
     */
    protected function getCurrentAcademicYearId() {
        return Session::getAcademicYear();
    }

    /**
     * Scope queries to current academic year
     */
    public function scopeCurrentAcademicYear($query = '') {
        $academicYearId = $this->getCurrentAcademicYearId();
        if ($academicYearId) {
            $whereClause = $query ? " AND academic_year_id = {$academicYearId}" : "academic_year_id = {$academicYearId}";
            return $whereClause;
        }
        return $query;
    }
}
?>