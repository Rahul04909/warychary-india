<?php
session_start();
echo "<h3>Session Debug</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "Referral Code (Session): " . ($_SESSION['referral_code'] ?? 'NOT SET') . "<br>";
echo "Referral Code (Cookie): " . ($_COOKIE['referral_code'] ?? 'NOT SET') . "<br>";
echo "<hr>";
echo "<a href='index.php?ref=TESTREF'>Set Referral Code to TESTREF</a>";
?>
