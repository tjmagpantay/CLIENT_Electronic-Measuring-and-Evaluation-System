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
