<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | EWGS</title>
    <?php require_once 'views/templates/admin/header.php'; ?>
    <style>
        /* ── Stat Card ──────────────────────────────────────── */
        .stat-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
            overflow: hidden;
            text-align: center;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.13);
        }
        .stat-card-bar {
            height: 10px;
            background: linear-gradient(90deg, #2e4e2e, #4b6b4b);
        }
        .stat-card-body {
            padding: 22px 20px 20px;
        }
        .stat-circle {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #e8f5e9;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 1.7rem;
            color: #2e7d32;
        }
        .stat-title {
            font-size: 13px;
            font-weight: 600;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .stat-count {
            font-size: 2.4rem;
            font-weight: 800;
            color: #1a1a1a;
            line-height: 1;
            margin-bottom: 18px;
        }
        .stat-btn {
            display: inline-block;
            background: #2e4e2e;
            color: #fff !important;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 8px 22px;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.15s;
        }
        .stat-btn:hover { background: #4b6b4b; }

        /* ── Dash card ───────────────────────────────────────── */
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

        /* ── Activity table ──────────────────────────────────── */
        .activity-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }
        .activity-table thead th {
            background: #f5f7f5;
            color: #555;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 10px 16px;
            border-bottom: 1px solid #e8ece8;
        }
        .activity-table tbody td {
            padding: 11px 16px;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
            vertical-align: middle;
        }
        .activity-table tbody tr:last-child td { border-bottom: none; }
        .activity-table tbody tr:hover { background: #f9fbf9; }
        .activity-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: #e8f5e9;
            color: #2e7d32;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        .no-activity {
            text-align: center;
            padding: 28px;
            color: #bbb;
            font-size: 13px;
        }
        .no-activity i {
            font-size: 2rem;
            display: block;
            margin-bottom: 8px;
            color: #ddd;
        }

        /* ── Quick Actions ───────────────────────────────────── */
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
        .quick-action-link:hover {
            background: #f5fbf5;
            color: inherit;
        }
        .qa-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .qa-icon.green  { background: #e8f5e9; color: #2e7d32; }
        .qa-icon.blue   { background: #e3f2fd; color: #1565c0; }
        .qa-icon.purple { background: #f3e5f5; color: #6a1b9a; }
        .qa-icon.orange { background: #fff3e0; color: #e65100; }
        .qa-icon.teal   { background: #e0f2f1; color: #00796b; }
        .qa-icon.red    { background: #fce4ec; color: #c62828; }
        .qa-label { font-size: 13.5px; font-weight: 600; color: #222; }
        .qa-sub   { font-size: 12px; color: #999; }
        .qa-arrow { margin-left: auto; color: #ccc; font-size: 13px; }

        /* ── Dark Mode ───────────────────────────────────────── */
        body.dark-mode .stat-card,
        body.dark-mode .dash-card {
            background: #1e1e1e;
            box-shadow: 0 3px 12px rgba(0,0,0,0.35);
        }
        body.dark-mode .stat-card-bar { background: linear-gradient(90deg, #1a3a1a, #3a5a3a); }
        body.dark-mode .stat-circle   { background: #1b2e1b; color: #66bb6a; }
        body.dark-mode .stat-title    { color: #aaa; }
        body.dark-mode .stat-count    { color: #f0f0f0; }
        body.dark-mode .stat-btn      { background: #3a5a3a; }
        body.dark-mode .stat-btn:hover { background: #4b6b4b; }
        body.dark-mode .dash-card-header { background: #1a3a1a; }
        body.dark-mode .activity-table thead th {
            background: #252525; color: #aaa; border-bottom-color: #333;
        }
        body.dark-mode .activity-table tbody td {
            color: #e0e0e0; border-bottom-color: #2a2a2a;
        }
        body.dark-mode .activity-table tbody tr:hover { background: #252525; }
        body.dark-mode .activity-icon { background: #1b2e1b; color: #66bb6a; }
        body.dark-mode .no-activity   { color: #555; }
        body.dark-mode .no-activity i { color: #444; }
        body.dark-mode .quick-action-link { border-bottom-color: #2a2a2a; }
        body.dark-mode .quick-action-link:hover { background: #252525; }
        body.dark-mode .qa-label { color: #e0e0e0; }
        body.dark-mode .qa-sub   { color: #666; }
        body.dark-mode .qa-arrow { color: #444; }
        body.dark-mode .qa-icon.green  { background: #1b2e1b; color: #66bb6a; }
        body.dark-mode .qa-icon.blue   { background: #0d1f35; color: #64b5f6; }
        body.dark-mode .qa-icon.purple { background: #1e1128; color: #ce93d8; }
        body.dark-mode .qa-icon.orange { background: #2a1800; color: #ffb74d; }
        body.dark-mode .qa-icon.teal   { background: #00211e; color: #4db6ac; }
        body.dark-mode .qa-icon.red    { background: #2a0000; color: #ef9a9a; }
    </style>
</head>
<body>
    <?php require_once 'views/templates/admin/sidebar.php'; ?>
    <?= displayFlash() ?>

    <div class="main-content">
        <div class="page-header">
            <h4>Dashboard</h4>
            <span style="font-size:13px; opacity:0.85;"><?= date('l, F j, Y') ?></span>
        </div>

        <div class="container-fluid px-4">

            <!-- ── Stat Cards ── -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-card-bar"></div>
                        <div class="stat-card-body">
                            <div class="stat-circle"><i class="bi bi-person-badge"></i></div>
                            <div class="stat-title">Total Teachers</div>
                            <div class="stat-count"><?= $stats['teachers'] ?></div>
                            <a href="<?= BASE ?>/admin/teacher" class="stat-btn">Manage Teachers</a>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-card-bar"></div>
                        <div class="stat-card-body">
                            <div class="stat-circle"><i class="bi bi-people"></i></div>
                            <div class="stat-title">Total Students</div>
                            <div class="stat-count"><?= $stats['students'] ?></div>
                            <a href="<?= BASE ?>/admin/student" class="stat-btn">Manage Students</a>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-card-bar"></div>
                        <div class="stat-card-body">
                            <div class="stat-circle"><i class="bi bi-door-open"></i></div>
                            <div class="stat-title">Total Classes</div>
                            <div class="stat-count"><?= $stats['classes'] ?></div>
                            <a href="<?= BASE ?>/admin/class" class="stat-btn">Manage Classes</a>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card">
                        <div class="stat-card-bar"></div>
                        <div class="stat-card-body">
                            <div class="stat-circle"><i class="bi bi-journal-bookmark"></i></div>
                            <div class="stat-title">Total Subjects</div>
                            <div class="stat-count"><?= $stats['subjects'] ?></div>
                            <a href="<?= BASE ?>/admin/subject" class="stat-btn">Manage Subjects</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Bottom Row ── -->
            <div class="row g-3">

                <!-- Activity Column (stacked) -->
                <div class="col-md-7">
                    <div class="row g-3">

                        <!-- Admin Activity -->
                        <div class="col-12">
                            <div class="dash-card">
                                <div class="dash-card-header">
                                    <i class="bi bi-shield-lock"></i> Admin Activity
                                </div>
                                <?php if (empty($adminActivity)): ?>
                                    <div class="no-activity">
                                        <i class="bi bi-inbox"></i>No admin activity yet.
                                    </div>
                                <?php else: ?>
                                    <table class="activity-table">
                                        <thead>
                                            <tr>
                                                <th style="width:40px;">#</th>
                                                <th style="width:36px;"></th>
                                                <th>Activity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($adminActivity as $i => $item): ?>
                                            <tr>
                                                <td style="color:#aaa;"><?= $i + 1 ?></td>
                                                <td><span class="activity-icon"><i class="bi <?= $item['icon'] ?>"></i></span></td>
                                                <td><?= htmlspecialchars($item['label']) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Teacher Activity -->
                        <div class="col-12">
                            <div class="dash-card">
                                <div class="dash-card-header">
                                    <i class="bi bi-person-workspace"></i> Teacher Activity
                                </div>
                                <?php if (empty($teacherActivity)): ?>
                                    <div class="no-activity">
                                        <i class="bi bi-journal-x"></i>Teacher activity will appear here once grading begins.
                                    </div>
                                <?php else: ?>
                                    <table class="activity-table">
                                        <thead>
                                            <tr>
                                                <th style="width:40px;">#</th>
                                                <th style="width:36px;"></th>
                                                <th>Activity</th>
                                                <th style="width:130px;">When</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($teacherActivity as $i => $t): ?>
                                            <tr>
                                                <td style="color:#aaa;"><?= $i + 1 ?></td>
                                                <td><span class="activity-icon"><i class="bi bi-pencil-square"></i></span></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($t['teacher_name']) ?></strong>
                                                    saved grades &mdash;
                                                    <?= htmlspecialchars($t['subject_name']) ?>
                                                    &middot; <?= htmlspecialchars($t['class_name']) ?> (Grade <?= htmlspecialchars($t['grade_level']) ?>)
                                                    &middot; <?= htmlspecialchars($t['quarter']) ?> Quarter
                                                    <span style="color:#888;font-size:12px;">(<?= (int)$t['student_count'] ?> students)</span>
                                                </td>
                                                <td style="font-size:12px;color:#888;white-space:nowrap;">
                                                    <?= date('M j, g:i A', strtotime($t['last_saved'])) ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col-md-5">
                    <div class="dash-card h-100">
                        <div class="dash-card-header">
                            <i class="bi bi-lightning-charge"></i> Quick Actions
                        </div>
                        <a href="<?= BASE ?>/admin/teacher" class="quick-action-link">
                            <div class="qa-icon green"><i class="bi bi-person-plus"></i></div>
                            <div>
                                <div class="qa-label">Manage Teachers</div>
                                <div class="qa-sub">Add or remove teachers</div>
                            </div>
                            <i class="bi bi-chevron-right qa-arrow"></i>
                        </a>
                        <a href="<?= BASE ?>/admin/student" class="quick-action-link">
                            <div class="qa-icon green"><i class="bi bi-person-lines-fill"></i></div>
                            <div>
                                <div class="qa-label">Manage Students</div>
                                <div class="qa-sub">Add or import students</div>
                            </div>
                            <i class="bi bi-chevron-right qa-arrow"></i>
                        </a>
                        <a href="<?= BASE ?>/admin/class" class="quick-action-link">
                            <div class="qa-icon green"><i class="bi bi-building"></i></div>
                            <div>
                                <div class="qa-label">Manage Classes</div>
                                <div class="qa-sub">Create or delete classes</div>
                            </div>
                            <i class="bi bi-chevron-right qa-arrow"></i>
                        </a>
                        <a href="<?= BASE ?>/admin/subject" class="quick-action-link">
                            <div class="qa-icon green"><i class="bi bi-book"></i></div>
                            <div>
                                <div class="qa-label">Manage Subjects</div>
                                <div class="qa-sub">Add or remove subjects</div>
                            </div>
                            <i class="bi bi-chevron-right qa-arrow"></i>
                        </a>
                        <a href="<?= BASE ?>/admin/assign/student" class="quick-action-link">
                            <div class="qa-icon green"><i class="bi bi-person-check"></i></div>
                            <div>
                                <div class="qa-label">Link Students to a Class</div>
                                <div class="qa-sub">Assign students to classes</div>
                            </div>
                            <i class="bi bi-chevron-right qa-arrow"></i>
                        </a>
                        <a href="<?= BASE ?>/admin/assign/subject" class="quick-action-link">
                            <div class="qa-icon green"><i class="bi bi-journal-arrow-up"></i></div>
                            <div>
                                <div class="qa-label">Link Subjects to a Class</div>
                                <div class="qa-sub">Assign subjects to classes</div>
                            </div>
                            <i class="bi bi-chevron-right qa-arrow"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="<?= BASE ?>/public/js/bootstrap.bundle.js"></script>
    <script>
        $(function () {
            $('.flash-toast').each(function () {
                bootstrap.Toast.getOrCreateInstance(this, { delay: 5000 }).show();
            });
        });
    </script>
</body>
</html>
