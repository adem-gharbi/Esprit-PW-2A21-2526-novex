<?php
require_once "../../config/database.php";
require_once "../../model/Circuit.php";

$db = new Database();
$pdo = $db->connect();

$circuit = new Circuit($pdo);

// récupérer id destination depuis URL
$id = $_GET['id'] ?? 0;

// circuits liés à la destination
$data = $circuit->getByDestination($id);
?>

<link rel="stylesheet" href="../../public/css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&family=Playfair+Display&display=swap" rel="stylesheet">

<!-- HERO HEADER (Friend's Style) -->
<header class="hero">
    <h1>Circuits disponibles 🎒</h1>
</header>

<!-- RETOUR BUTTON -->
<div style="text-align:center; margin: 20px 0;">
    <a href="destinations.php" class="btn">⬅ Retour aux destinations</a>
</div>

<!-- GRID CONTAINER -->
<div class="grid">

<?php foreach($data as $c): ?>

    <div class="card">
        
        <!-- CONTENT -->
        <div class="card-content">
            <h3><?= $c['titre'] ?></h3>

            <p><strong>Durée:</strong> <?= $c['duree'] ?> jours</p>
            <p><strong>Prix:</strong> <?= $c['prix'] ?> TND</p>
            <p><strong>Places:</strong> <?= $c['nb_places'] ?></p>
            <p><strong>Date:</strong> <?= $c['date_depart'] ?></p>
        </div>

    </div>

<?php endforeach; ?>

</div>