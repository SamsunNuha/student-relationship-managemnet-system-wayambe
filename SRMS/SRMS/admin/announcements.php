<?php
// admin/announcements.php - Manage Campus Announcements
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin_auth.php';

$success = '';
$error = '';

// Delete Announcement
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $del_id = (int)$_GET['id'];
    $stmtDel = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
    if ($stmtDel->execute([$del_id])) {
        $success = "Announcement deleted successfully.";
    } else {
        $error = "Failed to delete announcement.";
    }
}

// Add Announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (empty($title) || empty($content)) {
        $error = "Announcement title and content are required.";
    } else {
        $stmtIns = $pdo->prepare("INSERT INTO announcements (title, content) VALUES (?, ?)");
        if ($stmtIns->execute([$title, $content])) {
            $success = "New announcement published successfully!";
        } else {
            $error = "Failed to publish announcement.";
        }
    }
}

// Fetch all announcements
$announcements = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC")->fetchAll();

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
            <li><a href="announcements.php" class="active"><i class="fa-solid fa-bullhorn"></i> Announcements</a></li>
            <li><a href="profile.php"><i class="fa-solid fa-user-gear"></i> Admin Settings</a></li>
            <li><a href="logout.php" style="color: var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div>
        <div class="card-header" style="border: none; padding-bottom: 0; margin-bottom: 1.5rem;">
            <div>
                <h2><i class="fa-solid fa-bullhorn" style="color: #3182ce;"></i> Campus Announcements</h2>
                <p style="color: #718096; font-size: 0.95rem;">Publish official notices visible to all students.</p>
            </div>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Create Announcement Form -->
        <div class="card">
            <h3 style="color: #0f2c59; margin-bottom: 1rem;"><i class="fa-solid fa-plus-circle"></i> Create New Announcement</h3>
            <form action="announcements.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Announcement Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Fall 2026 Examination Schedule" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Announcement Content</label>
                    <textarea name="content" class="form-control" rows="4" placeholder="Enter detailed notification..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-paper-plane"></i> Publish Announcement
                </button>
            </form>
        </div>

        <!-- List Announcements -->
        <div class="card">
            <h3 style="color: #0f2c59; margin-bottom: 1.25rem;"><i class="fa-solid fa-list-check"></i> Published Announcements (<?php echo count($announcements); ?>)</h3>
            
            <?php if (!empty($announcements)): ?>
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <?php foreach ($announcements as $ann): ?>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.25rem; border-left: 4px solid #3182ce;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                <h4 style="color: #0f2c59; font-size: 1.1rem;"><?php echo sanitize($ann['title']); ?></h4>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <span style="font-size: 0.8rem; color: #718096;"><i class="fa-regular fa-clock"></i> <?php echo date('M d, Y', strtotime($ann['created_at'])); ?></span>
                                    <a href="announcements.php?action=delete&id=<?php echo $ann['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this announcement?');" title="Delete"><i class="fa-solid fa-trash"></i></a>
                                </div>
                            </div>
                            <p style="color: #4a5568; font-size: 0.95rem; line-height: 1.6;"><?php echo nl2br(sanitize($ann['content'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: #718096; text-align: center;">No announcements published yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
