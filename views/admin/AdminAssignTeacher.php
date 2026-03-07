<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Teacher | EWGS</title>
    <?php require_once 'views/templates/admin/header.php'; ?>
    <?php require_once 'views/templates/admin/modal.php'; ?>
    <?php require_once 'views/templates/admin/datatable.php'; ?>
    <link rel="stylesheet" href="/ewgs/public/css/tom-select.bootstrap5.min.css">
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
    <div class="page-header">
        <h4><i class="bi bi-person-check me-2"></i>Link Teacher to Class</h4>
    </div>

    <!-- Link Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-semibold" style="background:#4b6b4b; color:#fff;">
            <i class="bi bi-link-45deg me-2"></i>Assign a Teacher
        </div>
        <div class="card-body d-flex flex-wrap gap-3 align-items-end">
            <div style="min-width:220px; flex:1;">
                <label class="form-label fw-semibold mb-1">Select Teacher</label>
                <select id="selTeacher" class="form-select">
                    <option value="">— Select a teacher —</option>
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?= $t['teacher_id'] ?>">
                            <?= htmlspecialchars($t['teacher_fname'] . ' ' . $t['teacher_lname']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="min-width:220px; flex:1;">
                <label class="form-label fw-semibold mb-1">Assign to Class</label>
                <select id="selClass" class="form-select">
                    <option value="">— Select a class —</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['class_id'] ?>">
                            <?= htmlspecialchars($c['class_name']) ?> — Grade <?= htmlspecialchars($c['grade_level']) ?> (<?= htmlspecialchars($c['school_year']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button id="btnLink" class="btn" style="background-color:#2e4e2e; color:#fff;">
                    <i class="bi bi-link-45deg me-1"></i>Link
                </button>
            </div>
        </div>
    </div>

    <!-- Links Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table id="linksTable" class="table table-hover w-100">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Teacher</th>
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

<!-- Unlink Confirmation Modal -->
<div class="modal fade" id="unlinkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Remove Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-del" id="btnConfirmUnlink">Remove</button>
                </div>
                Remove <strong id="unlinkTeacherName"></strong> from <strong id="unlinkClassName"></strong>?
            </div>
        </div>
    </div>
</div>

<script src="/ewgs/public/js/bootstrap.bundle.js"></script>
<script src="/ewgs/public/js/tom-select.complete.min.js"></script>
<script>
$(function () {
    var tsTeacher = new TomSelect('#selTeacher', { allowEmptyOption: true });
    var tsClass   = new TomSelect('#selClass',   { allowEmptyOption: true });

    var table = $('#linksTable').DataTable({
        ajax: { url: '/ewgs/admin/assign/teacher/data', type: 'GET', dataSrc: 'data' },
        columns: [
            { data: 'count' },
            { data: 'teacher_name' },
            { data: 'class_name' },
            { data: 'grade_level' },
            { data: 'school_year' },
            {
                data: null, orderable: false,
                render: function (d) {
                    return '<button class="btn btn-sm btn-danger btn-unlink" ' +
                           'data-class-id="' + d.class_id + '" ' +
                           'data-teacher="' + d.teacher_name + '" ' +
                           'data-class="' + d.class_name + '">' +
                           '<i class="bi bi-x-lg me-1"></i>Unlink</button>';
                }
            }
        ],
        pageLength: 10,
        language: { emptyTable: 'No teacher–class links yet.' }
    });

    $('#btnLink').on('click', function () {
        var teacherId = tsTeacher.getValue();
        var classId   = tsClass.getValue();
        if (!teacherId || !classId) {
            showToast('warning', 'Please select both a teacher and a class.');
            return;
        }
        var $btn = $(this).prop('disabled', true);
        $.post('/ewgs/admin/assign/teacher/link', { teacher_id: teacherId, class_id: classId }, function (res) {
            if (res.success) {
                showToast('success', 'Teacher linked to class successfully.');
                table.ajax.reload(null, false);
                tsTeacher.clear(); tsClass.clear();
            } else if (res.duplicate) {
                showToast('warning', res.message);
            } else {
                showToast('error', 'Failed to link. Please try again.');
            }
        }).fail(function () {
            showToast('error', 'An error occurred. Please try again.');
        }).always(function () { $btn.prop('disabled', false); });
    });

    var pendingClassId = null;
    $(document).on('click', '.btn-unlink', function () {
        pendingClassId = $(this).data('class-id');
        $('#unlinkTeacherName').text($(this).data('teacher'));
        $('#unlinkClassName').text($(this).data('class'));
        bootstrap.Modal.getOrCreateInstance($('#unlinkModal')[0]).show();
    });

    $('#btnConfirmUnlink').on('click', function () {
        if (!pendingClassId) return;
        bootstrap.Modal.getOrCreateInstance($('#unlinkModal')[0]).hide();
        $.post('/ewgs/admin/assign/teacher/unlink', { class_id: pendingClassId }, function (res) {
            if (res.success) {
                showToast('success', 'Teacher unlinked from class.');
                table.ajax.reload(null, false);
            } else {
                showToast('error', 'Failed to unlink. Please try again.');
            }
        }).fail(function () {
            showToast('error', 'An error occurred. Please try again.');
        });
    });
});
</script>
</body>
</html>
