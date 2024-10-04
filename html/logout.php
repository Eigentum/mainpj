<?php
session_start();

// Logout, Remove all session 
session_unset();
session_destroy();

// redirect Login page
header("Location: login.php");
exit();
?>

