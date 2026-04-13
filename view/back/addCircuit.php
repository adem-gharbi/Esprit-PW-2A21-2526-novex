<?php
require_once "../../config/database.php";
require_once "../../model/Destination.php";

$db = new Database();
$pdo = $db->connect();

$destination = new Destination($pdo);
$destinations = $destination->getAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Circuit</title>

    <link rel="stylesheet" href="../../public/css/style.css">

    <style>
        form {
            width: 50%;
            margin: auto;
        }

        .msg {
            font-size: 12px;
            margin-bottom: 8px;
            display: block;
        }

        .msg-error { color: red; }
        .msg-success { color: green; }
    </style>
</head>

<body>

<header>Ajouter Circuit</header>

<a href="listCircuit.php" class="btn">⬅ Retour</a>

<form method="POST"
      action="../../controller/CircuitController.php"
      onsubmit="return validateCircuit()">

    <!-- TITRE -->
    <input type="text" id="titre" name="titre" placeholder="Titre">
    <span id="errTitre" class="msg"></span>

    <!-- DUREE -->
    <input type="text" id="duree" name="duree" placeholder="Durée">
    <span id="errDuree" class="msg"></span>

    <!-- PRIX -->
    <input type="number" id="prix" name="prix" placeholder="Prix">
    <span id="errPrix" class="msg"></span>

    <!-- PLACES -->
    <input type="number" id="nb_places" name="nb_places" placeholder="Places">
    <span id="errPlaces" class="msg"></span>

    <!-- DATE -->
    <input type="date" id="date_depart" name="date_depart">
    <span id="errDate" class="msg"></span>

    <!-- DESTINATION -->
    <select id="id_destination" name="id_destination">
        <option value="">-- Destination --</option>
        <?php foreach($destinations as $d): ?>
            <option value="<?= $d['id_destination'] ?>">
                <?= $d['ville'] ?>
            </option>
        <?php endforeach; ?>
    </select>
    <span id="errDest" class="msg"></span>

    <!-- HOTEL -->
    <input type="text" id="id_hotel" name="id_hotel" placeholder="Hotel">
    <span id="errHotel" class="msg"></span>

    <button type="submit" name="add">OK</button>

</form>

<script src="../../public/js/validationCircuit.js"></script>

</body>
</html>