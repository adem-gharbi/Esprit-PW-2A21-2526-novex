<?php

// 🔌 connexion base de données
require_once "../config/database.php";

// 📅 controller réservation
require_once "../controllers/ReservationController.php";

// 🏨 controller hôtel
require_once "../controllers/HotelController.php";

// 🏗️ instance réservation
$c = new ReservationController($db);

// 🏗️ instance hôtel
$h = new HotelController($db);

/* =========================
   ✏️ MODE EDITION
========================= */

// 🎯 si clic sur "modifier"
$edit = null;

if(isset($_GET['edit'])){
    // récupérer réservation à modifier
    $edit = $c->getById($_GET['edit']);
}

/* =========================
   ➕ AJOUT RÉSERVATION
========================= */

if(isset($_POST['add'])){
    $c->store($_POST);
    header("Location: reservation.php");
    exit;
}

/* =========================
   🔄 UPDATE RÉSERVATION
========================= */

if(isset($_POST['update'])){
    $c->update($_POST);
    header("Location: reservation.php");
    exit;
}

/* =========================
   ❌ DELETE RÉSERVATION
========================= */

if(isset($_GET['delete'])){
    $c->delete($_GET['delete']);
    header("Location: reservation.php");
    exit;
}

/* =========================
   📦 DONNÉES PAGE
========================= */

// 📋 toutes les réservations
$reservations = $c->index();

// 🏨 liste hôtels pour select
$hotels = $h->index();

// 📄 affichage vue
include "../views/front/reservation_list.php";
?>