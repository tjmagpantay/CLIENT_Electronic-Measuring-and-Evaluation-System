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
}
