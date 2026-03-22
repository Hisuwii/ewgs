<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Grades | EWGS</title>
    <?php require_once 'views/templates/user/header.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        .btn-manage {
            background-color: #4b6b4b;
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .btn-manage:hover {
            background-color: #3a5a3a;
            color: white;
        }
        .badge-complete {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        .badge-incomplete {
            background-color: #ffc107;
            color: #000;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        .badge-none {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        .btn-export {
            background-color: #1b5e20;
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: 5px;
            font-weight: 500;
            font-size: 13px;
            transition: all 0.2s ease;
        }
        .btn-export:hover { background-color: #145214; color: white; }
        .btn-export:disabled { background-color: #aaa; cursor: not-allowed; }
    </style>
</head>
<body>
    <?php require_once 'views/templates/user/sidebar.php'; ?>

    <div class="main-content">
        <!-- Header -->
        <div class="page-header">
            <h4>Manage Grades</h4>
        </div>

        <div class="container-fluid px-4">
            <!-- Table with horizontal scroll on small screens -->
            <div class="table-responsive">
                <table id="manageGradeTable" class="table table-bordered table-striped table-hover text-center">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Section Name</th>
                            <th scope="col">Grade Level</th>
                            <th scope="col">School Year</th>
                            <th scope="col">Students</th>
                            <th scope="col">Grade Status</th>
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
                                <td class="grade-status"><span class="badge-none">No Grades Yet</span></td>
                                <td>
                                    <a href="<?= BASE ?>/user/grade/manage/<?= $c['class_id'] ?>" class="btn btn-sm btn-manage me-1">
                                        <i class="bi bi-pencil-square"></i> Manage
                                    </a>
                                    <button class="btn-export btn-export-pdf"
                                            data-class-id="<?= (int) $c['class_id'] ?>"
                                            data-class-name="<?= htmlspecialchars($c['class_name']) ?>"
                                            data-grade-level="<?= htmlspecialchars($c['grade_level']) ?>"
                                            data-school-year="<?= htmlspecialchars($c['school_year']) ?>">
                                        <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Export Grades Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header" style="background:#2e4e2e;color:#fff;">
                    <h5 class="modal-title" id="exportModalLabel"><i class="bi bi-file-earmark-pdf me-2"></i>Export Grades PDF</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3" style="font-size:13px;">
                        Only quarters where <strong>all students</strong> have been fully graded are available for export.
                    </p>
                    <label class="form-label fw-semibold">Select Quarter</label>
                    <select id="exportQuarterSelect" class="form-select"></select>
                    <div id="exportModalError" class="text-danger mt-2" style="font-size:13px;display:none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="btnGeneratePDF" class="btn btn-sm" style="background:#2e4e2e;color:#fff;">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Generate PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= BASE ?>/public/js/bootstrap.bundle.js"></script>
    <?php require_once 'views/templates/user/datatable.php'; ?>
    <script>
        $(document).ready(function() {
            $('#manageGradeTable').DataTable({
                language: { emptyTable: 'No classes are assigned to you yet.' }
            });

            function refreshManageGradeStats() {
                $.getJSON('<?= BASE ?>/user/manage-grades/stats', function (data) {
                    data.forEach(function (item) {
                        var $row = $('tr[data-class-id="' + item.class_id + '"]');
                        if (!$row.length) return;

                        $row.find('.student-count').text(item.student_count);

                        var gradeCount  = parseInt(item.grade_count, 10);
                        var isComplete  = parseInt(item.is_complete, 10);

                        var statusHtml;
                        if (gradeCount === 0) {
                            statusHtml = '<span class="badge-none">No Grades Yet</span>';
                        } else if (isComplete) {
                            statusHtml = '<span class="badge-complete">Complete</span>';
                        } else {
                            statusHtml = '<span class="badge-incomplete">Incomplete</span>';
                        }
                        $row.find('.grade-status').html(statusHtml);
                    });
                });
            }

            // Poll every 15s, pauses when tab is hidden
            smartPoll(refreshManageGradeStats, 15000);

            // ── Export PDF ─────────────────────────────────────────
            var exportClassId   = null;
            var exportClassName = null;
            var exportGradeLevel = null;
            var exportSchoolYear = null;

            $(document).on('click', '.btn-export-pdf', function () {
                exportClassId    = $(this).data('class-id');
                exportClassName  = $(this).data('class-name');
                exportGradeLevel = $(this).data('grade-level');
                exportSchoolYear = $(this).data('school-year');

                var $btn = $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
                $('#exportQuarterSelect').html('<option>Checking quarters…</option>');
                $('#exportModalError').hide();
                $('#btnGeneratePDF').prop('disabled', false);

                $.getJSON('<?= BASE ?>/user/grade/export-check/' + exportClassId)
                    .done(function (quarters) {
                        if (!quarters.length) {
                            $('#exportModalError').text('No quarter has all students fully graded yet. Please complete grading before exporting.').show();
                            $('#exportQuarterSelect').html('');
                            $('#btnGeneratePDF').prop('disabled', true);
                        } else {
                            var opts = '<option value="">— Select Quarter —</option>';
                            quarters.forEach(function (q) {
                                opts += '<option value="' + q + '">' + q + ' Quarter</option>';
                            });
                            $('#exportQuarterSelect').html(opts);
                        }
                        $('#exportModal').modal('show');
                    })
                    .fail(function () {
                        alert('Failed to check grade status. Please try again.');
                    })
                    .always(function () {
                        $btn.prop('disabled', false).html('<i class="bi bi-file-earmark-pdf me-1"></i>Export PDF');
                    });
            });

            $('#btnGeneratePDF').on('click', function () {
                var quarter = $('#exportQuarterSelect').val();
                if (!quarter) {
                    $('#exportModalError').text('Please select a quarter.').show();
                    return;
                }
                $('#exportModalError').hide();
                var $btn = $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Generating…');

                $.getJSON('<?= BASE ?>/user/grade/export-data/' + exportClassId, { quarter: quarter })
                    .done(function (res) {
                        $('#exportModal').modal('hide');
                        buildGradesPDF(res.class, res.rows, quarter);
                    })
                    .fail(function () {
                        $('#exportModalError').text('Failed to load grade data.').show();
                    })
                    .always(function () {
                        $btn.prop('disabled', false).html('<i class="bi bi-file-earmark-pdf me-1"></i>Generate PDF');
                    });
            });

            function buildGradesPDF(classInfo, rows, quarter) {
                var { jsPDF } = window.jspdf;
                var doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
                var pageW = 210, pageH = 297, margin = 12, usableW = pageW - margin * 2;

                // Group rows by student
                var studentOrder = [];
                var students = {};
                rows.forEach(function (r) {
                    if (!students[r.student_id]) {
                        studentOrder.push(r.student_id);
                        students[r.student_id] = { name: r.student_name, lrn: r.lrn || 'N/A', subjects: [] };
                    }
                    students[r.student_id].subjects.push(r);
                });

                var now = new Date().toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' });

                function drawPageHeader() {
                    doc.setFillColor(46, 78, 46);
                    doc.rect(0, 0, pageW, 20, 'F');
                    doc.setTextColor(255, 255, 255);
                    doc.setFontSize(12);
                    doc.setFont('helvetica', 'bold');
                    doc.text('Elementary Web Grading System', margin, 9);
                    doc.setFontSize(8);
                    doc.setFont('helvetica', 'normal');
                    doc.setTextColor(180, 210, 180);
                    doc.text('Grade Report  ·  ' + classInfo.class_name + ' (Grade ' + classInfo.grade_level + ')  ·  ' + quarter + ' Quarter  ·  S.Y. ' + classInfo.school_year, margin, 16);
                }

                drawPageHeader();
                var y = 26;
                var rowH = 6, colHeaderH = 6, cardHeaderH = 9, cardPadV = 2, cardGap = 4;

                // Column x-positions (right-aligned for numbers)
                var cSubject = margin + 3;
                var cWW      = margin + usableW * 0.56;
                var cPT      = margin + usableW * 0.68;
                var cQE      = margin + usableW * 0.80;
                var cFinal   = margin + usableW * 0.90;
                var cStatus  = margin + usableW;

                studentOrder.forEach(function (sid, idx) {
                    var stu = students[sid];
                    var numSubjects = stu.subjects.length;
                    var cardH = cardHeaderH + colHeaderH + numSubjects * rowH + cardPadV * 2;

                    if (idx > 0 && y + cardH > pageH - 14) {
                        doc.addPage();
                        drawPageHeader();
                        y = 26;
                    }

                    // Card outer border
                    doc.setDrawColor(180, 180, 180);
                    doc.setFillColor(255, 255, 255);
                    doc.rect(margin, y, usableW, cardH, 'FD');

                    // Card header bg
                    doc.setFillColor(75, 107, 75);
                    doc.rect(margin, y, usableW, cardHeaderH, 'F');

                    // Student name
                    doc.setTextColor(255, 255, 255);
                    doc.setFontSize(9);
                    doc.setFont('helvetica', 'bold');
                    doc.text(stu.name, margin + 3, y + 6);

                    // LRN on right
                    doc.setFontSize(7.5);
                    doc.setFont('helvetica', 'normal');
                    doc.setTextColor(200, 230, 200);
                    doc.text('LRN: ' + stu.lrn, margin + usableW - 3, y + 6, { align: 'right' });

                    // Column headers row
                    var hY = y + cardHeaderH + cardPadV;
                    doc.setDrawColor(210, 210, 210);
                    doc.line(margin, hY + colHeaderH, margin + usableW, hY + colHeaderH);
                    doc.setFontSize(7);
                    doc.setFont('helvetica', 'bold');
                    doc.setTextColor(80, 80, 80);
                    doc.text('SUBJECT',     cSubject, hY + 4.5);
                    doc.text('WW %',  cWW,    hY + 4.5, { align: 'right' });
                    doc.text('PT %',  cPT,    hY + 4.5, { align: 'right' });
                    doc.text('QE %',  cQE,    hY + 4.5, { align: 'right' });
                    doc.text('FINAL', cFinal, hY + 4.5, { align: 'right' });
                    doc.text('STATUS',cStatus, hY + 4.5, { align: 'right' });

                    // Subject rows
                    stu.subjects.forEach(function (sub, i) {
                        var rY = hY + colHeaderH + i * rowH;
                        // Alternate row shading
                        if (i % 2 === 1) {
                            doc.setFillColor(248, 250, 248);
                            doc.rect(margin + 0.5, rY, usableW - 1, rowH, 'F');
                        }
                        var fg = parseInt(sub.final_grade);
                        doc.setFontSize(8);
                        doc.setFont('helvetica', 'normal');
                        doc.setTextColor(50, 50, 50);
                        doc.text(sub.subject_name, cSubject, rY + 4.2);
                        doc.text(parseFloat(sub.written_work).toFixed(1),    cWW,    rY + 4.2, { align: 'right' });
                        doc.text(parseFloat(sub.performance_task).toFixed(1), cPT,   rY + 4.2, { align: 'right' });
                        doc.text(parseFloat(sub.quarterly_exam).toFixed(1),  cQE,    rY + 4.2, { align: 'right' });
                        doc.setFont('helvetica', 'bold');
                        doc.setTextColor(fg >= 75 ? 22 : 153, fg >= 75 ? 101 : 27, fg >= 75 ? 52 : 27);
                        doc.text(String(fg), cFinal, rY + 4.2, { align: 'right' });
                        doc.setFontSize(7);
                        doc.text(fg >= 75 ? 'PASSED' : 'FAILED', cStatus, rY + 4.2, { align: 'right' });
                    });

                    y += cardH + cardGap;
                });

                // Footer on every page
                var totalPages = doc.getNumberOfPages();
                for (var p = 1; p <= totalPages; p++) {
                    doc.setPage(p);
                    doc.setFillColor(240, 245, 240);
                    doc.rect(0, pageH - 9, pageW, 9, 'F');
                    doc.setFontSize(7);
                    doc.setFont('helvetica', 'normal');
                    doc.setTextColor(120, 120, 120);
                    doc.text('EWGS — Confidential Grade Report', margin, pageH - 3.5);
                    doc.text('Generated: ' + now, pageW / 2, pageH - 3.5, { align: 'center' });
                    doc.text('Page ' + p + ' of ' + totalPages, pageW - margin, pageH - 3.5, { align: 'right' });
                }

                doc.save('grades-' + classInfo.class_name.replace(/[^a-z0-9]/gi, '_') + '-' + quarter + 'Q.pdf');
            }
        });
    </script>
</body>
</html>
