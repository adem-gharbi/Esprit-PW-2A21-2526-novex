<?php
class Hotel {
    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    public function getAll(){
        return $this->conn->query("SELECT * FROM hotel");
    }

    public function add($data){
        $stmt = $this->conn->prepare(
            "INSERT INTO hotel(nom, ville, etoiles, prix) VALUES (?,?,?,?)"
        );
        return $stmt->execute($data);
    }

    public function delete($id){
        $stmt = $this->conn->prepare("DELETE FROM hotel WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function getById($id){
        $stmt = $this->conn->prepare("SELECT * FROM hotel WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($data, $id){
        $stmt = $this->conn->prepare(
            "UPDATE hotel SET nom=?, ville=?, etoiles=?, prix=? WHERE id=?"
        );
        return $stmt->execute([
            $data['Nom'],
            $data['Ville'],
            $data['Etoiles'],
            $data['Prix'],
            $id
        ]);
    }
}