<?php
// admin/students.php - View & Search Students
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin_auth.php';

$success = '';
$error = '';

// Handle Delete Request
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = (int)$_GET['id'];
    $stmtDel = $pdo->prepare("DELETE FROM students WHERE id = ?");
    if ($stmtDel->execute([$delete_id])) {
        $success = "Student record deleted successfully.";
    } else {
        $error = "Failed to delete student record.";
    }
}

// Search and Filter logic
$search = trim($_GET['search'] ?? '');
$department = trim($_GET['department'] ?? '');

$query = "SELECT * FROM students WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (full_name LIKE ? OR student_id LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $term = "%{$search}%";
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
}

if (!empty($department)) {
    $query .= " AND department = ?";
    $params[] = $department;
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll();

// Fetch departments list for filter dropdown
$departments = $pdo->query("SELECT * FROM departments ORDER BY department_name ASC")->fetchAll();

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
                <h2><i class="fa-solid fa-users" style="color: #3182ce;"></i> Student Records</h2>
                <p style="color: #718096; font-size: 0.95rem;">Search, view, update, and manage student profiles.</p>
            </div>
            <a href="add_student.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add New Student</a>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Search & Filter Form -->
        <div class="card" style="padding: 1.25rem;">
            <form action="students.php" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
                <div style="flex: 2; min-width: 200px;">
                    <label class="form-label">Search Keyword</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, Student ID, Email..." value="<?php echo sanitize($search); ?>">
                </div>
                <div style="flex: 1; min-width: 180px;">
                    <label class="form-label">Department</label>
                    <select name="department" class="form-control">
                        <option value="">All Departments</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo sanitize($dept['department_name']); ?>" <?php echo $department === $dept['department_name'] ? 'selected' : ''; ?>>
                                <?php echo sanitize($dept['department_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                    <?php if (!empty($search) || !empty($department)): ?>
                        <a href="students.php" class="btn btn-outline"><i class="fa-solid fa-rotate-left"></i> Reset</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Student Records Table -->
        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Department</th>
                            <th>Academic Year</th>
                            <th>Email & Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($students)): ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><strong><?php echo sanitize($student['student_id']); ?></strong></td>
                                    <td><?php echo sanitize($student['full_name']); ?></td>
                                    <td><span class="badge badge-info"><?php echo sanitize($student['department']); ?></span></td>
                                    <td><?php echo sanitize($student['academic_year']); ?></td>
                                    <td>
                                        <div><?php echo sanitize($student['email']); ?></div>
                                        <div style="font-size: 0.8rem; color: #718096;"><?php echo sanitize($student['phone']); ?></div>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 0.35rem;">
                                            <a href="view_student.php?id=<?php echo $student['id']; ?>" class="btn btn-outline btn-sm" title="View Profile"><i class="fa-solid fa-eye"></i></a>
                                            <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn btn-secondary btn-sm" title="Edit Student"><i class="fa-solid fa-pen"></i></a>
                                            <a href="students.php?action=delete&id=<?php echo $student['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this student record?');" title="Delete Student"><i class="fa-solid fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #718096; padding: 2rem;">No matching student records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
