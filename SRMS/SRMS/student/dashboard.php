<?php
// student/dashboard.php - Student Portal Dashboard
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/student_auth.php';

$student_id = $_SESSION['student_db_id'];

// Fetch Student Data
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ? LIMIT 1");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

// Fetch Student Messages stats
$msgCountStmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE student_id = ?");
$msgCountStmt->execute([$student_id]);
$totalMessages = $msgCountStmt->fetchColumn();

$repliedStmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE student_id = ? AND status = 'Replied'");
$repliedStmt->execute([$student_id]);
$totalReplied = $repliedStmt->fetchColumn();

// Fetch Latest Announcements
$announcements = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3")->fetchAll();

// Fetch Latest Student Messages
$msgStmt = $pdo->prepare("SELECT * FROM messages WHERE student_id = ? ORDER BY created_at DESC LIMIT 3");
$msgStmt->execute([$student_id]);
$recentMessages = $msgStmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-layout">
    <!-- Student Sidebar -->
    <div class="sidebar">
        <div class="sidebar-title">Student Portal</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
            <li><a href="profile.php"><i class="fa-solid fa-user"></i> My Profile</a></li>
            <li><a href="edit_profile.php"><i class="fa-solid fa-pen-to-square"></i> Edit Info</a></li>
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
                <h2>Welcome, <?php echo sanitize($student['full_name']); ?>! 🎓</h2>
                <p style="color: #718096; font-size: 0.95rem;">Student ID: <strong><?php echo sanitize($student['student_id']); ?></strong> | <?php echo sanitize($student['department']); ?> (<?php echo sanitize($student['academic_year']); ?>)</p>
            </div>
            <a href="send_message.php" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Contact Admin</a>
        </div>

        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $totalMessages; ?></h3>
                    <p>Submitted Requests</p>
                </div>
                <div class="stat-icon blue">
                    <i class="fa-solid fa-envelope"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo $totalReplied; ?></h3>
                    <p>Admin Replies</p>
                </div>
                <div class="stat-icon green">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-info">
                    <h3><?php echo count($announcements); ?></h3>
                    <p>Recent Notices</p>
                </div>
                <div class="stat-icon orange">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
            </div>
        </div>

        <!-- Campus Announcements Card -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-bullhorn" style="color: #3182ce;"></i> Recent Campus Announcements</h3>
                <a href="announcements.php" class="btn btn-outline btn-sm">View All Notices</a>
            </div>
            <?php if (!empty($announcements)): ?>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($announcements as $ann): ?>
                        <div style="background: #f8fafc; padding: 1rem; border-left: 4px solid #3182ce; border-radius: 6px;">
                            <h4 style="color: #0f2c59; font-size: 1.05rem; margin-bottom: 0.2rem;"><?php echo sanitize($ann['title']); ?></h4>
                            <span style="font-size: 0.8rem; color: #718096; display: block; margin-bottom: 0.5rem;">
                                <i class="fa-regular fa-clock"></i> Posted on <?php echo date('M d, Y', strtotime($ann['created_at'])); ?>
                            </span>
                            <p style="color: #4a5568; font-size: 0.92rem;"><?php echo nl2br(sanitize($ann['content'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: #718096;">No announcements posted yet.</p>
            <?php endif; ?>
        </div>

        <!-- Recent Submitted Messages -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fa-solid fa-comments" style="color: #3182ce;"></i> My Recent Inquiries & Requests</h3>
                <a href="messages.php" class="btn btn-outline btn-sm">View All Messages</a>
            </div>
            <?php if (!empty($recentMessages)): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Date Submitted</th>
                                <th>Admin Response</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentMessages as $msg): ?>
                                <tr>
                                    <td><strong><?php echo sanitize($msg['subject']); ?></strong></td>
                                    <td>
                                        <?php if ($msg['status'] === 'Replied'): ?>
                                            <span class="badge badge-success">Replied</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($msg['created_at'])); ?></td>
                                    <td>
                                        <?php if (!empty($msg['reply'])): ?>
                                            <span style="color: #2b6cb0; font-size: 0.9rem;"><i class="fa-solid fa-comment-dots"></i> View Reply in Support Messages</span>
                                        <?php else: ?>
                                            <span style="color: #718096; font-size: 0.88rem;">Awaiting administrator reply</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="color: #718096; text-align: center;">You have not submitted any support inquiries yet. Need help? <a href="send_message.php">Send a message to admin</a>.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
