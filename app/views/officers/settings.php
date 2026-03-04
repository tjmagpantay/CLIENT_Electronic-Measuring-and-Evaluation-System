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
    <!-- Left Side: Account Info + Office Info -->
    <div class="col-lg-4">
        <!-- Account Info Card -->
        <div class="settings-card p-4 text-center mb-4">
            <div class="mb-3">
                <?php if (!empty($user['profile'])): ?>
                    <img src="<?php echo env('APP_URL'); ?>/public/<?php echo htmlspecialchars($user['profile']); ?>" alt="Profile" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #092C4C;">
                <?php else: ?>
                    <i class="bi bi-person-circle" style="font-size: 5rem; color: #092C4C;"></i>
                <?php endif; ?>
            </div>
            <h6 class="fw-bold"><?php echo htmlspecialchars(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '')); ?></h6>
            <span class="badge rounded-pill" style="background-color: #198754; color: #fff;">LGU Officer</span>
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

        <!-- Office Information Card -->
        <div class="settings-card p-4">
            <h6 class="fw-bold mb-3" style="color: #092C4C;">
                <i class="bi bi-building me-2"></i>Office Information
            </h6>
            <?php if ($office): ?>
                <div class="mb-3">
                    <div class="rounded-3 p-3 mb-3 text-center" style="background-color: rgba(9,44,76,0.1);">
                        <i class="bi bi-building fs-2" style="color: #092C4C;"></i>
                    </div>
                    <h6 class="fw-bold text-center mb-2"><?php echo htmlspecialchars($office['office_name']); ?></h6>
                    <div class="text-center mb-3">
                        <span class="badge bg-secondary"><?php echo $office['office_type']; ?></span>
                    </div>
                </div>
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted small fw-semibold" style="width: 100px;">Office ID</td>
                        <td class="small"><?php echo $office['office_id']; ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted small fw-semibold">Type</td>
                        <td class="small"><?php echo $office['office_type']; ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted small fw-semibold">Cluster</td>
                        <td class="small"><?php echo $office['cluster'] ? 'Cluster ' . $office['cluster'] : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted small fw-semibold">Status</td>
                        <td class="small">
                            <?php if ($office['status'] === 'ACTIVE'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            <?php else: ?>
                <div class="text-center py-3">
                    <i class="bi bi-building display-6 text-muted"></i>
                    <p class="small text-muted mt-2 mb-0">No office assigned</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Side: Profile Information Form -->
    <div class="col-lg-8">
        <div class="settings-card p-4">
            <h6 class="fw-bold mb-3" style="color: #092C4C;">
                <i class="bi bi-person me-2"></i>Profile Information
            </h6>
            <form action="<?php echo env('APP_URL'); ?>/officer/updateprofile" method="POST" enctype="multipart/form-data">
                <!-- Profile Picture Upload -->
                <div class="mb-4 pb-3 border-bottom">
                    <label class="form-label small fw-semibold mb-2">Profile Picture</label>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <?php if (!empty($user['profile'])): ?>
                                <img src="<?php echo env('APP_URL'); ?>/public/<?php echo htmlspecialchars($user['profile']); ?>" alt="Profile" id="profilePreview" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover; border: 2px solid #092C4C;">
                            <?php else: ?>
                                <i class="bi bi-person-circle" id="profilePreview" style="font-size: 5rem; color: #092C4C;"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <input type="file" class="form-control form-control-sm" name="profile_image" id="profileImageInput" accept="image/*">
                            <small class="text-muted">JPG, PNG or GIF (max 2MB)</small>
                        </div>
                    </div>
                </div>

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
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user333333['username'] ?? ''); ?>" disabled>
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
</div>

<script>
    // Profile image preview
    document.getElementById('profileImageInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imageUrl = e.target.result;

                // Update form preview (80px version)
                const preview = document.getElementById('profilePreview');
                if (preview.tagName === 'I') {
                    // Replace icon with image
                    const img = document.createElement('img');
                    img.src = imageUrl;
                    img.id = 'profilePreview';
                    img.className = 'rounded-circle';
                    img.style = 'width: 80px; height: 80px; object-fit: cover; border: 2px solid #092C4C;';
                    preview.replaceWith(img);
                } else {
                    preview.src = imageUrl;
                }

                // Update Account Info Card preview (100px version)
                const accountCardPreview = document.querySelector('.settings-card .mb-3 img, .settings-card .mb-3 i.bi-person-circle');
                if (accountCardPreview) {
                    if (accountCardPreview.tagName === 'I') {
                        // Replace icon with image
                        const img = document.createElement('img');
                        img.src = imageUrl;
                        img.className = 'rounded-circle';
                        img.alt = 'Profile';
                        img.style = 'width: 100px; height: 100px; object-fit: cover; border: 3px solid #092C4C;';
                        accountCardPreview.replaceWith(img);
                    } else {
                        accountCardPreview.src = imageUrl;
                    }
                }
            };
            reader.readAsDataURL(file);
        }
    });
</script>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>