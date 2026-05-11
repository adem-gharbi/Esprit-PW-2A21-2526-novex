<?php

define('BASE_URL', '/travel_db/');


class Database {
    private $host = "localhost";
    private $db_name = "travel_db"; 
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Establishing the PDO connection
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            die("Database connection failed: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
?>