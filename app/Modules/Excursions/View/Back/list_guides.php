<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Manage Guides - Voyagio Admin</title>
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
          <div class="page-title">Manage Guides</div>
          <div class="page-subtitle">View and manage all guides in your system</div>
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
            <h2>Guides Directory</h2>
            <p>Complete list of all guides</p>
          </div>
          <a href="admin.php?action=add" class="btn btn-primary">+ Add Guide</a>
        </div>

        <!-- TABLE -->
        <div class="table-card">
          <table class="records-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Specialty</th>
                <th>Phone</th>
                <th>Language</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($guides as $g): ?>
              <tr>
                <td><?php echo htmlspecialchars($g['prenom'] . ' ' . $g['nom']); ?></td>
                <td><?php echo htmlspecialchars($g['specialite']); ?></td>
                <td><?php echo htmlspecialchars($g['tel'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($g['langue']); ?></td>
                <td>
                  <a class="action-link" href="admin.php?action=edit&id=<?php echo $g['id']; ?>">Edit</a>
                  <a class="action-link danger" href="admin.php?action=delete&id=<?php echo $g['id']; ?>" onclick="return confirm('Delete this guide?')">Delete</a>
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
