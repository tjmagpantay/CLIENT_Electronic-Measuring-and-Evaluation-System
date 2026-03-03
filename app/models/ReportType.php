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

    /**
     * Get report type with full details including OPR and template link
     */
    public function getFullDetails($id)
    {
        $sql = "SELECT * FROM report_types WHERE report_type_id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    /**
     * Update report type template link
     */
    public function updateTemplateLink($id, $templateLink)
    {
        $sql = "UPDATE report_types SET template_link = ? WHERE report_type_id = ?";
        return $this->db->query($sql, [$templateLink, $id]);
    }

    /**
     * Update report type OPR
     */
    public function updateOpr($id, $opr)
    {
        $sql = "UPDATE report_types SET opr = ? WHERE report_type_id = ?";
        return $this->db->query($sql, [$opr, $id]);
    }

    /**
     * Get deadline day for a report type
     */
    public function getDeadlineDay($id)
    {
        $reportType = $this->getById($id);
        return $reportType['deadline_day'] ?? 15; // Default to 15th of the month
    }
}
