<?php
require_once "../../config/database.php";
require_once "../../model/Circuit.php";

$db = new Database();
$pdo = $db->connect();

$circuit = new Circuit($pdo);
$data = $circuit->getAll();
?>

<link rel="stylesheet" href="../../public/css/style.css">

<h1>Circuits</h1>

<a href="addCircuit.php" class="btn">+ Ajouter Circuit</a>

<table border="1" width="90%" style="margin:auto">

<tr>
<th>Titre</th>
<th>Durée</th>
<th>Prix</th>
<th>Places</th>
<th>Date</th>
<th>Actions</th>
</tr>

<?php foreach($data as $c): ?>
<tr>

<td><?= $c['titre'] ?></td>
<td><?= $c['duree'] ?></td>
<td><?= $c['prix'] ?></td>
<td><?= $c['nb_places'] ?></td>
<td><?= $c['date_depart'] ?></td>

<td>

<!-- Modifier -->
<a class="btn" href="addCircuit.php?id=<?= $c['id_circuit'] ?>">
Modifier
</a>

<!-- Supprimer -->
<a class="btn" href="../../controller/CircuitController.php?delete=<?= $c['id_circuit'] ?>"
onclick="return confirm('Supprimer ?')">
Supprimer
</a>

</td>

</tr>
<?php endforeach; ?>

</table>