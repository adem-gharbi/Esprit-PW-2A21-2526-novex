<?php
require_once "../../config/database.php";
require_once "../../model/Destination.php";
require_once "../../model/Circuit.php";

$db  = new Database();
$pdo = $db->connect();

$destination = new Destination($pdo);
$circuitModel = new Circuit($pdo);

$data          = $destination->getAll();
$circuitCounts = $circuitModel->countByDestination();
$notifications = $circuitModel->getNotifications();
$stats         = $circuitModel->getStats();

$nbNotifs = count($notifications);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Voyagio — Back Office — Destinations</title>
  <link rel="stylesheet" href="../../public/css/style.css">
  <style>
    /* ===== NOTIFICATIONS ===== */
    .notif-btn {
      position:relative; background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.3);
      color:#fff; border-radius:14px; padding:12px 18px; cursor:pointer;
      font-size:.95rem; transition:.2s; display:flex; align-items:center; gap:8px;
      margin-top:auto;
    }
    .notif-btn:hover { background:rgba(255,255,255,.25); }
    .notif-badge {
      position:absolute; top:-6px; right:-6px;
      background:#c0392b; color:#fff; font-size:.7rem; font-weight:700;
      border-radius:50%; width:20px; height:20px; display:grid; place-items:center;
    }
    .notif-panel {
      display:none; position:fixed; top:0; right:0; width:360px; height:100vh;
      background:#fff; box-shadow:-4px 0 24px rgba(0,0,0,.15); z-index:1000;
      flex-direction:column; overflow:hidden;
    }
    .notif-panel.open { display:flex; }
    .notif-header {
      background:var(--brown); color:#fff; padding:20px 24px;
      display:flex; justify-content:space-between; align-items:center;
    }
    .notif-header h3 { margin:0; font-family:'Playfair Display',serif; font-size:1.1rem; }
    .notif-close { background:none; border:none; color:#fff; font-size:1.4rem; cursor:pointer; line-height:1; }
    .notif-list { flex:1; overflow-y:auto; padding:16px; display:flex; flex-direction:column; gap:10px; }
    .notif-item {
      padding:12px 14px; border-radius:12px; font-size:.85rem; line-height:1.5;
      border-left:4px solid;
    }
    .notif-item.expired  { background:#fdf0ef; border-color:#c0392b; color:#7f1f14; }
    .notif-item.soon     { background:#fff8e6; border-color:#e67e22; color:#7a4806; }
    .notif-item.full     { background:#fff0f0; border-color:#e74c3c; color:#7a1e1e; }
    .notif-item.empty    { background:#f5f5f5; border-color:#aaa; color:#555; }
    .notif-empty { text-align:center; color:var(--text-muted); padding:30px; font-size:.9rem; }

    /* ===== STATS ===== */
    .stats-section { margin-top:32px; }
    .stats-section h2 { font-family:'Playfair Display',serif; color:var(--brown); font-size:1.2rem; margin:0 0 18px; }
    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px; }
    .stat-card {
      background:var(--nude); border-radius:16px; padding:18px 20px;
      border:1.5px solid rgba(166,123,91,.2); text-align:center;
    }
    .stat-card .stat-val { font-size:1.8rem; font-weight:700; color:var(--brown); font-family:'Playfair Display',serif; }
    .stat-card .stat-label { font-size:.78rem; color:var(--text-muted); margin-top:4px; }
    .stat-card.green .stat-val { color:#5a7350; }
    .stat-card.red   .stat-val { color:#c0392b; }

    .dest-stats-table { width:100%; border-collapse:collapse; margin-top:8px; }
    .dest-stats-table th, .dest-stats-table td { padding:10px 14px; text-align:left; border-bottom:1px solid var(--border); font-size:.85rem; }
    .dest-stats-table th { background:var(--nude); color:var(--dark); font-weight:600; font-size:.78rem; text-transform:uppercase; letter-spacing:.04em; }
    .dest-stats-table tbody tr:hover { background:var(--accent-soft); }
    .dest-stats-table .nb-badge { background:var(--brown); color:#fff; border-radius:8px; padding:2px 10px; font-size:.8rem; font-weight:600; }

    /* EXPORT BTN */
    .btn-export-pdf {
      background:linear-gradient(135deg,var(--green),#7d9665); color:#fff;
      border:none; border-radius:12px; padding:11px 20px;
      font-size:.88rem; font-weight:600; cursor:pointer; transition:.2s;
      text-decoration:none; display:inline-flex; align-items:center; gap:6px;
    }
    .btn-export-pdf:hover { transform:translateY(-1px); box-shadow:0 6px 16px rgba(156,175,136,.3); }
  </style>
</head>
<body>
<link rel="stylesheet" href="../../../../../public/assets/css/return-dashboard.css">
<a class="voyagio-return-dashboard" href="../../../../../back.php">&larr; Back Dashboard</a>

<div class="app-shell">
  <!-- SIDEBAR -->
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

    <!-- Notification bell -->
    <button class="notif-btn" onclick="openNotifs()">
      🔔 Notifications
      <?php if ($nbNotifs > 0): ?>
        <span class="notif-badge"><?= $nbNotifs ?></span>
      <?php endif; ?>
    </button>
  </aside>

  <!-- MAIN -->
  <main class="content">
    <header class="topbar">
      <div>
        <div class="page-title">Gestion des Destinations</div>
        <div class="page-subtitle">Manage all travel destinations</div>
      </div>
      <div style="display:flex; gap:12px; align-items:center;">
        <a class="btn-export-pdf"
           href="../../controller/ExportController.php?type=destinations">
          📄 Exporter PDF
        </a>
        <div class="user-card">
          <span>Admin</span>
          <div class="avatar">AD</div>
        </div>
      </div>
    </header>

    <section class="panel-card">
      <div class="panel-header">
        <div><h2>Destinations enregistrées</h2></div>
        <a href="addDestination.php" class="btn btn-primary">+ Ajouter Destination</a>
      </div>

      <?php if (count($data) > 0): ?>
        <div class="table-card">
          <table class="records-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Ville</th>
                <th>Pays</th>
                <th>Description</th>
                <th>Nb Circuits</th>
                <th>Image</th>
                <th>Catégorie</th>
                <th>Lat / Lng</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($data as $d): ?>
              <tr>
                <td><?= $d['id_destination'] ?></td>
                <td><?= htmlspecialchars($d['ville']) ?></td>
                <td><?= htmlspecialchars($d['pays']) ?></td>
                <td><?= htmlspecialchars(mb_substr($d['description'], 0, 60)) ?>…</td>
                <td style="text-align:center; font-weight:600; color:var(--brown);">
                  <?= $circuitCounts[$d['id_destination']] ?? 0 ?>
                </td>
                <td><img src="../../public/images/<?= htmlspecialchars($d['image']) ?>" width="70" style="border-radius:8px;" onerror="this.src='../../public/images/default.jpg'"></td>
                <td><?= htmlspecialchars($d['categorie']) ?></td>
                <td style="font-size:.8rem; color:var(--text-muted);">
                  <?= !empty($d['latitude']) ? $d['latitude'] . '<br>' . $d['longitude'] : '<span style="color:#bbb;">—</span>' ?>
                </td>
                <td>
                  <a class="action-link" href="addDestination.php?id=<?= $d['id_destination'] ?>">Modifier</a>
                  <a class="action-link danger"
                     href="../../controller/DestinationController.php?delete=<?= $d['id_destination'] ?>"
                     onclick="return confirm('Supprimer cette destination ?')">Supprimer</a>
                  <br>
                  <a class="action-link" style="margin-top:6px; display:inline-block;"
                     href="listCircuit.php?id=<?= $d['id_destination'] ?>">Gérer circuits</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p style="color:var(--text-muted); text-align:center; padding:20px;">
          Aucune destination.
          <a href="addDestination.php" style="color:var(--accent);">Ajouter la première</a>.
        </p>
      <?php endif; ?>
    </section>

    <!-- ===== STATISTIQUES ===== -->
    <section class="panel-card stats-section">
      <div class="panel-header">
        <div><h2>Statistiques</h2></div>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-val"><?= $stats['total'] ?></div>
          <div class="stat-label">Total circuits</div>
        </div>
        <div class="stat-card green">
          <div class="stat-val"><?= $stats['actifs'] ?></div>
          <div class="stat-label">Circuits actifs</div>
        </div>
        <div class="stat-card red">
          <div class="stat-val"><?= $stats['expires'] ?></div>
          <div class="stat-label">Circuits expirés</div>
        </div>
        <div class="stat-card">
          <div class="stat-val"><?= $stats['prix_moyen'] ?> <small style="font-size:.9rem;">TND</small></div>
          <div class="stat-label">Prix moyen</div>
        </div>
      </div>

      <h3 style="font-size:.9rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:.05em; margin:0 0 12px;">
        Circuits par destination
      </h3>
      <div class="table-card" style="margin-top:0;">
        <table class="dest-stats-table">
          <thead>
            <tr>
              <th>Destination</th>
              <th>Pays</th>
              <th>Nb circuits</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($stats['par_destination'] as $s): ?>
            <tr>
              <td><?= htmlspecialchars($s['ville']) ?></td>
              <td><?= htmlspecialchars($s['pays']) ?></td>
              <td><span class="nb-badge"><?= $s['nb'] ?></span></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

  </main>
</div>

<!-- ===== NOTIFICATION PANEL ===== -->
<div class="notif-panel" id="notifPanel">
  <div class="notif-header">
    <h3>🔔 Notifications (<?= $nbNotifs ?>)</h3>
    <button class="notif-close" onclick="closeNotifs()">✕</button>
  </div>
  <div class="notif-list">
    <?php if (empty($notifications)): ?>
      <p class="notif-empty">✅ Aucune notification pour le moment.</p>
    <?php else: ?>
      <?php foreach ($notifications as $n): ?>
        <div class="notif-item <?= $n['type'] ?>">
          <?php
            $icon = ['expired'=>'⏰','soon'=>'⚠️','full'=>'🪑','empty'=>'❌'];
            echo $icon[$n['type']] . ' ' . htmlspecialchars($n['msg']);
          ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<script>
function openNotifs()  { document.getElementById('notifPanel').classList.add('open'); }
function closeNotifs() { document.getElementById('notifPanel').classList.remove('open'); }
document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeNotifs(); });
</script>
</body>
</html>