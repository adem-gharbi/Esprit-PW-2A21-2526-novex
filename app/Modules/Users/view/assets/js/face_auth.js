document.addEventListener('DOMContentLoaded', function() {
    const btnKey = document.getElementById('btn-mode-key');
    const btnFace = document.getElementById('btn-mode-faceid');
    const containerKey = document.getElementById('mode-key-container');
    const containerFace = document.getElementById('mode-faceid-container');
    const emailInput = document.getElementById('email');
    const mainSubmitBtn = document.getElementById('main-submit-btn');
    const recoveryKeyInput = document.getElementById('recovery_key');
    
    // FaceID Elements
    const faceLoadingMsg = document.getElementById('face-loading-msg');
    const faceVideo = document.getElementById('face-video');
    const btnStartFaceScan = document.getElementById('btn-start-face-scan');
    const referenceImage = document.getElementById('reference-image');
    const faceidToken = document.getElementById('faceid-token');

    let modelsLoaded = false;
    let currentMode = 'key';
    let mediaStream = null;

    if (!btnKey || !btnFace) return;

    // Toggle Modes
    btnKey.addEventListener('click', () => {
        currentMode = 'key';
        btnKey.style.background = 'var(--color-accent)';
        btnKey.style.color = 'white';
        btnFace.style.background = 'transparent';
        btnFace.style.color = 'var(--color-text)';
        
        containerKey.style.display = 'block';
        containerFace.style.display = 'none';
        
        mainSubmitBtn.style.display = 'block';
        recoveryKeyInput.required = true;
        
        if(mediaStream) {
            mediaStream.getTracks().forEach(t => t.stop());
        }
    });

    btnFace.addEventListener('click', async () => {
        currentMode = 'faceid';
        btnFace.style.background = '#1976d2';
        btnFace.style.color = 'white';
        btnKey.style.background = 'transparent';
        btnKey.style.color = 'var(--color-text)';
        
        containerKey.style.display = 'none';
        containerFace.style.display = 'block';
        
        mainSubmitBtn.style.display = 'none'; // Le bouton de récupération se cachera au profit du stream
        recoveryKeyInput.required = false;

        if (!modelsLoaded) {
            await loadModels();
        }
    });

    async function loadModels() {
        faceLoadingMsg.textContent = "Téléchargement des réseaux de neurones (1/3)...";
        try {
            const MODEL_URL = 'https://justadudewhohacks.github.io/face-api.js/models';
            await faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL);
            faceLoadingMsg.textContent = "Téléchargement des réseaux de neurones (2/3)...";
            await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
            faceLoadingMsg.textContent = "Téléchargement des réseaux de neurones (3/3)...";
            await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
            
            modelsLoaded = true;
            faceLoadingMsg.textContent = "✅ IA Prête. Saisissez votre email puis lancez le scan.";
            btnStartFaceScan.style.display = 'inline-block';
        } catch (e) {
            console.error(e);
            faceLoadingMsg.textContent = "❌ Erreur de chargement de l'IA (Vérifiez votre connexion internet).";
        }
    }

    btnStartFaceScan.addEventListener('click', async () => {
        if (!emailInput.value) {
            alert('Veuillez d\'abord saisir votre Adresse Email dans le champ pour que l\'IA trouve votre photo d\'origine.');
            return;
        }

        btnStartFaceScan.textContent = "Recherche de la photo d'origine...";
        
        // Fetch Reference Photo Path from Backend
        const formData = new FormData();
        formData.append('email', emailInput.value);
        
        try {
            const response = await fetch('../Controller/UserController.php?action=getPhotoByEmail', {
                method: 'POST',
                body: formData
            });
            const imgPath = await response.text();
            
            if (imgPath.includes("erreur") || imgPath.trim() === '') {
                alert("Impossible de trouver une photo de profil pour cet Email.");
                btnStartFaceScan.textContent = "📡 Lancer le Scan Faciale";
                return;
            }

            // Set Reference Image
            referenceImage.src = "../" + imgPath.trim();
            
            referenceImage.onerror = () => {
                alert("Impossible de charger votre photo de profil. L'image a peut-être été supprimée ou le chemin est incorrect.");
                btnStartFaceScan.textContent = "📡 Lancer le Scan Faciale";
            };

            referenceImage.onload = async () => {
                btnStartFaceScan.textContent = "Extraction de l'ADN Visage ⏳...";
                try {
                    const refDetection = await faceapi.detectSingleFace(referenceImage).withFaceLandmarks().withFaceDescriptor();
                    if (!refDetection) {
                        alert("L'IA n'arrive pas à détecter de visage sur votre photo enregistrée. Assurez-vous que la photo contient un visage clair.");
                        btnStartFaceScan.textContent = "📡 Lancer le Scan Faciale";
                        return;
                    }
                    
                    const faceMatcher = new faceapi.FaceMatcher(refDetection);
                    startWebcam(faceMatcher);
                } catch(e) {
                     alert("Erreur de traitement d'image : " + e);
                     btnStartFaceScan.textContent = "📡 Lancer le Scan Faciale";
                }
            };
        } catch (e) {
            alert("Erreur serveur.");
            btnStartFaceScan.textContent = "📡 Lancer le Scan Faciale";
        }
    });

    async function startWebcam(faceMatcher) {
        btnStartFaceScan.style.display = 'none';
        faceLoadingMsg.textContent = "Veuillez regarder la caméra...";
        faceVideo.style.display = 'block';

        mediaStream = await navigator.mediaDevices.getUserMedia({ video: true });
        faceVideo.srcObject = mediaStream;

        faceVideo.addEventListener('play', async () => {
             // scan chaque seconde jusqu'à trouver le match
             const scanInterval = setInterval(async () => {
                 if(faceVideo.paused || faceVideo.ended) return clearInterval(scanInterval);

                 const detection = await faceapi.detectSingleFace(faceVideo).withFaceLandmarks().withFaceDescriptor();
                 if (detection) {
                     const bestMatch = faceMatcher.findBestMatch(detection.descriptor);
                     
                     // _distance < 0.55 signifie une bonne correspondance algorithmique
                     if (bestMatch.distance < 0.55) {
                         clearInterval(scanInterval);
                         faceVideo.pause();
                         mediaStream.getTracks().forEach(t => t.stop());
                         
                         faceLoadingMsg.style.color = 'green';
                         faceLoadingMsg.innerHTML = "✅ Identité confirmée ! Match: " + ( (1 - bestMatch.distance)*100 ).toFixed(1) + "%";
                         
                         // Autorisaton Accordée
                         faceidToken.value = "AUTHORIZED_FACE_" + emailInput.value;
                         
                         setTimeout(() => {
                             submitFaceID();
                         }, 1500);
                     } else {
                         faceLoadingMsg.innerHTML = "❌ Visage non reconnu. (Correspondance trop faible)";
                         faceLoadingMsg.style.color = 'red';
                     }
                 }
             }, 1000);
        });
    }

    function submitFaceID() {
        const formData = new FormData();
        formData.append('email', emailInput.value);
        formData.append('faceid_token', faceidToken.value);

        fetch('../Controller/UserController.php?action=recoverPasswordFaceID', {
            method: 'POST',
            body: formData
        })
        .then(r => r.text())
        .then(data => {
            if (data.startsWith('success_forgot_client|')) {
                const pwd = data.split('|')[1];
                alert("✨ FACE ID VALIDÉ !\n\nVoici votre mot de passe pour le compte " + emailInput.value + " :\n" + pwd);
                window.location.href = 'login.html';
            } else {
                alert("Erreur Serveur: " + data);
            }
        });
    }
});
