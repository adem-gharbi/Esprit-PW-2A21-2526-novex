<?php
require_once "../../config/database.php";
require_once "../../model/Destination.php";
require_once "../../model/Circuit.php";

$db  = new Database();
$pdo = $db->connect();

$destinationModel = new Destination($pdo);
$circuitModel     = new Circuit($pdo);

// Récupérer paramètres GET
$params = [
    'search'    => $_GET['search']    ?? '',
    'sort'      => $_GET['sort']      ?? 'ville_asc',
    'pays'      => $_GET['pays']      ?? '',
    'categorie' => $_GET['categorie'] ?? '',
    'prix_min'  => $_GET['prix_min']  ?? '',
    'prix_max'  => $_GET['prix_max']  ?? '',
    'duree_min' => $_GET['duree_min'] ?? '',
    'duree_max' => $_GET['duree_max'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to'   => $_GET['date_to']   ?? '',
];

$data       = $destinationModel->getWithValidCircuits($params);
$paysList   = $destinationModel->getDistinctPays();
$catList    = $destinationModel->getDistinctCategories();

// Prix min/max global pour le range hint
$allCircuits = $circuitModel->getAll();
$allPrix = array_column($allCircuits, 'prix');
$globalPrixMin = $allPrix ? (int)min($allPrix) : 0;
$globalPrixMax = $allPrix ? (int)max($allPrix) : 9999;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Voyagio — Destinations</title>
  <link rel="stylesheet" href="../../public/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <style>
    /* ===== FRONT DESTINATIONS — layout ===== */
    body { background: var(--beige); }

    .dest-page { display: flex; flex-direction: column; min-height: 100vh; }

    /* HERO */
    .dest-hero {
      background: linear-gradient(rgba(166,123,91,0.35),rgba(166,123,91,0.35)),
                  url('/voyagio/descir/public/images/ph3.jpg') center/cover;
      height: 300px;
      display: flex; align-items: center; justify-content: center;
    }
    .dest-hero h1 { font-family:'Playfair Display',serif; font-size:42px; color:#fff; margin:0; text-shadow:0 2px 6px rgba(0,0,0,.35); }

    /* MAIN TWO-COL */
    .dest-body {
      display: grid;
      grid-template-columns: 1fr 380px;
      gap: 28px;
      padding: 32px 40px;
      align-items: start;
    }

    /* LEFT — cards grid */
    .dest-cards { display: flex; flex-wrap: wrap; gap: 22px; justify-content: flex-start; }

    .dest-card {
      background: var(--nude);
      width: 260px;
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 8px 24px rgba(0,0,0,.10);
      transition: transform .3s, box-shadow .3s;
      cursor: pointer;
    }
    .dest-card:hover, .dest-card.highlighted {
      transform: translateY(-8px) scale(1.02);
      box-shadow: 0 18px 40px rgba(166,123,91,.28);
      outline: 3px solid var(--brown);
    }
    .dest-card.hidden { display: none; }
    .dest-card img { width:100%; height:170px; object-fit:cover; display:block; }
    .dest-card-body { padding:14px 16px; }
    .dest-card-body h3 { font-family:'Playfair Display',serif; font-size:1rem; margin:0 0 6px; color:var(--dark); }
    .dest-card-body p  { font-size:.82rem; color:#666; margin:3px 0; }
    .dest-card-body .badge { background:var(--green); color:#fff; font-size:.72rem; padding:3px 9px; border-radius:20px; display:inline-block; margin:6px 0; }
    .dest-card-body .circuit-count-bar { background:var(--nude); color:var(--brown); font-size:.8rem; font-weight:600; text-align:center; padding:7px; border-radius:4px; margin:10px 0; border:1px solid rgba(166,123,91,.2); }
    .dest-card-body .btn { display:block; text-align:center; background:linear-gradient(135deg,var(--brown),#8d6a4f); color:#fff; padding:9px; border-radius:10px; text-decoration:none; font-size:.85rem; font-weight:600; margin-top:8px; transition:.2s; }
    .dest-card-body .btn:hover { transform:scale(1.03); }

    .no-results { color:var(--text-muted); text-align:center; padding:40px; width:100%; font-size:1rem; }

    /* RIGHT — sidebar card */
    .sidebar-card {
      background: #fff;
      border: 1.5px solid rgba(166,123,91,.22);
      border-radius: 24px;
      padding: 24px;
      box-shadow: 0 12px 40px rgba(166,123,91,.13);
      position: sticky;
      top: 24px;
    }
    .sidebar-card h2 { font-family:'Playfair Display',serif; color:var(--brown); font-size:1.2rem; margin:0 0 18px; }
    .sidebar-section { margin-bottom:20px; }
    .sidebar-section h3 { font-size:.8rem; text-transform:uppercase; letter-spacing:.06em; color:var(--text-muted); font-weight:600; margin:0 0 10px; }

    /* Search */
    .search-bar { display:flex; gap:8px; }
    .search-bar input {
      flex:1; border:1.5px solid rgba(166,123,91,.25); border-radius:10px;
      padding:10px 13px; font-size:.9rem; background:#faf8f6; outline:none;
      transition:.2s;
    }
    .search-bar input:focus { border-color:var(--brown); box-shadow:0 0 0 3px rgba(166,123,91,.15); }
    .search-bar button {
      background:var(--brown); color:#fff; border:none; border-radius:10px;
      padding:10px 14px; cursor:pointer; font-size:.9rem; transition:.2s;
    }
    .search-bar button:hover { background:#8d6a4f; }

    /* Sort */
    .sort-btns { display:flex; flex-wrap:wrap; gap:8px; }
    .sort-btn {
      padding:7px 13px; border-radius:10px; border:1.5px solid rgba(166,123,91,.3);
      background:#faf8f6; color:var(--dark); font-size:.82rem; cursor:pointer; transition:.2s;
    }
    .sort-btn:hover, .sort-btn.active { background:var(--brown); color:#fff; border-color:var(--brown); }

    /* Filters */
    .filter-group { display:grid; gap:10px; }
    .filter-row { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
    .filter-group input, .filter-group select {
      border:1.5px solid rgba(166,123,91,.25); border-radius:10px;
      padding:9px 12px; font-size:.82rem; background:#faf8f6; outline:none; width:100%; transition:.2s;
    }
    .filter-group input:focus, .filter-group select:focus { border-color:var(--brown); box-shadow:0 0 0 3px rgba(166,123,91,.15); }
    .filter-group label { font-size:.78rem; color:var(--text-muted); font-weight:500; display:block; margin-bottom:3px; }
    .filter-apply {
      width:100%; background:linear-gradient(135deg,var(--brown),#8d6a4f); color:#fff;
      border:none; border-radius:12px; padding:11px; font-size:.9rem; font-weight:600;
      cursor:pointer; transition:.2s; margin-top:6px;
    }
    .filter-apply:hover { transform:translateY(-1px); box-shadow:0 6px 16px rgba(166,123,91,.3); }
    .filter-reset { width:100%; background:var(--nude); color:var(--dark); border:1.5px solid rgba(166,123,91,.2); border-radius:12px; padding:9px; font-size:.85rem; font-weight:500; cursor:pointer; margin-top:6px; transition:.2s; }
    .filter-reset:hover { background:var(--nude); border-color:var(--brown); }

    /* Map */
    #map { height:260px; border-radius:16px; overflow:hidden; border:1.5px solid rgba(166,123,91,.2); margin-top:8px; }
    .leaflet-popup-content-wrapper { border-radius:12px; }

    /* Responsive */
    @media(max-width:900px){
      .dest-body { grid-template-columns:1fr; }
      .sidebar-card { position:static; }
    }
  </style>
</head>
<body>
<div class="dest-page">

  <!-- HERO -->
  <header class="dest-hero">
    <h1>Nos Destinations ✈️</h1>
  </header>

  <!-- MAIN BODY -->
  <div class="dest-body">

    <!-- LEFT: destination cards -->
    <div>
      <div class="dest-cards" id="cardsContainer">
        <?php if (empty($data)): ?>
          <p class="no-results">Aucune destination ne correspond à vos critères.</p>
        <?php else: ?>
          <?php foreach ($data as $d): ?>
            <div class="dest-card"
                 id="card-<?= $d['id_destination'] ?>"
                 data-id="<?= $d['id_destination'] ?>"
                 data-ville="<?= htmlspecialchars($d['ville']) ?>"
                 onmouseenter="highlightMarker(<?= $d['id_destination'] ?>)"
                 onmouseleave="unhighlightMarker(<?= $d['id_destination'] ?>)">

              <img src="/voyagio/descir/public/images/<?= htmlspecialchars($d['image']) ?>"
                   alt="<?= htmlspecialchars($d['ville']) ?>"
                   onerror="this.src='/voyagio/descir/public/images/default.jpg'">

              <div class="dest-card-body">
                <h3><?= htmlspecialchars($d['ville']) ?> — <?= htmlspecialchars($d['pays']) ?></h3>
                <span class="badge"><?= htmlspecialchars($d['categorie']) ?></span>
                <p><?= htmlspecialchars(mb_substr($d['description'], 0, 80)) ?>...</p>
                <div class="circuit-count-bar"><?= $d['nb_circuits'] ?> circuit(s) disponible(s)</div>
                <a class="btn" href="circuits.php?id=<?= $d['id_destination'] ?>">Voir les circuits</a>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- RIGHT: sidebar card -->
    <aside class="sidebar-card">

      <!-- SEARCH -->
      <div class="sidebar-section">
        <h3>🔎 Recherche</h3>
        <div class="search-bar">
          <input type="text" id="searchInput" placeholder="Ville, pays, catégorie…"
                 value="<?= htmlspecialchars($params['search']) ?>">
          <button onclick="applyFilters()">→</button>
        </div>
      </div>

      <!-- TRI -->
      <div class="sidebar-section">
        <h3>🔃 Tri</h3>
        <div class="sort-btns">
          <button class="sort-btn <?= $params['sort']==='ville_asc'?'active':'' ?>"
                  onclick="setSort('ville_asc')">A → Z</button>
          <button class="sort-btn <?= $params['sort']==='ville_desc'?'active':'' ?>"
                  onclick="setSort('ville_desc')">Z → A</button>
          <button class="sort-btn <?= $params['sort']==='nb_circuits_desc'?'active':'' ?>"
                  onclick="setSort('nb_circuits_desc')">+ de circuits</button>
          <button class="sort-btn <?= $params['sort']==='nb_circuits_asc'?'active':'' ?>"
                  onclick="setSort('nb_circuits_asc')">- de circuits</button>
        </div>
      </div>

      <!-- FILTRES -->
      <div class="sidebar-section">
        <h3>🎛️ Filtres</h3>
        <div class="filter-group">

          <div>
            <label>Pays</label>
            <input type="text" id="fPays" placeholder="ex: France" value="<?= htmlspecialchars($params['pays']) ?>">
          </div>

          <div>
            <label>Catégorie</label>
            <input type="text" id="fCategorie" placeholder="ex: Culture" value="<?= htmlspecialchars($params['categorie']) ?>">
          </div>

          <div>
            <label>Prix (TND)</label>
            <div class="filter-row">
              <input type="number" id="fPrixMin" placeholder="Min (<?= $globalPrixMin ?>)"
                     value="<?= htmlspecialchars($params['prix_min']) ?>" min="0">
              <input type="number" id="fPrixMax" placeholder="Max (<?= $globalPrixMax ?>)"
                     value="<?= htmlspecialchars($params['prix_max']) ?>" min="0">
            </div>
          </div>

          <div>
            <label>Durée (jours)</label>
            <div class="filter-row">
              <input type="number" id="fDureeMin" placeholder="Min" value="<?= htmlspecialchars($params['duree_min']) ?>" min="1">
              <input type="number" id="fDureeMax" placeholder="Max" value="<?= htmlspecialchars($params['duree_max']) ?>" min="1">
            </div>
          </div>

          <div>
            <label>Date de départ</label>
            <div class="filter-row">
              <input type="date" id="fDateFrom" value="<?= htmlspecialchars($params['date_from']) ?>">
              <input type="date" id="fDateTo"   value="<?= htmlspecialchars($params['date_to']) ?>">
            </div>
          </div>

          <button class="filter-apply" onclick="applyFilters()">Appliquer les filtres</button>
          <button class="filter-reset" onclick="resetFilters()">Réinitialiser</button>
        </div>
      </div>

      <!-- MAP -->
      <div class="sidebar-section">
        <h3>🗺️ Carte</h3>
        <div id="map"></div>
      </div>

    </aside>
  </div><!-- /dest-body -->
</div><!-- /dest-page -->

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ===========================
// Destinations data for map
// ===========================
const destinations = <?php
    $mapData = [];
    foreach ($data as $d) {
        if (!empty($d['latitude']) && !empty($d['longitude'])) {
            $mapData[] = [
                'id'     => (int)$d['id_destination'],
                'ville'  => $d['ville'],
                'pays'   => $d['pays'],
                'lat'    => (float)$d['latitude'],
                'lng'    => (float)$d['longitude'],
                'nb'     => (int)$d['nb_circuits'],
            ];
        }
    }
    echo json_encode($mapData);
?>;

// ===========================
// Init Leaflet map
// ===========================
const map = L.map('map').setView([20, 10], 2);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

const markers = {};
let activeFilter = null; // track card filtered by map click

destinations.forEach(d => {
    const marker = L.circleMarker([d.lat, d.lng], {
        radius: 9,
        fillColor: '#A67B5B',
        color: '#fff',
        weight: 2,
        fillOpacity: 0.85
    }).addTo(map);

    marker.bindPopup(
        '<strong>' + d.ville + '</strong><br>' + d.pays + '<br>' +
        '<span style="color:#A67B5B;font-size:.8rem;">' + d.nb + ' circuit(s)</span>'
    );

    // Click marker → show only that card
    marker.on('click', function() {
        if (activeFilter === d.id) {
            // deselect
            activeFilter = null;
            showAllCards();
            marker.setStyle({ fillColor: '#A67B5B', radius: 9 });
        } else {
            activeFilter = d.id;
            filterToCard(d.id);
            marker.setStyle({ fillColor: '#9CAF88', radius: 13 });
        }
    });

    markers[d.id] = marker;
});

function filterToCard(id) {
    document.querySelectorAll('.dest-card').forEach(card => {
        card.classList.toggle('hidden', parseInt(card.dataset.id) !== id);
    });
    // reset other markers
    Object.keys(markers).forEach(mid => {
        if (parseInt(mid) !== id) {
            markers[mid].setStyle({ fillColor: '#A67B5B', radius: 9 });
        }
    });
}

function showAllCards() {
    document.querySelectorAll('.dest-card').forEach(card => card.classList.remove('hidden'));
}

// Hover card → highlight marker
function highlightMarker(id) {
    if (markers[id]) {
        markers[id].setStyle({ fillColor: '#9CAF88', radius: 13 });
        markers[id].openPopup();
    }
}
function unhighlightMarker(id) {
    if (markers[id] && activeFilter !== id) {
        markers[id].setStyle({ fillColor: '#A67B5B', radius: 9 });
        markers[id].closePopup();
    }
}

// ===========================
// Filters / Search / Sort
// ===========================
let currentSort = '<?= $params['sort'] ?>';

function setSort(val) {
    currentSort = val;
    document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
    event.target.classList.add('active');
    applyFilters();
}

function applyFilters() {
    const params = new URLSearchParams();
    const search   = document.getElementById('searchInput').value.trim();
    const pays     = document.getElementById('fPays').value.trim();
    const cat      = document.getElementById('fCategorie').value.trim();
    const pmin     = document.getElementById('fPrixMin').value.trim();
    const pmax     = document.getElementById('fPrixMax').value.trim();
    const dmin     = document.getElementById('fDureeMin').value.trim();
    const dmax     = document.getElementById('fDureeMax').value.trim();
    const dfrom    = document.getElementById('fDateFrom').value;
    const dto      = document.getElementById('fDateTo').value;

    if (search)   params.set('search',    search);
    if (pays)     params.set('pays',      pays);
    if (cat)      params.set('categorie', cat);
    if (pmin)     params.set('prix_min',  pmin);
    if (pmax)     params.set('prix_max',  pmax);
    if (dmin)     params.set('duree_min', dmin);
    if (dmax)     params.set('duree_max', dmax);
    if (dfrom)    params.set('date_from', dfrom);
    if (dto)      params.set('date_to',   dto);
    if (currentSort) params.set('sort',   currentSort);

    window.location.href = 'destinations.php?' + params.toString();
}

function resetFilters() {
    window.location.href = 'destinations.php';
}

// Enter key on search
document.getElementById('searchInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') applyFilters();
});
</script>
</body>
</html>