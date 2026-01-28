<?php


require_once 'AppController.php';
require_once __DIR__.'/../services/AuthService.php';

class SecurityController extends AppController {

    private $authService;
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        // Tworzymy instancję serwisu
        $this->authService = new AuthService();
    }

    public function login() {
        // GET: Wyświetl formularz
        if (!$this->isPost()) {
            return $this->render("login");
        }

        // POST: Próba logowania
        $email = $_POST["email"] ?? '';
        $password = $_POST["password"] ?? ''; 

        try {
            // Serwis zwraca dane usera LUB rzuca wyjątek
            $userDto = $this->authService->login($email, $password);

            // Sukces -> Ustawiamy sesję (to zadanie kontrolera/warstwy HTTP)
            $_SESSION['user_id'] = $userDto->id;
            $_SESSION['user_email'] = $userDto->email; 
            $_SESSION['role_id'] = $userDto->roleId;

            // Przekierowanie
            $this->redirect('/dashboard');

        } catch (Exception $e) {

            if (strpos($e->getMessage(), 'locked') !== false || strpos($e->getMessage(), 'Try again') !== false) {
                http_response_code(429); // 429 Too Many Requests
            } else {
                http_response_code(401); // 401 Unauthorized (złe hasło/email)
            }
            // Błąd -> Wyświetlamy formularz z komunikatem
            return $this->render('login', ['messages' => [$e->getMessage()]]);
        }
    }

    public function register() {
        // GET: Wyświetl formularz
        if (!$this->isPost()) { // Zmieniłem z isGet() na !isPost() dla spójności, ale isGet() też jest ok
            return $this->render("register");
        }

        // POST: Próba rejestracji
        $email = $_POST["email"] ?? '';
        $password = $_POST["password"] ?? ''; // password1 w formularzu? Zmieniłem na password dla uproszczenia, sprawdź name w HTML
        $confirmPassword = $_POST["password_confirm"] ?? ''; // Sprawdź name w HTML (często password2 lub password_confirm)
        
        // Jeśli w HTML masz name="password1" i "password2", użyj tego:
        $password = $_POST["password1"] ?? '';
        $confirmPassword = $_POST["password2"] ?? '';

        $name = $_POST["name"] ?? '';
        $surname = $_POST["surname"] ?? '';
        $roleName = $_POST["role"] ?? 'trainee';

        try {
            $this->authService->register($email, $password, $confirmPassword, $name, $surname, $roleName);
            http_response_code(201); 
            // Sukces -> Przekieruj na login lub wyświetl login z komunikatem
            return $this->render("login", ["messages" => ["Account created! Please log in."]]);

        } catch (Exception $e) {
            http_response_code(400);
            // Błąd -> Wyświetl formularz rejestracji z błędem
            return $this->render('register', ['messages' => [$e->getMessage()]]);
        }
    }

    public function logout() {
        // To jest czysto operacja na sesji HTTP, więc może zostać w kontrolerze
        session_unset();
        session_destroy();

        $this->redirect('/login');
    }

    // Pomocnicza metoda (możesz ją przenieść do AppController, żeby nie powielać w każdym kontrolerze)
    private function redirect($path) {
        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}{$path}");
        exit;
    }
}
