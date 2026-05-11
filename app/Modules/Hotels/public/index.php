<?php

// 🔌 connexion base de données
require_once "../config/database.php";

// 🏨 controller hôtel
require_once "../controllers/HotelController.php";

// 🏗️ création controller
$controller = new HotelController($db);

// 📋 récupération de tous les hôtels depuis le modèle
$hotels = $controller->index();

/* =========================
   🔥 TRI PAR ÉTOILES
========================= */

// 🎯 récupération paramètre URL (asc ou desc)
$order = $_GET['order'] ?? 'desc';

// 🔼 tri croissant (moins d’étoiles → plus d’étoiles)
if($order === 'asc'){

    usort($hotels, function($a, $b){
        return $a['Etoiles'] <=> $b['Etoiles'];
    });

}else{

    // 🔽 tri décroissant (plus d’étoiles → moins d’étoiles)
    usort($hotels, function($a, $b){
        return $b['Etoiles'] <=> $a['Etoiles'];
    });
}

// 📄 affichage vue front
include "../views/front/hotel_list.php";
?>