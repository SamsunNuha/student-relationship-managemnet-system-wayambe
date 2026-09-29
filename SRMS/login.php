<?php
// login.php - Student Login
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if (isStudentLoggedIn()) {
    header("Location: student/dashboard.php");
    exit();
}

$error = '';
$success = isset($_GET['registered']) ? "Registration successful! You can now log in." : '';
if (isset($_GET['error'])) {
    $error = sanitize($_GET['error']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = "Please enter both Email/Student ID and Password.";
    } else {
        // Allow login using Email OR Student ID
        $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ? OR student_id = ? LIMIT 1");
        $stmt->execute([$email, $email]);
        $student = $stmt->fetch();

        if ($student && verifyPassword($password, $student['password'])) {
            $_SESSION['student_db_id'] = $student['id'];
            $_SESSION['student_id'] = $student['student_id'];
            $_SESSION['student_name'] = $student['full_name'];
            $_SESSION['student_email'] = $student['email'];

            header("Location: student/dashboard.php");
            exit();
        } else {
            $error = "Invalid Email/Student ID or Password.";
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="main-content">
    <div style="max-width: 450px; margin: 3rem auto;">
        <div class="card">
            <div class="card-header" style="justify-content: center; text-align: center;">
                <h2><i class="fa-solid fa-user-graduate" style="color: #3182ce;"></i> Student Login</h2>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo $success; ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Email or Student ID</label>
                    <input type="text" name="email" class="form-control" placeholder="e.g. alice@student.edu or STU-2026-001" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem; padding: 0.75rem;">
                    <i class="fa-solid fa-right-to-bracket"></i> Login to Portal
                </button>
            </form>

            <div style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem; color: #718096;">
                Don't have an account? <a href="register.php" style="font-weight: 600;">Register Here</a>
            </div>
            
            <div style="margin-top: 1rem; text-align: center; font-size: 0.85rem; background: #edf2f7; padding: 0.6rem; border-radius: 6px;">
                💡 Sample Student: <code>alice@student.edu</code> / <code>student123</code>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
