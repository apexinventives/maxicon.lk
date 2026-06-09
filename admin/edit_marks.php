<?php
require_once 'config.php';
requireAdminLogin();

$pdo = getDB();
$search = $_GET['search'] ?? '';
$selectedStudent = $_GET['exam_id'] ?? '';
$marks = [];
$studentName = '';

// Search students
$students = [];
if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT exam_id, student_name, grade, medium FROM students WHERE student_name LIKE ? OR exam_id LIKE ? ORDER BY student_name LIMIT 50");
    $stmt->execute(["%$search%", "%$search%"]);
    $students = $stmt->fetchAll();
} else if (!empty($selectedStudent)) {
    $stmt = $pdo->prepare("SELECT exam_id, student_name, grade, medium FROM students WHERE exam_id = ?");
    $stmt->execute([$selectedStudent]);
    $students = $stmt->fetchAll();
} else {
    // Show recent students
    $stmt = $pdo->query("SELECT exam_id, student_name, grade, medium FROM students ORDER BY created_at DESC LIMIT 20");
    $students = $stmt->fetchAll();
}

if ($selectedStudent) {
    $stmt = $pdo->prepare("SELECT student_name, grade, medium FROM students WHERE exam_id = ?");
    $stmt->execute([$selectedStudent]);
    $studentInfo = $stmt->fetch();
    if ($studentInfo) {
        $studentName = $studentInfo['student_name'];
        $studentGrade = $studentInfo['grade'];
        $studentMedium = $studentInfo['medium'];
    }
    
    $stmt = $pdo->prepare("SELECT * FROM weekly_marks WHERE exam_id = ? ORDER BY week_number ASC");
    $stmt->execute([$selectedStudent]);
    $marks = $stmt->fetchAll();
}

// Handle mark update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_marks'])) {
    foreach ($_POST['marks'] as $markId => $data) {
        $stmt = $pdo->prepare("UPDATE weekly_marks SET mission_20 = ?, homework = ?, total_score = ?, rank_position = ?, attendance = ? WHERE id = ?");
        $stmt->execute([$data['mission_20'], $data['homework'], $data['total'], $data['rank'], $data['attendance'], $markId]);
    }
    
    // Recalculate ranks for this week
    if (!empty($marks)) {
        $weeksToUpdate = array_unique(array_column($marks, 'week_number'));
        foreach ($weeksToUpdate as $weekNum) {
            $stmt = $pdo->prepare("
                UPDATE weekly_marks w1
                JOIN (
                    SELECT exam_id, total_score,
                        @rank := @rank + 1 as new_rank
                    FROM weekly_marks, (SELECT @rank := 0) r
                    WHERE week_number = ?
                    ORDER BY total_score DESC, mission_20 DESC
                ) w2 ON w1.exam_id = w2.exam_id
                SET w1.rank_position = w2.new_rank
                WHERE w1.week_number = ?
            ");
            $stmt->execute([$weekNum, $weekNum]);
        }
    }
    
    header("Location: edit_marks.php?exam_id=$selectedStudent&success=1");
    exit();
}

// Handle bulk update for all weeks
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_update'])) {
    $weekNumber = $_POST['week_number'] ?? 0;
    $attendance = $_POST['bulk_attendance'] ?? '';
    $mission20 = $_POST['bulk_mission20'] ?? null;
    $homework = $_POST['bulk_homework'] ?? null;
    
    if ($weekNumber && $selectedStudent) {
        $sql = "UPDATE weekly_marks SET ";
        $params = [];
        
        if ($attendance) {
            $sql .= "attendance = ?, ";
            $params[] = $attendance;
        }
        if ($mission20 !== null && $mission20 !== '') {
            $sql .= "mission_20 = ?, ";
            $params[] = $mission20;
        }
        if ($homework !== null && $homework !== '') {
            $sql .= "homework = ?, ";
            $params[] = $homework;
        }
        
        $sql = rtrim($sql, ', ');
        $sql .= " WHERE exam_id = ? AND week_number = ?";
        $params[] = $selectedStudent;
        $params[] = $weekNumber;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        header("Location: edit_marks.php?exam_id=$selectedStudent&success=1");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Marks - Maxicon Admin</title>
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
            z-index: 100;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 24px;
            border-radius: 12px;
            margin: 4px 12px;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
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
        .btn-apex {
            background: linear-gradient(135deg, #f57c00, #ff9f4a);
            border: none;
            border-radius: 40px;
            padding: 10px 24px;
            color: white;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-apex:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(245,124,0,0.3);
            color: white;
        }
        .btn-outline-apex {
            border: 2px solid #f57c00;
            background: transparent;
            border-radius: 40px;
            padding: 8px 20px;
            color: #f57c00;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-outline-apex:hover {
            background: #f57c00;
            color: white;
        }
        .search-box {
            background: white;
            border-radius: 60px;
            padding: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .search-box input {
            border: none;
            padding: 12px 20px;
            border-radius: 60px;
            font-size: 0.9rem;
        }
        .search-box button {
            background: linear-gradient(135deg, #f57c00, #ff9f4a);
            border: none;
            border-radius: 60px;
            padding: 10px 25px;
            color: white;
            font-weight: 600;
        }
        .student-card {
            background: white;
            border-radius: 20px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
            border: 1px solid #eee;
        }
        .student-card:hover {
            border-color: #f57c00;
            box-shadow: 0 5px 15px rgba(245,124,0,0.1);
            transform: translateX(5px);
        }
        .student-card.active {
            border-color: #f57c00;
            background: #fff8f0;
        }
        .edit-table th {
            background: #fff3e0;
            color: #f57c00;
            font-weight: 600;
        }
        .bulk-card {
            background: white;
            border-radius: 16px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="p-4">
            <h3 class="text-white">Maxicon<span style="color:#f57c00;">Admin</span></h3>
            <p class="text-white-50 small">Exam Management System</p>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a class="nav-link" href="upload.php"><i class="fas fa-upload"></i> Upload Exam</a>
            <a class="nav-link" href="manage_students.php"><i class="fas fa-users"></i> Manage Students</a>
            <a class="nav-link active" href="edit_marks.php"><i class="fas fa-edit"></i> Edit Marks</a>
            <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>
    
    <div class="main-content">
        <h2 class="mb-4"><i class="fas fa-edit me-2"></i> Edit Student Marks</h2>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> Marks updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Left Side - Student Search -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3">
                    <h5 class="mb-3"><i class="fas fa-search me-2" style="color:#f57c00;"></i> Find Student</h5>
                    
                    <!-- Search Form -->
                    <form method="GET" action="">
                        <div class="search-box d-flex">
                            <input type="text" name="search" class="form-control flex-grow-1" placeholder="Search by name or Exam ID..." value="<?php echo htmlspecialchars($search); ?>" autocomplete="off">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <!-- Student List -->
                    <div class="student-list mt-3" style="max-height: 500px; overflow-y: auto;">
                        <?php if (empty($students)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-users fa-2x mb-2 opacity-50"></i>
                                <p>No students found<br><small>Try searching by name or Exam ID</small></p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($students as $student): ?>
                                <div class="student-card <?php echo ($selectedStudent == $student['exam_id']) ? 'active' : ''; ?>" 
                                     onclick="window.location.href='edit_marks.php?exam_id=<?php echo urlencode($student['exam_id']); ?>'">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong><i class="fas fa-user-graduate me-1" style="color:#f57c00;"></i> <?php echo htmlspecialchars($student['student_name']); ?></strong>
                                            <div class="small text-muted mt-1">
                                                <code><?php echo $student['exam_id']; ?></code><br>
                                                <?php echo $student['grade']; ?> | <?php echo $student['medium']; ?>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-right" style="color:#f57c00; font-size: 0.8rem;"></i>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - Edit Marks -->
            <div class="col-md-8">
                <?php if ($selectedStudent && $studentName): ?>
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="mb-1"><?php echo htmlspecialchars($studentName); ?></h4>
                                <p class="text-muted mb-0">
                                    <code><?php echo $selectedStudent; ?></code> | 
                                    <?php echo $studentGrade; ?> | 
                                    <?php echo $studentMedium; ?> Medium
                                </p>
                            </div>
                            <a href="edit_student.php?exam_id=<?php echo $selectedStudent; ?>" class="btn btn-outline-apex">
                                <i class="fas fa-user-edit me-2"></i> Edit Student
                            </a>
                        </div>
                        
                        <?php if (empty($marks)): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i> 
                                No marks found for this student. Please upload exam data first.
                            </div>
                        <?php else: ?>
                            <!-- Bulk Update Section -->
                            <div class="bulk-card">
                                <h6 class="mb-3"><i class="fas fa-layer-group me-2" style="color:#f57c00;"></i> Bulk Update for Specific Week</h6>
                                <form method="POST" class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label small">Select Week</label>
                                        <select name="week_number" class="form-control form-control-sm" style="border-radius: 60px;" required>
                                            <option value="">Choose Week</option>
                                            <?php foreach ($marks as $mark): ?>
                                                <option value="<?php echo $mark['week_number']; ?>">
                                                    Week <?php echo $mark['week_number']; ?> (<?php echo date('d M', strtotime($mark['week_date'])); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">Attendance</label>
                                        <select name="bulk_attendance" class="form-control form-control-sm" style="border-radius: 60px;">
                                            <option value="">-- Keep --</option>
                                            <option value="Attended">Attended</option>
                                            <option value="Absent">Absent</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">Mission 20</label>
                                        <input type="number" name="bulk_mission20" class="form-control form-control-sm" style="border-radius: 60px;" placeholder="Score">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">Homework</label>
                                        <input type="number" name="bulk_homework" class="form-control form-control-sm" style="border-radius: 60px;" placeholder="Score">
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" name="bulk_update" class="btn btn-sm btn-apex w-100">
                                            <i class="fas fa-save me-1"></i> Apply to Week
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Individual Marks Edit Form -->
                            <form method="POST">
                                <div class="table-responsive">
                                    <table class="table table-bordered edit-table">
                                        <thead>
                                            <tr>
                                                <th>Week</th>
                                                <th>Date</th>
                                                <th>Attendance</th>
                                                <th>Mission 20</th>
                                                <th>Homework</th>
                                                <th>Total</th>
                                                <th>Rank</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($marks as $mark): ?>
                                            <tr>
                                                <td><strong>Week <?php echo $mark['week_number']; ?></strong></td>
                                                <td><?php echo date('d M Y', strtotime($mark['week_date'])); ?></td>
                                                <td>
                                                    <select name="marks[<?php echo $mark['id']; ?>][attendance]" class="form-control form-control-sm" style="border-radius: 60px;">
                                                        <option value="Attended" <?php echo $mark['attendance'] == 'Attended' ? 'selected' : ''; ?>>Attended</option>
                                                        <option value="Absent" <?php echo $mark['attendance'] == 'Absent' ? 'selected' : ''; ?>>Absent</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="marks[<?php echo $mark['id']; ?>][mission_20]" 
                                                           value="<?php echo $mark['mission_20']; ?>" class="form-control form-control-sm" style="border-radius: 60px;">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="marks[<?php echo $mark['id']; ?>][homework]" 
                                                           value="<?php echo $mark['homework']; ?>" class="form-control form-control-sm" style="border-radius: 60px;">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="marks[<?php echo $mark['id']; ?>][total]" 
                                                           value="<?php echo $mark['total_score']; ?>" class="form-control form-control-sm" style="border-radius: 60px;">
                                                </td>
                                                <td>
                                                    <input type="number" name="marks[<?php echo $mark['id']; ?>][rank]" 
                                                           value="<?php echo $mark['rank_position']; ?>" class="form-control form-control-sm" style="border-radius: 60px;">
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="submit" name="update_marks" class="btn btn-apex">
                                        <i class="fas fa-save me-2"></i> Save All Changes
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="window.location.reload();">
                                        <i class="fas fa-sync-alt me-2"></i> Refresh
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                        <i class="fas fa-user-search fa-4x mb-3" style="color:#f57c00; opacity:0.5;"></i>
                        <h4>Select a Student</h4>
                        <p class="text-muted">Search and select a student from the left panel to edit their marks</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // Live search on student list (optional)
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                // Form will submit automatically
            }
        });
    }
</script>
</body>
</html>