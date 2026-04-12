<?php
require_once "config/database.php";
require_once "controllers/ReservationController.php";

$db = new Database();
$conn = $db->getConnection();

$resController = new ReservationController($conn);

/* DELETE */
if(isset($_GET['delete'])){
    $resController->delete($_GET['delete']);
}

/* LIST */
$reservations = $resController->list();

/* VIEW */
include "views/reservations_admin/list.php";
?>