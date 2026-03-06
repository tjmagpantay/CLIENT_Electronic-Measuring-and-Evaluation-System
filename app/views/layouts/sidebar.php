<?php
$role = $_SESSION['role'] ?? '';
$roleBase = '';
switch ($role) {
    case 'SUPER_ADMIN':
        $roleBase = 'superadmin';
        break;
    case 'ADMIN':
        $roleBase = 'admin';
        break;
    case 'LGU_OFFICER':
        $roleBase = 'officer';
        break;
}

$currentUrl = $_GET['url'] ?? '';
$segments = explode('/', trim($currentUrl, '/'));
$currentPage = $segments[1] ?? 'index';

function isActive($page, $current)
{
    return $page === $current ? 'active' : '';
}
?>

<?php if ($role === 'SUPER_ADMIN'): ?>
    <li class="sidebar-item <?php echo isActive('index', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/superadmin" class="sidebar-link">
            <i class="bi bi-grid-1x2"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="sidebar-item <?php echo isActive('offices', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/superadmin/offices" class="sidebar-link">
            <i class="bi bi-people"></i>
            <span>Offices</span>
        </a>
    </li>
    <li class="sidebar-item <?php echo isActive('reports', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/superadmin/reports" class="sidebar-link">
            <i class="bi bi-file-earmark-text"></i>
            <span>Report Types</span>
        </a>
    </li>
    <li class="sidebar-item <?php echo isActive('periods', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/superadmin/periods" class="sidebar-link">
            <i class="bi bi-calendar3"></i>
            <span>Reporting Periods</span>
        </a>
    </li>
    <li class="sidebar-item <?php echo isActive('submissions', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/superadmin/submissions" class="sidebar-link">
            <i class="bi bi-clipboard-check"></i>
            <span>Submissions</span>
        </a>
    </li>
    <li class="sidebar-item <?php echo isActive('users', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/superadmin/users" class="sidebar-link">
            <i class="bi bi-person-lines-fill"></i>
            <span>Users</span>
        </a>
    </li>
    <li class="sidebar-item <?php echo isActive('announcements', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/superadmin/announcements" class="sidebar-link">
            <i class="bi bi-megaphone"></i>
            <span>Announcement</span>
        </a>
    </li>
    <li class="sidebar-item <?php echo isActive('logs', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/superadmin/logs" class="sidebar-link">
            <i class="bi bi-journal-text"></i>
            <span>Audit Logs</span>
        </a>
    </li>
    <li class="sidebar-item <?php echo isActive('settings', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/superadmin/settings" class="sidebar-link">
            <i class="bi bi-gear"></i>
            <span>Settings</span>
        </a>
    </li>

<?php elseif ($role === 'ADMIN'): ?>
    <li class="sidebar-item <?php echo isActive('index', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/admin" class="sidebar-link">
            <i class="bi bi-grid-1x2"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="sidebar-item <?php echo isActive('offices', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/admin/offices" class="sidebar-link">
            <i class="bi bi-people"></i>
            <span>Offices</span>
        </a>
    </li>
    <li class="sidebar-item <?php echo isActive('reporttypes', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/admin/reporttypes" class="sidebar-link">
            <i class="bi bi-file-earmark-text"></i>
            <span>Report Types</span>
        </a>
    </li>
    <li class="sidebar-item <?php echo isActive('periods', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/admin/periods" class="sidebar-link">
            <i class="bi bi-calendar3"></i>
            <span>Reporting Periods</span>
        </a>
    </li>
    <li class="sidebar-item <?php echo isActive('submissions', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/admin/submissions" class="sidebar-link">
            <i class="bi bi-clipboard-check"></i>
            <span>Submissions</span>
        </a>
    </li>
    <li class="sidebar-item <?php echo isActive('reports', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/admin/reports" class="sidebar-link">
            <i class="bi bi-bar-chart-line"></i>
            <span>Reports</span>
        </a>
    </li>
    <li class="sidebar-item <?php echo isActive('announcements', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/admin/announcements" class="sidebar-link">
            <i class="bi bi-megaphone"></i>
            <span>Announcement</span>
        </a>
    </li>
    <li class="sidebar-item <?php echo isActive('settings', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/admin/settings" class="sidebar-link">
            <i class="bi bi-gear"></i>
            <span>Settings</span>
        </a>
    </li>

<?php elseif ($role === 'LGU_OFFICER'): ?>
    <li class="sidebar-item <?php echo isActive('index', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/officer" class="sidebar-link">
            <i class="bi bi-grid-1x2"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="sidebar-item <?php echo isActive('submissions', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/officer/submissions" class="sidebar-link">
            <i class="bi bi-clipboard-check"></i>
            <span>My Submissions</span>
        </a>
    </li>
    <li class="sidebar-item <?php echo isActive('submit', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/officer/submit" class="sidebar-link">
            <i class="bi bi-upload"></i>
            <span>Submit Report</span>
        </a>
    </li>
    <li class="sidebar-item <?php echo isActive('settings', $currentPage); ?>">
        <a href="<?php echo env('APP_URL'); ?>/officer/settings" class="sidebar-link">
            <i class="bi bi-gear"></i>
            <span>Settings</span>
        </a>
    </li>
<?php endif; ?>

<!-- Spacer to push logout to bottom (only for officers) -->
<?php if ($role === 'LGU_OFFICER'): ?>
    <li style="flex-grow: 1; min-height: 20px;"></li>

    <!-- Logout Button at Very Bottom -->
    <li class="sidebar-item">
        <a href="<?php echo env('APP_URL'); ?>/auth/logout" class="sidebar-link text-danger">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </li>
<?php endif; ?>