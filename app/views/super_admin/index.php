<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold" style="color: #092C4C;">Super Admin Dashboard</h4>
            <p class="text-muted mb-0">Welcome, <?php echo htmlspecialchars($_SESSION['firstname'] . ' ' . $_SESSION['lastname']); ?></p>
        </div>
        <a href="<?php echo env('APP_URL'); ?>/auth/logout" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>

    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Super Admin panel is under development.
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>