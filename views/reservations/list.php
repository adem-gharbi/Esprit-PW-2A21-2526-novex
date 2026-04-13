<?php include __DIR__ . "/../layout/header.php"; ?>

<div class="hero">
    <h1>🧾 Choisir un hôtel</h1>
</div>

<h1 class="text-center">Reservations</h1>

<!-- ================= FORM ================= -->
<div class="card" style="width:60%;margin:auto;">

<form method="POST">

    <label>🏨 Choose Hotel</label>
    <select name="hotel_id">

        <option value="">-- Select Hotel --</option>

        <?php while($h = $hotelsList->fetch(PDO::FETCH_ASSOC)) { ?>
            <option value="<?= $h['Id'] ?>">
                <?= $h['Nom'] ?>
            </option>
        <?php } ?>

    </select>

    <input type="text" name="nom_client" placeholder="👤 Client Name">
    <input type="date" name="date_reservation" min="<?= date('Y-m-d') ?>">
    <input type="number" name="nb_personnes" placeholder="👥 Persons">

    <button class="btn-add" type="submit" name="add">
        ➕ Add Reservation
    </button>

</form>

</div>

<!-- ================= LIST ================= -->
<div class="hotel-grid">

<?php while($r = $reservations->fetch(PDO::FETCH_ASSOC)) { ?>

<div class="hotel-card">

    <div class="hotel-badge">RESERVATION</div>

    <h2>🏨 <?= $r['hotel_nom'] ?></h2>

    <p>👤 <?= $r['nom_client'] ?></p>
    <p>📅 <?= $r['date_reservation'] ?></p>
    <p>👥 <?= $r['nb_personnes'] ?> persons</p>

    <div style="margin-top:10px;">

        <a href="?edit=<?= $r['id'] ?>" class="btn-edit">
            ✏ Modifier
        </a>

        <a href="?delete=<?= $r['id'] ?>"
           class="btn-delete"
           onclick="return confirm('Delete this reservation?')">
            ❌ Delete
        </a>

    </div>

</div>

<?php } ?>

</div>
<script>
document.querySelector("form").addEventListener("submit", function(e){

    let date = document.querySelector("[name='date_reservation']").value;

    let today = new Date();
    today.setHours(0,0,0,0);

    let selectedDate = new Date(date);

    if(selectedDate < today){
        alert("❌ Date cannot be before today");
        e.preventDefault();
    }

});
</script>
<?php include __DIR__ . "/../layout/footer.php"; ?>