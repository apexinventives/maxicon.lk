<?php
require_once 'config.php';

if (isAdminLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION[ADMIN_SESSION_KEY] = true;
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: index.php');
        exit();
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Maxicon Institute</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f57c00 0%, #fff3e0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 32px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 420px;
        }
        .login-card h2 {
            font-weight: 700;
            color: #1a1a2e;
        }
        .btn-apex {
            background: linear-gradient(135deg, #f57c00, #ff9f4a);
            border: none;
            border-radius: 40px;
            padding: 12px;
            font-weight: 600;
            color: white;
            width: 100%;
        }
        .btn-apex:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(245,124,0,0.3);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <h2>Maxicon Admin</h2>
            <p class="text-muted">Login to manage exam system</p>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required style="border-radius: 60px; padding: 12px 20px;">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required style="border-radius: 60px; padding: 12px 20px;">
            </div>
            <button type="submit" class="btn btn-apex mt-2">Login</button>
        </form>
        <div class="text-center mt-3">
            <small class="text-muted">Default: admin / admin123</small>
        </div>
    </div>
</body>
</html>