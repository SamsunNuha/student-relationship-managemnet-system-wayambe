<?php
// admin/add_student.php - Add New Student Record
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin_auth.php';

$error = '';
$success = '';

// Fetch departments dropdown
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
    $enrollment_date = trim($_POST['enrollment_date'] ?? date('Y-m-d'));
    $password = trim($_POST['password'] ?? '');

    if (empty($full_name) || empty($dob) || empty($gender) || empty($email) || empty($phone) || empty($address) || empty($department) || empty($academic_year) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Check duplicate email
        $stmtCheck = $pdo->prepare("SELECT id FROM students WHERE email = ? LIMIT 1");
        $stmtCheck->execute([$email]);
        if ($stmtCheck->fetch()) {
            $error = "A student with this email address already exists.";
        } else {
            // Generate Unique Student ID
            $year = date('Y', strtotime($enrollment_date));
            $countStmt = $pdo->query("SELECT COUNT(*) FROM students");
            $nextNum = str_pad($countStmt->fetchColumn() + 1, 3, '0', STR_PAD_LEFT);
            $student_id = "STU-{$year}-{$nextNum}";

            // Ensure unique ID
            $checkId = $pdo->prepare("SELECT id FROM students WHERE student_id = ?");
            $checkId->execute([$student_id]);
            if ($checkId->fetch()) {
                $student_id = "STU-{$year}-" . rand(100, 999);
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmtIns = $pdo->prepare("
                INSERT INTO students (student_id, full_name, dob, gender, email, phone, address, department, academic_year, enrollment_date, password)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            if ($stmtIns->execute([$student_id, $full_name, $dob, $gender, $email, $phone, $address, $department, $academic_year, $enrollment_date, $hashedPassword])) {
                $success = "Student '{$full_name}' added successfully! Student ID: <strong>{$student_id}</strong>";
            } else {
                $error = "Failed to add student. Please try again.";
            }
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
            <li><a href="students.php"><i class="fa-solid fa-users"></i> Manage Students</a></li>
            <li><a href="add_student.php" class="active"><i class="fa-solid fa-user-plus"></i> Add Student</a></li>
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
                <h2><i class="fa-solid fa-user-plus" style="color: #3182ce;"></i> Add New Student</h2>
                <p style="color: #718096; font-size: 0.95rem;">Fill in student details to enroll them into the SRMS database.</p>
            </div>
            <a href="students.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back to Students</a>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <form action="add_student.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" required placeholder="e.g. Alice Johnson">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required placeholder="alice@student.edu">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" required placeholder="555-0101">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-control" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Department</label>
                        <select name="department" class="form-control" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo sanitize($dept['department_name']); ?>">
                                    <?php echo sanitize($dept['department_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Academic Year</label>
                        <select name="academic_year" class="form-control" required>
                            <option value="">Select Year</option>
                            <option value="1st Year">1st Year</option>
                            <option value="2nd Year">2nd Year</option>
                            <option value="3rd Year">3rd Year</option>
                            <option value="4th Year">4th Year</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Enrollment Date</label>
                        <input type="date" name="enrollment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Residential Address</label>
                    <textarea name="address" class="form-control" rows="2" required placeholder="Enter student home address"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Initial Account Password</label>
                    <input type="text" name="password" class="form-control" value="student123" required placeholder="Default password for student login">
                    <span style="font-size: 0.8rem; color: #718096;">Student can change their password after logging in.</span>
                </div>

                <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary" style="padding: 0.7rem 1.5rem;">
                        <i class="fa-solid fa-user-check"></i> Register Student Record
                    </button>
                    <a href="students.php" class="btn btn-outline" style="padding: 0.7rem 1.5rem;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
