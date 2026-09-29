<?php
// register.php - Student Registration
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if (isStudentLoggedIn()) {
    header("Location: student/dashboard.php");
    exit();
}

// Fetch departments for dropdown
$departments = $pdo->query("SELECT * FROM departments ORDER BY department_name ASC")->fetchAll();

$error = '';
$form_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $academic_year = trim($_POST['academic_year'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    $form_data = $_POST;

    if (empty($full_name) || empty($dob) || empty($gender) || empty($email) || empty($phone) || empty($address) || empty($department) || empty($academic_year) || empty($password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Check if email already exists
        $stmtCheck = $pdo->prepare("SELECT id FROM students WHERE email = ? LIMIT 1");
        $stmtCheck->execute([$email]);
        if ($stmtCheck->fetch()) {
            $error = "A student account with this email already exists.";
        } else {
            // Generate Unique Student ID (e.g. STU-2026-XXX)
            $year = date('Y');
            $countStmt = $pdo->query("SELECT COUNT(*) FROM students");
            $nextNum = str_pad($countStmt->fetchColumn() + 1, 3, '0', STR_PAD_LEFT);
            $student_id = "STU-{$year}-{$nextNum}";

            // Ensure unique ID loop if exists
            $checkId = $pdo->prepare("SELECT id FROM students WHERE student_id = ?");
            $checkId->execute([$student_id]);
            if ($checkId->fetch()) {
                $student_id = "STU-{$year}-" . rand(100, 999);
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $enrollment_date = date('Y-m-d');

            $stmtIns = $pdo->prepare("
                INSERT INTO students (student_id, full_name, dob, gender, email, phone, address, department, academic_year, enrollment_date, password)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            if ($stmtIns->execute([$student_id, $full_name, $dob, $gender, $email, $phone, $address, $department, $academic_year, $enrollment_date, $hashedPassword])) {
                header("Location: login.php?registered=1");
                exit();
            } else {
                $error = "Failed to register. Please try again.";
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="main-content">
    <div style="max-width: 650px; margin: 2rem auto;">
        <div class="card">
            <div class="card-header" style="justify-content: center; text-align: center;">
                <h2><i class="fa-solid fa-user-plus" style="color: #3182ce;"></i> Student Registration</h2>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo sanitize($form_data['full_name'] ?? ''); ?>" required placeholder="e.g. John Doe">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?php echo sanitize($form_data['email'] ?? ''); ?>" required placeholder="john@student.edu">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo sanitize($form_data['phone'] ?? ''); ?>" required placeholder="555-0199">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="<?php echo sanitize($form_data['dob'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-control" required>
                            <option value="">Select Gender</option>
                            <option value="Male" <?php echo ($form_data['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($form_data['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo ($form_data['gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Department</label>
                        <select name="department" class="form-control" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo sanitize($dept['department_name']); ?>" <?php echo ($form_data['department'] ?? '') === $dept['department_name'] ? 'selected' : ''; ?>>
                                    <?php echo sanitize($dept['department_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year" class="form-control" required>
                            <option value="">Select Year</option>
                            <option value="1st Year" <?php echo ($form_data['academic_year'] ?? '') === '1st Year' ? 'selected' : ''; ?>>1st Year</option>
                            <option value="2nd Year" <?php echo ($form_data['academic_year'] ?? '') === '2nd Year' ? 'selected' : ''; ?>>2nd Year</option>
                            <option value="3rd Year" <?php echo ($form_data['academic_year'] ?? '') === '3rd Year' ? 'selected' : ''; ?>>3rd Year</option>
                            <option value="4th Year" <?php echo ($form_data['academic_year'] ?? '') === '4th Year' ? 'selected' : ''; ?>>4th Year</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Residential Address</label>
                    <textarea name="address" class="form-control" rows="2" required placeholder="Enter street address, city"><?php echo sanitize($form_data['address'] ?? ''); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="Min 6 characters">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required placeholder="Re-enter password">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem; padding: 0.75rem;">
                    <i class="fa-solid fa-user-plus"></i> Submit Registration
                </button>
            </form>

            <div style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem; color: #718096;">
                Already have an account? <a href="login.php" style="font-weight: 600;">Login Here</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
