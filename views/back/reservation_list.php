<?php
// =========================
// 🔌 CONNEXION BASE DE DONNÉES
// =========================
require_once "../config/database.php";

// =========================
// 📦 CONTROLLER RÉSERVATION
// =========================
require_once "../controllers/ReservationController.php";

// instance controller
$controller = new ReservationController($db);

// =========================
// 🔍 RÉCUPÉRATION SEARCH
// =========================
$search = $_GET['search'] ?? "";

// =========================
// 📋 PAR DÉFAUT : TOUTES LES RÉSERVATIONS
// =========================
$reservations = $controller->index();

// flag si aucun résultat
$noResult = false;

// =========================
// 🔍 SI RECHERCHE ACTIVE
// =========================
if (!empty($search)) {

    // appel fonction search
    $result = $controller->search($search);

    // si résultat trouvé
    if (!empty($result)) {
        $reservations = $result;
    } else {
        $noResult = true;
    }
}

// =========================
// 🟡 START HTML
// =========================
ob_start();
?>

<!-- =========================
     HEADER PAGE
========================= -->
<div class="res-header">

    <div class="res-title">
        📅 Liste des réservations
    </div>

    <!-- 🔍 SEARCH FORM -->
    <form class="search-box" method="GET">

        <input type="text"
               name="search"
               placeholder="🔍 Rechercher client..."
               value="<?= htmlspecialchars($search) ?>">

        <button type="submit">Rechercher</button>

    </form>

</div>

<!-- =========================
     MESSAGE SI VIDE
========================= -->
<?php if ($noResult): ?>
    <p style="color:red;text-align:center;font-weight:600;">
        ❌ Aucun résultat pour "<?= htmlspecialchars($search) ?>"
    </p>
<?php endif; ?>

<!-- =========================
     GRID RESERVATIONS
========================= -->
<div class="res-grid">

<?php foreach ($reservations as $r): ?>

    <div class="res-card">

        <!-- 🏨 HOTEL -->
        <div class="res-hotel">
            🏨 <?= htmlspecialchars($r['hotel_nom'] ?? $r['hotel'] ?? '') ?>
        </div>

        <!-- 📍 VILLE + ⭐ -->
        <div class="res-location">
            📍 <?= htmlspecialchars($r['ville'] ?? $r['Ville'] ?? '') ?>
            |
            <span class="star">
                ⭐ <?= htmlspecialchars($r['etoiles'] ?? $r['Etoiles'] ?? '') ?>
            </span>
        </div>

        <!-- 👤 CLIENT -->
        <div class="res-info">

            👤 <?= htmlspecialchars($r['nom_client'] ?? $r['client'] ?? '') ?>
            <br><br>

            📥 Arrivée :
            <?= !empty($r['date_arrivee'] ?? $r['date_arrive'])
                ? date("d/m/Y", strtotime($r['date_arrivee'] ?? $r['date_arrive']))
                : '-' ?>
            <br>

            📤 Départ :
            <?= !empty($r['date_depart'])
                ? date("d/m/Y", strtotime($r['date_depart']))
                : '-' ?>
            <br><br>

            👥 <?= htmlspecialchars($r['nb_personnes'] ?? $r['personnes'] ?? 0) ?> personnes

        </div>

        <!-- 🆔 ID -->
        <div class="res-id">
            ID #<?= htmlspecialchars($r['id']) ?>
        </div>

    </div>

<?php endforeach; ?>

</div>

<?php
// =========================
// 📤 ENVOI TEMPLATE
// =========================
$content = ob_get_clean();
include "../views/back/template.php";
?>