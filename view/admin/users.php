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
                <a href="dashboard.php" class="nav-item">Tableau de bord</a>
                <a href="users.php" class="nav-item active">Utilisateurs</a>
                <a href="settings.php" class="nav-item">Paramètres</a>
                <a style="margin-top: auto;" href="../login.html" class="nav-item logout">Déconnexion</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <h1>Liste complète des Utilisateurs</h1>
                <div class="admin-profile">
                    <span>Admin</span>
                    <div class="profile-avatar-small">A</div>
                </div>
            </header>

            <div class="admin-panel" style="margin-top: 30px;">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th style="width: 60px;">Photo</th>
                                <th>Nom Complet</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Sexe</th>
                                <th>Actions</th>
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
                                    <a href="editUser.php?id=<?= $c['id'] ?>" class="btn btn-outline" style="padding: 5px 15px; font-size: 0.85em;">Modifier</a>
                                    <a href="../../Controller/UserController.php?action=deleteUser&id=<?= $c['id'] ?>" class="btn btn-outline" style="padding: 5px 15px; font-size: 0.85em; background-color: #ff4d4d; color: white; border-color: #ff4d4d;" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (count($clients) === 0): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 20px;">Aucun utilisateur trouvé.</td>
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
