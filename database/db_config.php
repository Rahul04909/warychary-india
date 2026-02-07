<?php
// Database Helper Class
class Database {
    private $host = 'localhost';
    private $db_name = 'jhdindus_warycharycare';
    private $username;
    private $password;

    public function __construct() {
        if (php_sapi_name() === 'cli' || $_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') {
            $this->username = 'root';
            $this->password = '';
        } else {
            $this->username = 'jhdindus_warycharycare';
            $this->password = 'Rd14072003@./';
        }
    }
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
