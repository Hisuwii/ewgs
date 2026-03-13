<?php

class ClassController
{
    public function index()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: /ewgs/admin');
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
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $classes = AdminModel::getAllClasses($_SESSION['user_id'] ?? null);

        $data = [];
        $count = 1;
        foreach ($classes as $class) {
            $data[] = [
                'count'        => $count++,
                'class_id'     => $class['class_id'],
                'class_name'   => htmlspecialchars($class['class_name']),
                'grade_level'  => htmlspecialchars($class['grade_level']),
                'school_year'  => htmlspecialchars($class['school_year']),
                'teacher_id'   => $class['teacher_id'] ?? null,
                'teacher_name' => $class['teacher_name']
                    ? htmlspecialchars($class['teacher_name'])
                    : null,
            ];
        }

        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
        exit;
    }

    public function add()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: /ewgs/admin');
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

        if (AdminModel::isClassDuplicate($data['class_name'], $data['grade_level'], $data['school_year'], null, $adminId)) {
            $this->jsonResponse(false, 'A class with the same section name, grade level, and school year already exists.');
        }

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

        header('Location: /ewgs/admin/class');
        exit;
    }

    public function edit()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            if ($this->isAjax()) { http_response_code(401); echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }
            header('Location: /ewgs/admin'); exit;
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
        if (AdminModel::isClassDuplicate($data['class_name'], $data['grade_level'], $data['school_year'], $id, $_SESSION['user_id'] ?? null)) {
            $this->jsonResponse(false, 'A class with the same section name, grade level, and school year already exists.');
        }

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
            header('Location: /ewgs/admin');
            exit;
        }

        $id     = (int) ($_POST['class_id'] ?? 0);
        $result = AdminModel::removeClass($id);

        if ($result === 'fk') {
            if ($this->isAjax()) $this->jsonResponse(false, 'Cannot delete this class. It has students or grades linked to it.');
            setFlash('error', 'Cannot delete this class. It has students or grades linked to it.');
            header('Location: /ewgs/admin/class'); exit;
        }

        if ($this->isAjax()) {
            $result ? $this->jsonResponse(true, 'Class deleted successfully.')
                    : $this->jsonResponse(false, 'Failed to delete class.');
        }

        $result ? setFlash('success', 'Class deleted successfully.') : setFlash('error', 'Failed to delete class.');
        header('Location: /ewgs/admin/class');
        exit;
    }

    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    private function jsonResponse($success, $message) {
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'message' => $message]);
        exit;
    }
}
