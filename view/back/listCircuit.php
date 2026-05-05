<?php
require_once "../../config/database.php";
require_once "../../model/Circuit.php";
require_once "../../model/Destination.php";

$db  = new Database();
$pdo = $db->connect();

$circuitModel     = new Circuit($pdo);
$destinationModel = new Destination($pdo);

if (isset($_GET['id'])) {
    $data = $circuitModel->getByDestination($_GET['id']);
} else {
    $data = $circuitModel->getAll();
}

$notifications = $circuitModel->getNotifications();
$nbNotifs      = count($notifications);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Voyagio — Back Office — Circuits</title>
  <link rel="stylesheet" href="../../public/css/style.css">
  <style>
    /* Notification styles (same as listDestination) */
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
    .notif-header { background:var(--brown); color:#fff; padding:20px 24px; display:flex; justify-content:space-between; align-items:center; }
    .notif-header h3 { margin:0; font-family:'Playfair Display',serif; font-size:1.1rem; }
    .notif-close { background:none; border:none; color:#fff; font-size:1.4rem; cursor:pointer; line-height:1; }
    .notif-list { flex:1; overflow-y:auto; padding:16px; display:flex; flex-direction:column; gap:10px; }
    .notif-item { padding:12px 14px; border-radius:12px; font-size:.85rem; line-height:1.5; border-left:4px solid; }
    .notif-item.expired { background:#fdf0ef; border-color:#c0392b; color:#7f1f14; }
    .notif-item.soon    { background:#fff8e6; border-color:#e67e22; color:#7a4806; }
    .notif-item.full    { background:#fff0f0; border-color:#e74c3c; color:#7a1e1e; }
    .notif-item.empty   { background:#f5f5f5; border-color:#aaa; color:#555; }
    .notif-empty { text-align:center; color:var(--text-muted); padding:30px; font-size:.9rem; }

    .btn-export-pdf {
      background:linear-gradient(135deg,var(--green),#7d9665); color:#fff;
      border:none; border-radius:12px; padding:11px 20px;
      font-size:.88rem; font-weight:600; cursor:pointer; transition:.2s;
      text-decoration:none; display:inline-flex; align-items:center; gap:6px;
    }
    .btn-export-pdf:hover { transform:translateY(-1px); box-shadow:0 6px 16px rgba(156,175,136,.3); }

    .places-low { color:#c0392b; font-weight:600; }
  </style>
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
      <a href="listCircuit.php"     class="menu-item active">Circuits</a>
    </nav>
    <button class="notif-btn" onclick="openNotifs()">
      🔔 Notifications
      <?php if ($nbNotifs > 0): ?>
        <span class="notif-badge"><?= $nbNotifs ?></span>
      <?php endif; ?>
    </button>
  </aside>

  <main class="content">
    <header class="topbar">
      <div>
        <div class="page-title">Circuits</div>
        <div class="page-subtitle">Manage all travel circuits</div>
      </div>
      <div style="display:flex; gap:12px; align-items:center;">
        <a class="btn-export-pdf"
           href="../../controller/ExportController.php?type=circuits<?= isset($_GET['id']) ? '&id='.(int)$_GET['id'] : '' ?>">
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
        <div><h2>Circuits enregistrés</h2></div>
        <a href="addCircuit.php?dest_id=<?= isset($_GET['id']) ? intval($_GET['id']) : '' ?>"
           class="btn btn-primary">+ Ajouter Circuit</a>
      </div>

      <?php if (count($data) > 0): ?>
        <div class="table-card">
          <table class="records-table">
            <thead>
              <tr>
                <th>Titre</th>
                <th>Durée</th>
                <th>Prix (TND)</th>
                <th>Places</th>
                <th>Date départ</th>
                <th>Statut</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($data as $c):
                $today   = date('Y-m-d');
                $expired = $c['date_depart'] < $today;
                $soon    = !$expired && $c['date_depart'] <= date('Y-m-d', strtotime('+2 days'));
                $low     = !$expired && (int)$c['nb_places'] > 0 && (int)$c['nb_places'] <= 3;
              ?>
              <tr>
                <td><?= htmlspecialchars($c['titre']) ?></td>
                <td><?= htmlspecialchars($c['duree']) ?> j</td>
                <td><?= number_format((float)$c['prix'], 2, ',', ' ') ?></td>
                <td class="<?= $low ? 'places-low' : '' ?>">
                  <?= $c['nb_places'] ?><?= $low ? ' ⚠️' : '' ?>
                </td>
                <td><?= htmlspecialchars($c['date_depart']) ?></td>
                <td>
                  <?php if ($expired): ?>
                    <span class="status-badge status-archived">Expiré ⏰</span>
                  <?php elseif ($soon): ?>
                    <span class="status-badge status-pending">Bientôt ⚠️</span>
                  <?php else: ?>
                    <span class="status-badge status-active">Actif</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a class="action-link" href="addCircuit.php?id=<?= $c['id_circuit'] ?>">Modifier</a>
                  <a class="action-link danger"
                     href="../../controller/CircuitController.php?delete=<?= $c['id_circuit'] ?>&dest_id=<?= isset($_GET['id']) ? intval($_GET['id']) : '' ?>"
                     onclick="return confirm('Supprimer ?')">Supprimer</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p style="color:var(--text-muted); text-align:center; padding:20px;">
          Aucun circuit.
          <a href="addCircuit.php?dest_id=<?= isset($_GET['id']) ? intval($_GET['id']) : '' ?>"
             style="color:var(--accent);">Ajouter le premier</a>.
        </p>
      <?php endif; ?>
    </section>
  </main>
</div>

<!-- NOTIFICATION PANEL -->
<div class="notif-panel" id="notifPanel">
  <div class="notif-header">
    <h3>🔔 Notifications (<?= $nbNotifs ?>)</h3>
    <button class="notif-close" onclick="closeNotifs()">✕</button>
  </div>
  <div class="notif-list">
    <?php if (empty($notifications)): ?>
      <p class="notif-empty">✅ Aucune notification.</p>
    <?php else: ?>
      <?php foreach ($notifications as $n):
        $icons = ['expired'=>'⏰','soon'=>'⚠️','full'=>'🪑','empty'=>'❌'];
      ?>
        <div class="notif-item <?= $n['type'] ?>">
          <?= $icons[$n['type']] ?> <?= htmlspecialchars($n['msg']) ?>
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