<?php

// 🔌 Connexion à la base de données
require_once "../config/database.php";

// 🧠 Controller Hotel
require_once "../controllers/HotelController.php";

// 🏗️ Instanciation du controller avec DB
$c = new HotelController($db);

// 🎯 Récupération de l'action (list par défaut)
$action = $_GET['action'] ?? 'list';


/* =========================
   ➕ CREATE HOTEL
========================= */
if($action == 'create' && $_POST){

    // 💾 insertion en base
    $c->store($_POST);

    // 🔁 redirection après insertion
    header("Location: admin.php");
    exit;
}


/* =========================
   ✏ UPDATE HOTEL
========================= */
if($action == 'update' && $_POST){

    // 🔄 mise à jour hôtel par ID
    $c->update($_GET['id'], $_POST);

    // 🔁 retour liste
    header("Location: admin.php");
    exit;
}


/* =========================
   🗑 DELETE HOTEL
========================= */
if($action == 'delete' && isset($_GET['id'])){

    // ❌ suppression hôtel
    $c->delete($_GET['id']);

    // 🔁 retour liste
    header("Location: admin.php");
    exit;
}


/* =========================
   ✏ EDIT FORM
========================= */
if($action == 'edit'){

    // 📦 récupération données hôtel
    $hotel = $c->edit($_GET['id']);

    // 📄 affichage formulaire
    include "../views/back/hotel_form.php";
    exit;
}


/* =========================
   ➕ CREATE FORM
========================= */
if($action == 'create'){

    // 📄 formulaire vide
    include "../views/back/hotel_form.php";
    exit;
}


/* =========================
   📋 LISTE HOTELS
========================= */
$hotels = $c->index();

// 📄 affichage liste hôtels
include "../views/back/hotel_list.php";
?>