<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classes | EWGS</title>
    <?php require_once 'views/templates/admin/header.php'; ?>
    <?php require_once 'views/templates/admin/modal.php'; ?>
    <link rel="stylesheet" href="<?= BASE ?>/public/css/tom-select.bootstrap5.min.css">
    <style>
        #addClass {
            background-color: #4b6b4b !important;
            color: white;
        }
        .btn-edit {
            background-color: #4b6b4b;
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .btn-edit:hover {
            background-color: #3a5a3a;
            color: white;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .btn-delete:hover {
            background-color: #bb2d3b;
            color: white;
        }
        /* Tom Select theme */
        .ts-wrapper .ts-control {
            border-color: #d0d8d0 !important;
            border-radius: 6px !important;
            min-height: 38px;
            font-size: 14px;
        }
        .ts-wrapper.focus .ts-control {
            border-color: #4b6b4b !important;
            box-shadow: 0 0 0 2px rgba(75,107,75,0.15) !important;
        }
        .ts-wrapper.is-invalid .ts-control {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220,53,69,0.18) !important;
        }
        .ts-invalid-feedback {
            display: block;
            font-size: 0.875em;
            color: #dc3545;
            margin-top: 4px;
        }
        .ts-dropdown .option { padding: 8px 12px; font-size: 14px; }
        .ts-dropdown .active  { background: #4b6b4b !important; color: #fff !important; }
        body.dark-mode .ts-wrapper .ts-control,
        body.dark-mode .ts-wrapper .ts-control input { background: #2d2d2d !important; color: #e0e0e0 !important; border-color: #3a3a3a !important; }
        body.dark-mode .ts-dropdown { background: #2d2d2d !important; border-color: #3a3a3a !important; }
        body.dark-mode .ts-dropdown .option { color: #e0e0e0 !important; background: #2d2d2d !important; }
        body.dark-mode .ts-dropdown .active { background: #3a5a3a !important; color: #fff !important; }
    </style>
</head>
<body>
    <?php require_once 'views/templates/admin/sidebar.php'; ?>

    <!-- Flash Messages -->
    <?= displayFlash() ?>

    <div class="main-content">
        <div class="page-header">
            <h4>Classes</h4>
        </div>

        <div class="container-fluid px-4">
            <div class="d-flex justify-content-end mb-3">
                <button type="button" id="addClass" class="btn" data-bs-toggle="modal" data-bs-target="#addClassModal">
                    <i class="bi bi-plus-circle"></i> Add Class
                </button>
            </div>

            <div class="table-responsive">
                <table id="classTable" class="table table-bordered table-striped table-hover text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Section Name</th>
                            <th>Grade Level</th>
                            <th>School Year</th>
                            <th>Teacher</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Class Modal -->
    <div class="modal fade" id="addClassModal" tabindex="-1" aria-labelledby="addClassModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addClassForm" method="POST" action="<?= BASE ?>/admin/class/add">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addClassModalLabel">Add New Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="class_name" class="form-label">Section Name</label>
                            <input type="text" class="form-control" id="class_name" name="class_name" placeholder="e.g. Section A" required>
                        </div>
                        <div class="mb-3">
                            <label for="grade_level" class="form-label">Grade Level</label>
                            <input type="number" class="form-control" id="grade_level" name="grade_level" placeholder="e.g. 1" min="1" max="6" step="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="school_year" class="form-label">School Year</label>
                            <select class="form-select" id="school_year" name="school_year">
                                <option value="">-- Select School Year --</option>
                                <?php $curYear = (int)date('Y');
                                $defaultSy = $curYear . '-' . ($curYear + 1);
                                for ($y = $curYear; $y <= $curYear + 5; $y++):
                                    $sy = $y . '-' . ($y + 1); ?>
                                    <option value="<?= $sy ?>" <?= $sy === $defaultSy ? 'selected' : '' ?>><?= $sy ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="add_teacher_id" class="form-label">Assign Teacher</label>
                            <select class="form-select" id="add_teacher_id" name="teacher_id">
                                <option value="">-- Select Teacher --</option>
                                <?php foreach ($teachers as $t): ?>
                                    <option value="<?= $t['teacher_id'] ?>">
                                        <?= htmlspecialchars($t['teacher_lname'] . ', ' . $t['teacher_fname']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-save">Save Class</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Class Modal -->
    <div class="modal fade" id="editClassModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editClassForm" method="POST" action="<?= BASE ?>/admin/class/edit">
                    <input type="hidden" name="class_id" id="editClassId">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Section Name</label>
                            <input type="text" class="form-control" id="editClassName" name="class_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Grade Level</label>
                            <input type="number" class="form-control" id="editClassGrade" name="grade_level" placeholder="e.g. 1" min="1" max="6" step="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">School Year</label>
                            <select class="form-select" id="editClassYear" name="school_year">
                                <option value="">-- Select School Year --</option>
                                <?php for ($y = $curYear; $y <= $curYear + 5; $y++):
                                    $sy = $y . '-' . ($y + 1); ?>
                                    <option value="<?= $sy ?>"><?= $sy ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_teacher_id" class="form-label">Assign Teacher</label>
                            <select class="form-select" id="edit_teacher_id" name="teacher_id">
                                <option value="">-- Select Teacher --</option>
                                <?php foreach ($teachers as $t): ?>
                                    <option value="<?= $t['teacher_id'] ?>">
                                        <?= htmlspecialchars($t['teacher_lname'] . ', ' . $t['teacher_fname']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-save">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Class Modal -->
    <div class="modal fade" id="deleteClassModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="deleteClassForm" method="POST" action="<?= BASE ?>/admin/class/delete">
                    <input type="hidden" name="class_id" id="deleteClassId">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete <strong id="deleteClassName"></strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-del">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="<?= BASE ?>/public/js/bootstrap.bundle.js"></script>
    <?php require_once 'views/templates/admin/datatable.php'; ?>
    <script src="<?= BASE ?>/public/js/tom-select.complete.min.js"></script>
    <script>
        $(document).ready(function () {
            var pendingDeleteRow = null;
            var origClassSnap    = '';
            var tsAddYear     = new TomSelect('#school_year',     { allowEmptyOption: true });
            var tsEditYear    = new TomSelect('#editClassYear',   { allowEmptyOption: true });
            var tsAddTeacher  = new TomSelect('#add_teacher_id',  { allowEmptyOption: true });
            var tsEditTeacher = new TomSelect('#edit_teacher_id', { allowEmptyOption: true });
            tsAddYear.setValue('<?= $defaultSy ?>');

            /* ── Live duplicate check ──────────────────────────── */
            function checkClassDuplicate(nameEl, gradeEl, excludeId, callback) {
                var name  = $(nameEl).val().trim();
                var grade = $(gradeEl).val().trim();
                if (!name || !grade) { callback(false); return; }
                $.get('<?= BASE ?>/admin/class/check', {
                    class_name: name, grade_level: grade, exclude_id: excludeId || ''
                }, function (res) { callback(res.exists); });
            }

            function showDupWarning(nameEl) {
                $(nameEl).addClass('is-invalid').next('.dup-feedback').remove();
                $(nameEl).after('<div class="dup-feedback invalid-feedback d-block">A class with this section name already exists in this grade level.</div>');
            }
            function clearDupWarning(nameEl) {
                var $el = $(nameEl);
                $el.next('.dup-feedback').remove();
                // Only clear is-invalid if there's no digit-error still present
                if (!$el.next('.name-invalid-feedback').length) {
                    $el.removeClass('is-invalid');
                }
            }

            // Live section name validation
            function liveSectionCheck($el) {
                var val = $el.val().trim();
                var $btn = $el.closest('form').find('[type=submit]');
                if (val === '') {
                    $el.next('.name-invalid-feedback').remove();
                    $el.removeClass('is-valid is-invalid');
                    $btn.prop('disabled', false);
                } else if (/[^a-zA-ZÀ-ÿ\s'\-]/.test(val)) {
                    setTimeout(function () {
                        var v = $el.val().trim();
                        $el.next('.name-invalid-feedback').remove();
                        if (/[^a-zA-ZÀ-ÿ\s'\-]/.test(v)) {
                            $el.removeClass('is-valid').addClass('is-invalid')
                               .after('<div class="name-invalid-feedback invalid-feedback">Section Name must only contain letters.</div>');
                            $btn.prop('disabled', true);
                        } else {
                            $el.removeClass('is-invalid').addClass('is-valid');
                            $btn.prop('disabled', false);
                        }
                    }, 0);
                } else {
                    $el.next('.name-invalid-feedback').remove();
                    $el.removeClass('is-invalid').addClass('is-valid');
                    $btn.prop('disabled', false);
                }
            }

            $('#class_name, #editClassName').on('input change blur', function () {
                liveSectionCheck($(this));
            });

            // Add form — check on blur of either field
            $('#class_name, #grade_level').on('blur', function () {
                checkClassDuplicate('#class_name', '#grade_level', null, function (exists) {
                    exists ? showDupWarning('#class_name') : clearDupWarning('#class_name');
                });
            });
            // Edit form — check on blur of either field
            $('#editClassName, #editClassGrade').on('blur', function () {
                var excludeId = $('#editClassId').val();
                checkClassDuplicate('#editClassName', '#editClassGrade', excludeId, function (exists) {
                    exists ? showDupWarning('#editClassName') : clearDupWarning('#editClassName');
                });
            });

            function showTsError(ts, msg) {
                $(ts.wrapper).addClass('is-invalid');
                if (!$(ts.wrapper).next('.ts-invalid-feedback').length) {
                    $(ts.wrapper).after('<div class="ts-invalid-feedback">' + msg + '</div>');
                }
            }
            function clearTsError(ts) {
                $(ts.wrapper).removeClass('is-invalid');
                $(ts.wrapper).next('.ts-invalid-feedback').remove();
            }

            var table = $('#classTable').DataTable({
                serverSide: true,
                processing: true,
                searchDelay: 500,
                order: [],
                ajax: {
                    url: '<?= BASE ?>/admin/class/data',
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [
                    { data: null, orderable: false, searchable: false, width: '5%',
                      render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
                    { data: 'class_name' },
                    { data: 'grade_level' },
                    { data: 'school_year' },
                    {
                        data: 'teacher_name',
                        render: function (data) {
                            return data
                                ? data
                                : '<span class="text-muted">Unassigned</span>';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data) {
                            return `
                                <button class="btn btn-sm btn-edit btn-edit-class me-1"
                                    data-id="${data.class_id}"
                                    data-name="${data.class_name}"
                                    data-grade="${data.grade_level}"
                                    data-year="${data.school_year}"
                                    data-teacher="${data.teacher_id || ''}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-delete btn-delete-class"
                                    data-id="${data.class_id}"
                                    data-name="${data.class_name}">
                                    <i class="bi bi-trash"></i> Delete
                                </button>`;
                        }
                    }
                ],
                language: {
                    emptyTable: 'No classes found.'
                }
            });
            // Auto-refresh every 15s; fires immediately on tab focus if a refresh was missed
            (function () {
                var missed = false;
                setInterval(function () {
                    if (document.hidden) { missed = true; } else { table.ajax.reload(null, false); }
                }, 15000);
                document.addEventListener('visibilitychange', function () {
                    if (!document.hidden && missed) { missed = false; table.ajax.reload(null, false); }
                });
            })();

            /* ── Add class AJAX ────────────────────────────────── */
            $('#addClassForm').on('submit', function (e) {
                e.preventDefault();
                clearTsError(tsAddYear);
                clearTsError(tsAddTeacher);
                var ok = validateForm([
                    { el: $('#class_name'),  label: 'Section Name', required: true, minLen: 2, noDigits: true },
                    { el: $('#grade_level'), label: 'Grade Level',  required: true, digits: true,
                      pattern: /^[1-6]$/, patternMsg: 'Grade Level must be a number from 1 to 6.' },
                ]);
                if ($('#class_name').hasClass('is-invalid') && $('#class_name').next('.dup-feedback').length) ok = false;
                if (!tsAddYear.getValue())    { showTsError(tsAddYear,    'School Year is required.'); ok = false; }
                if (!tsAddTeacher.getValue()) { showTsError(tsAddTeacher, 'Teacher is required.');     ok = false; }
                if (!ok) return;
                var $btn = $(this).find('[type=submit]').prop('disabled', true);
                bootstrap.Modal.getInstance(document.getElementById('addClassModal'))?.hide();
                $('#preloader').fadeIn(200);
                $.ajax({
                    url: $(this).attr('action'), type: 'POST',
                    data: $(this).serialize(), dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            showToast('success', res.message);
                            table.ajax.reload(null, false);
                        } else { showToast('error', res.message); }
                    },
                    error: function () { showToast('error', 'An unexpected error occurred.'); },
                    complete: function () { $('#preloader').fadeOut(400); $btn.prop('disabled', false); }
                });
            });

            $('#addClassModal').on('hidden.bs.modal', function () {
                document.getElementById('addClassForm').reset();
                tsAddYear.setValue('');
                tsAddTeacher.setValue('');
                clearFormValidation($('#addClassForm'));
                clearTsError(tsAddYear);
                clearTsError(tsAddTeacher);
                clearDupWarning('#class_name');
            });

            $('#classTable').on('click', '.btn-delete-class', function () {
                pendingDeleteRow = $(this).closest('tr');
                $('#deleteClassId').val($(this).data('id'));
                $('#deleteClassName').text($(this).data('name'));
                bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteClassModal')).show();
            });

            $('#deleteClassForm').on('submit', function (e) {
                e.preventDefault();
                var $btn = $(this).find('[type=submit]').prop('disabled', true);
                bootstrap.Modal.getInstance(document.getElementById('deleteClassModal'))?.hide();
                $('#preloader').fadeIn(200);
                $.ajax({
                    url: $(this).attr('action'), type: 'POST',
                    data: $(this).serialize(), dataType: 'json',
                    success: function (res) {
                        showToast(res.success ? 'success' : 'error', res.message);
                        if (res.success) {
                            pendingDeleteRow.fadeOut(400, function () { table.ajax.reload(null, false); });
                        }
                    },
                    error: function () { showToast('error', 'An unexpected error occurred.'); },
                    complete: function () { $('#preloader').fadeOut(400); $btn.prop('disabled', false); }
                });
            });

            $('#classTable').on('click', '.btn-edit-class', function () {
                $('#editClassId').val($(this).data('id'));
                $('#editClassName').val($(this).data('name'));
                $('#editClassGrade').val($(this).data('grade'));
                var yr = $(this).data('year') || '';
                if (yr && !tsEditYear.options[yr]) {
                    tsEditYear.addOption({ value: yr, text: yr });
                }
                tsEditYear.setValue(yr);
                tsEditTeacher.setValue($(this).data('teacher') || '');
                origClassSnap = $('#editClassForm').serialize();
                bootstrap.Modal.getOrCreateInstance(document.getElementById('editClassModal')).show();
            });

            $('#editClassModal').on('hidden.bs.modal', function () {
                tsEditTeacher.setValue('');
                clearFormValidation($('#editClassForm'));
                clearTsError(tsEditYear);
                clearTsError(tsEditTeacher);
                clearDupWarning('#editClassName');
            });

            $('#editClassForm').on('submit', function (e) {
                e.preventDefault();
                if ($('#editClassForm').serialize() === origClassSnap) {
                    showToast('warning', 'No changes were made.');
                    return;
                }
                clearTsError(tsEditYear);
                clearTsError(tsEditTeacher);
                var ok = validateForm([
                    { el: $('#editClassName'),  label: 'Section Name', required: true, minLen: 2, noDigits: true },
                    { el: $('#editClassGrade'), label: 'Grade Level',  required: true, digits: true,
                      pattern: /^[1-6]$/, patternMsg: 'Grade Level must be a number from 1 to 6.' },
                ]);
                if ($('#editClassName').hasClass('is-invalid') && $('#editClassName').next('.dup-feedback').length) ok = false;
                if (!tsEditYear.getValue())    { showTsError(tsEditYear,    'School Year is required.'); ok = false; }
                if (!tsEditTeacher.getValue()) { showTsError(tsEditTeacher, 'Teacher is required.');     ok = false; }
                if (!ok) return;
                var $btn = $(this).find('[type=submit]').prop('disabled', true);
                $('#preloader').fadeIn(200);
                $.ajax({
                    url: $(this).attr('action'), type: 'POST',
                    data: $(this).serialize(), dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            bootstrap.Modal.getInstance(document.getElementById('editClassModal'))?.hide();
                            showToast('success', res.message);
                            table.ajax.reload(null, false);
                        } else { showToast('error', res.message); }
                    },
                    error: function () { showToast('error', 'An unexpected error occurred.'); },
                    complete: function () { $('#preloader').fadeOut(400); $btn.prop('disabled', false); }
                });
            });
        });

    </script>
</body>
</html>
