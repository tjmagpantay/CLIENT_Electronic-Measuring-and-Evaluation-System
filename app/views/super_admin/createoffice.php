<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<div class="page-header">
    <h4>Create Office</h4>
    <p>Add a new LGU office to the system.</p>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card dash-card p-4">
    <form action="<?php echo env('APP_URL'); ?>/superadmin/createoffice" method="POST">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Office Name <span class="text-danger">*</span></label>
                <input type="text" name="office_name" class="form-control" placeholder="e.g. Province of Laguna" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Office Type <span class="text-danger">*</span></label>
                <select name="office_type" class="form-select" required>
                    <option value="">Select Type</option>
                    <option value="PROVINCE">Province</option>
                    <option value="CITY">City</option>
                    <option value="MUNICIPALITY">Municipality</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Cluster</label>
                <select name="cluster" class="form-select">
                    <option value="">No Cluster</option>
                    <option value="1">Cluster 1</option>
                    <option value="2">Cluster 2</option>
                    <option value="3">Cluster 3</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="ACTIVE">Active</option>
                    <option value="INACTIVE">Inactive</option>
                </select>
            </div>
        </div>

        <hr class="my-4">

        <div class="d-flex gap-2">
            <button type="submit" class="btn text-white px-4" style="background-color: #092C4C;">
                <i class="bi bi-plus-lg me-1"></i> Create Office
            </button>
            <a href="<?php echo env('APP_URL'); ?>/superadmin/offices" class="btn btn-outline-secondary px-4">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>