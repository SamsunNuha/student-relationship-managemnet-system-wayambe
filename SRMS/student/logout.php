<?php
// student/logout.php
session_start();
session_unset();
session_destroy();
header("Location: ../login.php?msg=" . urlencode("Logged out successfully."));
exit();
?>
