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
            die($e->getMessage());
        }
    }

    public function handleInvitation() {
        if (!$this->isPost()) {
            return;
        }

        // Pobieramy dane z POST
        $requestId = (int)$_POST['request_id'];
        $action = $_POST['action']; 

        // Delegujemy akcję do serwisu
        $this->dashboardService->processInvitation($requestId, $action);

        // Przekierowanie (Zadanie kontrolera)
        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}/dashboard");
    }
}
/*
require_once 'AppController.php';
require_once __DIR__.'/../repository/UserRepository.php';
require_once __DIR__.'/../repository/WorkoutRepository.php';

class DashboardController extends AppController {

    private $workoutRepository;
    private $userRepository;

    private static $instance = null;


    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->workoutRepository = new WorkoutRepository();
        $this->userRepository = new UserRepository();
    }

   

    public function index() {
        // 1. Sprawdzamy czy użytkownik jest zalogowany
        if (!isset($_SESSION['user_id'])) {
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/login");
            return;
        }

        $userId = $_SESSION['user_id'];
        $userRoleId = $_SESSION['role_id']; 
        try {
            // Pytamy bazę: jakie ID ma rola "trainer"?
            $trainerRoleId = $this->userRepository->getRoleByName('trainer');
        } catch (Exception $e) {
            // Jeśli ktoś usunął rolę z bazy, to mamy krytyczny błąd
            die("Critical Error: Trainer role not defined in database.");
        }
        // Sprawdź w bazie jakie ID ma trener (np. 2). Dostosuj tę liczbę!

        if ($userRoleId == $trainerRoleId) {
            // === SCENARIUSZ TRENERA ===
            
            // Pobieramy dane dla trenera
            $trainees = $this->workoutRepository->getTraineesByTrainerId($userId);
            
            // Ładujemy widok TRENERA
            // AppController szuka pliku: public/views/dashboard_trainer.html
            $this->render('dashboard_trainer', ['trainees' => $trainees]);

        } else {
            // === SCENARIUSZ ĆWICZĄCEGO ===
            
            // Pobieramy plany dla ćwiczącego
            $plans = $this->workoutRepository->getPlansByUserId($userId);
           
            
            $invitations = $this->workoutRepository->getPendingInvitations($userId);

            // Ładujemy widok ĆWICZĄCEGO
            // AppController szuka pliku: public/views/dashboard_trainee.html
            $this->render('dashboard_trainee', [
                'plans' => $plans,
                'invitations' => $invitations
            ]);
        }
    }
    

    public function handleInvitation() {
        if (!$this->isPost()) {
            return;
        }

        $requestId = $_POST['request_id'];
        $action = $_POST['action']; // 'accept' lub 'decline'

        if ($action === 'accept') {
            $this->workoutRepository->acceptInvitation((int)$requestId);
        } elseif ($action === 'decline') {
            $this->workoutRepository->declineInvitation((int)$requestId);
        }

        // Odśwież stronę
        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}/dashboard");
    }
}
    */