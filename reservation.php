<?php
require_once "config/database.php";
require_once "controllers/ReservationController.php";
require_once "controllers/HotelController.php";

$db = new Database();
$conn = $db->getConnection();

$controller = new ReservationController($conn);

/* EDIT */
$edit = null;
if(isset($_GET['edit'])){
    $edit = $controller->getById($_GET['edit']);
}

/* hotels */
$hotelController = new HotelController($conn);
$hotelsList = $hotelController->list();

/* ADD */
if(isset($_POST['add'])){
    $controller->add([
        $_POST['hotel_id'],
        $_POST['nom_client'],
        $_POST['nb_personnes'],
        $_POST['date_reservation']
    ]);
}

/* UPDATE */
if(isset($_POST['update'])){
    $controller->update([
        $_POST['hotel_id'],
        $_POST['nom_client'],
        $_POST['nb_personnes'],
        $_POST['date_reservation']
    ], $_POST['id']);
}

/* DELETE */
if(isset($_GET['delete'])){
    $controller->delete($_GET['delete']);
}

$reservations = $controller->list();

include "views/reservations/list.php";