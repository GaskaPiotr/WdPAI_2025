<?php

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
            
            // Ładujemy widok ĆWICZĄCEGO
            // AppController szuka pliku: public/views/dashboard_trainee.html
            $this->render('dashboard_trainee', ['plans' => $plans]);
        }
    }
}