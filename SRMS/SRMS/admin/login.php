<?php
// admin/login.php - Admin Portal Login
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if (isAdminLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
if (isset($_GET['error'])) {
    $error = sanitize($_GET['error']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Please enter Admin Username and Password.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$username, $username]);
        $admin = $stmt->fetch();

        if ($admin && verifyPassword($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_email'] = $admin['email'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid Admin Username or Password.";
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="main-content">
    <div style="max-width: 420px; margin: 3rem auto;">
        <div class="card" style="border-top: 4px solid #0f2c59;">
            <div class="card-header" style="justify-content: center; text-align: center;">
                <h2><i class="fa-solid fa-user-shield" style="color: #0f2c59;"></i> Admin Login</h2>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Username or Email</label>
                    <input type="text" name="username" class="form-control" placeholder="Default: admin" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Default: admin123" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem; padding: 0.75rem;">
                    <i class="fa-solid fa-lock"></i> Access Admin Console
                </button>
            </form>

            <div style="margin-top: 1.25rem; text-align: center; font-size: 0.85rem; background: #edf2f7; padding: 0.6rem; border-radius: 6px;">
                🔑 Default Admin: <code>admin</code> / <code>admin123</code>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
