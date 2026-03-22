<?php

class UserClassController
{
    private function authCheck()
    {
        if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
            header('Location: ' . BASE . '/');
            exit;
        }
    }

    public function index()
    {
        $this->authCheck();
        $classes = AdminModel::getClassesByTeacher((int) $_SESSION['teacher_id']);
        require_once 'views/user/UserMyClasses.php';
    }

    public function stats()
    {
        if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $classes = AdminModel::getClassesByTeacher((int) $_SESSION['teacher_id']);
        $result  = [];
        foreach ($classes as $c) {
            $result[] = [
                'class_id'      => (int) $c['class_id'],
                'student_count' => (int) $c['student_count'],
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
}
