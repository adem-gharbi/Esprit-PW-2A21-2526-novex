<?php
session_start();
if(!isset($_SESSION['admin'])){ header("Location: login.php"); exit(); }
require_once "../../model/Post.php";
$postModel = new Post();

$totalPosts    = $postModel->countPosts();
$totalLikes    = $postModel->countAllLikes();
$totalComments = $postModel->countComments();
$pendingReports= $postModel->countPendingReports();
$topPosts      = $postModel->getTopPosts();
$postsPerDay   = $postModel->getPostsPerDay();
$commentsPerDay= $postModel->getCommentsPerDay();

// Préparer données Chart.js
$chartDays   = [];
$chartPosts  = [];
foreach(array_reverse($postsPerDay) as $row){
    $chartDays[]  = date('d/m', strtotime($row['day'] ?? 'today'));
    $chartPosts[] = (int)$row['total'];
}

$chartCommentDays  = [];
$chartCommentData  = [];
foreach(array_reverse($commentsPerDay) as $row){
    $chartCommentDays[] = date('d/m', strtotime($row['day'] ?? 'today'));
    $chartCommentData[] = (int)$row['total'];
}

$topLabels = array_map(fn($p)=>mb_substr($p['titre'],0,20).'…', $topPosts);
$topData   = array_map(fn($p)=>(int)$p['reactions'], $topPosts);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin</title>
<link rel="stylesheet" href="../../assets/css/admin.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
.stat-grid{ display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:28px; }
.stat-card{
  background:#fff; border:1px solid var(--border); border-radius:18px;
  padding:20px 24px; text-align:center;
}
.stat-value{ font-size:2rem; font-weight:700; color:var(--brown); }
.stat-label{ font-size:0.8rem; color:var(--text-muted); text-transform:uppercase; margin-bottom:6px; letter-spacing:.05em; }
.alert-badge{ background:#e74c3c; color:#fff; border-radius:20px; padding:2px 8px; font-size:11px; vertical-align:middle; }
.charts-grid{ display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px; }
.chart-card{ background:#fff; border:1px solid var(--border); border-radius:18px; padding:20px; }
.chart-card h3{ margin:0 0 16px; font-size:1rem; color:var(--brown); font-family:'Playfair Display',serif; }
.export-row{ display:flex; gap:12px; flex-wrap:wrap; }
@media(max-width:860px){
  .stat-grid{ grid-template-columns:1fr 1fr; }
  .charts-grid{ grid-template-columns:1fr; }
}
</style>
</head>
<body>
<link rel="stylesheet" href="../../../../../public/assets/css/return-dashboard.css">
<a class="voyagio-return-dashboard" href="../../../../../back.php">&larr; Back Dashboard</a>

<div class="app-shell">

  <aside class="sidebar">
    <div class="brand">
      <div class="brand-icon">VA</div>
      <div><div class="brand-title">Voyagio</div><div class="brand-subtitle">Back Office</div></div>
    </div>
    <nav class="menu">
      <a href="dashboard.php" class="menu-item active">Dashboard</a>
      <a href="managePosts.php" class="menu-item">Posts</a>
      <a href="manageComments.php" class="menu-item">Comments</a>
      <a href="manageReports.php" class="menu-item">
        Signalements
        <?php if($pendingReports>0): ?><span class="alert-badge"><?= $pendingReports ?></span><?php endif; ?>
      </a>
    </nav>
  </aside>

  <main class="content">
    <header class="topbar">
      <div>
        <div class="page-title">Dashboard</div>
        <div class="page-subtitle">Vue globale du forum</div>
      </div>
      <div class="user-card">
        <span><?= $_SESSION['admin'] ?></span>
        <div class="avatar">AD</div>
      </div>
    </header>

    <!-- STATS -->
    <div class="stat-grid">
      <div class="stat-card">
        <div class="stat-label">Total Posts</div>
        <div class="stat-value"><?= $totalPosts ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Total Réactions</div>
        <div class="stat-value"><?= $totalLikes ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Commentaires</div>
        <div class="stat-value"><?= $totalComments ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Signalements en attente</div>
        <div class="stat-value" style="color:<?= $pendingReports>0?'#e74c3c':'var(--brown)' ?>"><?= $pendingReports ?></div>
      </div>
    </div>

    <!-- CHARTS -->
    <div class="charts-grid">
      <div class="chart-card">
        <h3>📈 Posts par jour (7 jours)</h3>
        <canvas id="chartPosts" height="180"></canvas>
      </div>
      <div class="chart-card">
        <h3>💬 Commentaires par jour</h3>
        <canvas id="chartComments" height="180"></canvas>
      </div>
    </div>
    <div class="chart-card" style="margin-bottom:24px;">
      <h3>🏆 Top 5 posts les plus réactionnés</h3>
      <canvas id="chartTop" height="120"></canvas>
    </div>

    <!-- EXPORT -->
    <section class="panel-card">
      <div class="panel-header">
        <div><h2>Export CSV</h2><p>Télécharger les données du forum</p></div>
      </div>
      <div class="export-row">
        <a class="btn" href="../../controller/PostController.php?exportPosts=1">
          ⬇️ Exporter les Posts
        </a>
        <a class="btn btn-secondary" href="../../controller/PostController.php?exportComments=1">
          ⬇️ Exporter les Commentaires
        </a>
      </div>
    </section>

  </main>
</div>

<script>
const brown = '#A67B5B';
const green = '#9CAF88';
const nude  = '#E8CFC1';

// Chart Posts/jour
new Chart(document.getElementById('chartPosts'), {
  type: 'line',
  data: {
    labels: <?= json_encode($chartDays ?: ['Auj.']) ?>,
    datasets:[{
      label:'Posts',
      data: <?= json_encode($chartPosts ?: [0]) ?>,
      borderColor: brown, backgroundColor: 'rgba(166,123,91,0.12)',
      fill:true, tension:0.4, pointRadius:4
    }]
  },
  options:{ plugins:{legend:{display:false}}, scales:{ y:{ beginAtZero:true, ticks:{stepSize:1} } } }
});

// Chart Comments/jour
new Chart(document.getElementById('chartComments'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($chartCommentDays ?: ['Auj.']) ?>,
    datasets:[{
      label:'Commentaires',
      data: <?= json_encode($chartCommentData ?: [0]) ?>,
      backgroundColor: green, borderRadius: 8
    }]
  },
  options:{ plugins:{legend:{display:false}}, scales:{ y:{ beginAtZero:true, ticks:{stepSize:1} } } }
});

// Chart Top posts
new Chart(document.getElementById('chartTop'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($topLabels ?: ['Aucun post']) ?>,
    datasets:[{
      label:'Réactions',
      data: <?= json_encode($topData ?: [0]) ?>,
      backgroundColor: nude, borderColor: brown,
      borderWidth: 1, borderRadius: 6
    }]
  },
  options:{
    indexAxis:'y',
    plugins:{legend:{display:false}},
    scales:{ x:{ beginAtZero:true, ticks:{stepSize:1} } }
  }
});
</script>
</body>
</html>