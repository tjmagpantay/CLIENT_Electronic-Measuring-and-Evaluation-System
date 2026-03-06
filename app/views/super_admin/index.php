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

$months = [
    1 => 'January',
    2 => 'February',
    3 => 'March',
    4 => 'April',
    5 => 'May',
    6 => 'June',
    7 => 'July',
    8 => 'August',
    9 => 'September',
    10 => 'October',
    11 => 'November',
    12 => 'December'
];
?>

<!-- Row 1: Page Header + Filters -->
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: #092C4C;">Dashboard</h4>
        <p class="text-muted mb-0 small">Here is your report from <?php echo $dateRange; ?>.</p>
    </div>
    <div class="d-flex align-items-center flex-wrap gap-2" style="padding:8px 12px;background:#f8f9fa;border-radius:10px;">
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

        <button class="btn btn-sm text-white" style="background-color: #092C4C;">
            <i class="bi bi-funnel me-1"></i> Apply Filter
        </button>
    </div>
</div>

<!-- Row 2: Compliance Overview + Compliance Rate Chart -->
<div class="row g-3 mb-4">
    <!-- Compliance Overview: gradient cards (same as officer dashboard) -->
    <div class="col-lg-8">
        <div class="card dash-card p-4 h-100">
            <div class="mb-3">
                <h6 class="fw-bold mb-1" style="color:#092C4C;">Compliance Overview</h6>
                <p class="text-muted small mb-0">Summary of office submission compliance for the current period.</p>
            </div>
            <div class="row g-3">
                <!-- Non Compliant -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 rounded-3 p-4"
                        style="background:linear-gradient(135deg,#E43535 0%,#b71c1c 100%);">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="fw-semibold text-uppercase" style="color:rgba(255,255,255,.85);font-size:.72rem;letter-spacing:.6px;">Non Compliant</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                style="width:36px;height:36px;background:rgba(255,255,255,.2);">
                                <i class="bi bi-person-x" style="color:#fff;font-size:.95rem;"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-1" style="color:#fff;"><?php echo $pendingCount; ?></h2>
                        <p class="mb-0" style="color:rgba(255,255,255,.75);font-size:.78rem;"><?php echo $nonCompliantRate; ?>% of total submissions</p>
                    </div>
                </div>
                <!-- Late -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 rounded-3 p-4"
                        style="background:linear-gradient(135deg,#FEC53D 0%,#e6a800 100%);">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="fw-semibold text-uppercase" style="color:rgba(0,0,0,.6);font-size:.72rem;letter-spacing:.6px;">Late Submissions</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                style="width:36px;height:36px;background:rgba(0,0,0,.1);">
                                <i class="bi bi-clock-history" style="color:rgba(0,0,0,.65);font-size:.95rem;"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-1" style="color:#092C4C;"><?php echo $lateCount; ?></h2>
                        <p class="mb-0" style="color:rgba(0,0,0,.55);font-size:.78rem;"><?php echo $lateRate; ?>% of total submissions</p>
                    </div>
                </div>
                <!-- On Time -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 rounded-3 p-4"
                        style="background:linear-gradient(135deg,#092C4C 0%,#1a4a7a 100%);">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="fw-semibold text-uppercase" style="color:rgba(255,255,255,.85);font-size:.72rem;letter-spacing:.6px;">Submitted On Time</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                style="width:36px;height:36px;background:rgba(255,255,255,.2);">
                                <i class="bi bi-graph-up-arrow" style="color:#fff;font-size:.95rem;"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-1" style="color:#fff;"><?php echo $onTimeCount; ?></h2>
                        <p class="mb-0" style="color:rgba(255,255,255,.75);font-size:.78rem;"><?php echo $complianceRate; ?>% on time</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Compliance Rate Donut — chart left, legend right -->
    <div class="col-lg-4">
        <div class="card dash-card p-4 h-100">
            <h6 class="fw-bold mb-1" style="color:#092C4C;">Compliance Rate</h6>
            <p class="text-muted small mb-3">Overall submission status breakdown.</p>
            <div class="d-flex align-items-center gap-3">
                <div style="position:relative;width:130px;height:130px;flex-shrink:0;">
                    <canvas id="complianceChart"></canvas>
                </div>
                <div class="d-flex flex-column gap-2" style="font-size:.8rem;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle flex-shrink-0" style="width:10px;height:10px;background:#092C4C;display:inline-block;"></span>
                        <div class="d-flex align-items-center gap-1">
                            <span class="fw-semibold" style="color:#092C4C;"><?php echo $onTimeCount; ?></span>
                            <span class="text-muted" style="font-size:.72rem;">On Time</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle flex-shrink-0" style="width:10px;height:10px;background:#FFAE4C;display:inline-block;"></span>
                        <div class="d-flex align-items-center gap-1">
                            <span class="fw-semibold" style="color:#092C4C;"><?php echo $lateCount; ?></span>
                            <span class="text-muted" style="font-size:.72rem;">Late</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle flex-shrink-0" style="width:10px;height:10px;background:#EB5050;display:inline-block;"></span>
                        <div class="d-flex align-items-center gap-1">
                            <span class="fw-semibold" style="color:#092C4C;"><?php echo $pendingCount; ?></span>
                            <span class="text-muted" style="font-size:.72rem;">Non Compliant</span>
                        </div>
                    </div>
                    <hr class="my-1">
                    <div class="d-flex align-items-center gap-2">
                        <div class="lh-sm">
                            <div class="fw-bold" style="color:#092C4C;font-size:.95rem;"><?php echo $complianceRate; ?>%</div>
                            <div class="text-muted" style="font-size:.72rem;">Compliance</div>
                        </div>
                    </div>
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
                    <h6 class="fw-bold mb-1" style="color:#092C4C;">Submission Trend</h6>
                    <p class="text-muted small mb-0" id="trendSubtitle">Monthly submission activity for <?php echo date('Y'); ?>.</p>
                </div>
                <div class="btn-group btn-group-sm" role="group" id="trendToggle">
                    <button type="button" class="btn btn-outline-secondary active" data-mode="monthly">Monthly</button>
                    <button type="button" class="btn btn-outline-secondary" data-mode="yearly">Yearly</button>
                </div>
            </div>
            <div style="position:relative;height:260px;">
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
                                            'ON_TIME' => '<span class="badge bg-success bg-opacity-10 text-success rounded-small">On Time</span>',
                                            'LATE' => '<span class="badge bg-warning bg-opacity-10 text-warning rounded-small">Late</span>',
                                            'ERROR' => '<span class="badge bg-danger bg-opacity-10 text-danger rounded-small">Error</span>',
                                            'NO_SUBMISSION' => '<span class="badge bg-secondary bg-opacity-10 text-secondary rounded-small">Pending</span>',
                                            default => '<span class="badge bg-secondary rounded-small">N/A</span>'
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
                <a href="<?php echo env('APP_URL'); ?>/superadmin/reports" class="btn btn-sm btn-outline-secondary">
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
                                        <span class="badge rounded-small" style="background-color: #092C4C; color: #fff;">
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

<?php
// Build PHP data arrays for JS
$monthly = $monthlyTrend; // [1..12] => ['on_time','late','non_compliant']
$monthly_on_time      = array_values(array_map(fn($v) => $v['on_time'],      $monthly));
$monthly_late         = array_values(array_map(fn($v) => $v['late'],         $monthly));
$monthly_non_compliant = array_values(array_map(fn($v) => $v['non_compliant'], $monthly));

$yearly = $yearlyTrend; // [year] => ...
$yearly_labels        = array_keys($yearly);
$yearly_on_time       = array_values(array_map(fn($v) => $v['on_time'],      $yearly));
$yearly_late          = array_values(array_map(fn($v) => $v['late'],         $yearly));
$yearly_non_compliant = array_values(array_map(fn($v) => $v['non_compliant'], $yearly));
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ── Compliance Rate Donut ──
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
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: hasData
                    }
                }
            }
        });

        // ── Submission Trend ──
        var trendCtx = document.getElementById('submissionTrendChart').getContext('2d');

        var monthlyData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            onTime: <?php echo json_encode($monthly_on_time); ?>,
            late: <?php echo json_encode($monthly_late); ?>,
            nonCompliant: <?php echo json_encode($monthly_non_compliant); ?>
        };

        var yearlyData = {
            labels: <?php echo json_encode(array_map('strval', $yearly_labels)); ?>,
            onTime: <?php echo json_encode($yearly_on_time); ?>,
            late: <?php echo json_encode($yearly_late); ?>,
            nonCompliant: <?php echo json_encode($yearly_non_compliant); ?>
        };

        function buildDatasets(data) {
            return [{
                    label: 'On Time',
                    data: data.onTime,
                    borderColor: '#092C4C',
                    backgroundColor: 'rgba(9,44,76,0.08)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#092C4C'
                },
                {
                    label: 'Late',
                    data: data.late,
                    borderColor: '#FFAE4C',
                    backgroundColor: 'rgba(255,174,76,0.08)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#FFAE4C'
                },
                {
                    label: 'Non Compliant',
                    data: data.nonCompliant,
                    borderColor: '#EB5050',
                    backgroundColor: 'rgba(235,80,80,0.08)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#EB5050'
                }
            ];
        }

        var trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: monthlyData.labels,
                datasets: buildDatasets(monthlyData)
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
                            precision: 0,
                            font: {
                                size: 11,
                                family: 'Inter'
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 11,
                                family: 'Inter'
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 20,
                            font: {
                                size: 11,
                                family: 'Inter'
                            }
                        }
                    }
                }
            }
        });

        // Toggle Monthly / Yearly
        document.querySelectorAll('#trendToggle button').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.querySelectorAll('#trendToggle button').forEach(function(b) {
                    b.classList.remove('active');
                });
                this.classList.add('active');

                var mode = this.dataset.mode;
                var src = mode === 'monthly' ? monthlyData : yearlyData;

                trendChart.data.labels = src.labels;
                trendChart.data.datasets = buildDatasets(src);
                trendChart.update();

                document.getElementById('trendSubtitle').textContent =
                    mode === 'monthly' ?
                    'Monthly submission activity for <?php echo date('Y'); ?>.' :
                    'Yearly submission activity overview.';
            });
        });

    });
</script>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>