<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classes | EWGS</title>
    <?php require_once 'views/templates/admin/header.php'; ?>
    <?php require_once 'views/templates/admin/modal.php'; ?>
    <link rel="stylesheet" href="/ewgs/public/css/tom-select.bootstrap5.min.css">
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
                <form id="addClassForm" method="POST" action="/ewgs/admin/class/add">
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
                            <label for="add_teacher_id" class="form-label">Assign Teacher <small class="text-muted">(optional)</small></label>
                            <select class="form-select" id="add_teacher_id" name="teacher_id">
                                <option value="">-- No Teacher --</option>
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
                <form id="editClassForm" method="POST" action="/ewgs/admin/class/edit">
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
                            <label for="edit_teacher_id" class="form-label">Assign Teacher <small class="text-muted">(optional)</small></label>
                            <select class="form-select" id="edit_teacher_id" name="teacher_id">
                                <option value="">-- No Teacher --</option>
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
                <form id="deleteClassForm" method="POST" action="/ewgs/admin/class/delete">
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

    <script src="/ewgs/public/js/bootstrap.bundle.js"></script>
    <?php require_once 'views/templates/admin/datatable.php'; ?>
    <script src="/ewgs/public/js/tom-select.complete.min.js"></script>
    <script>
        $(document).ready(function () {
            var pendingDeleteRow = null;
            var tsAddYear     = new TomSelect('#school_year',     { allowEmptyOption: true });
            var tsEditYear    = new TomSelect('#editClassYear',   { allowEmptyOption: true });
            var tsAddTeacher  = new TomSelect('#add_teacher_id',  { allowEmptyOption: true });
            var tsEditTeacher = new TomSelect('#edit_teacher_id', { allowEmptyOption: true });
            tsAddYear.setValue('<?= $defaultSy ?>');

            var table = $('#classTable').DataTable({
                ajax: {
                    url: '/ewgs/admin/class/data',
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'count', width: '5%' },
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

            /* ── Add class AJAX ────────────────────────────────── */
            $('#addClassForm').on('submit', function (e) {
                e.preventDefault();
                if (!validateForm([
                    { el: $('#class_name'),  label: 'Section Name', required: true, minLen: 2 },
                    { el: $('#grade_level'), label: 'Grade Level',  required: true, digits: true,
                      pattern: /^[1-6]$/, patternMsg: 'Grade Level must be a number from 1 to 6.' },
                    { el: $('#school_year'), label: 'School Year',  required: true }
                ])) return;
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
                bootstrap.Modal.getOrCreateInstance(document.getElementById('editClassModal')).show();
            });

            $('#editClassModal').on('hidden.bs.modal', function () {
                tsEditTeacher.setValue('');
                clearFormValidation($('#editClassForm'));
            });

            $('#editClassForm').on('submit', function (e) {
                e.preventDefault();
                if (!validateForm([
                    { el: $('#editClassName'),  label: 'Section Name', required: true, minLen: 2 },
                    { el: $('#editClassGrade'), label: 'Grade Level',  required: true, digits: true,
                      pattern: /^[1-6]$/, patternMsg: 'Grade Level must be a number from 1 to 6.' },
                    { el: $('#editClassYear'),  label: 'School Year',  required: true }
                ])) return;
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
