<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Classes | EWGS</title>
    <?php require_once 'views/templates/user/header.php'; ?>
    <style>
        .sy-badge {
            background: #e8f5e9;
            color: #2e4e2e;
            border: 1px solid #c8d8c8;
            border-radius: 6px;
            padding: 2px 10px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        @keyframes stat-flash { 0%,100% { opacity:1; } 50% { opacity:0.4; } }
        .stat-updated { animation: stat-flash 0.6s ease; }
        body.dark-mode .sy-badge { background: #1a2e1a; color: #a5d6a7; border-color: #3a5a3a; }
    </style>
</head>
<body>
    <?php require_once 'views/templates/user/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <h4>My Classes</h4>
        </div>

        <div class="container-fluid px-4">
            <div class="table-responsive">
                <table id="classTable" class="table table-bordered table-striped table-hover text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Section Name</th>
                            <th>Grade Level</th>
                            <th>School Year</th>
                            <th>Students</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($classes as $i => $c): ?>
                            <tr data-class-id="<?= (int) $c['class_id'] ?>">
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($c['class_name']) ?></td>
                                <td>Grade <?= htmlspecialchars($c['grade_level']) ?></td>
                                <td>
                                    <span class="sy-badge">
                                        <i class="bi bi-calendar3"></i>
                                        <?= htmlspecialchars($c['school_year']) ?>
                                    </span>
                                </td>
                                <td class="student-count"><?= (int) $c['student_count'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="<?= BASE ?>/public/js/bootstrap.bundle.js"></script>
    <?php require_once 'views/templates/user/datatable.php'; ?>
    <script>
        $(function () {
            $('#classTable').DataTable({
                language: { emptyTable: 'No classes are assigned to you yet.' }
            });

            function refreshClassStats() {
                $.getJSON('<?= BASE ?>/user/my-classes/stats', function (data) {
                    $.each(data, function (_, item) {
                        var $cell   = $('#classTable tbody tr[data-class-id="' + item.class_id + '"] td.student-count');
                        var current = parseInt($cell.text(), 10);
                        if (!isNaN(current) && current !== item.student_count) {
                            $cell.text(item.student_count).addClass('stat-updated');
                            setTimeout(function () { $cell.removeClass('stat-updated'); }, 600);
                        }
                    });
                });
            }
            smartPoll(refreshClassStats, 15000);
        });
    </script>
</body>
</html>
