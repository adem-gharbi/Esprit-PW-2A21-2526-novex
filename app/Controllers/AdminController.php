<?php

require_once __DIR__ . '/../Core/Controller.php';

class AdminController extends Controller
{
    public function index(): void
    {
        $this->view('admin', [
            'appName' => $this->config['name'],
            'adminLinks' => [
                [
                    'title' => 'Users Admin',
                    'url' => 'app/Modules/Users/view/admin/dashboard.php',
                    'description' => 'User dashboard, settings, and account controls.',
                ],
                [
                    'title' => 'Excursions Admin',
                    'url' => 'app/Modules/Excursions/admin.php',
                    'description' => 'Guides and excursions management.',
                ],
                [
                    'title' => 'Destinations Admin',
                    'url' => 'app/Modules/Destinations/public/admin.php',
                    'description' => 'Destination and circuit management.',
                ],
                [
                    'title' => 'Hotels Admin',
                    'url' => 'app/Modules/Hotels/public/admin.php',
                    'description' => 'Hotels and reservation management.',
                ],
                [
                    'title' => 'Forum Admin',
                    'url' => 'app/Modules/Forum/view/back/login.php',
                    'description' => 'Forum posts, comments, reports, and statistics.',
                ],
                [
                    'title' => 'Discount Game Admin',
                    'url' => 'app/Modules/DiscountGame/view/back_office/index.php',
                    'description' => 'Games, discounts, coupons, and reviews.',
                ],
            ],
        ]);
    }
}
