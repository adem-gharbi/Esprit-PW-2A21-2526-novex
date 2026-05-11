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
    <style>
        .lang-switcher { margin-left: 20px; z-index: 100; display: inline-block; }
        
        /* RTL overrides for Admin */
        html[dir="rtl"] .admin-layout { flex-direction: row-reverse; }
        html[dir="rtl"] .admin-header { flex-direction: row-reverse; }
        html[dir="rtl"] .stats-grid { flex-direction: row-reverse; }
        html[dir="rtl"] .admin-table th, html[dir="rtl"] .admin-table td { text-align: right; }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }

        // Détecter si la page a été rechargée (Refresh/F5)
        if (performance.getEntriesByType("navigation").length > 0) {
            if (performance.getEntriesByType("navigation")[0].type === "reload") {
                // Détruire la session et retourner à la connexion
                window.location.href = "logout.php";
            }
        }

        // Détecter si le lien a été entré/collé directement dans le navigateur (pas de page de provenance)
        if (document.referrer === "") {
            window.location.href = "logout.php";
        }
    </script>
</head>
<body class="admin-body">
<link rel="stylesheet" href="../../../../../public/assets/css/return-dashboard.css">
<a class="voyagio-return-dashboard" href="../../../../../back.php">&larr; Back Dashboard</a>


    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2>EcoAdmin</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item active" data-i18n="nav_dashboard">Tableau de bord</a>
                <a href="users.php" class="nav-item" data-i18n="nav_users">Utilisateurs</a>
                <a href="settings.php" class="nav-item" data-i18n="nav_settings">Paramètres</a>
                <a style="margin-top: auto;" href="logout.php" class="nav-item logout" data-i18n="nav_logout">Déconnexion</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <h1 data-i18n="overview">Vue d'ensemble</h1>
                <div style="display:flex; align-items:center;">
                    <!-- Language Switcher inside header -->
                    <div class="lang-switcher">
                        <select onchange="changeLanguage(this.value)" style="padding: 5px; border-radius: 5px; background: white; border: 1px solid #ccc; font-size: 1rem;">
                            <option value="fr">🇫🇷 FR</option>
                            <option value="en">🇬🇧 EN</option>
                            <option value="ar">🇸🇦 AR</option>
                        </select>
                    </div>
                    <div class="admin-profile" style="margin-left: 15px;">
                        <span data-i18n="admin">Admin</span>
                        <div class="profile-avatar-small">A</div>
                    </div>
                </div>
            </header>

            <!-- Stats Widgets -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3 data-i18n="total_users">Total Utilisateurs</h3>
                    <p class="stat-value"><?= $totalClients ?></p>
                </div>
                <div class="stat-card">
                    <h3 data-i18n="new_users_month">Nouveaux (Ce mois)</h3>
                    <p class="stat-value">42</p>
                </div>
                <div class="stat-card">
                    <h3 data-i18n="activity_rate">Taux d'Activité</h3>
                    <p class="stat-value">85%</p>
                </div>
            </div>

            <!-- Recent Table -->
            <div class="admin-panel">
                <h2 data-i18n="recent_users">Récents Utilisateurs</h2>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;" data-i18n="photo">Photo</th>
                                <th data-i18n="fullname">Nom Complet</th>
                                <th data-i18n="email_address">Email</th>
                                <th data-i18n="registration_date">Date d'inscription</th>
                                <th data-i18n="status">Statut</th>
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
                                <td><span class="badge active" data-i18n="status_recent">Récent</span></td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if(count($recentClients) === 0): ?>
                            <tr>
                                <td colspan="5" style="text-align:center;" data-i18n="no_users_found">Aucun utilisateur trouvé.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/i18n.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
</html>
