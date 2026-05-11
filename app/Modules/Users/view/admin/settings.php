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
    <style>
        .lang-switcher { margin-left: 20px; z-index: 100; display: inline-block; }
        
        /* RTL overrides for Admin */
        html[dir="rtl"] .admin-layout { flex-direction: row-reverse; }
        html[dir="rtl"] .admin-header { flex-direction: row-reverse; }
        html[dir="rtl"] .admin-table th, html[dir="rtl"] .admin-table td { text-align: right; }
    </style>
    <!-- Script pour appliquer le thème immédiatement avant le rendu -->
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
                <a href="users.php" class="nav-item" data-i18n="nav_users">Utilisateurs</a>
                <a href="settings.php" class="nav-item active" data-i18n="nav_settings">Paramètres</a>
                <a style="margin-top: auto;" href="../login.html" class="nav-item logout" data-i18n="nav_logout">Déconnexion</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <h1 data-i18n="app_settings">Paramètres de l'application</h1>
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

            <!-- Settings Panel -->
            <div class="admin-panel">
                <h2 data-i18n="interface_appearance">Interface & Apparence</h2>
                <div style="padding: 20px 0; border-top: 1px solid var(--color-border);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="margin-bottom: 5px;" data-i18n="visual_theme">Thème visuel</h3>
                            <p style="color: rgba(74, 74, 74, 0.7); font-size: 0.95rem;" data-i18n="toggle_theme_desc">Basculez entre le mode clair et le mode sombre.</p>
                        </div>
                        <div>
                            <button id="themeToggleBtn" class="btn btn-outline" style="width: auto; min-width: 250px;" data-i18n="switch_dark">Passer en mode Sombre 🌙</button>
                        </div>
                    </div>
                </div>
                
                <h2 style="margin-top: 40px;" data-i18n="account_security">Sécurité du Compte</h2>
                <div style="padding: 20px 0; border-top: 1px solid var(--color-border);">
                    <p style="color: rgba(74, 74, 74, 0.7); font-size: 0.95rem;" data-i18n="upcoming_options">Options à venir...</p>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/i18n.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
</html>
