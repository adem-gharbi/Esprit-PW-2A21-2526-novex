<?php
require_once "../../config/database.php";
require_once "../../model/Destination.php";

$db = new Database();
$pdo = $db->connect();

$destination = new Destination($pdo);
$destinations = $destination->getAll();

/* =========================
   INITIALIZE VARIABLES
========================= */
$id_circuit = "";
$titre = "";
$duree = "";
$prix = "";
$nb_places = "";
$date_depart = "";
$id_destination = "";
$id_hotel = "";
$destDisplay = ""; 

/* =========================
   MODE EDITION
========================= */
if (isset($_GET['id'])) {
    $id_circuit = $_GET['id'];
    $data = $circuit->getById($id_circuit);

    if ($data) {
        $titre = $data['titre'];
        $duree = $data['duree'];
        $prix = $data['prix'];
        $nb_places = $data['nb_places'];
        $date_depart = $data['date_depart'];
        $id_destination = $data['id_destination'];
        $id_hotel = $data['id_hotel'];

        // Fetch display name for edit mode
        if (!empty($id_destination)) {
            $destInfo = $destination->getById($id_destination);
            if ($destInfo) $destDisplay = $destInfo['ville'] . ' - ' . $destInfo['pays'];
        }
    }
}

/* =========================
   MODE AJOUTER (from listCircuit.php)
========================= */
if (isset($_GET['dest_id']) && is_numeric($_GET['dest_id']) && empty($id_circuit)) {
    $id_destination = $_GET['dest_id'];
    $destInfo = $destination->getById($id_destination);
    if ($destInfo) $destDisplay = $destInfo['ville'] . ' - ' . $destInfo['pays'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($_GET['id']) ? "Modifier Circuit" : "Ajouter Circuit" ?></title>
  <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
<link rel="stylesheet" href="../../../../../public/assets/css/return-dashboard.css">
<a class="voyagio-return-dashboard" href="../../../../../back.php">&larr; Back Dashboard</a>

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
        <a href="listDestination.php" class="menu-item">Destinations</a>
        <a href="listCircuit.php" class="menu-item active">Circuits</a>
      </nav>
    </aside>

    <main class="content">
      <header class="topbar">
        <div>
          <div class="page-title"><?= isset($_GET['id']) ? "Modifier Circuit" : "Ajouter Circuit" ?></div>
          <div class="page-subtitle"><?= isset($_GET['id']) ? "Edit circuit details" : "Create a new circuit" ?></div>
        </div>
        <div class="user-card">
          <span>Admin</span>
          <div class="avatar">AD</div>
        </div>
      </header>

      <section class="panel-card">
        <div class="panel-header">
          <div>
            <h2>Circuit Details</h2>
            <p>Fill in the circuit information below.</p>
          </div>
        </div>

        <?php
        $returnId = "";
        if (isset($_GET['dest_id']) && is_numeric($_GET['dest_id'])) {
            $returnId = $_GET['dest_id'];
        } elseif (!empty($id_destination)) {
            $returnId = $id_destination;
        }
        $returnUrl = "listCircuit.php" . ($returnId ? "?id=" . intval($returnId) : "");
        ?>

        <form class="panel-form" method="POST" action="../../controller/CircuitController.php" onsubmit="return validateCircuit()">
          <input type="hidden" name="id" value="<?= $id_circuit ?>">
          <input type="hidden" name="dest_id" value="<?= $returnId ?>">

          <label>
            Destination
            <input type="text" value="<?= htmlspecialchars($destDisplay) ?>" disabled style="background:rgba(255,255,255,0.08); color:var(--text-muted); cursor:not-allowed;">
            <input type="hidden" name="id_destination" value="<?= $id_destination ?>">
          </label>

          <label>
            Titre
            <input type="text" id="titre" name="titre" value="<?= $titre ?>">
            <span id="errTitre" class="msg"></span>
          </label>

          <label>
            Durée
            <input type="text" id="duree" name="duree" value="<?= $duree ?>">
            <span id="errDuree" class="msg"></span>
          </label>

          <label>
            Prix
            <input type="number" id="prix" name="prix" value="<?= $prix ?>">
            <span id="errPrix" class="msg"></span>
          </label>

          <label>
            Places
            <input type="number" id="nb_places" name="nb_places" value="<?= $nb_places ?>">
            <span id="errPlaces" class="msg"></span>
          </label>

          <label>
            Date de départ
            <input type="date" id="date_depart" name="date_depart" value="<?= $date_depart ?>">
            <span id="errDate" class="msg"></span>
          </label>

          <label>
            Hotel
            <input type="text" id="id_hotel" name="id_hotel" value="<?= $id_hotel ?>">
            <span id="errHotel" class="msg"></span>
          </label>

          <div class="form-actions">
            <button type="submit" name="<?= isset($_GET['id']) ? 'update' : 'add' ?>" class="btn btn-primary">
              <?= isset($_GET['id']) ? 'Modifier' : 'OK' ?>
            </button>
            <a href="<?= $returnUrl ?>" class="btn btn-secondary">Retour</a>
          </div>
        </form>
      </section>
    </main>
  </div>
  <script src="../../public/js/validationCircuit.js"></script>
</body>
</html>