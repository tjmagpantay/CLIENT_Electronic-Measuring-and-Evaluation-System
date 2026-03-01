<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<div class="page-header">
    <h4>My Office</h4>
    <p>Your assigned office information.</p>
</div>

<?php if ($office): ?>
    <div class="row">
        <div class="col-lg-6">
            <div class="card dash-card p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="rounded-3 p-3 me-3" style="background-color: rgba(9,44,76,0.1);">
                        <i class="bi bi-building fs-3" style="color: #092C4C;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="color: #092C4C;"><?php echo htmlspecialchars($office['office_name']); ?></h5>
                        <span class="badge bg-secondary"><?php echo $office['office_type']; ?></span>
                    </div>
                </div>

                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted fw-semibold" style="width: 150px;">Office ID</td>
                        <td><?php echo $office['office_id']; ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Type</td>
                        <td><?php echo $office['office_type']; ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Cluster</td>
                        <td><?php echo $office['cluster'] ? 'Cluster ' . $office['cluster'] : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Status</td>
                        <td>
                            <?php if ($office['status'] === 'ACTIVE'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="card dash-card p-4">
        <div class="text-center py-4">
            <i class="bi bi-building display-4 text-muted"></i>
            <h5 class="mt-3" style="color: #092C4C;">No Office Assigned</h5>
            <p class="text-muted">Your account is not assigned to any office. Please contact your administrator.</p>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>