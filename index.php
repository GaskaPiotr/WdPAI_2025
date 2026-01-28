<?php

spl_autoload_register(function ($class) {
    // To prosty autoloader, w dużych projektach robi to Composer (PSR-4). 
    $path = 'src/controllers/' . $class . '.php';
    
    if (file_exists($path)) {
        require_once $path;
    } else {
        // Opcjonalnie sprawdź inne ścieżki, np. serwisy
        $pathServices = 'src/services/' . $class . '.php';
        if (file_exists($pathServices)) {
            require_once $pathServices;
        }
    }
});

session_set_cookie_params([
    'lifetime' => 0,            // Sesja wygasa po zamknięciu przeglądarki
    'path' => '/',              // Ciasteczko dostępne dla całej domeny
    
    // C3. HttpOnly: JavaScript nie ma dostępu do ciasteczka (Ochrona przed XSS)
    'httponly' => true,

    // E1. Secure: Ciasteczko przesyłane tylko po HTTPS
    // (Ustawiamy dynamicznie: true jeśli masz HTTPS, false jeśli nie, żeby nie zepsuć logowania na localhost)
    'secure' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'),

    // Dodatkowa ochrona przed CSRF (wymaga nowoczesnej przeglądarki)
    'samesite' => 'Strict' 
]);

session_start();

include ("Routing.php");


$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);


Routing::route($path)

?>