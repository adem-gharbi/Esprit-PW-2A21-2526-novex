<?php
session_start();
require_once __DIR__ . '/../Controller/UserController.php';

if (!isset($_SESSION['client_email'])) {
    header('Location: login.html');
    exit();
}

$userController = new UserController();
$client = $userController->getClientByEmail($_SESSION['client_email']);

if (!$client) {
    session_destroy();
    header('Location: login.html');
    exit();
}

// Format the date if it's set
$formattedDate = "Non renseignée";
if (!empty($client['birthdate'])) {
    $dateObj = new DateTime($client['birthdate']);
    $formattedDate = $dateObj->format('d M Y');
}

$initial = strtoupper(substr($client['fullname'], 0, 1));
$photoUrl = null;
if (!empty($client['profile_photo']) && $client['profile_photo'] !== 'erreur_no_photo') {
    // Si c'est une URL externe (Google), on l'utilise telle quelle, sinon on préfixe le chemin relatif
    if (filter_var($client['profile_photo'], FILTER_VALIDATE_URL)) {
        $photoUrl = $client['profile_photo'];
    } else {
        $photoUrl = '../' . $client['profile_photo'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil | Projet Écologique</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="description" content="Aperçu de vos coordonnées et détails de profil.">
    <style>
        .lang-switcher { position: absolute; top: 20px; right: 20px; z-index: 100; }
        
        /* RTL overrides */
        html[dir="rtl"] .lang-switcher { right: auto; left: 20px; }
        html[dir="rtl"] .auth-wrapper { text-align: right; }
        html[dir="rtl"] .form-control { text-align: right; }
        html[dir="rtl"] .info-group { display: flex; justify-content: space-between; flex-direction: row-reverse; }
        html[dir="rtl"] .profile-actions { flex-direction: row-reverse; }
    </style>
</head>

<body>

    <!-- Language Switcher -->
    <div class="lang-switcher">
        <select onchange="changeLanguage(this.value)" style="padding: 5px; border-radius: 5px; background: white; border: 1px solid #ccc; font-size: 1rem;">
            <option value="fr">🇫🇷 FR</option>
            <option value="en">🇬🇧 EN</option>
            <option value="ar">🇸🇦 AR</option>
        </select>
    </div>

    <div class="auth-wrapper profile-wrapper">
        <div class="card">
            <div class="card-header">
                <?php if ($photoUrl): ?>
                    <img src="<?= htmlspecialchars($photoUrl) ?>" alt="Photo de profil" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; border: 3px solid var(--color-accent);">
                <?php else: ?>
                    <div class="profile-avatar"><?= $initial ?></div>
                <?php endif; ?>
                <h2><?= htmlspecialchars($client['fullname']) ?></h2>
                <p data-i18n="eco_member">Membre Écologique</p>
            </div>

            <div class="profile-info">
                <div class="info-group">
                    <span class="info-label" data-i18n="fullname">Nom complet</span>
                    <span class="info-value"><?= htmlspecialchars($client['fullname']) ?></span>
                </div>

                <div class="info-group">
                    <span class="info-label" data-i18n="email_address">Adresse Email</span>
                    <span class="info-value"><?= htmlspecialchars($client['email']) ?></span>
                </div>

                <div class="info-group">
                    <span class="info-label" data-i18n="birthdate">Date de naissance</span>
                    <span class="info-value"><?= htmlspecialchars($formattedDate) ?></span>
                </div>

                <div class="info-group">
                    <span class="info-label" data-i18n="phone">Téléphone</span>
                    <span class="info-value"><?= htmlspecialchars($client['tel']) ?></span>
                </div>

                <div class="info-group">
                    <span class="info-label" data-i18n="gender">Sexe</span>
                    <span class="info-value"><?= htmlspecialchars($client['sexe']) ?></span>
                </div>
            </div>

            <div class="profile-actions">
                <a href="edit_profile.php" class="btn btn-outline" style="flex: 1; text-align: center; text-decoration: none;" data-i18n="edit_profile">Modifier le profil</a>
                <a href="../Controller/UserController.php?action=logoutClient" class="btn btn-accent" style="flex: 1; text-align: center; text-decoration: none;" data-i18n="logout">Déconnexion</a>
            </div>
        </div>
    </div>

    <script src="assets/js/i18n.js"></script>
</body>

</html>