<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<div class="page-header">
    <h4>Submissions Management</h4>
    <p>View and monitor all report submissions from LGU officers.</p>
</div>

<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo htmlspecialchars($_SESSION['flash_success']);
        unset($_SESSION['flash_success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?php echo htmlspecialchars($_SESSION['flash_error']);
        unset($_SESSION['flash_error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card dash-card text-center">
            <div class="card-body">
                <h3 class="mb-0" style="color: #092C4C;">
                    <?php
                    $total = count($submissions);
                    echo $total;
                    ?>
                </h3>
                <p class="text-muted mb-0 small">Total Submissions</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dash-card text-center">
            <div class="card-body">
                <h3 class="mb-0 text-success">
                    <?php
                    $onTime = array_filter($submissions, fn($s) => $s['submission_status'] === 'ON_TIME');
                    echo count($onTime);
                    ?>
                </h3>
                <p class="text-muted mb-0 small">On Time</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dash-card text-center">
            <div class="card-body">
                <h3 class="mb-0 text-warning">
                    <?php
                    $late = array_filter($submissions, fn($s) => $s['submission_status'] === 'LATE');
                    echo count($late);
                    ?>
                </h3>
                <p class="text-muted mb-0 small">Late</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dash-card text-center">
            <div class="card-body">
                <h3 class="mb-0 text-danger">
                    <?php
                    $noSubmission = array_filter($submissions, fn($s) => $s['submission_status'] === 'NO_SUBMISSION');
                    echo count($noSubmission);
                    ?>
                </h3>
                <p class="text-muted mb-0 small">No Submission</p>
            </div>
        </div>
    </div>
</div>

<!-- Submissions Table -->
<div class="card dash-card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">All Submissions</h6>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #092C4C; color: #fff;">
                    <tr>
                        <th>#</th>
                        <th>Office</th>
                        <th>Report Type</th>
                        <th>Period</th>
                        <th>Submitted By</th>
                        <th>Submitted At</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($submissions)): ?>
                        <?php foreach ($submissions as $i => $sub): ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($sub['office_name']); ?></div>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($sub['office_type']); ?>
                                        <?php if ($sub['cluster']): ?>
                                            - Cluster <?php echo htmlspecialchars($sub['cluster']); ?>
                                        <?php endif; ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: #F3AF0E; color: #092C4C;">
                                        <?php echo htmlspecialchars($sub['report_code']); ?>
                                    </span>
                                    <div class="small"><?php echo htmlspecialchars($sub['report_title']); ?></div>
                                </td>
                                <td>
                                    <?php echo $periodModel->getMonthName($sub['period_month']) . ' ' . $sub['period_year']; ?>
                                </td>
                                <td>
                                    <?php if ($sub['firstname']): ?>
                                        <?php echo htmlspecialchars($sub['firstname'] . ' ' . $sub['lastname']); ?>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($sub['submitted_at']): ?>
                                        <?php echo date('M d, Y h:i A', strtotime($sub['submitted_at'])); ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not submitted</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = 'secondary';
                                    $statusLabel = $sub['submission_status'];

                                    switch ($sub['submission_status']) {
                                        case 'ON_TIME':
                                            $statusClass = 'success';
                                            $statusLabel = 'On Time';
                                            break;
                                        case 'LATE':
                                            $statusClass = 'warning';
                                            $statusLabel = 'Late';
                                            break;
                                        case 'NO_SUBMISSION':
                                            $statusClass = 'danger';
                                            $statusLabel = 'No Submission';
                                            break;
                                        case 'ERROR':
                                            $statusClass = 'dark';
                                            $statusLabel = 'Error';
                                            break;
                                        case 'NOT_REQUIRED':
                                            $statusClass = 'secondary';
                                            $statusLabel = 'Not Required';
                                            break;
                                    }
                                    ?>
                                    <span class="badge bg-<?php echo $statusClass; ?>">
                                        <?php echo $statusLabel; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($sub['file_link']): ?>
                                        <a href="<?php echo htmlspecialchars($sub['file_link']); ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="View File">
                                            <i class="bi bi-file-earmark-arrow-down"></i>
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-secondary" disabled title="No file">
                                            <i class="bi bi-file-earmark-x"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-outline-info" onclick='viewDetails(<?php echo json_encode($sub); ?>)' title="View Details">
                                        <i class="bi bi-info-circle"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No submissions found. Officers haven't submitted any reports yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #092C4C; color: #fff;">
                <h5 class="modal-title">Submission Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold">Office:</label>
                        <p id="detailOffice"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Report Type:</label>
                        <p id="detailReportType"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold">Period:</label>
                        <p id="detailPeriod"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Status:</label>
                        <p id="detailStatus"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold">Submitted By:</label>
                        <p id="detailSubmittedBy"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Submitted At:</label>
                        <p id="detailSubmittedAt"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="fw-bold">File Link:</label>
                        <p id="detailFileLink"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <label class="fw-bold">Remarks:</label>
                        <p id="detailRemarks"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function viewDetails(submission) {
        document.getElementById('detailOffice').textContent = submission.office_name;
        document.getElementById('detailReportType').textContent = submission.report_code + ' - ' + submission.report_title;

        const months = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        document.getElementById('detailPeriod').textContent = months[submission.period_month] + ' ' + submission.period_year;

        let statusBadge = '';
        switch (submission.submission_status) {
            case 'ON_TIME':
                statusBadge = '<span class="badge bg-success">On Time</span>';
                break;
            case 'LATE':
                statusBadge = '<span class="badge bg-warning">Late</span>';
                break;
            case 'NO_SUBMISSION':
                statusBadge = '<span class="badge bg-danger">No Submission</span>';
                break;
            case 'ERROR':
                statusBadge = '<span class="badge bg-dark">Error</span>';
                break;
            default:
                statusBadge = '<span class="badge bg-secondary">Not Required</span>';
        }
        document.getElementById('detailStatus').innerHTML = statusBadge;

        document.getElementById('detailSubmittedBy').textContent = submission.firstname ? submission.firstname + ' ' + submission.lastname : 'N/A';
        document.getElementById('detailSubmittedAt').textContent = submission.submitted_at ? new Date(submission.submitted_at).toLocaleString() : 'Not submitted';

        if (submission.file_link) {
            document.getElementById('detailFileLink').innerHTML = '<a href="' + submission.file_link + '" target="_blank" class="btn btn-sm btn-primary"><i class="bi bi-box-arrow-up-right"></i> Open File</a>';
        } else {
            document.getElementById('detailFileLink').textContent = 'No file uploaded';
        }

        document.getElementById('detailRemarks').textContent = submission.remarks || 'No remarks';

        new bootstrap.Modal(document.getElementById('viewDetailsModal')).show();
    }
</script>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>