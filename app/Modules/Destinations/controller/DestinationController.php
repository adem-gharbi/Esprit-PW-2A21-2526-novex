<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Destination.php';

$db  = new Database();
$pdo = $db->connect();

$destination = new Destination($pdo);

// ================= ADD =================
if (isset($_POST['add'])) {
    $lat = isset($_POST['latitude'])  && $_POST['latitude']  !== '' ? (float)$_POST['latitude']  : null;
    $lng = isset($_POST['longitude']) && $_POST['longitude'] !== '' ? (float)$_POST['longitude'] : null;

    $destination->add(
        $_POST['ville'],
        $_POST['pays'],
        $_POST['description'],
        $_POST['image'],
        $_POST['categorie'],
        $lat,
        $lng
    );

    header("Location: ../view/back/listDestination.php");
    exit();
}

// ================= UPDATE =================
if (isset($_POST['update'])) {
    $lat = isset($_POST['latitude'])  && $_POST['latitude']  !== '' ? (float)$_POST['latitude']  : null;
    $lng = isset($_POST['longitude']) && $_POST['longitude'] !== '' ? (float)$_POST['longitude'] : null;

    $destination->update(
        $_POST['id'],
        $_POST['ville'],
        $_POST['pays'],
        $_POST['description'],
        $_POST['image'],
        $_POST['categorie'],
        $lat,
        $lng
    );

    header("Location: ../view/back/listDestination.php");
    exit();
}

// ================= DELETE =================
if (isset($_GET['delete'])) {
    $destination->delete($_GET['delete']);
    header("Location: ../view/back/listDestination.php");
    exit();
}
?>