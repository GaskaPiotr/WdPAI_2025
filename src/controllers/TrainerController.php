<?php

require_once 'AppController.php';
require_once __DIR__.'/../services/TrainerService.php';

class TrainerController extends AppController {

    private $trainerService;
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->trainerService = new TrainerService();
    }

    public function addTrainee()
    {
        // 1. GET: Wyświetl formularz
        if (!$this->isPost()) {
            return $this->render('add_trainee');
        }

        // 2. POST: Obsłuż logikę przez Serwis
        $email = $_POST['email'];
        $trainerId = $_SESSION['user_id'];

        try {
            $this->trainerService->addTrainee($trainerId, $email);

            // Sukces - przekierowanie
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/dashboard");

        } catch (Exception $e) {
            // Błąd - wyświetl formularz z komunikatem błędu z serwisu
            return $this->render('add_trainee', ['messages' => [$e->getMessage()]]);
        }
    }

    public function showTraineeDashboard()
    {
        $traineeId = $_GET['id'] ?? null;
        $trainerId = $_SESSION['user_id'];

        if (!$traineeId) {
            // Przekierowanie - brak ID w URL
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/dashboard");
            return;
        }

        try {
            // Pobieramy dane z serwisu
            $data = $this->trainerService->getTraineeDetails($trainerId, (int)$traineeId);

            // Renderujemy widok używając danych z serwisu
            return $this->render('dashboard_trainee', [
                'plans' => $data['plans'],
                'trainee' => $data['trainee']
            ]);

        } catch (Exception $e) {
            $msg = $e->getMessage();
            $code = 400;

            // Sprawdzamy treść błędu (z TrainerService)
            if (strpos($msg, 'Access Denied') !== false) {
                // Próba wejścia na nieswojego podopiecznego
                $code = 403; // Forbidden
            } 
            elseif (strpos($msg, 'not found') !== false) {
                // Niepoprawne ID użytkownika
                $code = 404; // Not found
            } 
            else {
                // Inne błędy
                http_response_code($code); // Bad Request
            }

            return $this->render('error', [
                'code' => $code,
                'message' => $msg
            ]);
        }
    }
}
/*
require_once 'AppController.php';
require_once __DIR__.'/../repository/UserRepository.php';
require_once __DIR__.'/../repository/WorkoutRepository.php';

class TrainerController extends AppController {

    private $userRepository;
    private $workoutRepository;
    
    // 1. Dodajemy statyczną właściwość na instancję
    private static $instance = null;

    // 2. Dodajemy metodę getInstance(), której wymaga Twój Router
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        // Pamiętaj: USUŃ parent::__construct(), bo AppController go nie ma
        $this->userRepository = new UserRepository();
        $this->workoutRepository = new WorkoutRepository();
    }

    public function addTrainee()
    {
        // 1. Jeśli to wejście GET (wyświetlenie formularza)
        if (!$this->isPost()) {
            return $this->render('add_trainee');
        }

        // 2. Jeśli to POST (przesłanie formularza)
        $email = $_POST['email'];

        if (empty($email)) {
            return $this->render('add_trainee', ['messages' => ['Please provide an email address']]);
        }

        // 3. Szukamy użytkownika w bazie
        $user = $this->userRepository->getUserByEmail($email);

        if (!$user) {
            return $this->render('add_trainee', ['messages' => ['User with this email does not exist']]);
        }

        // 4. Pobieramy ID z sesji i z bazy
        $trainerId = $_SESSION['user_id'];
        $traineeId = $user['id'];

        if ($trainerId == $traineeId) {
            return $this->render('add_trainee', ['messages' => ['You cannot add yourself!']]);
        }

        // 5. Sprawdzamy czy już nie jest Twoim podopiecznym
        if ($this->workoutRepository->isTraineeAssigned($trainerId, $traineeId)) {
            return $this->render('add_trainee', ['messages' => ['This user is already your trainee']]);
        }

        // 6. Dodajemy do bazy
        $this->workoutRepository->addTrainee($trainerId, $traineeId);

        // Sukces!
        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}/dashboard");
    }

   public function showTraineeDashboard()
    {
        // 1. Pobieramy ID z URL (np. ?id=5)
        // WAŻNE: W widoku link to /trainee-dashboard?id=..., więc tu szukamy 'id'
        $traineeId = $_GET['id'] ?? null;
        $trainerId = $_SESSION['user_id'];

        if (!$traineeId) {
            // Brak ID w URL -> Wracamy na główny dashboard
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/dashboard");
            return;
        }

        // 2. Security: Czy to Twój podopieczny?
        // Rzutujemy na (int), bo $_GET zwraca stringi, a baza lubi inty
        if (!$this->workoutRepository->isTraineeAssigned($trainerId, (int)$traineeId)) {
            die("Access Denied. This user is not your trainee.");
        }

        // 3. Pobieramy dane podopiecznego
        // Tu korzystamy z Twojej nowej metody.
        // Jeśli ID jest złe, zwróci false.
        $traineeData = $this->userRepository->getUserById((int)$traineeId);

        // --- DEBUG: Jeśli tutaj $traineeData jest puste, to znaczy że ID jest złe ---
        if (!$traineeData) {
            die("User not found in database (ID: $traineeId)");
        }

        // 4. Pobieramy plany
        $plans = $this->workoutRepository->getPlansByTrainerAndTrainee($trainerId, (int)$traineeId);

        // 5. Renderujemy widok
        // Klucz 'trainee' musi odpowiadać temu, co używasz w HTML ($trainee['name'])
        return $this->render('dashboard_trainee', [
            'plans' => $plans,
            'trainee' => $traineeData
        ]);
    }
}
*/