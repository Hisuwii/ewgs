<?php

class AdminDashboardController
{
    public function index()
    {
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: /ewgs/admin');
            exit;
        }

        $adminId = $_SESSION['user_id'] ?? null;
        $stats = [
            'teachers' => AdminModel::countTeachers($adminId),
            'students' => AdminModel::countStudents($adminId),
            'classes'  => AdminModel::countClasses($adminId),
            'subjects' => AdminModel::countSubjects($adminId),
        ];
        $adminActivity   = AdminModel::getAdminActivity(5, $adminId);
        $teacherActivity = AdminModel::getTeacherActivity($adminId);

        require_once 'views/admin/AdminDashboard.php';
    }

    public function ping()
    {
        header('Content-Type: application/json');
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            http_response_code(401);
            echo json_encode(['ok' => false]);
            exit;
        }
        $_SESSION['last_activity'] = time();
        echo json_encode(['ok' => true]);
        exit;
    }

    public function addTeacher()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: /ewgs/admin');
            exit;
        }

        $data = [
            'teacher_fname' => $_POST['teacher_fname'] ?? '',
            'teacher_lname' => $_POST['teacher_lname'] ?? '',
            'teacher_email' => $_POST['teacher_email'] ?? ''
        ];

        if (AdminModel::addTeacher($data)) {
            setFlash('success', 'Teacher added successfully!');
        } else {
            setFlash('error', 'Failed to add teacher.');
        }

        header('Location: /ewgs/admin/AdminDashboard');
        exit;
    }
}
