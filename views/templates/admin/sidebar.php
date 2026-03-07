<?php
// Helper: returns 'active' if page matches current URL
function isActive($page) {
    return strpos($_SERVER['REQUEST_URI'], $page) !== false ? 'active' : '';
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
            <a href="/ewgs/admin/AdminDashboard" class="nav-link <?= isActive('AdminDashboard') ?>">
                <span><i class="bi bi-speedometer2 "></i> Dashboard</span>
            </a>

            <a href="/ewgs/admin/teacher" class="nav-link <?= isActive('admin/teacher') && !isActive('teacher/logs') ? 'active' : '' ?>">
                <span><i class="bi bi-person-badge"></i> Teachers</span>
            </a>

            <a href="/ewgs/admin/class" class="nav-link <?= isActive('admin/class') ?>">
                <span><i class="bi bi-person-badge"></i> Classes</span>
            </a>

            <a href="/ewgs/admin/subject" class="nav-link <?= isActive('admin/subject') ?>">
                <span><i class="bi bi-book"></i> Subjects</span>
            </a>

            <!-- Students dropdown -->
            <a class="nav-link d-flex justify-content-between align-items-center"
               data-bs-toggle="collapse" href="#studentsMenu" style="cursor:pointer;">
                <span><i class="bi bi-people"></i> Students</span>
                <i class="bi bi-chevron-down" style="font-size:12px;"></i>
            </a>
            <div class="collapse <?= (strpos($_SERVER['REQUEST_URI'], '/admin/student') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/assign/student') !== false) ? 'show' : '' ?>" id="studentsMenu">
                <a href="/ewgs/admin/student" class="nav-link ps-2 <?= isActive('admin/student') && !isActive('assign/student') ? 'active' : '' ?>">
                    <span><i class="bi bi-person-lines-fill me-1"></i> Student List</span>
                </a>
                <a href="/ewgs/admin/assign/student" class="nav-link ps-2 <?= isActive('assign/student') && !isActive('assign/student/enrolled') ? 'active' : '' ?>">
                    <span><i class="bi bi-person-plus me-1"></i> Enroll Students</span>
                </a>
                <a href="/ewgs/admin/assign/student/enrolled" class="nav-link ps-2 <?= isActive('assign/student/enrolled') ?>">
                    <span><i class="bi bi-card-checklist me-1"></i> Enrolled Students</span>
                </a>
            </div>

            <a href="/ewgs/admin/teacher/logs" class="nav-link <?= isActive('teacher/logs') ?>">
                <span><i class="bi bi-journal-text"></i> Teacher Logs</span>
            </a>
        </nav>
    </div>

    <div class="sidebar-footer">
        <div class="user-info">
            <i class="bi bi-person-circle"></i>
            <div class="user-text">
                <strong><?= $_SESSION['username'] ?? 'Admin' ?></strong>
                <small>Administrator</small>
            </div>
        </div>
        <div class="dropdown">
            <button class="btn dropdown-toggle no-caret" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/ewgs/admin/profile">Profile</a></li>
                 <li class="dropdown-item d-flex justify-content-between align-items-center" style="min-width: 160px;">
                    <span>Appearance</span>
                    <div class="form-check form-switch m-0 ms-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="mode-toggle">
                    </div>
                </li>
                <li><a class="dropdown-item" href="/ewgs/admin/AdminLogin">Logout</a></li>
            </ul>
        </div>
    </div>
</div>

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
    // localStorage saves data in the browser (persists even after closing)
    // localStorage.getItem('key')  - get saved value
    // localStorage.setItem('key', 'value') - save value

    var isDarkMode = localStorage.getItem('darkMode');

    // On page load: check if dark mode was enabled before
    if (isDarkMode === 'true') {
        $('body').addClass('dark-mode');
        $('#mode-toggle').prop('checked', true);
    }

    // When toggle is clicked
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

    //TOAST NOTIFICATION
    $('.toast-notification').fadeIn().delay(3000).fadeOut();

    //MOBILE SIDEBAR TOGGLE
    var $sidebar        = $('.sidebar');
    var $overlay        = $('#sidebarOverlay');
    var $toggleBtn      = $('#sidebarToggle');

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

    // Close on nav link click (but not collapse toggles)
    $('.sidebar .nav-link').on('click', function () {
        if ($(window).width() <= 768 && !$(this).data('bsToggle') && $(this).attr('data-bs-toggle') !== 'collapse') {
            closeSidebar();
        }
    });

    // Restore overflow if window resized to desktop
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
    var WARN_AT    = 25 * 60 * 1000;   // show warning after 25 min idle
    var LOGOUT_AT  = 30 * 60 * 1000;   // auto-logout after 30 min idle
    var LOGOUT_URL = '/ewgs/admin/AdminLogin';
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
            // Ping server to refresh last_activity
            $.get('/ewgs/admin/AdminDashboard/ping').always(function () {});
        });
    }

    var countdownInterval;
    function startCountdown() {
        var remaining = 5 * 60; // 5 minutes (LOGOUT_AT - WARN_AT)
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

    // Reset on any user interaction
    $(document).on('mousemove keydown click scroll touchstart', resetTimers);
    resetTimers(); // start the timers on page load
})();

// Global helper — show a Bootstrap toast from JavaScript
function showToast(type, message) {
    var bg    = { success: '#2e7d32', error: '#b02a37', warning: '#856404', info: '#1565c0' };
    var icons = { success: 'bi-check-circle', error: 'bi-x-circle', warning: 'bi-exclamation-circle', info: 'bi-info-circle' };
    var bgColor = bg[type]    || bg.info;
    var icon    = icons[type] || 'bi-info-circle';
    var $container = $('<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;"></div>');
    var $toast = $(
        '<div class="toast align-items-center text-white border-0" role="alert" aria-live="assertive">' +
        '<div class="d-flex">' +
        '<div class="toast-body"><i class="bi ' + icon + ' me-2"></i>' + message + '</div>' +
        '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
        '</div></div>'
    ).css({ 'background-color': bgColor });
    $container.append($toast);
    $('body').append($container);
    bootstrap.Toast.getOrCreateInstance($toast[0], { delay: 4000 }).show();
    $toast[0].addEventListener('hidden.bs.toast', function () { $container.remove(); });
}

// Global helper — validate form fields with jQuery
function validateForm(fields) {
    var ok = true;
    $.each(fields, function (_, f) {
        var $el  = f.el;
        var val  = ($el.val() || '').trim();
        var msg  = '';
        $el.removeClass('is-invalid is-valid').next('.invalid-feedback').remove();

        if      (f.required && !val)                                         { msg = (f.label || 'This field') + ' is required.'; }
        else if (val && f.minLen  && val.length < f.minLen)                  { msg = (f.label || 'This field') + ' must be at least ' + f.minLen + ' characters.'; }
        else if (val && f.email   && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)){ msg = 'Enter a valid email address.'; }
        else if (val && f.digits  && !/^\d+$/.test(val))                     { msg = (f.label || 'This field') + ' must contain digits only.'; }
        else if (val && f.exactLen && val.length !== f.exactLen)             { msg = (f.label || 'This field') + ' must be exactly ' + f.exactLen + ' digits.'; }
        else if (val && f.pattern && !f.pattern.test(val))                  { msg = f.patternMsg || 'Invalid format.'; }

        if (msg) { $el.addClass('is-invalid').after('<div class="invalid-feedback">' + msg + '</div>'); ok = false; }
        else if (val) { $el.addClass('is-valid'); }
    });
    return ok;
}

// Global helper — clear all validation states from a form
function clearFormValidation($form) {
    $form.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
    $form.find('.invalid-feedback').remove();
}

// Real-time: remove error highlight as user corrects a field
$(document).on('input change', '.form-control.is-invalid, .form-select.is-invalid', function () {
    $(this).removeClass('is-invalid').next('.invalid-feedback').remove();
});
</script>