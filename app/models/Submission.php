<?php

class Submission
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAll()
    {
        $sql = "SELECT s.*, o.office_name, o.office_type,
                       rt.report_code, rt.report_title,
                       rp.period_month, rp.period_year,
                       u.firstname, u.lastname
                FROM submissions s
                JOIN offices o ON s.office_id = o.office_id
                JOIN report_types rt ON s.report_type_id = rt.report_type_id
                JOIN reporting_period rp ON s.period_id = rp.period_id
                LEFT JOIN users u ON s.submitted_by = u.user_id
                ORDER BY s.created_at DESC";
        return $this->db->fetchAll($sql);
    }

    public function getByOffice($officeId)
    {
        $sql = "SELECT s.*, rt.report_code, rt.report_title, rt.opr,
                       rp.period_month, rp.period_year, rp.deadline,
                       u.firstname, u.lastname,
                       o.cluster, o.office_name
                FROM submissions s
                JOIN report_types rt ON s.report_type_id = rt.report_type_id
                JOIN reporting_period rp ON s.period_id = rp.period_id
                LEFT JOIN users u ON s.submitted_by = u.user_id
                LEFT JOIN offices o ON s.office_id = o.office_id
                WHERE s.office_id = ?
                ORDER BY s.created_at DESC";
        return $this->db->fetchAll($sql, [$officeId]);
    }

    public function getById($id)
    {
        $sql = "SELECT s.*, o.office_name, rt.report_code, rt.report_title,
                       rp.period_month, rp.period_year
                FROM submissions s
                JOIN offices o ON s.office_id = o.office_id
                JOIN report_types rt ON s.report_type_id = rt.report_type_id
                JOIN reporting_period rp ON s.period_id = rp.period_id
                WHERE s.submission_id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function create($data)
    {
        $sql = "INSERT INTO submissions (office_id, report_type_id, period_id, submitted_by, file_link, submitted_at, submission_status, remarks)
                VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)";

        $params = [
            $data['office_id'],
            $data['report_type_id'],
            $data['period_id'],
            $data['submitted_by'],
            $data['file_link'],
            $data['submission_status'] ?? 'ON_TIME',
            $data['remarks'] ?? null
        ];

        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }

    public function updateStatus($id, $status, $remarks = null)
    {
        $sql = "UPDATE submissions SET submission_status = ?, remarks = ? WHERE submission_id = ?";
        return $this->db->query($sql, [$status, $remarks, $id]);
    }

    public function countByStatus($status)
    {
        $row = $this->db->fetch("SELECT COUNT(*) as total FROM submissions WHERE submission_status = ?", [$status]);
        return $row['total'];
    }

    public function countByOfficeAndStatus($officeId, $status)
    {
        $row = $this->db->fetch("SELECT COUNT(*) as total FROM submissions WHERE office_id = ? AND submission_status = ?", [$officeId, $status]);
        return $row['total'];
    }

    public function countByOffice($officeId)
    {
        $row = $this->db->fetch("SELECT COUNT(*) as total FROM submissions WHERE office_id = ?", [$officeId]);
        return $row['total'];
    }

    public function countAll()
    {
        $row = $this->db->fetch("SELECT COUNT(*) as total FROM submissions");
        return $row['total'];
    }

    public function countOnTime()
    {
        $row = $this->db->fetch("SELECT COUNT(*) as total FROM submissions WHERE submission_status = 'ON_TIME'");
        return $row['total'];
    }

    public function countLate()
    {
        $row = $this->db->fetch("SELECT COUNT(*) as total FROM submissions WHERE submission_status = 'LATE'");
        return $row['total'];
    }

    public function getRecent($limit = 5)
    {
        $sql = "SELECT s.*, o.office_name, o.office_type,
                       rt.report_code, rt.report_title,
                       rp.period_month, rp.period_year
                FROM submissions s
                JOIN offices o ON s.office_id = o.office_id
                JOIN report_types rt ON s.report_type_id = rt.report_type_id
                JOIN reporting_period rp ON s.period_id = rp.period_id
                ORDER BY s.created_at DESC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    public function existsForOfficePeriodReport($officeId, $periodId, $reportTypeId)
    {
        $row = $this->db->fetch(
            "SELECT submission_id FROM submissions WHERE office_id = ? AND period_id = ? AND report_type_id = ?",
            [$officeId, $periodId, $reportTypeId]
        );
        return $row ? true : false;
    }

    /**
     * Get submission with files
     */
    public function getWithFiles($submissionId)
    {
        $submission = $this->getById($submissionId);
        if (!$submission) {
            return null;
        }

        // Get associated files
        require_once __DIR__ . '/SubmissionFile.php';
        $fileModel = new SubmissionFile();
        $submission['files'] = $fileModel->getBySubmission($submissionId);

        return $submission;
    }

    /**
     * Get submissions with their files for an office
     */
    public function getByOfficeWithFiles($officeId)
    {
        $submissions = $this->getByOffice($officeId);

        require_once __DIR__ . '/SubmissionFile.php';
        $fileModel = new SubmissionFile();

        foreach ($submissions as &$submission) {
            $submission['files'] = $fileModel->getBySubmission($submission['submission_id']);
        }

        return $submissions;
    }
}
