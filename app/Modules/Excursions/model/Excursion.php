<?php
class Excursion {
    private $conn;
    private $table = "excursion";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        // Query to select all excursions from the table
        $query = "SELECT * FROM " . $this->table . " ORDER BY titre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
