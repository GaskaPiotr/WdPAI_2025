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

    public function createUser(string $email, string $hashedPassword, string $firstname): void {

        // Try catch
        $stmt = $this->database->connect()->prepare('
            INSERT INTO users (email, password, firstName) VALUES (?, ?, ?);
        ');

        $stmt->execute([
            $email,
            $hashedPassword,
            $firstname
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