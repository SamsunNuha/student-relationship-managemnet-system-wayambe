<?php
// includes/admin_auth.php
require_once __DIR__ . '/auth.php';

if (!isAdminLoggedIn()) {
    header("Location: ../admin/login.php?error=" . urlencode("Please log in to access the Admin Panel."));
    exit();
}
?>
