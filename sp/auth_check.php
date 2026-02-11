<?php
session_start();

if (!isset($_SESSION['senior_partner_id'])) {
    header("Location: login.php");
    exit;
}
?>
