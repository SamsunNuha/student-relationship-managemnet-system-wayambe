<?php
// student/profile.php - View Personal Student Profile
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/student_auth.php';

$student_id = $_SESSION['student_db_id'];

// Fetch current student profile
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ? LIMIT 1");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-layout">
    <!-- Student Sidebar -->
    <div class="sidebar">
        <div class="sidebar-title">Student Portal</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
            <li><a href="profile.php" class="active"><i class="fa-solid fa-user"></i> My Profile</a></li>
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
                <h2><i class="fa-solid fa-id-card" style="color: #3182ce;"></i> Personal Student Profile</h2>
                <p style="color: #718096; font-size: 0.95rem;">Review your official university record details.</p>
            </div>
            <a href="edit_profile.php" class="btn btn-secondary"><i class="fa-solid fa-pen-to-square"></i> Edit Profile</a>
        </div>

        <!-- Student Info Header Card -->
        <div class="card" style="display: flex; gap: 2rem; align-items: center; flex-wrap: wrap;">
            <?php if (!empty($student['profile_image']) && file_exists('../assets/uploads/' . $student['profile_image'])): ?>
                <img src="../assets/uploads/<?php echo sanitize($student['profile_image']); ?>" alt="Profile Picture" class="profile-avatar">
            <?php else: ?>
                <div class="profile-default-avatar">
                    <?php echo strtoupper(substr($student['full_name'], 0, 1)); ?>
                </div>
            <?php endif; ?>

            <div>
                <h2 style="color: #0f2c59; font-size: 1.75rem;"><?php echo sanitize($student['full_name']); ?></h2>
                <p style="color: #4a5568; font-size: 1.05rem; margin-top: 0.25rem;">
                    <strong>Student ID:</strong> <span class="badge badge-info"><?php echo sanitize($student['student_id']); ?></span> |
                    <strong>Department:</strong> <?php echo sanitize($student['department']); ?>
                </p>
                <p style="color: #718096; font-size: 0.9rem; margin-top: 0.3rem;">
                    Academic Standing: <strong><?php echo sanitize($student['academic_year']); ?></strong> | Enrolled Date: <?php echo date('F d, Y', strtotime($student['enrollment_date'])); ?>
                </p>
            </div>
        </div>

        <!-- Academic & Personal Breakdown -->
        <div class="card">
            <h3 style="color: #0f2c59; margin-bottom: 1.25rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;">
                <i class="fa-solid fa-user-check"></i> Account Details
            </h3>

            <div class="form-row" style="row-gap: 1.5rem;">
                <div>
                    <strong style="color: #718096; font-size: 0.85rem; text-transform: uppercase;">Registered Email</strong>
                    <div style="font-size: 1rem; color: #2d3748; font-weight: 600; margin-top: 0.2rem;"><?php echo sanitize($student['email']); ?></div>
                </div>
                <div>
                    <strong style="color: #718096; font-size: 0.85rem; text-transform: uppercase;">Contact Phone</strong>
                    <div style="font-size: 1rem; color: #2d3748; font-weight: 600; margin-top: 0.2rem;"><?php echo sanitize($student['phone']); ?></div>
                </div>
                <div>
                    <strong style="color: #718096; font-size: 0.85rem; text-transform: uppercase;">Date of Birth</strong>
                    <div style="font-size: 1rem; color: #2d3748; font-weight: 600; margin-top: 0.2rem;"><?php echo date('M d, Y', strtotime($student['dob'])); ?></div>
                </div>
                <div>
                    <strong style="color: #718096; font-size: 0.85rem; text-transform: uppercase;">Gender</strong>
                    <div style="font-size: 1rem; color: #2d3748; font-weight: 600; margin-top: 0.2rem;"><?php echo sanitize($student['gender']); ?></div>
                </div>
            </div>

            <div style="margin-top: 1.5rem;">
                <strong style="color: #718096; font-size: 0.85rem; text-transform: uppercase;">Residential Address</strong>
                <div style="font-size: 1rem; color: #2d3748; background: #f8fafc; padding: 0.85rem; border-radius: 6px; margin-top: 0.3rem;">
                    <?php echo nl2br(sanitize($student['address'])); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
