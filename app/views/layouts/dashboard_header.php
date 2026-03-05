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

            <!-- Right: Notifications + User Profile -->
            <div class="d-flex align-items-center gap-2">

                <!-- Notification Bell -->
                <button class="btn position-relative p-2 d-flex align-items-center justify-content-center" style="width:46px;height:46px;background:#f4f6f9;border:1px solid #e2e6ea;border-radius:8px;" title="Notifications" disabled="">
                    <i class="bi bi-bell" style="font-size:1rem;color:#092C4C;"></i>
                    <!-- Badge placeholder — uncomment when notification feature is live:
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem;">3</span>
                -->
                </button>



                <!-- User Profile -->
                <div class="dropdown">
                    <a class="d-flex align-items-center gap-2 text-decoration-none navbar-user-btn" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="background:#f4f6f9;border:1px solid #e2e6ea;border-radius:8px;padding:5px 12px 5px 12px;transition:background .2s;">
                        <!-- Avatar -->
                        <?php if (!empty($_SESSION['profile'])): ?>
                            <img src="<?php echo env('APP_URL'); ?>/public/<?php echo htmlspecialchars($_SESSION['profile']); ?>" alt="Profile" class="rounded-circle flex-shrink-0" style="width:36px;height:36px;object-fit:cover;border:2px solid #092C4C;">
                        <?php else: ?>
                            <span class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;background:#092C4C;">
                                <i class="bi bi-person-fill" style="color:#fff;font-size:.95rem;"></i>
                            </span>
                        <?php endif; ?>
                        <!-- Name & role -->
                        <div class="d-none d-sm-block lh-sm">
                            <div class="fw-semibold" style="font-size:.82rem;color:#092C4C;"><?php echo $fullname; ?></div>
                            <div style="font-size:.72rem;color:#6c757d;"><?php echo $roleLabel; ?></div>
                        </div>
                        <!-- Modern chevron -->
                        <i class="bi bi-chevron-down d-none d-sm-inline" style="font-size:.7rem;color:#6c757d;margin-left:2px;"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width: 280px;">
                        <!-- Profile Quick View -->
                        <li class="px-3 py-2">
                            <div class="d-flex align-items-center mb-2">
                                <div class="me-3">
                                    <?php if (!empty($_SESSION['profile'])): ?>
                                        <img src="<?php echo env('APP_URL'); ?>/public/<?php echo htmlspecialchars($_SESSION['profile']); ?>" alt="Profile" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #092C4C;">
                                    <?php else: ?>
                                        <i class="bi bi-person-circle" style="font-size: 3rem; color: #092C4C;"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="fw-bold"><?php echo $fullname; ?></div>
                                    <div style="font-size:.72rem;color:#6c757d;"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></div>
                                </div>
                            </div>
                            <a href="<?php echo env('APP_URL'); ?>/<?php echo $roleBase; ?>/settings" class="btn btn-sm btn-outline-primary w-100">
                                <i class="bi bi-gear me-1"></i> Manage Profile
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="<?php echo env('APP_URL'); ?>/auth/logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>

            </div><!-- /d-flex notifications+profile -->
        </div>
    </nav>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-search px-3 pt-3 pb-2">
            <div class="position-relative">
                <i class="bi bi-search position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: #6c757d; font-size: 0.8rem; z-index: 10;"></i>
                <input type="text" class="form-control form-control-sm py-2" id="sidebarSearch" placeholder="Search menu..." style="padding-left: 35px; border: 1px solid #ced4da; border-radius: 8px; font-size: 0.8rem;">
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