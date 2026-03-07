<?php

class SubjectController
{
    public function index()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: /ewgs/admin');
            exit;
        }

        $classes = AdminModel::getAllClasses($_SESSION['user_id'] ?? null);

        require_once 'views/admin/AdminSubject.php';
    }

    public function getData()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $subjects = AdminModel::getAllSubjects($_SESSION['user_id'] ?? null);

        $data = [];
        $count = 1;
        foreach ($subjects as $subject) {
            $data[] = [
                'count'        => $count++,
                'subject_id'   => $subject['subject_id'],
                'subject_name' => htmlspecialchars($subject['subject_name']),
                'class_name'   => $subject['class_name']
                    ? htmlspecialchars($subject['class_name'] . ' (' . $subject['grade_level'] . ')')
                    : null,
                'teacher_name' => $subject['teacher_name']
                    ? htmlspecialchars($subject['teacher_name'])
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

        $data = [
            'subject_name'             => trim($_POST['subject_name']),
            'class_id'                 => !empty($_POST['class_id']) ? (int) $_POST['class_id'] : null,
            'admin_id'                 => $_SESSION['user_id'] ?? null,
            'written_percentage'       => $_POST['written_percentage']       ?? 0,
            'written_count'            => $_POST['written_count']            ?? 0,
            'written_activity_score'   => $_POST['written_activity_score']   ?? [],
            'performance_percentage'   => $_POST['performance_percentage']   ?? 0,
            'performance_count'        => $_POST['performance_count']        ?? 0,
            'performance_activity_score' => $_POST['performance_activity_score'] ?? [],
            'quarterly_percentage'     => $_POST['quarterly_percentage']     ?? 0,
            'quarterly_count'          => $_POST['quarterly_count']          ?? 0,
            'quarterly_activity_score' => $_POST['quarterly_activity_score'] ?? [],
        ];

        $ok = AdminModel::addSubject($data);

        if ($this->isAjax()) {
            $ok ? $this->jsonResponse(true, 'Subject added successfully.')
                : $this->jsonResponse(false, 'Failed to add subject. Please try again.');
        }

        if ($ok) {
            setFlash('success', 'Subject added successfully.');
        } else {
            setFlash('error', 'Failed to add subject. Please try again.');
        }

        header('Location: /ewgs/admin/subject');
        exit;
    }

    public function edit()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            if ($this->isAjax()) { http_response_code(401); echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }
            header('Location: /ewgs/admin'); exit;
        }
        $id   = (int) ($_POST['subject_id'] ?? 0);
        $name = trim($_POST['subject_name'] ?? '');
        if ($id && $name && AdminModel::updateSubject($id, $name)) {
            $this->jsonResponse(true, 'Subject updated successfully.');
        } else {
            $this->jsonResponse(false, 'Failed to update subject.');
        }
    }

    public function delete()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: /ewgs/admin');
            exit;
        }

        $id     = (int) ($_POST['subject_id'] ?? 0);
        $result = AdminModel::removeSubject($id);

        if ($result === 'fk') {
            if ($this->isAjax()) $this->jsonResponse(false, 'Cannot delete this subject. It has grades linked to it.');
            setFlash('error', 'Cannot delete this subject. It has grades linked to it.');
            header('Location: /ewgs/admin/subject'); exit;
        }

        if ($this->isAjax()) {
            $result ? $this->jsonResponse(true, 'Subject deleted successfully.')
                    : $this->jsonResponse(false, 'Failed to delete subject.');
        }

        $result ? setFlash('success', 'Subject deleted successfully.') : setFlash('error', 'Failed to delete subject.');
        header('Location: /ewgs/admin/subject');
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
