<?php
// Démarrage de la session pour accéder aux données de l'utilisateur connecté
session_start();

// 🔐 Sécurité : vérification si l'admin est connecté
if(!isset($_SESSION['admin'])){
    // Redirection vers la page login si non connecté
    header("Location: login.php");
    exit(); // arrêt du script
}

// Inclusion du controller pour accéder aux fonctions (posts, comments)
include("../../controller/PostController.php");

// Récupération de tous les posts depuis la base de données
$posts = getPosts();

// Accès global au modèle Post pour utiliser ses méthodes
global $postModel;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">

  <!-- Responsive design -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Titre de la page -->
  <title>Manage Posts</title>

  <!-- Import du CSS admin -->
  <link rel="stylesheet" href="../../assets/css/admin.css">
</head>

<body>

<!-- STRUCTURE PRINCIPALE -->
<div class="app-shell">

  <!-- ================= SIDEBAR ================= -->
  <aside class="sidebar">

    <!-- Logo / Brand -->
    <div class="brand">
      <div class="brand-icon">VA</div>
      <div>
        <div class="brand-title">Voyagio</div>
        <div class="brand-subtitle">Back Office</div>
      </div>
    </div>

    <!-- Menu navigation admin -->
    <nav class="menu">
      <a href="dashboard.php" class="menu-item">Dashboard</a>
      <a href="managePosts.php" class="menu-item active">Posts</a>
      <a href="manageComments.php" class="menu-item">Comments</a>
    </nav>

  </aside>

  <!-- ================= CONTENT ================= -->
  <main class="content">

    <!-- HEADER TOP BAR -->
    <header class="topbar">
      <div>
        <!-- Titre page -->
        <div class="page-title">Manage Posts</div>

        <!-- Sous-titre -->
        <div class="page-subtitle">Edit or remove forum posts</div>
      </div>

      <!-- Info admin connecté -->
      <div class="user-card">
        <span><?= $_SESSION['admin'] ?></span>
        <div class="avatar">AD</div>
      </div>
    </header>

    <!-- PANEL PRINCIPAL -->
    <section class="panel-card">

      <!-- Header section -->
      <div class="panel-header">
        <div>
          <h2>Registered Posts</h2>
          <p>Manage content and comments below.</p>
        </div>

        <!-- Bouton retour dashboard -->
        <a href="dashboard.php" class="btn btn-secondary">← Dashboard</a>
      </div>

      <!-- ================= LISTE POSTS ================= -->
      <?php foreach($posts as $post){ ?>

      <!-- Bloc post -->
      <div style="margin-bottom: 32px; padding-bottom: 24px; border-bottom: 1px dashed var(--border);">

        <!-- Titre du post -->
        <h3 style="font-family:'Playfair Display',serif; color:var(--brown); margin:0 0 16px 0; font-size:1.2rem;">
          <?= $post['titre'] ?>
        </h3>

        <!-- Contenu du post -->
        <p style="color:var(--text-muted); margin-bottom:16px; line-height:1.5;">
          <?= nl2br(htmlspecialchars($post['contenu'])) ?>
        </p>

        <!-- Image du post (si existe) -->
        <?php if($post['image']): ?>
          <img src="../../assets/images/<?= $post['image'] ?>" width="150"
               style="border-radius:12px; margin-bottom:16px; border:2px solid var(--nude);">
          <br>
        <?php endif; ?>

        <!-- ACTIONS POST -->
        <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px;">

          <!-- Bouton modifier post -->
          <a class="btn" href="editPost.php?id=<?= $post['id'] ?>">Edit</a>

          <!-- Bouton supprimer post -->
          <a class="btn btn-delete"
             href="../../controller/PostController.php?delete=<?= $post['id'] ?>"
             onclick="return confirm('Delete this post?')">
             Delete
          </a>

        </div>

        <!-- ================= COMMENTAIRES ================= -->
        <h4 style="font-family:'Playfair Display',serif; color:var(--brown); margin:24px 0 12px 0; font-size:1.1rem;">
          Commentaires :
        </h4>

        <?php
        // Récupération des commentaires du post actuel
        $comments = $postModel->getComments($post['id']);

        // boucle commentaires
        foreach($comments as $c){
        ?>

        <!-- COMMENT ITEM -->
        <div class="comment-item">

          <!-- contenu commentaire -->
          <div class="comment-content">
            <p style="margin:0; color:var(--dark);">
              <?= htmlspecialchars($c['contenu']) ?>
            </p>
          </div>

          <!-- actions commentaire -->
          <div class="comment-actions">

            <!-- FORMULAIRE UPDATE COMMENT -->
            <form action="../../controller/PostController.php" method="POST"
                  style="display:flex; gap:8px; margin:0;">

              <!-- id caché commentaire -->
              <input type="hidden" name="id" value="<?= $c['id'] ?>">

              <!-- champ modification -->
              <input type="text" name="contenu" value="<?= $c['contenu'] ?>"
                     style="padding:8px 12px; border-radius:10px; border:1px solid var(--border);">

              <!-- bouton update -->
              <button class="btn" name="updateComment" type="submit"
                      style="padding:8px 14px; font-size:0.85rem;">
                Modifier
              </button>

            </form>

            <!-- bouton delete commentaire -->
            <a class="btn btn-delete"
               style="padding:8px 14px; font-size:0.85rem;"
               href="../../controller/PostController.php?deleteComment=<?= $c['id'] ?>"
               onclick="return confirm('Delete this comment?')">

               Delete
            </a>

          </div>
        </div>

        <?php } // fin boucle comments ?>

      </div>

      <?php } // fin boucle posts ?>

    </section>

  </main>
</div>

</body>
</html>