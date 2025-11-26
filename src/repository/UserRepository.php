<?php

require_once 'Repository.php';

class UserRepository extends Repository
{
    public function getUsers(): ?array
    {
        $stmt = $this->database->connect()->prepare('
            Select * FROM users
         ');
        $stmt->execute();

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $users;
    }
}