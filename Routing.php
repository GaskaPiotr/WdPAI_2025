<?php


class Routing {

    public static $routes = [
        '' => [
            'controller' => 'DashboardController',
            'action' => 'index' 
        ],
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
            'action' => 'index'
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
            $controllerName = 'DashboardController';
            $object = $controllerName::getInstance();
            $object->show($matches[1]); // wywołujemy metodę show($id)
            return;
        }

        if (array_key_exists($path, self::$routes)) {
            // Wyciągamy nazwę kontrolera i akcji z tablicy
            $route = self::$routes[$path];
            
            $controllerName = $route['controller'];
            $action = $route['action'];

            // Tworzymy obiekt dynamicznie
            $object = $controllerName::getInstance();
            
            // Wywołujemy metodę
            $object->$action();
        } else {
            // 3. Brak trasy -> 404
            http_response_code(404);
            
            // Ustawiamy zmienne, których oczekuje error.html
            $code = 404;
            $message = 'Page not found. The URL you are looking for does not exist.';

            // Używamy uniwersalnego widoku
            include 'public/views/error.html';
        }
    }

}
?>