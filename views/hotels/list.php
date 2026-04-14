<?php include __DIR__ . "/../layout/header.php"; ?>

<div class="hero">
    <h1>🏨 Hotels </h1>
</div>

<!-- ================= FORM ================= -->
<div class="card" style="width:60%;margin:auto;">

<h2 class="text-center">
    <?= isset($edit) ? "✏ Modifier Hotel" : "➕ Ajouter Hotel" ?>
</h2>

<form method="POST" action="index.php">

<input type="hidden" name="Id" value="<?= $edit['Id'] ?? '' ?>">

<input type="text" name="Nom" placeholder="Nom"
       value="<?= $edit['Nom'] ?? '' ?>">

<input type="text" name="Ville" placeholder="Ville"
       value="<?= $edit['Ville'] ?? '' ?>">

<input type="number" name="Etoiles" placeholder="Étoiles"
       value="<?= $edit['Etoiles'] ?? '' ?>">

<input type="text" name="Prix" placeholder="Prix"
       value="<?= $edit['Prix'] ?? '' ?>">

<?php if(isset($edit) && $edit): ?>
    <button class="btn-edit" name="update">✔ Modifier</button>
<?php else: ?>
    <button class="btn-add" name="add">➕ Ajouter</button>
<?php endif; ?>

</form>

</div>

<!-- ================= LIST ================= -->
<div class="hotel-grid">

<?php while($h = $hotels->fetch(PDO::FETCH_ASSOC)) { ?>

<div class="hotel-card">

    <div class="hotel-badge">HOTEL</div>

    <h2>🏨 <?= $h['Nom'] ?></h2>

    <p>📍 <?= $h['Ville'] ?></p>
    <p>⭐ <?= $h['Etoiles'] ?> Stars</p>
    <p class="price">💰 <?= $h['Prix'] ?> DT</p>

    <div style="margin-top:10px;">

        <a class="btn-edit"
           href="index.php?edit=<?= $h['Id'] ?>">
           ✏ Modifier
        </a>

        <a class="btn-delete"
           href="index.php?delete=<?= $h['Id'] ?>"
           onclick="return confirm('Supprimer ce hotel ?')">
           🗑 Supprimer
        </a>

    </div>

</div>

<?php } ?>

</div>

<script>
document.querySelector("form").addEventListener("submit", function(e){

    let etoiles = document.querySelector("[name='Etoiles']").value;
    etoiles = parseInt(etoiles);

    if(etoiles > 5 || etoiles < 0){
        alert("❌ Stars must be between 0 and 5");
        e.preventDefault();
    }

});
</script>

<?php include __DIR__ . "/../layout/footer.php"; ?>