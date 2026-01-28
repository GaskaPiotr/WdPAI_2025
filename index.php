<?php

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