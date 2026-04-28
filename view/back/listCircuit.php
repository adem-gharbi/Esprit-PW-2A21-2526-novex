<?php
require_once "../../config/database.php";
require_once "../../model/Circuit.php";

$db = new Database();
$pdo = $db->connect();

$circuit = new Circuit($pdo);
if(isset($_GET['id'])){
    $data = $circuit->getByDestination($_GET['id']);
} else {
    $data = $circuit->getAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Circuits</title>
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
        <a href="listDestination.php" class="menu-item">Destinations</a>
        <a href="listCircuit.php" class="menu-item active">Circuits</a>
      </nav>
    </aside>

    <main class="content">
      <header class="topbar">
        <div>
          <div class="page-title">Circuits</div>
          <div class="page-subtitle">Manage all travel circuits</div>
        </div>
        <div class="user-card">
          <span>Admin</span>
          <div class="avatar">AD</div>
        </div>
      </header>

      <section class="panel-card">
        <div class="panel-header">
          <div>
            <h2>Registered Circuits</h2>
          </div>
          <a href="addCircuit.php?dest_id=<?= isset($_GET['id']) ? intval($_GET['id']) : '' ?>" class="btn btn-primary">+ Ajouter Circuit</a>
        </div>

        <?php if (count($data) > 0) : ?>
          <div class="table-card">
            <table class="records-table">
              <thead>
                <tr>
                  <th>Titre</th>
                  <th>Durée</th>
                  <th>Prix</th>
                  <th>Places</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($data as $c): ?>
                <tr>
                  <td><?= $c['titre'] ?></td>
                  <td><?= $c['duree'] ?></td>
                  <td><?= $c['prix'] ?></td>
                  <td><?= $c['nb_places'] ?></td>
                  <td><?= $c['date_depart'] ?></td>
                  <td>
                    <a class="action-link" href="addCircuit.php?id=<?= $c['id_circuit'] ?>">Modifier</a>
                    <a class="action-link danger" href="../../controller/CircuitController.php?delete=<?= $c['id_circuit'] ?>&dest_id=<?= isset($_GET['id']) ? intval($_GET['id']) : '' ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else : ?>
          <p style="color:var(--text-muted); text-align:center; padding:20px;">No circuits found. <a href="addCircuit.php?dest_id=<?= isset($_GET['id']) ? intval($_GET['id']) : '' ?>" style="color:var(--accent);">Add the first circuit</a>.</p>
        <?php endif; ?>
      </section>
    </main>
  </div>
</body>
</html>