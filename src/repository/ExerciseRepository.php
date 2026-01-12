<?php

require_once 'Repository.php';

class ExerciseRepository extends Repository {

    // 1. Pobierz wszystkie dostępne ćwiczenia (do listy wyboru)
    public function getAllExercises(): array {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM exercises ORDER BY name
        ');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Pobierz ćwiczenia przypisane do konkretnego planu
    public function getExercisesByPlanId(int $planId): array {
        // Dodajemy e.type do SELECT, żeby widzieć typ na liście
        $stmt = $this->database->connect()->prepare('
            SELECT pe.id as relation_id, pe.sets_count, pe.note, e.name, e.type
            FROM plan_exercises pe
            JOIN exercises e ON pe.exercise_id = e.id
            WHERE pe.workout_plan_id = :plan_id
            ORDER BY pe.order_index ASC, pe.id ASC
        ');
        $stmt->bindParam(':plan_id', $planId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Dodaj ćwiczenie do planu
    public function addExerciseToPlan(int $planId, int $exerciseId, int $sets, string $note): void {
        $stmt = $this->database->connect()->prepare('
            INSERT INTO plan_exercises (workout_plan_id, exercise_id, sets_count, note, order_index)
            VALUES (:plan_id, :exercise_id, :sets, :note, 0)
        ');
        // order_index na razie 0, w przyszłości można dorobić logikę kolejności
        $stmt->bindParam(':plan_id', $planId, PDO::PARAM_INT);
        $stmt->bindParam(':exercise_id', $exerciseId, PDO::PARAM_INT);
        $stmt->bindParam(':sets', $sets, PDO::PARAM_INT);
        $stmt->bindParam(':note', $note, PDO::PARAM_STR);
        $stmt->execute();
    }

    // 4. Usuń ćwiczenie z planu
    public function deleteExerciseFromPlan(int $relationId): void {
        $stmt = $this->database->connect()->prepare('
            DELETE FROM plan_exercises WHERE id = :id
        ');
        $stmt->bindParam(':id', $relationId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getOrCreateExercise(string $name, string $type): int 
    {
        // 1. Najpierw szukamy
        $stmt = $this->database->connect()->prepare('
            SELECT id FROM exercises WHERE LOWER(name) = LOWER(:name)
        ');
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        $exercise = $stmt->fetch(PDO::FETCH_ASSOC);

        // Jeśli znaleźliśmy -> zwracamy ID i IGNORUJEMY przesłany $type
        if ($exercise) {
            return $exercise['id'];
        }

        // 2. Jeśli nie ma -> Dopiero wtedy tworzymy z podanym typem
        $stmt = $this->database->connect()->prepare('
            INSERT INTO exercises (name, type) VALUES (:name, :type) RETURNING id
        ');
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'];
    }
}