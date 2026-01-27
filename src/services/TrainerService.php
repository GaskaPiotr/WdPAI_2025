<?php

require_once __DIR__.'/../repository/UserRepository.php';
require_once __DIR__.'/../repository/WorkoutRepository.php';

class TrainerService {
    
    private $userRepository;
    private $workoutRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->workoutRepository = new WorkoutRepository();
    }

    // Logika dodawania podopiecznego
    public function addTrainee(int $trainerId, string $email): void {
        
        if (empty($email)) {
            throw new Exception('Please provide an email address');
        }

        // 1. Szukamy użytkownika
        $user = $this->userRepository->getUserByEmail($email);

        if (!$user) {
            throw new Exception('User with this email does not exist');
        }

        $traineeId = $user->getId();

        // 2. Walidacja: czy nie dodajemy siebie
        if ($trainerId == $traineeId) {
            throw new Exception('You cannot add yourself!');
        }

        // 3. Walidacja: czy relacja już istnieje
        if ($this->workoutRepository->isTraineeAssigned($trainerId, $traineeId)) {
            throw new Exception('This user is already your trainee');
        }

        // 4. Zapis do bazy
        $this->workoutRepository->addTrainee($trainerId, $traineeId);
    }

    // Logika pobierania dashboardu podopiecznego
    public function getTraineeDetails(int $trainerId, int $traineeId): array {
        
        // 1. Security: Czy to Twój podopieczny?
        if (!$this->workoutRepository->isTraineeAccepted($trainerId, $traineeId)) {
            throw new Exception("Access Denied. User is not active or invitation is pending.");
        }

        // 2. Pobieramy dane usera
        $userEntity = $this->userRepository->getUserById($traineeId);

        if (!$userEntity) {
            throw new Exception("User not found in database");
        }


        $traineeDto = new UserDto(
            $userEntity->getId(),
            $userEntity->getEmail(),
            $userEntity->getName(),
            $userEntity->getSurname(),
            'trainee'
        );
        
        // 3. Pobieramy plany
        $plans = $this->workoutRepository->getPlansByTrainerAndTrainee($trainerId, $traineeId);

        // Zwracamy paczkę danych potrzebną do widoku
        return [
            'trainee' => $traineeDto,
            'plans' => $plans
        ];
    }


    public function getTraineesList(int $trainerId): array {
        $rawTrainees = $this->workoutRepository->getTraineesByTrainerId($trainerId);
        $dtos = [];

        foreach ($rawTrainees as $row) {
            $dtos[] = new UserDto(
                $row['id'],
                $row['email'],
                $row['name'],
                $row['surname'],
                'trainee',
                $row['status'] // Tu jest ten status, o który pytałeś
            );
        }

        return $dtos;
    }
}