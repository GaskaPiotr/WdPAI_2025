<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/UserRepository.php';

class DashboardController extends AppController {

    private static $instance = null;

    private function __construct() {}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

   
    private $cards = [
            [
                'id' => 1,
                'title' => 'Ace of Spades',
                'subtitle' => 'Legendary card',
                'imageUrlPath' => 'https://deckofcardsapi.com/static/img/AS.png',
                'href' => '/cards/ace-of-spades'
            ],
            [
                'id' => 2,
                'title' => 'Queen of Hearts',
                'subtitle' => 'Classic romance',
                'imageUrlPath' => 'https://deckofcardsapi.com/static/img/QH.png',
                'href' => '/cards/queen-of-hearts'
            ],
            [
                'id' => 3,
                'title' => 'King of Clubs',
                'subtitle' => 'Royal strength',
                'imageUrlPath' => 'https://deckofcardsapi.com/static/img/KC.png',
                'href' => '/cards/king-of-clubs'
            ],
            [
                'id' => 4,
                'title' => 'Jack of Diamonds',
                'subtitle' => 'Sly and sharp',
                'imageUrlPath' => 'https://deckofcardsapi.com/static/img/JD.png',
                'href' => '/cards/jack-of-diamonds'
            ],
            [
                'id' => 5,
                'title' => 'Ten of Hearts',
                'subtitle' => 'Lucky draw',
                'imageUrlPath' => 'https://deckofcardsapi.com/static/img/0H.png',
                'href' => '/cards/ten-of-hearts'
            ],
        ];

    public function index() {
        // TODO prepare dataset and display in HTML
    
        $userRepository = new UserRepository();
        $users = $userRepository->getUsers();

        var_dump($users);

        return $this->render("dashboard", ["cards" => $this->cards]);
    }
    public function show($id) {
        $card = null;

        foreach ($this->cards as $c) {
            if ($c['id'] == $id) {
                $card = $c;
                break;
            }
        }

        if ($card === null) {
            include 'public/views/404.html';
            return;
        }

        return $this->render("card", ["card" => $card]);
    }
}