<!-- logout.php -->

<?php
// Initialize session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page after logout
header("location: login.php");
exit();
?>
