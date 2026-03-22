<?php

class AssignmentController
{
    private function authCheck()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: ' . BASE . '/admin');
            exit;
        }
    }

    private function jsonAuthCheck()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['draw' => (int)($_GET['draw']??1), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => [], 'error' => 'Unauthorized']);
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
        $adminId = $_SESSION['user_id'] ?? null;
        $conn    = getConnection();
        datatable_response(
            $conn,
            "SELECT tc.class_id, c.class_name, c.grade_level, c.school_year,
                    t.teacher_id,
                    CONCAT(t.teacher_fname, ' ', t.teacher_lname) AS teacher_name
             FROM tbl_teacher_class tc
             INNER JOIN tbl_class c ON tc.class_id = c.class_id
             INNER JOIN tbl_teacher t ON tc.teacher_id = t.teacher_id
             WHERE c.admin_id = ?",
            ['t.teacher_fname', 't.teacher_lname', 'c.class_name', 'c.grade_level'],
            'tc.class_id ASC',
            'i', [$adminId],
            fn($r) => [
                'class_id'     => $r['class_id'],
                'teacher_name' => htmlspecialchars($r['teacher_name']),
                'class_name'   => htmlspecialchars($r['class_name']),
                'grade_level'  => htmlspecialchars($r['grade_level']),
                'school_year'  => htmlspecialchars($r['school_year']),
            ],
            '',
            [1 => 't.teacher_lname', 2 => 'c.class_name', 3 => 'c.grade_level', 4 => 'c.school_year']
        );
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
        $adminId = $_SESSION['user_id'] ?? null;
        $conn    = getConnection();
        datatable_response(
            $conn,
            "SELECT sc.subject_id, sc.class_id,
                    s.subject_name,
                    c.class_name, c.grade_level, c.school_year
             FROM tbl_subject_class sc
             INNER JOIN tbl_subject s ON sc.subject_id = s.subject_id
             INNER JOIN tbl_class c ON sc.class_id = c.class_id
             WHERE c.admin_id = ?",
            ['s.subject_name', 'c.class_name', 'c.grade_level'],
            'sc.subject_id ASC',
            'i', [$adminId],
            fn($r) => [
                'subject_id'   => $r['subject_id'],
                'class_id'     => $r['class_id'],
                'subject_name' => htmlspecialchars($r['subject_name']),
                'class_name'   => htmlspecialchars($r['class_name']),
                'grade_level'  => htmlspecialchars($r['grade_level']),
                'school_year'  => htmlspecialchars($r['school_year']),
            ],
            '',
            [1 => 's.subject_name', 2 => 'c.class_name', 3 => 'c.grade_level', 4 => 'c.school_year']
        );
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
        $adminId = $_SESSION['user_id'] ?? null;
        $classId = isset($_GET['class_id']) ? (int) $_GET['class_id'] : null;
        $conn    = getConnection();

        $baseQuery   = "SELECT s.student_id, sc2.class_id,
                               CONCAT(s.student_lname, ', ', s.student_fname) AS student_name,
                               s.student_lrn AS lrn, s.student_gender AS gender,
                               c.class_name, c.grade_level, c.school_year
                        FROM tbl_student s
                        INNER JOIN tbl_student_class sc2 ON sc2.student_id = s.student_id
                        INNER JOIN tbl_class c ON c.class_id = sc2.class_id
                        WHERE c.admin_id = ?";
        $bindTypes   = 'i';
        $bindValues  = [$adminId];
        $defaultOrder = 's.student_id ASC';

        if ($classId) {
            $baseQuery  .= ' AND sc2.class_id = ?';
            $bindTypes  .= 'i';
            $bindValues[] = $classId;
        }

        datatable_response(
            $conn, $baseQuery,
            ['s.student_fname', 's.student_lname', 's.student_lrn', 'c.class_name'],
            $defaultOrder,
            $bindTypes, $bindValues,
            fn($r) => [
                'student_id'   => $r['student_id'],
                'class_id'     => $r['class_id'],
                'student_name' => htmlspecialchars($r['student_name']),
                'lrn'          => htmlspecialchars($r['lrn']),
                'gender'       => htmlspecialchars($r['gender']),
                'class_name'   => htmlspecialchars($r['class_name']),
                'grade_level'  => htmlspecialchars($r['grade_level']),
                'school_year'  => htmlspecialchars($r['school_year']),
            ],
            '',
            [1 => 's.student_lname', 2 => 's.student_lrn', 3 => 's.student_gender', 4 => 'c.class_name', 5 => 'c.grade_level', 6 => 'c.school_year']
        );
    }

    public function getAvailableStudents()
    {
        $this->jsonAuthCheck();
        $adminId = $_SESSION['user_id'] ?? null;
        $classId = (int) ($_GET['class_id'] ?? 0);
        $conn    = getConnection();

        if ($classId) {
            $baseQuery  = "SELECT s.student_id,
                                  CONCAT(s.student_lname, ', ', s.student_fname) AS student_name,
                                  s.student_lrn AS lrn, s.student_gender AS gender
                           FROM tbl_student s
                           WHERE s.student_id NOT IN (
                               SELECT student_id FROM tbl_student_class WHERE class_id = ?
                           ) AND s.admin_id = ?";
            $bindTypes  = 'ii';
            $bindValues = [$classId, $adminId];
        } else {
            $baseQuery  = "SELECT s.student_id,
                                  CONCAT(s.student_lname, ', ', s.student_fname) AS student_name,
                                  s.student_lrn AS lrn, s.student_gender AS gender
                           FROM tbl_student s
                           WHERE s.admin_id = ?";
            $bindTypes  = 'i';
            $bindValues = [$adminId];
        }

        datatable_response(
            $conn, $baseQuery,
            ['s.student_fname', 's.student_lname', 's.student_lrn'],
            's.student_id ASC',
            $bindTypes, $bindValues,
            fn($s) => [
                'student_id'   => $s['student_id'],
                'student_name' => htmlspecialchars($s['student_name']),
                'lrn'          => htmlspecialchars($s['lrn']),
                'gender'       => htmlspecialchars($s['gender']),
            ],
            '',
            [2 => 's.student_lname', 3 => 's.student_lrn', 4 => 's.student_gender']
        );
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
