<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'LGMES'; ?></title>
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS (local) -->
    <link rel="stylesheet" href="<?php echo env('APP_URL'); ?>/public/vendor/bootstrap/css/bootstrap.min.css">
    <!-- Bootstrap Icons (local) -->
    <link rel="stylesheet" href="<?php echo env('APP_URL'); ?>/public/vendor/bootstrap-icons/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo env('APP_URL'); ?>/public/css/style.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <nav class="navbar fixed-top dashboard-topnav">
            <!-- Left: Hamburger + Brand -->
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center">
                <a class="navbar-brand d-flex align-items-center mb-0" href="<?php echo env('APP_URL'); ?>">
                    <img src="<?php echo env('APP_URL'); ?>/public/img/dilg_logo.png" alt="DILG Logo" height="48" class="me-2">
                    <span class="fw-bold">LGMES</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow-1">