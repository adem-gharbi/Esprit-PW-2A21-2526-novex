<?php

// 🟢 capture du contenu HTML
ob_start();

?>

<!-- =========================
     TITRE PAGE
========================= -->
<h2>🏨 Nos Hôtels</h2>

<!-- =========================
     TRI TOGGLE (1 bouton)
========================= -->

<?php
// 🔁 alternance asc / desc
$next = (isset($_GET['order']) && $_GET['order'] === 'asc') ? 'desc' : 'asc';
?>

<div style="text-align:center;margin:20px;">

    <!-- ⭐ bouton tri -->
    <a href="index.php?order=<?= $next ?>" class="btn">
        ⭐ Trier par étoiles
    </a>

</div>

<!-- =========================
     GRID HOTELS
========================= -->
<div class="grid">

<?php if(!empty($hotels)): ?>

    <!-- 🔁 boucle hôtels -->
    <?php foreach($hotels as $h): ?>
    <div class="card">

        <!-- 🖼️ image hôtel -->
        <img src="/voyagio_final/assets/<?= strtolower($h['Nom']) ?>.jpg"

             onerror="this.src='/voyagio_final/assets/default.jpg'">

        <div class="card-content">

            <!-- 🏨 nom hôtel -->
            <h3>
                🏨 <?= htmlspecialchars($h['Nom']) ?>
            </h3>

            <!-- 📍 ville -->
            <p>
                📍 <?= htmlspecialchars($h['Ville']) ?>
            </p>

            <!-- ⭐ étoiles -->
            <p>
                ⭐ <?= $h['Etoiles'] ?> étoiles
            </p>

            <!-- 💰 prix -->
            <p class="price">
                💰 <?= $h['Prix'] ?> TND
            </p>

            <!-- 📅 réservation -->
            <a href="reservation.php?hotel_id=<?= (int)$h['Id'] ?>" class="btn">
                📅 Réserver
            </a>

        </div>

    </div>
    <?php endforeach; ?>

<?php else: ?>

    <!-- ❌ aucun hôtel -->
    <p style="text-align:center;">
        Aucun hôtel trouvé
    </p>

<?php endif; ?>

</div>

<?php

// 📦 envoi vers template global
$content = ob_get_clean();
include "template.php";

?>