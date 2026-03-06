<?php

class AdminController extends Controller
{
    private $userModel;
    private $officeModel;
    private $reportTypeModel;
    private $periodModel;
    private $submissionModel;
    private $announcementModel;

    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
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
        $this->reportTypeModel = new ReportType();
        $this->periodModel = new ReportingPeriod();
        $this->submissionModel = new Submission();
        $this->announcementModel = new Announcement();
    }

    // ── Dashboard ──
    public function index()
    {
        $totalSubmissions = $this->submissionModel->countAll();
        $onTime = $this->submissionModel->countOnTime();
        $late = $this->submissionModel->countLate();
        $noSubmission = $this->submissionModel->countByStatus('NO_SUBMISSION');

        $data = [
            'title' => 'Admin Dashboard - LGMES',
            'totalOffices' => $this->officeModel->countAll(),
            'totalSubmissions' => $totalSubmissions,
            'pendingCount' => $noSubmission,
            'onTimeCount' => $onTime,
            'lateCount' => $late,
            'reportTypes' => $this->reportTypeModel->getActive(),
            'recentSubmissions' => $this->submissionModel->getRecent(5),
            'reportDeadlines' => $this->reportTypeModel->getAll()
        ];
        $this->view('admin/index', $data);
    }

    // ── Offices ──
    public function offices()
    {
        $data = [
            'title' => 'Offices - LGMES',
            'offices' => $this->officeModel->getAll()
        ];
        $this->view('admin/offices', $data);
    }

    // ── Report Types ──
    public function reporttypes()
    {
        $data = [
            'title' => 'Report Types - LGMES',
            'reportTypes' => $this->reportTypeModel->getAll()
        ];
        $this->view('admin/reporttypes', $data);
    }

    // ── Reporting Periods ──
    public function periods()
    {
        $data = [
            'title' => 'Reporting Periods - LGMES',
            'periods' => $this->periodModel->getAll(),
            'periodModel' => $this->periodModel
        ];
        $this->view('admin/periods', $data);
    }

    // ── Submissions ──
    public function submissions()
    {
        $data = [
            'title' => 'Submissions - LGMES',
            'submissions' => $this->submissionModel->getAll(),
            'periodModel' => $this->periodModel
        ];
        $this->view('admin/submissions', $data);
    }

    // ── Reports ──
    public function reports()
    {
        $data = [
            'title' => 'Reports - LGMES',
            'offices' => $this->officeModel->getActive(),
            'reportTypes' => $this->reportTypeModel->getActive(),
            'periods' => $this->periodModel->getActive()
        ];
        $this->view('admin/reports', $data);
    }

    // ── Announcements ──
    public function announcements()
    {
        $data = [
            'title' => 'Announcements - LGMES',
            'announcements' => $this->announcementModel->getAll()
        ];
        $this->view('admin/announcements', $data);
    }

    public function createannouncement()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/announcements');
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $effectivityDate = $_POST['effectivity_date'] ?? '';
        $expiryDate = $_POST['expiry_date'] ?? '';
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($title) || empty($description) || empty($effectivityDate)) {
            $_SESSION['flash_error'] = 'Title, description, and effectivity date are required.';
            $this->redirect('admin/announcements');
            return;
        }

        if (empty($expiryDate)) $expiryDate = null;

        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($ext, $allowed)) {
                $_SESSION['flash_error'] = 'Invalid image type. Allowed: JPG, PNG, GIF, WEBP.';
                $this->redirect('admin/announcements');
                return;
            }

            if ($file['size'] > 5 * 1024 * 1024) {
                $_SESSION['flash_error'] = 'Image size must not exceed 5MB.';
                $this->redirect('admin/announcements');
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
        $this->redirect('admin/announcements');
    }

    public function editannouncement($id = null)
    {
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/announcements');
            return;
        }

        $announcement = $this->announcementModel->getById($id);
        if (!$announcement) {
            $this->redirect('admin/announcements');
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $effectivityDate = $_POST['effectivity_date'] ?? '';
        $expiryDate = $_POST['expiry_date'] ?? '';
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($title) || empty($description) || empty($effectivityDate)) {
            $_SESSION['flash_error'] = 'Title, description, and effectivity date are required.';
            $this->redirect('admin/announcements');
            return;
        }

        if (empty($expiryDate)) $expiryDate = null;

        $imagePath = $announcement['image_path'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($ext, $allowed)) {
                $_SESSION['flash_error'] = 'Invalid image type. Allowed: JPG, PNG, GIF, WEBP.';
                $this->redirect('admin/announcements');
                return;
            }

            if ($file['size'] > 5 * 1024 * 1024) {
                $_SESSION['flash_error'] = 'Image size must not exceed 5MB.';
                $this->redirect('admin/announcements');
                return;
            }

            $uploadsDir = __DIR__ . '/../../public/uploads/announcements/';
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }

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
        $this->redirect('admin/announcements');
    }

    public function deleteannouncement($id = null)
    {
        if ($id) {
            $announcement = $this->announcementModel->getById($id);
            if ($announcement) {
                if ($announcement['image_path'] && file_exists(__DIR__ . '/../../public/' . $announcement['image_path'])) {
                    unlink(__DIR__ . '/../../public/' . $announcement['image_path']);
                }
                $this->announcementModel->delete($id);
                $_SESSION['flash_success'] = 'Announcement deleted.';
            }
        }
        $this->redirect('admin/announcements');
    }

    public function toggleannouncement($id = null)
    {
        if ($id) {
            $this->announcementModel->toggleActive($id);
            $_SESSION['flash_success'] = 'Announcement status updated.';
        }
        $this->redirect('admin/announcements');
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
        $this->view('admin/settings', $data);
    }

    public function updateprofile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/settings');
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
            $this->view('admin/settings', ['title' => 'Settings - LGMES', 'user' => $user, 'success' => '', 'error' => 'First name, last name, and email are required.']);
            return;
        }

        $existingUser = $this->userModel->findByEmail($email);
        if ($existingUser && $existingUser['user_id'] != $userId) {
            $user = $this->userModel->findById($userId);
            $this->view('admin/settings', ['title' => 'Settings - LGMES', 'user' => $user, 'success' => '', 'error' => 'Email is already in use by another account.']);
            return;
        }

        // Handle profile image upload
        $profilePath = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_image'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($ext, $allowed)) {
                $user = $this->userModel->findById($userId);
                $this->view('admin/settings', ['title' => 'Settings - LGMES', 'user' => $user, 'success' => '', 'error' => 'Invalid image type. Allowed: JPG, PNG, GIF.']);
                return;
            }

            if ($file['size'] > 2 * 1024 * 1024) {
                $user = $this->userModel->findById($userId);
                $this->view('admin/settings', ['title' => 'Settings - LGMES', 'user' => $user, 'success' => '', 'error' => 'Image size must not exceed 2MB.']);
                return;
            }

            $uploadsDir = __DIR__ . '/../../public/uploads/profiles/';
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }

            // Delete old profile image if exists
            $oldUser = $this->userModel->findById($userId);
            if (!empty($oldUser['profile']) && file_exists(__DIR__ . '/../../public/' . $oldUser['profile'])) {
                unlink(__DIR__ . '/../../public/' . $oldUser['profile']);
            }

            $filename = 'profile_' . $userId . '_' . time() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadsDir . $filename)) {
                $profilePath = 'uploads/profiles/' . $filename;
            }
        }

        $updateData = [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'middlename' => $middlename,
            'email' => $email
        ];

        if ($profilePath) {
            $updateData['profile'] = $profilePath;
            $_SESSION['profile'] = $profilePath;
        }

        $this->userModel->updateProfile($userId, $updateData);

        $_SESSION['firstname'] = $firstname;
        $_SESSION['lastname'] = $lastname;

        if (!empty($newPassword)) {
            $user = $this->userModel->findById($userId);
            if (empty($currentPassword)) {
                $this->view('admin/settings', ['title' => 'Settings - LGMES', 'user' => $this->userModel->findById($userId), 'success' => 'Profile updated.', 'error' => 'Current password is required to change password.']);
                return;
            }
            if (!password_verify($currentPassword, $user['password_hash'])) {
                $this->view('admin/settings', ['title' => 'Settings - LGMES', 'user' => $this->userModel->findById($userId), 'success' => 'Profile updated.', 'error' => 'Current password is incorrect.']);
                return;
            }
            if ($newPassword !== $confirmPassword) {
                $this->view('admin/settings', ['title' => 'Settings - LGMES', 'user' => $this->userModel->findById($userId), 'success' => 'Profile updated.', 'error' => 'New passwords do not match.']);
                return;
            }
            if (strlen($newPassword) < 6) {
                $this->view('admin/settings', ['title' => 'Settings - LGMES', 'user' => $this->userModel->findById($userId), 'success' => 'Profile updated.', 'error' => 'New password must be at least 6 characters.']);
                return;
            }
            $this->userModel->updatePassword($userId, $newPassword);
        }

        $this->view('admin/settings', ['title' => 'Settings - LGMES', 'user' => $this->userModel->findById($userId), 'success' => 'Profile updated successfully.', 'error' => '']);
    }
}
