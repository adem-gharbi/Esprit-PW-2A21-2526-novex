<?php
include("../../controller/PostController.php");

$user = $postModel->getUser($_GET['id']);
?>

<h2><?= $user['username'] ?></h2>

<p><?= $user['bio'] ?></p>

<img src="../../assets/images/<?= $user['image'] ?>" width="100">

<p>Followers: <?= $postModel->countFollowers($_GET['id']) ?></p>

<?php
$isFollowing = $postModel->isFollowing(1,$_GET['id']);
?>

<?php if($isFollowing){ ?>
<button onclick="unfollowUser(<?= $_GET['id'] ?>)">Unfollow</button>
<?php } else { ?>
<button onclick="followUser(<?= $_GET['id'] ?>)">Follow</button>
<?php } ?>