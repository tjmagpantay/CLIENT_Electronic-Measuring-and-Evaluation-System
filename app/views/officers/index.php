<?php
// Determine urgency level for an announcement based on expiry_date
function announcementUrgency($expiry)
{
    if (empty($expiry)) return 'normal'; // no expiry = not urgent
    $now  = time();
    $diff = strtotime($expiry) - $now;
    if ($diff <= 0)             return 'expired';
    if ($diff <= 3 * 86400)     return 'urgent';
    if ($diff <= 7 * 86400)     return 'warning';
    return 'normal';
}
?>

<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<!-- Greeting (full-width, above both columns) -->
<div class="mb-4">
    <h4 class="fw-bold mb-1" style="color: #092C4C;">
        Hello, <?php echo htmlspecialchars($_SESSION['firstname'] . ' ' . ($_SESSION['lastname'] ?? '')); ?>
    </h4>
    <p class="text-muted mb-0 small">
        <?php if (!empty($activePeriod)): ?>
            Reporting period: <?php echo date('F Y', mktime(0, 0, 0, $activePeriod['period_month'], 1, $activePeriod['period_year'])); ?>
            <?php if (!empty($activePeriod['deadline'])): ?>
                &mdash; Deadline: <strong><?php echo date('M d, Y', strtotime($activePeriod['deadline'])); ?></strong>
            <?php endif; ?>
        <?php else: ?>
            Here's an overview of your office submissions and announcements.
        <?php endif; ?>
    </p>
</div>

<!-- ===== MAIN DASHBOARD LAYOUT ===== -->
<div class="row g-4">

    <!-- ===================== LEFT SECTION (col-lg-8) ===================== -->
    <div class="col-lg-8">

        <!-- Stat Cards Row -->
        <div class="row g-3 mb-4">

            <!-- Non Compliant -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-3 p-4"
                    style="background: linear-gradient(135deg, #E43535 0%, #b71c1c 100%);">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="fw-semibold text-uppercase" style="color:rgba(255,255,255,.85);font-size:.72rem;letter-spacing:.6px;">Non Compliant</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width:38px;height:38px;background:rgba(255,255,255,.2);">
                            <i class="bi bi-person-x" style="color:#fff;font-size:1rem;"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1" style="color:#fff;"><?php echo $pendingCount; ?></h2>
                    <p class="mb-0" style="color:rgba(255,255,255,.75);font-size:.78rem;">Reports not yet submitted to the system.</p>
                </div>
            </div>

            <!-- Late Submissions -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-3 p-4"
                    style="background: linear-gradient(135deg, #FEC53D 0%, #e6a800 100%);">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="fw-semibold text-uppercase" style="color:rgba(0,0,0,.6);font-size:.72rem;letter-spacing:.6px;">Late Submissions</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width:38px;height:38px;background:rgba(0,0,0,.1);">
                            <i class="bi bi-clock-history" style="color:rgba(0,0,0,.65);font-size:1rem;"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1" style="color:#092C4C;"><?php echo $overdueCount; ?></h2>
                    <p class="mb-0" style="color:rgba(0,0,0,.55);font-size:.78rem;">Submitted past the designated deadline.</p>
                </div>
            </div>

            <!-- Submitted On Time -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-3 p-4"
                    style="background: linear-gradient(135deg, #092C4C 0%, #1a4a7a 100%);">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="fw-semibold text-uppercase" style="color:rgba(255,255,255,.85);font-size:.72rem;letter-spacing:.6px;">Submitted On Time</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width:38px;height:38px;background:rgba(255,255,255,.2);">
                            <i class="bi bi-graph-up-arrow" style="color:#fff;font-size:1rem;"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1" style="color:#fff;"><?php echo $onTimeCount; ?></h2>
                    <p class="mb-0" style="color:rgba(255,255,255,.75);font-size:.78rem;">Successfully submitted before the deadline.</p>
                </div>
            </div>

        </div><!-- /Stat Cards Row -->

        <!-- Office Compliance Status Table -->
        <div class="border-0 shadow-sm" style="border-radius:16px;overflow:hidden;background:#fff;">
            <div class="px-4 pt-4 pb-2">
                <h6 class="fw-bold mb-0" style="color:#092C4C;">Office Compliance Status</h6>
                <p class="text-muted mb-0" style="font-size:.78rem;">List of report submissions and their current status.</p>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:.85rem;">
                    <thead>
                        <tr style="background:#092C4C;color:#fff;">
                            <th class="px-4 py-3 fw-semibold border-0">Report</th>
                            <th class="py-3 fw-semibold border-0">Status</th>
                            <th class="py-3 fw-semibold border-0">% Completion</th>
                            <th class="py-3 fw-semibold border-0">Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentSubmissions)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4 px-4">No submissions found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentSubmissions as $sub):
                                $status = $sub['submission_status'] ?? 'NO_SUBMISSION';
                                $badgeStyle = '';
                                switch ($status) {
                                    case 'ON_TIME':
                                        $badgeClass = 'bg-success';
                                        $badgeLabel = 'Completed';
                                        $pct        = 100;
                                        $barColor   = '#43A047';
                                        break;
                                    case 'LATE':
                                        $badgeClass = 'text-white';
                                        $badgeStyle = 'background:#FB8C00;';
                                        $badgeLabel = 'Late';
                                        $pct        = 100;
                                        $barColor   = '#FB8C00';
                                        break;
                                    case 'PENDING':
                                        $badgeClass = 'text-dark';
                                        $badgeStyle = 'background:#FDD835;';
                                        $badgeLabel = 'Ongoing';
                                        $pct        = 60;
                                        $barColor   = '#FDD835';
                                        break;
                                    default: // NO_SUBMISSION
                                        $badgeClass = 'bg-danger';
                                        $badgeLabel = 'Non Compliant';
                                        $pct        = 0;
                                        $barColor   = '#E53935';
                                }
                            ?>
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="fw-semibold" style="color:#092C4C;"><?php echo htmlspecialchars($sub['report_title']); ?></div>
                                        <div class="text-muted" style="font-size:.75rem;"><?php echo htmlspecialchars($sub['report_code']); ?></div>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge rounded-pill px-3 py-2 <?php echo $badgeClass; ?>" style="font-size:.75rem;<?php echo $badgeStyle; ?>">
                                            <?php echo $badgeLabel; ?>
                                        </span>
                                    </td>
                                    <td class="py-3" style="min-width:130px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="flex-grow-1" style="height:6px;background:#E0E0E0;border-radius:4px;overflow:hidden;">
                                                <div style="width:<?php echo $pct; ?>%;height:100%;background:<?php echo $barColor; ?>;border-radius:4px;"></div>
                                            </div>
                                            <span class="text-muted" style="font-size:.75rem;white-space:nowrap;"><?php echo $pct; ?>%</span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-muted">
                                        <?php echo !empty($sub['deadline']) ? date('M d, Y', strtotime($sub['deadline'])) : '—'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div><!-- /Compliance Table -->

    </div><!-- /LEFT SECTION -->

    <!-- ===================== RIGHT SECTION (col-lg-4) ===================== -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-0">
                <div class="px-4 pt-4 pb-2">
                    <h6 class="fw-bold mb-0" style="color:#092C4C;">Recent Announcements</h6>
                    <p class="text-muted mb-0" style="font-size:.78rem;">List of recent announcements by LGU</p>
                </div>

                <?php if (empty($announcements)): ?>
                    <div class="text-center text-muted py-5 px-4">
                        <i class="bi bi-megaphone" style="font-size:2.5rem;opacity:.3;"></i>
                        <p class="mt-2 small">No announcements at this time.</p>
                    </div>
                <?php else: ?>
                    <div class="px-3 pb-3" style="max-height:520px;overflow-y:auto;">
                        <?php foreach ($announcements as $a):
                            $urgency = announcementUrgency($a['expiry_date'] ?? null);
                            switch ($urgency) {
                                case 'urgent':
                                    $dotColor = '#E53935';
                                    $dotTitle = 'Urgent';
                                    break;
                                case 'warning':
                                    $dotColor = '#FB8C00';
                                    $dotTitle = 'Expiring soon';
                                    break;
                                case 'expired':
                                    $dotColor = '#9E9E9E';
                                    $dotTitle = 'Expired';
                                    break;
                                default:
                                    $dotColor = '#43A047';
                                    $dotTitle = 'Active';
                                    break;
                            }
                        ?>
                            <div class="border rounded-3 p-3 mb-2" style="background:#FAFAFA;">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <h6 class="fw-semibold mb-1" style="color:#092C4C;font-size:.85rem;flex:1;">
                                        <?php echo htmlspecialchars($a['title']); ?>
                                    </h6>
                                    <!-- Urgency indicator dot -->
                                    <span title="<?php echo $dotTitle; ?>"
                                        style="width:10px;height:10px;min-width:10px;border-radius:50%;background:<?php echo $dotColor; ?>;margin-top:4px;"></span>
                                </div>
                                <p class="text-muted mb-2" style="font-size:.78rem;line-height:1.5;">
                                    <?php echo htmlspecialchars(mb_strimwidth($a['description'], 0, 100, '...')); ?>
                                </p>
                                <a href="#" class="btn-view-announcement text-decoration-none fw-semibold"
                                    style="font-size:.78rem;color:#1565C0;"
                                    data-title="<?php echo htmlspecialchars($a['title']); ?>"
                                    data-description="<?php echo htmlspecialchars($a['description']); ?>"
                                    data-date="<?php echo date('F d, Y h:i A', strtotime($a['effectivity_date'])); ?>"
                                    data-image="<?php echo !empty($a['image_path']) ? env('APP_URL') . '/public/' . htmlspecialchars($a['image_path']) : ''; ?>">
                                    View Details <i class="bi bi-arrow-right" style="font-size:.72rem;"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div><!-- /RIGHT SECTION -->

</div><!-- /MAIN DASHBOARD LAYOUT -->

<!-- Announcement Detail Modal -->
<div class="modal fade" id="announcementDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <!-- Header on Top -->
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #092C4C 0%, #1a4a7a 100%); color: #fff; padding: 1.5rem;">
                <h6 class="modal-title fw-bold mb-0" id="announcementModalTitle"></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Image Below Header -->
            <div id="announcementModalImage" style="display: none; height: 300px; background-size: cover; background-position: center;"></div>

            <!-- Description -->
            <!-- Description -->
            <div class="modal-body p-4">
                <div class="mb-3">
                    <span class="badge rounded-pill"
                        style="border: 1px solid #b6babd; color: #092C4C; padding: 0.5rem 1rem; font-size: 0.85rem;"
                        id="announcementModalDate"></span>
                </div>

                <div id="announcementModalDescription"
                    class="text-muted mb-0 small"
                    style="white-space: pre-wrap; line-height: 1.8;">
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Auto-Show Announcement on Login -->
<?php if (!empty($announcements)): ?>
    <!-- Show the first active announcement automatically -->
    <?php $firstAnnouncement = $announcements[0]; ?>
    <div class="modal fade" id="autoAnnouncementModal" tabindex="-1" aria-hidden="true" data-announcement-id="<?php echo $firstAnnouncement['announcement_id']; ?>">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
                <!-- Header on Top -->
                <div class="modal-header border-0" style="background: linear-gradient(135deg, #092C4C 0%, #1a4a7a 100%); color: #fff; padding: 1.5rem;">
                    <h5 class="modal-title fw-bold mb-0"><?php echo htmlspecialchars($firstAnnouncement['title']); ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Image Carousel Below Header -->
                <?php if (!empty($firstAnnouncement['image_path'])): ?>
                    <?php
                    // Support multiple images separated by comma
                    $images = array_filter(array_map('trim', explode(',', $firstAnnouncement['image_path'])));
                    ?>
                    <?php if (count($images) > 1): ?>
                        <!-- Multiple images - Carousel -->
                        <div id="announcementCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                <?php foreach ($images as $idx => $img): ?>
                                    <button type="button" data-bs-target="#announcementCarousel" data-bs-slide-to="<?php echo $idx; ?>" <?php echo $idx === 0 ? 'class="active"' : ''; ?>></button>
                                <?php endforeach; ?>
                            </div>
                            <div class="carousel-inner">
                                <?php foreach ($images as $idx => $img): ?>
                                    <div class="carousel-item <?php echo $idx === 0 ? 'active' : ''; ?>">
                                        <img src="<?php echo env('APP_URL'); ?>/public/<?php echo htmlspecialchars($img); ?>" class="d-block w-100" style="height: 350px; object-fit: cover;" alt="Announcement Image">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#announcementCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#announcementCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        </div>
                    <?php else: ?>
                        <!-- Single image -->
                        <div style="height: 350px; background: url('<?php echo env('APP_URL'); ?>/public/<?php echo htmlspecialchars($images[0]); ?>') center/cover no-repeat;"></div>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- No image - gradient background -->
                    <div style="height: 250px; background: linear-gradient(135deg, #E3EBF3 0%, #C4D7E8 100%); display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-megaphone" style="font-size: 5rem; color: #092C4C; opacity: 0.3;"></i>
                    </div>
                <?php endif; ?>

                <!-- Description Below Image -->
                <div class="modal-body p-4">

                    <div class="mb-3 d-flex gap-2 flex-wrap">

                        <!-- Date Badge -->
                        <span class="badge rounded-s"
                            style="border: 1px solid #b6babd; color: #092C4C; padding: 0.5rem 1rem; font-size: 0.85rem; font-weight: 500;">
                            <i class="bi bi-calendar3 me-1"></i>
                            <?php echo date('F d, Y', strtotime($firstAnnouncement['effectivity_date'])); ?>
                        </span>

                        <!-- Time Badge -->
                        <span class="badge rounded-s"
                            style="border: 1px solid #b6babd; color: #092C4C; padding: 0.5rem 1rem; font-size: 0.85rem; font-weight: 500;">
                            <i class="bi bi-clock me-1"></i>
                            <?php echo date('h:i A', strtotime($firstAnnouncement['effectivity_date'])); ?>
                        </span>

                    </div>

                    <div style="line-height: 1.8; color: #495057; font-weight: 300;">
                        <?php echo htmlspecialchars($firstAnnouncement['description']); ?>
                    </div>

                </div>

            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle announcement detail modal (View Details links)
        document.querySelectorAll('.btn-view-announcement').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('announcementModalTitle').textContent = this.dataset.title;
                document.getElementById('announcementModalDescription').textContent = this.dataset.description;

                // Format date as badge
                document.getElementById('announcementModalDate').innerHTML = '<i class="bi bi-calendar3 me-1"></i>' + this.dataset.date;

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

        // Auto-show announcement on login (shows every time until it expires)
        <?php if (!empty($announcements)): ?>
            var autoModal = document.getElementById('autoAnnouncementModal');
            if (autoModal) {
                var announcementId = autoModal.dataset.announcementId;
                var today = new Date().toDateString();
                var viewedKey = 'announcement_viewed_' + announcementId + '_' + today;

                // Check if user has seen this announcement today
                if (!sessionStorage.getItem(viewedKey)) {
                    var modal = new bootstrap.Modal(autoModal);
                    modal.show();

                    // Mark as viewed for this session
                    sessionStorage.setItem(viewedKey, 'true');
                }
            }
        <?php endif; ?>
    });
</script>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>