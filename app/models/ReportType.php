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

    /**
     * Create a new report type
     */
    public function create($data)
    {
        $sql = "INSERT INTO report_types (report_code, report_title, description, opr, submission_type, template_link, deadline_day, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        return $this->db->query($sql, [
            $data['report_code'],
            $data['report_title'],
            $data['description'] ?? null,
            $data['opr'] ?? null,
            $data['submission_type'] ?? 'FILE_UPLOAD',
            $data['template_link'] ?? null,
            $data['deadline_day'] ?? 15,
            $data['is_active'] ?? 1
        ]);
    }

    /**
     * Update an existing report type
     */
    public function update($id, $data)
    {
        $sql = "UPDATE report_types 
                SET report_code = ?, report_title = ?, description = ?, opr = ?, 
                    submission_type = ?, template_link = ?, deadline_day = ?, is_active = ? 
                WHERE report_type_id = ?";

        return $this->db->query($sql, [
            $data['report_code'],
            $data['report_title'],
            $data['description'] ?? null,
            $data['opr'] ?? null,
            $data['submission_type'] ?? 'FILE_UPLOAD',
            $data['template_link'] ?? null,
            $data['deadline_day'] ?? 15,
            $data['is_active'] ?? 1,
            $id
        ]);
    }

    /**
     * Delete a report type (soft delete by setting inactive)
     */
    public function delete($id)
    {
        $sql = "UPDATE report_types SET is_active = 0 WHERE report_type_id = ?";
        return $this->db->query($sql, [$id]);
    }

    /**
     * Check if report code already exists
     */
    public function codeExists($code, $excludeId = null)
    {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) as count FROM report_types WHERE report_code = ? AND report_type_id != ?";
            $result = $this->db->fetch($sql, [$code, $excludeId]);
        } else {
            $sql = "SELECT COUNT(*) as count FROM report_types WHERE report_code = ?";
            $result = $this->db->fetch($sql, [$code]);
        }
        return $result['count'] > 0;
    }
}
