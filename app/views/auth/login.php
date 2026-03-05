<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex align-items-center justify-content-center" style="min-height:calc(100vh - 70px);padding-top:70px;background:#F4F5FA;">
    <div class="w-100 px-3" style="max-width:860px;">

        <!-- Card -->
        <div class="shadow-lg border-0 overflow-hidden" style="border-radius:20px;background:#fff;">
            <div class="row g-0">

                <!-- Left — Branding -->
                <div class="col-md-5 d-flex flex-column align-items-center justify-content-between text-white p-5" style="background:linear-gradient(160deg,#092C4C 0%,#1a4a7a 100%);min-height:480px;">
                    <div class="text-center">
                        <img src="<?php echo env('APP_URL'); ?>/public/img/dilg_logo.png" alt="DILG Logo" class="mb-4" style="width:96px;height:96px;object-fit:contain;filter:drop-shadow(0 4px 12px rgba(0,0,0,.3));">
                        <h3 class="fw-bold mb-2" style="letter-spacing:.5px;">LGMES</h3>
                        <p style="opacity:.8;font-size:.83rem;line-height:1.7;">Local Government Monitoring and Evaluation System — Streamlining report compliance tracking for LGUs under the DILG.</p>
                    </div>
                    <div class="text-center w-100 mt-4">
                        <div class="d-flex justify-content-center gap-3 mb-3">
                            <div class="text-center">
                                <div class="fw-bold" style="font-size:1.3rem;">100%</div>
                                <div style="font-size:.68rem;opacity:.7;letter-spacing:.4px;">DIGITAL</div>
                            </div>
                            <div style="width:1px;background:rgba(255,255,255,.2);"></div>
                            <div class="text-center">
                                <div class="fw-bold" style="font-size:1.3rem;">DILG</div>
                                <div style="font-size:.68rem;opacity:.7;letter-spacing:.4px;">CERTIFIED</div>
                            </div>
                            <div style="width:1px;background:rgba(255,255,255,.2);"></div>
                            <div class="text-center">
                                <div class="fw-bold" style="font-size:1.3rem;">24/7</div>
                                <div style="font-size:.68rem;opacity:.7;letter-spacing:.4px;">ACCESS</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-link p-0 text-decoration-none" style="color:rgba(255,255,255,.55);font-size:.72rem;" data-bs-toggle="modal" data-bs-target="#privacyModal">
                            <i class="bi bi-shield-check me-1"></i>Privacy Policy
                        </button>
                    </div>
                </div>

                <!-- Right — Form -->
                <div class="col-md-7 d-flex align-items-center">
                    <div class="w-100 p-4 p-md-5">

                        <div class="mb-4">
                            <h4 class="fw-bold mb-1" style="color:#092C4C;">Welcome Back</h4>
                            <p class="text-muted mb-0" style="font-size:.83rem;">Sign in to your LGMES account to continue.</p>
                        </div>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show py-2 rounded-3" role="alert" style="font-size:.82rem;">
                                <i class="bi bi-exclamation-circle me-1"></i><?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="<?php echo env('APP_URL'); ?>/auth/authenticate" method="POST">

                            <!-- Username -->
                            <div class="mb-3">
                                <label for="username" class="form-label fw-semibold small mb-1" style="color:#092C4C;">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text border-end-0" style="background:#F4F5FA;border-color:#dee2e6;color:#092C4C;">
                                        <i class="bi bi-person" style="font-size:.9rem;"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="username" name="username"
                                        placeholder="your_username"
                                        style="background:#F4F5FA;font-size:.85rem;color:#495057;"
                                        required value="<?php echo htmlspecialchars($username ?? ''); ?>">
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold small mb-1" style="color:#092C4C;">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text border-end-0" style="background:#F4F5FA;border-color:#dee2e6;color:#092C4C;">
                                        <i class="bi bi-envelope" style="font-size:.9rem;"></i>
                                    </span>
                                    <input type="email" class="form-control border-start-0 ps-0" id="email" name="email"
                                        placeholder="you@example.com"
                                        style="background:#F4F5FA;font-size:.85rem;"
                                        required value="<?php echo htmlspecialchars($email ?? ''); ?>">
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="mb-2">
                                <label for="password" class="form-label fw-semibold small mb-1" style="color:#092C4C;">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text border-end-0" style="background:#F4F5FA;border-color:#dee2e6;color:#092C4C;">
                                        <i class="bi bi-lock" style="font-size:.9rem;"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0 ps-0 border-end-0" id="password" name="password"
                                        placeholder="••••••••"
                                        style="background:#F4F5FA;font-size:.85rem;"
                                        required>
                                    <button class="btn border-start-0" type="button" id="togglePassword"
                                        style="background:#F4F5FA;border-color:#dee2e6;color:#6c757d;">
                                        <i class="bi bi-eye" style="font-size:.85rem;"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Forgot Password -->
                            <div class="mb-4 text-end">
                                <button type="button" class="btn btn-link p-0 text-decoration-none" style="font-size:.78rem;color:#092C4C;" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
                                    Forgot your password?
                                </button>
                            </div>

                            <button type="submit" class="btn w-100 fw-semibold" style="background-color:#F3AF0E;color:#092C4C;border:none;padding:.65rem;border-radius:8px;letter-spacing:.3px;">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                            </button>

                        </form>

                        <p class="text-center text-muted mt-4 mb-0" style="font-size:.72rem;">
                            By signing in you agree to our
                            <button type="button" class="btn btn-link p-0 text-decoration-none align-baseline" style="font-size:.72rem;color:#092C4C;" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</button>.
                        </p>

                    </div>
                </div>

            </div>
        </div><!-- /card -->

    </div>
</div>

<!-- ===== Forgot Password Modal ===== -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <div>
                    <h5 class="fw-bold mb-1" style="color:#092C4C;">Forgot Password?</h5>
                    <p class="text-muted mb-0" style="font-size:.8rem;">Contact your system administrator to reset your account password.</p>
                </div>
                <button type="button" class="btn-close ms-3 flex-shrink-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <div class="rounded-3 p-3 mb-3" style="background:#EAF1FB;border-left:4px solid #4A90D9;font-size:.82rem;">
                    <i class="bi bi-info-circle me-1" style="color:#4A90D9;"></i>
                    Password resets are managed by your <strong>DILG System Administrator</strong>. Please reach out directly to request a reset.
                </div>
                <div class="d-flex align-items-start gap-3 mb-3">
                    <span class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;background:#EEF2FF;">
                        <i class="bi bi-envelope" style="color:#092C4C;font-size:.9rem;"></i>
                    </span>
                    <div style="font-size:.82rem;">
                        <div class="fw-semibold" style="color:#092C4C;">Email the Admin</div>
                        <div class="text-muted">admin@dilg.gov.ph</div>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-3">
                    <span class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;background:#EEF2FF;">
                        <i class="bi bi-telephone" style="color:#092C4C;font-size:.9rem;"></i>
                    </span>
                    <div style="font-size:.82rem;">
                        <div class="fw-semibold" style="color:#092C4C;">Call the Help Desk</div>
                        <div class="text-muted">Available Mon–Fri, 8AM–5PM</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn w-100" style="background:#092C4C;color:#fff;border-radius:8px;" data-bs-dismiss="modal">Got it</button>
            </div>
        </div>
    </div>
</div>

<!-- ===== Privacy Policy Modal ===== -->
<div class="modal fade" id="privacyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-0" style="background:#092C4C;">
                <div>
                    <h5 class="fw-bold text-white mb-1">Privacy Policy</h5>
                    <p style="color:rgba(255,255,255,.7);font-size:.78rem;margin-bottom:.75rem;">LGMES — Local Government Monitoring and Evaluation System</p>
                </div>
                <button type="button" class="btn-close btn-close-white ms-3 flex-shrink-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-4" style="max-height:420px;overflow-y:auto;font-size:.83rem;color:#495057;line-height:1.8;">

                <p class="fw-semibold" style="color:#092C4C;">1. Information We Collect</p>
                <p>LGMES collects personal information including your name, email address, username, office assignment, and submitted report data. This information is provided by authorized system administrators or entered by you during account registration and use.</p>

                <p class="fw-semibold" style="color:#092C4C;">2. How We Use Your Information</p>
                <p>Your information is used solely for the purpose of tracking LGU report compliance within the Department of the Interior and Local Government (DILG). This includes managing your account, recording submissions, and generating compliance reports.</p>

                <p class="fw-semibold" style="color:#092C4C;">3. Data Sharing</p>
                <p>Personal data collected through LGMES is not sold or shared with third parties. Access is limited to authorized DILG personnel and system administrators on a need-to-know basis.</p>

                <p class="fw-semibold" style="color:#092C4C;">4. Data Security</p>
                <p>We implement reasonable security measures to protect your data from unauthorized access, alteration, or disclosure. Passwords are stored in hashed form and access is controlled through role-based authentication.</p>

                <p class="fw-semibold" style="color:#092C4C;">5. File Uploads</p>
                <p>Files submitted through the system are stored securely via Google Drive and/or local server storage. Uploaded files are accessible only to authorized officers and administrators.</p>

                <p class="fw-semibold" style="color:#092C4C;">6. Your Rights</p>
                <p>You have the right to access, correct, or request deletion of your personal data. Please contact your system administrator to exercise these rights.</p>

                <p class="fw-semibold" style="color:#092C4C;">7. Contact</p>
                <p class="mb-0">For privacy concerns, please contact the DILG system administrator at <strong>admin@dilg.gov.ph</strong>.</p>

            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-2">
                <button type="button" class="btn px-4" style="background:#092C4C;color:#fff;border-radius:8px;" data-bs-dismiss="modal">Close</button>
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

    // Keep input borders flush — remove focus outline mismatch on split input-group
    document.querySelectorAll('.input-group .form-control').forEach(function(el) {
        el.addEventListener('focus', function() {
            this.closest('.input-group').style.boxShadow = '0 0 0 .2rem rgba(9,44,76,.15)';
            this.closest('.input-group').style.borderRadius = '6px';
        });
        el.addEventListener('blur', function() {
            this.closest('.input-group').style.boxShadow = '';
        });
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>