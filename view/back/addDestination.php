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
<html lang="en">
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
        <a href="listCircuit.php" class="menu-item">Circuits</a>
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
            <input type="text" id="ville" name="ville" value="<?= $ville ?>">
            <span id="errVille" class="msg"></span>
          </label>

          <label>
            Pays
            <input type="text" id="pays" name="pays" value="<?= $pays ?>">
            <span id="errPays" class="msg"></span>
          </label>

          <label>
            Description
            <textarea id="description" name="description"><?= $description ?></textarea>
            <span id="errDescription" class="msg"></span>
          </label>

          <label>
            Image (nom du fichier)
            <input type="text" id="image" name="image" value="<?= $image ?>">
            <span id="errImage" class="msg"></span>
          </label>

          <label>
            Catégorie
            <input type="text" id="categorie" name="categorie" value="<?= $categorie ?>">
            <span id="errCategorie" class="msg"></span>
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
</body>
</html>