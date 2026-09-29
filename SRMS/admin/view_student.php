<?php
// admin/view_student.php - Detailed View of Individual Student
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin_auth.php';

$student_id_param = (int)($_GET['id'] ?? 0);

// Fetch Student Profile
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ? LIMIT 1");
$stmt->execute([$student_id_param]);
$student = $stmt->fetch();

if (!$student) {
    header("Location: students.php?error=" . urlencode("Student record not found."));
    exit();
}

// Fetch Student Messages
$msgStmt = $pdo->prepare("SELECT * FROM messages WHERE student_id = ? ORDER BY created_at DESC");
$msgStmt->execute([$student_id_param]);
$messages = $msgStmt->fetchAll();

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
                <h2><i class="fa-solid fa-address-card" style="color: #3182ce;"></i> Student Profile Details</h2>
                <p style="color: #718096; font-size: 0.95rem;">Full student record and request history</p>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn btn-secondary"><i class="fa-solid fa-pen"></i> Edit Profile</a>
                <a href="students.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back to List</a>
            </div>
        </div>

        <!-- Student Summary Header -->
        <div class="card" style="display: flex; gap: 2rem; align-items: center; flex-wrap: wrap;">
            <?php if (!empty($student['profile_image']) && file_exists('../assets/uploads/' . $student['profile_image'])): ?>
                <img src="../assets/uploads/<?php echo sanitize($student['profile_image']); ?>" alt="Profile Avatar" class="profile-avatar">
            <?php else: ?>
                <div class="profile-default-avatar">
                    <?php echo strtoupper(substr($student['full_name'], 0, 1)); ?>
                </div>
            <?php endif; ?>

            <div>
                <h2 style="color: #0f2c59; font-size: 1.6rem;"><?php echo sanitize($student['full_name']); ?></h2>
                <p style="color: #4a5568; font-size: 1.05rem;">
                    <strong>ID:</strong> <span class="badge badge-info"><?php echo sanitize($student['student_id']); ?></span> |
                    <strong>Department:</strong> <?php echo sanitize($student['department']); ?> |
                    <strong>Academic Year:</strong> <?php echo sanitize($student['academic_year']); ?>
                </p>
                <p style="color: #718096; font-size: 0.9rem; margin-top: 0.25rem;">
                    Enrolled on <?php echo date('F d, Y', strtotime($student['enrollment_date'])); ?>
                </p>
            </div>
        </div>

        <!-- Detailed Info Cards -->
        <div class="card">
            <h3 style="color: #0f2c59; margin-bottom: 1.25rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;">
                <i class="fa-solid fa-circle-info"></i> Personal & Academic Details
            </h3>

            <div class="form-row" style="row-gap: 1.25rem;">
                <div>
                    <strong style="color: #718096; font-size: 0.85rem; text-transform: uppercase;">Email Address</strong>
                    <div style="font-size: 1rem; color: #2d3748; font-weight: 600;"><?php echo sanitize($student['email']); ?></div>
                </div>
                <div>
                    <strong style="color: #718096; font-size: 0.85rem; text-transform: uppercase;">Phone Number</strong>
                    <div style="font-size: 1rem; color: #2d3748; font-weight: 600;"><?php echo sanitize($student['phone']); ?></div>
                </div>
                <div>
                    <strong style="color: #718096; font-size: 0.85rem; text-transform: uppercase;">Date of Birth</strong>
                    <div style="font-size: 1rem; color: #2d3748; font-weight: 600;"><?php echo date('M d, Y', strtotime($student['dob'])); ?></div>
                </div>
                <div>
                    <strong style="color: #718096; font-size: 0.85rem; text-transform: uppercase;">Gender</strong>
                    <div style="font-size: 1rem; color: #2d3748; font-weight: 600;"><?php echo sanitize($student['gender']); ?></div>
                </div>
            </div>

            <div style="margin-top: 1.5rem;">
                <strong style="color: #718096; font-size: 0.85rem; text-transform: uppercase;">Residential Address</strong>
                <div style="font-size: 1rem; color: #2d3748; background: #f8fafc; padding: 0.75rem; border-radius: 6px; margin-top: 0.25rem;">
                    <?php echo nl2br(sanitize($student['address'])); ?>
                </div>
            </div>
        </div>

        <!-- Message/Support Ticket History -->
        <div class="card">
            <h3 style="color: #0f2c59; margin-bottom: 1.25rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;">
                <i class="fa-solid fa-comments"></i> Communication & Message History (<?php echo count($messages); ?>)
            </h3>

            <?php if (!empty($messages)): ?>
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <?php foreach ($messages as $msg): ?>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.25rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                <h4 style="color: #0f2c59; font-size: 1.05rem;"><?php echo sanitize($msg['subject']); ?></h4>
                                <div>
                                    <?php if ($msg['status'] === 'Replied'): ?>
                                        <span class="badge badge-success">Replied</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Pending Reply</span>
                                    <?php endif; ?>
                                    <span style="font-size: 0.8rem; color: #718096; margin-left: 0.5rem;"><?php echo date('M d, Y g:i A', strtotime($msg['created_at'])); ?></span>
                                </div>
                            </div>

                            <p style="color: #4a5568; margin-bottom: 0.75rem; line-height: 1.5;"><?php echo nl2br(sanitize($msg['message'])); ?></p>

                            <?php if (!empty($msg['reply'])): ?>
                                <div style="background: #ebf8ff; border-left: 4px solid #3182ce; padding: 0.85rem 1rem; border-radius: 4px; margin-top: 0.5rem;">
                                    <strong style="color: #2b6cb0; font-size: 0.9rem;"><i class="fa-solid fa-reply"></i> Admin Response:</strong>
                                    <p style="color: #2d3748; margin-top: 0.25rem; font-size: 0.95rem;"><?php echo nl2br(sanitize($msg['reply'])); ?></p>
                                </div>
                            <?php else: ?>
                                <a href="messages.php?reply_id=<?php echo $msg['id']; ?>" class="btn btn-secondary btn-sm" style="margin-top: 0.5rem;">
                                    <i class="fa-solid fa-reply"></i> Send Reply
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: #718096;">No messages sent by this student yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
