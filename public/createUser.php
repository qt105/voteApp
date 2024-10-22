<?php
session_start();

require_once __DIR__ . "/../templates/header.html.php";
require_once __DIR__ . "/../vendor/autoload.php";
use App\VoteApp\ClassUser;

if (empty($_POST)) {
    require_once "../templates/formUser.html.php";
} else {
    $user = new ClassUser(
        username: $_POST['username'], 
        password: $_POST['password']);

    $user->setFirstName($_POST['firstName'])
         ->setLastName($_POST['lastName'])
         ->setEmail($_POST['email']);

$_SESSION[$user->getUsername()] = $user;

    require_once "../templates/profile.html.php"; 
}