<?php

// 🏨 Classe Hotel (MODELE = communication avec la base de données)
class Hotel {

    // 🔌 connexion à la base de données (PDO)
    private $conn;

    // 🧱 constructeur : reçoit la connexion DB
    public function __construct($db){

        // 📌 on stocke la connexion dans la classe
        $this->conn = $db;
    }

    // 📋 RÉCUPÉRER TOUS LES HÔTELS
    public function getAll(){

        // 🧠 requête SQL pour tout sélectionner
        $stmt = $this->conn->prepare("SELECT * FROM hotel");

        // ▶ exécution requête
        $stmt->execute();

        // 📦 retourner tous les résultats sous forme de tableau
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔍 RÉCUPÉRER UN HÔTEL PAR ID
    public function getById($id){

        // 🧠 requête avec paramètre sécurisé
        $stmt = $this->conn->prepare("SELECT * FROM hotel WHERE Id=?");

        // ▶ exécution avec ID
        $stmt->execute([$id]);

        // 📦 retourne un seul hôtel
        return $stmt->fetch();
    }

    // ➕ AJOUTER UN HÔTEL
    public function add($nom,$ville,$etoiles,$prix){

        // 🧠 insertion SQL
        $stmt = $this->conn->prepare(
            "INSERT INTO hotel(Nom,Ville,Etoiles,Prix) VALUES (?,?,?,?)"
        );

        // ▶ exécute avec valeurs
        return $stmt->execute([$nom,$ville,$etoiles,$prix]);
    }

    // ✏️ MODIFIER UN HÔTEL
    public function update($id,$nom,$ville,$etoiles,$prix){

        // 🧠 update SQL sécurisé
        $stmt = $this->conn->prepare(
            "UPDATE hotel SET Nom=?,Ville=?,Etoiles=?,Prix=? WHERE Id=?"
        );

        // ▶ exécution avec paramètres
        return $stmt->execute([$nom,$ville,$etoiles,$prix,$id]);
    }

    // 🗑️ SUPPRIMER UN HÔTEL
    public function delete($id){

        // 🧠 suppression sécurisée
        $stmt = $this->conn->prepare("DELETE FROM hotel WHERE Id=?");

        // ▶ exécution
        return $stmt->execute([$id]);
    }
}
?>