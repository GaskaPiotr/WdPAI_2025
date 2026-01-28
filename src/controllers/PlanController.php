<?php


require_once 'AppController.php';
require_once __DIR__.'/../services/PlanService.php';

class PlanController extends AppController {

    private $planService;
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->planService = new PlanService();
    }


    public function index() {
        $planId = $_GET['id'] ?? null;
        if (!$planId) {
            $this->redirect('/dashboard');
            return;
        }

        $currentUserId = $_SESSION['user_id'];

        try {
            $data = $this->planService->getPlanDetails((int)$planId, $currentUserId);
            return $this->render('plan_details', $data);
        } catch (Exception $e) {
             // Obsługa błędu, np. plan nie istnieje
            $code = 404;
            if (strpos($e->getMessage(), 'Access Denied') !== false) {
                $code = 403;
            }

            http_response_code($code);
            return $this->render('error', [
                'code' => $code,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function addExercise() {
        if (!$this->isPost()) return;

        $planId = $_POST['plan_id'];
        $exerciseName = trim($_POST['exercise_name']);
        $exerciseType = $_POST['exercise_type'];
        $sets = (int)$_POST['sets'];
        $note = $_POST['note'];

        try {
            $this->planService->addExerciseToPlan((int)$planId, $exerciseName, $exerciseType, $sets, $note);
        } catch (Exception $e) {
            http_response_code(400);
            return $this->render('error', [
                'code' => 400,
                'message' => 'Failed to add exercise: ' . $e->getMessage()
            ]);
        }
        
        $this->redirect("/plan?id={$planId}");
    }

    public function deleteExercise() {
        if (!$this->isPost()) return;

        $relationId = $_POST['relation_id'];
        $planId = $_POST['plan_id'];
        try {
            $this->planService->deleteExerciseFromPlan((int)$relationId);

            $this->redirect("/plan?id={$planId}");
        } catch (Exception $e) {
            http_response_code(500);
            return $this->render('error', ['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function addPlan()
    {
        // === GET: Wyświetlanie formularza ===
        if (!$this->isPost()) {
            $targetTraineeId = $_GET['traineeId'] ?? null;
            $data = $this->planService->getAddPlanData($targetTraineeId ? (int)$targetTraineeId : null);
            
            return $this->render('add_plan', [
                'targetTraineeId' => $targetTraineeId,
                'trainee' => $data['trainee']
            ]);
        }

        // === POST: Zapisywanie ===
        $planName = $_POST['plan_name'];
        $targetTraineeId = $_POST['target_trainee_id'] ?? null; 

        try {
            $this->planService->createPlan($planName, (int)$_SESSION['user_id'], $targetTraineeId ? (int)$targetTraineeId : null);

            if ($targetTraineeId) {
                $this->redirect("/trainee-dashboard?id={$targetTraineeId}");
            } else {
                $this->redirect("/dashboard");
            }

        } catch (Exception $e) {
             // W razie błędu ponownie pobieramy dane
            http_response_code(400);

            // Wracamy do formularza z komunikatem błędu
            $data = $this->planService->getAddPlanData($targetTraineeId ? (int)$targetTraineeId : null);
            return $this->render('add_plan', [
                'messages' => [$e->getMessage()], 
                'targetTraineeId' => $targetTraineeId,
                'trainee' => $data['trainee']
            ]);
        }
    }

    public function delete()
    {
        if (!$this->isPost()) {
            $this->redirect("/dashboard");
            return;
        }

        $planId = $_POST['plan_id'];
        $currentUserId = $_SESSION['user_id'];

        try {
            $traineeId = $this->planService->deletePlan((int)$planId, $currentUserId);

             if ($traineeId !== null) {
                $this->redirect("/trainee-dashboard?id={$traineeId}");
            } else {
                $this->redirect("/dashboard");
            }
        } catch (Exception $e) {
            http_response_code(400);
            return $this->render('error', [
                'code' => 400,
                'message' => 'Could not delete plan: ' . $e->getMessage()
            ]);
        }
    }

   public function startWorkout()
    {
        $planId = $_GET['id'] ?? null;
        if (!$planId) { $this->redirect("/dashboard"); return; }

        try {
            $data = $this->planService->getWorkoutSessionData((int)$planId);
            return $this->render('workout_session', $data);
        } catch (Exception $e) {
            http_response_code(404);
            return $this->render('error', ['code' => 404, 'message' => $e->getMessage()]);
        }
    }

    public function saveWorkout()
    {
        if (!$this->isPost()) {
            $this->redirect("/dashboard");
            return;
        }

        $planId = $_POST['plan_id'];
        $userNote = $_POST['user_note'] ?? '';
        $results = $_POST['results'] ?? [];

        try {
            $this->planService->saveWorkoutSession((int)$planId, $userNote, $results);
            $this->redirect("/start-workout?id={$planId}&saved=1");
        } catch (Exception $e) {
            http_response_code(500);
            return $this->render('error', [
                'code' => 500, 
                'message' => "Error saving workout: " . $e->getMessage()
            ]);
        }
    }

    public function deleteSession()
    {
        if (!$this->isPost()) {
            $this->redirect("/dashboard");
            return;
        }

        $sessionId = $_POST['session_id'];
        $planId = $_POST['plan_id'];
        
        try {
            if ($sessionId) {
                $this->planService->deleteSession((int)$sessionId);
            }
            $this->redirect("/start-workout?id={$planId}");
        } catch (Exception $e) {
            http_response_code(500);
            return $this->render('error', ['code' => 500, 'message' => $e->getMessage()]);
        }
    }
    
    // Pomocnicza metoda do przekierowań
    private function redirect($path) {
        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}{$path}");
        exit;
    }
}
