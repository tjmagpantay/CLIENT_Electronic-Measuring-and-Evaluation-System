<?php

class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);

        $viewFile = __DIR__ . '/../views/' . $view . '.php';

        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View '{$view}' not found.");
        }
    }

    protected function redirect($url)
    {
        header('Location: ' . env('APP_URL') . '/' . $url);
        exit;
    }

    protected function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    protected function getSession($key)
    {
        return $_SESSION[$key] ?? null;
    }

    protected function setSession($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    protected function destroySession()
    {
        session_unset();
        session_destroy();
    }
}
