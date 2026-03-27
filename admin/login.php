<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to dashboard if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

require_once(__DIR__ . '/../includes/db.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['last_activity'] = time(); // Initialize activity timestamp
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid password!';
        }
    } else {
        $error = 'Admin not found!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Elite Estates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background-color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <h3 class="fw-bold">ELITE <span class="text-accent">ESTATES</span></h3>
        <p class="text-muted">Admin Dashboard Login</p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-danger mb-4"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['reason']) && $_GET['reason'] == 'timeout'): ?>
    <div class="alert alert-warning mb-4 small">Session expired due to inactivity. Please login again.</div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="mb-3">
            <label class="form-label fw-bold">Username</label>
            <input type="text" name="username" class="form-control py-2" required autofocus>
        </div>
        <div class="mb-4">
            <label class="form-label fw-bold">Password</label>
            <input type="password" name="password" class="form-control py-2" required>
        </div>
        <button type="submit" class="btn btn-accent w-100 py-2 fw-bold">Login to Dashboard</button>
    </form>
    
    <div class="mt-4 text-center">
        <a href="../index.php" class="text-decoration-none text-muted small"><i class="fas fa-arrow-left me-1"></i> Back to Website</a>
    </div>
</div>

</body>
</html>
