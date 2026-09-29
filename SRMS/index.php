<?php
// index.php - SRMS Homepage
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';

// Fetch quick stats
$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalDepts = $pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn();
$totalAnnouncements = $pdo->query("SELECT COUNT(*) FROM announcements")->fetchColumn();
$totalMessages = $pdo->query("SELECT COUNT(*) FROM messages WHERE status='Pending'")->fetchColumn();

// Fetch latest announcements
$stmtAnn = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3");
$announcements = $stmtAnn->fetchAll();

$annIcons = ['blue', 'orange', 'green'];
$annIconClasses = ['fa-solid fa-bullhorn', 'fa-solid fa-calendar-check', 'fa-solid fa-graduation-cap'];
?>

<div class="main-content">
    <!-- Hero Banner -->
    <div class="hero">
        <img src="assets/images/hero-bg.png" alt="Wayamba University Campus" class="hero-bg">
        <div class="hero-overlay"></div>
        <div class="hero-motto">Knowledge Builds the Future</div>
        <div class="hero-content">
            <div class="hero-eyebrow">Welcome to</div>
            <h1>Wayamba University</h1>
            <p>Connecting students, faculty, and administration through a unified relationship management platform.</p>
            <div class="hero-actions">
                <?php if (isStudentLoggedIn()): ?>
                    <a href="student/dashboard.php" class="btn-hero btn-hero-primary"><i class="fa-solid fa-gauge"></i> Go to Student Dashboard →</a>
                <?php elseif (isAdminLoggedIn()): ?>
                    <a href="admin/dashboard.php" class="btn-hero btn-hero-primary"><i class="fa-solid fa-gauge"></i> Go to Admin Dashboard →</a>
                <?php else: ?>
                    <a href="login.php" class="btn-hero btn-hero-primary"><i class="fa-solid fa-right-to-bracket"></i> Student Login →</a>
                    <a href="register.php" class="btn-hero btn-hero-outline"><i class="fa-solid fa-user-plus"></i> Register Now</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-icon blue"><i class="fa-solid fa-user-graduate"></i></div>
            <div class="stat-info">
                <h3><?php echo $totalStudents; ?></h3>
                <p>Enrolled Students</p>
                <small><i class="fa-solid fa-users"></i> Total registered students</small>
            </div>
            <span class="stat-arrow"><i class="fa-solid fa-chevron-right"></i></span>
        </div>
        <div class="stat-card green">
            <div class="stat-icon green"><i class="fa-solid fa-building-columns"></i></div>
            <div class="stat-info">
                <h3><?php echo $totalDepts; ?></h3>
                <p>Academic Departments</p>
                <small><i class="fa-solid fa-landmark"></i> All departments</small>
            </div>
            <span class="stat-arrow"><i class="fa-solid fa-chevron-right"></i></span>
        </div>
        <div class="stat-card orange">
            <div class="stat-icon orange"><i class="fa-solid fa-bullhorn"></i></div>
            <div class="stat-info">
                <h3><?php echo $totalAnnouncements; ?></h3>
                <p>Recent Announcements</p>
                <small><i class="fa-solid fa-bell"></i> Latest updates</small>
            </div>
            <span class="stat-arrow"><i class="fa-solid fa-chevron-right"></i></span>
        </div>
        <div class="stat-card purple">
            <div class="stat-icon purple"><i class="fa-solid fa-comments"></i></div>
            <div class="stat-info">
                <h3><?php echo $totalMessages; ?></h3>
                <p>Unread Messages</p>
                <small><i class="fa-solid fa-envelope"></i> From students</small>
            </div>
            <span class="stat-arrow"><i class="fa-solid fa-chevron-right"></i></span>
        </div>
    </div>

    <!-- Latest Announcements -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-bullhorn" style="color: #2563eb;"></i> Latest Campus Announcements</h2>
            <?php if (isAdminLoggedIn()): ?>
                <a href="admin/announcements.php" class="view-all">View All Announcements <i class="fa-solid fa-arrow-right"></i></a>
            <?php endif; ?>
        </div>
        <?php if (!empty($announcements)): ?>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php foreach ($announcements as $i => $ann): ?>
                    <div class="announcement-item">
                        <div class="announcement-icon <?php echo $annIcons[$i % 3]; ?>">
                            <i class="<?php echo $annIconClasses[$i % 3]; ?>"></i>
                        </div>
                        <div>
                            <div class="announcement-title"><?php echo sanitize($ann['title']); ?></div>
                            <div class="announcement-date">
                                <i class="fa-regular fa-clock"></i> Posted on <?php echo date('M d, Y', strtotime($ann['created_at'])); ?>
                            </div>
                        </div>
                        <div class="announcement-text">
                            <?php echo nl2br(sanitize($ann['content'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: var(--text-muted);">No announcements posted yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
