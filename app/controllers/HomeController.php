<?php

/**
 * Home Controller
 * Default controller for the application
 */

class HomeController extends Controller
{

    public function index()
    {
        $data = [
            'title' => 'Welcome to E-MES',
            'description' => 'This is a PHP MVC application'
        ];

        $this->view('home/index', $data);
    }

    public function about()
    {
        $data = [
            'title' => 'About Us',
            'description' => 'Learn more about E-MES'
        ];

        $this->view('home/about', $data);
    }
}
