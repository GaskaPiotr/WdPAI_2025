<?php

require_once __DIR__.'/../repository/UserRepository.php';

class AuthService {

    private $userRepository;
    private const MAX_LOGIN_ATTEMPTS = 3;
    private const BLOCK_TIME_SECONDS = 10;

    public function __construct() {
        $this->userRepository = UserRepository::getInstance();
    }

    public function login(string $email, string $password): UserDto {
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $attemptData = $this->userRepository->getLoginAttempts($ipAddress);

        if (!empty($attemptData)) {
            $attempts = $attemptData['attempts'];
            $lastAttemptTime = strtotime($attemptData['last_attempt']);
            $currentTime = time();

            // Jeśli przekroczono limit prób
            if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
                // i nie minął jeszcze czas blokady
                if (($currentTime - $lastAttemptTime) < self::BLOCK_TIME_SECONDS) {
                    $timeLeft = self::BLOCK_TIME_SECONDS - ($currentTime - $lastAttemptTime);
                    throw new Exception("Too many failed attempts. Try again in $timeLeft seconds.");
                } else {
                    // Czas kary minął -> Resetujemy licznik, dając czystą kartę
                    $this->userRepository->clearLoginAttempts($ipAddress);
                }
            }
        }
    
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }


        if (empty($email) || empty($password)) {
            throw new Exception('Fill all fields');
        }

        // 1. Pobieramy użytkownika
        $user = $this->userRepository->getUserByEmail($email);

        // 2. Weryfikujemy email i hasło
        if (!$user || !password_verify($password, $user->getPassword())) {
            $this->userRepository->incrementLoginAttempts($ipAddress);

            sleep(1);

            throw new Exception('Wrong email or password!');
        }

        $this->userRepository->clearLoginAttempts($ipAddress);

        // Zwracamy dane użytkownika, aby kontroler mógł ustawić sesję
        return $user->toDto();
    }

    public function register(string $email, string $password, string $confirmPassword, string $name, string $surname, string $roleName): void {

        if (strlen($email) > 255) {
            throw new Exception("Email is too long (max 255 chars).");
        }
        
        if (strlen($name) > 50 || strlen($surname) > 50) {
            throw new Exception("Name or surname is too long (max 50 chars).");
        }
        
        if (strlen($password) > 255) {
            throw new Exception("Password is too long.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }
        
        // 1. Podstawowa walidacja
        if (empty($email) || empty($password) || empty($name) || empty($surname)) {
            throw new Exception('Fill all fields');
        }

        if ($password !== $confirmPassword) {
            throw new Exception('Passwords should be the same!');
        }

        $this->validatePassword($password);

        // 2. Sprawdzenie duplikatów
        $existingUser = $this->userRepository->getUserByEmail($email);
        if ($existingUser) {
            throw new Exception('If User with this email already exists, we sent information to this email');
        }

        // 3. Pobranie ID roli i zapis
        try {
            $roleId = $this->userRepository->getRoleByName($roleName);
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $this->userRepository->createUser($name, $surname, $email, $hashedPassword, $roleId);
            
        } catch (Exception $e) {
            throw new Exception('An error occurred during registration.');
        }
    }
    private function validatePassword(string $password): void {
        // 1. Długość (minimum 8 znaków)
        if (strlen($password) < 8) {
            throw new Exception("Password is too short. Minimum 8 characters required.");
        }

        // 2. Musi zawierać wielką literę
        if (!preg_match('/[A-Z]/', $password)) {
            throw new Exception("Password must contain at least one uppercase letter.");
        }

        // 3. Musi zawierać małą literę
        if (!preg_match('/[a-z]/', $password)) {
            throw new Exception("Password must contain at least one lowercase letter.");
        }

        // 4. Musi zawierać cyfrę
        if (!preg_match('/[0-9]/', $password)) {
            throw new Exception("Password must contain at least one number.");
        }

        // 5. Musi zawierać specjalny znak
        if (!preg_match('/[\W]/', $password)) {
            throw new Exception("Password must contain at least one special character (!@#$%^&*).");
        }
    }
}