<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Students to Class | EWGS</title>
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

        .enroll-card .card-header { background: #4b6b4b; color: #fff; font-weight: 600; }

        #btnEnroll {
            background-color: #4b6b4b;
            color: #fff;
            min-width: 160px;
        }
        #btnEnroll:hover:not(:disabled) { background-color: #3a5a3a; }
        #btnEnroll:disabled { opacity: 0.6; cursor: not-allowed; }

        .sel-badge {
            background: rgba(255,255,255,0.25);
            border-radius: 20px;
            padding: 1px 9px;
            font-size: 12px;
            margin-left: 6px;
        }

        #studentsTable th:first-child,
        #studentsTable td:first-child { width: 48px; text-align: center; vertical-align: middle; }

        /* Larger checkboxes */
        #studentsTable input[type="checkbox"] {
            width: 18px; height: 18px; cursor: pointer; accent-color: #4b6b4b;
        }
        /* Row click highlight */
        #studentsTable tbody tr { cursor: pointer; }
        #studentsTable tbody tr:hover { background-color: rgba(75,107,75,0.07) !important; }
        #studentsTable tbody tr.row-selected { background-color: rgba(75,107,75,0.13) !important; }
    </style>
</head>
<body>
<?php require_once 'views/templates/admin/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h4 class="mb-0"><i class="bi bi-person-plus me-2"></i>Link Students to Class</h4>
        <a href="/ewgs/admin/assign/student/enrolled" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-person-lines-fill me-1"></i>View Linked Students
        </a>
    </div>

    <!-- Enroll Controls -->
    <div class="card shadow-sm mb-4 enroll-card">
        <div class="card-header">
            <i class="bi bi-link-45deg me-2"></i>Link Selection
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Select Class</label>
                    <select id="selClass" class="form-select">
                        <option value="">— Choose a class —</option>
                        <?php foreach ($classes as $c): ?>
                            <option value="<?= $c['class_id'] ?>">
                                <?= htmlspecialchars($c['class_name']) ?> — Grade <?= htmlspecialchars($c['grade_level']) ?> (<?= htmlspecialchars($c['school_year']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-auto">
                    <button id="btnEnroll" class="btn" disabled>
                        <i class="bi bi-person-check me-1"></i>Link Selected
                        <span class="sel-badge" id="selCount">0</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card shadow-sm enroll-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-people me-2"></i>All Students</span>
            <small class="fw-normal opacity-75">Check students, select a class, then click Link Selected.</small>
        </div>
        <div class="card-body">
            <table id="studentsTable" class="table table-hover w-100">
                <thead class="table-dark">
                    <tr>
                        <th><input type="checkbox" id="checkAll" title="Select / deselect all on this page"></th>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>LRN</th>
                        <th>Gender</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script src="/ewgs/public/js/bootstrap.bundle.js"></script>
<script src="/ewgs/public/js/tom-select.complete.min.js"></script>
<script>
$(function () {
    var selectedIds = new Set();
    var currentClassId = null;

    /* ── Tom Select ─────────────────────────────────── */
    var tsClass = new TomSelect('#selClass', { allowEmptyOption: true });
    tsClass.on('change', function (val) {
        currentClassId = val ? parseInt(val) : null;
        selectedIds.clear();
        table.ajax.reload(null, false);
        refreshEnrollBtn();
    });

    /* ── Students DataTable ──────────────────────────── */
    var table = $('#studentsTable').DataTable({
        ajax: {
            url: '/ewgs/admin/assign/student/available',
            type: 'GET',
            data: function () { return { class_id: currentClassId || 0 }; },
            dataSrc: 'data'
        },
        columns: [
            {
                data: null, orderable: false, searchable: false,
                render: function (d) {
                    var chk = selectedIds.has(String(d.student_id)) ? ' checked' : '';
                    return '<input type="checkbox" class="row-check"' + chk + ' value="' + d.student_id + '">';
                }
            },
            { data: 'count' },
            { data: 'student_name' },
            { data: 'lrn' },
            { data: 'gender' }
        ],
        pageLength: 25,
        language: { emptyTable: 'No students available.' },
        drawCallback: function () {
            // Restore row-selected class after page change
            $('#studentsTable tbody input.row-check').each(function () {
                $(this).closest('tr').toggleClass('row-selected', selectedIds.has(this.value));
            });
            updateUI();
        }
    });

    /* ── Checkbox logic ─────────────────────────────── */
    function updateUI() {
        $('#selCount').text(selectedIds.size);
        refreshEnrollBtn();
        syncCheckAll();
    }

    function refreshEnrollBtn() {
        $('#btnEnroll').prop('disabled', selectedIds.size === 0 || !currentClassId);
    }

    function syncCheckAll() {
        var $all     = $('#studentsTable tbody input.row-check');
        var $checked = $all.filter(':checked');
        $('#checkAll')
            .prop('indeterminate', $checked.length > 0 && $checked.length < $all.length)
            .prop('checked',       $all.length > 0 && $checked.length === $all.length);
    }

    // Clicking anywhere on the row toggles the checkbox
    $(document).on('click', '#studentsTable tbody tr', function (e) {
        // If they clicked the checkbox itself, let its native change fire
        if ($(e.target).is('input[type="checkbox"]')) return;
        var $chk = $(this).find('input.row-check');
        if (!$chk.length) return;
        $chk.prop('checked', !$chk.prop('checked')).trigger('change');
    });

    $(document).on('change', '#studentsTable tbody input.row-check', function () {
        if (this.checked) selectedIds.add(this.value);
        else              selectedIds.delete(this.value);
        $(this).closest('tr').toggleClass('row-selected', this.checked);
        updateUI();
    });

    $('#checkAll').on('change', function () {
        var on = this.checked;
        $('#studentsTable tbody input.row-check').each(function () {
            this.checked = on;
            if (on) selectedIds.add(this.value);
            else    selectedIds.delete(this.value);
        });
        updateUI();
    });

    /* ── Enroll ─────────────────────────────────────── */
    $('#btnEnroll').on('click', function () {
        if (!selectedIds.size || !currentClassId) return;
        var $btn = $(this).prop('disabled', true);

        $.post('/ewgs/admin/assign/student/link',
            { student_ids: Array.from(selectedIds), class_id: currentClassId },
            function (res) {
                if (res.success) {
                    var msg = res.enrolled + ' student(s) linked to class.';
                    if (res.skipped > 0) msg += ' ' + res.skipped + ' already linked (skipped).';
                    showToast('success', msg);
                    selectedIds.clear();
                    table.ajax.reload(null, false);
                } else {
                    showToast('error', res.message || 'Failed to enroll. Please try again.');
                    $btn.prop('disabled', false);
                }
            }
        ).fail(function () {
            showToast('error', 'An error occurred. Please try again.');
            $btn.prop('disabled', false);
        });
    });
});
</script>
</body>
</html>
