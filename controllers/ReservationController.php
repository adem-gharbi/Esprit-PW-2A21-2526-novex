<?php

// =========================
// 📦 MODELE RESERVATION
// =========================
require_once __DIR__ . "/../models/Reservation.php";

// =========================
// 🎮 CONTROLLER
// =========================
class ReservationController {

    private $res;

    // =========================
    // 🔌 CONSTRUCTOR
    // =========================
    public function __construct($db){
        $this->res = new Reservation($db);
    }

    // =========================
    // 📋 AFFICHER TOUTES LES RESERVATIONS
    // =========================
    public function index(){
        return $this->res->getAll();
    }

    // =========================
    // ➕ AJOUT RESERVATION
    // =========================
    public function store($data){

        $today = date("Y-m-d");

        if(empty($data['nom']) || strlen($data['nom']) < 3){
            die("Nom invalide");
        }

        if($data['nb'] <= 0){
            die("Nombre de personnes invalide");
        }

        if($data['arrivee'] < $today){
            die("Erreur : date d'arrivée passée interdite");
        }

        if($data['depart'] < $data['arrivee']){
            die("Erreur : date de départ invalide");
        }

        return $this->res->add(
            $data['hotel_id'],
            $data['nom'],
            $data['arrivee'],
            $data['depart'],
            $data['nb'],
            $data['discount']
        );
    }

    // =========================
    // ✏️ UPDATE RESERVATION
    // =========================
    public function update($data){

        $today = date("Y-m-d");

        if(empty($data['nom']) || strlen($data['nom']) < 3){
            die("Nom invalide");
        }

        if($data['nb'] <= 0){
            die("Nombre de personnes invalide");
        }

        if($data['arrivee'] < $today){
            die("Erreur : date d'arrivée passée interdite");
        }

        if($data['depart'] < $data['arrivee']){
            die("Erreur : date de départ invalide");
        }

        return $this->res->update(
            $data['id'],
            $data['hotel_id'],
            $data['nom'],
            $data['arrivee'],
            $data['depart'],
            $data['nb'],
            $data['discount']
        );
    }

    // =========================
    // 🗑️ DELETE
    // =========================
    public function delete($id){
        return $this->res->delete($id);
    }

    // =========================
    // 🔍 GET BY ID
    // =========================
    public function getById($id){
        return $this->res->getById($id);
    }

    // =========================
    // 🏨 GET BY HOTEL
    // =========================
    public function getByHotel($hotel_id){
        return $this->res->getByHotel($hotel_id);
    }

    // =========================
    // 🔎 SEARCH
    // =========================
    public function search($nom){
        return $this->res->searchByName($nom);
    }

    // =========================
    // 📊 STATISTIQUES PAR HOTEL ⭐ (AJOUTÉ)
    // =========================
    public function statsByHotel(){
    return $this->res->countReservationsByHotel();
}
}
?>