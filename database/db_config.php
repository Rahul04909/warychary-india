<?php
// Database Helper Class
class Database
{
    private $host = 'localhost';
    private $db_name = 'jhdindus_warycharycare';
    private $username;
    private $password;

    public function __construct()
    {
        if (php_sapi_name() === 'cli' || (isset($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1'))) {
            $this->username = 'root';
            $this->password = '';
        } else {
            // Production Credentials
            // Based on error logs, the hosting account seems to be 'jghfrodu'
            if (strpos(__DIR__, '/home/jghfrodu/') !== false) {
                $this->username = 'jghfrodu_warycharycare';
                $this->db_name = 'jghfrodu_warycharycare';
            } else {
                $this->username = 'jhdindus_warycharycare';
            }
            $this->password = 'Rd14072003@./';
        }
    }
    public $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            // If DB doesn't exist, try to create it (only if on localhost)
            if ($this->username === 'root') {
                try {
                    $temp_conn = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
                    $temp_conn->exec("CREATE DATABASE IF NOT EXISTS `" . $this->db_name . "`");
                    $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
                    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    return $this->conn;
                } catch (PDOException $e) {
                    // Fall through to error
                }
            }
            
            // Log the error and show a user-friendly message
            error_log("Database Connection Error: " . $exception->getMessage());
            die("Database Connection Error. Please check your credentials in database/db_config.php. Error: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
