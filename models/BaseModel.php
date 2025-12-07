<?php
/**
 * Base Model Class
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
        $result = $this->db->query("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?")
                          ->bind(1, $id)
                          ->single();

        return $result ? $this->processResult($result) : null;
    }

    /**
     * Find record by conditions
     */
    public function findBy($conditions = [], $limit = null) {
        $whereClause = '';
        $params = [];
        $paramIndex = 1;

        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $column => $value) {
                if (is_array($value)) {
                    $placeholders = str_repeat('?,', count($value) - 1) . '?';
                    $whereParts[] = "{$column} IN ({$placeholders})";
                    $params = array_merge($params, $value);
                } else {
                    $whereParts[] = "{$column} = ?";
                    $params[] = $value;
                }
            }
            $whereClause = 'WHERE ' . implode(' AND ', $whereParts);
        }

        $limitClause = $limit ? "LIMIT {$limit}" : '';

        $sql = "SELECT * FROM {$this->table} {$whereClause} {$limitClause}";
        $stmt = $this->db->query($sql);

        foreach ($params as $index => $param) {
            $stmt->bind($index + 1, $param);
        }

        $results = $stmt->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Get all records
     */
    public function all($orderBy = null, $limit = null) {
        $orderClause = $orderBy ? "ORDER BY {$orderBy}" : '';
        $limitClause = $limit ? "LIMIT {$limit}" : '';

        $results = $this->db->query("SELECT * FROM {$this->table} {$orderClause} {$limitClause}")
                           ->resultSet();

        return array_map([$this, 'processResult'], $results);
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

        $columns = implode(', ', array_keys($data));
        $placeholders = str_repeat('?, ', count($data) - 1) . '?';

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->query($sql);

        $paramIndex = 1;
        foreach ($data as $value) {
            $stmt->bind($paramIndex++, $value);
        }

        $result = $stmt->execute();

        if ($result) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Update record
     */
    public function update($id, $data) {
        $data = $this->filterFillable($data);

        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $setParts = [];
        foreach ($data as $column => $value) {
            $setParts[] = "{$column} = ?";
        }
        $setClause = implode(', ', $setParts);

        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->query($sql);

        $paramIndex = 1;
        foreach ($data as $value) {
            $stmt->bind($paramIndex++, $value);
        }
        $stmt->bind($paramIndex, $id);

        return $stmt->execute();
    }

    /**
     * Delete record
     */
    public function delete($id) {
        return $this->db->query("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?")
                       ->bind(1, $id)
                       ->execute();
    }

    /**
     * Count records
     */
    public function count($conditions = []) {
        $whereClause = '';
        $params = [];

        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $column => $value) {
                $whereParts[] = "{$column} = ?";
                $params[] = $value;
            }
            $whereClause = 'WHERE ' . implode(' AND ', $whereParts);
        }

        $sql = "SELECT COUNT(*) as count FROM {$this->table} {$whereClause}";
        $stmt = $this->db->query($sql);

        foreach ($params as $index => $param) {
            $stmt->bind($index + 1, $param);
        }

        $result = $stmt->single();
        return $result['count'];
    }

    /**
     * Paginate results
     */
    public function paginate($page = 1, $perPage = 25, $conditions = [], $orderBy = null) {
        $offset = ($page - 1) * $perPage;

        $whereClause = '';
        $params = [];

        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $column => $value) {
                $whereParts[] = "{$column} = ?";
                $params[] = $value;
            }
            $whereClause = 'WHERE ' . implode(' AND ', $whereParts);
        }

        $orderClause = $orderBy ? "ORDER BY {$orderBy}" : '';

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} {$whereClause}";
        $countStmt = $this->db->query($countSql);

        foreach ($params as $index => $param) {
            $countStmt->bind($index + 1, $param);
        }

        $total = $countStmt->single()['total'];

        // Get paginated results
        $sql = "SELECT * FROM {$this->table} {$whereClause} {$orderClause} LIMIT ? OFFSET ?";
        $stmt = $this->db->query($sql);

        foreach ($params as $index => $param) {
            $stmt->bind($index + 1, $param);
        }
        $stmt->bind(count($params) + 1, $perPage);
        $stmt->bind(count($params) + 2, $offset);

        $results = $stmt->resultSet();
        $results = array_map([$this, 'processResult'], $results);

        return [
            'data' => $results,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }

    /**
     * Filter fillable attributes
     */
    protected function filterFillable($data) {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Process result (hide sensitive data, etc.)
     */
    protected function processResult($result) {
        if (!empty($this->hidden)) {
            foreach ($this->hidden as $field) {
                unset($result[$field]);
            }
        }

        return $result;
    }

    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit() {
        return $this->db->endTransaction();
    }

    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->db->cancelTransaction();
    }
}
?>