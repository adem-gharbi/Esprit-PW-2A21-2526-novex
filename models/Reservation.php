<?php

// 🏨 Classe Reservation = MODELE (accès base de données)
class Reservation {

    // 🔌 connexion PDO à la base de données
    private $conn;

    // 🧱 constructeur : reçoit la connexion DB
    public function __construct($db){

        // 📌 stocke la connexion dans la classe
        $this->conn = $db;
    }

    // 📋 RÉCUPÉRER TOUTES LES RÉSERVATIONS
    public function getAll(){

        // 🧠 requête SQL avec JOIN pour afficher hôtel + réservation
        $sql = "SELECT r.*, h.Nom AS hotel_nom, h.Ville, h.Etoiles
                FROM reservation r
                JOIN hotel h ON r.hotel_id = h.Id";

        // ▶ exécution directe (pas de paramètre ici)
        return $this->conn->query($sql);
    }

    // ➕ AJOUTER UNE RÉSERVATION
    public function add($hotel_id, $nom, $arrivee, $depart, $nb){

        // 🧠 requête préparée pour sécurité SQL injection
        $stmt = $this->conn->prepare("
            INSERT INTO reservation
            (hotel_id, nom_client, date_arrivee, date_depart, nb_personnes)
            VALUES (?, ?, ?, ?, ?)
        ");

        // ▶ exécution avec données utilisateur
        return $stmt->execute([
            $hotel_id,
            $nom,
            $arrivee,
            $depart,
            $nb
        ]);
    }

    // ✏️ MODIFIER UNE RÉSERVATION
    public function update($id, $hotel_id, $nom, $arrivee, $depart, $nb){

        // 🧠 update sécurisé
        $stmt = $this->conn->prepare("
            UPDATE reservation
            SET hotel_id = ?,
                nom_client = ?,
                date_arrivee = ?,
                date_depart = ?,
                nb_personnes = ?
            WHERE id = ?
        ");

        // ▶ exécution
        return $stmt->execute([
            $hotel_id,
            $nom,
            $arrivee,
            $depart,
            $nb,
            $id
        ]);
    }

    // 🗑️ SUPPRIMER UNE RÉSERVATION
    public function delete($id){

        // 🧠 suppression sécurisée
        $stmt = $this->conn->prepare("
            DELETE FROM reservation
            WHERE id = ?
        ");

        // ▶ exécution
        return $stmt->execute([$id]);
    }

    // 🔍 RÉCUPÉRER UNE RÉSERVATION PAR ID
    public function getById($id){

        // 🧠 requête simple
        $stmt = $this->conn->prepare("
            SELECT *
            FROM reservation
            WHERE id = ?
        ");

        // ▶ exécution
        $stmt->execute([$id]);

        // 📦 retourne une seule réservation
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🏨 RÉSERVATIONS D’UN HOTEL
    public function getByHotel($hotel_id){

        // 🧠 join pour afficher hotel + reservation
        $stmt = $this->conn->prepare("
            SELECT r.*, h.Nom AS hotel_nom, h.Ville, h.Etoiles
            FROM reservation r
            JOIN hotel h ON r.hotel_id = h.Id
            WHERE r.hotel_id = ?
        ");

        // ▶ exécution
        $stmt->execute([$hotel_id]);

        // 📦 retourne plusieurs lignes
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔎 RECHERCHE PAR NOM CLIENT
    public function searchByName($nom){

        // 🧠 recherche LIKE (partielle)
        $stmt = $this->conn->prepare("
            SELECT r.*, h.Nom AS hotel_nom, h.Ville, h.Etoiles
            FROM reservation r
            JOIN hotel h ON r.hotel_id = h.Id
            WHERE r.nom_client LIKE ?
        ");

        // ▶ recherche avec wildcard %
        $stmt->execute(["%$nom%"]);

        // 📦 retourne résultats
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>