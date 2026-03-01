<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<div class="page-header">
    <h4>Submit Report</h4>
    <p>Upload a report document for your office.</p>
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
                <form action="<?php echo env('APP_URL'); ?>/officer/upload" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #092C4C;">Office</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($office['office_name']); ?>" disabled>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="report_type_id" class="form-label fw-semibold" style="color: #092C4C;">Report Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="report_type_id" name="report_type_id" required>
                                <option value="">-- Select Report Type --</option>
                                <?php foreach ($reportTypes as $rt): ?>
                                    <option value="<?php echo $rt['report_type_id']; ?>">
                                        <?php echo htmlspecialchars($rt['report_code'] . ' - ' . $rt['report_title']); ?>
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
                                    <option value="<?php echo $p['period_id']; ?>">
                                        <?php echo $periodHelper->getMonthName($p['period_month']) . ' ' . $p['period_year']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="report_file" class="form-label fw-semibold" style="color: #092C4C;">Upload File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="report_file" name="report_file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        <div class="form-text">Accepted formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG. Max size: 10MB.</div>
                    </div>

                    <div class="mb-4">
                        <label for="remarks" class="form-label fw-semibold" style="color: #092C4C;">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Optional remarks..."></textarea>
                    </div>

                    <button type="submit" class="btn text-white fw-semibold" style="background-color: #F3AF0E;">
                        <i class="bi bi-upload me-1"></i> Submit Report
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card dash-card p-4">
            <h6 class="fw-bold mb-3" style="color: #092C4C;"><i class="bi bi-info-circle me-1"></i> Guidelines</h6>
            <ul class="small text-muted ps-3">
                <li class="mb-2">Select the correct report type and period before uploading.</li>
                <li class="mb-2">Ensure the file is in the correct format and does not exceed 10MB.</li>
                <li class="mb-2">Only one submission per report type per period is allowed.</li>
                <li class="mb-2">Reports submitted after the deadline will be marked as <span class="badge bg-warning text-dark">LATE</span>.</li>
            </ul>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>