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
            SELECT u.id, u.name, u.surname, u.email, tt.status, 
                   u.role_id, r.name as role_name
            FROM users u
            JOIN trainer_trainees tt ON u.id = tt.trainee_id
            JOIN roles r ON u.role_id = r.id
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


    public function getPlanById(int $planId)
    {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM workout_plans WHERE id = :id
        ');
        $stmt->bindParam(':id', $planId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deletePlan(int $planId): void
    {
        $stmt = $this->database->connect()->prepare('
            DELETE FROM workout_plans WHERE id = :id
        ');
        $stmt->bindParam(':id', $planId, PDO::PARAM_INT);
        $stmt->execute();
    }


    public function saveSession(int $planId, string $userNote, array $results): void
    {
        $pdo = $this->database->connect();
        
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('
                INSERT INTO workout_sessions (workout_plan_id, user_note) 
                VALUES (:plan_id, :note) 
                RETURNING id
            ');
            $stmt->bindParam(':plan_id', $planId, PDO::PARAM_INT);
            $stmt->bindParam(':note', $userNote, PDO::PARAM_STR);
            $stmt->execute();
            
            $sessionId = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

            $sql = 'INSERT INTO workout_logs 
                    (workout_session_id, plan_exercise_id, set_number, weight, reps, time_seconds, distance_km) 
                    VALUES (:session_id, :pe_id, :set_num, :w, :r, :t, :d)';
            $stmtLog = $pdo->prepare($sql);

            foreach ($results as $planExerciseId => $sets) {
                foreach ($sets as $setNumber => $data) {
                    
                    // Walidacja: czy cokolwiek wpisano?
                    if ($this->isEmptySet($data)) {
                        continue; 
                    }
 
                    // Pobieramy wartość lub pusty string, jeśli klucz nie istnieje
                    $rawWeight = $data['weight'] ?? '';
                    $rawReps   = $data['reps'] ?? '';
                    $rawDist   = $data['dist'] ?? '';
                    $rawTime   = $data['time'] ?? '';

                    // Konwersja na typy odpowiednie dla bazy (lub NULL)
                    $weight = $rawWeight !== '' ? (float)$rawWeight : null;
                    $reps   = $rawReps !== '' ? (int)$rawReps : null;
                    $dist   = $rawDist !== '' ? (float)$rawDist : null;
                    
                    // Konwersja czasu
                    $timeSeconds = null;
                    if ($rawTime !== '') {
                        $timeSeconds = $this->timeToSeconds($rawTime);
                    }

                    $stmtLog->bindValue(':session_id', $sessionId, PDO::PARAM_INT);
                    $stmtLog->bindValue(':pe_id', $planExerciseId, PDO::PARAM_INT);
                    $stmtLog->bindValue(':set_num', $setNumber, PDO::PARAM_INT);
                    $stmtLog->bindValue(':w', $weight);
                    $stmtLog->bindValue(':r', $reps);
                    $stmtLog->bindValue(':t', $timeSeconds);
                    $stmtLog->bindValue(':d', $dist);
                    
                    $stmtLog->execute();
                }
            }

            $pdo->commit();

        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    // Pomocnicza: Zamiana mm:ss na sekundy
    private function timeToSeconds($timeStr) {
        if (strpos($timeStr, ':') !== false) {
            list($min, $sec) = explode(':', $timeStr);
            return ($min * 60) + $sec;
        }
        return (int)$timeStr;
    }

    // Pomocnicza: Sprawdzenie czy inputy są puste
    private function isEmptySet($data) {
        // Funkcja empty() jest bezpieczna - nie rzuca błędu jeśli klucz nie istnieje
        return empty($data['weight']) && empty($data['reps']) 
            && empty($data['time']) && empty($data['dist']);
    }

    // Pobierz X ostatnich sesji dla danego planu
    public function getLastSessions(int $planId, int $limit = 3): array
    {
        $stmt = $this->database->connect()->prepare('
            SELECT id, date, user_note 
            FROM workout_sessions 
            WHERE workout_plan_id = :plan_id 
            ORDER BY date DESC 
            LIMIT :limit
        ');
        $stmt->bindParam(':plan_id', $planId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_reverse($sessions);
    }

    // Pobierz logi (wyniki) dla listy ID sesji
    public function getLogsForSessions(array $sessionIds): array
    {
        if (empty($sessionIds)) {
            return [];
        }

        // Tworzymy string z placeholderami (?,?,?) zależnie od ilości sesji
        $placeholders = implode(',', array_fill(0, count($sessionIds), '?'));

        $stmt = $this->database->connect()->prepare("
            SELECT * FROM workout_logs 
            WHERE workout_session_id IN ($placeholders)
            ORDER BY set_number ASC
        ");

        $stmt->execute($sessionIds);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteSession(int $sessionId): void
    {
        $stmt = $this->database->connect()->prepare('
            DELETE FROM workout_sessions WHERE id = :id
        ');
        $stmt->bindParam(':id', $sessionId, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function isTraineeAccepted(int $trainerId, int $traineeId): bool
    {
        $stmt = $this->database->connect()->prepare("
            SELECT 1 FROM trainer_trainees 
            WHERE trainer_id = :trainer_id 
              AND trainee_id = :trainee_id 
              AND status = 'accepted'
        ");
        $stmt->bindParam(':trainer_id', $trainerId, PDO::PARAM_INT);
        $stmt->bindParam(':trainee_id', $traineeId, PDO::PARAM_INT);
        $stmt->execute();

        return (bool) $stmt->fetch();
    }
}
