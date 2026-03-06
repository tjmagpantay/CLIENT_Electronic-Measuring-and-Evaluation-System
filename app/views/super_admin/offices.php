<?php require_once __DIR__ . '/../layouts/dashboard_header.php'; ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#092C4C;">Offices</h4>
        <p class="text-muted mb-0 small">Manage LGU offices - Provincial, Municipal, and City levels.</p>
    </div>
    <a href="<?php echo env('APP_URL'); ?>/superadmin/createoffice" class="btn text-white" style="background-color:#092C4C;">
        <i class="bi bi-plus-lg me-1"></i> Add Office
    </a>
</div>

<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['flash_success']);
                                                unset($_SESSION['flash_success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card dash-card overflow-hidden">
    <!-- Filter & Search bar inside card -->
    <div class="p-3 d-flex align-items-center flex-wrap gap-2" style="background:#f8f9fa;border-bottom:1px solid #e9ecef;">
        <!-- Search -->
        <div class="input-group input-group-sm" style="max-width:220px;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" id="officeSearch" class="form-control border-start-0 ps-0" placeholder="Search office...">
        </div>
        <!-- Type -->
        <select id="filterType" class="form-select form-select-sm" style="width:auto;min-width:140px;">
            <option value="">All Types</option>
            <option value="PROVINCE">Province</option>
            <option value="CITY">City</option>
            <option value="MUNICIPALITY">Municipality</option>
        </select>
        <!-- Cluster -->
        <select id="filterCluster" class="form-select form-select-sm" style="width:auto;min-width:130px;">
            <option value="">All Clusters</option>
            <?php
            $uniqueClusters = array_unique(array_filter(array_column($offices, 'cluster')));
            sort($uniqueClusters);
            foreach ($uniqueClusters as $cl): ?>
                <option value="<?php echo htmlspecialchars($cl); ?>"><?php echo htmlspecialchars($cl); ?></option>
            <?php endforeach; ?>
        </select>
        <!-- Status -->
        <select id="filterStatus" class="form-select form-select-sm" style="width:auto;min-width:120px;">
            <option value="">All Status</option>
            <option value="ACTIVE">Active</option>
            <option value="INACTIVE">Inactive</option>
        </select>
        <!-- Reset -->
        <button id="resetFilters" class="btn btn-sm btn-outline-secondary">Reset</button>
        <!-- Results count -->
        <span id="officeCount" class="ms-auto small text-muted"></span>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="officesTable">
            <thead style="background-color:#f8f9fa;">
                <tr>
                    <th class="ps-4" style="background:#f8f9fa;">#</th>
                    <th style="background:#f8f9fa;">Office Name</th>
                    <th style="background:#f8f9fa;">Type</th>
                    <th style="background:#f8f9fa;">Cluster</th>
                    <th style="background:#f8f9fa;">Status</th>
                    <th class="text-center" style="background:#f8f9fa;">Action</th>
                </tr>
            </thead>
            <tbody id="officesBody">
                <?php if (empty($offices)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No offices found.</td>
                    </tr>
                <?php else: ?>
                    <?php $i = 1;
                    foreach ($offices as $office): ?>
                        <tr
                            data-name="<?php echo strtolower(htmlspecialchars($office['office_name'])); ?>"
                            data-type="<?php echo htmlspecialchars($office['office_type']); ?>"
                            data-cluster="<?php echo htmlspecialchars($office['cluster'] ?? ''); ?>"
                            data-status="<?php echo htmlspecialchars($office['status']); ?>">
                            <td class="ps-4 small text-muted"><?php echo $i++; ?></td>
                            <td>
                                <span class="fw-semibold small"><?php echo htmlspecialchars($office['office_name']); ?></span>
                            </td>
                            <td>
                                <?php
                                $typeBadge = match ($office['office_type']) {
                                    'PROVINCE'     => '<span class="badge rounded-small" style="background-color:#092C4C;color:#fff;">Province</span>',
                                    'CITY'         => '<span class="badge rounded-small" style="background-color:#092C4C;color:#FFFFFF;">City</span>',
                                    'MUNICIPALITY' => '<span class="badge rounded-small" style="background-color:#092C4C;color:#FFFFFF;">Municipality</span>',
                                    default        => '<span class="badge bg-secondary">' . htmlspecialchars($office['office_type']) . '</span>'
                                };
                                echo $typeBadge;
                                ?>
                            </td>
                            <td><span class="small"><?php echo htmlspecialchars($office['cluster'] ?? '—'); ?></span></td>
                            <td>
                                <?php if ($office['status'] === 'ACTIVE'): ?>
                                    <span class="badge rounded-small bg-success bg-opacity-10 text-success">Active</span>
                                <?php else: ?>
                                    <span class="badge rounded-small bg-danger bg-opacity-10 text-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Action
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <a class="dropdown-item" href="<?php echo env('APP_URL'); ?>/superadmin/editoffice/<?php echo $office['office_id']; ?>">
                                                <i class="bi bi-pencil me-2 text-primary"></i>Edit
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

    <!-- No results row (hidden initially) -->
    <div id="noResults" class="text-center text-muted py-4" style="display:none!important;">No offices match your filters.</div>
</div>

<script>
    (function() {
        var searchInput = document.getElementById('officeSearch');
        var filterType = document.getElementById('filterType');
        var filterCluster = document.getElementById('filterCluster');
        var filterStatus = document.getElementById('filterStatus');
        var resetBtn = document.getElementById('resetFilters');
        var tbody = document.getElementById('officesBody');
        var countEl = document.getElementById('officeCount');
        var noResults = document.getElementById('noResults');

        function applyFilters() {
            var search = searchInput.value.toLowerCase().trim();
            var type = filterType.value;
            var cluster = filterCluster.value.toLowerCase();
            var status = filterStatus.value;
            var rows = tbody.querySelectorAll('tr[data-name]');
            var visible = 0;

            rows.forEach(function(row) {
                var matchName = !search || row.dataset.name.includes(search);
                var matchType = !type || row.dataset.type === type;
                var matchCluster = !cluster || row.dataset.cluster.toLowerCase() === cluster;
                var matchStatus = !status || row.dataset.status === status;
                var show = matchName && matchType && matchCluster && matchStatus;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            countEl.textContent = visible + ' office' + (visible !== 1 ? 's' : '') + ' found';
            noResults.style.setProperty('display', visible === 0 ? 'block' : 'none', 'important');
        }

        searchInput.addEventListener('input', applyFilters);
        filterType.addEventListener('change', applyFilters);
        filterCluster.addEventListener('change', applyFilters);
        filterStatus.addEventListener('change', applyFilters);

        resetBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterType.value = '';
            filterCluster.value = '';
            filterStatus.value = '';
            applyFilters();
        });

        applyFilters();
    })();
</script>

<?php require_once __DIR__ . '/../layouts/dashboard_footer.php'; ?>