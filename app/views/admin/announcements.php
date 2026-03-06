<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#092C4C;">Announcements</h4>
        <p class="text-muted mb-0 small">Create and manage system-wide announcements.</p>
    </div>
    <button class="btn text-white" style="background-color: #092C4C;" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
        <i class="bi bi-plus-lg me-1"></i> New Announcement
    </button>
</div>

<!-- Flash Messages -->
<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['flash_success']);
                                                unset($_SESSION['flash_success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($_SESSION['flash_error']);
                                                        unset($_SESSION['flash_error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Announcements Table -->
<div class="card dash-card" style="border-radius: 8px; overflow: visible;">
    <!-- Search and Filter Bar -->
    <div class="p-3 d-flex align-items-center flex-wrap gap-2" style="background:#f8f9fa;border-bottom:1px solid #e9ecef;">
        <!-- Search -->
        <div class="input-group input-group-sm" style="max-width:220px;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" id="announcementSearch" class="form-control border-start-0 ps-0" placeholder="Search announcement...">
        </div>
        <!-- Status Filter -->
        <select id="filterStatus" class="form-select form-select-sm" style="width:auto;min-width:120px;">
            <option value="">All Status</option>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>
        <!-- Reset -->
        <button id="resetFilters" class="btn btn-sm btn-outline-secondary">Reset</button>
        <!-- Results count -->
        <span id="announcementCount" class="ms-auto small text-muted"></span>
    </div>

    <!-- Table -->
    <div class="card-body p-0">
        <div class="table-responsive" style="overflow-x: auto; overflow-y: visible;">
            <table class="table table-hover align-middle mb-0" id="announcementsTable">
                <thead style="background-color: #f8f9fa;">
                    <tr>
                        <th class="ps-4" style="background:#f8f9fa;">Title</th>
                        <th style="background:#f8f9fa;">Effectivity</th>
                        <th style="background:#f8f9fa;">Expiry</th>
                        <th style="background:#f8f9fa;">Created By</th>
                        <th style="background:#f8f9fa;">Status</th>
                        <th class="text-center" style="background:#f8f9fa;">Actions</th>
                    </tr>
                </thead>
                <tbody id="announcementsBody">
                    <?php if (empty($announcements)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No announcements found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($announcements as $a): ?>
                            <tr
                                data-title="<?php echo strtolower(htmlspecialchars($a['title'])); ?>"
                                data-description="<?php echo strtolower(htmlspecialchars($a['description'])); ?>"
                                data-status="<?php echo $a['is_active'] ? '1' : '0'; ?>">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($a['image_path'])): ?>
                                            <img src="<?php echo env('APP_URL'); ?>/public/<?php echo htmlspecialchars($a['image_path']); ?>" alt="" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px; background-color: rgba(9,44,76,0.1);">
                                                <i class="bi bi-megaphone" style="color: #092C4C;"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <span class="fw-semibold small"><?php echo htmlspecialchars($a['title']); ?></span>
                                            <br><small class="text-muted"><?php echo htmlspecialchars(mb_strimwidth($a['description'], 0, 60, '...')); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="small"><?php echo date('M d, Y h:i A', strtotime($a['effectivity_date'])); ?></span></td>
                                <td><span class="small text-muted"><?php echo $a['expiry_date'] ? date('M d, Y h:i A', strtotime($a['expiry_date'])) : '—'; ?></span></td>
                                <td><span class="small text-muted"><?php echo htmlspecialchars(($a['firstname'] ?? '') . ' ' . ($a['lastname'] ?? '')); ?></span></td>
                                <td>
                                    <?php if ($a['is_active']): ?>
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
                                                <a class="dropdown-item btn-view-details" href="#"
                                                    data-id="<?php echo $a['announcement_id']; ?>"
                                                    data-title="<?php echo htmlspecialchars($a['title']); ?>"
                                                    data-description="<?php echo htmlspecialchars($a['description']); ?>"
                                                    data-image="<?php echo htmlspecialchars($a['image_path'] ?? ''); ?>"
                                                    data-effectivity="<?php echo date('M d, Y h:i A', strtotime($a['effectivity_date'])); ?>"
                                                    data-expiry="<?php echo $a['expiry_date'] ? date('M d, Y h:i A', strtotime($a['expiry_date'])) : '—'; ?>"
                                                    data-creator="<?php echo htmlspecialchars(($a['firstname'] ?? '') . ' ' . ($a['lastname'] ?? '')); ?>"
                                                    data-status="<?php echo $a['is_active'] ? 'Active' : 'Inactive'; ?>"
                                                    onclick="return false;">
                                                    <i class="bi bi-eye me-2"></i>View Details
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item btn-edit-announcement" href="#"
                                                    data-id="<?php echo $a['announcement_id']; ?>"
                                                    data-title="<?php echo htmlspecialchars($a['title']); ?>"
                                                    data-description="<?php echo htmlspecialchars($a['description']); ?>"
                                                    data-image="<?php echo htmlspecialchars($a['image_path'] ?? ''); ?>"
                                                    data-effectivity="<?php echo date('Y-m-d\TH:i', strtotime($a['effectivity_date'])); ?>"
                                                    data-expiry="<?php echo $a['expiry_date'] ? date('Y-m-d\TH:i', strtotime($a['expiry_date'])) : ''; ?>"
                                                    data-active="<?php echo $a['is_active']; ?>"
                                                    onclick="return false;">
                                                    <i class="bi bi-pencil me-2"></i>Edit
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <a class="dropdown-item <?php echo $a['is_active'] ? 'text-warning' : 'text-success'; ?>"
                                                    href="<?php echo env('APP_URL'); ?>/admin/toggleannouncement/<?php echo $a['announcement_id']; ?>"
                                                    onclick="return confirm('Are you sure you want to <?php echo $a['is_active'] ? 'deactivate' : 'activate'; ?> this announcement?');">
                                                    <i class="bi bi-<?php echo $a['is_active'] ? 'eye-slash' : 'eye'; ?> me-2"></i><?php echo $a['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger"
                                                    href="<?php echo env('APP_URL'); ?>/admin/deleteannouncement/<?php echo $a['announcement_id']; ?>"
                                                    onclick="return confirm('Are you sure you want to delete this announcement? This cannot be undone.');">
                                                    <i class="bi bi-trash me-2"></i>Delete
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- No results row (hidden initially) -->
    <div id="noResults" class="text-center text-muted py-4" style="display:none!important;">No announcements match your filters.</div>
</div>

<!-- ==================== VIEW DETAILS MODAL ==================== -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 12px;">
            <div class="modal-header border-bottom" style="background-color: #092C4C; color: #fff; border-radius: 12px 12px 0 0;">
                <h6 class="modal-title fw-bold" id="viewDetailsModalLabel"><i class="bi bi-eye me-2"></i>Announcement Details</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- Image (if exists) -->
                    <div class="col-12" id="viewImageContainer" style="display: none;">
                        <img id="viewImage" src="" alt="Announcement Image" class="w-100 rounded" style="max-height: 300px; object-fit: cover;">
                    </div>

                    <!-- Title -->
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted mb-1">Title</label>
                        <p class="mb-0 fw-semibold" style="color:#092C4C; font-size:1.1rem;" id="viewTitle"></p>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted mb-1">Description</label>
                        <p class="mb-0" style="color:#495057; white-space: pre-wrap;" id="viewDescription"></p>
                    </div>

                    <!-- Dates and Info -->
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Effectivity Date</label>
                        <p class="mb-0" id="viewEffectivity"></p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Expiry Date</label>
                        <p class="mb-0" id="viewExpiry"></p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Created By</label>
                        <p class="mb-0" id="viewCreator"></p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Status</label>
                        <p class="mb-0" id="viewStatusBadge"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== ADD ANNOUNCEMENT MODAL ==================== -->
<div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="addAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 12px;">
            <form action="<?php echo env('APP_URL'); ?>/admin/createannouncement" method="POST" enctype="multipart/form-data">
                <div class="modal-header border-bottom" style="background-color: #092C4C; color: #fff; border-radius: 12px 12px 0 0;">
                    <h6 class="modal-title fw-bold" id="addAnnouncementModalLabel"><i class="bi bi-megaphone me-2"></i>New Announcement</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Image <small class="text-muted">(optional)</small></label>
                            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                            <small class="text-muted">Max 5MB. Allowed: JPG, PNG, GIF, WEBP.</small>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="addIsActive" checked>
                                <label class="form-check-label small" for="addIsActive">Active immediately</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Effectivity Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="effectivity_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Expiry Date <small class="text-muted">(optional)</small></label>
                            <input type="datetime-local" name="expiry_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white" style="background-color: #092C4C;">
                        <i class="bi bi-plus-lg me-1"></i> Create Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================== EDIT ANNOUNCEMENT MODAL ==================== -->
<div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-labelledby="editAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 12px;">
            <form id="editAnnouncementForm" method="POST" enctype="multipart/form-data">
                <div class="modal-header border-bottom" style="background-color: #092C4C; color: #fff; border-radius: 12px 12px 0 0;">
                    <h6 class="modal-title fw-bold" id="editAnnouncementModalLabel"><i class="bi bi-pencil-square me-2"></i>Edit Announcement</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="editTitle" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="editDescription" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Image <small class="text-muted">(optional)</small></label>
                            <div id="editCurrentImage" class="mb-2" style="display: none;">
                                <small class="text-muted">Current image:</small>
                                <img id="editImagePreview" src="" alt="" class="d-block rounded mt-1" style="max-width: 120px; max-height: 80px; object-fit: cover;">
                            </div>
                            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                            <small class="text-muted">Leave empty to keep current image. Max 5MB.</small>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="editIsActive">
                                <label class="form-check-label small" for="editIsActive">Active</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Effectivity Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="effectivity_date" id="editEffectivity" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Expiry Date <small class="text-muted">(optional)</small></label>
                            <input type="datetime-local" name="expiry_date" id="editExpiry" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white" style="background-color: #092C4C;">
                        <i class="bi bi-check-lg me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Announcements Page JS -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var appUrl = '<?php echo env("APP_URL"); ?>';

        // ==================== FILTERING FUNCTIONALITY ====================
        var searchInput = document.getElementById('announcementSearch');
        var filterStatus = document.getElementById('filterStatus');
        var resetBtn = document.getElementById('resetFilters');
        var tbody = document.getElementById('announcementsBody');
        var countEl = document.getElementById('announcementCount');
        var noResults = document.getElementById('noResults');

        function applyFilters() {
            var search = searchInput.value.toLowerCase().trim();
            var status = filterStatus.value;
            var rows = tbody.querySelectorAll('tr[data-title]');
            var visible = 0;

            rows.forEach(function(row) {
                var matchSearch = !search || row.dataset.title.includes(search) || row.dataset.description.includes(search);
                var matchStatus = !status || row.dataset.status === status;
                var show = matchSearch && matchStatus;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            countEl.textContent = visible + ' announcement' + (visible !== 1 ? 's' : '') + ' found';
            noResults.style.setProperty('display', visible === 0 ? 'block' : 'none', 'important');
        }

        searchInput.addEventListener('input', applyFilters);
        filterStatus.addEventListener('change', applyFilters);

        resetBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterStatus.value = '';
            applyFilters();
        });

        applyFilters();

        // ==================== VIEW DETAILS MODAL ====================
        document.querySelectorAll('.btn-view-details').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('viewTitle').textContent = this.dataset.title;
                document.getElementById('viewDescription').textContent = this.dataset.description;
                document.getElementById('viewEffectivity').textContent = this.dataset.effectivity;
                document.getElementById('viewExpiry').textContent = this.dataset.expiry;
                document.getElementById('viewCreator').textContent = this.dataset.creator;

                // Status badge
                var statusBadgeHtml = this.dataset.status === 'Active' ?
                    '<span class="badge rounded-small bg-success bg-opacity-10 text-success">Active</span>' :
                    '<span class="badge rounded-small bg-danger bg-opacity-10 text-danger">Inactive</span>';
                document.getElementById('viewStatusBadge').innerHTML = statusBadgeHtml;

                // Image
                var imageContainer = document.getElementById('viewImageContainer');
                var image = document.getElementById('viewImage');
                if (this.dataset.image) {
                    image.src = appUrl + '/public/' + this.dataset.image;
                    imageContainer.style.display = 'block';
                } else {
                    imageContainer.style.display = 'none';
                }

                new bootstrap.Modal(document.getElementById('viewDetailsModal')).show();
            });
        });

        // ==================== EDIT ANNOUNCEMENT MODAL ====================
        document.querySelectorAll('.btn-edit-announcement').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('editAnnouncementForm').action = appUrl + '/admin/editannouncement/' + this.dataset.id;
                document.getElementById('editTitle').value = this.dataset.title;
                document.getElementById('editDescription').value = this.dataset.description;
                document.getElementById('editEffectivity').value = this.dataset.effectivity;
                document.getElementById('editExpiry').value = this.dataset.expiry;
                document.getElementById('editIsActive').checked = this.dataset.active === '1';

                // Show current image preview if exists
                var imageContainer = document.getElementById('editCurrentImage');
                var imagePreview = document.getElementById('editImagePreview');
                if (this.dataset.image) {
                    imagePreview.src = appUrl + '/public/' + this.dataset.image;
                    imageContainer.style.display = 'block';
                } else {
                    imageContainer.style.display = 'none';
                }

                new bootstrap.Modal(document.getElementById('editAnnouncementModal')).show();
            });
        });
    });
</script>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>