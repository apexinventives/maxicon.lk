<?php
// download_result.php - No external dependencies
require_once 'admin/config.php';

$pdo = getDB();
$examId = $_GET['exam_id'] ?? '';

if (empty($examId)) {
    die('Invalid request');
}

// Get student data
$stmt = $pdo->prepare("SELECT * FROM students WHERE exam_id = ?");
$stmt->execute([$examId]);
$student = $stmt->fetch();

if (!$student) {
    die('Student not found');
}

// Get marks
$stmt = $pdo->prepare("SELECT * FROM weekly_marks WHERE exam_id = ? ORDER BY week_number ASC");
$stmt->execute([$examId]);
$marks = $stmt->fetchAll();

// Calculate statistics
$totalWeeks = count($marks);
$attendedWeeks = 0;
$totalScore = 0;
$bestScore = 0;
$bestWeek = '';
$worstScore = 1000;
$worstWeek = '';

foreach ($marks as $mark) {
    if ($mark['attendance'] == 'Attended') {
        $attendedWeeks++;
        $totalScore += $mark['total_score'];
        if ($mark['total_score'] > $bestScore) {
            $bestScore = $mark['total_score'];
            $bestWeek = $mark['week_number'];
        }
        if ($mark['total_score'] < $worstScore && $mark['total_score'] > 0) {
            $worstScore = $mark['total_score'];
            $worstWeek = $mark['week_number'];
        }
    }
}
$averageScore = $attendedWeeks > 0 ? round($totalScore / $attendedWeeks, 2) : 0;
$attendanceRate = $totalWeeks > 0 ? round(($attendedWeeks / $totalWeeks) * 100) : 0;

// Check if PDF download requested
$download = isset($_GET['download']) && $_GET['download'] == 'pdf';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result - <?php echo htmlspecialchars($student['student_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', Arial, sans-serif;
            background: #f5f5f5;
            padding: 40px;
        }
        
        .result-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .header h1 span {
            color: #f57c00;
        }
        
        .header p {
            opacity: 0.8;
        }
        
        .content {
            padding: 40px;
        }
        
        .info-section {
            background: #f8fafc;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .info-item i {
            font-size: 2rem;
            color: #f57c00;
        }
        
        .info-item label {
            font-size: 0.8rem;
            color: #666;
            display: block;
        }
        
        .info-item value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1a1a2e;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #fff3e0, #ffffff);
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            border: 1px solid rgba(245,124,0,0.2);
        }
        
        .stat-card i {
            font-size: 2rem;
            color: #f57c00;
            margin-bottom: 10px;
        }
        
        .stat-card .number {
            font-size: 2rem;
            font-weight: 800;
            color: #f57c00;
        }
        
        .stat-card .label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .result-table th {
            background: #f57c00;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .result-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        .result-table tr:hover {
            background: #f8fafc;
        }
        
        .grade-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .grade-A { background: #4caf50; color: white; }
        .grade-B { background: #2196f3; color: white; }
        .grade-C { background: #ff9800; color: white; }
        .grade-S { background: #f44336; color: white; }
        .grade-absent { background: #9e9e9e; color: white; }
        
        .insights {
            background: #f8fafc;
            border-radius: 20px;
            padding: 25px;
            margin-top: 20px;
        }
        
        .insights h3 {
            margin-bottom: 20px;
            color: #1a1a2e;
        }
        
        .insight-item {
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .insight-success {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
        }
        
        .insight-warning {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
        }
        
        .insight-danger {
            background: #ffebee;
            border-left: 4px solid #f44336;
        }
        
        .footer {
            background: #1a1a2e;
            color: white;
            text-align: center;
            padding: 30px;
            font-size: 0.8rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .btn {
            padding: 12px 30px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #f57c00, #ff9f4a);
            color: white;
            border: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(245,124,0,0.3);
        }
        
        .btn-secondary {
            background: #1a1a2e;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #2d2d44;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .action-buttons {
                display: none;
            }
            .result-container {
                box-shadow: none;
                border-radius: 0;
            }
            .stat-card {
                break-inside: avoid;
            }
            .result-table {
                break-inside: avoid;
            }
        }
        
        @media (max-width: 768px) {
            body { padding: 20px; }
            .content { padding: 20px; }
            .header { padding: 30px; }
            .header h1 { font-size: 1.8rem; }
        }
        
        function getGrade($score, $attendance) {
            if ($attendance == 'Absent') return '<span class="grade-badge grade-absent">Absent</span>';
            $percentage = ($score / 250) * 100;
            if ($percentage >= 75) return '<span class="grade-badge grade-A">A</span>';
            if ($percentage >= 65) return '<span class="grade-badge grade-B">B</span>';
            if ($percentage >= 45) return '<span class="grade-badge grade-C">C</span>';
            if ($percentage >= 35) return '<span class="grade-badge grade-S">S</span>';
            return '<span class="grade-badge" style="background:#eee; color:#666;">-</span>';
        }
    </style>
</head>
<body>

<div class="result-container">
    <div class="header">
        <h1>Maxicon <span>Institute</span></h1>
        <p>Official Examination Result Report</p>
    </div>
    
    <div class="content">
        <!-- Action Buttons (Hidden when printing) -->
        <div class="action-buttons">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print / Save as PDF
            </button>
            <a href="student_results.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Results
            </a>
        </div>
        
        <!-- Student Information -->
        <div class="info-section">
            <div class="info-item">
                <i class="fas fa-id-card"></i>
                <div>
                    <label>Exam ID</label>
                    <value><?php echo htmlspecialchars($examId); ?></value>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-user-graduate"></i>
                <div>
                    <label>Student Name</label>
                    <value><?php echo htmlspecialchars($student['student_name']); ?></value>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-school"></i>
                <div>
                    <label>Class</label>
                    <value><?php echo htmlspecialchars($student['grade']); ?> - <?php echo htmlspecialchars($student['medium']); ?> Medium</value>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-calendar-alt"></i>
                <div>
                    <label>Report Date</label>
                    <value><?php echo date('d M Y, h:i A'); ?></value>
                </div>
            </div>
        </div>
        
        <!-- Performance Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-calendar-week"></i>
                <div class="number"><?php echo $totalWeeks; ?></div>
                <div class="label">Total Weeks</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle"></i>
                <div class="number"><?php echo $attendedWeeks; ?></div>
                <div class="label">Weeks Attended</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-chart-line"></i>
                <div class="number"><?php echo $averageScore; ?></div>
                <div class="label">Average Score</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-percent"></i>
                <div class="number"><?php echo $attendanceRate; ?>%</div>
                <div class="label">Attendance Rate</div>
            </div>
        </div>
        
        <!-- Marks Table -->
        <table class="result-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Week</th>
                    <th>Date</th>
                    <th>Attendance</th>
                    <th>Mission 20</th>
                    <th>Homework</th>
                    <th>Total Score</th>
                    <th>Rank</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 0;
                foreach ($marks as $mark):
                    $count++;
                    $totalScore = $mark['total_score'] ?: 0;
                    $grade = getGrade($totalScore, $mark['attendance']);
                ?>
                <tr>
                    <td><?php echo $count; ?></td>
                    <td><strong>Week <?php echo $mark['week_number']; ?></strong></td>
                    <td><?php echo date('d M Y', strtotime($mark['week_date'])); ?></td>
                    <td>
                        <?php if ($mark['attendance'] == 'Attended'): ?>
                            <span style="color: #4caf50;"><i class="fas fa-check-circle"></i> Attended</span>
                        <?php else: ?>
                            <span style="color: #f44336;"><i class="fas fa-times-circle"></i> Absent</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $mark['mission_20'] ?: '-'; ?></td>
                    <td><?php echo $mark['homework'] ?: '-'; ?></td>
                    <td><strong><?php echo $mark['total_score'] ?: '-'; ?></strong></td>
                    <td><?php echo $mark['rank_position'] ?: 'N/A'; ?></td>
                    <td><?php echo $grade; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Performance Insights -->
        <div class="insights">
            <h3><i class="fas fa-lightbulb" style="color:#f57c00;"></i> Performance Insights</h3>
            
            <?php if ($attendedWeeks == 0): ?>
                <div class="insight-item insight-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>No Attendance Recorded</strong><br>
                        You have not attended any sessions yet. Please contact the institute for your class schedule.
                    </div>
                </div>
            <?php elseif ($averageScore >= 200): ?>
                <div class="insight-item insight-success">
                    <i class="fas fa-trophy"></i>
                    <div>
                        <strong>Outstanding Performance!</strong><br>
                        Your average score is excellent. Keep up the great work! You are among the top performers.
                    </div>
                </div>
            <?php elseif ($averageScore >= 150): ?>
                <div class="insight-item insight-success">
                    <i class="fas fa-smile"></i>
                    <div>
                        <strong>Good Performance!</strong><br>
                        You are doing well. Focus on improving your weaker areas to reach excellence.
                    </div>
                </div>
            <?php elseif ($averageScore >= 100): ?>
                <div class="insight-item insight-warning">
                    <i class="fas fa-chart-line"></i>
                    <div>
                        <strong>Satisfactory Performance</strong><br>
                        You have a good foundation. Consistent practice will help you improve further.
                    </div>
                </div>
            <?php else: ?>
                <div class="insight-item insight-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <strong>Needs Improvement</strong><br>
                        We recommend additional practice and attending all sessions regularly. Please contact your instructor for guidance.
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($bestWeek): ?>
                <div class="insight-item insight-warning">
                    <i class="fas fa-star"></i>
                    <div>
                        <strong>Best Performance: Week <?php echo $bestWeek; ?></strong><br>
                        Your highest score of <?php echo $bestScore; ?> points. Keep up the momentum!
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($worstWeek && $worstScore < 100): ?>
                <div class="insight-item insight-warning">
                    <i class="fas fa-arrow-trend-down"></i>
                    <div>
                        <strong>Area for Improvement: Week <?php echo $worstWeek; ?></strong><br>
                        Your score of <?php echo $worstScore; ?> points suggests need for extra attention. Consider reviewing the material.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="footer">
        <p>This is a computer-generated document. No signature required.</p>
        <p>&copy; <?php echo date('Y'); ?> Maxicon Institute - All Rights Reserved</p>
        <p>145/2/2 Kandy Rd, Kiribathgoda 11600 | +94 75 909 8096 | info@maxicon.lk</p>
    </div>
</div>

<script>
    // Auto-show print dialog if download parameter is present
    <?php if ($download): ?>
    window.onload = function() {
        window.print();
    }
    <?php endif; ?>
</script>

</body>
</html>

<?php
// Helper function for grade calculation
function getGrade($score, $attendance) {
    if ($attendance == 'Absent') return '<span class="grade-badge grade-absent">Absent</span>';
    $percentage = ($score / 250) * 100;
    if ($percentage >= 75) return '<span class="grade-badge grade-A">A</span>';
    if ($percentage >= 65) return '<span class="grade-badge grade-B">B</span>';
    if ($percentage >= 45) return '<span class="grade-badge grade-C">C</span>';
    if ($percentage >= 35) return '<span class="grade-badge grade-S">S</span>';
    return '<span class="grade-badge" style="background:#eee; color:#666;">-</span>';
}
?>