<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Guide.php';
require_once __DIR__ . '/../model/Excursion.php';

class AdminController {
    private $db;
    private $guide;
    private $excursion;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->guide = new Guide($this->db);
        $this->excursion = new Excursion($this->db);
    }

    public function listGuides() {  
        $stmt = $this->guide->readAll();
        $guides = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__ . '/../View/Back/list_guides.php';
    }

    public function addGuide() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // 1. BACKEND FAIL-SAFE: Trim all inputs to detect empty spaces
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $tel = trim($_POST['tel'] ?? '');
            $langue = trim($_POST['langue'] ?? '');
            $specialite = trim($_POST['specialite'] ?? '');
            $photo = trim($_POST['photo'] ?? '');

            // 2. STRICT CONDITION CHECK - All fields are required
            if (empty($nom)) {
                die("Error: Last Name is required.");
            }
            if (empty($prenom)) {
                die("Error: First Name is required.");
            }
            if (empty($tel)) {
                die("Error: Phone number is required.");
            }
            if (empty($langue)) {
                die("Error: Language is required.");
            }
            if (empty($specialite)) {
                die("Error: Specialty is required.");
            }
            if (empty($photo)) {
                die("Error: Photo filename is required.");
            }

            // 3. VALIDATE PHONE NUMBER
            if (strlen($tel) > 13) {
                die("Error: Phone number cannot exceed 13 characters.");
            }
            if (!preg_match('/^[0-9+\-\s()]+$/', $tel)) {
                die("Error: Phone number contains invalid characters.");
            }

            $query = "INSERT INTO guide (nom, prenom, tel, langue, specialite, photo) 
                      VALUES (:nom, :prenom, :tel, :langue, :specialite, :photo)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'nom'        => $nom, 
                'prenom'     => $prenom, 
                'tel'        => $tel,
                'langue'     => $langue, 
                'specialite' => $specialite, 
                'photo'      => $photo 
            ]);
            header("Location: admin.php");
            exit();
        }
        include __DIR__ . '/../View/Back/add_guide.php';
    }

    public function editGuide($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Trim all inputs
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $tel = trim($_POST['tel'] ?? '');
            $langue = trim($_POST['langue'] ?? '');
            $specialite = trim($_POST['specialite'] ?? '');
            $photo = trim($_POST['photo'] ?? '');

            // Validate all fields are not empty
            if (empty($nom)) {
                die("Error: Last Name is required.");
            }
            if (empty($prenom)) {
                die("Error: First Name is required.");
            }
            if (empty($tel)) {
                die("Error: Phone number is required.");
            }
            if (empty($langue)) {
                die("Error: Language is required.");
            }
            if (empty($specialite)) {
                die("Error: Specialty is required.");
            }

            // Validate phone number
            if (strlen($tel) > 13) {
                die("Error: Phone number cannot exceed 13 characters.");
            }
            if (!preg_match('/^[0-9+\-\s()]+$/', $tel)) {
                die("Error: Phone number contains invalid characters.");
            }

            $query = "UPDATE guide SET nom=:nom, prenom=:prenom, tel=:tel, langue=:langue, specialite=:specialite, photo=:photo WHERE id=:id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'nom'        => $nom,
                'prenom'     => $prenom,
                'tel'        => $tel,
                'langue'     => $langue,
                'specialite' => $specialite,
                'photo'      => $photo,
                'id'         => $id
            ]);
            header("Location: admin.php");
            exit();
        }
        
        $query = "SELECT * FROM guide WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        $guide = $stmt->fetch(PDO::FETCH_ASSOC);
        include __DIR__ . '/../View/Back/edit_guide.php';
    }

    public function deleteGuide($id) {
        $query = "DELETE FROM guide WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        header("Location: admin.php");
        exit();
    }

    // ===== EXCURSION METHODS =====

    public function listExcursions() {  
        $stmt = $this->excursion->readAll();
        $excursions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__ . '/../View/Back/list_excursions.php';
    }

    public function addExcursion() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // 1. BACKEND FAIL-SAFE: Trim all inputs to detect empty spaces
            $titre = trim($_POST['titre'] ?? '');
            $duree = trim($_POST['duree'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $prix = trim($_POST['prix'] ?? '');
            $circuit_id = trim($_POST['circuit_id'] ?? '');
            $guide_id = trim($_POST['guide_id'] ?? '');

            // 2. STRICT CONDITION CHECK - All fields are required
            if (empty($titre)) {
                die("Error: Title is required.");
            }
            if (empty($duree)) {
                die("Error: Duration is required.");
            }
            if (empty($description)) {
                die("Error: Description is required.");
            }
            if (empty($prix)) {
                die("Error: Price is required.");
            }
            if (empty($circuit_id)) {
                die("Error: Circuit ID is required.");
            }
            if (empty($guide_id)) {
                die("Error: Guide ID is required.");
            }

            // 3. VALIDATE PRICE - must be a valid positive number with up to 2 decimal places
            if (!is_numeric($prix)) {
                die("Error: Price must be a valid number.");
            }
            if ((float)$prix < 0) {
                die("Error: Price cannot be negative.");
            }
            if (!preg_match('/^\d+(\.\d{1,2})?$/', $prix)) {
                die("Error: Price can have at most 2 decimal places.");
            }

            // 4. VALIDATE IDs - must be positive integers
            if (!is_numeric($circuit_id) || !ctype_digit((string)$circuit_id)) {
                die("Error: Circuit ID must be a positive integer.");
            }
            if ((int)$circuit_id <= 0) {
                die("Error: Circuit ID must be a positive integer.");
            }

            if (!is_numeric($guide_id) || !ctype_digit((string)$guide_id)) {
                die("Error: Guide ID must be a positive integer.");
            }
            if ((int)$guide_id <= 0) {
                die("Error: Guide ID must be a positive integer.");
            }

            $query = "INSERT INTO excursion (titre, duree, description, prix, circuit_id, guide_id) 
                      VALUES (:titre, :duree, :description, :prix, :circuit_id, :guide_id)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'titre'       => $titre, 
                'duree'       => $duree, 
                'description' => $description,
                'prix'        => $prix, 
                'circuit_id'  => $circuit_id,
                'guide_id'    => $guide_id
            ]);
            header("Location: admin.php?page=excursions");
            exit();
        }
        include __DIR__ . '/../View/Back/add_excursion.php';
    }

    public function editExcursion($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Trim all inputs
            $titre = trim($_POST['titre'] ?? '');
            $duree = trim($_POST['duree'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $prix = trim($_POST['prix'] ?? '');
            $circuit_id = trim($_POST['circuit_id'] ?? '');
            $guide_id = trim($_POST['guide_id'] ?? '');

            // Validate all fields are not empty
            if (empty($titre)) {
                die("Error: Title is required.");
            }
            if (empty($duree)) {
                die("Error: Duration is required.");
            }
            if (empty($description)) {
                die("Error: Description is required.");
            }
            if (empty($prix)) {
                die("Error: Price is required.");
            }
            if (empty($circuit_id)) {
                die("Error: Circuit ID is required.");
            }
            if (empty($guide_id)) {
                die("Error: Guide ID is required.");
            }

            // Validate price - must be a valid positive number with up to 2 decimal places
            if (!is_numeric($prix)) {
                die("Error: Price must be a valid number.");
            }
            if ((float)$prix < 0) {
                die("Error: Price cannot be negative.");
            }
            if (!preg_match('/^\d+(\.\d{1,2})?$/', $prix)) {
                die("Error: Price can have at most 2 decimal places.");
            }

            // Validate IDs - must be positive integers
            if (!is_numeric($circuit_id) || !ctype_digit((string)$circuit_id)) {
                die("Error: Circuit ID must be a positive integer.");
            }
            if ((int)$circuit_id <= 0) {
                die("Error: Circuit ID must be a positive integer.");
            }

            if (!is_numeric($guide_id) || !ctype_digit((string)$guide_id)) {
                die("Error: Guide ID must be a positive integer.");
            }
            if ((int)$guide_id <= 0) {
                die("Error: Guide ID must be a positive integer.");
            }

            $query = "UPDATE excursion SET titre=:titre, duree=:duree, description=:description, prix=:prix, circuit_id=:circuit_id, guide_id=:guide_id WHERE id=:id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'titre'       => $titre,
                'duree'       => $duree,
                'description' => $description,
                'prix'        => $prix,
                'circuit_id'  => $circuit_id,
                'guide_id'    => $guide_id,
                'id'          => $id
            ]);
            header("Location: admin.php?page=excursions");
            exit();
        }
        
        $query = "SELECT * FROM excursion WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        $excursion = $stmt->fetch(PDO::FETCH_ASSOC);
        include __DIR__ . '/../View/Back/edit_excursion.php';
    }

    public function deleteExcursion($id) {
        $query = "DELETE FROM excursion WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        header("Location: admin.php?page=excursions");
        exit();
    }
}