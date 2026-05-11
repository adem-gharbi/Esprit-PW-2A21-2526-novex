<?php
require_once 'check_auth.php';
require_once '../../Controller/UserController.php';
$userController = new UserController();
$clients = $userController->getAllClients();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilisateurs | Projet Écologique</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .lang-switcher { margin-left: 20px; z-index: 100; display: inline-block; }
        
        /* RTL overrides for Admin */
        html[dir="rtl"] .admin-layout { flex-direction: row-reverse; }
        html[dir="rtl"] .admin-header { flex-direction: row-reverse; }
        html[dir="rtl"] .admin-table th, html[dir="rtl"] .admin-table td { text-align: right; }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
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
                <a href="dashboard.php" class="nav-item" data-i18n="nav_dashboard">Tableau de bord</a>
                <a href="users.php" class="nav-item active" data-i18n="nav_users">Utilisateurs</a>
                <a href="settings.php" class="nav-item" data-i18n="nav_settings">Paramètres</a>
                <a style="margin-top: auto;" href="../login.html" class="nav-item logout" data-i18n="nav_logout">Déconnexion</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <h1 data-i18n="all_users_list">Liste complète des Utilisateurs</h1>
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

            <div class="admin-panel" style="margin-top: 30px;">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th style="width: 60px;" data-i18n="photo">Photo</th>
                                <th data-i18n="fullname">Nom Complet</th>
                                <th data-i18n="email_address">Email</th>
                                <th data-i18n="phone">Téléphone</th>
                                <th data-i18n="gender">Sexe</th>
                                <th data-i18n="status">Statut</th>
                                <th data-i18n="actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($clients as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c['id']) ?></td>
                                <td>
                                    <?php if (!empty($c['profile_photo'])): ?>
                                        <img src="../../<?= htmlspecialchars($c['profile_photo']) ?>" alt="Photo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--color-accent);">
                                    <?php else: ?>
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #e0e0e0; color: #666; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; margin: 0 auto;">N/A</div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($c['fullname']) ?></td>
                                <td><?= htmlspecialchars($c['email']) ?></td>
                                <td><?= htmlspecialchars($c['tel']) ?></td>
                                <td><?= htmlspecialchars($c['sexe']) ?></td>
                                <td>
                                    <?php if (isset($c['status']) && $c['status'] === 'blocked'): ?>
                                        <span style="background-color: #ffcccc; color: #cc0000; padding: 3px 8px; border-radius: 12px; font-size: 0.8em; font-weight: bold;">Bloqué ❌</span>
                                    <?php else: ?>
                                        <span style="background-color: #ccffcc; color: #008000; padding: 3px 8px; border-radius: 12px; font-size: 0.8em; font-weight: bold;">Actif ✅</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="editUser.php?id=<?= $c['id'] ?>" class="btn btn-outline" style="padding: 5px 15px; font-size: 0.85em;" data-i18n="edit">Modifier</a>
                                    
                                    <?php if (isset($c['status']) && $c['status'] === 'blocked'): ?>
                                        <a href="../../Controller/UserController.php?action=toggleStatus&id=<?= $c['id'] ?>&status=active" class="btn btn-outline" style="padding: 5px 15px; font-size: 0.85em; background-color: #28a745; color: white; border-color: #28a745;">Débloquer</a>
                                    <?php else: ?>
                                        <a href="../../Controller/UserController.php?action=toggleStatus&id=<?= $c['id'] ?>&status=blocked" class="btn btn-outline" style="padding: 5px 15px; font-size: 0.85em; background-color: #ff9800; color: white; border-color: #ff9800;">Bloquer</a>
                                    <?php endif; ?>

                                    <a href="../../Controller/UserController.php?action=deleteUser&id=<?= $c['id'] ?>" class="btn btn-outline" style="padding: 5px 15px; font-size: 0.85em; background-color: #ff4d4d; color: white; border-color: #ff4d4d;" onclick="return confirm(getTranslation('confirm_delete') || 'Êtes-vous sûr de vouloir supprimer cet utilisateur ?');" data-i18n="delete">Supprimer</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (count($clients) === 0): ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 20px;" data-i18n="no_users_found">Aucun utilisateur trouvé.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/i18n.js"></script>
    <script>
        // Helper function for confirm translation
        function getTranslation(key) {
            const currentLang = localStorage.getItem('lang') || 'fr';
            return translations[currentLang] ? translations[currentLang][key] : null;
        }
    </script>
    <script src="../assets/js/theme.js"></script>
</body>
</html>
