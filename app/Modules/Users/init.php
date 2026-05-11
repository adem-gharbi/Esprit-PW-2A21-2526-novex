<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Définir la langue par défaut
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'fr'; // Français par défaut
}

// Fonction de traduction globale
function __($key) {
    $lang = $_SESSION['lang'];
    $langFile = __DIR__ . '/lang/' . $lang . '.php';
    
    // Fallback en français si le fichier n'existe pas
    if (!file_exists($langFile)) {
        $langFile = __DIR__ . '/lang/fr.php';
    }
    
    $translations = include($langFile);
    
    // Retourne la traduction si elle existe, sinon retourne la clé
    return isset($translations[$key]) ? $translations[$key] : $key;
}

// Helper pour savoir si on est en RTL (Arabe)
function is_rtl() {
    return $_SESSION['lang'] === 'ar';
}
?>
