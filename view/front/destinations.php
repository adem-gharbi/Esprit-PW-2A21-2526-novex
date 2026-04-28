<!-- FILE: /voyagio/descir/view/front/destinations.php -->
<!-- FULL COPY-PASTE READY VERSION -->

<head>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&family=Playfair+Display&display=swap" rel="stylesheet">
</head>
<?php
require_once "../../config/database.php";
require_once "../../model/Destination.php";

$db = new Database();
$pdo = $db->connect();

$destination = new Destination($pdo);
$data = $destination->getAll();

// ✅ Count circuits per destination (calculated, NOT stored in DB)
$circuitCounts = [];
$stmtCounts = $pdo->prepare("SELECT id_destination, COUNT(*) as nb FROM circuit GROUP BY id_destination");
$stmtCounts->execute();
foreach ($stmtCounts->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $circuitCounts[$row['id_destination']] = $row['nb'];
}
?>

<!-- HERO HEADER (More Transparent) -->
<header class="hero">
    <h1>Nos Destinations ✈️</h1>
</header>

<!-- GRID CONTAINER -->
<div class="grid">

<?php foreach($data as $d): ?>

    <div class="card">
        
        <!-- IMAGE ON TOP -->
        <img src="/voyagio/descir/public/images/<?= $d['image'] ?>"
             alt="<?= $d['ville'] ?>">

        <!-- CONTENT WITH FLEX FOR BUTTON POSITIONING -->
        <div class="card-content">
            <h3><?= $d['ville'] ?> - <?= $d['pays'] ?></h3>

            <!-- Description -->
            <p class="mini-label">Description :</p>
            <p><?= $d['description'] ?></p>

            <!-- Catégorie (Plain Text, No Badge) -->
            <p class="mini-label">Catégorie :</p>
            <p><?= $d['categorie'] ?></p>

            <!-- ✅ Circuit Count Strip (Thin, Full-Width, No Emoji) -->
            <div class="circuit-count-bar">
                <?= $circuitCounts[$d['id_destination']] ?? 0 ?> circuits disponibles
            </div>

            <!-- Button (Always at Bottom) -->
            <a class="btn"
               href="circuits.php?id=<?= $d['id_destination'] ?>">
               Voir les circuits
            </a>
        </div>
        
    </div>
    
<?php endforeach; ?>

</div>