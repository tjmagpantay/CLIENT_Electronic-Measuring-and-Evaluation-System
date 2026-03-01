<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 130px);">
    <div class="card login-card shadow-lg border-0 overflow-hidden" style="max-width: 900px; width: 100%;">
        <div class="row g-0">
            <!-- Left Side - Branding -->
            <div class="col-md-5 d-flex flex-column align-items-center justify-content-center text-white text-center p-4" style="background-color: #092C4C;">
                <img src="<?php echo env('APP_URL'); ?>/public/img/dilg_logo.png" alt="DILG Logo" class="mb-3" style="width: 120px; height: 120px; object-fit: contain;">
                <h3 class="fw-bold mb-2">LGMES</h3>
                <p class="small px-3" style="opacity: 0.85;">Local Government Monitoring and Evaluation System — Streamlining report compliance tracking for LGUs under the Department of the Interior and Local Government.</p>
            </div>

            <!-- Right Side - Login Form -->
            <div class="col-md-7 d-flex align-items-center">
                <div class="p-4 p-md-5 w-100">
                    <h4 class="fw-bold mb-1" style="color: #092C4C;">Welcome Back</h4>
                    <p class="text-muted mb-4">Sign in to your account</p>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
                            <small><?php echo htmlspecialchars($error); ?></small>
                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo env('APP_URL'); ?>/auth/authenticate" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold" style="color: #092C4C;">Username</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background-color: #092C4C; border-color: #092C4C; color: white;">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required value="<?php echo htmlspecialchars($username ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold" style="color: #092C4C;">Email</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background-color: #092C4C; border-color: #092C4C; color: white;">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold" style="color: #092C4C;">Password</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background-color: #092C4C; border-color: #092C4C; color: white;">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn w-100 fw-semibold text-white" style="background-color: #F3AF0E; border-color: #F3AF0E;">
                            Sign In
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const pwd = document.getElementById('password');
        const icon = this.querySelector('i');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            pwd.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>