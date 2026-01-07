<?php
session_start();

include ("Routing.php");

//echo "<h1>Hello world 💪</h1>";

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);

//var_dump($path);

Routing::route($path)

?>