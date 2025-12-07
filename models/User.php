<?php
/**
 * User Model
 */

class User extends BaseModel {
    protected $table = 'users';
    protected $fillable = ['username', 'email', 'password', 'role_id'];
    protected $hidden = ['password'];

    /**
     * Get user with role information
     */
    public function findWithRole($id) {
        $result = $this->db->query("
            SELECT u.*, r.name as role_name, r.description as role_description
            FROM {$this->table} u
            LEFT JOIN user_roles r ON u.role_id = r.id
            WHERE u.{$this->primaryKey} = ?
        ")->bind(1, $id)->single();

        return $result ? $this->processResult($result) : null;
    }

    /**
     * Find user by username or email
     */
    public function findByUsernameOrEmail($usernameOrEmail) {
        $result = $this->db->query("
            SELECT u.*, r.name as role_name, r.description as role_description
            FROM {$this->table} u
            LEFT JOIN user_roles r ON u.role_id = r.id
            WHERE u.username = ? OR u.email = ?
        ")->bind(1, $usernameOrEmail)->bind(2, $usernameOrEmail)->single();

        return $result ? $this->processResult($result) : null;
    }

    /**
     * Get all users with roles
     */
    public function allWithRoles($orderBy = 'u.created_at DESC') {
        $results = $this->db->query("
            SELECT u.*, r.name as role_name, r.description as role_description
            FROM {$this->table} u
            LEFT JOIN user_roles r ON u.role_id = r.id
            ORDER BY {$orderBy}
        ")->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Update user password
     */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = Security::getInstance()->hashPassword($newPassword);

        return $this->update($userId, ['password' => $hashedPassword]);
    }

    /**
     * Check if user has permission
     */
    public function hasPermission($userId, $module, $action) {
        $result = $this->db->query("
            SELECT COUNT(*) as count
            FROM users u
            JOIN permissions p ON u.role_id = p.role_id
            WHERE u.id = ? AND p.module = ? AND p.action = ?
        ")->bind(1, $userId)->bind(2, $module)->bind(3, $action)->single();

        return $result['count'] > 0;
    }

    /**
     * Get user permissions
     */
    public function getPermissions($userId) {
        return $this->db->query("
            SELECT p.module, p.action
            FROM users u
            JOIN permissions p ON u.role_id = p.role_id
            WHERE u.id = ?
        ")->bind(1, $userId)->resultSet();
    }

    /**
     * Update last login
     */
    public function updateLastLogin($userId) {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }

    /**
     * Get active users count
     */
    public function getActiveUsersCount() {
        $result = $this->db->query("SELECT COUNT(*) as count FROM {$this->table} WHERE is_active = 1")
                          ->single();
        return $result['count'];
    }

    /**
     * Get users by role
     */
    public function getUsersByRole($roleId) {
        $results = $this->db->query("
            SELECT u.*, r.name as role_name
            FROM {$this->table} u
            LEFT JOIN user_roles r ON u.role_id = r.id
            WHERE u.role_id = ? AND u.is_active = 1
            ORDER BY u.created_at DESC
        ")->bind(1, $roleId)->resultSet();

        return array_map([$this, 'processResult'], $results);
    }
}
?>