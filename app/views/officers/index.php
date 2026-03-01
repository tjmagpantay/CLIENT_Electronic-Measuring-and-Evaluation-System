<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<!-- Row 1: Greeting -->
<div class="mb-4">
    <h4 class="fw-bold mb-1" style="color: #092C4C;">Welcome back, <?php echo htmlspecialchars($_SESSION['firstname']); ?>!</h4>
    <p class="text-muted mb-0 small">Here's an overview of your office submissions and announcements.</p>
</div>

<!-- Row 2: Announcements Section -->
<div class="mb-4">
    <h6 class="fw-bold mb-3" style="color: #092C4C;"><i class="bi bi-megaphone me-2"></i>Announcements</h6>
    <?php if (empty($announcements)): ?>
        <div class="card dash-card p-4">
            <p class="text-muted small mb-0 text-center">No announcements at this time.</p>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($announcements as $a): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card dash-card overflow-hidden h-100" style="border: none;">
                        <!-- Image or gradient background -->
                        <div class="position-relative" style="height: 160px; <?php echo !empty($a['image_path']) ? 'background: url(' . env('APP_URL') . '/public/' . htmlspecialchars($a['image_path']) . ') center/cover no-repeat;' : 'background: linear-gradient(135deg, #092C4C 0%, #1a4a7a 100%);'; ?>">
                            <div class="position-absolute bottom-0 start-0 end-0 p-3" style="background: linear-gradient(transparent, rgba(0,0,0,0.7));">
                                <h6 class="text-white fw-bold mb-0 small"><?php echo htmlspecialchars($a['title']); ?></h6>
                                <small class="text-white-50"><?php echo date('M d, Y', strtotime($a['effectivity_date'])); ?></small>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <p class="small text-muted mb-2"><?php echo htmlspecialchars(mb_strimwidth($a['description'], 0, 100, '...')); ?></p>
                            <button class="btn btn-sm btn-outline-primary btn-view-announcement"
                                data-title="<?php echo htmlspecialchars($a['title']); ?>"
                                data-description="<?php echo htmlspecialchars($a['description']); ?>"
                                data-date="<?php echo date('F d, Y h:i A', strtotime($a['effectivity_date'])); ?>"
                                data-image="<?php echo !empty($a['image_path']) ? env('APP_URL') . '/public/' . htmlspecialchars($a['image_path']) : ''; ?>">
                                Read More
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Row 3: How to Submit Your Reports -->
<div class="mb-4">
    <h6 class="fw-bold mb-3" style="color: #092C4C;"><i class="bi bi-question-circle me-2"></i>How to Submit Your Reports and Documents</h6>
    <div class="row g-3">
        <!-- Step 1 -->
        <div class="col-md-4">
            <div class="card dash-card p-4 h-100 text-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 56px; height: 56px; background-color: #E3EBF3;">
                    <i class="bi bi-file-earmark-text fs-4" style="color: #092C4C;"></i>
                </div>
                <div class="badge rounded-pill mb-2 mx-auto" style="background-color: #092C4C; width: fit-content;">Step 1</div>
                <h6 class="fw-bold" style="color: #092C4C;">Select Report Type</h6>
                <p class="text-muted small mb-0">Choose the report type and reporting period from the available options in the Submit Report page.</p>
            </div>
        </div>
        <!-- Step 2 -->
        <div class="col-md-4">
            <div class="card dash-card p-4 h-100 text-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 56px; height: 56px; background-color: #F5E7CE;">
                    <i class="bi bi-cloud-arrow-up fs-4" style="color: #F3AF0E;"></i>
                </div>
                <div class="badge rounded-pill mb-2 mx-auto" style="background-color: #F3AF0E; color: #092C4C; width: fit-content;">Step 2</div>
                <h6 class="fw-bold" style="color: #092C4C;">Upload Your File</h6>
                <p class="text-muted small mb-0">Attach your report document (PDF, DOC, XLS, or images). Maximum file size is 10MB.</p>
            </div>
        </div>
        <!-- Step 3 -->
        <div class="col-md-4">
            <div class="card dash-card p-4 h-100 text-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 56px; height: 56px; background-color: #FFE2E2;">
                    <i class="bi bi-send-check fs-4" style="color: #198754;"></i>
                </div>
                <div class="badge rounded-pill mb-2 mx-auto bg-success" style="width: fit-content;">Step 3</div>
                <h6 class="fw-bold" style="color: #092C4C;">Submit & Track</h6>
                <p class="text-muted small mb-0">Submit your report and track its status under My Submissions. Submit before the deadline to avoid late status.</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?php echo env('APP_URL'); ?>/officer/submit" class="btn text-white" style="background-color: #092C4C;">
                <i class="bi bi-upload me-1"></i> Submit Report
            </a>
            <a href="<?php echo env('APP_URL'); ?>/officer/submissions" class="btn" style="background-color: #F3AF0E; color: #092C4C;">
                <i class="bi bi-clipboard-check me-1"></i> View My Submissions
            </a>
        </div>
    </div>
</div>

<!-- Announcement Detail Modal -->
<div class="modal fade" id="announcementDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 12px; overflow: hidden;">
            <div id="announcementModalImage" style="display: none; height: 200px; background-size: cover; background-position: center;"></div>
            <div class="modal-header border-bottom" style="background-color: #092C4C; color: #fff;">
                <h6 class="modal-title fw-bold" id="announcementModalTitle"></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3" id="announcementModalDate"></p>
                <div id="announcementModalDescription" style="white-space: pre-wrap;"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-view-announcement').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('announcementModalTitle').textContent = this.dataset.title;
            document.getElementById('announcementModalDescription').textContent = this.dataset.description;
            document.getElementById('announcementModalDate').textContent = this.dataset.date;

            var imgDiv = document.getElementById('announcementModalImage');
            if (this.dataset.image) {
                imgDiv.style.backgroundImage = 'url(' + this.dataset.image + ')';
                imgDiv.style.display = 'block';
            } else {
                imgDiv.style.display = 'none';
            }

            new bootstrap.Modal(document.getElementById('announcementDetailModal')).show();
        });
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>
