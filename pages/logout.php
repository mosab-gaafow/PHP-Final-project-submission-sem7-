<?php
// Start the session
session_start();

// Destroy the session
session_unset();
session_destroy();

// Clear the cookies by setting their expiration date to a past time
setcookie("username", "", time() - 3600, "/");
setcookie("password", "", time() - 3600, "/");

// Redirect to the login page (index.php)
header("Location: index.php");
exit();
?>
