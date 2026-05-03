<?php

// 🏨 MODELE RESERVATION
class Reservation {

    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    // =========================
    // 📋 GET ALL
    // =========================
    public function getAll(){

        $sql = "SELECT r.*, h.Nom AS hotel_nom, h.Ville, h.Etoiles
                FROM reservation r
                JOIN hotel h ON r.hotel_id = h.Id";

        return $this->conn->query($sql);
    }

    // =========================
    // ➕ ADD
    // =========================
    public function add($hotel_id, $nom, $arrivee, $depart, $nb, $discount){

        $stmt = $this->conn->prepare("
            INSERT INTO reservation
            (hotel_id, nom_client, date_arrivee, date_depart, nb_personnes, discount)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $hotel_id,
            $nom,
            $arrivee,
            $depart,
            $nb,
            $discount
        ]);
    }

    // =========================
    // ✏️ UPDATE
    // =========================
    public function update($id, $hotel_id, $nom, $arrivee, $depart, $nb, $discount){

        $stmt = $this->conn->prepare("
            UPDATE reservation
            SET hotel_id = ?,
                nom_client = ?,
                date_arrivee = ?,
                date_depart = ?,
                nb_personnes = ?,
                discount = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $hotel_id,
            $nom,
            $arrivee,
            $depart,
            $nb,
            $discount,
            $id
        ]);
    }

    // =========================
    // 🗑️ DELETE
    // =========================
    public function delete($id){

        $stmt = $this->conn->prepare("
            DELETE FROM reservation
            WHERE id = ?
        ");

        return $stmt->execute([$id]);
    }

    // =========================
    // 🔍 GET BY ID
    // =========================
    public function getById($id){

        $stmt = $this->conn->prepare("
            SELECT *
            FROM reservation
            WHERE id = ?
        ");

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =========================
    // 🏨 GET BY HOTEL
    // =========================
    public function getByHotel($hotel_id){

        $stmt = $this->conn->prepare("
            SELECT r.*, h.Nom AS hotel_nom, h.Ville, h.Etoiles
            FROM reservation r
            JOIN hotel h ON r.hotel_id = h.Id
            WHERE r.hotel_id = ?
        ");

        $stmt->execute([$hotel_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================
    // 🔎 SEARCH
    // =========================
    public function searchByName($nom){

        $stmt = $this->conn->prepare("
            SELECT r.*, h.Nom AS hotel_nom, h.Ville, h.Etoiles
            FROM reservation r
            JOIN hotel h ON r.hotel_id = h.Id
            WHERE r.nom_client LIKE ?
        ");

        $stmt->execute(["%$nom%"]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================
    // 📊 STATISTIQUES PAR HÔTEL AVEC POURCENTAGE
    // =========================
    public function countReservationsByHotel(){

    $sql = "
        SELECT 
            h.Nom AS hotel_nom,
            COUNT(r.id) AS total,
            ROUND(
                (COUNT(r.id) * 100.0) / (SELECT COUNT(*) FROM reservation),
                2
            ) AS percentage
        FROM reservation r
        JOIN hotel h ON r.hotel_id = h.Id
        GROUP BY r.hotel_id
    ";

    return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}
}
?>