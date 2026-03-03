<?php

class ReportingPeriod
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM reporting_period ORDER BY period_year DESC, period_month DESC";
        return $this->db->fetchAll($sql);
    }

    public function getActive()
    {
        $sql = "SELECT * FROM reporting_period WHERE is_active = 1 ORDER BY period_year DESC, period_month DESC";
        return $this->db->fetchAll($sql);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM reporting_period WHERE period_id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function getMonthName($month)
    {
        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
        return $months[$month] ?? '';
    }

    /**
     * Create a new reporting period
     */
    public function create($data)
    {
        $sql = "INSERT INTO reporting_period (period_month, period_year, deadline, is_active) 
                VALUES (?, ?, ?, ?)";

        return $this->db->query($sql, [
            $data['period_month'],
            $data['period_year'],
            $data['deadline'] ?? null,
            $data['is_active'] ?? 1
        ]);
    }

    /**
     * Update an existing reporting period
     */
    public function update($id, $data)
    {
        $sql = "UPDATE reporting_period 
                SET period_month = ?, period_year = ?, deadline = ?, is_active = ? 
                WHERE period_id = ?";

        return $this->db->query($sql, [
            $data['period_month'],
            $data['period_year'],
            $data['deadline'] ?? null,
            $data['is_active'] ?? 1,
            $id
        ]);
    }

    /**
     * Delete a reporting period (soft delete by setting inactive)
     */
    public function delete($id)
    {
        $sql = "UPDATE reporting_period SET is_active = 0 WHERE period_id = ?";
        return $this->db->query($sql, [$id]);
    }

    /**
     * Check if a period already exists
     */
    public function periodExists($month, $year, $excludeId = null)
    {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) as count FROM reporting_period 
                    WHERE period_month = ? AND period_year = ? AND period_id != ?";
            $result = $this->db->fetch($sql, [$month, $year, $excludeId]);
        } else {
            $sql = "SELECT COUNT(*) as count FROM reporting_period 
                    WHERE period_month = ? AND period_year = ?";
            $result = $this->db->fetch($sql, [$month, $year]);
        }
        return $result['count'] > 0;
    }

    /**
     * Get the current active period
     */
    public function getCurrentPeriod()
    {
        $currentMonth = date('n');
        $currentYear = date('Y');

        $sql = "SELECT * FROM reporting_period 
                WHERE period_month = ? AND period_year = ? AND is_active = 1 
                LIMIT 1";
        return $this->db->fetch($sql, [$currentMonth, $currentYear]);
    }
}
