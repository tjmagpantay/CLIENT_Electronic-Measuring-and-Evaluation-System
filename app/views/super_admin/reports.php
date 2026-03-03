<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<div class="page-header">
    <h4>Report Types Management</h4>
    <p>Manage all report types that LGUs must submit.</p>
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
            <h6 class="mb-0">All Report Types</h6>
            <button class="btn btn-sm text-white" style="background-color: #F3AF0E;" data-bs-toggle="modal" data-bs-target="#addReportTypeModal">
                <i class="bi bi-plus-circle"></i> Add Report Type
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #092C4C; color: #fff;">
                    <tr>
                        <th>#</th>
                        <th>Report Code</th>
                        <th>Report Title</th>
                        <th>Submission Type</th>
                        <th>OPR</th>
                        <th>Deadline Day</th>
                        <th>Status</th>
                        <th>Actions</th>
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
                                <td><small><?php echo htmlspecialchars($rt['opr'] ?? 'Not set'); ?></small></td>
                                <td>Day <?php echo htmlspecialchars($rt['deadline_day'] ?? 15); ?></td>
                                <td>
                                    <?php if ($rt['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick='editReportType(<?php echo json_encode($rt); ?>)'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteReportType(<?php echo $rt['report_type_id']; ?>, '<?php echo htmlspecialchars($rt['report_code']); ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No report types found. Click "Add Report Type" to create one.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Report Type Modal -->
<div class="modal fade" id="addReportTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #092C4C; color: #fff;">
                <h5 class="modal-title">Add Report Type</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo env('APP_URL'); ?>/superadmin/createreporttype">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Report Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="report_code" required placeholder="e.g., BR-001">
                        <small class="text-muted">Will be converted to uppercase</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Report Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="report_title" required placeholder="e.g., Monthly Budget Report">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Brief description of the report"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">OPR (Office of Primary Responsibility)</label>
                        <input type="text" class="form-control" name="opr" placeholder="e.g., Budget Officer - Finance Dept">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Submission Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="submission_type" required>
                            <option value="FILE_UPLOAD">File Upload</option>
                            <option value="GOOGLE_SHEET">Google Sheet</option>
                            <option value="BOTH">Both (Sheet + Files)</option>
                        </select>
                        <small class="text-muted">How officers will submit this report</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Template Link</label>
                        <input type="text" class="form-control" name="template_link" placeholder="https://docs.google.com/spreadsheets/... (for Google Sheets)">
                        <small class="text-muted">Google Sheet URL (required for Google Sheet types)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Default Deadline Day</label>
                        <input type="number" class="form-control" name="deadline_day" min="1" max="31" value="15">
                        <small class="text-muted">Day of the month (1-31)</small>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" id="addIsActive" checked>
                        <label class="form-check-label" for="addIsActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white" style="background-color: #F3AF0E;">Create Report Type</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Report Type Modal -->
<div class="modal fade" id="editReportTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #092C4C; color: #fff;">
                <h5 class="modal-title">Edit Report Type</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo env('APP_URL'); ?>/superadmin/updatereporttype">
                <input type="hidden" name="report_type_id" id="editReportTypeId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Report Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="report_code" id="editReportCode" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Report Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="report_title" id="editReportTitle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="editDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">OPR (Office of Primary Responsibility)</label>
                        <input type="text" class="form-control" name="opr" id="editOpr">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Submission Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="submission_type" id="editSubmissionType" required>
                            <option value="FILE_UPLOAD">File Upload</option>
                            <option value="GOOGLE_SHEET">Google Sheet</option>
                            <option value="BOTH">Both (Sheet + Files)</option>
                        </select>
                        <small class="text-muted">How officers will submit this report</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Template Link</label>
                        <input type="text" class="form-control" name="template_link" id="editTemplateLink">
                        <small class="text-muted">Google Sheet URL (required for Google Sheet types)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Default Deadline Day</label>
                        <input type="number" class="form-control" name="deadline_day" id="editDeadlineDay" min="1" max="31">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" id="editIsActive">
                        <label class="form-check-label" for="editIsActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Report Type</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Report Type Modal -->
<div class="modal fade" id="deleteReportTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo env('APP_URL'); ?>/superadmin/deletereporttype">
                <input type="hidden" name="report_type_id" id="deleteReportTypeId">
                <div class="modal-body">
                    <p>Are you sure you want to delete this report type?</p>
                    <p class="fw-bold" id="deleteReportTypeName"></p>
                    <p class="text-muted small">This will deactivate the report type. Existing submissions will not be affected.</p>
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
    function editReportType(reportType) {
        document.getElementById('editReportTypeId').value = reportType.report_type_id;
        document.getElementById('editReportCode').value = reportType.report_code;
        document.getElementById('editReportTitle').value = reportType.report_title;
        document.getElementById('editDescription').value = reportType.description || '';
        document.getElementById('editOpr').value = reportType.opr || '';
        document.getElementById('editSubmissionType').value = reportType.submission_type || 'FILE_UPLOAD';
        document.getElementById('editTemplateLink').value = reportType.template_link || '';
        document.getElementById('editDeadlineDay').value = reportType.deadline_day || 15;
        document.getElementById('editIsActive').checked = reportType.is_active == 1;

        new bootstrap.Modal(document.getElementById('editReportTypeModal')).show();
    }

    function deleteReportType(id, code) {
        document.getElementById('deleteReportTypeId').value = id;
        document.getElementById('deleteReportTypeName').textContent = code;

        new bootstrap.Modal(document.getElementById('deleteReportTypeModal')).show();
    }
</script>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>