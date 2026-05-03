<?php

//  buffer HTML
ob_start();

?>

<!-- =========================
     TITRE
========================= -->
<h2 style="text-align:center;">
    📅 Réservations
</h2>

<!-- =========================
     FORMULAIRE STYLE SEARCH
========================= -->
<form method="POST" onsubmit="return validate()" class="search-box">

    <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">

    <!-- 🏨 HOTEL -->
    <select name="hotel_id" id="hotel_id">
        <option value="">🏨 Hôtel</option>
        <?php foreach($hotels as $h): ?>
            <option value="<?= $h['Id'] ?>"
                <?= (isset($edit) && $edit['hotel_id'] == $h['Id']) ? 'selected' : '' ?>>
                <?= $h['Nom'] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- 👤 CLIENT -->
    <input name="nom" id="nom"
           placeholder="👤 Nom client"
           value="<?= $edit['nom_client'] ?? '' ?>">

    <!-- 📅 ARRIVEE -->
    <input type="date" name="arrivee" id="arrivee"
           value="<?= $edit['date_arrivee'] ?? '' ?>">

    <!-- 📅 DEPART -->
    <input type="date" name="depart" id="depart"
           value="<?= $edit['date_depart'] ?? '' ?>">

    <!-- 👥 NB -->
    <input name="nb" id="nb"
           placeholder="👥 Personnes"
           value="<?= $edit['nb_personnes'] ?? '' ?>">
    <input name="discount" id="discount"
       placeholder="Discount %"
       value="<?= $edit['discount'] ?? '' ?>">

    <!-- 🔘 BOUTON -->
    <?php if(isset($edit)): ?>
        <button type="submit" name="update" class="btn">
            ✏ Modifier
        </button>
    <?php else: ?>
        <button type="submit" name="add" class="btn">
            ➕ Ajouter
        </button>
    <?php endif; ?>

</form>

<hr>

<!-- =========================
     LISTE
========================= -->
<div class="grid">

<?php if(!empty($reservations)): ?>

    <?php foreach($reservations as $r): ?>

<div class="card">

    <!-- 🎟️ BUTTON QR (ICI DANS LA CARD) -->
    <a href="/voyagio_final/views/front/ticket.php?id=<?= $r['id'] ?>" class="qr-btn">
    🎟️ Voir Ticket
</a>

    <!-- 🖼️ IMAGE -->
    <img src="/voyagio_final/assets/<?= strtolower($r['hotel_nom']) ?>.jpg"
         onerror="this.src='/voyagio_final/assets/default.jpg'">

    <div class="card-content">

        <h3>🏨 <?= $r['hotel_nom'] ?></h3>

        <p>👤 <b><?= $r['nom_client'] ?></b></p>

        <p>📥 Arrivée :
            <?= (!empty($r['date_arrivee']) && $r['date_arrivee'] != '0000-00-00')
                ? date("d/m/Y", strtotime($r['date_arrivee']))
                : '-' ?>
        </p>

        <p>📤 Départ :
            <?= (!empty($r['date_depart']) && $r['date_depart'] != '0000-00-00')
                ? date("d/m/Y", strtotime($r['date_depart']))
                : '-' ?>
        </p>

        <p class="badge">
            👥 <?= $r['nb_personnes'] ?> personnes
        </p>

        <div class="actions">

            <a href="?edit=<?= $r['id'] ?>" class="btn-edit">
                ✏ Modifier
            </a>

            <a href="?delete=<?= $r['id'] ?>"
               class="btn-delete"
               onclick="return confirm('Supprimer cette réservation ?')">
                🗑 Supprimer
            </a>

        </div>

    </div>

</div>

<?php endforeach; ?>

<?php else: ?>

    <p style="text-align:center;">
        Aucune réservation
    </p>

<?php endif; ?>

</div>

<!-- =========================
     VALIDATION JS
========================= -->
<script>
function validate(){

    let hotel = document.getElementById("hotel_id").value;
    let nom = document.getElementById("nom").value;
    let nb = document.getElementById("nb").value;
    let arrivee = document.getElementById("arrivee").value;
    let depart = document.getElementById("depart").value;

    if(hotel === ""){
        alert("Choisissez un hôtel");
        return false;
    }

    if(nom.length < 3){
        alert("Nom invalide");
        return false;
    }

    if(nb <= 0){
        alert("Nombre de personnes invalide");
        return false;
    }

    if(arrivee === "" || depart === ""){
        alert("Dates obligatoires");
        return false;
    }

    if(arrivee > depart){
        alert("Date départ invalide");
        return false;
    }

    return true;
}
</script>

<!-- =========================
     MIN DATE JS
========================= -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    let today = new Date().toISOString().split("T")[0];

    document.getElementById("arrivee").min = today;
    document.getElementById("depart").min = today;

});
</script>

<?php

// 📦 output template
$content = ob_get_clean();
include "template.php";

?>