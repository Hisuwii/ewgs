<!-- Prevent browser caching on protected pages -->
<meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">

<!-- Common CSS Resources -->
<link href="<?= BASE ?>/public/css/bootstrap.css" rel="stylesheet">
<script src="<?= BASE ?>/public/js/jquery.min.js"></script>
<script src="<?= BASE ?>/public/js/app.js"></script>

<!-- bfcache fix: hide page instantly and redirect to login when restored from back/forward cache -->
<script>
    $(window).on('pageshow', function (event) {
        if (event.originalEvent.persisted) {
            $('body').hide();
            window.location.replace('<?= BASE ?>/admin?expired=1');
        }
    });
</script>
<link href="<?= BASE ?>/public/css/bootstrap-icons.min.css" rel="stylesheet">

<style>
    body {
        background-color: #f5f7fa;
        font-family: system-ui, -apple-system, 'Segoe UI', sans-serif;
    }

    /* Sidebar */
    .sidebar {
        width: 240px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        background: #fff;
        display: flex;
        flex-direction: column;
        border-right: 1px solid #e5e7eb;
        box-shadow: 5px 0 5px rgba(0,0,0,0.21);
    }
    .sidebar-body {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding: 0.5rem 0.75rem;
    }
    .sidebar .logo {
        text-align: center;
        margin: 1rem 0;
    }
    .sidebar .logo img {
        width: 120px;
        margin-bottom: 10px;
    }
    .sidebar .logo h5 {
        font-size: 25px;
        font-weight: 800;
        color: #2e4e2e;
        margin-bottom: 30px;
    }
    .sidebar .nav-link {
        color: #444;
        font-weight: 500;
        font-size: 15px;
        border-radius: 8px;
        padding: 12px 14px;
        margin: 4px 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .sidebar .nav-link i {
        font-size: 18px;
        margin-right: 8px;
        color: inherit;
    }
    .sidebar .nav-link:hover {
        background-color: #e8f5e9;
        color: #1b5e20;
    }
    .sidebar .nav-link:focus,
    .sidebar .nav-link:active {
        color: #444;
        outline: none;
        box-shadow: none;
    }
    .sidebar .nav-link.active {
        background-color: #2e4e2e;
        color: #fff;
        font-weight: 600;
    }
    .sidebar .nav-link.active:focus {
        color: #fff;
    }

    /* Sidebar Footer */
    .sidebar-footer {
        background-color: #2e4e2e;
        color: #fff;
        padding: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .sidebar-footer .user-info {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .sidebar-footer .user-info i {
        font-size: 1.6rem;
    }
    .sidebar-footer .user-text {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }
    .sidebar-footer .user-text strong {
        font-size: 14px;
    }
    .sidebar-footer .user-text small {
        font-size: 12px;
        color: #e0e0e0;
    }
    .sidebar-footer .btn {
        border: none;
        background: none;
    }
    .sidebar-footer .dropdown-toggle {
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: background 0.18s;
        padding: 0;
    }
    .sidebar-footer .dropdown-toggle:hover,
    .sidebar-footer .dropdown-toggle:focus {
        background: rgba(255,255,255,0.18);
        outline: none;
        box-shadow: none;
    }
    .sidebar-footer .dropdown-toggle i {
        color: #fff;
        font-size: 1.15rem;
    }
    .no-caret::after {
        display: none !important;
    }

    /* Sidebar footer dropdown menu */
    .sidebar-footer .dropdown-menu {
        border: none;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.18);
        padding: 6px;
        min-width: 190px;
        z-index: 2000;
    }
    .sidebar-footer .dropdown-item {
        border-radius: 7px;
        padding: 9px 14px;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
        display: flex;
        align-items: center;
        gap: 9px;
        transition: background 0.15s;
    }
    .sidebar-footer .dropdown-item:hover {
        background-color: #e8f5e9;
        color: #1b5e20;
    }
    .sidebar-footer .dropdown-item i {
        font-size: 15px;
        width: 16px;
        text-align: center;
        flex-shrink: 0;
    }
    .sidebar-footer .dropdown-divider {
        margin: 4px 6px;
        border-color: #e5e7eb;
    }
    /* Dark mode dropdown */
    body.dark-mode .sidebar-footer .dropdown-menu {
        background: #2d2d2d;
        box-shadow: 0 8px 24px rgba(0,0,0,0.45);
    }
    body.dark-mode .sidebar-footer .dropdown-item {
        color: #e0e0e0;
    }
    body.dark-mode .sidebar-footer .dropdown-item:hover {
        background-color: #3a3a3a;
        color: #fff;
    }
    body.dark-mode .sidebar-footer .dropdown-divider {
        border-color: #444;
    }
    body.dark-mode .sidebar-footer .dropdown-item.text-danger {
        color: #f87171 !important;
    }
    body.dark-mode .sidebar-footer .dropdown-item.text-danger:hover {
        background-color: #3b1a1a;
        color: #fca5a5 !important;
    }

    /* Main Content */
    .main-content {
        margin-left: 240px;
        padding: 1rem;
    }
    .page-header {
        background: #4b6b4b;
        color: #fff;
        padding: 1.2rem 1.5rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }
    .page-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.3rem;
    }

    /* Dark Mode */
    body.dark-mode {
        background-color: #121212;
        color: #fff;
    }
    body.dark-mode .sidebar {
        background-color: #1e1e1e;
        border-color: #333;
    }
    body.dark-mode .sidebar .logo h5 {
        color: #fff;
    }
    body.dark-mode .sidebar .nav-link {
        color: #e0e0e0;
    }
    body.dark-mode .sidebar .nav-link:hover {
        background-color: #333;
        color: #fff;
    }
    body.dark-mode .sidebar .nav-link.active {
        background-color: #2e4e2e;
        color: #fff;
    }
    body.dark-mode .main-content {
        background-color: #121212;
    }
    body.dark-mode .card {
        background-color: #1e1e1e;
        color: #fff;
        border-color: #333;
    }
    body.dark-mode .card-header {
        background-color: #2d2d2d;
        color: #fff;
    }
    body.dark-mode .form-control {
        background-color: #2d2d2d;
        color: #fff;
        border-color: #444;
    }
    body.dark-mode .form-label {
        color: #e0e0e0;
    }

    /* ── Mobile Collapsible Sidebar ─────────────────────────── */
    .sidebar-toggle {
        display: none;
        position: fixed;
        top: 14px;
        left: 14px;
        z-index: 1100;
        background: #4b6b4b;
        color: #fff;
        border: none;
        border-radius: 8px;
        width: 40px;
        height: 40px;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0,0,0,0.25);
        transition: background 0.2s;
    }
    .sidebar-toggle:hover { background: #3a5a3a; }
    body.dark-mode .sidebar-toggle { background: #2a3f2a; }
    body.dark-mode .sidebar-toggle:hover { background: #3a5a3a; }

    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        z-index: 1040;
        backdrop-filter: blur(2px);
    }
    .sidebar-overlay.active { display: block; }

    @media (max-width: 768px) {
        .sidebar-toggle { display: flex; }
        .sidebar {
            transform: translateX(-260px);
            transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1050;
        }
        .sidebar.sidebar-open { transform: translateX(0); }
        .main-content {
            margin-left: 0 !important;
            padding-top: 64px;
        }
    }

    /* Preloader */
    #preloader {
        position: fixed;
        inset: 0;
        background: #f5f7fa;
        z-index: 9997;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    body.dark-mode #preloader { background: #121212; }
    .preloader-spin {
        width: 48px;
        height: 48px;
        border: 5px solid #e8f5e9;
        border-top-color: #2e4e2e;
        border-radius: 50%;
        animation: preloader-spin 0.75s linear infinite;
    }
    @keyframes preloader-spin { to { transform: rotate(360deg); } }

    /* Toast Notification */
    .toast-notification {
        display: none;
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 20px;
        border-radius: 10px;
        align-items: center;
        gap: 12px;
        min-width: 300px;
        color: #fff;
        font-weight: 500;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 9999;
    }
    .toast-success {
        background: #dcfce7;
        color: #166534;
        border-left: 4px solid #22c55e;
    }
    .toast-error {
        background: #c62828;
    }
    .toast-warning {
        background: #f9a825;
    }
    .toast-info {
        background: #1565c0;
    }
    .toast-notification i:first-child {
        font-size: 1.4rem;
    }
    .toast-notification span {
        flex: 1;
    }

</style>
