<?php
require_once "../../config/database.php";
require_once "../../model/Destination.php";

$db = new Database();
$pdo = $db->connect();

$destination = new Destination($pdo);
$data = $destination->getAll();

// Count circuits per destination (calculated, NOT stored in DB)
$circuitCounts = [];
$stmtCounts = $pdo->prepare("SELECT id_destination, COUNT(*) as nb FROM circuit GROUP BY id_destination");
$stmtCounts->execute();
foreach ($stmtCounts->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $circuitCounts[$row['id_destination']] = $row['nb'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BackOffice - Destinations</title>
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
          <div class="page-title">Gestion des Destinations</div>
          <div class="page-subtitle">Manage all travel destinations</div>
        </div>
        <div class="user-card">
          <span>Admin</span>
          <div class="avatar">AD</div>
        </div>
      </header>

      <section class="panel-card">
        <div class="panel-header">
          <div>
            <h2>Registered Destinations</h2>
          </div>
          <a href="addDestination.php" class="btn btn-primary">+ Ajouter Destination</a>
        </div>

        <?php if (count($data) > 0) : ?>
          <div class="table-card">
            <table class="records-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Ville</th>
                  <th>Pays</th>
                  <th>Description</th>
                  <th>Nombre de circuits</th>
                  <th>Image</th>
                  <th>Catégorie</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($data as $d): ?>
                <tr>
                  <td><?= $d['id_destination'] ?></td>
                  <td><?= $d['ville'] ?></td>
                  <td><?= $d['pays'] ?></td>
                  <td><?= $d['description'] ?></td>
                  <td style="text-align:center; font-weight:600; color:var(--brown);"><?= $circuitCounts[$d['id_destination']] ?? 0 ?></td>
                  <td><img src="../../public/images/<?= $d['image'] ?>" width="70" style="border-radius:8px;"></td>
                  <td><?= $d['categorie'] ?></td>
                  <td>
                    <a class="action-link" href="addDestination.php?id=<?= $d['id_destination'] ?>">Modifier</a>
                    <a class="action-link danger" href="../../controller/DestinationController.php?delete=<?= $d['id_destination'] ?>" onclick="return confirm('Supprimer cette destination ?')">Supprimer</a>
                    <br>
                    <a class="action-link" style="margin-top:6px; display:inline-block;" href="listCircuit.php?id=<?= $d['id_destination'] ?>">Gérer les circuits</a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else : ?>
          <p style="color:var(--text-muted); text-align:center; padding:20px;">No destinations found. <a href="addDestination.php" style="color:var(--accent);">Add the first destination</a>.</p>
        <?php endif; ?>
      </section>
    </main>
  </div>
</body>
</html>