<?php

require_once __DIR__ . '/../Core/Controller.php';

class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home', [
            'appName' => $this->config['name'],
            'modules' => [
                [
                    'title' => 'Users',
                    'description' => 'Client and admin authentication, profiles, languages, and account management.',
                    'url' => 'app/Modules/Users/index.php',
                    'tag' => 'Account',
                ],
                [
                    'title' => 'Excursions',
                    'description' => 'Travel guides, excursions, recommendation engine, and excursion administration.',
                    'url' => 'app/Modules/Excursions/index.php',
                    'tag' => 'Trips',
                ],
                [
                    'title' => 'Destinations',
                    'description' => 'Destination and circuit catalog with PDF export support.',
                    'url' => 'app/Modules/Destinations/public/index.php',
                    'tag' => 'Explore',
                ],
                [
                    'title' => 'Hotels',
                    'description' => 'Hotel listings, reservations, tickets, and back-office reservation management.',
                    'url' => 'app/Modules/Hotels/public/index.php',
                    'tag' => 'Stay',
                ],
                [
                    'title' => 'Forum',
                    'description' => 'Community posts, comments, tags, reactions, reports, and forum administration.',
                    'url' => 'app/Modules/Forum/view/front/index.php',
                    'tag' => 'Community',
                ],
                [
                    'title' => 'Discount Game',
                    'description' => 'Travel game registration, gameplay, coupons, reviews, and game management.',
                    'url' => 'app/Modules/DiscountGame/view/front_office/index.php',
                    'tag' => 'Rewards',
                ],
            ],
        ]);
    }
}
