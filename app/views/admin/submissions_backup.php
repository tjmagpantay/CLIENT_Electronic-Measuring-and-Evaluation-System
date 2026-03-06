<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<div class="page-header">
    <h4>Submissions</h4>
    <p>All report submissions from LGU offices.</p>
</div>

<div class="card dash-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #092C4C; color: #fff;">
                    <tr>
                        <th>#</th>
                        <th>Office</th>
                        <th>Report</th>
                        <th>Period</th>
                        <th>Submitted By</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>File</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($submissions)): ?>
                        <?php foreach ($submissions as $i => $s): ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td class="fw-semibold"><?php echo htmlspecialchars($s['office_name']); ?></td>
                                <td>
                                    <span class="badge" style="background-color: #F3AF0E; color: #092C4C;"><?php echo htmlspecialchars($s['report_code']); ?></span>
                                    <small class="d-block text-muted"><?php echo htmlspecialchars($s['report_title']); ?></small>
                                </td>
                                <td><?php echo $periodModel->getMonthName($s['period_month']) . ' ' . $s['period_year']; ?></td>
                                <td><?php echo htmlspecialchars(($s['firstname'] ?? '') . ' ' . ($s['lastname'] ?? '')); ?></td>
                                <td><small><?php echo $s['submitted_at'] ? date('M d, Y', strtotime($s['submitted_at'])) : '-'; ?></small></td>
                                <td>
                                    <?php
                                    $statusClass = match ($s['submission_status']) {
                                        'ON_TIME' => 'bg-success',
                                        'LATE' => 'bg-warning text-dark',
                                        'ERROR' => 'bg-danger',
                                        'NO_SUBMISSION' => 'bg-secondary',
                                        'NOT_REQUIRED' => 'bg-info',
                                        default => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>"><?php echo str_replace('_', ' ', $s['submission_status']); ?></span>
                                </td>
                                <td>
                                    <?php if ($s['file_link']): ?>
                                        <a href="<?php echo env('APP_URL'); ?>/public/<?php echo htmlspecialchars($s['file_link']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No submissions found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>