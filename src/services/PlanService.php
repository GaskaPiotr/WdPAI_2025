<?php

require_once __DIR__.'/../repository/WorkoutRepository.php';
require_once __DIR__.'/../repository/UserRepository.php';
require_once __DIR__.'/../repository/ExerciseRepository.php';

class PlanService {

    private $workoutRepository;
    private $userRepository;
    private $exerciseRepository;

    public function __construct() {
        $this->workoutRepository = new WorkoutRepository();
        $this->userRepository = new UserRepository();
        $this->exerciseRepository = new ExerciseRepository();
    }

    public function getPlanDetails(int $planId): array {
        $plan = $this->workoutRepository->getPlanById($planId);
        
        if (!$plan) {
             throw new Exception("Plan not found");
        }

        // Security check (opcjonalnie, w zależności od wymagań)
        // ...

        $exercises = $this->exerciseRepository->getExercisesByPlanId($planId);
        $allExercises = $this->exerciseRepository->getAllExercises();

        return [
            'plan' => $plan,
            'exercises' => $exercises,
            'allExercises' => $allExercises
        ];
    }

    public function addExerciseToPlan(int $planId, string $exerciseName, string $exerciseType, int $sets, ?string $note): void {
        if (empty($exerciseName)) {
            throw new Exception("Exercise name cannot be empty");
        }

        $exerciseId = $this->exerciseRepository->getOrCreateExercise($exerciseName, $exerciseType);
        $this->exerciseRepository->addExerciseToPlan($planId, $exerciseId, $sets, $note);
    }

    public function deleteExerciseFromPlan(int $relationId): void {
        $this->exerciseRepository->deleteExerciseFromPlan($relationId);
    }

    public function getAddPlanData(?int $targetTraineeId): array {
        $traineeData = null;
        if ($targetTraineeId) {
            $traineeData = $this->userRepository->getUserById($targetTraineeId);
        }
        return ['trainee' => $traineeData];
    }

    public function createPlan(string $planName, int $creatorId, ?int $targetTraineeId): void {
         if (empty($planName)) {
            throw new Exception("Plan name cannot be empty");
        }

        if ($targetTraineeId) {
            // Trener tworzy plan dla podopiecznego
            $this->workoutRepository->addPlan($planName, $targetTraineeId, $creatorId);
        } else {
            // Użytkownik tworzy plan dla siebie
            $this->workoutRepository->addPlan($planName, $creatorId, null);
        }
    }

    public function deletePlan(int $planId, int $currentUserId): ?int {
        $plan = $this->workoutRepository->getPlanById($planId);

        if (!$plan) {
            return null; // Plan nie istnieje
        }

        if ($plan['user_id'] !== $currentUserId && $plan['trainer_id'] !== $currentUserId) {
            throw new Exception("You do not have permission to delete this plan.");
        }

        $this->workoutRepository->deletePlan($planId);

        // Zwracamy ID podopiecznego, jeśli plan był stworzony przez trenera (dla przekierowania)
        return ($plan['trainer_id'] === $currentUserId) ? $plan['user_id'] : null;
    }

    public function getWorkoutSessionData(int $planId): array {
        $plan = $this->workoutRepository->getPlanById($planId);
        if (!$plan) {
            throw new Exception("Plan not found");
        }

        $exercises = $this->exerciseRepository->getExercisesByPlanId($planId);
        $pastSessions = $this->workoutRepository->getLastSessions($planId, 3);
        
        $sessionIds = [];
        $historyNotes = [];

        foreach ($pastSessions as $s) {
            $sessionIds[] = $s['id'];
            $historyNotes[$s['id']] = $s['user_note'] ?? '';
        }

        $logs = $this->workoutRepository->getLogsForSessions($sessionIds);
        $historyData = [];

        foreach ($logs as $log) {
            $relationId = $log['plan_exercise_id'];
            $sessionId = $log['workout_session_id'];
            
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
            $historyData[$relationId][$sessionId][$arrayIndex] = $formattedResult;
        }

        return [
            'plan' => $plan,
            'exercises' => $exercises,
            'pastSessions' => $pastSessions,
            'historyData' => $historyData,
            'historyNotes' => $historyNotes
        ];
    }

    public function saveWorkoutSession(int $planId, string $userNote, array $results): void {
         if (empty($results)) {
            throw new Exception("No results to save");
        }
        $this->workoutRepository->saveSession($planId, $userNote, $results);
    }

    public function deleteSession(int $sessionId): void {
        $this->workoutRepository->deleteSession($sessionId);
    }
}