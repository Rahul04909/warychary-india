<?php
session_start();

if (!isset($_SESSION['sp_logged_in']) || $_SESSION['sp_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>
