<?php

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private $userModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userModel = new User();
    }

    protected function tearDown(): void
    {
        // Clean up test data
        Database::getInstance()->query("DELETE FROM test_users WHERE username LIKE 'test_%'");
        parent::tearDown();
    }

    public function testCanCreateUser()
    {
        $userData = [
            'username' => 'test_user_' . time(),
            'email' => 'test' . time() . '@example.com',
            'password' => 'password123',
            'role' => 'student',
            'status' => 'active'
        ];

        $user = $this->userModel->create($userData);

        $this->assertIsObject($user);
        $this->assertEquals($userData['username'], $user->username);
        $this->assertEquals($userData['email'], $user->email);
        $this->assertEquals($userData['role'], $user->role);
    }

    public function testCanFindUserById()
    {
        // Create test user
        $userData = [
            'username' => 'test_user_' . time(),
            'email' => 'test' . time() . '@example.com',
            'password' => 'password123',
            'role' => 'student',
            'status' => 'active'
        ];

        $createdUser = $this->userModel->create($userData);

        // Find user by ID
        $foundUser = $this->userModel->find($createdUser->id);

        $this->assertIsObject($foundUser);
        $this->assertEquals($createdUser->id, $foundUser->id);
        $this->assertEquals($createdUser->username, $foundUser->username);
    }

    public function testCanUpdateUser()
    {
        // Create test user
        $userData = [
            'username' => 'test_user_' . time(),
            'email' => 'test' . time() . '@example.com',
            'password' => 'password123',
            'role' => 'student',
            'status' => 'active'
        ];

        $user = $this->userModel->create($userData);

        // Update user
        $updateData = ['status' => 'inactive'];
        $updatedUser = $this->userModel->update($user->id, $updateData);

        $this->assertIsObject($updatedUser);
        $this->assertEquals('inactive', $updatedUser->status);
    }

    public function testCanDeleteUser()
    {
        // Create test user
        $userData = [
            'username' => 'test_user_' . time(),
            'email' => 'test' . time() . '@example.com',
            'password' => 'password123',
            'role' => 'student',
            'status' => 'active'
        ];

        $user = $this->userModel->create($userData);

        // Delete user
        $result = $this->userModel->delete($user->id);

        $this->assertTrue($result);

        // Verify user is deleted
        $foundUser = $this->userModel->find($user->id);
        $this->assertNull($foundUser);
    }

    public function testCanFindUserByUsernameOrEmail()
    {
        $userData = [
            'username' => 'test_user_' . time(),
            'email' => 'test' . time() . '@example.com',
            'password' => 'password123',
            'role' => 'student',
            'status' => 'active'
        ];

        $this->userModel->create($userData);

        // Find by username
        $userByUsername = $this->userModel->findByUsernameOrEmail($userData['username']);
        $this->assertIsObject($userByUsername);
        $this->assertEquals($userData['username'], $userByUsername->username);

        // Find by email
        $userByEmail = $this->userModel->findByUsernameOrEmail($userData['email']);
        $this->assertIsObject($userByEmail);
        $this->assertEquals($userData['email'], $userByEmail->email);
    }

    public function testPasswordHashing()
    {
        $password = 'testpassword123';

        // Test password hashing
        $hashedPassword = Security::hashPassword($password);
        $this->assertNotEquals($password, $hashedPassword);

        // Test password verification
        $isValid = Security::verifyPassword($password, $hashedPassword);
        $this->assertTrue($isValid);

        // Test invalid password
        $isInvalid = Security::verifyPassword('wrongpassword', $hashedPassword);
        $this->assertFalse($isInvalid);
    }

    public function testUserStatusValidation()
    {
        $userData = [
            'username' => 'test_user_' . time(),
            'email' => 'test' . time() . '@example.com',
            'password' => 'password123',
            'role' => 'student',
            'status' => 'inactive'
        ];

        $user = $this->userModel->create($userData);

        // Test inactive user cannot login
        $canLogin = $this->userModel->canLogin($user->id);
        $this->assertFalse($canLogin);

        // Update to active and test
        $this->userModel->update($user->id, ['status' => 'active']);
        $canLogin = $this->userModel->canLogin($user->id);
        $this->assertTrue($canLogin);
    }
}
?>