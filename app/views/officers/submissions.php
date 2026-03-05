<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<!-- Page Header -->
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: #092C4C;">My Submissions</h4>
        <p class="text-muted mb-0 small">Track and manage all report submissions from your office.</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <!-- Stat Badges -->
        <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3" style="background:#fff;border:1px solid #e9ecef;">
            <span class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#FDECEA;">
                <i class="bi bi-x-circle" style="color:#E43535;font-size:.78rem;"></i>
            </span>
            <div class="d-flex align-items-center gap-1">
                <span class="fw-bold" style="color:#092C4C;font-size:.88rem;">
                    <?php echo $pendingCount; ?>
                </span>
                <span class="text-muted" style="font-size:.75rem;">
                    Non Compliant
                </span>
            </div>

        </div>
        <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3" style="background:#fff;border:1px solid #e9ecef;">
            <span class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#FFF8E1;">
                <i class="bi bi-clock-history" style="color:#FEC53D;font-size:.78rem;"></i>
            </span>
            <div class="d-flex align-items-center gap-1">
                <span class="fw-bold" style="color:#092C4C;font-size:.88rem;">
                    <?php echo $lateCount; ?>
                </span>
                <span class="text-muted" style="font-size:.75rem;">
                    Late
                </span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3" style="background:#fff;border:1px solid #e9ecef;">
            <span class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#E8F5E9;">
                <i class="bi bi-check-circle" style="color:#43A047;font-size:.78rem;"></i>
            </span>

            <div class="d-flex align-items-center gap-1">
                <span class="fw-bold" style="color:#092C4C;font-size:.88rem;">
                    <?php echo $onTimeCount; ?>
                </span>
                <span class="text-muted" style="font-size:.75rem;">
                    On Time
                </span>
            </div>
        </div>
        <!-- Submit Button -->
        <a href="<?php echo env('APP_URL'); ?>/officer/submit"
            class="btn text-white d-flex align-items-center px-3 py-2 small"
            style="background-color:#092C4C; border-radius:.5rem;">
            <i class="bi bi-upload me-1"></i> Submit New Report
        </a>
    </div>
</div>

<!-- Submissions Table -->
<div style="border-radius:16px;overflow:hidden;background:#fff;box-shadow:0 1px 6px rgba(0,0,0,.07);">
    <!-- Table Header with Filters -->
    <div class="px-4 py-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background:#fff;border-bottom:1px solid #e9ecef;">
        <h6 class="fw-bold mb-0" style="color:#092C4C;"><i class="bi bi-table me-2"></i>Submitted Reports</h6>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <select class="form-select form-select-sm p-2" id="filterReportType" style="width:auto;min-width:130px;font-size:.78rem;">
                <option value="">All Report Types</option>
                <?php foreach ($reportTypes as $rt): ?>
                    <option value="<?php echo htmlspecialchars($rt['report_code']); ?>">
                        <?php echo htmlspecialchars($rt['report_code'] . ' - ' . $rt['report_title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select class="form-select form-select-sm p-2" id="filterStatus" style="width:auto;min-width:120px;font-size:.78rem;">
                <option value="">All Status</option>
                <option value="ON_TIME">On Time</option>
                <option value="LATE">Late</option>
                <option value="NO_SUBMISSION">No Submission</option>
                <option value="NOT_REQUIRED">Not Required</option>
            </select>
            <select class="form-select form-select-sm p-2" id="filterPeriod" style="width:auto;min-width:120px;font-size:.78rem;">
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
            <button class="btn btn-sm p-2" style="background-color:#F3AF0E;color:#092C4C;font-weight:600;font-size:.78rem;" id="btnApplyFilter">
                <i class="bi bi-funnel me-1"></i> Filter
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="submissionsTable" style="font-size:.85rem;">
            <thead>
                <tr style="background:#092C4C;color:#fff;">
                    <th class="ps-4 py-3 fw-semibold border-0">#</th>
                    <th class="py-3 fw-semibold border-0">Report</th>
                    <th class="py-3 fw-semibold border-0">Period</th>
                    <th class="py-3 fw-semibold border-0">OPR</th>
                    <th class="py-3 fw-semibold border-0">Submitted By</th>
                    <th class="py-3 fw-semibold border-0">Date Submitted</th>
                    <th class="py-3 fw-semibold border-0">Status</th>
                    <th class="py-3 fw-semibold border-0">Files</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($submissions)): ?>
                    <?php foreach ($submissions as $i => $s): ?>
                        <tr data-report-code="<?php echo htmlspecialchars($s['report_code']); ?>"
                            data-status="<?php echo htmlspecialchars($s['submission_status']); ?>"
                            data-period="<?php echo $s['period_month'] . '-' . $s['period_year']; ?>">
                            <td class="ps-4 py-3 text-muted" style="font-size:.78rem;"><?php echo $i + 1; ?></td>
                            <td class="py-3">
                               
                                <div class="fw-semibold mt-1" style="color:#092C4C;font-size:.82rem;"><?php echo htmlspecialchars($s['report_title']); ?></div>
                            </td>
                            <td class="py-3" style="color:#495057;font-size:.82rem;white-space:nowrap;">
                                <?php echo $periodModel->getMonthName($s['period_month']) . ' ' . $s['period_year']; ?>
                            </td>
                            <td class="py-3 text-muted" style="font-size:.82rem;"><?php echo htmlspecialchars($s['opr'] ?? '—'); ?></td>
                            <td class="py-3" style="font-size:.82rem;"><?php echo htmlspecialchars(($s['firstname'] ?? '') . ' ' . ($s['lastname'] ?? '')); ?></td>
                            <td class="py-3 text-muted" style="font-size:.82rem;white-space:nowrap;">
                                <?php echo $s['submitted_at'] ? date('M d, Y h:i A', strtotime($s['submitted_at'])) : '—'; ?>
                            </td>
                            <td class="py-3">
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
                                <span class="badge <?php echo $statusClass; ?> rounded-small p-2 fw-normal" style="font-size:.75rem; "><?php echo $statusLabel; ?></span>
                            </td>
                            <td class="py-3">
                                <?php if (!empty($s['files']) && is_array($s['files'])): ?>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="bi bi-cloud-download me-1"></i> <?php echo count($s['files']); ?> file(s)
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php foreach ($s['files'] as $file): ?>
                                                <li>
                                                    <?php if (!empty($file['google_drive_link'])): ?>
                                                        <!-- Google Drive Link -->
                                                        <a class="dropdown-item" href="<?php echo htmlspecialchars($file['google_drive_link']); ?>" target="_blank">
                                                            <i class="bi bi-google text-primary me-1"></i>
                                                            <?php echo htmlspecialchars($file['file_name']); ?>
                                                            <small class="text-muted d-block">
                                                                <i class="bi bi-cloud-check"></i> Google Drive • <?php echo number_format($file['file_size'] / 1024, 2); ?> KB
                                                            </small>
                                                        </a>
                                                    <?php else: ?>
                                                        <!-- Local File Fallback -->
                                                        <a class="dropdown-item" href="<?php echo env('APP_URL'); ?>/<?php echo htmlspecialchars($file['file_path']); ?>" target="_blank">
                                                            <i class="bi bi-file-earmark-<?php echo $file['file_type']; ?> me-1"></i>
                                                            <?php echo htmlspecialchars($file['file_name']); ?>
                                                            <small class="text-muted d-block"><?php echo number_format($file['file_size'] / 1024, 2); ?> KB</small>
                                                        </a>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php elseif ($s['file_link']): ?>
                                    <!-- Legacy single file support -->
                                    <a href="<?php echo env('APP_URL'); ?>/public/<?php echo htmlspecialchars($s['file_link']); ?>"
                                        target="_blank" class="btn btn-sm btn-outline-primary" title="Download File">
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
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2" style="color:#ccc;"></i>
                            <p class="mb-1 fw-semibold">No submissions found.</p>
                            <small>Submit your first report to see it here.</small>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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