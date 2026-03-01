<?php

class SuperadminController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'SUPER_ADMIN') {
            $this->redirect('auth/login');
        }
    }

    public function index()
    {
        $data = [
            'title' => 'Super Admin Dashboard - LGMES'
        ];
        $this->view('super_admin/index', $data);
    }
}
