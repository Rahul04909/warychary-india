<?php
include_once '../database/db_config.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    try {
        // Create Admins Table
        $sql = "CREATE TABLE IF NOT EXISTS admins (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $db->exec($sql);
        echo "Table 'admins' created successfully.<br>";

        // Insert Default Admin (if not exists)
        $username = 'admin';
        $password = 'admin123'; // Change this!
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $email = 'admin@warychary.in';

        // Check if admin exists
        $check_stmt = $db->prepare("SELECT id FROM admins WHERE username = :username");
        $check_stmt->bindParam(':username', $username);
        $check_stmt->execute();

        if ($check_stmt->rowCount() == 0) {
            $insert_stmt = $db->prepare("INSERT INTO admins (username, password, email) VALUES (:username, :password, :email)");
            $insert_stmt->bindParam(':username', $username);
            $insert_stmt->bindParam(':password', $password_hash);
            $insert_stmt->bindParam(':email', $email);
            
            if ($insert_stmt->execute()) {
                echo "Default Admin User Created.<br>";
                echo "Username: <strong>" . $username . "</strong><br>";
                echo "Password: <strong>" . $password . "</strong><br>";
                echo "<p style='color:red;'>Please change this password immediately after login!</p>";
            } else {
                echo "Failed to create admin user.<br>";
            }
        } else {
            echo "Admin user already exists.<br>";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Database connection failed.";
}
?>
