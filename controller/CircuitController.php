<?php
require_once "../config/database.php";
require_once "../model/Circuit.php";

$db = new Database();
$pdo = $db->connect();

$circuit = new Circuit($pdo);

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

    header("Location: ../view/back/listCircuit.php");
}

// DELETE
if(isset($_GET['delete'])) {

    $circuit->delete($_GET['delete']);

    header("Location: ../view/back/listCircuit.php");
}
?>
