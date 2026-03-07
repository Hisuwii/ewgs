<?php

class TeacherController
{
    public function index()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: /ewgs/admin');
            exit;
        }

        require_once 'views/admin/AdminTeacher.php';
    }

    public function getData()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $teachers = AdminModel::getAllTeachers($_SESSION['user_id'] ?? null);

        $data = [];
        $count = 1;
        foreach ($teachers as $teacher) {
            $data[] = [
                'count'         => $count++,
                'teacher_id'    => $teacher['teacher_id'],
                'teacher_fname' => htmlspecialchars($teacher['teacher_fname']),
                'teacher_lname' => htmlspecialchars($teacher['teacher_lname']),
                'teacher_email' => htmlspecialchars($teacher['teacher_email']),
                'status'        => $teacher['status'] ?? 'Active',
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

        header('Location: /ewgs/admin/teacher');
        exit;
    }

    public function edit()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            if ($this->isAjax()) { http_response_code(401); echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }
            header('Location: /ewgs/admin'); exit;
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
            header('Location: /ewgs/admin');
            exit;
        }

        $id      = (int) ($_POST['teacher_id'] ?? 0);
        $teacher = AdminModel::getTeacherById($id);
        if (!$teacher || $teacher['admin_id'] != ($_SESSION['user_id'] ?? null)) {
            if ($this->isAjax()) $this->jsonResponse(false, 'Unauthorized: you can only delete teachers you created.');
            setFlash('error', 'Unauthorized: you can only delete teachers you created.');
            header('Location: /ewgs/admin/teacher'); exit;
        }
        $result = AdminModel::removeTeacher($id);

        if ($result === 'fk') {
            if ($this->isAjax()) $this->jsonResponse(false, 'Cannot delete this teacher. They are currently assigned to one or more classes.');
            setFlash('error', 'Cannot delete this teacher. They are currently assigned to one or more classes.');
            header('Location: /ewgs/admin/teacher'); exit;
        }

        if ($this->isAjax()) {
            $result ? $this->jsonResponse(true, 'Teacher deleted successfully.')
                    : $this->jsonResponse(false, 'Failed to delete teacher.');
        }

        $result ? setFlash('success', 'Teacher deleted successfully!') : setFlash('error', 'Failed to delete teacher.');
        header('Location: /ewgs/admin/teacher');
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

    public function logs()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: /ewgs/admin');
            exit;
        }
        require_once 'views/admin/AdminTeacherLog.php';
    }

    public function getLogsData()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $logs  = AdminModel::getTeacherActivityLogs($_SESSION['user_id'] ?? null);
        $count = 1;
        $data  = array_map(function ($t) use (&$count) {
            return [
                'count'              => $count++,
                'teacher_id'         => $t['teacher_id'],
                'teacher_name'       => htmlspecialchars($t['teacher_lname'] . ', ' . $t['teacher_fname']),
                'teacher_email'      => htmlspecialchars($t['teacher_email']),
                'status'             => $t['status'],
                'password_changed'   => (int) $t['must_change_password'] === 0 ? 'Yes' : 'No',
                'account_created'    => $t['created_at'],
                'classes_assigned'   => (int) $t['classes_assigned'],
                'grades_saved'       => (int) $t['grades_saved'],
                'scores_entered'     => (int) $t['scores_entered'],
                'last_grade_activity'=> $t['last_grade_activity'] ?? null,
            ];
        }, $logs);
        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
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
