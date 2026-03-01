<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<!-- Page Header -->
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4>Users</h4>
        <p>Manage system users, assign roles, and control access.</p>
    </div>
    <button class="btn text-white" style="background-color: #092C4C;" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-plus-lg me-1"></i> Add User
    </button>
</div>

<!-- Flash Messages -->
<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['flash_success']);
                                                unset($_SESSION['flash_success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($_SESSION['flash_error']);
                                                        unset($_SESSION['flash_error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Users Table -->
<div class="card dash-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #f8f9fa;">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Office</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No users found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; background-color: #092C4C; color: #fff; font-size: 0.8rem; font-weight: 600;">
                                            <?php echo strtoupper(substr($u['firstname'], 0, 1) . substr($u['lastname'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <span class="fw-semibold small"><?php echo htmlspecialchars($u['firstname'] . ' ' . $u['lastname']); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="small"><?php echo htmlspecialchars($u['username']); ?></span></td>
                                <td><span class="small"><?php echo htmlspecialchars($u['email']); ?></span></td>
                                <td>
                                    <?php
                                    $roleBadge = match ($u['role']) {
                                        'SUPER_ADMIN' => '<span class="badge rounded-pill text-white" style="background-color: #092C4C;">Super Admin</span>',
                                        'ADMIN' => '<span class="badge rounded-pill" style="background-color: #F3AF0E; color: #092C4C;">Admin</span>',
                                        'LGU_OFFICER' => '<span class="badge rounded-pill bg-success">LGU Officer</span>',
                                        default => '<span class="badge bg-secondary">Unknown</span>'
                                    };
                                    echo $roleBadge;
                                    ?>
                                </td>
                                <td><span class="small text-muted"><?php echo htmlspecialchars($u['office_name'] ?? '—'); ?></span></td>
                                <td>
                                    <?php if ($u['is_active']): ?>
                                        <span class="badge rounded-pill bg-success bg-opacity-10 text-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary me-1 btn-edit-user" title="Edit"
                                        data-id="<?php echo $u['user_id']; ?>"
                                        data-firstname="<?php echo htmlspecialchars($u['firstname']); ?>"
                                        data-lastname="<?php echo htmlspecialchars($u['lastname']); ?>"
                                        data-middlename="<?php echo htmlspecialchars($u['middlename'] ?? ''); ?>"
                                        data-username="<?php echo htmlspecialchars($u['username']); ?>"
                                        data-email="<?php echo htmlspecialchars($u['email']); ?>"
                                        data-role="<?php echo $u['role']; ?>"
                                        data-office="<?php echo $u['office_id'] ?? ''; ?>"
                                        data-active="<?php echo $u['is_active']; ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <?php if ($u['user_id'] != $_SESSION['user_id']): ?>
                                        <a href="<?php echo env('APP_URL'); ?>/superadmin/toggleuser/<?php echo $u['user_id']; ?>"
                                            class="btn btn-sm <?php echo $u['is_active'] ? 'btn-outline-warning' : 'btn-outline-success'; ?>"
                                            title="<?php echo $u['is_active'] ? 'Deactivate' : 'Activate'; ?>"
                                            onclick="return confirm('Are you sure you want to <?php echo $u['is_active'] ? 'deactivate' : 'activate'; ?> this user?');">
                                            <i class="bi bi-<?php echo $u['is_active'] ? 'person-dash' : 'person-check'; ?>"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==================== ADD USER MODAL ==================== -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 12px;">
            <form action="<?php echo env('APP_URL'); ?>/superadmin/createuser" method="POST">
                <div class="modal-header border-bottom" style="background-color: #092C4C; color: #fff; border-radius: 12px 12px 0 0;">
                    <h6 class="modal-title fw-bold" id="addUserModalLabel"><i class="bi bi-person-plus me-2"></i>Add New User</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="firstname" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Middle Name</label>
                            <input type="text" name="middlename" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="lastname" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" minlength="6" required>
                            <small class="text-muted">Minimum 6 characters.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select add-role-select" required>
                                <option value="">Select Role</option>
                                <option value="SUPER_ADMIN">Super Admin</option>
                                <option value="ADMIN">Admin</option>
                                <option value="LGU_OFFICER">LGU Officer</option>
                            </select>
                        </div>
                        <div class="col-md-6 add-office-field" style="display: none;">
                            <label class="form-label small fw-semibold">Assign Office</label>
                            <select name="office_id" class="form-select">
                                <option value="">No Office</option>
                                <?php foreach ($offices as $office): ?>
                                    <option value="<?php echo $office['office_id']; ?>"><?php echo htmlspecialchars($office['office_name']); ?> (<?php echo $office['office_type']; ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="addIsActive" checked>
                                <label class="form-check-label small" for="addIsActive">Account is active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white" style="background-color: #092C4C;">
                        <i class="bi bi-plus-lg me-1"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================== EDIT USER MODAL ==================== -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 12px;">
            <form id="editUserForm" method="POST">
                <div class="modal-header border-bottom" style="background-color: #092C4C; color: #fff; border-radius: 12px 12px 0 0;">
                    <h6 class="modal-title fw-bold" id="editUserModalLabel"><i class="bi bi-pencil-square me-2"></i>Edit User</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="firstname" id="editFirstname" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Middle Name</label>
                            <input type="text" name="middlename" id="editMiddlename" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="lastname" id="editLastname" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Username</label>
                            <input type="text" id="editUsername" class="form-control" disabled>
                            <small class="text-muted">Username cannot be changed.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="editEmail" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Role <span class="text-danger">*</span></label>
                            <select name="role" id="editRole" class="form-select edit-role-select" required>
                                <option value="SUPER_ADMIN">Super Admin</option>
                                <option value="ADMIN">Admin</option>
                                <option value="LGU_OFFICER">LGU Officer</option>
                            </select>
                        </div>
                        <div class="col-md-6 edit-office-field" style="display: none;">
                            <label class="form-label small fw-semibold">Assign Office</label>
                            <select name="office_id" id="editOffice" class="form-select">
                                <option value="">No Office</option>
                                <?php foreach ($offices as $office): ?>
                                    <option value="<?php echo $office['office_id']; ?>"><?php echo htmlspecialchars($office['office_name']); ?> (<?php echo $office['office_type']; ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="editIsActive">
                                <label class="form-check-label small" for="editIsActive">Account is active</label>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">
                    <h6 class="fw-bold mb-2 small" style="color: #092C4C;">
                        <i class="bi bi-shield-lock me-1"></i> Reset Password
                    </h6>
                    <p class="text-muted small mb-2">Leave blank to keep the current password.</p>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="password" name="new_password" class="form-control" placeholder="New password" minlength="6">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white" style="background-color: #092C4C;">
                        <i class="bi bi-check-lg me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Users Page JS -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var appUrl = '<?php echo env("APP_URL"); ?>';

        // Add User - toggle office field based on role
        document.querySelector('.add-role-select').addEventListener('change', function() {
            document.querySelector('.add-office-field').style.display = (this.value === 'LGU_OFFICER') ? 'block' : 'none';
        });

        // Edit User - toggle office field based on role
        document.querySelector('.edit-role-select').addEventListener('change', function() {
            document.querySelector('.edit-office-field').style.display = (this.value === 'LGU_OFFICER') ? 'block' : 'none';
        });

        // Edit User - populate modal from data attributes
        document.querySelectorAll('.btn-edit-user').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('editUserForm').action = appUrl + '/superadmin/edituser/' + this.dataset.id;
                document.getElementById('editFirstname').value = this.dataset.firstname;
                document.getElementById('editLastname').value = this.dataset.lastname;
                document.getElementById('editMiddlename').value = this.dataset.middlename;
                document.getElementById('editUsername').value = this.dataset.username;
                document.getElementById('editEmail').value = this.dataset.email;
                document.getElementById('editRole').value = this.dataset.role;
                document.getElementById('editOffice').value = this.dataset.office;
                document.getElementById('editIsActive').checked = this.dataset.active === '1';

                document.querySelector('.edit-office-field').style.display = (this.dataset.role === 'LGU_OFFICER') ? 'block' : 'none';

                new bootstrap.Modal(document.getElementById('editUserModal')).show();
            });
        });
    });
</script>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>