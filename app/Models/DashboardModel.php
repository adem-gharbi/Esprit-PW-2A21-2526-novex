<?php

class DashboardModel
{
    public function frontSections(): array
    {
        return [
            [
                'title' => 'Hotel and Reservation',
                'description' => 'Browse hotels, make reservations, and view booking details.',
                'url' => 'app/Modules/Hotels/public/index.php',
                'image' => 'app/Modules/Destinations/public/images/bg.jpg',
                'label' => 'Stay',
            ],
            [
                'title' => 'Guide and Excursion',
                'description' => 'Discover expert guides and curated excursions.',
                'url' => 'app/Modules/Excursions/index.php',
                'image' => 'app/Modules/Excursions/assets/images/destination-1.jpg',
                'label' => 'Trips',
            ],
            [
                'title' => 'Circuit and Destination',
                'description' => 'Explore destinations, circuits, maps, prices, and availability.',
                'url' => 'app/Modules/Destinations/public/index.php',
                'image' => 'app/Modules/Destinations/public/images/paris.jpg',
                'label' => 'Explore',
            ],
            [
                'title' => 'Game and Discount',
                'description' => 'Play the travel game and unlock discount coupons.',
                'url' => 'app/Modules/DiscountGame/view/front_office/index.php',
                'image' => 'app/Modules/Destinations/public/images/dubai.jpg',
                'label' => 'Rewards',
            ],
            [
                'title' => 'Forums',
                'description' => 'Join the community, posts, comments, and travel tips.',
                'url' => 'app/Modules/Forum/view/front/index.php',
                'image' => 'app/Modules/Destinations/public/images/marrakech.jpg',
                'label' => 'Community',
            ],
        ];
    }

    public function backSections(): array
    {
        return [
            [
                'title' => 'Users and Accounts',
                'description' => 'Manage client accounts, admin users, account status, and user settings from the Adem module.',
                'url' => 'app/Modules/Users/view/admin/dashboard.php',
            ],
            [
                'title' => 'Hotel and Reservation',
                'description' => 'Manage hotels, reservations, and reservation lists.',
                'url' => 'app/Modules/Hotels/public/admin.php',
            ],
            [
                'title' => 'Guide and Excursion',
                'description' => 'Manage travel guides and excursions.',
                'url' => 'app/Modules/Excursions/admin.php',
            ],
            [
                'title' => 'Circuit and Destination',
                'description' => 'Manage destinations, circuits, filters, and exports.',
                'url' => 'app/Modules/Destinations/view/back/listDestination.php',
            ],
            [
                'title' => 'Game and Discount',
                'description' => 'Manage games, discounts, coupons, and reviews.',
                'url' => 'app/Modules/DiscountGame/view/back_office/index.php',
            ],
            [
                'title' => 'Forums',
                'description' => 'Manage forum posts, comments, reports, and statistics.',
                'url' => 'app/Modules/Forum/view/back/login.php',
            ],
        ];
    }
}
