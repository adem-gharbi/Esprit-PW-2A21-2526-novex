<head>
    <link rel="stylesheet" href="../../public/css/style.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins&family=Playfair+Display&display=swap" rel="stylesheet">
</head>
<?php
require_once "../../config/database.php";
require_once "../../model/Destination.php";

$db = new Database();
$pdo = $db->connect();

$destination = new Destination($pdo);
$data = $destination->getAll();
?>

<h1>Nos Destinations ✈️</h1>

<div style="display:flex; flex-wrap:wrap; gap:20px;">

<?php foreach($data as $d): ?>

    <div style="
        border:1px solid #ccc;
        padding:15px;
        width:250px;
        border-radius:10px;
        background:#f9f9f9;
    ">

        <img src="../../public/images/<?= $d['image'] ?>"
             width="100%"
             height="150"
             style="object-fit:cover; border-radius:10px;">

        <h3><?= $d['ville'] ?> - <?= $d['pays'] ?></h3>

        <p><?= $d['description'] ?></p>

        <small><?= $d['categorie'] ?></small>

    </div>

<?php endforeach; ?>

</div>