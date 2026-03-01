<?php

class ReportType
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM report_types ORDER BY report_code ASC";
        return $this->db->fetchAll($sql);
    }

    public function getActive()
    {
        $sql = "SELECT * FROM report_types WHERE is_active = 1 ORDER BY report_code ASC";
        return $this->db->fetchAll($sql);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM report_types WHERE report_type_id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function countAll()
    {
        $row = $this->db->fetch("SELECT COUNT(*) as total FROM report_types WHERE is_active = 1");
        return $row['total'];
    }
}
