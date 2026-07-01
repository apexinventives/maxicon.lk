<?php
require_once 'config.php';
requireAdminLogin();

$pdo = getDB();

// Get dashboard statistics
$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalWeeks = $pdo->query("SELECT COUNT(*) FROM weeks_metadata")->fetchColumn();
$totalMarks = $pdo->query("SELECT COUNT(*) FROM weekly_marks")->fetchColumn();
$totalUploads = $pdo->query("SELECT COUNT(*) FROM weeks_metadata")->fetchColumn();

// Grade distribution
$gradeBreakdown = $pdo->query("SELECT grade, COUNT(*) AS total FROM students GROUP BY grade ORDER BY total DESC")->fetchAll();

// Medium distribution
$mediumBreakdown = $pdo->query("SELECT medium, COUNT(*) AS total FROM students GROUP BY medium ORDER BY total DESC")->fetchAll();

// Recent uploads
$recentWeeks = $pdo->query("SELECT week_number, week_date, grade, medium FROM weeks_metadata ORDER BY week_date DESC, week_number DESC LIMIT 8")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Power Dashboard - Maxicon Institute</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f4f7fb; }
        .sidebar {
            background: linear-gradient(180deg, #111827 0%, #0f172a 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.74);
            padding: 12px 24px;
            border-radius: 12px;
            margin: 4px 12px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(245,124,0,0.2);
            color: #f59e0b;
        }
        .sidebar .nav-link i { width: 24px; margin-right: 12px; }
        .main-content {
            margin-left: 260px;
            padding: 24px;
            min-height: 100vh;
        }
        .hero-card {
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            color: white;
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 16px 40px rgba(249,115,22,0.18);
        }
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 8px 24px rgba(15,23,42,0.06);
            transition: all 0.3s;
        }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 14px 32px rgba(15,23,42,0.1); }
        .stat-card i { font-size: 1.8rem; color: #f59e0b; }
        .btn-apex { background: linear-gradient(135deg, #f59e0b, #f97316); border: none; border-radius: 40px; padding: 10px 22px; color: white; font-weight: 600; }
        .btn-outline-apex { border: 2px solid #f59e0b; background: transparent; border-radius: 40px; padding: 8px 18px; color: #f59e0b; font-weight: 600; }
        .btn-outline-apex:hover { background: #f59e0b; color: white; }
        .progress-bar-custom { background: #fef3c7; border-radius: 999px; overflow: hidden; }
        .progress-bar-custom .progress-bar { background: linear-gradient(90deg, #f59e0b, #f97316); }
        .table thead { background: #fff7ed; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="p-4">
            <h3 class="text-white">Maxicon<span style="color:#f59e0b;">Admin</span></h3>
            <p class="text-white-50 small">Powerful Exam Management</p>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link active" href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a class="nav-link" href="upload.php"><i class="fas fa-upload"></i> Upload Exam</a>
            <a class="nav-link" href="manage_students.php"><i class="fas fa-users"></i> Manage Students</a>
            <a class="nav-link" href="edit_marks.php"><i class="fas fa-edit"></i> Edit Marks</a>
            <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="hero-card mb-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h2 class="mb-2">Power Dashboard</h2>
                    <p class="mb-0 opacity-75">Monitor your exam data, uploads, and student activity from one place.</p>
                </div>
                <div>
                    <a href="upload.php" class="btn btn-apex me-2">Upload New Exam</a>
                    <a href="manage_students.php" class="btn btn-outline-apex">Manage Students</a>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <i class="fas fa-user-graduate"></i>
                        <span class="badge bg-light text-dark">Students</span>
                    </div>
                    <h3 class="mb-1"><?php echo $totalStudents; ?></h3>
                    <p class="text-muted mb-0">Registered students</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <i class="fas fa-file-upload"></i>
                        <span class="badge bg-light text-dark">Uploads</span>
                    </div>
                    <h3 class="mb-1"><?php echo $totalUploads; ?></h3>
                    <p class="text-muted mb-0">Weekly uploads</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <i class="fas fa-calendar-week"></i>
                        <span class="badge bg-light text-dark">Weeks</span>
                    </div>
                    <h3 class="mb-1"><?php echo $totalWeeks; ?></h3>
                    <p class="text-muted mb-0">Tracked week records</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <i class="fas fa-chart-line"></i>
                        <span class="badge bg-light text-dark">Marks</span>
                    </div>
                    <h3 class="mb-1"><?php echo $totalMarks; ?></h3>
                    <p class="text-muted mb-0">Mark entries stored</p>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="stat-card h-100">
                    <h5 class="mb-3"><i class="fas fa-layer-group me-2"></i> Grade Distribution</h5>
                    <?php if (!empty($gradeBreakdown)): ?>
                        <?php foreach ($gradeBreakdown as $grade): ?>
                            <?php $percent = $totalStudents > 0 ? round(($grade['total'] / $totalStudents) * 100) : 0; ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-semibold"><?php echo htmlspecialchars($grade['grade']); ?></span>
                                    <span class="text-muted"><?php echo $grade['total']; ?> students</span>
                                </div>
                                <div class="progress-bar-custom">
                                    <div class="progress-bar" style="width: <?php echo $percent; ?>%; height: 8px;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">No grade data available yet.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="stat-card h-100">
                    <h5 class="mb-3"><i class="fas fa-language me-2"></i> Medium Distribution</h5>
                    <?php if (!empty($mediumBreakdown)): ?>
                        <?php foreach ($mediumBreakdown as $medium): ?>
                            <?php $percent = $totalStudents > 0 ? round(($medium['total'] / $totalStudents) * 100) : 0; ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-semibold"><?php echo htmlspecialchars($medium['medium']); ?></span>
                                    <span class="text-muted"><?php echo $medium['total']; ?> students</span>
                                </div>
                                <div class="progress-bar-custom">
                                    <div class="progress-bar" style="width: <?php echo $percent; ?>%; height: 8px;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">No medium data available yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i> Recent Uploads</h5>
                <a href="upload.php" class="btn btn-outline-apex btn-sm">Open Uploads</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Week</th>
                            <th>Date</th>
                            <th>Grade</th>
                            <th>Medium</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentWeeks as $week): ?>
                        <tr>
                            <td><span class="fw-semibold">Week <?php echo htmlspecialchars($week['week_number']); ?></span></td>
                            <td><?php echo htmlspecialchars($week['week_date']); ?></td>
                            <td><?php echo htmlspecialchars($week['grade']); ?></td>
                            <td><?php echo htmlspecialchars($week['medium']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentWeeks)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">No uploads available yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>