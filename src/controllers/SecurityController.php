<?php

require_once 'AppController.php';

class SecurityController extends AppController {

    public function login() {
        //TODO get data from database

        // $this->render("login", ("name" -> "jakiesimie"));
        return $this->render("login");
    }
}

?>