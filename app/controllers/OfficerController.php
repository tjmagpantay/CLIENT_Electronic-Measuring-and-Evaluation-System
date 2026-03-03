<?php

class OfficerController extends Controller
{
    private $submissionModel;
    private $reportTypeModel;
    private $periodModel;
    private $officeModel;
    private $userModel;

    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'LGU_OFFICER') {
            $this->redirect('auth/login');
        }

        require_once __DIR__ . '/../models/Submission.php';
        require_once __DIR__ . '/../models/ReportType.php';
        require_once __DIR__ . '/../models/ReportingPeriod.php';
        require_once __DIR__ . '/../models/Office.php';
        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/Announcement.php';

        $this->submissionModel = new Submission();
        $this->reportTypeModel = new ReportType();
        $this->periodModel = new ReportingPeriod();
        $this->officeModel = new Office();
        $this->userModel = new User();
    }

    public function index()
    {
        $officeId = $_SESSION['office_id'] ?? null;
        $announcementModel = new Announcement();

        $data = [
            'title' => 'Officer Dashboard - LGMES',
            'submittedCount' => $officeId ? $this->submissionModel->countByOfficeAndStatus($officeId, 'ON_TIME') + $this->submissionModel->countByOfficeAndStatus($officeId, 'LATE') : 0,
            'pendingCount' => $officeId ? $this->submissionModel->countByOfficeAndStatus($officeId, 'NO_SUBMISSION') : 0,
            'overdueCount' => $officeId ? $this->submissionModel->countByOfficeAndStatus($officeId, 'LATE') : 0,
            'announcements' => $announcementModel->getActive()
        ];
        $this->view('officers/index', $data);
    }

    public function submissions()
    {
        $officeId = $_SESSION['office_id'] ?? null;
        $data = [
            'title' => 'My Submissions - LGMES',
            'submissions' => $officeId ? $this->submissionModel->getByOfficeWithFiles($officeId) : [],
            'periodModel' => $this->periodModel,
            'onTimeCount' => $officeId ? $this->submissionModel->countByOfficeAndStatus($officeId, 'ON_TIME') : 0,
            'lateCount' => $officeId ? $this->submissionModel->countByOfficeAndStatus($officeId, 'LATE') : 0,
            'pendingCount' => $officeId ? $this->submissionModel->countByOfficeAndStatus($officeId, 'NO_SUBMISSION') : 0,
            'reportTypes' => $this->reportTypeModel->getActive()
        ];
        $this->view('officers/submissions', $data);
    }

    public function submit()
    {
        $officeId = $_SESSION['office_id'] ?? null;
        $data = [
            'title' => 'Submit Report - LGMES',
            'reportTypes' => $this->reportTypeModel->getActive(),
            'periods' => $this->periodModel->getActive(),
            'office' => $officeId ? $this->officeModel->getById($officeId) : null,
            'error' => '',
            'success' => ''
        ];
        $this->view('officers/submit', $data);
    }

    public function upload()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('officer/submit');
            return;
        }

        $officeId = $_SESSION['office_id'] ?? null;
        $userId = $_SESSION['user_id'];
        $reportTypeId = $_POST['report_type_id'] ?? '';
        $periodId = $_POST['period_id'] ?? '';
        $remarks = trim($_POST['remarks'] ?? '');

        // Validation
        if (empty($officeId) || empty($reportTypeId) || empty($periodId)) {
            $this->renderSubmitWithError('Please fill in all required fields.');
            return;
        }

        if ($this->submissionModel->existsForOfficePeriodReport($officeId, $periodId, $reportTypeId)) {
            $this->renderSubmitWithError('A submission already exists for this report and period.');
            return;
        }

        // Get office cluster
        $office = $this->officeModel->getById($officeId);
        $cluster = $office['cluster'] ?? null;

        if (empty($cluster)) {
            $this->renderSubmitWithError('Your office is not assigned to a cluster. Please contact administrator.');
            return;
        }

        // Handle multiple file uploads
        if (!isset($_FILES['report_files']) || empty($_FILES['report_files']['name'][0])) {
            $this->renderSubmitWithError('Please select at least one file to upload.');
            return;
        }

        $files = $_FILES['report_files'];
        $fileCount = count($files['name']);
        $allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];
        $maxFileSize = 10 * 1024 * 1024; // 10MB

        // Validate all files first
        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $this->renderSubmitWithError('Invalid file type: ' . htmlspecialchars($files['name'][$i]) . '. Allowed: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG.');
                return;
            }

            if ($files['size'][$i] > $maxFileSize) {
                $this->renderSubmitWithError('File size must not exceed 10MB: ' . htmlspecialchars($files['name'][$i]));
                return;
            }
        }

        // Create temporary upload directory
        $uploadsDir = __DIR__ . '/../../public/uploads/temp/';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }

        // Determine ON_TIME or LATE status based on period deadline
        $period = $this->periodModel->getById($periodId);
        $reportType = $this->reportTypeModel->getById($reportTypeId);

        // Use the deadline set by Super Admin for this period
        $deadlineDate = $period['deadline'] ?? date('Y-m-d 23:59:59', strtotime('+1 month'));
        $status = (date('Y-m-d H:i:s') <= $deadlineDate) ? 'ON_TIME' : 'LATE';

        // Initialize Google Drive Service
        require_once __DIR__ . '/../core/GoogleDriveService.php';
        $driveService = new GoogleDriveService();

        // Create submission record
        $submissionId = $this->submissionModel->create([
            'office_id' => $officeId,
            'report_type_id' => $reportTypeId,
            'period_id' => $periodId,
            'submitted_by' => $userId,
            'file_link' => '', // Legacy field
            'submission_status' => $status,
            'remarks' => $remarks
        ]);

        // Upload files
        require_once __DIR__ . '/../models/SubmissionFile.php';
        $fileModel = new SubmissionFile();
        $uploadedFiles = [];
        $driveErrors = [];

        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
            $originalName = pathinfo($files['name'][$i], PATHINFO_FILENAME);
            $filename = $office['office_name'] . '_' . $reportType['report_code'] . '_' . $period['period_month'] . '-' . $period['period_year'] . '_' . ($i + 1) . '.' . $ext;
            $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $filename); // Sanitize filename

            // Save temporarily
            $tempPath = $uploadsDir . $filename;
            if (!move_uploaded_file($files['tmp_name'][$i], $tempPath)) {
                continue;
            }

            // Upload to Google Drive
            $driveResult = null;
            if ($driveService->isEnabled()) {
                $driveResult = $driveService->uploadFile(
                    $tempPath,
                    $filename,
                    $cluster,
                    $period['period_month'],
                    $period['period_year']
                );
            }

            // Store file record
            $fileModel->create([
                'submission_id' => $submissionId,
                'file_name' => $originalName . '.' . $ext,
                'file_path' => 'uploads/temp/' . $filename, // Temporary local path
                'file_size' => $files['size'][$i],
                'file_type' => $ext,
                'google_drive_id' => ($driveResult && isset($driveResult['success']) && $driveResult['success']) ? $driveResult['file_id'] : null,
                'google_drive_link' => ($driveResult && isset($driveResult['success']) && $driveResult['success']) ? $driveResult['web_link'] : null
            ]);

            if ($driveResult && isset($driveResult['success']) && $driveResult['success']) {
                $uploadedFiles[] = $filename;
                // Delete temp file after successful Drive upload
                @unlink($tempPath);
            } else {
                $driveErrors[] = $filename . ': ' . (($driveResult && isset($driveResult['error'])) ? $driveResult['error'] : 'Google Drive not configured');
            }
        }

        if (empty($uploadedFiles) && empty($driveErrors)) {
            $this->renderSubmitWithError('Failed to upload any files. Please try again.');
            return;
        }

        // Prepare success message
        $successMsg = 'Report submitted successfully! ' . count($uploadedFiles) . ' file(s) uploaded to Google Drive. Status: ' . str_replace('_', ' ', $status);

        if (!empty($driveErrors)) {
            $successMsg .= ' (Note: Google Drive integration not fully configured. Files stored locally.)';
        }

        $officeId = $_SESSION['office_id'] ?? null;
        $data = [
            'title' => 'Submit Report - LGMES',
            'reportTypes' => $this->reportTypeModel->getActive(),
            'periods' => $this->periodModel->getActive(),
            'office' => $officeId ? $this->officeModel->getById($officeId) : null,
            'error' => '',
            'success' => $successMsg
        ];
        $this->view('officers/submit', $data);
    }

    public function office()
    {
        $officeId = $_SESSION['office_id'] ?? null;
        $data = [
            'title' => 'My Office - LGMES',
            'office' => $officeId ? $this->officeModel->getById($officeId) : null
        ];
        $this->view('officers/office', $data);
    }

    private function renderSubmitWithError($error)
    {
        $officeId = $_SESSION['office_id'] ?? null;
        $data = [
            'title' => 'Submit Report - LGMES',
            'reportTypes' => $this->reportTypeModel->getActive(),
            'periods' => $this->periodModel->getActive(),
            'office' => $officeId ? $this->officeModel->getById($officeId) : null,
            'error' => $error,
            'success' => ''
        ];
        $this->view('officers/submit', $data);
    }

    public function settings()
    {
        $user = $this->userModel->findById($_SESSION['user_id']);
        $data = [
            'title' => 'Settings - LGMES',
            'user' => $user,
            'success' => '',
            'error' => ''
        ];
        $this->view('officers/settings', $data);
    }

    public function updateprofile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('officer/settings');
            return;
        }

        $userId = $_SESSION['user_id'];
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        $middlename = trim($_POST['middlename'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($firstname) || empty($lastname) || empty($email)) {
            $user = $this->userModel->findById($userId);
            $this->view('officers/settings', ['title' => 'Settings - LGMES', 'user' => $user, 'success' => '', 'error' => 'First name, last name, and email are required.']);
            return;
        }

        $existingUser = $this->userModel->findByEmail($email);
        if ($existingUser && $existingUser['user_id'] != $userId) {
            $user = $this->userModel->findById($userId);
            $this->view('officers/settings', ['title' => 'Settings - LGMES', 'user' => $user, 'success' => '', 'error' => 'Email is already in use by another account.']);
            return;
        }

        $this->userModel->updateProfile($userId, [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'middlename' => $middlename,
            'email' => $email
        ]);

        $_SESSION['firstname'] = $firstname;
        $_SESSION['lastname'] = $lastname;

        if (!empty($newPassword)) {
            $user = $this->userModel->findById($userId);
            if (empty($currentPassword)) {
                $this->view('officers/settings', ['title' => 'Settings - LGMES', 'user' => $this->userModel->findById($userId), 'success' => 'Profile updated.', 'error' => 'Current password is required to change password.']);
                return;
            }
            if (!password_verify($currentPassword, $user['password_hash'])) {
                $this->view('officers/settings', ['title' => 'Settings - LGMES', 'user' => $this->userModel->findById($userId), 'success' => 'Profile updated.', 'error' => 'Current password is incorrect.']);
                return;
            }
            if ($newPassword !== $confirmPassword) {
                $this->view('officers/settings', ['title' => 'Settings - LGMES', 'user' => $this->userModel->findById($userId), 'success' => 'Profile updated.', 'error' => 'New passwords do not match.']);
                return;
            }
            if (strlen($newPassword) < 6) {
                $this->view('officers/settings', ['title' => 'Settings - LGMES', 'user' => $this->userModel->findById($userId), 'success' => 'Profile updated.', 'error' => 'New password must be at least 6 characters.']);
                return;
            }
            $this->userModel->updatePassword($userId, $newPassword);
        }

        $this->view('officers/settings', ['title' => 'Settings - LGMES', 'user' => $this->userModel->findById($userId), 'success' => 'Profile updated successfully.', 'error' => '']);
    }
}
