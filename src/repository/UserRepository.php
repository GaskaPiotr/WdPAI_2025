<?php

require_once 'Repository.php';

class UserRepository extends Repository
{
    public function getUsers(): ?array
    {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM users
        ');
        $stmt->execute();

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

       return $users;
    }

    // Nowa metoda: zamienia nazwę roli (np. "trainer") na jej ID (np. 2)
    public function getRoleByName(string $roleName): int {
        $stmt = $this->database->connect()->prepare('
            SELECT id FROM roles WHERE name = :roleName
        ');
        $stmt->bindParam(':roleName', $roleName, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            // Jeśli ktoś próbuje włamać się i wysłać "admin", a nie mamy takiej roli
            // to rzucamy błąd lub ustawiamy domyślną rolę. 
            throw new Exception("Role not found!"); 
        }

        return $result['id'];
    }

    public function createUser(string $name, string $surname, string $email, string $password, int $roleId): void {
        
        $stmt = $this->database->connect()->prepare('
            INSERT INTO users (name, surname, email, password, role_id) 
            VALUES (?, ?, ?, ?, ?)
        ');

        $stmt->execute([
            $name,
            $surname,
            $email,
            $password,
            $roleId 
        ]);
    }


    public function getUserByEmail(string $email) {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM users WHERE email = :email
        ');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $users = $stmt->fetch(PDO::FETCH_ASSOC);

       return $users;
    }
}