<?php

//  capture du HTML
ob_start();

?>

<!-- 🏨 TITRE PAGE -->
<h2 style="text-align:center;">🏨 Gestion Hotels</h2>

<!-- =========================
     TABLE DES HOTELS
========================= -->
<table class="table">

    <!--  HEADER TABLE -->
    <tr>
        <th>Nom</th>
        <th>Ville</th>
        <th>Étoiles</th>
        <th>Prix</th>
        <th>Actions</th>
    </tr>

    <!--  boucle hôtels -->
    <?php foreach($hotels as $h): ?>
    <tr>

        <!-- 🏨 NOM + lien vers réservations -->
        <td>
            <a href="hotel_reservations.php?id=<?= $h['Id'] ?>"

               style="text-decoration:none;
                      color:#2c3e50;
                      font-weight:bold;">

                🏨 <?= $h['Nom'] ?>

            </a>
        </td>

        <!-- 📍 ville -->
        <td>📍 <?= $h['Ville'] ?></td>

        <!-- ⭐ étoiles -->
        <td>
            <span class="stars">
                ⭐ <?= $h['Etoiles'] ?>
            </span>
        </td>

        <!-- 💰 prix -->
        <td>
            <b><?= $h['Prix'] ?> TND</b>
        </td>

        <!--  actions -->
        <td>

            <!-- ✏️ edit -->
            <a href="?action=edit&id=<?= $h['Id'] ?>" class="btn-edit">
                ✏ Edit
            </a>

            <!-- 🗑 delete -->
            <a href="?action=delete&id=<?= $h['Id'] ?>" class="btn-delete"
               onclick="return confirm('Supprimer cet hôtel ?')">
                 Delete
            </a>

        </td>

    </tr>
    <?php endforeach; ?>

</table>

<?php

//  envoi au template global
$content = ob_get_clean();
include "template.php";

?>