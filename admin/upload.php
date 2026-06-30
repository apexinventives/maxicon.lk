<?php
require_once 'config.php';
requireAdminLogin();

$pdo = getDB();
$success = '';
$error = '';

// Delete week and all associated records
if (isset($_GET['delete_week']) && isset($_GET['week_number']) && isset($_GET['week_date'])) {
    $weekNumber = intval($_GET['week_number']);
    $weekDate = $_GET['week_date'];
    $grade = $_GET['grade'] ?? '';
    $medium = $_GET['medium'] ?? '';
    
    if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("DELETE FROM weekly_marks WHERE week_number = ? AND week_date = ?");
            $stmt->execute([$weekNumber, $weekDate]);
            $marksDeleted = $stmt->rowCount();
            
            $stmt = $pdo->prepare("DELETE FROM weeks_metadata WHERE week_number = ? AND week_date = ?");
            $stmt->execute([$weekNumber, $weekDate]);
            
            $pdo->commit();
            $success = "Week {$weekNumber} ({$weekDate}) deleted successfully! {$marksDeleted} mark records removed.";
            
            $csvFile = UPLOAD_DIR . "week_{$weekNumber}_{$grade}_{$medium}_*.csv";
            array_map('unlink', glob($csvFile));
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to delete: " . $e->getMessage();
        }
    } else {
        $error = "confirm_delete";
        $confirmWeek = ['week_number' => $weekNumber, 'week_date' => $weekDate, 'grade' => $grade, 'medium' => $medium];
    }
}

// CSV Template Generator - Simplified
if (isset($_GET['download_template'])) {
    $grade = $_GET['grade'] ?? 'Grade 11';
    $medium = $_GET['medium'] ?? 'Sinhala';
    $weekNumber = $_GET['week_number'] ?? '1';
    
    $isLowerGrade = in_array($grade, ['Grade 6', 'Grade 7', 'Grade 8', 'Grade 9']);
    $columnName = $isLowerGrade ? 'Speed Test' : 'Mission 20';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="maxicon_template_week_' . $weekNumber . '_' . $grade . '_' . $medium . '.csv"');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    $headers = ['No', 'Student Name', 'Attendance', $columnName, 'Home-Work', 'Total', 'Rank'];
    fputcsv($output, $headers);
    
    $sampleData = [
        [1, 'Hesandu Nethmadu', 'Attended', '95', '56', '251', '1'],
        [2, 'Sathini Sahansa', 'Attended', '15', '39', '154', '15'],
        [3, 'Shenal Demiyan', 'Attended', '50', '30', '180', '10'],
        [4, 'Movindu Lithmika', 'Absent', '', '', '', ''],
        [5, 'Aathma Iduwara', 'Attended', '10', '30', '140', '20'],
        [6, 'Warusha Sansala', 'Attended', '55', '', '155', '14'],
        [7, 'Vidumini Samindara', 'Attended', '100', '74', '274', '1'],
        [8, 'Punhara Thenu', 'Absent', '', '', '', ''],
        [9, 'Kalana Geeth Anuhas', 'Attended', '', '', '100', '30'],
        [10, 'Kushal Kaveeja', 'Attended', '95', '55', '250', '2'],
    ];
    
    foreach ($sampleData as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grade = $_POST['grade'] ?? '';
    $medium = $_POST['medium'] ?? '';
    $weekNumber = intval($_POST['week_number'] ?? 0);
    $weekDate = $_POST['week_date'] ?? '';
    
    if (!$grade || !$medium || !$weekNumber || !$weekDate) {
        $error = 'Please fill all fields';
    } elseif (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please upload a valid CSV file';
    } else {
        $file = $_FILES['csv_file'];
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM weeks_metadata WHERE week_number = ? AND grade = ? AND medium = ?");
        $stmt->execute([$weekNumber, $grade, $medium]);
        $weekExists = $stmt->fetchColumn();
        
        if ($weekExists > 0) {
            $error = "Week {$weekNumber} for {$grade} {$medium} already exists! Please delete the existing week first.";
        } else {
            $handle = fopen($file['tmp_name'], 'r');
            $data = [];
            $foundHeader = false;
            
            $isLowerGrade = in_array($grade, ['Grade 6', 'Grade 7', 'Grade 8', 'Grade 9']);
            $columnName = $isLowerGrade ? 'Speed Test' : 'Mission 20';
            
            while (($row = fgetcsv($handle)) !== false) {
                if (empty(array_filter($row))) continue;
                if (strpos(trim($row[0] ?? ''), '#') === 0) continue;
                
                if (!$foundHeader) {
                    $rowString = implode(' ', $row);
                    if (stripos($rowString, 'student name') !== false || 
                        stripos($rowString, 'attendance') !== false ||
                        stripos($rowString, $columnName) !== false ||
                        stripos($rowString, 'homework') !== false) {
                        $foundHeader = true;
                        continue;
                    }
                } else {
                    if (!empty($row[1]) && $row[1] !== 'Student Name' && trim($row[1]) != '') {
                        $data[] = $row;
                    }
                }
            }
            fclose($handle);
            
            if (empty($data)) {
                $error = 'No valid student data found in CSV. Please use the template format.';
            } else {
                $inserted = 0;
                $updated = 0;
                
                foreach ($data as $row) {
                    $name = trim($row[1] ?? '');
                    $attendance = trim($row[2] ?? '');
                    $mission20 = floatval($row[3] ?? 0);
                    $homework = floatval($row[4] ?? 0);
                    $total = floatval($row[5] ?? 0);
                    $rank = isset($row[6]) ? intval($row[6]) : null;
                    
                    if (empty($name)) continue;
                    
                    if ($total == 0 && $attendance == 'Attended') {
                        $total = 100 + $mission20 + $homework;
                    }
                    
                    $stmt = $pdo->prepare("SELECT exam_id FROM students WHERE student_name = ? AND grade = ? AND medium = ?");
                    $stmt->execute([$name, $grade, $medium]);
                    $existing = $stmt->fetch();
                    
                    if ($existing) {
                        $examId = $existing['exam_id'];
                    } else {
                        $examId = generateExamID($name, $grade);
                        $stmt = $pdo->prepare("INSERT INTO students (exam_id, student_name, grade, medium) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$examId, $name, $grade, $medium]);
                    }
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO weekly_marks (exam_id, week_date, week_number, attendance, mission_20, homework, total_score, rank_position)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    
                    $stmt->execute([
                        $examId,
                        $weekDate,
                        $weekNumber,
                        $attendance === 'Attended' ? 'Attended' : 'Absent',
                        $mission20,
                        $homework,
                        $total,
                        $rank
                    ]);
                    
                    if ($stmt->rowCount() > 0) $inserted++;
                    else $updated++;
                }
                
                $stmt = $pdo->prepare("
                    UPDATE weekly_marks w1
                    JOIN (
                        SELECT exam_id, total_score,
                            @rank := @rank + 1 as new_rank
                        FROM weekly_marks, (SELECT @rank := 0) r
                        WHERE week_number = ? AND week_date = ?
                        ORDER BY total_score DESC, mission_20 DESC
                    ) w2 ON w1.exam_id = w2.exam_id
                    SET w1.rank_position = w2.new_rank
                    WHERE w1.week_number = ? AND w1.week_date = ?
                ");
                $stmt->execute([$weekNumber, $weekDate, $weekNumber, $weekDate]);
                
                $stmt = $pdo->prepare("
                    INSERT INTO weeks_metadata (week_number, week_date, grade, medium)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$weekNumber, $weekDate, $grade, $medium]);
                
                $success = "Uploaded successfully! {$inserted} records added. Ranks have been auto-calculated.";
                
                $newFileName = "week_{$weekNumber}_{$grade}_{$medium}_" . date('Ymd_His') . ".csv";
                move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $newFileName);
            }
        }
    }
}

$weeks = $pdo->query("SELECT * FROM weeks_metadata ORDER BY week_date DESC")->fetchAll();

foreach ($weeks as &$week) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as student_count, AVG(total_score) as avg_score FROM weekly_marks WHERE week_number = ? AND week_date = ?");
    $stmt->execute([$week['week_number'], $week['week_date']]);
    $stats = $stmt->fetch();
    $week['student_count'] = $stats['student_count'];
    $week['avg_score'] = round($stats['avg_score'], 1);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Exam - Maxicon Admin</title>
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
        .btn-danger-apex {
            background: #dc3545;
            border: none;
            border-radius: 40px;
            padding: 5px 15px;
            color: white;
            font-size: 0.8rem;
            transition: all 0.3s;
        }
        .btn-danger-apex:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        .upload-area {
            border: 2px dashed #ddd;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            background: #fafafa;
            transition: all 0.3s;
        }
        .upload-area:hover {
            border-color: #f57c00;
            background: #fff8f0;
        }
        .template-card {
            background: linear-gradient(135deg, #fff8f0, #ffffff);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(245,124,0,0.2);
        }
        .delete-confirm {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
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
            <a class="nav-link active" href="upload.php"><i class="fas fa-upload"></i> Upload Exam</a>
            <a class="nav-link" href="manage_students.php"><i class="fas fa-users"></i> Manage Students</a>
            <a class="nav-link" href="edit_marks.php"><i class="fas fa-edit"></i> Edit Marks</a>
            <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>
    
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="fas fa-upload me-2"></i> Upload Weekly Exam</h2>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error && $error !== 'confirm_delete'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error === 'confirm_delete' && isset($confirmWeek)): ?>
            <div class="delete-confirm">
                <h5><i class="fas fa-exclamation-triangle me-2" style="color:#ffc107;"></i> Confirm Deletion</h5>
                <p>Are you sure you want to delete <strong>Week <?php echo $confirmWeek['week_number']; ?></strong> (<?php echo $confirmWeek['week_date']; ?>)?</p>
                <p class="text-danger mb-3">This will permanently delete all marks for this week. This action cannot be undone!</p>
                <a href="?delete_week=1&week_number=<?php echo $confirmWeek['week_number']; ?>&week_date=<?php echo urlencode($confirmWeek['week_date']); ?>&grade=<?php echo urlencode($confirmWeek['grade']); ?>&medium=<?php echo urlencode($confirmWeek['medium']); ?>&confirm=yes" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash me-2"></i> Yes, Delete Week
                </a>
                <a href="upload.php" class="btn btn-secondary btn-sm ms-2">
                    <i class="fas fa-times me-2"></i> Cancel
                </a>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-5">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h4 class="mb-3"><i class="fas fa-cloud-upload-alt me-2" style="color:#f57c00;"></i> Upload New Week</h4>
                    
                    <div class="template-card mb-4">
                        <h5><i class="fas fa-download me-2" style="color:#f57c00;"></i> Download Template</h5>
                        <p class="text-muted small">Download a clean CSV template with sample data.</p>
                        <div class="row g-2">
                            <div class="col-md-5">
                                <select id="template_grade" class="form-control form-control-sm" style="border-radius: 60px;" onchange="updateTemplateColumnHint()">
                                    <option value="Grade 6">Grade 6 (Speed Test)</option>
                                    <option value="Grade 7">Grade 7 (Speed Test)</option>
                                    <option value="Grade 8">Grade 8 (Speed Test)</option>
                                    <option value="Grade 9">Grade 9 (Speed Test)</option>
                                    <option value="Grade 10">Grade 10 (Mission 20)</option>
                                    <option value="Grade 11" selected>Grade 11 (Mission 20)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select id="template_medium" class="form-control form-control-sm" style="border-radius: 60px;">
                                    <option value="Sinhala" selected>Sinhala</option>
                                    <option value="English">English</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" id="template_week" class="form-control form-control-sm" value="1" style="border-radius: 60px;" placeholder="Week">
                            </div>
                            <div class="col-md-12 mt-2">
                                <button onclick="downloadTemplate()" class="btn btn-sm btn-apex w-100">
                                    <i class="fas fa-download me-2"></i> Download CSV Template
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Grade *</label>
                            <select name="grade" class="form-control" style="border-radius: 60px; padding: 12px 20px;" required onchange="updateGradeHint(this.value)">
                                <option value="">Select Grade</option>
                                <option value="Grade 6">Grade 6</option>
                                <option value="Grade 7">Grade 7</option>
                                <option value="Grade 8">Grade 8</option>
                                <option value="Grade 9">Grade 9</option>
                                <option value="Grade 10">Grade 10</option>
                                <option value="Grade 11">Grade 11</option>
                            </select>
                            <small id="gradeHint" class="text-muted">Select grade to see the required column format</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Medium *</label>
                            <select name="medium" class="form-control" style="border-radius: 60px; padding: 12px 20px;" required>
                                <option value="Sinhala">Sinhala Medium</option>
                                <option value="English">English Medium</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Week Number *</label>
                            <input type="number" name="week_number" class="form-control" style="border-radius: 60px; padding: 12px 20px;" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Exam Date *</label>
                            <input type="date" name="week_date" class="form-control" style="border-radius: 60px; padding: 12px 20px;" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">CSV File *</label>
                            <div class="upload-area">
                                <i class="fas fa-file-csv fa-3x mb-2" style="color:#f57c00;"></i>
                                <p>Drag & drop or click to upload</p>
                                <input type="file" name="csv_file" accept=".csv" class="form-control" required style="border-radius: 60px;">
                                <small class="text-muted" id="csvFormatHint">
                                    <i class="fas fa-info-circle"></i> 
                                    Format: No, Student Name, Attendance, Column Name, Homework, Total, Rank
                                </small>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-apex w-100">
                            <i class="fas fa-cloud-upload-alt me-2"></i> Upload & Process
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="col-md-7">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h4 class="mb-3"><i class="fas fa-history me-2" style="color:#f57c00;"></i> Uploaded Weeks</h4>
                    
                    <?php if (empty($weeks)): ?>
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-calendar-week fa-3x mb-3 opacity-50"></i>
                            <p>No weeks uploaded yet.<br>Use the form to upload your first exam week.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Week</th>
                                        <th>Date</th>
                                        <th>Grade</th>
                                        <th>Medium</th>
                                        <th>Students</th>
                                        <th>Avg Score</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($weeks as $week): ?>
                                    <tr>
                                        <td><strong>Week <?php echo $week['week_number']; ?></strong></td>
                                        <td><?php echo date('d M Y', strtotime($week['week_date'])); ?></td>
                                        <td><?php echo $week['grade']; ?></td>
                                        <td><?php echo $week['medium']; ?></td>
                                        <td><?php echo $week['student_count']; ?> students</td>
                                        <td><?php echo $week['avg_score'] ?: '-'; ?></td>
                                        <td>
                                            <button onclick="confirmDelete(<?php echo $week['week_number']; ?>, '<?php echo $week['week_date']; ?>', '<?php echo $week['grade']; ?>', '<?php echo $week['medium']; ?>')" 
                                                    class="btn btn-danger-apex">
                                                <i class="fas fa-trash me-1"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> Deleting a week will remove all marks for that week. Student profiles will remain intact.
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="card border-0 shadow-sm rounded-4 p-4 mt-4">
                    <h4 class="mb-3"><i class="fas fa-info-circle me-2" style="color:#f57c00;"></i> CSV Format Guide</h4>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr><th>Column</th><th>Grades 6-9</th><th>Grades 10-11</th><th>Example</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>No</td><td>Optional</td><td>Optional</td><td>1</td></tr>
                                <tr><td>Student Name</td><td>Required</td><td>Required</td><td>Hesandu Nethmadu</td></tr>
                                <tr><td>Attendance</td><td>"Attended" or "Absent"</td><td>"Attended" or "Absent"</td><td>Attended</td></tr>
                                <tr><td><span id="guideColumnName">Mission 20</span></td><td><strong>Speed Test</strong></td><td><strong>Mission 20</strong></td><td>95</td></tr>
                                <tr><td>Home-Work</td><td>Score (0-100)</td><td>Score (0-100)</td><td>56</td></tr>
                                <tr><td>Total</td><td>Leave empty or provide</td><td>Leave empty or provide</td><td></td></tr>
                                <tr><td>Rank</td><td>Leave empty or provide</td><td>Leave empty or provide</td><td></td></tr>
                            </tbody>
                        </table>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Grades 6-9:</strong> Use "Speed Test" column | 
                            <strong>Grades 10-11:</strong> Use "Mission 20" column
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Delete Week</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteWeekName"></strong>?</p>
                <p class="text-danger mb-0">This will permanently delete all marks for this week. This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Yes, Delete Week</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function updateTemplateColumnHint() {
        var grade = document.getElementById('template_grade').value;
        var hint = document.getElementById('templateHint');
        var guideColumn = document.getElementById('guideColumnName');
        var isLowerGrade = ['Grade 6', 'Grade 7', 'Grade 8', 'Grade 9'].includes(grade);
        var columnName = isLowerGrade ? 'Speed Test' : 'Mission 20';
        hint.innerHTML = '<i class="fas fa-info-circle"></i> Template for ' + grade + ': Column "' + columnName + '"';
        guideColumn.textContent = columnName;
    }
    
    function updateGradeHint(grade) {
        var hint = document.getElementById('gradeHint');
        var guideColumn = document.getElementById('guideColumnName');
        var csvHint = document.getElementById('csvFormatHint');
        var isLowerGrade = ['Grade 6', 'Grade 7', 'Grade 8', 'Grade 9'].includes(grade);
        var columnName = isLowerGrade ? 'Speed Test' : 'Mission 20';
        
        if (grade) {
            hint.innerHTML = '<i class="fas fa-check-circle" style="color:#4caf50;"></i> For ' + grade + ', use column: <strong>"' + columnName + '"</strong>';
            guideColumn.textContent = columnName;
            csvHint.innerHTML = '<i class="fas fa-info-circle"></i> Format: No, Student Name, Attendance, <strong>' + columnName + '</strong>, Homework, Total, Rank';
        } else {
            hint.innerHTML = 'Select grade to see the required column format';
        }
    }
    
    function downloadTemplate() {
        var grade = document.getElementById('template_grade').value;
        var medium = document.getElementById('template_medium').value;
        var week = document.getElementById('template_week').value;
        
        window.location.href = 'upload.php?download_template=1&grade=' + encodeURIComponent(grade) + '&medium=' + encodeURIComponent(medium) + '&week_number=' + week;
    }
    
    function confirmDelete(weekNumber, weekDate, grade, medium) {
        document.getElementById('deleteWeekName').innerHTML = 'Week ' + weekNumber + ' (' + weekDate + ') - ' + grade + ' ' + medium;
        document.getElementById('confirmDeleteBtn').href = '?delete_week=1&week_number=' + weekNumber + '&week_date=' + encodeURIComponent(weekDate) + '&grade=' + encodeURIComponent(grade) + '&medium=' + encodeURIComponent(medium) + '&confirm=yes';
        
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
    
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    document.addEventListener('DOMContentLoaded', function() {
        updateTemplateColumnHint();
        var gradeSelect = document.querySelector('select[name="grade"]');
        if (gradeSelect.value) {
            updateGradeHint(gradeSelect.value);
        }
    });
</script>
</body>
</html>