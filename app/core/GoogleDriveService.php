<?php

/**
 * GoogleDriveService
 * Handles file uploads to DILG-managed Google Drive organized by cluster and month
 */
class GoogleDriveService
{
    private $client;
    private $service;
    private $db;
    private $enabled = false;

    public function __construct()
    {
        $this->db = new Database();
        $this->initializeGoogleClient();
    }

    /**
     * Initialize Google API Client
     */
    private function initializeGoogleClient()
    {
        $credentialsPath = __DIR__ . '/../../config/google_drive_credentials.json';

        // Check if credentials file exists
        if (!file_exists($credentialsPath)) {
            error_log('Google Drive credentials not found. Drive integration disabled.');
            return;
        }

        try {
            // Check if Google API client is installed
            if (!class_exists('Google_Client')) {
                error_log('Google API Client library not installed. Run: composer install');
                return;
            }

            $this->client = new Google_Client();
            $this->client->setAuthConfig($credentialsPath);
            $this->client->addScope(Google_Service_Drive::DRIVE_FILE);
            $this->client->setApplicationName('LGMES - LGU Reporting System');

            $this->service = new Google_Service_Drive($this->client);
            $this->enabled = true;
        } catch (Exception $e) {
            error_log('Failed to initialize Google Drive: ' . $e->getMessage());
        }
    }

    /**
     * Check if Google Drive is enabled
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Upload file to Google Drive
     * 
     * @param string $filePath Local file path
     * @param string $fileName File name
     * @param string $cluster Cluster number (1, 2, or 3)
     * @param int $month Month number
     * @param int $year Year
     * @return array ['success' => bool, 'file_id' => string, 'web_link' => string, 'error' => string]
     */
    public function uploadFile($filePath, $fileName, $cluster, $month, $year)
    {
        if (!$this->enabled) {
            return [
                'success' => false,
                'error' => 'Google Drive integration is not enabled'
            ];
        }

        try {
            // Get cluster folder ID
            $clusterFolderId = $this->getClusterFolderId($cluster);
            if (!$clusterFolderId) {
                return [
                    'success' => false,
                    'error' => 'Cluster folder not configured'
                ];
            }

            // Get or create month folder
            $monthFolderName = sprintf('%04d-%02d', $year, $month);
            $monthFolderId = $this->getOrCreateFolder($monthFolderName, $clusterFolderId);

            if (!$monthFolderId) {
                return [
                    'success' => false,
                    'error' => 'Failed to create/access month folder'
                ];
            }

            // Upload file to Google Drive
            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $fileName,
                'parents' => [$monthFolderId]
            ]);

            $content = file_get_contents($filePath);
            $mimeType = mime_content_type($filePath);

            $file = $this->service->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => $mimeType,
                'uploadType' => 'multipart',
                'fields' => 'id, webViewLink, webContentLink'
            ]);

            // Make file accessible to anyone with the link
            $permission = new Google_Service_Drive_Permission([
                'type' => 'anyone',
                'role' => 'reader'
            ]);
            $this->service->permissions->create($file->id, $permission);

            return [
                'success' => true,
                'file_id' => $file->id,
                'web_link' => $file->webViewLink,
                'download_link' => $file->webContentLink,
                'error' => ''
            ];
        } catch (Exception $e) {
            error_log('Google Drive upload error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Upload failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get cluster folder ID from database
     */
    private function getClusterFolderId($cluster)
    {
        $sql = "SELECT folder_id FROM drive_folder_config WHERE cluster = ? AND is_active = 1";
        $result = $this->db->fetch($sql, [$cluster]);
        return $result ? $result['folder_id'] : null;
    }

    /**
     * Get or create a folder in Google Drive
     */
    private function getOrCreateFolder($folderName, $parentFolderId)
    {
        try {
            // Check if folder already exists
            $query = "name='{$folderName}' and '{$parentFolderId}' in parents and mimeType='application/vnd.google-apps.folder' and trashed=false";
            $results = $this->service->files->listFiles([
                'q' => $query,
                'fields' => 'files(id, name)'
            ]);

            if (count($results->getFiles()) > 0) {
                return $results->getFiles()[0]->id;
            }

            // Create new folder
            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $folderName,
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents' => [$parentFolderId]
            ]);

            $folder = $this->service->files->create($fileMetadata, [
                'fields' => 'id'
            ]);

            return $folder->id;
        } catch (Exception $e) {
            error_log('Folder creation error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete file from Google Drive
     */
    public function deleteFile($fileId)
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $this->service->files->delete($fileId);
            return true;
        } catch (Exception $e) {
            error_log('Failed to delete Drive file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cluster folder URL
     */
    public function getClusterFolderUrl($cluster)
    {
        $sql = "SELECT folder_url FROM drive_folder_config WHERE cluster = ? AND is_active = 1";
        $result = $this->db->fetch($sql, [$cluster]);
        return $result ? $result['folder_url'] : null;
    }
}
