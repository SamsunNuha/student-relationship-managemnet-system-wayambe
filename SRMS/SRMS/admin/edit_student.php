<?php
// admin/edit_student.php - Edit Existing Student
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin_auth.php';

$student_id_param = (int)($_GET['id'] ?? 0);
$error = '';
$success = '';

// Fetch student record
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ? LIMIT 1");
$stmt->execute([$student_id_param]);
$student = $stmt->fetch();

if (!$student) {
    header("Location: students.php?error=" . urlencode("Student not found."));
    exit();
}

// Fetch departments for dropdown
$departments = $pdo->query("SELECT * FROM departments ORDER BY department_name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $academic_year = trim($_POST['academic_year'] ?? '');
    $enrollment_date = trim($_POST['enrollment_date'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');

    if (empty($full_name) || empty($dob) || empty($gender) || empty($email) || empty($phone) || empty($address) || empty($department) || empty($academic_year) || empty($enrollment_date)) {
        $error = "All mandatory fields are required.";
    } else {
        // Check duplicate email (excluding current student)
        $stmtCheck = $pdo->prepare("SELECT id FROM students WHERE email = ? AND id != ? LIMIT 1");
        $stmtCheck->execute([$email, $student_id_param]);
        if ($stmtCheck->fetch()) {
            $error = "Another student is already using this email address.";
        } else {
            if (!empty($new_password)) {
                $hashedPass = password_hash($new_password, PASSWORD_DEFAULT);
                $updStmt = $pdo->prepare("
                    UPDATE students 
                    SET full_name = ?, dob = ?, gender = ?, email = ?, phone = ?, address = ?, department = ?, academic_year = ?, enrollment_date = ?, password = ?
                    WHERE id = ?
                ");
                $updStmt->execute([$full_name, $dob, $gender, $email, $phone, $address, $department, $academic_year, $enrollment_date, $hashedPass, $student_id_param]);
            } else {
                $updStmt = $pdo->prepare("
                    UPDATE students 
                    SET full_name = ?, dob = ?, gender = ?, email = ?, phone = ?, address = ?, department = ?, academic_year = ?, enrollment_date = ?
                    WHERE id = ?
                ");
                $updStmt->execute([$full_name, $dob, $gender, $email, $phone, $address, $department, $academic_year, $enrollment_date, $student_id_param]);
            }

            $success = "Student profile updated successfully!";
            
            // Refresh student data
            $stmt->execute([$student_id_param]);
            $student = $stmt->fetch();
        }
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
            <li><a href="students.php" class="active"><i class="fa-solid fa-users"></i> Manage Students</a></li>
            <li><a href="add_student.php"><i class="fa-solid fa-user-plus"></i> Add Student</a></li>
            <li><a href="messages.php"><i class="fa-solid fa-comments"></i> Messages</a></li>
            <li><a href="announcements.php"><i class="fa-solid fa-bullhorn"></i> Announcements</a></li>
            <li><a href="profile.php"><i class="fa-solid fa-user-gear"></i> Admin Settings</a></li>
            <li><a href="logout.php" style="color: var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div>
        <div class="card-header" style="border: none; padding-bottom: 0; margin-bottom: 1.5rem;">
            <div>
                <h2><i class="fa-solid fa-user-pen" style="color: #3182ce;"></i> Edit Student Profile</h2>
                <p style="color: #718096; font-size: 0.95rem;">Updating Record for <strong><?php echo sanitize($student['student_id']); ?></strong></p>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <a href="view_student.php?id=<?php echo $student['id']; ?>" class="btn btn-outline"><i class="fa-solid fa-eye"></i> View Profile</a>
                <a href="students.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back to List</a>
            </div>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <form action="edit_student.php?id=<?php echo $student['id']; ?>" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Student ID (Read-only)</label>
                        <input type="text" class="form-control" value="<?php echo sanitize($student['student_id']); ?>" disabled style="background-color: #edf2f7; font-weight: 600;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo sanitize($student['full_name']); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?php echo sanitize($student['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo sanitize($student['phone']); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="<?php echo sanitize($student['dob']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-control" required>
                            <option value="Male" <?php echo $student['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo $student['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo $student['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Department</label>
                        <select name="department" class="form-control" required>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo sanitize($dept['department_name']); ?>" <?php echo $student['department'] === $dept['department_name'] ? 'selected' : ''; ?>>
                                    <?php echo sanitize($dept['department_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year" class="form-control" required>
                            <option value="1st Year" <?php echo $student['academic_year'] === '1st Year' ? 'selected' : ''; ?>>1st Year</option>
                            <option value="2nd Year" <?php echo $student['academic_year'] === '2nd Year' ? 'selected' : ''; ?>>2nd Year</option>
                            <option value="3rd Year" <?php echo $student['academic_year'] === '3rd Year' ? 'selected' : ''; ?>>3rd Year</option>
                            <option value="4th Year" <?php echo $student['academic_year'] === '4th Year' ? 'selected' : ''; ?>>4th Year</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Enrollment Date</label>
                        <input type="date" name="enrollment_date" class="form-control" value="<?php echo sanitize($student['enrollment_date']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Residential Address</label>
                    <textarea name="address" class="form-control" rows="2" required><?php echo sanitize($student['address']); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Reset Password (Optional)</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Leave empty to keep existing password">
                </div>

                <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary" style="padding: 0.7rem 1.5rem;">
                        <i class="fa-solid fa-floppy-disk"></i> Save Profile Changes
                    </button>
                    <a href="students.php" class="btn btn-outline" style="padding: 0.7rem 1.5rem;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
