<?php

class ClassController
{
    public function index()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: ' . BASE . '/admin');
            exit;
        }

        $adminId  = $_SESSION['user_id'] ?? null;
        $teachers = AdminModel::getAllTeachers($adminId);
        require_once 'views/admin/AdminClass.php';
    }

    public function getData()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['draw' => (int)($_GET['draw']??1), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => [], 'error' => 'Unauthorized']);
            exit;
        }

        $adminId = $_SESSION['user_id'] ?? null;
        $conn    = getConnection();
        datatable_response(
            $conn,
            "SELECT c.class_id, c.class_name, c.grade_level, c.school_year,
                    MIN(tc.teacher_id) AS teacher_id,
                    GROUP_CONCAT(CONCAT(t.teacher_fname, ' ', t.teacher_lname) SEPARATOR ', ') AS teacher_name
             FROM tbl_class c
             LEFT JOIN tbl_teacher_class tc ON c.class_id = tc.class_id
             LEFT JOIN tbl_teacher t ON tc.teacher_id = t.teacher_id
             WHERE c.admin_id = ?",
            ['c.class_name', 'c.grade_level', 'c.school_year'],
            'c.class_id ASC',
            'i', [$adminId],
            fn($c) => [
                'class_id'     => $c['class_id'],
                'class_name'   => htmlspecialchars($c['class_name']),
                'grade_level'  => htmlspecialchars($c['grade_level']),
                'school_year'  => htmlspecialchars($c['school_year']),
                'teacher_id'   => $c['teacher_id'] ?? null,
                'teacher_name' => $c['teacher_name'] ? htmlspecialchars($c['teacher_name']) : null,
            ],
            'GROUP BY c.class_id',
            [1 => 'c.class_name', 2 => 'c.grade_level', 3 => 'c.school_year']
        );
    }

    public function checkDuplicate()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            http_response_code(401); echo json_encode(['exists' => false]); exit;
        }
        $adminId    = $_SESSION['user_id'] ?? null;
        $className  = trim($_GET['class_name']  ?? '');
        $gradeLevel = trim($_GET['grade_level'] ?? '');
        $excludeId  = (int) ($_GET['exclude_id'] ?? 0) ?: null;
        if ($className === '' || $gradeLevel === '') {
            echo json_encode(['exists' => false]); exit;
        }
        $exists = AdminModel::isClassDuplicate($className, $gradeLevel, '', $excludeId, $adminId);
        header('Content-Type: application/json');
        echo json_encode(['exists' => $exists]);
        exit;
    }

    public function add()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: ' . BASE . '/admin');
            exit;
        }

        $adminId   = $_SESSION['user_id'] ?? null;
        $teacherId = (int) ($_POST['teacher_id'] ?? 0);

        if (!$teacherId) {
            $this->jsonResponse(false, 'A teacher must be assigned to the class.');
        }

        $data = [
            'class_name'  => trim($_POST['class_name']),
            'grade_level' => trim($_POST['grade_level']),
            'school_year' => trim($_POST['school_year']),
            'admin_id'    => $adminId,
        ];

        $ok = AdminModel::addClass($data);

        if ($ok && $teacherId > 0) {
            // Get the newly inserted class ID and link the teacher
            $conn    = getConnection();
            $classId = $conn->insert_id;
            AdminModel::linkTeacherToClass($classId, $teacherId);
        }

        if ($this->isAjax()) {
            $ok ? $this->jsonResponse(true, 'Class added successfully.')
                : $this->jsonResponse(false, 'Failed to add class. Please try again.');
        }

        if ($ok) {
            setFlash('success', 'Class added successfully.');
        } else {
            setFlash('error', 'Failed to add class. Please try again.');
        }

        header('Location: ' . BASE . '/admin/class');
        exit;
    }

    public function edit()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            if ($this->isAjax()) { http_response_code(401); echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }
            header('Location: ' . BASE . '/admin'); exit;
        }
        $id        = (int) ($_POST['class_id']   ?? 0);
        $teacherId = (int) ($_POST['teacher_id'] ?? 0);

        if (!$teacherId) {
            $this->jsonResponse(false, 'A teacher must be assigned to the class.');
        }

        $data = [
            'class_name'  => trim($_POST['class_name']  ?? ''),
            'grade_level' => trim($_POST['grade_level'] ?? ''),
            'school_year' => trim($_POST['school_year'] ?? ''),
        ];
        if ($id && AdminModel::updateClass($id, $data)) {
            // Replace teacher: unlink all current teachers then link the selected one
            AdminModel::unlinkTeacherFromClass($id);
            if ($teacherId > 0) {
                AdminModel::linkTeacherToClass($id, $teacherId);
            }
            $this->jsonResponse(true, 'Class updated successfully.');
        } else {
            $this->jsonResponse(false, 'Failed to update class.');
        }
    }

    public function delete()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: ' . BASE . '/admin');
            exit;
        }

        $id     = (int) ($_POST['class_id'] ?? 0);
        $result = AdminModel::removeClass($id);

        if ($result === 'fk') {
            if ($this->isAjax()) $this->jsonResponse(false, 'Cannot delete this class. It has students or grades linked to it.');
            setFlash('error', 'Cannot delete this class. It has students or grades linked to it.');
            header('Location: ' . BASE . '/admin/class'); exit;
        }

        if ($this->isAjax()) {
            $result ? $this->jsonResponse(true, 'Class deleted successfully.')
                    : $this->jsonResponse(false, 'Failed to delete class.');
        }

        $result ? setFlash('success', 'Class deleted successfully.') : setFlash('error', 'Failed to delete class.');
        header('Location: ' . BASE . '/admin/class');
        exit;
    }

    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    private function jsonResponse($success, $message) {
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'message' => $message]);
        exit;
    }
}
