<?php

require_once __DIR__.'/../repository/UserRepository.php';
require_once __DIR__.'/../repository/WorkoutRepository.php';

class DashboardService {

    private $workoutRepository;
    private $userRepository;

    public function __construct() {
        $this->workoutRepository = new WorkoutRepository();
        $this->userRepository = UserRepository::getInstance();
    }

    public function getDashboardData(int $userId, int $userRoleId): array {
        
        // 1. Sprawdzamy ID roli trenera w bazie
        try {
            $trainerRoleId = $this->userRepository->getRoleByName('trainer');
        } catch (Exception $e) {
            throw new Exception("Critical Error: Trainer role not defined in database.");
        }

        // 2. Decydujemy o scenariuszu
        if ($userRoleId == $trainerRoleId) {
            // Scenariusz Trenera
            $trainerService = new TrainerService(); 
            $traineesDto = $trainerService->getTraineesList($userId);
            
            return [
                'type' => 'trainer', // Flaga dla kontrolera
                'data' => ['trainees' => $traineesDto]
            ];
        } else {
            // Scenariusz Ćwiczącego
            $plans = $this->workoutRepository->getPlansByUserId($userId);
            $invitations = $this->workoutRepository->getPendingInvitations($userId);

            return [
                'type' => 'trainee', // Flaga dla kontrolera
                'data' => [
                    'plans' => $plans,
                    'invitations' => $invitations
                ]
            ];
        }
    }

    public function processInvitation(int $requestId, string $action): void {
        if ($action === 'accept') {
            $this->workoutRepository->acceptInvitation($requestId);
        } elseif ($action === 'decline') {
            $this->workoutRepository->declineInvitation($requestId);
        }
    }
}