<form method="POST" action="index.php">

<input type="hidden" name="id" value="<?= $hotel['id'] ?>">

<input type="text" name="nom" value="<?= $hotel['nom'] ?>">
<input type="text" name="ville" value="<?= $hotel['ville'] ?>">
<input type="number" name="etoiles" value="<?= $hotel['etoiles'] ?>">
<input type="text" name="prix" value="<?= $hotel['prix'] ?>">

<button name="update">Modifier</button>

</form>