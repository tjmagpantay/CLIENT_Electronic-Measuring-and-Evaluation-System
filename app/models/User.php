<?php

/**
 * User Model
 * Example model for user-related database operations
 */

class User
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all users
     */
    public function getAllUsers()
    {
        $sql = "SELECT * FROM users";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get user by ID
     */
    public function getUserById($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    /**
     * Create new user
     */
    public function createUser($data)
    {
        $sql = "INSERT INTO users (username, email, password, created_at) 
                VALUES (?, ?, ?, NOW())";

        $params = [
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        ];

        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }

    /**
     * Update user
     */
    public function updateUser($id, $data)
    {
        $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        $params = [$data['username'], $data['email'], $id];
        return $this->db->query($sql, $params);
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }

    /**
     * Find user by email
     */
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        return $this->db->fetch($sql, [$email]);
    }
}
