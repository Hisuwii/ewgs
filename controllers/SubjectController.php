<?php

class SubjectController
{
    public function index()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: ' . BASE . '/admin');
            exit;
        }

        $classes = AdminModel::getAllClasses($_SESSION['user_id'] ?? null);

        require_once 'views/admin/AdminSubject.php';
    }

    public function checkDuplicate()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            http_response_code(401); echo json_encode(['exists' => false]); exit;
        }
        $adminId     = $_SESSION['user_id'] ?? null;
        $subjectName = trim($_GET['subject_name'] ?? '');
        $excludeId   = (int) ($_GET['exclude_id'] ?? 0) ?: null;
        if ($subjectName === '') { echo json_encode(['exists' => false]); exit; }
        $conn  = getConnection();
        $sql   = "SELECT COUNT(*) FROM tbl_subject WHERE subject_name = ? AND admin_id = ?";
        $types = "si";
        $params = [$subjectName, $adminId];
        if ($excludeId) { $sql .= " AND subject_id != ?"; $types .= "i"; $params[] = $excludeId; }
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_row()[0] > 0;
        header('Content-Type: application/json');
        echo json_encode(['exists' => $exists]);
        exit;
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
            "SELECT s.subject_id, s.subject_name,
                    c.class_name, c.grade_level,
                    GROUP_CONCAT(CONCAT(t.teacher_fname, ' ', t.teacher_lname) SEPARATOR ', ') AS teacher_name
             FROM tbl_subject s
             LEFT JOIN tbl_subject_class sc ON s.subject_id = sc.subject_id
             LEFT JOIN tbl_class c ON sc.class_id = c.class_id
             LEFT JOIN tbl_teacher_class tc ON c.class_id = tc.class_id
             LEFT JOIN tbl_teacher t ON tc.teacher_id = t.teacher_id
             WHERE s.admin_id = ?",
            ['s.subject_name', 'c.class_name'],
            's.subject_id ASC',
            'i', [$adminId],
            fn($s) => [
                'subject_id'   => $s['subject_id'],
                'subject_name' => htmlspecialchars($s['subject_name']),
                'class_name'   => $s['class_name']
                    ? htmlspecialchars($s['class_name'] . ' (' . $s['grade_level'] . ')')
                    : null,
                'teacher_name' => $s['teacher_name'] ? htmlspecialchars($s['teacher_name']) : null,
            ],
            'GROUP BY s.subject_id, c.class_id',
            [1 => 's.subject_name', 2 => 'c.class_name']
        );
    }

    public function add()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: ' . BASE . '/admin');
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

        header('Location: ' . BASE . '/admin/subject');
        exit;
    }

    public function getEditData()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit;
        }
        $id   = (int) ($_GET['subject_id'] ?? 0);
        $data = $id ? AdminModel::getSubjectForEdit($id) : null;
        header('Content-Type: application/json');
        echo json_encode($data ?: ['error' => 'Not found']);
        exit;
    }

    public function edit()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            if ($this->isAjax()) { http_response_code(401); echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit; }
            header('Location: ' . BASE . '/admin'); exit;
        }
        $id   = (int) ($_POST['subject_id'] ?? 0);
        $name = trim($_POST['subject_name'] ?? '');
        if (!$id || !$name) {
            $this->jsonResponse(false, 'Invalid request.');
        }
        $data = [
            'subject_id'                 => $id,
            'subject_name'               => $name,
            'class_id'                   => !empty($_POST['class_id']) ? (int)$_POST['class_id'] : null,
            'written_percentage'         => $_POST['written_percentage']         ?? 0,
            'written_activity_score'     => $_POST['written_activity_score']     ?? [],
            'performance_percentage'     => $_POST['performance_percentage']     ?? 0,
            'performance_activity_score' => $_POST['performance_activity_score'] ?? [],
            'quarterly_percentage'       => $_POST['quarterly_percentage']       ?? 0,
            'quarterly_activity_score'   => $_POST['quarterly_activity_score']   ?? [],
        ];
        if (AdminModel::updateSubjectFull($data)) {
            $this->jsonResponse(true, 'Subject updated successfully.');
        } else {
            $this->jsonResponse(false, 'Failed to update subject.');
        }
    }

    public function delete()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: ' . BASE . '/admin');
            exit;
        }

        $id     = (int) ($_POST['subject_id'] ?? 0);
        $result = AdminModel::removeSubject($id);

        if ($result === 'fk') {
            if ($this->isAjax()) $this->jsonResponse(false, 'Cannot delete this subject. It has grades linked to it.');
            setFlash('error', 'Cannot delete this subject. It has grades linked to it.');
            header('Location: ' . BASE . '/admin/subject'); exit;
        }

        if ($this->isAjax()) {
            $result ? $this->jsonResponse(true, 'Subject deleted successfully.')
                    : $this->jsonResponse(false, 'Failed to delete subject.');
        }

        $result ? setFlash('success', 'Subject deleted successfully.') : setFlash('error', 'Failed to delete subject.');
        header('Location: ' . BASE . '/admin/subject');
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
