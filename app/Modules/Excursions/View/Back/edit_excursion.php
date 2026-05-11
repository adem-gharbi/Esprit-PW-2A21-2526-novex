<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Edit Excursion - Voyagio Admin</title>
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
        <a href="admin.php" class="menu-item">Guides</a>
        <a href="admin.php?page=excursions" class="menu-item active">Excursions</a>
        <a href="index.php" class="menu-item">View Website</a>
      </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="content">
      <!-- TOPBAR -->
      <header class="topbar">
        <div>
          <div class="page-title">Edit Excursion</div>
          <div class="page-subtitle">Update excursion information</div>
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
            <h2>Edit Excursion</h2>
            <p>Modify excursion details below</p>
          </div>
        </div>

        <!-- FORM -->
        <form class="panel-form" id="editExcursionForm" method="POST">
          <label>
            Title
            <input type="text" name="titre" id="titre" value="<?php echo htmlspecialchars($excursion['titre']); ?>">
            <small class="error-message" id="titre-error" style="display: none;"></small>
          </label>

          <label>
            Duration
            <input type="text" name="duree" id="duree" value="<?php echo htmlspecialchars($excursion['duree']); ?>">
            <small class="error-message" id="duree-error" style="display: none;"></small>
          </label>

          <label>
            Description
            <textarea name="description" id="description"><?php echo htmlspecialchars($excursion['description']); ?></textarea>
            <small class="error-message" id="description-error" style="display: none;"></small>
          </label>

          <label>
            Price (TND)
            <input type="text" name="prix" id="prix" value="<?php echo htmlspecialchars($excursion['prix']); ?>">
            <small class="error-message" id="prix-error" style="display: none;"></small>
          </label>

          <label>
            Circuit ID
            <input type="text" name="circuit_id" id="circuit_id" value="<?php echo htmlspecialchars($excursion['circuit_id']); ?>">
            <small class="error-message" id="circuit_id-error" style="display: none;"></small>
          </label>

          <label>
            Guide ID
            <input type="text" name="guide_id" id="guide_id" value="<?php echo htmlspecialchars($excursion['guide_id']); ?>">
            <small class="error-message" id="guide_id-error" style="display: none;"></small>
          </label>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Excursion</button>
            <a href="admin.php?page=excursions" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </section>
    </main>
  </div>
</body>
</html>
