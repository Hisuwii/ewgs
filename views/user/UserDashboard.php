<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | EWGS</title>
    <?php require_once 'views/templates/user/header.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        /* ── Stat Card (matches admin) ── */
        .stat-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
            overflow: hidden;
            text-align: center;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.13); }
        .stat-card-bar { height: 10px; background: linear-gradient(90deg, #2e4e2e, #4b6b4b); }
        .stat-card-body { padding: 22px 20px 20px; }
        .stat-circle {
            width: 64px; height: 64px; border-radius: 50%;
            background: #e8f5e9; display: flex; align-items: center; justify-content: center;
            margin: 0 auto 12px; font-size: 1.7rem; color: #2e7d32;
        }
        .stat-title {
            font-size: 13px; font-weight: 600; color: #555;
            text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;
        }
        .stat-count { font-size: 2.4rem; font-weight: 800; color: #1a1a1a; line-height: 1; margin-bottom: 4px; }

        @keyframes stat-flash { 0%,100% { opacity:1; } 50% { opacity:0.4; } }
        .stat-updated { animation: stat-flash 0.6s ease; }

        body.dark-mode .stat-card { background: #1e1e1e; box-shadow: 0 3px 12px rgba(0,0,0,0.35); }
        body.dark-mode .stat-card-bar { background: linear-gradient(90deg, #1a3a1a, #3a5a3a); }
        body.dark-mode .stat-circle   { background: #1b2e1b; color: #66bb6a; }
        body.dark-mode .stat-title    { color: #aaa; }
        body.dark-mode .stat-count    { color: #f0f0f0; }

        /* ── Dash card (matches admin) ── */
        .dash-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.07);
            overflow: hidden;
        }
        .dash-card-header {
            background: #2e4e2e;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            padding: 12px 18px;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .quick-action-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 18px;
            text-decoration: none;
            color: inherit;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.12s;
        }
        .quick-action-link:last-child { border-bottom: none; }
        .quick-action-link:hover { background: #f5fbf5; color: inherit; }
        .qa-icon {
            width: 38px; height: 38px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; flex-shrink: 0;
        }
        .qa-icon.green  { background: #e8f5e9; color: #2e7d32; }
        .qa-icon.blue   { background: #e3f2fd; color: #1565c0; }
        .qa-icon.orange { background: #fff3e0; color: #e65100; }
        .qa-icon.purple { background: #f3e5f5; color: #6a1b9a; }
        .qa-label { font-size: 13.5px; font-weight: 600; color: #222; }
        .qa-sub   { font-size: 12px; color: #999; }
        .qa-arrow { margin-left: auto; color: #ccc; font-size: 13px; }

        /* Chart card */
        .chart-card { background: #fff; border-radius: 14px; padding: 20px 24px; box-shadow: 0 3px 12px rgba(0,0,0,0.07); }
        .chart-card-title { font-size: 1rem; font-weight: 700; color: #2e4e2e; margin-bottom: 1rem; }
        .chart-wrapper { position: relative; height: 260px; }

        /* Dark mode */
        body.dark-mode .dash-card  { background: #1e1e1e; box-shadow: 0 3px 12px rgba(0,0,0,0.35); }
        body.dark-mode .dash-card-header { background: #1a3a1a; }
        body.dark-mode .quick-action-link { border-bottom-color: #2a2a2a; }
        body.dark-mode .quick-action-link:hover { background: #252525; }
        body.dark-mode .qa-label { color: #e0e0e0; }
        body.dark-mode .qa-sub   { color: #666; }
        body.dark-mode .qa-arrow { color: #444; }
        body.dark-mode .qa-icon.green  { background: #1b2e1b; color: #66bb6a; }
        body.dark-mode .qa-icon.blue   { background: #0d1f35; color: #64b5f6; }
        body.dark-mode .qa-icon.orange { background: #2a1800; color: #ffb74d; }
        body.dark-mode .qa-icon.purple { background: #1e1128; color: #ce93d8; }
        body.dark-mode .chart-card { background: #1e1e1e; color: #e0e0e0; }
        body.dark-mode .chart-card-title { color: #a5d6a7; }
        #classPickerChart {
            border-color: #c8d8c8;
            font-size: 13px;
        }
        #classPickerChart:focus { border-color: #4b6b4b; box-shadow: 0 0 0 2px rgba(75,107,75,0.15); }
        #chartSchoolYear {
            background: #e8f5e9;
            color: #2e4e2e;
            font-weight: 600;
            font-size: 12px;
            border: 1px solid #c8d8c8;
            border-radius: 6px;
            padding: 3px 10px;
        }
        body.dark-mode #classPickerChart { background: #2d2d2d; color: #e0e0e0; border-color: #444; }
        body.dark-mode #chartSchoolYear  { background: #1a2e1a; color: #a5d6a7; border-color: #3a5a3a; }
    </style>
</head>
<body>
    <?php require_once 'views/templates/user/sidebar.php'; ?>

    <?= displayFlash() ?>

    <div class="main-content">
        <div class="page-header">
            <h4>Dashboard</h4>
        </div>

        <div class="container-fluid px-4">

            <!-- Stats Cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-card-bar"></div>
                        <div class="stat-card-body">
                            <div class="stat-circle"><i class="bi bi-people"></i></div>
                            <div class="stat-title">My Classes</div>
                            <div class="stat-count" id="stat-classes"><?= $classCount ?? 0 ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-card-bar"></div>
                        <div class="stat-card-body">
                            <div class="stat-circle"><i class="bi bi-person"></i></div>
                            <div class="stat-title">Total Students</div>
                            <div class="stat-count" id="stat-students"><?= $studentCount ?? 0 ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-card-bar"></div>
                        <div class="stat-card-body">
                            <div class="stat-circle"><i class="bi bi-book"></i></div>
                            <div class="stat-title">Subjects</div>
                            <div class="stat-count" id="stat-subjects"><?= $subjectCount ?? 0 ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-card-bar"></div>
                        <div class="stat-card-body">
                            <div class="stat-circle"><i class="bi bi-clipboard-check"></i></div>
                            <div class="stat-title">Grades Entered</div>
                            <div class="stat-count" id="stat-grades"><?= $gradeCount ?? 0 ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">

                <!-- Quick Actions -->
                <div class="col-lg-5">
                    <div class="dash-card h-100">
                        <div class="dash-card-header">
                            <i class="bi bi-lightning-charge"></i> Quick Actions
                        </div>
                        <a href="/ewgs/user/add-grade" class="quick-action-link">
                            <div class="qa-icon green"><i class="bi bi-plus-circle-fill"></i></div>
                            <div>
                                <div class="qa-label">Add Grades</div>
                                <div class="qa-sub">Enter scores for a class subject</div>
                            </div>
                            <i class="bi bi-chevron-right qa-arrow"></i>
                        </a>
                        <a href="/ewgs/user/manage-grades" class="quick-action-link">
                            <div class="qa-icon blue"><i class="bi bi-clipboard-data-fill"></i></div>
                            <div>
                                <div class="qa-label">Manage Grades</div>
                                <div class="qa-sub">View and update existing grades</div>
                            </div>
                            <i class="bi bi-chevron-right qa-arrow"></i>
                        </a>
                        <a href="/ewgs/user/my-classes" class="quick-action-link">
                            <div class="qa-icon orange"><i class="bi bi-people-fill"></i></div>
                            <div>
                                <div class="qa-label">My Classes</div>
                                <div class="qa-sub">View your assigned classes</div>
                            </div>
                            <i class="bi bi-chevron-right qa-arrow"></i>
                        </a>
                        <a href="/ewgs/user/reports" class="quick-action-link">
                            <div class="qa-icon purple"><i class="bi bi-bar-chart-line-fill"></i></div>
                            <div>
                                <div class="qa-label">Reports</div>
                                <div class="qa-sub">View class and student performance</div>
                            </div>
                            <i class="bi bi-chevron-right qa-arrow"></i>
                        </a>
                    </div>
                </div>

                <!-- Grade Overview Chart -->
                <div class="col-lg-7">
                    <div class="dash-card">
                        <div class="dash-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <span><i class="bi bi-bar-chart-fill me-1"></i><span id="chartTitle">All Classes</span></span>
                            <div class="d-flex align-items-center gap-2">
                                <span id="chartSchoolYear" style="display:none;"></span>
                                <select id="classPickerChart" class="form-select form-select-sm" style="width:auto;min-width:160px;">
                                    <option value="">All Classes</option>
                                    <?php foreach ($classes as $c): ?>
                                        <option value="<?= $c['class_id'] ?>"
                                            data-sy="<?= htmlspecialchars($c['school_year']) ?>"
                                            data-name="<?= htmlspecialchars($c['class_name'] . ' · Grade ' . $c['grade_level']) ?>">
                                            <?= htmlspecialchars($c['class_name'] . ' – Grade ' . $c['grade_level']) ?>
                                            <small>(<?= htmlspecialchars($c['school_year']) ?>)</small>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div id="chartNoData" class="text-center text-muted py-4" style="display:none;">
                            <i class="bi bi-info-circle me-1"></i> No grade data yet for this selection.
                        </div>
                        <div class="chart-wrapper" style="padding:0 16px 16px;">
                            <canvas id="gradeOverviewChart"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="/ewgs/public/js/bootstrap.bundle.js"></script>
    <script>
    $(function () {
        $('.flash-toast').each(function () {
            bootstrap.Toast.getOrCreateInstance(this, { delay: 5000 }).show();
        });

        // ── Stat polling ──────────────────────────────────────
        function animateCount($el, newVal) {
            var current = parseInt($el.text(), 10);
            if (current === newVal) return;
            $el.text(newVal);
            $el.addClass('stat-updated');
            setTimeout(function () { $el.removeClass('stat-updated'); }, 600);
        }
        function refreshStats() {
            $.getJSON('/ewgs/user/dashboard/stats', function (data) {
                animateCount($('#stat-classes'),  data.classes);
                animateCount($('#stat-students'), data.students);
                animateCount($('#stat-subjects'), data.subjects);
            });
        }
        setInterval(refreshStats, 10000);

        // ── Grade overview chart ──────────────────────────────
        var overviewChart = null;
        function isDark() { return $('body').hasClass('dark-mode'); }

        function loadOverviewChart(classId) {
            var url = '/ewgs/user/reports/dashboard-chart';
            if (classId) url += '?class_id=' + classId;

            $.getJSON(url, function (data) {
                if (!data.length) {
                    $('#chartNoData').show();
                    $('.chart-wrapper').hide();
                    return;
                }
                $('#chartNoData').hide();
                $('.chart-wrapper').show();

                var isClassView = !!classId;
                var labels  = data.map(function (d) { return isClassView ? d.subject_name : d.class_name; });
                var passed  = data.map(function (d) { return parseInt(d.passed); });
                var failed  = data.map(function (d) { return parseInt(d.failed); });
                var yLabel  = isClassView ? 'Students' : 'Grade Records';

                Chart.defaults.color = isDark() ? '#e0e0e0' : '#374151';
                if (overviewChart) overviewChart.destroy();
                var ctx = document.getElementById('gradeOverviewChart').getContext('2d');
                overviewChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Passed',
                                data: passed,
                                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                                borderColor: '#16a34a',
                                borderWidth: 1,
                                borderRadius: 6
                            },
                            {
                                label: 'Failed',
                                data: failed,
                                backgroundColor: 'rgba(239, 68, 68, 0.65)',
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
                                        return ' ' + ctx.dataset.label + ': ' + ctx.parsed.y + (isClassView ? ' students' : ' grade records');
                                    }
                                }
                            }
                        },
                        scales: {
                            x: { stacked: false },
                            y: { beginAtZero: true, title: { display: true, text: yLabel } }
                        }
                    }
                });
            });
        }

        // Class picker change
        $('#classPickerChart').on('change', function () {
            var classId  = $(this).val();
            var $opt     = $(this).find('option:selected');
            var name     = classId ? $opt.data('name') : 'All Classes';
            var sy       = $opt.data('sy') || '';
            $('#chartTitle').text(name);
            if (sy) {
                $('#chartSchoolYear').text('S.Y. ' + sy).css('display', 'inline-block');
            } else {
                $('#chartSchoolYear').hide();
            }
            loadOverviewChart(classId || null);
        });

        loadOverviewChart(null);

        // Refresh chart on dark mode toggle
        $('#mode-toggle').on('change', function () {
            var classId = $('#classPickerChart').val() || null;
            setTimeout(function () { loadOverviewChart(classId); }, 60);
        });
    });
    </script>
</body>
</html>
