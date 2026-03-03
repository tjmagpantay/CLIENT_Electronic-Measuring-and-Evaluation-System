<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<div class="page-header mb-4">
    <h4 class="fw-bold" style="color: #092C4C;">Submit Report</h4>
    <p class="text-muted">Upload report documents for your office. You can submit multiple files per report.</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card dash-card p-4">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <small><i class="bi bi-exclamation-circle me-1"></i><?php echo htmlspecialchars($error); ?></small>
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <small><i class="bi bi-check-circle me-1"></i><?php echo htmlspecialchars($success); ?></small>
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!$office): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-1"></i> Your account is not assigned to any office. Please contact your administrator.
                </div>
            <?php else: ?>
                <form action="<?php echo env('APP_URL'); ?>/officer/upload" method="POST" enctype="multipart/form-data" id="submitForm">
                    <!-- Office and Cluster Information -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold" style="color: #092C4C;">Office</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($office['office_name']); ?>" disabled>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="color: #092C4C;">Cluster</label>
                            <input type="text" class="form-control"
                                value="<?php
                                        require_once __DIR__ . '/../../models/Office.php';
                                        $officeModel = new Office();
                                        echo $officeModel->getClusterName($office['cluster'] ?? '');
                                        ?>"
                                disabled
                                style="background-color: #F3AF0E; color: #092C4C; font-weight: 600;">
                        </div>
                    </div>

                    <?php if (!empty($office['cluster'])): ?>
                        <div class="alert alert-info mb-3" style="background-color: #E3EBF3; border-left: 4px solid #092C4C;">
                            <small>
                                <i class="bi bi-info-circle me-1"></i>
                                <strong>Google Drive Storage:</strong> Your files will be automatically uploaded to the DILG Google Drive folder for
                                <strong><?php echo $officeModel->getClusterName($office['cluster']); ?></strong>, organized by submission month.
                            </small>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="report_type_id" class="form-label fw-semibold" style="color: #092C4C;">Report Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="report_type_id" name="report_type_id" required>
                                <option value="">-- Select Report Type --</option>
                                <?php foreach ($reportTypes as $rt): ?>
                                    <option value="<?php echo $rt['report_type_id']; ?>"
                                        data-opr="<?php echo htmlspecialchars($rt['opr'] ?? 'Not Assigned'); ?>"
                                        data-template="<?php echo htmlspecialchars($rt['template_link'] ?? ''); ?>"
                                        data-deadline="<?php echo htmlspecialchars($rt['deadline_day'] ?? '15'); ?>"
                                        data-submission-type="<?php echo htmlspecialchars($rt['submission_type'] ?? 'FILE_UPLOAD'); ?>">
                                        <?php echo htmlspecialchars($rt['report_code'] . ' - ' . $rt['report_title']); ?>
                                        <?php if (isset($rt['submission_type']) && $rt['submission_type'] === 'GOOGLE_SHEET'): ?>
                                            <span class="badge bg-info">Google Sheet</span>
                                        <?php elseif (isset($rt['submission_type']) && $rt['submission_type'] === 'BOTH'): ?>
                                            <span class="badge bg-warning">Sheet + Files</span>
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="period_id" class="form-label fw-semibold" style="color: #092C4C;">Reporting Period <span class="text-danger">*</span></label>
                            <select class="form-select" id="period_id" name="period_id" required>
                                <option value="">-- Select Period --</option>
                                <?php
                                $periodHelper = new ReportingPeriod();
                                foreach ($periods as $p):
                                ?>
                                    <option value="<?php echo $p['period_id']; ?>"
                                        data-month="<?php echo $p['period_month']; ?>"
                                        data-year="<?php echo $p['period_year']; ?>"
                                        data-deadline="<?php echo $p['deadline'] ? date('M d, Y', strtotime($p['deadline'])) : ''; ?>">
                                        <?php echo $periodHelper->getMonthName($p['period_month']) . ' ' . $p['period_year']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Report Information Card -->
                    <div id="reportInfoCard" class="card mb-3" style="display: none; border-left: 4px solid #F3AF0E;">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-2" style="color: #092C4C;"><i class="bi bi-info-circle me-1"></i> Report Information</h6>
                            <div class="row text-sm">
                                <div class="col-md-4">
                                    <small class="text-muted d-block">OPR (Focal Person)</small>
                                    <strong id="reportOpr" style="color: #092C4C;">-</strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Deadline</small>
                                    <strong id="reportDeadline" style="color: #092C4C;">-</strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Submission Type</small>
                                    <strong id="reportSubmissionType" style="color: #092C4C;">-</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Google Sheet Section (for GOOGLE_SHEET and BOTH types) -->
                    <div id="googleSheetSection" class="mb-3" style="display: none;">
                        <div class="card border-info">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3" style="color: #092C4C;">
                                    <i class="bi bi-table me-1"></i> Google Sheet Data Entry Required
                                </h6>
                                <p class="text-muted small mb-3">
                                    This report requires you to fill in data directly in the Google Sheet. Click the button below to open it in a new tab, fill in your office's data, then return here to submit.
                                </p>

                                <!-- Google Sheet Link Display -->
                                <div class="alert alert-success mb-3" id="sheetLinkBox" style="display: none;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <strong><i class="bi bi-link-45deg me-1"></i> Google Sheet Link:</strong>
                                            <div class="small text-break mt-1" id="sheetLinkDisplay"></div>
                                        </div>
                                        <a href="#" id="googleSheetLink" target="_blank" class="btn btn-success btn-sm ms-3">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> Open Sheet
                                        </a>
                                    </div>
                                </div>

                                <!-- No Link Warning -->
                                <div class="alert alert-warning" id="noSheetLinkWarning" style="display: none;">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    <strong>Google Sheet link not set up yet.</strong> Please contact your administrator to add the Google Sheet link for this report.
                                </div>

                                <div class="form-check" id="sheetCompletedCheck" style="display: none;">
                                    <input type="checkbox" class="form-check-input" id="sheetCompleted" name="sheet_completed">
                                    <label class="form-check-label" for="sheetCompleted">
                                        <strong>✓ I have completed filling out the Google Sheet</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- File Upload Section (for FILE_UPLOAD and BOTH types) -->
                    <div id="fileUploadSection" class="mb-3">
                        <label for="report_files" class="form-label fw-semibold" style="color: #092C4C;">
                            Upload Files <span class="text-danger" id="uploadRequired">*</span>
                            <small class="text-muted fw-normal">(You can select multiple files)</small>
                        </label>
                        <input type="file" class="form-control" id="report_files" name="report_files[]" multiple required
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Accepted formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG. Max size per file: 10MB.
                        </div>
                        <div id="fileList" class="mt-2"></div>
                    </div>

                    <div class="mb-4">
                        <label for="remarks" class="form-label fw-semibold" style="color: #092C4C;">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Optional remarks or notes about this submission..."></textarea>
                    </div>

                    <button type="submit" class="btn text-white fw-semibold px-4" style="background-color: #F3AF0E;">
                        <i class="bi bi-upload me-1"></i> Submit Report
                    </button>
                    <a href="<?php echo env('APP_URL'); ?>/officer/submissions" class="btn btn-outline-secondary px-4 ms-2">
                        <i class="bi bi-arrow-left me-1"></i> Back to Submissions
                    </a>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card dash-card p-4 mb-3">
            <h6 class="fw-bold mb-3" style="color: #092C4C;"><i class="bi bi-lightbulb me-1"></i> Guidelines</h6>
            <ul class="small text-muted ps-3 mb-0">
                <li class="mb-2">Select the correct report type and period before uploading.</li>
                <li class="mb-2">You can upload <strong>multiple files</strong> (e.g., Excel, PDF, images) in one submission.</li>
                <li class="mb-2">Check the OPR (focal person) and deadline for each report type.</li>
                <li class="mb-2">Download the template if available to ensure correct format.</li>
                <li class="mb-2">Ensure each file is in the correct format and does not exceed 10MB.</li>
                <li class="mb-2">Only one submission per report type per period is allowed.</li>
                <li class="mb-2">Reports submitted after the deadline will be marked as <span class="badge bg-warning text-dark">LATE</span>.</li>
            </ul>
        </div>

        <!-- Submission Status Guide -->
        <div class="card dash-card p-4">
            <h6 class="fw-bold mb-3" style="color: #092C4C;"><i class="bi bi-flag me-1"></i> Status Guide</h6>
            <div class="mb-2">
                <span class="badge bg-success">On Time</span>
                <small class="text-muted d-block mt-1">Submitted before or on the deadline</small>
            </div>
            <div class="mb-2">
                <span class="badge bg-warning text-dark">Late</span>
                <small class="text-muted d-block mt-1">Submitted after the deadline</small>
            </div>
            <div class="mb-0">
                <span class="badge bg-secondary">No Submission</span>
                <small class="text-muted d-block mt-1">Report not yet submitted</small>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reportTypeSelect = document.getElementById('report_type_id');
        const periodSelect = document.getElementById('period_id');
        const reportInfoCard = document.getElementById('reportInfoCard');
        const reportOpr = document.getElementById('reportOpr');
        const reportDeadline = document.getElementById('reportDeadline');
        const reportSubmissionType = document.getElementById('reportSubmissionType');
        const googleSheetSection = document.getElementById('googleSheetSection');
        const googleSheetLink = document.getElementById('googleSheetLink');
        const sheetCompleted = document.getElementById('sheetCompleted');
        const sheetLinkBox = document.getElementById('sheetLinkBox');
        const sheetLinkDisplay = document.getElementById('sheetLinkDisplay');
        const noSheetLinkWarning = document.getElementById('noSheetLinkWarning');
        const sheetCompletedCheck = document.getElementById('sheetCompletedCheck');
        const fileUploadSection = document.getElementById('fileUploadSection');
        const fileInput = document.getElementById('report_files');
        const uploadRequired = document.getElementById('uploadRequired');
        const fileList = document.getElementById('fileList');
        const submitForm = document.getElementById('submitForm');

        // Show report information when report type is selected
        reportTypeSelect.addEventListener('change', function() {
            updateReportInfo();
        });

        periodSelect.addEventListener('change', function() {
            updateReportInfo();
        });

        function updateReportInfo() {
            const selectedOption = reportTypeSelect.options[reportTypeSelect.selectedIndex];
            const selectedPeriod = periodSelect.options[periodSelect.selectedIndex];

            if (reportTypeSelect.value && selectedOption) {
                const opr = selectedOption.dataset.opr || 'Not Assigned';
                const template = selectedOption.dataset.template || '';
                const submissionType = selectedOption.dataset.submissionType || 'FILE_UPLOAD';

                reportOpr.textContent = opr;

                // Show submission type
                if (submissionType === 'GOOGLE_SHEET') {
                    reportSubmissionType.innerHTML = '<span class="badge bg-info">Google Sheet</span>';
                } else if (submissionType === 'BOTH') {
                    reportSubmissionType.innerHTML = '<span class="badge bg-warning text-dark">Sheet + Files</span>';
                } else {
                    reportSubmissionType.innerHTML = '<span class="badge bg-primary">File Upload</span>';
                }

                // Show period deadline (set by Super Admin)
                if (periodSelect.value && selectedPeriod) {
                    const deadline = selectedPeriod.dataset.deadline;

                    if (deadline) {
                        reportDeadline.textContent = deadline;

                        // Check if deadline has passed
                        const deadlineDate = new Date(deadline);
                        const now = new Date();
                        if (now > deadlineDate) {
                            reportDeadline.innerHTML = deadline + ' <span class="badge bg-warning text-dark ms-1">Late Submission</span>';
                        }
                    } else {
                        reportDeadline.textContent = 'No deadline set';
                    }
                } else {
                    reportDeadline.textContent = 'Select period to see deadline';
                }

                // Handle different submission types
                if (submissionType === 'GOOGLE_SHEET') {
                    // Show Google Sheet section only
                    googleSheetSection.style.display = 'block';
                    fileUploadSection.style.display = 'none';
                    fileInput.required = false;
                    sheetCompleted.required = true;

                    if (template) {
                        // Show the link box with the actual URL
                        googleSheetLink.href = template;
                        sheetLinkDisplay.textContent = template;
                        sheetLinkBox.style.display = 'block';
                        noSheetLinkWarning.style.display = 'none';
                        sheetCompletedCheck.style.display = 'block';
                    } else {
                        // Show warning that link is not configured
                        sheetLinkBox.style.display = 'none';
                        noSheetLinkWarning.style.display = 'block';
                        sheetCompletedCheck.style.display = 'none';
                    }
                } else if (submissionType === 'BOTH') {
                    // Show both sections
                    googleSheetSection.style.display = 'block';
                    fileUploadSection.style.display = 'block';
                    fileInput.required = true;
                    sheetCompleted.required = true;
                    uploadRequired.textContent = '*';

                    if (template) {
                        // Show the link box with the actual URL
                        googleSheetLink.href = template;
                        sheetLinkDisplay.textContent = template;
                        sheetLinkBox.style.display = 'block';
                        noSheetLinkWarning.style.display = 'none';
                        sheetCompletedCheck.style.display = 'block';
                    } else {
                        // Show warning that link is not configured
                        sheetLinkBox.style.display = 'none';
                        noSheetLinkWarning.style.display = 'block';
                        sheetCompletedCheck.style.display = 'none';
                    }
                } else {
                    // FILE_UPLOAD - Show file upload section only
                    googleSheetSection.style.display = 'none';
                    fileUploadSection.style.display = 'block';
                    fileInput.required = true;
                    sheetCompleted.required = false;
                    uploadRequired.textContent = '*';
                }

                reportInfoCard.style.display = 'block';
            } else {
                reportInfoCard.style.display = 'none';
                googleSheetSection.style.display = 'none';
            }
        }

        // Display selected files
        fileInput.addEventListener('change', function() {
            const files = Array.from(this.files);
            if (files.length === 0) {
                fileList.innerHTML = '';
                return;
            }

            let html = '<div class="alert alert-info py-2 px-3 small"><strong><i class="bi bi-files me-1"></i>' + files.length + ' file(s) selected:</strong><ul class="mb-0 mt-1 ps-3">';
            files.forEach(file => {
                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                html += '<li>' + file.name + ' <span class="text-muted">(' + sizeMB + ' MB)</span></li>';
            });
            html += '</ul></div>';
            fileList.innerHTML = html;
        });
    });
</script>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>