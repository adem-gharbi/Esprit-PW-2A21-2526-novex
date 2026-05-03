<?php
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../controllers/ReservationController.php";

$controller = new ReservationController($db);

// 🔍 vérifier ID
if (!isset($_GET['id'])) {
    die("❌ ID manquant");
}

$id = $_GET['id'];

// 📌 récupérer réservation
$res = $controller->getById($id);

// ❌ si réservation introuvable
if (!$res) {
    die("❌ Réservation introuvable");
}

// 📦 data QR
$data = "Res#".$res['id'].
" | Client: ".$res['nom_client'].
" | Hotel: ".$res['hotel_id'].
" | ".$res['date_arrivee']." -> ".$res['date_depart'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ticket Réservation</title>
    <style>
        body{
            font-family: Arial;
            text-align: center;
            padding: 20px;
            background: #f5f5f5;
        }

        .ticket{
            background: white;
            padding: 20px;
            width: 350px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }

        img{
            margin-top: 15px;
        }
    </style>
</head>

<body>

<div class="ticket">

    <h2>🎟️ Ticket de Réservation</h2>

    <p>👤 Client : <?= htmlspecialchars($res['nom_client']) ?></p>
    <p>🏨 Hôtel ID : <?= $res['hotel_id'] ?></p>
    <p>📅 Arrivée : <?= $res['date_arrivee'] ?></p>
    <p>📅 Départ : <?= $res['date_depart'] ?></p>
    <p>👥 Personnes : <?= $res['nb_personnes'] ?></p>

    <h3>📱 QR Code</h3>

    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($data) ?>">

</div>

</body>
</html>