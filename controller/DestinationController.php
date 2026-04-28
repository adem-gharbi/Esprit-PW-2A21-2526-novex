<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Destination.php';

$db = new Database();
$pdo = $db->connect();

$destination = new Destination($pdo);


// ================= ADD =================
if (isset($_POST['add'])) {

    $destination->add(
        $_POST['ville'],
        $_POST['pays'],
        $_POST['description'],
        $_POST['image'],
        $_POST['categorie']
    );

    header("Location: ../view/back/listDestination.php");
    exit();
}


// ================= UPDATE =================
if (isset($_POST['update'])) {

    $sql = "UPDATE destination SET
        ville = ?,
        pays = ?,
        description = ?,
        image = ?,
        categorie = ?
        WHERE id_destination = ?";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $_POST['ville'],
        $_POST['pays'],
        $_POST['description'],
        $_POST['image'],
        $_POST['categorie'],
        $_POST['id']
    ]);

    header("Location: ../view/back/listDestination.php");
    exit();
}


// ================= DELETE =================
if (isset($_GET['delete'])) {

    $destination->delete($_GET['delete']);

    header("Location: ../view/back/listDestination.php");
    exit();
}