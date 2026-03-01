<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LGMES</title>
    <link rel="icon" type="image/x-icon" href="/img/dilg_logo.png">

<?php

session_start();

// Load configuration
require_once __DIR__ . '/../config/env.php';

// Load core classes
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/App.php';

// Initialize application
$app = new App();
