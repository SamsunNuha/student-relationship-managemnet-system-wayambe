<?php
// includes/header.php
require_once __DIR__ . '/auth.php';

// Determine base path depending on location
$base_url = (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || strpos($_SERVER['REQUEST_URI'], '/student/') !== false) ? '../' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wayamba University — Student Relationship Management System</title>
    <meta name="description" content="Wayamba University SRMS - Connecting students, faculty, and administration through a unified relationship management platform.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
    <!-- FontAwesome Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="<?php echo $base_url; ?>index.php" class="navbar-brand">
                <img src="<?php echo $base_url; ?>assets/images/logo.png" alt="Wayamba University Logo">
                <div class="navbar-brand-text">
                    <span class="navbar-brand-title">Wayamba University</span>
                    <span class="navbar-brand-sub">Student Relationship Management System</span>
                </div>
            </a>
            <ul class="navbar-nav">
                <li><a href="<?php echo $base_url; ?>index.php"><i class="fa-solid fa-house"></i> Home</a></li>
                <li><a href="<?php echo $base_url; ?>about.php"><i class="fa-solid fa-circle-info"></i> About</a></li>
                <li><a href="<?php echo $base_url; ?>contact.php"><i class="fa-solid fa-envelope"></i> Contact</a></li>

                <li><div class="nav-divider"></div></li>

                <?php if (isAdminLoggedIn()): ?>
                    <li><a href="<?php echo $base_url; ?>admin/dashboard.php" class="btn-nav"><i class="fa-solid fa-user-shield"></i> Admin Dashboard</a></li>
                    <li><a href="<?php echo $base_url; ?>admin/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                <?php elseif (isStudentLoggedIn()): ?>
                    <li><a href="<?php echo $base_url; ?>student/dashboard.php" class="btn-nav"><i class="fa-solid fa-user-graduate"></i> Student Portal</a></li>
                    <li><a href="<?php echo $base_url; ?>student/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $base_url; ?>login.php"><i class="fa-solid fa-right-to-bracket"></i> Student Login</a></li>
                    <li><a href="<?php echo $base_url; ?>register.php" class="btn-nav"><i class="fa-solid fa-user-plus"></i> Register</a></li>
                    <li><a href="<?php echo $base_url; ?>admin/login.php" style="font-size: 0.82rem; opacity: 0.7;"><i class="fa-solid fa-lock"></i> Admin</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
