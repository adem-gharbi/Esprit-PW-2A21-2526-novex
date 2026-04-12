<?php
require_once "config/database.php";
require_once "models/Hotel.php";
require_once "controllers/HotelController.php";

/* DB connection */
$db = new Database();
$conn = $db->getConnection();

/* Controller */
$controller = new HotelController($conn);

/* Get data */
$hotels = $controller->list();

/* Layout */
include "views/layout/header.php";
include "views/front/list.php";
include "views/layout/footer.php";
?>