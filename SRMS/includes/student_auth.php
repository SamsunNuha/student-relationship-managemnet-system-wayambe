<?php
// includes/student_auth.php
require_once __DIR__ . '/auth.php';

if (!isStudentLoggedIn()) {
    header("Location: ../login.php?error=" . urlencode("Please log in to access your student portal."));
    exit();
}
?>
