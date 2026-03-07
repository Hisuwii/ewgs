<?php

class UserGradeController
{
    /**
     * DepEd K-12 Transmutation Table
     * Converts a Percentage Score (0–100) to a Transmuted Grade (60–100).
     *
     * Two linear regions:
     *  PS  0 – 59.99 → TG  60 – 74  (every 4 PS points = 1 TG step, min grade is 60)
     *  PS 60 – 100   → TG  75 – 100 (every 1.6 PS points = 1 TG step)
     */
    private function transmute(float $ps): int
    {
        if ($ps >= 100) return 100;
        if ($ps >= 60)  return (int) floor(($ps - 60) / 1.6) + 75;
        return (int) floor($ps / 4) + 60;
    }

    private function authCheck()
    {
        if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
            header('Location: /ewgs/');
            exit;
        }
    }

    private function rbacCheck($classId)
    {
        if (!AdminModel::teacherOwnsClass((int) $_SESSION['teacher_id'], (int) $classId)) {
            http_response_code(403);
            die('Access denied. This class is not assigned to you.');
        }
    }

    public function index()
    {
        $this->authCheck();
        $classes = AdminModel::getClassesByTeacher((int) $_SESSION['teacher_id']);
        require_once 'views/user/UserAddGrade.php';
    }

    public function gradeForm($classId)
    {
        $this->authCheck();
        $this->rbacCheck($classId);
        $class    = AdminModel::getClassById((int) $classId);
        $students = AdminModel::getStudentsByClass((int) $classId);
        $subjects = AdminModel::getSubjectsByClass((int) $classId);
        require_once 'views/user/UserGradeForm.php';
    }

    public function manageGrades()
    {
        $this->authCheck();
        $classes = AdminModel::getClassesByTeacher((int) $_SESSION['teacher_id']);
        require_once 'views/user/UserManageGrades.php';
    }

    public function manageGradeForm($classId)
    {
        $this->authCheck();
        $this->rbacCheck($classId);
        $class    = AdminModel::getClassById((int) $classId);
        $students = AdminModel::getStudentsByClass((int) $classId);
        $subjects = AdminModel::getSubjectsByClass((int) $classId);
        $pageMode = 'manage';
        require_once 'views/user/UserGradeForm.php';
    }

    // AJAX: manage grades list stats (student count + grade status per class)
    public function manageGradesStats()
    {
        if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
            http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit;
        }
        $stats = AdminModel::getManageGradesStats((int) $_SESSION['teacher_id']);
        header('Content-Type: application/json');
        echo json_encode($stats);
        exit;
    }

    // AJAX: subjects linked to a class
    public function getSubjects($classId)
    {
        if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
            http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit;
        }
        $this->rbacCheck((int) $classId);
        $subjects = AdminModel::getSubjectsByClass((int) $classId);
        header('Content-Type: application/json');
        echo json_encode($subjects);
        exit;
    }

    // AJAX: grading structure (components + activities) for a subject
    public function getGradingStructure($subjectId)
    {
        if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
            http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit;
        }
        $structure = AdminModel::getGradingStructure((int) $subjectId);
        header('Content-Type: application/json');
        echo json_encode($structure);
        exit;
    }

    // AJAX: existing per-activity scores for class/subject/quarter
    public function fetchExistingGrades()
    {
        if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
            http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit;
        }
        $classId   = (int) ($_GET['class_id']   ?? 0);
        $subjectId = (int) ($_GET['subject_id']  ?? 0);
        $quarter   = $_GET['quarter'] ?? '';
        if (!AdminModel::teacherOwnsClass((int) $_SESSION['teacher_id'], $classId)) {
            http_response_code(403); echo json_encode(['error' => 'Access denied']); exit;
        }
        // Returns { student_id: { activity_id: score } }
        $scores = AdminModel::getActivityScores($classId, $subjectId, $quarter);
        header('Content-Type: application/json');
        echo json_encode($scores);
        exit;
    }

    // AJAX: which quarters have complete grades for all students in a class
    public function exportCheck($classId)
    {
        $this->authCheck();
        $this->rbacCheck($classId);
        $quarters = AdminModel::getQuartersWithCompleteGrades((int) $classId);
        header('Content-Type: application/json');
        echo json_encode($quarters);
        exit;
    }

    // AJAX: full grade data for PDF export
    public function exportData($classId)
    {
        $this->authCheck();
        $this->rbacCheck($classId);
        $quarter = $_GET['quarter'] ?? '';
        $validQuarters = ['1st', '2nd', '3rd', '4th'];
        if (!in_array($quarter, $validQuarters)) {
            http_response_code(400); echo json_encode(['error' => 'Invalid quarter']); exit;
        }
        $class = AdminModel::getClassById((int) $classId);
        $rows  = AdminModel::getClassGradesForExport((int) $classId, $quarter);
        header('Content-Type: application/json');
        echo json_encode(['class' => $class, 'rows' => $rows]);
        exit;
    }

    // AJAX: save per-activity scores and compute DepEd formula grades
    public function saveGrades()
    {
        if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
            http_response_code(401); echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit;
        }
        $classId   = (int) ($_POST['class_id']   ?? 0);
        $subjectId = (int) ($_POST['subject_id']  ?? 0);
        $quarter   = $_POST['quarter'] ?? '';
        $entries   = $_POST['scores']  ?? [];  // flat array of {student_id, activity_id, score}

        $validQuarters = ['1st', '2nd', '3rd', '4th'];
        if (!AdminModel::teacherOwnsClass((int) $_SESSION['teacher_id'], $classId) ||
            !in_array($quarter, $validQuarters) || $subjectId < 1 || $classId < 1) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            exit;
        }

        $classData  = AdminModel::getClassById($classId);
        $schoolYear = $classData['school_year'] ?? '';
        $structure  = AdminModel::getGradingStructure($subjectId);

        // Build activity → perfect_score lookup
        $activityInfo = [];
        foreach ($structure as $comp) {
            foreach ($comp['activities'] as $act) {
                $activityInfo[(int) $act['activity_id']] = [
                    'perfect'   => (float) $act['perfect_score'],
                    'comp_name' => $comp['component_name'],
                    'comp_pct'  => (float) $comp['percentage'],
                ];
            }
        }

        // Group by student_id and save each activity score
        $byStudent = [];
        foreach ($entries as $e) {
            $sid   = (int) ($e['student_id']  ?? 0);
            $actId = (int) ($e['activity_id'] ?? 0);
            $score = max(0, (float) ($e['score'] ?? 0));
            if ($sid < 1 || $actId < 1) continue;
            if (isset($activityInfo[$actId])) {
                $score = min($score, $activityInfo[$actId]['perfect']);
            }
            $byStudent[$sid][$actId] = $score;
            AdminModel::saveActivityScore($sid, $actId, $quarter, $schoolYear, $score);
        }

        // Compute component % scores and final grade per student using DepEd formula:
        // Component % = (sum of raw scores / sum of perfect scores) * 100
        // Final = Σ (Component% × weight)
        foreach ($byStudent as $studentId => $actScores) {
            $compTotals = [];
            foreach ($structure as $comp) {
                $sumRaw  = 0;
                $sumPerfect = 0;
                foreach ($comp['activities'] as $act) {
                    $sumRaw     += (float) ($actScores[(int) $act['activity_id']] ?? 0);
                    $sumPerfect += (float) $act['perfect_score'];
                }
                $compTotals[$comp['component_name']] = [
                    'pct' => $sumPerfect > 0 ? ($sumRaw / $sumPerfect * 100) : 0,
                    'wt'  => (float) $comp['percentage'],
                ];
            }
            $ww    = round($compTotals['Written Work']['pct']     ?? 0, 2);
            $pt    = round($compTotals['Performance Task']['pct'] ?? 0, 2);
            $qe    = round($compTotals['Quarterly Exam']['pct']   ?? 0, 2);
            // Weights stored as decimals (e.g. 0.25, 0.50, 0.25) — already correct
            $wwWt  = $compTotals['Written Work']['wt']     ?? 0.25;
            $ptWt  = $compTotals['Performance Task']['wt'] ?? 0.50;
            $qeWt  = $compTotals['Quarterly Exam']['wt']   ?? 0.25;
            // Initial Grade = weighted sum of component percentage scores
            $initialGrade = round(($ww * $wwWt) + ($pt * $ptWt) + ($qe * $qeWt), 2);
            // Apply DepEd transmutation table to get the Quarterly Grade
            $final = $this->transmute($initialGrade);
            AdminModel::saveGrade($studentId, $subjectId, $classId, $quarter, $ww, $pt, $qe, $final);
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Grades saved successfully!']);
        exit;
    }
}
