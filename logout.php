<?php
session_start(); // Start the session

if (isset($_SESSION['user_id'])) {
    unset($_SESSION['user_id']);
  
    if (empty($_SESSION)) {
        session_destroy();
    }

    header("Location: user_login.php");
    exit;
}
?>
