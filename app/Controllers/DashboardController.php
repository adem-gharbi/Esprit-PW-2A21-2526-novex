<?php

require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/DashboardModel.php';

class DashboardController extends Controller
{
    private DashboardModel $dashboardModel;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->dashboardModel = new DashboardModel();
    }

    public function front(): void
    {
        $this->startSession();

        if (empty($_SESSION['client_email'])) {
            header('Location: app/Modules/Users/view/login.html');
            exit;
        }

        $this->view('dashboard_front', [
            'appName' => $this->config['name'],
            'email' => $_SESSION['client_email'],
            'sections' => $this->dashboardModel->frontSections(),
        ]);
    }

    public function back(): void
    {
        $this->startSession();

        if (empty($_SESSION['admin_id'])) {
            header('Location: app/Modules/Users/view/login_admin.html');
            exit;
        }

        $this->view('dashboard_back', [
            'appName' => $this->config['name'],
            'adminName' => $_SESSION['admin_name'] ?? 'Admin',
            'sections' => $this->dashboardModel->backSections(),
        ]);
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
