<?php
require_once "../../config/database.php";
require_once "../../model/Circuit.php";
require_once "../../model/Destination.php";

$db  = new Database();
$pdo = $db->connect();

$circuitModel     = new Circuit($pdo);
$destinationModel = new Destination($pdo);

$id_dest = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

$params = [
    'search'       => $_GET['search']    ?? '',
    'sort'         => $_GET['sort']      ?? 'date_asc',
    'prix_min'     => $_GET['prix_min']  ?? '',
    'prix_max'     => $_GET['prix_max']  ?? '',
    'duree_min'    => $_GET['duree_min'] ?? '',
    'duree_max'    => $_GET['duree_max'] ?? '',
    'date_from'    => $_GET['date_from'] ?? '',
    'date_to'      => $_GET['date_to']   ?? '',
    'dispo'        => $_GET['dispo']     ?? '',
    'id_destination' => $id_dest ?? '',
];

$data        = $circuitModel->search($params);
$destination = $id_dest ? $destinationModel->getById($id_dest) : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Voyagio — Circuits<?= $destination ? ' — ' . htmlspecialchars($destination['ville']) : '' ?></title>
  <link rel="stylesheet" href="../../public/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body { background: var(--beige); }

    /* HERO */
    .circ-hero {
      background: linear-gradient(rgba(166,123,91,.35),rgba(166,123,91,.35)),
                  url('/voyagio/descir/public/images/ph3.jpg') center/cover;
      height: 260px;
      display: flex; align-items: center; justify-content: center; flex-direction:column; gap:8px;
    }
    .circ-hero h1 { font-family:'Playfair Display',serif; font-size:40px; color:#fff; margin:0; text-shadow:0 2px 6px rgba(0,0,0,.35); }
    .circ-hero p  { color:rgba(255,255,255,.85); font-size:.95rem; margin:0; }

    /* TOOLBAR */
    .toolbar {
      display:flex; flex-wrap:wrap; gap:14px; align-items:center;
      background:#fff; border:1.5px solid rgba(166,123,91,.2);
      border-radius:18px; padding:18px 24px;
      margin:28px 40px 0; box-shadow:0 4px 16px rgba(166,123,91,.1);
    }
    .toolbar-search { display:flex; gap:8px; flex:1; min-width:200px; }
    .toolbar-search input {
      flex:1; border:1.5px solid rgba(166,123,91,.25); border-radius:10px;
      padding:10px 14px; font-size:.9rem; background:#faf8f6; outline:none; transition:.2s;
    }
    .toolbar-search input:focus { border-color:var(--brown); box-shadow:0 0 0 3px rgba(166,123,91,.15); }
    .toolbar-search button {
      background:var(--brown); color:#fff; border:none; border-radius:10px;
      padding:10px 16px; cursor:pointer; font-size:.9rem; font-weight:600; transition:.2s;
    }
    .toolbar-search button:hover { background:#8d6a4f; }

    .sort-btns { display:flex; gap:8px; flex-wrap:wrap; }
    .sort-btn {
      padding:8px 13px; border-radius:10px; border:1.5px solid rgba(166,123,91,.3);
      background:#faf8f6; color:var(--dark); font-size:.82rem; cursor:pointer; transition:.2s;
    }
    .sort-btn:hover, .sort-btn.active { background:var(--brown); color:#fff; border-color:var(--brown); }

    .btn-export {
      background:linear-gradient(135deg,var(--green),#7d9665); color:#fff;
      border:none; border-radius:12px; padding:10px 18px;
      font-size:.88rem; font-weight:600; cursor:pointer; transition:.2s;
      text-decoration:none; display:inline-flex; align-items:center; gap:6px;
    }
    .btn-export:hover { transform:translateY(-1px); box-shadow:0 6px 16px rgba(156,175,136,.35); }

    .btn-back {
      background:var(--nude); color:var(--dark); border:1.5px solid rgba(166,123,91,.2);
      border-radius:12px; padding:10px 16px; font-size:.88rem; font-weight:500;
      text-decoration:none; transition:.2s;
    }
    .btn-back:hover { background:var(--brown); color:#fff; }

    /* FILTER BAR */
    .filter-bar {
      display:flex; flex-wrap:wrap; gap:14px; align-items:flex-end;
      background:#fff; border:1.5px solid rgba(166,123,91,.2);
      border-radius:18px; padding:18px 24px;
      margin:12px 40px 0; box-shadow:0 4px 16px rgba(166,123,91,.08);
    }
    .filter-field { display:flex; flex-direction:column; gap:4px; }
    .filter-field label { font-size:.75rem; color:var(--text-muted); font-weight:500; }
    .filter-field input, .filter-field select {
      border:1.5px solid rgba(166,123,91,.25); border-radius:10px;
      padding:9px 12px; font-size:.82rem; background:#faf8f6; outline:none;
      width:130px; transition:.2s;
    }
    .filter-field input:focus, .filter-field select:focus { border-color:var(--brown); }
    .filter-dispo { display:flex; align-items:center; gap:6px; margin-top:20px; cursor:pointer; }
    .filter-dispo input { width:auto; }
    .filter-apply {
      background:linear-gradient(135deg,var(--brown),#8d6a4f); color:#fff;
      border:none; border-radius:12px; padding:10px 20px;
      font-size:.88rem; font-weight:600; cursor:pointer; margin-top:20px; transition:.2s;
    }
    .filter-apply:hover { transform:translateY(-1px); }
    .filter-reset {
      background:var(--nude); color:var(--dark); border:1.5px solid rgba(166,123,91,.2);
      border-radius:12px; padding:10px 16px; font-size:.85rem; cursor:pointer; margin-top:20px; transition:.2s;
    }

    /* RESULTS COUNT */
    .results-count {
      margin:16px 40px 0;
      color:var(--text-muted);
      font-size:.85rem;
    }
    .results-count strong { color:var(--brown); }

    /* CIRCUITS GRID */
    .circuits-grid {
      display:flex; flex-wrap:wrap; gap:22px;
      padding:20px 40px 40px;
      justify-content:flex-start;
    }
    .circ-card {
      background:var(--nude); width:280px; border-radius:18px;
      overflow:hidden; box-shadow:0 8px 24px rgba(0,0,0,.09);
      transition:transform .3s, box-shadow .3s;
    }
    .circ-card:hover { transform:translateY(-7px); box-shadow:0 18px 40px rgba(166,123,91,.22); }
    .circ-card-body { padding:18px 18px 16px; }
    .circ-card-body h3 { font-family:'Playfair Display',serif; font-size:.98rem; margin:0 0 10px; color:var(--dark); }
    .circ-row { display:flex; justify-content:space-between; font-size:.82rem; margin:5px 0; color:#555; }
    .circ-row strong { color:var(--dark); }
    .circ-prix { font-size:1.1rem; font-weight:700; color:var(--brown); margin-top:10px; }
    .circ-places { font-size:.78rem; font-weight:600; color:var(--green); margin-top:4px; }
    .circ-places.low { color:#c0392b; }

    .no-results { color:var(--text-muted); text-align:center; padding:50px; width:100%; font-size:1rem; }
  </style>
</head>
<body>

<!-- HERO -->
<header class="circ-hero">
  <h1>Circuits disponibles 🎒</h1>
  <?php if ($destination): ?>
    <p><?= htmlspecialchars($destination['ville']) ?> — <?= htmlspecialchars($destination['pays']) ?></p>
  <?php endif; ?>
</header>

<!-- TOOLBAR -->
<div class="toolbar">
  <!-- Search -->
  <div class="toolbar-search">
    <input type="text" id="searchInput" placeholder="Rechercher un circuit…"
           value="<?= htmlspecialchars($params['search']) ?>">
    <button onclick="applyFilters()">Rechercher</button>
  </div>

  <!-- Sort -->
  <div class="sort-btns">
    <button class="sort-btn <?= $params['sort']==='prix_asc'?'active':'' ?>"   onclick="setSort('prix_asc')">Prix ↑</button>
    <button class="sort-btn <?= $params['sort']==='prix_desc'?'active':'' ?>"  onclick="setSort('prix_desc')">Prix ↓</button>
    <button class="sort-btn <?= $params['sort']==='date_asc'?'active':'' ?>"   onclick="setSort('date_asc')">Date ↑</button>
    <button class="sort-btn <?= $params['sort']==='date_desc'?'active':'' ?>"  onclick="setSort('date_desc')">Date ↓</button>
    <button class="sort-btn <?= $params['sort']==='duree_asc'?'active':'' ?>"  onclick="setSort('duree_asc')">Durée ↑</button>
    <button class="sort-btn <?= $params['sort']==='duree_desc'?'active':'' ?>" onclick="setSort('duree_desc')">Durée ↓</button>
  </div>

  <!-- Export PDF -->
  <a class="btn-export" href="../../controller/ExportController.php?type=circuits<?= $id_dest ? '&id='.$id_dest : '' ?><?= !empty($_SERVER['QUERY_STRING']) ? '&'.htmlspecialchars($_SERVER['QUERY_STRING']) : '' ?>">
    📄 Exporter PDF
  </a>

  <!-- Retour -->
  <a class="btn-back" href="destinations.php">⬅ Destinations</a>
</div>

<!-- FILTER BAR -->
<div class="filter-bar">
  <div class="filter-field">
    <label>Prix min (TND)</label>
    <input type="number" id="fPrixMin" placeholder="0" value="<?= htmlspecialchars($params['prix_min']) ?>" min="0">
  </div>
  <div class="filter-field">
    <label>Prix max (TND)</label>
    <input type="number" id="fPrixMax" placeholder="9999" value="<?= htmlspecialchars($params['prix_max']) ?>" min="0">
  </div>
  <div class="filter-field">
    <label>Durée min (j)</label>
    <input type="number" id="fDureeMin" placeholder="1" value="<?= htmlspecialchars($params['duree_min']) ?>" min="1">
  </div>
  <div class="filter-field">
    <label>Durée max (j)</label>
    <input type="number" id="fDureeMax" placeholder="30" value="<?= htmlspecialchars($params['duree_max']) ?>" min="1">
  </div>
  <div class="filter-field">
    <label>Date du</label>
    <input type="date" id="fDateFrom" value="<?= htmlspecialchars($params['date_from']) ?>">
  </div>
  <div class="filter-field">
    <label>Date au</label>
    <input type="date" id="fDateTo" value="<?= htmlspecialchars($params['date_to']) ?>">
  </div>
  <label class="filter-dispo">
    <input type="checkbox" id="fDispo" <?= $params['dispo']==='1'?'checked':'' ?>>
    <span style="font-size:.83rem;">Disponibles uniquement</span>
  </label>
  <button class="filter-apply" onclick="applyFilters()">Appliquer</button>
  <button class="filter-reset" onclick="resetFilters()">Réinitialiser</button>
</div>

<!-- RESULTS COUNT -->
<div class="results-count">
  <strong><?= count($data) ?></strong> circuit(s) trouvé(s)
</div>

<!-- CIRCUITS GRID -->
<div class="circuits-grid">
  <?php if (empty($data)): ?>
    <p class="no-results">Aucun circuit ne correspond à vos critères.</p>
  <?php else: ?>
    <?php foreach ($data as $c): ?>
      <?php $low = (int)$c['nb_places'] <= 3 && (int)$c['nb_places'] > 0; ?>
      <div class="circ-card">
        <div class="circ-card-body">
          <h3><?= htmlspecialchars($c['titre']) ?></h3>
          <?php if (!empty($c['ville'])): ?>
            <div class="circ-row"><span>Destination</span><strong><?= htmlspecialchars($c['ville']) ?></strong></div>
          <?php endif; ?>
          <div class="circ-row"><span>Durée</span><strong><?= htmlspecialchars($c['duree']) ?> jour(s)</strong></div>
          <div class="circ-row"><span>Date départ</span><strong><?= htmlspecialchars($c['date_depart']) ?></strong></div>
          <div class="circ-prix"><?= number_format((float)$c['prix'], 2, ',', ' ') ?> TND</div>
          <div class="circ-places <?= $low ? 'low' : '' ?>">
            <?= $c['nb_places'] ?> place(s) disponible(s)
            <?= $low ? ' ⚠️' : '' ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<script>
let currentSort = '<?= $params['sort'] ?>';

function setSort(val) {
    currentSort = val;
    document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
    event.target.classList.add('active');
    applyFilters();
}

function applyFilters() {
    const params = new URLSearchParams();
    <?php if ($id_dest): ?>params.set('id', '<?= $id_dest ?>');<?php endif; ?>
    const s = document.getElementById('searchInput').value.trim();
    if (s) params.set('search', s);
    const pm = document.getElementById('fPrixMin').value.trim(); if (pm) params.set('prix_min', pm);
    const px = document.getElementById('fPrixMax').value.trim(); if (px) params.set('prix_max', px);
    const dm = document.getElementById('fDureeMin').value.trim(); if (dm) params.set('duree_min', dm);
    const dx = document.getElementById('fDureeMax').value.trim(); if (dx) params.set('duree_max', dx);
    const df = document.getElementById('fDateFrom').value; if (df) params.set('date_from', df);
    const dt = document.getElementById('fDateTo').value;   if (dt) params.set('date_to', dt);
    if (document.getElementById('fDispo').checked) params.set('dispo', '1');
    if (currentSort) params.set('sort', currentSort);
    window.location.href = 'circuits.php?' + params.toString();
}

function resetFilters() {
    window.location.href = 'circuits.php<?= $id_dest ? "?id=$id_dest" : "" ?>';
}

document.getElementById('searchInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') applyFilters();
});
</script>
</body>
</html>