<?php

class AssignmentController
{
    private function authCheck()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: /ewgs/admin');
            exit;
        }
    }

    private function jsonAuthCheck()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
    }

    // ============================================================
    // TEACHER → CLASS
    // ============================================================

    public function teacherClass()
    {
        $this->authCheck();
        $adminId  = $_SESSION['user_id'] ?? null;
        $teachers = AdminModel::getAllTeachers($adminId);
        $classes  = AdminModel::getAllClasses($adminId);
        require_once 'views/admin/AdminAssignTeacher.php';
    }

    public function getTeacherClassLinks()
    {
        $this->jsonAuthCheck();
        $links = AdminModel::getTeacherClassLinks($_SESSION['user_id'] ?? null);
        $data  = [];
        $i = 1;
        foreach ($links as $row) {
            $data[] = [
                'count'        => $i++,
                'teacher_name' => htmlspecialchars($row['teacher_name']),
                'class_name'   => htmlspecialchars($row['class_name']),
                'grade_level'  => htmlspecialchars($row['grade_level']),
                'school_year'  => htmlspecialchars($row['school_year']),
                'class_id'     => $row['class_id'],
            ];
        }
        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
        exit;
    }

    public function linkTeacher()
    {
        $this->jsonAuthCheck();
        $classId   = (int) ($_POST['class_id']   ?? 0);
        $teacherId = (int) ($_POST['teacher_id'] ?? 0);
        $result    = AdminModel::linkTeacherToClass($classId, $teacherId);
        header('Content-Type: application/json');
        if ($result === 'duplicate') {
            echo json_encode(['success' => false, 'duplicate' => true, 'message' => 'This teacher is already linked to that class.']);
        } else {
            echo json_encode(['success' => (bool) $result]);
        }
        exit;
    }

    public function unlinkTeacher()
    {
        $this->jsonAuthCheck();
        $classId   = (int) ($_POST['class_id']   ?? 0);
        $teacherId = (int) ($_POST['teacher_id'] ?? 0) ?: null;
        $success   = AdminModel::unlinkTeacherFromClass($classId, $teacherId);
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit;
    }

    // ============================================================
    // SUBJECT → CLASS
    // ============================================================

    public function subjectClass()
    {
        $this->authCheck();
        $adminId  = $_SESSION['user_id'] ?? null;
        $subjects = AdminModel::getAllSubjects($adminId);
        $classes  = AdminModel::getAllClasses($adminId);
        require_once 'views/admin/AdminAssignSubject.php';
    }

    public function getSubjectClassLinks()
    {
        $this->jsonAuthCheck();
        $links = AdminModel::getSubjectClassLinks($_SESSION['user_id'] ?? null);
        $data  = [];
        $i = 1;
        foreach ($links as $row) {
            $data[] = [
                'count'        => $i++,
                'subject_name' => htmlspecialchars($row['subject_name']),
                'class_name'   => htmlspecialchars($row['class_name']),
                'grade_level'  => htmlspecialchars($row['grade_level']),
                'school_year'  => htmlspecialchars($row['school_year']),
                'subject_id'   => $row['subject_id'],
                'class_id'     => $row['class_id'],
            ];
        }
        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
        exit;
    }

    public function linkSubject()
    {
        $this->jsonAuthCheck();
        $subjectId = (int) ($_POST['subject_id'] ?? 0);
        $classId   = (int) ($_POST['class_id']   ?? 0);
        $result    = AdminModel::linkSubjectToClass($subjectId, $classId);
        header('Content-Type: application/json');
        if ($result === 'duplicate') {
            echo json_encode(['success' => false, 'duplicate' => true, 'message' => 'This subject is already linked to that class.']);
        } else {
            echo json_encode(['success' => (bool) $result]);
        }
        exit;
    }

    public function unlinkSubject()
    {
        $this->jsonAuthCheck();
        $subjectId = (int) ($_POST['subject_id'] ?? 0);
        $classId   = (int) ($_POST['class_id']   ?? 0);
        $success   = AdminModel::unlinkSubjectFromClass($subjectId, $classId);
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit;
    }

    // ============================================================
    // STUDENT → CLASS
    // ============================================================

    public function studentClass()
    {
        $this->authCheck();
        $adminId  = $_SESSION['user_id'] ?? null;
        $students = AdminModel::getAllStudents($adminId);
        $classes  = AdminModel::getAllClasses($adminId);
        require_once 'views/admin/AdminAssignStudent.php';
    }

    public function enrolledStudents()
    {
        $this->authCheck();
        $classes = AdminModel::getAllClasses($_SESSION['user_id'] ?? null);
        require_once 'views/admin/AdminEnrolledStudents.php';
    }

    public function getStudentClassLinks()
    {
        $this->jsonAuthCheck();
        $classId = isset($_GET['class_id']) ? (int) $_GET['class_id'] : null;
        $links   = AdminModel::getStudentClassLinks($classId, $_SESSION['user_id'] ?? null);
        $data    = [];
        $i = 1;
        foreach ($links as $row) {
            $data[] = [
                'count'        => $i++,
                'student_name' => htmlspecialchars($row['student_name']),
                'lrn'          => htmlspecialchars($row['lrn']),
                'gender'       => htmlspecialchars($row['gender']),
                'class_name'   => htmlspecialchars($row['class_name']),
                'grade_level'  => htmlspecialchars($row['grade_level']),
                'school_year'  => htmlspecialchars($row['school_year']),
                'student_id'   => $row['student_id'],
                'class_id'     => $row['class_id'],
            ];
        }
        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
        exit;
    }

    public function getAvailableStudents()
    {
        $this->jsonAuthCheck();
        $classId  = (int) ($_GET['class_id'] ?? 0);
        $adminId  = $_SESSION['user_id'] ?? null;
        $students = $classId
            ? AdminModel::getUnenrolledStudents($classId, $adminId)
            : AdminModel::getAllStudents($adminId);
        $data = [];
        $i = 1;
        foreach ($students as $s) {
            $name = isset($s['student_name'])
                ? $s['student_name']
                : ($s['student_lname'] . ', ' . $s['student_fname']);
            $data[] = [
                'count'        => $i++,
                'student_id'   => $s['student_id'],
                'student_name' => htmlspecialchars($name),
                'lrn'          => htmlspecialchars($s['lrn']),
                'gender'       => htmlspecialchars($s['gender']),
            ];
        }
        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
        exit;
    }

    public function enrollStudent()
    {
        $this->jsonAuthCheck();
        $classId    = (int) ($_POST['class_id'] ?? 0);
        $studentIds = $_POST['student_ids'] ?? [];
        if (!is_array($studentIds)) $studentIds = [$studentIds];

        if (!$classId || empty($studentIds)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            exit;
        }

        $enrolled        = 0;
        $skipped         = 0;
        $alreadyEnrolled = 0;
        foreach ($studentIds as $sid) {
            $sid = (int) $sid;
            if (!$sid) continue;
            $result = AdminModel::enrollStudentInClass($sid, $classId);
            if ($result === 'duplicate' || $result === 'already_enrolled') {
                $skipped++;
                if ($result === 'already_enrolled') $alreadyEnrolled++;
            } elseif ($result === true) {
                $enrolled++;
            }
        }

        $msg = '';
        if ($alreadyEnrolled > 0) {
            $msg = " {$alreadyEnrolled} student(s) already enrolled in another class.";
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'enrolled' => $enrolled, 'skipped' => $skipped, 'message' => trim($msg)]);
        exit;
    }

    public function removeStudent()
    {
        $this->jsonAuthCheck();
        $studentId = (int) ($_POST['student_id'] ?? 0);
        $classId   = (int) ($_POST['class_id']   ?? 0);
        $success   = AdminModel::removeStudentFromClass($studentId, $classId);
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit;
    }
}
