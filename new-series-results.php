<?php
// student_results.php
require_once 'admin/config.php';

$object = getDB(); // Using the PDO connection from config

$studentData = null;
$marks = [];
$error = '';
$download_button = '';

// Get all exams/exam types for dropdown
$examsList = [];
try {
    $stmt = $object->prepare("SELECT DISTINCT week_number, week_date FROM weeks_metadata ORDER BY week_number DESC");
    $stmt->execute();
    $examsList = $stmt->fetchAll();
} catch (PDOException $e) {
    // No weeks table yet
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0, viewport-fit=cover" name="viewport">
    <title>View Results - Maxicon Institute</title>
    <meta content="Maxicon Institute - Exam Results Portal" name="description">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome & Bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #fef9f0 100%);
            min-height: 100vh;
        }
        
        .main-card {
            background: white;
            border-radius: 32px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            margin: 30px auto;
            overflow: hidden;
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%);
            padding: 25px 30px;
            color: white;
        }
        
        .card-header-custom h3 {
            font-weight: 700;
            margin: 0;
        }
        
        .btn-back {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 40px;
            padding: 8px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            background: rgba(255,255,255,0.3);
            color: white;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #f57c00, #ff9f4a);
            border: none;
            border-radius: 40px;
            padding: 12px 30px;
            font-weight: 600;
            color: white;
            width: 100%;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(245,124,0,0.3);
        }
        
        .result-table {
            width: 100%;
            border-radius: 20px;
            overflow: hidden;
        }
        
        .result-table th {
            background: #fff3e0;
            color: #f57c00;
            font-weight: 600;
            padding: 15px;
        }
        
        .result-table td {
            padding: 12px 15px;
            vertical-align: middle;
        }
        
        .badge-grade {
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .grade-A { background: #4caf50; color: white; }
        .grade-B { background: #2196f3; color: white; }
        .grade-C { background: #ff9800; color: white; }
        .grade-S { background: #f44336; color: white; }
        .grade-absent { background: #9e9e9e; color: white; }
        
        .analysis-card {
            background: linear-gradient(135deg, #ffffff, #fff8f0);
            border-radius: 24px;
            padding: 25px;
            margin-top: 25px;
            border: 1px solid rgba(245,124,0,0.15);
        }
        
        .stat-box {
            background: white;
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .stat-box:hover {
            transform: translateY(-5px);
        }
        
        .stat-box i {
            font-size: 2rem;
            color: #f57c00;
            margin-bottom: 10px;
        }
        
        .stat-box h2 {
            font-size: 2rem;
            font-weight: 800;
            margin: 0;
            color: #f57c00;
        }
        
        .stat-box p {
            margin: 0;
            color: #666;
        }
        
        .insight-text {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 15px 20px;
            border-radius: 16px;
            margin-top: 20px;
        }
        
        .insight-warning {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
        }
        
        .insight-danger {
            background: #ffebee;
            border-left: 4px solid #f44336;
        }
        
        .download-btn {
            background: linear-gradient(135deg, #dc3545, #e74c3c);
            border: none;
            border-radius: 40px;
            padding: 12px 30px;
            font-weight: 600;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(220,53,69,0.3);
            color: white;
        }
        
        @media (max-width: 768px) {
            .main-card { margin: 15px; }
            .card-header-custom { padding: 20px; }
            .stat-box h2 { font-size: 1.5rem; }
        }

        /* Shared navbar/footer styles */
        :root {
            --apex-orange: #f57c00;
            --apex-orange-light: #fff3e0;
            --apex-orange-dark: #e65100;
            --apex-text-dark: #1a1a2e;
        }
        body {
            padding-top: 150px;
        }
        .apex-topbar {
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(10px);
            padding: 8px 0;
            font-size: 0.85rem;
            border-bottom: 1px solid rgba(245,124,0,0.15);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1002;
        }
        .apex-topbar a { color: #555; text-decoration: none; }
        .apex-topbar a:hover { color: var(--apex-orange); }
        .apex-navbar {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(12px);
            box-shadow: 0 2px 25px rgba(0,0,0,0.03);
            padding: 0.8rem 0;
            position: fixed;
            top: 38px;
            left: 0;
            right: 0;
            z-index: 1001;
            transition: all 0.3s ease;
        }
        body {
            padding-top: 150px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .page-content {
            flex: 1;
        }
        .apex-navbar.scrolled { box-shadow: 0 4px 30px rgba(0,0,0,0.08); }
        .apex-brand {
            font-weight: 800;
            font-size: 1.75rem;
            color: #1a1a2e;
            text-decoration: none;
        }
        .apex-nav-link {
            font-weight: 550;
            color: #2c2c36 !important;
            margin: 0 0.5rem;
            transition: 0.2s;
            text-decoration: none;
        }
        .apex-nav-link:hover, .apex-nav-link.active { color: var(--apex-orange) !important; }
        .apex-btn-glow {
            background: linear-gradient(135deg, var(--apex-orange), #ff9f4a);
            border: none;
            color: white;
            border-radius: 40px;
            padding: 10px 22px;
            font-weight: 600;
            text-decoration: none;
        }
        .apex-btn-glow:hover { transform: translateY(-2px); }
        .apex-footer {
            background: linear-gradient(135deg, #0a0a0f 0%, #111118 100%);
            color: #cbcbd4;
            padding: 60px 0 30px;
        }
        .apex-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--apex-orange), transparent);
        }
        .apex-footer h3, .apex-footer h5 { color: white; }
        .apex-footer a { color: #cbcbd4; text-decoration: none; }
        .apex-footer a:hover { color: var(--apex-orange); }
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, var(--apex-orange), #ffb347);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999;
            transition: 0.3s;
            opacity: 0;
            visibility: hidden;
        }
        .back-to-top.active { opacity: 1; visibility: visible; }
        .back-to-top:hover { transform: translateY(-5px); }
    </style>
    
</head>
<body>

<div class="apex-topbar d-none d-md-block">
    <div class="container d-flex justify-content-between">
        <div>
            <i class="fas fa-envelope me-2" style="color: #f57c00;"></i> <a href="mailto:info@maxicon.lk">info@maxicon.lk</a>
            <i class="fas fa-phone-alt ms-3 me-2" style="color: #f57c00;"></i> <span>0777 198 096</span>
        </div>
        <div>
            <a href="#" class="me-2"><i class="fab fa-twitter"></i></a>
            <a href="#" class="me-2"><i class="fab fa-facebook"></i></a>
            <a href="#" class="me-2"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin"></i></a>
        </div>
    </div>
</div>

<nav class="navbar navbar-expand-lg navbar-light apex-navbar">
    <div class="container">
        <a class="navbar-brand apex-brand" href="index.php">Maxicon Institute</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link apex-nav-link" href="index.php#home">Home</a></li>
                <li class="nav-item"><a class="nav-link apex-nav-link" href="index.php#about">About</a></li>
                <li class="nav-item"><a class="nav-link apex-nav-link" href="index.php#services">Services</a></li>
                <li class="nav-item"><a class="nav-link apex-nav-link" href="index.php#portfolio">Gallery</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Exams</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="https://recorrection.maxicon.lk/">Exam Recorrection</a></li>
                        <li><a class="dropdown-item" href="#">Pastpapers (Maths only)</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle active" href="#" data-bs-toggle="dropdown">Results</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="new-series-results.php">2026 New Exam Series</a></li>
                        <li><a class="dropdown-item" href="https://maths2025.maxicon.lk/">Ranking Series 2026</a></li>
                        <li><a class="dropdown-item" href="https://www.rev.maxicon.lk">Revision Series 2024</a></li>
                        <li><a class="dropdown-item" href="https://www.pepare.maxicon.lk">Pepare Series</a></li>
                        <li><a class="dropdown-item" href="https://www.onlp.maxicon.lk">Online Series</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link apex-nav-link" href="index.php#contact">Contact</a></li>
            </ul>
            <div class="d-flex">
                <a href="https://wa.me/94777198096" target="_blank" rel="noopener noreferrer" class="btn apex-btn-glow">
                    <i class="fab fa-whatsapp me-2"></i> WhatsApp Us (0777 198 096)
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="page-content">
    <div class="container">
        <div class="main-card">
        <div class="card-header-custom">
            <div class="row align-items-center">
                <div class="col col-sm-6">
                    <h3><i class="fas fa-chart-line me-2"></i> Exam Results Portal</h3>
                    <p class="mb-0 mt-2 opacity-75">Enter your Exam ID to view your performance</p>
                </div>
                <div class="col col-sm-6 text-end">
                    <a href="index.php" class="btn-back"><i class="fas fa-home me-2"></i> Back to Home</a>
                </div>
            </div>
        </div>
        
        <div class="card-body p-4">
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["submit"])) {
                $examId = trim($_POST["exam_id"]);
                
                try {
                    // Get student info
                    $stmt = $object->prepare("SELECT * FROM students WHERE exam_id = ?");
                    $stmt->execute([$examId]);
                    $studentData = $stmt->fetch();
                    
                    if ($studentData) {
                        // Display student info
                        echo '
                        <div class="mb-4 p-3 bg-light rounded-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-1"><strong><i class="fas fa-id-card me-2" style="color:#f57c00;"></i>Exam ID</strong></p>
                                    <p><code>' . htmlspecialchars($examId) . '</code></p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong><i class="fas fa-user-graduate me-2" style="color:#f57c00;"></i>Student Name</strong></p>
                                    <p>' . htmlspecialchars($studentData['student_name']) . '</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong><i class="fas fa-school me-2" style="color:#f57c00;"></i>Class</strong></p>
                                    <p>' . htmlspecialchars($studentData['grade']) . ' - ' . htmlspecialchars($studentData['medium']) . ' Medium</p>
                                </div>
                            </div>
                        </div>';
                        
                        // Get all weekly marks
                        $stmt = $object->prepare("SELECT * FROM weekly_marks WHERE exam_id = ? ORDER BY week_number ASC");
                        $stmt->execute([$examId]);
                        $marks = $stmt->fetchAll();
                        
                        if (!empty($marks)) {
                            // Display results table
                            echo '
                            <div class="table-responsive">
                                <table class="table result-table table-bordered">
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
                                    <tbody>';
                            
                            $count = 0;
                            $totalScoreSum = 0;
                            $attendedWeeks = 0;
                            $scoresForChart = [];
                            $weeksForChart = [];
                            
                            foreach ($marks as $mark) {
                                $count++;
                                $totalScore = $mark['total_score'] ?: 0;
                                $totalScoreSum += $totalScore;
                                
                                if ($mark['attendance'] == 'Attended') {
                                    $attendedWeeks++;
                                }
                                
                                // Calculate grade based on total score (out of 200+)
                                $percentage = ($totalScore / 250) * 100;
                                if ($mark['attendance'] == 'Absent') {
                                    $grade = '<span class="badge-grade grade-absent">Absent</span>';
                                } elseif ($percentage >= 75) {
                                    $grade = '<span class="badge-grade grade-A">A</span>';
                                } elseif ($percentage >= 65) {
                                    $grade = '<span class="badge-grade grade-B">B</span>';
                                } elseif ($percentage >= 45) {
                                    $grade = '<span class="badge-grade grade-C">C</span>';
                                } elseif ($percentage >= 35) {
                                    $grade = '<span class="badge-grade grade-S">S</span>';
                                } else {
                                    $grade = '<span class="badge-grade" style="background:#eee; color:#666;">-</span>';
                                }
                                
                                $scoresForChart[] = $totalScore;
                                $weeksForChart[] = 'Week ' . $mark['week_number'];
                                
                                echo '
                                <tr>
                                    <td>' . $count . '</td>
                                    <td><strong>Week ' . $mark['week_number'] . '</strong></td>
                                    <td>' . date('d M Y', strtotime($mark['week_date'])) . '</td>
                                    <td>' . ($mark['attendance'] == 'Attended' ? '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Attended</span>' : '<span class="badge bg-danger"><i class="fas fa-times-circle"></i> Absent</span>') . '</td>
                                    <td>' . ($mark['mission_20'] ?: '-') . '</td>
                                    <td>' . ($mark['homework'] ?: '-') . '</td>
                                    <td><strong>' . ($mark['total_score'] ?: '-') . '</strong></td>
                                    <td>' . ($mark['rank_position'] ?: 'N/A') . '</td>
                                    <td>' . $grade . '</td>
                                </tr>';
                            }
                            
                            $averageScore = ($attendedWeeks > 0) ? round($totalScoreSum / $attendedWeeks, 2) : 0;
                            
                            echo '</tbody>
                                </table>
                            </div>';
                            
                            // Set download button
                            $download_button = '<a href="download_result.php?exam_id=' . urlencode($examId) . '" class="download-btn"><i class="fas fa-download me-2"></i> Download Result (PDF)</a>';
                            
                            // ========== ANALYSIS SECTION ==========
                            echo '
                            <!-- ANALYSIS SECTION -->
                            <div class="analysis-card">
                                <h4 class="mb-4"><i class="fas fa-chart-pie me-2" style="color:#f57c00;"></i> Performance Analysis</h4>
                                
                                <div class="row g-4 mb-4">
                                    <div class="col-md-3 col-6">
                                        <div class="stat-box">
                                            <i class="fas fa-calendar-week"></i>
                                            <h2>' . count($marks) . '</h2>
                                            <p>Total Weeks</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="stat-box">
                                            <i class="fas fa-check-circle"></i>
                                            <h2>' . $attendedWeeks . '</h2>
                                            <p>Attended</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="stat-box">
                                            <i class="fas fa-star"></i>
                                            <h2>' . $averageScore . '</h2>
                                            <p>Avg Score</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="stat-box">
                                            <i class="fas fa-chart-line"></i>
                                            <h2>' . round(($attendedWeeks / max(1, count($marks))) * 100) . '%</h2>
                                            <p>Attendance Rate</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Progress Chart -->
                                <div class="mb-4">
                                    <canvas id="progressChart" style="max-height: 300px; width: 100%;"></canvas>
                                </div>
                                
                                <!-- Subject/Score Distribution -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <canvas id="scoreDistribution" style="max-height: 250px;"></canvas>
                                    </div>
                                    <div class="col-md-6">
                                        <canvas id="attendanceChart" style="max-height: 250px;"></canvas>
                                    </div>
                                </div>
                                
                                <!-- AI Insights -->
                                <div class="mt-4">';
                            
                            // Generate insights based on performance
                            $bestWeek = '';
                            $bestScore = 0;
                            $worstWeek = '';
                            $worstScore = 1000;
                            $improvement = 0;
                            
                            foreach ($marks as $mark) {
                                if ($mark['total_score'] > $bestScore && $mark['attendance'] == 'Attended') {
                                    $bestScore = $mark['total_score'];
                                    $bestWeek = $mark['week_number'];
                                }
                                if ($mark['total_score'] < $worstScore && $mark['total_score'] > 0 && $mark['attendance'] == 'Attended') {
                                    $worstScore = $mark['total_score'];
                                    $worstWeek = $mark['week_number'];
                                }
                            }
                            
                            if (count($marks) >= 2) {
                                $firstScore = $marks[0]['total_score'] ?: 0;
                                $lastScore = end($marks)['total_score'] ?: 0;
                                $improvement = $lastScore - $firstScore;
                            }
                            
                            if ($attendedWeeks == 0) {
                                echo '<div class="insight-text insight-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i> 
                                    <strong>No Attendance Recorded:</strong> You have not attended any sessions yet. Please contact the institute for your class schedule.
                                </div>';
                            } elseif ($averageScore >= 200) {
                                echo '<div class="insight-text">
                                    <i class="fas fa-trophy me-2"></i> 
                                    <strong>Outstanding Performance!</strong> Your average score is excellent. Keep up the great work! You are among the top performers.
                                </div>';
                            } elseif ($averageScore >= 150) {
                                echo '<div class="insight-text">
                                    <i class="fas fa-smile me-2"></i> 
                                    <strong>Good Performance!</strong> You are doing well. Focus on improving your weaker areas to reach excellence.
                                </div>';
                            } elseif ($averageScore >= 100) {
                                echo '<div class="insight-text insight-warning">
                                    <i class="fas fa-chart-line me-2"></i> 
                                    <strong>Satisfactory Performance.</strong> You have a good foundation. Consistent practice will help you improve further.
                                </div>';
                            } else {
                                echo '<div class="insight-text insight-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i> 
                                    <strong>Needs Improvement.</strong> We recommend additional practice and attending all sessions regularly. Please contact your instructor for guidance.
                                </div>';
                            }
                            
                            if ($improvement > 30) {
                                echo '<div class="insight-text mt-3" style="background:#e8f5e9;">
                                    <i class="fas fa-arrow-up me-2" style="color:#4caf50;"></i> 
                                    <strong>Great Improvement!</strong> Your score has improved by ' . round($improvement) . ' points from Week ' . $marks[0]['week_number'] . ' to Week ' . end($marks)['week_number'] . '. Keep going!
                                </div>';
                            } elseif ($improvement < -30) {
                                echo '<div class="insight-text insight-danger mt-3">
                                    <i class="fas fa-arrow-down me-2"></i> 
                                    <strong>Performance Decline.</strong> Your score dropped by ' . abs(round($improvement)) . ' points. Please review the topics and attend extra help sessions.
                                </div>';
                            }
                            
                            if ($bestWeek) {
                                echo '<div class="insight-text insight-warning mt-3">
                                    <i class="fas fa-star me-2" style="color:#ff9800;"></i> 
                                    <strong>Best Performance:</strong> Week ' . $bestWeek . ' with ' . $bestScore . ' points. 
                                    <strong>Areas to focus:</strong> ' . ($worstWeek ? 'Week ' . $worstWeek . ' needs more attention.' : 'Keep consistent!') . '
                                </div>';
                            }
                            
                            echo '
                                </div>
                            </div>';
                            
                        } else {
                            echo '<div class="alert alert-warning text-center"><i class="fas fa-info-circle me-2"></i> No marks found for this student yet. Please check back later.</div>';
                        }
                    } else {
                        echo '<div class="alert alert-danger text-center"><i class="fas fa-exclamation-circle me-2"></i> No Result Found. Please check your Exam ID and try again.</div>';
                    }
                } catch (PDOException $e) {
                    echo '<div class="alert alert-danger text-center"><i class="fas fa-database me-2"></i> Database error. Please try again later.</div>';
                }
            } else {
                // Show search form
                ?>
                <div class="text-center mb-4">
                    <i class="fas fa-search fa-3x" style="color:#f57c00; opacity:0.5;"></i>
                    <h4 class="mt-3">Enter Your Exam ID</h4>
                    <p class="text-muted">Your Exam ID is provided by the institute</p>
                </div>
                
                <form method="POST" class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="input-group" style="box-shadow: 0 5px 20px rgba(0,0,0,0.1); border-radius: 60px; overflow: hidden;">
                            <input type="text" name="exam_id" class="form-control" placeholder="Enter Exam ID (e.g., MAX11SAM123)" style="border: none; padding: 15px 25px; font-size: 1rem;" required>
                            <button type="submit" name="submit" class="btn-submit" style="width: auto; padding: 15px 35px;">
                                <i class="fas fa-arrow-right me-2"></i> View Results
                            </button>
                        </div>
                        <div class="text-center mt-3">
                            <small class="text-muted">Don't have an Exam ID? <a  style="color:#f57c00;">Contact your Class Cordinator</a></small>
                        </div>
                    </div>
                </form>
                <?php
            }
            ?>
        </div>
        
        <?php if ($download_button): ?>
        <div class="card-footer text-center p-4" style="background: #f8fafc;">
            <?php echo $download_button; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
</div>

<footer class="apex-footer">
    <div class="container position-relative">
        <div class="row g-4">
            <div class="col-md-4">
                <h3>Maxicon Institute</h3>
                <p class="mt-3 text-white-50">145/2/2 Kandy Rd, Kiribathgoda 11600<br><strong class="text-warning">Phone:</strong> 0777 198 096<br><strong class="text-warning">Email:</strong> info@maxicon.lk</p>
            </div>
            <div class="col-md-4">
                <h5 class="text-warning">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="index.php#home">Home</a></li>
                    <li><a href="index.php#about">About</a></li>
                    <li><a href="index.php#services">Services</a></li>
                    <li><a href="index.php#contact">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5 class="text-warning">Connect With Us</h5>
                <div class="d-flex gap-3 mt-2">
                    <a href="#" class="fs-4"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="fs-4"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="fs-4"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="fs-4"><i class="fab fa-linkedin"></i></a>
                </div>
                <div class="mt-4 small text-white-50">© 2025 Maxicon Exams. All rights reserved.<br>Designed by <a href="https://Apexinventives.com/" class="text-warning">apexinventives</a></div>
            </div>
        </div>
    </div>
</footer>

<a class="back-to-top" onclick="window.scrollTo({top:0,behavior:'smooth'})"><i class="fas fa-arrow-up"></i></a>

<script>
<?php if (!empty($marks) && isset($scoresForChart)): ?>
    // Progress Chart
    const ctx = document.getElementById('progressChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($weeksForChart); ?>,
            datasets: [{
                label: 'Total Score',
                data: <?php echo json_encode($scoresForChart); ?>,
                borderColor: '#f57c00',
                backgroundColor: 'rgba(245, 124, 0, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#f57c00',
                pointBorderColor: '#fff',
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    max: 250,
                    title: { display: true, text: 'Score', font: { weight: 'bold' } }
                },
                x: { 
                    title: { display: true, text: 'Week', font: { weight: 'bold' } }
                }
            }
        }
    });
    
    // Score Distribution (Mission20 vs Homework)
    const mission20Data = <?php echo json_encode(array_map(function($m) { return $m['mission_20'] ?: 0; }, $marks)); ?>;
    const homeworkData = <?php echo json_encode(array_map(function($m) { return $m['homework'] ?: 0; }, $marks)); ?>;
    
    const ctxDist = document.getElementById('scoreDistribution').getContext('2d');
    new Chart(ctxDist, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($weeksForChart); ?>,
            datasets: [
                {
                    label: 'Mission 20 Score',
                    data: mission20Data,
                    backgroundColor: 'rgba(245, 124, 0, 0.7)',
                    borderRadius: 8
                },
                {
                    label: 'Homework Score',
                    data: homeworkData,
                    backgroundColor: 'rgba(76, 175, 80, 0.7)',
                    borderRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Mission 20 vs Homework Comparison' }
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Score' } }
            }
        }
    });
    
    // Attendance Chart
    const attended = <?php echo $attendedWeeks; ?>;
    const absent = <?php echo count($marks) - $attendedWeeks; ?>;
    
    const ctxAttend = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctxAttend, {
        type: 'doughnut',
        data: {
            labels: ['Attended', 'Absent'],
            datasets: [{
                data: [attended, absent],
                backgroundColor: ['#4caf50', '#f44336'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: 'Attendance Overview' }
            }
        }
    });

    const navbar = document.querySelector('.apex-navbar');
    const backToTop = document.querySelector('.back-to-top');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
            backToTop.classList.add('active');
        } else {
            navbar.classList.remove('scrolled');
            backToTop.classList.remove('active');
        }
    });
<?php endif; ?>
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>