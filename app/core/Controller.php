<?php

/**
 * Base Controller Class
 * All controllers extend this class
 */

class Controller
{
    /**
     * Load model
     */
    public function model($model)
    {
        $modelPath = __DIR__ . '/../models/' . $model . '.php';

        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $model();
        } else {
            die("Model $model not found!");
        }
    }

    /**
     * Load view
     */
    public function view($view, $data = [])
    {
        $viewPath = __DIR__ . '/../views/' . $view . '.php';

        if (file_exists($viewPath)) {
            // Extract data array to variables
            extract($data);
            require_once $viewPath;
        } else {
            die("View $view not found!");
        }
    }

    /**
     * Redirect to another page
     */
    public function redirect($url)
    {
        header('Location: ' . $url);
        exit();
    }

    /**
     * Get POST data
     */
    public function getPost($key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get GET data
     */
    public function getQuery($key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Sanitize input
     */
    public function sanitize($data)
    {
        return htmlspecialchars(strip_tags(trim($data)));
    }

    /**
     * Check if request is POST
     */
    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Return JSON response
     */
    public function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
