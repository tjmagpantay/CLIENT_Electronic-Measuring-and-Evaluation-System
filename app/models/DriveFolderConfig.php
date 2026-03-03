<?php

class DriveFolderConfig
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all drive folder configurations
     */
    public function getAll()
    {
        $sql = "SELECT * FROM drive_folder_config ORDER BY cluster ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get configuration for a specific cluster
     */
    public function getByCluster($cluster)
    {
        $sql = "SELECT * FROM drive_folder_config WHERE cluster = ? AND is_active = 1";
        return $this->db->fetch($sql, [$cluster]);
    }

    /**
     * Update or create cluster folder configuration
     */
    public function upsert($cluster, $folderId, $folderUrl)
    {
        // Check if exists
        $existing = $this->getByCluster($cluster);

        if ($existing) {
            $sql = "UPDATE drive_folder_config SET folder_id = ?, folder_url = ? WHERE cluster = ?";
            return $this->db->query($sql, [$folderId, $folderUrl, $cluster]);
        } else {
            $sql = "INSERT INTO drive_folder_config (cluster, folder_id, folder_url) VALUES (?, ?, ?)";
            return $this->db->query($sql, [$cluster, $folderId, $folderUrl]);
        }
    }

    /**
     * Get folder URL for a cluster
     */
    public function getFolderUrl($cluster)
    {
        $config = $this->getByCluster($cluster);
        return $config ? $config['folder_url'] : null;
    }

    /**
     * Get folder ID for a cluster
     */
    public function getFolderId($cluster)
    {
        $config = $this->getByCluster($cluster);
        return $config ? $config['folder_id'] : null;
    }

    /**
     * Check if cluster is configured
     */
    public function isConfigured($cluster)
    {
        $config = $this->getByCluster($cluster);
        return !empty($config) && !empty($config['folder_id']);
    }
}
