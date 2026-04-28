<?php 
require_once 'check_auth.php'; 
require_once '../../Controller/UserController.php';
$userController = new UserController();
// On récupère les 5 derniers inscrits
$allClients = $userController->getAllClients();
$recentClients = array_slice($allClients, 0, 5);
$totalClients = count($allClients);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Projet Écologique</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <meta name="description" content="Tableau de bord administrateur.">
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    </script>
</head>
<body class="admin-body">

    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2>EcoAdmin</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item active">Tableau de bord</a>
                <a href="users.php" class="nav-item">Utilisateurs</a>
                <a href="settings.php" class="nav-item">Paramètres</a>
                <a style="margin-top: auto;" href="../login.html" class="nav-item logout">Déconnexion</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <h1>Vue d'ensemble</h1>
                <div class="admin-profile">
                    <span>Admin</span>
                    <div class="profile-avatar-small">A</div>
                </div>
            </header>

            <!-- Stats Widgets -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Utilisateurs</h3>
                    <p class="stat-value"><?= $totalClients ?></p>
                </div>
                <div class="stat-card">
                    <h3>Nouveaux (Ce mois)</h3>
                    <p class="stat-value">42</p>
                </div>
                <div class="stat-card">
                    <h3>Taux d'Activité</h3>
                    <p class="stat-value">85%</p>
                </div>
            </div>

            <!-- Recent Table -->
            <div class="admin-panel">
                <h2>Récents Utilisateurs</h2>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;">Photo</th>
                                <th>Nom Complet</th>
                                <th>Email</th>
                                <th>Date d'inscription</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recentClients as $c): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($c['profile_photo'])): ?>
                                        <img src="../../<?= htmlspecialchars($c['profile_photo']) ?>" alt="Photo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--color-accent);">
                                    <?php else: ?>
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #e0e0e0; color: #666; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; margin: 0 auto;">N/A</div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($c['fullname']) ?></td>
                                <td><?= htmlspecialchars($c['email']) ?></td>
                                <td><?= htmlspecialchars($c['birthdate']) ?></td>
                                <td><span class="badge active">Récent</span></td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if(count($recentClients) === 0): ?>
                            <tr>
                                <td colspan="5" style="text-align:center;">Aucun utilisateur trouvé.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/theme.js"></script>
</body>
</html>
