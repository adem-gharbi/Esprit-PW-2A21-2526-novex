<?php

$config = require __DIR__ . '/../config/app.php';
require_once $config['base_path'] . '/app/Controllers/HomeController.php';

(new HomeController($config))->index();
