<?php

require_once __DIR__ . '/../model/Destination.php';

class DestinationController {

    private $model;

    public function __construct() {
        $this->model = new Destination();
    }

    // 👉 Display all destinations (FrontOffice)
    public function showAll() {
        $destinations = $this->model->getAll();

        // path to the content page
        $content = __DIR__ . '/../view/front/destinations.php';

        // load the main layout (skeleton)
        require __DIR__ . '/../view/layouts/main.php';
    }

}