<?php
require_once __DIR__ . "/../config/database.php";

class Post {

    private $conn;

    public function __construct(){
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // ============================
    // USER
    // ============================
    public function getUser($id){
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id=:id");
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ============================
    // POSTS
    // ============================

    // Retourne les posts publiés (épinglés en premier)
    public function getAllPosts(){
        $stmt = $this->conn->prepare("
            SELECT * FROM post
            WHERE scheduled_at IS NULL OR scheduled_at <= NOW()
            ORDER BY is_pinned DESC, id DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPostById($id){
        $stmt = $this->conn->prepare("SELECT * FROM post WHERE id=:id");
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function searchPosts($keyword){
        $stmt = $this->conn->prepare("
            SELECT * FROM post
            WHERE (titre LIKE :k OR contenu LIKE :k)
            AND (scheduled_at IS NULL OR scheduled_at <= NOW())
            ORDER BY is_pinned DESC, id DESC
        ");
        $stmt->execute([':k'=>"%".$keyword."%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Filtre par tag
    public function getPostsByTag($tag_id){
        $stmt = $this->conn->prepare("
            SELECT p.* FROM post p
            INNER JOIN post_tags pt ON pt.post_id = p.id
            WHERE pt.tag_id = :t
            AND (p.scheduled_at IS NULL OR p.scheduled_at <= NOW())
            ORDER BY p.is_pinned DESC, p.id DESC
        ");
        $stmt->execute([':t'=>$tag_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajout post avec scheduled_at
    public function addPost($titre, $contenu, $image, $user_id, $scheduled_at = null){
        $stmt = $this->conn->prepare("
            INSERT INTO post(titre, contenu, image, user_id, scheduled_at)
            VALUES(:t, :c, :i, :u, :s)
        ");
        $stmt->execute([
            ':t' => $titre,
            ':c' => $contenu,
            ':i' => $image,
            ':u' => $user_id,
            ':s' => $scheduled_at
        ]);
        return $this->conn->lastInsertId();
    }

    // Sauvegarde historique avant update
    public function saveHistory($id){
        $post = $this->getPostById($id);
        if($post){
            $stmt = $this->conn->prepare("
                INSERT INTO post_history(post_id, titre, contenu)
                VALUES(:p, :t, :c)
            ");
            $stmt->execute([':p'=>$id, ':t'=>$post['titre'], ':c'=>$post['contenu']]);
        }
    }

    public function getHistory($post_id){
        $stmt = $this->conn->prepare("
            SELECT * FROM post_history WHERE post_id=:id ORDER BY edited_at DESC LIMIT 10
        ");
        $stmt->execute([':id'=>$post_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updatePost($id, $titre, $contenu, $image){
        $this->saveHistory($id); // sauvegarde avant modif
        if($image == ""){
            $stmt = $this->conn->prepare("UPDATE post SET titre=:t, contenu=:c WHERE id=:id");
            return $stmt->execute([':t'=>$titre, ':c'=>$contenu, ':id'=>$id]);
        } else {
            $stmt = $this->conn->prepare("UPDATE post SET titre=:t, contenu=:c, image=:i WHERE id=:id");
            return $stmt->execute([':t'=>$titre, ':c'=>$contenu, ':i'=>$image, ':id'=>$id]);
        }
    }

    public function deletePost($id){
        $stmt = $this->conn->prepare("DELETE FROM post WHERE id=:id");
        return $stmt->execute([':id'=>$id]);
    }

    // Épingler / désépingler
    public function togglePin($id){
        $stmt = $this->conn->prepare("UPDATE post SET is_pinned = !is_pinned WHERE id=:id");
        return $stmt->execute([':id'=>$id]);
    }

    // ============================
    // TAGS
    // ============================

    public function getAllTags(){
        return $this->conn->query("SELECT * FROM tags ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTagById($id){
        $stmt = $this->conn->prepare("SELECT * FROM tags WHERE id=:id");
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Associer des tags à un post
    public function setPostTags($post_id, $tag_ids){
        $this->conn->prepare("DELETE FROM post_tags WHERE post_id=:p")->execute([':p'=>$post_id]);
        if(!empty($tag_ids)){
            $stmt = $this->conn->prepare("INSERT IGNORE INTO post_tags(post_id,tag_id) VALUES(:p,:t)");
            foreach($tag_ids as $tid){
                $stmt->execute([':p'=>$post_id, ':t'=>intval($tid)]);
            }
        }
    }

    // Tags d'un post
    public function getPostTags($post_id){
        $stmt = $this->conn->prepare("
            SELECT t.* FROM tags t
            INNER JOIN post_tags pt ON pt.tag_id = t.id
            WHERE pt.post_id=:p
        ");
        $stmt->execute([':p'=>$post_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================
    // REACTIONS (remplace likes)
    // ============================

    public function setReaction($post_id, $type){
        $ip = $_SERVER['REMOTE_ADDR'];
        $allowed = ['like','love','haha','wow','sad'];
        if(!in_array($type, $allowed)) $type = 'like';

        // Vérifie si une réaction existe
        $stmt = $this->conn->prepare("SELECT id, type FROM reactions WHERE post_id=:p AND user_ip=:ip");
        $stmt->execute([':p'=>$post_id, ':ip'=>$ip]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if($existing){
            if($existing['type'] === $type){
                // Même réaction = retrait
                $this->conn->prepare("DELETE FROM reactions WHERE id=:id")->execute([':id'=>$existing['id']]);
            } else {
                // Réaction différente = mise à jour
                $this->conn->prepare("UPDATE reactions SET type=:t WHERE id=:id")->execute([':t'=>$type, ':id'=>$existing['id']]);
            }
        } else {
            $stmt = $this->conn->prepare("INSERT INTO reactions(post_id,user_ip,type) VALUES(:p,:ip,:t)");
            $stmt->execute([':p'=>$post_id, ':ip'=>$ip, ':t'=>$type]);
        }
    }

    public function getReactions($post_id){
        $stmt = $this->conn->prepare("
            SELECT type, COUNT(*) as total FROM reactions
            WHERE post_id=:id GROUP BY type
        ");
        $stmt->execute([':id'=>$post_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = ['like'=>0,'love'=>0,'haha'=>0,'wow'=>0,'sad'=>0,'total'=>0];
        foreach($rows as $r){
            $result[$r['type']] = (int)$r['total'];
            $result['total'] += (int)$r['total'];
        }
        return $result;
    }

    public function getUserReaction($post_id){
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt = $this->conn->prepare("SELECT type FROM reactions WHERE post_id=:p AND user_ip=:ip");
        $stmt->execute([':p'=>$post_id, ':ip'=>$ip]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ? $r['type'] : null;
    }

    // Compatibilité ancienne méthode (pour dashboard)
    public function likePost($post_id){ $this->setReaction($post_id,'like'); }
    public function countLikes($id){ return $this->getReactions($id)['total']; }

    // ============================
    // COMMENTS
    // ============================

    public function addComment($post_id, $contenu, $parent_id = null, $username = 'User'){
        $stmt = $this->conn->prepare("
            INSERT INTO commentaire(post_id, contenu, parent_id, username)
            VALUES(:p, :c, :par, :u)
        ");
        $stmt->execute([':p'=>$post_id, ':c'=>$contenu, ':par'=>$parent_id, ':u'=>$username]);
        return $this->conn->lastInsertId();
    }

    public function getComments($post_id){
        $stmt = $this->conn->prepare("
            SELECT * FROM commentaire
            WHERE post_id=:id AND parent_id IS NULL
            ORDER BY id ASC
        ");
        $stmt->execute([':id'=>$post_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReplies($comment_id){
        $stmt = $this->conn->prepare("
            SELECT * FROM commentaire WHERE parent_id=:id ORDER BY id ASC
        ");
        $stmt->execute([':id'=>$comment_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateComment($id, $contenu){
        $stmt = $this->conn->prepare("UPDATE commentaire SET contenu=:c WHERE id=:id");
        return $stmt->execute([':c'=>$contenu, ':id'=>$id]);
    }

    public function deleteComment($id){
        $stmt = $this->conn->prepare("DELETE FROM commentaire WHERE id=:id OR parent_id=:id2");
        return $stmt->execute([':id'=>$id, ':id2'=>$id]);
    }

    // ============================
    // LIKE COMMENT
    // ============================

    public function likeComment($id){
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt = $this->conn->prepare("SELECT * FROM comment_likes WHERE comment_id=:c AND user_ip=:ip");
        $stmt->execute([':c'=>$id, ':ip'=>$ip]);
        if($stmt->rowCount()==0){
            $stmt = $this->conn->prepare("INSERT INTO comment_likes(comment_id,user_ip) VALUES(:c,:ip)");
            $stmt->execute([':c'=>$id, ':ip'=>$ip]);
        }
    }

    public function countCommentLikes($id){
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM comment_likes WHERE comment_id=:id");
        $stmt->execute([':id'=>$id]);
        return $stmt->fetchColumn();
    }

    // ============================
    // SIGNALEMENT
    // ============================

    public function addReport($type, $target_id, $motif){
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt = $this->conn->prepare("
            INSERT INTO reports(type, target_id, motif, user_ip)
            VALUES(:type, :tid, :m, :ip)
        ");
        return $stmt->execute([':type'=>$type, ':tid'=>$target_id, ':m'=>$motif, ':ip'=>$ip]);
    }

    public function getReports(){
        $stmt = $this->conn->query("SELECT * FROM reports ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markReportReviewed($id){
        $stmt = $this->conn->prepare("UPDATE reports SET is_reviewed=1 WHERE id=:id");
        return $stmt->execute([':id'=>$id]);
    }

    public function countPendingReports(){
        return $this->conn->query("SELECT COUNT(*) FROM reports WHERE is_reviewed=0")->fetchColumn();
    }

    // ============================
    // NOTIFICATIONS
    // ============================

    public function addNotification($user_id, $message, $post_id = null){
        $stmt = $this->conn->prepare("
            INSERT INTO notifications(user_id, message, post_id)
            VALUES(:u, :m, :p)
        ");
        return $stmt->execute([':u'=>$user_id, ':m'=>$message, ':p'=>$post_id]);
    }

    public function getNotifications($user_id = 1, $limit = 10){
        $stmt = $this->conn->prepare("
            SELECT * FROM notifications WHERE user_id=:u
            ORDER BY created_at DESC LIMIT :l
        ");
        $stmt->bindValue(':u', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':l', $limit,   PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countUnreadNotifications($user_id = 1){
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=:u AND is_read=0");
        $stmt->execute([':u'=>$user_id]);
        return $stmt->fetchColumn();
    }

    public function markNotificationsRead($user_id = 1){
        $stmt = $this->conn->prepare("UPDATE notifications SET is_read=1 WHERE user_id=:u");
        return $stmt->execute([':u'=>$user_id]);
    }

    // ============================
    // FOLLOW
    // ============================

    public function followUser($follower, $followed){
        $stmt = $this->conn->prepare("INSERT IGNORE INTO followers(follower_id,followed_id) VALUES(:f,:u)");
        return $stmt->execute([':f'=>$follower, ':u'=>$followed]);
    }

    public function unfollowUser($follower, $followed){
        $stmt = $this->conn->prepare("DELETE FROM followers WHERE follower_id=:f AND followed_id=:u");
        return $stmt->execute([':f'=>$follower, ':u'=>$followed]);
    }

    public function countFollowers($user_id){
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM followers WHERE followed_id=:id");
        $stmt->execute([':id'=>$user_id]);
        return $stmt->fetchColumn();
    }

    // ============================
    // DASHBOARD STATS
    // ============================

    public function countPosts(){
        return $this->conn->query("SELECT COUNT(*) FROM post")->fetchColumn();
    }

    public function countAllLikes(){
        return $this->conn->query("SELECT COUNT(*) FROM reactions")->fetchColumn();
    }

    public function countComments(){
        return $this->conn->query("SELECT COUNT(*) FROM commentaire")->fetchColumn();
    }

    // Posts par jour (7 derniers jours)
    public function getPostsPerDay(){
        $stmt = $this->conn->query("
            SELECT DATE(id) as day, COUNT(*) as total
            FROM post
            GROUP BY DATE(id)
            ORDER BY day DESC
            LIMIT 7
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Top 5 posts les plus réactionnés
    public function getTopPosts(){
        $stmt = $this->conn->query("
            SELECT p.id, p.titre, COUNT(r.id) as reactions
            FROM post p
            LEFT JOIN reactions r ON r.post_id = p.id
            GROUP BY p.id, p.titre
            ORDER BY reactions DESC
            LIMIT 5
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Activité commentaires par jour
    public function getCommentsPerDay(){
        $stmt = $this->conn->query("
            SELECT DATE(date_comment) as day, COUNT(*) as total
            FROM commentaire
            WHERE date_comment IS NOT NULL
            GROUP BY DATE(date_comment)
            ORDER BY day DESC
            LIMIT 7
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Export CSV posts
    public function exportPostsCSV(){
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="posts_export_'.date('Ymd').'.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID','Titre','Contenu','Image','User ID','Scheduled','Pinned']);
        $posts = $this->conn->query("SELECT * FROM post ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        foreach($posts as $p){
            fputcsv($out, [$p['id'],$p['titre'],$p['contenu'],$p['image'],$p['user_id'],$p['scheduled_at'],$p['is_pinned']]);
        }
        fclose($out);
        exit();
    }

    // Export CSV comments
    public function exportCommentsCSV(){
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="comments_export_'.date('Ymd').'.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID','Post ID','Username','Contenu','Parent ID','Date']);
        $comments = $this->conn->query("SELECT * FROM commentaire ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        foreach($comments as $c){
            fputcsv($out, [$c['id'],$c['post_id'],$c['username'] ?? '',$c['contenu'],$c['parent_id'] ?? '',$c['date_comment'] ?? '']);
        }
        fclose($out);
        exit();
    }
}
?>