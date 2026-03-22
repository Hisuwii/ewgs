<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Logs | EWGS</title>
    <?php require_once 'views/templates/admin/header.php'; ?>
    <?php require_once 'views/templates/admin/modal.php'; ?>
    <style>
        .badge-active   { background-color: #198754; }
        .badge-inactive { background-color: #6c757d; }
        .badge-yes      { background-color: #198754; }
        .badge-no       { background-color: #dc3545; }
        .stat-badge {
            display: inline-block;
            min-width: 36px;
            text-align: center;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            background-color: #e9ecef;
            color: #333;
        }
        .stat-badge.has-data { background-color: #d1e7dd; color: #0f5132; }
    </style>
</head>
<body>
    <?php require_once 'views/templates/admin/sidebar.php'; ?>

    <?= displayFlash() ?>

    <div class="main-content">
        <div class="page-header">
            <h4>Teacher Logs</h4>
        </div>

        <div class="container-fluid px-4">
            <div class="table-responsive">
                <table id="logTable" class="table table-bordered table-striped table-hover text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Teacher Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Password Changed</th>
                            <th>Account Created</th>
                            <th>Classes Assigned</th>
                            <th>Grades Saved</th>
                            <th>Scores Entered</th>
                            <th>Last Grade Activity</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="<?= BASE ?>/public/js/bootstrap.bundle.js"></script>
    <?php require_once 'views/templates/admin/datatable.php'; ?>
    <script>
        $(document).ready(function () {
            $('#logTable').DataTable({
                serverSide: true,
                processing: true,
                searchDelay: 500,
                order: [],
                ajax: {
                    url: '<?= BASE ?>/admin/teacher/logs/data',
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [
                    { data: null, orderable: false, searchable: false, width: '4%',
                      render: function(data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
                    { data: 'teacher_name' },
                    { data: 'teacher_email' },
                    {
                        data: 'status',
                        render: function (data) {
                            var cls = data === 'Active' ? 'badge-active' : 'badge-inactive';
                            return '<span class="badge ' + cls + '">' + data + '</span>';
                        }
                    },
                    {
                        data: 'password_changed',
                        render: function (data) {
                            var cls = data === 'Yes' ? 'badge-yes' : 'badge-no';
                            return '<span class="badge ' + cls + '">' + data + '</span>';
                        }
                    },
                    {
                        data: 'account_created',
                        render: function (data) {
                            if (!data) return '<span class="text-muted">—</span>';
                            var d = new Date(data);
                            return d.toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });
                        }
                    },
                    {
                        data: 'classes_assigned',
                        render: function (data) {
                            var cls = data > 0 ? 'has-data' : '';
                            return '<span class="stat-badge ' + cls + '">' + data + '</span>';
                        }
                    },
                    {
                        data: 'grades_saved',
                        render: function (data) {
                            var cls = data > 0 ? 'has-data' : '';
                            return '<span class="stat-badge ' + cls + '">' + data + '</span>';
                        }
                    },
                    {
                        data: 'scores_entered',
                        render: function (data) {
                            var cls = data > 0 ? 'has-data' : '';
                            return '<span class="stat-badge ' + cls + '">' + data + '</span>';
                        }
                    },
                    {
                        data: 'last_grade_activity',
                        render: function (data) {
                            if (!data) return '<span class="text-muted">No activity yet</span>';
                            var d = new Date(data);
                            return d.toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' })
                                 + ' ' + d.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit' });
                        }
                    }
                ],
                language: {
                    emptyTable: 'No teachers found.'
                }
            });
        });
    </script>
</body>
</html>
