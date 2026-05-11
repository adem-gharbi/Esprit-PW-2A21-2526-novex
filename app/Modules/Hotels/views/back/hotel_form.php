<?php

// =========================
//  BUFFER OUTPUT START
// =========================
ob_start();

?>

<!-- =========================
     TITRE PAGE
     - Change automatiquement selon add / edit
========================= -->
<div class="res-header">

    <div class="res-title">
        <?= isset($hotel) ? "✏️ Edit Hotel" : "🏨 Add Hotel" ?>//voir le variable existe ou no
    </div>

</div>

<!-- =========================
     FORMULAIRE HOTEL
     - create = ajout
     - update = modification
     - validation JS activée
========================= -->
<form method="POST"

    action="?action=<?= isset($hotel) ? 'update&id='.$hotel['Id'] : 'create' ?>"

    onsubmit="return validate()"

    class="panel-form">

    <!-- =========================
         NOM HOTEL
    ========================= -->
    <label>
        🏨 Nom hôtel
        <input name="nom" id="nom"
               value="<?= $hotel['Nom'] ?? '' ?>"
               placeholder="Entrer nom hôtel">
    </label>

    <!-- =========================
         VILLE
    ========================= -->
    <label>
        📍 Ville
        <input name="ville" id="ville"
               value="<?= $hotel['Ville'] ?? '' ?>"
               placeholder="Entrer ville">
    </label>

    <!-- =========================
         ETOILES (1-5)
    ========================= -->
    <label>
        ⭐ Etoiles (1-5)
        <input name="etoiles" id="etoiles"
               value="<?= $hotel['Etoiles'] ?? '' ?>"
               placeholder="Ex: 4">
    </label>

    <!-- =========================
         PRIX
    ========================= -->
    <label>
        💰 Prix
        <input name="prix" id="prix"
               value="<?= $hotel['Prix'] ?? '' ?>"
               placeholder="Prix par nuit">
    </label>

    <!-- =========================
         ACTIONS
    ========================= -->
    <div class="form-actions">

        <!-- SUBMIT -->
        <button type="submit" class="btn">
            💾 Save
        </button>

        <!-- CANCEL -->
        <a href="admin.php" class="btn" style="background:#ddd;color:#333;">
            Cancel
        </a>

    </div>

</form>

<!-- =========================
     VALIDATION JS
     - contrôle côté client
========================= -->
<script>// Vérifie si les informations sont correctes avant ajout.

function validate(){

    // récupération champs
    let nom = document.getElementById("nom").value;
    let ville = document.getElementById("ville").value;
    let etoiles = document.getElementById("etoiles").value;
    let prix = document.getElementById("prix").value;

    // validation nom
    if(nom.length < 3){
        alert("❌ Nom invalide (min 3 caractères)");
        return false;
    }

    // validation ville
    if(ville.length < 3){
        alert("❌ Ville invalide (min 3 caractères)");
        return false;
    }

    // validation étoiles
    if(etoiles < 1 || etoiles > 5){
        alert("❌ Les étoiles doivent être entre 1 et 5");
        return false;
    }

    // validation prix
    if(prix <= 0){
        alert("❌ Prix invalide");
        return false;
    }

    return true;
}

</script>

<?php

// =========================
//  SEND TO TEMPLATE GLOBAL
// =========================
$content = ob_get_clean();
include "template.php";

?>