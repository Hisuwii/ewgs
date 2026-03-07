<?php

class UserReportController
{
    private function authCheck()
    {
        if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
            header('Location: /ewgs/');
            exit;
        }
    }

    public function index()
    {
        $this->authCheck();
        $classes = AdminModel::getClassesByTeacher((int) $_SESSION['teacher_id']);
        require_once 'views/user/UserReports.php';
    }

    // AJAX: section performance data (avg grade per subject for a class/quarter)
    public function sectionData()
    {
        $this->authCheck();
        $classId = (int) ($_GET['class_id'] ?? 0);
        $quarter = $_GET['quarter'] ?? '';
        if (!AdminModel::teacherOwnsClass((int) $_SESSION['teacher_id'], $classId)) {
            http_response_code(403); echo json_encode(['error' => 'Access denied']); exit;
        }
        $report   = AdminModel::getClassGradeReport($classId, $quarter);
        $students = AdminModel::getStudentsWithGrades($classId, $quarter);
        header('Content-Type: application/json');
        echo json_encode(['report' => $report, 'students' => $students]);
        exit;
    }

    // AJAX: individual student grade data for a class/quarter
    public function studentData()
    {
        $this->authCheck();
        $classId   = (int) ($_GET['class_id']   ?? 0);
        $studentId = (int) ($_GET['student_id'] ?? 0);
        $quarter   = $_GET['quarter'] ?? '';
        if (!AdminModel::teacherOwnsClass((int) $_SESSION['teacher_id'], $classId)) {
            http_response_code(403); echo json_encode(['error' => 'Access denied']); exit;
        }
        $data = AdminModel::getStudentGradeReport($studentId, $classId, $quarter);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // AJAX: dashboard chart data
    // No class_id → passing/failing overview per class
    // With class_id → passing/failing per subject for that class
    public function dashboardChart()
    {
        $this->authCheck();
        $classId = (int) ($_GET['class_id'] ?? 0);
        if ($classId > 0) {
            if (!AdminModel::teacherOwnsClass((int) $_SESSION['teacher_id'], $classId)) {
                http_response_code(403); echo json_encode(['error' => 'Access denied']); exit;
            }
            $data = AdminModel::getClassSubjectChart($classId);
        } else {
            $data = AdminModel::getDashboardChartData((int) $_SESSION['teacher_id']);
        }
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
