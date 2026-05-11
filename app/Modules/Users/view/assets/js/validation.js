document.addEventListener("DOMContentLoaded", function() {
    // --- WEBRTC CAMÉRA INTÉGRÉE ---
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

    const forms = document.querySelectorAll("form");
    
    forms.forEach(form => {
        form.addEventListener("submit", function(event) {
            let isValid = true;
            let errorMessage = "";

            // Validation du nom complet (nom et prenom obligatoire)
            const fullnameField = form.querySelector('[name="fullname"]');
            if (fullnameField) {
                if (fullnameField.value.trim() === "") {
                    isValid = false;
                    errorMessage += "- Le nom et prénom sont obligatoires.\n";
                }
            }

            // Validation du téléphone (8 chiffres)
            const telField = form.querySelector('[name="tel"]');
            if (telField) {
                const telRegex = /^\d{8}$/;
                if (!telRegex.test(telField.value.trim())) {
                    isValid = false;
                    errorMessage += "- Le numéro de téléphone doit contenir exactement 8 chiffres.\n";
                }
            }

            // Validation de l'email (contient @)
            const emailField = form.querySelector('[name="email"]');
            if (emailField) {
                if (!emailField.value.includes("@")) {
                    isValid = false;
                    errorMessage += "- L'adresse email doit contenir le caractère '@'.\n";
                }
            }

            // Validation du mot de passe (lettres et chiffres autorisés)
            const passwordField = form.querySelector('[name="password"]');
            if (passwordField) {
                // On autorise maintenant les chiffres pour permettre le mot de passe 'admin123'
                const alphaRegex = /^[A-Za-z0-9]+$/;
                if (!alphaRegex.test(passwordField.value)) {
                    isValid = false;
                    errorMessage += "- Le mot de passe doit être alphanumérique (lettres et chiffres sans caractères spéciaux).\n";
                }
            }

            // Si la confirmation de mot de passe existe, on peut aussi vérifier
            const confirmPasswordField = form.querySelector('[name="confirm_password"]');
            if (confirmPasswordField && passwordField) {
                if (confirmPasswordField.value !== passwordField.value) {
                    isValid = false;
                    errorMessage += "- Les mots de passe ne correspondent pas.\n";
                }
            }

            if (!isValid) {
                event.preventDefault(); // Empêche l'envoi du formulaire s'il y a des erreurs
                alert("Erreur de validation :\n\n" + errorMessage);
            } else {
                event.preventDefault(); // On gère l'envoi via AJAX

                // Vérifier la photo pour l'inscription
                if (form.getAttribute('action').includes('register')) {
                    if (!photoBase64Input || photoBase64Input.value === '') {
                        alert("Vous devez obligatoirement prendre votre photo de profil via la caméra avant de vous inscrire !");
                        return;
                    }
                }

                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("HTTP error " + response.status);
                    }
                    return response.text();
                })
                .then(data => {
                    data = data.trim();
                    
                    if (form.getAttribute('action').includes('registerAdmin')) {
                        if(data.startsWith('success_admin_created')) {
                            alert('Nouveau compte Administrateur créé avec succès !\nLa photo a été sauvegardée.');
                            window.location.href = 'login_admin.html';
                        } else {
                            alert("Erreur lors de la nomination : \n\n" + data);
                        }
                    }
                    else if (form.getAttribute('action').includes('register')) {
                        if(data.startsWith('success_client|')) {
                            const key = data.split('|')[1];
                            alert('Inscription réussie !\n\nVeuillez enregistrer cette Clé de Récupération précieusement. Elle vous sera demandée en cas d\'oubli de mot de passe.\n\nVotre Clé : ' + key);
                            window.location.href = 'login.html';
                        } else {
                            alert("Erreur lors de l'inscription : \n\n" + data);
                        }
                    } 
                    else if (form.getAttribute('action').includes('loginClient')) {
                        if (data.includes("error_blocked")) {
                            alert("Accès refusé : Votre compte a été bloqué par un administrateur.");
                        } else if (data.includes("success_client")) {
                            alert("Connexion Client réussie !");
                            window.location.href = "../../../../dashboard.php";
                        } else {
                            alert("Erreur côté Client : " + data);
                        }
                    }
                    else if (form.getAttribute('action').includes('loginAdmin')) {
                        if (data.includes("success_admin")) {
                            alert("Connexion Administrateur réussie !");
                            window.location.href = "../../../../back.php";
                        } else {
                            alert("Erreur côté Admin : " + data);
                        }
                    }
                    else if (form.getAttribute('action').includes('forgotPassword') && !form.getAttribute('action').includes('forgotPasswordClient')) {
                        if (data.includes("success_forgot_password")) {
                            alert("Un mail contenant votre mot de passe a été envoyé à l'adresse indiquée.");
                            window.location.href = "login.html";
                        } else {
                            alert("Erreur : " + data);
                        }
                    }
                    else if (form.getAttribute('action').includes('forgotPasswordClient')) {
                        if (data.startsWith("success_forgot_client|")) {
                            const pwd = data.split('|')[1];
                            alert("Clé de récupération acceptée !\n\nVoici votre mot de passe d'accès : " + pwd);
                            window.location.href = "login.html";
                        } else {
                            alert("Erreur : " + data);
                        }
                    }
                })
                .catch(error => console.error("Erreur req AJAX:", error));
            }
        });
    });
});
