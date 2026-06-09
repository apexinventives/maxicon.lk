<?php
require_once 'config.php';
requireAdminLogin();

$pdo = getDB();

// Get statistics
$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalWeeks = $pdo->query("SELECT COUNT(DISTINCT week_number) FROM weekly_marks")->fetchColumn();
$totalMarks = $pdo->query("SELECT COUNT(*) FROM weekly_marks")->fetchColumn();

// Get recent weeks
$recentWeeks = $pdo->query("SELECT DISTINCT week_number, week_date, grade, medium FROM weeks_metadata ORDER BY week_date DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Maxicon Institute</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .sidebar {
            background: linear-gradient(180deg, #1a1a2e 0%, #0a0a0f 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 24px;
            border-radius: 12px;
            margin: 4px 12px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(245,124,0,0.2);
            color: #f57c00;
        }
        .sidebar .nav-link i { width: 24px; margin-right: 12px; }
        .main-content {
            margin-left: 260px;
            padding: 24px;
            background: #f8fafc;
            min-height: 100vh;
        }
        .stat-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .stat-card i { font-size: 2.5rem; color: #f57c00; }
        .btn-apex { background: linear-gradient(135deg, #f57c00, #ff9f4a); border: none; border-radius: 40px; padding: 10px 24px; color: white; font-weight: 600; }
        .btn-outline-apex { border: 2px solid #f57c00; background: transparent; border-radius: 40px; padding: 8px 20px; color: #f57c00; font-weight: 600; }
        .btn-outline-apex:hover { background: #f57c00; color: white; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="p-4">
            <h3 class="text-white">Maxicon<span style="color:#f57c00;">Admin</span></h3>
            <p class="text-white-50 small">Exam Management System</p>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Dashboard</h2>
            <div>
                <span class="text-muted me-3">Welcome, <?php echo $_SESSION['admin_username']; ?></span>
            </div>
        </div>
        
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center">
                    <i class="fas fa-user-graduate me-3"></i>
                    <div>
                        <h3 class="mb-0"><?php echo $totalStudents; ?></h3>
                        <p class="text-muted mb-0">Total Students</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center">
                    <i class="fas fa-calendar-week me-3"></i>
                    <div>
                        <h3 class="mb-0"><?php echo $totalWeeks; ?></h3>
                        <p class="text-muted mb-0">Weeks Uploaded</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center">
                    <i class="fas fa-chart-line me-3"></i>
                    <div>
                        <h3 class="mb-0"><?php echo $totalMarks; ?></h3>
                        <p class="text-muted mb-0">Total Marks Entries</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="stat-card">
                    <h4><i class="fas fa-history me-2"></i> Recent Uploads</h4>
                    <table class="table">
                        <thead>
                            <tr><th>Week</th><th>Date</th><th>Grade</th><th>Medium</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentWeeks as $week): ?>
                            <tr>
                                <td>Week <?php echo $week['week_number']; ?></td>
                                <td><?php echo $week['week_date']; ?></td>
                                <td><?php echo $week['grade']; ?></td>
                                <td><?php echo $week['medium']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentWeeks)): ?>
                            <tr><td colspan="4" class="text-center">No data yet. Upload your first exam file.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>