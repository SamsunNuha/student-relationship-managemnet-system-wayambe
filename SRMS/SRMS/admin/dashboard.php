<?php
// admin/dashboard.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin_auth.php';

// Fetch Statistics
$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalDepartments = $pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn();
$pendingMessages = $pdo->query("SELECT COUNT(*) FROM messages WHERE status = 'Pending'")->fetchColumn();
$totalAnnouncements = $pdo->query("SELECT COUNT(*) FROM announcements")->fetchColumn();

// Fetch Recent Students
$recentStudents = $pdo->query("SELECT * FROM students ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Fetch Pending Messages
$recentMessages = $pdo->query("
    SELECT m.*, s.full_name, s.student_id as stu_code 
    FROM messages m 
    JOIN students s ON m.student_id = s.id 
    WHERE m.status = 'Pending' 
    ORDER BY m.created_at DESC LIMIT 5
")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-layout">
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="sidebar-title">Admin Navigation</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
            <li><a href="students.php"><i class="fa-solid fa-users"></i> Manage Students</a></li>
            <li><a href="add_student.php"><i class="fa-solid fa-user-plus"></i> Add Student</a></li>
            <li><a href="messages.php"><i class="fa-solid fa-comments"></i> Messages <?php if ($pendingMessages > 0): ?><span class="badge badge-warning"><?php echo $pendingMessages; ?></span><?php endif; ?></a></li>
            <li><a href="announcements.php"><i class="fa-solid fa-bullhorn"></i> Announcements</a></li>
            <li><a href="profile.php"><i class="fa-solid fa-user-gear"></i> Admin Settings</a></li>
            <li><a href="logout.php" style="color: var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Dashboard Content -->
    <div>
        <div class="card-header" style="border: none; padding-bottom: 0; margin-bottom: 1.5rem;">
            <div>
                <h2>Welcome Back, <?php echo sanitize($_SESSION['admin_name']); ?> 👋</h2>
                <p style="color: #718096; font-size: 0.95rem;">System Relationship & Overview Dashboard</p>
            </div>
            <a href="add_student.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add New Student</a>
        </div>

        <!-- 4 Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $totalStudents; ?></h3>
                    <p>Total Students</p>
                </div>
                <div class="stat-icon blue">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $totalDepartments; ?></h3>
                    <p>Departments</p>
                </div>
                <div class="stat-icon green">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $pendingMessages; ?></h3>
                    <p>New Messages</p>
                </div>
                <div class="stat-icon orange">
                    <i class="fa-solid fa-envelope-open-text"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $totalAnnouncements; ?></h3>
                    <p>Announcements</p>
                </div>
                <div class="stat-icon purple">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
            </div>
        </div>

        <!-- Recent Pending Messages Ticket Section -->
        <?php if (!empty($recentMessages)): ?>
            <div class="card">
                <div class="card-header">
                    <h3><i class="fa-solid fa-circle-exclamation" style="color: #dd6b20;"></i> Pending Messages / Requests</h3>
                    <a href="messages.php" class="btn btn-outline btn-sm">View All Messages</a>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentMessages as $msg): ?>
                                <tr>
                                    <td><strong><?php echo sanitize($msg['stu_code']); ?></strong></td>
                                    <td><?php echo sanitize($msg['full_name']); ?></td>
                                    <td><?php echo sanitize($msg['subject']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($msg['created_at'])); ?></td>
                                    <td>
                                        <a href="messages.php?reply_id=<?php echo $msg['id']; ?>" class="btn btn-secondary btn-sm">
                                            <i class="fa-solid fa-reply"></i> Reply
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Recently Enrolled Students Table -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-users" style="color: #3182ce;"></i> Recently Enrolled Students</h3>
                <a href="students.php" class="btn btn-outline btn-sm">View All Students</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Department</th>
                            <th>Year</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentStudents)): ?>
                            <?php foreach ($recentStudents as $student): ?>
                                <tr>
                                    <td><strong><?php echo sanitize($student['student_id']); ?></strong></td>
                                    <td><?php echo sanitize($student['full_name']); ?></td>
                                    <td><span class="badge badge-info"><?php echo sanitize($student['department']); ?></span></td>
                                    <td><?php echo sanitize($student['academic_year']); ?></td>
                                    <td><?php echo sanitize($student['email']); ?></td>
                                    <td>
                                        <a href="view_student.php?id=<?php echo $student['id']; ?>" class="btn btn-outline btn-sm"><i class="fa-solid fa-eye"></i> View</a>
                                        <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn btn-secondary btn-sm"><i class="fa-solid fa-pen"></i> Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #718096;">No students found in system.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
