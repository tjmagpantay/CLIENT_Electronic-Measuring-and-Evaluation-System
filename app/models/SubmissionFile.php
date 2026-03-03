<?php

class SubmissionFile
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all files for a submission
     */
    public function getBySubmission($submissionId)
    {
        $sql = "SELECT * FROM submission_files WHERE submission_id = ? ORDER BY uploaded_at ASC";
        return $this->db->fetchAll($sql, [$submissionId]);
    }

    /**
     * Get a single file by ID
     */
    public function getById($fileId)
    {
        $sql = "SELECT * FROM submission_files WHERE file_id = ?";
        return $this->db->fetch($sql, [$fileId]);
    }

    /**
     * Add a file to a submission
     */
    public function create($data)
    {
        $sql = "INSERT INTO submission_files (submission_id, file_name, file_path, file_size, file_type, google_drive_id, google_drive_link)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $data['submission_id'],
            $data['file_name'],
            $data['file_path'],
            $data['file_size'] ?? null,
            $data['file_type'] ?? null,
            $data['google_drive_id'] ?? null,
            $data['google_drive_link'] ?? null
        ];

        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }

    /**
     * Delete a file
     */
    public function delete($fileId)
    {
        $sql = "DELETE FROM submission_files WHERE file_id = ?";
        return $this->db->query($sql, [$fileId]);
    }

    /**
     * Delete all files for a submission
     */
    public function deleteBySubmission($submissionId)
    {
        $sql = "DELETE FROM submission_files WHERE submission_id = ?";
        return $this->db->query($sql, [$submissionId]);
    }

    /**
     * Count files for a submission
     */
    public function countBySubmission($submissionId)
    {
        $row = $this->db->fetch("SELECT COUNT(*) as total FROM submission_files WHERE submission_id = ?", [$submissionId]);
        return $row['total'] ?? 0;
    }

    /**
     * Get total file size for a submission
     */
    public function getTotalSizeBySubmission($submissionId)
    {
        $row = $this->db->fetch("SELECT SUM(file_size) as total FROM submission_files WHERE submission_id = ?", [$submissionId]);
        return $row['total'] ?? 0;
    }
}
