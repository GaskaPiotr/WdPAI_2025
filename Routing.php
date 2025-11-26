<?php


//TODO AUTOWIRING NIE TRZEBA INCLUDOWAC 
require_once 'src/controllers/SecurityController.php';
require_once 'src/controllers/DashboardController.php';

class Routing {

    public static $routes = [
        'login' => [
            'controller' => 'SecurityController',
            'action' => 'login'
        ],
        'register' => [
            'controller' => 'SecurityController',
            'action' => 'register'
        ],

        'dashboard' => [
            'controller' => 'DashboardController',
            'action' => 'index'
        ],
    ];

    public static function route($path) {
        if (preg_match('/^dashboard\/(\d+)$/', $path, $matches)) {
            $controller = DashboardController::getInstance();
            $controller->show($matches[1]); // wywołujemy metodę show($id)
            return;
        }
        switch ($path) {
            case 'dashboard':
            case 'login':
            case 'register':
                $controller = Routing::$routes[$path]['controller'];
                $action = Routing::$routes[$path]['action'];

                $controllerObj = $controller::getInstance();
                $controllerObj->$action();
                break;
            default:
                include 'public/views/404.html';
                break;
        }
    }

}
?>