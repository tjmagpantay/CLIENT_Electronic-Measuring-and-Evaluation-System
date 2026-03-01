<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4>Offices</h4>
        <p>Manage LGU offices - Provincial, Municipal, and City levels.</p>
    </div>
    <a href="<?php echo env('APP_URL'); ?>/superadmin/createoffice" class="btn text-white" style="background-color: #092C4C;">
        <i class="bi bi-plus-lg me-1"></i> Add Office
    </a>
</div>

<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['flash_success']);
                                                unset($_SESSION['flash_success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card dash-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #f8f9fa;">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Office Name</th>
                        <th>Type</th>
                        <th>Cluster</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($offices)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No offices found.</td>
                        </tr>
                    <?php else: ?>
                        <?php $i = 1;
                        foreach ($offices as $office): ?>
                            <tr>
                                <td class="ps-4 small"><?php echo $i++; ?></td>
                                <td>
                                    <span class="fw-semibold small"><?php echo htmlspecialchars($office['office_name']); ?></span>
                                </td>
                                <td>
                                    <?php
                                    $typeBadge = match ($office['office_type']) {
                                        'PROVINCE' => '<span class="badge rounded-pill" style="background-color: #092C4C; color: #fff;">Province</span>',
                                        'CITY' => '<span class="badge rounded-pill" style="background-color: #F3AF0E; color: #092C4C;">City</span>',
                                        'MUNICIPALITY' => '<span class="badge rounded-pill bg-info text-dark">Municipality</span>',
                                        default => '<span class="badge bg-secondary">' . htmlspecialchars($office['office_type']) . '</span>'
                                    };
                                    echo $typeBadge;
                                    ?>
                                </td>
                                <td><span class="small"><?php echo htmlspecialchars($office['cluster'] ?? '—'); ?></span></td>
                                <td>
                                    <?php if ($office['status'] === 'ACTIVE'): ?>
                                        <span class="badge rounded-pill bg-success bg-opacity-10 text-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo env('APP_URL'); ?>/superadmin/editoffice/<?php echo $office['office_id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>