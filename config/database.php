<?php

// ============================
// 📌 CLASSE DATABASE
// 👉 Cette classe permet de gérer la connexion entre PHP et MySQL
// 👉 Elle utilise PDO (méthode sécurisée et moderne)
// ============================
class Database {

    // 🏠 Adresse du serveur de base de données
    // localhost = serveur local (XAMPP / WAMP)
    private $host = "localhost";

    // 🗄️ Nom de la base de données à utiliser
    private $dbname = "voyagio";

    // 👤 Nom d'utilisateur MySQL (par défaut XAMPP = root)
    private $user = "root";

    // 🔒 Mot de passe MySQL (vide par défaut sur XAMPP)
    private $pass = "";

    // 🔌 Variable qui va contenir la connexion PDO
    public $conn;


    // ============================
    // 🔗 MÉTHODE DE CONNEXION
    // ============================
    public function getConnection(){

        // 🔄 Initialisation de la connexion à null
        // (on prépare la variable avant connexion)
        $this->conn = null;

        // ⚠️ try = on essaie d'exécuter le code de connexion
        try {

            // 🔥 Création de l'objet PDO (connexion à MySQL)
            // Paramètres :
            // - type de base : mysql
            // - host : serveur
            // - dbname : base de données
            $this->conn = new PDO(
                "mysql:host=".$this->host.";dbname=".$this->dbname,
                $this->user,
                $this->pass
            );

            // 🛡️ Configuration du mode d'erreur PDO
            // ERRMODE_EXCEPTION = affiche les erreurs comme exceptions
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } 
        // ❌ catch = si une erreur survient lors de la connexion
        catch(PDOException $e){

            // 🚨 Affiche un message d'erreur et arrête le script
            // utile pour debug (connexion DB)
            die("Erreur connexion: " . $e->getMessage());
        }

        // 📤 Retourne la connexion PDO pour l'utiliser dans les models
        return $this->conn;
    }
}

?>