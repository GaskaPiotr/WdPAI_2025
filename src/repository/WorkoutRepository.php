<?php

require_once 'Repository.php';

class WorkoutRepository extends Repository
{
    public function getPlansByUserId(int $userId) {
        $stmt = $this->database->connect()->prepare('
            SELECT wp.*, u.name as trainer_name, u.surname as trainer_surname
            FROM workout_plans wp
            LEFT JOIN users u ON wp.trainer_id = u.id
            WHERE wp.user_id = :id
            ORDER BY wp.created_at DESC
        ');
        
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTraineesByTrainerId(int $trainerId): array
    {
        $stmt = $this->database->connect()->prepare('
            SELECT u.id, u.name, u.surname, u.email, tt.status
            FROM users u
            JOIN trainer_trainees tt ON u.id = tt.trainee_id
            WHERE tt.trainer_id = :trainer_id
        ');

        $stmt->bindParam(':trainer_id', $trainerId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function isTraineeAssigned(int $trainerId, int $traineeId): bool
    {
        $stmt = $this->database->connect()->prepare('
            SELECT 1 FROM trainer_trainees 
            WHERE trainer_id = :trainer_id AND trainee_id = :trainee_id
        ');
        $stmt->bindParam(':trainer_id', $trainerId, PDO::PARAM_INT);
        $stmt->bindParam(':trainee_id', $traineeId, PDO::PARAM_INT);
        $stmt->execute();

        return (bool) $stmt->fetch();
    }

    public function addTrainee(int $trainerId, int $traineeId): void
    {
        $stmt = $this->database->connect()->prepare('
            INSERT INTO trainer_trainees (trainer_id, trainee_id)
            VALUES (:trainer_id, :trainee_id)
        ');
        $stmt->bindParam(':trainer_id', $trainerId, PDO::PARAM_INT);
        $stmt->bindParam(':trainee_id', $traineeId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getPendingInvitations(int $traineeId): array
    {
        $stmt = $this->database->connect()->prepare('
            SELECT tt.id as request_id, u.name, u.surname, u.email
            FROM trainer_trainees tt
            JOIN users u ON tt.trainer_id = u.id
            WHERE tt.trainee_id = :id AND tt.status = \'pending\'
        ');
        $stmt->bindParam(':id', $traineeId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Akceptacja zaproszenia
    public function acceptInvitation(int $requestId): void
    {
        $stmt = $this->database->connect()->prepare('
            UPDATE trainer_trainees 
            SET status = \'accepted\'
            WHERE id = :id
        ');
        $stmt->bindParam(':id', $requestId, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Odrzucenie zaproszenia (usuwamy rekord)
    public function declineInvitation(int $requestId): void
    {
        $stmt = $this->database->connect()->prepare('
            DELETE FROM trainer_trainees 
            WHERE id = :id
        ');
        $stmt->bindParam(':id', $requestId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function addPlan(string $name, int $userId, ?int $trainerId = null): void
    {
        $stmt = $this->database->connect()->prepare('
            INSERT INTO workout_plans (name, user_id, trainer_id, created_at)
            VALUES (:name, :user_id, :trainer_id, NOW())
        ');

        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':trainer_id', $trainerId, PDO::PARAM_INT); // PDO obsłuży NULL automatycznie
        
        $stmt->execute();
    }

    // Pobiera plany dla konkretnego podopiecznego, stworzone przez konkretnego trenera
    public function getPlansByTrainerAndTrainee(int $trainerId, int $traineeId): array
    {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM workout_plans 
            WHERE user_id = :trainee_id AND trainer_id = :trainer_id
            ORDER BY created_at DESC
        ');

        $stmt->bindParam(':trainee_id', $traineeId, PDO::PARAM_INT);
        $stmt->bindParam(':trainer_id', $trainerId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 
}