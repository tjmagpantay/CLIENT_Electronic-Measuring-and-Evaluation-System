<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<div class="page-header">
    <h4>Report Types</h4>
    <p>All report types configured in the system.</p>
</div>

<div class="card dash-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #092C4C; color: #fff;">
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Title</th>
                        <th>Submission Type</th>
                        <th>OPR</th>
                        <th>Deadline Day</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reportTypes)): ?>
                        <?php foreach ($reportTypes as $i => $rt): ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td><span class="badge" style="background-color: #F3AF0E; color: #092C4C;"><?php echo htmlspecialchars($rt['report_code']); ?></span></td>
                                <td class="fw-semibold"><?php echo htmlspecialchars($rt['report_title']); ?></td>
                                <td>
                                    <?php
                                    $type = $rt['submission_type'] ?? 'FILE_UPLOAD';
                                    if ($type === 'GOOGLE_SHEET'): ?>
                                        <span class="badge bg-info"><i class="bi bi-file-spreadsheet"></i> Google Sheet</span>
                                    <?php elseif ($type === 'BOTH'): ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-files"></i> Sheet + Files</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary"><i class="bi bi-cloud-upload"></i> File Upload</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($rt['opr'] ?? '-'); ?></td>
                                <td><?php echo $rt['deadline_day'] ? 'Day ' . $rt['deadline_day'] : '-'; ?></td>
                                <td>
                                    <?php if ($rt['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No report types found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>