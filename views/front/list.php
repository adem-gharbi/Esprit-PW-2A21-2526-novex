<!-- HERO -->
<div class="hero">
    <h1>✈ Discover Our Best Hotels</h1>
</div>

<h1 class="text-center">Hotels</h1>

<div class="hotel-grid">

<?php while($h = $hotels->fetch(PDO::FETCH_ASSOC)) { ?>

<div class="hotel-card">

    <div class="hotel-badge">HOTEL</div>

    <h2>🏨 <?= $h['Nom'] ?></h2>

    <div class="info">
        <p>📍 <?= $h['Ville'] ?></p>
        <p>⭐ <?= $h['Etoiles'] ?> Stars</p>
        <p class="price">💰 <?= $h['Prix'] ?> DT / night</p>
    </div>

    <a href="reservation.php?hotel=<?= $h['Id'] ?>" class="btn">
        Book Now →
    </a>

</div>

<?php } ?>

</div>