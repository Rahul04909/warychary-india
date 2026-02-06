<?php
// Database Helper Class
class Database {
    private $host = 'localhost';
    private $db_name = 'jhdindus_warycharycare'; // Default name, change if needed
    private $username = 'jhdindus_warycharycare';
    private $password = 'Rd14072003@./';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // If DB doesn't exist, try to create it (for setup purposes)
            try {
                $temp_conn = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
                $temp_conn->exec("CREATE DATABASE IF NOT EXISTS `" . $this->db_name . "`");
                $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Connection error: " . $exception->getMessage();
            }
        }
        return $this->conn;
    }
}
?>
