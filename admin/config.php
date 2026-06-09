<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'maxicon_exams');

// Admin session key
define('ADMIN_SESSION_KEY', 'admin_logged_in');

// Upload directory
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/');

// Create uploads directory if not exists
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

// Database connection
function getDB() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch(PDOException $e) {
        die("Database Connection Failed: " . $e->getMessage());
    }
}

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION[ADMIN_SESSION_KEY]) && $_SESSION[ADMIN_SESSION_KEY] === true;
}

// Redirect if not logged in
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Generate unique Exam ID in format: MAXM-XXXX (4 random digits)
function generateExamID($name, $grade) {
    $pdo = getDB();
    
    // Generate random 4-digit number
    do {
        $randomNumber = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $examId = 'MAXM-' . $randomNumber;
        
        // Check if this exam ID already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE exam_id = ?");
        $stmt->execute([$examId]);
        $exists = $stmt->fetchColumn();
        
    } while ($exists > 0); // Keep generating until unique
    
    return $examId;
}

// Alternative: Generate Exam ID with custom prefix (for bulk operations)
function generateExamIDSimple() {
    $pdo = getDB();
    
    do {
        $randomNumber = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $examId = 'MAXM-' . $randomNumber;
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE exam_id = ?");
        $stmt->execute([$examId]);
        $exists = $stmt->fetchColumn();
        
    } while ($exists > 0);
    
    return $examId;
}
?>