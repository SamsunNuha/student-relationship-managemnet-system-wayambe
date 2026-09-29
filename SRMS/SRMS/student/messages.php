<?php
// student/messages.php - View Sent Messages & Admin Replies
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/student_auth.php';

$student_id = $_SESSION['student_db_id'];

// Fetch all messages for this student
$stmt = $pdo->prepare("SELECT * FROM messages WHERE student_id = ? ORDER BY created_at DESC");
$stmt->execute([$student_id]);
$messages = $stmt->fetchAll();

$submitted = isset($_GET['submitted']) ? true : false;

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
            <li><a href="messages.php" class="active"><i class="fa-solid fa-comments"></i> Support Messages</a></li>
            <li><a href="send_message.php"><i class="fa-solid fa-paper-plane"></i> Send Message</a></li>
            <li><a href="announcements.php"><i class="fa-solid fa-bullhorn"></i> Announcements</a></li>
            <li><a href="logout.php" style="color: var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div>
        <div class="card-header" style="border: none; padding-bottom: 0; margin-bottom: 1.5rem;">
            <div>
                <h2><i class="fa-solid fa-comments" style="color: #3182ce;"></i> My Support Messages</h2>
                <p style="color: #718096; font-size: 0.95rem;">Track all your submitted inquiries and admin responses.</p>
            </div>
            <a href="send_message.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> New Message</a>
        </div>

        <?php if ($submitted): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Your request has been submitted! The admin will respond shortly.</div>
        <?php endif; ?>

        <div class="card">
            <?php if (!empty($messages)): ?>
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <?php foreach ($messages as $msg): ?>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                                <div>
                                    <h3 style="color: #0f2c59; font-size: 1.1rem;"><?php echo sanitize($msg['subject']); ?></h3>
                                    <span style="font-size: 0.82rem; color: #718096;">
                                        <i class="fa-regular fa-clock"></i> Submitted on <?php echo date('M d, Y g:i A', strtotime($msg['created_at'])); ?>
                                    </span>
                                </div>
                                <?php if ($msg['status'] === 'Replied'): ?>
                                    <span class="badge badge-success"><i class="fa-solid fa-check"></i> Replied</span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><i class="fa-solid fa-clock"></i> Awaiting Reply</span>
                                <?php endif; ?>
                            </div>

                            <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 1rem; border-radius: 6px; color: #2d3748; line-height: 1.6; margin-bottom: 1rem;">
                                <strong style="color: #718096; font-size: 0.82rem; text-transform: uppercase; display: block; margin-bottom: 0.4rem;">Your Message:</strong>
                                <?php echo nl2br(sanitize($msg['message'])); ?>
                            </div>

                            <?php if (!empty($msg['reply'])): ?>
                                <div style="background: #ebf8ff; border-left: 4px solid #3182ce; padding: 1rem; border-radius: 4px;">
                                    <strong style="color: #2b6cb0; font-size: 0.9rem; display: block; margin-bottom: 0.4rem;">
                                        <i class="fa-solid fa-user-shield"></i> Official Administration Response:
                                    </strong>
                                    <p style="color: #2d3748; font-size: 0.95rem; line-height: 1.6;"><?php echo nl2br(sanitize($msg['reply'])); ?></p>
                                </div>
                            <?php else: ?>
                                <div style="background: #fffaf0; border: 1px solid #feebc8; padding: 0.85rem 1rem; border-radius: 6px; font-size: 0.9rem; color: #744210;">
                                    <i class="fa-solid fa-hourglass-half"></i> Your request is being reviewed by the administration. Please check back soon.
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem; color: #718096;">
                    <i class="fa-solid fa-comment-slash" style="font-size: 2.5rem; margin-bottom: 1rem; color: #cbd5e0;"></i>
                    <p style="font-size: 1.05rem; margin-bottom: 1rem;">You haven't sent any messages yet.</p>
                    <a href="send_message.php" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Send a Message to Admin</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
