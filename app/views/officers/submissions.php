<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<!-- Page Header -->
<div class="mb-4 d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: #092C4C;">My Submissions</h4>
        <p class="text-muted mb-0 small">Track and manage all report submissions from your office.</p>
    </div>
    <a href="<?php echo env('APP_URL'); ?>/officer/submit" class="btn text-white" style="background-color: #092C4C;">
        <i class="bi bi-upload me-1"></i> Submit New Report
    </a>
</div>

<!-- Filtering Section -->
<div class="card dash-card mb-4">
    <div class="card-body p-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1" style="color: #092C4C;">Report Type</label>
                <select class="form-select form-select-sm" id="filterReportType">
                    <option value="">All Report Types</option>
                    <?php foreach ($reportTypes as $rt): ?>
                        <option value="<?php echo htmlspecialchars($rt['report_code']); ?>">
                            <?php echo htmlspecialchars($rt['report_code'] . ' - ' . $rt['report_title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1" style="color: #092C4C;">Status</label>
                <select class="form-select form-select-sm" id="filterStatus">
                    <option value="">All Status</option>
                    <option value="ON_TIME">On Time</option>
                    <option value="LATE">Late</option>
                    <option value="NO_SUBMISSION">No Submission</option>
                    <option value="NOT_REQUIRED">Not Required</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1" style="color: #092C4C;">Period</label>
                <select class="form-select form-select-sm" id="filterPeriod">
                    <option value="">All Periods</option>
                    <?php
                    $seenPeriods = [];
                    if (!empty($submissions)):
                        foreach ($submissions as $s):
                            $periodKey = $s['period_month'] . '-' . $s['period_year'];
                            if (!in_array($periodKey, $seenPeriods)):
                                $seenPeriods[] = $periodKey;
                    ?>
                                <option value="<?php echo $periodKey; ?>">
                                    <?php echo $periodModel->getMonthName($s['period_month']) . ' ' . $s['period_year']; ?>
                                </option>
                    <?php
                            endif;
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-sm w-100" style="background-color: #F3AF0E; color: #092C4C; font-weight: 600;" id="btnApplyFilter">
                    <i class="bi bi-funnel me-1"></i> Apply Filter
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Compliance Cards -->
<div class="row g-3 mb-4">
    <!-- Non Compliant -->
    <div class="col-md-4">
        <div class="card dash-card h-100" style="border: none;">
            <div class="card-body p-3 d-flex align-items-center" style="background-color: #FFE2E2; border-radius: 12px;">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: rgba(235,80,80,0.15);">
                    <i class="bi bi-x-circle fs-5" style="color: #EB5050;"></i>
                </div>
                <div>
                    <p class="text-muted small mb-0">Non Compliant</p>
                    <h4 class="fw-bold mb-0" style="color: #092C4C;"><?php echo $pendingCount; ?></h4>
                </div>
            </div>
        </div>
    </div>
    <!-- Late Submissions -->
    <div class="col-md-4">
        <div class="card dash-card h-100" style="border: none;">
            <div class="card-body p-3 d-flex align-items-center" style="background-color: #F5E7CE; border-radius: 12px;">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: rgba(255,174,76,0.2);">
                    <i class="bi bi-clock-history fs-5" style="color: #FFAE4C;"></i>
                </div>
                <div>
                    <p class="text-muted small mb-0">Late Submissions</p>
                    <h4 class="fw-bold mb-0" style="color: #092C4C;"><?php echo $lateCount; ?></h4>
                </div>
            </div>
        </div>
    </div>
    <!-- Submitted -->
    <div class="col-md-4">
        <div class="card dash-card h-100" style="border: none;">
            <div class="card-body p-3 d-flex align-items-center" style="background-color: #E3EBF3; border-radius: 12px;">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: rgba(9,44,76,0.1);">
                    <i class="bi bi-check-circle fs-5" style="color: #092C4C;"></i>
                </div>
                <div>
                    <p class="text-muted small mb-0">Submitted (On Time)</p>
                    <h4 class="fw-bold mb-0" style="color: #092C4C;"><?php echo $onTimeCount; ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Submissions Table -->
<div class="card dash-card">
    <div class="card-header py-3 px-4" style="background-color: #092C4C; border-radius: 12px 12px 0 0;">
        <h6 class="fw-bold text-white mb-0"><i class="bi bi-table me-2"></i>Submitted Reports</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="submissionsTable">
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th class="ps-4" style="color: #092C4C; font-size: 0.8rem; font-weight: 600;">#</th>
                        <th style="color: #092C4C; font-size: 0.8rem; font-weight: 600;">Report</th>
                        <th style="color: #092C4C; font-size: 0.8rem; font-weight: 600;">Period</th>
                        <th style="color: #092C4C; font-size: 0.8rem; font-weight: 600;">Submitted By</th>
                        <th style="color: #092C4C; font-size: 0.8rem; font-weight: 600;">Date Submitted</th>
                        <th style="color: #092C4C; font-size: 0.8rem; font-weight: 600;">Status</th>
                        <th style="color: #092C4C; font-size: 0.8rem; font-weight: 600;">File</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($submissions)): ?>
                        <?php foreach ($submissions as $i => $s): ?>
                            <tr data-report-code="<?php echo htmlspecialchars($s['report_code']); ?>"
                                data-status="<?php echo htmlspecialchars($s['submission_status']); ?>"
                                data-period="<?php echo $s['period_month'] . '-' . $s['period_year']; ?>">
                                <td class="ps-4"><?php echo $i + 1; ?></td>
                                <td>
                                    <span class="badge" style="background-color: #F3AF0E; color: #092C4C; font-weight: 600;"><?php echo htmlspecialchars($s['report_code']); ?></span>
                                    <small class="d-block text-muted mt-1"><?php echo htmlspecialchars($s['report_title']); ?></small>
                                </td>
                                <td>
                                    <small><?php echo $periodModel->getMonthName($s['period_month']) . ' ' . $s['period_year']; ?></small>
                                </td>
                                <td>
                                    <small><?php echo htmlspecialchars(($s['firstname'] ?? '') . ' ' . ($s['lastname'] ?? '')); ?></small>
                                </td>
                                <td>
                                    <small><?php echo $s['submitted_at'] ? date('M d, Y h:i A', strtotime($s['submitted_at'])) : '-'; ?></small>
                                </td>
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
                                    $statusLabel = match ($s['submission_status']) {
                                        'ON_TIME' => 'On Time',
                                        'LATE' => 'Late',
                                        'ERROR' => 'Error',
                                        'NO_SUBMISSION' => 'No Submission',
                                        'NOT_REQUIRED' => 'Not Required',
                                        default => $s['submission_status']
                                    };
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?> rounded-pill"><?php echo $statusLabel; ?></span>
                                </td>
                                <td>
                                    <?php if ($s['file_link']): ?>
                                        <a href="<?php echo env('APP_URL'); ?>/public/<?php echo htmlspecialchars($s['file_link']); ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Download File">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-2" style="color: #ccc;"></i>
                                <p class="mb-1">No submissions found.</p>
                                <small>Submit your first report to see it here.</small>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var btnFilter = document.getElementById('btnApplyFilter');
    if (btnFilter) {
        btnFilter.addEventListener('click', function() {
            var reportType = document.getElementById('filterReportType').value;
            var status = document.getElementById('filterStatus').value;
            var period = document.getElementById('filterPeriod').value;
            var rows = document.querySelectorAll('#submissionsTable tbody tr[data-report-code]');

            rows.forEach(function(row) {
                var showReport = !reportType || row.dataset.reportCode === reportType;
                var showStatus = !status || row.dataset.status === status;
                var showPeriod = !period || row.dataset.period === period;
                row.style.display = (showReport && showStatus && showPeriod) ? '' : 'none';
            });
        });
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>
