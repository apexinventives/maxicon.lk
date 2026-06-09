<?php
require_once 'config.php';
requireAdminLogin();

$pdo = getDB();

// Handle add/edit/delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add' && !empty($_POST['student_name'])) {
            $examId = generateExamID($_POST['student_name'], $_POST['grade']);
            $stmt = $pdo->prepare("INSERT INTO students (exam_id, student_name, grade, medium) VALUES (?, ?, ?, ?)");
            $stmt->execute([$examId, $_POST['student_name'], $_POST['grade'], $_POST['medium']]);
        } elseif ($_POST['action'] === 'edit' && !empty($_POST['exam_id'])) {
            $stmt = $pdo->prepare("UPDATE students SET student_name = ?, grade = ?, medium = ? WHERE exam_id = ?");
            $stmt->execute([$_POST['student_name'], $_POST['grade'], $_POST['medium'], $_POST['exam_id']]);
        } elseif ($_POST['action'] === 'delete' && !empty($_POST['exam_id'])) {
            $stmt = $pdo->prepare("DELETE FROM students WHERE exam_id = ?");
            $stmt->execute([$_POST['exam_id']]);
        }
        header('Location: manage_students.php');
        exit();
    }
}

// Get all students
$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE student_name LIKE ? OR exam_id LIKE ? ORDER BY student_name");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM students ORDER BY student_name");
}
$students = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Maxicon Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .sidebar { background: linear-gradient(180deg, #1a1a2e 0%, #0a0a0f 100%); min-height: 100vh; position: fixed; left: 0; top: 0; width: 260px; color: white; }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 12px 24px; border-radius: 12px; margin: 4px 12px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(245,124,0,0.2); color: #f57c00; }
        .sidebar .nav-link i { width: 24px; margin-right: 12px; }
        .main-content { margin-left: 260px; padding: 24px; background: #f8fafc; min-height: 100vh; }
        .btn-apex { background: linear-gradient(135deg, #f57c00, #ff9f4a); border: none; border-radius: 40px; padding: 8px 20px; color: white; font-weight: 600; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="p-4"><h3 class="text-white">Maxicon<span style="color:#f57c00;">Admin</span></h3><p class="text-white-50 small">Exam Management System</p></div>
        <nav class="nav flex-column">
            <a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a class="nav-link" href="upload.php"><i class="fas fa-upload"></i> Upload Exam</a>
            <a class="nav-link active" href="manage_students.php"><i class="fas fa-users"></i> Manage Students</a>
            <a class="nav-link" href="edit_marks.php"><i class="fas fa-edit"></i> Edit Marks</a>
            <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>
    
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-users me-2"></i> Manage Students</h2>
            <button class="btn btn-apex" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-2"></i>Add Student</button>
        </div>
        
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="mb-3">
                <form method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" style="border-radius: 60px;" placeholder="Search by name or Exam ID..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-outline-secondary" style="border-radius: 60px;"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr><th>Exam ID</th><th>Student Name</th><th>Grade</th><th>Medium</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><code><?php echo $student['exam_id']; ?></code></td>
                            <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                            <td><?php echo $student['grade']; ?></td>
                            <td><?php echo $student['medium']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-btn" data-exam-id="<?php echo $student['exam_id']; ?>" data-name="<?php echo htmlspecialchars($student['student_name']); ?>" data-grade="<?php echo $student['grade']; ?>" data-medium="<?php echo $student['medium']; ?>"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-danger delete-btn" data-exam-id="<?php echo $student['exam_id']; ?>" data-name="<?php echo htmlspecialchars($student['student_name']); ?>"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-4">
                <form method="POST">
                    <div class="modal-header"><h5 class="modal-title">Add Student</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3"><label>Student Name</label><input type="text" name="student_name" class="form-control" required></div>
                        <div class="mb-3"><label>Grade</label><select name="grade" class="form-control" required><option>Grade 6</option><option>Grade 7</option><option>Grade 8</option><option>Grade 9</option><option>Grade 10</option><option>Grade 11</option></select></div>
                        <div class="mb-3"><label>Medium</label><select name="medium" class="form-control" required><option>Sinhala</option><option>English</option></select></div>
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn btn-apex">Add Student</button></div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const examId = btn.dataset.examId;
                const name = btn.dataset.name;
                const grade = btn.dataset.grade;
                const medium = btn.dataset.medium;
                // Simple edit - redirect to edit page
                window.location.href = `edit_student.php?exam_id=${examId}`;
            });
        });
        
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if (confirm(`Delete ${btn.dataset.name}? This will also delete all their marks.`)) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `<input type="hidden" name="action" value="delete"><input type="hidden" name="exam_id" value="${btn.dataset.examId}">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>