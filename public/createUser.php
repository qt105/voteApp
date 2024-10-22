<?php

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

    require_once "../templates/profile.html.php"; 
}