<?php
require_once 'config.php';
requireAdminLogin();

$pdo = getDB();
$error = '';
$success = '';

$examId = $_GET['exam_id'] ?? '';

if (empty($examId)) {
    header('Location: manage_students.php');
    exit();
}

// Get student data
$stmt = $pdo->prepare("SELECT * FROM students WHERE exam_id = ?");
$stmt->execute([$examId]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: manage_students.php?error=Student not found');
    exit();
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $studentName = trim($_POST['student_name'] ?? '');
    $grade = $_POST['grade'] ?? '';
    $medium = $_POST['medium'] ?? '';
    
    if (empty($studentName) || empty($grade) || empty($medium)) {
        $error = 'All fields are required';
    } else {
        $stmt = $pdo->prepare("UPDATE students SET student_name = ?, grade = ?, medium = ? WHERE exam_id = ?");
        if ($stmt->execute([$studentName, $grade, $medium, $examId])) {
            $success = 'Student updated successfully!';
            // Refresh student data
            $stmt = $pdo->prepare("SELECT * FROM students WHERE exam_id = ?");
            $stmt->execute([$examId]);
            $student = $stmt->fetch();
        } else {
            $error = 'Failed to update student';
        }
    }
}

// Handle regenerating Exam ID
if (isset($_GET['regenerate_id'])) {
    $newExamId = generateExamID($student['student_name'], $student['grade']);
    
    // Check if new ID already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE exam_id = ?");
    $stmt->execute([$newExamId]);
    if ($stmt->fetchColumn() == 0) {
        // Update student records
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE weekly_marks SET exam_id = ? WHERE exam_id = ?");
            $stmt->execute([$newExamId, $examId]);
            
            $stmt = $pdo->prepare("UPDATE students SET exam_id = ? WHERE exam_id = ?");
            $stmt->execute([$newExamId, $examId]);
            
            $pdo->commit();
            header("Location: edit_student.php?exam_id=$newExamId&success=ID regenerated successfully");
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Failed to regenerate Exam ID: ' . $e->getMessage();
        }
    } else {
        $error = 'Generated ID already exists. Please try again.';
    }
}

// Get student's weekly marks summary
$stmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total_weeks,
        AVG(total_score) as avg_score,
        SUM(CASE WHEN attendance = 'Attended' THEN 1 ELSE 0 END) as attended_weeks,
        MAX(total_score) as highest_score,
        MIN(total_score) as lowest_score
    FROM weekly_marks 
    WHERE exam_id = ?
");
$stmt->execute([$examId]);
$summary = $stmt->fetch();

// Get all weeks for this student
$stmt = $pdo->prepare("SELECT * FROM weekly_marks WHERE exam_id = ? ORDER BY week_number ASC");
$stmt->execute([$examId]);
$weeks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - Maxicon Admin</title>
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
        .info-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 24px;
        }
        .stat-box {
            background: linear-gradient(135deg, #fff3e0, #ffffff);
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            border: 1px solid rgba(245,124,0,0.2);
        }
        .stat-box i {
            font-size: 2rem;
            color: #f57c00;
            margin-bottom: 10px;
        }
        .stat-box h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            color: #f57c00;
        }
        .exam-id-box {
            background: #f8f9fa;
            border-radius: 16px;
            padding: 15px;
            font-family: monospace;
            font-size: 1.2rem;
            letter-spacing: 1px;
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
            <a class="nav-link" href="edit_marks.php"><i class="fas fa-edit"></i> Edit Marks</a>
            <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>
    
    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0"><i class="fas fa-user-edit me-2"></i> Edit Student</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mt-2">
                        <li class="breadcrumb-item"><a href="manage_students.php">Manage Students</a></li>
                        <li class="breadcrumb-item active">Edit Student</li>
                    </ol>
                </nav>
            </div>
            <a href="manage_students.php" class="btn btn-outline-apex">
                <i class="fas fa-arrow-left me-2"></i> Back to List
            </a>
        </div>
        
        <!-- Alerts -->
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Student Info Card -->
        <div class="row">
            <div class="col-md-6">
                <div class="info-card">
                    <h4 class="mb-3"><i class="fas fa-user-graduate me-2" style="color:#f57c00;"></i> Student Information</h4>
                    <form method="POST">
                        <input type="hidden" name="action" value="update">
                        
                        <div class="mb-3">
                            <label class="form-label">Exam ID</label>
                            <div class="exam-id-box d-flex justify-content-between align-items-center">
                                <code style="font-size: 1.1rem;"><?php echo htmlspecialchars($student['exam_id']); ?></code>
                                <a href="?exam_id=<?php echo $examId; ?>&regenerate_id=1" class="btn btn-sm btn-warning" onclick="return confirm('Regenerating Exam ID will update all related records. Continue?')">
                                    <i class="fas fa-sync-alt"></i> Regenerate
                                </a>
                            </div>
                            <small class="text-muted">Exam ID is unique for each student</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Student Name *</label>
                            <input type="text" name="student_name" class="form-control" style="border-radius: 60px; padding: 12px 20px;" 
                                   value="<?php echo htmlspecialchars($student['student_name']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Grade *</label>
                            <select name="grade" class="form-control" style="border-radius: 60px; padding: 12px 20px;" required>
                                <option value="Grade 6" <?php echo $student['grade'] == 'Grade 6' ? 'selected' : ''; ?>>Grade 6</option>
                                <option value="Grade 7" <?php echo $student['grade'] == 'Grade 7' ? 'selected' : ''; ?>>Grade 7</option>
                                <option value="Grade 8" <?php echo $student['grade'] == 'Grade 8' ? 'selected' : ''; ?>>Grade 8</option>
                                <option value="Grade 9" <?php echo $student['grade'] == 'Grade 9' ? 'selected' : ''; ?>>Grade 9</option>
                                <option value="Grade 10" <?php echo $student['grade'] == 'Grade 10' ? 'selected' : ''; ?>>Grade 10</option>
                                <option value="Grade 11" <?php echo $student['grade'] == 'Grade 11' ? 'selected' : ''; ?>>Grade 11</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Medium *</label>
                            <select name="medium" class="form-control" style="border-radius: 60px; padding: 12px 20px;" required>
                                <option value="Sinhala" <?php echo $student['medium'] == 'Sinhala' ? 'selected' : ''; ?>>Sinhala Medium</option>
                                <option value="English" <?php echo $student['medium'] == 'English' ? 'selected' : ''; ?>>English Medium</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Registered On</label>
                            <input type="text" class="form-control" style="border-radius: 60px;" 
                                   value="<?php echo date('d M Y, h:i A', strtotime($student['created_at'])); ?>" disabled>
                        </div>
                        
                        <button type="submit" class="btn btn-apex w-100">
                            <i class="fas fa-save me-2"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="col-md-6">
                <!-- Performance Summary -->
                <div class="info-card">
                    <h4 class="mb-3"><i class="fas fa-chart-line me-2" style="color:#f57c00;"></i> Performance Summary</h4>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stat-box">
                                <i class="fas fa-calendar-week"></i>
                                <h3><?php echo $summary['total_weeks'] ?? 0; ?></h3>
                                <small class="text-muted">Weeks Completed</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                <i class="fas fa-chart-simple"></i>
                                <h3><?php echo $summary['avg_score'] ? round($summary['avg_score'], 1) : 0; ?></h3>
                                <small class="text-muted">Average Score</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                <i class="fas fa-trophy"></i>
                                <h3><?php echo $summary['highest_score'] ?? 0; ?></h3>
                                <small class="text-muted">Highest Score</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                <i class="fas fa-arrow-trend-down"></i>
                                <h3><?php echo $summary['lowest_score'] ?? 0; ?></h3>
                                <small class="text-muted">Lowest Score</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="stat-box">
                                <i class="fas fa-check-circle"></i>
                                <h3><?php echo $summary['attended_weeks'] ?? 0; ?> / <?php echo $summary['total_weeks'] ?? 0; ?></h3>
                                <small class="text-muted">Attendance Rate</small>
                                <div class="progress mt-2" style="height: 8px;">
                                    <?php $attendanceRate = ($summary['total_weeks'] > 0) ? (($summary['attended_weeks'] / $summary['total_weeks']) * 100) : 0; ?>
                                    <div class="progress-bar" style="width: <?php echo $attendanceRate; ?>%; background: linear-gradient(90deg, #f57c00, #ffb347);"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="info-card">
                    <h4 class="mb-3"><i class="fas fa-bolt me-2" style="color:#f57c00;"></i> Quick Actions</h4>
                    <div class="d-grid gap-2">
                        <a href="edit_marks.php?exam_id=<?php echo $examId; ?>" class="btn btn-outline-apex">
                            <i class="fas fa-edit me-2"></i> Edit Student's Marks
                        </a>
                        <button class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash me-2"></i> Delete Student (All Records)
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Weeks Table -->
        <?php if (!empty($weeks)): ?>
        <div class="info-card mt-3">
            <h4 class="mb-3"><i class="fas fa-history me-2" style="color:#f57c00;"></i> Recent Weeks Performance</h4>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Week</th>
                            <th>Date</th>
                            <th>Attendance</th>
                            <th>Mission 20</th>
                            <th>Homework</th>
                            <th>Total Score</th>
                            <th>Rank</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($weeks, -5) as $week): ?>
                        <tr>
                            <td><strong>Week <?php echo $week['week_number']; ?></strong></td>
                            <td><?php echo date('d M Y', strtotime($week['week_date'])); ?></td>
                            <td>
                                <?php if ($week['attendance'] == 'Attended'): ?>
                                    <span class="badge bg-success">Present</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Absent</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $week['mission_20'] ?: '-'; ?></td>
                            <td><?php echo $week['homework'] ?: '-'; ?></td>
                            <td><strong><?php echo $week['total_score'] ?: '-'; ?></strong></td>
                            <td><?php echo $week['rank_position'] ?: '-'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (count($weeks) > 5): ?>
            <div class="text-center mt-3">
                <a href="edit_marks.php?exam_id=<?php echo $examId; ?>" class="btn btn-sm btn-link">
                    View all <?php echo count($weeks); ?> weeks <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Delete Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong><?php echo htmlspecialchars($student['student_name']); ?></strong>?</p>
                <p class="text-danger mb-0">This action cannot be undone. All marks and records for this student will be permanently deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="manage_students.php">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="exam_id" value="<?php echo $examId; ?>">
                    <button type="submit" class="btn btn-danger">Delete Permanently</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function confirmDelete() {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
</body>
</html>