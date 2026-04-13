<?php require_once 'check_auth.php'; ?>
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
                    <p class="stat-value">1,248</p>
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
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Date d'inscription</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td> Adem gharbi</td>
                                <td>Adem@exemple.com</td>
                                <td>06/04/2026</td>
                                <td><span class="badge active">Actif</span></td>
                            </tr>
                            <tr>
                                <td>Alice Smith</td>
                                <td>alice@exemple.com</td>
                                <td>05/04/2026</td>
                                <td><span class="badge active">Actif</span></td>
                            </tr>
                            <tr>
                                <td>Marc Dubois</td>
                                <td>marc@exemple.com</td>
                                <td>02/04/2026</td>
                                <td><span class="badge inactive">Inactif</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/theme.js"></script>
</body>
</html>
