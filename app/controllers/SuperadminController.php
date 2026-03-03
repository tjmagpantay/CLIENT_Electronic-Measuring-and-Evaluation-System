<?php

class SuperadminController extends Controller
{
    private $userModel;
    private $officeModel;
    private $submissionModel;
    private $announcementModel;

    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'SUPER_ADMIN') {
            $this->redirect('auth/login');
        }

        require_once __DIR__ . '/../models/User.php';
        require_once __DIR__ . '/../models/Office.php';
        require_once __DIR__ . '/../models/ReportType.php';
        require_once __DIR__ . '/../models/ReportingPeriod.php';
        require_once __DIR__ . '/../models/Submission.php';
        require_once __DIR__ . '/../models/Announcement.php';

        $this->userModel = new User();
        $this->officeModel = new Office();
        $this->submissionModel = new Submission();
        $this->announcementModel = new Announcement();
    }

    // ── Dashboard ──
    public function index()
    {
        $reportTypeModel = new ReportType();
        $reportingPeriodModel = new ReportingPeriod();

        $totalSubmissions = $this->submissionModel->countAll();
        $onTime = $this->submissionModel->countOnTime();
        $late = $this->submissionModel->countLate();
        $noSubmission = $this->submissionModel->countByStatus('NO_SUBMISSION');

        $data = [
            'title' => 'Super Admin Dashboard - LGMES',
            'totalUsers' => $this->userModel->countAll(),
            'activeUsers' => $this->userModel->countActive(),
            'totalOffices' => $this->officeModel->countAll(),
            'totalSubmissions' => $totalSubmissions,
            'pendingCount' => $noSubmission,
            'onTimeCount' => $onTime,
            'lateCount' => $late,
            'reportTypes' => $reportTypeModel->getActive(),
            'recentSubmissions' => $this->submissionModel->getRecent(5),
            'reportDeadlines' => $reportTypeModel->getAll()
        ];
        $this->view('super_admin/index', $data);
    }

    // ── Users ──
    public function users()
    {
        $data = [
            'title' => 'Users - LGMES',
            'users' => $this->userModel->getAllUsers(),
            'offices' => $this->officeModel->getActive()
        ];
        $this->view('super_admin/users', $data);
    }

    public function createuser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('superadmin/users');
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        $middlename = trim($_POST['middlename'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? '';
        $officeId = $_POST['office_id'] ?? null;
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($officeId)) $officeId = null;

        if (empty($username) || empty($email) || empty($firstname) || empty($lastname) || empty($password) || empty($role)) {
            $_SESSION['flash_error'] = 'All required fields must be filled.';
            $this->redirect('superadmin/users');
            return;
        }

        if ($this->userModel->findByUsername($username)) {
            $_SESSION['flash_error'] = 'Username already exists.';
            $this->redirect('superadmin/users');
            return;
        }

        if ($this->userModel->findByEmail($email)) {
            $_SESSION['flash_error'] = 'Email already in use.';
            $this->redirect('superadmin/users');
            return;
        }

        if (strlen($password) < 6) {
            $_SESSION['flash_error'] = 'Password must be at least 6 characters.';
            $this->redirect('superadmin/users');
            return;
        }

        $this->userModel->createUser([
            'username' => $username,
            'email' => $email,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'middlename' => $middlename,
            'password' => $password,
            'role' => $role,
            'is_active' => $isActive,
            'office_id' => $officeId
        ]);

        $_SESSION['flash_success'] = 'User created successfully.';
        $this->redirect('superadmin/users');
    }

    public function edituser($id = null)
    {
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('superadmin/users');
            return;
        }

        $user = $this->userModel->findById($id);
        if (!$user) {
            $this->redirect('superadmin/users');
            return;
        }

        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        $middlename = trim($_POST['middlename'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? '';
        $officeId = $_POST['office_id'] ?? null;
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $newPassword = $_POST['new_password'] ?? '';

        if (empty($officeId)) $officeId = null;

        if (empty($firstname) || empty($lastname) || empty($email) || empty($role)) {
            $_SESSION['flash_error'] = 'All required fields must be filled.';
            $this->redirect('superadmin/users');
            return;
        }

        $existingEmail = $this->userModel->findByEmail($email);
        if ($existingEmail && $existingEmail['user_id'] != $id) {
            $_SESSION['flash_error'] = 'Email already in use by another account.';
            $this->redirect('superadmin/users');
            return;
        }

        $this->userModel->updateUser($id, [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'middlename' => $middlename,
            'email' => $email,
            'role' => $role,
            'is_active' => $isActive,
            'office_id' => $officeId
        ]);

        if (!empty($newPassword)) {
            if (strlen($newPassword) < 6) {
                $_SESSION['flash_error'] = 'Password must be at least 6 characters. Other changes were saved.';
                $this->redirect('superadmin/users');
                return;
            }
            $this->userModel->updatePassword($id, $newPassword);
        }

        $_SESSION['flash_success'] = 'User updated successfully.';
        $this->redirect('superadmin/users');
    }

    public function toggleuser($id = null)
    {
        if ($id) {
            if ($id == $_SESSION['user_id']) {
                $_SESSION['flash_error'] = 'You cannot deactivate your own account.';
            } else {
                $this->userModel->toggleActive($id);
                $_SESSION['flash_success'] = 'User status updated.';
            }
        }
        $this->redirect('superadmin/users');
    }

    // ── Offices ──
    public function offices()
    {
        $data = [
            'title' => 'Offices - LGMES',
            'offices' => $this->officeModel->getAll()
        ];
        $this->view('super_admin/offices', $data);
    }

    public function createoffice()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $officeName = trim($_POST['office_name'] ?? '');
            $officeType = $_POST['office_type'] ?? '';
            $cluster = $_POST['cluster'] ?? null;
            $status = $_POST['status'] ?? 'ACTIVE';

            if (empty($officeName) || empty($officeType)) {
                $data = [
                    'title' => 'Create Office - LGMES',
                    'error' => 'Office name and type are required.'
                ];
                $this->view('super_admin/createoffice', $data);
                return;
            }

            $this->officeModel->create([
                'office_name' => $officeName,
                'office_type' => $officeType,
                'cluster' => $cluster,
                'status' => $status
            ]);

            $_SESSION['flash_success'] = 'Office created successfully.';
            $this->redirect('superadmin/offices');
            return;
        }

        $data = [
            'title' => 'Create Office - LGMES',
            'error' => ''
        ];
        $this->view('super_admin/createoffice', $data);
    }

    public function editoffice($id = null)
    {
        if (!$id) {
            $this->redirect('superadmin/offices');
            return;
        }

        $office = $this->officeModel->getById($id);
        if (!$office) {
            $this->redirect('superadmin/offices');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $officeName = trim($_POST['office_name'] ?? '');
            $officeType = $_POST['office_type'] ?? '';
            $cluster = $_POST['cluster'] ?? null;
            $status = $_POST['status'] ?? 'ACTIVE';

            if (empty($officeName) || empty($officeType)) {
                $data = [
                    'title' => 'Edit Office - LGMES',
                    'office' => $office,
                    'error' => 'Office name and type are required.'
                ];
                $this->view('super_admin/editoffice', $data);
                return;
            }

            $this->officeModel->update($id, [
                'office_name' => $officeName,
                'office_type' => $officeType,
                'cluster' => $cluster,
                'status' => $status
            ]);

            $_SESSION['flash_success'] = 'Office updated successfully.';
            $this->redirect('superadmin/offices');
            return;
        }

        $data = [
            'title' => 'Edit Office - LGMES',
            'office' => $office,
            'error' => ''
        ];
        $this->view('super_admin/editoffice', $data);
    }

    // ── Reports ──
    public function reports()
    {
        $reportTypeModel = new ReportType();
        $data = [
            'title' => 'Report Types - LGMES',
            'reportTypes' => $reportTypeModel->getAll()
        ];
        $this->view('super_admin/reports', $data);
    }

    public function createreporttype()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('superadmin/reports');
            return;
        }

        $reportTypeModel = new ReportType();

        $reportCode = strtoupper(trim($_POST['report_code'] ?? ''));
        $reportTitle = trim($_POST['report_title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $opr = trim($_POST['opr'] ?? '');
        $templateLink = trim($_POST['template_link'] ?? '');
        $deadlineDay = intval($_POST['deadline_day'] ?? 15);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($reportCode) || empty($reportTitle)) {
            $_SESSION['flash_error'] = 'Report code and title are required.';
            $this->redirect('superadmin/reports');
            return;
        }

        if ($reportTypeModel->codeExists($reportCode)) {
            $_SESSION['flash_error'] = 'Report code already exists.';
            $this->redirect('superadmin/reports');
            return;
        }

        $reportTypeModel->create([
            'report_code' => $reportCode,
            'report_title' => $reportTitle,
            'description' => $description,
            'opr' => $opr,
            'template_link' => $templateLink,
            'deadline_day' => $deadlineDay,
            'is_active' => $isActive
        ]);

        $_SESSION['flash_success'] = 'Report type created successfully.';
        $this->redirect('superadmin/reports');
    }

    public function updatereporttype()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('superadmin/reports');
            return;
        }

        $reportTypeModel = new ReportType();

        $reportTypeId = intval($_POST['report_type_id'] ?? 0);
        $reportCode = strtoupper(trim($_POST['report_code'] ?? ''));
        $reportTitle = trim($_POST['report_title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $opr = trim($_POST['opr'] ?? '');
        $templateLink = trim($_POST['template_link'] ?? '');
        $deadlineDay = intval($_POST['deadline_day'] ?? 15);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($reportCode) || empty($reportTitle)) {
            $_SESSION['flash_error'] = 'Report code and title are required.';
            $this->redirect('superadmin/reports');
            return;
        }

        if ($reportTypeModel->codeExists($reportCode, $reportTypeId)) {
            $_SESSION['flash_error'] = 'Report code already exists.';
            $this->redirect('superadmin/reports');
            return;
        }

        $reportTypeModel->update($reportTypeId, [
            'report_code' => $reportCode,
            'report_title' => $reportTitle,
            'description' => $description,
            'opr' => $opr,
            'template_link' => $templateLink,
            'deadline_day' => $deadlineDay,
            'is_active' => $isActive
        ]);

        $_SESSION['flash_success'] = 'Report type updated successfully.';
        $this->redirect('superadmin/reports');
    }

    public function deletereporttype()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('superadmin/reports');
            return;
        }

        $reportTypeModel = new ReportType();
        $reportTypeId = intval($_POST['report_type_id'] ?? 0);

        $reportTypeModel->delete($reportTypeId);

        $_SESSION['flash_success'] = 'Report type deleted successfully.';
        $this->redirect('superadmin/reports');
    }

    // ── Reporting Periods ──
    public function periods()
    {
        $reportingPeriodModel = new ReportingPeriod();
        $data = [
            'title' => 'Reporting Periods - LGMES',
            'periods' => $reportingPeriodModel->getAll(),
            'periodModel' => $reportingPeriodModel
        ];
        $this->view('super_admin/periods', $data);
    }

    public function createperiod()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('superadmin/periods');
            return;
        }

        $reportingPeriodModel = new ReportingPeriod();

        $periodMonth = intval($_POST['period_month'] ?? 0);
        $periodYear = intval($_POST['period_year'] ?? date('Y'));
        $deadline = $_POST['deadline'] ?? null;
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($periodMonth < 1 || $periodMonth > 12) {
            $_SESSION['flash_error'] = 'Invalid month selected.';
            $this->redirect('superadmin/periods');
            return;
        }

        if ($reportingPeriodModel->periodExists($periodMonth, $periodYear)) {
            $_SESSION['flash_error'] = 'This reporting period already exists.';
            $this->redirect('superadmin/periods');
            return;
        }

        $reportingPeriodModel->create([
            'period_month' => $periodMonth,
            'period_year' => $periodYear,
            'deadline' => $deadline,
            'is_active' => $isActive
        ]);

        $_SESSION['flash_success'] = 'Reporting period created successfully.';
        $this->redirect('superadmin/periods');
    }

    public function updateperiod()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('superadmin/periods');
            return;
        }

        $reportingPeriodModel = new ReportingPeriod();

        $periodId = intval($_POST['period_id'] ?? 0);
        $periodMonth = intval($_POST['period_month'] ?? 0);
        $periodYear = intval($_POST['period_year'] ?? date('Y'));
        $deadline = $_POST['deadline'] ?? null;
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($periodMonth < 1 || $periodMonth > 12) {
            $_SESSION['flash_error'] = 'Invalid month selected.';
            $this->redirect('superadmin/periods');
            return;
        }

        if ($reportingPeriodModel->periodExists($periodMonth, $periodYear, $periodId)) {
            $_SESSION['flash_error'] = 'This reporting period already exists.';
            $this->redirect('superadmin/periods');
            return;
        }

        $reportingPeriodModel->update($periodId, [
            'period_month' => $periodMonth,
            'period_year' => $periodYear,
            'deadline' => $deadline,
            'is_active' => $isActive
        ]);

        $_SESSION['flash_success'] = 'Reporting period updated successfully.';
        $this->redirect('superadmin/periods');
    }

    public function deleteperiod()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('superadmin/periods');
            return;
        }

        $reportingPeriodModel = new ReportingPeriod();
        $periodId = intval($_POST['period_id'] ?? 0);

        $reportingPeriodModel->delete($periodId);

        $_SESSION['flash_success'] = 'Reporting period deleted successfully.';
        $this->redirect('superadmin/periods');
    }

    // ── Submissions ──
    public function submissions()
    {
        $reportTypeModel = new ReportType();
        $reportingPeriodModel = new ReportingPeriod();

        // Get all submissions
        $submissions = $this->submissionModel->getAll();

        $data = [
            'title' => 'Submissions - LGMES',
            'submissions' => $submissions,
            'offices' => $this->officeModel->getAll(),
            'reportTypes' => $reportTypeModel->getAll(),
            'periods' => $reportingPeriodModel->getAll(),
            'periodModel' => $reportingPeriodModel
        ];

        $this->view('super_admin/submissions', $data);
    }

    // ── Audit Logs ──
    public function logs()
    {
        $data = [
            'title' => 'Audit Logs - LGMES'
        ];
        $this->view('super_admin/logs', $data);
    }

    // ── Announcements ──
    public function announcements()
    {
        $data = [
            'title' => 'Announcements - LGMES',
            'announcements' => $this->announcementModel->getAll()
        ];
        $this->view('super_admin/announcements', $data);
    }

    public function createannouncement()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('superadmin/announcements');
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $effectivityDate = $_POST['effectivity_date'] ?? '';
        $expiryDate = $_POST['expiry_date'] ?? '';
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($title) || empty($description) || empty($effectivityDate)) {
            $_SESSION['flash_error'] = 'Title, description, and effectivity date are required.';
            $this->redirect('superadmin/announcements');
            return;
        }

        if (empty($expiryDate)) $expiryDate = null;

        // Handle image upload
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($ext, $allowed)) {
                $_SESSION['flash_error'] = 'Invalid image type. Allowed: JPG, PNG, GIF, WEBP.';
                $this->redirect('superadmin/announcements');
                return;
            }

            if ($file['size'] > 5 * 1024 * 1024) {
                $_SESSION['flash_error'] = 'Image size must not exceed 5MB.';
                $this->redirect('superadmin/announcements');
                return;
            }

            $uploadsDir = __DIR__ . '/../../public/uploads/announcements/';
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }

            $filename = 'announcement_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadsDir . $filename)) {
                $imagePath = 'uploads/announcements/' . $filename;
            }
        }

        $this->announcementModel->create([
            'title' => $title,
            'description' => $description,
            'image_path' => $imagePath,
            'effectivity_date' => $effectivityDate,
            'expiry_date' => $expiryDate,
            'is_active' => $isActive,
            'created_by' => $_SESSION['user_id']
        ]);

        $_SESSION['flash_success'] = 'Announcement created successfully.';
        $this->redirect('superadmin/announcements');
    }

    public function editannouncement($id = null)
    {
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('superadmin/announcements');
            return;
        }

        $announcement = $this->announcementModel->getById($id);
        if (!$announcement) {
            $this->redirect('superadmin/announcements');
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $effectivityDate = $_POST['effectivity_date'] ?? '';
        $expiryDate = $_POST['expiry_date'] ?? '';
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($title) || empty($description) || empty($effectivityDate)) {
            $_SESSION['flash_error'] = 'Title, description, and effectivity date are required.';
            $this->redirect('superadmin/announcements');
            return;
        }

        if (empty($expiryDate)) $expiryDate = null;

        // Handle image upload (keep existing if no new upload)
        $imagePath = $announcement['image_path'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($ext, $allowed)) {
                $_SESSION['flash_error'] = 'Invalid image type. Allowed: JPG, PNG, GIF, WEBP.';
                $this->redirect('superadmin/announcements');
                return;
            }

            if ($file['size'] > 5 * 1024 * 1024) {
                $_SESSION['flash_error'] = 'Image size must not exceed 5MB.';
                $this->redirect('superadmin/announcements');
                return;
            }

            $uploadsDir = __DIR__ . '/../../public/uploads/announcements/';
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }

            // Delete old image if exists
            if ($imagePath && file_exists(__DIR__ . '/../../public/' . $imagePath)) {
                unlink(__DIR__ . '/../../public/' . $imagePath);
            }

            $filename = 'announcement_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadsDir . $filename)) {
                $imagePath = 'uploads/announcements/' . $filename;
            }
        }

        $this->announcementModel->update($id, [
            'title' => $title,
            'description' => $description,
            'image_path' => $imagePath,
            'effectivity_date' => $effectivityDate,
            'expiry_date' => $expiryDate,
            'is_active' => $isActive
        ]);

        $_SESSION['flash_success'] = 'Announcement updated successfully.';
        $this->redirect('superadmin/announcements');
    }

    public function deleteannouncement($id = null)
    {
        if ($id) {
            $announcement = $this->announcementModel->getById($id);
            if ($announcement) {
                // Delete image file if exists
                if ($announcement['image_path'] && file_exists(__DIR__ . '/../../public/' . $announcement['image_path'])) {
                    unlink(__DIR__ . '/../../public/' . $announcement['image_path']);
                }
                $this->announcementModel->delete($id);
                $_SESSION['flash_success'] = 'Announcement deleted.';
            }
        }
        $this->redirect('superadmin/announcements');
    }

    public function toggleannouncement($id = null)
    {
        if ($id) {
            $this->announcementModel->toggleActive($id);
            $_SESSION['flash_success'] = 'Announcement status updated.';
        }
        $this->redirect('superadmin/announcements');
    }

    // ── Settings ──
    public function settings()
    {
        $user = $this->userModel->findById($_SESSION['user_id']);
        $data = [
            'title' => 'Settings - LGMES',
            'user' => $user,
            'success' => '',
            'error' => ''
        ];
        $this->view('super_admin/settings', $data);
    }

    public function updateprofile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('superadmin/settings');
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
            $data = [
                'title' => 'Settings - LGMES',
                'user' => $user,
                'success' => '',
                'error' => 'First name, last name, and email are required.'
            ];
            $this->view('super_admin/settings', $data);
            return;
        }

        $existingUser = $this->userModel->findByEmail($email);
        if ($existingUser && $existingUser['user_id'] != $userId) {
            $user = $this->userModel->findById($userId);
            $data = [
                'title' => 'Settings - LGMES',
                'user' => $user,
                'success' => '',
                'error' => 'Email is already in use by another account.'
            ];
            $this->view('super_admin/settings', $data);
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
                $data = [
                    'title' => 'Settings - LGMES',
                    'user' => $this->userModel->findById($userId),
                    'success' => 'Profile updated successfully.',
                    'error' => 'Current password is required to change password.'
                ];
                $this->view('super_admin/settings', $data);
                return;
            }

            if (!password_verify($currentPassword, $user['password_hash'])) {
                $data = [
                    'title' => 'Settings - LGMES',
                    'user' => $this->userModel->findById($userId),
                    'success' => 'Profile updated successfully.',
                    'error' => 'Current password is incorrect.'
                ];
                $this->view('super_admin/settings', $data);
                return;
            }

            if ($newPassword !== $confirmPassword) {
                $data = [
                    'title' => 'Settings - LGMES',
                    'user' => $this->userModel->findById($userId),
                    'success' => 'Profile updated successfully.',
                    'error' => 'New passwords do not match.'
                ];
                $this->view('super_admin/settings', $data);
                return;
            }

            if (strlen($newPassword) < 6) {
                $data = [
                    'title' => 'Settings - LGMES',
                    'user' => $this->userModel->findById($userId),
                    'success' => 'Profile updated successfully.',
                    'error' => 'New password must be at least 6 characters.'
                ];
                $this->view('super_admin/settings', $data);
                return;
            }

            $this->userModel->updatePassword($userId, $newPassword);
        }

        $data = [
            'title' => 'Settings - LGMES',
            'user' => $this->userModel->findById($userId),
            'success' => 'Profile updated successfully.',
            'error' => ''
        ];
        $this->view('super_admin/settings', $data);
    }
}
