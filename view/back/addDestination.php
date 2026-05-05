<?php
require_once "../../config/database.php";
require_once "../../model/Destination.php";

$db  = new Database();
$pdo = $db->connect();

$destination = new Destination($pdo);

$id          = "";
$ville       = "";
$pays        = "";
$description = "";
$image       = "";
$categorie   = "";
$latitude    = "";
$longitude   = "";

if (isset($_GET['id'])) {
    $id   = $_GET['id'];
    $data = $destination->getById($id);

    if ($data) {
        $ville       = $data['ville'];
        $pays        = $data['pays'];
        $description = $data['description'];
        $image       = $data['image'];
        $categorie   = $data['categorie'];
        $latitude    = $data['latitude']  ?? '';
        $longitude   = $data['longitude'] ?? '';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($_GET['id']) ? "Modifier Destination" : "Ajouter Destination" ?></title>
  <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-icon">VY</div>
      <div>
        <div class="brand-title">Voyagio</div>
        <div class="brand-subtitle">Back Office</div>
      </div>
    </div>
    <nav class="menu">
      <a href="listDestination.php" class="menu-item active">Destinations</a>
      <a href="listCircuit.php"     class="menu-item">Circuits</a>
    </nav>
  </aside>

  <main class="content">
    <header class="topbar">
      <div>
        <div class="page-title"><?= isset($_GET['id']) ? "Modifier Destination" : "Ajouter Destination" ?></div>
        <div class="page-subtitle"><?= isset($_GET['id']) ? "Edit destination details" : "Create a new destination" ?></div>
      </div>
      <div class="user-card">
        <span>Admin</span>
        <div class="avatar">AD</div>
      </div>
    </header>

    <section class="panel-card">
      <div class="panel-header">
        <div>
          <h2>Destination Details</h2>
          <p>Fill in the destination information below.</p>
        </div>
      </div>

      <form class="panel-form" method="POST" action="../../controller/DestinationController.php" onsubmit="return validateDestination()">
        <input type="hidden" name="id" value="<?= $id ?>">

        <label>
          Ville
          <input type="text" id="ville" name="ville" value="<?= htmlspecialchars($ville) ?>">
          <span id="errVille" class="msg"></span>
        </label>

        <label>
          Pays
          <input type="text" id="pays" name="pays" value="<?= htmlspecialchars($pays) ?>">
          <span id="errPays" class="msg"></span>
        </label>

        <label>
          Description
          <textarea id="description" name="description"><?= htmlspecialchars($description) ?></textarea>
          <span id="errDescription" class="msg"></span>
        </label>

        <label>
          Image (nom du fichier)
          <input type="text" id="image" name="image" value="<?= htmlspecialchars($image) ?>">
          <span id="errImage" class="msg"></span>
        </label>

        <label>
          Catégorie
          <input type="text" id="categorie" name="categorie" value="<?= htmlspecialchars($categorie) ?>">
          <span id="errCategorie" class="msg"></span>
        </label>

        <label>
          Latitude (ex: 48.8566)
          <input type="text" id="latitude" name="latitude" value="<?= htmlspecialchars($latitude) ?>" placeholder="ex: 48.8566">
          <span id="errLatitude" class="msg"></span>
        </label>

        <label>
          Longitude (ex: 2.3522)
          <input type="text" id="longitude" name="longitude" value="<?= htmlspecialchars($longitude) ?>" placeholder="ex: 2.3522">
          <span id="errLongitude" class="msg"></span>
        </label>

        <div class="form-actions">
          <button type="submit" name="<?= isset($_GET['id']) ? 'update' : 'add' ?>" class="btn btn-primary">
            <?= isset($_GET['id']) ? 'Modifier' : 'Ajouter' ?>
          </button>
          <a href="listDestination.php" class="btn btn-secondary">Retour</a>
        </div>
      </form>
    </section>
  </main>
</div>
<script src="../../public/js/validation.js"></script>
<script>
// Validation lat/lng (optionnel, format décimal)
document.getElementById("latitude").addEventListener("input", function () {
    const el = document.getElementById("errLatitude");
    if (this.value === "" || /^-?\d{1,3}(\.\d+)?$/.test(this.value.trim())) {
        el.innerHTML = this.value ? "✔ OK" : "";
        el.className = "msg msg-success";
    } else {
        el.innerHTML = "❌ Format invalide (ex: 48.8566)";
        el.className = "msg msg-error";
    }
});
document.getElementById("longitude").addEventListener("input", function () {
    const el = document.getElementById("errLongitude");
    if (this.value === "" || /^-?\d{1,3}(\.\d+)?$/.test(this.value.trim())) {
        el.innerHTML = this.value ? "✔ OK" : "";
        el.className = "msg msg-success";
    } else {
        el.innerHTML = "❌ Format invalide (ex: 2.3522)";
        el.className = "msg msg-error";
    }
});
</script>
</body>
</html>