<?php

class HomeController extends Controller
{
    public function index()
    {
        if ($this->isLoggedIn()) {
            $role = $this->getSession('role');
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
        } else {
            $this->redirect('auth/login');
        }
    }
}
