<?php
// student/send_message.php - Submit Support Message/Request to Admin
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/student_auth.php';

$student_id = $_SESSION['student_db_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($subject) || empty($message)) {
        $error = "Subject and Message content are required.";
    } else {
        $stmtIns = $pdo->prepare("INSERT INTO messages (student_id, subject, message, status) VALUES (?, ?, ?, 'Pending')");
        if ($stmtIns->execute([$student_id, $subject, $message])) {
            header("Location: messages.php?submitted=1");
            exit();
        } else {
            $error = "Failed to send message. Please try again.";
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
            <li><a href="edit_profile.php"><i class="fa-solid fa-pen-to-square"></i> Edit Info</a></li>
            <li><a href="messages.php"><i class="fa-solid fa-comments"></i> Support Messages</a></li>
            <li><a href="send_message.php" class="active"><i class="fa-solid fa-paper-plane"></i> Send Message</a></li>
            <li><a href="announcements.php"><i class="fa-solid fa-bullhorn"></i> Announcements</a></li>
            <li><a href="logout.php" style="color: var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div>
        <div class="card-header" style="border: none; padding-bottom: 0; margin-bottom: 1.5rem;">
            <div>
                <h2><i class="fa-solid fa-paper-plane" style="color: #3182ce;"></i> Send Message to Administration</h2>
                <p style="color: #718096; font-size: 0.95rem;">Submit inquiries, transcript requests, or administrative questions.</p>
            </div>
            <a href="messages.php" class="btn btn-outline"><i class="fa-solid fa-comments"></i> View Sent Messages</a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card" style="max-width: 700px;">
            <form action="send_message.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Message Subject</label>
                    <input type="text" name="subject" class="form-control" placeholder="e.g. Transcript Request / Elective Change Inquiry" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Message Description</label>
                    <textarea name="message" class="form-control" rows="6" placeholder="Provide full details of your request or question..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem;">
                    <i class="fa-solid fa-paper-plane"></i> Submit Request
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
