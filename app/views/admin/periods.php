<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<div class="page-header">
    <h4>Reporting Periods</h4>
    <p>All configured reporting periods.</p>
</div>

<div class="card dash-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #092C4C; color: #fff;">
                    <tr>
                        <th>#</th>
                        <th>Period</th>
                        <th>Year</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($periods)): ?>
                        <?php foreach ($periods as $i => $p): ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td class="fw-semibold"><?php echo $periodModel->getMonthName($p['period_month']); ?></td>
                                <td><?php echo $p['period_year']; ?></td>
                                <td>
                                    <?php if ($p['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Closed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No reporting periods found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>