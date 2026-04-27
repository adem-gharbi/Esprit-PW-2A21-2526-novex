<?php
// Inclusion du controller (contient les fonctions du forum)
include("../../controller/PostController.php");

// Récupération de tous les posts depuis la base de données
$posts = getPosts();

// Accès global au modèle Post pour utiliser ses méthodes (likes, comments)
global $postModel;
?>

<!DOCTYPE html>
<html>
<head>

<!-- Titre de la page -->
<title>Forum</title>

<!-- Lien CSS principal -->
<link rel="stylesheet" href="../../assets/css/style.css">

<script>

// ============================
// LIKE POST (AJAX)
// ============================
function likePost(id){

    // création requête AJAX
    let xhr = new XMLHttpRequest();

    // configuration requête POST vers controller
    xhr.open("POST","../../controller/PostController.php",true);

    // type de contenu envoyé
    xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");

    // lorsque réponse reçue
    xhr.onload=function(){

        // mise à jour compteur likes post
        document.getElementById("like-"+id).innerHTML=this.responseText;
    }

    // envoi id post
    xhr.send("likePost=1&id="+id);
}

// ============================
// LIKE COMMENT (AJAX)
// ============================
function likeComment(id){

    let xhr = new XMLHttpRequest();
    xhr.open("POST","../../controller/PostController.php",true);
    xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");

    xhr.onload=function(){
        document.getElementById("clike-"+id).innerHTML=this.responseText;
    }

    xhr.send("likeComment=1&id="+id);
}

// ============================
// AJOUT COMMENTAIRE
// ============================
function addComment(id){

    // récupérer input commentaire
    let input = document.getElementById("c-"+id);

    // contenu du commentaire
    let contenu = input.value.trim();

    // découper en mots
    let words = contenu.split(/\s+/);

    // zone d’erreur
    let errorBox = document.getElementById("err-"+id);

    // si vide
    if(contenu == ""){
        alert("Commentaire vide !");
        return;
    }

    // validation max 5 mots
    if(words.length > 5){
        errorBox.style.display = "inline";
        return;
    } else {
        errorBox.style.display = "none";
    }

    // requête AJAX
    let xhr = new XMLHttpRequest();
    xhr.open("POST","../../controller/PostController.php",true);
    xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");

    xhr.onload=function(){
        // recharger page après ajout commentaire
        location.reload();
    }

    xhr.send("addComment=1&post_id="+id+"&contenu="+contenu);
}

// ============================
// VALIDATION EN TEMPS RÉEL (LIVE CHECK)
// ============================
function checkComment(id){

    let input = document.getElementById("c-"+id);
    let error = document.getElementById("err-"+id);

    // découpage mots
    let words = input.value.trim().split(/\s+/);

    // si champ vide
    if(input.value.trim() === ""){
        error.style.display = "none";
        input.style.border = "";
        return;
    }

    // si plus de 5 mots
    if(words.length > 5){
        error.style.display = "inline";
        input.style.border = "2px solid red";
    } else {
        error.style.display = "none";
        input.style.border = "";
    }
}

</script>
</head>

<body>

<!-- HEADER HERO -->
<header class="hero">
<h1>Mini Forum ✨</h1>
</header>

<!-- BOUTON AJOUT POST -->
<div style="text-align:center; margin:15px 0;">
<a class="btn" href="addPost.php">+ Ajouter un post</a>
</div>

<!-- FEED (liste posts) -->
<div class="feed">

<?php foreach($posts as $post){ ?>

<!-- CARD POST -->
<div class="post">

<!-- titre post -->
<h3><?= htmlspecialchars($post['titre']) ?></h3>

<!-- contenu post -->
<p><?= htmlspecialchars($post['contenu']) ?></p>

<!-- image post (si existe) -->
<?php if(!empty($post['image'])){ ?>
<img src="../../assets/images/<?= htmlspecialchars($post['image']) ?>">
<?php } ?>

<!-- bouton like post -->
<button class="btn" onclick="likePost(<?= $post['id'] ?>)">👍</button>

<!-- compteur likes post -->
<span id="like-<?= $post['id'] ?>">
<?= $postModel->countLikes($post['id']) ?>
</span>

<br><br>

<!-- ================= COMMENT INPUT ================= -->

<!-- champ commentaire -->
<input id="c-<?= $post['id'] ?>"
       placeholder="Commentaire..."
       oninput="checkComment(<?= $post['id'] ?>)">

<!-- message erreur max 5 mots -->
<span id="err-<?= $post['id'] ?>"
      style="display:none; color:red; font-size:12px;">
⚠️ max 5 mots
</span>

<!-- bouton envoyer commentaire -->
<button class="btn" onclick="addComment(<?= $post['id'] ?>)">
Envoyer
</button>

<?php
// récupération commentaires du post
$comments = $postModel->getComments($post['id']);

// boucle commentaires
foreach($comments as $c){
?>

<!-- COMMENT BOX -->
<div class="comment-box">

<!-- utilisateur -->
<strong><?= $c['username'] ?? 'User' ?></strong>

<!-- date commentaire -->
<small><?= $c['date_comment'] ?></small>

<!-- contenu commentaire -->
<p><?= htmlspecialchars($c['contenu']) ?></p>

<!-- like commentaire -->
<button onclick="likeComment(<?= $c['id'] ?>)">❤️</button>

<!-- compteur likes commentaire -->
<span id="clike-<?= $c['id'] ?>">
<?= $postModel->countCommentLikes($c['id']) ?>
</span>

</div>

<?php } ?>

</div>

<?php } ?>

</div>

</body>
</html>