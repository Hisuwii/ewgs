<?php

class AdminAuthController
{
    public function showLogin()
    {
        // Prevent browser from caching this page
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        // Already logged in → go to dashboard
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            header('Location: ' . BASE . '/admin/AdminDashboard');
            exit;
        }

        require_once 'views/admin/AdminLogin.php';
    }

    public function login()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password';
            require_once 'views/admin/AdminLogin.php';
            return;
        }

        $conn = getConnection();
        $stmt = $conn->prepare("SELECT * FROM tbl_admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if (!$admin) {
            $error = 'Invalid username or password';
            require_once 'views/admin/AdminLogin.php';
            return;
        }

        $passwordMatch = false;
        if (password_verify($password, $admin['password'])) {
            $passwordMatch = true;
        } elseif ($password === $admin['password']) {
            $passwordMatch = true;
        }

        if (!$passwordMatch) {
            $error = 'Invalid username or password';
            require_once 'views/admin/AdminLogin.php';
            return;
        }

        $_SESSION['user_id']    = $admin['admin_id'];
        $_SESSION['username']   = $admin['username'];
        $_SESSION['logged_in']  = true;

        setFlash('success', 'Welcome back, ' . $admin['username'] . '!');

        header('Location: ' . BASE . '/admin/AdminDashboard');
        exit;
    }

    public function showRegister()
    {
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            header('Location: ' . BASE . '/admin/AdminDashboard');
            exit;
        }

        require_once 'views/admin/AdminRegister.php';
    }

    public function register()
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password']         ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if (empty($username) || empty($password) || empty($confirm)) {
            $error = 'All fields are required.';
            require_once 'views/admin/AdminRegister.php';
            return;
        }

        if (strlen($username) < 3) {
            $error = 'Username must be at least 3 characters.';
            require_once 'views/admin/AdminRegister.php';
            return;
        }

        if (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
            require_once 'views/admin/AdminRegister.php';
            return;
        }

        if ($password !== $confirm) {
            $error = 'Passwords do not match.';
            require_once 'views/admin/AdminRegister.php';
            return;
        }

        if (AdminModel::isAdminUsernameTaken($username)) {
            $error = 'Username already taken. Please choose another.';
            require_once 'views/admin/AdminRegister.php';
            return;
        }

        AdminModel::addAdmin($username, password_hash($password, PASSWORD_DEFAULT));

        setFlash('success', 'Account created! You can now log in.');
        header('Location: ' . BASE . '/admin');
        exit;
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Location: ' . BASE . '/admin');
        exit;
    }
}
