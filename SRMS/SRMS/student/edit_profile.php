<?php
// student/edit_profile.php - Edit Personal Information & Upload Photo
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/student_auth.php';

$student_id = $_SESSION['student_db_id'];
$success = '';
$error = '';

// Fetch current student profile
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ? LIMIT 1");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $current_pass = trim($_POST['current_password'] ?? '');
    $new_pass = trim($_POST['new_password'] ?? '');
    $confirm_pass = trim($_POST['confirm_password'] ?? '');

    if (empty($full_name) || empty($phone) || empty($address)) {
        $error = "Name, Phone, and Address are required.";
    } else {
        $profile_image = $student['profile_image'];

        // Handle Profile Image Upload (Optional)
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_image']['tmp_name'];
            $fileName = $_FILES['profile_image']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($fileExtension, $allowedExtensions)) {
                $uploadDir = '../assets/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $newFileName = 'stu_' . $student_id . '_' . time() . '.' . $fileExtension;
                $dest_path = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $profile_image = $newFileName;
                }
            } else {
                $error = "Invalid image format. Allowed: JPG, PNG, GIF, WEBP.";
            }
        }

        if (empty($error)) {
            // Check password change request
            if (!empty($current_pass) || !empty($new_pass)) {
                if (!verifyPassword($current_pass, $student['password'])) {
                    $error = "Your current password was entered incorrectly.";
                } elseif ($new_pass !== $confirm_pass) {
                    $error = "New passwords do not match.";
                } elseif (strlen($new_pass) < 6) {
                    $error = "New password must be at least 6 characters.";
                } else {
                    $newHash = password_hash($new_pass, PASSWORD_DEFAULT);
                    $updStmt = $pdo->prepare("UPDATE students SET full_name = ?, phone = ?, address = ?, profile_image = ?, password = ? WHERE id = ?");
                    $updStmt->execute([$full_name, $phone, $address, $profile_image, $newHash, $student_id]);
                    $success = "Profile details and password updated successfully!";
                }
            } else {
                $updStmt = $pdo->prepare("UPDATE students SET full_name = ?, phone = ?, address = ?, profile_image = ? WHERE id = ?");
                $updStmt->execute([$full_name, $phone, $address, $profile_image, $student_id]);
                $success = "Profile details updated successfully!";
            }

            // Refresh student data
            $stmt->execute([$student_id]);
            $student = $stmt->fetch();
            $_SESSION['student_name'] = $student['full_name'];
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-layout">
    <!-- Student Sidebar -->
    <div class="sidebar">
        <div class="sidebar-title">Student Portal</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
            <li><a href="profile.php"><i class="fa-solid fa-user"></i> My Profile</a></li>
            <li><a href="edit_profile.php" class="active"><i class="fa-solid fa-pen-to-square"></i> Edit Info</a></li>
            <li><a href="messages.php"><i class="fa-solid fa-comments"></i> Support Messages</a></li>
            <li><a href="send_message.php"><i class="fa-solid fa-paper-plane"></i> Send Message</a></li>
            <li><a href="announcements.php"><i class="fa-solid fa-bullhorn"></i> Announcements</a></li>
            <li><a href="logout.php" style="color: var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div>
        <div class="card-header" style="border: none; padding-bottom: 0; margin-bottom: 1.5rem;">
            <div>
                <h2><i class="fa-solid fa-user-pen" style="color: #3182ce;"></i> Edit Profile Details</h2>
                <p style="color: #718096; font-size: 0.95rem;">Update your personal contact details, picture, or password.</p>
            </div>
            <a href="profile.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back to Profile</a>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Student ID (Read-Only)</label>
                        <input type="text" class="form-control" value="<?php echo sanitize($student['student_id']); ?>" disabled style="background: #edf2f7; font-weight: 600;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Department & Year (Read-Only)</label>
                        <input type="text" class="form-control" value="<?php echo sanitize($student['department'] . ' - ' . $student['academic_year']); ?>" disabled style="background: #edf2f7;">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo sanitize($student['full_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo sanitize($student['phone']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Residential Address</label>
                    <textarea name="address" class="form-control" rows="2" required><?php echo sanitize($student['address']); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Profile Picture (Optional)</label>
                    <input type="file" name="profile_image" class="form-control" accept="image/*">
                    <span style="font-size: 0.8rem; color: #718096;">Upload JPG, PNG, or WEBP photo.</span>
                </div>

                <h3 style="color: #0f2c59; margin-top: 2rem; margin-bottom: 1rem; border-top: 1px solid #e2e8f0; padding-top: 1rem; font-size: 1.1rem;">
                    <i class="fa-solid fa-lock"></i> Change Password (Optional)
                </h3>

                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" placeholder="Required only if setting a new password">
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

                <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem;">
                        <i class="fa-solid fa-floppy-disk"></i> Save Profile Changes
                    </button>
                    <a href="profile.php" class="btn btn-outline" style="padding: 0.75rem 1.5rem;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
