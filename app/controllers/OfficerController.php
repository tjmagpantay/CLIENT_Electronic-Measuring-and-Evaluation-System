<?php

class OfficerController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'LGU_OFFICER') {
            $this->redirect('auth/login');
        }
    }

    public function index()
    {
        $data = [
            'title' => 'Officer Dashboard - LGMES'
        ];
        $this->view('officers/index', $data);
    }
}
