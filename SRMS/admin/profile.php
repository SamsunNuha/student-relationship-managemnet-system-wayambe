<?php
// admin/profile.php - Admin Account Settings
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin_auth.php';

$admin_id = $_SESSION['admin_id'];
$success = '';
$error = '';

// Fetch current admin profile
$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ? LIMIT 1");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $current_pass = trim($_POST['current_password'] ?? '');
    $new_pass = trim($_POST['new_password'] ?? '');
    $confirm_pass = trim($_POST['confirm_password'] ?? '');

    if (empty($full_name) || empty($email)) {
        $error = "Full Name and Email are required.";
    } else {
        // If changing password
        if (!empty($current_pass) || !empty($new_pass)) {
            if (!verifyPassword($current_pass, $admin['password'])) {
                $error = "Current Password is incorrect.";
            } elseif ($new_pass !== $confirm_pass) {
                $error = "New passwords do not match.";
            } elseif (strlen($new_pass) < 6) {
                $error = "New password must be at least 6 characters.";
            } else {
                $newHash = password_hash($new_pass, PASSWORD_DEFAULT);
                $updStmt = $pdo->prepare("UPDATE admins SET full_name = ?, email = ?, password = ? WHERE id = ?");
                $updStmt->execute([$full_name, $email, $newHash, $admin_id]);
                $success = "Profile and password updated successfully!";
            }
        } else {
            $updStmt = $pdo->prepare("UPDATE admins SET full_name = ?, email = ? WHERE id = ?");
            $updStmt->execute([$full_name, $email, $admin_id]);
            $success = "Profile updated successfully!";
        }

        // Refresh admin data
        $stmt->execute([$admin_id]);
        $admin = $stmt->fetch();
        $_SESSION['admin_name'] = $admin['full_name'];
        $_SESSION['admin_email'] = $admin['email'];
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-layout">
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="sidebar-title">Admin Navigation</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
            <li><a href="students.php"><i class="fa-solid fa-users"></i> Manage Students</a></li>
            <li><a href="add_student.php"><i class="fa-solid fa-user-plus"></i> Add Student</a></li>
            <li><a href="messages.php"><i class="fa-solid fa-comments"></i> Messages</a></li>
            <li><a href="announcements.php"><i class="fa-solid fa-bullhorn"></i> Announcements</a></li>
            <li><a href="profile.php" class="active"><i class="fa-solid fa-user-gear"></i> Admin Settings</a></li>
            <li><a href="logout.php" style="color: var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div>
        <div class="card-header" style="border: none; padding-bottom: 0; margin-bottom: 1.5rem;">
            <div>
                <h2><i class="fa-solid fa-user-gear" style="color: #3182ce;"></i> Admin Account Settings</h2>
                <p style="color: #718096; font-size: 0.95rem;">Update your administrator credentials and password.</p>
            </div>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card" style="max-width: 600px;">
            <form action="profile.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Username (Read-Only)</label>
                    <input type="text" class="form-control" value="<?php echo sanitize($admin['username']); ?>" disabled style="background: #edf2f7; font-weight: 600;">
                </div>

                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo sanitize($admin['full_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Administrator Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo sanitize($admin['email']); ?>" required>
                </div>

                <h3 style="color: #0f2c59; margin-top: 2rem; margin-bottom: 1rem; border-top: 1px solid #e2e8f0; padding-top: 1rem; font-size: 1.1rem;">
                    <i class="fa-solid fa-key"></i> Change Password
                </h3>

                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" placeholder="Required only if changing password">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Min 6 characters">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter new password">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 1rem; padding: 0.75rem 1.5rem;">
                    <i class="fa-solid fa-floppy-disk"></i> Update Profile Settings
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
