<?php


//TODO AUTOWIRING NIE TRZEBA INCLUDOWAC 
require_once 'src/controllers/SecurityController.php';
require_once 'src/controllers/DashboardController.php';
require_once 'src/controllers/TrainerController.php';
require_once 'src/controllers/PlanController.php';

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
        'search-cards' => [
            'controller' => 'DashboardController',
            'action' => 'search'
        ],
        'add-trainee' => [
            'controller' => 'TrainerController',
            'action' => 'addTrainee'
        ],
        'handle-invitation' => [
            'controller' => 'DashboardController',
            'action' => 'handleInvitation'
        ],
        'logout' => [
            'controller' => 'SecurityController',
            'action' => 'logout'
        ],
        'add-plan' => [
            'controller' => 'PlanController',
            'action' => 'addPlan'
        ],
        'trainee-dashboard' => [
            'controller' => 'TrainerController',
            'action' => 'showTraineeDashboard'
        ],
        'delete-plan' => [
            'controller' => 'PlanController',
            'action' => 'delete'
        ],
        'plan' => [
            'controller' => 'PlanController',
            'action' => 'index' // To wyświetla szczegóły
        ],
        'add-exercise' => [
            'controller' => 'PlanController',
            'action' => 'addExercise'
        ],
        'delete-exercise' => [
            'controller' => 'PlanController',
            'action' => 'deleteExercise'
        ],
        'start-workout' => [
            'controller' => 'PlanController',
            'action' => 'startWorkout'
        ],
        'save-workout' => [
            'controller' => 'PlanController',
            'action' => 'saveWorkout'
        ],
        'delete-session' => [
            'controller' => 'PlanController',
            'action' => 'deleteSession'
        ],
    ];

    public static function route($path) {
        if (preg_match('/^dashboard\/(\d+)$/', $path, $matches)) {
            $controller = DashboardController::getInstance();
            $controller->show($matches[1]); // wywołujemy metodę show($id)
            return;
        }
        switch ($path) {
            case 'delete-session':
            case 'save-workout':
            case 'start-workout':
            case 'plan':
            case 'add-exercise':
            case 'delete-exercise':
            case 'delete-plan':
            case 'search-cards':
            case 'dashboard':
            case 'add-trainee':
            case 'handle-invitation':
            case 'logout':
            case 'add-plan':
            case 'trainee-dashboard':
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