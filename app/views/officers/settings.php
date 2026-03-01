<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<!-- Page Header -->
<div class="page-header">
    <h4>Settings</h4>
    <p>Manage your account and profile information.</p>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Profile Information -->
    <div class="col-lg-8">
        <div class="settings-card p-4">
            <h6 class="fw-bold mb-3" style="color: #092C4C;">
                <i class="bi bi-person me-2"></i>Profile Information
            </h6>
            <form action="<?php echo env('APP_URL'); ?>/officer/updateprofile" method="POST">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="firstname" class="form-control" value="<?php echo htmlspecialchars($user['firstname'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Middle Name</label>
                        <input type="text" name="middlename" class="form-control" value="<?php echo htmlspecialchars($user['middlename'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="lastname" class="form-control" value="<?php echo htmlspecialchars($user['lastname'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Username</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" disabled>
                        <small class="text-muted">Username cannot be changed.</small>
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="fw-bold mb-3" style="color: #092C4C;">
                    <i class="bi bi-shield-lock me-2"></i>Change Password
                </h6>
                <p class="text-muted small mb-3">Leave blank if you don't want to change your password.</p>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Current Password</label>
                        <input type="password" name="current_password" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">New Password</label>
                        <input type="password" name="new_password" class="form-control" minlength="6">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" minlength="6">
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn text-white px-4" style="background-color: #092C4C;">
                        <i class="bi bi-check-lg me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Account Info Sidebar -->
    <div class="col-lg-4">
        <div class="settings-card p-4 text-center">
            <div class="mb-3">
                <i class="bi bi-person-circle" style="font-size: 4rem; color: #092C4C;"></i>
            </div>
            <h6 class="fw-bold"><?php echo htmlspecialchars(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '')); ?></h6>
            <span class="badge rounded-pill" style="background-color: #198754; color: #fff;">LGU Officer</span>
            <?php if (!empty($user['office_name'])): ?>
                <p class="text-muted small mt-2 mb-0"><?php echo htmlspecialchars($user['office_name']); ?></p>
            <?php endif; ?>
            <hr>
            <div class="text-start">
                <p class="small mb-2">
                    <i class="bi bi-envelope me-2 text-muted"></i>
                    <?php echo htmlspecialchars($user['email'] ?? ''); ?>
                </p>
                <p class="small mb-2">
                    <i class="bi bi-person me-2 text-muted"></i>
                    @<?php echo htmlspecialchars($user['username'] ?? ''); ?>
                </p>
                <p class="small mb-0">
                    <i class="bi bi-calendar me-2 text-muted"></i>
                    Joined <?php echo isset($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : 'N/A'; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>