<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/UserRepository.php';

class SecurityController extends AppController {
    private $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }
    private static $instance = null;


    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // ======= LOKALNA "BAZA" UŻYTKOWNIKÓW =======
    private static array $users = [
        [
            'email' => 'anna@example.com',
            'password' => '$2y$10$wz2g9JrHYcF8bLGBbDkEXuJQAnl4uO9RV6cWJKcf.6uAEkhFZpU0i', // test123
            'first_name' => 'Anna'
        ],
        [
            'email' => 'bartek@example.com',
            'password' => '$2y$10$fK9rLobZK2C6rJq6B/9I6u6Udaez9CaRu7eC/0zT3pGq5piVDsElW', // haslo456
            'first_name' => 'Bartek'
        ],
        [
            'email' => 'celina@example.com',
            'password' => '$2y$10$Cq1J6YMGzRKR6XzTb3fDF.6sC6CShm8kFgEv7jJdtyWkhC1GuazJa', // qwerty
            'first_name' => 'Celina'
        ],
    ];


    public function login() {
        //TODO get data from database
        var_dump($_SERVER["REQUEST_METHOD"]);
        if ($_SERVER["REQUEST_METHOD"] !==  "POST") {
            return $this->render("login");
        } 

        //TERNARY OPERATOR IF JEDNO LINIJKOWY 
        // JESLI $_POST["email"] ISTNIEJE TO $email = $_POST["email"]
        // JESLI NIE TO JEST NULL`
        $email = $_POST["email"] ?? '';
        $password = $_POST["password"] ?? ''; 
        if (empty($email) || empty($password)) {
            return $this->render('login', ['messages' => ['Fill all fields']]);
        }

       //TODO replace with search from database
        $userRow = null;
        foreach (self::$users as $u) {
            if (strcasecmp($u['email'], $email) === 0) {
                $userRow = $u;
                break;
            }
        }

        if (!$userRow) {
            return $this->render('login', ['message' => 'User not found']);
        }

        if (!password_verify($password, $userRow['password'])) {
            return $this->render('login', ['message' => 'Wrong password']);
        }

        // TODO możemy przechowywać sesje użytkowika lub token
        // setcookie("username", $userRow['email'], time() + 3600, '/');


//        var_dump($email, $password);

        // $this->render("login", ("name" -> "jakiesimie"));
        return $this->render("dashboard");
    }
    
    public function register() {

        if ($this->isGet()) {
            return $this->render("register");
        }


        $email = $_POST["email"] ?? '';
        
        $password1 = $_POST["password1"] ?? '';
        $password2 = $_POST["password2"] ?? '';

        $firstname = $_POST["firstname"] ?? '';
        $lastname = $_POST["lastname"] ?? '';

       
        if (empty($email) || empty($password1) || empty($firstname)) {
            return $this->render('register', ['messages' => 'Fill all fields']);
        }

        if ($password1 !== $password2) {
            return $this->render('register', ['messages' => 'asswords should be the same!']);
        }
        
        $hashedPassword = password_hash($password1, PASSWORD_BCRYPT);


        $this->userRepository->createUser(
            $email,
            $hashedPassword,
            $firstname
        );

        return $this->render("login", ["message" => "Zarejestrowano uzytkownika ".$email]);

    }
 
}

?>