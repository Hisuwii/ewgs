<?php
// Helper: returns 'active' if page matches current URL
if (!function_exists('isActive')) {
    function isActive($page) {
        return strpos($_SERVER['REQUEST_URI'], $page) !== false ? 'active' : '';
    }
}
?>
<div id="preloader"><div class="preloader-spin"></div></div>
<script>if(localStorage.getItem('darkMode')==='true'){document.getElementById('preloader').style.background='#121212';}</script>

<button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle navigation">
    <i class="bi bi-list"></i>
</button>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="sidebar">
    <div class="logo">
        <img src="/ewgs/public/images/logo.png" alt="Logo">
        <h5>Elementary Web Grading System</h5>
    </div>

    <div class="sidebar-body">
        <nav class="nav flex-column px-3">
            <a href="/ewgs/user/dashboard" class="nav-link <?= isActive('user/dashboard') ?>">
                <span><i class="bi bi-speedometer2"></i> Dashboard</span>
            </a>

            <a href="/ewgs/user/my-classes" class="nav-link <?= isActive('my-classes') ?>">
                <span><i class="bi bi-people"></i> My Classes</span>
            </a>

            <a href="/ewgs/user/add-grade" class="nav-link <?= isActive('add-grade') ?>">
                <span><i class="bi bi-plus-circle"></i> Add Grades</span>
            </a>

            <a href="/ewgs/user/manage-grades" class="nav-link <?= isActive('manage-grades') ?>">
                <span><i class="bi bi-clipboard-data"></i> Manage Grades</span>
            </a>

            <a href="/ewgs/user/reports" class="nav-link <?= isActive('user/reports') ?>">
                <span><i class="bi bi-file-earmark-text"></i> Reports</span>
            </a>
        </nav>
    </div>

    <div class="sidebar-footer">
        <div class="user-info">
            <i class="bi bi-person-circle"></i>
            <div class="user-text">
                <strong><?= $_SESSION['teacher_name'] ?? 'Teacher' ?></strong>
                <small>Teacher</small>
            </div>
        </div>
        <div class="dropdown">
            <button class="btn dropdown-toggle no-caret" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/ewgs/user/profile">Profile</a></li>
                <li class="dropdown-item d-flex justify-content-between align-items-center" style="min-width: 160px;">
                    <span>Appearance</span>
                    <div class="form-check form-switch m-0 ms-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="mode-toggle">
                    </div>
                </li>
                <li><a class="dropdown-item" href="/ewgs/user/logout">Logout</a></li>
            </ul>
        </div>
    </div>
</div>

<?php if (!empty($_SESSION['must_change_password'])): ?>
<style>
    /* ── Password Change Modal ───────────────────────────────── */
    #changePasswordModal .modal-content {
        border: none;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0,0,0,0.18);
    }
    #changePasswordModal .modal-header {
        background: linear-gradient(135deg, #2e4e2e, #4b6b4b);
        color: #fff;
        padding: 20px 24px;
        border-bottom: none;
    }
    #changePasswordModal .modal-title {
        font-size: 1rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    #changePasswordModal .cp-notice {
        background: #fff8e1;
        border-left: 4px solid #f9a825;
        border-radius: 6px;
        padding: 10px 14px;
        font-size: 13px;
        color: #5d4e00;
        margin-bottom: 20px;
    }
    #changePasswordModal .modal-body {
        padding: 24px;
        background: #fff;
    }
    #changePasswordModal .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }
    #changePasswordModal .form-control {
        border-radius: 8px;
        border: 1.5px solid #d1d5db;
        padding: 10px 14px;
        font-size: 14px;
        transition: border-color 0.2s;
    }
    #changePasswordModal .form-control:focus {
        border-color: #4b6b4b;
        box-shadow: 0 0 0 3px rgba(75,107,75,0.15);
    }
    #changePasswordModal .pw-strength {
        height: 4px;
        border-radius: 4px;
        background: #e5e7eb;
        margin-top: 6px;
        overflow: hidden;
    }
    #changePasswordModal .pw-strength-bar {
        height: 100%;
        border-radius: 4px;
        width: 0;
        transition: width 0.3s, background 0.3s;
    }
    #changePasswordModal .pw-strength-label {
        font-size: 11px;
        margin-top: 3px;
        color: #9ca3af;
    }
    #changePasswordModal #cpError {
        border-radius: 8px;
        font-size: 13px;
        padding: 10px 14px;
    }
    #changePasswordModal .btn-cp-submit {
        background: linear-gradient(135deg, #2e4e2e, #4b6b4b);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 11px;
        font-weight: 600;
        font-size: 14px;
        width: 100%;
        transition: opacity 0.2s;
    }
    #changePasswordModal .btn-cp-submit:hover { opacity: 0.9; color: #fff; }
    #changePasswordModal .btn-cp-submit:disabled { opacity: 0.7; }

    /* Dark mode */
    body.dark-mode #changePasswordModal .modal-content {
        background: #1e1e1e;
    }
    body.dark-mode #changePasswordModal .modal-body {
        background: #1e1e1e;
    }
    body.dark-mode #changePasswordModal .cp-notice {
        background: #2d2600;
        border-color: #f59e0b;
        color: #fcd34d;
    }
    body.dark-mode #changePasswordModal .form-label {
        color: #e5e7eb;
    }
    body.dark-mode #changePasswordModal .form-control {
        background: #2d2d2d;
        border-color: #444;
        color: #f3f4f6;
    }
    body.dark-mode #changePasswordModal .form-control:focus {
        border-color: #6b9e6b;
        box-shadow: 0 0 0 3px rgba(107,158,107,0.25);
    }
    body.dark-mode #changePasswordModal .form-control::placeholder {
        color: #9ca3af;
    }
    body.dark-mode #changePasswordModal .pw-strength {
        background: #374151;
    }
    body.dark-mode #changePasswordModal .pw-strength-label {
        color: #9ca3af;
    }
    body.dark-mode #changePasswordModal #cpError {
        background: #3b1a1a;
        border-color: #ef4444;
        color: #fca5a5;
    }
</style>

<!-- Mandatory Password Change Modal -->
<div class="modal fade" id="changePasswordModal"
     data-bs-backdrop="static" data-bs-keyboard="false"
     tabindex="-1" aria-labelledby="changePwLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePwLabel">
                    <i class="bi bi-shield-lock-fill me-2"></i>Password Change Required
                </h5>
            </div>
            <div class="modal-body">
                <div class="cp-notice">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    Your current password was <strong>system-generated</strong>. You must set a personal password before you can use the portal.
                </div>
                <form id="changePasswordForm" autocomplete="off">
                    <div class="mb-3">
                        <label class="form-label">New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="cpNewPassword"
                               placeholder="Minimum 8 characters" required minlength="8">
                        <div class="pw-strength"><div class="pw-strength-bar" id="cpStrengthBar"></div></div>
                        <div class="pw-strength-label" id="cpStrengthLabel"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="cpConfirmPassword"
                               placeholder="Re-enter your new password" required minlength="8">
                    </div>
                    <div id="cpError" class="alert alert-danger py-2 small mb-3" style="display:none;"></div>
                    <button type="submit" class="btn btn-cp-submit">
                        <i class="bi bi-check-circle me-1"></i> Set New Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
$(document).ready(function(){

    // ========== PRELOADER ==========
    var preloaderDone = false;
    function hidePreloader() {
        if (!preloaderDone) {
            preloaderDone = true;
            $('#preloader').fadeOut(400);
        }
    }
    $(window).on('load', hidePreloader);
    setTimeout(hidePreloader, 3000); // max wait 3 s — prevents stuck preloader if a resource stalls

    // ========== DARK MODE ==========
    var isDarkMode = localStorage.getItem('darkMode');

    if (isDarkMode === 'true') {
        $('body').addClass('dark-mode');
        $('#mode-toggle').prop('checked', true);
    }

    $('#mode-toggle').on('change', function(){
        var isChecked = $(this).is(':checked');

        if (isChecked) {
            $('body').addClass('dark-mode');
            localStorage.setItem('darkMode', 'true');
        } else {
            $('body').removeClass('dark-mode');
            localStorage.setItem('darkMode', 'false');
        }
    });

    // ========== TOAST NOTIFICATION ==========
    $('.toast-notification').fadeIn().delay(3000).fadeOut();

    // ========== MOBILE SIDEBAR TOGGLE ==========
    var $sidebar   = $('.sidebar');
    var $overlay   = $('#sidebarOverlay');
    var $toggleBtn = $('#sidebarToggle');

    function openSidebar() {
        $sidebar.addClass('sidebar-open');
        $overlay.addClass('active');
        $('body').css('overflow', 'hidden');
        $toggleBtn.html('<i class="bi bi-x-lg"></i>');
    }
    function closeSidebar() {
        $sidebar.removeClass('sidebar-open');
        $overlay.removeClass('active');
        $('body').css('overflow', '');
        $toggleBtn.html('<i class="bi bi-list"></i>');
    }

    $toggleBtn.on('click', function () {
        $sidebar.hasClass('sidebar-open') ? closeSidebar() : openSidebar();
    });

    $overlay.on('click', closeSidebar);

    // Close on nav link click
    $('.sidebar .nav-link').on('click', function () {
        if ($(window).width() <= 768) closeSidebar();
    });

    // Restore overflow on resize to desktop
    $(window).on('resize', function () {
        if ($(window).width() > 768) {
            $('body').css('overflow', '');
            $overlay.removeClass('active');
            $toggleBtn.html('<i class="bi bi-list"></i>');
        }
    });

});

// ── Session Idle Timeout ─────────────────────────────────────────────────────
(function () {
    var WARN_AT    = 25 * 60 * 1000;
    var LOGOUT_AT  = 30 * 60 * 1000;
    var LOGOUT_URL = '/ewgs/user/logout';
    var warnTimer, logoutTimer;
    var $modal, modalInstance;

    function buildModal() {
        $modal = $(
            '<div class="modal fade" id="idleWarningModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">' +
            '  <div class="modal-dialog modal-dialog-centered">' +
            '    <div class="modal-content">' +
            '      <div class="modal-header" style="background:#856404;color:#fff;">' +
            '        <h5 class="modal-title"><i class="bi bi-clock-history me-2"></i>Session About to Expire</h5>' +
            '      </div>' +
            '      <div class="modal-body">' +
            '        <p class="mb-1">You have been inactive for a while. You will be automatically logged out in <strong id="idleCountdown">5:00</strong>.</p>' +
            '        <p class="text-muted mb-0" style="font-size:13px;">Click <strong>Stay Logged In</strong> to continue your session.</p>' +
            '      </div>' +
            '      <div class="modal-footer">' +
            '        <button id="btnStayLoggedIn" class="btn" style="background:#4b6b4b;color:#fff;">Stay Logged In</button>' +
            '        <a href="' + LOGOUT_URL + '" class="btn btn-outline-secondary">Logout Now</a>' +
            '      </div>' +
            '    </div>' +
            '  </div>' +
            '</div>'
        );
        $('body').append($modal);
        modalInstance = new bootstrap.Modal($modal[0]);

        $('#btnStayLoggedIn').on('click', function () {
            modalInstance.hide();
            resetTimers();
            $.get('/ewgs/user/dashboard/ping').always(function () {});
        });
    }

    var countdownInterval;
    function startCountdown() {
        var remaining = 5 * 60;
        clearInterval(countdownInterval);
        countdownInterval = setInterval(function () {
            remaining--;
            var m = Math.floor(remaining / 60);
            var s = remaining % 60;
            $('#idleCountdown').text(m + ':' + (s < 10 ? '0' : '') + s);
            if (remaining <= 0) clearInterval(countdownInterval);
        }, 1000);
    }

    function showWarning() {
        if (!$modal) buildModal();
        startCountdown();
        modalInstance.show();
    }

    function doLogout() {
        clearInterval(countdownInterval);
        window.location.href = LOGOUT_URL;
    }

    function resetTimers() {
        clearTimeout(warnTimer);
        clearTimeout(logoutTimer);
        clearInterval(countdownInterval);
        if ($modal && modalInstance) modalInstance.hide();
        warnTimer   = setTimeout(showWarning, WARN_AT);
        logoutTimer = setTimeout(doLogout,    LOGOUT_AT);
    }

    $(document).on('mousemove keydown click scroll touchstart', resetTimers);
    resetTimers();
})();

<?php if (!empty($_SESSION['must_change_password'])): ?>
window.addEventListener('load', function () {
    var pwModal = new bootstrap.Modal(document.getElementById('changePasswordModal'), { backdrop: 'static', keyboard: false });
    pwModal.show();

    // Password strength indicator
    document.getElementById('cpNewPassword').addEventListener('input', function () {
        var val = this.value;
        var bar = document.getElementById('cpStrengthBar');
        var lbl = document.getElementById('cpStrengthLabel');
        var score = 0;
        if (val.length >= 8)  score++;
        if (val.length >= 12) score++;
        if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        var levels = [
            { w: '20%',  bg: '#ef4444', label: 'Very weak' },
            { w: '40%',  bg: '#f97316', label: 'Weak' },
            { w: '60%',  bg: '#eab308', label: 'Fair' },
            { w: '80%',  bg: '#22c55e', label: 'Strong' },
            { w: '100%', bg: '#16a34a', label: 'Very strong' },
        ];
        var lvl = levels[Math.max(0, score - 1)] || levels[0];
        if (val.length === 0) { bar.style.width = '0'; lbl.textContent = ''; return; }
        bar.style.width = lvl.w;
        bar.style.background = lvl.bg;
        lbl.textContent = lvl.label;
        lbl.style.color = lvl.bg;
    });

    document.getElementById('changePasswordForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var newPw     = document.getElementById('cpNewPassword').value;
        var confirmPw = document.getElementById('cpConfirmPassword').value;
        var errorEl   = document.getElementById('cpError');
        var btn       = this.querySelector('[type=submit]');

        errorEl.style.display = 'none';

        if (newPw.length < 8) {
            errorEl.textContent = 'Password must be at least 8 characters.';
            errorEl.style.display = 'block';
            return;
        }
        if (newPw !== confirmPw) {
            errorEl.textContent = 'Passwords do not match.';
            errorEl.style.display = 'block';
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        $.ajax({
            url: '/ewgs/user/change-password',
            type: 'POST',
            data: { new_password: newPw },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    pwModal.hide();
                    var toast = $('<div class="toast-notification toast-success" style="display:flex;">' +
                        '<i class="bi bi-check-circle-fill"></i>' +
                        '<span>Password updated successfully!</span></div>');
                    $('body').append(toast);
                    toast.fadeIn().delay(3000).fadeOut(function () { toast.remove(); });
                } else {
                    errorEl.textContent = res.message;
                    errorEl.style.display = 'block';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Set New Password';
                }
            },
            error: function () {
                errorEl.textContent = 'An unexpected error occurred. Please try again.';
                errorEl.style.display = 'block';
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Set New Password';
            }
        });
    });
});
<?php endif; ?>
</script>
