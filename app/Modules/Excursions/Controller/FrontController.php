<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Guide.php';
require_once __DIR__ . '/../model/Excursion.php';

class FrontController {
    public function index() {
        $database = new Database();
        $db = $database->getConnection();
        
        $guideModel = new Guide($db);
        $stmt = $guideModel->readAll();
        $guides = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $excursionModel = new Excursion($db);
        $stmt = $excursionModel->readAll();
        $excursions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include __DIR__ . '/../View/Front/home.php';
    }
}
?>