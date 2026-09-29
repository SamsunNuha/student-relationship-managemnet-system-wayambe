<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in as Admin
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Check if user is logged in as Student
 */
function isStudentLoggedIn() {
    return isset($_SESSION['student_db_id']) && !empty($_SESSION['student_db_id']);
}
?>
