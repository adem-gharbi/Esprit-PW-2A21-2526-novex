<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include("../../controller/PostController.php");

$posts = getPosts();
global $postModel;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Comments</title>
<link rel="stylesheet" href="../../assets/css/admin.css">

<script>
// LIKE COMMENT
function likeComment(id){
    let xhr = new XMLHttpRequest();
    xhr.open("POST","../../controller/PostController.php",true);
    xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");

    xhr.onload=function(){
        document.getElementById("clike-"+id).innerHTML=this.responseText;
    }

    xhr.send("likeComment=1&id="+id);
}
</script>

</head>

<body>

<div class="app-shell">

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="brand">
    <div class="brand-icon">VA</div>
    <div>
      <div class="brand-title">Voyagio</div>
      <div class="brand-subtitle">Back Office</div>
    </div>
  </div>

  <nav class="menu">
    <a href="dashboard.php" class="menu-item">Dashboard</a>
    <a href="managePosts.php" class="menu-item">Posts</a>
    <a href="manageComments.php" class="menu-item active">Comments</a>
  </nav>
</aside>

<!-- CONTENT -->
<main class="content">

<header class="topbar">
  <div>
    <div class="page-title">Manage Comments</div>
    <div class="page-subtitle">Moderation system</div>
  </div>
</header>

<section class="panel-card">

<?php foreach($posts as $post){ ?>

<div style="margin-bottom:40px;">

<h2><?= htmlspecialchars($post['titre']) ?></h2>

<?php
$comments = $postModel->getComments($post['id']);
foreach($comments as $c){
?>

<div class="comment-card">

<!-- USER + DATE -->
<div class="comment-header">
    <strong><?= $c['username'] ?? 'User' ?></strong>
    <span><?= $c['date_comment'] ?? '' ?></span>
</div>

<!-- CONTENT -->
<p><?= htmlspecialchars($c['contenu']) ?></p>

<!-- ACTIONS -->
<div class="comment-actions">

<!-- LIKE -->
<button class="btn-small" onclick="likeComment(<?= $c['id'] ?>)">
❤️
</button>

<span id="clike-<?= $c['id'] ?>">
<?= $postModel->countCommentLikes($c['id']) ?>
</span>

<!-- UPDATE -->
<form action="../../controller/PostController.php" method="POST" style="display:flex; gap:5px;">
    <input type="hidden" name="id" value="<?= $c['id'] ?>">
    <input type="text" name="contenu" value="<?= htmlspecialchars($c['contenu']) ?>">
    <button class="btn-small" name="updateComment">✏️</button>
</form>

<!-- DELETE -->
<a class="btn-danger"
   href="../../controller/PostController.php?deleteComment=<?= $c['id'] ?>"
   onclick="return confirm('Delete comment?')">
   🗑
</a>

</div>

</div>

<?php } ?>

</div>

<?php } ?>

</section>

</main>
</div>

</body>
</html>