<?php

$config = require __DIR__ . '/config/app.php';
require_once __DIR__ . '/app/Controllers/DashboardController.php';

(new DashboardController($config))->front();
