<?php
// 🔗 Import de la classe Database (connexion MySQL via PDO)
require_once __DIR__ . "/../config/database.php";

class Post {

    // 🔌 Propriété qui va stocker la connexion PDO
    private $conn;

    // ============================
    // 🏗️ CONSTRUCTEUR
    // 👉 exécuté automatiquement lors de new Post()
    // ============================
    public function __construct(){
        // 📦 création de l'objet Database
        $db = new Database();

        // 🔗 récupération de la connexion PDO
        $this->conn = $db->getConnection();
    }

    // ============================
    // 📰 POSTS
    // ============================

    // 📌 récupérer tous les posts
    public function getAllPosts(){
        // 🧾 préparation requête SQL
        $stmt = $this->conn->prepare("SELECT * FROM post ORDER BY date_post DESC");

        // ▶️ exécution requête
        $stmt->execute();

        // 📤 retour tableau associatif
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ➕ ajouter un post
    public function addPost($titre,$contenu,$image){
        // 🧾 insertion avec paramètres sécurisés (anti SQL injection)
        $stmt = $this->conn->prepare("INSERT INTO post(titre,contenu,image) VALUES(:t,:c,:i)");

        // ▶️ exécution avec binding des valeurs
        return $stmt->execute([
            ':t'=>$titre,
            ':c'=>$contenu,
            ':i'=>$image
        ]);
    }

    // ❌ supprimer un post
    public function deletePost($id){
        // 🧾 requête delete
        $stmt = $this->conn->prepare("DELETE FROM post WHERE id=:id");

        // ▶️ exécution avec id
        return $stmt->execute([':id'=>$id]);
    }

    // 🔍 récupérer un post par ID
    public function getPostById($id){
        // 🧾 select filtré
        $stmt = $this->conn->prepare("SELECT * FROM post WHERE id=:id");

        // ▶️ exécution
        $stmt->execute([':id'=>$id]);

        // 📤 retourner une seule ligne
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ✏️ modifier un post
    public function updatePost($id,$titre,$contenu,$image){

        // 📌 si pas d'image (update sans image)
        if($image==""){
            $stmt = $this->conn->prepare("UPDATE post SET titre=:t,contenu=:c WHERE id=:id");

            return $stmt->execute([
                ':t'=>$titre,
                ':c'=>$contenu,
                ':id'=>$id
            ]);
        } 
        // 📌 si image existe
        else {
            $stmt = $this->conn->prepare("UPDATE post SET titre=:t,contenu=:c,image=:i WHERE id=:id");

            return $stmt->execute([
                ':t'=>$titre,
                ':c'=>$contenu,
                ':i'=>$image,
                ':id'=>$id
            ]);
        }
    }

    // ============================
    // 📊 STATISTIQUES
    // ============================

    // 🔢 nombre total de posts
    public function countPosts(){
        return $this->conn
            ->query("SELECT COUNT(*) FROM post")
            ->fetchColumn();
    }

    // ❤️ nombre total de likes
    public function countAllLikes(){
        return $this->conn
            ->query("SELECT COUNT(*) FROM likes")
            ->fetchColumn();
    }

    // 💬 nombre total de commentaires
    public function countComments(){
        return $this->conn
            ->query("SELECT COUNT(*) FROM commentaire")
            ->fetchColumn();
    }

    // ============================
    // 👍 LIKE POST
    // ============================

    public function likePost($post_id){

        // 🌍 récupération IP utilisateur (anti double like simple)
        $ip = $_SERVER['REMOTE_ADDR'];

        // 🔍 vérifier si déjà liké
        $stmt = $this->conn->prepare("
            SELECT * FROM likes WHERE post_id=:p AND user_ip=:ip
        ");

        $stmt->execute([':p'=>$post_id, ':ip'=>$ip]);

        // 📌 si pas encore liké
        if($stmt->rowCount()==0){

            // ➕ insertion like
            $stmt = $this->conn->prepare("
                INSERT INTO likes(post_id,user_ip) VALUES(:p,:ip)
            ");

            $stmt->execute([':p'=>$post_id, ':ip'=>$ip]);
        }
    }

    // 🔢 compter likes d’un post
    public function countLikes($id){
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total FROM likes WHERE post_id=:id
        ");

        $stmt->execute([':id'=>$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // ============================
    // 💬 COMMENTS
    // ============================

    // ➕ ajouter commentaire
    public function addComment($post_id,$contenu){

        $stmt = $this->conn->prepare("
            INSERT INTO commentaire(post_id,contenu)
            VALUES(:p,:c)
        ");

        return $stmt->execute([
            ':p'=>$post_id,
            ':c'=>$contenu
        ]);
    }

    // 📥 récupérer commentaires d’un post
    public function getComments($post_id){

        $stmt = $this->conn->prepare("
            SELECT * FROM commentaire 
            WHERE post_id=:id 
            ORDER BY id DESC
        ");

        $stmt->execute([':id'=>$post_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ❌ supprimer commentaire
    public function deleteComment($id){

        $stmt = $this->conn->prepare("
            DELETE FROM commentaire WHERE id=:id
        ");

        return $stmt->execute([':id'=>$id]);
    }

    // ✏️ modifier commentaire
    public function updateComment($id,$contenu){

        $stmt = $this->conn->prepare("
            UPDATE commentaire SET contenu=:c WHERE id=:id
        ");

        return $stmt->execute([
            ':c'=>$contenu,
            ':id'=>$id
        ]);
    }

    // ============================
    // 🔥 LIKE COMMENTAIRE
    // ============================

    public function likeComment($comment_id){

        // 🌍 IP utilisateur
        $ip = $_SERVER['REMOTE_ADDR'];

        // 🔍 vérifier si déjà liké
        $stmt = $this->conn->prepare("
            SELECT * FROM comment_likes 
            WHERE comment_id=:c AND user_ip=:ip
        ");

        $stmt->execute([':c'=>$comment_id, ':ip'=>$ip]);

        // ➕ si pas encore liké
        if($stmt->rowCount()==0){

            $stmt = $this->conn->prepare("
                INSERT INTO comment_likes(comment_id,user_ip)
                VALUES(:c,:ip)
            ");

            $stmt->execute([':c'=>$comment_id, ':ip'=>$ip]);
        }
    }

    // 🔢 compter likes commentaire
    public function countCommentLikes($id){

        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total 
            FROM comment_likes 
            WHERE comment_id=:id
        ");

        $stmt->execute([':id'=>$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
?>