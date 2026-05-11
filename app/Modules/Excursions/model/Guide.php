<?php
class Guide {
    private $conn;
    private $table = "guide";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        // Query to select all guides from the table
        $query = "SELECT * FROM " . $this->table . " ORDER BY nom ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>