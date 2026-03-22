<?php

class TeacherController
{
    public function index()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: ' . BASE . '/admin');
            exit;
        }

        require_once 'views/admin/AdminTeacher.php';
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
            "SELECT teacher_id, teacher_fname, teacher_lname, teacher_email, status
             FROM tbl_teacher
             WHERE admin_id = ?",
            ['teacher_fname', 'teacher_lname', 'teacher_email'],
            'teacher_id ASC',
            'i', [$adminId],
            fn($t) => [
                'teacher_id'    => $t['teacher_id'],
                'teacher_fname' => htmlspecialchars($t['teacher_fname']),
                'teacher_lname' => htmlspecialchars($t['teacher_lname']),
                'teacher_email' => htmlspecialchars($t['teacher_email']),
                'status'        => $t['status'] ?? 'Active',
            ],
            '',
            [1 => 'teacher_fname', 2 => 'teacher_lname', 3 => 'teacher_email', 4 => 'status']
        );
    }

    public function add()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: ' . BASE . '/admin');
            exit;
        }

        $data = [
            'teacher_fname' => $_POST['teacher_fname'] ?? '',
            'teacher_lname' => $_POST['teacher_lname'] ?? '',
            'teacher_email' => $_POST['teacher_email'] ?? '',
            'admin_id'      => $_SESSION['user_id'] ?? null,
        ];

        $result = AdminModel::addTeacher($data);

        if ($this->isAjax()) {
            if ($result['success']) {
                sendTeacherCredentials($data['teacher_email'], $data['teacher_fname'], $data['teacher_lname'], $result['password']);
                $this->jsonResponse(true, 'Teacher added! Generated password: ' . $result['password']);
            } else {
                $this->jsonResponse(false, 'Failed to add teacher.');
            }
        }

        if ($result['success']) {
            sendTeacherCredentials($data['teacher_email'], $data['teacher_fname'], $data['teacher_lname'], $result['password']);
            setFlash('success', 'Teacher added successfully! Generated password: ' . $result['password']);
        } else {
            setFlash('error', 'Failed to add teacher.');
        }

        header('Location: ' . BASE . '/admin/teacher');
        exit;
    }

    public function edit()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            if ($this->isAjax()) { http_response_code(401); echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }
            header('Location: ' . BASE . '/admin'); exit;
        }
        $id      = (int) ($_POST['teacher_id'] ?? 0);
        $teacher = AdminModel::getTeacherById($id);
        if (!$teacher || $teacher['admin_id'] != ($_SESSION['user_id'] ?? null)) {
            $this->jsonResponse(false, 'Unauthorized: you can only edit teachers you created.');
        }
        $data = [
            'teacher_fname' => trim($_POST['teacher_fname'] ?? ''),
            'teacher_lname' => trim($_POST['teacher_lname'] ?? ''),
            'teacher_email' => trim($_POST['teacher_email'] ?? ''),
        ];
        if ($id && AdminModel::updateTeacher($id, $data)) {
            $this->jsonResponse(true, 'Teacher updated successfully.');
        } else {
            $this->jsonResponse(false, 'Failed to update teacher.');
        }
    }

    public function delete()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: ' . BASE . '/admin');
            exit;
        }

        $id      = (int) ($_POST['teacher_id'] ?? 0);
        $teacher = AdminModel::getTeacherById($id);
        if (!$teacher || $teacher['admin_id'] != ($_SESSION['user_id'] ?? null)) {
            if ($this->isAjax()) $this->jsonResponse(false, 'Unauthorized: you can only delete teachers you created.');
            setFlash('error', 'Unauthorized: you can only delete teachers you created.');
            header('Location: ' . BASE . '/admin/teacher'); exit;
        }
        $result = AdminModel::removeTeacher($id);

        if ($result === 'fk') {
            if ($this->isAjax()) $this->jsonResponse(false, 'Cannot delete this teacher. They are currently assigned to one or more classes.');
            setFlash('error', 'Cannot delete this teacher. They are currently assigned to one or more classes.');
            header('Location: ' . BASE . '/admin/teacher'); exit;
        }

        if ($this->isAjax()) {
            $result ? $this->jsonResponse(true, 'Teacher deleted successfully.')
                    : $this->jsonResponse(false, 'Failed to delete teacher.');
        }

        $result ? setFlash('success', 'Teacher deleted successfully!') : setFlash('error', 'Failed to delete teacher.');
        header('Location: ' . BASE . '/admin/teacher');
        exit;
    }

    public function resetPassword()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            $this->jsonResponse(false, 'Unauthorized');
        }

        $id      = (int) ($_POST['teacher_id'] ?? 0);
        $teacher = AdminModel::getTeacherById($id);

        if (!$teacher || $teacher['admin_id'] != ($_SESSION['user_id'] ?? null)) {
            $this->jsonResponse(false, 'Unauthorized: you can only reset passwords for teachers you created.');
        }

        $newPassword = AdminModel::resetTeacherPassword($id);
        if (!$newPassword) {
            $this->jsonResponse(false, 'Failed to reset password.');
        }

        // Attempt to email the new password; proceed even if email fails
        sendTeacherCredentials(
            $teacher['teacher_email'],
            $teacher['teacher_fname'],
            $teacher['teacher_lname'],
            $newPassword,
            true
        );

        header('Content-Type: application/json');
        echo json_encode([
            'success'  => true,
            'message'  => 'Password reset successfully.',
            'password' => $newPassword,
        ]);
        exit;
    }

    public function toggleStatus()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            $this->jsonResponse(false, 'Unauthorized');
        }
        $id = (int) ($_POST['teacher_id'] ?? 0);
        $teacher = AdminModel::getTeacherById($id);
        if (!$teacher || $teacher['admin_id'] != ($_SESSION['user_id'] ?? null)) {
            $this->jsonResponse(false, 'Unauthorized: you can only manage teachers you created.');
        }
        $newStatus = AdminModel::toggleTeacherStatus($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'status' => $newStatus,
                          'message' => 'Teacher status set to ' . $newStatus . '.']);
        exit;
    }

    public function checkEmail()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit;
        }
        $email     = trim($_GET['email']       ?? '');
        $excludeId = (int) ($_GET['exclude_id'] ?? 0);
        $conn      = getConnection();
        if ($excludeId) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_teacher WHERE teacher_email = ? AND teacher_id != ?");
            $stmt->bind_param("si", $email, $excludeId);
        } else {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_teacher WHERE teacher_email = ?");
            $stmt->bind_param("s", $email);
        }
        $stmt->execute();
        $count = (int) $stmt->get_result()->fetch_row()[0];
        header('Content-Type: application/json');
        echo json_encode(['available' => $count === 0]);
        exit;
    }

    public function logs()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: ' . BASE . '/admin');
            exit;
        }
        require_once 'views/admin/AdminTeacherLog.php';
    }

    public function getLogsData()
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
            "SELECT t.teacher_id, t.teacher_fname, t.teacher_lname, t.teacher_email,
                    t.status, t.must_change_password, t.created_at,
                    (SELECT COUNT(DISTINCT tc2.class_id) FROM tbl_teacher_class tc2
                     WHERE tc2.teacher_id = t.teacher_id) AS classes_assigned,
                    (SELECT COUNT(*) FROM tbl_grade g
                     INNER JOIN tbl_subject_class scc ON g.subject_class_id = scc.subject_class_id
                     INNER JOIN tbl_teacher_class tc2 ON scc.class_id = tc2.class_id
                     WHERE tc2.teacher_id = t.teacher_id) AS grades_saved,
                    (SELECT COUNT(*) FROM tbl_student_score ss
                     INNER JOIN tbl_activity a ON ss.activity_id = a.activity_id
                     INNER JOIN tbl_grading_component gc ON a.component_id = gc.component_id
                     INNER JOIN tbl_subject_class scc ON gc.subject_id = scc.subject_id
                     INNER JOIN tbl_teacher_class tc2 ON scc.class_id = tc2.class_id
                     WHERE tc2.teacher_id = t.teacher_id) AS scores_entered,
                    (SELECT MAX(g2.updated_at) FROM tbl_grade g2
                     INNER JOIN tbl_subject_class scc ON g2.subject_class_id = scc.subject_class_id
                     INNER JOIN tbl_teacher_class tc2 ON scc.class_id = tc2.class_id
                     WHERE tc2.teacher_id = t.teacher_id) AS last_grade_activity
             FROM tbl_teacher t
             WHERE t.admin_id = ?",
            ['t.teacher_fname', 't.teacher_lname', 't.teacher_email'],
            't.teacher_id ASC',
            'i', [$adminId],
            fn($t) => [
                'teacher_id'          => $t['teacher_id'],
                'teacher_name'        => htmlspecialchars($t['teacher_lname'] . ', ' . $t['teacher_fname']),
                'teacher_email'       => htmlspecialchars($t['teacher_email']),
                'status'              => $t['status'],
                'password_changed'    => (int) $t['must_change_password'] === 0 ? 'Yes' : 'No',
                'account_created'     => $t['created_at'],
                'classes_assigned'    => (int) $t['classes_assigned'],
                'grades_saved'        => (int) $t['grades_saved'],
                'scores_entered'      => (int) $t['scores_entered'],
                'last_grade_activity' => $t['last_grade_activity'] ?? null,
            ],
            '',
            [1 => 't.teacher_lname', 2 => 't.teacher_email', 3 => 't.status', 4 => 't.must_change_password', 5 => 't.created_at', 9 => 'last_grade_activity']
        );
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
