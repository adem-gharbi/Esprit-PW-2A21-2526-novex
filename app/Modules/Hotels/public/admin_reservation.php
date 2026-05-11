<?php

//  connexion base de données
require_once "../config/database.php";

//  controller des réservations
require_once "../controllers/ReservationController.php";

//  création du controller avec la DB
$c = new ReservationController($db);

//  récupération de l'action (list, delete, etc.)
$action = $_GET['action'] ?? 'list';


//  SUPPRESSION RÉSERVATION
if($action == 'delete' && isset($_GET['id'])){

    //  suppression dans la base
    $c->delete($_GET['id']);

    //  redirection vers la liste (important pour éviter re-suppression)
    header("Location: admin_reservation.php");
    exit;
}


//  LISTE DES RÉSERVATIONS (par défaut)
$reservations = $c->index();


//  affichage vue back-office
include "../views/back/reservation_list.php";
?>