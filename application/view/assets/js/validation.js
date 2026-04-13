document.addEventListener("DOMContentLoaded", function() {
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
                // Gestion AJAX pour ne pas recharger la page
                event.preventDefault(); 
                let formData = new FormData(form);
                
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
                    
                    if (form.getAttribute('action').includes('register')) {
                        if (data.includes("success")) {
                            alert("client cree avec seccee");
                            window.location.href = "login.html";
                        } else {
                            alert("Erreur lors de l'inscription : \n\n" + data);
                        }
                    } 
                    else if (form.getAttribute('action').includes('loginClient')) {
                        if (data.includes("success_client")) {
                            alert("Connexion Client réussie !");
                            window.location.href = "profile.html";
                        } else {
                            alert("Erreur côté Client : " + data);
                        }
                    }
                    else if (form.getAttribute('action').includes('loginAdmin')) {
                        if (data.includes("success_admin")) {
                            alert("Connexion Administrateur réussie !");
                            window.location.href = "admin/dashboard.php";
                        } else {
                            alert("Erreur côté Admin : " + data);
                        }
                    }
                })
                .catch(error => console.error("Erreur req AJAX:", error));
            }
        });
    });
});
