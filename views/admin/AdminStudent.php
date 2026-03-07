<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students | EWGS</title>
    <?php require_once 'views/templates/admin/header.php'; ?>
    <?php require_once 'views/templates/admin/modal.php'; ?>
    <style>
        #addStudent {
            background-color: #4b6b4b !important;
            color: white;
        }
        #importStudent {
            background-color: #1565c0 !important;
            color: white;
        }
        .btn-action-edit {
            background-color: #4b6b4b;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 5px;
            font-weight: 500;
            font-size: 13px;
            transition: all 0.2s ease;
        }
        .btn-action-edit:hover {
            background-color: #3a5a3a;
            color: white;
        }
        .btn-action-delete {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 5px;
            font-weight: 500;
            font-size: 13px;
            transition: all 0.2s ease;
        }
        .btn-action-delete:hover {
            background-color: #bb2d3b;
            color: white;
        }
        .delete-warning-icon {
            width: 60px;
            height: 60px;
            background-color: #fff3f3;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
        }
        .delete-warning-icon i {
            font-size: 28px;
            color: #dc3545;
        }
        .import-info {
            background: #f0f4ff;
            border-left: 4px solid #1565c0;
            padding: 10px 14px;
            border-radius: 4px;
            font-size: 13px;
            color: #333;
        }
    </style>
</head>
<body>
    <?php require_once 'views/templates/admin/sidebar.php'; ?>

    <?= displayFlash() ?>

    <div class="main-content">
        <div class="page-header">
            <h4>Students</h4>
        </div>

        <div class="container-fluid px-4">
            <div class="d-flex justify-content-end gap-2 mb-3">
                <button type="button" id="importStudent" class="btn"
                        data-bs-toggle="modal" data-bs-target="#importStudentModal">
                    <i class="bi bi-file-earmark-excel me-1"></i> Import Excel
                </button>
                <button type="button" id="addStudent" class="btn"
                        data-bs-toggle="modal" data-bs-target="#addStudentModal">
                    <i class="bi bi-plus-circle me-1"></i> Add Student
                </button>
            </div>

            <div class="table-responsive">
                <table id="studentTable" class="table table-hover text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>LRN</th>
                            <th>Gender</th>
                            <th>Age</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addStudentForm" method="POST" action="/ewgs/admin/student/add">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStudentModalLabel">Add New Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="student_fname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="student_fname" name="student_fname" required>
                        </div>
                        <div class="mb-3">
                            <label for="student_lname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="student_lname" name="student_lname" required>
                        </div>
                        <div class="mb-3">
                            <label for="lrn" class="form-label">LRN</label>
                            <input type="text" class="form-control" id="lrn" name="lrn"
                                   pattern="\d{12}" maxlength="12" title="LRN must be exactly 12 digits" required>
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">— Select —</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="birth_date" class="form-label">Birth Date</label>
                            <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-save">Save Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importStudentModal" tabindex="-1" aria-labelledby="importStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="importStudentForm" method="POST" action="/ewgs/admin/student/import" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importStudentModalLabel">
                            <i class="bi bi-file-earmark-excel me-1"></i> Import Students
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="import-info mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            Upload a <strong>.xlsx</strong> or <strong>.csv</strong> file.
                            The <strong>first row</strong> must be the column header.<br>
                            <strong>Required columns (in order):</strong>
                            First Name, Last Name, LRN, Gender<br>
                            <strong>Optional:</strong> Birth Date (column 5, format YYYY-MM-DD)
                        </div>
                        <div class="mb-3">
                            <label for="import_file" class="form-label fw-semibold">Choose File</label>
                            <input type="file" class="form-control" id="import_file"
                                   name="import_file" accept=".xlsx,.csv" required>
                        </div>
                        <div class="text-center">
                            <a href="/ewgs/admin/student/template" class="text-decoration-none small">
                                <i class="bi bi-download me-1"></i>Download sample template (.xlsx)
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-save">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editStudentForm" method="POST" action="/ewgs/admin/student/edit">
                    <input type="hidden" name="student_id" id="editStudentId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStudentModalLabel">Edit Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editStudentFname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="editStudentFname" name="student_fname" required>
                        </div>
                        <div class="mb-3">
                            <label for="editStudentLname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="editStudentLname" name="student_lname" required>
                        </div>
                        <div class="mb-3">
                            <label for="editStudentLrn" class="form-label">LRN</label>
                            <input type="text" class="form-control" id="editStudentLrn" name="lrn"
                                   pattern="\d{12}" maxlength="12" title="LRN must be exactly 12 digits" required>
                        </div>
                        <div class="mb-3">
                            <label for="editStudentGender" class="form-label">Gender</label>
                            <select class="form-select" id="editStudentGender" name="gender">
                                <option value="">— Select —</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editStudentBirthDate" class="form-label">Birth Date</label>
                            <input type="date" class="form-control" id="editStudentBirthDate" name="birth_date" required>
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

    <!-- Delete Student Modal -->
    <div class="modal fade" id="deleteStudentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form id="deleteStudentForm" method="POST" action="/ewgs/admin/student/delete">
                    <input type="hidden" name="student_id" id="deleteStudentId">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="delete-warning-icon">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <p class="mb-1 fw-semibold">Delete Student?</p>
                        <p class="text-muted small mb-0">You are about to delete <strong id="deleteStudentName"></strong>. This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-del">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/ewgs/public/js/bootstrap.bundle.js"></script>
    <?php require_once 'views/templates/admin/datatable.php'; ?>
    <script>
        function ajaxForm(formId, modalId, onSuccess, validateFn) {
            $(formId).on('submit', function (e) {
                e.preventDefault();
                if (validateFn && !validateFn()) return;
                var $btn = $(this).find('[type=submit]').prop('disabled', true).text('Saving…');
                $('#preloader').fadeIn(200);
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            bootstrap.Modal.getInstance(document.getElementById(modalId))?.hide();
                            showToast('success', res.message);
                            if (onSuccess) onSuccess();
                        } else {
                            showToast('error', res.message);
                        }
                    },
                    error: function () {
                        showToast('error', 'An unexpected error occurred.');
                    },
                    complete: function () {
                        $('#preloader').fadeOut(400);
                        $btn.prop('disabled', false).text($btn.data('label'));
                    }
                });
            });
        }

        $(document).ready(function () {
            var pendingDeleteRow = null;

            $('.flash-toast').each(function () {
                bootstrap.Toast.getOrCreateInstance(this, { delay: 5000 }).show();
            });

            // Store original button labels for restore after request
            $('[type=submit]').each(function () {
                $(this).data('label', $(this).text());
            });

            var table = $('#studentTable').DataTable({
                ajax: {
                    url: '/ewgs/admin/student/data',
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'count',         width: '5%' },
                    { data: 'student_fname' },
                    { data: 'student_lname' },
                    { data: 'lrn' },
                    { data: 'gender' },
                    {
                        data: 'birth_date',
                        render: function (data) {
                            if (!data) return '<span class="text-muted">—</span>';
                            var today = new Date();
                            var birth = new Date(data);
                            var age = today.getFullYear() - birth.getFullYear();
                            var m = today.getMonth() - birth.getMonth();
                            if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
                            return age;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data) {
                            return '<div class="d-flex justify-content-center gap-1">' +
                                   '<button class="btn-action-edit btn-edit-student" ' +
                                   'data-id="'     + data.student_id    + '" ' +
                                   'data-fname="'  + data.student_fname + '" ' +
                                   'data-lname="'  + data.student_lname + '" ' +
                                   'data-lrn="'    + data.lrn           + '" ' +
                                   'data-gender="' + data.gender        + '" ' +
                                   'data-bdate="'  + (data.birth_date || '') + '">' +
                                   '<i class="bi bi-pencil-square"></i> Edit</button>' +
                                   '<button class="btn-action-delete btn-delete-student" ' +
                                   'data-id="'   + data.student_id + '" ' +
                                   'data-name="' + data.student_fname + ' ' + data.student_lname + '">' +
                                   '<i class="bi bi-trash"></i> Delete</button>' +
                                   '</div>';
                        }
                    }
                ],
                language: { emptyTable: 'No students found.' }
            });

            // ── Open Edit modal ──────────────────────────────────────
            $('#studentTable').on('click', '.btn-edit-student', function () {
                $('#editStudentId').val($(this).data('id'));
                $('#editStudentFname').val($(this).data('fname'));
                $('#editStudentLname').val($(this).data('lname'));
                $('#editStudentLrn').val($(this).data('lrn'));
                $('#editStudentGender').val($(this).data('gender'));
                $('#editStudentBirthDate').val($(this).data('bdate') || '');
                new bootstrap.Modal(document.getElementById('editStudentModal')).show();
            });

            // ── Open Delete modal ────────────────────────────────────
            $('#studentTable').on('click', '.btn-delete-student', function () {
                pendingDeleteRow = $(this).closest('tr');
                $('#deleteStudentId').val($(this).data('id'));
                $('#deleteStudentName').text($(this).data('name'));
                new bootstrap.Modal(document.getElementById('deleteStudentModal')).show();
            });

            // ── Reset Add form when modal closes ─────────────────────
            $('#addStudentModal').on('hidden.bs.modal', function () {
                document.getElementById('addStudentForm').reset();
                clearFormValidation($('#addStudentForm'));
            });

            $('#editStudentModal').on('hidden.bs.modal', function () {
                clearFormValidation($('#editStudentForm'));
            });

            // ── AJAX form handlers ───────────────────────────────────
            ajaxForm('#addStudentForm',  'addStudentModal',  function () { table.ajax.reload(null, false); }, function () {
                return validateForm([
                    { el: $('#student_fname'), label: 'First Name', required: true, minLen: 2 },
                    { el: $('#student_lname'), label: 'Last Name',  required: true, minLen: 2 },
                    { el: $('#lrn'),           label: 'LRN',        required: true, digits: true, exactLen: 12 },
                    { el: $('#gender'),        label: 'Gender',     required: true },
                    { el: $('#birth_date'),    label: 'Birth Date', required: true }
                ]);
            });
            ajaxForm('#editStudentForm', 'editStudentModal', function () { table.ajax.reload(null, false); }, function () {
                return validateForm([
                    { el: $('#editStudentFname'),     label: 'First Name', required: true, minLen: 2 },
                    { el: $('#editStudentLname'),     label: 'Last Name',  required: true, minLen: 2 },
                    { el: $('#editStudentLrn'),       label: 'LRN',        required: true, digits: true, exactLen: 12 },
                    { el: $('#editStudentGender'),    label: 'Gender',     required: true },
                    { el: $('#editStudentBirthDate'), label: 'Birth Date', required: true }
                ]);
            });
            // ── Delete student AJAX (with row fadeOut) ───────────────
            $('#deleteStudentForm').on('submit', function (e) {
                e.preventDefault();
                var $btn = $(this).find('[type=submit]').prop('disabled', true);
                bootstrap.Modal.getInstance(document.getElementById('deleteStudentModal'))?.hide();
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

            // ── Import Excel AJAX (file upload uses FormData) ────────
            $('#importStudentForm').on('submit', function (e) {
                e.preventDefault();
                var formData = new FormData(this);
                var $btn = $(this).find('[type=submit]').prop('disabled', true);
                bootstrap.Modal.getInstance(document.getElementById('importStudentModal'))?.hide();
                $('#preloader').fadeIn(200);
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (res) {
                        showToast(res.success ? 'success' : 'error', res.message);
                        if (res.success) table.ajax.reload(null, false);
                    },
                    error: function () {
                        showToast('error', 'An unexpected error occurred during import.');
                    },
                    complete: function () {
                        $('#preloader').fadeOut(400);
                        $btn.prop('disabled', false);
                    }
                });
            });

            $('#importStudentModal').on('hidden.bs.modal', function () {
                document.getElementById('importStudentForm').reset();
            });
        });
    </script>
</body>
</html>
