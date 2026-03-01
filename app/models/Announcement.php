<?php

class Announcement
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAll()
    {
        $sql = "SELECT a.*, u.firstname, u.lastname
                FROM announcements a
                LEFT JOIN users u ON a.created_by = u.user_id
                ORDER BY a.created_at DESC";
        return $this->db->fetchAll($sql);
    }

    public function getById($id)
    {
        $sql = "SELECT a.*, u.firstname, u.lastname
                FROM announcements a
                LEFT JOIN users u ON a.created_by = u.user_id
                WHERE a.announcement_id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function getActive()
    {
        $sql = "SELECT a.*, u.firstname, u.lastname
                FROM announcements a
                LEFT JOIN users u ON a.created_by = u.user_id
                WHERE a.is_active = 1
                  AND a.effectivity_date <= NOW()
                  AND (a.expiry_date IS NULL OR a.expiry_date >= NOW())
                ORDER BY a.effectivity_date DESC";
        return $this->db->fetchAll($sql);
    }

    public function create($data)
    {
        $sql = "INSERT INTO announcements (title, description, image_path, effectivity_date, expiry_date, is_active, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $data['title'],
            $data['description'],
            $data['image_path'] ?? null,
            $data['effectivity_date'],
            $data['expiry_date'] ?? null,
            $data['is_active'] ?? 1,
            $data['created_by']
        ];
        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE announcements SET title = ?, description = ?, image_path = ?, effectivity_date = ?, expiry_date = ?, is_active = ? WHERE announcement_id = ?";
        $params = [
            $data['title'],
            $data['description'],
            $data['image_path'] ?? null,
            $data['effectivity_date'],
            $data['expiry_date'] ?? null,
            $data['is_active'] ?? 1,
            $id
        ];
        $this->db->query($sql, $params);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM announcements WHERE announcement_id = ?";
        $this->db->query($sql, [$id]);
    }

    public function toggleActive($id)
    {
        $sql = "UPDATE announcements SET is_active = NOT is_active WHERE announcement_id = ?";
        $this->db->query($sql, [$id]);
    }

    public function countAll()
    {
        $row = $this->db->fetch("SELECT COUNT(*) as total FROM announcements");
        return $row['total'];
    }

    public function countActive()
    {
        $row = $this->db->fetch("SELECT COUNT(*) as total FROM announcements WHERE is_active = 1 AND effectivity_date <= NOW() AND (expiry_date IS NULL OR expiry_date >= NOW())");
        return $row['total'];
    }
}
