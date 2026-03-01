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
            'submissions' => $officeId ? $this->submissionModel->getByOffice($officeId) : [],
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

        // Handle file upload
        if (!isset($_FILES['report_file']) || $_FILES['report_file']['error'] !== UPLOAD_ERR_OK) {
            $this->renderSubmitWithError('Please select a file to upload.');
            return;
        }

        $file = $_FILES['report_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];

        if (!in_array($ext, $allowed)) {
            $this->renderSubmitWithError('Invalid file type. Allowed: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG.');
            return;
        }

        if ($file['size'] > 10 * 1024 * 1024) {
            $this->renderSubmitWithError('File size must not exceed 10MB.');
            return;
        }

        $uploadsDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }

        $filename = 'report_' . $officeId . '_' . $reportTypeId . '_' . $periodId . '_' . time() . '.' . $ext;

        if (!move_uploaded_file($file['tmp_name'], $uploadsDir . $filename)) {
            $this->renderSubmitWithError('Failed to upload file. Please try again.');
            return;
        }

        $fileLink = 'uploads/' . $filename;

        // Determine ON_TIME or LATE
        $period = $this->periodModel->getById($periodId);
        $reportType = $this->reportTypeModel->getById($reportTypeId);
        $deadlineDay = $reportType['default_deadline_day'] ?? 15;
        $deadlineDate = sprintf('%04d-%02d-%02d', $period['period_year'], $period['period_month'], $deadlineDay);
        $status = (date('Y-m-d') <= $deadlineDate) ? 'ON_TIME' : 'LATE';

        $this->submissionModel->create([
            'office_id' => $officeId,
            'report_type_id' => $reportTypeId,
            'period_id' => $periodId,
            'submitted_by' => $userId,
            'file_link' => $fileLink,
            'submission_status' => $status,
            'remarks' => $remarks
        ]);

        $officeId = $_SESSION['office_id'] ?? null;
        $data = [
            'title' => 'Submit Report - LGMES',
            'reportTypes' => $this->reportTypeModel->getActive(),
            'periods' => $this->periodModel->getActive(),
            'office' => $officeId ? $this->officeModel->getById($officeId) : null,
            'error' => '',
            'success' => 'Report submitted successfully! Status: ' . str_replace('_', ' ', $status)
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
