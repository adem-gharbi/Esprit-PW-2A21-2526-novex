<?php
require_once "../config/database.php";
require_once "../model/Circuit.php";

$db = new Database();
$pdo = $db->connect();
$circuit = new Circuit($pdo);

// Helper: Build redirect URL preserving destination filter
$destId = $_POST['dest_id'] ?? $_GET['dest_id'] ?? null;
$redirect = "../view/back/listCircuit.php" . ($destId ? "?id=" . intval($destId) : "");

// ADD
if(isset($_POST['add'])) {
    $titre = $_POST['titre'];
    $duree = $_POST['duree'];
    $prix = $_POST['prix'];
    $places = $_POST['nb_places'];
    $date = $_POST['date_depart'];
    $id_dest = $_POST['id_destination'];
    $hotel = $_POST['id_hotel'];

    if(strlen($titre) < 3) die("Titre invalide");

    $circuit->add($titre,$duree,$prix,$places,$date,$id_dest,$hotel);
    header("Location: $redirect");
    exit();
}

// DELETE
if(isset($_GET['delete'])) {
    $circuit->delete($_GET['delete']);
    header("Location: $redirect");
    exit();
}

// UPDATE
if(isset($_POST['update'])) {
    $id = $_POST['id'];
    $titre = $_POST['titre'];
    $duree = $_POST['duree'];
    $prix = $_POST['prix'];
    $places = $_POST['nb_places'];
    $date = $_POST['date_depart'];
    $id_dest = $_POST['id_destination'];
    $hotel = $_POST['id_hotel'];

    if(strlen($titre) < 3) die("Titre invalide");

    $circuit->update($id, $titre, $duree, $prix, $places, $date, $id_dest, $hotel);
    header("Location: $redirect");
    exit();
}
?>