<?php
session_start();

if (!isset($_SESSION['p_logged_in']) || $_SESSION['p_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>
