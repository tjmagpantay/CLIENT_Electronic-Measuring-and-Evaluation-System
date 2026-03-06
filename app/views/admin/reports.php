<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#092C4C;">Report Types</h4>
        <p class="text-muted mb-0 small">Manage all report types that LGUs must submit.</p>
    </div>
    <button class="btn text-white" style="background-color:#092C4C;" data-bs-toggle="modal" data-bs-target="#addReportTypeModal">
        <i class="bi bi-plus-lg me-1"></i> Add Report Type
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
        <div class="input-group input-group-sm" style="max-width:250px;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" id="reportSearch" class="form-control border-start-0 ps-0" placeholder="Search report...">
        </div>
        <!-- Submission Type Filter -->
        <select id="filterSubmissionType" class="form-select form-select-sm" style="width:auto;min-width:150px;">
            <option value="">All Types</option>
            <option value="FILE_UPLOAD">File Upload</option>
            <option value="GOOGLE_SHEET">Google Sheet</option>
            <option value="BOTH">Both</option>
        </select>
        <!-- Status Filter -->
        <select id="filterStatus" class="form-select form-select-sm" style="width:auto;min-width:120px;">
            <option value="">All Status</option>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>
        <!-- Reset -->
        <button id="resetFilters" class="btn btn-sm btn-outline-secondary">Reset</button>
        <!-- Results count -->
        <span id="reportCount" class="ms-auto small text-muted"></span>
    </div>

    <!-- Table -->
    <div class="table-responsive" style="overflow-x: auto; overflow-y: visible;">
        <table class="table table-hover align-middle mb-0" id="reportsTable">
            <thead style="background-color:#f8f9fa;">
                <tr>
                    <th class="ps-4" style="background:#f8f9fa;">#</th>
                    <th style="background:#f8f9fa;">Report Code</th>
                    <th style="background:#f8f9fa;">Report Title</th>
                    <th style="background:#f8f9fa;">Submission Type</th>
                    <th style="background:#f8f9fa;">OPR</th>
                    <th style="background:#f8f9fa;">Deadline Day</th>
                    <th style="background:#f8f9fa;">Status</th>
                    <th class="text-center" style="background:#f8f9fa;">Actions</th>
                </tr>
            </thead>
            <tbody id="reportsBody">
                <?php if (!empty($reportTypes)): ?>
                    <?php foreach ($reportTypes as $i => $rt): ?>
                        <tr
                            data-code="<?php echo strtolower(htmlspecialchars($rt['report_code'])); ?>"
                            data-title="<?php echo strtolower(htmlspecialchars($rt['report_title'])); ?>"
                            data-submission-type="<?php echo htmlspecialchars($rt['submission_type'] ?? 'FILE_UPLOAD'); ?>"
                            data-status="<?php echo $rt['is_active'] ? '1' : '0'; ?>">
                            <td class="ps-4 small text-muted"><?php echo $i + 1; ?></td>
                            <td>
                                <span class="badge rounded-small" style="background-color:#092C4C;color:#fff;">
                                    <?php echo htmlspecialchars($rt['report_code']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold small"><?php echo htmlspecialchars($rt['report_title']); ?></span>
                            </td>
                            <td>
                                <?php
                                $type = $rt['submission_type'] ?? 'FILE_UPLOAD';
                                if ($type === 'GOOGLE_SHEET'): ?>
                                    <span class="badge rounded-small bg-success"><i class="bi bi-file-spreadsheet me-1"></i>Google Sheet</span>
                                <?php elseif ($type === 'BOTH'): ?>
                                    <span class="badge rounded-small" style="background-color:#F3AF0E;color:#fff;"><i class="bi bi-files me-1"></i>Sheet + Files</span>
                                <?php else: ?>
                                    <span class="badge rounded-small" style="background-color:#092C4C;color:#fff;"><i class="bi bi-cloud-upload me-1"></i>File Upload</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="small text-muted"><?php echo htmlspecialchars($rt['opr'] ?? '—'); ?></span></td>
                            <td><span class="small">Day <?php echo htmlspecialchars($rt['deadline_day'] ?? 15); ?></span></td>
                            <td>
                                <?php if ($rt['is_active']): ?>
                                    <span class="badge rounded-small bg-success bg-opacity-10 text-success">Active</span>
                                <?php else: ?>
                                    <span class="badge rounded-small bg-danger bg-opacity-10 text-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="#" onclick='editReportType(<?php echo json_encode($rt); ?>); return false;'>
                                                <i class="bi bi-pencil me-2"></i>Edit
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="#" onclick="deleteReportType(<?php echo $rt['report_type_id']; ?>, '<?php echo htmlspecialchars($rt['report_code']); ?>'); return false;">
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
                        <td colspan="8" class="text-center text-muted py-4">
                            No report types found. Click "Add Report Type" to create one.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- No results row (hidden initially) -->
    <div id="noResults" class="text-center text-muted py-4" style="display:none!important;">No report types match your filters.</div>
</div>

<script>
    (function() {
        var searchInput = document.getElementById('reportSearch');
        var filterSubmissionType = document.getElementById('filterSubmissionType');
        var filterStatus = document.getElementById('filterStatus');
        var resetBtn = document.getElementById('resetFilters');
        var tbody = document.getElementById('reportsBody');
        var countEl = document.getElementById('reportCount');
        var noResults = document.getElementById('noResults');

        function applyFilters() {
            var search = searchInput.value.toLowerCase().trim();
            var submissionType = filterSubmissionType.value;
            var status = filterStatus.value;
            var rows = tbody.querySelectorAll('tr[data-code]');
            var visible = 0;

            rows.forEach(function(row) {
                var matchSearch = !search || row.dataset.code.includes(search) || row.dataset.title.includes(search);
                var matchType = !submissionType || row.dataset.submissionType === submissionType;
                var matchStatus = !status || row.dataset.status === status;
                var show = matchSearch && matchType && matchStatus;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            countEl.textContent = visible + ' report type' + (visible !== 1 ? 's' : '') + ' found';
            noResults.style.setProperty('display', visible === 0 ? 'block' : 'none', 'important');
        }

        searchInput.addEventListener('input', applyFilters);
        filterSubmissionType.addEventListener('change', applyFilters);
        filterStatus.addEventListener('change', applyFilters);

        resetBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterSubmissionType.value = '';
            filterStatus.value = '';
            applyFilters();
        });

        applyFilters();
    })();
</script>

<!-- Add Report Type Modal -->
<div class="modal fade" id="addReportTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #092C4C; color: #fff;">
                <h5 class="modal-title">Add Report Type</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo env('APP_URL'); ?>/admin/createreporttype">
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
            <form method="POST" action="<?php echo env('APP_URL'); ?>/admin/updatereporttype">
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
            <form method="POST" action="<?php echo env('APP_URL'); ?>/admin/deletereporttype">
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