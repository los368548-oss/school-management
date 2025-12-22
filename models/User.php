<?php
/**
 * User Model
 *
 * Handles user-related database operations
 */

class User extends BaseModel {
    protected $table = 'users';
    protected $fillable = [
        'username', 'email', 'password', 'role', 'status',
        'last_login', 'login_attempts', 'locked_until'
    ];
    protected $hidden = ['password'];

    /**
     * Find user by username or email
     */
    public function findByUsernameOrEmail($usernameOrEmail) {
        $result = $this->db->selectOne(
            "SELECT u.*, CONCAT(up.first_name, ' ', up.last_name) as full_name
             FROM {$this->table} u
             LEFT JOIN user_profiles up ON u.id = up.user_id
             WHERE u.username = ? OR u.email = ?",
            [$usernameOrEmail, $usernameOrEmail]
        );
        return $result ? $this->createInstance($result) : null;
    }

    /**
     * Create new user with profile
     */
    public function createWithProfile($userData, $profileData = []) {
        $this->db->beginTransaction();

        try {
            // Hash password
            $userData['password'] = Security::hashPassword($userData['password']);

            // Create user
            $user = $this->create($userData);

            // Create profile if provided
            if (!empty($profileData)) {
                $profileData['user_id'] = $user->id;
                $this->db->insert('user_profiles', $profileData);
            }

            $this->db->commit();
            return $user;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Update password
     */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = Security::hashPassword($newPassword);
        return $this->update($userId, ['password' => $hashedPassword]);
    }

    /**
     * Check if user can log in
     */
    public function canLogin($userId) {
        $user = $this->find($userId);

        if (!$user) {
            return false;
        }

        // Check if account is active
        if ($user->status !== 'active') {
            return false;
        }

        // Check if account is locked
        if ($user->locked_until && strtotime($user->locked_until) > time()) {
            return false;
        }

        return true;
    }

    /**
     * Record failed login attempt
     */
    public function recordFailedLogin($userId) {
        $user = $this->find($userId);

        if ($user) {
            $attempts = $user->login_attempts + 1;

            $updateData = ['login_attempts' => $attempts];

            // Lock account if too many attempts
            if ($attempts >= 5) {
                $config = require BASE_PATH . 'config/security.php';
                $lockDuration = $config['lockout_duration'];
                $updateData['locked_until'] = date('Y-m-d H:i:s', time() + $lockDuration);
            }

            $this->update($userId, $updateData);
        }
    }

    /**
     * Reset login attempts
     */
    public function resetLoginAttempts($userId) {
        return $this->update($userId, [
            'login_attempts' => 0,
            'locked_until' => null
        ]);
    }

    /**
     * Get user with profile
     */
    public function getWithProfile($userId) {
        $result = $this->db->selectOne(
            "SELECT u.*, up.*
             FROM {$this->table} u
             LEFT JOIN user_profiles up ON u.id = up.user_id
             WHERE u.id = ?",
            [$userId]
        );
        return $result ? $this->createInstance($result) : null;
    }

    /**
     * Get users by role
     */
    public function getByRole($role) {
        return $this->where(['role' => $role]);
    }

    /**
     * Update last login
     */
    public function updateLastLogin($userId) {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }
}
?>