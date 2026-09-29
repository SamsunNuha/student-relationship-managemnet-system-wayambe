<?php
// admin/messages.php - Manage & Reply to Student Requests
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin_auth.php';

$success = '';
$error = '';

// Handle Reply Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reply') {
    $msg_id = (int)$_POST['message_id'];
    $reply = trim($_POST['reply_text'] ?? '');

    if (empty($reply)) {
        $error = "Reply content cannot be empty.";
    } else {
        $stmtRep = $pdo->prepare("UPDATE messages SET reply = ?, status = 'Replied' WHERE id = ?");
        if ($stmtRep->execute([$reply, $msg_id])) {
            $success = "Reply sent to student successfully!";
        } else {
            $error = "Failed to submit reply.";
        }
    }
}

// Fetch all messages with student info
$stmt = $pdo->query("
    SELECT m.*, s.full_name, s.student_id as stu_code, s.email, s.department 
    FROM messages m 
    JOIN students s ON m.student_id = s.id 
    ORDER BY m.created_at DESC
");
$messages = $stmt->fetchAll();

$reply_id = (int)($_GET['reply_id'] ?? 0);

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
            <li><a href="messages.php" class="active"><i class="fa-solid fa-comments"></i> Messages</a></li>
            <li><a href="announcements.php"><i class="fa-solid fa-bullhorn"></i> Announcements</a></li>
            <li><a href="profile.php"><i class="fa-solid fa-user-gear"></i> Admin Settings</a></li>
            <li><a href="logout.php" style="color: var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div>
        <div class="card-header" style="border: none; padding-bottom: 0; margin-bottom: 1.5rem;">
            <div>
                <h2><i class="fa-solid fa-comments" style="color: #3182ce;"></i> Student Messages & Inquiries</h2>
                <p style="color: #718096; font-size: 0.95rem;">Review and answer messages sent by students.</p>
            </div>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <?php if (!empty($messages)): ?>
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <?php foreach ($messages as $msg): ?>
                        <div id="msg-<?php echo $msg['id']; ?>" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem; box-shadow: var(--shadow-sm); <?php echo ($reply_id === (int)$msg['id']) ? 'border-color: #3182ce; box-shadow: 0 0 0 2px rgba(49,130,206,0.2);' : ''; ?>">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                                <div>
                                    <h3 style="color: #0f2c59; font-size: 1.15rem; margin-bottom: 0.2rem;"><?php echo sanitize($msg['subject']); ?></h3>
                                    <div style="font-size: 0.9rem; color: #4a5568;">
                                        From: <strong><?php echo sanitize($msg['full_name']); ?></strong> (<code><?php echo sanitize($msg['stu_code']); ?></code>)
                                        - <span class="badge badge-info"><?php echo sanitize($msg['department']); ?></span>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <?php if ($msg['status'] === 'Replied'): ?>
                                        <span class="badge badge-success"><i class="fa-solid fa-check"></i> Replied</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning"><i class="fa-solid fa-clock"></i> Pending</span>
                                    <?php endif; ?>
                                    <div style="font-size: 0.8rem; color: #718096; margin-top: 0.25rem;">
                                        <?php echo date('M d, Y h:i A', strtotime($msg['created_at'])); ?>
                                    </div>
                                </div>
                            </div>

                            <div style="background: #f8fafc; padding: 1rem; border-radius: 6px; color: #2d3748; line-height: 1.6; margin-bottom: 1rem;">
                                <?php echo nl2br(sanitize($msg['message'])); ?>
                            </div>

                            <?php if (!empty($msg['reply'])): ?>
                                <div style="background: #ebf8ff; border-left: 4px solid #3182ce; padding: 1rem; border-radius: 4px;">
                                    <strong style="color: #2b6cb0; font-size: 0.95rem;"><i class="fa-solid fa-user-shield"></i> Official Admin Reply:</strong>
                                    <p style="color: #2d3748; margin-top: 0.3rem; font-size: 0.95rem; line-height: 1.5;"><?php echo nl2br(sanitize($msg['reply'])); ?></p>
                                </div>
                            <?php else: ?>
                                <form action="messages.php" method="POST" style="margin-top: 1rem; background: #fffaf0; padding: 1rem; border: 1px solid #feebc8; border-radius: 6px;">
                                    <input type="hidden" name="action" value="reply">
                                    <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                    
                                    <label class="form-label" style="color: #742a2a;"><i class="fa-solid fa-reply"></i> Write Response to Student:</label>
                                    <textarea name="reply_text" class="form-control" rows="3" placeholder="Type official reply here..." required></textarea>
                                    
                                    <button type="submit" class="btn btn-secondary btn-sm" style="margin-top: 0.75rem; padding: 0.5rem 1.25rem;">
                                        <i class="fa-solid fa-paper-plane"></i> Send Reply
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: #718096; text-align: center; padding: 2rem;">No student messages or requests found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
