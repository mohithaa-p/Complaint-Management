<?php
session_start();
$_SESSION = []; // Clear all session variables
session_destroy(); // Destroy the session

// Redirect to userlogin.php with the message
header("Location: logoutprint.php?logout=1");
exit;
?>
