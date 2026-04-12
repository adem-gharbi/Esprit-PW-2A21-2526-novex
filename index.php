<?php
require_once "config/database.php";
require_once "models/Hotel.php";
require_once "controllers/HotelController.php";

/* DB */
$db = new Database();
$conn = $db->getConnection();

/* Controller */
$controller = new HotelController($conn);

/* ================= ADD ================= */
if(isset($_POST['add'])){

    if(empty($_POST['Nom']) || empty($_POST['Ville']) || empty($_POST['Etoiles']) || empty($_POST['Prix'])){
        die("Tous les champs sont obligatoires ❌");
    }

    $controller->add([
        $_POST['Nom'],
        $_POST['Ville'],
        $_POST['Etoiles'],
        $_POST['Prix']
    ]);

    header("Location: index.php");
    exit;
}

/* ================= DELETE ================= */
if(isset($_GET['delete'])){
    $controller->delete($_GET['delete']);
    header("Location: index.php");
    exit;
}

/* ================= EDIT ================= */
$edit = null;
if(isset($_GET['edit'])){
    $edit = $controller->getById($_GET['edit']);
}

/* ================= UPDATE ================= */
if(isset($_POST['update'])){

    if(empty($_POST['Nom']) || empty($_POST['Ville']) || empty($_POST['Etoiles']) || empty($_POST['Prix'])){
        die("Tous les champs sont obligatoires ❌");
    }

    $controller->update([
        $_POST['Nom'],
        $_POST['Ville'],
        $_POST['Etoiles'],
        $_POST['Prix']
    ], $_POST['Id']);

    header("Location: index.php");
    exit;
}

/* ================= LIST ================= */
$hotels = $controller->list();

/* VIEW */
include "views/hotels/list.php";
?>