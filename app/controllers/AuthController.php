<?php

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        require_once __DIR__ . '/../models/User.php';
        $this->userModel = new User();
    }

    public function login()
    {
        if ($this->isLoggedIn()) {
            $this->redirectByRole($this->getSession('role'));
            return;
        }

        $data = [
            'title' => 'Login - LGMES',
            'error' => '',
            'username' => '',
            'email' => ''
        ];

        $this->view('auth/login', $data);
    }

    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('auth/login');
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($email) || empty($password)) {
            $data = [
                'title' => 'Login - LGMES',
                'error' => 'All fields are required.',
                'username' => $username,
                'email' => $email
            ];
            $this->view('auth/login', $data);
            return;
        }

        $user = $this->userModel->authenticate($username, $password);

        if (!$user) {
            $data = [
                'title' => 'Login - LGMES',
                'error' => 'Invalid username or password.',
                'username' => $username,
                'email' => $email
            ];
            $this->view('auth/login', $data);
            return;
        }

        // Verify email matches
        if ($user['email'] !== $email) {
            $data = [
                'title' => 'Login - LGMES',
                'error' => 'Invalid credentials. Please check your email.',
                'username' => $username,
                'email' => $email
            ];
            $this->view('auth/login', $data);
            return;
        }

        // Set session data
        $this->setSession('user_id', $user['user_id']);
        $this->setSession('username', $user['username']);
        $this->setSession('email', $user['email']);
        $this->setSession('firstname', $user['firstname']);
        $this->setSession('lastname', $user['lastname']);
        $this->setSession('role', $user['role']);
        $this->setSession('office_id', $user['office_id']);
        $this->setSession('profile', $user['profile'] ?? '');

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        $this->redirectByRole($user['role']);
    }

    public function logout()
    {
        $this->destroySession();
        $this->redirect('auth/login');
    }

    private function redirectByRole($role)
    {
        switch ($role) {
            case 'SUPER_ADMIN':
                $this->redirect('superadmin');
                break;
            case 'ADMIN':
                $this->redirect('admin');
                break;
            case 'LGU_OFFICER':
                $this->redirect('officer');
                break;
            default:
                $this->redirect('auth/login');
        }
    }
}
