<?php

require_once 'Repository.php';

class WorkoutRepository extends Repository
{
    public function getPlansByUserId(int $userId) {
        // Wybieramy wszystkie kolumny z planu (wp.*)
        // Oraz imię i nazwisko trenera (u.name, u.surname)
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

    public function getTraineesByTrainerId(int $trainerId) {
        // Pobieramy dane użytkowników, którzy mają przypisany plan od tego trenera
        // DISTINCT zapobiega duplikatom (jeśli jeden user ma 5 planów od tego trenera)
        $stmt = $this->database->connect()->prepare('
            SELECT DISTINCT u.id, u.name, u.surname, u.email
            FROM users u
            JOIN workout_plans wp ON u.id = wp.user_id
            WHERE wp.trainer_id = :trainer_id
        ');

        $stmt->bindParam(':trainer_id', $trainerId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}