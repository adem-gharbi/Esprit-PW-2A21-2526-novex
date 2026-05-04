<?php
session_start();
if(!isset($_SESSION['admin'])){ header("Location: login.php"); exit(); }
include("../../controller/PostController.php");
$posts = getPosts();
global $postModel;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Posts</title>
<link rel="stylesheet" href="../../assets/css/admin.css">
<style>
.tag-badge{
  display:inline-block; background:#f5ede6; color:#a67b5b;
  border:1px solid #e8cfc1; border-radius:12px;
  padding:2px 9px; font-size:11px; margin:2px;
}
.pin-btn{
  background:none; border:1px solid #ffc107; color:#856404;
  border-radius:10px; padding:5px 12px; cursor:pointer; font-size:12px;
}
.pin-btn.pinned{ background:#fff3cd; font-weight:600; }
.scheduled-info{ font-size:12px; color:#0066cc; font-style:italic; }
.history-toggle{
  background:none; border:none; color:#a67b5b; cursor:pointer;
  font-size:12px; text-decoration:underline; padding:0;
}
.history-box{
  display:none; background:#fafafa; border:1px solid #eee;
  border-radius:10px; padding:12px; margin-top:10px;
}
.history-box.open{ display:block; }
.history-item{
  border-bottom:1px solid #eee; padding:6px 0; font-size:12px; color:#666;
}
</style>
</head>
<body>
<div class="app-shell">

  <aside class="sidebar">
    <div class="brand">
      <div class="brand-icon">VA</div>
      <div><div class="brand-title">Voyagio</div><div class="brand-subtitle">Back Office</div></div>
    </div>
    <nav class="menu">
      <a href="dashboard.php" class="menu-item">Dashboard</a>
      <a href="managePosts.php" class="menu-item active">Posts</a>
      <a href="manageComments.php" class="menu-item">Comments</a>
      <a href="manageReports.php" class="menu-item">Signalements</a>
    </nav>
  </aside>

  <main class="content">
    <header class="topbar">
      <div>
        <div class="page-title">Manage Posts</div>
        <div class="page-subtitle">Edit, épingler ou supprimer les posts</div>
      </div>
      <div class="user-card">
        <span><?= $_SESSION['admin'] ?></span>
        <div class="avatar">AD</div>
      </div>
    </header>

    <?php if(isset($_GET['updated'])): ?>
      <div style="background:#d4edda;border:1px solid #c3e6cb;border-radius:10px;padding:12px 20px;color:#155724;">
        ✅ Post modifié avec succès.
      </div>
    <?php endif; ?>

    <section class="panel-card">
      <div class="panel-header">
        <div><h2>Registered Posts</h2><p>Manage content and settings.</p></div>
        <a href="dashboard.php" class="btn btn-secondary">← Dashboard</a>
      </div>

      <?php foreach($posts as $post):
        $postTags = $postModel->getPostTags($post['id']);
        $history  = $postModel->getHistory($post['id']);
      ?>
      <div style="margin-bottom:32px;padding-bottom:24px;border-bottom:1px dashed var(--border);">

        <!-- Titre + badges -->
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:8px;">
          <?php if($post['is_pinned']): ?>
            <span style="background:#fff3cd;color:#856404;border-radius:12px;padding:2px 10px;font-size:12px;font-weight:600;">📌 Épinglé</span>
          <?php endif; ?>
          <h3 style="font-family:'Playfair Display',serif;color:var(--brown);margin:0;font-size:1.2rem;">
            <?= htmlspecialchars($post['titre']) ?>
          </h3>
        </div>

        <!-- Tags -->
        <?php if(!empty($postTags)): ?>
          <div style="margin-bottom:8px;">
            <?php foreach($postTags as $t): ?>
              <span class="tag-badge">#<?= htmlspecialchars($t['nom']) ?></span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <!-- Scheduled -->
        <?php if(!empty($post['scheduled_at'])): ?>
          <div class="scheduled-info">🕐 Planifié : <?= date('d/m/Y H:i', strtotime($post['scheduled_at'])) ?></div>
        <?php endif; ?>

        <p style="color:var(--text-muted);margin:10px 0;line-height:1.5;">
          <?= nl2br(htmlspecialchars(strip_tags($post['contenu']))) ?>
        </p>

        <?php if($post['image']): ?>
          <img src="../../assets/images/<?= $post['image'] ?>" width="150"
               style="border-radius:12px;margin-bottom:12px;border:2px solid var(--nude);">
        <?php endif; ?>

        <!-- Actions -->
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
          <a class="btn" href="editPost.php?id=<?= $post['id'] ?>">✏️ Modifier</a>

          <a class="pin-btn <?= $post['is_pinned']?'pinned':'' ?>"
             href="../../controller/PostController.php?togglePin=<?= $post['id'] ?>">
            <?= $post['is_pinned']?'📌 Désépingler':'📌 Épingler' ?>
          </a>

          <a class="btn btn-delete"
             href="../../controller/PostController.php?delete=<?= $post['id'] ?>"
             onclick="return confirm('Supprimer ce post ?')">
             🗑 Supprimer
          </a>
        </div>

        <!-- Historique -->
        <?php if(!empty($history)): ?>
          <button class="history-toggle" onclick="toggleHistory(<?= $post['id'] ?>)">
            🕓 Voir l'historique des modifications (<?= count($history) ?>)
          </button>
          <div class="history-box" id="hist-<?= $post['id'] ?>">
            <?php foreach($history as $h): ?>
              <div class="history-item">
                <strong><?= date('d/m/Y H:i', strtotime($h['edited_at'])) ?></strong> —
                Titre : <?= htmlspecialchars($h['titre']) ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

      </div>
      <?php endforeach; ?>

    </section>
  </main>
</div>
<script>
function toggleHistory(id){
  let box = document.getElementById('hist-'+id);
  box.classList.toggle('open');
}
</script>
</body>
</html>