<?php
// ============================
// 📌 IMPORT DU CONTROLLER
// 👉 permet d'accéder aux fonctions (getPost, updatePost, etc.)
// ============================
include("../../controller/PostController.php");


// ============================
// 📌 RÉCUPÉRATION DU POST À MODIFIER
// 👉 on récupère l'id depuis l'URL (ex: edit.php?id=3)
// ============================

// 🔍 appel fonction getPost avec l'id passé en GET
$post = getPost($_GET['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">

  <!-- 📱 responsive design -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- 🏷️ titre page -->
  <title>Modifier Post</title>

  <!-- 🎨 fichier CSS admin -->
  <link rel="stylesheet" href="../../assets/css/admin.css">
</head>

<body>
<link rel="stylesheet" href="../../../../../public/assets/css/return-dashboard.css">
<a class="voyagio-return-dashboard" href="../../../../../back.php">&larr; Back Dashboard</a>


  <!-- 🧱 structure globale -->
  <div class="app-shell">

    <!-- =========================
         SIDEBAR MENU
    ========================== -->
    <aside class="sidebar">

      <!-- 🏷️ logo brand -->
      <div class="brand">

        <!-- 🔤 initiales -->
        <div class="brand-icon">VA</div>

        <!-- 📛 nom application -->
        <div>
          <div class="brand-title">Voyagio</div>
          <div class="brand-subtitle">Back Office</div>
        </div>
      </div>

      <!-- 📂 navigation menu -->
      <nav class="menu">

        <!-- 📊 dashboard -->
        <a href="dashboard.php" class="menu-item">Dashboard</a>

        <!-- 📝 posts (actif ici) -->
        <a href="managePosts.php" class="menu-item active">Posts</a>

        <!-- 💬 comments -->
        <a href="manageComments.php" class="menu-item">Comments</a>

      </nav>
    </aside>

    <!-- =========================
         CONTENU PRINCIPAL
    ========================== -->
    <main class="content">

      <!-- 🔝 top bar -->
      <header class="topbar">

        <div>

          <!-- 📌 titre page -->
          <div class="page-title">Modifier Post</div>

          <!-- 📌 sous-titre -->
          <div class="page-subtitle">Edit post details and content</div>

        </div>

        <!-- 👤 utilisateur admin -->
        <div class="user-card">

          <!-- 🧑 session admin -->
          <span><?= $_SESSION['admin'] ?? 'Admin' ?></span>

          <!-- 👤 avatar -->
          <div class="avatar">AD</div>

        </div>
      </header>

      <!-- =========================
           FORMULAIRE MODIFICATION
      ========================== -->
      <section class="panel-card">

        <div class="panel-header">

          <div>

            <!-- 📊 titre section -->
            <h2>Post Details</h2>

            <!-- 📌 description -->
            <p>Update the post information below.</p>

          </div>

          <!-- 🔙 bouton retour -->
          <a href="managePosts.php" class="btn btn-secondary">← Retour</a>

        </div>

        <!-- =========================
             FORM UPDATE POST
        ========================== -->
        <form class="panel-form"
              action="../../controller/PostController.php"
              method="POST"
              enctype="multipart/form-data">

          <!-- 🔑 id caché du post -->
          <input type="hidden" name="id" value="<?= $post['id'] ?>">

          <!-- =========================
               TITRE
          ========================== -->
          <label>
            Titre

            <!-- ✏️ champ titre pré-rempli -->
            <input type="text"
                   name="titre"
                   value="<?= $post['titre'] ?>"
                   required>
          </label>

          <!-- =========================
               CONTENU
          ========================== -->
          <label>
            Contenu

            <!-- 📝 textarea pré-rempli -->
            <textarea name="contenu" rows="6" required>
              <?= $post['contenu'] ?>
            </textarea>
          </label>

          <!-- =========================
               IMAGE ACTUELLE
          ========================== -->
          <label>
            Image actuelle

            <?php if($post['image']): ?>
              <!-- 🖼️ afficher image si existe -->
              <img src="../../assets/images/<?= $post['image'] ?>"
                   width="150"
                   style="border-radius:12px; margin-top:8px; border:2px solid var(--nude);">
            <?php else: ?>
              <!-- ⚠️ message si pas d'image -->
              <span style="color:var(--text-muted); font-size:0.9rem;">
                Aucune image
              </span>
            <?php endif; ?>

          </label>

          <!-- =========================
               NOUVELLE IMAGE
          ========================== -->
          <label>
            Nouvelle image (optionnel)

            <!-- 📁 upload fichier -->
            <input type="file" name="image" accept="image/*">

          </label>

          <!-- =========================
               ACTIONS FORMULAIRE
          ========================== -->
          <div class="form-actions">

            <!-- 💾 bouton modifier -->
            <button class="btn btn-primary"
                    type="submit"
                    name="updatePost">
              Modifier
            </button>

            <!-- ❌ annuler -->
            <a href="managePosts.php"
               class="btn btn-secondary">
              Annuler
            </a>

          </div>

        </form>

      </section>

    </main>
  </div>

</body>
</html>