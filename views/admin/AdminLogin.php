<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <title>Login | Elementary Web Grading System</title>
    <link href="/ewgs/public/css/bootstrap.css" rel="stylesheet">
    <link href="/ewgs/public/css/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f5f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Header */
        .page-header {
            display: flex;
            align-items: center;
            padding: 1rem 2rem;
        }
        .page-header img {
            height: 75px;
            margin-right: 10px;
        }
        .page-header h5 {
            margin: 0;
            font-weight: 800;
            font-size: 25px;
            color: #2e4e2e;
        }

        /* Login card */
        .login-card {
            text-align: center;
            padding: 3rem 3.5rem;
            max-width: 550px;
            margin: auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .login-card h4 {
            font-weight: 700;
            font-size: 2rem;
            color: #2e4e2e;
            margin-bottom: 0.5rem;
        }
        .login-card p {
            font-size: 1.15rem;
            color: #555;
            margin-bottom: 1.8rem;
        }

        /* Input fields */
        .input-group {
            border: 1px solid #ccc;
            border-radius: 8px;
            overflow: hidden;
        }
        .input-group .form-control {
            border: none;
            padding: 12px;
            font-size: 1rem;
        }
        .input-group .input-group-text {
            border: none;
            background-color: #f9f9f9;
            cursor: default;
        }

        /* Password field with eye */
        .password-group .form-control {
            border-radius: 0;
        }
        .password-group .input-group-text:first-child {
            border-radius: 5px 0 0 5px;
        }
        .password-group .input-group-text:last-child {
            border-radius: 0 8px 8px 0;
            cursor: pointer;
            background-color: #f9f9f9;
        }

        /* Buttons */
        .btn-custom {
            background-color: #2e4e2e;
            color: #fff;
            font-weight: 600;
            padding: 12px;
            border-radius: 8px;
        }
        .btn-custom:hover {
            background-color: #243d24;
            color: #fff;
        }

        /* Links */
        .login-links a {
            color: #2e4e2e;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
        }
        .login-links a:hover {
            text-decoration: underline;
            color: #243d24;
        }

        /* Footer */
        .page-footer {
            background-color: #3d6b3d;
            color: #fff;
            text-align: center;
            padding: 0.75rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="page-header">
        <a href="/ewgs/admin" style="text-decoration: none; display: flex; align-items: center;">
            <img src="/ewgs/public/images/logo.png" alt="Logo">
            <h5>Elementary Web Grading System</h5>
        </a>
    </header>

    <!-- Login card -->
    <main class="d-flex flex-grow-1 align-items-center justify-content-center">
        <div class="login-card">
            <h4>Administrator Panel</h4>
            <p>Sign in to your account</p>



            <form action="/ewgs/admin/AdminLogin" method="post">
                <!-- Username -->
                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Username" autocomplete="username">
                </div>

                <!-- Password -->
                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                    <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </span>
                </div>

                <button type="submit" class="btn btn-custom w-100 mb-3">Sign in</button>
            </form>

            <div class="login-links">
                <a href="/ewgs/admin/register">Don't have an account? Register here</a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="page-footer">
        &copy; <?= date('Y') ?> Elementary Web Grading System. All rights reserved.
    </footer>

    <!-- Flash message (e.g. registration success) -->
    <?php
    $flashColors = ['success' => '#2e7d32', 'error' => '#b02a37', 'warning' => '#856404', 'info' => '#1565c0'];
    $flashIcons  = ['success' => 'bi-check-circle', 'error' => 'bi-x-circle', 'warning' => 'bi-exclamation-circle', 'info' => 'bi-info-circle'];
    $flashType    = null;
    $flashMessage = null;
    foreach (['success', 'error', 'warning', 'info'] as $t) {
        if (!empty($_SESSION['flash'][$t])) {
            $flashType    = $t;
            $flashMessage = $_SESSION['flash'][$t];
            unset($_SESSION['flash'][$t]);
            break;
        }
    }
    ?>
    <?php if ($flashType): ?>
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;">
        <div id="flashToast" class="toast align-items-center text-white border-0"
             style="background-color:<?= $flashColors[$flashType] ?>;"
             role="alert" aria-live="assertive">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi <?= $flashIcons[$flashType] ?> me-2"></i>
                    <?= htmlspecialchars($flashMessage) ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Session Expired Toast -->
    <?php if (isset($_GET['expired'])): ?>
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;">
        <div id="sessionToast" class="toast align-items-center text-white border-0"
             style="background-color:#856404;" role="alert" aria-live="assertive">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-exclamation-circle me-2"></i> Session expired. Please log in again.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Login error toast -->
    <?php if (isset($error)): ?>
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;">
        <div id="errorToast" class="toast align-items-center text-white border-0"
             style="background-color:#b02a37;" role="alert" aria-live="assertive">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-x-circle me-2"></i> <?= htmlspecialchars($error) ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="/ewgs/public/js/jquery.min.js"></script>
    <script src="/ewgs/public/js/bootstrap.bundle.js"></script>
    <script>
        $(function () {
            // bfcache fix: force reload so PHP session check runs
            $(window).on('pageshow', function (event) {
                if (event.originalEvent.persisted) {
                    window.location.reload();
                }
            });

            // Auto-show flash toast (e.g. registration success)
            var $flashToast = $('#flashToast');
            if ($flashToast.length) {
                bootstrap.Toast.getOrCreateInstance($flashToast[0], { delay: 5000 }).show();
            }

            // Auto-show session expired toast
            var $sessionToast = $('#sessionToast');
            if ($sessionToast.length) {
                bootstrap.Toast.getOrCreateInstance($sessionToast[0], { delay: 5000 }).show();
            }

            // Auto-show login error toast
            var $errorToast = $('#errorToast');
            if ($errorToast.length) {
                bootstrap.Toast.getOrCreateInstance($errorToast[0], { delay: 5000 }).show();
            }

            // Toggle password visibility
            $('#togglePassword').on('click', function () {
                var $input = $('#password');
                $input.attr('type', $input.attr('type') === 'password' ? 'text' : 'password');
                $('#eyeIcon').toggleClass('bi-eye bi-eye-slash');
            });
        });
    </script>
</body>
</html>
