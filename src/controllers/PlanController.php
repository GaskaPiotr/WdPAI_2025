<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/WorkoutRepository.php';
require_once __DIR__.'/../repository/UserRepository.php'; // Dodajemy to!

class PlanController extends AppController {

    private $workoutRepository;
    private $userRepository; // Dodajemy to!
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        // Inicjalizujemy oba repozytoria
        $this->workoutRepository = new WorkoutRepository();
        $this->userRepository = new UserRepository();
    }

    public function addPlan()
    {
        // === GET: Wyświetlanie formularza ===
        if (!$this->isPost()) {
            $targetTraineeId = $_GET['traineeId'] ?? null;
            $traineeData = null;

            // Jeśli tworzymy plan dla kogoś, pobieramy jego dane (imię/nazwisko)
            if ($targetTraineeId) {
                $traineeData = $this->userRepository->getUserById((int)$targetTraineeId);
            }
            
            return $this->render('add_plan', [
                'targetTraineeId' => $targetTraineeId,
                'trainee' => $traineeData // Przekazujemy dane do widoku
            ]);
        }

        // === POST: Zapisywanie ===
        $planName = $_POST['plan_name'];
        // Pobieramy ID ukryte w formularzu
        $targetTraineeId = $_POST['target_trainee_id'] ?? null; 

        if (empty($planName)) {
            // W razie błędu musimy ponownie pobrać dane, żeby formularz się nie rozsypał
            $traineeData = $targetTraineeId ? $this->userRepository->getUserById((int)$targetTraineeId) : null;
            
            return $this->render('add_plan', [
                'messages' => ['Plan name cannot be empty'], 
                'targetTraineeId' => $targetTraineeId,
                'trainee' => $traineeData
            ]);
        }

        // --- LOGIKA ZAPISU I PRZEKIEROWANIA ---
        
        if ($targetTraineeId) {
            // SCENARIUSZ TRENERA: Tworzę plan DLA KOGOŚ
            // Właścicielem planu (user_id) jest Trainee, trenerem (trainer_id) jestem Ja.
            $this->workoutRepository->addPlan($planName, (int)$targetTraineeId, $_SESSION['user_id']);
            
            // WAŻNE: Wracamy do dashboardu KONKRETNEGO UŻYTKOWNIKA
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/trainee-dashboard?id={$targetTraineeId}");
            
        } else {
            // SCENARIUSZ ZWYKŁY: Tworzę plan DLA SIEBIE
            $this->workoutRepository->addPlan($planName, $_SESSION['user_id'], null);
            
            // Wracamy na mój główny dashboard
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/dashboard");
        }
    }
}