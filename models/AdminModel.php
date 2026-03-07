<?php

class AdminModel {

    // Register a new admin account
    public static function addAdmin($username, $hashedPassword) {
        $conn = getConnection();
        $stmt = $conn->prepare("INSERT INTO tbl_admin (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashedPassword);
        return $stmt->execute();
    }

    // Check if admin username is already taken
    public static function isAdminUsernameTaken($username) {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_row()[0] > 0;
    }

    // Generate random 8-character password
    public static function generatePassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $password;
    }

    // Add new teacher with auto-generated password (scoped to the creating admin)
    public static function addTeacher($data) {
        $conn = getConnection();

        // Generate 8-character random password and hash it for storage
        $password = self::generatePassword(8);
        $hashed   = password_hash($password, PASSWORD_BCRYPT);

        $adminId  = $data['admin_id'] ?? null;
        $stmt = $conn->prepare("INSERT INTO tbl_teacher (teacher_fname, teacher_lname, teacher_email, teacher_password, admin_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $data['teacher_fname'], $data['teacher_lname'], $data['teacher_email'], $hashed, $adminId);

        if ($stmt->execute()) {
            return ['success' => true, 'password' => $password]; // plain returned once for display/email
        }
        return ['success' => false];
    }

    // Get teachers — scoped to the given admin (null = all)
    public static function getAllTeachers($adminId = null) {
        $conn = getConnection();
        if ($adminId !== null) {
            $stmt = $conn->prepare("SELECT * FROM tbl_teacher WHERE admin_id = ? ORDER BY teacher_id ASC");
            $stmt->bind_param("i", $adminId);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        $result = $conn->query("SELECT * FROM tbl_teacher ORDER BY teacher_id ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get single teacher by ID
    public static function getTeacherById($id) {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT * FROM tbl_teacher WHERE teacher_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Update teacher
    public static function updateTeacher($id, $data) {
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE tbl_teacher SET teacher_fname = ?, teacher_lname = ?, teacher_email = ? WHERE teacher_id = ?");
        $stmt->bind_param("sssi", $data['teacher_fname'], $data['teacher_lname'], $data['teacher_email'], $id);
        return $stmt->execute();
    }

    // Update teacher password (hashes internally) and clear the must-change flag
    public static function updateTeacherPassword($id, $plainPassword) {
        $conn   = getConnection();
        $hashed = password_hash($plainPassword, PASSWORD_BCRYPT);
        $stmt   = $conn->prepare("UPDATE tbl_teacher SET teacher_password = ?, must_change_password = 0 WHERE teacher_id = ?");
        $stmt->bind_param("si", $hashed, $id);
        return $stmt->execute();
    }

    // Reset teacher password — generates a new one, hashes and saves it, forces change on next login
    // Returns the plain password for one-time display/email, or false on failure
    public static function resetTeacherPassword($id) {
        $conn     = getConnection();
        $password = self::generatePassword(8);
        $hashed   = password_hash($password, PASSWORD_BCRYPT);
        $stmt     = $conn->prepare("UPDATE tbl_teacher SET teacher_password = ?, must_change_password = 1 WHERE teacher_id = ?");
        $stmt->bind_param("si", $hashed, $id);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            return $password;
        }
        return false;
    }

    // Remove teacher
    public static function removeTeacher($id) {
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM tbl_teacher WHERE teacher_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt->errno === 1451) return 'fk';
        return $stmt->affected_rows > 0;
    }

    // Get all classes with their assigned teacher names (via tbl_teacher_class), scoped to admin
    public static function getAllClasses($adminId = null) {
        $conn = getConnection();
        if ($adminId !== null) {
            $stmt = $conn->prepare("
                SELECT c.*,
                       MIN(tc.teacher_id) AS teacher_id,
                       GROUP_CONCAT(CONCAT(t.teacher_fname, ' ', t.teacher_lname) SEPARATOR ', ') AS teacher_name
                FROM tbl_class c
                LEFT JOIN tbl_teacher_class tc ON c.class_id = tc.class_id
                LEFT JOIN tbl_teacher t ON tc.teacher_id = t.teacher_id
                WHERE c.admin_id = ?
                GROUP BY c.class_id
                ORDER BY c.class_id ASC
            ");
            $stmt->bind_param("i", $adminId);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        $result = $conn->query("
            SELECT c.*,
                   MIN(tc.teacher_id) AS teacher_id,
                   GROUP_CONCAT(CONCAT(t.teacher_fname, ' ', t.teacher_lname) SEPARATOR ', ') AS teacher_name
            FROM tbl_class c
            LEFT JOIN tbl_teacher_class tc ON c.class_id = tc.class_id
            LEFT JOIN tbl_teacher t ON tc.teacher_id = t.teacher_id
            GROUP BY c.class_id
            ORDER BY c.class_id ASC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Check if a class with the same name, grade, and school year already exists (scoped to admin)
    public static function isClassDuplicate($className, $gradeLevel, $schoolYear, $excludeClassId = null, $adminId = null) {
        $conn   = getConnection();
        $sql    = "SELECT COUNT(*) FROM tbl_class WHERE class_name = ? AND grade_level = ? AND school_year = ?";
        $types  = "sss";
        $params = [$className, $gradeLevel, $schoolYear];
        if ($excludeClassId) { $sql .= " AND class_id != ?";  $types .= "i"; $params[] = $excludeClassId; }
        if ($adminId !== null) { $sql .= " AND admin_id = ?"; $types .= "i"; $params[] = $adminId; }
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_row()[0] > 0;
    }

    // Add new class (teacher assigned separately via tbl_teacher_class)
    public static function addClass($data) {
        $conn    = getConnection();
        $adminId = $data['admin_id'] ?? null;
        $stmt    = $conn->prepare("INSERT INTO tbl_class (class_name, grade_level, school_year, admin_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $data['class_name'], $data['grade_level'], $data['school_year'], $adminId);
        return $stmt->execute();
    }

    // Update class
    public static function updateClass($id, $data) {
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE tbl_class SET class_name=?, grade_level=?, school_year=? WHERE class_id=?");
        $stmt->bind_param("sssi", $data['class_name'], $data['grade_level'], $data['school_year'], $id);
        return $stmt->execute();
    }

    // Remove class
    public static function removeClass($id) {
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM tbl_class WHERE class_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt->errno === 1451) return 'fk';
        return $stmt->affected_rows > 0;
    }

    // Get all subjects with class and teacher info (via tbl_teacher_class), scoped to admin
    public static function getAllSubjects($adminId = null) {
        $conn = getConnection();
        if ($adminId !== null) {
            $stmt = $conn->prepare("
                SELECT s.subject_id, s.subject_name,
                       c.class_name, c.grade_level,
                       GROUP_CONCAT(CONCAT(t.teacher_fname, ' ', t.teacher_lname) SEPARATOR ', ') AS teacher_name
                FROM tbl_subject s
                LEFT JOIN tbl_subject_class sc ON s.subject_id = sc.subject_id
                LEFT JOIN tbl_class c ON sc.class_id = c.class_id
                LEFT JOIN tbl_teacher_class tc ON c.class_id = tc.class_id
                LEFT JOIN tbl_teacher t ON tc.teacher_id = t.teacher_id
                WHERE s.admin_id = ?
                GROUP BY s.subject_id, c.class_id
                ORDER BY s.subject_id ASC
            ");
            $stmt->bind_param("i", $adminId);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        $result = $conn->query("
            SELECT s.subject_id, s.subject_name,
                   c.class_name, c.grade_level,
                   GROUP_CONCAT(CONCAT(t.teacher_fname, ' ', t.teacher_lname) SEPARATOR ', ') AS teacher_name
            FROM tbl_subject s
            LEFT JOIN tbl_subject_class sc ON s.subject_id = sc.subject_id
            LEFT JOIN tbl_class c ON sc.class_id = c.class_id
            LEFT JOIN tbl_teacher_class tc ON c.class_id = tc.class_id
            LEFT JOIN tbl_teacher t ON tc.teacher_id = t.teacher_id
            GROUP BY s.subject_id, c.class_id
            ORDER BY s.subject_id ASC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Add new subject, link to class, and save grading components + activities
    public static function addSubject($data) {
        $conn    = getConnection();
        $adminId = $data['admin_id'] ?? null;

        // Insert subject
        $stmt = $conn->prepare("INSERT INTO tbl_subject (subject_name, admin_id) VALUES (?, ?)");
        $stmt->bind_param("si", $data['subject_name'], $adminId);
        if (!$stmt->execute()) {
            return false;
        }
        $subjectId = $conn->insert_id;

        // Link to class
        if (!empty($data['class_id'])) {
            $stmt2 = $conn->prepare("INSERT INTO tbl_subject_class (subject_id, class_id) VALUES (?, ?)");
            $stmt2->bind_param("ii", $subjectId, $data['class_id']);
            $stmt2->execute();
        }

        // Insert grading components and their activities
        $components = [
            [
                'name'   => 'Written Work',
                'pct'    => (float) $data['written_percentage'] / 100,
                'count'  => (int)   $data['written_count'],
                'scores' => $data['written_activity_score'] ?? [],
            ],
            [
                'name'   => 'Performance Task',
                'pct'    => (float) $data['performance_percentage'] / 100,
                'count'  => (int)   $data['performance_count'],
                'scores' => $data['performance_activity_score'] ?? [],
            ],
            [
                'name'   => 'Quarterly Exam',
                'pct'    => (float) $data['quarterly_percentage'] / 100,
                'count'  => (int)   $data['quarterly_count'],
                'scores' => $data['quarterly_activity_score'] ?? [],
            ],
        ];

        foreach ($components as $comp) {
            $stmtComp = $conn->prepare(
                "INSERT INTO tbl_grading_component (subject_id, component_name, percentage, activity_count)
                 VALUES (?, ?, ?, ?)"
            );
            $stmtComp->bind_param("isdi", $subjectId, $comp['name'], $comp['pct'], $comp['count']);
            $stmtComp->execute();
            $componentId = $conn->insert_id;

            $order = 1;
            foreach ($comp['scores'] as $score) {
                $label    = ($comp['name'] === 'Quarterly Exam') ? 'Exam ' . $order : 'Activity ' . $order;
                $scoreVal = (float) $score;
                $stmtAct  = $conn->prepare(
                    "INSERT INTO tbl_activity (component_id, activity_name, perfect_score, activity_order)
                     VALUES (?, ?, ?, ?)"
                );
                $stmtAct->bind_param("isdi", $componentId, $label, $scoreVal, $order);
                $stmtAct->execute();
                $order++;
            }
        }

        return true;
    }

    // Update subject name only (components affect grading integrity)
    public static function updateSubject($id, $name) {
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE tbl_subject SET subject_name=? WHERE subject_id=?");
        $stmt->bind_param("si", $name, $id);
        return $stmt->execute();
    }

    // Remove subject
    public static function removeSubject($id) {
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM tbl_subject WHERE subject_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt->errno === 1451) return 'fk';
        return $stmt->affected_rows > 0;
    }

    // Add a new student
    public static function addStudent($data) {
        $conn    = getConnection();
        $fname   = $data['student_fname'] ?? '';
        $lname   = $data['student_lname'] ?? '';
        $lrn     = $data['lrn']           ?? '';
        $gender  = $data['gender']        ?? '';
        $bdate   = !empty($data['birth_date']) ? $data['birth_date'] : null;
        $adminId = $data['admin_id'] ?? null;
        $stmt = $conn->prepare("INSERT IGNORE INTO tbl_student (student_fname, student_lname, student_lrn, student_gender, birth_date, admin_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $fname, $lname, $lrn, $gender, $bdate, $adminId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    // Update a student
    public static function updateStudent($data) {
        $conn   = getConnection();
        $id     = (int) ($data['student_id']   ?? 0);
        $fname  = $data['student_fname'] ?? '';
        $lname  = $data['student_lname'] ?? '';
        $lrn    = $data['lrn']           ?? '';
        $gender = $data['gender']        ?? '';
        $bdate  = !empty($data['birth_date']) ? $data['birth_date'] : null;
        $stmt = $conn->prepare("UPDATE tbl_student SET student_fname=?, student_lname=?, student_lrn=?, student_gender=?, birth_date=? WHERE student_id=?");
        $stmt->bind_param("sssssi", $fname, $lname, $lrn, $gender, $bdate, $id);
        return $stmt->execute();
    }

    // Remove a student
    public static function removeStudent($id) {
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM tbl_student WHERE student_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt->errno === 1451) return 'fk';
        return $stmt->affected_rows > 0;
    }

    // Get all students (alias renamed columns back to lrn/gender for compatibility), scoped to admin
    public static function getAllStudents($adminId = null) {
        $conn = getConnection();
        if ($adminId !== null) {
            $stmt = $conn->prepare("SELECT student_id, student_fname, student_lname, student_lrn AS lrn, student_gender AS gender, birth_date FROM tbl_student WHERE admin_id = ? ORDER BY student_lname ASC, student_fname ASC");
            $stmt->bind_param("i", $adminId);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        $result = $conn->query("SELECT student_id, student_fname, student_lname, student_lrn AS lrn, student_gender AS gender, birth_date FROM tbl_student ORDER BY student_lname ASC, student_fname ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // ============================================================
    // DASHBOARD COUNTS
    // ============================================================

    public static function countTeachers($adminId = null) {
        $conn = getConnection();
        if ($adminId !== null) {
            $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM tbl_teacher WHERE admin_id = ?");
            $stmt->bind_param("i", $adminId); $stmt->execute();
            return (int) $stmt->get_result()->fetch_assoc()['cnt'];
        }
        return (int) $conn->query("SELECT COUNT(*) AS cnt FROM tbl_teacher")->fetch_assoc()['cnt'];
    }

    public static function countStudents($adminId = null) {
        $conn = getConnection();
        if ($adminId !== null) {
            $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM tbl_student WHERE admin_id = ?");
            $stmt->bind_param("i", $adminId); $stmt->execute();
            return (int) $stmt->get_result()->fetch_assoc()['cnt'];
        }
        return (int) $conn->query("SELECT COUNT(*) AS cnt FROM tbl_student")->fetch_assoc()['cnt'];
    }

    public static function countClasses($adminId = null) {
        $conn = getConnection();
        if ($adminId !== null) {
            $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM tbl_class WHERE admin_id = ?");
            $stmt->bind_param("i", $adminId); $stmt->execute();
            return (int) $stmt->get_result()->fetch_assoc()['cnt'];
        }
        return (int) $conn->query("SELECT COUNT(*) AS cnt FROM tbl_class")->fetch_assoc()['cnt'];
    }

    public static function countSubjects($adminId = null) {
        $conn = getConnection();
        if ($adminId !== null) {
            $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM tbl_subject WHERE admin_id = ?");
            $stmt->bind_param("i", $adminId); $stmt->execute();
            return (int) $stmt->get_result()->fetch_assoc()['cnt'];
        }
        return (int) $conn->query("SELECT COUNT(*) AS cnt FROM tbl_subject")->fetch_assoc()['cnt'];
    }

    public static function getAdminActivity($limit = 5, $adminId = null) {
        $conn  = getConnection();
        $items = [];
        $where = $adminId !== null ? " WHERE admin_id = {$adminId}" : "";

        $r1 = $conn->query("SELECT student_id AS id, CONCAT('Student added: ', student_fname, ' ', student_lname) AS label FROM tbl_student{$where} ORDER BY student_id DESC LIMIT {$limit}");
        while ($row = $r1->fetch_assoc()) {
            $items[] = ['label' => $row['label'], 'icon' => 'bi-person-plus', 'id' => (int)$row['id']];
        }

        $r2 = $conn->query("SELECT teacher_id AS id, CONCAT('Teacher added: ', teacher_fname, ' ', teacher_lname) AS label FROM tbl_teacher{$where} ORDER BY teacher_id DESC LIMIT {$limit}");
        while ($row = $r2->fetch_assoc()) {
            $items[] = ['label' => $row['label'], 'icon' => 'bi-person-badge', 'id' => (int)$row['id']];
        }

        $r3 = $conn->query("SELECT class_id AS id, CONCAT('Class created: ', class_name) AS label FROM tbl_class{$where} ORDER BY class_id DESC LIMIT {$limit}");
        while ($row = $r3->fetch_assoc()) {
            $items[] = ['label' => $row['label'], 'icon' => 'bi-door-open', 'id' => (int)$row['id']];
        }

        $r4 = $conn->query("SELECT subject_id AS id, CONCAT('Subject added: ', subject_name) AS label FROM tbl_subject{$where} ORDER BY subject_id DESC LIMIT {$limit}");
        while ($row = $r4->fetch_assoc()) {
            $items[] = ['label' => $row['label'], 'icon' => 'bi-journal-bookmark', 'id' => (int)$row['id']];
        }

        usort($items, fn($a, $b) => $b['id'] <=> $a['id']);
        return array_slice($items, 0, $limit);
    }

    public static function getTeacherActivity($adminId, $limit = 15) {
        $conn = getConnection();
        $stmt = $conn->prepare("
            SELECT
                CONCAT(t.teacher_fname, ' ', t.teacher_lname) AS teacher_name,
                sub.subject_name,
                c.class_name,
                c.grade_level,
                g.quarter,
                COUNT(DISTINCT g.student_id) AS student_count,
                MAX(g.updated_at) AS last_saved
            FROM tbl_grade g
            INNER JOIN tbl_subject_class sc ON g.subject_class_id = sc.subject_class_id
            INNER JOIN tbl_class c          ON sc.class_id = c.class_id
            INNER JOIN tbl_subject sub      ON sc.subject_id = sub.subject_id
            INNER JOIN tbl_teacher_class tc ON c.class_id = tc.class_id
            INNER JOIN tbl_teacher t        ON tc.teacher_id = t.teacher_id
            WHERE t.admin_id = ?
            GROUP BY t.teacher_id, sub.subject_id, c.class_id, g.quarter
            ORDER BY last_saved DESC
            LIMIT ?
        ");
        $stmt->bind_param("ii", $adminId, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // ============================================================
    // LINK METHODS
    // ============================================================

    // Assign a teacher to a class (via junction table)
    public static function linkTeacherToClass($classId, $teacherId) {
        $conn = getConnection();
        $stmt = $conn->prepare("INSERT IGNORE INTO tbl_teacher_class (teacher_id, class_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $teacherId, $classId);
        $stmt->execute();
        if ($stmt->affected_rows === 0) return 'duplicate';
        return true;
    }

    // Remove a teacher from a class (or all teachers if no teacherId given)
    public static function unlinkTeacherFromClass($classId, $teacherId = null) {
        $conn = getConnection();
        if ($teacherId) {
            $stmt = $conn->prepare("DELETE FROM tbl_teacher_class WHERE class_id = ? AND teacher_id = ?");
            $stmt->bind_param("ii", $classId, $teacherId);
        } else {
            $stmt = $conn->prepare("DELETE FROM tbl_teacher_class WHERE class_id = ?");
            $stmt->bind_param("i", $classId);
        }
        return $stmt->execute();
    }

    // Get all teacher-class links, scoped to admin
    public static function getTeacherClassLinks($adminId = null) {
        $conn = getConnection();
        if ($adminId !== null) {
            $stmt = $conn->prepare("
                SELECT tc.class_id, c.class_name, c.grade_level, c.school_year,
                       t.teacher_id,
                       CONCAT(t.teacher_fname, ' ', t.teacher_lname) AS teacher_name
                FROM tbl_teacher_class tc
                INNER JOIN tbl_class c ON tc.class_id = c.class_id
                INNER JOIN tbl_teacher t ON tc.teacher_id = t.teacher_id
                WHERE c.admin_id = ?
                ORDER BY c.grade_level ASC, c.class_name ASC
            ");
            $stmt->bind_param("i", $adminId);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        $result = $conn->query("
            SELECT tc.class_id, c.class_name, c.grade_level, c.school_year,
                   t.teacher_id,
                   CONCAT(t.teacher_fname, ' ', t.teacher_lname) AS teacher_name
            FROM tbl_teacher_class tc
            INNER JOIN tbl_class c ON tc.class_id = c.class_id
            INNER JOIN tbl_teacher t ON tc.teacher_id = t.teacher_id
            ORDER BY c.grade_level ASC, c.class_name ASC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Link a subject to a class (prevents duplicate)
    public static function linkSubjectToClass($subjectId, $classId) {
        $conn = getConnection();
        $check = $conn->prepare("SELECT COUNT(*) FROM tbl_subject_class WHERE subject_id = ? AND class_id = ?");
        $check->bind_param("ii", $subjectId, $classId);
        $check->execute();
        $check->bind_result($count);
        $check->fetch();
        $check->close();
        if ($count > 0) return 'duplicate';
        $stmt = $conn->prepare("INSERT INTO tbl_subject_class (subject_id, class_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $subjectId, $classId);
        return $stmt->execute();
    }

    // Remove a subject from a class
    public static function unlinkSubjectFromClass($subjectId, $classId) {
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM tbl_subject_class WHERE subject_id = ? AND class_id = ?");
        $stmt->bind_param("ii", $subjectId, $classId);
        return $stmt->execute();
    }

    // Get all subject-class links, scoped to admin
    public static function getSubjectClassLinks($adminId = null) {
        $conn = getConnection();
        if ($adminId !== null) {
            $stmt = $conn->prepare("
                SELECT sc.subject_id, sc.class_id,
                       s.subject_name,
                       c.class_name, c.grade_level, c.school_year
                FROM tbl_subject_class sc
                INNER JOIN tbl_subject s ON sc.subject_id = s.subject_id
                INNER JOIN tbl_class c ON sc.class_id = c.class_id
                WHERE c.admin_id = ?
                ORDER BY c.grade_level ASC, c.class_name ASC, s.subject_name ASC
            ");
            $stmt->bind_param("i", $adminId);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        $result = $conn->query("
            SELECT sc.subject_id, sc.class_id,
                   s.subject_name,
                   c.class_name, c.grade_level, c.school_year
            FROM tbl_subject_class sc
            INNER JOIN tbl_subject s ON sc.subject_id = s.subject_id
            INNER JOIN tbl_class c ON sc.class_id = c.class_id
            ORDER BY c.grade_level ASC, c.class_name ASC, s.subject_name ASC
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Enroll a student in a class — returns 'already_enrolled', 'duplicate', true, or false
    public static function enrollStudentInClass($studentId, $classId) {
        $conn = getConnection();

        // Check current class assignment
        $check = $conn->prepare("SELECT class_id FROM tbl_student WHERE student_id = ?");
        $check->bind_param("i", $studentId);
        $check->execute();
        $row = $check->get_result()->fetch_row();
        $check->close();
        if ($row && $row[0] !== null) {
            return $row[0] == $classId ? 'duplicate' : 'already_enrolled';
        }

        $stmt = $conn->prepare("UPDATE tbl_student SET class_id = ? WHERE student_id = ?");
        $stmt->bind_param("ii", $classId, $studentId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    // Remove a student from a class (clear assignment)
    public static function removeStudentFromClass($studentId, $classId) {
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE tbl_student SET class_id = NULL WHERE student_id = ? AND class_id = ?");
        $stmt->bind_param("ii", $studentId, $classId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    // Get student-class links, optionally filtered by class and/or admin
    public static function getStudentClassLinks($classId = null, $adminId = null) {
        $conn   = getConnection();
        $sql    = "
            SELECT s.student_id, s.class_id,
                   CONCAT(s.student_lname, ', ', s.student_fname) AS student_name,
                   s.student_lrn AS lrn, s.student_gender AS gender,
                   c.class_name, c.grade_level, c.school_year
            FROM tbl_student s
            INNER JOIN tbl_class c ON s.class_id = c.class_id
            WHERE 1=1";
        $types  = "";
        $params = [];
        if ($classId)          { $sql .= " AND s.class_id = ?";  $types .= "i"; $params[] = $classId; }
        if ($adminId !== null) { $sql .= " AND c.admin_id = ?";  $types .= "i"; $params[] = $adminId; }
        $sql .= $classId
            ? " ORDER BY s.student_lname ASC, s.student_fname ASC"
            : " ORDER BY c.grade_level ASC, c.class_name ASC, s.student_lname ASC";
        if ($types) {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // Get students not yet assigned to any class, scoped to admin
    public static function getUnenrolledStudents($classId, $adminId = null) {
        $conn   = getConnection();
        $sql    = "
            SELECT s.student_id,
                   CONCAT(s.student_lname, ', ', s.student_fname) AS student_name,
                   s.student_lrn AS lrn, s.student_gender AS gender
            FROM tbl_student s
            WHERE s.class_id IS NULL";
        $types  = "";
        $params = [];
        if ($adminId !== null) { $sql .= " AND s.admin_id = ?"; $types .= "i"; $params[] = $adminId; }
        $sql .= " ORDER BY s.student_lname ASC, s.student_fname ASC";
        if ($types) {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // ============================================================
    // TEACHER-SIDE (RBAC)
    // ============================================================

    // Get all classes assigned to a specific teacher, with enrolled student count
    public static function getClassesByTeacher($teacherId) {
        $conn = getConnection();
        $stmt = $conn->prepare("
            SELECT c.class_id, c.class_name, c.grade_level, c.school_year,
                   COUNT(s.student_id) AS student_count
            FROM tbl_class c
            INNER JOIN tbl_teacher_class tc ON c.class_id = tc.class_id
            LEFT JOIN tbl_student s ON s.class_id = c.class_id
            WHERE tc.teacher_id = ?
            GROUP BY c.class_id
            ORDER BY c.grade_level ASC, c.class_name ASC
        ");
        $stmt->bind_param("i", $teacherId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Verify a class is assigned to a teacher (RBAC)
    public static function teacherOwnsClass($teacherId, $classId) {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_teacher_class WHERE class_id = ? AND teacher_id = ?");
        $stmt->bind_param("ii", $classId, $teacherId);
        $stmt->execute();
        return (int) $stmt->get_result()->fetch_row()[0] > 0;
    }

    // Get a single class by ID
    public static function getClassById($classId) {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT * FROM tbl_class WHERE class_id = ?");
        $stmt->bind_param("i", $classId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Get students enrolled in a class
    public static function getStudentsByClass($classId) {
        $conn = getConnection();
        $stmt = $conn->prepare("
            SELECT s.student_id, s.student_fname, s.student_lname,
                   s.student_lrn AS lrn, s.student_gender AS gender
            FROM tbl_student s
            WHERE s.class_id = ?
            ORDER BY s.student_lname ASC, s.student_fname ASC
        ");
        $stmt->bind_param("i", $classId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get subjects linked to a specific class
    public static function getSubjectsByClass($classId) {
        $conn = getConnection();
        $stmt = $conn->prepare("
            SELECT s.subject_id, s.subject_name
            FROM tbl_subject s
            INNER JOIN tbl_subject_class sc ON s.subject_id = sc.subject_id
            WHERE sc.class_id = ?
            ORDER BY s.subject_name ASC
        ");
        $stmt->bind_param("i", $classId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Look up subject_class_id from tbl_subject_class
    private static function getSubjectClassId($subjectId, $classId) {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT subject_class_id FROM tbl_subject_class WHERE subject_id = ? AND class_id = ?");
        $stmt->bind_param("ii", $subjectId, $classId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_row();
        return $row ? (int) $row[0] : null;
    }

    // Get existing grades for a class/subject/quarter, keyed by student_id
    public static function getExistingGrades($classId, $subjectId, $quarter) {
        $conn = getConnection();
        $scId = self::getSubjectClassId($subjectId, $classId);
        if (!$scId) return [];
        $stmt = $conn->prepare("
            SELECT student_id, written_work, performance_task, quarterly_exam, final_grade
            FROM tbl_grade
            WHERE subject_class_id = ? AND quarter = ?
        ");
        $stmt->bind_param("is", $scId, $quarter);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $result = [];
        foreach ($rows as $r) {
            $result[$r['student_id']] = $r;
        }
        return $result;
    }

    // Insert or update one student's grade record (uses tbl_grade + subject_class_id)
    public static function saveGrade($studentId, $subjectId, $classId, $quarter, $ww, $pt, $qe, $final) {
        $conn = getConnection();
        $scId = self::getSubjectClassId($subjectId, $classId);
        if (!$scId) return false;
        $stmt = $conn->prepare("
            INSERT INTO tbl_grade (student_id, subject_class_id, quarter, written_work, performance_task, quarterly_exam, final_grade)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE written_work=VALUES(written_work), performance_task=VALUES(performance_task),
                                    quarterly_exam=VALUES(quarterly_exam), final_grade=VALUES(final_grade)
        ");
        $stmt->bind_param("iisdddd", $studentId, $scId, $quarter, $ww, $pt, $qe, $final);
        return $stmt->execute();
    }

    // Get student count, subject count, and grade entry count per class for a teacher
    public static function getManageGradesStats($teacherId) {
        $conn = getConnection();
        $stmt = $conn->prepare("
            SELECT c.class_id,
                   (SELECT COUNT(*) FROM tbl_student WHERE class_id = c.class_id) AS student_count,
                   (SELECT COUNT(*) FROM tbl_subject_class  WHERE class_id = c.class_id) AS subject_count,
                   (SELECT COUNT(*) FROM tbl_grade g
                    INNER JOIN tbl_subject_class scc ON g.subject_class_id = scc.subject_class_id
                    WHERE scc.class_id = c.class_id) AS grade_count
            FROM tbl_class c
            INNER JOIN tbl_teacher_class tc ON c.class_id = tc.class_id
            WHERE tc.teacher_id = ?
        ");
        $stmt->bind_param("i", $teacherId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get grading structure: components + activities for a subject
    public static function getGradingStructure($subjectId) {
        $conn = getConnection();
        $stmt = $conn->prepare("
            SELECT component_id, component_name, percentage, activity_count
            FROM tbl_grading_component
            WHERE subject_id = ?
            ORDER BY FIELD(component_name, 'Written Work', 'Performance Task', 'Quarterly Exam')
        ");
        $stmt->bind_param("i", $subjectId);
        $stmt->execute();
        $components = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($components as &$comp) {
            $stmt2 = $conn->prepare("
                SELECT activity_id, activity_name, perfect_score, activity_order
                FROM tbl_activity
                WHERE component_id = ?
                ORDER BY activity_order ASC
            ");
            $stmt2->bind_param("i", $comp['component_id']);
            $stmt2->execute();
            $comp['activities'] = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return $components;
    }

    // Get per-activity scores for students in a class/subject/quarter
    // Returns: [ student_id => [ activity_id => score ] ]
    public static function getActivityScores($classId, $subjectId, $quarter) {
        $conn = getConnection();
        $stmt = $conn->prepare("
            SELECT ss.student_id, ss.activity_id, ss.score
            FROM tbl_student_score ss
            INNER JOIN tbl_activity a           ON ss.activity_id  = a.activity_id
            INNER JOIN tbl_grading_component gc ON a.component_id  = gc.component_id
            INNER JOIN tbl_student s            ON ss.student_id   = s.student_id
            WHERE gc.subject_id = ? AND ss.quarter = ? AND s.class_id = ?
        ");
        $stmt->bind_param("isi", $subjectId, $quarter, $classId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $result = [];
        foreach ($rows as $r) {
            $result[$r['student_id']][$r['activity_id']] = (float) $r['score'];
        }
        return $result;
    }

    // Insert or update one per-activity score record
    public static function saveActivityScore($studentId, $activityId, $quarter, $schoolYear, $score) {
        $conn = getConnection();
        $check = $conn->prepare("SELECT score_id FROM tbl_student_score WHERE student_id=? AND activity_id=? AND quarter=? AND school_year=?");
        $check->bind_param("iiss", $studentId, $activityId, $quarter, $schoolYear);
        $check->execute();
        $existing = $check->get_result()->fetch_assoc();

        if ($existing) {
            $stmt = $conn->prepare("UPDATE tbl_student_score SET score=? WHERE score_id=?");
            $stmt->bind_param("di", $score, $existing['score_id']);
        } else {
            $stmt = $conn->prepare("INSERT INTO tbl_student_score (student_id, activity_id, quarter, school_year, score) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iissd", $studentId, $activityId, $quarter, $schoolYear, $score);
        }
        return $stmt->execute();
    }

    // Count subjects linked to teacher's classes
    public static function countSubjectsForTeacher($teacherId) {
        $conn = getConnection();
        $stmt = $conn->prepare("
            SELECT COUNT(DISTINCT sc.subject_id)
            FROM tbl_subject_class sc
            INNER JOIN tbl_teacher_class tc ON sc.class_id = tc.class_id
            WHERE tc.teacher_id = ?
        ");
        $stmt->bind_param("i", $teacherId);
        $stmt->execute();
        return (int) $stmt->get_result()->fetch_row()[0];
    }

    // Get teacher activity log summary scoped to admin
    public static function getTeacherActivityLogs($adminId) {
        $conn = getConnection();
        $stmt = $conn->prepare("
            SELECT
                t.teacher_id,
                t.teacher_fname,
                t.teacher_lname,
                t.teacher_email,
                t.status,
                t.must_change_password,
                t.created_at,
                (SELECT COUNT(DISTINCT tc2.class_id)
                 FROM tbl_teacher_class tc2
                 WHERE tc2.teacher_id = t.teacher_id) AS classes_assigned,
                (SELECT COUNT(*)
                 FROM tbl_grade g
                 INNER JOIN tbl_subject_class scc ON g.subject_class_id = scc.subject_class_id
                 INNER JOIN tbl_teacher_class tc2 ON scc.class_id = tc2.class_id
                 WHERE tc2.teacher_id = t.teacher_id) AS grades_saved,
                (SELECT COUNT(*)
                 FROM tbl_student_score ss
                 INNER JOIN tbl_activity a ON ss.activity_id = a.activity_id
                 INNER JOIN tbl_grading_component gc ON a.component_id = gc.component_id
                 INNER JOIN tbl_subject_class scc ON gc.subject_id = scc.subject_id
                 INNER JOIN tbl_teacher_class tc2 ON scc.class_id = tc2.class_id
                 WHERE tc2.teacher_id = t.teacher_id) AS scores_entered,
                (SELECT MAX(g2.updated_at)
                 FROM tbl_grade g2
                 INNER JOIN tbl_subject_class scc ON g2.subject_class_id = scc.subject_class_id
                 INNER JOIN tbl_teacher_class tc2 ON scc.class_id = tc2.class_id
                 WHERE tc2.teacher_id = t.teacher_id) AS last_grade_activity
            FROM tbl_teacher t
            WHERE t.admin_id = ?
            ORDER BY t.teacher_lname ASC, t.teacher_fname ASC
        ");
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Toggle teacher Active/Inactive status; returns the new status string
    public static function toggleTeacherStatus($id) {
        $conn = getConnection();
        $stmt = $conn->prepare("UPDATE tbl_teacher SET status = IF(status='Active','Inactive','Active') WHERE teacher_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt2 = $conn->prepare("SELECT status FROM tbl_teacher WHERE teacher_id = ?");
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        return $stmt2->get_result()->fetch_row()[0] ?? 'Active';
    }

    // ============================================================
    // REPORTS
    // ============================================================

    // Section performance: average final grade per subject for a class/quarter
    public static function getClassGradeReport($classId, $quarter) {
        $conn = getConnection();
        $stmt = $conn->prepare("
            SELECT sub.subject_name,
                   ROUND(AVG(g.final_grade), 2)  AS avg_grade,
                   COUNT(g.grade_id)              AS total_graded,
                   SUM(CASE WHEN g.final_grade >= 75 THEN 1 ELSE 0 END) AS passed,
                   SUM(CASE WHEN g.final_grade < 75  THEN 1 ELSE 0 END) AS failed
            FROM tbl_grade g
            INNER JOIN tbl_subject_class sc ON g.subject_class_id = sc.subject_class_id
            INNER JOIN tbl_subject sub      ON sc.subject_id = sub.subject_id
            WHERE sc.class_id = ? AND g.quarter = ?
            GROUP BY sub.subject_id, sub.subject_name
            ORDER BY sub.subject_name ASC
        ");
        $stmt->bind_param("is", $classId, $quarter);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Student performance: grades per subject for one student in a class/quarter
    // Also includes component weights from tbl_grading_component for grade calculation display
    public static function getStudentGradeReport($studentId, $classId, $quarter) {
        $conn = getConnection();
        $stmt = $conn->prepare("
            SELECT sub.subject_name,
                   g.written_work, g.performance_task, g.quarterly_exam, g.final_grade,
                   COALESCE(MAX(CASE WHEN gc.component_name = 'Written Work'     THEN gc.percentage END), 0.25) AS ww_weight,
                   COALESCE(MAX(CASE WHEN gc.component_name = 'Performance Task' THEN gc.percentage END), 0.50) AS pt_weight,
                   COALESCE(MAX(CASE WHEN gc.component_name = 'Quarterly Exam'   THEN gc.percentage END), 0.25) AS qe_weight
            FROM tbl_grade g
            INNER JOIN tbl_subject_class sc  ON g.subject_class_id = sc.subject_class_id
            INNER JOIN tbl_subject sub       ON sc.subject_id = sub.subject_id
            LEFT  JOIN tbl_grading_component gc ON gc.subject_id = sub.subject_id
            WHERE g.student_id = ? AND sc.class_id = ? AND g.quarter = ?
            GROUP BY sub.subject_id, sub.subject_name,
                     g.written_work, g.performance_task, g.quarterly_exam, g.final_grade
            ORDER BY sub.subject_name ASC
        ");
        $stmt->bind_param("iis", $studentId, $classId, $quarter);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Dashboard chart: passing/failing grade counts per class for a teacher
    public static function getDashboardChartData($teacherId) {
        $conn = getConnection();
        $stmt = $conn->prepare("
            SELECT c.class_id, c.class_name,
                   SUM(CASE WHEN g.final_grade >= 75 THEN 1 ELSE 0 END) AS passed,
                   SUM(CASE WHEN g.final_grade < 75  THEN 1 ELSE 0 END) AS failed
            FROM tbl_class c
            INNER JOIN tbl_teacher_class tc   ON c.class_id = tc.class_id
            INNER JOIN tbl_subject_class sc   ON c.class_id = sc.class_id
            INNER JOIN tbl_grade g            ON sc.subject_class_id = g.subject_class_id
            WHERE tc.teacher_id = ?
            GROUP BY c.class_id, c.class_name
            ORDER BY c.class_name ASC
        ");
        $stmt->bind_param("i", $teacherId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Dashboard chart: passing/failing per subject for a specific class (all quarters)
    public static function getClassSubjectChart($classId) {
        $conn = getConnection();
        $stmt = $conn->prepare("
            SELECT sub.subject_name,
                   SUM(CASE WHEN g.final_grade >= 75 THEN 1 ELSE 0 END) AS passed,
                   SUM(CASE WHEN g.final_grade < 75  THEN 1 ELSE 0 END) AS failed,
                   ROUND(AVG(g.final_grade), 2) AS avg_grade
            FROM tbl_grade g
            INNER JOIN tbl_subject_class sc ON g.subject_class_id = sc.subject_class_id
            INNER JOIN tbl_subject sub      ON sc.subject_id = sub.subject_id
            WHERE sc.class_id = ?
            GROUP BY sub.subject_id, sub.subject_name
            ORDER BY sub.subject_name ASC
        ");
        $stmt->bind_param("i", $classId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Students with at least one grade in a class/quarter (for report student picker)
    public static function getStudentsWithGrades($classId, $quarter) {
        $conn = getConnection();
        $stmt = $conn->prepare("
            SELECT DISTINCT s.student_id,
                   CONCAT(s.student_lname, ', ', s.student_fname) AS full_name
            FROM tbl_student s
            INNER JOIN tbl_grade g            ON s.student_id = g.student_id
            INNER JOIN tbl_subject_class sc   ON g.subject_class_id = sc.subject_class_id
            WHERE sc.class_id = ? AND g.quarter = ?
            ORDER BY s.student_lname ASC, s.student_fname ASC
        ");
        $stmt->bind_param("is", $classId, $quarter);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Export: quarters where every student has a grade for every subject in the class
    public static function getQuartersWithCompleteGrades($classId) {
        $conn = getConnection();
        $stmt = $conn->prepare("
            SELECT g.quarter
            FROM tbl_grade g
            INNER JOIN tbl_subject_class sc ON g.subject_class_id = sc.subject_class_id
            WHERE sc.class_id = ?
            GROUP BY g.quarter
            HAVING COUNT(*) = (
                (SELECT COUNT(*) FROM tbl_student      WHERE class_id = ?) *
                (SELECT COUNT(*) FROM tbl_subject_class WHERE class_id = ?)
            )
            ORDER BY FIELD(g.quarter, '1st', '2nd', '3rd', '4th')
        ");
        $stmt->bind_param("iii", $classId, $classId, $classId);
        $stmt->execute();
        return array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'quarter');
    }

    // Export: all student grades per subject for a class/quarter
    public static function getClassGradesForExport($classId, $quarter) {
        $conn = getConnection();
        $stmt = $conn->prepare("
            SELECT s.student_id,
                   CONCAT(s.student_lname, ', ', s.student_fname) AS student_name,
                   s.student_lrn AS lrn,
                   sub.subject_name,
                   g.written_work, g.performance_task, g.quarterly_exam, g.final_grade
            FROM tbl_student s
            INNER JOIN tbl_grade g            ON g.student_id = s.student_id
            INNER JOIN tbl_subject_class sc   ON g.subject_class_id = sc.subject_class_id
                                             AND sc.class_id = ?
            INNER JOIN tbl_subject sub        ON sc.subject_id = sub.subject_id
            WHERE s.class_id = ? AND g.quarter = ?
            ORDER BY s.student_lname ASC, s.student_fname ASC, sub.subject_name ASC
        ");
        $stmt->bind_param("iis", $classId, $classId, $quarter);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
