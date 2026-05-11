<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Edit Guide - Voyagio Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<link rel="stylesheet" href="../../../public/assets/css/return-dashboard.css">
<a class="voyagio-return-dashboard" href="../../../back.php">&larr; Back Dashboard</a>

  <div class="app-shell">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="brand">
        <div class="brand-icon">VO</div>
        <div>
          <div class="brand-title">Voyagio</div>
          <div class="brand-subtitle">Back Office</div>
        </div>
      </div>
      <nav class="menu">
        <a href="admin.php" class="menu-item active">Guides</a>
        <a href="admin.php?page=excursions" class="menu-item">Excursions</a>
        <a href="index.php" class="menu-item">View Website</a>
      </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="content">
      <!-- TOPBAR -->
      <header class="topbar">
        <div>
          <div class="page-title">Edit Guide</div>
          <div class="page-subtitle">Update guide information</div>
        </div>
        <div class="user-card">
          <span>Admin</span>
          <div class="avatar">AD</div>
        </div>
      </header>

      <!-- PANEL CARD -->
      <section class="panel-card">
        <div class="panel-header">
          <div>
            <h2>Edit Guide</h2>
            <p>Modify guide details below</p>
          </div>
        </div>

        <!-- FORM -->
        <form class="panel-form" id="editForm" method="POST">
          <label>
            First Name
            <input type="text" name="prenom" id="prenom" value="<?php echo htmlspecialchars($guide['prenom']); ?>">
            <small class="error-message" id="prenom-error" style="display: none;"></small>
          </label>

          <label>
            Last Name
            <input type="text" name="nom" id="nom" value="<?php echo htmlspecialchars($guide['nom']); ?>">
            <small class="error-message" id="nom-error" style="display: none;"></small>
          </label>

          <label>
            Specialty
            <input type="text" name="specialite" id="specialite" value="<?php echo htmlspecialchars($guide['specialite']); ?>">
            <small class="error-message" id="specialite-error" style="display: none;"></small>
          </label>

          <label>
            Language
            <input type="text" name="langue" id="langue" value="<?php echo htmlspecialchars($guide['langue']); ?>">
            <small class="error-message" id="langue-error" style="display: none;"></small>
          </label>

          <label>
            Phone
            <input type="text" name="tel" id="tel" value="<?php echo htmlspecialchars($guide['tel'] ?? ''); ?>">
            <small class="error-message" id="tel-error" style="display: none;"></small>
          </label>

          <label>
            Photo Filename
            <input type="text" name="photo" id="photo" value="<?php echo htmlspecialchars($guide['photo']); ?>">
            <small class="error-message" id="photo-error" style="display: none;"></small>
          </label>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Guide</button>
            <a href="admin.php" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </section>
    </main>
  </div>
</body>
</html>
