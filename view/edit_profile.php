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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Profil | Projet Écologique</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .edit-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .form-row {
            display: flex;
            gap: 15px;
        }
        .form-row .form-group {
            flex: 1;
        }
        
        .lang-switcher { position: absolute; top: 20px; right: 20px; z-index: 100; }
        
        /* RTL overrides */
        html[dir="rtl"] .lang-switcher { right: auto; left: 20px; }
        html[dir="rtl"] .edit-wrapper { text-align: right; }
        html[dir="rtl"] .form-control { text-align: right; }
        html[dir="rtl"] .profile-actions { flex-direction: row-reverse; }
        html[dir="rtl"] .form-row { flex-direction: row-reverse; }
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

    <div class="edit-wrapper">
        <div class="card" style="max-width: 500px;">
            <div class="card-header">
                <h2 data-i18n="edit_profile">Modifier mon Profil</h2>
                <p data-i18n="edit_profile_desc">Mettez à jour vos informations personnelles</p>
            </div>
            
            <form action="../Controller/UserController.php?action=editProfile" method="POST" id="editProfileForm">
                <div class="form-group">
                    <label for="fullname" data-i18n="fullname">Nom Complet</label>
                    <input type="text" id="fullname" name="fullname" class="form-control" value="<?= htmlspecialchars($client['fullname']) ?>" required>
                </div>
                
                <!-- On n'affiche pas l'email comme modifiable pour des raisons de sécurité, c'est l'identifiant -->
                <div class="form-group">
                    <label data-i18n="email_address">Adresse Email</label>
                    <input type="email" class="form-control" value="<?= htmlspecialchars($client['email']) ?>" disabled>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="tel" data-i18n="phone">Téléphone</label>
                        <input type="text" id="tel" name="tel" class="form-control" value="<?= htmlspecialchars($client['tel']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="birthdate" data-i18n="birthdate">Date de naissance</label>
                        <input type="date" id="birthdate" name="birthdate" class="form-control" value="<?= htmlspecialchars($client['birthdate']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="sexe" data-i18n="gender">Sexe</label>
                    <select id="sexe" name="sexe" class="form-control" required>
                        <option value="Homme" <?= $client['sexe'] == 'Homme' ? 'selected' : '' ?> data-i18n="male">Homme</option>
                        <option value="Femme" <?= $client['sexe'] == 'Femme' ? 'selected' : '' ?> data-i18n="female">Femme</option>
                        <option value="Inconnu" <?= $client['sexe'] == 'Inconnu' ? 'selected' : '' ?> data-i18n="unspecified">Non précisé</option>
                    </select>
                </div>
                
                <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
                
                <!-- SECTION PHOTO WEBRTC -->
                <div class="form-group" style="text-align: center; margin-bottom: 20px;">
                    <label style="color: var(--color-accent); font-weight: bold;" data-i18n="face_id_setup">Configuration Face ID (Optionnel)</label>
                    <p style="font-size: 0.85rem; color: #666; margin-bottom: 10px; line-height: 1.4;" data-i18n="security_photo_desc">Prenez une photo locale via votre webcam pour activer la récupération de mot de passe par reconnaissance faciale.</p>
                    
                    <button type="button" id="btn-start-camera" class="btn btn-outline" style="width: 100%; margin-bottom: 10px;" data-i18n="enable_camera">📸 Activer la caméra</button>
                    
                    <div id="camera-container" style="display: none; border: 2px dashed #ddd; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                        <video id="video-stream" autoplay playsinline style="width: 100%; max-width: 300px; border-radius: 8px; transform: scaleX(-1);"></video>
                        <canvas id="photo-canvas" width="300" height="225" style="display: none; width: 100%; max-width: 300px; border-radius: 8px; transform: scaleX(-1);"></canvas>
                        
                        <div style="margin-top: 10px; display: flex; gap: 10px; justify-content: center;">
                            <button type="button" id="btn-capture" class="btn btn-accent" style="flex: 1;" data-i18n="take_photo">Prendre la photo</button>
                            <button type="button" id="btn-retake" class="btn btn-outline" style="display: none; flex: 1;" data-i18n="retake_photo">Reprendre</button>
                        </div>
                    </div>
                    <input type="hidden" name="photo_base64" id="photo_base64" value="">
                </div>
                
                <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

                <p style="font-size: 0.85rem; color: #666; margin-bottom: 10px;" data-i18n="leave_empty_pwd">Laissez vide si vous ne souhaitez pas modifier le mot de passe</p>
                <div class="form-group">
                    <label for="password" data-i18n="new_password">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••">
                </div>

                <div class="profile-actions" style="margin-top: 25px;">
                    <button type="submit" class="btn btn-accent" style="flex: 1;" data-i18n="save_changes">Sauvegarder</button>
                    <a href="profile.php" class="btn btn-outline" style="flex: 1; text-align: center; text-decoration: none;" data-i18n="cancel">Annuler</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="assets/js/i18n.js"></script>

    <script>
        // --- LOGIQUE CAMÉRA WEBRTC ---
        const btnStartCam = document.getElementById('btn-start-camera');
        const cameraContainer = document.getElementById('camera-container');
        const videoStream = document.getElementById('video-stream');
        const photoCanvas = document.getElementById('photo-canvas');
        const btnCapture = document.getElementById('btn-capture');
        const btnRetake = document.getElementById('btn-retake');
        const photoBase64Input = document.getElementById('photo_base64');
        let mediaStream = null;

        if (btnStartCam) {
            btnStartCam.addEventListener('click', async function() {
                try {
                    mediaStream = await navigator.mediaDevices.getUserMedia({ video: true });
                    videoStream.srcObject = mediaStream;
                    btnStartCam.style.display = 'none';
                    cameraContainer.style.display = 'block';
                } catch (err) {
                    alert('Erreur: Impossible d\'accéder à la caméra. Autorisez l\'accès dans votre navigateur.');
                }
            });

            btnCapture.addEventListener('click', function() {
                const context = photoCanvas.getContext('2d');
                context.drawImage(videoStream, 0, 0, photoCanvas.width, photoCanvas.height);
                const imageData = photoCanvas.toDataURL('image/png');
                photoBase64Input.value = imageData;
                
                videoStream.style.display = 'none';
                photoCanvas.style.display = 'inline-block';
                btnCapture.style.display = 'none';
                btnRetake.style.display = 'inline-block';
                
                mediaStream.getTracks().forEach(track => track.stop());
            });

            btnRetake.addEventListener('click', async function() {
                mediaStream = await navigator.mediaDevices.getUserMedia({ video: true });
                videoStream.srcObject = mediaStream;
                photoBase64Input.value = ''; 
                photoCanvas.style.display = 'none';
                videoStream.style.display = 'inline-block';
                btnRetake.style.display = 'none';
                btnCapture.style.display = 'inline-block';
            });
        }

        // --- SOUMISSION DU FORMULAIRE ---
        document.getElementById('editProfileForm').addEventListener('submit', function(e) {

            e.preventDefault();
            
            // Validation très simple du tel
            const tel = document.getElementById('tel').value.trim();
            if(!/^\d{8}$/.test(tel)) {
                alert("Le numéro de téléphone doit contenir exactement 8 chiffres.");
                return;
            }

            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(data => {
                if(data.trim() === 'success_edit_profile') {
                    alert('Profil mis à jour avec succès !');
                    window.location.href = 'profile.php';
                } else {
                    alert('Erreur: ' + data);
                }
            })
            .catch(err => alert("Erreur réseau."));
        });
    </script>
</body>
</html>
