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

        if ($user && password_verify($password, $user['password_hash'])) {
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
        $sql = "INSERT INTO users (username, password_hash, email, firstname, lastname, middlename, role, office_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $data['username'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['email'],
            $data['firstname'],
            $data['lastname'],
            $data['middlename'] ?? null,
            $data['role'],
            $data['office_id'] ?? null,
        ];

        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }
}
