<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<div class="page-header">
    <h4>Reporting Periods Management</h4>
    <p>Manage reporting periods that LGUs submitting reports for.</p>
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

<div class="card dash-card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">All Reporting Periods</h6>
            <button class="btn btn-sm text-white" style="background-color: #F3AF0E;" data-bs-toggle="modal" data-bs-target="#addPeriodModal">
                <i class="bi bi-plus-circle"></i> Add Reporting Period
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #092C4C; color: #fff;">
                    <tr>
                        <th>#</th>
                        <th>Period</th>
                        <th>Year</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Actions</th>
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
                                    <?php if ($p['deadline']): ?>
                                        <?php echo date('M d, Y', strtotime($p['deadline'])); ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not set</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($p['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Closed</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick='editPeriod(<?php echo json_encode($p); ?>)'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deletePeriod(<?php echo $p['period_id']; ?>, '<?php echo $periodModel->getMonthName($p['period_month']) . ' ' . $p['period_year']; ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
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
    </div>
</div>

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