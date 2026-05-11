/**
 * Voice Input Script
 * Ajoute la saisie vocale multilingue aux champs de formulaire.
 */

document.addEventListener('DOMContentLoaded', () => {
    // Vérifier si le navigateur supporte l'API Speech Recognition
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    
    if (!SpeechRecognition) {
        console.warn("La reconnaissance vocale n'est pas supportée par ce navigateur.");
        // Masquer tous les boutons vocaux si non supporté
        document.querySelectorAll('.voice-btn').forEach(btn => btn.style.display = 'none');
        return;
    }

    const recognition = new SpeechRecognition();
    recognition.continuous = false; // Ne pas écouter en continu
    recognition.interimResults = true; // Afficher les résultats pendant qu'on parle

    // Fonction pour obtenir la locale de reconnaissance basée sur la langue sélectionnée
    function getSpeechLang() {
        const lang = localStorage.getItem('lang') || 'fr';
        switch(lang) {
            case 'ar': return 'ar-SA';
            case 'en': return 'en-US';
            case 'fr': 
            default: return 'fr-FR';
        }
    }

    let currentInputId = null;
    let currentVoiceBtn = null;
    let originalValue = "";

    // Attacher l'événement à tous les boutons vocaux
    document.querySelectorAll('.voice-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Si le même bouton est cliqué, on arrête
            if (currentVoiceBtn === btn && btn.classList.contains('listening')) {
                recognition.stop();
                return;
            }

            // Réinitialiser les autres boutons
            document.querySelectorAll('.voice-btn').forEach(b => {
                b.classList.remove('listening');
                b.style.color = '#888';
            });

            currentInputId = btn.getAttribute('data-target');
            currentVoiceBtn = btn;
            
            const inputField = document.getElementById(currentInputId);
            if(inputField) {
                originalValue = inputField.value; // Sauvegarder la valeur actuelle
            }
            
            // Configurer la langue juste avant de démarrer
            recognition.lang = getSpeechLang();

            try {
                recognition.start();
                btn.classList.add('listening');
                btn.style.color = '#f44336'; // Rouge pour indiquer l'écoute
            } catch (err) {
                console.error("Erreur de démarrage micro :", err);
            }
        });
    });

    recognition.onresult = (event) => {
        let interimTranscript = '';
        let finalTranscript = '';

        for (let i = event.resultIndex; i < event.results.length; ++i) {
            if (event.results[i].isFinal) {
                finalTranscript += event.results[i][0].transcript;
            } else {
                interimTranscript += event.results[i][0].transcript;
            }
        }

        if (currentInputId) {
            const inputField = document.getElementById(currentInputId);
            if (inputField) {
                // Remplacer le contenu par ce qui est détecté
                inputField.value = finalTranscript || interimTranscript;
            }
        }
    };

    recognition.onerror = (event) => {
        console.error("Erreur de reconnaissance vocale:", event.error);
        
        if (event.error === 'not-allowed') {
            alert("Accès au microphone refusé. Veuillez l'autoriser dans les paramètres de votre navigateur (en haut à gauche de l'URL).");
        } else if (event.error === 'network') {
            alert("Erreur réseau. La reconnaissance vocale nécessite une connexion internet (service Google).");
        } else if (event.error === 'no-speech') {
            // Ignorer silencieusement si l'utilisateur ne dit rien
        } else {
            console.warn("Erreur Speech API :", event.error);
        }

        if (currentVoiceBtn) {
            currentVoiceBtn.classList.remove('listening');
            currentVoiceBtn.style.color = '#888';
        }
    };

    recognition.onend = () => {
        if (currentVoiceBtn) {
            currentVoiceBtn.classList.remove('listening');
            currentVoiceBtn.style.color = '#888'; // Revenir à la couleur normale
            currentVoiceBtn = null;
        }
        currentInputId = null;
    };
});
