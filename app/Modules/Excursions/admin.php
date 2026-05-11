<?php
// Since this file is in the root, do NOT use /../ to find folders inside the root
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/model/Guide.php';
require_once __DIR__ . '/model/Excursion.php';
require_once __DIR__ . '/Controller/AdminController.php';

$controller = new AdminController();
$page = $_GET['page'] ?? 'guides';
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

if ($page === 'excursions') {
    switch ($action) {
        case 'add':
            $controller->addExcursion();
            break;
        case 'edit':
            $controller->editExcursion($id);
            break;
        case 'delete':
            $controller->deleteExcursion($id);
            break;
        default:
            $controller->listExcursions();
            break;
    }
} else {
    // Default to guides
    switch ($action) {
        case 'add':
            $controller->addGuide();
            break;
        case 'edit':
            $controller->editGuide($id);
            break;
        case 'delete':
            $controller->deleteGuide($id);
            break;
        default:
            $controller->listGuides();
            break;
    }
}