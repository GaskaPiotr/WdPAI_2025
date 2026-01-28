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
