<?php
require_once __DIR__ . "/../model/Post.php";

$postModel = new Post();

// ============================
// REACTIONS EMOJI
// ============================
if(isset($_POST['setReaction'])){
    $postModel->setReaction($_POST['post_id'], $_POST['type']);
    echo json_encode($postModel->getReactions($_POST['post_id']));
    exit();
}

// ============================
// GET REACTIONS (poll)
// ============================
if(isset($_GET['getReactions'])){
    echo json_encode($postModel->getReactions($_GET['post_id']));
    exit();
}

// ============================
// LIKE COMMENT
// ============================
if(isset($_POST['likeComment'])){
    $postModel->likeComment($_POST['id']);
    echo $postModel->countCommentLikes($_POST['id']);
    exit();
}

// ============================
// ADD COMMENT (avec parent_id et notifications)
// ============================
if(isset($_POST['addComment'])){
    $parent_id = (isset($_POST['parent_id']) && $_POST['parent_id'] != '')
                 ? intval($_POST['parent_id'])
                 : null;
    $username = isset($_POST['username']) ? trim($_POST['username']) : 'User';
    $comment_id = $postModel->addComment($_POST['post_id'], $_POST['contenu'], $parent_id, $username);

    // Notification au propriétaire du post (user_id=1 par défaut)
    $post = $postModel->getPostById($_POST['post_id']);
    if($post){
        $who = $parent_id ? "a répondu à un commentaire" : "a commenté";
        $postModel->addNotification(
            $post['user_id'] ?? 1,
            ($username ?: 'Quelqu\'un') . " " . $who . " sur : " . $post['titre'],
            $_POST['post_id']
        );
    }
    echo "ok";
    exit();
}

// ============================
// ADD POST
// ============================
if(isset($_POST['addPost'])){

    $titre   = trim($_POST['titre']);
    $contenu = trim($_POST['contenu']);
    $image   = "";
    $user_id = 1;

    if(isset($_FILES['image']) && $_FILES['image']['name'] != ""){
        $image = str_replace(" ", "_", $_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'],
            __DIR__."/../assets/images/".$image);
    }

    $scheduled_at = null;
    if(isset($_POST['scheduled_at']) && trim($_POST['scheduled_at']) != ""){
        $scheduled_at = trim($_POST['scheduled_at']);
    }

    $post_id = $postModel->addPost($titre, $contenu, $image, $user_id, $scheduled_at);

    // Associer les tags
    if(isset($_POST['tags']) && is_array($_POST['tags'])){
        $postModel->setPostTags($post_id, $_POST['tags']);
    }

    header("Location: ../view/front/index.php?success=1");
    exit();
}

// ============================
// UPDATE POST
// ============================
if(isset($_POST['updatePost'])){
    $id      = $_POST['id'];
    $titre   = $_POST['titre'];
    $contenu = $_POST['contenu'];
    $image   = "";

    if(isset($_FILES['image']) && $_FILES['image']['name'] != ""){
        $image = str_replace(" ", "_", $_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'],
            __DIR__."/../assets/images/".$image);
    }

    $postModel->updatePost($id, $titre, $contenu, $image);

    // Mise à jour des tags
    if(isset($_POST['tags']) && is_array($_POST['tags'])){
        $postModel->setPostTags($id, $_POST['tags']);
    }

    header("Location: ../view/back/managePosts.php?updated=1");
    exit();
}

// ============================
// UPDATE COMMENT
// ============================
if(isset($_POST['updateComment'])){
    $postModel->updateComment($_POST['id'], $_POST['contenu']);
    header("Location: ../view/back/manageComments.php?updated=1");
    exit();
}

// ============================
// DELETE POST
// ============================
if(isset($_GET['delete'])){
    $postModel->deletePost($_GET['delete']);
    header("Location: ../view/back/managePosts.php");
    exit();
}

// ============================
// DELETE COMMENT
// ============================
if(isset($_GET['deleteComment'])){
    $postModel->deleteComment($_GET['deleteComment']);
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// ============================
// PIN / UNPIN POST
// ============================
if(isset($_GET['togglePin'])){
    $postModel->togglePin($_GET['togglePin']);
    header("Location: ../view/back/managePosts.php");
    exit();
}

// ============================
// SIGNALEMENT
// ============================
if(isset($_POST['report'])){
    $postModel->addReport($_POST['report_type'], $_POST['target_id'], $_POST['motif']);
    echo "reported";
    exit();
}

// ============================
// MARQUER RAPPORT EXAMINÉ
// ============================
if(isset($_GET['reviewReport'])){
    $postModel->markReportReviewed($_GET['reviewReport']);
    header("Location: ../view/back/manageReports.php");
    exit();
}

// ============================
// NOTIFICATIONS - MARQUER LU
// ============================
if(isset($_POST['markNotifRead'])){
    $postModel->markNotificationsRead(1);
    echo "ok";
    exit();
}

// ============================
// NOTIFICATIONS - POLL
// ============================
if(isset($_GET['pollNotifications'])){
    echo json_encode([
        'count' => (int)$postModel->countUnreadNotifications(1),
        'items' => $postModel->getNotifications(1, 5)
    ]);
    exit();
}

// ============================
// EXPORT CSV
// ============================
if(isset($_GET['exportPosts'])){
    $postModel->exportPostsCSV();
}

if(isset($_GET['exportComments'])){
    $postModel->exportCommentsCSV();
}

// ============================
// FOLLOW / UNFOLLOW
// ============================
if(isset($_POST['follow'])){
    $postModel->followUser(1, $_POST['user_id']);
    echo "followed";
    exit();
}

if(isset($_POST['unfollow'])){
    $postModel->unfollowUser(1, $_POST['user_id']);
    echo "unfollowed";
    exit();
}

// ============================
// FUNCTIONS GLOBALES
// ============================

function getPosts(){
    global $postModel;
    return $postModel->getAllPosts();
}

function getPost($id){
    global $postModel;
    return $postModel->getPostById($id);
}
?>