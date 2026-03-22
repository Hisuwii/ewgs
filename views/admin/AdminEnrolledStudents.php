<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linked Students | EWGS</title>
    <?php require_once 'views/templates/admin/header.php'; ?>
    <?php require_once 'views/templates/admin/modal.php'; ?>
    <?php require_once 'views/templates/admin/datatable.php'; ?>
    <link rel="stylesheet" href="<?= BASE ?>/public/css/tom-select.bootstrap5.min.css">
    <style>
        .ts-wrapper .ts-control { border-color: #d0d8d0 !important; border-radius: 6px !important; min-height: 38px; font-size: 14px; }
        .ts-wrapper.focus .ts-control { border-color: #4b6b4b !important; box-shadow: 0 0 0 2px rgba(75,107,75,0.15) !important; }
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

<div class="main-content">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h4 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Linked Students</h4>
        <a href="<?= BASE ?>/admin/assign/student" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-person-plus me-1"></i>Link Students to Class
        </a>
    </div>

    <!-- Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-end g-3">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Filter by Class</label>
                    <select id="filterClass" class="form-select">
                        <option value="">— All Classes —</option>
                        <?php foreach ($classes as $c): ?>
                            <option value="<?= $c['class_id'] ?>">
                                <?= htmlspecialchars($c['class_name']) ?> — Grade <?= htmlspecialchars($c['grade_level']) ?> (<?= htmlspecialchars($c['school_year']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrollment Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table id="enrolledTable" class="table table-hover w-100">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>LRN</th>
                        <th>Gender</th>
                        <th>Class</th>
                        <th>Grade Level</th>
                        <th>School Year</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Remove Confirmation Modal -->
<div class="modal fade" id="removeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Remove Class Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Remove <strong id="removeStudentName"></strong> from <strong id="removeClassName"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-del" id="btnConfirmRemove">Remove</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= BASE ?>/public/js/bootstrap.bundle.js"></script>
<script src="<?= BASE ?>/public/js/tom-select.complete.min.js"></script>
<script>
$(function () {
    var currentClassId = null;

    /* ── Tom Select ─────────────────────────────────── */
    var tsFilter = new TomSelect('#filterClass', { allowEmptyOption: true });
    tsFilter.on('change', function (val) {
        currentClassId = val ? parseInt(val) : null;
        table.ajax.reload(null, false);
    });

    /* ── DataTable ───────────────────────────────────── */
    var table = $('#enrolledTable').DataTable({
        serverSide: true,
        processing: true,
        searchDelay: 500,
        order: [],
        ajax: {
            url: '<?= BASE ?>/admin/assign/student/data',
            type: 'GET',
            data: function (d) { if (currentClassId) d.class_id = currentClassId; return d; },
            dataSrc: 'data'
        },
        columns: [
            { data: null, orderable: false, searchable: false,
              render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
            { data: 'student_name' },
            { data: 'lrn' },
            { data: 'gender' },
            { data: 'class_name' },
            { data: 'grade_level' },
            { data: 'school_year' },
            {
                data: null, orderable: false,
                render: function (d) {
                    return '<button class="btn btn-sm btn-danger btn-remove"' +
                           ' data-student-id="' + d.student_id + '"' +
                           ' data-class-id="' + d.class_id + '"' +
                           ' data-student="' + d.student_name + '"' +
                           ' data-class="' + d.class_name + '">' +
                           '<i class="bi bi-person-x me-1"></i>Remove</button>';
                }
            }
        ],
        pageLength: 25,
        language: { emptyTable: 'No student enrollments found.' }
    });

    /* ── Remove ─────────────────────────────────────── */
    var pendingStudentId = null, pendingClassId = null;

    $(document).on('click', '.btn-remove', function () {
        pendingStudentId = $(this).data('student-id');
        pendingClassId   = $(this).data('class-id');
        $('#removeStudentName').text($(this).data('student'));
        $('#removeClassName').text($(this).data('class'));
        bootstrap.Modal.getOrCreateInstance($('#removeModal')[0]).show();
    });

    $('#btnConfirmRemove').on('click', function () {
        if (!pendingStudentId || !pendingClassId) return;
        bootstrap.Modal.getOrCreateInstance($('#removeModal')[0]).hide();

        $.post('<?= BASE ?>/admin/assign/student/unlink',
            { student_id: pendingStudentId, class_id: pendingClassId },
            function (res) {
                if (res.success) {
                    showToast('success', 'Student unlinked from class.');
                    table.ajax.reload(null, false);
                } else {
                    showToast('error', 'Failed to remove. Please try again.');
                }
            }
        ).fail(function () {
            showToast('error', 'An error occurred. Please try again.');
        });
    });
});
</script>
</body>
</html>
