<?php
session_start();

if (!isset($_SESSION['partner_id'])) {
    header("Location: login.php");
    exit;
}
?>
