<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Manage Excursions - Voyagio Admin</title>
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
          <div class="page-title">Manage Excursions</div>
          <div class="page-subtitle">View and manage all excursions in your system</div>
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
            <h2>Excursions Directory</h2>
            <p>Complete list of all excursions</p>
          </div>
          <a href="admin.php?page=excursions&action=add" class="btn btn-primary">+ Add Excursion</a>
        </div>

        <!-- TABLE -->
        <div class="table-card">
          <table class="records-table">
            <thead>
              <tr>
                <th>Title</th>
                <th>Duration</th>
                <th>Price</th>
                <th>Circuit</th>
                <th>Guide</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($excursions as $e): ?>
              <tr>
                <td><?php echo htmlspecialchars($e['titre']); ?></td>
                <td><?php echo htmlspecialchars($e['duree']); ?></td>
                <td><?php echo htmlspecialchars($e['prix']); ?> TND</td>
                <td><?php echo htmlspecialchars($e['circuit_id'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($e['guide_id'] ?? 'N/A'); ?></td>
                <td>
                  <a class="action-link" href="admin.php?page=excursions&action=edit&id=<?php echo $e['id']; ?>">Edit</a>
                  <a class="action-link danger" href="admin.php?page=excursions&action=delete&id=<?php echo $e['id']; ?>" onclick="return confirm('Delete this excursion?')">Delete</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
