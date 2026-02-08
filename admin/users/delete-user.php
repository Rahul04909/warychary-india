<?php
session_start();
include_once __DIR__ . '/../../database/db_config.php';

// Check admin auth (assuming admin login sets admin_id)
// if (!isset($_SESSION['admin_id'])) { header("Location: ../login.php"); exit; }

if (isset($_GET['id'])) {
    $database = new Database();
    $db = $database->getConnection();
    
    $id = $_GET['id'];
    
    $query = "DELETE FROM users WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "User deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to delete user.";
    }
}

header("Location: index.php");
exit;
?>
