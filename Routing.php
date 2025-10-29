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

        'dashboard' => [
            'controller' => 'DashboardController',
            'action' => 'index'
        ],
    ];

    public static function route($path) {
        switch ($path) {
            case 'dashboard':
            case 'login':
            case 'register':
                $controller = Routing::$routes[$path]['controller'];
                $action = Routing::$routes[$path]['action'];

                $controllerObj = new $controller;
                $controllerObj->$action();
                break;
            default:
                include 'public/views/404.html';
                break;
        }
    }

}
?>