<?php

class StudentController {

    private function authCheck() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: /ewgs/admin');
            exit;
        }
    }

    private function jsonAuthCheck() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
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

    public function index() {
        $this->authCheck();
        require_once 'views/admin/AdminStudent.php';
    }

    public function getData() {
        $this->jsonAuthCheck();
        $students = AdminModel::getAllStudents($_SESSION['user_id'] ?? null);
        $count = 1;
        $data = array_map(function ($s) use (&$count) {
            return [
                'count'         => $count++,
                'student_id'    => $s['student_id'],
                'student_fname' => $s['student_fname'],
                'student_lname' => $s['student_lname'],
                'lrn'           => $s['lrn'],
                'gender'        => $s['gender'],
                'birth_date'    => $s['birth_date'] ?? null,
            ];
        }, $students);
        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
    }

    private function isValidLrn($lrn) {
        return preg_match('/^\d{12}$/', $lrn);
    }

    public function add() {
        $this->authCheck();
        $lrn = trim($_POST['lrn'] ?? '');
        if (!$this->isValidLrn($lrn)) {
            if ($this->isAjax()) $this->jsonResponse(false, 'Invalid LRN. Must be exactly 12 digits.');
            setFlash('error', 'Invalid LRN. LRN must be exactly 12 digits.');
            header('Location: /ewgs/admin/student'); exit;
        }
        $_POST['admin_id'] = $_SESSION['user_id'] ?? null;
        if (AdminModel::addStudent($_POST)) {
            if ($this->isAjax()) $this->jsonResponse(true, 'Student added successfully.');
            setFlash('success', 'Student added successfully.');
        } else {
            if ($this->isAjax()) $this->jsonResponse(false, 'Failed to add student. LRN may already exist.');
            setFlash('error', 'Failed to add student. LRN may already exist.');
        }
        header('Location: /ewgs/admin/student'); exit;
    }

    public function edit() {
        $this->authCheck();
        $lrn = trim($_POST['lrn'] ?? '');
        if (!$this->isValidLrn($lrn)) {
            if ($this->isAjax()) $this->jsonResponse(false, 'Invalid LRN. Must be exactly 12 digits.');
            setFlash('error', 'Invalid LRN. LRN must be exactly 12 digits.');
            header('Location: /ewgs/admin/student'); exit;
        }
        if (AdminModel::updateStudent($_POST)) {
            if ($this->isAjax()) $this->jsonResponse(true, 'Student updated successfully.');
            setFlash('success', 'Student updated successfully.');
        } else {
            if ($this->isAjax()) $this->jsonResponse(false, 'Failed to update student.');
            setFlash('error', 'Failed to update student.');
        }
        header('Location: /ewgs/admin/student'); exit;
    }

    public function delete() {
        $this->authCheck();
        $id     = (int) ($_POST['student_id'] ?? 0);
        $result = $id ? AdminModel::removeStudent($id) : false;

        if ($result === 'fk') {
            if ($this->isAjax()) $this->jsonResponse(false, 'Cannot delete this student. They have grades recorded.');
            setFlash('error', 'Cannot delete this student. They have grades recorded.');
            header('Location: /ewgs/admin/student'); exit;
        }

        if ($result) {
            if ($this->isAjax()) $this->jsonResponse(true, 'Student deleted successfully.');
            setFlash('success', 'Student deleted successfully.');
        } else {
            if ($this->isAjax()) $this->jsonResponse(false, 'Failed to delete student.');
            setFlash('error', 'Failed to delete student.');
        }
        header('Location: /ewgs/admin/student'); exit;
    }

    public function importStudents() {
        $this->authCheck();

        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            if ($this->isAjax()) $this->jsonResponse(false, 'No file uploaded or an upload error occurred.');
            setFlash('error', 'No file uploaded or an upload error occurred.');
            header('Location: /ewgs/admin/student');
            exit;
        }

        $file = $_FILES['import_file'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $rows = [];

        if ($ext === 'xlsx' || $ext === 'xls') {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file['tmp_name']);
                // toArray(null, true, true, false) — returns all cells including empty ones,
                // using formatted values, with numeric (0-based) column indexes.
                $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
            } catch (\Exception $e) {
                if ($this->isAjax()) $this->jsonResponse(false, 'Could not read the Excel file: ' . $e->getMessage());
                setFlash('error', 'Could not read the Excel file: ' . $e->getMessage());
                header('Location: /ewgs/admin/student');
                exit;
            }
        } elseif ($ext === 'csv') {
            $handle = fopen($file['tmp_name'], 'r');
            while (($row = fgetcsv($handle)) !== false) {
                $rows[] = $row;
            }
            fclose($handle);
        } else {
            if ($this->isAjax()) $this->jsonResponse(false, 'Invalid file type. Please upload a .xlsx or .csv file.');
            setFlash('error', 'Invalid file type. Please upload a .xlsx or .csv file.');
            header('Location: /ewgs/admin/student');
            exit;
        }

        // Remove header row
        if (!empty($rows)) array_shift($rows);
        // Skip guide row if present (template row 2 where first cell starts with "e.g.")
        if (!empty($rows) && stripos(trim((string)($rows[0][0] ?? '')), 'e.g.') === 0) {
            array_shift($rows);
        }

        $imported       = 0;
        $invalidLrn     = 0;
        $missingFields  = 0;
        $duplicateFile  = 0;
        $duplicateDb    = 0;
        $seenLrns       = [];

        foreach ($rows as $row) {
            $fname  = trim($row[0] ?? '');
            $lname  = trim($row[1] ?? '');
            $gender = trim($row[3] ?? '');

            // Excel may return LRN as float or as string in scientific notation (e.g. "1.23456789011E+11")
            $rawLrn = $row[2] ?? '';
            if (is_int($rawLrn)) {
                $lrn = (string) $rawLrn;
            } elseif (is_float($rawLrn) || (is_string($rawLrn) && is_numeric($rawLrn) && stripos($rawLrn, 'e') !== false)) {
                $lrn = sprintf('%.0f', (float) $rawLrn);
            } else {
                $lrn = trim((string) $rawLrn);
            }

            // Skip rows with missing required fields (including gender)
            if ($fname === '' || $lname === '' || $lrn === '' || $gender === '') {
                $missingFields++;
                continue;
            }

            // Normalize gender casing and validate
            $gender = ucfirst(strtolower($gender));
            if (!in_array($gender, ['Male', 'Female'])) {
                $missingFields++;
                continue;
            }

            // Skip rows with invalid LRN format
            if (!$this->isValidLrn($lrn)) {
                $invalidLrn++;
                continue;
            }

            // Skip duplicate LRNs within the file
            if (isset($seenLrns[$lrn])) {
                $duplicateFile++;
                continue;
            }
            $seenLrns[$lrn] = true;

            // Parse birth date — required field
            // Handles YYYY-MM-DD strings, other date strings, and Excel date serials (e.g. 44927)
            $bdate = null;
            $rawBdate = isset($row[4]) ? $row[4] : null;
            if ($rawBdate !== null && trim((string) $rawBdate) !== '') {
                $raw = trim((string) $rawBdate);
                if (is_numeric($raw) && strpos($raw, '-') === false) {
                    // Excel date serial number — convert using PhpSpreadsheet
                    try {
                        $bdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $raw)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $bdate = null;
                    }
                } else {
                    // String date — normalize to Y-m-d
                    $ts = strtotime($raw);
                    $bdate = $ts !== false ? date('Y-m-d', $ts) : null;
                }
            }

            // Birth date is required — skip if missing or unparseable
            if ($bdate === null) {
                $missingFields++;
                continue;
            }

            if (AdminModel::addStudent([
                'student_fname' => $fname,
                'student_lname' => $lname,
                'lrn'           => $lrn,
                'gender'        => $gender,
                'birth_date'    => $bdate,
                'admin_id'      => $_SESSION['user_id'] ?? null,
            ])) {
                $imported++;
            } else {
                $duplicateDb++;
            }
        }

        $details = [];
        if ($missingFields) $details[] = "{$missingFields} missing required field(s)";
        if ($invalidLrn)    $details[] = "{$invalidLrn} invalid LRN (must be exactly 12 digits)";
        if ($duplicateFile) $details[] = "{$duplicateFile} duplicate LRN within the file";
        if ($duplicateDb)   $details[] = "{$duplicateDb} LRN already exists in the system";

        $msg = "{$imported} student(s) added.";
        if (!empty($details)) {
            $msg .= " Skipped: " . implode(', ', $details) . '.';
        }

        if ($this->isAjax()) $this->jsonResponse($imported > 0, "Import complete: {$msg}");
        setFlash($imported > 0 ? 'success' : 'error', "Import complete: {$msg}");
        header('Location: /ewgs/admin/student');
        exit;
    }

    public function downloadTemplate() {
        $this->authCheck();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        // Header row
        $sheet->setCellValue('A1', 'First Name');
        $sheet->setCellValue('B1', 'Last Name');
        $sheet->setCellValue('C1', 'LRN');
        $sheet->setCellValue('D1', 'Gender');
        $sheet->setCellValue('E1', 'Birth Date');

        // Bold header row
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        // Guide row (row 2) — shows expected format for each column
        $sheet->setCellValue('A2', 'e.g. Juan');
        $sheet->setCellValue('B2', 'e.g. Dela Cruz');
        $sheet->setCellValue('C2', 'e.g. 123456789012');
        $sheet->setCellValue('D2', 'Male or Female');
        $sheet->setCellValue('E2', 'e.g. 2015-06-15 (YYYY-MM-DD)');

        // Style guide row — gray italic
        $sheet->getStyle('A2:E2')->getFont()->setItalic(true);
        $sheet->getStyle('A2:E2')->getFont()->getColor()
              ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN);

        // Format LRN column as Text so Excel never converts it to scientific notation
        $sheet->getStyle('C')->getNumberFormat()
              ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        // Format Birth Date column as Text to prevent Excel from converting to date serial
        $sheet->getStyle('E')->getNumberFormat()
              ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        // Auto-fit column widths
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="student_import_template.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
