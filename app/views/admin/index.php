<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<?php
// Calculate current week range
$today = new DateTime();
$weekStart = clone $today;
$weekStart->modify('monday this week');
$weekEnd = clone $weekStart;
$weekEnd->modify('+6 days');
$dateRange = $weekStart->format('F d') . ' - ' . $weekEnd->format('F d');

// Calculate compliance rate
$submitted = $onTimeCount + $lateCount;
$total = $submitted + $pendingCount;
$complianceRate = $total > 0 ? round(($onTimeCount / $total) * 100) : 0;
$lateRate = $total > 0 ? round(($lateCount / $total) * 100) : 0;
$nonCompliantRate = $total > 0 ? round(($pendingCount / $total) * 100) : 0;

$months = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June',
           7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];
?>

<!-- Row 1: Page Header + Filters -->
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: #092C4C;">Dashboard</h4>
        <p class="text-muted mb-0 small">Here is your report from <?php echo $dateRange; ?>.</p>
    </div>
    <div class="d-flex align-items-center flex-wrap gap-2">
        <select class="form-select form-select-sm" style="width: auto; min-width: 130px; border-color: #dee2e6;">
            <option value="">All Clusters</option>
            <option value="1">Cluster 1</option>
            <option value="2">Cluster 2</option>
            <option value="3">Cluster 3</option>
        </select>
        <select class="form-select form-select-sm" style="width: auto; min-width: 160px; border-color: #dee2e6;">
            <option value="">All Report Types</option>
            <?php foreach ($reportTypes as $rt): ?>
                <option value="<?php echo $rt['report_type_id']; ?>"><?php echo htmlspecialchars($rt['report_code']); ?></option>
            <?php endforeach; ?>
        </select>
        <select class="form-select form-select-sm" style="width: auto; min-width: 130px; border-color: #dee2e6;">
            <option value="">Monthly</option>
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?php echo $m; ?>" <?php echo ($m == (int)date('n')) ? 'selected' : ''; ?>><?php echo $months[$m]; ?></option>
            <?php endfor; ?>
        </select>
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-outline-secondary active">Q1</button>
            <button type="button" class="btn btn-outline-secondary">Q2</button>
            <button type="button" class="btn btn-outline-secondary">Q3</button>
            <button type="button" class="btn btn-outline-secondary">Q4</button>
        </div>
        <button class="btn btn-sm text-white" style="background-color: #092C4C;">
            <i class="bi bi-funnel me-1"></i> Apply Filter
        </button>
    </div>
</div>

<!-- Row 2: Compliance Overview + Compliance Rate Chart -->
<div class="row g-3 mb-4">
    <!-- Compliance Overview -->
    <div class="col-lg-8">
        <div class="card dash-card p-4 h-100">
            <div class="mb-3">
                <h6 class="fw-bold mb-1" style="color: #092C4C;">Compliance Overview</h6>
                <p class="text-muted small mb-0">Summary of office submission compliance for the current period.</p>
            </div>
            <div class="row g-3">
                <!-- Non Compliant -->
                <div class="col-md-4">
                    <div class="rounded-3 p-3 h-100" style="background-color: #FFE2E2;">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; background-color: rgba(235,80,80,0.2);">
                                <i class="bi bi-x-circle" style="color: #EB5050;"></i>
                            </div>
                            <span class="small fw-semibold" style="color: #92400E;">Non Compliant</span>
                        </div>
                        <h3 class="fw-bold mb-1" style="color: #092C4C;"><?php echo $pendingCount; ?></h3>
                        <p class="small mb-0" style="color: #92400E;">
                            <?php echo $nonCompliantRate; ?>% of total offices
                        </p>
                    </div>
                </div>
                <!-- Late Submissions -->
                <div class="col-md-4">
                    <div class="rounded-3 p-3 h-100" style="background-color: #F5E7CE;">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; background-color: rgba(255,174,76,0.3);">
                                <i class="bi bi-clock-history" style="color: #D97706;"></i>
                            </div>
                            <span class="small fw-semibold" style="color: #92400E;">Late Submissions</span>
                        </div>
                        <h3 class="fw-bold mb-1" style="color: #092C4C;"><?php echo $lateCount; ?></h3>
                        <p class="small mb-0" style="color: #92400E;">
                            <?php echo $lateRate; ?>% of total submissions
                        </p>
                    </div>
                </div>
                <!-- Submitted -->
                <div class="col-md-4">
                    <div class="rounded-3 p-3 h-100" style="background-color: #E3EBF3;">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; background-color: rgba(9,44,76,0.15);">
                                <i class="bi bi-check-circle" style="color: #092C4C;"></i>
                            </div>
                            <span class="small fw-semibold" style="color: #1E3A5F;">Submitted</span>
                        </div>
                        <h3 class="fw-bold mb-1" style="color: #092C4C;"><?php echo $onTimeCount; ?></h3>
                        <p class="small mb-0" style="color: #1E3A5F;">
                            <?php echo $complianceRate; ?>% on time
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Compliance Rate Pie Chart -->
    <div class="col-lg-4">
        <div class="card dash-card p-4 h-100">
            <h6 class="fw-bold mb-1" style="color: #092C4C;">Compliance Rate</h6>
            <p class="text-muted small mb-3">Overall submission status breakdown.</p>
            <div class="d-flex justify-content-center" style="position: relative; height: 200px;">
                <canvas id="complianceChart"></canvas>
            </div>
            <div class="d-flex justify-content-center gap-3 mt-3">
                <div class="d-flex align-items-center">
                    <span class="rounded-circle me-1" style="width: 10px; height: 10px; display: inline-block; background-color: #092C4C;"></span>
                    <small class="text-muted">On Time</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="rounded-circle me-1" style="width: 10px; height: 10px; display: inline-block; background-color: #FFAE4C;"></span>
                    <small class="text-muted">Late</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="rounded-circle me-1" style="width: 10px; height: 10px; display: inline-block; background-color: #EB5050;"></span>
                    <small class="text-muted">Non Compliant</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Submission Trend + Recent Submissions -->
<div class="row g-3 mb-4">
    <!-- Submission Trend -->
    <div class="col-lg-8">
        <div class="card dash-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="fw-bold mb-1" style="color: #092C4C;">Submission Trend</h6>
                    <p class="text-muted small mb-0">Monthly submission activity overview.</p>
                </div>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary active">Month</button>
                    <button type="button" class="btn btn-outline-secondary">Year</button>
                </div>
            </div>
            <div style="position: relative; height: 260px;">
                <canvas id="submissionTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Submissions -->
    <div class="col-lg-4">
        <div class="card dash-card h-100">
            <div class="p-4 pb-2">
                <h6 class="fw-bold mb-1" style="color: #092C4C;">Recent Submissions</h6>
                <p class="text-muted small mb-0">Latest reports submitted by offices.</p>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="ps-4 fw-semibold">Office</th>
                            <th class="fw-semibold">Report</th>
                            <th class="fw-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentSubmissions)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No submissions yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentSubmissions as $rs): ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-semibold"><?php echo htmlspecialchars(mb_strimwidth($rs['office_name'], 0, 20, '...')); ?></span>
                                    </td>
                                    <td>
                                        <span class="text-muted"><?php echo htmlspecialchars($rs['report_code']); ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $statusBadge = match ($rs['submission_status']) {
                                            'ON_TIME' => '<span class="badge bg-success bg-opacity-10 text-success rounded-pill">On Time</span>',
                                            'LATE' => '<span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">Late</span>',
                                            'ERROR' => '<span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">Error</span>',
                                            'NO_SUBMISSION' => '<span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">Pending</span>',
                                            default => '<span class="badge bg-secondary rounded-pill">N/A</span>'
                                        };
                                        echo $statusBadge;
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Row 4: Report Deadlines -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card dash-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="fw-bold mb-1" style="color: #092C4C;">Report Deadlines</h6>
                    <p class="text-muted small mb-0">Upcoming and active report submission deadlines.</p>
                </div>
                <a href="<?php echo env('APP_URL'); ?>/admin/reports" class="btn btn-sm btn-outline-secondary">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="ps-4">Report Code</th>
                            <th>Report Title</th>
                            <th>OPR</th>
                            <th>Deadline Day</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reportDeadlines)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No report types configured yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reportDeadlines as $rd): ?>
                                <?php
                                $deadlineDay = $rd['default_deadline_day'] ?? null;
                                $currentDay = (int)date('j');
                                $daysLeft = $deadlineDay ? $deadlineDay - $currentDay : null;
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge rounded-pill" style="background-color: #092C4C; color: #fff;">
                                            <?php echo htmlspecialchars($rd['report_code']); ?>
                                        </span>
                                    </td>
                                    <td><span class="small"><?php echo htmlspecialchars($rd['report_title']); ?></span></td>
                                    <td><span class="small text-muted"><?php echo htmlspecialchars($rd['opr'] ?? '—'); ?></span></td>
                                    <td>
                                        <?php if ($deadlineDay): ?>
                                            <span class="small fw-semibold">Day <?php echo $deadlineDay; ?> of month</span>
                                        <?php else: ?>
                                            <span class="small text-muted">Not set</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($deadlineDay && $daysLeft !== null): ?>
                                            <?php if ($daysLeft > 5): ?>
                                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success"><?php echo $daysLeft; ?> days left</span>
                                            <?php elseif ($daysLeft > 0): ?>
                                                <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning"><?php echo $daysLeft; ?> days left</span>
                                            <?php elseif ($daysLeft === 0): ?>
                                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger">Due today</span>
                                            <?php else: ?>
                                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger">Overdue</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ── Compliance Rate Pie Chart ──
    var complianceCtx = document.getElementById('complianceChart').getContext('2d');
    var onTime = <?php echo $onTimeCount; ?>;
    var late = <?php echo $lateCount; ?>;
    var nonCompliant = <?php echo $pendingCount; ?>;
    var hasData = (onTime + late + nonCompliant) > 0;

    new Chart(complianceCtx, {
        type: 'doughnut',
        data: {
            labels: ['On Time', 'Late', 'Non Compliant'],
            datasets: [{
                data: hasData ? [onTime, late, nonCompliant] : [1],
                backgroundColor: hasData ? ['#092C4C', '#FFAE4C', '#EB5050'] : ['#e9ecef'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: hasData,
                    callbacks: {
                        label: function(context) {
                            var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                            var pct = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
                            return context.label + ': ' + context.parsed + ' (' + pct + '%)';
                        }
                    }
                }
            }
        },
        plugins: hasData ? [{
            id: 'centerText',
            beforeDraw: function(chart) {
                var ctx = chart.ctx;
                var width = chart.width;
                var height = chart.height;
                ctx.restore();
                ctx.font = 'bold 24px Inter, sans-serif';
                ctx.fillStyle = '#092C4C';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText('<?php echo $complianceRate; ?>%', width / 2, height / 2 - 8);
                ctx.font = '12px Inter, sans-serif';
                ctx.fillStyle = '#6c757d';
                ctx.fillText('Compliance', width / 2, height / 2 + 14);
                ctx.save();
            }
        }] : [{
            id: 'emptyText',
            beforeDraw: function(chart) {
                var ctx = chart.ctx;
                ctx.restore();
                ctx.font = '13px Inter, sans-serif';
                ctx.fillStyle = '#6c757d';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText('No data yet', chart.width / 2, chart.height / 2);
                ctx.save();
            }
        }]
    });

    // ── Submission Trend Line Chart ──
    var trendCtx = document.getElementById('submissionTrendChart').getContext('2d');
    var monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [
                {
                    label: 'On Time',
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    borderColor: '#092C4C',
                    backgroundColor: 'rgba(9,44,76,0.08)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: '#092C4C'
                },
                {
                    label: 'Late',
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    borderColor: '#FFAE4C',
                    backgroundColor: 'rgba(255,174,76,0.08)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: '#FFAE4C'
                },
                {
                    label: 'Non Compliant',
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    borderColor: '#EB5050',
                    backgroundColor: 'rgba(235,80,80,0.08)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: '#EB5050'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { size: 11, family: 'Inter' }
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    ticks: {
                        font: { size: 11, family: 'Inter' }
                    },
                    grid: { display: false }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20,
                        font: { size: 11, family: 'Inter' }
                    }
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>
