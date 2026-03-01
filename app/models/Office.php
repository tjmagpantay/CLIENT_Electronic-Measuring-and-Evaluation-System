<?php

class Office
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM offices ORDER BY office_name ASC";
        return $this->db->fetchAll($sql);
    }

    public function getActive()
    {
        $sql = "SELECT * FROM offices WHERE status = 'ACTIVE' ORDER BY office_name ASC";
        return $this->db->fetchAll($sql);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM offices WHERE office_id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function getByType($type)
    {
        $sql = "SELECT * FROM offices WHERE office_type = ? AND status = 'ACTIVE' ORDER BY office_name ASC";
        return $this->db->fetchAll($sql, [$type]);
    }

    public function countAll()
    {
        $row = $this->db->fetch("SELECT COUNT(*) as total FROM offices WHERE status = 'ACTIVE'");
        return $row['total'];
    }

    public function create($data)
    {
        $sql = "INSERT INTO offices (office_name, office_type, cluster, status) VALUES (?, ?, ?, ?)";
        $params = [
            $data['office_name'],
            $data['office_type'],
            $data['cluster'] ?? null,
            $data['status'] ?? 'ACTIVE'
        ];
        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE offices SET office_name = ?, office_type = ?, cluster = ?, status = ? WHERE office_id = ?";
        $params = [
            $data['office_name'],
            $data['office_type'],
            $data['cluster'] ?? null,
            $data['status'] ?? 'ACTIVE',
            $id
        ];
        $this->db->query($sql, $params);
    }

    public function toggleStatus($id)
    {
        $office = $this->getById($id);
        $newStatus = ($office['status'] === 'ACTIVE') ? 'INACTIVE' : 'ACTIVE';
        $this->db->query("UPDATE offices SET status = ? WHERE office_id = ?", [$newStatus, $id]);
    }
}
