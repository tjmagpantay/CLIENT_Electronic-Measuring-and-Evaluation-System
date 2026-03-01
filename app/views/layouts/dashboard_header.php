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

<?php
$role = $_SESSION['role'] ?? '';
$fullname = htmlspecialchars(($_SESSION['firstname'] ?? '') . ' ' . ($_SESSION['lastname'] ?? ''));
$roleLabel = '';
$roleBase = '';
switch ($role) {
    case 'SUPER_ADMIN':
        $roleLabel = 'Super Admin';
        $roleBase = 'superadmin';
        break;
    case 'ADMIN':
        $roleLabel = 'Admin';
        $roleBase = 'admin';
        break;
    case 'LGU_OFFICER':
        $roleLabel = 'LGU Officer';
        $roleBase = 'officer';
        break;
}
?>

<body id="body-dashboard">
    <!-- Top Navbar -->
    <nav class="navbar fixed-top dashboard-topnav">
        <div class="container-fluid px-3">
            <!-- Left: Hamburger + Brand -->
            <div class="d-flex align-items-center">
                <button class="btn btn-link me-2 p-0 sidebar-toggle" type="button" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <a class="navbar-brand d-flex align-items-center mb-0" href="<?php echo env('APP_URL'); ?>">
                    <img src="<?php echo env('APP_URL'); ?>/public/img/dilg_logo.png" alt="DILG Logo" height="48" class="me-2">
                    <span class="fw-bold">LGMES</span>
                </a>
            </div>

            <!-- Right: User Profile -->
            <div class="dropdown">
                <a class="d-flex align-items-center text-decoration-none dropdown-toggle navbar-user-btn" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar me-2">
                        <i class="bi bi-person-circle fs-4"></i>
                    </div>
                    <div class="d-none d-sm-block text-end lh-sm">
                        <span class="fw-semibold small"><?php echo $fullname; ?></span><br>
                        <span class="user-role"><?php echo $roleLabel; ?></span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><a class="dropdown-item" href="<?php echo env('APP_URL'); ?>/<?php echo $roleBase; ?>/settings"><i class="bi bi-person me-2"></i>My Profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="<?php echo env('APP_URL'); ?>/auth/logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-search px-3 pt-3 pb-2">
            <div class="position-relative">
                <i class="bi bi-search position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: #6c757d; font-size: 0.8rem;"></i>
                <input type="text" class="form-control form-control-sm" id="sidebarSearch" placeholder="Search menu..." style="padding-left: 32px; background-color: #f0f2f5; border: none; border-radius: 8px; font-size: 0.8rem;">
            </div>
        </div>
        <ul class="sidebar-nav">
            <?php require_once __DIR__ . '/sidebar.php'; ?>
        </ul>
    </aside>

    <!-- Overlay for mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content Wrapper -->
    <div class="dashboard-content" id="dashboardContent">
        <div class="container-fluid p-4">
