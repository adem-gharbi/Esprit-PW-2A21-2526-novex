<!DOCTYPE html>
<!-- Déclaration du type de document HTML -->

<html>
<!-- Début du document HTML -->

<head>
    <!-- En-tête de la page (informations non visibles directement) -->

    <title>Ajouter Post</title>
    <!-- Titre affiché dans l’onglet du navigateur -->

    <link rel="stylesheet" href="../../assets/css/style.css">
    <!-- Lien vers le fichier CSS principal -->

    <link href="https://fonts.googleapis.com/css2?family=Poppins&family=Playfair+Display&display=swap" rel="stylesheet">
    <!-- Importation des polices Google Fonts -->

    <style>
    /* Style local pour afficher les erreurs */
    .error{
        color:red;              /* texte en rouge */
        font-weight:bold;       /* texte en gras */
        margin-bottom:10px;     /* espace en bas */
    }
    </style>

    <script>
    // ============================
    // VALIDATION FORMULAIRE JS
    // ============================

    function validateForm(){

        // récupérer le titre et supprimer les espaces inutiles
        let titre = document.forms["f"]["titre"].value.trim();

        // récupérer le contenu et supprimer les espaces inutiles
        let contenu = document.forms["f"]["contenu"].value.trim();

        // variable pour stocker les erreurs
        let error = "";

        // vérifier si titre est vide
        if(titre == ""){
            error += "❌ Titre obligatoire<br>";
        }

        // vérifier si contenu est vide
        if(contenu == ""){
            error += "❌ Contenu obligatoire<br>";
        }

        // vérifier si titre dépasse 3 mots
        if(titre.split(" ").length > 3){
            error += "❌ Titre max 3 mots<br>";
        }

        // vérifier si contenu dépasse 9 mots
        if(contenu.split(" ").length > 9){
            error += "❌ Contenu max 9 mots<br>";
        }

        // si erreurs existent
        if(error != ""){
            // afficher les erreurs dans la div errorBox
            document.getElementById("errorBox").innerHTML = error;

            // empêcher l’envoi du formulaire
            return false;
        }

        // si tout est correct, autoriser l’envoi
        return true;
    }
    </script>

</head>

<body>
<!-- Corps visible de la page -->

<!-- HERO HEADER -->
<header class="hero">
    <!-- Section principale en haut de page -->
    <h1>Créer un Post ✍️</h1>
    <!-- Titre principal -->
</header>

<!-- BOUTON RETOUR -->
<div style="text-align:center; margin:20px 0;">
    <!-- lien retour vers le forum -->
    <a class="btn" href="index.php">⬅ Retour au forum</a>
</div>

<!-- FORMULAIRE (CARTE) -->
<div class="card" style="width:90%; max-width:500px; margin:20px auto; padding:25px;">

    <!-- zone où s’affichent les erreurs -->
    <div id="errorBox" class="error"></div>

    <!-- FORMULAIRE D'AJOUT POST -->
    <form name="f"
          action="../../controller/PostController.php"
          method="POST"
          enctype="multipart/form-data"
          onsubmit="return validateForm()">

        <!-- champ titre -->
        <label style="display:block; margin:12px 0 5px 0; font-weight:500;">
            Titre
        </label>
        <input type="text" name="titre" required>
        <!-- input texte obligatoire -->

        <!-- champ contenu -->
        <label style="display:block; margin:12px 0 5px 0; font-weight:500;">
            Contenu
        </label>
        <textarea name="contenu" rows="4" required></textarea>
        <!-- zone de texte obligatoire -->

        <!-- champ image -->
        <label style="display:block; margin:12px 0 5px 0; font-weight:500;">
            Image (optionnel)
        </label>
        <input type="file" name="image" accept="image/*">
        <!-- upload image facultatif -->

        <!-- bouton submit -->
        <button class="btn" type="submit" name="addPost"
                style="width:100%; margin-top:20px;">
            📤 Publier
        </button>

    </form>

</div>

</body>
</html>