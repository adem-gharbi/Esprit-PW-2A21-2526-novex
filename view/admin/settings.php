<?php
require_once 'check_auth.php';
// On vérifie que la config et le controlleur soient accessibles si besoin plus tard
require_once '../../Controller/UserController.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres | Projet Écologique</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <!-- Script pour appliquer le thème immédiatement avant le rendu -->
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
                <a href="users.php" class="nav-item">Utilisateurs</a>
                <a href="settings.php" class="nav-item active">Paramètres</a>
                <a style="margin-top: auto;" href="../login.html" class="nav-item logout">Déconnexion</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <h1>Paramètres de l'application</h1>
                <div class="admin-profile">
                    <span>Admin</span>
                    <div class="profile-avatar-small">A</div>
                </div>
            </header>

            <!-- Settings Panel -->
            <div class="admin-panel">
                <h2>Interface & Apparence</h2>
                <div style="padding: 20px 0; border-top: 1px solid var(--color-border);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="margin-bottom: 5px;">Thème visuel</h3>
                            <p style="color: rgba(74, 74, 74, 0.7); font-size: 0.95rem;">Basculez entre le mode clair et le mode sombre.</p>
                        </div>
                        <div>
                            <button id="themeToggleBtn" class="btn btn-outline" style="width: auto; min-width: 250px;">Passer en mode Sombre 🌙</button>
                        </div>
                    </div>
                </div>
                
                <h2 style="margin-top: 40px;">Sécurité du Compte</h2>
                <div style="padding: 20px 0; border-top: 1px solid var(--color-border);">
                    <p style="color: rgba(74, 74, 74, 0.7); font-size: 0.95rem;">Options à venir...</p>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/theme.js"></script>
</body>
</html>
