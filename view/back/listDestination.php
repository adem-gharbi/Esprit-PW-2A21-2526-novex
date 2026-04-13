<?php
require_once "../../config/database.php";
require_once "../../model/Destination.php";

$db = new Database();
$pdo = $db->connect();

$destination = new Destination($pdo);
$data = $destination->getAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>BackOffice - Destinations</title>

    <link rel="stylesheet" href="../../public/css/style.css">

    <style>
        table {
            width: 85%;
            margin: 30px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        th {
            background: #E8CFC1;
            color: #3A3A3A;
            padding: 10px;
        }

        td {
            padding: 10px;
            text-align: center;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        img {
            border-radius: 8px;
        }

        .btn-small {
            display: inline-block;
            padding: 5px 8px;
            font-size: 12px;
            border-radius: 6px;
            text-decoration: none;
            color: white;
            margin: 2px;
        }

        .btn-add {
            display: block;
            width: fit-content;
            margin: 20px auto;
        }

        .edit {
            background: #A67B5B;
        }

        .delete {
            background: #e74c3c;
        }

        .circuit {
            background: #9CAF88;
        }
    </style>
</head>

<body>

<header>
    Gestion des Destinations
</header>

<a href="addDestination.php" class="btn btn-add">+ Ajouter Destination</a>

<table>

    <tr>
        <th>ID</th>
        <th>Ville</th>
        <th>Pays</th>
        <th>Description</th>
        <th>Image</th>
        <th>Catégorie</th>
        <th>Actions</th>
    </tr>

    <?php foreach($data as $d): ?>
    <tr>

        <td><?= $d['id_destination'] ?></td>
        <td><?= $d['ville'] ?></td>
        <td><?= $d['pays'] ?></td>
        <td><?= $d['description'] ?></td>

        <td>
            <img src="../../public/images/<?= $d['image'] ?>" width="70">
        </td>

        <td><?= $d['categorie'] ?></td>

        <td>

            <!-- ✏️ Modifier -->
            <a class="btn-small edit"
               href="addDestination.php?id=<?= $d['id_destination'] ?>">
               Modifier
            </a>

            <!-- 🗑️ Supprimer -->
            <a class="btn-small delete"
               href="../../controller/DestinationController.php?delete=<?= $d['id_destination'] ?>"
               onclick="return confirm('Supprimer cette destination ?')">
               Supprimer
            </a>

            <br>

            <!-- 🌍 Voir circuits -->
            <a class="btn-small circuit"
               href="../front/circuits.php?id=<?= $d['id_destination'] ?>">
               Voir Circuits
            </a>

        </td>

    </tr>
    <?php endforeach; ?>

</table>

</body>
</html>