// LGMES Application
console.log('LGMES Application Loaded');

document.addEventListener('DOMContentLoaded', function() {

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 5000);
    });

    // Sidebar Toggle
    var sidebarToggle = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var body = document.getElementById('body-dashboard');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            var isMobile = window.innerWidth < 992;

            if (isMobile) {
                sidebar.classList.toggle('show');
                if (overlay) overlay.classList.toggle('show');
            } else {
                document.body.classList.toggle('sidebar-collapsed');
            }
        });

        // Close sidebar when clicking overlay (mobile)
        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                sidebar.classList.remove('show');
                if (overlay) overlay.classList.remove('show');
            }
        });
    }

    // Sidebar Search Filter
    var sidebarSearchInput = document.getElementById('sidebarSearch');
    if (sidebarSearchInput) {
        sidebarSearchInput.addEventListener('input', function() {
            var query = this.value.toLowerCase().trim();
            var items = document.querySelectorAll('.sidebar-item');
            items.forEach(function(item) {
                var text = item.textContent.toLowerCase();
                item.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }
});
