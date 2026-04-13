<?php
require_once "../../config/database.php";

$db = new Database();
$pdo = $db->connect();

$id = "";
$ville = "";
$pays = "";
$description = "";
$image = "";
$categorie = "";

/* =========================
   MODE EDITION
========================= */
if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM destination WHERE id_destination = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        $ville = $data['ville'];
        $pays = $data['pays'];
        $description = $data['description'];
        $image = $data['image'];
        $categorie = $data['categorie'];
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Destination</title>

    <link rel="stylesheet" href="../../public/css/style.css">

    <style>
        .msg {
            font-size: 12px;
            margin-top: 3px;
            display: block;
        }

        .msg-error {
            color: red;
        }

        .msg-success {
            color: green;
        }

        form {
            width: 50%;
            margin: auto;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }

        button {
            margin-top: 15px;
        }
    </style>
</head>

<body>

<header>
    <?= isset($_GET['id']) ? "Modifier Destination" : "Ajouter Destination" ?>
</header>

<a href="listDestination.php" class="btn">⬅ Retour</a>

<form method="POST"
      action="../../controller/DestinationController.php"
      onsubmit="return validateDestination()">

    <input type="hidden" name="id" value="<?= $id ?>">

    <!-- VILLE -->
    <input type="text" id="ville" name="ville" placeholder="Ville" value="<?= $ville ?>">
    <span id="errVille" class="msg"></span>

    <!-- PAYS -->
    <input type="text" id="pays" name="pays" placeholder="Pays" value="<?= $pays ?>">
    <span id="errPays" class="msg"></span>

    <!-- DESCRIPTION -->
    <textarea id="description" name="description" placeholder="Description"><?= $description ?></textarea>
    <span id="errDescription" class="msg"></span>

    <!-- IMAGE -->
    <input type="text" id="image" name="image" placeholder="Image (ex: paris.jpg)" value="<?= $image ?>">
    <span id="errImage" class="msg"></span>

    <!-- CATEGORIE -->
    <input type="text" id="categorie" name="categorie" placeholder="Catégorie" value="<?= $categorie ?>">
    <span id="errCategorie" class="msg"></span>

    <!-- BUTTON -->
    <button type="submit" name="<?= isset($_GET['id']) ? 'update' : 'add' ?>">
        OK
    </button>

</form>

<script src="../../public/js/validation.js"></script>

</body>
</html>