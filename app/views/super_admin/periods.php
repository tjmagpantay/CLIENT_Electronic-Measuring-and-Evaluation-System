<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#092C4C;">Reporting Periods</h4>
        <p class="text-muted mb-0 small">Manage reporting periods that LGUs submitting reports for.</p>
    </div>
    <button class="btn text-white" style="background-color:#092C4C;" data-bs-toggle="modal" data-bs-target="#addPeriodModal">
        <i class="bi bi-plus-lg me-1"></i> Add Reporting Period
    </button>
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

<div class="card dash-card" style="border-radius: 8px; overflow: visible;">
    <!-- Search bar inside card -->
    <div class="p-3 d-flex align-items-center flex-wrap gap-2" style="background:#f8f9fa;border-bottom:1px solid #e9ecef;">
        <!-- Search -->
        <div class="input-group input-group-sm" style="max-width:200px;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" id="periodSearch" class="form-control border-start-0 ps-0" placeholder="Search period...">
        </div>
        <!-- Year Filter -->
        <select id="filterYear" class="form-select form-select-sm" style="width:auto;min-width:120px;">
            <option value="">All Years</option>
            <?php
            $uniqueYears = array_unique(array_column($periods, 'period_year'));
            rsort($uniqueYears);
            foreach ($uniqueYears as $year): ?>
                <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
            <?php endforeach; ?>
        </select>
        <!-- Status Filter -->
        <select id="filterStatus" class="form-select form-select-sm" style="width:auto;min-width:120px;">
            <option value="">All Status</option>
            <option value="1">Active</option>
            <option value="0">Closed</option>
        </select>
        <!-- Reset -->
        <button id="resetFilters" class="btn btn-sm btn-outline-secondary">Reset</button>
        <!-- Results count -->
        <span id="periodCount" class="ms-auto small text-muted"></span>
    </div>

    <!-- Table -->
    <div class="table-responsive" style="overflow-x: auto; overflow-y: visible;">
        <table class="table table-hover align-middle mb-0" id="periodsTable">
            <thead style="background-color:#f8f9fa;">
                <tr>
                    <th class="ps-4" style="background:#f8f9fa;">#</th>
                    <th style="background:#f8f9fa;">Period</th>
                    <th style="background:#f8f9fa;">Year</th>
                    <th style="background:#f8f9fa;">Deadline</th>
                    <th style="background:#f8f9fa;">Status</th>
                    <th class="text-center" style="background:#f8f9fa;">Actions</th>
                </tr>
            </thead>
            <tbody id="periodsBody">
                <?php if (!empty($periods)): ?>
                    <?php foreach ($periods as $i => $p): ?>
                        <tr
                            data-period="<?php echo strtolower($periodModel->getMonthName($p['period_month'])); ?>"
                            data-year="<?php echo $p['period_year']; ?>"
                            data-status="<?php echo $p['is_active'] ? '1' : '0'; ?>">
                            <td class="ps-4 small text-muted"><?php echo $i + 1; ?></td>
                            <td>
                                <span class="fw-semibold small"><?php echo $periodModel->getMonthName($p['period_month']); ?></span>
                            </td>
                            <td><span class="small"><?php echo $p['period_year']; ?></span></td>
                            <td>
                                <?php if ($p['deadline']): ?>
                                    <span class="small"><?php echo date('M d, Y', strtotime($p['deadline'])); ?></span>
                                <?php else: ?>
                                    <span class="small text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['is_active']): ?>
                                    <span class="badge rounded-small bg-success bg-opacity-10 text-success">Active</span>
                                <?php else: ?>
                                    <span class="badge rounded-small bg-secondary bg-opacity-10 text-secondary">Closed</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="#" onclick='editPeriod(<?php echo json_encode($p); ?>); return false;'>
                                                <i class="bi bi-pencil me-2"></i>Edit
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="#" onclick="deletePeriod(<?php echo $p['period_id']; ?>, '<?php echo $periodModel->getMonthName($p['period_month']) . ' ' . $p['period_year']; ?>'); return false;">
                                                <i class="bi bi-trash me-2"></i>Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No reporting periods found. Click "Add Reporting Period" to create one.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- No results row (hidden initially) -->
    <div id="noResults" class="text-center text-muted py-4" style="display:none!important;">No periods match your filters.</div>
</div>

<script>
    (function() {
        var searchInput = document.getElementById('periodSearch');
        var filterYear = document.getElementById('filterYear');
        var filterStatus = document.getElementById('filterStatus');
        var resetBtn = document.getElementById('resetFilters');
        var tbody = document.getElementById('periodsBody');
        var countEl = document.getElementById('periodCount');
        var noResults = document.getElementById('noResults');

        function applyFilters() {
            var search = searchInput.value.toLowerCase().trim();
            var year = filterYear.value;
            var status = filterStatus.value;
            var rows = tbody.querySelectorAll('tr[data-period]');
            var visible = 0;

            rows.forEach(function(row) {
                var matchSearch = !search || row.dataset.period.includes(search);
                var matchYear = !year || row.dataset.year === year;
                var matchStatus = !status || row.dataset.status === status;
                var show = matchSearch && matchYear && matchStatus;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            countEl.textContent = visible + ' period' + (visible !== 1 ? 's' : '') + ' found';
            noResults.style.setProperty('display', visible === 0 ? 'block' : 'none', 'important');
        }

        searchInput.addEventListener('input', applyFilters);
        filterYear.addEventListener('change', applyFilters);
        filterStatus.addEventListener('change', applyFilters);

        resetBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterYear.value = '';
            filterStatus.value = '';
            applyFilters();
        });

        applyFilters();
    })();
</script>

<!-- Add Period Modal -->
<div class="modal fade" id="addPeriodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #092C4C; color: #fff;">
                <h5 class="modal-title">Add Reporting Period</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo env('APP_URL'); ?>/superadmin/createperiod">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Month <span class="text-danger">*</span></label>
                        <select class="form-select" name="period_month" required>
                            <option value="">Select Month</option>
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Year <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="period_year" min="2020" max="2099" value="<?php echo date('Y'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deadline (Optional)</label>
                        <input type="date" class="form-control" name="deadline">
                        <small class="text-muted">Set a specific deadline for all reports in this period</small>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" id="addIsActive" checked>
                        <label class="form-check-label" for="addIsActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white" style="background-color: #F3AF0E;">Create Period</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Period Modal -->
<div class="modal fade" id="editPeriodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #092C4C; color: #fff;">
                <h5 class="modal-title">Edit Reporting Period</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo env('APP_URL'); ?>/superadmin/updateperiod">
                <input type="hidden" name="period_id" id="editPeriodId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Month <span class="text-danger">*</span></label>
                        <select class="form-select" name="period_month" id="editPeriodMonth" required>
                            <option value="">Select Month</option>
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Year <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="period_year" id="editPeriodYear" min="2020" max="2099" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deadline (Optional)</label>
                        <input type="date" class="form-control" name="deadline" id="editDeadline">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" id="editIsActive">
                        <label class="form-check-label" for="editIsActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Period</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Period Modal -->
<div class="modal fade" id="deletePeriodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo env('APP_URL'); ?>/superadmin/deleteperiod">
                <input type="hidden" name="period_id" id="deletePeriodId">
                <div class="modal-body">
                    <p>Are you sure you want to delete this reporting period?</p>
                    <p class="fw-bold" id="deletePeriodName"></p>
                    <p class="text-muted small">This will close the period. Existing submissions will not be affected.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editPeriod(period) {
        document.getElementById('editPeriodId').value = period.period_id;
        document.getElementById('editPeriodMonth').value = period.period_month;
        document.getElementById('editPeriodYear').value = period.period_year;
        document.getElementById('editDeadline').value = period.deadline ? period.deadline.split(' ')[0] : '';
        document.getElementById('editIsActive').checked = period.is_active == 1;

        new bootstrap.Modal(document.getElementById('editPeriodModal')).show();
    }

    function deletePeriod(id, name) {
        document.getElementById('deletePeriodId').value = id;
        document.getElementById('deletePeriodName').textContent = name;

        new bootstrap.Modal(document.getElementById('deletePeriodModal')).show();
    }
</script>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>