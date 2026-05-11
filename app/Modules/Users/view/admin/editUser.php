<?php
require_once 'check_auth.php';
require_once '../../Controller/UserController.php';

if (!isset($_GET['id'])) {
    header('Location: users.php');
    exit();
}

$userController = new UserController();
$client = $userController->getClientById($_GET['id']);

if (!$client) {
    die("Utilisateur non trouvé.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Utilisateur | Projet Écologique</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .edit-form { max-width: 600px; background: var(--bg-card); padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        
        .lang-switcher { margin-left: 20px; z-index: 100; display: inline-block; }
        
        /* RTL overrides for Admin */
        html[dir="rtl"] .admin-layout { flex-direction: row-reverse; }
        html[dir="rtl"] .admin-header { flex-direction: row-reverse; }
        html[dir="rtl"] .edit-form { text-align: right; }
        html[dir="rtl"] .form-control { text-align: right; }
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
                <h1><span data-i18n="edit_user_title">Modifier l'utilisateur</span> #<?= htmlspecialchars($client['id']) ?></h1>
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
                <form action="../../Controller/UserController.php?action=editUser" method="POST" class="edit-form">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($client['id']) ?>">
                    
                    <div class="form-group">
                        <label for="fullname" data-i18n="fullname">Nom complet</label>
                        <input type="text" id="fullname" name="fullname" class="form-control" value="<?= htmlspecialchars($client['fullname']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email" data-i18n="email_address">Adresse Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($client['email']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="birthdate" data-i18n="birthdate">Date de naissance</label>
                        <input type="date" id="birthdate" name="birthdate" class="form-control" value="<?= htmlspecialchars($client['birthdate']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="tel" data-i18n="phone">Téléphone</label>
                        <input type="tel" id="tel" name="tel" class="form-control" value="<?= htmlspecialchars($client['tel']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="sexe" data-i18n="gender">Sexe</label>
                        <select id="sexe" name="sexe" class="form-control" required>
                            <option value="homme" <?= $client['sexe'] == 'homme' ? 'selected' : '' ?> data-i18n="male">Homme</option>
                            <option value="femme" <?= $client['sexe'] == 'femme' ? 'selected' : '' ?> data-i18n="female">Femme</option>
                        </select>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button type="submit" class="btn btn-accent" style="flex: 1; border: none; cursor: pointer; padding: 12px; border-radius: 8px;" data-i18n="save">Enregistrer</button>
                        <a href="users.php" class="btn btn-outline" style="flex: 1; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; border-radius: 8px;" data-i18n="cancel">Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="../assets/js/i18n.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
</html>
