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
    </style>
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
                <h1>Modifier l'utilisateur #<?= htmlspecialchars($client['id']) ?></h1>
                <div class="admin-profile">
                    <span>Admin</span>
                    <div class="profile-avatar-small">A</div>
                </div>
            </header>

            <div class="admin-panel" style="margin-top: 30px;">
                <form action="../../Controller/UserController.php?action=editUser" method="POST" class="edit-form">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($client['id']) ?>">
                    
                    <div class="form-group">
                        <label for="fullname">Nom complet</label>
                        <input type="text" id="fullname" name="fullname" class="form-control" value="<?= htmlspecialchars($client['fullname']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Adresse Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($client['email']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="birthdate">Date de naissance</label>
                        <input type="date" id="birthdate" name="birthdate" class="form-control" value="<?= htmlspecialchars($client['birthdate']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="tel">Téléphone</label>
                        <input type="tel" id="tel" name="tel" class="form-control" value="<?= htmlspecialchars($client['tel']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="sexe">Sexe</label>
                        <select id="sexe" name="sexe" class="form-control" required>
                            <option value="homme" <?= $client['sexe'] == 'homme' ? 'selected' : '' ?>>Homme</option>
                            <option value="femme" <?= $client['sexe'] == 'femme' ? 'selected' : '' ?>>Femme</option>
                        </select>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button type="submit" class="btn btn-accent" style="flex: 1; border: none; cursor: pointer; padding: 12px; border-radius: 8px;">Enregistrer</button>
                        <a href="users.php" class="btn btn-outline" style="flex: 1; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; border-radius: 8px;">Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="../assets/js/theme.js"></script>
</body>
</html>
