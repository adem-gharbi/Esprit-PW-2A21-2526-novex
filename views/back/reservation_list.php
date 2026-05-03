<?php
require_once "../config/database.php";
require_once "../controllers/ReservationController.php";

$controller = new ReservationController($db);

$search = $_GET['search'] ?? "";

$stats = [];

$reservations = $controller->index();
$noResult = false;

// 🔍 SEARCH
if (!empty($search)) {
    $result = $controller->search($search);

    if (!empty($result)) {
        $reservations = $result;
    } else {
        $noResult = true;
    }
}

// 📊 STATS
if (isset($_GET['show_stat'])) {
    $stats = $controller->statsByHotel();
}

// 📊 PREPARE CHART DATA
$labels = [];
$data = [];

if (!empty($stats)) {
    foreach ($stats as $s) {
        $labels[] = $s['hotel_nom'] . " (" . $s['percentage'] . "%)";
        $data[]   = $s['percentage'];
    }
}

ob_start();
?>

<!-- ========================= HEADER ========================= -->
<div class="res-header">

    <h2>📅 Liste des réservations</h2>

    <!-- SEARCH -->
    <form method="GET">
        <input type="text" name="search" placeholder="🔍 Rechercher client..." value="<?= htmlspecialchars($search) ?>">
        <button>Rechercher</button>
    </form>

    <!-- STATS BUTTON -->
    <a href="?show_stat=1" class="stat-btn">
    📊 Statistiques
</a>

</div>

<!-- ========================= MESSAGE ========================= -->
<?php if ($noResult): ?>
    <p style="color:red;text-align:center;">
        ❌ Aucun résultat
    </p>
<?php endif; ?>

<!-- ========================= LIST ========================= -->
<div class="res-grid">

<?php foreach ($reservations as $r): ?>

    <div class="res-card">

        <h3>🏨 <?= htmlspecialchars($r['hotel_nom']) ?></h3>

        <p>👤 <?= htmlspecialchars($r['nom_client']) ?></p>

        <p>📥 <?= date("d/m/Y", strtotime($r['date_arrivee'])) ?></p>
        <p>📤 <?= date("d/m/Y", strtotime($r['date_depart'])) ?></p>

        <p>👥 <?= $r['nb_personnes'] ?> personnes</p>

        <p>ID #<?= $r['id'] ?></p>

    </div>

<?php endforeach; ?>

</div>

<!-- ========================= CHART ========================= -->
<?php if (!empty($stats)): ?>

<h2 style="text-align:center; margin-top:40px;">
    📊 Réservations en pourcentage
</h2>

<div style="width:400px; margin:40px auto;">
    <canvas id="hotelChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('hotelChart');

new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            data: <?= json_encode($data) ?>,
            backgroundColor: [
                '#ff6384',
                '#36a2eb',
                '#ffce56',
                '#4bc0c0',
                '#9966ff',
                '#ff9f40'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + " : " + context.raw + "%";
                    }
                }
            }
        }
    }
});
</script>

<?php endif; ?>

<?php
$content = ob_get_clean();
include "../views/back/template.php";
?>