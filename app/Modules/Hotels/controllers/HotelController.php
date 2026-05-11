<?php

//  On importe le modèle Hotel (qui gère la base de données)
require_once "../models/Hotel.php";

//  Classe contrôleur qui gère la logique des hôtels
class HotelController {

    //  Variable privée qui va contenir l'objet Hotel (modèle)
    private $hotel;

    //  Constructeur : exécuté automatiquement quand on crée le controller
    public function __construct($db){

        //  On crée un objet Hotel et on lui passe la connexion DB
        $this->hotel = new Hotel($db);
    }

    //  Méthode pour récupérer tous les hôtels (liste)
    public function index(){

        //  Appelle la méthode getAll() du modèle Hotel
        return $this->hotel->getAll();
    }

    //  DUPLICATION (inutile mais fonctionne)
    public function getAll(){

        //  Redondant avec index(), retourne la même chose
        return $this->hotel->getAll();
    }

    //  Ajouter un hôtel
    public function store($data){

        //  On envoie les données vers le modèle pour insertion
        return $this->hotel->add(
            $data['nom'],      // nom hôtel
            $data['ville'],    // ville
            $data['etoiles'],  // nombre étoiles
            $data['prix']      // prix
        );
    }

    //  Récupérer un hôtel par ID pour modification
    public function edit($id){

        //  Appelle le modèle pour chercher un hôtel précis
        return $this->hotel->getById($id);
    }

    //  Mettre à jour un hôtel existant
    public function update($id,$data){

        //  Envoie les nouvelles valeurs vers le modèle
        return $this->hotel->update(
            $id,              // ID de l’hôtel
            $data['nom'],    // nouveau nom
            $data['ville'],  // nouvelle ville
            $data['etoiles'],// nouvelles étoiles
            $data['prix']    // nouveau prix
        );
    }

    //  Supprimer un hôtel
    public function delete($id){

        //  Appelle la méthode delete du modèle
        return $this->hotel->delete($id);
    }
}
?>