<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | EWGS</title>
    <?php require_once 'views/templates/user/header.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        .filter-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            margin-bottom: 1.5rem;
        }
        .chart-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            margin-bottom: 1.5rem;
        }
        .chart-card .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: #2e4e2e;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .chart-wrapper {
            position: relative;
            height: 320px;
        }
        .stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        .pill-pass  { background: #dcfce7; color: #166534; }
        .pill-fail  { background: #fee2e2; color: #991b1b; }
        .pill-avg   { background: #dbeafe; color: #1e40af; }
        .summary-pills { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 1rem; }
        .section-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 8px;
        }
        #emptyState {
            text-align: center;
            padding: 50px 20px;
        }
        #emptyState .empty-icon { font-size: 3rem; color: #9ca3af; display: block; margin-bottom: 16px; }
        #emptyState .empty-box {
            display: inline-block;
            background: #f0fdf4;
            border: 1.5px dashed #4b6b4b;
            border-radius: 14px;
            padding: 28px 36px;
            max-width: 420px;
        }
        #emptyState .empty-box h6 { color: #2e4e2e; font-weight: 700; margin-bottom: 10px; font-size: 1rem; }
        #emptyState .empty-box p  { color: #6b7280; margin-bottom: 14px; font-size: 14px; line-height: 1.6; }
        #emptyState .step {
            display: flex; align-items: center; gap: 10px;
            background: #fff; border-radius: 8px; padding: 8px 14px;
            margin-bottom: 8px; border: 1px solid #e5e7eb;
            font-size: 13px; color: #374151; text-align: left;
        }
        #emptyState .step .step-num {
            background: #4b6b4b; color: #fff;
            border-radius: 50%; width: 22px; height: 22px;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; flex-shrink: 0;
        }
        body.dark-mode #emptyState .empty-box { background: #1a2e1a; border-color: #3a5a3a; }
        body.dark-mode #emptyState .empty-box h6 { color: #a5d6a7; }
        body.dark-mode #emptyState .empty-box p  { color: #9ca3af; }
        body.dark-mode #emptyState .step { background: #1e1e1e; border-color: #333; color: #e0e0e0; }

        /* Grade table */
        .grade-table th { font-size: 12px; text-transform: uppercase; letter-spacing: .5px; }
        .badge-pass { background: #dcfce7; color: #166534; font-weight: 600; border-radius: 6px; padding: 2px 8px; }
        .badge-fail { background: #fee2e2; color: #991b1b; font-weight: 600; border-radius: 6px; padding: 2px 8px; }

        /* Dark mode */
        body.dark-mode .filter-card,
        body.dark-mode .chart-card { background: #1e1e1e; color: #e0e0e0; }
        body.dark-mode .chart-card .card-title { color: #a5d6a7; }
        body.dark-mode .form-select,
        body.dark-mode .form-control { background: #2d2d2d; color: #e0e0e0; border-color: #444; }
        body.dark-mode .grade-table th,
        body.dark-mode .grade-table td { border-color: #444; color: #e0e0e0; }
        body.dark-mode .section-label { color: #9ca3af; }
        body.dark-mode #sectionBreakdownText,
        body.dark-mode #studentBreakdownText { color: #aaa; }
        #sectionBreakdownText div,
        #studentBreakdownText div { margin-bottom: 3px; }
        /* Select2 styling */
        .select2-container--default .select2-selection--single {
            height: 38px; border-radius: 6px; border: 1px solid #ced4da;
            display: flex; align-items: center; padding: 0 8px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px; padding-left: 4px; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
        .select2-container--default .select2-results__option--highlighted { background-color: #2e4e2e !important; }
        .select2-dropdown { border-color: #ced4da; border-radius: 6px; }
        /* Grade calculation table */
        .calc-table { font-size: 12px; width: 100%; border-collapse: collapse; margin-top: 8px; }
        .calc-table th { background: #f0fdf4; color: #2e4e2e; font-weight: 600; padding: 5px 8px; text-align: left; border-bottom: 1.5px solid #d1fae5; }
        .calc-table td { padding: 4px 8px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
        .calc-table tr.total-row td { font-weight: 700; background: #f0fdf4; border-top: 1.5px solid #d1fae5; }
        .calc-table tr.final-row td { font-weight: 700; background: #dcfce7; color: #166534; }
        .calc-table tr.final-fail td { font-weight: 700; background: #fee2e2; color: #991b1b; }
        .subject-breakdown-block { border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 12px; margin-bottom: 10px; }
        .subject-breakdown-title { font-weight: 700; font-size: 13px; color: #2e4e2e; margin-bottom: 6px; }
        body.dark-mode .calc-table th { background: #1a2e1a; color: #a5d6a7; border-color: #3a5a3a; }
        body.dark-mode .calc-table td { border-color: #333; color: #e0e0e0; }
        body.dark-mode .calc-table tr.total-row td { background: #1a2e1a; }
        body.dark-mode .calc-table tr.final-row td { background: #14532d; color: #86efac; }
        body.dark-mode .calc-table tr.final-fail td { background: #450a0a; color: #fca5a5; }
        body.dark-mode .subject-breakdown-block { border-color: #333; }
        body.dark-mode .subject-breakdown-title { color: #a5d6a7; }
        body.dark-mode .select2-container--default .select2-selection--single { background: #2d2d2d; border-color: #444; color: #e0e0e0; }
        body.dark-mode .select2-container--default .select2-selection--single .select2-selection__rendered { color: #e0e0e0; }
        body.dark-mode .select2-dropdown { background: #2d2d2d; border-color: #444; }
        body.dark-mode .select2-container--default .select2-results__option { color: #e0e0e0; }
        body.dark-mode .select2-container--default .select2-search--dropdown .select2-search__field { background: #1e1e1e; color: #e0e0e0; border-color: #444; }
    </style>
</head>
<body>
    <?php require_once 'views/templates/user/sidebar.php'; ?>

    <?= displayFlash() ?>

    <div class="main-content">
        <div class="page-header">
            <h4><i class="bi bi-bar-chart-line me-2"></i>Reports</h4>
        </div>

        <div class="container-fluid px-4">

            <!-- Filters -->
            <div class="filter-card">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Class</label>
                        <select id="filterClass" class="form-select">
                            <option value="">— Select Class —</option>
                            <?php foreach ($classes as $c): ?>
                                <option value="<?= $c['class_id'] ?>">
                                    <?= htmlspecialchars($c['class_name']) ?> (<?= htmlspecialchars($c['grade_level']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Quarter</label>
                        <select id="filterQuarter" class="form-select">
                            <option value="">— Select Quarter —</option>
                            <option value="1st">1st Quarter</option>
                            <option value="2nd">2nd Quarter</option>
                            <option value="3rd">3rd Quarter</option>
                            <option value="4th">4th Quarter</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button id="btnLoad" class="btn btn-success w-100">
                            <i class="bi bi-search me-1"></i> Load
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div id="emptyState">
                <i class="bi bi-bar-chart-line empty-icon"></i>
                <div class="empty-box">
                    <h6><i class="bi bi-info-circle me-1"></i> No Report Loaded Yet</h6>
                    <p>To view performance reports, follow these steps:</p>
                    <div class="step"><span class="step-num">1</span> Choose a <strong>Class</strong> from the dropdown above.</div>
                    <div class="step"><span class="step-num">2</span> Choose a <strong>Quarter</strong> (1st – 4th).</div>
                    <div class="step"><span class="step-num">3</span> Click the <strong>Load</strong> button to generate the report.</div>
                </div>
            </div>

            <!-- Report content (hidden until data loads) -->
            <div id="reportContent" style="display:none;">

                <!-- Section Performance -->
                <div class="chart-card" id="sectionCard">
                    <div class="card-title">
                        <i class="bi bi-people-fill"></i> Section Performance
                        <small class="text-muted fw-normal ms-2" id="sectionTitle"></small>
                        <button id="btnExportSection" class="btn btn-sm ms-auto" style="background:#2e4e2e;color:#fff;font-size:12px;padding:4px 12px;">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
                        </button>
                    </div>
                    <div id="sectionSummary" class="summary-pills"></div>
                    <div class="chart-wrapper">
                        <canvas id="sectionChart"></canvas>
                    </div>
                    <!-- Data breakdown -->
                    <div style="margin-top:1.2rem;">
                        <div class="section-label"><i class="bi bi-info-circle me-1"></i>Chart Breakdown</div>
                        <p id="sectionBreakdownText" class="mb-0" style="font-size:13px;color:#555;line-height:1.7;"></p>
                    </div>
                </div>

                <!-- Subject Grade Table -->
                <div class="chart-card">
                    <div class="card-title"><i class="bi bi-table"></i> Subject Summary</div>
                    <div class="table-responsive">
                        <table class="table grade-table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Subject</th>
                                    <th class="text-center">Avg Grade</th>
                                    <th class="text-center">Passed</th>
                                    <th class="text-center">Failed</th>
                                    <th class="text-center">Not Yet Graded</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody id="subjectTableBody"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Student Performance -->
                <div class="chart-card" id="studentCard">
                    <div class="card-title">
                        <i class="bi bi-person-lines-fill"></i> Student Performance
                        <button id="btnExportStudent" class="btn btn-sm ms-auto" style="background:#2e4e2e;color:#fff;font-size:12px;padding:4px 12px;display:none;">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
                        </button>
                    </div>
                    <div class="row g-3 align-items-end mb-3">
                        <div class="col-md-5">
                            <label class="form-label section-label">Select Student</label>
                            <select id="filterStudent" class="form-select"></select>
                        </div>
                    </div>
                    <div id="studentEmpty" class="text-muted" style="padding:20px 0;">
                        Select a student to view their grade breakdown.
                    </div>
                    <div id="studentChartArea" style="display:none;">
                        <div id="studentSummary" class="summary-pills mb-3"></div>
                        <div class="chart-wrapper">
                            <canvas id="studentChart"></canvas>
                        </div>
                        <!-- Data breakdown -->
                        <div style="margin-top:1.2rem;">
                            <div class="section-label"><i class="bi bi-info-circle me-1"></i>Chart Breakdown</div>
                            <p id="studentBreakdownText" class="mb-0" style="font-size:13px;color:#555;line-height:1.7;"></p>
                        </div>
                    </div>
                </div>

            </div><!-- /reportContent -->
        </div>
    </div>

    <script src="/ewgs/public/js/bootstrap.bundle.js"></script>
    <script>
    $(function () {

        var sectionChartInstance = null;
        var studentChartInstance = null;
        var currentClassId = null;
        var currentQuarter = null;

        // ── Chart.js dark mode defaults ────────────────────────
        function isDark() { return $('body').hasClass('dark-mode'); }
        function chartDefaults() {
            var color = isDark() ? '#e0e0e0' : '#374151';
            Chart.defaults.color = color;
            Chart.defaults.borderColor = isDark() ? '#444' : '#e5e7eb';
        }

        // ── Transmutation (mirrors PHP) ────────────────────────
        function transmute(ps) {
            if (ps >= 100) return 100;
            if (ps >= 60)  return Math.floor((ps - 60) / 1.6) + 75;
            return Math.floor(ps / 4) + 60;
        }

        // ── Load report ───────────────────────────────────────
        $('#btnLoad').on('click', function () {
            currentClassId = $('#filterClass').val();
            currentQuarter = $('#filterQuarter').val();
            if (!currentClassId || !currentQuarter) {
                showToast('warning', 'Please select a class and quarter.');
                return;
            }
            var $btn = $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Loading…');
            // Load main report first; stats call is secondary and must not block
            $.getJSON('/ewgs/user/reports/section-data', { class_id: currentClassId, quarter: currentQuarter })
                .done(function (res) {
                    $('#emptyState').hide();
                    $('#reportContent').show();
                    // Render chart immediately so it's never blank
                    renderSection(res.report, null);
                    populateStudentPicker(res.students);
                    // Enhance with enrolled count from stats (updates not-yet-graded pill + table)
                    $.getJSON('/ewgs/user/manage-grades/stats')
                        .done(function (statsList) {
                            var classStat = null;
                            if (Array.isArray(statsList)) {
                                statsList.forEach(function (s) {
                                    if (String(s.class_id) === String(currentClassId)) classStat = s;
                                });
                            }
                            if (classStat) {
                                renderSection(res.report, parseInt(classStat.student_count, 10));
                            }
                        });
                })
                .fail(function () { showToast('error', 'Failed to load report data.'); })
                .always(function () {
                    $btn.prop('disabled', false).html('<i class="bi bi-search me-1"></i> Load');
                });
        });

        // ── Section chart + table ─────────────────────────────
        function renderSection(report, enrolledCount) {
            var labels    = report.map(function (r) { return r.subject_name; });
            var passedArr = report.map(function (r) { return parseInt(r.passed); });
            var failedArr = report.map(function (r) { return parseInt(r.failed); });

            // Compute not-yet-graded using enrolled student count from manage-grade stats
            // not_graded per subject = enrolled - total_graded for that subject
            var notGradedArr = report.map(function (r) {
                if (enrolledCount === null) return 0;
                return Math.max(0, enrolledCount - parseInt(r.total_graded));
            });

            var totalPassed    = passedArr.reduce(function(a,b){return a+b;},0);
            var totalFailed    = failedArr.reduce(function(a,b){return a+b;},0);
            var totalNotGraded = notGradedArr.reduce(function(a,b){return a+b;},0);

            // Trim whitespace from PHP-generated option text to avoid line-break artifacts
            var className   = $('#filterClass option:selected').text().trim();
            var quarterName = $('#filterQuarter option:selected').text().trim();
            var classLabel  = className + ' · ' + quarterName;
            $('#sectionTitle').text(classLabel);

            var overallAvg = report.length
                ? (report.reduce(function(a,b){return a + parseFloat(b.avg_grade);},0) / report.length).toFixed(2)
                : '—';

            var pillsHtml =
                '<span class="stat-pill pill-avg"><i class="bi bi-graph-up"></i> Avg: ' + overallAvg + '</span>' +
                '<span class="stat-pill pill-pass"><i class="bi bi-check-circle"></i> Passed: ' + totalPassed + '</span>' +
                '<span class="stat-pill pill-fail"><i class="bi bi-x-circle"></i> Failed: ' + totalFailed + '</span>';
            if (totalNotGraded > 0) {
                pillsHtml += '<span class="stat-pill" style="background:#f3f4f6;color:#6b7280;"><i class="bi bi-dash-circle"></i> Not Yet Graded: ' + totalNotGraded + '</span>';
            }
            $('#sectionSummary').html(pillsHtml);

            // Subject table
            var tbody = '';
            if (report.length === 0) {
                tbody = '<tr><td colspan="6" class="text-center text-muted py-4">No grades found for this class and quarter.</td></tr>';
            } else {
                report.forEach(function (r, i) {
                    var avgVal = parseFloat(r.avg_grade);
                    var badge  = avgVal >= 75
                        ? '<span class="badge-pass">' + avgVal.toFixed(2) + '</span>'
                        : '<span class="badge-fail">' + avgVal.toFixed(2) + '</span>';
                    var notGraded = notGradedArr[i];
                    tbody += '<tr>' +
                        '<td>' + $('<span>').text(r.subject_name).html() + '</td>' +
                        '<td class="text-center">' + badge + '</td>' +
                        '<td class="text-center text-success fw-semibold">' + r.passed + '</td>' +
                        '<td class="text-center text-danger fw-semibold">' + r.failed + '</td>' +
                        '<td class="text-center text-muted">' + (notGraded > 0 ? notGraded : '—') + '</td>' +
                        '<td class="text-center">' + (enrolledCount !== null ? enrolledCount : r.total_graded) + '</td>' +
                        '</tr>';
                });
            }
            $('#subjectTableBody').html(tbody);

            // Breakdown text — each sentence on its own line for clean alignment
            var lines = [];
            if (report.length > 0) {
                lines.push('This report covers <strong>' + report.length + ' subject(s)</strong> for <strong>' + className + '</strong>, ' + quarterName + '.');
                lines.push('Overall class average (graded students only): <strong>' + overallAvg + '</strong> — ' + (parseFloat(overallAvg) >= 75 ? 'above' : 'below') + ' the passing mark of 75.');
                lines.push('Passing: <strong>' + totalPassed + '</strong> &nbsp;|&nbsp; Failing: <strong>' + totalFailed + '</strong>' + (totalNotGraded > 0 ? ' &nbsp;|&nbsp; Not yet graded: <strong>' + totalNotGraded + '</strong>' : '') + '.');
                var best    = report.reduce(function(a,b){ return parseFloat(a.avg_grade)>parseFloat(b.avg_grade)?a:b; });
                var weakest = report.reduce(function(a,b){ return parseFloat(a.avg_grade)<parseFloat(b.avg_grade)?a:b; });
                lines.push('Highest-performing subject: <strong>' + best.subject_name + '</strong> (avg ' + parseFloat(best.avg_grade).toFixed(2) + ').');
                if (best.subject_name !== weakest.subject_name) {
                    lines.push('Subject needing improvement: <strong>' + weakest.subject_name + '</strong> (avg ' + parseFloat(weakest.avg_grade).toFixed(2) + ').');
                }
                if (totalNotGraded > 0) {
                    lines.push('<span class="text-muted fst-italic"><i class="bi bi-info-circle me-1"></i>Note: <strong>' + totalNotGraded + '</strong> student(s) have not been graded yet and are excluded from the pass/fail count.</span>');
                }
            } else {
                lines.push('No grade data was found for this selection.');
            }
            $('#sectionBreakdownText').html(lines.map(function(l){ return '<div>' + l + '</div>'; }).join(''));

            // Bar chart
            chartDefaults();
            if (sectionChartInstance) sectionChartInstance.destroy();
            var ctx = document.getElementById('sectionChart').getContext('2d');
            sectionChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Passed',
                            data: passedArr,
                            backgroundColor: 'rgba(34, 197, 94, 0.6)',
                            borderColor: '#16a34a',
                            borderWidth: 1,
                            borderRadius: 6
                        },
                        {
                            label: 'Failed',
                            data: failedArr,
                            backgroundColor: 'rgba(239, 68, 68, 0.6)',
                            borderColor: '#dc2626',
                            borderWidth: 1,
                            borderRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    return ' ' + ctx.dataset.label + ': ' + ctx.parsed.y + ' students';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, precision: 0 },
                            title: { display: true, text: 'Number of Students' }
                        }
                    }
                }
            });
        }

        // ── Student picker ─────────────────────────────────────
        function loadStudentChart(studentId, studentName) {
            if (!studentId) { $('#studentChartArea').hide(); $('#studentEmpty').show(); return; }
            $.getJSON('/ewgs/user/reports/student-data', {
                class_id: currentClassId, student_id: studentId, quarter: currentQuarter
            }).done(function (data) {
                renderStudentChart(data, studentName);
            }).fail(function () { showToast('error', 'Failed to load student data.'); });
        }

        function populateStudentPicker(students) {
            var opts = '<option value="">— Select Student —</option>';
            students.forEach(function (s) {
                opts += '<option value="' + s.student_id + '">' + $('<span>').text(s.full_name).html() + '</option>';
            });
            // Destroy existing Select2 before replacing options
            if (typeof $.fn.select2 === 'function' && $('#filterStudent').hasClass('select2-hidden-accessible')) {
                $('#filterStudent').select2('destroy');
            }
            $('#filterStudent').html(opts);
            $('#studentChartArea').hide();
            $('#studentEmpty').show().text('Select a student to view their grade breakdown.');

            // Init Select2 with search (if available)
            if (typeof $.fn.select2 === 'function') {
                $('#filterStudent').select2({
                    placeholder: '— Select Student —',
                    allowClear: true,
                    width: '100%'
                });
            }

            // Auto-load the first student so the chart appears immediately
            if (students.length > 0) {
                $('#filterStudent').val(students[0].student_id).trigger('change');
                loadStudentChart(students[0].student_id, students[0].full_name);
            }
        }

        $('#filterStudent').on('change', function () {
            var name = $(this).find('option:selected').text();
            loadStudentChart($(this).val(), name === '— Select Student —' ? '' : name);
        });

        // ── Student chart ──────────────────────────────────────
        function renderStudentChart(data, studentName) {
            if (!data.length) {
                $('#studentChartArea').hide();
                $('#studentEmpty').text('No grades found for this student.').show();
                return;
            }
            $('#studentEmpty').hide();
            $('#studentChartArea').show();

            var labels = data.map(function (r) { return r.subject_name; });
            var finals = data.map(function (r) { return parseInt(r.final_grade); });
            var ww     = data.map(function (r) { return parseFloat(r.written_work); });
            var pt     = data.map(function (r) { return parseFloat(r.performance_task); });
            var qe     = data.map(function (r) { return parseFloat(r.quarterly_exam); });
            // Component weights (stored as decimals e.g. 0.25 → display as 25%)
            var wwWts  = data.map(function (r) { return Math.round(parseFloat(r.ww_weight) * 100); });
            var ptWts  = data.map(function (r) { return Math.round(parseFloat(r.pt_weight) * 100); });
            var qeWts  = data.map(function (r) { return Math.round(parseFloat(r.qe_weight) * 100); });

            var totalPassed = finals.filter(function(f){return f>=75;}).length;
            var totalFailed = finals.length - totalPassed;
            var avg = finals.length ? (finals.reduce(function(a,b){return a+b;},0)/finals.length).toFixed(2) : '—';
            var className   = $('#filterClass option:selected').text().trim();
            var quarterName = $('#filterQuarter option:selected').text().trim();

            $('#studentSummary').html(
                '<strong class="me-2">' + studentName + '</strong>' +
                '<span class="stat-pill pill-avg"><i class="bi bi-graph-up"></i> Avg: ' + avg + '</span>' +
                '<span class="stat-pill pill-pass"><i class="bi bi-check-circle"></i> Passed: ' + totalPassed + '</span>' +
                '<span class="stat-pill pill-fail"><i class="bi bi-x-circle"></i> Failed: ' + totalFailed + '</span>'
            );

            // ── Per-subject grade calculation breakdown ──────────
            var calcHtml = '';
            data.forEach(function (r) {
                var wwScore = parseFloat(r.written_work);
                var ptScore = parseFloat(r.performance_task);
                var qeScore = parseFloat(r.quarterly_exam);
                var wwWt    = parseFloat(r.ww_weight);
                var ptWt    = parseFloat(r.pt_weight);
                var qeWt    = parseFloat(r.qe_weight);
                var wwPct   = Math.round(wwWt * 100);
                var ptPct   = Math.round(ptWt * 100);
                var qePct   = Math.round(qeWt * 100);
                var wwContrib = (wwScore * wwWt).toFixed(2);
                var ptContrib = (ptScore * ptWt).toFixed(2);
                var qeContrib = (qeScore * qeWt).toFixed(2);
                var initialGrade = (wwScore * wwWt + ptScore * ptWt + qeScore * qeWt).toFixed(2);
                var fg = parseInt(r.final_grade);
                var finalRowClass = fg >= 75 ? 'final-row' : 'final-fail';
                var finalLabel = fg >= 75 ? '✓ Passed' : '✗ Failed';
                calcHtml +=
                    '<div class="subject-breakdown-block">' +
                    '<div class="subject-breakdown-title"><i class="bi bi-book me-1"></i>' + r.subject_name + '</div>' +
                    '<table class="calc-table">' +
                    '<thead><tr><th>Component</th><th class="text-center">Weight</th><th class="text-center">Score %</th><th class="text-center">Contribution</th></tr></thead>' +
                    '<tbody>' +
                    '<tr><td>Written Work</td><td class="text-center">' + wwPct + '%</td><td class="text-center">' + wwScore.toFixed(2) + '%</td><td class="text-center">' + wwScore.toFixed(2) + ' × ' + wwPct + '% = <strong>' + wwContrib + '</strong></td></tr>' +
                    '<tr><td>Performance Task</td><td class="text-center">' + ptPct + '%</td><td class="text-center">' + ptScore.toFixed(2) + '%</td><td class="text-center">' + ptScore.toFixed(2) + ' × ' + ptPct + '% = <strong>' + ptContrib + '</strong></td></tr>' +
                    '<tr><td>Quarterly Exam</td><td class="text-center">' + qePct + '%</td><td class="text-center">' + qeScore.toFixed(2) + '%</td><td class="text-center">' + qeScore.toFixed(2) + ' × ' + qePct + '% = <strong>' + qeContrib + '</strong></td></tr>' +
                    '<tr class="total-row"><td colspan="3">Initial Grade (weighted sum)</td><td class="text-center">' + initialGrade + '</td></tr>' +
                    '<tr class="' + finalRowClass + '"><td colspan="3">Final Grade (transmuted) — ' + finalLabel + '</td><td class="text-center">' + fg + '</td></tr>' +
                    '</tbody></table></div>';
            });

            // Summary breakdown text
            var sLines = [];
            sLines.push('<strong>' + studentName + '</strong> — <strong>' + data.length + ' subject(s)</strong>, ' + quarterName + ', ' + className + '.');
            sLines.push('Average final grade: <strong>' + avg + '</strong> — ' + (parseFloat(avg) >= 75 ? 'passing' : 'failing') + '.');
            sLines.push('Subjects passed: <strong>' + totalPassed + '</strong> &nbsp;|&nbsp; Failed: <strong>' + totalFailed + '</strong>.');
            if (data.length > 0) {
                var bestS    = data.reduce(function(a,b){ return parseInt(a.final_grade)>parseInt(b.final_grade)?a:b; });
                var weakestS = data.reduce(function(a,b){ return parseInt(a.final_grade)<parseInt(b.final_grade)?a:b; });
                sLines.push('Best subject: <strong>' + bestS.subject_name + '</strong> (grade ' + bestS.final_grade + ').');
                if (bestS.subject_name !== weakestS.subject_name) {
                    sLines.push('Needs improvement in: <strong>' + weakestS.subject_name + '</strong> (grade ' + weakestS.final_grade + ').');
                }
            }
            $('#studentBreakdownText').html(
                sLines.map(function(l){ return '<div>' + l + '</div>'; }).join('') +
                '<div class="section-label mt-3"><i class="bi bi-calculator me-1"></i>Grade Calculation per Subject</div>' +
                calcHtml
            );
            $('#btnExportStudent').show();

            // Use first subject's weights for legend labels (most subjects share same weights)
            var wwLabel = 'Written Work % (wt: ' + (wwWts[0] || 25) + '%)';
            var ptLabel = 'Performance Task % (wt: ' + (ptWts[0] || 50) + '%)';
            var qeLabel = 'Quarterly Exam % (wt: ' + (qeWts[0] || 25) + '%)';

            chartDefaults();
            if (studentChartInstance) studentChartInstance.destroy();
            var ctx = document.getElementById('studentChart').getContext('2d');
            studentChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: wwLabel,
                            data: ww,
                            backgroundColor: 'rgba(59,130,246,0.65)',
                            borderColor: '#2563eb',
                            borderWidth: 1,
                            borderRadius: 4
                        },
                        {
                            label: ptLabel,
                            data: pt,
                            backgroundColor: 'rgba(234,179,8,0.65)',
                            borderColor: '#ca8a04',
                            borderWidth: 1,
                            borderRadius: 4
                        },
                        {
                            label: qeLabel,
                            data: qe,
                            backgroundColor: 'rgba(168,85,247,0.65)',
                            borderColor: '#9333ea',
                            borderWidth: 1,
                            borderRadius: 4
                        },
                        {
                            label: 'Final Grade',
                            data: finals,
                            type: 'line',
                            borderColor: '#2e4e2e',
                            backgroundColor: 'rgba(46,78,46,0.1)',
                            pointBackgroundColor: finals.map(function(f){return f>=75?'#16a34a':'#dc2626';}),
                            pointRadius: 6,
                            borderWidth: 2,
                            tension: 0.3,
                            fill: false,
                            yAxisID: 'yFinal'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    var i = ctx.dataIndex;
                                    if (ctx.dataset.label === 'Final Grade')
                                        return ' Final Grade: ' + ctx.parsed.y + (ctx.parsed.y >= 75 ? ' ✓ Passed' : ' ✗ Failed');
                                    if (ctx.dataset.label === wwLabel) {
                                        var contrib = (ctx.parsed.y * (wwWts[i]/100)).toFixed(2);
                                        return ' Written Work: ' + ctx.parsed.y + '% score (wt ' + wwWts[i] + '%) → contributes ' + contrib + ' pts';
                                    }
                                    if (ctx.dataset.label === ptLabel) {
                                        var contrib = (ctx.parsed.y * (ptWts[i]/100)).toFixed(2);
                                        return ' Performance Task: ' + ctx.parsed.y + '% score (wt ' + ptWts[i] + '%) → contributes ' + contrib + ' pts';
                                    }
                                    if (ctx.dataset.label === qeLabel) {
                                        var contrib = (ctx.parsed.y * (qeWts[i]/100)).toFixed(2);
                                        return ' Quarterly Exam: ' + ctx.parsed.y + '% score (wt ' + qeWts[i] + '%) → contributes ' + contrib + ' pts';
                                    }
                                    return ' ' + ctx.dataset.label + ': ' + ctx.parsed.y + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        x: { stacked: false },
                        y: {
                            min: 0, max: 100,
                            title: { display: true, text: 'Component % Score' }
                        },
                        yFinal: {
                            type: 'linear',
                            position: 'right',
                            min: 60, max: 100,
                            title: { display: true, text: 'Final Grade' },
                            grid: { drawOnChartArea: false }
                        }
                    }
                }
            });
        }

        // ── PDF Export ─────────────────────────────────────────
        function exportPDF(canvasId, title, breakdownHtml, filename) {
            var canvas = document.getElementById(canvasId);
            html2canvas(canvas, { scale: 2, backgroundColor: '#ffffff' }).then(function (snap) {
                var imgData = snap.toDataURL('image/png');
                var { jsPDF } = window.jspdf;
                var doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
                var pageW = doc.internal.pageSize.getWidth();
                var pageH = doc.internal.pageSize.getHeight();
                var margin = 14;

                // ── Header bar (two lines) ──
                var headerH = 22;
                doc.setFillColor(46, 78, 46);
                doc.rect(0, 0, pageW, headerH, 'F');

                // Line 1: system name
                doc.setTextColor(255, 255, 255);
                doc.setFontSize(12);
                doc.setFont('helvetica', 'bold');
                doc.text('Elementary Web Grading System', margin, 9);

                // Line 2: report type only (class/quarter go in meta line below)
                doc.setFontSize(9);
                doc.setFont('helvetica', 'normal');
                doc.setTextColor(180, 210, 180);
                doc.text(title, margin, 17);

                // ── Meta line (outside header) ──
                var classLabel   = $('#filterClass option:selected').text().trim();
                var quarterLabel = $('#filterQuarter option:selected').text().trim();
                var now = new Date().toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' });
                var metaY = headerH + 7;
                doc.setFontSize(8);
                doc.setFont('helvetica', 'normal');
                doc.setTextColor(80, 80, 80);
                doc.text(classLabel + '   ·   ' + quarterLabel + '   ·   Generated: ' + now, margin, metaY);

                // Divider line
                doc.setDrawColor(200, 200, 200);
                doc.line(margin, metaY + 2, pageW - margin, metaY + 2);

                // ── Chart image ──
                var chartTop = metaY + 7;
                var maxChartH = pageH - chartTop - 28; // reserve space for breakdown + footer
                var imgW = pageW - margin * 2;
                var imgH = (snap.height / snap.width) * imgW;
                if (imgH > maxChartH) imgH = maxChartH;
                doc.addImage(imgData, 'PNG', margin, chartTop, imgW, imgH);

                // ── Breakdown text ──
                var plainText = $('<div>').html(breakdownHtml).text();
                var textY = chartTop + imgH + 5;
                doc.setFontSize(8);
                doc.setFont('helvetica', 'normal');
                doc.setTextColor(60, 60, 60);
                var lines = doc.splitTextToSize(plainText, pageW - margin * 2);
                // If text overflows page, add a new page
                if (textY + lines.length * 4 > pageH - 10) {
                    doc.addPage();
                    textY = 14;
                }
                doc.text(lines, margin, textY);

                // ── Footer ──
                doc.setFillColor(240, 245, 240);
                doc.rect(0, pageH - 8, pageW, 8, 'F');
                doc.setFontSize(7);
                doc.setTextColor(130, 130, 130);
                doc.text('EWGS — Confidential Grade Report', margin, pageH - 3);
                doc.text('Page 1', pageW - margin - 10, pageH - 3);

                doc.save(filename);
            });
        }

        $('#btnExportSection').on('click', function () {
            var title    = 'Section Performance';
            var breakdown = $('#sectionBreakdownText').html();
            exportPDF('sectionChart', title, breakdown, 'section-performance.pdf');
        });

        $('#btnExportStudent').on('click', function () {
            var student  = $('#filterStudent option:selected').text();
            var title    = 'Student Performance — ' + student;
            var breakdown = $('#studentBreakdownText').html();
            exportPDF('studentChart', title, breakdown, 'student-performance-' + student.replace(/[^a-z0-9]/gi, '_') + '.pdf');
        });

        // Re-render charts when dark mode toggles
        $('#mode-toggle').on('change', function () {
            setTimeout(function () {
                if (sectionChartInstance) { sectionChartInstance.options.plugins.legend.labels.color = isDark()?'#e0e0e0':'#374151'; sectionChartInstance.update(); }
                if (studentChartInstance) { studentChartInstance.options.plugins.legend.labels.color = isDark()?'#e0e0e0':'#374151'; studentChartInstance.update(); }
            }, 50);
        });

        // Init Select2 on student picker (empty state, if library loaded)
        if (typeof $.fn.select2 === 'function') {
            $('#filterStudent').select2({ placeholder: '— Select Student —', allowClear: true, width: '100%' });
        }

        // Flash toasts
        $('.flash-toast').each(function () {
            bootstrap.Toast.getOrCreateInstance(this, { delay: 5000 }).show();
        });
    });
    </script>
</body>
</html>
