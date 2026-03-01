<?php

class AdminController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
            $this->redirect('auth/login');
        }
    }

    public function index()
    {
        $data = [
            'title' => 'Admin Dashboard - LGMES'
        ];
        $this->view('admin/index', $data);
    }
}
