<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageMode) && $pageMode === 'manage' ? 'Manage Grade' : 'Add Grade' ?> | EWGS</title>
    <?php require_once 'views/templates/user/header.php'; ?>
    <style>
        /* ── Toolbar buttons ─────────────────────────────── */
        .btn-save {
            background-color: #4b6b4b; color: #fff; border: none;
            padding: 10px 25px; border-radius: 8px; font-weight: 500;
        }
        .btn-save:hover { background-color: #3a5a3a; color: #fff; }
        .btn-save:disabled { opacity: .65; cursor: not-allowed; }
        .btn-cancel {
            background-color: #6c757d; color: #fff; border: none;
            padding: 10px 25px; border-radius: 8px; font-weight: 500;
        }
        .btn-cancel:hover { background-color: #5a6268; color: #fff; }

        /* ── Section info + selectors ────────────────────── */
        .section-info {
            background: #fff; padding: 15px 20px;
            border-radius: 8px; margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .section-info h5 { margin: 0; color: #4b6b4b; }
        .section-info span { color: #666; font-size: .9rem; }
        body.dark-mode .section-info { background: #1e1e1e; }
        body.dark-mode .section-info h5 { color: #8fbc8f; }
        body.dark-mode .section-info span { color: #aaa; }

        .grade-selectors { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; }
        .grade-selectors .selector-group { display: flex; flex-direction: column; gap: 4px; min-width: 180px; }
        .grade-selectors label { font-size: 12px; font-weight: 600; color: #555; margin: 0; }
        body.dark-mode .grade-selectors label { color: #bbb; }

        /* ── Grade table ─────────────────────────────────── */
        .grade-table-wrap { overflow-x: auto; }
        #gradeTable { min-width: 600px; font-size: 14px; }
        #gradeTable thead tr:first-child th { background: #2e4e2e; color: #fff; }
        #gradeTable thead tr:last-child  th { background: #4b6b4b; color: #fff; font-size: 12px; }
        body.dark-mode #gradeTable thead tr:first-child th { background: #1a3a1a; }
        body.dark-mode #gradeTable thead tr:last-child  th { background: #2e4e2e; }
        #gradeTable tbody td { vertical-align: middle; }

        .score-input {
            width: 72px; text-align: center; font-size: 13px;
            background: transparent !important; padding: 4px 6px;
        }
        body.dark-mode .score-input { color: #f3f4f6 !important; }
        .score-input.is-invalid { border-color: #dc3545 !important; }

        .comp-pct-cell { font-weight: 600; font-size: .95rem; color: #555; min-width: 70px; }
        body.dark-mode .comp-pct-cell { color: #bbb; }
        .grade-display { font-weight: 700; font-size: 1.05rem; }
        .grade-passed  { color: #28a745; }
        .grade-failed  { color: #dc3545; }

        /* ── Component header groups ─────────────────────── */
        th.comp-header { text-align: center; border-right: 2px solid rgba(255,255,255,.3); }

        /* ── Toolbar above table ─────────────────────────── */
        .grade-toolbar {
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 10px; margin-bottom: 12px;
        }
        .grade-toolbar .search-wrap input {
            border-radius: 7px; border: 1px solid #d0d8d0;
            padding: 6px 12px; font-size: 13px; min-width: 200px;
        }
        .grade-toolbar .search-wrap input:focus {
            border-color: #4b6b4b; outline: none;
            box-shadow: 0 0 0 2px rgba(75,107,75,.15);
        }
        body.dark-mode .grade-toolbar .search-wrap input {
            background: #2d2d2d; border-color: #3a3a3a; color: #e0e0e0;
        }
        .btn-group-toolbar { display: flex; gap: 8px; }

        /* ── Loading overlay ─────────────────────────────── */
        #tableLoadingMsg { display: none; text-align: center; padding: 40px; color: #888; }

        /* ── Leave modal ─────────────────────────────────── */
        #leaveModal .modal-header {
            background: linear-gradient(135deg, #c0392b, #e74c3c);
            color: #fff; border-bottom: none;
        }
        #leaveModal .modal-title { font-weight: 600; }
        #leaveModal .btn-stay  { background:#6c757d; color:#fff; border:none; border-radius:7px; padding:8px 20px; font-weight:600; }
        #leaveModal .btn-leave { background:linear-gradient(135deg,#c0392b,#e74c3c); color:#fff; border:none; border-radius:7px; padding:8px 20px; font-weight:600; }
        body.dark-mode #leaveModal .modal-content { background: #1e1e1e; }
        body.dark-mode #leaveModal .modal-body    { color: #e0e0e0; }
    </style>
</head>
<body>
    <?php require_once 'views/templates/user/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header"><h4><?= isset($pageMode) && $pageMode === 'manage' ? 'Manage Grade' : 'Add Grade' ?></h4></div>

        <div class="container-fluid px-4">

            <!-- Section info + selectors -->
            <div class="section-info">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h5><?= htmlspecialchars($class['class_name']) ?> — Grade <?= htmlspecialchars($class['grade_level']) ?></h5>
                        <span>School Year: <?= htmlspecialchars($class['school_year']) ?></span>
                    </div>
                    <div class="grade-selectors">
                        <div class="selector-group">
                            <label for="subjectSelect">Subject</label>
                            <select class="form-select form-select-sm" id="subjectSelect">
                                <option value="">— Select subject —</option>
                                <?php foreach ($subjects as $sub): ?>
                                    <option value="<?= (int) $sub['subject_id'] ?>"><?= htmlspecialchars($sub['subject_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="selector-group">
                            <label for="quarterSelect">Quarter</label>
                            <select class="form-select form-select-sm" id="quarterSelect">
                                <option value="">— Select quarter —</option>
                                <option value="1st">1st Quarter</option>
                                <option value="2nd">2nd Quarter</option>
                                <option value="3rd">3rd Quarter</option>
                                <option value="4th">4th Quarter</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (empty($subjects)): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    No subjects are linked to this class yet. Ask the admin to assign subjects first.
                </div>
            <?php elseif (empty($students)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    No students are enrolled in this class yet.
                </div>
            <?php else: ?>

                <!-- Toolbar: search + action buttons -->
                <div class="grade-toolbar" id="gradeToolbar" style="display:none !important;">
                    <div class="search-wrap">
                        <input type="text" id="studentSearch" placeholder="Search student…">
                    </div>
                    <div class="btn-group-toolbar">
                        <button type="button" class="btn btn-cancel" id="btnCancel">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-save" id="btnSave" disabled>
                            <i class="bi bi-check-circle"></i> Save
                        </button>
                    </div>
                </div>

                <!-- Loading indicator -->
                <div id="tableLoadingMsg">
                    <span class="spinner-border spinner-border-sm me-2"></span> Loading grade structure…
                </div>

                <!-- Grade table -->
                <div class="grade-table-wrap" id="gradeTableWrap" style="display:none;">
                    <table class="table table-bordered table-hover text-center" id="gradeTable">
                        <thead id="gradeTableHead"></thead>
                        <tbody id="gradeTableBody"></tbody>
                    </table>
                </div>

                <div id="selectPrompt" class="text-muted text-center py-5">
                    <i class="bi bi-arrow-up-circle fs-3 d-block mb-2"></i>
                    Select a subject and quarter above to begin grading.
                </div>

            <?php endif; ?>
        </div>
    </div>

    <!-- Unsaved-changes modal -->
    <div class="modal fade" id="leaveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Unsaved Changes</h5>
                </div>
                <div class="modal-body py-4">
                    You have unsaved grades. If you leave now, your changes will be lost.<br><br>
                    Are you sure you want to leave?
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-stay"  data-bs-dismiss="modal">Stay</button>
                    <button type="button" class="btn btn-leave" id="btnConfirmLeave">Leave Anyway</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/ewgs/public/js/bootstrap.bundle.js"></script>
    <script>
    $(document).ready(function () {

        var CLASS_ID        = <?= (int) $class['class_id'] ?>;
        var CANCEL_URL      = '<?= isset($pageMode) && $pageMode === 'manage' ? '/ewgs/user/manage-grades' : '/ewgs/user/add-grade' ?>';
        var isDirty         = false;
        var pendingUrl      = null;
        var pendingAction   = null;
        var currentStructure = null;

        var STUDENTS = <?= json_encode(array_values(array_map(function($s) {
            return [
                'student_id' => (int) $s['student_id'],
                'name'       => $s['student_lname'] . ', ' . $s['student_fname'],
            ];
        }, $students))) ?>;

        var leaveModal = new bootstrap.Modal(document.getElementById('leaveModal'), { backdrop: 'static', keyboard: false });

        // ── Helpers ───────────────────────────────────────────────
        function esc(str) {
            return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }
        function getSubject()  { return $('#subjectSelect').val(); }
        function getQuarter()  { return $('#quarterSelect').val(); }

        function showPrompt()  { $('#selectPrompt').show(); $('#gradeTableWrap').hide(); $('#gradeToolbar').hide(); $('#tableLoadingMsg').hide(); }
        function showLoading() { $('#selectPrompt').hide(); $('#gradeTableWrap').hide(); $('#gradeToolbar').hide(); $('#tableLoadingMsg').show(); }
        function showTable()   { $('#selectPrompt').hide(); $('#tableLoadingMsg').hide(); $('#gradeTableWrap').show(); $('#gradeToolbar').css('display', 'flex'); }

        // ── Selector changes ──────────────────────────────────────
        function onSelectorChange() {
            if (!getSubject() || !getQuarter()) { showPrompt(); return; }
            if (isDirty) {
                pendingAction = loadGradeTable;
                leaveModal.show();
            } else {
                loadGradeTable();
            }
        }
        $('#subjectSelect, #quarterSelect').on('change', onSelectorChange);

        // ── Load structure + scores ───────────────────────────────
        function loadGradeTable() {
            showLoading();
            $.when(
                $.getJSON('/ewgs/user/grade/structure/' + getSubject()),
                $.getJSON('/ewgs/user/grade/existing', { class_id: CLASS_ID, subject_id: getSubject(), quarter: getQuarter() })
            ).done(function (sResult, eResult) {
                currentStructure = sResult[0];
                var existingScores = eResult[0];
                buildTable(currentStructure, existingScores);
                isDirty = false;
                $('#btnSave').prop('disabled', false);
                showTable();
            }).fail(function () {
                showPrompt();
                alert('Failed to load grade data. Please try again.');
            });
        }

        // ── Build dynamic table ───────────────────────────────────
        function buildTable(structure, existingScores) {
            if (!structure || structure.length === 0) {
                $('#gradeTableHead').html('');
                $('#gradeTableBody').html('<tr><td class="text-muted py-4" colspan="3">No grading components set up for this subject. Ask the admin to configure them.</td></tr>');
                return;
            }

            // Header row 1: component group headers
            var h1 = '<tr>';
            h1 += '<th rowspan="2" class="align-middle" style="width:38px">#</th>';
            h1 += '<th rowspan="2" class="align-middle text-start" style="min-width:160px">Student Name</th>';
            structure.forEach(function (comp) {
                var span = comp.activities.length + 1;
                h1 += '<th colspan="' + span + '" class="comp-header">' +
                      esc(comp.component_name) +
                      ' <small class="fw-normal">(' + Math.round(comp.percentage * 100) + '%)</small></th>';
            });
            h1 += '<th rowspan="2" class="align-middle">Final<br>Grade</th></tr>';

            // Header row 2: individual activity columns + % score
            var h2 = '<tr>';
            structure.forEach(function (comp) {
                comp.activities.forEach(function (act) {
                    h2 += '<th>' + esc(act.activity_name) +
                          '<br><small class="fw-normal opacity-75">/' + parseInt(act.perfect_score, 10) + '</small></th>';
                });
                h2 += '<th>% Score</th>';
            });
            h2 += '</tr>';

            $('#gradeTableHead').html(h1 + h2);

            // Body rows
            var tbody = '';
            STUDENTS.forEach(function (s, idx) {
                var sid = s.student_id;
                tbody += '<tr data-student-id="' + sid + '" data-name="' + esc(s.name.toLowerCase()) + '">';
                tbody += '<td>' + (idx + 1) + '</td>';
                tbody += '<td class="text-start">' + esc(s.name) + '</td>';
                structure.forEach(function (comp) {
                    comp.activities.forEach(function (act) {
                        var val = (existingScores[sid] && existingScores[sid][act.activity_id] != null)
                                  ? existingScores[sid][act.activity_id] : '';
                        tbody += '<td><input type="number" class="form-control score-input"' +
                                 ' data-component-id="' + comp.component_id + '"' +
                                 ' data-activity-id="' + act.activity_id + '"' +
                                 ' data-perfect="' + act.perfect_score + '"' +
                                 ' min="0" max="' + act.perfect_score + '" step="1"' +
                                 ' value="' + val + '" placeholder="0"></td>';
                    });
                    tbody += '<td class="comp-pct-cell" data-component-id="' + comp.component_id + '">--</td>';
                });
                tbody += '<td class="final-grade-cell grade-display">--</td>';
                tbody += '</tr>';
            });
            $('#gradeTableBody').html(tbody);

            // Calculate all rows from loaded scores
            $('#gradeTableBody tr[data-student-id]').each(function () { recalcRow($(this)); });
        }

        // ── DepEd transmutation table (mirrors PHP transmute()) ───
        function transmute(ps) {
            if (ps >= 100) return 100;
            if (ps >= 60)  return Math.floor((ps - 60) / 1.6) + 75;
            return Math.floor(ps / 4) + 60;
        }

        // ── DepEd formula recalc for one row ──────────────────────
        function recalcRow($row) {
            if (!currentStructure) return;
            var initialGrade = 0;
            var hasAny = false;

            currentStructure.forEach(function (comp) {
                var sumRaw = 0, sumPerfect = 0;
                comp.activities.forEach(function (act) {
                    var $inp = $row.find('.score-input[data-activity-id="' + act.activity_id + '"]');
                    if ($inp.val() !== '') hasAny = true;
                    sumRaw     += parseFloat($inp.val()) || 0;
                    sumPerfect += parseFloat(act.perfect_score);
                });
                var pct = sumPerfect > 0 ? (sumRaw / sumPerfect * 100) : 0;
                $row.find('.comp-pct-cell[data-component-id="' + comp.component_id + '"]')
                    .text(pct.toFixed(2));
                initialGrade += pct * parseFloat(comp.percentage);
            });

            var $fg = $row.find('.final-grade-cell');
            if (hasAny) {
                var transmuted = transmute(initialGrade);
                $fg.text(transmuted)
                   .removeClass('grade-passed grade-failed')
                   .addClass(transmuted >= 75 ? 'grade-passed' : 'grade-failed');
            } else {
                $fg.text('--').removeClass('grade-passed grade-failed');
            }
        }

        // ── Score input events ────────────────────────────────────
        $(document).on('input', '.score-input', function () {
            // Enforce whole number and max = perfect score
            var max = parseInt($(this).data('perfect'), 10);
            var val = parseInt($(this).val(), 10);
            if (!isNaN(val)) {
                if (val > max) { $(this).val(max); }
                else if (val < 0) { $(this).val(0); }
                else { $(this).val(val); } // strip any decimal the browser might allow
            }
            isDirty = true;
            recalcRow($(this).closest('tr'));
        });

        // ── Student search filter ─────────────────────────────────
        $('#studentSearch').on('input', function () {
            var q = $(this).val().toLowerCase();
            $('#gradeTableBody tr[data-student-id]').each(function () {
                $(this).toggle($(this).data('name').indexOf(q) !== -1);
            });
        });

        // ── Save ──────────────────────────────────────────────────
        $(document).on('click', '#btnSave', function () {
            if (!getSubject() || !getQuarter() || !currentStructure) return;

            var scores = [];
            $('#gradeTableBody tr[data-student-id]').each(function () {
                var sid = $(this).data('student-id');
                $(this).find('.score-input').each(function () {
                    scores.push({
                        student_id:  sid,
                        activity_id: $(this).data('activity-id'),
                        score:       $(this).val() || 0
                    });
                });
            });

            var $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving…');

            $.ajax({
                url:      '/ewgs/user/grade/save',
                type:     'POST',
                data:     { class_id: CLASS_ID, subject_id: getSubject(), quarter: getQuarter(), scores: scores },
                dataType: 'json',
                success: function (res) {
                    $btn.html('<i class="bi bi-check-circle"></i> Save').prop('disabled', false);
                    if (res.success) {
                        isDirty = false;
                        var toast = $('<div class="toast-notification toast-success" style="display:flex;">' +
                            '<i class="bi bi-check-circle-fill"></i><span>' + res.message + '</span></div>');
                        $('body').append(toast);
                        toast.fadeIn().delay(3000).fadeOut(function () { toast.remove(); });
                    } else {
                        alert(res.message || 'An error occurred.');
                    }
                },
                error: function () {
                    alert('An unexpected error occurred. Please try again.');
                    $btn.html('<i class="bi bi-check-circle"></i> Save').prop('disabled', false);
                }
            });
        });

        // ── Cancel ────────────────────────────────────────────────
        $(document).on('click', '#btnCancel', function (e) {
            e.preventDefault();
            if (isDirty) { pendingAction = null; pendingUrl = CANCEL_URL; leaveModal.show(); }
            else         { window.location.href = CANCEL_URL; }
        });

        // ── Sidebar interception ──────────────────────────────────
        $(document).on('click', '.sidebar .nav-link', function (e) {
            if (!isDirty) return;
            e.preventDefault();
            pendingAction = null;
            pendingUrl = $(this).attr('href');
            leaveModal.show();
        });

        // ── Leave modal confirm ───────────────────────────────────
        $('#btnConfirmLeave').on('click', function () {
            leaveModal.hide();
            isDirty = false;
            if (pendingAction) { var fn = pendingAction; pendingAction = null; fn(); }
            else if (pendingUrl) { window.location.href = pendingUrl; }
        });

        // ── Browser back/refresh guard ────────────────────────────
        window.addEventListener('beforeunload', function (e) {
            if (isDirty) { e.preventDefault(); e.returnValue = ''; }
        });

    });
    </script>
</body>
</html>
