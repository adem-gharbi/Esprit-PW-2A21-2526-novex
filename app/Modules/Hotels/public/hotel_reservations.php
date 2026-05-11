<?php

// 🔌 connexion base de données (chemin absolu sécurisé)
require_once __DIR__ . "/../config/database.php";

// 🧠 controller réservation
require_once "../controllers/ReservationController.php";

// 🏨 model hôtel (pour récupérer infos hôtel)
require_once "../models/Hotel.php";

// 🏗️ création du controller réservation
$controller = new ReservationController($db);

// 🏗️ création du model hôtel
$hotelModel = new Hotel($db);

// 🎯 récupération ID hôtel depuis URL
$hotel_id = $_GET['id'];

// ⚠️ IMPORTANT : ici tu dois sécuriser l'ID
// $hotel_id = (int) $_GET['id'];

// 🏨 récupération infos hôtel
$hotel = $hotelModel->getById($hotel_id);

// 📅 récupération des réservations liées à cet hôtel
$reservations = $controller->getByHotel($hotel_id);
?>

<?php ob_start(); ?>

<!-- 🟢 HEADER VISUEL HOTEL -->
<div style="text-align:center; margin-bottom:30px;">

    <!-- 🖼️ image hôtel -->
    <img class="hotel-back-img"
         src="/voyagio_final/assets/<?= strtolower($hotel['Nom']) ?>.jpg"
         onerror="this.src='/voyagio_final/assets/default.jpg'">

    <!-- 🏨 nom hôtel -->
    <h1>🏨 <?= $hotel['Nom'] ?></h1>

    <!-- 📍 ville + ⭐ étoiles -->
    <p>📍 <?= $hotel['Ville'] ?> | ⭐ <?= $hotel['Etoiles'] ?></p>

</div>

<!-- 📅 TITRE SECTION -->
<h2 style="text-align:center;">📅 Réservations de cet hôtel</h2>

<!-- 📦 GRID DES RÉSERVATIONS -->
<div class="grid">

<?php if(empty($reservations)): ?>
    <!-- ❌ message si aucune réservation -->
    <p style="text-align:center;">Aucune réservation</p>
<?php endif; ?>

<!-- 🔁 boucle des réservations -->
<?php foreach($reservations as $r): ?>
<div class="card">

    <!-- 👤 client -->
    <h3>👤 <?= $r['nom_client'] ?></h3>

    <!-- 📥 date arrivée -->
    <p>
        📥 Arrivée :
        <?= !empty($r['date_arrivee']) ? date("d/m/Y", strtotime($r['date_arrivee'])) : '-' ?>
    </p>

    <!-- 📤 date départ -->
    <p>
        📤 Départ :
        <?= !empty($r['date_depart']) ? date("d/m/Y", strtotime($r['date_depart'])) : '-' ?>
    </p>

    <!-- 👥 nb personnes -->
    <p class="badge">
        👥 <?= $r['nb_personnes'] ?> personnes
    </p>

</div>
<?php endforeach; ?>

</div>

<!-- 🔙 bouton retour -->
<div style="text-align:center; margin-top:20px;">
    <a href="admin.php" class="btn">⬅ Retour</a>
</div>

<?php
// 📄 injection dans template back
$content = ob_get_clean();
include __DIR__ . "/../views/back/template.php";
?>