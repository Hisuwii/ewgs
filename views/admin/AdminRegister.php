<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <title>Register | Elementary Web Grading System</title>
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

    <!-- Register card -->
    <main class="d-flex flex-grow-1 align-items-center justify-content-center">
        <div class="login-card">
            <h4>Create Account</h4>
            <p>Register a new administrator</p>

            <form action="/ewgs/admin/register" method="post">
                <!-- Username -->
                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Username"
                           value="<?= htmlspecialchars($username ?? '') ?>" autocomplete="username">
                </div>

                <!-- Password -->
                <div class="mb-3 input-group password-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                    <span class="input-group-text" id="togglePassword">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </span>
                </div>

                <!-- Confirm Password -->
                <div class="mb-4 input-group password-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="confirm_password" id="confirmPassword" class="form-control" placeholder="Confirm Password">
                    <span class="input-group-text" id="toggleConfirm">
                        <i class="bi bi-eye" id="eyeIconConfirm"></i>
                    </span>
                </div>

                <button type="submit" class="btn btn-custom w-100 mb-3">Register</button>
            </form>

            <div class="login-links">
                <a href="/ewgs/admin">Already have an account? Sign in</a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="page-footer">
        &copy; <?= date('Y') ?> Elementary Web Grading System. All rights reserved.
    </footer>

    <!-- Error toast -->
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
            var $errorToast = $('#errorToast');
            if ($errorToast.length) {
                bootstrap.Toast.getOrCreateInstance($errorToast[0], { delay: 5000 }).show();
            }

            $('#togglePassword').on('click', function () {
                var $input = $('#password');
                $input.attr('type', $input.attr('type') === 'password' ? 'text' : 'password');
                $('#eyeIcon').toggleClass('bi-eye bi-eye-slash');
            });

            $('#toggleConfirm').on('click', function () {
                var $input = $('#confirmPassword');
                $input.attr('type', $input.attr('type') === 'password' ? 'text' : 'password');
                $('#eyeIconConfirm').toggleClass('bi-eye bi-eye-slash');
            });
        });
    </script>
</body>
</html>
