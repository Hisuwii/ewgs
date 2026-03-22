<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teachers | EWGS</title>
    <?php require_once 'views/templates/admin/header.php'; ?>
    <?php require_once 'views/templates/admin/modal.php'; ?>
    <style>
        #addTeacher {
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
        .btn-reset-pw {
            background-color: #fd7e14;
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .btn-reset-pw:hover {
            background-color: #e8650a;
            color: white;
        }
        .btn-toggle-status {
            border: none;
            padding: 6px 14px;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .active-status   { background-color: #198754; color: white; }
        .active-status:hover { background-color: #157347; color: white; }
        .inactive-status { background-color: #6c757d; color: white; }
        .inactive-status:hover { background-color: #565e64; color: white; }
    </style>
</head>
<body>
    <?php require_once 'views/templates/admin/sidebar.php'; ?>

    <!-- Flash Messages -->
    <?= displayFlash() ?>

    <div class="main-content">
        <div class="page-header">
            <h4>Teachers</h4>
        </div>

        <div class="container-fluid px-4">
            <div class="d-flex justify-content-end mb-3">
                <button type="button" id="addTeacher" class="btn" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                    <i class="bi bi-plus-circle"></i> Add Teacher
                </button>
            </div>

            <div class="table-responsive">
                <table id="teacherTable" class="table table-bordered table-striped table-hover text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Teacher Modal -->
    <div class="modal fade" id="addTeacherModal" tabindex="-1" aria-labelledby="addTeacherModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addTeacherForm" method="POST" action="<?= BASE ?>/admin/teacher/add">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addTeacherModalLabel">Add New Teacher</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="teacher_fname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="teacher_fname" name="teacher_fname" required>
                        </div>
                        <div class="mb-3">
                            <label for="teacher_lname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="teacher_lname" name="teacher_lname" required>
                        </div>
                        <div class="mb-3">
                            <label for="teacher_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="teacher_email" name="teacher_email" required>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> Password will be auto-generated (8 characters)
                        </small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-save">Save Teacher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Teacher Modal -->
    <div class="modal fade" id="editTeacherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editTeacherForm" method="POST" action="<?= BASE ?>/admin/teacher/edit">
                    <input type="hidden" name="teacher_id" id="editTeacherId">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Teacher</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" id="editTeacherFname" name="teacher_fname" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="editTeacherLname" name="teacher_lname" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="editTeacherEmail" name="teacher_email" required>
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

    <!-- Delete Teacher Modal -->
    <div class="modal fade" id="deleteTeacherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="deleteTeacherForm" method="POST" action="<?= BASE ?>/admin/teacher/delete">
                    <input type="hidden" name="teacher_id" id="deleteTeacherId">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Teacher</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete <strong id="deleteTeacherName"></strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-del">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reset Password Result Modal -->
    <div class="modal fade" id="resetPwModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-key me-2"></i>Password Reset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-1">New password for <strong id="resetPwTeacherName"></strong>:</p>
                    <div class="input-group">
                        <input type="text" id="resetPwValue" class="form-control font-monospace fw-bold" readonly>
                        <button class="btn btn-outline-secondary" id="copyPwBtn" type="button">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        <i class="bi bi-info-circle"></i> The teacher has been emailed this password and will be required to change it on next login.
                    </small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-save" data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= BASE ?>/public/js/bootstrap.bundle.js"></script>
    <?php require_once 'views/templates/admin/datatable.php'; ?>
    <script>
        $(document).ready(function () {
            var pendingDeleteRow = null;
            var origTeacherSnap  = '';
            var table = $('#teacherTable').DataTable({
                serverSide: true,
                processing: true,
                searchDelay: 500,
                order: [],
                ajax: {
                    url: '<?= BASE ?>/admin/teacher/data',
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [
                    { data: null, orderable: false, searchable: false, width: '5%',
                      render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
                    { data: 'teacher_fname' },
                    { data: 'teacher_lname' },
                    { data: 'teacher_email' },
                    {
                        data: 'status',
                        render: function (data) {
                            var cls = data === 'Active' ? 'text-success fw-semibold' : 'text-secondary fw-semibold';
                            return '<span class="' + cls + '">' + data + '</span>';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data) {
                            var isActive = data.status === 'Active';
                            var toggleCls = isActive ? 'active-status' : 'inactive-status';
                            var toggleLabel = isActive ? 'Deactivate' : 'Activate';
                            return `
                                <button class="btn btn-sm btn-edit btn-edit-teacher me-1"
                                    data-id="${data.teacher_id}"
                                    data-fname="${data.teacher_fname}"
                                    data-lname="${data.teacher_lname}"
                                    data-email="${data.teacher_email}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-reset-pw btn-reset-teacher me-1"
                                    data-id="${data.teacher_id}"
                                    data-name="${data.teacher_fname} ${data.teacher_lname}">
                                    <i class="bi bi-key"></i> Reset PW
                                </button>
                                <button class="btn btn-sm btn-toggle-status ${toggleCls} me-1"
                                    data-id="${data.teacher_id}"
                                    data-status="${data.status}">
                                    <i class="bi bi-toggle-${isActive ? 'on' : 'off'}"></i> ${toggleLabel}
                                </button>
                                <button class="btn btn-sm btn-delete btn-delete-teacher"
                                    data-id="${data.teacher_id}"
                                    data-name="${data.teacher_fname} ${data.teacher_lname}">
                                    <i class="bi bi-trash"></i> Delete
                                </button>`;
                        }
                    }
                ],
                language: {
                    emptyTable: 'No teachers found.'
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

            /* ── Add teacher AJAX ──────────────────────────────── */
            $('#addTeacherForm').on('submit', function (e) {
                e.preventDefault();
                if (!validateForm([
                    { el: $('#teacher_fname'), label: 'First Name', required: true, minLen: 2, noDigits: true },
                    { el: $('#teacher_lname'), label: 'Last Name',  required: true, minLen: 2, noDigits: true },
                    { el: $('#teacher_email'), label: 'Email',      required: true, email: true }
                ])) return;
                var $btn = $(this).find('[type=submit]').prop('disabled', true);
                bootstrap.Modal.getInstance(document.getElementById('addTeacherModal'))?.hide();
                $('#preloader').fadeIn(200);
                $.ajax({
                    url: $(this).attr('action'), type: 'POST',
                    data: $(this).serialize(), dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            showToast('success', res.message);
                            table.ajax.reload(null, false);
                        } else {
                            showToast('error', res.message);
                        }
                    },
                    error: function () { showToast('error', 'An unexpected error occurred.'); },
                    complete: function () {
                        $('#preloader').fadeOut(400);
                        $btn.prop('disabled', false);
                    }
                });
            });

            $('#addTeacherModal').on('hidden.bs.modal', function () {
                document.getElementById('addTeacherForm').reset();
                clearFormValidation($('#addTeacherForm'));
            });

            $('#teacherTable').on('click', '.btn-toggle-status', function () {
                var $btn = $(this).prop('disabled', true);
                var id   = $(this).data('id');
                $.ajax({
                    url: '<?= BASE ?>/admin/teacher/toggle-status',
                    type: 'POST',
                    data: { teacher_id: id },
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            showToast('success', res.message);
                            table.ajax.reload(null, false);
                        } else {
                            showToast('error', res.message);
                        }
                    },
                    error: function () { showToast('error', 'An unexpected error occurred.'); },
                    complete: function () { $btn.prop('disabled', false); }
                });
            });

            $('#teacherTable').on('click', '.btn-reset-teacher', function () {
                var $btn  = $(this).prop('disabled', true);
                var id    = $(this).data('id');
                var name  = $(this).data('name');
                $.ajax({
                    url: '<?= BASE ?>/admin/teacher/reset-password',
                    type: 'POST',
                    data: { teacher_id: id },
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            $('#resetPwTeacherName').text(name);
                            $('#resetPwValue').val(res.password);
                            bootstrap.Modal.getOrCreateInstance(document.getElementById('resetPwModal')).show();
                        } else {
                            showToast('error', res.message);
                        }
                    },
                    error: function () { showToast('error', 'An unexpected error occurred.'); },
                    complete: function () { $btn.prop('disabled', false); }
                });
            });

            $('#copyPwBtn').on('click', function () {
                navigator.clipboard.writeText($('#resetPwValue').val()).then(function () {
                    $('#copyPwBtn').html('<i class="bi bi-clipboard-check"></i> Copied').addClass('btn-success').removeClass('btn-outline-secondary');
                    setTimeout(function () {
                        $('#copyPwBtn').html('<i class="bi bi-clipboard"></i> Copy').removeClass('btn-success').addClass('btn-outline-secondary');
                    }, 2000);
                });
            });

            $('#teacherTable').on('click', '.btn-delete-teacher', function () {
                pendingDeleteRow = $(this).closest('tr');
                $('#deleteTeacherId').val($(this).data('id'));
                $('#deleteTeacherName').text($(this).data('name'));
                bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteTeacherModal')).show();
            });

            $('#deleteTeacherForm').on('submit', function (e) {
                e.preventDefault();
                var $btn = $(this).find('[type=submit]').prop('disabled', true);
                bootstrap.Modal.getInstance(document.getElementById('deleteTeacherModal'))?.hide();
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

            $('#teacherTable').on('click', '.btn-edit-teacher', function () {
                $('#editTeacherId').val($(this).data('id'));
                $('#editTeacherFname').val($(this).data('fname'));
                $('#editTeacherLname').val($(this).data('lname'));
                $('#editTeacherEmail').val($(this).data('email'));
                origTeacherSnap = $('#editTeacherForm').serialize();
                liveNameCheck($('#editTeacherFname'), 'First Name');
                liveNameCheck($('#editTeacherLname'), 'Last Name');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('editTeacherModal')).show();
            });

            // ── Email uniqueness check ───────────────────────────────
            function emailFeedback($input, available) {
                var $fb = $input.siblings('.email-feedback');
                if (!$fb.length) $fb = $('<small class="email-feedback d-block mt-1"></small>').insertAfter($input);
                var $btn = $input.closest('form').find('[type=submit]');
                if (available === null) {
                    $fb.text('').removeClass('text-success text-danger');
                    $btn.prop('disabled', false);
                } else if (available) {
                    $fb.text('✓ Email is available').removeClass('text-danger').addClass('text-success');
                    $btn.prop('disabled', false);
                } else {
                    $fb.text('✗ Email already in use').removeClass('text-success').addClass('text-danger');
                    $btn.prop('disabled', true);
                }
            }

            function checkEmail($input, excludeId) {
                var email = $input.val().trim();
                if (!email || !email.includes('@')) { emailFeedback($input, null); return; }
                var params = { email: email };
                if (excludeId) params.exclude_id = excludeId;
                $.getJSON('<?= BASE ?>/admin/teacher/check-email', params, function (res) {
                    emailFeedback($input, res.available);
                });
            }

            // Live name validation
            // Valid/empty branches run synchronously for immediate visual feedback.
            // Invalid branch uses setTimeout so it runs after the global is-invalid
            // clear handler in sidebar.php (which fires synchronously on input).
            function liveNameCheck($el, label) {
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
                               .after('<div class="name-invalid-feedback invalid-feedback">' + label + ' must only contain letters.</div>');
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

            $('#teacher_fname, #teacher_lname, #editTeacherFname, #editTeacherLname').on('input change blur', function () {
                liveNameCheck($(this), $(this).is('#teacher_fname, #editTeacherFname') ? 'First Name' : 'Last Name');
            });

            var emailTimer;
            $('#teacher_email').on('input', function () {
                clearTimeout(emailTimer);
                var $el = $(this);
                emailTimer = setTimeout(function () { checkEmail($el, null); }, 300);
            });
            $('#editTeacherEmail').on('input', function () {
                clearTimeout(emailTimer);
                var $el = $(this);
                emailTimer = setTimeout(function () { checkEmail($el, $('#editTeacherId').val()); }, 300);
            });

            $('#addTeacherModal').on('hidden.bs.modal', function () {
                emailFeedback($('#teacher_email'), null);
            });
            $('#editTeacherModal').on('hidden.bs.modal', function () {
                clearFormValidation($('#editTeacherForm'));
                emailFeedback($('#editTeacherEmail'), null);
            });

            $('#editTeacherForm').on('submit', function (e) {
                e.preventDefault();
                if ($('#editTeacherForm').serialize() === origTeacherSnap) {
                    showToast('warning', 'No changes were made.');
                    return;
                }
                if (!validateForm([
                    { el: $('#editTeacherFname'), label: 'First Name', required: true, minLen: 2, noDigits: true },
                    { el: $('#editTeacherLname'), label: 'Last Name',  required: true, minLen: 2, noDigits: true },
                    { el: $('#editTeacherEmail'), label: 'Email',      required: true, email: true }
                ])) return;
                var $btn = $(this).find('[type=submit]').prop('disabled', true);
                $('#preloader').fadeIn(200);
                $.ajax({
                    url: $(this).attr('action'), type: 'POST',
                    data: $(this).serialize(), dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            bootstrap.Modal.getInstance(document.getElementById('editTeacherModal'))?.hide();
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
