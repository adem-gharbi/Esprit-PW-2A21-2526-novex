<?php
// ==================== CONFIGURATION PDO - Version Pro ====================

$host = 'localhost';
$dbname = 'projet_ecologique';     // ←←← CHANGE ÇA avec le vrai nom de ta base de données
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

} catch (PDOException $e) {
    // En production, on n'affiche pas les détails de l'erreur pour des raisons de sécurité
    die("❌ Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
}

// Optionnel : définir le fuseau horaire
$pdo->exec("SET NAMES utf8mb4");
$pdo->exec("SET time_zone = '+01:00'");   // Change selon ton pays (Tunisie = +01:00)

// ==================== CONFIGURATION GOOGLE OAUTH ====================
// On charge les clés depuis un fichier non suivi par Git
if (file_exists(__DIR__ . '/credentials.php')) {
    require_once __DIR__ . '/credentials.php';
} else {
    define('GOOGLE_CLIENT_ID', 'VOTRE_CLIENT_ID');
    define('GOOGLE_CLIENT_SECRET', 'VOTRE_CLIENT_SECRET');
}
define('GOOGLE_REDIRECT_URI', 'http://localhost/Esprit-PW-2A21-2526-novex/Controller/UserController.php?action=googleCallback');

?>