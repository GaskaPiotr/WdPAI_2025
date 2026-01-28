<?php

require_once 'AppController.php';
require_once __DIR__.'/../services/DashboardService.php';

class DashboardController extends AppController {

    private $dashboardService;
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        // Inicjalizujemy serwis (nie repozytoria!)
        $this->dashboardService = new DashboardService();
    }

    public function index() {
        // 1. Auth Guard (Zadanie kontrolera)
        if (!isset($_SESSION['user_id'])) {
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $userRoleId = $_SESSION['role_id']; 

        try {
            // 2. Delegujemy logikę pobierania danych do serwisu
            $result = $this->dashboardService->getDashboardData($userId, $userRoleId);

            // 3. Renderujemy odpowiedni widok na podstawie decyzji serwisu
            if ($result['type'] === 'trainer') {
                $this->render('dashboard_trainer', $result['data']);
            } else {
                $this->render('dashboard_trainee', $result['data']);
            }

        } catch (Exception $e) {
            // Obsługa błędu krytycznego (np. brak roli w bazie)
            http_response_code(500); // Internal Server Error
            
            return $this->render('error', [
                'code' => 500,
                'message' => 'Critical Error: ' . $e->getMessage()
            ]);
        }
    }

    public function handleInvitation() {
        if (!$this->isPost()) {
            return;
        }

        // Pobieramy dane z POST
        $requestId = (int)$_POST['request_id'];
        $action = $_POST['action']; 

        try {
            // Delegujemy akcję do serwisu
            $this->dashboardService->processInvitation($requestId, $action);

            // Przekierowanie (Zadanie kontrolera)
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/dashboard"); 
        } catch (Exception $e) {
            http_response_code(400); // Bad Request
            
            return $this->render('error', [
                'code' => 400,
                'message' => $e->getMessage()
            ]);
        }
    }

    
}
