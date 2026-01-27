<?php

require_once __DIR__.'/../repository/UserRepository.php';

class AuthService {

    private $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    public function login(string $email, string $password): User {
        if (empty($email) || empty($password)) {
            throw new Exception('Fill all fields');
        }

        // 1. Pobieramy użytkownika
        $user = $this->userRepository->getUserByEmail($email);

        // 2. Sprawdzamy czy istnieje
        if (!$user) {
            throw new Exception('User not found');
        }

        // 3. Weryfikujemy hasło
        if (!password_verify($password, $user->getPassword())) {
            throw new Exception('Wrong password');
        }

        // Zwracamy dane użytkownika, aby kontroler mógł ustawić sesję
        return $user;
    }

    public function register(string $email, string $password, string $confirmPassword, string $name, string $surname, string $roleName): void {
        
        // 1. Podstawowa walidacja
        if (empty($email) || empty($password) || empty($name) || empty($surname)) {
            throw new Exception('Fill all fields');
        }

        if ($password !== $confirmPassword) {
            throw new Exception('Passwords should be the same!');
        }

        // 2. Sprawdzenie duplikatów
        $existingUser = $this->userRepository->getUserByEmail($email);
        if ($existingUser) {
            throw new Exception('User with this email already exists!');
        }

        // 3. Pobranie ID roli i zapis
        try {
            $roleId = $this->userRepository->getRoleByName($roleName);
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $this->userRepository->createUser($name, $surname, $email, $hashedPassword, $roleId);
            
        } catch (Exception $e) {
            // Możemy rzucić bardziej ogólny błąd dla użytkownika, logując prawdziwy błąd w tle
            throw new Exception('An error occurred during registration.');
        }
    }
}