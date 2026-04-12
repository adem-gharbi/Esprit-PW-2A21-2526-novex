<?php
class Reservation {

    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    /* LIST */
    public function getAll(){
        return $this->conn->query("
            SELECT r.*, h.nom AS hotel_nom
            FROM reservation r
            JOIN hotel h ON r.hotel_id = h.id
        ");
    }

    /* GET ONE (EDIT) */
    public function getById($id){
        $stmt = $this->conn->prepare("
            SELECT * FROM reservation WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ADD */
    public function add($data){
        $stmt = $this->conn->prepare("
            INSERT INTO reservation (hotel_id, nom_client, nb_personnes, date_reservation)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute($data);
    }

    /* UPDATE */
    public function update($data, $id){
        $stmt = $this->conn->prepare("
            UPDATE reservation
            SET hotel_id = ?, nom_client = ?, nb_personnes = ?, date_reservation = ?
            WHERE id = ?
        ");
        return $stmt->execute([...$data, $id]);
    }

    /* DELETE */
    public function delete($id){
        $stmt = $this->conn->prepare("
            DELETE FROM reservation WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }
}
?>