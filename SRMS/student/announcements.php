<?php
// student/announcements.php - View All Campus Announcements
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/student_auth.php';

// Fetch all announcements
$announcements = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-layout">
    <!-- Student Sidebar -->
    <div class="sidebar">
        <div class="sidebar-title">Student Portal</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
            <li><a href="profile.php"><i class="fa-solid fa-user"></i> My Profile</a></li>
            <li><a href="edit_profile.php"><i class="fa-solid fa-pen-to-square"></i> Edit Info</a></li>
            <li><a href="messages.php"><i class="fa-solid fa-comments"></i> Support Messages</a></li>
            <li><a href="send_message.php"><i class="fa-solid fa-paper-plane"></i> Send Message</a></li>
            <li><a href="announcements.php" class="active"><i class="fa-solid fa-bullhorn"></i> Announcements</a></li>
            <li><a href="logout.php" style="color: var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div>
        <div class="card-header" style="border: none; padding-bottom: 0; margin-bottom: 1.5rem;">
            <div>
                <h2><i class="fa-solid fa-bullhorn" style="color: #3182ce;"></i> Official Campus Announcements</h2>
                <p style="color: #718096; font-size: 0.95rem;">Stay updated with all recent notices from university administration.</p>
            </div>
        </div>

        <div class="card">
            <?php if (!empty($announcements)): ?>
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <?php foreach ($announcements as $ann): ?>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #3182ce; border-radius: 8px; padding: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                                <h3 style="color: #0f2c59; font-size: 1.2rem;"><?php echo sanitize($ann['title']); ?></h3>
                                <span style="font-size: 0.82rem; color: #718096; background: #edf2f7; padding: 0.3rem 0.75rem; border-radius: 50px;">
                                    <i class="fa-regular fa-calendar"></i> <?php echo date('F d, Y', strtotime($ann['created_at'])); ?>
                                </span>
                            </div>
                            <p style="color: #4a5568; font-size: 0.97rem; line-height: 1.7;"><?php echo nl2br(sanitize($ann['content'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem; color: #718096;">
                    <i class="fa-solid fa-bell-slash" style="font-size: 2.5rem; margin-bottom: 1rem; color: #cbd5e0;"></i>
                    <p style="font-size: 1.05rem;">No announcements have been posted by administration yet. Check back later.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
