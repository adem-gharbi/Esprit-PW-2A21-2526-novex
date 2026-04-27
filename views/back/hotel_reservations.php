<?php ob_start(); ?>

<!-- =========================
   🏨 HEADER HOTEL
========================= -->
<div class="hotel-box">

    <h1>🏨 <?= $hotel['nom'] ?></h1>

    <p>
        📍 <?= $hotel['ville'] ?>
        <span class="stars">⭐ <?= $hotel['etoiles'] ?></span>
    </p>

</div>

<!-- =========================
   TITRE
========================= -->
<div class="title-row">
    <h2>📅 Réservations de cet hôtel</h2>

    <a href="admin.php" class="back-btn">⬅ Retour</a>
</div>

<!-- =========================
   RESERVATIONS
========================= -->
<div class="res-wrapper">

<?php foreach($reservations as $r) { ?>

    <div class="res-big-card">

        <!-- CLIENT -->
        <div class="client">
            👤 <?= $r['client'] ?>
        </div>

        <!-- INFOS GRID -->
        <div class="info-grid">

            <div class="box">
                📥 <br>
                <b>Arrivée</b><br>
                <?= $r['date_arrive'] ?>
            </div>

            <div class="box">
                📤 <br>
                <b>Départ</b><br>
                <?= $r['date_depart'] ?>
            </div>

            <div class="box people">
                👥 <br>
                <b><?= $r['personnes'] ?></b><br>
                personnes
            </div>

        </div>

    </div>

<?php } ?>

</div>

<?php
$content = ob_get_clean();
include "template.php";
?>