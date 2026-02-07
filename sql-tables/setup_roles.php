<?php
include_once '../database/db_config.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    try {
        // --- 1. Senior Partners Table ---
        $sql_sp = "CREATE TABLE IF NOT EXISTS senior_partners (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100),
            phone VARCHAR(20),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $db->exec($sql_sp);
        echo "Table 'senior_partners' created/exists.<br>";

        // Default Senior Partner
        $sp_user = 'senior';
        $sp_pass = 'senior123';
        $sp_hash = password_hash($sp_pass, PASSWORD_DEFAULT);
        
        $check_sp = $db->prepare("SELECT id FROM senior_partners WHERE username = ?");
        $check_sp->execute([$sp_user]);
        
        if ($check_sp->rowCount() == 0) {
            $ins_sp = $db->prepare("INSERT INTO senior_partners (username, password, email) VALUES (?, ?, ?)");
            if ($ins_sp->execute([$sp_user, $sp_hash, 'senior@warychary.in'])) {
                echo "Default Senior Partner created: <strong>$sp_user / $sp_pass</strong><br>";
            }
        } else {
            echo "Senior Partner '$sp_user' already exists.<br>";
        }

        // --- 2. Partners Table ---
        $sql_p = "CREATE TABLE IF NOT EXISTS partners (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100),
            phone VARCHAR(20),
            senior_partner_id INT(11) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (senior_partner_id) REFERENCES senior_partners(id) ON DELETE SET NULL
        )";
        $db->exec($sql_p);
        echo "Table 'partners' created/exists.<br>";

        // Default Partner
        $p_user = 'partner';
        $p_pass = 'partner123';
        $p_hash = password_hash($p_pass, PASSWORD_DEFAULT);
        
        $check_p = $db->prepare("SELECT id FROM partners WHERE username = ?");
        $check_p->execute([$p_user]);
        
        if ($check_p->rowCount() == 0) {
            $ins_p = $db->prepare("INSERT INTO partners (username, password, email) VALUES (?, ?, ?)");
            if ($ins_p->execute([$p_user, $p_hash, 'partner@warychary.in'])) {
                echo "Default Partner created: <strong>$p_user / $p_pass</strong><br>";
            }
        } else {
            echo "Partner '$p_user' already exists.<br>";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
