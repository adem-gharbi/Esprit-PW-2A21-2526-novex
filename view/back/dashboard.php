<?php
// 🚀 Démarrage de la session utilisateur (permet de stocker admin connecté)
session_start();


// 🔐 PROTECTION ADMIN
// 👉 Vérifie si l'admin est connecté
if(!isset($_SESSION['admin'])){

    // 🔁 redirection vers login si non connecté
    header("Location: login.php");

    // ⛔ arrêt du script
    exit();
}


// 📦 import du modèle Post (gestion base de données)
require_once "../../model/Post.php";


// 🧠 création objet Post
$postModel = new Post();


// ============================
// 📊 STATISTIQUES DASHBOARD
// ============================

// 🔢 récupérer nombre total de posts
$totalPosts = $postModel->countPosts();

// ❤️ récupérer nombre total de likes
$totalLikes = $postModel->countAllLikes();

// 💬 récupérer nombre total de commentaires
$totalComments = $postModel->countComments();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">

  <!-- 📱 responsive design -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- 🏷️ titre de la page -->
  <title>Dashboard Admin</title>

  <!-- 🎨 fichier CSS admin -->
  <link rel="stylesheet" href="../../assets/css/admin.css">
</head>

<body>

  <!-- 🧱 STRUCTURE PRINCIPALE -->
  <div class="app-shell">

    <!-- =========================
         SIDEBAR (MENU LATÉRAL)
    ========================== -->
    <aside class="sidebar">

      <!-- 🏷️ BRAND / LOGO -->
      <div class="brand">

        <!-- 🔤 logo initiales -->
        <div class="brand-icon">VA</div>

        <!-- 📛 nom application -->
        <div>
          <div class="brand-title">Voyagio</div>
          <div class="brand-subtitle">Back Office</div>
        </div>
      </div>

      <!-- 📂 MENU NAVIGATION -->
      <nav class="menu">

        <!-- 📊 lien dashboard actif -->
        <a href="dashboard.php" class="menu-item active">Dashboard</a>

        <!-- 📝 gestion posts -->
        <a href="managePosts.php" class="menu-item">Posts</a>

        <!-- 💬 gestion commentaires -->
        <a href="manageComments.php" class="menu-item">Comments</a>

      </nav>
    </aside>

    <!-- =========================
         CONTENU PRINCIPAL
    ========================== -->
    <main class="content">

      <!-- 🔝 TOP BAR -->
      <header class="topbar">

        <div>

          <!-- 📌 titre page -->
          <div class="page-title">Dashboard</div>

          <!-- 🧾 sous-titre -->
          <div class="page-subtitle">Vue globale</div>

        </div>

        <!-- 👤 info admin connecté -->
        <div class="user-card">

          <!-- 🧑 nom admin depuis session -->
          <span><?= $_SESSION['admin'] ?></span>

          <!-- 👤 avatar admin -->
          <div class="avatar">AD</div>

        </div>
      </header>

      <!-- =========================
           PANEL STATISTIQUES
      ========================== -->
      <section class="panel-card">

        <div class="panel-header">

          <div>

            <!-- 📊 titre section -->
            <h2>Statistiques</h2>

            <!-- 📌 description -->
            <p>Aperçu des activités du forum</p>

          </div>
        </div>

        <!-- =========================
             GRILLE DES STATISTIQUES
        ========================== -->
        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:20px;">

          <!-- 📌 TOTAL POSTS -->
          <div class="panel-card" style="text-align:center; padding:24px;">

            <div style="font-size:0.9rem; color:var(--text-muted); text-transform:uppercase; margin-bottom:8px;">
              Total Posts
            </div>

            <!-- 🔢 affichage dynamique -->
            <div style="font-size:2rem; font-weight:700; color:var(--brown);">
              <?= $totalPosts ?>
            </div>

          </div>

          <!-- ❤️ TOTAL LIKES -->
          <div class="panel-card" style="text-align:center; padding:24px;">

            <div style="font-size:0.9rem; color:var(--text-muted); text-transform:uppercase; margin-bottom:8px;">
              Total Likes
            </div>

            <!-- 🔢 likes -->
            <div style="font-size:2rem; font-weight:700; color:var(--brown);">
              <?= $totalLikes ?>
            </div>

          </div>

          <!-- 💬 TOTAL COMMENTS -->
          <div class="panel-card" style="text-align:center; padding:24px;">

            <div style="font-size:0.9rem; color:var(--text-muted); text-transform:uppercase; margin-bottom:8px;">
              Total Comments
            </div>

            <!-- 🔢 commentaires -->
            <div style="font-size:2rem; font-weight:700; color:var(--brown);">
              <?= $totalComments ?>
            </div>

          </div>

        </div>
      </section>

    </main>
  </div>

</body>
</html>