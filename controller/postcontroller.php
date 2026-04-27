<?php

// ============================
// 📌 IMPORT DU MODEL POST
// 👉 On charge la classe Post pour accéder à la base de données
// ============================
require_once __DIR__ . "/../model/Post.php";

// 🧠 Création d'un objet Post (accès aux fonctions du modèle)
$postModel = new Post();


// ============================
// 👍 LIKE POST
// ============================
// Vérifie si l'utilisateur a cliqué sur "likePost"
if(isset($_POST['likePost']) && isset($_POST['id'])){

    // 🔢 conversion de l'id en entier pour sécurité
    $id = intval($_POST['id']);

    // ❤️ appel du modèle pour ajouter un like
    $postModel->likePost($id);

    // 📤 retourne le nombre de likes mis à jour
    echo $postModel->countLikes($id);

    // ⛔ stop script après réponse AJAX
    exit();
}


// ============================
// ❤️ LIKE COMMENTAIRE
// ============================
// Vérifie si like sur commentaire
if(isset($_POST['likeComment']) && isset($_POST['id'])){

    // 🔢 sécurisation ID commentaire
    $id = intval($_POST['id']);

    // ❤️ ajout like commentaire
    $postModel->likeComment($id);

    // 📤 retourne nombre de likes commentaire
    echo $postModel->countCommentLikes($id);

    exit();
}


// ============================
// 💬 AJOUT COMMENTAIRE
// ============================
// Vérifie si demande ajout commentaire
if(isset($_POST['addComment'])){

    // 🔍 vérifie que les champs ne sont pas vides
    if(!empty($_POST['post_id']) && !empty($_POST['contenu'])){

        // 🔢 sécurisation id post
        $post_id = intval($_POST['post_id']);

        // ✂️ nettoyage texte commentaire
        $contenu = trim($_POST['contenu']);

        // 🔥 VALIDATION BACKEND (sécurité importante)
        // 👉 interdit plus de 5 mots
        if(str_word_count($contenu) > 5){
            echo "error: max 5 words";
            exit();
        }

        // 💾 ajout commentaire en base
        $postModel->addComment($post_id, $contenu);

        // 📤 réponse succès AJAX
        echo "ok";
    }

    exit();
}


// ============================
// ✏️ MODIFIER COMMENTAIRE
// ============================
// Vérifie modification commentaire
if(isset($_POST['updateComment'])){

    if(!empty($_POST['id']) && !empty($_POST['contenu'])){

        // 🔢 sécurisation ID commentaire
        $id = intval($_POST['id']);

        // ✂️ nettoyage contenu
        $contenu = trim($_POST['contenu']);

        // 💾 update en base
        $postModel->updateComment($id, $contenu);
    }

    // 🔁 retour page précédente
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


// ============================
// ❌ SUPPRIMER COMMENTAIRE
// ============================
// Vérifie suppression commentaire
if(isset($_GET['deleteComment'])){

    // 🔢 sécurisation id
    $id = intval($_GET['deleteComment']);

    // 🗑 suppression en base
    $postModel->deleteComment($id);

    // 🔁 retour page précédente
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}


// ============================
// ➕ AJOUT POST
// ============================
// Vérifie ajout post
if(isset($_POST['addPost'])){

    // 📝 récupération titre
    $titre = trim($_POST['titre']);

    // 📝 récupération contenu
    $contenu = trim($_POST['contenu']);

    // 🖼 image vide par défaut
    $image = "";

    // 📁 vérifie si image uploadée
    if(isset($_FILES['image']) && $_FILES['image']['name'] != ""){

        // 🔄 nettoyage nom image
        $image = str_replace(" ", "_", $_FILES['image']['name']);

        // 📂 fichier temporaire
        $tmp = $_FILES['image']['tmp_name'];

        // 📥 déplacement vers dossier images
        move_uploaded_file($tmp, __DIR__."/../assets/images/".$image);
    }

    // 💾 insertion post en base
    $postModel->addPost($titre, $contenu, $image);

    // 🔁 redirection vers page forum
    header("Location: ../view/front/index.php");
    exit();
}


// ============================
// 🗑 SUPPRIMER POST
// ============================
if(isset($_GET['delete'])){

    // 🔢 sécurisation id post
    $id = intval($_GET['delete']);

    // 🗑 suppression post
    $postModel->deletePost($id);

    // 🔁 retour page admin
    header("Location: ../view/back/managePosts.php");
    exit();
}


// ============================
// 🔄 MODIFIER POST
// ============================
if(isset($_POST['updatePost'])){

    // 🔢 id post
    $id = intval($_POST['id']);

    // 📝 titre
    $titre = trim($_POST['titre']);

    // 📝 contenu
    $contenu = trim($_POST['contenu']);

    // 🖼 image vide
    $image = "";

    // 📁 si nouvelle image
    if(isset($_FILES['image']) && $_FILES['image']['name'] != ""){

        // 🔄 nettoyage nom image
        $image = str_replace(" ", "_", $_FILES['image']['name']);

        // 📂 fichier temporaire
        $tmp = $_FILES['image']['tmp_name'];

        // 📥 upload image
        move_uploaded_file($tmp, __DIR__."/../assets/images/".$image);
    }

    // 💾 update post
    $postModel->updatePost($id, $titre, $contenu, $image);

    // 🔁 retour admin
    header("Location: ../view/back/managePosts.php");
    exit();
}


// ============================
// 📦 FONCTIONS UTILITAIRES FRONT
// ============================

// 📌 récupérer tous les posts
function getPosts(){
    global $postModel;
    return $postModel->getAllPosts();
}

// 📌 récupérer un seul post
function getPost($id){
    global $postModel;
    return $postModel->getPostById($id);
}

?>