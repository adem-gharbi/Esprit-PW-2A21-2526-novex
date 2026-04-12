<?php include __DIR__ . "/../layout/header.php"; ?>

<div class="hero">
    <h1>🧾 Reservations </h1>
</div>

<div class="hotel-grid">

<?php while($r = $reservations->fetch(PDO::FETCH_ASSOC)) { ?>

<div class="hotel-card">

    <div class="hotel-badge">VOYAGIO</div>

    <h2>🏨 <?= $r['hotel_nom'] ?></h2>

    <p>👤 Client: <?= $r['nom_client'] ?></p>
    <p>👥 Persons: <?= $r['nb_personnes'] ?></p>
    <p>📅 Date: <?= $r['date_reservation'] ?></p>

    <a class="btn-delete"
       href="?delete=<?= $r['id'] ?>"
       onclick="return confirm('Delete this reservation ?')">
       ❌ Delete
    </a>

</div>

<?php } ?>

</div>

<?php include __DIR__ . "/../layout/footer.php"; ?>