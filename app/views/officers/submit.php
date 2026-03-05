<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<!-- Page Header -->
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: #092C4C;">Submit Report</h4>
        <p class="text-muted mb-0 small">Upload report documents for your office. You can submit multiple files per report.</p>
    </div>
    <a href="<?php echo env('APP_URL'); ?>/officer/submissions" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Submissions
    </a>
</div>

<div class="row g-4">
    <!-- Main Form -->
    <div class="col-lg-8">
        <div style="border-radius:16px;background:#fff;box-shadow:0 1px 6px rgba(0,0,0,.07);overflow:hidden;">
            <!-- Form Header -->
            <div class="px-4 py-3" style="background:#092C4C;">
                <h6 class="fw-bold text-white mb-0"><i class="bi bi-file-earmark-arrow-up me-2"></i>Report Submission Form</h6>
            </div>
            <div class="p-4">
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

                        <!-- Office & Cluster -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold small mb-1" style="color:#092C4C;">Office</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($office['office_name']); ?>" disabled style="background:#f8f9fa;color:#495057;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small mb-1" style="color:#092C4C;">Cluster</label>
                                <?php
                                require_once __DIR__ . '/../../models/Office.php';
                                $officeModel = new Office();
                                $clusterName = $officeModel->getClusterName($office['cluster'] ?? '');
                                ?>
                                <div class="d-flex align-items-center justify-content-center rounded-2 fw-bold py-2 px-3 text-center"
                                    style="background:#F3AF0E;color:#092C4C;font-size:.9rem;min-height:38px;">
                                    <?php echo htmlspecialchars($clusterName); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Report Type & Period -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="report_type_id" class="form-label fw-semibold small mb-1" style="color:#092C4C;">Report Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="report_type_id" name="report_type_id" style="font-size: 0.85rem;" required>
                                    <option value="" > Select Report Type</option>
                                    <?php foreach ($reportTypes as $rt): ?>
                                        <option value="<?php echo $rt['report_type_id']; ?>"
                                            data-opr="<?php echo htmlspecialchars($rt['opr'] ?? 'Not Assigned'); ?>"
                                            data-template="<?php echo htmlspecialchars($rt['template_link'] ?? ''); ?>"
                                            data-deadline="<?php echo htmlspecialchars($rt['deadline_day'] ?? '15'); ?>"
                                            data-submission-type="<?php echo htmlspecialchars($rt['submission_type'] ?? 'FILE_UPLOAD'); ?>">
                                            <?php echo htmlspecialchars($rt['report_code'] . ' - ' . $rt['report_title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="period_id" class="form-label fw-semibold small mb-1" style="color:#092C4C;">Reporting Period <span class="text-danger">*</span></label>
                                <select class="form-select" id="period_id" name="period_id" style="font-size: 0.85rem;" required>
                                    <option value="">Select Period</option>
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

                        <!-- Report Information Card (dynamic) -->
                        <div id="reportInfoCard" class="rounded-2 p-3 mb-3" style="display:none;background:#F8F9FA;border-left:4px solid #F3AF0E;">
                            <div class="row g-2" style="font-size:.82rem;">
                                <div class="col-md-4">
                                    <span class="text-muted d-block">OPR (Focal Person)</span>
                                    <strong id="reportOpr" style="color:#092C4C;">-</strong>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-muted d-block">Deadline</span>
                                    <strong id="reportDeadline" style="color:#092C4C;">-</strong>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-muted d-block">Submission Type</span>
                                    <strong id="reportSubmissionType" style="color:#092C4C;">-</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Google Drive Storage Notice -->
                        <?php if (!empty($office['cluster'])): ?>
                            <div class="rounded-2 p-3 mb-3" style="background:#EAF1FB;border-left:4px solid #4A90D9;font-size:.83rem;">
                                <i class="bi bi-info-circle me-1" style="color:#4A90D9;"></i>
                                <strong>Google Drive Storage:</strong> Your files will be automatically uploaded to the DILG Google Drive folder for
                                <strong><?php echo htmlspecialchars($clusterName); ?></strong>, organized by submission month.
                            </div>
                        <?php endif; ?>

                        <!-- Google Sheet Section -->
                        <div id="googleSheetSection" class="mb-3" style="display:none;">
                            <div class="rounded-2 p-3" style="border:1px solid #17a2b8;background:#f0fafb;">
                                <h6 class="fw-bold mb-2" style="color:#092C4C;font-size:.88rem;">
                                    <i class="bi bi-table me-1"></i> Google Sheet Data Entry Required
                                </h6>
                                <p class="text-muted mb-3" style="font-size:.82rem;">
                                    This report requires you to fill in data directly in the Google Sheet. Open it in a new tab, fill in your data, then return here to submit.
                                </p>
                                <div class="alert alert-success py-2 px-3 mb-2" id="sheetLinkBox" style="display:none;font-size:.82rem;">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <div class="overflow-hidden">
                                            <strong><i class="bi bi-link-45deg me-1"></i> Google Sheet Link:</strong>
                                            <div class="text-truncate mt-1" id="sheetLinkDisplay" style="max-width:320px;"></div>
                                        </div>
                                        <a href="#" id="googleSheetLink" target="_blank" class="btn btn-success btn-sm flex-shrink-0">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> Open
                                        </a>
                                    </div>
                                </div>
                                <div class="alert alert-warning py-2 px-3 mb-2" id="noSheetLinkWarning" style="display:none;font-size:.82rem;">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    <strong>Google Sheet link not set up yet.</strong> Contact your administrator.
                                </div>
                                <div class="form-check mt-2" id="sheetCompletedCheck" style="display:none;">
                                    <input type="checkbox" class="form-check-input" id="sheetCompleted" name="sheet_completed">
                                    <label class="form-check-label fw-semibold" for="sheetCompleted" style="font-size:.83rem;">
                                        ✓ I have completed filling out the Google Sheet
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- File Upload Section -->
                        <div id="fileUploadSection" class="mb-3">
                            <label class="form-label fw-semibold small mb-1" style="color:#092C4C;">
                                Upload Files <span class="text-danger" id="uploadRequired">*</span>
                            </label>
                            <!-- Hidden actual input -->
                            <input type="file" id="report_files" name="report_files[]" multiple required
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" style="display:none;">
                            <!-- Upload trigger area -->
                            <div id="uploadDropZone" class="rounded-2 d-flex align-items-center justify-content-between px-3 py-2"
                                style="background:#EAF1FB;border:1.5px dashed #4A90D9;cursor:pointer;"
                                onclick="document.getElementById('report_files').click();">
                                <div style="font-size:.82rem;color:#4A90D9;">
                                    <i class="bi bi-cloud-arrow-up me-1"></i>
                                    <strong>Choose Files</strong>
                                    <span class="text-muted" style="color:#6b9bc8 !important;"> — PDF, DOC, DOCX, XLS, XLSX, JPG, PNG &nbsp;·&nbsp; Max 10MB each</span>
                                </div>
                                <span class="btn btn-sm px-3" style="background:#4A90D9;color:#fff;font-size:.78rem;">Browse</span>
                            </div>
                            <!-- File list -->
                            <div id="fileList" class="mt-2"></div>
                        </div>

                        <!-- Remarks -->
                        <div class="mb-4">
                            <label for="remarks" class="form-label fw-semibold small mb-1" style="color:#092C4C;">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3"
                                style="font-size: 0.85rem;"
                                placeholder="Optional remarks or notes about this submission..."></textarea>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-end gap-2 ">
                            <button type="submit" class="btn fw-normal px-4" style="background-color:#092c4c;color:#ffffff;">
                                <i class="bi bi-upload me-1"></i> Submit Report
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Guidelines -->
        <div style="border-radius:16px;background:#fff;box-shadow:0 1px 6px rgba(0,0,0,.07);overflow:hidden;" class="mb-4">
            <div class="px-4 py-3" style="background:#092C4C;">
                <h6 class="fw-bold text-white mb-0"><i class="bi bi-lightbulb me-2"></i>Guidelines</h6>
            </div>
            <ul class="small text-muted ps-4 pe-3 py-3 mb-0" style="font-size:.82rem;">
                <li class="mb-2">Select the correct report type and period before uploading.</li>
                <li class="mb-2">You can upload <strong>multiple files</strong> (e.g., Excel, PDF, images) in one submission.</li>
                <li class="mb-2">Check the OPR (focal person) and deadline for each report type.</li>
                <li class="mb-2">Download the template if available to ensure correct format.</li>
                <li class="mb-2">Ensure each file does not exceed <strong>10MB</strong>.</li>
                <li class="mb-2">Only one submission per report type per period is allowed.</li>
                <li class="mb-0">Reports submitted after the deadline will be marked as <span class="badge bg-warning text-dark">LATE</span>.</li>
            </ul>
        </div>

        <!-- Status Guide -->
        <div style="border-radius:16px;background:#fff;box-shadow:0 1px 6px rgba(0,0,0,.07);overflow:hidden;">
            <div class="px-4 py-3" style="background:#092C4C;">
                <h6 class="fw-bold text-white mb-0"><i class="bi bi-flag me-2"></i>Status Guide</h6>
            </div>
            <div class="p-4" style="font-size:.82rem;">
                <div class="d-flex align-items-start gap-2 mb-3 ">
                    <span class="badge bg-success p-2">On Time</span>
                    <span class="text-muted">Submitted before or on the deadline</span>
                </div>
                <div class="d-flex align-items-start gap-2 mb-3">
                    <span class="badge bg-warning text-dark p-2">Late</span>
                    <span class="text-muted">Submitted after the deadline</span>
                </div>
                <div class="d-flex align-items-start gap-2">
                    <span class="badge bg-secondary p-2" style="white-space:nowrap;">No Submission</span>
                    <span class="text-muted">Report not yet submitted for the period</span>
                </div>
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

        // --- Multi-file accumulation ---
        let allFiles = new DataTransfer();

        function getFileIcon(name) {
            const ext = name.split('.').pop().toLowerCase();
            if (['pdf'].includes(ext)) return 'bi-file-earmark-pdf text-danger';
            if (['doc', 'docx'].includes(ext)) return 'bi-file-earmark-word text-primary';
            if (['xls', 'xlsx'].includes(ext)) return 'bi-file-earmark-excel text-success';
            if (['jpg', 'jpeg', 'png'].includes(ext)) return 'bi-file-earmark-image text-warning';
            return 'bi-file-earmark text-secondary';
        }

        function renderFileList() {
            const files = Array.from(allFiles.files);
            if (files.length === 0) {
                fileList.innerHTML = '';
                return;
            }
            let html = '<div class="rounded-2 overflow-hidden" style="border:1px solid #c8ddf5;">';
            // header
            html += '<div class="d-flex align-items-center justify-content-between px-3 py-2" style="background:#4A90D9;">' +
                '<span style="font-size:.78rem;color:#fff;font-weight:600;"><i class="bi bi-files me-1"></i>' + files.length + ' file(s) queued</span>' +
                '<button type="button" class="btn btn-sm py-0 px-2" style="background:rgba(255,255,255,.2);color:#fff;font-size:.72rem;" onclick="clearAllFiles()">Clear All</button>' +
                '</div>';
            files.forEach((file, i) => {
                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                const icon = getFileIcon(file.name);
                const isLast = i === files.length - 1;
                html += '<div class="d-flex align-items-center gap-2 px-3 py-2" style="background:#EAF1FB;' + (isLast ? '' : 'border-bottom:1px solid #c8ddf5;') + '">' +
                    '  <i class="bi ' + icon + '" style="font-size:1rem;flex-shrink:0;"></i>' +
                    '  <div class="flex-grow-1 overflow-hidden">' +
                    '    <div class="fw-semibold text-truncate" style="font-size:.8rem;color:#092C4C;">' + file.name + '</div>' +
                    '    <div style="font-size:.72rem;color:#6b9bc8;">' + sizeMB + ' MB</div>' +
                    '  </div>' +
                    '  <button type="button" class="btn btn-sm p-0" style="width:22px;height:22px;line-height:1;background:rgba(74,144,217,.15);color:#4A90D9;border-radius:50%;flex-shrink:0;" onclick="removeFile(' + i + ')" title="Remove">' +
                    '    <i class="bi bi-x" style="font-size:.8rem;"></i>' +
                    '  </button>' +
                    '</div>';
            });
            html += '</div>';
            fileList.innerHTML = html;
        }

        window.removeFile = function(index) {
            const newDT = new DataTransfer();
            Array.from(allFiles.files).forEach((f, i) => {
                if (i !== index) newDT.items.add(f);
            });
            allFiles = newDT;
            fileInput.files = allFiles.files;
            renderFileList();
        };

        window.clearAllFiles = function() {
            allFiles = new DataTransfer();
            fileInput.files = allFiles.files;
            fileList.innerHTML = '';
        };

        fileInput.addEventListener('change', function() {
            Array.from(this.files).forEach(file => allFiles.items.add(file));
            fileInput.files = allFiles.files;
            renderFileList();
        });
    });
</script>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>