<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#092C4C;">Submissions</h4>
        <p class="text-muted mb-0 small">View and monitor all report submissions from LGU officers.</p>
    </div>
</div>

<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['flash_success']);
                                                unset($_SESSION['flash_success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($_SESSION['flash_error']);
                                                        unset($_SESSION['flash_error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card dash-card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                        style="width:50px;height:50px;background-color:rgba(9,44,76,0.1);">
                        <i class="bi bi-file-earmark-text" style="font-size:1.5rem;color:#092C4C;"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h3 class="mb-0 fw-bold" style="color:#092C4C;">
                        <?php
                        $total = count($submissions);
                        echo $total;
                        ?>
                    </h3>
                    <p class="text-muted mb-0 small">Total Submissions</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card dash-card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                        style="width:50px;height:50px;background-color:rgba(25,135,84,0.1);">
                        <i class="bi bi-check-circle" style="font-size:1.5rem;color:#198754;"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h3 class="mb-0 fw-bold text-success">
                        <?php
                        $onTime = array_filter($submissions, fn($s) => $s['submission_status'] === 'ON_TIME');
                        echo count($onTime);
                        ?>
                    </h3>
                    <p class="text-muted mb-0 small">On Time</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card dash-card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                        style="width:50px;height:50px;background-color:rgba(255,193,7,0.1);">
                        <i class="bi bi-clock-history" style="font-size:1.5rem;color:#ffc107;"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h3 class="mb-0 fw-bold text-warning">
                        <?php
                        $late = array_filter($submissions, fn($s) => $s['submission_status'] === 'LATE');
                        echo count($late);
                        ?>
                    </h3>
                    <p class="text-muted mb-0 small">Late Submissions</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card dash-card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                        style="width:50px;height:50px;background-color:rgba(220,53,69,0.1);">
                        <i class="bi bi-x-circle" style="font-size:1.5rem;color:#dc3545;"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h3 class="mb-0 fw-bold text-danger">
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
</div>

<!-- Submissions Table -->
<div class="card dash-card" style="border-radius: 8px; overflow: visible;">
    <!-- Search bar inside card -->
    <div class="p-3 d-flex align-items-center flex-wrap gap-2" style="background:#f8f9fa;border-bottom:1px solid #e9ecef;">
        <!-- Search -->
        <div class="input-group input-group-sm" style="max-width:240px;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" id="submissionSearch" class="form-control border-start-0 ps-0" placeholder="Search submissions...">
        </div>
        <!-- Status Filter -->
        <select id="filterStatus" class="form-select form-select-sm" style="width:auto;min-width:140px;">
            <option value="">All Status</option>
            <option value="ON_TIME">On Time</option>
            <option value="LATE">Late</option>
            <option value="NO_SUBMISSION">No Submission</option>
        </select>
        <!-- Reset -->
        <button id="resetFilters" class="btn btn-sm btn-outline-secondary">Reset</button>
        <!-- Results count -->
        <span id="submissionCount" class="ms-auto small text-muted"></span>
    </div>

    <!-- Table -->
    <div class="table-responsive" style="overflow-x: auto; overflow-y: visible;">
        <table class="table table-hover align-middle mb-0" id="submissionsTable">
            <thead style="background-color:#f8f9fa;">
                <tr>
                    <th class="ps-4" style="background:#f8f9fa;">#</th>
                    <th style="background:#f8f9fa;">Office</th>
                    <th style="background:#f8f9fa;">Report Type</th>
                    <th style="background:#f8f9fa;">Period</th>
                    <th style="background:#f8f9fa;">Submitted By</th>
                    <th style="background:#f8f9fa;">Submitted At</th>
                    <th style="background:#f8f9fa;">Status</th>
                    <th class="text-center" style="background:#f8f9fa;">Actions</th>
                </tr>
            </thead>
            <tbody id="submissionsBody">
                <?php if (!empty($submissions)): ?>
                    <?php foreach ($submissions as $i => $sub): ?>
                        <tr
                            data-office="<?php echo strtolower(htmlspecialchars($sub['office_name'])); ?>"
                            data-report="<?php echo strtolower(htmlspecialchars($sub['report_code'])); ?>"
                            data-status="<?php echo htmlspecialchars($sub['submission_status']); ?>">
                            <td class="ps-4 small text-muted"><?php echo $i + 1; ?></td>
                            <td>
                                <div class="fw-semibold small"><?php echo htmlspecialchars($sub['office_name']); ?></div>
                                <small class="text-muted">
                                    <?php echo htmlspecialchars($sub['office_type']); ?>
                                    <?php if ($sub['cluster']): ?>
                                        - Cluster <?php echo htmlspecialchars($sub['cluster']); ?>
                                    <?php endif; ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge rounded-small" style="background-color:#092C4C;color:#fff;">
                                    <?php echo htmlspecialchars($sub['report_code']); ?>
                                </span>
                                <div class="small text-muted"><?php echo htmlspecialchars($sub['report_title']); ?></div>
                            </td>
                            <td>
                                <span class="small"><?php echo $periodModel->getMonthName($sub['period_month']) . ' ' . $sub['period_year']; ?></span>
                            </td>
                            <td>
                                <span class="small">
                                    <?php if ($sub['firstname']): ?>
                                        <?php echo htmlspecialchars($sub['firstname'] . ' ' . $sub['lastname']); ?>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <span class="small">
                                    <?php if ($sub['submitted_at']): ?>
                                        <?php echo date('M d, Y h:i A', strtotime($sub['submitted_at'])); ?>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </span>
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
                                <span class="badge rounded-small bg-<?php echo $statusClass; ?> bg-opacity-10 text-<?php echo $statusClass; ?>">
                                    <?php echo $statusLabel; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <?php if ($sub['file_link']): ?>
                                            <li>
                                                <a class="dropdown-item" href="<?php echo htmlspecialchars($sub['file_link']); ?>" target="_blank">
                                                    <i class="bi bi-file-earmark-arrow-down me-2"></i>View File
                                                </a>
                                            </li>
                                        <?php else: ?>
                                            <li>
                                                <span class="dropdown-item text-muted disabled">
                                                    <i class="bi bi-file-earmark-x me-2"></i>No File
                                                </span>
                                            </li>
                                        <?php endif; ?>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick='viewDetails(<?php echo json_encode($sub); ?>); return false;'>
                                                <i class="bi bi-info-circle me-2"></i>View Details
                                            </a>
                                        </li>
                                    </ul>
                                </div>
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

    <!-- No results row (hidden initially) -->
    <div id="noResults" class="text-center text-muted py-4" style="display:none!important;">No submissions match your filters.</div>
</div>
</div>

<script>
    (function() {
        var searchInput = document.getElementById('submissionSearch');
        var filterStatus = document.getElementById('filterStatus');
        var resetBtn = document.getElementById('resetFilters');
        var tbody = document.getElementById('submissionsBody');
        var countEl = document.getElementById('submissionCount');
        var noResults = document.getElementById('noResults');

        function applyFilters() {
            var search = searchInput.value.toLowerCase().trim();
            var status = filterStatus.value;
            var rows = tbody.querySelectorAll('tr[data-office]');
            var visible = 0;

            rows.forEach(function(row) {
                var matchSearch = !search || row.dataset.office.includes(search) || row.dataset.report.includes(search);
                var matchStatus = !status || row.dataset.status === status;
                var show = matchSearch && matchStatus;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            countEl.textContent = visible + ' submission' + (visible !== 1 ? 's' : '') + ' found';
            noResults.style.setProperty('display', visible === 0 ? 'block' : 'none', 'important');
        }

        searchInput.addEventListener('input', applyFilters);
        filterStatus.addEventListener('change', applyFilters);

        resetBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterStatus.value = '';
            applyFilters();
        });

        applyFilters();
    })();
</script>

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