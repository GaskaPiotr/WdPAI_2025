<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/WorkoutRepository.php';
require_once __DIR__.'/../repository/UserRepository.php';
require_once __DIR__.'/../repository/ExerciseRepository.php';

class PlanController extends AppController {

    private $workoutRepository;
    private $userRepository;
    private $exerciseRepository;
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->workoutRepository = new WorkoutRepository();
        $this->userRepository = new UserRepository();
        $this->exerciseRepository = new ExerciseRepository();
    }


    public function index() { // Zmieniłem nazwę z show na index dla trasy /plan
        $planId = $_GET['id'] ?? null;
        if (!$planId) {
            header("Location: /dashboard");
            return;
        }

        // Pobieramy info o planie (nagłówek)
        $plan = $this->workoutRepository->getPlanById((int)$planId);
        
        // Security: Czy masz prawo to widzieć? (Tu wstaw swoją logikę weryfikacji usera/trenera)
        // ...

        // Pobieramy ćwiczenia w tym planie
        $exercises = $this->exerciseRepository->getExercisesByPlanId((int)$planId);
        
        // Pobieramy listę wszystkich możliwych ćwiczeń (do formularza dodawania)
        $allExercises = $this->exerciseRepository->getAllExercises();

        return $this->render('plan_details', [
            'plan' => $plan,
            'exercises' => $exercises,
            'allExercises' => $allExercises
        ]);
    }

    public function addExercise() {
        if (!$this->isPost()) return;

        $planId = $_POST['plan_id'];
        
        // Pobieramy nazwę wpisaną przez użytkownika
        $exerciseName = trim($_POST['exercise_name']);
        $exerciseType = $_POST['exercise_type']; // 'weight_reps' lub 'time_only'
        
        $sets = $_POST['sets'];
        $note = $_POST['note'];

        if (empty($exerciseName)) {
            // Tutaj można dodać obsługę błędu
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/plan?id={$planId}");
            return;
        }

        // Używamy naszej nowej metody "Smart Add"
        $exerciseId = $this->exerciseRepository->getOrCreateExercise($exerciseName, $exerciseType);

        // Dodajemy do planu
        $this->exerciseRepository->addExerciseToPlan($planId, $exerciseId, $sets, $note);

        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}/plan?id={$planId}");
    }

    public function deleteExercise() {
        if (!$this->isPost()) return;

        $relationId = $_POST['relation_id']; // ID z tabeli plan_exercises
        $planId = $_POST['plan_id']; // Potrzebne tylko do przekierowania powrotnego

        $this->exerciseRepository->deleteExerciseFromPlan($relationId);

        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}/plan?id={$planId}");
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

    public function delete()
    {
        if (!$this->isPost()) {
            // Usuwanie powinno być tylko metodą POST dla bezpieczeństwa
            header("Location: /dashboard");
            return;
        }

        $planId = $_POST['plan_id'];
        $currentUserId = $_SESSION['user_id'];

        // 1. Pobieramy plan, żeby sprawdzić czy istnieje i czyj jest
        $plan = $this->workoutRepository->getPlanById((int)$planId);

        if (!$plan) {
            header("Location: /dashboard");
            return;
        }

        // 2. SPRAWDZENIE UPRAWNIEŃ (Security)
        // Możesz usunąć jeśli: jesteś właścicielem (user_id) LUB twórcą-trenerem (trainer_id)
        if ($plan['user_id'] !== $currentUserId && $plan['trainer_id'] !== $currentUserId) {
            die("You do not have permission to delete this plan.");
        }

        // 3. Usuwamy plan
        $this->workoutRepository->deletePlan((int)$planId);

        // 4. MĄDRE PRZEKIEROWANIE
        // Jeśli jestem trenerem tego planu -> wracam do podopiecznego
        if ($plan['trainer_id'] === $currentUserId) {
            // Musimy wiedzieć czyj to był plan, żeby wrócić na dobrą stronę
            $traineeId = $plan['user_id'];
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/trainee-dashboard?id={$traineeId}");
        } else {
            // Jeśli jestem użytkownikiem -> wracam do siebie
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/dashboard");
        }
    }


 
   public function startWorkout()
    {
        $planId = $_GET['id'] ?? null;
        if (!$planId) { header("Location: /dashboard"); return; }

        $plan = $this->workoutRepository->getPlanById((int)$planId);
        $exercises = $this->exerciseRepository->getExercisesByPlanId((int)$planId);

        // 1. Pobieramy sesje (już są odwrócone w repozytorium: Stare -> Nowe)
        $pastSessions = $this->workoutRepository->getLastSessions((int)$planId, 3);
        
        $sessionIds = [];
        $historyNotes = [];

        // Zbieramy ID sesji do zapytania SQL i notatki
        foreach ($pastSessions as $s) {
            $sessionIds[] = $s['id'];
            // Kluczem notatki jest teraz ID SESJI, a nie data
            $historyNotes[$s['id']] = $s['user_note'] ?? '';
        }

        // 2. Pobieramy logi
        $logs = $this->workoutRepository->getLogsForSessions($sessionIds);

        // 3. Budujemy dane: $historyData[plan_exercise_id][SESSION_ID][set_index]
        $historyData = [];

        foreach ($logs as $log) {
            $relationId = $log['plan_exercise_id'];
            $sessionId = $log['workout_session_id']; // <--- To jest nasz unikalny klucz
            
            // Formatowanie (bez zmian)
            $formattedResult = '-';
            if ($log['weight'] !== null && $log['reps'] !== null) {
                $formattedResult = floatval($log['weight']) . 'kg x ' . $log['reps'];
            } elseif ($log['time_seconds'] !== null && $log['distance_km'] !== null) {
                $timeStr = gmdate("i:s", $log['time_seconds']);
                $formattedResult = floatval($log['distance_km']) . 'km ' . $timeStr;
            } elseif ($log['time_seconds'] !== null) {
                $formattedResult = gmdate("i:s", $log['time_seconds']);
            } elseif ($log['reps'] !== null) {
                $formattedResult = $log['reps'] . ' reps';
            } elseif ($log['distance_km'] !== null) {
                $formattedResult = floatval($log['distance_km']) . ' km';
            }

            $arrayIndex = $log['set_number'] - 1;
            
            // ZAPISUJEMY POD ID SESJI, NIE POD DATĄ
            $historyData[$relationId][$sessionId][$arrayIndex] = $formattedResult;
        }

        return $this->render('workout_session', [
            'plan' => $plan,
            'exercises' => $exercises,
            'pastSessions' => $pastSessions, // Przekazujemy całe obiekty sesji (mają datę i ID)
            'historyData' => $historyData,
            'historyNotes' => $historyNotes
        ]);
    }

    public function saveWorkout()
    {
        if (!$this->isPost()) {
            header("Location: /dashboard");
            return;
        }

        $planId = $_POST['plan_id'];
        $userNote = $_POST['user_note'] ?? ''; // Odbieramy nową notatkę
        $results = $_POST['results'] ?? [];

        if (empty($results)) {
            header("Location: /dashboard"); 
            return;
        }

        try {
            // Wywołujemy repozytorium (już bez user_id, zgodnie z Twoją tabelą)
            $this->workoutRepository->saveSession((int)$planId, $userNote, $results);
            
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/dashboard");

        } catch (Exception $e) {
            die("Error saving workout: " . $e->getMessage());
        }
    }
}