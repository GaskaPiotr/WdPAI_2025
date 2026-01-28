<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/User.php';
require_once __DIR__.'/../models/dto/UserDto.php';

class UserRepository extends Repository {

    // 1. Statyczna właściwość na instancję
    private static $instance = null;

    // 2. Metoda publiczna do pobierania instancji
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // 3. PRYWATNY konstruktor (blokuje `new UserRepository()`)
    // Wywołujemy parent::__construct(), aby zainicjować połączenie z bazą z klasy Repository
    private function __construct() {
        parent::__construct();
    }


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
            SELECT u.*, r.name as role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.id
            WHERE u.email = :email
        ');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        
        if ($user == false) {
            return null;
        }

        return new User(
            $user['email'],
            $user['password'],
            $user['name'],
            $user['surname'],
            $user['role_id'],
            $user['role_name'],
            $user['id']
        );
    }
    
    public function getUserById(int $id) {
        $stmt = $this->database->connect()->prepare('
            SELECT u.*, r.name as role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.id
            WHERE u.id = :id
        ');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // fetch zwraca tablicę LUB false. Jeśli zwróci false, kontroler to wyłapie.
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user == false) {
            return null;
        }

        return new User(
            $user['email'],
            $user['password'],
            $user['name'],
            $user['surname'],
            $user['role_id'],
            $user['role_name'],
            $user['id']
        );
    }


    public function getLoginAttempts(string $ipAddress): array {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM login_attempts WHERE ip_address = :ip
        ');
        $stmt->bindParam(':ip', $ipAddress, PDO::PARAM_STR);
        $stmt->execute();
        
        // Zwracamy tablicę lub null
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: []; 
    }


    public function incrementLoginAttempts(string $ipAddress): void {
        // Sprawdzamy czy IP już istnieje
        $exists = $this->getLoginAttempts($ipAddress);

        if ($exists) {
            $stmt = $this->database->connect()->prepare('
                UPDATE login_attempts 
                SET attempts = attempts + 1, last_attempt = NOW() 
                WHERE ip_address = :ip
            ');
        } else {
            $stmt = $this->database->connect()->prepare('
                INSERT INTO login_attempts (ip_address, attempts, last_attempt) 
                VALUES (:ip, 1, NOW())
            ');
        }
        $stmt->bindParam(':ip', $ipAddress, PDO::PARAM_STR);
        $stmt->execute();
    }


    public function clearLoginAttempts(string $ipAddress): void {
        $stmt = $this->database->connect()->prepare('
            DELETE FROM login_attempts WHERE ip_address = :ip
        ');
        $stmt->bindParam(':ip', $ipAddress, PDO::PARAM_STR);
        $stmt->execute();
    }
}