<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Grades | EWGS</title>
    <?php require_once 'views/templates/user/header.php'; ?>
    <style>
        .btn-add-grade {
            background-color: #4b6b4b;
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .btn-add-grade:hover {
            background-color: #3a5a3a;
            color: white;
        }
    </style>
</head>
<body>
    <?php require_once 'views/templates/user/sidebar.php'; ?>

    <div class="main-content">
        <!-- Header -->
        <div class="page-header">
            <h4>Add Grades</h4>
        </div>

        <div class="container-fluid px-4">
            <!-- Table with horizontal scroll on small screens -->
            <div class="table-responsive">
                <table id="gradeTable" class="table table-bordered table-striped table-hover text-center">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Section Name</th>
                            <th scope="col">Grade Level</th>
                            <th scope="col">School Year</th>
                            <th scope="col">Students</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($classes as $i => $c): ?>
                            <tr data-class-id="<?= (int) $c['class_id'] ?>">
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($c['class_name']) ?></td>
                                <td>Grade <?= htmlspecialchars($c['grade_level']) ?></td>
                                <td><?= htmlspecialchars($c['school_year']) ?></td>
                                <td class="student-count"><?= (int) $c['student_count'] ?></td>
                                <td>
                                    <a href="<?= BASE ?>/user/grade/add/<?= $c['class_id'] ?>" class="btn btn-sm btn-add-grade">
                                        <i class="bi bi-plus-circle"></i> Add Grade
                                    </a>
                                </td>
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
        $(document).ready(function() {
            $('#gradeTable').DataTable({
                language: { emptyTable: 'No classes are assigned to you yet.' }
            });

            function refreshStats() {
                $.getJSON('<?= BASE ?>/user/my-classes/stats', function (data) {
                    $.each(data, function (_, item) {
                        var $cell = $('#gradeTable tbody tr[data-class-id="' + item.class_id + '"] td.student-count');
                        if ($cell.length) $cell.text(item.student_count);
                    });
                });
            }
            smartPoll(refreshStats, 15000);
        });
    </script>
</body>
</html>
