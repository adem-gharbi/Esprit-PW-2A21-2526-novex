<?php

//  Import du modèle Reservation (accès base de données)
require_once "../models/Reservation.php";

//  Classe contrôleur des réservations
class ReservationController {

    //  Objet modèle Reservation
    private $res;

    //  Constructeur : crée le lien avec la base de données
    public function __construct($db){

        //  On instancie le modèle Reservation avec la connexion DB
        $this->res = new Reservation($db);
    }

    //  AFFICHER TOUTES LES RÉSERVATIONS
    public function index(){

        //  Appelle le modèle pour récupérer toutes les réservations
        return $this->res->getAll();
    }

    //  AJOUTER UNE RÉSERVATION
    public function store($data){

        //  Date du jour (utilisée pour validation)
        $today = date("Y-m-d");

        //  Vérification nom client
        if(empty($data['nom']) || strlen($data['nom']) < 3){
            die("Nom invalide");
        }

        //  Vérification nombre personnes
        if($data['nb'] <= 0){
            die("Nombre de personnes invalide");
        }

        //  Interdire date passée pour arrivée
        if($data['arrivee'] < $today){
            die("Erreur : date d'arrivée passée interdite");
        }

        //  Départ doit être après arrivée
        if($data['depart'] < $data['arrivee']){
            die("Erreur : date de départ invalide");
        }

        //  Envoi vers le modèle pour insertion en DB
        return $this->res->add(
            $data['hotel_id'],
            $data['nom'],
            $data['arrivee'],
            $data['depart'],
            $data['nb']
        );
    }

    // ✏️ MODIFIER UNE RÉSERVATION
    public function update($data){

        // 📅 Date du jour
        $today = date("Y-m-d");

        // ❌ validation nom
        if(empty($data['nom']) || strlen($data['nom']) < 3){
            die("Nom invalide");
        }

        // ❌ validation nb personnes
        if($data['nb'] <= 0){
            die("Nombre de personnes invalide");
        }

        // 🚫 arrivée non passée
        if($data['arrivee'] < $today){
            die("Erreur : date d'arrivée passée interdite");
        }

        // 🚫 départ logique
        if($data['depart'] < $data['arrivee']){
            die("Erreur : date de départ invalide");
        }

        // 📤 update dans la base
        return $this->res->update(
            $data['id'],        // ID réservation
            $data['hotel_id'],  // hôtel
            $data['nom'],       // client
            $data['arrivee'],   // date arrivée
            $data['depart'],    // date départ
            $data['nb']         // nombre personnes
        );
    }

    // 🗑️ SUPPRIMER UNE RÉSERVATION
    public function delete($id){

        // ❌ suppression via modèle
        return $this->res->delete($id);
    }

    // 🔍 RÉCUPÉRER UNE RÉSERVATION PAR ID
    public function getById($id){

        return $this->res->getById($id);
    }

    // 🏨 RÉSERVATIONS D’UN HOTEL
    public function getByHotel($hotel_id){

        return $this->res->getByHotel($hotel_id);
    }

    // 🔎 RECHERCHE PAR NOM CLIENT
    public function search($nom){

        // ⚠️ dépend du modèle Reservation
        return $this->res->searchByName($nom);
    }
}
?>