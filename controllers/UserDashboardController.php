<?php

class UserDashboardController
{
    public function index()
    {
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
            header('Location: ' . BASE . '/');
            exit;
        }

        $teacherId    = (int) $_SESSION['teacher_id'];
        $classes      = AdminModel::getClassesByTeacher($teacherId);
        $classCount   = count($classes);
        $studentCount = array_sum(array_column($classes, 'student_count'));
        $subjectCount = AdminModel::countSubjectsForTeacher($teacherId);
        $gradeCount   = 0;

        require_once 'views/user/UserDashboard.php';
    }

    public function ping()
    {
        header('Content-Type: application/json');
        if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
            http_response_code(401);
            echo json_encode(['ok' => false]);
            exit;
        }
        $_SESSION['last_activity'] = time();
        echo json_encode(['ok' => true]);
        exit;
    }

    public function stats()
    {
        if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $teacherId    = (int) $_SESSION['teacher_id'];
        $classes      = AdminModel::getClassesByTeacher($teacherId);
        $classCount   = count($classes);
        $studentCount = array_sum(array_column($classes, 'student_count'));
        $subjectCount = AdminModel::countSubjectsForTeacher($teacherId);

        header('Content-Type: application/json');
        echo json_encode([
            'classes'  => $classCount,
            'students' => $studentCount,
            'subjects' => $subjectCount,
        ]);
        exit;
    }
}
