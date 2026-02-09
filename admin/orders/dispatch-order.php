<?php
session_start();
include_once '../../database/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: ../login.php");
        exit;
    }

    $database = new Database();
    $db = $database->getConnection();

    $order_id = $_POST['order_id'];
    $courier_name = $_POST['courier_name'];
    $tracking_id = $_POST['tracking_id'];
    $dispatched_from = $_POST['dispatched_from'];

    try {
        $sql = "UPDATE orders 
                SET courier_name = :courier_name, 
                    tracking_id = :tracking_id, 
                    dispatched_from = :dispatched_from, 
                    dispatched_date = NOW(),
                    status = 'completed' 
                WHERE id = :id";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':courier_name', $courier_name);
        $stmt->bindParam(':tracking_id', $tracking_id);
        $stmt->bindParam(':dispatched_from', $dispatched_from);
        $stmt->bindParam(':id', $order_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Order dispatched successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to dispatch order.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }

    header("Location: pending-orders.php");
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>
