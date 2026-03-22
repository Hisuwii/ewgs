<?php

class UserAuthController
{
    public function index()
    {
        // Prevent browser from caching this page
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        // Already logged in → go to dashboard
        if (isset($_SESSION['teacher_logged_in']) && $_SESSION['teacher_logged_in'] === true) {
            header('Location: ' . BASE . '/user/dashboard');
            exit;
        }

        require_once 'views/user/UserLogin.php';
    }

    public function login()
    {
        $email    = $_POST['email']    ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'Please enter both email and password.';
            require_once 'views/user/UserLogin.php';
            return;
        }

        $conn = getConnection();
        $stmt = $conn->prepare("SELECT * FROM tbl_teacher WHERE teacher_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $teacher = $result->fetch_assoc();

            if (!password_verify($password, $teacher['teacher_password'])) {
                $error = 'Invalid email or password.';
                require_once 'views/user/UserLogin.php';
                return;
            }

            if (($teacher['status'] ?? 'Active') === 'Inactive') {
                $error = 'Your account has been deactivated. Please contact the administrator.';
                require_once 'views/user/UserLogin.php';
                return;
            }

            $_SESSION['teacher_logged_in']    = true;
            $_SESSION['teacher_id']           = $teacher['teacher_id'];
            $_SESSION['teacher_name']         = $teacher['teacher_fname'] . ' ' . $teacher['teacher_lname'];
            $_SESSION['teacher_email']        = $teacher['teacher_email'];
            if (!empty($teacher['must_change_password'])) {
                $_SESSION['must_change_password'] = true;
            }

            setFlash('success', 'Welcome, ' . $teacher['teacher_fname'] . ' ' . $teacher['teacher_lname'] . '!');

            header('Location: ' . BASE . '/user/dashboard');
            exit;
        } else {
            $error = 'Invalid email or password.';
            require_once 'views/user/UserLogin.php';
        }
    }

    public function changePassword()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $newPassword = trim($_POST['new_password'] ?? '');
        if (strlen($newPassword) < 8) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters.']);
            exit;
        }

        $ok = AdminModel::updateTeacherPassword((int) $_SESSION['teacher_id'], $newPassword);

        if ($ok) {
            unset($_SESSION['must_change_password']);
            echo json_encode(['success' => true, 'message' => 'Password updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update password. Please try again.']);
        }
        exit;
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Location: ' . BASE . '/');
        exit;
    }
}
