<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subjects | EWGS</title>
    <?php require_once 'views/templates/admin/header.php'; ?>
    <?php require_once 'views/templates/admin/modal.php'; ?>
    <link rel="stylesheet" href="/ewgs/public/css/tom-select.bootstrap5.min.css">
    <style>
        #addSubject {
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
        /* Fix scrollable modal with form wrapper */
        #addSubjectModal .modal-content > form {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            flex: 1;
            min-height: 0;
        }
        #addSubjectModal .modal-body {
            flex: 1;
            overflow-y: auto;
            min-height: 0;
        }

        /* Dark mode fixes for modal inputs */
        body.dark-mode .config-table input,
        body.dark-mode .scores-table input {
            background: #2a2a2a;
            color: #e0e0e0;
        }
        body.dark-mode .config-table .component-name,
        body.dark-mode .scores-table .row-label {
            background: #1e1e1e;
            color: #e0e0e0;
        }
        body.dark-mode .config-table .total-row td {
            background: #1a2a1a;
            color: #e0e0e0;
        }
        /* Component config table */
        .config-table th {
            background: #4b6b4b;
            color: #fff;
            font-weight: 500;
            padding: 10px 12px;
        }
        .config-table td {
            padding: 0;
            vertical-align: middle;
        }
        .config-table .component-name {
            background: #f8f9fa;
            padding: 10px 12px;
            font-weight: 500;
        }
        .config-table input {
            border: none;
            border-radius: 0;
            text-align: center;
            height: 40px;
        }
        .config-table input:focus {
            background: #fffef0;
            box-shadow: inset 0 0 0 2px #4b6b4b;
            outline: none;
        }
        .config-table .total-row td {
            background: #e8f5e9;
            padding: 10px 12px;
        }
        /* Activity scores table */
        .scores-table th {
            background: #4b6b4b;
            color: #fff;
            font-weight: 500;
            padding: 8px 10px;
            text-align: center;
            white-space: nowrap;
        }
        .scores-table td {
            padding: 0;
            text-align: center;
        }
        .scores-table .row-label {
            background: #f8f9fa;
            padding: 10px 12px;
            font-weight: 500;
            text-align: left;
        }
        .scores-table input {
            border: none;
            border-radius: 0;
            text-align: center;
            height: 40px;
            width: 100%;
        }
        .scores-table input:focus {
            background: #fffef0;
            box-shadow: inset 0 0 0 2px #4b6b4b;
            outline: none;
        }
        .percentage-valid   { color: #2e7d32; }
        .percentage-invalid { color: #c62828; }
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

    <?= displayFlash() ?>

    <div class="main-content">
        <div class="page-header">
            <h4>Subjects</h4>
        </div>

        <div class="container-fluid px-4">
            <div class="d-flex justify-content-end mb-3">
                <button type="button" id="addSubject" class="btn" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                    <i class="bi bi-plus-circle"></i> Add Subject
                </button>
            </div>

            <div class="table-responsive">
                <table id="subjectTable" class="table table-bordered table-striped table-hover text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Subject Name</th>
                            <th>Class</th>
                            <th>Teacher</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Subject Modal -->
    <div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <form id="addSubjectForm" method="POST" action="/ewgs/admin/subject/add">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addSubjectModalLabel">Add New Subject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <!-- Subject Name -->
                        <div class="mb-3">
                            <label for="subject_name" class="form-label fw-semibold">Subject Name</label>
                            <input type="text" class="form-control" id="subject_name" name="subject_name"
                                   placeholder="e.g. Mathematics" required>
                        </div>

                        <!-- Assign to Class -->
                        <div class="mb-4">
                            <label for="class_id" class="form-label fw-semibold">Assign to Class</label>
                            <select class="form-select" id="class_id" name="class_id">
                                <option value="">-- No Class Assigned --</option>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?= $class['class_id'] ?>">
                                        <?= htmlspecialchars($class['class_name'] . ' (' . $class['grade_level'] . ')') ?>
                                        <?= $class['teacher_name'] ? ' — ' . htmlspecialchars($class['teacher_name']) : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Component Configuration -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Component Configuration</label>
                            <div class="table-responsive">
                                <table class="table table-bordered config-table mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width:40%;">Component</th>
                                            <th class="text-center" style="width:30%;">Percentage (%)</th>
                                            <th class="text-center" style="width:30%;">No. of Activities</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="component-name">Written Work</td>
                                            <td>
                                                <input type="number" class="form-control percentage-input"
                                                       name="written_percentage" id="written_percentage"
                                                       min="0" max="100" step="0.01" placeholder="0" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control activity-count"
                                                       name="written_count" id="written_count"
                                                       min="1" max="20" value="1" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="component-name">Performance Task</td>
                                            <td>
                                                <input type="number" class="form-control percentage-input"
                                                       name="performance_percentage" id="performance_percentage"
                                                       min="0" max="100" step="0.01" placeholder="0" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control activity-count"
                                                       name="performance_count" id="performance_count"
                                                       min="1" max="20" value="1" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="component-name">Quarterly Exam</td>
                                            <td>
                                                <input type="number" class="form-control percentage-input"
                                                       name="quarterly_percentage" id="quarterly_percentage"
                                                       min="0" max="100" step="0.01" placeholder="0" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control activity-count"
                                                       name="quarterly_count" id="quarterly_count"
                                                       min="1" max="5" value="1" required>
                                            </td>
                                        </tr>
                                        <tr class="total-row">
                                            <td class="fw-bold text-end">Total:</td>
                                            <td class="text-center fw-bold">
                                                <span id="percentageTotal" class="percentage-invalid">0.0%
                                                    <i class="bi bi-exclamation-circle-fill"></i>
                                                </span>
                                            </td>
                                            <td class="text-center text-muted">
                                                <span id="activityTotal">3</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Dynamic Activity Perfect Scores -->
                        <div id="activityScoresContainer"></div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-save">Save Subject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Subject Modal -->
    <div class="modal fade" id="editSubjectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editSubjectForm" method="POST" action="/ewgs/admin/subject/edit">
                    <input type="hidden" name="subject_id" id="editSubjectId">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Subject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Subject Name</label>
                            <input type="text" class="form-control" id="editSubjectName" name="subject_name" required>
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

    <!-- Delete Subject Modal -->
    <div class="modal fade" id="deleteSubjectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="deleteSubjectForm" method="POST" action="/ewgs/admin/subject/delete">
                    <input type="hidden" name="subject_id" id="deleteSubjectId">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Subject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete <strong id="deleteSubjectName"></strong>?
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
            var tsClass = new TomSelect('#class_id', { allowEmptyOption: true });

            /* ── DataTable ─────────────────────────────────────── */
            var table = $('#subjectTable').DataTable({
                ajax: {
                    url: '/ewgs/admin/subject/data',
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'count', width: '5%' },
                    { data: 'subject_name' },
                    {
                        data: 'class_name',
                        render: function (data) {
                            return data ? data : '<span class="text-muted">Unassigned</span>';
                        }
                    },
                    {
                        data: 'teacher_name',
                        render: function (data) {
                            return data ? data : '<span class="text-muted">Unassigned</span>';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data) {
                            return `
                                <button class="btn btn-sm btn-edit btn-edit-subject me-1"
                                    data-id="${data.subject_id}"
                                    data-name="${data.subject_name}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-delete btn-delete-subject"
                                    data-id="${data.subject_id}"
                                    data-name="${data.subject_name}">
                                    <i class="bi bi-trash"></i> Delete
                                </button>`;
                        }
                    }
                ],
                language: { emptyTable: 'No subjects found.' }
            });

            /* ── Delete modal ──────────────────────────────────── */
            $('#subjectTable').on('click', '.btn-delete-subject', function () {
                pendingDeleteRow = $(this).closest('tr');
                $('#deleteSubjectId').val($(this).data('id'));
                $('#deleteSubjectName').text($(this).data('name'));
                bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteSubjectModal')).show();
            });

            $('#deleteSubjectForm').on('submit', function (e) {
                e.preventDefault();
                var $btn = $(this).find('[type=submit]').prop('disabled', true);
                bootstrap.Modal.getInstance(document.getElementById('deleteSubjectModal'))?.hide();
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

            /* ── Edit modal ────────────────────────────────────── */
            $('#subjectTable').on('click', '.btn-edit-subject', function () {
                $('#editSubjectId').val($(this).data('id'));
                $('#editSubjectName').val($(this).data('name'));
                bootstrap.Modal.getOrCreateInstance(document.getElementById('editSubjectModal')).show();
            });

            $('#editSubjectModal').on('hidden.bs.modal', function () {
                clearFormValidation($('#editSubjectForm'));
            });

            $('#editSubjectForm').on('submit', function (e) {
                e.preventDefault();
                if (!validateForm([
                    { el: $('#editSubjectName'), label: 'Subject Name', required: true, minLen: 2 }
                ])) return;
                var $btn = $(this).find('[type=submit]').prop('disabled', true);
                $('#preloader').fadeIn(200);
                $.ajax({
                    url: $(this).attr('action'), type: 'POST',
                    data: $(this).serialize(), dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            bootstrap.Modal.getInstance(document.getElementById('editSubjectModal'))?.hide();
                            showToast('success', res.message);
                            table.ajax.reload(null, false);
                        } else { showToast('error', res.message); }
                    },
                    error: function () { showToast('error', 'An unexpected error occurred.'); },
                    complete: function () { $('#preloader').fadeOut(400); $btn.prop('disabled', false); }
                });
            });

            /* ── Reset modal form when closed ─────────────────── */
            $('#addSubjectModal').on('hidden.bs.modal', function () {
                $('#addSubjectForm')[0].reset();
                tsClass.setValue('');
                $('#activityScoresContainer').empty();
                updatePercentageTotal();
                updateActivityTotal();
                clearFormValidation($('#addSubjectForm'));
            });

            /* ── Percentage total ──────────────────────────────── */
            function updatePercentageTotal() {
                var ww = parseFloat($('#written_percentage').val())     || 0;
                var pt = parseFloat($('#performance_percentage').val()) || 0;
                var qa = parseFloat($('#quarterly_percentage').val())   || 0;
                var total = ww + pt + qa;

                var $el = $('#percentageTotal');
                if (Math.abs(total - 100) < 0.01) {
                    $el.removeClass('percentage-invalid').addClass('percentage-valid')
                       .html(total.toFixed(1) + '% <i class="bi bi-check-circle-fill"></i>');
                } else {
                    $el.removeClass('percentage-valid').addClass('percentage-invalid')
                       .html(total.toFixed(1) + '% <i class="bi bi-exclamation-circle-fill"></i>');
                }
            }

            /* ── Activity count total ──────────────────────────── */
            function updateActivityTotal() {
                var ww = parseInt($('#written_count').val())     || 0;
                var pt = parseInt($('#performance_count').val()) || 0;
                var qa = parseInt($('#quarterly_count').val())   || 0;
                $('#activityTotal').text(ww + pt + qa);
            }

            /* ── Generate activity perfect-score inputs ─────────── */
            function generateActivityInputs() {
                var writtenCount     = parseInt($('#written_count').val())     || 0;
                var performanceCount = parseInt($('#performance_count').val()) || 0;
                var quarterlyCount   = parseInt($('#quarterly_count').val())   || 0;

                updateActivityTotal();

                var $container = $('#activityScoresContainer');
                $container.empty();

                if (writtenCount <= 0 && performanceCount <= 0 && quarterlyCount <= 0) return;

                var maxCount = Math.max(writtenCount, performanceCount, quarterlyCount);

                var html = '<label class="form-label fw-semibold mt-2 mb-2">Perfect Scores per Activity</label>';
                html += '<div class="table-responsive"><table class="table table-bordered scores-table mb-0"><thead><tr>';
                html += '<th style="width:150px;">Component</th>';
                for (var i = 1; i <= maxCount; i++) {
                    html += '<th>Activity ' + i + '</th>';
                }
                html += '</tr></thead><tbody>';

                var components = [
                    { label: 'Written Work',     count: writtenCount,     name: 'written_activity_score' },
                    { label: 'Performance Task', count: performanceCount, name: 'performance_activity_score' },
                    { label: 'Quarterly Exam',   count: quarterlyCount,   name: 'quarterly_activity_score' }
                ];

                $.each(components, function (_, comp) {
                    if (comp.count <= 0) return;
                    html += '<tr><td class="row-label">' + comp.label + '</td>';
                    for (var i = 1; i <= maxCount; i++) {
                        if (i <= comp.count) {
                            html += '<td><input type="number" name="' + comp.name + '[]" placeholder="Score" min="1" step="0.01" required></td>';
                        } else {
                            html += '<td class="bg-light"></td>';
                        }
                    }
                    html += '</tr>';
                });

                html += '</tbody></table></div>';
                $container.html(html);
            }

            /* ── Wire up events ────────────────────────────────── */
            $(document).on('input', '.percentage-input', updatePercentageTotal);
            $(document).on('input change', '.activity-count', function () {
                updateActivityTotal();
                generateActivityInputs();
            });

            /* ── Add subject AJAX (validates % first) ───────────── */
            $('#addSubjectForm').on('submit', function (e) {
                e.preventDefault();
                if (!validateForm([
                    { el: $('#subject_name'), label: 'Subject Name', required: true, minLen: 2 }
                ])) return;
                var ww = parseFloat($('#written_percentage').val())     || 0;
                var pt = parseFloat($('#performance_percentage').val()) || 0;
                var qa = parseFloat($('#quarterly_percentage').val())   || 0;
                var total = ww + pt + qa;

                if (Math.abs(total - 100) >= 0.01) {
                    showToast('error', 'Percentages must total 100%. Current: ' + total.toFixed(1) + '%');
                    return;
                }

                var $btn = $(this).find('[type=submit]').prop('disabled', true);
                bootstrap.Modal.getInstance(document.getElementById('addSubjectModal'))?.hide();
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

            /* ── Init ──────────────────────────────────────────── */
            generateActivityInputs();
            updatePercentageTotal();
        });

    </script>
</body>
</html>
