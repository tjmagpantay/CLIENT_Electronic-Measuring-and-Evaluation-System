<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<div class="page-header">
    <h4>Offices</h4>
    <p>List of all registered LGU offices.</p>
</div>

<div class="card dash-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #092C4C; color: #fff;">
                    <tr>
                        <th>#</th>
                        <th>Office Name</th>
                        <th>Type</th>
                        <th>Cluster</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($offices)): ?>
                        <?php foreach ($offices as $i => $office): ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td class="fw-semibold"><?php echo htmlspecialchars($office['office_name']); ?></td>
                                <td><span class="badge bg-secondary"><?php echo $office['office_type']; ?></span></td>
                                <td><?php echo $office['cluster'] ? 'Cluster ' . $office['cluster'] : '-'; ?></td>
                                <td>
                                    <?php if ($office['status'] === 'ACTIVE'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No offices found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>