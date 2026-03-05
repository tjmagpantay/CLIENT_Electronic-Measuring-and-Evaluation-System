<?php

class User
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function findByUsername($username)
    {
        $sql = "SELECT * FROM users WHERE username = ?";
        return $this->db->fetch($sql, [$username]);
    }

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        return $this->db->fetch($sql, [$email]);
    }

    public function findById($id)
    {
        $sql = "SELECT u.*, o.office_name FROM users u
                LEFT JOIN offices o ON u.office_id = o.office_id
                WHERE u.user_id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function authenticate($username, $password)
    {
        $user = $this->findByUsername($username);

        if ($user && $user['is_active'] && password_verify($password, $user['password_hash'])) {
            return $user;
        }

        return false;
    }

    public function getAllUsers()
    {
        $sql = "SELECT u.*, o.office_name FROM users u
                LEFT JOIN offices o ON u.office_id = o.office_id
                ORDER BY u.created_at DESC";
        return $this->db->fetchAll($sql);
    }

    public function createUser($data)
    {
        $sql = "INSERT INTO users (username, password_hash, email, firstname, lastname, middlename, role, is_active, office_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $data['username'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['email'],
            $data['firstname'],
            $data['lastname'],
            $data['middlename'] ?? null,
            $data['role'],
            $data['is_active'] ?? 1,
            $data['office_id'] ?? null,
        ];

        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }

    public function updateUser($userId, $data)
    {
        $sql = "UPDATE users SET firstname = ?, lastname = ?, middlename = ?, email = ?, role = ?, is_active = ?, office_id = ? WHERE user_id = ?";
        $params = [
            $data['firstname'],
            $data['lastname'],
            $data['middlename'] ?? null,
            $data['email'],
            $data['role'],
            $data['is_active'] ?? 1,
            $data['office_id'] ?? null,
            $userId
        ];
        $this->db->query($sql, $params);
    }

    public function updateProfile($userId, $data)
    {
        $sets   = ['firstname = ?', 'lastname = ?', 'middlename = ?', 'email = ?'];
        $params = [
            $data['firstname'],
            $data['lastname'],
            $data['middlename'] ?? null,
            $data['email'],
        ];
        if (isset($data['profile'])) {
            $sets[]   = 'profile = ?';
            $params[] = $data['profile'];
        }
        $params[] = $userId;
        $sql = 'UPDATE users SET ' . implode(', ', $sets) . ' WHERE user_id = ?';
        $this->db->query($sql, $params);
    }

    public function updatePassword($userId, $newPassword)
    {
        $sql = "UPDATE users SET password_hash = ? WHERE user_id = ?";
        $this->db->query($sql, [password_hash($newPassword, PASSWORD_DEFAULT), $userId]);
    }

    public function toggleActive($userId)
    {
        $sql = "UPDATE users SET is_active = NOT is_active WHERE user_id = ?";
        $this->db->query($sql, [$userId]);
    }

    public function countAll()
    {
        $row = $this->db->fetch("SELECT COUNT(*) as total FROM users");
        return $row['total'];
    }

    public function countActive()
    {
        $row = $this->db->fetch("SELECT COUNT(*) as total FROM users WHERE is_active = 1");
        return $row['total'];
    }
}
